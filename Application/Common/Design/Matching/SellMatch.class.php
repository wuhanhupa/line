<?php

namespace Common\Design\Matching;

//卖出匹配
class SellMatch implements Handle
{
    public function handle($market, $id)
    {
        $coin = explode('_', $market)[0]; //交易货币
        $legal = explode('_', $market)[1]; //法币

        //当前委托单
        $trade = M('Trade')->where(array('id' => $id))->find();

        //匹配买单(匹配用户)，达成交易(过滤自己的单)  卖的时候多卖钱
        $sells = M('Trade')->where(array(
            'type' => 1,
            'status' => 0,
            'trade_type' => 2,
            'market' => $market,
            'price' => array('egt', $trade['price'])
        ))->order('price desc')->limit(50)->select();

        try {
            //开启事物
            M()->startTrans();
            //匹配到满足条件的订单
            if ($sells) {
                //要匹配的数量
                $start = bcsub($trade['num'], $trade['deal'], 8);
                //遍历数组
                foreach ($sells as $sell) {
                    //如果计数器大于0
                    if (floatval($start) > 0) {
                        //剩余数量
                        $surplus = bcsub($sell['num'], $sell['deal'], 8);
                        $whereA = array('id' => $trade['id']);
                        //计算实际成交数量
                        if ($surplus >= $start) { //如果未成交数量大于要购买的数量
                            $realnum = $start;
                            //订单直接完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                        } else {
                            $realnum = $surplus;
                        }
                        //更新已成交数
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, true);
                        //更新剩余数量
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, false);
                        //生成交易单
                        ModelHandle::AddTradeLog($sell['userid'], userid(), $market, $sell['price'], $realnum, 2);

                        $whereB = array('id' => $sell['id']);
                        //如果剩余数量小于等于成交数量
                        if ($surplus <= $realnum) {
                            //卖单成交完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                        }
                        //更新买单剩余数量
                        ModelHandle::UpdateFieldNum('Trade', $whereB, 'mum', $realnum, false);
                        //更新买单已成交数
                        ModelHandle::UpdateFieldNum('Trade', $whereB, 'deal', $realnum, true);
                        //法币成交总额
                        $legalNum = bcmul($sell['price'], $realnum, 8);
                        //写入财务表
                        //卖家法币收入
                        ModelHandle::AddFinance($market, userid(), $legal, $legalNum, true);
                        //卖家虚拟币支出
                        ModelHandle::AddFinance($market, userid(), $coin, $realnum, false);
                        //买家虚拟币收入
                        ModelHandle::AddFinance($market, $sell['userid'], $coin, $realnum, true);
                        //买家法币支出
                        ModelHandle::AddFinance($market, $sell['userid'], $legal, $legalNum, false);
                        $whereC = array('userid' => $sell['userid']);
                        //减去买家冻结法币资产
                        ModelHandle::UpdateFieldNum('UserCoin', $whereC, $legal.'d', $legalNum, false);
                        //增加买家货币资产
                        ModelHandle::UpdateFieldNum('UserCoin', $whereC, $coin, $realnum, true);
                        $whereD = array('userid' => userid());
                        //减去卖家冻结货币资产
                        ModelHandle::UpdateFieldNum('UserCoin', $whereD, $coin.'d', $realnum, false);
                        //增加卖家法币资产
                        ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal, $legalNum, true);
                        //更新计数器
                        $start = bcsub($start, $realnum, 8);
                        //更新最新价格
                        ModelHandle::UpdateMarketPrice($market);
                    }
                }
            }

            M()->commit();

            return ['errcode' => 1, 'msg' => '下单成功'];
        } catch (\Exception $e) {
            M()->rollback();

            $msg = $e->getMessage();

            return ['errcode' => 0, 'msg' => $msg];
        }
    }
}
