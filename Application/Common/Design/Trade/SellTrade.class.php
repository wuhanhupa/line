<?php

namespace Common\Design\Trade;

//币币交易卖出
class SellTrade implements BaseInterface
{
    public function handle($market, $price, $num, $coinname, $coin)
    {
        try {
            //开启事物
            M()->startTrans();

            //冻结用户货币资产
            M('UserCoin')->where(array('userid' => userid()))->setDec($coinname, $num);
            M('UserCoin')->where(array('userid' => userid()))->setInc($coinname.'d', $num);
            $algo = new \Common\Design\Suanfa\SnowFlake(0,0);
            $id = $algo->nextId();
            //写入委托单记录
            M('Trade')->add(array(
                'id' => $id,
                'userid' => userid(),
                'market' => $market,
                'price' => $price,
                'num' => $num,
                'mum' => $num,
                'fee' => 0,
                'type' => 2,
                'addtime' => time(),
                'status' => 0,
                'trade_type' => 2,
            ));

            //执行自动匹配用户单
            $match = new \Common\Design\Matching\Collection($market, 2, $id);
            $match->entry();

            M()->commit();

            return ['errcode' => 1, 'msg' => '下单成功'];
        } catch (\Exception $e) {
            M()->rollback();

            $msg = $e->getMessage();

            return ['errcode' => 0, 'msg' => $msg];
        }
    }
}
