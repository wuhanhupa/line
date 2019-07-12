<?php

namespace Home\Controller;

//富pay支付
class FupayController extends HomeController
{
    //商户ID
    protected $usdtpay_cid = '6v7nRBnJw5J6yEdcNDwi7sBQpYCNZFlq';
    //商户密钥
    protected $usdtpay_md5key = 'g0rPhIh3BvnyLcwIcGG42SMfk5oigvSr';
    //回调地址
    protected $callback_url = 'http://47.100.54.209:3200';

    public function index()
    {
        //当前美元
        $usd = getRateByBaidu();
        //当前usdt
        $usdt = getRateByUsdt();
        $buy = ($usd - $usdt) / $usd;
        $bfz = 0.5 * 100;
        $sell = ($usdt - $usd) / $usd;
        if ($buy <= $bfz) {
            $buy = TRUE;
        } else {
            $buy = FALSE;
        }
        if ($sell <= $bfz) {
            $sell = TRUE;
        } else {
            $sell = FALSE;
        }
        $this->assign('buy', $buy);
        $this->assign('sell', $sell);
        $this->display();
    }

    //提交订单
    public function createOrder($num, $price, $type, $paypassword)
    {
        if (!userid()) {
            $info['msg'] = '请先登录';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        if (empty($num)) {
            $info['msg'] = '数量必须';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        if (empty($price)) {
            $info['msg'] = '金额必须';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        if (empty($paypassword)) {
            $info['msg'] = '交易密码必须';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        //判断时间区间
        $now = date('H', time());
        if ($now >= 9 && $now <= 17) {
            $max = 100000;
        } else {
            $max = 50000;
        }
        if ($price > $max) {
            $info['msg'] = '总金额不能大于' . $max;
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }

        //用户
        $user = M('User')->where(['id' => userid()])->find();
        if ($user['ident_status'] != 2) {
            $info['msg'] = '请先通过实名认证';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        //用户资产
        //$userCoin = M('UserCoin')->where(['userid' => userid()])->field('usdt')->find();
        $userCoin = M('ContractUserCoinResult')->where(['user_id' => userid(), 'coin_name' => 'usdt'])->find();
        //用户绑定银行卡
        $bank = M('UserBank')->where(['userid' => userid()])->find();
        //判断交易密码
        if ($user['paypassword'] != $paypassword) {
            $info['msg'] = '交易密码错误';
            $info['code'] = '1000';
            $this->ajaxReturn($info);
        }
        //如果是卖
        if ($type == 2) {
            //判断usdt资产
            if ($num > $userCoin['balance']) {
                $info['msg'] = '用户usdt资产不足';
                $info['code'] = '1000';
                $this->ajaxReturn($info);
            }
            //必须绑定银行卡
            if (!$bank) {
                $info['msg'] = '必须绑定银行卡';
                $info['code'] = '1000';
                $this->ajaxReturn($info);
            }
        }
        $orderNo = $this->getOrderNo();
        //首先生成订单
        $add = M('FupayOrder')->add([
            'order_no' => $orderNo,
            'userid' => userid(),
            'num' => 0,
            'mum' => $num,
            'type' => $type,
            'price' => $price,
            'status' => 1,
            'truename' => $user['truename'],
            'ctime' => time(),
        ]);
        //如果是买，跳转入金链接
        if ($add && $type == 1) {
            // 支付的请求参数
            $request = [
                'orderNo' => $orderNo, //商户订单号，最大长度60个字符
                'customerId' => $user['truename'], //买家姓名
                'orderCurrency' => 'CNY', //订单币种，固定值：USDT或CNY
                'orderAmount' => $price, //订单金额，USDT单位为个，CNY单位为元
                'receiveUrl' => $this->callback_url . '/Fupay/callback', //  通知回调地址
                'pickupUrl' => $this->callback_url . '/Fupay/index', //  交易完成后跳转URL
                'signType' => 'MD5', //固定值不要改
            ];

            $url = $this->usdtpay_MakePaymentUrl($request);

            $info['code'] = '0000';
            $info['msg'] = '下单链接';
            $info['data'] = $url;

            $this->ajaxReturn($info);
        } else {
            //出金，首先冻结用户usdt资产
            //这里的数量为预估数量，实际扣除数量以最后出金为准
            //$res1 = M('UserCoin')->where(['userid' => userid()])->setDec('usdt', $num);
            //$res2 = M('UserCoin')->where(['userid' => userid()])->setInc('usdtd', $num);

            //冻结合约账号资产
            $res1 = M('ContractUserCoinResult')->where([
                'user_id' => userid(), 'coin_name' => 'usdt'
            ])->setDec('balance', $num);
            $res2 = M('ContractUserCoinResult')->where([
                'user_id' => userid(), 'coin_name' => 'usdt'
            ])->setInc('freeze', $num);

            if ($res1 && $res2) {
                $info['msg'] = '已提交，等待工作人员处理';
                $info['code'] = '0000';
                $this->ajaxReturn($info);
            } else {
                $info['msg'] = '冻结资产失败';
                $info['code'] = '1000';
                $this->ajaxReturn($info);
            }
        }
    }

    //生成一个订单号
    protected function getOrderNo()
    {
        return 'GTE' . date('YmdHis') . rand(111, 999);
    }

    //生成入金url
    protected function usdtpay_MakePaymentUrl($request)
    {
        $request['sign'] = $this->usdtpay_Sign($request, $this->usdtpay_md5key);

        return sprintf('http://we.fupaipay.com/payment/%s?%s', $this->usdtpay_cid, http_build_query($request));
    }

    //生成签名方法
    protected function usdtpay_Sign($request, $salt)
    {
        $signStr = $request['pickupUrl'];
        $signStr .= $request['receiveUrl'];
        $signStr .= $request['signType'];
        $signStr .= $request['orderNo'];
        $signStr .= $request['orderAmount'];
        $signStr .= $request['orderCurrency'];
        $signStr .= $request['customerId'];
        $signStr .= $salt;

        return md5($signStr);
    }

    //入金回调
    public function callback()
    {
        //mlog(json_encode($_POST));

        $orderNo = $_POST['orderNo']; //商户订单号
        $orderAmount = $_POST['orderAmount']; //订单金额
        $orderCurrency = $_POST['orderCurrency']; //订单币种， 固定值： USDT
        $paymentAmount = $_POST['paymentAmount']; //用户支付金额，单位：元，币种：人民币
        $transactionId = $_POST['transactionId']; //平台流水号
        $status = $_POST['status']; //状态说明，固定值：success
        $signType = $_POST['signType']; //签名算法，固定值：MD5
        $sign = $_POST['sign']; //参数校验签名

        //sign = md5( signType + orderNo + orderAmount + orderCurrency + transactionId + sta tus + md5Key );
        //验证签名是否伪造
        $str = md5($signType . $orderNo . $orderAmount . $orderCurrency . $transactionId . $status . $this->usdtpay_md5key);
        if ($str != $sign) {
            echo '签名错误';
        } else {
            //根据订单号查找用户订单
            $order = M('FupayOrder')->where(['order_no' => $orderNo])->find();
            if ($order && $order['status'] == 1) {
                //查找是否有合约资产账户
                $find = M('ContractUserCoinResult')->where([
                    'user_id' => $order['userid'], 'coin_name' => 'usdt'
                ])->find();
                //没有合约资产生成一条
                if (!$find) {
                    M('ContractUserCoinResult')->add([
                        'user_id' => $order['userid'],
                        'coin_name' => 'usdt',
                        'total' => 0,
                        'balance' => 0,
                        'freeze' => 0,
                        'income' => 0,
                        'order_margin' => 0,
                        'position_margin' => 0,
                        'ctime' => msectime(),
                        'mtime' => msectime(),
                        'remark' => '',
                    ]);
                }

                //修改订单状态
                $save = M('FupayOrder')->where(['order_no' => $orderNo])->save(['status' => 2, 'num' => $orderAmount]);
                //增加用户可用usdt资产
                //$coin = M('UserCoin')->where(['userid' => $order['userid']])->setInc('usdt', $order['num']);
                $coin = M('ContractUserCoinResult')->where([
                    'user_id' => $order['userid'], 'coin_name' => 'usdt'
                ])->setInc('balance', $orderAmount);
                $coin = M('ContractUserCoinResult')->where([
                    'user_id' => $order['userid'], 'coin_name' => 'usdt'
                ])->setInc('total', $orderAmount);
                if ($save && $coin) {
                    echo 'success';
                }
            }
        }
    }

    //订单列表
    public function orderList($type)
    {
        $list = M('FupayOrder')->where(['userid' => userid(), 'status' => $type])->order('id desc')->limit(50)->select();

        $data = [];
        $arr = ['1' => '未完成', '2' => '已完成', '3' => '已取消'];
        foreach ($list as $k => $v) {
            $data[$k]['id'] = $v['id'];
            $data[$k]['order_no'] = $v['order_no'];
            $data[$k]['num'] = sprintf("%.2f", $v['num']);
            $data[$k]['type'] = $v['type'];
            $data[$k]['price'] = round($v['price'], 2);
            $data[$k]['status'] = $arr[$v['status']];
            $data[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
        }

        $info['code'] = '0000';
        $info['msg'] = '订单列表';
        $info['data'] = $data;
        $this->ajaxReturn($info);
    }
}