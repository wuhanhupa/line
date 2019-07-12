<?php

namespace Home\Controller;

class CztradeController extends HomeController
{
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

    public function yj_register()
    {
        $user = M('User')->where(['id' => userid()])->find();
        if ($user['member_id'] == 0 || empty($user['member_id'])) {
            //当前用户没有注册彧捷支付
            $ret = $this->member_register($user);

            if ($ret['Status'] == "Success") {
                //注册成功之后修改用户表中的meid

                $user = M('User')->where(['id' => userid()])->save(['member_id' => $ret['Result']['member_id']]);
                $info['code'] = '0000';
                $info['msg'] = 'success';
                $this->ajaxReturn($info);
            } else {
                $info['code'] = '4000';
                $info['msg'] = 'error';
                $this->ajaxReturn($info);
            }
        } else {
            $info['code'] = '0000';
            $info['msg'] = 'success';
            $this->ajaxReturn($info);
        }
    }

    //注册新会员 
    public function member_register($user)
    {
        $result = $this->yjToken();

        $data = [
            'name' => $user['truename'],//会员姓名
            'username' => $user['username'],//用户名
            'password' => 'hupa123456',//密码
            'mobile_phone' => $user['moble'],//手机号码
            'email' => 'hupa123@qq.com',//邮箱
            'member_number' => $user['id'],//会员号
            'merchant_id' => $result['Result']['merchant_id'],//平台商编号
            'api_token' => $result['Result']['api_token'],//Api Token
        ];

        $url = C('YJ_ADDR') . "/api/v3/member_register";

        $result = curl_post_http($url, $data);
        $ret = json_decode($result, TRUE);

        return $ret;
    }

    //用户买入卖出
    public function member_order($parities, $num, $price, $type, $paypassword)
    {
        $info['msg'] = '体验站不支持出入金';
        $info['code'] = '7011';

        return $this->ajaxReturn($info);


        //判断是否绑卡
        $user = M('User')->where(['id' => userid()])->find();
        $bank = M('User_bank')->where(['userid' => userid()])->find();
        $user_coin = M('User_coin')->where(['userid' => userid()])->field('usdt')->find();
        $config = M('Config')->field('yuudidi')->find();
        $where = " userid = " . userid() . " and (order_status != 'Cancel' and order_status != 'Completed' )";
        $user_order = M('Cztrade_order')->where($where)->find();
        //所有用户下单之前判断下是否有member_token
        $member_token = $this->memberToken();

        if ($config['yuudidi'] == '1') {
            $info['msg'] = '系统繁忙中,请您稍后在交易';
            $info['code'] = '7011';

            return $this->ajaxReturn($info);
        }

        if ($paypassword != $user['paypassword']) {
            $info['msg'] = '交易密码错误！';
            $info['code'] = '7001';

            return $this->ajaxReturn($info);
        }
        if (!empty($user_order)) {
            $info['msg'] = '您有未处理的订单,请处理完成之后再下单';
            $info['code'] = '7601';

            return $this->ajaxReturn($info);
        }

        if ($type == 2 && ($num > $user_coin['usdt'])) {
            $info['msg'] = '余额不足不能卖出';
            $info['code'] = '7001';

            return $this->ajaxReturn($info);
        }

        if ($price < 100) {
            $info['msg'] = '最小金额100';
            $info['code'] = '7002';

            return $this->ajaxReturn($info);
        }

        $isint = is_int($price / 100);

        if ($isint == FALSE) {
            $info['msg'] = '输入的金额必须是100的整数或者是倍数';
            $info['code'] = '7003';

            return $this->ajaxReturn($info);
        }

        $FialLimit = getFialLimit();

        if ($price > $FialLimit) {
            $info['msg'] = '单笔金额不能大于' . $FialLimit;
            $info['code'] = '7004';

            return $this->ajaxReturn($info);
        }

        //用户下单
        $data = [
            'member_id' => $user['member_id'],
            'userid' => userid(),
            'parities' => $parities,
            'num' => $num,
            'price' => $price,
            'order_number' => orderId(),
            'crtime' => time(),
            'type' => $type,
            'name' => $bank['name'],  //售单需要的
            'bank_name' => $bank['bank'],//银行名称
            'branch_name' => $bank['bankaddr'],//开户支行
            'bank_account_no' => $bank['bankcard']//银行账号
        ];

        //下单成功之后调用彧捷接口
        if ($type == 1) {
            $this->member_buy($data, $member_token);
        } else {
            $this->ispay();
            $this->member_sell($data, $member_token);
        }
    }

