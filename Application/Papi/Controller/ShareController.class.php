<?php

namespace Papi\Controller;

//通用方法
class ShareController extends CommonController
{
    //图片上传(base64)
    public function uploadimg($file)
    {
        header('Content-type: text/json; charset=UTF-8');

        if (empty($file)) {
            $info['code'] = -1;
            $info['msg'] = '文件不能为空！';
            $info['data'] = ['info' => $info['msg']];

            $this->ajaxReturn($info);
        }

        $new_file = './Upload/pay/' . uniqid() . '.png';

        if (file_put_contents($new_file, base64_decode($file))) {
            $info['code'] = 0;
            $info['msg'] = '上传成功！';
            $info['data'] = ['path' => trim($new_file, '.')];

            $this->ajaxReturn($info);
        } else {
            $info['code'] = -1;
            $info['msg'] = '文件保存失败';
            $info['data'] = ['info' => $info['msg']];

            $this->ajaxReturn($info);
        }
    }

    //文件上传
    public function uploadimgfile()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = ['jpg', 'gif', 'png', 'jpeg'];
        $upload->rootPath = './Upload/pay/';
        $upload->autoSub = FALSE;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'] . $v['savename'];
            $info['code'] = 0;
            $info['msg'] = '上传成功！';
            $info['data'] = ['path' => $path];

            $this->ajaxReturn($info);
        }
    }

    //银行列表
    public function banklist()
    {
        $banks = M('UserBankType')->where(['status' => 1])->field('name,title,img')->order('id desc')->select();

        foreach ($banks as &$v) {
            $v['img'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/bank/' . $v['img'];
        }

        $info['code'] = 0;
        $info['msg'] = '操作成功';
        $info['data'] = $banks;

        $this->ajaxReturn($info);
    }

    //发送验证码
    public function sendMoble($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $moble = $result['data']['mobile'];
            $type = $result['data']['type'];

            if (empty($moble)) {
                $info['code'] = -1;
                $info['msg'] = '手机号码不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($type)) {
                $info['code'] = -1;
                $info['msg'] = '类型不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($moble, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机格式错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            //发送验证码
            $vitiy = rand(100000, 999999);

            if ($type == 1) {
                $content = '【GTE数字平台】尊敬的用户您好，欢迎您注册GTE平台您的验证码为:' . $vitiy . '，如非本人操作，请忽略';
            } else if (in_array($type, [5, 6])) {
                $content = "【GTE数字平台】您的验证码为：{$vitiy}，如非本人操作，请忽略。";
            } else {
                $info['code'] = -1;
                $info['msg'] = 'type值错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            $result = sendSMS($moble, $content, 0, '');

            if ($result['Code'] == 0) {
                if (M('Usersms')->add(['phone' => $moble, 'yzm' => $vitiy, 'type' => $type, 'endtime' => strtotime('now') + 180, 'status' => 0])) {
                    $info['msg'] = '短信发送成功';
                    $info['code'] = 0;
                    $info['data'] = ['code' => $vitiy];

                    $this->ajaxReturn($info);
                }
            } else {
                $info['msg'] = $result['Message'];
                $info['code'] = -1;
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //检测验证码是否正确
    public function validCode($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $mobile = $result['data']['mobile'];
            $code = $result['data']['code'];
            $type = $result['data']['type'];
            $username = $result['data']['username'];

            if (empty($mobile)) {
                $info['code'] = -1;
                $info['msg'] = '手机号码不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($code)) {
                $info['code'] = -1;
                $info['msg'] = '验证码不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($type)) {
                $info['code'] = -1;
                $info['msg'] = '类型不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($mobile, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机格式错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($username, 'username')) {
                $info['code'] = -1;
                $info['msg'] = '用户名格式错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            //注册的时候
            if ($type == 1) {
                if (M('User')->where(['moble' => $mobile])->find()) {
                    $info['code'] = -1;
                    $info['msg'] = '手机已注册！';
                    $info['data'] = ['info' => $info['msg']];
                    $this->ajaxReturn($info);
                }

                if (M('User')->where(['username' => $username])->find()) {
                    $info['code'] = -1;
                    $info['msg'] = '用户名已存在';
                    $info['data'] = ['info' => $info['msg']];
                    $this->ajaxReturn($info);
                }
            }
            //获取验证码
            $sms = M('Usersms')->where(['phone' => $mobile, 'type' => $type, 'status' => 0])->order('id desc')->find();
            if ($sms['endtime'] < time()) {
                $info['code'] = -1;
                $info['msg'] = '验证码已过期';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($sms['yzm'] == $code) {
                $info['msg'] = '验证码正确';
                $info['code'] = 0;
                $info['data'] = ['mobile' => $mobile];

                $this->ajaxReturn($info);
            } else {
                $info['code'] = -1;
                $info['msg'] = '验证码不正确';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    /**
     * 单独验证交易密码正确性.
     */
    public function valid_paypass($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $paypassword = $result['data']['paypassword'];
            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($paypassword)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($user['paypassword'] != md5($paypassword)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不正确！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            } else {
                $info['code'] = 0;
                $info['msg'] = '验证成功！';
                $info['data'] = ['paypassword' => $paypassword];

                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //用户当前资产
    public function current_assets($jmcode = NULL)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $userid = $result['data']['userid'];
            $market = $result['data']['market'];

            if (empty($userid)) {
                $info['code'] = -1;
                $info['msg'] = '用户ID不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($market)) {
                $info['code'] = -1;
                $info['msg'] = '虚拟币名称不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            $userCoin = M('UserCoin')->where(['userid' => $userid])->find();

            $xnb = explode('_', $market)[0];

            $data['usdt'] = (string)$userCoin['usdt']; //可用usdt
            $data['usdtd'] = (string)$userCoin['usdtd']; //冻结usdt
            $data['available'] = (string)$userCoin[$xnb]; //可用虚拟币
            $data['freeze'] = (string)$userCoin[$xnb . 'd']; //冻结虚拟币

            $data['all'] = D('UserCoin')->getUserSummaryProperty($userid); //用户总资产

            //$data['volume'] = D('Market')->getVolumeByMarket($market.'_usdt'); //交易量

            $info['code'] = 0;
            $info['msg'] = '查询成功！';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }
}
