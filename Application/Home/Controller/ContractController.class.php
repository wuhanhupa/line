<?php

namespace Home\Controller;

use Common\Design\Contract\ContractSimulation;

class ContractController extends HomeController
{
    public function index($market = 'btc_usdt')
    {
        $this->assign('market', $market);

        $this->display();
    }

    public function chart($market = 'btc_usdt')
    {
        $this->assign('market', $market);

        $this->display();
    }

    public function test()
    {
        $this->display();
    }

    public function chart2()
    {
        $this->display();
    }

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
    public function getDepth($market = 'btc_usdt')
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(5);

        $pair = strtoupper($market);

        $buyKey = 'contract:book:' . $pair . ':buy';
        $sellKey = 'contract:book:' . $pair . ':sell';

        $buy = $redis->ZREVRANGE($buyKey, 0, 5);
        $sell = $redis->zRange($sellKey, 0, 5);

        if ($buy) {
            foreach ($buy as $k => $v) {
                $arr = explode('@', $v);
                $data['depth']['buy'][$k] = ['price' => $arr[1], 'num' => $arr[0]];
            }
        } else {
            $data['depth']['buy'] = '';
        }

        if ($sell) {
            $sell = array_reverse($sell);
            foreach ($sell as $k => $v) {
                $arr = explode('@', $v);
                $data['depth']['sell'][$k] = ['price' => $arr[1], 'num' => $arr[0]];
            }
        } else {
            $data['depth']['sell'] = '';
        }

