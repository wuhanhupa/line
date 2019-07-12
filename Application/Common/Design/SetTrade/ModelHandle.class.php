<?php

namespace Common\Design\SetTrade;

class ModelHandle
{
    /**
     * 更新某个字段的值根据条件.
     *
     * @param [string] $tableName 表名
     * @param [array]  $where     [where条件]
     * @param [string] $field     [字段名]
     * @param [float]  $num       [数值]
     * @param bool     $self      [自增还是自减]
     *
     * @return bool
     */
    public static function UpdateFieldNum($tableName, $where, $field, $num, $self = true)
    {
        //自增
        if ($self == true) {
            return M($tableName)->where($where)->setInc($field, $num);
        } else {
            //自减
            return M($tableName)->where($where)->setDec($field, $num);
        }
    }

    /**
     * 根据ID更新表数据.
     *
     * @param [type] $tableName [description]
     * @param [type] $id        [description]
     * @param array  $data      [description]
     *
     * @return bool
     */
    public static function UpdateDataByWhere($tableName, $where, $data = array())
    {
        return M($tableName)->where($where)->save($data);
    }

    /**
     * 添加数据到模型.
     *
     * @param [type] $tableName [description]
     * @param array  $data      [description]
     *
     * @return mixed
     */
    public static function AddDataToTable($tableName, $data = array())
    {
        return M($tableName)->add($data);
    }

    /**
     * 添加一条交易记录.
     *
     * @param [type] $buyid  [买家ID]
     * @param [type] $sellid [description]
     * @param [type] $market [description]
     * @param [type] $price  [description]
     * @param [type] $num    [description]
     * @param [type] $type   [description]
     *
     * @return mixed
     */
    public static function AddTradeLog($buyid, $sellid, $market, $price, $num, $type)
    {
        $data = array();
        $data['userid'] = $buyid; //买家ID
        $data['peerid'] = $sellid; //买家ID
        $data['market'] = $market; //交易对
        $data['price'] = $price; //成交价格
        $data['num'] = $num; //成交数量
        $data['mum'] = bcmul($price, $num, 8); //成交总额
        $data['type'] = $type; //交易类型
        $data['fee_buy'] = bcmul(bcmul($num, $price, 8), 0.003, 8); //买入手续费(存法币手续费)
        $data['fee_sell'] = bcmul($num, 0.003, 8); //卖出手续费（存虚拟币手续费）

        $data['addtime'] = time(); //添加时间
        $data['status'] = 1; //状态

        return M('TradeLog')->add($data);
    }

    /**
     * 添加一条财务记录.
     *
     * @param [type] $market   [交易对]
     * @param [type] $userid   [用户ID]
     * @param [type] $coinname [法币名称]
     * @param [type] $num      [操作数量]
     * @param bool   $buy      [收入还是支出]
     *
     * @return mixed
     */
    public static function AddFinance($market, $userid, $coinname, $num, $buy = true)
    {
        //查询更新之前的用户资产
        $property = M('UserCoin')->where(array('userid' => $userid))->find();
        //modellog('用户ID是'.$userid);
        $data = array();
        $data['num_a'] = $property[$coinname];
        $data['num_b'] = $property[$coinname.'d'];
        $data['num'] = bcadd($property[$coinname], $property[$coinname.'d'], 8);
        $data['fee'] = $num;
        //modellog(print_r($data));
        //收入
        if ($buy) {
            $data['type'] = 1;
            $data['remark'] = '交易中心-成功买入-市场'.$market;
            $data['mum_a'] = bcadd($property[$coinname], $num, 8);
            $data['mum_b'] = $property[$coinname.'d'];
            $data['mum'] = bcadd($data['num'], $num, 8);
        } else {
            //支出
            $data['type'] = 2;
            $data['remark'] = '交易中心-成功卖出-市场'.$market;
            $data['mum_a'] = $property[$coinname];
            //$data['mum_b'] = bcsub($property[$coinname.'d'], $num, 8);
            $data['mum_b'] = $num;
            $data['mum'] = bcsub($data['num'], $num, 8);
        }
        $data['userid'] = $userid;
        $data['coinname'] = $coinname;
        $data['name'] = 'tradelog';
        $data['nameid'] = 0;
        $data['move'] = 'this is a trade';
        $data['addtime'] = time();
        $data['status'] = 1;

        //存入日志
        //modellog(print_r($data));

        return M('Finance')->add($data);
    }

    /**
     * 更新行情价格
     *
     * @param [type] $market [description]
     *
     * @return bool
     */
    public static function UpdateMarketPrice($market)
    {
        //更新币种价格
        $new_price = round(M('TradeLog')->where(array('market' => $market))->order('addtime desc')->getField('price'), 6);
        $buy_price = round(M('Trade')->where(array('type' => 1, 'market' => $market, 'status' => 0))->max('price'), 6);
        $sell_price = round(M('Trade')->where(array('type' => 2, 'market' => $market, 'status' => 0))->min('price'), 6);
        $min_price = round(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24)),
        ))->min('price'), 6);
        $max_price = round(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24)),
        ))->max('price'), 6);
        /*$volume = round(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24)),
        ))->sum('num'), 6);*/
        $sta_price = round(M('TradeLog')->where(array(
            'market' => $market,
            'addtime' => array('gt', time() - (60 * 60 * 24)),
        ))->order('addtime asc')->getField('price'), 6);

        $Cmarket = M('Market')->where(array('name' => $market))->find();

        if ($Cmarket['new_price'] != $new_price) {
            $upCoinData['new_price'] = $new_price;
        }

        if ($Cmarket['buy_price'] != $buy_price) {
            if ($buy_price > 0) {
                $upCoinData['buy_price'] = $buy_price;
            }
        }

        if ($Cmarket['sell_price'] != $sell_price) {
            if ($sell_price > 0) {
                $upCoinData['sell_price'] = $sell_price;
            }
        }

        if ($Cmarket['min_price'] != $min_price) {
            $upCoinData['min_price'] = $min_price;
        }

        if ($Cmarket['max_price'] != $max_price) {
            $upCoinData['max_price'] = $max_price;
        }

        /*if ($Cmarket['volume'] != $volume) {
            $upCoinData['volume'] = $volume;
        }*/

        $change = round((($new_price - $sta_price) / $sta_price) * 100, 2);
        $upCoinData['change'] = $change;

        if ($upCoinData) {
            return M('Market')->where(array('name' => $market))->save($upCoinData);
        }

        return false;
    }
}
