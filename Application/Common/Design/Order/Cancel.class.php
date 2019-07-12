<?php

namespace Common\Design\Order;

class Cancel implements Handle
{
    public function handle($id)
    {
        try {
            //启动事务
            M()->startTrans();

            $order = M('CtwocLog')->where(array('id' => $id))->find();

            //只能取消未付款的订单
            if ($order['status'] != 0) {
                throw new \Exception('只能取消未付款的订单');
            }
            //取消订单
            M('CtwocLog')->where(array('id' => $id))->save(array('status' => 4));

            $where = array('userid' => $order['perrid']);
            //恢复卖家的冻结法币资产
            $UserCoin = M('UserCoin')->where($where)->find();

            if ($UserCoin) {
                //增加usdt
                \Common\Design\Matching\ModelHandle::UpdateFieldNum('UserCoin', $where, 'usdt', $order['num'], true);
                //减去冻结usdt
                \Common\Design\Matching\ModelHandle::UpdateFieldNum('UserCoin', $where, 'usdtd', $order['num'], false);
                //写入财务明细表
            }
            //广告单
            if ($order['type'] == 1) {
                $where = array('id' => $order['buyid']);
            } else {
                $where = array('id' => $order['sell_id']);
            }
            //修改广告单冻结数量
            M('Ctwoc')->where($where)->setDec('rest_num', $order['num']);
            //增加广告单剩余数量
            M('Ctwoc')->where($where)->setInc('rest', $order['num']);

            M()->commit();

            return ['status' => 1, 'msg' => '取消成功'];
        } catch (\Exception $e) {
            M()->rollback();

            $msg = $e->getMessages();

            return ['status' => 0, 'msg' => $msg];
        }
    }
}
