<?php

namespace Home\Controller;

use Think\Page;

class FinanceController extends HomeController
{
    /**
     * Notice: 财务中心
     * @param int $cate 1-币币账户 2-合约账户
     */
    public function index($cate = 1)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }
        //账户类型
        $this->assign('cate', $cate);

        $CoinList = M('Coin')->where(['status' => 1])->select();
        $UserCoin = M('UserCoin')->where(['userid' => userid()])->find();
        $Market = M('Market')->where(['status' => 1])->select();

        foreach ($Market as $k => $v) {
            $Market[$v['name']] = $v;
        }
        
        $cny['zj'] = 0;
        //获取汇率
        $jia = getRate();

        foreach ($CoinList as $k => $v) {
            if ($v['name'] == 'usdt') {
                $coinList[$v['name']] = [
                    'name' => $v['name'],
                    'img' => $v['img'],
                    'title' => $v['title'] . '(' . strtoupper($v['name']) . ')',
                    'xnb' => round($UserCoin[$v['name']], 6) * 1,
                    'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1,
                    'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6),
                    'zhehe' => sprintf('%.2f', ($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia),
                ];
            } else {
                $new_price = M('Market')->where(['name' => $v['name'] . '_usdt'])->getField('new_price');
                $coinList[$v['name']] = [
                    'name' => $v['name'],
                    'img' => $v['img'],
                    'title' => $v['title'] . '(' . strtoupper($v['name']) . ')',
                    'xnb' => round($UserCoin[$v['name']], 6) * 1,
                    'xnbd' => round($UserCoin[$v['name'] . 'd'], 6) * 1,
                    'xnbz' => round($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd'], 6),
                    'zhehe' => sprintf('%.2f', ($UserCoin[$v['name']] + $UserCoin[$v['name'] . 'd']) * $jia * $new_price),
                ];
            }
        }
        //币币账户折合总计
        $spot = 0;
        foreach ($coinList as $val) {
            $spot += $val['zhehe'];
        }
        $this->assign('spot', round($spot, 2));
        //合约账号折合
        $totals = M('ContractUserCoinResult')->where(['user_id' => userid()])->select();
        $cont = 0;
        foreach ($totals as $total) {
            $cont += $total['total'];
        }
        $contractCny = bcmul($cont, $jia, 2);
        $this->assign('contractCny', $contractCny);

        //合约资产列表
        foreach ($CoinList as $key => $value) {
            if ($value['name'] == 'usdt') {
                $result = M('ContractUserCoinResult')->where(['user_id' => userid(), 'coin_name' => 'usdt'])->find();
                $contList[$value['name']] = [
                    'name' => $value['name'],
                    'img' => $value['img'],
                    'title' => $value['title'] . '(' . strtoupper($value['name']) . ')',
                    'balance' => $result['balance'],
                    'position_margin' => $result['position_margin'],
                    'order_margin' => $result['order_margin'],
                    'total' => $result['total'],
                    'xnb' => round($UserCoin['usdt'], 6),
                ];
            }
        }
        $this->assign('contList', $contList);

        $this->assign('cny', $cny);
        $this->assign('coinList', $coinList);
        $this->assign('prompt_text', D('Text')->get_content('finance_index'));
        $this->display();
    }

    /**
     * Notice: 委托管理
     * @author: hxq
     * @param null $market
     * @param null $type
     * @param null $status
     * @param      $cate
     */
    public function mywt($market = NULL, $type = NULL, $status = NULL, $cate = 1)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }
        //温馨提示
        $this->assign('prompt_text', D('Text')->get_content('finance_mywt'));
        $this->assign('cate', $cate);
        //币种列表
        if ($cate == 1) {
            $Coin = M('Coin')->where(['status' => 1])->select();
        } else {
            $Coin = M('Coin')->where(['status' => 1, 'name' => ['in', ['btc', 'eth']]])->select();
        }
        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }
        $this->assign('coin_list', $coin_list);

        $Market = M('Market')->where(['status' => 1])->select();
        foreach ($Market as $k => $v) {
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $market_list[$v['name']] = $v;
        }
        $this->assign('market_list', $market_list);

        if (!$market_list[$market]) {
            $market = $Market[0]['name'];
        }

        $where['market'] = $market;

        if (($type == 1) || ($type == 2)) {
            $where['type'] = $type;
        }

        if (($status == 1) || ($status == 2) || ($status == 3)) {
            $where['status'] = $status - 1;
        }

        $where['userid'] = userid();
        $this->assign('market', $market);
        $this->assign('type', $type);
        $this->assign('status', $status);
        $Moble = M('Trade');
        $count = $Moble->where($where)->count();
        $Page = new Page($count, 10);
        $Page->parameter .= 'type=' . $type . '&status=' . $status . '&market=' . $market . '&';
        $show = $Page->show();
        $list = $Moble->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = $v['num'];
            $list[$k]['price'] = $v['price'];
            $list[$k]['deal'] = $v['deal'];
        }

        //合约委托列表
        $where2 = ['user_id' => userid()];
        $count2 = M('ContractOrder')->where($where2)->count();
        $page2 = new Page($count2, 10);
        $show2 = $page2->show();
        $contList = M('ContractOrder')->where($where2)->order('id desc')->limit($page2->firstRow . ',' . $page2->listRows)->select();
        $arr = ['1' => '委托', '2' => '平仓', '3' => '强制平仓'];
        foreach ($contList as $k => $v) {
            $clist[$k]['id'] = $v['id'];
            $clist[$k]['pair'] = $this->handlePair($v['pair']);
            $clist[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime'] / 1000);
            $clist[$k]['bid_flag'] = $v['bid_flag'] == 1 ? 'Buy' : 'Sell';
            $clist[$k]['amount'] = round($v['amount']);
            $clist[$k]['price'] = round($v['price'], 1);
            $clist[$k]['exchanged_amount'] = round($v['exchanged_amount']);
            $clist[$k]['remaining'] = round($v['amount'] - $v['exchanged_amount']);
            $clist[$k]['close_type'] = $arr[$v['close_type']];
            $clist[$k]['status'] = $this->handleStatus($v['status']);
        }

        $this->assign('list', $list);
        $this->assign('page', $show);

        $this->assign('list2', $clist);
        $this->assign('page2', $show2);

        $this->display();
    }

    private function handlePair($pair)
    {
        $arr = explode('_', $pair);

        return strtoupper($arr[0]) . strtoupper($arr[1]);
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

    /**
     * Notice: 成交查询
     * @param null $market
     * @param null $type
     * @param $cate
     */
    public function mycj($market = NULL, $type = NULL, $cate = 1)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $this->assign('cate', $cate);

        $Coin = M('Coin')->where(['status' => 1])->select();
        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }
        $Market = M('Market')->where(['status' => 1])->select();
        foreach ($Market as $k => $v) {
            $v['xnb'] = explode('_', $v['name'])[0];
            $v['rmb'] = explode('_', $v['name'])[1];
            $market_list[$v['name']] = $v;
        }
        if (!$market_list[$market]) {
            $market = $Market[0]['name'];
        }
        $this->assign('prompt_text', D('Text')->get_content('finance_mycj'));
        $this->assign('market_list', $market_list);
        $this->assign('coin_list', $coin_list);
        $this->assign('market', $market);
        //$this->assign('type', $type);
        //$this->assign('userid', userid());
        $userid = userid();
        //查询合约成交记录
        //$where = '((taker_user_id=' . $userid . ') or (maker_user_id=' . $userid . ')) and pair=\'' . $market . '\'';
        $where = '((taker_user_id=' . $userid . ') or (maker_user_id=' . $userid . '))';
        $count = M('ContractTrade')->where($where)->count();
        $Page = new Page($count, 10);
        $show = $Page->show();
        $list = M('ContractTrade')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        $arr = ['1' => '委托', '2' => '平仓', '3' => '强制平仓'];
        foreach($list as $k => $v) {
            //maker委托单
            $maker = M('ContractOrder')->where(['id' => $v['contract_maker_order_id']])->find();
            $arr = ['1' => '委托', '2' => '平仓', '3' => '强制平仓'];

            $data[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime'] / 1000);
            $data[$k]['pair'] = $this->handlePair($v['pair']);
            $data[$k]['trade_type'] = $arr[$maker['close_type']];
            $data[$k]['bid_flag'] = $v['bid_flag'] == 1 ? 'Buy' : 'Sell';
            $data[$k]['amount'] = round($v['amount']);
            $data[$k]['price'] = round($v['price'], 1);
            $data[$k]['value'] = bcdiv($v['amount'], $v['price'], 4);  //价值
            $data[$k]['taker_fee_ratio'] = $v['taker_fee_ratio'] * 100; //taker_fee_ratio
            //对应委托单
            $order = M('ContractOrder')->where(['id' => $v['contract_maker_order_id']])->find();
            $data[$k]['close_type'] = $arr[$order['close_type']];
            $data[$k]['trade_amount'] = round($order['amount']);
            $data[$k]['remaining'] = round($order['amount'] - $order['exchanged_amount']);
            $data[$k]['trade_price'] = round($order['price'], 1);
        }

        $this->assign('page', $show);
        $this->assign('list', $data);

        $this->display();
    }

    //成交时间 addtime 成交价格price 成交数量num 总金额mum 手续费fee_buy
    public function cjdata($userid, $market = 'bys_usdt', $type, $page = 1)
    {
        if ($type == 1) {
            $where = 'userid=' . $userid . ' and market=\'' . $market . '\'';
        } else if ($type == 2) {
            $where = 'peerid=' . $userid . ' and market=\'' . $market . '\'';
        } else {
            $where = '((userid=' . $userid . ') or (peerid=' . $userid . ')) and market=\'' . $market . '\'';
        }

        $Moble = M('TradeLog');
        $count = $Moble->where($where)->count();
        $perNumber = 15;
        $count_page = ceil($count / $perNumber);//总页数 开始用的计算方式是 总页数 = intval(总数/每页数量) 结果发生总少一页
        $start = ($page - 1) * $perNumber;     //分页开始,根据此方法计算出开始的记录
        $end = $perNumber;

        $list = $Moble->where($where)->order('id desc')->limit($start . ',' . $end)->field('id,market,type,userid,peerid,num,price,mum,fee_buy,fee_sell,addtime')->select();

        foreach ($list as $k => $v) {
            $list[$k]['num'] = $v['num'];
            $list[$k]['price'] = $v['price'];
            $list[$k]['mum'] = $v['mum'];
            $list[$k]['fee_buy'] = $v['fee_buy'];
            $list[$k]['fee_sell'] = $v['fee_sell'];
            $list[$k]['userid'] = $userid;
        }
        $info['count_page'] = $count_page;
        $info['count'] = $count;
        $info['info'] = "列表数据";
        $info['data'] = $list;
        $this->ajaxReturn($info);
    }

    public function mytj()
    {
        $this->display();
    }

    public function myjp()
    {
        $this->display();
    }

    public function mywd()
    {
        $this->display();
    }

    //资金划转流水 币币账户划转到合约账户
    public function funds_transfer($coin_name, $money, $money_type)
    {
        //1.第一步查询 当前资金划转的用户余额是否足够 查询余额
        $coin_name = strtolower($coin_name);
        $banlnce = M('UserCoin')->where(['userid' => userid()])->find();

        if ($banlnce[$coin_name] < $money) {
            $info['code'] = "4000";
            $info['msg'] = "当前余额不足不能划转";
            $this->ajaxReturn($info);
        }
        //余额充足那么就首先扣掉余额
        $pirce = $banlnce[$coin_name] - $money;
        $save = M('UserCoin')->where(['userid' => userid()])->save([$coin_name => $pirce]);
        if ($save) {
            //扣掉成功之后,将这条扣除记录写到流水表中
            $data = [
                'user_id' => userid(),
                'coin_name' => $coin_name,
                'source_table' => 'btchanges_user_coin',
                'source_id' => $banlnce['id'],
                'money' => $money,
                'money_type' => $money_type,
                'ctime' => msectime(),
                'mtime' => msectime()
            ];
            $costrun = M('ContractUserCoinRun')->add($data);
            if ($costrun) {
                //如果流水添加成功之后 在记录到总账表当中
                //写入总账表的时候首先判断写入的币种是否已经存在如果存在就直接修改总账金额否者就直接写入
                $result = M('ContractUserCoinResult')->where(['userid' => userid(), 'coin_name' => $coin_name])->find();

                if (!$result) {
                    $data = [
                        'user_id' => userid(),
                        'coin_name' => $coin_name,
                        'total_money' => $money,
                        'balance_money' => $money,
                        'income' => 0,
                        'balance_margin' => 0,
                        'order_margin' => 0,
                        'position_margin' => 0,
                        'ctime' => msectime(),
                        'mtime' => msectime()
                    ];
                    $costrelst = M('ContractUserCoinResult')->add($data);
                    if ($costrelst) {
                        $info['code'] = '0000';
                        $info['msg'] = '划转成功';
                        $this->ajaxReturn($info);
                    } else {
                        $info['code'] = '2000';
                        $info['msg'] = '划转失败';
                        $this->ajaxReturn($info);
                    }
                } else {
                    //修改总额以及可用余额
                    $total_money = $result['total_money'] + $money;
                    $balance_money = $result['balance_money'] + $money;
                    $save = ['total_money' => $total_money, 'balance_money' => $balance_money];
                    $consresult = M('ContractUserCoinResult')->where(['userid' => userid(), 'coin_name' => $coin_name])->save($save);
                    if ($consresult) {
                        $info['code'] = '0000';
                        $info['msg'] = '资金划转成功';
                        $this->ajaxReturn($info);
                    } else {
                        $info['code'] = '2000';
                        $info['msg'] = '资金划转失败';
                        $this->ajaxReturn($info);
                    }
                }
            }
        }
    }

    //查询合约账户的列表
    public function funds_transfer_list()
    {
        $list = M('Contractusercoinresult')->where(['userid' => userid()])->field('id,coin_name,total_money,balance_money,position_margin,order_margin')->select();

        $info['data'] = $list;
        $info['msg'] = '合约账户列表';
        $info['code'] = '0000';
        $this->ajaxReturn($info);
    }
}