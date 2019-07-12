<?php

namespace Common\Design\SetTrade;

//卖出匹配
class SellMatch
{
    public static function handle($taker, $maker, $market)
    {
        try {
            //开启事物
            M()->startTrans();

            $coin = explode('_', $market)[0]; //交易货币
            $legal = explode('_', $market)[1]; //法币
            //获取卖单剩余数量
            $buy = M('Trade')->where(['id' => $taker['orderId']])->find();
            if (!$buy) {
                throw new \Exception('订单不存在');
            }
            $start = bcsub($buy['num'], $buy['deal'], 8);
            //循环买单
            foreach ($maker as $order) {
                //如果剩余数量等于本次成交数量
                if ($start == $order['currentExchangedAmount']) {
                    ModelHandle::UpdateDataByWhere('Trade', ['id' => $taker['orderId']], ['status' => 1]);
                }

                //卖家用户ID
                $buyid = $taker['userId'];
                //查找买单
                $sell = M('Trade')->where(['id' => $order['orderId']])->find();
                if (count($sell) == 0) {
                    throw new \Exception('匹配到的订单不存在');
                }
                if ($sell['market'] != $market) {
                    throw new \Exception('交易对不匹配');
                }
                //买家ID
                $peerid = $sell['userid'];
                //本次成交数量
                $realnum = $order['currentExchangedAmount'];
                //本次成交价格
                $price = $order['exchangePrice'];
                //本次法币总额
                $legalNum = bcmul($price, $realnum, 8);
                //法币手续费
                $feeLegal = bcmul($legalNum, 0.003, 8);
                //虚拟币手续费
                $feeCoin = bcmul($realnum, 0.003, 8);
                //实际到账法币总额
                $realLegalNum = bcsub($legalNum, $feeLegal, 8);
                //实际到账虚拟币
                $realCoinNum = bcsub($realnum, $feeCoin, 8);
                //委托单条件
                $whereA = ['id' => $taker['orderId']];
                //减去卖单剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, FALSE);
                //增加卖单已成交数量
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, TRUE);
                //生成交易单
                ModelHandle::AddTradeLog($peerid, $buyid, $market, $price, $realnum, $buy['type']);
                //卖单剩余数量
                $surplus = bcsub($sell['num'], $sell['deal'], 8);
                //如果买单剩余数量小于本次成交数量，报错
                if ($surplus < $realnum) {
                    throw new \Exception('买单' . $order['orderId'] . '剩余数量不足，撮合出错了！');
                }
                $whereB = ['id' => $sell['id']];
                //减去买单剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'mum', $realnum, FALSE);
                //增加买单已成交数
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'deal', $realnum, TRUE);
                //如果买单剩余数量等于本次成交数量，买单完成
                if ($surplus == $realnum) {
                    ModelHandle::UpdateDataByWhere('Trade', $whereB, ['status' => 1]);
                }
                //写入财务表
                //卖家法币收入
                ModelHandle::AddFinance($market, $buyid, $legal, $legalNum, TRUE);
                //卖家虚拟币支出
                ModelHandle::AddFinance($market, $buyid, $coin, $realnum, FALSE);
                //买家虚拟币收入
                ModelHandle::AddFinance($market, $peerid, $coin, $realnum, TRUE);
                //买家法币支出（冻结资产）
                ModelHandle::AddFinance($market, $peerid, $legal, $legalNum, FALSE);
                $whereC = ['userid' => $peerid];
                //对于买家来说，减去的冻结总额等于数量乘以挂单价格（非本次成交价格）
                //$buyLegalNum = bcmul($sell['price'], $realnum, 8);
                //减去买家冻结法币资产
                //总支出，包含给卖家和返回差价
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $legal . 'd', $legalNum, FALSE);
                //补差价
                //$frozen = bcsub($buyLegalNum, $legalNum, 8);
                //ModelHandle::UpdateFieldNum('UserCoin', $whereC, $legal, $frozen, true);
                //增加买家货币资产(减去手续费之后)
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $coin, $realCoinNum, TRUE);
                $whereD = ['userid' => $buyid];
                //减去卖家冻结货币资产
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $coin . 'd', $realnum, FALSE);
                //增加卖家法币资产(减去手续费之后)
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal, $realLegalNum, TRUE);

                //更新最新价格
                ModelHandle::UpdateMarketPrice($market);
                //减去步长
                $start = bcsub($start, $realnum, 8);
            }

            M()->commit();

            return ['status' => 1, 'msg' => 'success'];
        } catch (\Exception $e) {
            $msg = $e->getMessage();

            modellog($msg);

            M()->rollback();

            return ['status' => 0, 'msg' => $msg];
        }
    }
}
