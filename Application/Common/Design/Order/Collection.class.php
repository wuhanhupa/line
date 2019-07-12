<?php

namespace Common\Design\Order;

/**
 * 策略模式修改C2C订单状态
 */
class Collection
{
	public $status; //订单状态

	public $orderid; //订单ID

	public $handle; //处理流程

	public function __construct($status, $orderid)
	{
		$this->status = $status;

		$this->orderid = $orderid;
	}

	//执行入口
	public function entry()
	{
		//订单已付款
		if ($this->status == 1) {
			$this->setHandle(new Pay());
		}

		//订单已完成
		if ($this->status == 2) {
			$this->setHandle(new Done());
		}

		//订单已取消
		if ($this->status == 3) {
			$this->setHandle(new Cancel());
		}

		return $this->handle->handle($this->orderid);
	}

	//分配接口
	public function setHandle(Handle $handle)
	{
		$this->handle = $handle;
	}
}