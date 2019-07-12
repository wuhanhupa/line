<?php

namespace Common\Design\Matching;

/**
 * 撮合交易.
 */
class MatchCollection
{
    /**
     * 实例化类时将数据写入redis.
     *
     * @param [type] $market [description]
     */
    public function __construct($market)
    {
        //所有买单(价格从高到低)
        $buyOrders = M('Trade')->where(array('market' => $market, 'status' => 0, 'type' => 1))->order('price desc')->limit(100)->select();
        //$buyOrders = M('Trade')->where('market="'.$market.'" and status=0 and type=1')->order('price desc')->limit(100)->select();
        $redis = $this->connectRedis();
        //推送到redis
        foreach ($buyOrders as $buy) {
            $buylist = json_encode($buy);
            $redis->RPUSH('BUYORDER', $buylist);
        }

        //所有卖单（价格从低到高）
        $sellOrders = M('Trade')->where('market="'.$market.'" and status=0 and type=2')->order('price asc')->limit(100)->select();

        foreach ($sellOrders as $sell) {
            $selllist = json_encode($sell);
            $redis->RPUSH('SELLORDER', $selllist);
        }
    }

    //每次从redis里拿出一条记录进行匹配
    public function entry()
    {
        $redis = $this->pconnectRedis();

        //echo $redis->LSIZE('BUYORDER').'<br>';
        //echo $redis->LSIZE('SELLORDER').'<br>';
        try {
            do {
                $buy = json_decode($redis->LPOP('BUYORDER'), true);
                $sell = json_decode($redis->LPOP('SELLORDER'), true);

                if ($buy && $sell) {
                    $this->handle($buy, $sell);
                }
            } while ($redis->LSIZE('BUYORDER') > 0 || $redis->LSIZE('SELLORDER') > 0);

            //执行完分发处理后执行一次机器人匹配交易
            //dump($redis->LSIZE('ROBOTORDER'));

            $this->robotReach();

            echo 'success';
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            echo $msg;
        }
    }

    public function handle($buy, $sell)
    {
        //如果是诱单和诱单
        if ($buy['trade_type'] == 1 && $sell['trade_type'] == 1) {
            //将诱单放到队列以便后面执行成交诱单操作
            $this->distribute($buy);
            $this->distribute($sell);
        }
        //如果匹配的到的是用户跟诱单
        if ($buy['trade_type'] == 2 && $sell['trade_type'] == 1) {
            //将用户推给第三方
            //$this->handleThridParty($buy);
            //将诱单推给队列
            $this->distribute($sell);
        }
        //如果是第三方和用户
        if ($buy['trade_type'] == 3 && $sell['trade_type'] == 1) {
            //撤销第三方
            $this->cancelTrade($buy['id']);
            //将诱单推给队列
            $this->distribute($sell);
        }
        //如果是诱单和用户
        if ($buy['trade_type'] == 1 && $sell['trade_type'] == 2) {
            //将诱单放到队列以便后面执行成交诱单操作
            $this->distribute($buy);
            //将用户单推送给第三方去成交
            //$this->handleThridParty($sell);
        }
        //如果匹配的到的是用户跟用户
        if ($buy['trade_type'] == 2 && $sell['trade_type'] == 2) {
            //执行成交流程
            $this->ReachDeal($buy, $sell);
        }
        //如果是第三方和用户
        if ($buy['trade_type'] == 3 && $sell['trade_type'] == 2) {
            //撤销第三方
            $this->cancelTrade($buy['id']);
            //将用户单推送给第三方
            //$this->handleThridParty($sell);
        }
        //如果时诱单和第三方
        if ($buy['trade_type'] == 1 && $sell['trade_type'] == 3) {
            //将诱单放到队列以便后面执行成交诱单操作
            $this->distribute($buy);
            //将第三方撤销
            $this->cancelTrade($sell['id']);
        }
        //如果是用户和第三方
        if ($buy['trade_type'] == 2 && $sell['trade_type'] == 3) {
            //将用户推给第三方
            //$this->handleThridParty($buy);
            //将第三方撤销
            $this->cancelTrade($sell['id']);
        }
        //如果是第三方和第三方，将第三方撤销
        if ($buy['trade_type'] == 3 && $sell['trade_type'] == 3) {
            //将用户推给第三方
            $this->cancelTrade($buy['id']);
            //将第三方撤销
            $this->cancelTrade($sell['id']);
        }
    }

    /**
     * 将订单状态改为取消.
     */
    private function cancelTrade($id)
    {
        $trade = M('Trade')->where(array('id' => $id))->find();

        if ($trade) {
            return M('Trade')->where(array('id' => $id))->save(array('status' => 2));
        }
    }

