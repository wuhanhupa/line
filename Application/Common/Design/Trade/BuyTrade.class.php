<?php

namespace Common\Design\Trade;

//币币交易买入
class BuyTrade implements BaseInterface
{
    public function handle($market, $price, $num, $coinname, $coin)
    {
        try {
            //开启事物
            M()->startTrans();

            //用户资产
            $property = M('UserCoin')->where(array('userid' => userid()))->find();
            //冻结用户法币资产
            $value = bcmul($price, $num, 8); //需要冻结的法币总数
            M('UserCoin')->where(array('userid' => userid()))->setDec($coin, $value);
            M('UserCoin')->where(array('userid' => userid()))->setInc($coin.'d', $value);
            $algo = new \Common\Design\Suanfa\SnowFlake(0,0);
            $id = $algo->nextId();
            //写入委托单记录(trade_type为2是用户下单)
            M('Trade')->add(array(
                'id' => $id,
                'userid' => userid(),
                'market' => $market,
                'price' => $price,
                'num' => $num,
                'mum' => $num,
                'fee' => 0,
                'type' => 1,
                'addtime' => time(),
                'status' => 0,
                'trade_type' => 2,
            ));

            //执行自动匹配用户单
            $match = new \Common\Design\Matching\Collection($market, 1, $id);
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