    /*会员购买订单
      备注
      1.登录到平台商后台管理后，您可以设置默认返回URL和默认通知URL。
      2.如果有提交参数的返回URL和通知URL将被优先处理，否则将使用默认值。
      3.订单完成后将通过返回URL跳转会员到平台商网站。
      4.通知URL用于将订单信息通过通知（POST）到平台商服务器。
      5.同一会员身份不能在1分钟内提交相同的货币价值金额。
      6.所有金额必须是整数，至少100和100的倍数。*/
    public function member_buy($order, $member_token)
    {
        $result = $this->getApiToken();

        $data = [
            'member_id' => $order['member_id'],//会员编号
            'member_number' => $order['userid'],//会员号
            'member_username' => $this->yname(),//用户名
            'currency_value' => $order['price'],//人民币购买数额
            'merchant_order_no' => $order['order_number'],//平台商订单号
            'api_token' => $result['api_token'],//API Token
            'merchant_id' => $result['merchant_id'],//平台商编号
            'return_url' => C('WEB_ADDR') . '/Home/Cztrade/reurldate',//返回网址
            'notify_url' => C('WEB_ADDR') . '/Home/Cztrade/notiyurl',//通知网址
            'alipay_account' => $this->alipay_account()//阿里账号
        ];

        $merchant_id = $data['merchant_id'];
        $apitoken = $data['api_token'];
        $username = $data['member_username'];

        $url = C('YJ_ADDR') . "/api/v3/member_buy";
        $result = curl_post_http($url, $data);
        $ret = json_decode($result, TRUE);

        $memberToken = $member_token;
        if ($ret['Status'] == "Success") {
            //下单成功 修改订单状态
            $order_data = M('Cztrade_order')->where(['order_number' => $data['merchant_order_no']])->add([
                'userid' => userid(),
                'order_number' => $order['order_number'],
                'parities' => $order['parities'],
                'num' => $order['num'],
                'price' => $order['price'],
                'order_number' => $order['order_number'],
                'crtime' => time(),
                'type' => $order['type'],
                'order_id' => $ret['Result']['order_id'],
                'order_no' => $ret['Result']['order_no'],
                'status' => $ret['Result']['status'],
                'order_status' => $ret['Result']['status'],
                'btusd' => $ret['Result']['btusd']
            ]);

            if ($order_data) {
                $this->redirect_web('/zh/member/currentorder1', $memberToken);
            }
        } else {
            if (($ret['Result'] == 'Invalid Merchant api token.') || ($ret['Result'] == 'Invalid credentials passed,please login')) {
                //更新api_token
                $re = $this->yjToken();

                if (M('Config')->where(['id' => 1])->save(['api_token' => $re['Result']['api_token']])) {
                    $info['code'] = '4916';
                    $info['msg'] = '订单匹配不成功,请重新下单!';
                    $info['status'] = 'error';
                    $this->ajaxReturn($info);
                } else {
                    $info['code'] = '4526';
                    $info['msg'] = '订单发送失败,请您重新下单!';
                    $info['status'] = 'error';
                    $this->ajaxReturn($info);
                }
            } else {
                $info['code'] = $ret['Status Code'];
                $info['msg'] = '未匹配交易,请重新尝试';
                $info['status'] = $ret['Sell quantity exceeded available quantity'];
                $this->ajaxReturn($info);
            }
        }
    }

