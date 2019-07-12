<?php

namespace Common\Model;

class UserCoinModel extends \Think\Model
{
    /**
     * 计算用户总资产.
     */
    public function getUserSummaryProperty($userid)
    {
        if (!$userid) {
            return false;
        }
        $UserCoin = M('UserCoin')->where(array('userid' => $userid))->find();
        if (!$UserCoin) {
            return false;
        }
        //获取所有开放的币种
        $coins = M('Coin')->where(array('status' => 1))->field('name')->select();
        //美元
        $usdt = 0;
        foreach ($coins as $coin) {
            if ($coin['name'] == 'usdt') {
                $usdt = bcadd($usdt, bcadd($UserCoin['usdt'], $UserCoin['usdtd'], 8), 8);
            } else {
                //获取市场最新成交价
                $newPrice = M('Market')->where(array('name' => $coin['name'].'_usdt'))->getField('new_price');
                //换算成usdt
                $usdt = bcadd($usdt, bcmul(bcadd($UserCoin[$coin['name']], $UserCoin[$coin['name'].'d'], 8), $newPrice, 8), 8);
            }
        }
        //$data = sprintf('%.2f', $usdt);
        //如果需要换算成人民币
        $data = bcmul($usdt, getRate(), 2);

        return $data;
    }

    /**
     * 获取用户资产列表.
     */
    public function getUserCoinList($userid)
    {
        if (!$userid) {
            return false;
        }
        //获取用户资产
        $UserCoin = M('UserCoin')->where(array('userid' => $userid))->find();
        if (!$UserCoin) {
            return false;
        }
        //获取所有开放的币种
        $coins = M('Coin')->where(array('status' => 1))->field('name,title')->select();
        $data = array();
        foreach ($coins as $k => $coin) {
            //组装列表
            $data[$k]['title'] = $coin['title'];
            $data[$k]['name'] = $coin['name'];
            $data[$k]['balance'] = bcadd($UserCoin[$coin['name']], 0, 6);
            $data[$k]['freeze'] = bcadd($UserCoin[$coin['name'].'d'], 0, 6);
            $data[$k]['total'] = bcadd($UserCoin[$coin['name']], $UserCoin[$coin['name'].'d'], 6);
            $data[$k]['img'] = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Upload/coin/'.trim(D('Coin')->get_app_img($coin['name']));
        }

        return $data;
    }
}
