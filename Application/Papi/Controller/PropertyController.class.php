<?php

namespace Papi\Controller;

/**
 * 资产api.
 */
class PropertyController extends CommonController
{
    /**
     * 资产列表.
     */
    public function property_list($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $user = M('User')->where(['id' => $userid])->find();

            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $data = [];
            $data['summary'] = D('UserCoin')->getUserSummaryProperty($userid);
            $data['list'] = D('UserCoin')->getUserCoinList($userid);

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * 充值获取系统地址
     */
    public function recharge_address($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = array('info'=>$info['msg']);

                $this->ajaxReturn($info);
            }

            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '币种名称不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $user = M('User')->where(['id' => $userid])->find();

            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            //验证token
            /*$token = $_SERVER['X_TOKEN'];
            if ($token != $user['token']) {
                $info['msg'] = 'token错误！';
                $info['code'] = -1;
                $info['data'] = $_SERVER;

                $this->ajaxReturn($info);
            }*/
            //查找虚拟币
            $coin = M('Coin')->where(['name' => $market, 'status' => 1])->find();
            if (!$coin) {
                $info['code'] = -1;
                $info['msg'] = '币种未上线！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
            if (!checkCoin($market)) {
                $info['code'] = -1;
                $info['msg'] = '该币未开发充提功能！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
            //获取虚拟币代码
            $number = D('Coin')->getSymbol($market);
            //强制使用eth地址
            if ($number > 0 && $number < 1000) {
                $numberOne = 1;
            } else {
                $numberOne = $number;
            }
            //1判断当前的用户对应的这个币种是否已经有地址如果有直接返回如果没有就生成
            $userAddr = M('User_qianbao')->where(['userid' => $userid, 'coinname' => $numberOne, 'type' => 1])->getField('addr');

            if (empty($userAddr)) {
                //区块交互redis操作
                $block = new \Common\Design\Redis\Collection();
                $push = $block->redispush(1, $userid, $number); //请求生成对应的钱包地址写入消息队列
                //获取出队消息
                if ($push) {
                    $userAddr = $block->createAddressHandle($userid, $number);
                } else {
                    $info['code'] = -1;
                    $info['msg'] = '网络不给力，请刷新再试！';
                    $info['data'] = array('info'=>$info['msg']);
                    $this->ajaxReturn($info);
                }
            }

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = ['address' => $userAddr];

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * 提现申请.
     */
    public function withdraw_cash($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            $num = $result['data']['num'];
            $address = $result['data']['address'];

            //处理地址
            $arr = explode(':', $address);
            if (count($arr) == 2) {
                $address = $arr[1];
            }

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '币种名称不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (empty($num)) {
                $info['code'] = -1;
                $info['msg'] = '提现数额不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (empty($address)) {
                $info['code'] = -1;
                $info['msg'] = '提现地址不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $user = M('User')->where(['id' => $userid])->find();

            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            //查找虚拟币
            $coin = M('Coin')->where(['name' => $market, 'status' => 1])->find();
            if (!$coin) {
                $info['code'] = -1;
                $info['msg'] = '币种未上线！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (!checkCoin($market)) {
                $info['code'] = -1;
                $info['msg'] = '该币未开发充提功能！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            //验证地址正确性
            if (!getValidWayByCoinname($market, $address)) {
                $info['code'] = -1;
                $info['msg'] = '不是一个有效的钱包地址';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            //验证余额是否足够
            $balance = M('UserCoin')->where(['userid' => $userid])->getField($market);
            if ($balance < $num) {
                $info['code'] = -1;
                $info['msg'] = '可用余额不足';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
            //虚拟币代码
            $symbol = D('Coin')->getSymbol($market);
            try {
                //开启事物
                M()->startTrans();
                //第一步凍結貨幣當前貨幣數量
                //1.查詢原數量扣除需要提現的數量
                $ynum = M('UserCoin')->where(['userid' => $userid])->getField($market);
                $xnum = bcsub($ynum, $num, 8);
                $resOne = M('UserCoin')->where(['userid' => $userid])->save([$market => $xnum]);
                //2 凍結數量
                $marketd = $market . 'd';
                $bznum = M('UserCoin')->where(['userid' => $userid])->getField($marketd);
                $nums = bcadd($bznum, $num, 8);
                $resTwo = M('UserCoin')->where(['userid' => $userid])->save([$marketd => $nums]);
                //3.修改當前用戶的幣種凍結的數量
                if ($resOne && $resTwo) {
                    $mark = uniqid();
                    //提现手续费
                    $coin = M('Coin')->where(['name' => $market])->find();
                    $fee = $coin['zc_fee'];
                    if ($fee >= $num) {
                        throw new \Exception('提现数量不够扣除手续费！');
                    }
                    //单次限额
                    $zc_max = $coin['zc_max'];
                    if ($num > $zc_max) {
                        throw new \Exception('超过单次转出限额！');
                    }
                    //单日限额
                    $zc_zd = $coin['zc_zd'];
                    //查询今日转出
                    $start = strtotime(date('Y-m-d', time()));
                    $end = strtotime(date('Y-m-d', strtotime('+1 day', time())));
                    $total = M('Mytx')->where([
                        'userid' => userid(), 'coinname' => $symbol, 'status' => ['in', [0, 1]], 'addtime' => ['between', [$start, $end]],
                    ])->sum('num');
                    if (bcadd($total, $num, 8) > $zc_zd) {
                        throw new \Exception('超过单日转出限额！');
                    }

                    //实际到账额度
                    $mum = bcsub($num, $fee, 8);
                    //插入数据库
                    $sql = 'insert into btchanges_mytx (userid,coinname,address,num,fee,mum,type,mark,addtime) values(' .
                        '\'' . $userid . '\', ' .
                        '\'' . $symbol . '\', '
                        . '\'' . $address . '\', '
                        . '\'' . $num . '\', '
                        . '\'' . $fee . '\', '
                        . '\'' . $mum . '\', '
                        . '\'' . 'tx' . '\', '
                        . '\'' . $mark . '\', '
                        . '\'' . time() . '\')';

                    $m = M('Mytx');
                    $cz = $m->execute($sql);

                    if ($cz) {
                        M()->commit();

                        $info['code'] = 0;
                        $info['msg'] = '提现申请已提交，请等待工作人员处理。';
                        $info['data'] = ['address' => $address];

                        $this->ajaxReturn($info);
                    } else {
                        throw new \Exception('服务器请求失败');
                    }
                } else {
                    throw new \Exception('资产修改失败');
                }
            } catch (\Exception $e) {
                M()->rollback();
                $msg = $e->getMessage();
                $info['code'] = -1;
                $info['msg'] = $msg;
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    /**
     * 获取虚拟币的提现手续费和限额.
     */
    public function market_assist($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '币种名称不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $coin = M('Coin')->where(['name' => $market, 'status' => 1])->find();
            if (!$coin) {
                $info['code'] = -1;
                $info['msg'] = '币种未上线！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (!checkCoin($market)) {
                $info['code'] = -1;
                $info['msg'] = '该币未开发充提功能！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            $data['fee'] = $coin['zc_fee'];
            $data['min'] = $coin['zc_min'];
            $data['max'] = $coin['zc_max'];

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * 账单列表.
     */
    public function bill_list($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            $type = $result['data']['type'] ? $result['data']['type'] : 0;
            $page = $result['data']['page'] ? $result['data']['page'] : 1;
            $pagesize = 10 * $page;
            $offset = ($page - 1) * $pagesize;

            $number = $this->getnumber($market);

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '虚拟币名称不能为空';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if ($type == 0) {
                $mycz = M('Mycz')->where(['userid' => $userid, 'coinname' => $number])->field('mum,type,coinname,addtime,status')->order('addtime desc')->select();
                $mytx = M('Mytx')->where(['userid' => $userid, 'coinname' => $number])->field('mum,type,coinname,addtime,status')->order('addtime desc')->select();
                //合并
                $merge = array_merge($mycz, $mytx);
                //排序
                $merge = array_sort($merge, 'addtime');
                $data = array_slice($merge, $offset, $pagesize - 1);
            }
            //充值
            if ($type == 1) {
                $data = M('Mycz')->field('mum,type,coinname,addtime,status')
                    ->where(['userid' => $userid, 'coinname' => $number])
                    ->order('id desc')
                    ->limit($offset, $pagesize)->select();
            }
            //提现
            if ($type == 2) {
                $data = M('Mytx')->field('mum,type,coinname,addtime,status')
                    ->where(['userid' => $userid, 'coinname' => $number])
                    ->order('id desc')
                    ->limit($offset, $pagesize)->select();
            }
            //处理虚拟币代码
            foreach ($data as &$val) {
                $val['coinname'] = strtoupper(D('Coin')->getMarket($val['coinname']));
            }

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * 单独验证虚拟币是否开启充提功能.
     */
    public function check_turn($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];

            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '币种名称不能为空';
                $info['data'] = array('info'=>$info['msg']);

                $this->ajaxReturn($info);
            }

            $coin = M('Coin')->where(['name' => $market, 'status' => 1])->find();
            if (!$coin) {
                $info['code'] = -1;
                $info['msg'] = '币种未上线！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            }

            if (!checkCoin($market)) {
                $info['code'] = -1;
                $info['msg'] = '该币未开发充提功能！';
                $info['data'] = array('info'=>$info['msg']);
                $this->ajaxReturn($info);
            } else {
                $info['code'] = 0;
                $info['msg'] = '可以充提';
                $info['data'] = ['market' => $market];

                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }
}