    //会员出售订单
    public function member_sell($order, $member_token)
    {
        $result = $this->getApiToken();
        //查询当前 用户绑定的银行卡
        $data = [
            'member_id' => $order['member_id'],//会员编号
            'member_username' => $this->yname(),//用户名
            'member_number' => $order['userid'],//会员号
            'currency_value' => $order['price'],//人民币购买数额
            'api_token' => $result['api_token'],//API Token
            'merchant_id' => $result['merchant_id'],//平台商编号
            'alipay_account' => $this->alipay_account(),//阿里账号
            'withdraw_no' => $order['order_number'],//平台商提款号
            'account_holder_name' => $order['name'],//持卡人姓名
            'bank_name' => $order['bank_name'],//银行名称
            'branch_name' => $order['branch_name'],//开户支行
            'bank_account_no' => $order['bank_account_no'],//银行账号http://139.224.64.194:9002
            'return_url' => C('WEB_ADDR') . '/Home/Cztrade/reurldate',//返回网址
            'notify_url' => C('WEB_ADDR') . '/Home/Cztrade/notiyurl',//通知网址
        ];
        $merchant_id = $data['merchant_id'];
        $apitoken = $data['api_token'];
        $username = $data['member_username'];

        $url = C('YJ_ADDR') . "/api/v3/order_sell";
        $result = curl_post_http($url, $data);
        $ret = json_decode($result, TRUE);
        $memberToken = $member_token;

        if ($ret['Status'] == "Success") {
            //下单成功 修改订单状态
            $order_data = M('Cztrade_order')->where(['order_number' => $data['merchant_order_no']])->add([
                'userid' => userid(), 'order_number' => $order['order_number'], 'parities' => $order['parities'],
                'num' => $order['num'], 'price' => $order['price'], 'order_number' => $order['order_number'], 'crtime' => time(), 'type' => $order['type'], 'order_id' => $ret['Result']['order_id'],
                'order_no' => $ret['Result']['order_no'], 'status' => $ret['Result']['status'], 'order_status' => $ret['Result']['status'], 'btusd' => $ret['Result']['btusd']
            ]);
            //下单成功之后冻结用户的资产
            $zc = M('User_coin')->where(['userid' => userid()])->field('usdt,usdtd')->find();
            $usdt = $zc['usdt'] - $order['num'];
            $usdtd = $zc['usdtd'] + $order['num'];
            $zc = M('User_coin')->where(['userid' => userid()])->save(['usdtd' => $usdtd, 'usdt' => $usdt]);

            if ($order_data) {
                $this->redirect_web('/zh/member/currentorder1', $memberToken);
            }
        } else {
            if ($ret['Result'] == 'Invalid Merchant api token.' || $ret['Result'] == 'Invalid credentials passed,please login') {
                //更新api_token
                $re = $this->yjToken();

                if (M('Config')->where(['id' => 1])->save(['api_token' => $re['Result']['api_token']])) {
                    $info['code'] = '4916';
                    $info['msg'] = '订单匹配不成功,请重新下单!';
                    $info['status'] = 'error';
                    $this->ajaxReturn($info);
                } else {
                    $info['code'] = '4526';
                    $info['msg'] = '订单发送失败,请您重新下单!';
                    $info['status'] = 'error';
                    $this->ajaxReturn($info);
                }
            } else if ($ret['Result'] == "Sell quantity exceeded merchant available quantity") {
                //更新api_token
                $info['code'] = $ret['Status Code'];
                $info['msg'] = '当前卖出系统维护中请稍后在卖出';
                $info['status'] = $ret['Sell quantity exceeded available quantity'];
                $this->ajaxReturn($info);
            } else {
                $info['code'] = $ret['Status Code'];
                $info['msg'] = '未匹配交易,请重新尝试';
                $info['status'] = '';
                $this->ajaxReturn($info);
            }
        }
    }

    //用户名
    public function yname()
    {
        $username = M('User')->where(['id' => userid()])->field('username')->find();

        return $username['username'];
    }

    //阿里账号
    public function alipay_account()
    {
        $account = M('Alipay')->where(['userid' => userid()])->field('account')->find();

        return $account['account'];
    }

    public function reurldate($merchant_order_no = NULL, $order_id = NULL, $order_no = NULL, $order_status = NULL, $completed_value = NULL, $transfer_id = NULL, $transfer_date = NULL, $transfer_btusd = NULL, $merchant_point = NULL, $transfer_status = NULL)
    {
        if ($merchant_order_no && $order_no) {
            $check = M('Cztrade_order')->where(['order_id' => $order_id])->find();

            if (!$check) {
                return $this->error('订单未找到');
            }

            mlog($merchant_order_no . '--' . $order_no . '--' . $order_status);

            if ($check) {
                //接受彧捷支付返回的订单结果
                $status = M('Cztrade_order')->where(['order_id' => $order_id])->save(['order_status' => $order_status, 'status' => $order_status]);
            } else {
                mlog('没有参数，没有找到订单');
            }
        }

        $this->redirect('Cztrade/index');
    }

