<?php

namespace Mapi\Controller;

use Common\Design\SetTrade\ModelHandle;
use Common\Design\Suanfa\SnowFlake;
use Common\Design\Cancel\Collection as CancelCollection;

class TradeController extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }

    // orderId:orderId_Id_hupa.com,pri,price:1000.1234,number:50.1234,bidFlag:1,pair:XRP_USDT,tradeType:2,validateType:order
    public function robtrends($price, $num, $market, $type, $trade_type, $token)
    {
        //cancellog('调用一次--');
        //cancellog('price:'.$price.'num:'.$num.'market:'.$market.'type:'.$type.'trade_type:'.$trade_type);
        //验证传过来的参数是否争取
        //$string=MD5("orderId:orderId_hupa.com,price:100.1234,number:10.12,validateType:order");
        $data['market'] = strtoupper($market);

        $auth_token = 'orderId:orderId_hupa.com,' . 'price:' . $price . ',number:' . $num . ',bidFlag:' . $type . ',pair:' . $data['market'] . ',tradeType:' . $trade_type . ',validateType:order';

        if (md5($auth_token) != $token) {
            $info['info'] = '验证失败';

            $this->ajaxReturn($info);
        }

        //1.根据交易对
        $market = M('Market')->where(['name' => $market])->field('id')->find();
        if ($trade_type == 2) {
            $uid = M('User')->where(['username' => 'admin_zw'])->field('id')->find();
        } else {
            $uid = M('User')->where(['username' => 'robot1'])->field('id')->find();
        }

        $data = $this->trade_order('123456', $market['id'], $price, $num, $uid['id'], $type, $trade_type);

        if ($data['state'] == 'success') {
            $this->ajaxReturn($data);
        } else {
            $this->ajaxReturn($data);
        }
    }

    /**
     * Notice: 机器人自动买单
     */
    public function automatic_buy()
    {
        //第一步获取机器人用户
        $robot = M('User')->where(['username' => 'robot1'])->field('id,paypassword')->find();

        $return = $this->matchrobot($robot, 1, 1);
        //写入日志
        $return = implode(',', $return);

        rlog($return);
    }

    /**
     * Notice: 机器人自动卖单
     */
    public function automatic_sale()
    {
        //第一步获取机器人用户
        $robot = M('User')->where(['username' => 'robot2'])->field('id,paypassword')->find();
        $return = $this->matchrobot($robot, 2, 1);
        //dump($return); exit();
        //写入日志
        $return = implode(',', $return);

        rlog($return);
    }

    /**
     * Notice: 根据市场币种进行随机交易
     * @param $robot
     * @param $type
     * @param $trade_type
     */
    public function matchrobot($robot, $type, $trade_type)
    {
        $where = 'id > 18 and status=1';
        $market_list = M('Market')->where($where)->field('id,new_price,buy_price,sell_price,min_price,max_price')->select();

        foreach ($market_list as $key => $value) {
            $start_price = floatval($value['min_price']);
            $end_price = floatval($value['max_price']);
            //随机产生价格数
            $price = ($start_price * 1000 + rand(1, 100)) / 1000;
            $num = rand(1, 300) / 100;
            if ($num == 0) {
                $num = rand(1, 90) / 100;
            }
            //目前系统自带的R1  R2所下的单都为诱单

            $data = $this->upTrade('123456', $market_list[$key]['id'], $price, $num, $robot['id'], $type, $trade_type);
        }
        //return $data;
    }

    public function trade_order($paypassword = NULL, $marketid = NULL, $price, $num, $uid, $type, $trade_type)
    {
        if (!check($price, 'double')) {
            $this->error('交易价格格式错误');
        }
        if (!check($num, 'double')) {
            $this->error('交易数量格式错误');
        }
        if (($type != 1) && ($type != 2)) {
            $this->error('交易类型格式错误');
        }
        $user = M('User')->where(['id' => $uid])->find();
        if (md5($paypassword) != $user['paypassword']) {
            $this->error('交易密码错误！');
        }
        if (!$marketid) {
            $this->ajaxShow(['marketid 不能为空'], -1);
        }
        $marketid = intval($marketid);
        if (!($marketData = M('Market')->where(['id' => $marketid])->find())) {
            $this->ajaxShow(['marketid 不存在'], -1);
        }
        $market = $marketData['name'];

        $sunfa = new SnowFlake();
        $id = $sunfa->nextId();

        //首先生成委托单
        $create = $this->createOrder($id, $uid, $market, $price, $num, $type);
        if ($create['status'] == 0) {
            $this->error($create['msg']);
        }

        $data = [];

        if ($type == 1) {
            $data['bidFlag'] = 1;
        } else {
            $data['bidFlag'] = 0;
        }

        $data['userId'] = (int)$uid;
        $data['orderId'] = (int)$id;
        $data['time'] = (int)time();
        $data['price'] = (float)$price;
        $data['pair'] = strtoupper($market);
        $data['orderType'] = $trade_type;
        $data['amount'] = (float)$num;
        $data['exchangedAmount'] = 0;
        $str = 'orderId:orderId_hupa.com' . ',price:' . $data['price'] . ',number:' . $data['amount'] . ',bidFlag:' . $data['bidFlag'] . ',pair:' . $data['pair'] . ',tradeType:' . $trade_type . ',validateType:order';
        $data['token'] = md5($str);

        $res = $this->setTradeToRobot($market, $data);
        if ($res['code'] == 0) {
            $data['info'] = 'success';
            $data['state'] = '0000';
            $data['tid'] = $id;

            return $data;
        } else {
            $this->error('撮合引擎接收失败');
        }
    }

    //创建委托单
    protected function createOrder($id, $uid, $market, $price, $num, $type)
    {
        try {
            //开启事物
            M()->startTrans();

            $fee = bcmul($price, $num, 8);
            $add = M('Trade')->add([
                'id' => $id,
                'userid' => $uid,
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
            $where = ['userid' => $uid];
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
     * Notice: 将用户下单信息推送给撮合机器人
     * @param $market
     * @param $data
     * @return mixed
     */
    private function setTradeToRobot($market, $data)
    {
        //撮合机器人接收下单信息地址
        $url = getUrlByMarket($market) . '/api/order/create_order';

        $json = curl_post_http($url, $data);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 撤销委托单
     * @param $id
     * @param $token
     */
    public function mywt($id, $token)
    {
        $auth_token = 'orderId:' . $id . ',price:price_hupa.com,number:number_hupa.com,bidFlag:bidFlag_hupa.com,pair:pair_hupa.com,tradeType:tradeType_hupa.com,validateType:order';

        if (md5($auth_token) != $token) {
            $info['info'] = 'token_error';
            $info['status'] = '1001';
        }

        if (!check($id, 'd')) {
            $info['status'] = '1002';

            $this->ajaxReturn($info);
        }
        $trade = M('Trade')->where(['id' => $id, 'status' => 0])->find();

        if (!$trade) {
            $info['status'] = '1003';

            $this->ajaxReturn($info);
        }

        //首先撤销委托单，然后通知撮合引擎
        $res = CancelCollection::cancelOrder($id);

        if ($res['status'] == 1) {
            //通知搬币机器人订单已撤销
            $market = $trade['market'];
            $res = $this->noticeRobot($id, $market);
            if ($res['status'] == 1) {
                $this->success('提交成功');
            } else {
                $this->error('请求失败，请稍后重试。');
            }
        } else {
            $this->error($res['msg']);
        }
    }

    /**
     * Notice: 通知搬币机器人取消订单
     * @param $id
     * @param $market
     * @return array
     */
    private function noticeRobot($id, $market)
    {
        $url = getUrlByMarket($market) . '/api/order/cancel_order';

        $data = ['orderId' => $id, 'pair' => strtoupper($market)];

        $str = 'orderId:orderId_hupa.com,pair:' . strtoupper($market) . ',tradeType:tradeType_hupa.com,validateType:order';

        $data['token'] = md5($str);

        $result = curl_post_http($url, $data);

        $result = json_decode($result, TRUE);

        $status = 0;
        $msg = '失败';

        if ($result['code'] == 0) {
            $status = 1;
            $msg = '撮合机器人已接收';
        }

        return ['status' => $status, 'msg' => $msg];
    }

    /**
     * Notice: 更新汇率
     */
    public function exchange_rate()
    {
        $result = curl_get_https('https://data.block.cc/api/v1/exchange_rate?base=USDT&symbols=CNY');

        $result = json_decode($result, TRUE);

        $huilv = M('Config')->where(['id' => 1])->save(['cny' => $result['data']['rates']['CNY']]);
    }
}
