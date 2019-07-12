<?php

namespace Papi\Controller;

class LoginController extends CommonController
{
    /**
     * 登陆.
     * @author hxq
     * @param [type] $jmcode [description]
     * @return [type] [description]
     */
    public function submit($jmcode = NULL)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $username = $result['data']['username'];
            $password = $result['data']['password'];

            if (check($username, 'email')) {
                $user = M('User')->where(['email' => $username])->find();
                $remark = '通过邮箱登录';
            }
            if (!$user && check($username, 'moble')) {
                $user = M('User')->where(['moble' => $username])->find();
                $remark = '通过手机号登录';
            }
            if (!$user) {
                $user = M('User')->where(['username' => $username])->find();
                $remark = '通过用户名登录';
            }
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (!check($password, 'password')) {
                $info['code'] = -1;
                $info['msg'] = '登录密码格式错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($user['status'] != 1) {
                $info['code'] = -1;
                $info['msg'] = '你的账号已冻结请联系管理员！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($user['password'] != md5($password)) {
                $info['code'] = -1;
                $info['msg'] = '账号密码错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables btchanges_user write , btchanges_user_log write ');
            $rs = [];
            $rs[] = $mo->table('btchanges_user')->where(['id' => $user['id']])->setInc('logins', 1);
            $rs[] = $mo->table('btchanges_user_log')->add(['userid' => $user['id'], 'type' => 'APP登录', 'remark' => $remark, 'addtime' => time(), 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'status' => 1]);
            $token = md5Token($user['id']);
            $rs[] = $mo->table('btchanges_user')->where(['id' => $user['id']])->save(['token' => $token]);

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');

                $data['userid'] = $user['id'];
                $data['token'] = $token;

                $info['code'] = 0;
                $info['msg'] = '登录成功！';
                $info['data'] = $data;

                $this->ajaxReturn($info);
            } else {
                $mo->execute('rollback');
                $info['code'] = -1;
                $info['msg'] = '登录失败！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    /**
     * 注册.
     */
    public function reg($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $username = $result['data']['username'];
            $moble = $result['data']['mobile'];
            $password = $result['data']['password'];
            $invit = $result['data']['invite'];
            $paypassword = $result['data']['paypassword'];

            if (empty($username)) {
                $info['code'] = -1;
                $info['msg'] = '用户名不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($moble)) {
                $info['code'] = -1;
                $info['msg'] = '手机不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($password)) {
                $info['code'] = -1;
                $info['msg'] = '密码不能为空！';
                $info['data'] = ['info' => $info['msg']];

                $this->ajaxReturn($info);
            }
            if (empty($paypassword)) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不能为空！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if ($password == $paypassword) {
                $info['code'] = -1;
                $info['msg'] = '交易密码不能和登陆密码一样！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if (!check($username, 'username')) {
                $info['code'] = -1;
                $info['msg'] = '用户名格式错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if (!check($moble, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机号格式错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if (!check($password, 'password')) {
                $info['code'] = -1;
                $info['msg'] = '登录密码格式错误！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if (M('User')->where(['username' => $username])->find()) {
                $info['code'] = -1;
                $info['msg'] = '用户名已存在';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if (M('User')->where(['moble' => $moble])->find()) {
                $info['code'] = -1;
                $info['msg'] = '手机号已存在';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            $scode = M()->table('admin_users')->where(['sharecode' => $invit])->find();
            if (empty($scode)) {
                $info['code'] = -1;
                $info['msg'] = '邀请码不存在';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables btchanges_user write , btchanges_user_coin write ');
            $rs = [];
            $token = md5Token();
            $rs[] = $mo->table('btchanges_user')->add([
                'username' => $username,
                'password' => md5($password),
                'invit' => $invit,
                'idcard' => 0,
                'moble' => $moble,
                'mobletime' => time(),
                'truename' => 0,
                'paypassword' => md5($paypassword),
                'tpwdsetting' => 1,
                'invit_1' => 0,
                'invit_2' => 0,
                'invit_3' => 0,
                'addip' => get_client_ip(),
                'addr' => get_city_ip(),
                'addtime' => time(),
                'status' => 1,
                'token' => $token,
                'sharecode' => $invit,
            ]);
            $rs[] = $mo->table('btchanges_user_coin')->add(['userid' => $rs[0]]);

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');

                $data['userid'] = $rs[0];
                $data['token'] = $token;
                $info['code'] = 0;
                $info['msg'] = '注册成功！';
                $info['data'] = $data;

                $this->ajaxReturn($info);
            } else {
                $info['code'] = -1;
                $info['msg'] = '注册失败！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }

    //忘记密码
    public function forgetSave($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $moble = $result['data']['moble'];
            $code = $result['data']['code'];
            $password = $result['data']['password'];

            if (empty($moble)) {
                $info['code'] = -1;
                $info['msg'] = '手机号码不能为空';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($moble, 'moble')) {
                $info['code'] = -1;
                $info['msg'] = '手机号码格式错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($code)) {
                $info['code'] = -1;
                $info['msg'] = '验证码不能为空';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($code, 'number')) {
                $info['code'] = -1;
                $info['msg'] = '验证码格式错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (empty($password)) {
                $info['code'] = -1;
                $info['msg'] = '密码不能为空';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if (!check($password, 'password')) {
                $info['code'] = -1;
                $info['msg'] = '密码格式错误';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            $user = M('User')->where(['moble' => $moble])->find();
            if (!$user) {
                $info['code'] = -1;
                $info['msg'] = '用户不存在！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($user['password'] == md5(trim($password))) {
                $info['code'] = -1;
                $info['msg'] = '新密码和旧的一样！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            if ($user['paypassword'] == md5(trim($password))) {
                $info['code'] = -1;
                $info['msg'] = '新密码不能和交易密码相同！';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            //获取验证码
            $sms = M('Usersms')->where(['phone' => $moble, 'type' => 5, 'status' => 0])->order('id desc')->find();
            if ($sms['endtime'] < time()) {
                $info['code'] = -1;
                $info['msg'] = '验证码已过期';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
            if ($sms['yzm'] != $code) {
                $info['code'] = -1;
                $info['msg'] = '验证码不正确';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }

            $user['password'] = md5(trim($password));

            if (M('user')->save($user)) {
                $info['code'] = 0;
                $info['msg'] = '修改成功,前往登陆';
                $info['data'] = ['mobile' => $moble];

                $this->ajaxReturn($info);
            } else {
                $info['code'] = -1;
                $info['msg'] = '修改失败';
                $info['data'] = ['info' => $info['msg']];
                $this->ajaxReturn($info);
            }
        } else {
            $this->apierror();
        }
    }
}
