<?php

namespace Common\Design\Trade;

interface BaseInterface
{
	public function handle($market, $price, $num, $coinname, $coin);
}