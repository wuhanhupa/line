<?php

namespace Admin\Controller;

use Think\Page;

class FupayController extends AdminController
{
    //入金列表
    public function index($name = NULL)
    {
        $where = [];

        if ($name) {
            $where['userid'] = M('User')->where(['username' => $name])->getField('id');
        }
        $where['type'] = 1;
        $where['status'] = 2;

        $count = M('FupayOrder')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('FupayOrder')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }

        $this->assign('name', $name);
        $this->assign('list', $list);
        $this->assign('page', $show);

        $this->display();
    }

    //出金列表
    public function outs($name = NULL)
    {
        $where = [];

        if ($name) {
            $where['userid'] = M('User')->where(['username' => $name])->getField('id');
        }
        $where['type'] = 2;

        $count = M('FupayOrder')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('FupayOrder')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }

        $this->assign('name', $name);
        $this->assign('list', $list);
        $this->assign('page', $show);

        $this->display();
    }

    //支付信息
    public function info($id)
    {
        //订单
        $order = M('FupayOrder')->where(['id' => $id])->find();
        //用户银行卡信息
        $bank = M('UserBank')->where(['userid' => $order['userid']])->find();

        $this->assign('bank', $bank);
        $this->assign('order', $order);

        $this->display();
    }

    //确认出金，修改订单状态
    public function check($id, $status)
    {
        //订单
        $order = M('FupayOrder')->where(['id' => $id])->find();
        if ($order['status'] != 1) {
            $this->error('订单状态已锁定');
        }
        try {
            M()->startTrans();

            //取消订单
            M('FupayOrder')->where(['id' => $id])->save(['status' => 3]);
            //返回冻结资产
            //M('UserCoin')->where(['userid' => $order['userid']])->setDec('usdtd', $order['num']);
            //M('UserCoin')->where(['userid' => $order['userid']])->setInc('usdt', $order['num']);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setDec('freeze', $order['mum']);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setInc('balance', $order['mum']);

            M()->commit();
            $this->redirect('/admin/Fupay/outs');
        } catch (\Exception $e) {
            M()->rollback();
            $msg = $e->getMessage();
            $this->error($msg);
        }
    }

    //确认出金
    public function over($id, $num)
    {
        if (empty($num)) {
            $this->error('数量必须');
        }

        $order = M('FupayOrder')->where(['id' => $id])->find();
        if ($order['status'] != 1) {
            $this->error('订单状态已锁定');
        }
        try {
            M()->startTrans();
            //完成订单
            M('FupayOrder')->where(['id' => $id])->save(['status' => 2, 'num' => $num]);
            //回滚冻结资产
            //M('UserCoin')->where(['userid' => $order['userid']])->setDec('usdtd', $order['num']);
            //M('UserCoin')->where(['userid' => $order['userid']])->setInc('usdt', $order['num']);
            //减去用户实际扣除资产
            //M('UserCoin')->where(['userid' => $order['userid']])->setDec('usdt', $num);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setDec('freeze', $order['mum']);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setInc('balance', $order['mum']);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setDec('balance', $num);
            M('ContractUserCoinResult')->where([
                'user_id' => $order['userid'], 'coin_name' => 'usdt'
            ])->setDec('total', $num);

            M()->commit();
            $this->redirect('/admin/Fupay/outs');
        } catch (\Exception $e) {
            M()->rollback();
            $msg = $e->getMessage();
            $this->error($msg);
        }
    }
}