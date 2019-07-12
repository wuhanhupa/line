<?php

namespace Common\Design\Order;

class Done implements Handle
{
    public function handle($id)
    {
        try {
            M()->startTrans();

            $order = M('CtwocLog')->where(array('id' => $id))->find();

            //只有已付款的订单才能完成
            if ($order['status'] != 1) {

				throw new \Exception('只有已付款的订单才能完成');
            }

            //广告单
            if ($order['type'] == 1) {
                $where = array('id' => $order['buyid']);
            } else {
                $where = array('id' => $order['sell_id']);
            }
            $ctwoc = M('Ctwoc')->where($where)->find();

            //增加买家usdt资产
            M('UserCoin')->where(array('userid' => $order['userid']))->setInc('usdt', floatval($order['num']));
            //减去卖家冻结usdt资产
            M('UserCoin')->where(array('userid' => $order['peerid']))->setDec('usdtd', floatval($order['num']));
            //减去广告单冻结数量
            M('Ctwoc')->where($where)->setDec('rest_num', $order['num']);
            //如果广告单剩余数量为0，修改状态为已完成
            if ($ctwoc['rest'] == 0) {
                M('Ctwoc')->where($where)->save(array('status' => 3));
            }
            //当前订单修改状态为已完成
            M('CtwocLog')->where(array('id' => $id))->save(array('status' => 2));
            //生成一条流水记录
            M('CtwocCenter')->add(array(

        	));

			M()->commit();

            return ['status' => 1, 'msg' => '订单已完成'];

        } catch (\Exception $e) {

			M()->rollback();

            $msg = $e->getMessage();

			return ['status' => 0, 'msg' => $msg];
        }
    }
}
