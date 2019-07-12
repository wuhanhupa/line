<?php

namespace Common\Design\Trade;

/**
 * 处理币币交易下单流程
 */
class Collection
{
	public $market; //交易对

	public $price; //挂单价格

	public $num; //挂单数量

	public $type; //挂单类型 1买入 2卖出

	public $coinname; //币种名称

	public $coin; //法币名称

	public $handle;

	public function __construct($market, $price, $num, $type)
	{
		$this->market = $market;
		$this->price = $price;
		$this->num = $num;
		$this->type = $type;
		$this->coinname = explode('_', $market)[0];
		$this->coin = explode('_', $market)[1];
	}

	public function entry()
	{	
		//买入
		if ($this->type == 1) {
			$this->setHandle(new BuyTrade());
		}

		//卖出
		if ($this->type == 2) {
			$this->setHandle(new SellTrade());
		}

		return $this->handle->handle($this->market, $this->price, $this->num, $this->coinname, $this->coin);
	}

	public function setHandle(BaseInterface $interface)
	{
		$this->handle = $interface;
	}
}