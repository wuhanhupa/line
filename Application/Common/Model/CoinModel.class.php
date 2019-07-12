<?php

namespace Common\Model;

class CoinModel extends \Think\Model
{
    public function get_all_name_list()
    {
        $list = M('Coin')->where(array())->order('sort asc')->select();

        if (is_array($list)) {
            foreach ($list as $k => $v) {
                $get_all_name_list[$v['name']] = $v['title'];
            }
        } else {
            $get_all_name_list = null;
        }

        return $get_all_name_list;
    }

    public function get_all_xnb_list()
    {
        $list = M('Coin')->where()->order('sort asc')->select();

        if (is_array($list)) {
            foreach ($list as $k => $v) {
                if ($v['type'] != 'rmb') {
                    $get_all_xnb_list[$v['name']] = $v['title'];
                }
            }
        } else {
            $get_all_xnb_list = null;
        }

        return $get_all_xnb_list;
    }

    public function get_title($name = null)
    {
        if (empty($name)) {
            return null;
        }

        $get_title = M('Coin')->where(array('name' => $name))->getField('title');

        return $get_title;
    }

    public function get_img($name = null)
    {
        if (empty($name)) {
            return null;
        }

        $get_img = M('Coin')->where(array('name' => $name))->getField('img');

        return $get_img;
    }

    public function get_app_img($name = null)
    {
        if (empty($name)) {
            return null;
        }

        $get_img = M('Coin')->where(array('name' => $name))->getField('imgapp');

        return $get_img;
    }

    /**
     * 获取在线币种信息.
     */
    public function getOnlineCoinInfo()
    {
        $coins = M('Coin')->where(array('status' => 1))->field('name,title,img')->select();

        return $coins;
    }

    /**
     * 根据虚拟币名称获取虚拟币代码
     */
    public function getSymbol($name)
    {
        return M('Bz')->where(array('market' => $name))->getField('number');
    }

    /**
     * 根据虚拟币代码获取虚拟币名称.
     */
    public function getMarket($symbol)
    {
        return M('Bz')->where(array('number' => $symbol))->getField('market');
    }
}
