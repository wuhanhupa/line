<?php

namespace Papi\Controller;

use Common\Design\Contract\ContractSimulation;

class ContractController extends CommonController
{
    //k线
    public function chart($market = 'btc_usdt')
    {
        $this->assign('market', $market);

        $this->display();
    }

    //处理交易对
    private function handlePair($pair)
    {
        $arr = explode('_', $pair);

        return strtoupper($arr[0]) . '/' . strtoupper($arr[1]);
    }

    //状态 1:下单失败;2:下单成功;4:已有成交;8:全部成交;16撤单
    private function handleStatus($status)
    {
        if ($status == 1) return '下单失败';
        if ($status == 2) return '下单成功';
        if ($status == 4) return '已有成交';
        if ($status == 8) return '全部成交';
        if ($status == 16) return '撤单';
    }

    //获取深度
    public function getDepth($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $redis = new \Redis();
            $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
            $redis->auth(C('REDIS_PWD')); //链接密码
            $redis->select(5);

            $pair = strtoupper($market);

            $buyKey = 'contract:book:' . $pair . ':buy';
            $sellKey = 'contract:book:' . $pair . ':sell';

            $buy = $redis->ZREVRANGE($buyKey, 0, 5);
            $sell = $redis->zRange($sellKey, 0, 5);
            $max = 0;
            if ($buy) {
                foreach ($buy as $k => $v) {
                    $arr = explode('@', $v);
                    $data['depth']['buy'][$k] = [
                        'price' => (string)$arr[1],
                        'num' => (string)$arr[0]
                    ];
                    if ($arr[0] > $max) {
                        $max = $arr[0];
                    }
                }
            } else {
                $data['depth']['buy'] = [];
            }

            if ($sell) {
                $sell = array_reverse($sell);
                foreach ($sell as $k => $v) {
                    $arr = explode('@', $v);
                    $data['depth']['sell'][$k] = [
                        'price' => (string)$arr[1],
                        'num' => (string)$arr[0]
                    ];

                    if ($arr[0] > $max) {
                        $max = $arr[0];
                    }
                }
            } else {
                $data['depth']['sell'] = [];
            }

            //期货最新成交价
            $new_price = M('ContractTrade')->where(['pair' => $market])->order('id desc')->limit(1)->getField('price');
            //24H收盘价
            $start = strtolower('-1 days') . '000';
            $hou_price = M('ContractTrade')->where([
                'pair' => $market,
                'ctime' => ['gt', $start]
            ])->order('id asc')->limit(1)->getField('price');
            $change = round((($new_price - $hou_price) / $hou_price) * 100, 2);

            $data['max'] = $max;
            $data['new_price'] = (string)'$' . round($new_price, 4);
            $data['change'] = (string)round($change, 2);
            $data['rate'] = getRate();

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //获取k线数据
    public function getKline($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $redis = new \Redis();
            $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
            $redis->auth(C('REDIS_PWD')); //链接密码
            $redis->select(5);

            $time = 5;
            $key = 'kline:' . $market . ':' . $time;
            $list = $redis->zrevrange($key, 0, 99);

            $list = array_reverse($list);

            $json_data = [];
            foreach ($list as $v) {
                $json_data[] = json_decode($v, TRUE);
            }
            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $json_data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //新增委托单
    public function create_order($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];
            $type = $result['data']['type'];
            $leveraged = $result['data']['leveraged'];
            $num = $result['data']['num'];
            $price = $result['data']['price'];
            $order_type = $result['data']['order_type'];

            if (empty($userid)) {
                $info['code'] = 1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '交易对必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($leveraged)) {
                $info['code'] = -1;
                $info['msg'] = '杠杆数必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($num)) {
                $info['code'] = -1;
                $info['msg'] = '数量必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($price)) {
                $info['code'] = -1;
                $info['msg'] = '价格必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($order_type)) {
                $info['code'] = -1;
                $info['msg'] = '市场类型必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $hand = new ContractSimulation();
            //用户ID，交易对，交易类型，杠杆数，数量，价格，order_type，orderType
            $res = $hand->create_order($userid, $market, $type, $leveraged, $num, $price, $order_type);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '创建成功';
                $info['data'] = [$res['data']['orderId']];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //用户可用资产
    public function user_balance($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $usdt = M('Coin')->where(['name' => 'usdt'])->find();

            //查找用户合约资产
            $coin = M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => 'usdt'])->find();
            if (!$coin) {
                $data = [
                    'summary' => 0,
                    'list' => [
                        [
                            'coin_name' => 'USDT',
                            'img' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . $usdt['img'],
                            'balance' => '0',
                            'order_margin' => '0',
                            'total' => '0',
                            'position_margin' => '0',
                            'freeze' => 0
                        ]
                    ]
                ];
            } else {
                $data = [
                    'summary' => bcmul($coin['total'], getRate(), 2),
                    'list' => [
                        [
                            'coin_name' => 'USDT',
                            'img' => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/coin/' . $usdt['img'],
                            'balance' => (string)abs($coin['balance']),
                            'order_margin' => (string)round($coin['order_margin'], 4),
                            'total' => (string)round($coin['total'], 4),
                            'position_margin' => (string)round($coin['position_margin'], 4),
                            'freeze' => (string)round($coin['freeze'], 4)
                        ]
                    ]
                ];
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    public function user_property($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            //查找用户合约资产
            $coin = M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => 'usdt'])->find();
            if (!$coin) {
                $data = [
                    'summary' => 0,
                    'balance' => '0',
                    'freeze' => 0
                ];
            } else {
                $data = [
                    'summary' => bcmul($coin['total'], getRate(), 2),
                    'balance' => (string)round($coin['balance'], 4),
                    'freeze' => (string)round($coin['freeze'], 4)
                ];
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //最近成交记录
    public function recent_record($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];

            $list = M('ContractTrade')->where(['status' => 2, 'pair' => $market])->order('id desc')->limit(50)->select();
            $data = [];
            foreach ($list as $k => $v) {
                $data[$k]['time'] = (string)date('H:i:s', $v['ctime'] / 1000);
                $data[$k]['type'] = (string)$v['bid_flag'];
                $data[$k]['price'] = (string)$v['price'];
                $data[$k]['num'] = (string)$v['amount'];
                $data[$k]['total'] = (string)bcmul($v['amount'], $v['price'], 2);
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //仓位列表
    public function position_list($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $list = M('ContractPosition')->where(['user_id' => $userid, 'status' => 1, 'pair' => $market])->select();
            $data = [];
            foreach ($list as $k => $v) {
                //$new_price = M('Market')->where(['name' => $v['pair']])->getField('new_price');
                //现货价格改为从redis读取
                $new_price = $this->getPairPrice($v['pair']);

                $data[$k]['id'] = (string)$v['id'];
                $data[$k]['leveraged'] = (string)round($v['leveraged'], 2); //杠杆倍数
                $data[$k]['bid_flag'] = (string)$v['bid_flag']; // 买卖类型 1买 0卖
                $data[$k]['market'] = (string)$this->handlePair($v['pair']); //合约
                $data[$k]['num'] = (string)round($v['amount'], 0); //目前仓位数量
                $data[$k]['value'] = (string)bcdiv($v['amount'], $v['price'], 4) . strtoupper(explode('_', $v['pair'])[0]); //价值(除法)
                $data[$k]['price'] = (string)round($v['price'], 4); //开仓价格
                $data[$k]['now_price'] = (string)round($new_price, 4); //标记价格==现货价格
                $data[$k]['liquidation_price_plan'] = (string)round($v['liquidation_price_plan'], 1); //强平价格
                $data[$k]['position_margin'] = (string)round($v['position_margin'], 4) . 'USDT' . ' (' . sprintf("%.2f", $v['leveraged']) . 'X)'; //仓位保证金
                //期货价格
                $order_price = M('ContractOrder')->where(['id' => $v['contract_order_id']])->getField('price');
                //[（（开仓价-现价）/开仓价）*100]  做空 回报率
                //[（（现价-开仓价）/开仓价）*100]  做多 回报率
                //[（开仓价-现价）*合约量/开仓价] 做空 未实现盈亏
                //[（现价-开仓价）*合约量/开仓价] 做多 未实现盈亏

                //回报率算法：（未实现盈亏/保证金）*100
                if ($v['bid_flag'] == 1) {
                    //做多
                    //未实现盈亏
                    $income = ($new_price - $v['price']) * $v['amount'] / $v['price'];
                    $income = round($income, 4);
                    //$rate = ($new_price - $v['price']) / $v['price'] * 100;
                    $rate = ($income / $v['position_margin']) * 100;
                    $rate = round($rate, 2);
                } else {
                    //做空
                    $income = ($v['price'] - $new_price) * $v['amount'] / $v['price'];
                    $income = round($income, 4);
                    //$rate = ($v['price'] - $new_price) / $v['price'] * 100;
                    $rate = ($income / $v['position_margin']) * 100;
                    $rate = round($rate, 2);
                }

                $data[$k]['income'] = (string)round($income, 4) . ' USDT' . '(' . $rate . '%)'; //未实现盈亏（回报率）
                $data[$k]['already_income'] = (string)round($v['already_income'], 4) . ' USDT'; //已实现盈亏
                //查询是否关闭平仓按钮
                $order = M('ContractOrder')->where(['contract_position_id' => $v['id'], 'status' => 2])->order('id desc')->find();
                if ($order['close_type'] == 2) {
                    $data[$k]['close'] = (string)1;
                    $data[$k]['order_id'] = (string)$order['id'];
                } else {
                    $data[$k]['close'] = (string)0;
                }
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //从redis获取交易对现货价格
    protected function getPairPrice($pair)
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(1);

        $key = 'spot:ticker:' . strtoupper($pair);
        //echo $key;
        $res = $redis->get($key);
        $arr = explode(',', $res);

        return $arr[1];
        //$market = M('Market')->where(['name' => $pair])->find();
        //return $market['new_price'];
    }

    //已平仓仓位
    public function been_liquidated($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $where['user_id'] = $userid;
            $where['pair'] = $market;
            $where['status'] = ['in', [4, 8]];
            $data = [];
            $list = M('ContractPosition')->where($where)->select();
            foreach ($list as $k => $v) {
                $data[$k]['market'] = (string)$this->handlePair($v['pair']); //合约
                $data[$k]['already_income'] = (string)round($v['already_income'], 4) . ' USDT'; //已实现盈亏
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //活动委托
    public function order_list($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $where['user_id'] = $userid;
            $where['pair'] = $market;
            $where['status'] = ['in', [2, 4]];
            $list = M('ContractOrder')->where($where)->select();
            $data = [];
            foreach ($list as $k => $v) {
                $data[$k]['id'] = (string)$v['id'];
                $data[$k]['market'] = (string)$this->handlePair($v['pair']); //合约交易对
                $data[$k]['bid_flag'] = (string)$v['bid_flag']; // 买卖类型 1买 0卖
                $data[$k]['amount'] = (string)round($v['amount'], 0); //数量
                $data[$k]['price'] = (string)round($v['price'], 1); //委托价格
                $data[$k]['value'] = (string)bcdiv($v['amount'], $v['price'], 4) . strtoupper(explode('_', $v['pair'])[0]); //价值(除法)
                $data[$k]['remaining'] = (string)bcsub($v['amount'], $v['exchanged_amount'], 0); //剩余数量
                $data[$k]['order_type'] = (string)$v['order_type'] == 1 ? '限价' : '市价'; //类型 1限价 2市价
                $data[$k]['status'] = (string)$this->handleStatus($v['status']); //状态 1:下单失败;2:下单成功;4:已有成交;8:全部成交;16撤单
                $data[$k]['time'] = (string)date('H:i:s', $v['ctime'] / 1000); //时间
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //已成交
    public function deal_list($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $where = '((taker_user_id=' . $userid . ') or (maker_user_id=' . $userid . ')) and pair=\'' . $market . '\'';

            $list = M('ContractTrade')->where($where)->order('id desc')->limit(50)->select();
            $data = [];
            foreach ($list as $k => $v) {
                //委托单
                if ($v['maker_user_id'] == $userid) {
                    $maker = M('ContractOrder')->where(['id' => $v['contract_maker_order_id']])->find();
                } else {
                    $maker = M('ContractOrder')->where(['id' => $v['contract_taker_order_id']])->find();
                }

                $data[$k]['market'] = (string)$this->handlePair($v['pair']); //合约交易对
                $data[$k]['num'] = (string)round($maker['amount'], 0); //数量
                $data[$k]['deal_num'] = (string)round($v['amount'], 0); //成交数量
                $data[$k]['bid_flag'] = (string)$v['bid_flag']; // 买卖类型 1买 0卖
                $data[$k]['remaining'] = (string)bcsub($maker['amount'], $maker['exchanged_amount'], 0); //剩余
                $data[$k]['price'] = (string)round($v['price'], 4); //成交价格
                $data[$k]['trade_price'] = (string)round($maker['price'], 4); //委托价格
                $data[$k]['value'] = (string)bcdiv($v['amount'], $v['price'], 4) . strtoupper(explode('_', $v['pair'])[0]); //价值(除法)
                $arr = ['1' => '委托', '2' => '平仓', '3' => '强制平仓'];
                $data[$k]['trade_type'] = (string)$arr[$maker['close_type']]; //是否是强制平仓下出的委托 1.委托 2.平仓 3强制平仓
                $data[$k]['trade_id'] = (string)$v['contract_maker_order_id']; //委托ID
                $data[$k]['time'] = (string)date('Y年m月d日 H:i:s', $v['ctime'] / 1000); //时间
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //委托历史
    public function trade_history($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $market = $result['data']['market'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $where['user_id'] = $userid;
            $where['pair'] = $market;
            $where['status'] = ['in', [8, 16]];

            $list = M('ContractOrder')->where($where)->order('id desc')->limit(50)->select();
            $data = [];
            foreach ($list as $k => $v) {
                $data[$k]['market'] = (string)$this->handlePair($v['pair']); //合约交易对
                $data[$k]['bid_flag'] = (string)$v['bid_flag']; // 买卖类型 1买 0卖
                $data[$k]['amount'] = (string)round($v['amount'], 0); //数量
                $data[$k]['price'] = (string)round($v['price'], 4); //委托价格
                $data[$k]['remaining'] = (string)bcsub($v['amount'], $v['exchanged_amount'], 0); //剩余数量
                $data[$k]['order_type'] = (string)$v['order_type'] == 1 ? '限价' : '市价'; //类型 1限价 2市价
                $data[$k]['status'] = (string)$this->handleStatus($v['status']); //状态 1:下单失败;2:下单成功;4:已有成交;8:全部成交;16撤单
                $data[$k]['time'] = (string)date('H:i:s', $v['ctime'] / 1000); //时间
            }

            $info['code'] = 0;
            $info['msg'] = 'success';
            $info['data'] = $data;
            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    /**
     * Notice: 平仓结算
     * @param $jmcode
     */
    public function liquidation($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $position_id = $result['data']['position_id'];
            $price = $result['data']['price'];

            if (empty($position_id)) {
                $info['code'] = -1;
                $info['msg'] = '仓位ID必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $position = M('ContractPosition')->where(['id' => $position_id])->find();
            if (!$position) {
                $info['code'] = -1;
                $info['msg'] = '未找到仓位记录';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($price)) {
                $info['code'] = -1;
                $info['msg'] = '平仓价格必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $order = M('ContractOrder')->where(['id' => $position['contract_order_id']])->find();
            if (!$order) {
                $info['code'] = -1;
                $info['msg'] = '委托单不存在';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $hand = new ContractSimulation();
            //仓位ID，市场类型，价格
            $res = $hand->liquidation($position_id, $order['order_type'], $price);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '提交成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //资金划转(币币-合约)
    public function funds_transfer($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $coin = 'usdt';
            $num = $result['data']['num'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($coin)) {
                $info['code'] = -1;
                $info['msg'] = '虚拟币名称必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($num)) {
                $info['code'] = -1;
                $info['msg'] = '划转数量必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $coin = strtolower($coin);
            if (!in_array($coin, ['usdt'])) {
                //$this->error('划转必须是usdt');
                $coin = 'usdt';
            }
            $where['userid'] = $userid;
            $userCoin = M('UserCoin')->where($where)->find();
            //判断资产是否充足
            if ($userCoin[$coin] < $num) {
                $info['code'] = -1;
                $info['msg'] = '币币账户余额不足';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            try {
                M()->startTrans();
                //减去余额
                M('UserCoin')->where($where)->setDec($coin, $num);
                //判断合约资产账户是否存在
                $check = M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => $coin])->find();
                if ($check) {
                    //修改资产
                    M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => $coin])->setInc('total', $num);
                    M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => $coin])->setInc('balance', $num);
                } else {
                    //增加资产
                    $add = M('ContractUserCoinResult')->add([
                        'user_id' => $userid,
                        'coin_name' => $coin,
                        'total' => $num,
                        'balance' => $num,
                        'freeze' => 0,
                        'income' => 0,
                        'order_margin' => 0,
                        'position_margin' => 0,
                        'ctime' => msectime(),
                        'mtime' => msectime()
                    ]);
                    if (!$add) {
                        throw new \Exception('新增合约资产失败');
                    }
                }
                //增加资金流水
                M('ContractUserCoinRun')->add([
                    'user_id' => $userid,
                    'coin' => $coin,
                    'source_table' => 'btchanges_user_coin',
                    'source_id' => $userCoin['id'],
                    'money' => $num,
                    'money_type' => 1,
                    'ctime' => msectime(),
                    'mtime' => msectime()
                ]);

                M()->commit();

                $info['code'] = 0;
                $info['msg'] = '划转成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            } catch (\Exception $e) {
                M()->rollback();
                $msg = $e->getMessage();

                $info['code'] = -1;
                $info['msg'] = $msg;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //资金划转（合约-币币）
    public function transfer_funds($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $coin = 'usdt';
            $num = $result['data']['num'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($coin)) {
                $info['code'] = -1;
                $info['msg'] = '虚拟币名称必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($num)) {
                $info['code'] = -1;
                $info['msg'] = '划转数量必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $coin = strtolower($coin);
            if (!in_array($coin, ['usdt'])) {
                //$this->error('划转必须是usdt');
                $coin = 'usdt';
            }

            $userCoin = M('ContractUserCoinResult')->where(['user_id' => $userid])->find();
            //判断资产是否充足
            if ($userCoin['balance'] < $num) {
                $info['code'] = -1;
                $info['msg'] = '合约账户余额不足';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            try {
                M()->startTrans();
                //减去合约余额
                M('ContractUserCoinResult')->where(['user_id' => $userid])->setDec('balance', $num);
                M('ContractUserCoinResult')->where(['user_id' => $userid])->setDec('total', $num);
                //增加用户币币资产
                M('UserCoin')->where(['userid' => $userid])->setInc($coin, $num);
                //增加资金流水
                M('ContractUserCoinRun')->add([
                    'user_id' => $userid,
                    'coin' => $coin,
                    'source_table' => 'btchanges_user_coin',
                    'source_id' => $userCoin['id'],
                    'money' => (0 - $num),
                    'money_type' => 2,
                    'ctime' => msectime(),
                    'mtime' => msectime()
                ]);

                M()->commit();

                $info['code'] = 0;
                $info['msg'] = '划转成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            } catch (\Exception $e) {
                M()->rollback();
                $msg = $e->getMessage();

                $info['code'] = -1;
                $info['msg'] = $msg;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //取消委托单
    public function cancel_order($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $order_id = $result['data']['order_id'];

            if (empty($order_id)) {
                $info['code'] = -1;
                $info['msg'] = '委托单ID必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $hand = new ContractSimulation();

            $res = $hand->cancel_order($order_id);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '撤销成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    /**
     * Notice: 追加仓位保证金
     * @param $jmcode
     */
    public function add_pos_margin($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $position_id = $result['data']['position_id'];
            $pair = $result['data']['pair'];
            $add_margin = $result['data']['add_margin'];
            $type = $result['data']['type'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($position_id)) {
                $info['code'] = -1;
                $info['msg'] = '仓位ID必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $position = M('ContractPosition')->where(['id' => $position_id])->find();
            if (!$position) {
                $info['code'] = -1;
                $info['msg'] = '未找到仓位记录';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($pair)) {
                $info['code'] = -1;
                $info['msg'] = '交易对必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($add_margin)) {
                $info['code'] = -1;
                $info['msg'] = '数量必须';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $hand = new ContractSimulation();
            //减少保证金
            if ($type == 2) {
                $add_margin = (0 - $add_margin);
            }

            $res = $hand->add_pos_margin($position_id, $userid, $pair, $add_margin);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //左侧菜单列表
    public function coin_list()
    {
        $list = M('ContractMarket')->select();
        $data = [];
        foreach ($list as $k => $v) {
            $data[$k]['id'] = (string)$v['id'];
            $data[$k]['name'] = (string)$v['market'];
            $xnb = explode('_', $v['market'])[0];
            $rmb = explode('_', $v['market'])[1];
            $data[$k]['title'] = (string)strtoupper($xnb) . D('Coin')->get_title($xnb);
            //期货最新成交价
            $data[$k]['price'] = (string)'$ ' . round($v['new_price'], 4);
            $data[$k]['change'] = (string)round($v['change'], 2);
            $data[$k]['img'] = (string)trim(D('Coin')->get_img($xnb));
        }

        $info['code'] = 0;
        $info['msg'] = '成功';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //最大可用张数
    public function max_order_amount($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $pair = $result['data']['pair'];
            $leveraged = $result['data']['leveraged'];
            $bid_flag = $result['data']['bid_flag'];
            $userid = $result['data']['userid'];
            if (!$userid) {
                $info['status'] = 0;
                $info['info'] = 'userid必须';
                $this->ajaxReturn($info);
            }
            if (empty($pair)) {
                $info['status'] = 0;
                $info['info'] = '交易对必须';
                $this->ajaxReturn($info);
            }
            if (empty($leveraged)) {
                $info['status'] = 0;
                $info['info'] = '杠杆数必须';
                $this->ajaxReturn($info);
            }

            $hand = new ContractSimulation();

            $res = $hand->max_order_amount($userid, $pair, $leveraged, $bid_flag);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '成功';
                $info['data'] = ['num' => abs($res['data']['amount'])];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //调整杠杆倍数
    public function change_leveraged($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $pair = $result['data']['pair'];
            $leveraged = $result['data']['leveraged'];
            $userid = $result['data']['userid'];
            if (!$userid) {
                $info['status'] = 0;
                $info['info'] = '请先登陆';
                $this->ajaxReturn($info);
            }
            if (empty($pair)) {
                $info['status'] = 0;
                $info['info'] = '交易对必须';
                $this->ajaxReturn($info);
            }
            if (empty($leveraged)) {
                $info['status'] = 0;
                $info['info'] = '杠杆数必须';
                $this->ajaxReturn($info);
            }

            $hand = new ContractSimulation();

            $res = $hand->change_leveraged($userid, $pair, $leveraged);

            if (!$res) {
                $info['code'] = -1;
                $info['msg'] = '合约接口没有返回消息';
                $this->ajaxReturn($info);
            }
            if (in_array($res['status'], [500, 405, 400])) {
                $info['code'] = -1;
                $info['msg'] = $res['message'];
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($res['code'] == 0) {
                $info['code'] = 0;
                $info['msg'] = '成功';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }
}