<?php

namespace Common\Design\Matching;

class BuyMatch implements Handle
{
    public function handle($market, $id)
    {
        $coin = explode('_', $market)[0]; //交易货币
        $legal = explode('_', $market)[1]; //法币

        //当前委托单
        $trade = M('Trade')->where(array('id' => $id))->find();

        //匹配卖单(匹配用户)，达成交易(过滤自己的单) 买的时候是少花钱
        $sells = M('Trade')->where(array(
            'type' => 2,
            'status' => 0,
            'trade_type' => 2,
            'market' => $market,
            'price' => array('elt', $trade['price'])
        ))->order('price asc')->limit(50)->select();

        try {
            //开启事物
            M()->startTrans();
            //优先匹配用户跟用户之间的交易
            if ($sells) {
                //要匹配的数量=挂单数量减去成交数量
                $start = bcsub($trade['num'], $trade['deal'], 8);
                //遍历数组
                foreach ($sells as $sell) {
                    //如果计数器大于0
                    if (floatval($start) > 0) {
                        $whereA = array('id' => $trade['id']);
                        //卖单剩余数量
                        $surplus = bcsub($sell['num'], $sell['deal'], 8);
                        //计算实际成交数量
                        if ($surplus >= $start) { //如果未成交数量大于要购买的数量
                            $realnum = $start;
                            //订单直接完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereA, array('status' => 1));
                        } else {
                            $realnum = $surplus;
                        }
                        //增加已成交数
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, true);
                        //减去剩余数量
                        ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, false);
                        //生成交易单
                        ModelHandle::AddTradeLog(userid(), $sell['userid'], $market, $sell['price'], $realnum, 1);

                        $whereB = array('id' => $sell['id']);
                        //如果剩余数量小于等于成交数量
                        if ($surplus <= $realnum) {
                            //卖单成交完成
                            ModelHandle::UpdateDataByWhere('Trade', $whereB, array('status' => 1));
                        }
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
                        //对于买家来说，减去的冻结资产等于挂单价格乘以本次数量
                        $buyLegalNum = bcmul($trade['price'], $realnum, 8);
                        //减去买家冻结法币资产
                        $whereD = array('userid' => userid());
                        ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal.'d', $buyLegalNum, false);
                        //补差价
                        $frozen = bcsub($buyLegalNum, $legalNum, 8);
                        ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal, $frozen, true);
                        //增加买家货币资产
                        ModelHandle::UpdateFieldNum('UserCoin', $whereD, $coin, $realnum, true);
                        //更新计数器
                        $start = bcsub($start, $realnum, 8);
                        //更新币种价格
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