        exit(json_encode($data));
    }

    //获取k线数据
    public function getKline($market = 'btc_usdt')
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(5);

        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'btc_usdt';

        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        if (in_array($input['time'], $timearr)) {
            $time = $input['time'];
        } else {
            $time = 5;
        }
        $key = 'kline:' . $market . ':' . $time;
        $list = $redis->zrevrange($key, 0, 99);

        $list = array_reverse($list);

        $json_data = [];
        foreach ($list as $v) {
            $json_data[] = json_decode($v, TRUE);
        }

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        exit(json_encode($json_data));
    }

    //新增委托单
    public function create_order($market = NULL, $type = NULL, $leveraged = NULL, $num = NULL, $price = NULL, $order_type = NULL)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        if (empty($market)) {
            $this->error('交易对必须');
        }

        if (empty($leveraged)) {
            $this->error('杠杆数必须');
        }
        if (empty($num)) {
            $this->error('数量必须');
        }
        if (empty($price)) {
            $this->error('价格必须');
        }
        if (empty($order_type)) {
            $this->error('市场类型必须');
        }

        $hand = new ContractSimulation();
        //用户ID，交易对，交易类型，杠杆数，数量，价格，order_type，orderType
        $res = $hand->create_order($userid, $market, $type, $leveraged, $num, $price, $order_type);

        if (!$res) {
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }

        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }

        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '创建成功';
            $this->ajaxReturn($info);
        }
    }

    //用户可用资产
    public function user_balance()
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        //查找用户合约资产
        $coin = M('ContractUserCoinResult')->where(['user_id' => $userid, 'coin_name' => 'usdt'])->find();
        if (!$coin) {
            $balance = 0;
        } else {
            $balance = round($coin['balance'], 1);
        }

        $userCoin = M('UserCoin')->where(['userid' => $userid])->find();

        $money = round($userCoin['usdt'], 1);

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = ['balance' => $balance, 'money' => $money];
        $this->ajaxReturn($info);
    }

    //最近成交记录
    public function recent_record($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $list = M('ContractTrade')->where(['status' => 2, 'pair' => $market])->order('id desc')->limit(50)->select();

        foreach ($list as $k => $v) {
            $data[$k]['time'] = date('H:i:s', $v['ctime'] / 1000);
            $data[$k]['type'] = $v['bid_flag'];
            $data[$k]['price'] = $v['price'];
            $data[$k]['num'] = $v['amount'];
            $data[$k]['total'] = bcmul($v['amount'], $v['price'], 2);
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //仓位列表
    public function position_list($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }

        $list = M('ContractPosition')->where(['user_id' => $userid, 'status' => 1, 'pair' => $market])->select();

        foreach ($list as $k => $v) {
            //$new_price = M('Market')->where(['name' => $v['pair']])->getField('new_price');
            //现货价格改为从redis读取
            $new_price = $this->getPairPrice($v['pair']);

            $data[$k]['id'] = $v['id'];
            $data[$k]['bid_flag'] = $v['bid_flag']; // 买卖类型 1买 0卖
            $data[$k]['market'] = $this->handlePair($v['pair']); //合约
            $data[$k]['num'] = round($v['amount'], 0); //目前仓位数量
            $data[$k]['value'] = bcdiv($v['amount'], $v['price'], 4); //价值(除法)
            $data[$k]['price'] = round($v['price'], 4); //开仓价格
            $data[$k]['now_price'] = round($new_price, 4); //标记价格==现货价格
            $data[$k]['liquidation_price_plan'] = round($v['liquidation_price_plan'], 1); //强平价格
            $data[$k]['position_margin'] = round($v['position_margin'], 1) . ' (' . sprintf("%.2f", $v['leveraged']) . '倍)'; //仓位保证金

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

            $data[$k]['income'] = round($income, 4) . ' USDT'; //未实现盈亏
            $data[$k]['rate'] = $rate . '%';//回报率
            $data[$k]['already_income'] = round($v['already_income'], 4) . ' USDT'; //已实现盈亏
            //查询是否关闭平仓按钮
            $order = M('ContractOrder')->where(['contract_position_id' => $v['id'], 'status' => 2])->order('id desc')->find();
            if ($order['close_type'] == 2) {
                $data[$k]['close'] = 1;
                $data[$k]['order_id'] = $order['id'];
            } else {
                $data[$k]['close'] = 0;
            }
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //已平仓仓位
    public function been_liquidated($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }

        $where['user_id'] = $userid;
        $where['pair'] = $market;
        $where['status'] = ['in', [4, 8]];

        $list = M('ContractPosition')->where($where)->select();
        foreach ($list as $k => $v) {
            $data[$k]['market'] = $this->handlePair($v['pair']); //合约
            $data[$k]['already_income'] = round($v['already_income'], 4) . ' USDT'; //已实现盈亏
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //活动委托
    public function order_list($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        $where['user_id'] = $userid;
        $where['pair'] = $market;
        $where['status'] = ['in', [2, 4]];
        $list = M('ContractOrder')->where($where)->order('id desc')->select();

        foreach ($list as $k => $v) {
            $data[$k]['id'] = $v['id'];
            $data[$k]['market'] = $this->handlePair($v['pair']); //合约交易对
            $data[$k]['bid_flag'] = $v['bid_flag']; // 买卖类型 1买 0卖
            $data[$k]['amount'] = round($v['amount'], 0); //数量
            $data[$k]['price'] = round($v['price'], 4); //委托价格
            $data[$k]['value'] = bcdiv($v['amount'], $v['price'], 4); //价值(除法)
            $data[$k]['remaining'] = bcsub($v['amount'], $v['exchanged_amount'], 0); //剩余数量
            $data[$k]['order_type'] = $v['order_type'] == 1 ? '限价' : '市价'; //类型 1限价 2市价
            $data[$k]['status'] = $this->handleStatus($v['status']); //状态 1:下单失败;2:下单成功;4:已有成交;8:全部成交;16撤单
            $data[$k]['time'] = date('H:i:s', $v['ctime'] / 1000); //时间
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //已成交
    public function deal_list($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }

        $where = '((taker_user_id=' . $userid . ') or (maker_user_id=' . $userid . ')) and pair=\'' . $market . '\'';

        $list = M('ContractTrade')->where($where)->order('id desc')->limit(50)->select();

        foreach ($list as $k => $v) {
            //委托单
            if ($v['maker_user_id'] == $userid) {
                $maker = M('ContractOrder')->where(['id' => $v['contract_maker_order_id']])->find();
            } else {
                $maker = M('ContractOrder')->where(['id' => $v['contract_taker_order_id']])->find();
            }

            $data[$k]['market'] = $this->handlePair($v['pair']); //合约交易对
            $data[$k]['num'] = round($maker['amount'], 0); //数量
            $data[$k]['deal_num'] = round($v['amount'], 0); //成交数量
            $data[$k]['bid_flag'] = $v['bid_flag']; // 买卖类型 1买 0卖
            $data[$k]['remaining'] = bcsub($maker['amount'], $maker['exchanged_amount'], 0); //剩余
            $data[$k]['price'] = round($v['price'], 4); //成交价格
            $data[$k]['trade_price'] = round($maker['price'], 4); //委托价格
            $data[$k]['value'] = bcdiv($v['amount'], $v['price'], 4); //价值(除法)
            $arr = ['1' => '委托', '2' => '平仓', '3' => '强制平仓'];
            $data[$k]['trade_type'] = $arr[$maker['close_type']]; //是否是强制平仓下出的委托 1.委托 2.平仓 3强制平仓
            $data[$k]['trade_id'] = $v['contract_maker_order_id']; //委托ID
            $data[$k]['time'] = date('Y年m月d日 H:i:s', $v['ctime'] / 1000); //时间
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //委托历史
    public function trade_history($market = 'btc_usdt')
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }

        $where['user_id'] = $userid;
        $where['pair'] = $market;
        $where['status'] = ['in', [8, 16]];

        $list = M('ContractOrder')->where($where)->order('id desc')->limit(50)->select();

        foreach ($list as $k => $v) {
            $data[$k]['market'] = $this->handlePair($v['pair']); //合约交易对
            $data[$k]['bid_flag'] = $v['bid_flag']; // 买卖类型 1买 0卖
            $data[$k]['amount'] = round($v['amount'], 0); //数量
            $data[$k]['price'] = round($v['price'], 4); //委托价格
            $data[$k]['remaining'] = bcsub($v['amount'], $v['exchanged_amount'], 0); //剩余数量
            $data[$k]['order_type'] = $v['order_type'] == 1 ? '限价' : '市价'; //类型 1限价 2市价
            $data[$k]['status'] = $this->handleStatus($v['status']); //状态 1:下单失败;2:下单成功;4:已有成交;8:全部成交;16撤单
            $data[$k]['time'] = date('H:i:s', $v['ctime'] / 1000); //时间
        }

        $info['status'] = 1;
        $info['info'] = 'success';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    /**
     * Notice: 平仓结算
     * @author: hxq
     * @param null $position_id
     * @param null $price
     */
    public function liquidation($position_id = NULL, $price = NULL)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        if (empty($position_id)) {
            $this->error('仓位ID必须');
        }
        $position = M('ContractPosition')->where(['id' => $position_id])->find();
        if (!$position) {
            $this->error('未找到仓位记录');
        }
        if (empty($price)) {
            $this->error('平仓价格必须');
        }
        $order = M('ContractOrder')->where(['id' => $position['contract_order_id']])->find();
        if (!$order) {
            $this->error('委托单不存在');
        }
        $hand = new ContractSimulation();
        //仓位ID，市场类型，价格
        $res = $hand->liquidation($position_id, $order['order_type'], $price);

        if (!$res) {
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }

        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }

        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '提交成功';
            $this->ajaxReturn($info);
        }
    }

    //资金划转(币币-合约)
    public function funds_transfer($coin, $num)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        if (empty($coin)) {
            $this->error('虚拟币名称必须');
        }
        if (empty($num)) {
            $this->error('划转数量必须');
        }
        if (!check($num, 'number')) {
            //$this->error('数量格式错误');
        }
        $coin = strtolower($coin);
        if (!in_array($coin, ['usdt'])) {
            //$this->error('划转必须是usdt');
            $coin = 'usdt';
        }
        $where['userid'] = $userid;
        $userCoin = M('UserCoin')->where($where)->find();
        //判断资产是否充足
        if (bccomp($userCoin[$coin], $num, 2) == -1) {
            $this->error('币币账户余额不足');
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

            $info['status'] = 1;
            $info['info'] = '划转成功';
            $this->ajaxReturn($info);
        } catch (\Exception $e) {
            M()->rollback();
            $msg = $e->getMessage();

            $info['status'] = 0;
            $info['info'] = $msg;
            $this->ajaxReturn($info);
        }
    }

    //资金划转（合约-币币）
    public function transfer_funds($coin, $num)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        if (empty($coin)) {
            $this->error('虚拟币名称必须');
        }
        if (empty($num)) {
            $this->error('划转数量必须');
        }
        if (!check($num, 'number')) {
            //$this->error('数量格式错误');
        }
        $coin = strtolower($coin);
        if (!in_array($coin, ['usdt'])) {
            //$this->error('划转必须是usdt');
            $coin = 'usdt';
        }

        $userCoin = M('ContractUserCoinResult')->where(['user_id' => $userid])->find();
        //判断资产是否充足
        if (bccomp($userCoin['balance'], $num, 2) == -1) {
            $this->error('合约账户余额不足');
        }
        try {
            M()->startTrans();
            //减去合约余额
            M('ContractUserCoinResult')->where(['user_id' => $userid])->setDec('balance', $num);
            //减去总额
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

            $info['status'] = 1;
            $info['info'] = '划转成功';
            $this->ajaxReturn($info);
        } catch (\Exception $e) {
            M()->rollback();
            $msg = $e->getMessage();

            $info['status'] = 0;
            $info['info'] = $msg;
            $this->ajaxReturn($info);
        }
    }

    //取消委托单
    public function cancel_order($order_id = NULL)
    {
        if (empty($order_id)) {
            $this->error('委托单ID必须');
        }

        $hand = new ContractSimulation();

        $res = $hand->cancel_order($order_id);

        if (!$res) {
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }

        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }

        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '撤销成功';
            $this->ajaxReturn($info);
        }
    }

    /**
     * Notice: 追加仓位保证金
     * @author: hxq
     * @param $position_id 仓位id
     * @param $pair 交易对
     * @param $add_margin 追加保证金 +新增 -减少
     * @param $type 1增加 2减少
     */
    public function add_pos_margin($position_id, $pair, $add_margin, $type = 1)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
        if (!$userid) {
            $this->error('请先登陆!');
        }
        if (empty($position_id)) {
            $this->error('仓位ID必须');
        }
        $position = M('ContractPosition')->where(['id' => $position_id])->find();
        if (!$position) {
            $this->error('未找到仓位记录');
        }
        if (empty($pair)) {
            $this->error('交易对必须');
        }
        if (empty($add_margin)) {
            $this->error('数量必须');
        }

        $hand = new ContractSimulation();
        //减少保证金
        if ($type == 2) {
            $add_margin = (0 - $add_margin);
        }

        $res = $hand->add_pos_margin($position_id, $userid, $pair, $add_margin);

        if (!$res) {
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }

        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }

        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '操作成功';
            $this->ajaxReturn($info);
        }
    }

    //左侧菜单列表
    public function coin_list()
    {
        $where['name'] = ['in', ['btc_usdt', 'eth_usdt']];
        $list = M('Market')->where($where)->select();
        $data = [];
        foreach ($list as $k => $v) {
            $data[$k]['id'] = $v['id'];
            $data[$k]['name'] = $v['name'];
            $xnb = explode('_', $v['name'])[0];
            $data[$k]['title'] = strtoupper($xnb) . '<span>' . D('Coin')->get_title($xnb) . '</span>';

            $market = M('ContractMarket')->where(['market' => $v['name']])->find();
            //期货最新成交价
            $data[$k]['new_price'] = round($market['new_price'], 4);
            $data[$k]['change'] = $market['change'];
            $data[$k]['img'] = trim(D('Coin')->get_img($xnb));
        }

        $info['status'] = 1;
        $info['info'] = '操作成功';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //k线头部信息
    public function kline_head($market = 'btc_usdt')
    {
        $pair = M('ContractMarket')->where(['market' => $market])->find();
        //标记价格
        //$data['new_price'] = round($pair['new_price'], 4);
        //改为从redis读取
        $data['new_price'] = round($this->getPairPrice($market), 4);
        $time = (time() - (60 * 60 * 24)) . '000';
        $data['min_price'] = round($pair['min_price'], 4);
        $data['max_price'] = round($pair['max_price'], 4);
        $data['volume'] = round($pair['volume'], 2);

        $info['status'] = 1;
        $info['info'] = '操作成功';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }

    //最大可用张数
    public function max_order_amount($pair, $leveraged, $bid_flag = 1)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
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

        $res = $hand->max_order_amount($userid, $pair, $leveraged, $bid_flag);

        if (!$res) {
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }
        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }
        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '成功';
            $info['data'] = ['number' => abs($res['data']['amount'])];
            $this->ajaxReturn($info);
        }
    }

    //调整杠杆倍数
    public function change_leveraged($pair = 'btc_usdt', $leveraged)
    {
        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        $userid = userid();
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
            $info['status'] = 0;
            $info['info'] = '合约接口没有返回消息';
            $this->ajaxReturn($info);
        }
        if (in_array($res['status'], [500, 405, 400])) {
            $info['status'] = 0;
            $info['info'] = $res['message'];
            $this->ajaxReturn($info);
        }
        if ($res['code'] == 0) {
            $info['status'] = 1;
            $info['info'] = '调整成功';
            $info['data'] = [];
            $this->ajaxReturn($info);
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
}
