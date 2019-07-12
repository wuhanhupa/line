<?php

namespace Common\Design\Order;

class Pay implements Handle
{
	public function handle($id)
	{
		$order = M('CtwocLog')->where(array('id' => $id))->find();

		//只有未付款的订单才能改为已付款
		if ($order['status'] != 0) {
			return ['status' => 0, 'msg' => '只有未付款的订单才能改为已付款'];
		}
		//修改订单状态
		M('CtwocLog')->where(array('id' => $id))->save(array('status' => 1));

		return ['status' => 1, 'msg' => '付款成功'];
	}
}
