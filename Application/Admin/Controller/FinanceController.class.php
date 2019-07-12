<?php

namespace Admin\Controller;

use Think\Page;
use Common\Design\Redis\Collection;

/**
 * Class FinanceController
 * 后台财务
 * @package Admin\Controller
 */
class FinanceController extends AdminController
{
    public function index($field = NULL, $name = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Finance')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Finance')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $name_list = ['mycz' => '人民币充值', 'mytx' => '人民币提现', 'trade' => '委托交易', 'tradelog' => '成功交易', 'issue' => '用户认购'];
        $nameid_list = ['mycz' => U('Mycz/index'), 'mytx' => U('Mytx/index'), 'trade' => U('Trade/index'), 'tradelog' => U('Tradelog/index'), 'issue' => U('Issue/index')];

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['num_a'] = Num($v['num_a']);
            $list[$k]['num_b'] = Num($v['num_b']);
            $list[$k]['num'] = Num($v['num']);
            $list[$k]['fee'] = Num($v['fee']);
            $list[$k]['type'] = ($v['type'] == 1 ? '收入' : '支出');
            $list[$k]['name'] = ($name_list[$v['name']] ? $name_list[$v['name']] : $v['name']);
            $list[$k]['nameid'] = ($name_list[$v['name']] ? $nameid_list[$v['name']] . '?id=' . $v['nameid'] : '');
            $list[$k]['mum_a'] = Num($v['mum_a']);
            $list[$k]['mum_b'] = Num($v['mum_b']);
            $list[$k]['mum'] = Num($v['mum']);
            $list[$k]['addtime'] = addtime($v['addtime']);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 虚拟币转入
     * @param null $field
     * @param null $name
     * @param null $coinname
     */
    public function myzr($field = NULL, $name = NULL, $coinname = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($coinname) {
            $where['coinname'] = $coinname;
        }

        $count = M('Mycz')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Mycz')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $market = $this->getmarket($v['coinname']);
            $list[$k]['coinname'] = explode('-', $market)[0];
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 虚拟币转出
     * @param null $field
     * @param null $name
     * @param null $coinname
     */
    public function myzc($field = NULL, $name = NULL, $coinname = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($coinname) {
            $where['coinname'] = $coinname;
        }
        $where['status'] = ['in', [1, 2, 3]];
        $count = M('Mytx')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $market = $this->getmarket($v['coinname']);
            $list[$k]['coinname'] = explode('-', $market)[0];
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 对应用户和币种的交易记录
     * @param null $userid
     * @param null $coinname
     */
    public function trade($userid = NULL, $coinname = NULL)
    {
        $where['userid'] = $userid;
        $where['market'] = $coinname . '_usdt';

        $count = M('TradeLog')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('TradeLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 对应用户的充提记录
     * @param null $userid
     * @param null $coinname
     */
    public function info($userid = NULL, $coinname = NULL)
    {
        $where['userid'] = $userid;
        $where['coinname'] = $this->getnumber($coinname);

        $count = M('Mytx')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['coinname'] = $coinname;
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 法币挂单列表
     * @param null $field
     * @param null $name
     * @param null $type
     */
    public function put($field = NULL, $name = NULL, $type = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($type) {
            $where['type'] = $type;
        }

        $count = M('Ctwoc')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Ctwoc')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['usernamea'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 法币交易单列表
     * @param null $field
     * @param null $name
     * @param null $status
     */
    public function trades($field = NULL, $name = NULL, $status = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($status) {
            $where['status'] = $status;
        }

        $count = M('CtwocLog')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('CtwocLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['buyname'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['sellname'] = M('User')->where(['id' => $v['peerid']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 交易明细
     * @param null $field
     * @param null $name
     * @param null $status
     */
    public function details($field = NULL, $name = NULL, $status = NULL)
    {
        $where = [];
        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }
        if ($status) {
            $where['status'] = $status;
        }

        $count = M('CtwocCenter')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('CtwocCenter')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['sellname'] = M('User')->where(['id' => $v['usersell']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //提现审核列表
    public function checkturn($field = NULL, $name = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        //$where['status'] = ['in', [0, 2, 3]];
        $where['status'] = 0;

        $count = M('Mytx')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Mytx')->where($where)->order('id desc')->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $market = $this->getmarket($v['coinname']);
            $list[$k]['coinname'] = explode('-', $market)[0];
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //审核详情页面
    public function checkinfo($id)
    {
        $mytx = M('Mytx')->where(['id' => $id])->find();
        $user = M('User')->where(['id' => $mytx['userid']])->find();
        $market = $this->getmarket($mytx['coinname']);
        $mytx['coinname'] = $market;
        $coin['ky'] = M('UserCoin')->where(['userid' => $mytx['userid']])->getField($market);
        $coin['dj'] = M('UserCoin')->where(['userid' => $mytx['userid']])->getField($market . 'd');

        $this->assign('mytx', $mytx);
        $this->assign('user', $user);
        $this->assign('coin', $coin);

        $this->display();
    }

    //签名数组转化方法
    public function getstr($array)
    {
        $res = [];
        foreach ($array as $key => $val) {
            $arr = ['name' => $key, 'value' => $val];
            $res[] = $arr;
        }
        
        return json_encode($res);
    }

    //连接redis
    protected function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS'));
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }

    //获取当前的币种代码
    public function getnumber($market)
    {
        $number = M('Bz')->where(['market' => $market])->getField('number');

        return $number;
    }

    //获取当前的币种名稱
    public function getmarket($number)
    {
        $market = M('Bz')->where(['number' => $number])->getField('market');

        return $market;
    }

    //向区块发送提现申请
    public function checktx($id, $status)
    {
        //查找提现订单
        $tx = M('Mytx')->where(['id' => $id])->find();
        //成功
        if ($status == 1) {
            //判断是否超过转出区间设置（币种设置）
            $market = $this->getmarket($tx['coinname']);
            $coin = M('Coin')->where(['name' => $market])->find();
            if ($tx['num'] < $coin['zc_min']) {
                $info['status'] = '2001';
                $info['info'] = '提现数额小于最小转出限额，请驳回申请！';

                $this->ajaxReturn($info);
            }
            if ($tx['num'] > $coin['zc_max']) {
                $info['status'] = '2001';
                $info['info'] = '提现数额大于最大转出限额，请驳回申请！';

                $this->ajaxReturn($info);
            }
            //单日限额
            $zc_zd = $coin['zc_zd'];
            //查询今日转出
            $start = strtotime(date('Y-m-d', time()));
            $end = strtotime(date('Y-m-d', strtotime('+1 day', time())));
            $total = M('Mytx')->where([
                'userid' => $tx['userid'],
                'coinname' => $tx['coinname'],
                'status' => ['in', [0, 1]],
                'addtime' => ['between', [$start, $end]],
            ])->sum('num');

            if (!$total) {
                $total = 0;
            }

            if (bcadd($total, $tx['num'], 8) > $zc_zd) {
                $info['status'] = '2001';
                $info['info'] = '超过单日转出限额，请驳回申请！';

                $this->ajaxReturn($info);
            }

            //调用redis封装
            $redis = new Collection();
            //提现推送redis
            $res = $redis->redispush(6, $tx['userid'], $tx['coinname'], '', $tx['mum'], $tx['address'], $tx['mark']);

            if (!$res) {
                $info['status'] = '2003';
                $info['info'] = '推送redis失败，请重新审核!';

                $this->ajaxReturn($info);
            } else {
                //修改状态为处理中
                M('Mytx')->where(['id' => $id])->save(['status' => 3]);
            }
        } else {
            //驳回必须是状态为0
            if ($tx['status'] != 0) {
                $info['status'] = '2003';
                $info['info'] = '区块处理中，不能驳回!';

                $this->ajaxReturn($info);
            }
            //失败
            M('Mytx')->where(['id' => $id])->save(['status' => 2]);
            //回复用户资产
            $market = $this->getmarket($tx['coinname']);
            $coin = explode('_', $market)[0]; //交易货币
            //增加可用资产
            M('UserCoin')->where(['userid' => $tx['userid']])->setInc($coin, $tx['num']);
            //减去冻结资产
            M('UserCoin')->where(['userid' => $tx['userid']])->setDec($coin . 'd', $tx['num']);
        }
        $info['status'] = '1000';
        $info['info'] = '操作成功';

        $this->ajaxReturn($info);
    }

    //搬币机器人当前余额.
    public function robot()
    {
        $redis = $this->connectRedis();
        //汇总
        $res = $redis->hGetAll('exp:gte:account:carryRobot');
        //处理数据
        $data = [];
        foreach ($res as $key => $val) {
            $arr = json_decode($val, TRUE);
            $data[$key] = $arr;
        }

        $this->assign('data', $data);

        $this->display();
    }

    /**
     * 根据类型获取对应资产余额
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

    //机器人挂单列表
    public function robotall($p = 1, $coinname = NULL, $status = NULL, $name = NULL)
    {
        //请求地址
        $url = C('ROBOT_URL') . '/api/select_order';
        //print_r($url);exit;
        $pageSize = 20;
        $arr = [];
        if ($coinname) {
            $arr['pair'] = strtoupper($coinname . '_usdt');
        }
        if ($status) {
            $arr['order_status'] = $status;
        }
        if ($name) {
            $arr['origin_order_id'] = $name;
        }

        $arr['current_page'] = $p;
        $arr['page_size'] = $pageSize;
        $arr['req_timestamp'] = msectime();
        $arr = http_build_query($arr);
        $url = $url . '?' . $arr;

        $result = curl_get_https($url);
        $res = json_decode($result, TRUE);
        //dump($res);
        if ($res['code'] == 0) {
            $count = $res['data']['totalCount'];
            $Page = new Page($count, $pageSize);
            $show = $Page->show();

            $list = $res['data']['rows'];
            //处理备注
            foreach ($list as &$v) {
                $remark = json_decode($v['remark'], TRUE);
                $v['remark'] = $remark['message'];
            }
        }
        //dump($list);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //搬币机器人挂单列表
    public function robotlist($p = 1, $coinname = NULL)
    {
        //请求地址
        $url = C('ROBOT_URL') . '/api/select_trade';
        $pageSize = 20;
        $arr = [];
        if ($coinname) {
            $arr['pair'] = strtoupper($coinname . '_usdt');
        }

        $arr['current_page'] = $p;
        $arr['page_size'] = $pageSize;
        $arr['req_timestamp'] = msectime();
        $arr = http_build_query($arr);
        $url = $url . '?' . $arr;
        $result = curl_get_https($url);
        $res = json_decode($result, TRUE);
        //dump($res);
        if ($res['code'] == 0) {
            $count = $res['data']['totalCount'];
            $Page = new Page($count, $pageSize);
            $show = $Page->show();

            $list = $res['data']['rows'];
        }
        //dump($list);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //coinx交易所资产余额
    public function coinxlist()
    {
        $redis = $this->connectRedis();
        //汇总
        $res = $redis->hGetAll('exp:coinx:account:klineRobot');
        //处理数据
        $arr = $data = [];
        foreach ($res as $key => $val) {
            $arr = json_decode($val, TRUE);
            $data[$key] = $arr;
        }

        $this->assign('data', $data);

        $this->display();
    }
}
