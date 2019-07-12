<?php

namespace Papi\Controller;

//个人中心
class CenterController extends CommonController
{
    //用户基本信息
    public function userinfo($jmcode)
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

            $user = M('User')->where(['id' => $userid])->find();

            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $data = [];
            $arr['userid'] = $user['id'];
            $arr['realname'] = $user['truename'];
            $arr['moble'] = $user['moble'];
            $arr['idcard'] = $user['idcard'];
            $arr['ident_status'] = $user['ident_status'];
            $data['info'] = $arr;
            $alipay = M('Alipay')->where(['userid' => $userid])->field('name,account,payimg,addtime')->find();
            if ($alipay) {
                $data['alipay'] = $alipay;
            }
            $weixin = M('Weixin')->where(['userid' => $userid])->field('name,waccount,wximg,addtime')->find();
            if ($weixin) {
                $data['weixin'] = $weixin;
            }
            $bank = M('UserBank')->where(['userid' => $userid])->field('name,bank,bankprov,bankcity,bankaddr,bankcard,addtime,status')->find();
            if ($bank) {
                $data['bank'] = $bank;
            }

            $info['code'] = 0;
            $info['msg'] = '成功';
            $info['data'] = $data;

            $this->ajaxReturn($info);
        } else {
            $this->apierror();
        }
    }

    //修改登录密码
    public function uppassword($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $moble = $result['data']['mobile'];
            $code = $result['data']['code'];
            $new_pass = $result['data']['new_pass'];
            $re_pass = $result['data']['re_pass'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($moble)) {
                $info['code'] = -1;
                $info['msg'] = '手机号码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($moble, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机号码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($code)) {
                $info['code'] = -1;
                $info['msg'] = '验证码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($code, 'number')) {
                $info['code'] = -1;
                $info['msg'] = '验证码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($new_pass)) {
                $info['code'] = -1;
                $info['msg'] = '密码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($new_pass, 'password')) {
                $info['code'] = -1;
                $info['msg'] = '密码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($re_pass)) {
                $info['code'] = -1;
                $info['msg'] = '确认密码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($new_pass != $re_pass) {
                $info['code'] = -1;
                $info['msg'] = '两次密码不一致';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($user['moble'] != $moble) {
                $info['code'] = -1;
                $info['msg'] = '请使用注册手机号码！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (md5($new_pass) == $user['paypassword']) {
                $info['code'] = -1;
                $info['msg'] = '登陆密码不能跟交易密码一样！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (md5($new_pass) == $user['password']) {
                $info['code'] = -1;
                $info['msg'] = '新密码不能跟原密码一样！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //获取验证码
            $sms = M('Usersms')->where(['phone' => $moble, 'type' => 5, 'status' => 0])->order('id desc')->find();
            if ($sms['endtime'] < time()) {
                $info['code'] = -1;
                $info['msg'] = '验证码已过期';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($sms['yzm'] != $code) {
                $info['code'] = -1;
                $info['msg'] = '验证码不正确';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //修改密码
            $rs = M('User')->where(['id' => $userid])->save(['password' => md5($new_pass)]);
            if ($rs) {
                $info['code'] = 0;
                $info['msg'] = '修改成功';
                $info['data'] = ['mobile' => $moble];

                $this->ajaxReturn($info);
            } else {
                $info['code'] = -1;
                $info['msg'] = '修改失败';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //修改交易密码
    public function uppaypass($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $moble = $result['data']['mobile'];
            $code = $result['data']['code'];
            $new_pass = $result['data']['new_pass'];
            $re_pass = $result['data']['re_pass'];
            $userid = $result['data']['userid'];

            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($moble)) {
                $info['code'] = -1;
                $info['msg'] = '手机号码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($moble, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机号码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($code)) {
                $info['code'] = -1;
                $info['msg'] = '验证码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($code, 'number')) {
                $info['code'] = -1;
                $info['msg'] = '验证码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($new_pass)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($new_pass, 'paypassword')) {
                $info['code'] = -1;
                $info['msg'] = '交易密码格式错误';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($re_pass)) {
                $info['code'] = -1;
                $info['msg'] = '确认交易密码不能为空';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($new_pass != $re_pass) {
                $info['code'] = -1;
                $info['msg'] = '两次交易密码不一致';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($user['moble'] != $moble) {
                $info['code'] = -1;
                $info['msg'] = '请使用注册手机号码！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (md5($new_pass) == $user['paypassword']) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不能跟登陆密码一样！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (md5($new_pass) == $user['password']) {
                $info['code'] = -1;
                $info['msg'] = '新密码不能跟原密码一样！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //获取验证码
            $sms = M('Usersms')->where(['phone' => $moble, 'type' => 6, 'status' => 0])->order('id desc')->find();
            if ($sms['endtime'] < time()) {
                $info['code'] = -1;
                $info['msg'] = '验证码已过期';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if ($sms['yzm'] != $code) {
                $info['code'] = -1;
                $info['msg'] = '验证码不正确';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //修改密码
            $rs = M('User')->where(['id' => $userid])->save(['paypassword' => md5($new_pass)]);
            if ($rs) {
                $info['code'] = 0;
                $info['msg'] = '修改成功';
                $info['data'] = ['mobile' => $moble];
                $this->ajaxReturn($info);
            } else {
                $info['code'] = -1;
                $info['msg'] = '修改失败';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //保存微信
    public function bindweixin($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $name = $result['data']['name'];
            $waccount = $result['data']['waccount'];
            $wximg = $result['data']['wximg'];
            $userid = $result['data']['userid'];
            $type = $result['data']['type'] ? $result['data']['type'] : 1;

            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($name)) {
                $info['msg'] = '微信昵称不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($wximg)) {
                $info['msg'] = '二维码图片不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($name, 'nc')) {
                $info['msg'] = '微信昵称的格式不正确，请重新输入';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($waccount)) {
                $info['msg'] = '微信账号不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($waccount, 'wx')) {
                $info['msg'] = '微信账号格式错误！';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //编辑还是新增
            if ($type == 1) {
                $arr = ['name' => $name, 'waccount' => $waccount, 'wximg' => $wximg, 'userid' => $userid, 'addtime' => time()];
                $save = M('Weixin')->add($arr);

                if ($save) {
                    $info['msg'] = '微信绑定成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '微信绑定失败！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else if ($type == 2) {
                $arr = ['name' => $name, 'waccount' => $waccount, 'wximg' => $wximg];
                $save = M('Weixin')->where(['userid' => $userid])->save($arr);

                if ($save) {
                    $info['msg'] = '微信信息修改成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '微信信息无更改！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else {
                $info['code'] = -1;
                $info['msg'] = '参数错误！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //绑定支付宝
    public function bindalipay($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $name = $result['data']['name'];
            $account = $result['data']['account'];
            $payimg = $result['data']['payimg'];
            $userid = $result['data']['userid'];
            $type = $result['data']['type'];

            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($name)) {
                $info['msg'] = '支付宝昵称不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($payimg)) {
                $info['msg'] = '二维码图片不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            /*if (!check($name, 'nc')) {
                $info['msg'] = '支付宝昵称格式错误,请重新输入！';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }*/
            if (empty($account)) {
                $info['msg'] = '支付宝账号不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!in_array($type, [1, 2])) {
                $info['msg'] = '类型格式错误';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (check($account, 'email') || check($account, 'moble')) {
            } else {
                $info['msg'] = '支付宝账号格式错误';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            //编辑还是新增
            if ($type == 1) {
                $arr = ['name' => $name, 'account' => $account, 'payimg' => $payimg, 'userid' => $userid, 'additme' => time()];
                $save = M('Alipay')->add($arr);

                if ($save) {
                    $info['msg'] = '支付宝绑定成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '支付宝绑定失败！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else if ($type == 2) {
                $arr = ['name' => $name, 'account' => $account, 'payimg' => $payimg];
                $save = M('Alipay')->where(['userid' => $userid])->save($arr);

                if ($save) {
                    $info['msg'] = '支付宝修改成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '支付宝信息无更改！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else {
                $info['code'] = -1;
                $info['msg'] = '参数错误！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //绑定银行卡
    public function bindbank($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $name = $result['data']['name'];
            $bank = $result['data']['bank'];
            $bankaddr = $result['data']['bankaddr'];
            $bankcard = $result['data']['bankcard'];
            $userid = $result['data']['userid'];
            $type = $result['data']['type'] ? $result['data']['type'] : 1;

            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($name)) {
                $info['msg'] = '开户姓名不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($bank)) {
                $info['msg'] = '银行名称不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($bankaddr)) {
                $info['msg'] = '开户行地址不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($bankcard)) {
                $info['msg'] = '银行卡号不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (!check($bankcard, 'number')) {
                $info['msg'] = '银行卡号格式不正确！';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            if (!checkBank($bankcard)) {
                $info['msg'] = '银行卡号格式不正确！';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!M('UserBankType')->where(['title' => $bank])->find()) {
                $info['code'] = -1;
                $info['msg'] = '开户银行错误！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            //编辑还是新增
            if ($type == 1) {
                $arr = ['name' => $name, 'bank' => $bank, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'userid' => $userid, 'addtime' => time(), 'status' => 1];
                $save = M('UserBank')->add($arr);

                if ($save) {
                    $info['msg'] = '银行绑定保存成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '银行绑定保存失败！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else if ($type == 2) {
                $arr = ['name' => $name, 'bank' => $bank, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard];
                $save = M('UserBank')->where(['userid' => $userid])->save($arr);

                if ($save) {
                    $info['msg'] = '银行信息修改成功！';
                    $info['code'] = 0;
                    $info['data'] = ['userid' => $userid];
                    $this->ajaxReturn($info);
                } else {
                    $info['msg'] = '银行信息无更改！';
                    $info['code'] = -1;
                    $info['data'] = [];
                    $this->ajaxReturn($info);
                }
            } else {
                $info['code'] = -1;
                $info['msg'] = '参数错误！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //实名认证
    public function realauth($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $truename = $result['data']['truename'];
            $idcard = $result['data']['idcard'];
            $idcardimg1 = $result['data']['idcardimg1'];
            $idcardimg2 = $result['data']['idcardimg2'];
            $idcardimg3 = $result['data']['idcardimg3'];
            $userid = $result['data']['userid'];

            if (empty($truename)) {
                $info['msg'] = '真实姓名不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($idcard)) {
                $info['msg'] = '身份证号码不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (!check($idcard, 'idcard')) {
                $info['msg'] = '身份证号码格式错误';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($userid)) {
                $info['msg'] = '用户ID不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($idcardimg1)) {
                $info['msg'] = '身份证正面照片不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            if (empty($idcardimg2)) {
                $info['msg'] = '身份证反面照片不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
            /*if (empty($idcardimg3)) {
                $info['msg'] = '手持身份证照片不能为空';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }*/
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = [];
                $this->ajaxReturn($info);
            }

            $arr = ['truename' => $truename, 'idcard' => $idcard, 'idcardimg1' => $idcardimg1, 'idcardimg2' => $idcardimg2, 'idcardimg3' => $idcardimg3, 'ident_status' => 1];
            $save = M('User')->where(['id' => $userid])->save($arr);

            if ($save) {
                $info['msg'] = '保存成功！';
                $info['code'] = 0;
                $info['data'] = ['userid' => $userid];
                $this->ajaxReturn($info);
            } else {
                $info['msg'] = '保存失败！';
                $info['code'] = -1;
                $info['data'] = [];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }
}
