<?php

namespace Home\Controller;

class LoginController extends HomeController
{
    public function index()
    {
        $this->display();
    }

    public function register()
    {
        $this->display();
    }

    public function webreg()
    {
        $this->display();
    }

    //注册时给用户发送短信
    public function userSmS($phone)
    {
        //判断给当前手机号是否发送了验证码
        if (!check($phone, 'moble')) {
            $info['info'] = "手机号码格式错误！";
            $info['status'] = "2007";
            $this->ajaxReturn($info);
        }

        if (M('User')->where(['moble' => $phone])->find()) {
            $info['info'] = "手机号码已存在！";
            $info['status'] = "2008";
            $this->ajaxReturn($info);
        }

        $yzm = M('Usersms')->where(['phone' => $phone, 'type' => 1])->find();
        //如果已经发送了判断是否过了逾期时间,防止重复发送验证码
        if (!empty($yzm['yzm'])) {
            if (strtotime('now') < $yzm['endtime']) {
                $info['info'] = "验证码已经发送";
                $info['status'] = "2005";
                $this->ajaxReturn($info);
            }
            if (strtotime('now') > $yzm['endtime']) {
                $this->reSmS($phone);
            }
        }

        $vitiy = rand(100000, 999999);
        $content = "【GTE数字平台】尊敬的用户您好，欢迎您注册GTE平台您的验证码为:" . $vitiy . "，如非本人操作，请忽略";
        $result = sendSMS($phone, $content, 0, "");

        if ($result['Code'] == 0) {
            if (M('Usersms')->add(['phone' => $phone, 'yzm' => $vitiy, 'type' => 1, 'endtime' => strtotime('now') + 60, 'status' => 0])) {
                $info['info'] = "短信发送成功";
                $info['status'] = "0000";
                $this->ajaxReturn($info);
            }
        } else {
            $info['info'] = "短信发送失败";
            $info['status'] = "2001";
            $this->ajaxReturn($info);
        }
    }

    //重新发送短信验证码
    public function reSmS($phone)
    {
        $vitiy = rand(100000, 999999);
        $content = "【GTE数字平台】尊敬的用户您好，欢迎您注册GTE平台您的验证码为:" . $vitiy . "，如非本人操作，请忽略";
        $result = sendSMS($phone, $content, 0, "");
        if ($result['Code'] == 0) {
            if (M('Usersms')->where(['phone' => $phone])->save(['yzm' => $vitiy, 'endtime' => strtotime('now') + 60])) {
                $info['info'] = "短信发送成功";
                $info['status'] = "0000";
                $this->ajaxReturn($info);
            }
        } else {
            $info['info'] = "短信发送失败";
            $info['status'] = "2001";
            $this->ajaxReturn($info);
        }
    }

