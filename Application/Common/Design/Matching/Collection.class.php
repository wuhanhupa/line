<?php

namespace Common\Design\Matching;

//匹配交易
class Collection
{
	public $market; //交易对

	public $type; //类型

	public $id; //订单ID

	public $handle;

	public function __construct($market, $type, $id)
	{
		$this->market = $market;
		$this->type = $type;
		$this->id = $id;
	}

	public function entry()
	{
		if ($this->type == 1) {
			$this->setHandle(new BuyMatch());
		}

		if ($this->type == 2) {
			$this->setHandle(new SellMatch());
		}

		return $this->handle->handle($this->market, $this->id);
	}

	public function setHandle(Handle $handle)
	{
		$this->handle = $handle;
	}
}
