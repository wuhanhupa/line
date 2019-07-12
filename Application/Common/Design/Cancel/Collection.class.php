<?php

namespace Common\Design\Cancel;

//撤销订单
class Collection
{
	//取消订单
	public static function cancelOrder($id)
	{
		$trade = M('Trade')->where(array('id' => $id))->find();

		if (count($trade) == 0) {
			return ['status' => 0, 'msg' => '订单不存在'];
		}

		if ($trade['status'] != 0) {
			return ['status' => 0, 'msg' => '订单已完成或已撤销'];
		}

		$market = $trade['market'];
		$coin  = explode('_', $market)[0]; //交易货币
        $legal = explode('_', $market)[1]; //法币

        if ($trade['deal'] == 0) {
        	//未成交
        	$num = $trade['num'];

        } else {
        	//有成交数量
        	$num = bcsub($trade['num'], $trade['deal'], 8);
		}
		//法币总额
		$total = bcmul($num, $trade['price'], 8);  //冻结总额

    	try {
    		//开启事物
        	M()->startTrans();

        	//修改订单状态
        	M('Trade')->where(array('id' => $id))->save(array('status' => 2));
        	//查询用户资产
        	$where = array('userid' => $trade['userid']);

        	//买单
        	if ($trade['type'] == 1) {
        		//减少法币冻结资产
        		M('UserCoin')->where($where)->setDec($legal.'d', $total);
        		//增加法币可用
        		M('UserCoin')->where($where)->setInc($legal, $total);
        	} else {
        		//减少虚拟币冻结资产
        		M('UserCoin')->where($where)->setDec($coin.'d', $num);
        		//增加虚拟币可用资产
        		M('UserCoin')->where($where)->setInc($coin, $num);
        	}

        	M()->commit();

        	return ['status' => 1, 'msg' => $id. ':撤销成功'];

    	} catch (\Exception $e) {

    		M()->rollback();

    		$msg = $e->getMessage();

    		return ['status' => 0, 'msg' => $msg];
    	}

	}
}