    public function upregister($username, $password, $phone, $verify, $sharecode = NULL)
    {
        $this->error('演示站注册未开放');
        exit;

        $yzm = M('Usersms')->where(['phone' => $phone, 'type' => 1])->find();
        $scode = M()->table('admin_users')->where(array('sharecode'=>$sharecode))->find();

        if ($verify != $yzm['yzm']) {
            $this->error('手机验证码错误!');
        }

        if (strtotime('now') > $yzm['endtime']) {
            $this->error('验证码已过期!');
        }

        if (!check($phone, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (M('User')->where(['moble' => $phone])->find()) {
            $this->error('手机号码已存在！');
        }

        if (!check($username, 'username')) {
            $this->error('用户名格式错误！');
        }

        if (!check($password, 'password')) {
            $this->error('登录密码格式错误！');
        }

        if (empty($scode)) {
            $this->error('邀请码不存在,请您重新输入!');
        }

        if (M('User')->where(['username' => $username])->find()) {
            $this->error('用户名已存在');
        }



        $mo =M();
        $mo->execute('set autocommit=0');
        $mo->execute('lock tables btchanges_user write , btchanges_user_coin write, btchanges_user_qianbao write ');
        $rs = [];
        $rs[] = $mo->table('btchanges_user')->add([
            'username' => $username,
            'password' => md5($password),
            'moble' => $phone,
            'mobletime' => time(),
            'sharecode' => $sharecode,
            'tpwdsetting' => 1,
            'invit_1' => 0,
            'invit_2' => 0,
            'invit_3' => 0,
            'addip' => get_client_ip(),
            'addr' => get_city_ip(),
            'addtime' => time(),
            'status' => 1,
            'token' => md5Token()
        ]);
        $rs[] = $mo->table('btchanges_user_coin')->add(['userid' => $rs[0]]);

        if (check_arr($rs)) {
            $mo->execute('commit');
            $mo->execute('unlock tables');
            session('reguserId', $rs[0]);
            $this->success('注册成功！');
        } else {
            $mo->execute('rollback');
            $this->error('注册失败！');
        }
    }

    public function register2()
    {
        $this->display();
    }

    public function upregister2($paypassword, $repaypassword)
    {
        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if ($paypassword != $repaypassword) {
            $this->error('确认密码错误！');
        }

        if (!session('reguserId')) {
            $this->error('非法访问！');
        }

        if (M('User')->where(['id' => session('reguserId'), 'password' => md5($paypassword)])->find()) {
            $this->error('交易密码不能和登录密码一样！');
        }

        if (M('User')->where(['id' => session('reguserId')])->save(['paypassword' => md5($paypassword)])) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
    }

    public function register3()
    {
        $this->display();
    }

    public function upregister3($truename, $idcard)
    {
        $result = auth_idcard($idcard, $truename);
        if ($result['error_code'] != 0) {
            $this->error($result['reason']);
        }

        if (!check($truename, 'truename')) {
            $this->error('真实姓名格式错误！');
        }

        if (!check($idcard, 'idcard')) {
            $this->error('身份证号格式错误！');
        }

        if (!session('reguserId')) {
            $this->error('非法访问！');
        }

        if (M('User')->where(['id' => session('reguserId')])->save(['truename' => $truename, 'idcard' => $idcard])) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
    }

    public function register4()
    {
        $user = M('User')->where(['id' => session('reguserId')])->find();
        session('userId', $user['id']);
        session('userName', $user['username']);
        $this->assign('user', $user);
        $this->display();
    }

    public function chkUser($username)
    {
        if (!check($username, 'username')) {
            $this->error('用户名格式错误！');
        }

        if (M('User')->where(['username' => $username])->find()) {
            $this->error('用户名已存在');
        }

        $this->success('');
    }

    public function submit($username, $password, $verify = NULL)
    {
        if (!check_verify(strtoupper($verify))) {
            $this->error('图形验证码错误!');
        }
        if ($username != 'test') {
            $this->error('用户名不存在');
        }
        if ($password != '123456') {
            $this->error('密码错误');
        }
        session('userId', 1);
        session('userName', 'test');
        $info['info'] = '登录成功！';
        $info['status'] = 1;
        $info['url'] = '/Finance/index';

        $this->ajaxReturn($info);

        //TODO

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
            $this->error('用户不存在！');
        }
        if (!check($password, 'password')) {
            $this->error('登录密码格式错误！');
        }
        if (md5($password) != $user['password']) {
            $this->error('登录密码错误！');
        }
        if ($user['status'] != 1) {
            $this->error('你的账号已冻结请联系管理员！');
        }

        $mo = M();
        //$mo->execute('set autocommit=0');
        //$mo->execute('lock tables btchanges_user write , btchanges_user_log write ');
        $rs = [];
        $rs[] = $mo->table('btchanges_user')->where(['id' => $user['id']])->setInc('logins', 1);
        $rs[] = $mo->table('btchanges_user_log')->add(['userid' => $user['id'], 'type' => '登录', 'remark' => $remark, 'addtime' => time(), 'addip' => get_client_ip(), 'addr' => get_city_ip(), 'status' => 1]);

        if (check_arr($rs)) {
            //$mo->execute('commit');
            //$mo->execute('unlock tables');

            if (!$user['invit']) {
                for (; TRUE;) {
                    $tradeno = tradenoa();

                    if (!M('User')->where(['invit' => $tradeno])->find()) {
                        break;
                    }
                }

                M('User')->where(['id' => $user['id']])->setField('invit', $tradeno);
            }

            session('userId', $user['id']);
            session('userName', $user['username']);

            if (!$user['paypassword']) {
                session('regpaypassword', $rs[0]);
                session('reguserId', $user['id']);
            }

            if (!$user['truename']) {
                session('regtruename', $rs[0]);
                session('reguserId', $user['id']);
            }

            $ident_status = M('User')->where(['id' => userid()])->getField('ident_status');

            $info['info'] = '登录成功！';
            $info['status'] = 1;
            if ($ident_status != 2) {
                $info['url'] = '/User/nameauth.html';
            } else {
                $info['url'] = '/Finance/index';
            }

            $this->ajaxReturn($info);
        } else {
            //$mo->execute('rollback');
            $this->error('登录失败！');
        }
    }

    public function loginout()
    {
        session(NULL);
        redirect('/');
    }

    public function findpwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check_verify(strtoupper($input['verify']))) {
                $this->error('图形验证码错误!');
            }

