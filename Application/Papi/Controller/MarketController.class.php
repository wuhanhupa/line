<?php

namespace Papi\Controller;

//行情相关
class MarketController extends CommonController
{
    /**
     * 获取所有币种
     * 币种名称，图片，交易量，最新价，日涨跌.
     */
    public function allcoin()
    {
        $markets = M('Market')->where(['status' => 1])->select();

        $arr = [];
        foreach ($markets as $k => $v) {
            $xnb = explode('_', $v['name'])[0];
            //中文名
            $arr[$k]['title'] = (string)D('Coin')->get_title($xnb);
            //图片
            $arr[$k]['img'] = (string)$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . trim(D('Coin')->get_app_img($xnb));
            //最新成交价
            if ($v['new_price'] >= 0.01) {
                $price = (string)'$ ' . sprintf('%.2f', $v['new_price']);
            } else {
                $price = '<$ 0.01';
            }

            $arr[$k]['price'] = (string)$price;
            //换算人民币
            $arr[$k]['cny_price'] = (string)'￥' . bcmul($v['new_price'], getRate(), 4);
            //成交总计
            $amount = D('Market')->getVolumeByMarket($v['name']);
            $arr[$k]['amount'] = (string)'$' . numberFormat($amount);
            //日涨跌
            $arr[$k]['change'] = (string)round($v['change'], 2);
            //小数位
            $arr[$k]['price_round'] = (string)$v['price_round'];
            $arr[$k]['num_round'] = (string)$v['num_round'];
            $arr[$k]['name'] = (string)$v['name'];
        }

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $arr;

        $this->ajaxReturn($info);
    }

    //币种名称列表
    public function allcoinmenu()
    {
        $data = [];
        $coins = M('Coin')->where(['status' => 1])->select();

        foreach ($coins as $k => $v) {
            $arr['name'] = strtoupper($v['name']) . '/USDT';
            $arr['id'] = $v['id'];

            $data[] = $arr;
        }

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }

    //k线
    public function ordinary($market = NULL)
    {
        if (!$market) {
            $market = 'bys_usdt';
        }

        $this->assign('market', $market);
        $this->display();
    }

    //k想头部数据
    public function getMarket($market = 'bys_usdt')
    {
        $arr = M('Market')->where(['name' => $market])->find();

        $data['new_price'] = round($arr['new_price'], $arr['price_round']);
        $data['max_price'] = round($arr['max_price'], $arr['price_round']);
        $data['min_price'] = round($arr['min_price'], $arr['price_round']);
        $data['volume'] = numberFormat($arr['volume']);
        $data['change'] = round($arr['change'], 2);
        $data['rate'] = getRate();

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }

    //合约首页行情
    public function contractList()
    {
        $list = M('ContractMarket')->select();
        $data = [];
        foreach ($list as $k => $v) {
            $xnb = explode('_', $v['market'])[0];
            $rmb = explode('_', $v['market'])[1];
            $data[$k]['market'] = (string)$v['market'];
            $data[$k]['title'] = (string)strtoupper($xnb); //. ' ' . strtoupper($xnb) . '/' . strtoupper($rmb);
            $data[$k]['img'] = (string)$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . trim(D('Coin')->get_app_img($xnb));
            $data[$k]['new_price'] = (string)round($v['new_price'], 2); //最新成交价
            $data[$k]['total'] = (string)$v['total']; //总成交量
            $data[$k]['min_price'] = (string)$v['min_price']; //24H最低价
            $data[$k]['max_price'] = (string)$v['max_price']; //24H最高价
            $data[$k]['volume'] = (string)$v['volume']; //24H成交量
            $data[$k]['change'] = (string)round($v['change'], 2); //24H涨跌幅
            $data[$k]['cny_price'] = (string)bcmul($v['new_price'], getRate(), 2);
        }

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }
}