    //彧捷支付消息通知链接如果设定了通知URL，当Yuudidi通知平台商服务器时，遵循HTTP POST信息将传递给通知URL。
    public function notiyurl($merchant_order_no, $order_id, $order_no, $order_status, $completed_value, $transfer_id, $transfer_date, $transfer_btusd, $merchant_point, $transfer_status)
    {
        //1.首先判断这个订单是否已经在notify表当中
        $order = M('Cztrade_notify')->where(['order_id' => $order_id])->field('order_id')->find();

        if (empty($order)) {
            $data = M('Cztrade_notify')->add([
                'merchant_order_no' => $merchant_order_no,
                'order_id' => $order_id,
                'order_no' => $order_no,
                'order_status' => $order_status,
                'completed_value' => $completed_value,
                'transfer_id' => $transfer_id,
                'transfer_date' => $transfer_date,
                'transfer_btusd' => $transfer_btusd,
                'merchant_point' => $merchant_point,
                'transfer_status' => $transfer_status,
                'crtime' => time()
            ]);
            if ($data) {
                //如果付款成功 查询需要增加的USDT数量
                $user_coin = M('Cztrade_order')->where(['order_id' => $order_id])->field('num,userid,type')->find();

                $user_usdt = M('user_coin')->where(['userid' => $user_coin['userid']])->field('usdt,usdtd')->find();
                if ($order_status == "Completed") {
                    if ($user_coin['type'] == 1) {
                        $usdt = $user_usdt['usdt'] + $user_coin['num'];
                        $user_usdt = M('user_coin')->where(['userid' => $user_coin['userid']])->save(['usdt' => $usdt]);
                    } else {
                        $usdt = $user_usdt['usdtd'] - $user_coin['num'];
                        $user_usdt = M('user_coin')->where(['userid' => $user_coin['userid']])->save(['usdtd' => $usdt]);
                    }
                }
                if ($order_status == "Cancel") {
                    $user_usdt = M('user_coin')->where(['userid' => $user_coin['userid']])->field('usdt,usdtd')->find();
                    if ($user_coin['type'] != 1) {
                        $usdtd = $user_usdt['usdtd'] - $user_coin['num'];
                        $usdt = $user_usdt['usdt'] + $user_coin['num'];
                        $user_usdt = M('user_coin')->where(['userid' => $user_coin['userid']])->save(['usdtd' => $usdtd, 'usdt' => $usdt]);
                    }
                }

                $save = M('Cztrade_order')->where(['order_id' => $order_id])->save(['status' => $order_status, 'order_status' => $order_status]);
                if ($save) {
                    $info['code'] = "0000";
                    $info['msg'] = "ok";

                    return $this->ajaxReturn($info);
                } else {
                    $info['code'] = "9000";
                    $info['msg'] = "error";

                    return $this->ajaxReturn($info);
                }
            }
        } else {
            $info['code'] = "5000";
            $info['msg'] = "订单已经存在";

            return $this->ajaxReturn($info);
        }
    }

    //type 1当前订单 2完成订单 3取消订单
    public function order_list($type)
    {
        if ($type == 1) {
            $where = 'userid=' . userid() . ' and order_status != "Completed" and order_status != "Cancel" ';
        }
        if ($type == 2) {
            $where = 'userid=' . userid() . ' and order_status = "Completed"';
        }
        if ($type == 3) {
            $where = 'userid=' . userid() . ' and order_status = "Cancel"';
        }
        $list = M('Cztrade_order')->where($where)->field('order_number,price,type,order_no,order_status,num,parities,crtime')->order('crtime desc')->select();

        $memberToken = $this->memberToken();

        foreach ($list as $k => $v) {
            $status = $this->orderstatus($v['order_status']);

            if ($status == "Orderplaced") {
                $list[$k]['order_status'] = "匹配中";
                $list[$k]['url'] = $this->redirect_orderweb('/zh/member/currentorder1?order_no=' . $v['order_no'], $memberToken);
            }
            if ($status == "Pendingpayment") {
                $list[$k]['order_status'] = "等待付款";
                $list[$k]['url'] = $this->redirect_orderweb('/zh/member/currentorder1?order_no=' . $v['order_no'], $memberToken);
            }
            if ($status == "Pendingconfirmation") {
                $list[$k]['order_status'] = "等待卖家确认收款";
                $list[$k]['url'] = $this->redirect_orderweb('/zh/member/currentorder1?order_no=' . $v['order_no'], $memberToken);
            }
            if ($status == "Completed") {
                $list[$k]['order_status'] = "交易完成";
            }
            if ($status == "Cancel") {
                $list[$k]['order_status'] = "取消订单";
            }
            $list[$k]['crtime'] = date('Y-m-d H:i:s', $v['crtime']);
            $list[$k]['price'] = round($v['price'], 2);
        }

        $info['code'] = '0000';
        $info['msg'] = '订单列表';
        $info['data'] = $list;
        $this->ajaxReturn($info);
    }

    //获取平台商token
    public function yjToken()
    {
        $url = C('YJ_ADDR') . "/api/v3/merchant_gettoken";
        $username = C('YJ_NAME');
        $password = C('YJ_PWD');
        $data = ['username' => $username, 'password' => $password];

        $ret = curl_post_http($url, $data);
        $result = json_decode($ret, TRUE);

        return $result;
    }

