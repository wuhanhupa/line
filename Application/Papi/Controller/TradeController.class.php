<?php

namespace Papi\Controller;

use Common\Design\SetTrade\ModelHandle;
use Think\Page;
use Common\Design\Suanfa\SnowFlake;
use Common\Design\Cancel\Collection as CancelCollection;
use Common\Design\SetTrade\Collection as SetCollection;

class TradeController extends CommonController
{
    /**
     * Notice: 获取深度排行
     * author: hxq
     * @param $jmcode
     */
    public function getDepth($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            //交易对
            $market = $result['data']['market'];
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            //交易市场设置
            $marketData = M('Market')->where(['name' => $market])->find();
            if (!$marketData) {
                $info['code'] = -1;
                $info['msg'] = '交易对错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }

            //获取条数
            $limt = 6;
            $redis = $this->connectRedis();
            $buyKey = 'spot:depth:' . strtoupper($market) . ':buy';
            $sellKey = 'spot:depth:' . strtoupper($market) . ':sell';

            $length = $limt - 1;

            $buy = $redis->ZREVRANGE($buyKey, 0, $length);
            $sell = $redis->zRange($sellKey, 0, $length);

            $max = 0;

            if ($buy) {
                foreach ($buy as $k => $v) {
                    $arr = explode('@', $v);
                    $data['buy'][$k] = [
                        'price' => (string)round($arr[1], $marketData['price_round']),
                        'num' => (string)round($arr[0], $marketData['num_round'])
                    ];

                    if ($arr[0] > $max) {
                        $max = $arr[0];
                    }
                }
            } else {
                $data['buy'] = [];
            }

            if ($sell) {
                foreach ($sell as $k => $v) {
                    $arr = explode('@', $v);
                    $data['sell'][$k] = [
                        'price' => (string)round($arr[1], $marketData['price_round']),
                        'num' => (string)round($arr[0], $marketData['num_round'])
                    ];

                    if ($arr[0] > $max) {
                        $max = $arr[0];
                    }
                }
            } else {
                $data['sell'] = [];
            }

            if ($marketData['new_price'] >= 0.01) {
                $price = '$ ' . sprintf('%.2f', $marketData['new_price']);
            } else {
                $price = '<$ 0.01';
            }

            $data['max'] = $max;
            $data['new_price'] = (string)$price;
            $data['change'] = (string)round($marketData['change'], 2);
            $data['rate'] = getRate();

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * Notice: 我的委托
     * author: hxq
     * @param $jmcode
     */
    public function myEntrust($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            $isall = $result['data']['isall'] ? $result['data']['isall'] : 1;
            $p = $result['data']['p'];
            $pagesize = $result['data']['pagesize'];
            $order = $result['data']['order'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            //页码
            if (empty($p)) {
                $p = 1;
            }
            if (empty($pagesize)) {
                $pagesize = 15;
            }
            if (empty($order)) {
                $order = 'desc';
            }
            if ($isall == 1) {
                $where = ['userid' => $userid, 'market' => $market, 'status' => 0];
            } else if ($isall == 2) {
                $where = ['userid' => $userid, 'status' => 0];
            }

            $start = ($p - 1) * $pagesize;
            $trades = M('Trade')->where($where)->order('addtime ' . $order)->limit($start . ',' . $pagesize)->select();

            //交易市场设置
            $marketData = M('Market')->where(['name' => $market])->find();

            //处理数据
            $data = [];
            foreach ($trades as $k => $v) {
                $data[$k]['id'] = (string)$v['id'];
                $data[$k]['type'] = (string)$v['type'];
                $data[$k]['num'] = (string)round($v['num'], $marketData['num_round']);
                $data[$k]['price'] = (string)round($v['price'], $marketData['price_round']);
                $data[$k]['time'] = (string)date('Y-m-d H:i:s', $v['addtime']);
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
     * Notice: 我的成交
     * author: hxq
     * @param $jmcode
     */
    public function myDeal($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            $p = $result['data']['p'];
            $pagesize = $result['data']['pagesize'];
            $order = $result['data']['order'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            //页码
            if (empty($p)) {
                $p = 1;
            }
            if (empty($pagesize)) {
                $pagesize = 15;
            }
            if (empty($order)) {
                $order = 'desc';
            }
            //交易市场设置
            $marketData = M('Market')->where(['name' => $market])->find();

            if (!$marketData) {
                $info['code'] = -1;
                $info['msg'] = '交易对错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }

            $where = '((userid=' . $userid . ') || (peerid=' . $userid . ')) && market=\'' . $market . '\'';

            $start = ($p - 1) * $pagesize;
            $trades = M('TradeLog')->where($where)->order('addtime ' . $order)->limit($start . ',' . $pagesize)->select();

            //处理数据
            $data = [];
            foreach ($trades as $k => $v) {
                $data[$k]['type'] = (string)$v['type'];
                $data[$k]['num'] = (string)round($v['num'], $marketData['num_round']);
                $data[$k]['price'] = (string)round($v['price'], $marketData['price_round']);
                $data[$k]['time'] = (string)date('Y-m-d H:i:s', $v['addtime']);
            }
            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //全球行情
    public function globalTrade()
    {
        $markets = M('Market_global')->field('market,symbol_pair,vol,change_daily,last,bid,ask')->order('id desc')->limit(20)->select();

        //人民币价格
        $cny = M('Rate')->where(['id' => 1])->getField('rate');

        $data = [];
        //处理数据
        foreach ($markets as $k => $val) {
            $data[$k]['market'] = (string)$val['market']; //交易所名称
            $data[$k]['pair'] = (string)$val['symbol_pair']; //交易对
            $data[$k]['vol'] = (string)numberFormat($val['vol']); //交易量
            $data[$k]['change'] = (string)($val['change_daily'] * 100) . '%'; //涨跌幅
            $data[$k]['bid'] = (string)$val['bid']; //报价
            $data[$k]['bid_cny'] = (string)bcmul($val['bid'], $cny, 2); //人民币价格
        }

        $info['code'] = 0;
        $info['msg'] = '成功';
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }

    /**
     * Notice: 全站成交
     * author: hxq
     * @param $jmcode
     */
    public function allDeal($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $p = $result['data']['p'];
            $pagesize = $result['data']['pagesize'];
            $order = $result['data']['order'];

            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对不能为空';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            //页码
            if (empty($p)) {
                $p = 1;
            }
            if (empty($pagesize)) {
                $pagesize = 15;
            }
            if (empty($order)) {
                $order = 'desc';
            }
            $where = ['market' => $market];
            $count = M('Trade')->where($where)->count();
            $Page = new Page($count, $pagesize);

            $trades = M('TradeLog')->where($where)->order('addtime ' . $order)->limit($Page->firstRow . ',' . $Page->listRows)->select();

            //交易市场设置
            $marketData = M('Market')->where(['name' => $market])->find();

            $rate = getRate();

            //处理数据
            $data = [];
            foreach ($trades as $k => $v) {
                $data[$k]['type'] = (string)$v['type'];
                $data[$k]['num'] = (string)round($v['num'], $marketData['num_round']);
                $data[$k]['price'] = (string)round($v['price'], $marketData['price_round']);
                $data[$k]['time'] = (string)date('Y-m-d H:i:s', $v['addtime']);
                $data[$k]['rate'] = $rate;
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
     * Notice: 保存委托提交
     * author: hxq
     * @param $jmcode
     */
    public function saveEntrust($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $price = $result['data']['price'];
            $num = $result['data']['num'];
            $type = $result['data']['type'];
            $market = $result['data']['market'];
            $paypassword = $result['data']['paypassword'];
            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($price)) {
                $info['code'] = -1;
                $info['msg'] = '价格必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($num)) {
                $info['code'] = -1;
                $info['msg'] = '数量必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($type)) {
                $info['code'] = -1;
                $info['msg'] = '类型必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($paypassword)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码必须！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            //验证用户实名认证（有没有提交身份证照片）
            $user = M('User')->where(['id' => $userid])->find();
            if ($user['ident_status'] != 2) {
                $info['code'] = -1;
                $info['msg'] = '请先通过实名认证！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if ($user['paypassword'] != md5($paypassword)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码错误！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (!check($price, 'double')) {
                $info['code'] = -1;
                $info['msg'] = '交易价格格式错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (!check($num, 'double')) {
                $info['code'] = -1;
                $info['msg'] = '交易数量格式错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (($type != 1) && ($type != 2)) {
                $info['code'] = -1;
                $info['msg'] = '交易类型格式错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            $agora = M('Market')->where(['name' => $market])->find();
            if (!$agora) {
                $info['code'] = -1;
                $info['msg'] = '交易市场错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            } else {
                $xnb = explode('_', $market)[0];
                $rmb = explode('_', $market)[1];
            }
            $price = round($price, $agora['price_round']);
            if (!$price) {
                $info['code'] = -1;
                $info['msg'] = '交易价格错误' . $price;
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            $num = round($num, $agora['num_round']);
            if (!check($num, 'double')) {
                $info['code'] = -1;
                $info['msg'] = '交易数量错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if ($type == 1) {
                $min_price = ($agora['buy_min'] ? $agora['buy_min'] : 1.0E-8);
                $max_price = ($agora['buy_max'] ? $agora['buy_max'] : 10000000);
            } else if ($type == 2) {
                $min_price = ($agora['sell_min'] ? $agora['sell_min'] : 1.0E-8);
                $max_price = ($agora['sell_max'] ? $agora['sell_max'] : 10000000);
            } else {
                $info['code'] = -1;
                $info['msg'] = '交易类型错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if ($max_price < $price) {
                $info['code'] = -1;
                $info['msg'] = '交易价格超过最大限制！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if ($price < $min_price) {
                $info['code'] = -1;
                $info['msg'] = '交易价格超过最小限制！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            $user_coin = M('UserCoin')->where(['userid' => $userid])->find();
            if ($type == 1) {
                $trade_fee = 0.3;
                if ($trade_fee) {
                    $fee = round((($num * $price) / 100) * $trade_fee, 8);
                    $mum = round((($num * $price) / 100) * (100 + $trade_fee), 8);
                } else {
                    $fee = 0;
                    $mum = round($num * $price, 8);
                }
                if ($user_coin[$rmb] < $mum) {
                    $info['code'] = -1;
                    $info['msg'] = '余额不足！';
                    $info['data'] = ['info' => $info['msg']];

                    $this->ajaxReturn($info);
                }
            } else if ($type == 2) {
                $trade_fee = 0.3;
                if ($trade_fee) {
                    $fee = round((($num * $price) / 100) * $trade_fee, 8);
                    $mum = round((($num * $price) / 100) * (100 - $trade_fee), 8);
                } else {
                    $fee = 0;
                    $mum = round($num * $price, 8);
                }
                if ($user_coin[$xnb] < $num) {
                    $info['code'] = -1;
                    $info['msg'] = '余额不足！';
                    $info['data'] = ['info' => $info['msg']];

                    $this->ajaxReturn($info);
                }
            } else {
                $info['code'] = -1;
                $info['msg'] = '交易类型错误';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if ($agora['trade_min']) {
                if ($mum < $agora['trade_min']) {
                    $info['code'] = -1;
                    $info['msg'] = '交易总额不能小于' . $agora['trade_min'];
                    $info['data'] = ['info' => $info['msg']];

                    $this->ajaxReturn($info);
                }
            }
            if ($agora['trade_max']) {
                if ($agora['trade_max'] < $mum) {
                    $info['code'] = -1;
                    $info['msg'] = '交易总额不能大于' . $agora['trade_max'];
                    $info['data'] = ['info' => $info['msg']];

                    $this->ajaxReturn($info);
                }
            }
            //组装数据
            $alog = new SnowFlake();
            $id = $alog->nextId();

            //首先生成委托单
            //首先生成订单
            $create = $this->createOrder($id, $userid, $market, $price, $num, $type);
            if ($create['status'] == 0) {
                $info['code'] = -1;
                $info['msg'] = $create['msg'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $data = [];
            $data['userId'] = (int)$userid;
            $data['orderId'] = (int)$id;
            $data['time'] = (int)time();
            $data['price'] = (float)$price;
            $data['pair'] = strtoupper($market);
            if ($type == 2) {
                $data['bidFlag'] = 0;
            } else {
                $data['bidFlag'] = 1;
            }
            $data['orderType'] = 2;
            $data['amount'] = (float)$num;
            $data['exchangedAmount'] = 0;
            $str = 'orderId:orderId_hupa.com' . ',price:' . $data['price'] . ',number:' . $data['amount'] . ',bidFlag:' . $data['bidFlag'] . ',pair:' . $data['pair'] . ',tradeType:2,validateType:order';

            $data['token'] = md5($str);

            $res = $this->setTradeToRobot($market, $data);
            //执行成功
            if (isset($res) && $res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '成功推送给智能引擎！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            } else {
                //给某人发短信，引擎失败了
                //noticeZW();

                $info['code'] = -1;
                $info['msg'] = '下单失败，稍后重试。';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }
    protected function createOrder($id, $userid, $market, $price, $num, $type)
    {
        try {
            //开启事物
            M()->startTrans();

            $fee = bcmul($price, $num, 8);
            $add = M('Trade')->add([
                'id' => $id,
                'userid' => $userid,
                'market' => $market,
                'price' => $price,
                'num' => $num,
                'deal' => 0,
                'mum' => $num,
                'fee' => $fee,
                'type' => $type,
                'addtime' => time(),
                'status' => 0,
                'trade_type' => 2,
            ]);

            if (!$add) {
                throw new \Exception('新增失败');
            }
            $coin = explode('_', $market)[0]; //交易货币
            $legal = explode('_', $market)[1]; //法币

            //冻结资产
            $where = ['userid' => $userid];
            //买入，冻结usdt
            if ($type == 1) {
                //判断可用资产是否足够
                $able = M('UserCoin')->where($where)->getField($legal);
                if ($able < $fee) {
                    throw new \Exception($legal . '资产不足');
                }
                //减去可用资产
                ModelHandle::UpdateFieldNum('UserCoin', $where, $legal, $fee, FALSE);
                //增加冻结资产
                ModelHandle::UpdateFieldNum('UserCoin', $where, $legal . 'd', $fee, TRUE);
            } else {
                //判断资产
                $able = M('UserCoin')->where($where)->getField($coin);
                if ($able < $num) {
                    throw new \Exception($coin . '资产不足');
                }
                //减去可用虚拟币
                ModelHandle::UpdateFieldNum('UserCoin', $where, $coin, $num, FALSE);
                //增加冻结虚拟币
                ModelHandle::UpdateFieldNum('UserCoin', $where, $coin . 'd', $num, TRUE);
            }

            M()->commit();

            return ['status' => 1, 'msg' => 'success'];
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            M()->rollback();

            return ['status' => 0, 'msg' => $msg];
        }
    }
    /**
     * Notice: 撤销委托单
     * author: hxq
     * @param $jmcode
     */
    public function cancelEntrust($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $tradeid = $result['data']['tradeid'];
            $isall = $result['data']['isall'];
            $market = $result['data']['market'];
            //撤销当前市场
            /*if ($isall == 1) {
                if (empty($market)) {
                    $info['code'] = -1;
                    $info['msg'] = '交易对不能为空';
                    $info['data'] = [];

                    $this->ajaxReturn($info);
                }
                if (empty($userid)) {
                    $info['code'] = -1;
                    $info['msg'] = '用户ID不能为空';
                    $info['data'] = [];

                    $this->ajaxReturn($info);
                }

                $trades = M('Trade')->where(['userid' => $userid, 'status' => 0, 'market' => $market]);

                foreach ($trades as $trade) {
                    //通知搬币机器人订单已撤销
                    $res = $this->noticeRobot($trade['id'], $market);

                    $rs[] = $res['status'];
                }
                $info['code'] = 0;
                $info['msg'] = '撤单申请已提交给智能引擎！';
                $info['data'] = [];

                $this->ajaxReturn($info);
            } //撤销全部
            else if ($isall == 2) {
                if (empty($userid)) {
                    $info['code'] = -1;
                    $info['msg'] = '用户ID不能为空';
                    $info['data'] = [];

                    $this->ajaxReturn($info);
                }

                $trades = M('Trade')->where(['userid' => $userid, 'status' => 0]);

                foreach ($trades as $trade) {
                    //通知搬币机器人订单已撤销
                    $res = $this->noticeRobot($trade['id'], $market);

                    $rs[] = $res['status'];
                }
                $info['code'] = 0;
                $info['msg'] = '撤单申请已提交给智能引擎！';
                $info['data'] = [];

                $this->ajaxReturn($info);
            } else { */
            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            if (empty($tradeid)) {
                $info['code'] = -1;
                $info['msg'] = '委托单ID不能为空';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            $trade = M('Trade')->where(['id' => $tradeid])->find();
            if (!$trade) {
                $info['code'] = -1;
                $info['msg'] = '委托单不存在';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            if ($trade['status'] != 0) {
                $info['code'] = -1;
                $info['msg'] = '委托单不能撤销';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            if ($trade['userid'] != $userid) {
                $info['code'] = -1;
                $info['msg'] = '委托人错误';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            //首先撤销委托单，然后通知撮合引擎
            $cancel = CancelCollection::cancelOrder($tradeid);
            if ($cancel['status'] != 1) {
                $info['code'] = -1;
                $info['msg'] = $cancel['msg'];
                $info['data'] = [];

                $this->ajaxReturn($info);
            }

            //通知搬币机器人订单已撤销
            $market = $trade['market'];
            $res = $this->noticeRobot($tradeid, $market);
            if ($res['status'] == 1) {
                $info['code'] = 0;
                $info['msg'] = '撤单申请已提交给智能引擎！';
                $info['data'] = [];

                $this->ajaxReturn($info);
            } else {
                //给某人发短信，引擎失败了
                //noticeZW();
                $info['code'] = -1;
                $info['msg'] = '请求失败，请稍后重试。';
                $info['data'] = [];

                $this->ajaxReturn($info);
            }
            // }
        } else {
            $this->apierror();
        }
    }

    //将用户下单信息推送给撮合机器人
    private function setTradeToRobot($market, $data)
    {
        //撮合机器人接收下单信息地址
        $url = getUrlByMarket($market) . '/api/order/create_order';

        $json = curl_post_http($url, $data);

        $res = json_decode($json, TRUE);

        return $res;
    }

    //通知搬币机器人取消订单
    private function noticeRobot($id, $market)
    {
        $url = getUrlByMarket($market) . '/api/order/cancel_order';

        $data = ['orderId' => $id, 'pair' => strtoupper($market), 'time' => msectime()];

        $str = 'orderId:' . $id . ',price:price_hupa.com,number:number_hupa.com,bidFlag:bidFlag_hupa.com,pair:pair_hupa.com,tradeType:tradeType_hupa.com,validateType:order';

        $data['token'] = md5($str);

        $result = curl_post_http($url, $data);

        $result = json_decode($result, TRUE);

        if ($result['code'] == 0) {
            $status = 1;
            $msg = '撮合机器人已接收';
        } else {
            $status = 0;
            $msg = '失败';
        }

        return ['status' => $status, 'msg' => $msg];
    }

    //交易市场列表
    public function marketlist()
    {
        $markets = M('Market')->where(['status' => 1])->select();

        $arr = [];
        foreach ($markets as $k => $v) {
            $xnb = explode('_', $v['name'])[0];
            //中文名
            $arr[$k]['title'] = D('Coin')->get_title($xnb);
            //图片
            $arr[$k]['img'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . trim(D('Coin')->get_app_img($xnb));
            //最新成交价
            if ($v['new_price'] >= 0.01) {
                $price = '$ ' . sprintf('%.2f', $v['new_price']);
            } else {
                $price = '<$ 0.01';
            }

            $arr[$k]['price'] = $price;

            $arr[$k]['name'] = $v['name'];

            //日涨跌
            $arr[$k]['change'] = (string)round($v['change'], 2);

            //小数位
            $arr[$k]['price_round'] = $v['price_round'];
            $arr[$k]['num_round'] = $v['num_round'];
        }

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $arr;

        $this->ajaxReturn($info);
    }
}
