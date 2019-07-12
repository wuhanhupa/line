<?php

namespace Common\Model;

class MarketModel extends \Think\Model
{
    /**
     * 获取最新成交价.
     */
    public function get_new_price($market = null)
    {
        if (empty($market)) {
            return null;
        }

        $get_new_price = (APP_DEBUG ? null : S('get_new_price_'.$market));

        if (!$get_new_price) {
            $get_new_price = M('Market')->where(array('name' => $market))->getField('new_price');
            S('get_new_price_'.$market, $get_new_price);
        }

        return $get_new_price;
    }

    /**
     * 组装title.
     */
    public function get_title($market = null)
    {
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $xnb_title = D('Coin')->get_title($xnb);
        $rmb_title = D('Coin')->get_title($rmb);

        return $xnb_title.'/'.$rmb_title;
    }

    /**
     * 获取在线交易对信息.
     */
    public function getOnlineMarkets()
    {
        $markets = M('Market')->where(array('status' => 1))->select();

        $data = array();

        foreach ($markets as $v) {
            $v['new_price'] = round($v['new_price'], $v['round']);
            $v['buy_price'] = round($v['buy_price'], $v['round']);
            $v['sell_price'] = round($v['sell_price'], $v['round']);
            $v['min_price'] = round($v['min_price'], $v['round']);
            $v['max_price'] = round($v['max_price'], $v['round']);
            $v['hou_price'] = round($v['hou_price'], $v['round']);
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $v['xnbimg'] = D('Coin')->get_img($v['xnb']);
            $v['rmbimg'] = D('Coin')->get_img($v['rmb']);
            $v['volume'] = $v['volume'];
            $v['change'] = $v['change'];
            $v['tendency'] = $v['tendency'];
            //$v['title'] = D('Coin')->get_title($v['xnb']).'('.strtoupper($v['xnb']).'/'.strtoupper($v['rmb']).')';
            $v['title'] = strtoupper($v['xnb']) . '<span>'.D('Coin')->get_title($v['xnb']) .'</span>';
            $data[$v['name']] = $v;
        }

        return $data;
    }

    /**
     * 获取在线币种信息，api接口使用.
     */
    public function getOnlineCoinInfo()
    {
        $markets = M('Market')->where(array('status' => 1))->select();

        $arr = array();
        foreach ($markets as $k => $v) {
            $xnb = explode('_', $v['name'])[0];
            //中文名
            $arr[$k]['title'] = D('Coin')->get_title($xnb);
            //图片
            $arr[$k]['img'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Upload/coin/'.D('Coin')->get_img($xnb);
            //最新成交价
            if ($v['new_price'] >= 0.01) {
                $price = '$ '.sprintf('%.2f', $v['new_price']);
            } else {
                $price = '<$ 0.01';
            }

            $arr[$k]['price'] = $price;
            //换算人民币
            $arr[$k]['cny_price'] = '￥'.bcmul($v['new_price'], getRate(), 4);
            //成交总计
            $amount = D('Market')->getVolumeByMarket($v['name']);
            $arr[$k]['amount'] = '$'.numberFormat($amount);
            //日涨跌
            $arr[$k]['change'] = round($v['change'], 2);
        }

        return $arr;
    }

    protected function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }

    /**
     * 从redis中获取交易对的成交总量.
     */
    public function getVolumeByMarket($pair)
    {
        $redis = $this->connectRedis();
        $json = $redis->hget('exp:coin:market:volume', $pair);
        $array = json_decode($json, true);

        return $array['volume'];
    }
}