    public function getApiToken()
    {
        $apitoken = M('Config')->where(['id' => 1])->field('api_token,merchant_id')->find();
        if (empty($apitoken['api_token'])) {
            $result = $this->yjToken();
            M('Config')->where(['id' => 1])->save(['api_token' => $result['Result']['api_token'], 'merchant_id' => $result['Result']['merchant_id']]);
            $ret['api_token'] = $result['Result']['api_token'];
            $ret['merchant_id'] = $result['Result']['merchant_id'];

            return $ret;
        } else {
            return $apitoken;
        }
    }

    public function getUserToken($result, $userid)
    {
        //判断当前用户的member_token为空
        $user_token = M('User')->where(['id' => $userid])->field('member_token')->find();

        if (empty($user_token['member_token'])) {
            $data = $this->tokenGet($result, userid());

            M('User')->where(['id' => $userid])->save(['member_token' => $data['Result']['member_token']]);
            $ret['member_token'] = $data['Result']['member_token'];
            $ret['Status'] = $data['Status'];

            return $ret;
        } else {
            return $user_token;
        }
    }

    public function memberToken()
    {
        $result = $this->getApiToken();

        $usertoken = $this->getUserToken($result, userid());

        $verify = $this->vitiy_token($usertoken['member_token']);

        if ($verify['Status'] == 'Failed' || empty($verify)) {
            $tokenGet = $this->tokenGet($result, userid());
            //var_dump($tokenGet); exit;
            //更新下会员token
            if (M('User')->where(['id' => userid()])->save(['member_token' => $tokenGet['Result']['member_token']])) {
                return $tokenGet['Result']['member_token'];
            } else {
                $info['code'] = '4006';
                $info['msg'] = '由于当前下单较多，系统处理繁忙请您重新下单!';
                $info['status'] = 'error';

                return $this->ajaxReturn($info);
            };
        } else {
            return $usertoken['member_token'];
        }
    }

    //sSGmKr2158orgWa6XLYlJcwB3BTyW2tIE6Irh38QHfBzxzxHpn8QuVeWglAd
    public function tokenGet($result, $userid)
    {
        $username = $this->yname($userid);
        $apitoken = $result['api_token'];

        $url = C('YJ_ADDR') . "/api/v3/member_gettoken";
        $data = ['username' => $username, 'api_token' => $apitoken];

        $ret = curl_post_http($url, $data);
        $result = json_decode($ret, TRUE);

        if (($result['Result'] == 'Invalid Merchant api token.') || ($result['Result'] == 'Invalid credentials passed,please login')) {
            //更新api_token
            $re = $this->yjToken();

            if (M('Config')->where(['id' => 1])->save(['api_token' => $re['Result']['api_token']])) {
                $info['code'] = '4906';
                $info['msg'] = '订单匹配不成功,请重新下单!';
                $info['status'] = 'error';
                $this->ajaxReturn($info);
            } else {
                $info['code'] = '4506';
                $info['msg'] = '订单发送失败,请您重新下单!';
                $info['status'] = 'error';
                $this->ajaxReturn($info);
            }
        } else {
            return $result;
        }
    }

    //验证member_token的 正确性
    public function vitiy_token($token)
    {
        $url = C('YJ_ADDR') . "/api/v3/member_verify_token";
        $data = ['member_token' => $token];

        $ret = curl_post_http($url, $data);

        $result = json_decode($ret, TRUE);

        return $result;
    }

    public function redirect_web($gotopage, $merchant_token)
    {
        $param = "&gotopage=" . $gotopage . "&member_token=" . $merchant_token . "";

        $url = C('YJ_ADDR') . "/api/v3/redirect_web?" . $param;
        $info['code'] = '0000';
        $info['msg'] = '下单链接';
        $info['data'] = $url;

        $this->ajaxReturn($info);
    }

    public function redirect_orderweb($gotopage, $merchant_token)
    {
        $param = "&gotopage=" . $gotopage . "&member_token=" . $merchant_token . "";

        $url = C('YJ_ADDR') . "/api/v3/redirect_web?" . $param;

        return $url;
    }

    public function orderstatus($value)
    {
        $ret = explode(' ', $value);
        $arr = [$ret[0], $ret[1]];
        $ret = implode("", $arr);

        return $ret;
    }

    public function ispay()
    {
        //3银行卡是否绑定

        $bank = M('User_bank')->where(['userid' => userid()])->count();
        if ($bank == 0 || !isset($bank)) {
            $info['code'] = '2018';
            $info['msg'] = '用户没有绑定银行卡';

            $this->ajaxReturn($info);
        }
    }
}