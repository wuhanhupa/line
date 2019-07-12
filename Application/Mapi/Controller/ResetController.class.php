<?php

namespace Mapi\Controller;

/**
 * 自动补资产
 */
class ResetController extends CommonController
{
    /**
     * 自动给test用户和机器人用户补充资产
     */
    public function add()
    {
        $money = M('UserCoin')->where(['id' => ['in', [1, 159]]])->select();

        $coins = M('Coin')->where(['status' => 1])->select();

        foreach($money as $value) {
            foreach($coins as $coin) {
                if ($value[$coin['name']] < 1000) {
                    $res = M('UserCoin')->where(['id' => $value['id']])->save([
                        $coin['name'] => 99999999
                    ]);

                    echo $res;
                }
            }
        }
    }
}