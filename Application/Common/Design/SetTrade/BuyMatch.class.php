<?php

namespace Common\Design\SetTrade;

//买单撮合处理
class BuyMatch
{
    public static function handle($taker, $maker, $market)
    {
        try {
            //开启事物
            M()->startTrans();

            $coin = explode('_', $market)[0]; //交易货币
            $legal = explode('_', $market)[1]; //法币

            //查找买单
            $buy = M('Trade')->where(['id' => $taker['orderId']])->find();
            if (!$buy) {
                throw new \Exception('订单不存在');
            }
            //获取买单剩余数量
            $start = bcsub($buy['num'], $buy['deal'], 8);

            //循环卖单
            foreach ($maker as $order) {
                //如果剩余数量等于本次成交数量
                if ($start == $order['currentExchangedAmount']) {
                    //买单修改为已完成
                    ModelHandle::UpdateDataByWhere('Trade', ['id' => $taker['orderId']], ['status' => 1]);
                }
                //买家用户ID
                $buyid = $taker['userId'];
                //查找卖单
                $sell = M('Trade')->where(['id' => $order['orderId']])->find();
                if (count($sell) == 0) {
                    throw new \Exception('匹配到的订单不存在');
                }
                if ($sell['market'] != $market) {
                    throw new \Exception('交易对不匹配');
                }
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
                //委托单查询条件
                $whereA = ['id' => $taker['orderId']];
                //减去买单剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'mum', $realnum, FALSE);
                //增加买单已成交数量
                ModelHandle::UpdateFieldNum('Trade', $whereA, 'deal', $realnum, TRUE);
                //生成交易单
                ModelHandle::AddTradeLog($buyid, $peerid, $market, $price, $realnum, $buy['type']);
                //卖单剩余数量
                $surplus = bcsub($sell['num'], $sell['deal'], 8);
                //如果卖单剩余数量小于本次成交数量，报错
                if ($surplus < $realnum) {
                    throw new \Exception('卖单' . $order['orderId'] . '剩余数量不足，撮合出错了！');
                }
                $whereB = ['id' => $sell['id']];
                //减去卖单剩余数量
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'mum', $realnum, FALSE);
                //增加卖单已成交数
                ModelHandle::UpdateFieldNum('Trade', $whereB, 'deal', $realnum, TRUE);
                //如果卖单剩余数量等于本次成交数量，卖单完成
                if ($surplus == $realnum) {
                    ModelHandle::UpdateDataByWhere('Trade', $whereB, ['status' => 1]);
                }
                //写入财务表
                //买家法币支出
                ModelHandle::AddFinance($market, $buyid, $legal, $legalNum, FALSE);
                //买家虚拟币收入
                ModelHandle::AddFinance($market, $buyid, $coin, $realnum, TRUE);
                //卖家虚拟币支出
                ModelHandle::AddFinance($market, $peerid, $coin, $realnum, FALSE);
                //卖家法币收入
                ModelHandle::AddFinance($market, $peerid, $legal, $legalNum, TRUE);
                //减去卖家冻结资产
                $whereC = ['userid' => $peerid];
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $coin . 'd', $realnum, FALSE);
                //增加卖家法币资产（扣除手续费之后）
                ModelHandle::UpdateFieldNum('UserCoin', $whereC, $legal, $realLegalNum, TRUE);
                //对于买家来说，减去的冻结资产等于挂单价格乘以本次数量
                $buyLegalNum = bcmul($buy['price'], $realnum, 8);
                //减去买家冻结法币资产
                $whereD = ['userid' => $buyid];
                //总支出，包含付给卖家的法币和差价
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal . 'd', $buyLegalNum, FALSE);
                //补差价
                $frozen = bcsub($buyLegalNum, $legalNum, 8);
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $legal, $frozen, TRUE);
                //增加买家货币资产(扣除手续费之后)
                ModelHandle::UpdateFieldNum('UserCoin', $whereD, $coin, $realCoinNum, TRUE);

                //更新币种价格
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
