<?php

namespace Mapi\Controller;

use Common\Design\Matching\MatchCollection;

class MatchController extends CommonController
{
    public function matching()
    {
        //查询所有在线币种的交易对
        $markets = M('Market')->where(['status' => 1])->field('name')->select();
        //针对每个币种执行一次自动匹配
        foreach ($markets as $key => $market) {
            try {
                $match = new MatchCollection($market['name']);
                $match->entry();
            } catch (\Exception $e) {
                $msg = $e->getMessage();
                echo $msg;
            }
            sleep(1);
        }
    }

    public function bsprice($pair, $token)
    {
        //交易对买一卖一的价格
        $auth_token = md5($pair);
        if ($auth_token != $token) {
            $info['info'] = '验证失败';
            $info['status'] = '2000';
            $this->ajaxReturn($info);
        }

        $market = strtolower($pair);
        $market_b = M('Trade')->where(['market' => $market, 'type' => 1, 'status' => 0])->order('price desc')->field('price')->find();
        $market_s = M('Trade')->where(['market' => $market, 'type' => 2, 'status' => 0])->order('price asc')->field('price')->find();
        $market_lasttime = M('Trade_log')->where(['market' => $market])->order('addtime desc')->field('addtime')->find();
        if (empty($market_lasttime)) {
            $info['lastTradeTimestamp'] = -1;
        }

        $info['buy'] = $market_b['price'];
        $info['sell'] = $market_s['price'];
        $info['lastTradeTimestamp'] = $market_lasttime['addtime'];
        $this->ajaxReturn($info);
    }

    /**
     * 短信告警.
     * 自动脚本.
     * [1-搬币usdt 2-coinx eth 3-coinx bys].
     */
    public function notice()
    {
        $array = [1, 2, 3];

        foreach ($array as $type) {
            //获取余额
            $balance = $this->getBalanceByType($type);
            echo $balance;
            echo "\n";
            //检查余额
            $check = $this->checkNotice($type, $balance);
            //当前余额不足，需要发送告警短信
            if ($check === FALSE) {
                //判断是否已发送
                $notice = M('Notice')->where(['type' => $type])->getField('notice');
                //没有发送，触发短信
                if ($notice == 0) {
                    $title = $this->getTitleByType($type);
                    $symbol = $this->getSymbolByType($type);
                    //短信模板
                    $template = "【GTE数字平台】{$title}:{$symbol},余额不足,剩余{$balance},请尽快处理";
                    //echo $template;
                    //从配置数据库获取运营手机号码
                    $phone = M('Config')->where(['id' => 1])->getField('contact_moble');
                    //echo $phone;
                    //发送短信
                    $res = sendSms($phone, $template, 0, '');

                    if ($res['Code'] == 0) {
                        echo '通知成功';
                        //标明已发送短信
                        M('Notice')->where(['type' => $type])->save(['notice' => 1]);
                    } else {
                        echo $res['Message'];
                    }
                }
            } else {
                //余额充足的情况下，重置notice值为0
                M('Notice')->where(['type' => $type])->save(['notice' => 0]);
            }
        }
    }

    /**
     * Notice:判断是否需要发送告警短信
     * 1-搬币usdt 2-coinx eth 3-coinx bys
     * 1-130 2-0.5 3-4000
     * 12小时不重复发送
     * @param $type
     * @param $balance
     * @return bool
     */
    protected function checkNotice($type, $balance)
    {
        $balance = round($balance, 2);
        if ($type == 1) {
            if ($balance >= 500) {
                return TRUE;
            }
        }
        if ($type == 2) {
            if ($balance >= 0.8) {
                return TRUE;
            }
        }
        if ($type == 3) {
            if ($balance >= 4000) {
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Notice:根据类型获取对应资产余额
     * @author: hxq
     * @param $type
     * @return float
     */
    protected function getBalanceByType($type)
    {
        $redis = $this->connectRedis();
        switch ($type) {
            case 1:
                $res = $redis->hGetAll('exp:gte:account:carryRobot');
                //获取USDT余额
                $usdt = json_decode($res['USDT'], TRUE);
                $balance = $usdt['available'];
                break;
            case 2:
                $res = $redis->hGetAll('exp:coinx:account:klineRobot');
                $eth = json_decode($res['ETH'], TRUE);
                $balance = $eth['available'];
                break;
            case 3:
                $res = $redis->hGetAll('exp:coinx:account:klineRobot');
                $eth = json_decode($res['BYS'], TRUE);
                $balance = $eth['available'];
                break;
        }

        return round($balance, 2);
    }

    /**
     * Notice:根据类型获取短信模板
     * @author: hxq
     * @param $type
     * @return mixed
     */
    protected function getSymbolByType($type)
    {
        switch ($type) {
            case 1:
                $str = 'usdt';
                break;
            case 2:
                $str = 'eth';
                break;
            case 3:
                $str = 'bys';
                break;
        }
        $number = D('Coin')->getSymbol($str);
        if (!$number) {
            $number = 2001;
        }

        return $number;
    }

    /**
     * Notice:根据类型获取平台名称
     * @author: hxq
     * @param $type
     * @return string
     */
    protected function getTitleByType($type)
    {
        switch ($type) {
            case 1:
                $str = '搬砖帐号';
                break;
            case 2:
                $str = 'Coinx帐号';
                break;
            case 3:
                $str = 'Coinx帐号';
                break;
        }

        return $str;
    }
}