    /**
     * 达成交易.
     */
    protected function ReachDeal($buy, $sell)
    {
        //卖单价要小于等于买单价
        if ($sell['price'] <= $buy['price']) {
            try {
                //开启事物
                M()->startTrans();
                $whereA = array('id' => $buy['id']);
                $whereB = array('id' => $sell['id']);
                //判断剩余数量
                if ($sell['mum'] > $buy['mum']) { //如果未成交数量大于要购买的数量
                    $realnum = $buy['mum'];
                    //买单完成
                    ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                } elseif ($sell['mum'] == $buy['mum']) {
                    $realnum = $sell['mum'];
                    ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                    ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                } else {
                    $realnum = $sell['mum'];
                    //卖单完成
                    ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                }
                //增加已成交数
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, true);
                //减去剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, false);
                //生成交易单
                ModelHandle::AddTradeLog(userid(), $sell['userid'], $market, $sell['price'], $realnum, 1);
                //减去卖单剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'mum', $realnum, false);
                //增加卖单已成交数
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'deal', $realnum, true);
                //法币成交总额
                $legalNum = bcmul($sell['price'], $realnum, 8);
                //写入财务表
                //买家法币支出
                ModelHandle::AddFinance($market, userid(), $legal, $legalNum, false);
                //买家虚拟币收入
                ModelHandle::AddFinance($market, userid(), $coin, $realnum, true);
                //卖家虚拟币支出
                ModelHandle::AddFinance($market, $sell['userid'], $coin, $realnum, false);
                //卖家法币收入
                ModelHandle::AddFinance($market, $sell['userid'], $legal, $legalNum, true);
                //减去卖家冻结资产
                $whereC = array('userid' => $sell['userid']);
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $coin.'d', $realnum, false);
                //增加卖家法币资产
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $legal, $legalNum, true);
                //减去买家冻结法币资产
                $whereD = array('userid' => userid());
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal.'d', $legalNum, false);
                //增加买家货币资产
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $coin, $realnum, true);
                //更新币种价格
                ModelHandle::UpdateMarketPrice($market);
                M()->commit();
                //这里如果有部分完成的，等待下次入队
                return ['errcode' => 1, 'msg' => '达成交易'];
            } catch (\Exception $e) {
                M()->rollback();
                $msg = $e->getMessage();

                return ['errcode' => 0, 'msg' => $msg];
            }
        } else {
            //推送给第三方
            //$this->handleThridParty($buy);
            //$this->handleThridParty($sell);
            echo '卖价大于买价';
        }
    }

    /**
     * 处理机器人队列，让机器人达成交易，产生k线数据.
     */
    public function robotReach()
    {
        $redis = $this->pconnectRedis();
        //dump($redis->LSIZE('ROBOTORDER'));
        do {
            //echo '执行了';
            $buy = json_decode($redis->LPOP('ROBOTBUY'), true);
            $sell = json_decode($redis->LPOP('ROBOTSELL'), true);
            if ($buy && $sell) {
                if ($sell['price'] <= $buy['price']) {
                    try {
                        //开启事物
                        M()->startTrans();
                        $whereA = array('id' => $buy['id']);
                        $whereB = array('id' => $sell['id']);
                        //判断剩余数量
                        if ($sell['mum'] > $buy['mum']) { //如果未成交数量大于要购买的数量
                            $realnum = $buy['mum'];
                            //买单完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                        } elseif ($sell['mum'] == $buy['mum']) {
                            $realnum = $sell['mum'];
                            ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                            ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                        } else {
                            $realnum = $sell['mum'];
                            //卖单完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                        }
                        //增加已成交数
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, true);
                        //减去剩余数量
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, false);
                        //生成交易单
                        ModelHandle::AddTradeLog($buy['userid'], $sell['userid'], $market, $sell['price'], $realnum, 1);
                        //减去卖单剩余数量
                        ModelHandle::UpdateFieldNum('Trade', $whereB, 'mum', $realnum, false);
                        //增加卖单已成交数
                        ModelHandle::UpdateFieldNum('Trade', $whereB, 'deal', $realnum, true);
                        //更新币种价格
                        ModelHandle::UpdateMarketPrice($market);
                        M()->commit();
                        //这里如果有部分完成的，等待下次入队
                        return ['errcode' => 1, 'msg' => '达成交易'];
                    } catch (\Exception $e) {
                        M()->rollback();
                        $msg = $e->getMessage();

                        return ['errcode' => 0, 'msg' => $msg];
                    }
                } else {
                    echo '卖价大于买价';
                }
            }
        } while ($redis->LSIZE('ROBOTBUY') > 0 || $redis->LSIZE('ROBOTSELL') > 0);
    }

    /**
     * 订单分发队列.(只有机器人).
     */
    private function distribute($order)
    {
        $redis = $this->connectRedis();
        //机器人不分买卖，直接达成交易
        $order = json_encode($order);
        if ($order['type'] == 1) {
            $redis->RPUSH('ROBOTBUY', $order);
        } else {
            $redis->RPUSH('ROBOTSELL', $order);
        }
        
    }

    //连接redis
    private function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }

    private function pconnectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }
}