            if (!check($input['username'], 'username')) {
                $this->error('用户名格式错误！');
            }

            if (!check($input['moble'], 'moble')) {
                $this->error('手机号码格式错误！');
            }

            if ($input['moble_verify'] != session('findpwd_verify')) {
                $this->error('短信验证码错误！');
            }

            $yzm = M('Usersms')->where(['phone' => $input['moble'], 'type' => 5])->order('id desc')->find();

            if (count($yzm) == 0) {
                $this->error('请获取验证码');
            }

            if ($yzm['endtime'] < strtotime('now')) {
                $this->error('验证码已过期!');
            }

            $user = M('User')->where(['username' => $input['username']])->find();

            if (!$user) {
                $this->error('用户名不存在！');
            }

            if ($user['password'] == md5($input['password'])) {
                $this->error('新密码不能与旧密码相同');
            }

            if ($user['moble'] != $input['moble']) {
                $this->error('用户名或手机号码错误！');
            }

            if (!check($input['password'], 'password')) {
                $this->error('新登录密码格式错误！');
            }

            if ($input['password'] != $input['repassword']) {
                $this->error('确认密码错误！');
            }

            $rs = M('User')->where(['id' => $user['id']])->save(['password' => md5($input['password'])]);

            if ($rs) {
                $this->success('修改成功');
            } else {
                $this->error('修改失败');
            }
        } else {
            $this->display();
        }
    }

    public function findpaypwd()
    {
        if (IS_POST) {
            $input = I('post.');

            if (!check($input['username'], 'username')) {
                $this->error('用户名格式错误！');
            }

            if (!check($input['moble'], 'moble')) {
                $this->error('手机号码格式错误！');
            }

            if (!check($input['moble_verify'], 'd')) {
                $this->error('短信验证码格式错误！');
            }

            if ($input['moble_verify'] != session('findpaypwd_verify')) {
                $this->error('短信验证码错误！');
            }

            $user = M('User')->where(['username' => $input['username']])->find();

            if (!$user) {
                $this->error('用户名不存在！');
            }

            if ($user['moble'] != $input['moble']) {
                $this->error('用户名或手机号码错误！');
            }

            if (!check($input['password'], 'password')) {
                $this->error('新交易密码格式错误！');
            }

            if ($input['password'] != $input['repassword']) {
                $this->error('确认密码错误！');
            }

            $mo = M();
            $mo->execute('set autocommit=0');
            $mo->execute('lock tables btchanges_user write , btchanges_user_log write ');
            $rs = [];
            $rs[] = $mo->table('btchanges_user')->where(['id' => $user['id']])->save(['paypassword' => md5($input['password'])]);

            if (check_arr($rs)) {
                $mo->execute('commit');
                $mo->execute('unlock tables');
                $this->success('修改成功');
            } else {
                $mo->execute('rollback');
                $this->error('修改失败' . $mo->table('btchanges_user')->getLastSql());
            }
        } else {
            $this->display();
        }
    }
}
