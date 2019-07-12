<?php

namespace Home\Controller;

use Common\Design\SetTrade\ModelHandle;
use Common\Design\Suanfa\SnowFlake;
use Common\Design\Cancel\Collection as CancelCollection;
use Common\Design\SetTrade\Collection as SetCollection;

class TradeController extends HomeController
{
    //交易首页
    public function index($market = NULL)
    {
        if (!$market) {
            $market = 'bys_usdt';
        }
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $title = D('Coin')->get_title($xnb);
        $round = M('Market')->where(['name' => $market])->getField('round');
        $num_round = M('Market')->where(['name' => $market])->getField('num_round');
        $price_round = M('Market')->where(['name' => $market])->getField('price_round');

        $this->assign('market', $market);
        $this->assign('round', $round);
        $this->assign('num_round', $num_round);
        $this->assign('price_round', $price_round);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('title', $title);

        $this->display();
    }

    //虚拟币行情页
    public function chart($market = 'bys_usdt')
    {
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $title = D('Coin')->get_title($xnb);

        $this->assign('market', $market);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('title', $title);
        $this->display();
    }

    //了解虚拟币
    public function info($market = NULL)
    {
        if (!$market) {
            $market = 'bys_usdt';
        }

        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];
        $title = D('Coin')->get_title($xnb);

        $coin = M('Coin')->where(['name' => $xnb])->find();

        $this->assign('market', $market);
        $this->assign('coin', $coin);
        $this->assign('xnb', $xnb);
        $this->assign('rmb', $rmb);
        $this->assign('title', $title);
        $this->display();
    }

    public function ordinary($market = NULL)
    {
        if (!$market) {
            $market = 'bys_usdt';
        }

        $this->assign('market', $market);
        $this->display();
    }

    public function specialty($market = NULL)
    {
        if (!$market) {
            $market = 'bys_usdt';
        }

        $this->assign('market', $market);
        $this->display();
    }

    /**
     * Notice:[upTrade 用户下单]
     * @author: hxq
     * @param null $paypassword
     * @param null $market
     * @param      $price
     * @param      $num
     * @param      $type
     */
    public function upTrade($paypassword = NULL, $market = NULL, $price, $num, $type)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($price, 'double')) {
            $this->error('交易价格格式错误');
        }

        if (!check($num, 'double')) {
            $this->error('交易数量格式错误');
        }

        if (($type != 1) && ($type != 2)) {
            $this->error('交易类型格式错误');
        }

        $user = M('User')->where(['id' => userid()])->find();
        /* if (md5($paypassword) != $user['paypassword']) {
             $this->error('交易密码错误！');
         }*/

        $pair = M('Market')->where(['name' => $market, 'status' => 1])->find();
        if (!$pair) {
            $this->error('交易市场错误');
        }
        $xnb = explode('_', $market)[0];
        $rmb = explode('_', $market)[1];

        $price = round($price, $pair['price_round']);
        if (!$price) {
            $this->error('交易价格错误' . $price);
        }

        $num = round($num, $pair['num_round']);

        if ($type == 1) {
            $min_price = ($pair['buy_min'] ? $pair['buy_min'] : 1.0E-8);
            $max_price = ($pair['buy_max'] ? $pair['buy_max'] : 10000000);
        } else if ($type == 2) {
            $min_price = ($pair['sell_min'] ? $pair['sell_min'] : 1.0E-8);
            $max_price = ($pair['sell_max'] ? $pair['sell_max'] : 10000000);
        } else {
            $this->error('交易类型错误');
        }

        if ($max_price < $price) {
            $this->error('交易价格超过最大限制！');
        }

        if ($price < $min_price) {
            $this->error('交易价格超过最小限制！');
        }

        $hou_price = $pair['hou_price'];

        if ($hou_price) {
            if ($pair['zhang']) {
                $zhang_price = round(($hou_price / 100) * (100 + $pair['zhang']), $pair['round']);

                if ($zhang_price < $price) {
                    $this->error('交易价格超过今日涨幅限制！');
                }
            }
            if ($pair['die']) {
                $die_price = round(($hou_price / 100) * (100 - $pair['die']), $pair['round']);

                if ($price < $die_price) {
                    $this->error('交易价格超过今日跌幅限制！');
                }
            }
        }

        $user_coin = M('UserCoin')->where(['userid' => userid()])->find();

        if ($type == 1) {
            $mum = round($num * $price, 8);

            if ($user_coin[$rmb] < $mum) {
                $this->error('USDT余额不足！');
            }
        } else if ($type == 2) {
            $mum = round($num * $price, 8);

            if ($user_coin[$xnb] < $num) {
                $this->error($xnb . '余额不足！');
            }
        } else {
            $this->error('交易类型错误');
        }
        if ($pair['trade_min']) {
            if ($mum < $pair['trade_min']) {
                //$this->error('交易总额不能小于' . $pair['trade_min']);
            }
        }
        if ($pair['trade_max']) {
            if ($pair['trade_max'] < $mum) {
                $this->error('交易总额不能大于' . $pair['trade_max']);
            }
        }
        if (!$rmb) {
            $this->error('数据错误1');
        }
        if (!$xnb) {
            $this->error('数据错误2');
        }
        if (!$market) {
            $this->error('数据错误3');
        }
        if (!$price) {
            $this->error('数据错误4');
        }
        if (!$num) {
            $this->error('数据错误5');
        }
        if (!$mum) {
            $this->error('数据错误6');
        }
        if (!$type) {
            $this->error('数据错误7');
        }

        //组装数据
        $alog = new SnowFlake();
        $id = $alog->nextId();

        //首先生成订单
        $create = $this->createOrder($id, $market, $price, $num, $type);
        if ($create['status'] == 0) {
            $this->error($create['msg']);
        }
        $data = [];
        $data['userId'] = (int)userid();
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
            $this->success('成功推送给智能引擎！');
        } else {
            //给某人发短信，引擎失败了
            noticeZW();

            $this->error('下单失败，稍后重试。');
        }
    }

    //创建委托单
    protected function createOrder($id, $market, $price, $num, $type)
    {
        try {
            //开启事物
            M()->startTrans();

            $fee = bcmul($price, $num, 8);
            $add = M('Trade')->add([
                'id' => $id,
                'userid' => userid(),
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
            $where = ['userid' => userid()];
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

    protected function createOrderOther($id, $userid, $market, $price, $num, $type)
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
                //M('UserCoin')->where($where)->setDec($legal, $fee);
                //增加冻结资产
                ModelHandle::UpdateFieldNum('UserCoin', $where, $legal . 'd', $fee, TRUE);
                //M('UserCoin')->where($where)->setInc($legal.'d', $fee);
            } else {
                //判断资产
                $able = M('UserCoin')->where($where)->getField($coin);
                if ($able < $num) {
                    throw new \Exception($coin . '资产不足');
                }
                //减去可用虚拟币
                ModelHandle::UpdateFieldNum('UserCoin', $where, $coin, $num, FALSE);
                //M('UserCoin')->where($where)->setDec($coin, $num);
                //增加冻结虚拟币
                ModelHandle::UpdateFieldNum('UserCoin', $where, $coin . 'd', $num, TRUE);
                //M('UserCoin')->where($where)->setInc($coin.'d', $num);
            }

            M()->commit();

            return ['status' => 1, 'msg' => 'success'];
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            M()->rollback();

            return ['status' => 0, 'msg' => $msg];
        }
    }

    //将用户下单信息推送给撮合机器人
    private function setTradeToRobot($market, $data)
    {
        //撮合机器人接收下单信息地址
        $url = getUrlByMarket($market) . '/api/order/create_order';

        $json = curl_post_http($url, $data);
        robotlog($json);
        $res = json_decode($json, TRUE);

        return $res;
    }

    public function chexiao($id)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($id, 'd')) {
            $this->error('请选择要撤销的委托！');
        }

        $trade = M('Trade')->where(['id' => $id])->find();

        if (count($trade) == 0) {
            $this->error('撤销委托参数错误！');
        }

        if ($trade['userid'] != userid()) {
            $this->error('参数非法！');
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

    public function cxtrade($id)
    {
        $this->show(D('Trade')->cxtrade($id));
    }

    public function show($rs = [])
    {
        if ($rs[0]) {
            $this->success($rs[1]);
        } else {
            $this->error($rs[1]);
        }
    }

    //通知搬币机器人取消订单
    private function noticeRobot($id, $market)
    {
        $url = getUrlByMarket($market) . '/api/order/cancel_order';

        $data = ['orderId' => $id, 'pair' => strtoupper($market), 'time' => msectime()];

        $str = 'orderId:' . $id . ',price:price_hupa.com,number:number_hupa.com,bidFlag:bidFlag_hupa.com,pair:pair_hupa.com,tradeType:tradeType_hupa.com,validateType:order';

        $data['token'] = md5($str);

        $result = curl_post_http($url, $data);
        //返回值写入日志
        modellog($result);

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

    //提供给撮合引擎的未成交订单
    public function getAllTrade($market = NULL)
    {
        if (empty($market)) {
            $info['status'] = 0;
            $info['msg'] = '缺少交易对参数';

            $this->ajaxReturn($info);
        }
        //交易对转小写
        $market = strtolower($market);

        //查询所有未成交的单
        $trades = M('Trade')->where(['market' => $market, 'status' => 0])->select();

        //处理数据
        $metadata = ['userId', 'orderId', 'time', 'price', 'bidFlag', 'orderType', 'amount', 'exchangedAmount'];

        $array = [];

        if (count($trades) > 0) {
            foreach ($trades as $key => $trade) {
                if ($trade['type'] == 1) {
                    $bidFlag = 1;
                } else {
                    $bidFlag = 0;
                }
                $data = [
                    (int)$trade['userid'],
                    (int)$trade['id'],
                    (int)$trade['addtime'],
                    floatval($trade['price']),
                    $bidFlag,
                    (int)$trade['trade_type'],
                    floatval($trade['num']),
                    floatval($trade['deal']),
                ];

                $array[] = $data;
            }
        }

        $result = [];
        $result['metadata'] = $metadata;
        $result['data'] = $array;

        $this->ajaxReturn($result);
    }

    //接受撮合引擎发送的撮合结果
    public function acceptMatchResult($input = NULL)
    {
        if (count($input) == 0) {
            $info['status'] = 0;
            $info['msg'] = '缺少参数';

            $this->ajaxReturn($info);
        }
        //转换数组
        $array = json_decode($input, TRUE);
        robotlog(json_encode($array));
        //获取委托单信息
        $taker = $array['taker'];
        //验证taker信息
        if (empty($taker['orderId']) || empty($taker['userId']) || empty($taker['pair'])) {
            $info['status'] = 0;
            $info['msg'] = '参数错误';

            $this->ajaxReturn($info);
        }
        //获取匹配订单信息
        $maker = $array['maker'];
        //处理撮合结果
        $handle = new SetCollection($taker, $maker);
        $res = $handle->entry();

        $this->ajaxReturn($res);
    }

    //接收指定交易对
    public function specifiedPair($userId = NULL, $pair = NULL, $price = NULL, $num = NULL, $bidFlag = NULL)
    {
        if (empty($userId)) {
            $info['status'] = 0;
            $info['msg'] = '用户id不能为空';

            $this->ajaxReturn($info);
        }
        if (empty($pair)) {
            $info['status'] = 0;
            $info['msg'] = '交易对不能为空';

            $this->ajaxReturn($info);
        }
        if (empty($price)) {
            $info['status'] = 0;
            $info['msg'] = '价格不能为空';

            $this->ajaxReturn($info);
        }
        if (empty($num)) {
            $info['status'] = 0;
            $info['msg'] = '数量不能为空';

            $this->ajaxReturn($info);
        }
        if (!in_array($bidFlag, [0, 1])) {
            $info['status'] = 0;
            $info['msg'] = '交易类型错误';

            $this->ajaxReturn($info);
        }
        $user = M('User')->where(['id' => $userId])->find();
        if (!$user) {
            $info['status'] = 0;
            $info['msg'] = '用户不存在';

            $this->ajaxReturn($info);
        }
        $userCoin = M('UserCoin')->where(['userid' => $userId])->find();
        //本次成交额
        $mum = bcmul($price, $num, 8);
        //买，查询usdt资产
        if ($bidFlag == 1) {
            if ($userCoin['usdt'] < $mum) {
                $info['status'] = 0;
                $info['msg'] = '用户usdt资产不足';

                $this->ajaxReturn($info);
            }
        }
        $xnb = strtolower(explode('_', $pair)[0]);
        //卖，查询bys资产
        if ($bidFlag == 0) {
            if ($userCoin[$xnb] < $num) {
                $info['status'] = 0;
                $info['msg'] = '用户' . $xnb . '虚拟币资产不足';

                $this->ajaxReturn($info);
            }
        }

        //组装数据
        $alog = new SnowFlake();
        $id = $alog->nextId();

        //首先生成订单
        if ($bidFlag == 1) {
            $type = 1;
        } else {
            $type = 2;
        }
        cancellog($id);
        $create = $this->createOrderOther($id, $userId, strtolower($pair), $price, $num, $type);

        if ($create['status'] == 0) {
            $this->error($create['msg']);
        }

        $data = [];
        $data['userId'] = (int)$userId;
        $data['orderId'] = (int)$id;
        $data['time'] = (int)time();
        $data['price'] = (float)$price;
        $data['pair'] = strtoupper($pair);
        $data['bidFlag'] = $bidFlag;
        $data['orderType'] = 2;
        $data['amount'] = (float)$num;
        $data['exchangedAmount'] = 0;
        $str = 'orderId:orderId_hupa.com' . ',price:' . $data['price'] . ',number:' . $data['amount'] . ',bidFlag:' . $data['bidFlag'] . ',pair:' . $data['pair'] . ',tradeType:2,validateType:order';

        $data['token'] = md5($str);

        $res = $this->setTradeToRobot($pair, $data);
        //执行成功
        if (isset($res) && $res['code'] == 0) {
            $info['status'] = 1;
            $info['msg'] = '成功推送给智能引擎';

            $this->ajaxReturn($info);
        } else {
            $info['status'] = 1;
            $info['msg'] = '撮合引擎接收失败';

            $this->ajaxReturn($info);
        }
    }

    //撤销
    public function cancelOrder($id)
    {
        $order = M('Trade')->where(['id' => $id])->find();
        if (!$order) {
            $info['status'] = 0;
            $info['msg'] = '订单不存在';

            $this->ajaxReturn($info);
        }

        //首先撤销委托单，然后通知撮合引擎
        $res = CancelCollection::cancelOrder($id);

        if ($res['status'] == 1) {
            //通知搬币机器人订单已撤销
            $market = $order['market'];
            $res = $this->noticeRobot($id, $market);
            if ($res['status'] == 1) {
                $info['status'] = 1;
                $info['msg'] = '提交成功';

                $this->ajaxReturn($info);
            } else {
                $info['status'] = 0;
                $info['msg'] = '请求失败，请稍后重试。';

                $this->ajaxReturn($info);
            }
        } else {
            $info['status'] = 0;
            $info['msg'] = $res['msg'];

            $this->ajaxReturn($info);
        }
    }
}
