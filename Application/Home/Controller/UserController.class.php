<?php

namespace Home\Controller;

class UserController extends HomeController
{
    public function index()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $user = M('User')->where(['id' => userid()])->find();
        $this->assign('user', $user);
        $this->assign('prompt_text', D('Text')->get_content('user_index'));
        $this->display();
    }

    /**
     * [Sendsms 发送短息].
     */
    public function SendYun()
    {
        //调用
        $user = M('User')->where(['id' => userid()])->field('username')->find();

        $content = '【GTE数字平台】用户' . $user['username'] . '上传了身份信息，请及时处理';
        $result = sendSMS('18516760143', $content, 0, '');
        if ($result['Code'] == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //忘记交易密码
    public function userSmS($phone)
    {
        $user_phone = M('User')->where(['id' => userid()])->find();
        if (empty($user_phone)) {
            $info['info'] = '手机号码不存在';
            $info['status'] = '2207';
            $this->ajaxReturn($info);
        }

        //判断给当前手机号是否发送了验证码
        if (!check($phone, 'moble')) {
            $info['info'] = '手机号码格式错误！';
            $info['status'] = '2007';
            $this->ajaxReturn($info);
        }

        //6表示忘記密碼
        $yzm = M('Usersms')->where(['phone' => $phone, 'type' => 6])->order('id desc')->find();
        //如果已经发送了判断是否过了逾期时间,防止重复发送验证码
        if (!empty($yzm['yzm'])) {
            if (strtotime('now') < $yzm['endtime']) {
                $info['info'] = '验证码已经发送';
                $info['status'] = '2005';

                $this->ajaxReturn($info);
            }
            if (strtotime('now') > $yzm['endtime']) {
                $this->reSmS($phone);
            }
        }

        $vitiy = rand(11111, 99999);
        $content = "【GTE数字平台】尊敬的用户您好,您正在进行手机操作您的验证码是:" . $vitiy;
        $result = sendSMS($phone, $content, 0, '');
        //6表示忘記密碼

        if ($result['code'] == 0) {
            if (M('Usersms')->add(['phone' => $phone, 'yzm' => $vitiy, 'type' => 6, 'endtime' => strtotime('now') + 90, 'status' => 0])) {
                $info['info'] = '短信发送成功';
                $info['status'] = '0000';
                $this->ajaxReturn($info);
            }
        } else {
            $info['info'] = $result['Message'];
            $info['status'] = '2001';
            $this->ajaxReturn($info);
        }
    }

    //重新发送短信验证码
    public function reSmS($phone)
    {
        $vitiy = rand(100000, 999999);
        $content = "【GTE数字平台】尊敬的用户您好,您正在进行手机操作您的验证码是:" . $vitiy;
        $result = sendSMS($phone, $content, 0, '');
//        var_dump($result); exit;
        if ($result['Code'] == 0) {
            if (M('Usersms')->where(['phone' => $phone])->save(['yzm' => $vitiy, 'endtime' => strtotime('now') + 60])) {
                $info['info'] = '短信发送成功';
                $info['status'] = '0000';

                $this->ajaxReturn($info);
            }
        } else {
            $info['info'] = $result['Message'];
            $info['status'] = '2001';
            $this->ajaxReturn($info);
        }
    }

    //忘记交易密码
    public function fgpaypassword($newpaypassword, $phone, $verify)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        //判断手机号码是否注册的手机号码
        $user = M('User')->where(['id' => userid()])->find();
        if ($phone != $user['moble']) {
            $this->error('请使用注册的手机号码!');
        }

        $yzm = M('Usersms')->where(['phone' => $phone, 'type' => 6])->order('id desc')->find();

        if (strtotime('now') > $yzm['endtime']) {
            $this->error('验证码已过期!');
        }

        if ($verify != $yzm['yzm']) {
            $this->error('手机验证码错误!');
        }

        if (!check($newpaypassword, 'newpaypassword')) {
            $this->error('新交易密码格式错误！');
        }

        $user = M('User')->where(['id' => userid()])->find();

        if (md5($newpaypassword) == $user['password']) {
            $this->error('交易密码不能和登录密码相同！');
        }

        $rs = M('User')->where(['id' => userid()])->save(['paypassword' => md5($newpaypassword)]);

        if ($rs) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    public function nameauth()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $user = M('User')->where(['id' => userid()])->find();

        if ($user['idcard']) {
            $user['idcard'] = substr_replace($user['idcard'], '********', 6, 8);
        }

        $this->assign('user', $user);
        $this->assign('prompt_text', D('Text')->get_content('user_nameauth'));
        $this->display();
    }

    public function password()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $this->assign('prompt_text', D('Text')->get_content('user_password'));
        $this->display();
    }

    public function uppassword($oldpassword, $newpassword, $repassword)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($oldpassword, 'password')) {
            $this->error('旧登录密码格式错误！');
        }

        if (!check($newpassword, 'password')) {
            $this->error('新登录密码格式错误！');
        }

        if ($newpassword != $repassword) {
            $this->error('确认新密码错误！');
        }

        $password = M('User')->where(['id' => userid()])->getField('password');

        if (md5($oldpassword) != $password) {
            $this->error('旧登录密码错误！');
        }

        if ($newpassword == $oldpassword) {
            $this->error('新密码不能跟原密码相同！！');
        }

        $rs = M('User')->where(['id' => userid()])->save(['password' => md5($newpassword)]);

        if ($rs) {
            session(NULL);

            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    public function paypassword()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $this->assign('prompt_text', D('Text')->get_content('user_paypassword'));
        $this->display();
    }

    public function uppaypassword($oldpaypassword, $newpaypassword, $repaypassword)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($oldpaypassword, 'password')) {
            $this->error('旧交易密码格式错误！');
        }

        if (!check($newpaypassword, 'password')) {
            $this->error('新交易密码格式错误！');
        }

        if ($newpaypassword != $repaypassword) {
            $this->error('确认新密码错误！');
        }

        $user = M('User')->where(['id' => userid()])->find();

        if (md5($oldpaypassword) != $user['paypassword']) {
            $this->error('旧交易密码错误！');
        }

        if (md5($newpaypassword) == $user['password']) {
            $this->error('交易密码不能和登录密码相同！');
        }

        if ($newpaypassword == $oldpaypassword) {
            $this->error('新密码不能跟原密码相同！');
        }

        $rs = M('User')->where(['id' => userid()])->save(['paypassword' => md5($newpaypassword)]);

        if ($rs) {
            $this->success('修改成功');
        } else {
            $this->error('修改失败');
        }
    }

    public function ga()
    {
        if (empty($_POST)) {
            if (!userid()) {
                redirect(U('Login/index'));
            }

            $this->assign('prompt_text', D('Text')->get_content('user_ga'));
            $user = M('User')->where(['id' => userid()])->find();
            $is_ga = ($user['ga'] ? 1 : 0);
            $this->assign('is_ga', $is_ga);

            if (!$is_ga) {
                $ga = new \Common\Ext\GoogleAuthenticator();
                $secret = $ga->createSecret();
                session('secret', $secret);
                $this->assign('Asecret', $secret);
                $qrCodeUrl = $ga->getQRCodeGoogleUrl($user['username'] . '%20-%20' . $_SERVER['HTTP_HOST'], $secret);
                $this->assign('qrCodeUrl', $qrCodeUrl);
                $this->display();
            } else {
                $arr = explode('|', $user['ga']);
                $this->assign('ga_login', $arr[1]);
                $this->assign('ga_transfer', $arr[2]);
                $this->display();
            }
        } else {
            if (!userid()) {
                $this->error('登录已经失效,请重新登录!');
            }

            $delete = '';
            $gacode = trim(I('ga'));
            $type = trim(I('type'));
            $ga_login = (I('ga_login') == FALSE ? 0 : 1);
            $ga_transfer = (I('ga_transfer') == FALSE ? 0 : 1);

            if (!$gacode) {
                $this->error('请输入验证码!');
            }

            if ($type == 'add') {
                $secret = session('secret');

                if (!$secret) {
                    $this->error('验证码已经失效,请刷新网页!');
                }
            } else if (($type == 'update') || ($type == 'delete')) {
                $user = M('User')->where('id = ' . userid())->find();

                if (!$user['ga']) {
                    $this->error('还未设置谷歌验证码!');
                }

                $arr = explode('|', $user['ga']);
                $secret = $arr[0];
                $delete = ($type == 'delete' ? 1 : 0);
            } else {
                $this->error('操作未定义');
            }

            $ga = new \Common\Ext\GoogleAuthenticator();

            if ($ga->verifyCode($secret, $gacode, 1)) {
                $ga_val = ($delete == '' ? $secret . '|' . $ga_login . '|' . $ga_transfer : '');
                M('User')->save(['id' => userid(), 'ga' => $ga_val]);
                $this->success('操作成功');
            } else {
                $this->error('验证失败');
            }
        }
    }

    public function moble()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $user = M('User')->where(['id' => userid()])->find();

        if ($user['moble']) {
            $user['moble'] = substr_replace($user['moble'], '****', 3, 4);
        }

        $this->assign('user', $user);
        $this->assign('prompt_text', D('Text')->get_content('user_moble'));
        $this->display();
    }

    public function upmoble($moble, $moble_verify)
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }

        if (!check($moble, 'moble')) {
            $this->error('手机号码格式错误！');
        }

        if (!check($moble_verify, 'd')) {
            $this->error('短信验证码格式错误！');
        }

        if ($moble_verify != session('real_verify')) {
            $this->error('短信验证码错误！');
        }

        if (M('User')->where(['moble' => $moble])->find()) {
            $this->error('手机号码已存在！');
        }

        $rs = M('User')->where(['id' => userid()])->save(['moble' => $moble, 'addtime' => time()]);

        if ($rs) {
            $this->success('手机认证成功！');
        } else {
            $this->error('手机认证失败！');
        }
    }

    //提交用户的身份证图片供参考
    public function idcardup($idcardimg1, $idcardimg2, $idcardimg3 = NULL, $ident_status)
    {
        $data = ['idcardimg1' => $idcardimg1, 'idcardimg2' => $idcardimg2, 'idcardimg3' => $idcardimg3, 'ident_status' => $ident_status];

        $save = M('User')->where(['id' => userid()])->save($data);

        if ($save) {
            $info['info'] = '提交成功,等待审核';
            $info['status'] = 1;
            $this->SendYun();
            $this->ajaxReturn($info);
        } else {
            $info['info'] = '提交失败,请重新提交';
            $info['status'] = 2;
            $this->ajaxReturn($info);
        }
    }

    public function alipay()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $this->assign('prompt_text', D('Text')->get_content('user_alipay'));
        $user = M('Alipay')->where(['userid' => userid()])->find();
        $this->assign('user', $user);
        $this->display();
    }

    public function upalipay($alipay, $name, $payimg, $paypassword)
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }
        $alipay = string_remove_xss($alipay);
        $name = string_remove_xss($name);
        if (check($alipay, 'email') || check($alipay, 'moble')) {
            if (check($name, 'jsz')) {
                $this->error('支付宝昵称格式错误,请重新输入！');
            }
            if (!check($paypassword, 'password')) {
                $this->error('交易密码格式错误！');
            }
            $user = M('User')->where(['id' => userid()])->find();

            if (md5($paypassword) != $user['paypassword']) {
                $this->error('交易密码错误！');
            }

            if (M('Alipay')->add(['userid' => userid(), 'name' => $name, 'account' => $alipay, 'addtime' => time(), 'payimg' => $payimg])) {
                $info['info'] = '支付宝绑定成功！';
                $info['status'] = '0000';
                $this->ajaxReturn($info);
            } else {
                $info['info'] = '支付宝绑定失败！';
                $info['status'] = '3003';
                $this->ajaxReturn($info);
            }
        } else {
            $info['info'] = '支付宝账号格式错误,请重新输入！';
            $info['status'] = '90009';
            $this->ajaxReturn($info);
        }
    }

    public function payupdate()
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }
        $alipay = string_remove_xss($_POST['alipay']);
        $name = string_remove_xss($_POST['name']);
        if (check($alipay, 'email') || check($alipay, 'moble')) {
            if (!check($name, 'nc')) {
                $info['info'] = '支付宝昵称格式错误,请重新输入！';
                $info['status'] = '91009';
                $this->ajaxReturn($info);
            } else {
                $arr = ['name' => $name, 'account' => $alipay, 'payimg' => $_POST['payimg']];
                $save = M('Alipay')->where(['userid' => userid()])->save($arr);
                if ($save) {
                    $info['info'] = '支付宝绑定保存成功！';
                    $info['status'] = '0000';
                    $this->ajaxReturn($info);
                } else {
                    $info['info'] = '支付宝绑定保存失败！';
                    $info['status'] = '1001';
                    $this->ajaxReturn($info);
                }
            }
        } else {
            $info['info'] = '支付宝账号格式错误,请重新输入！';
            $info['status'] = '90009';
            $this->ajaxReturn($info);
        }
    }

    public function wxinfo()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $this->assign('prompt_text', D('Text')->get_content('user_alipay'));
        $Weixin = M('Weixin')->where(['userid' => userid()])->find();
        $this->assign('user', $Weixin);
        $this->display();
    }

    public function uwxin($wxaccount, $name, $payimg, $paypassword)
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }

        if (!check($wxaccount, 'wx')) {
            $this->error('微信账号的格式不正确，请重新输入');
        }

        if (!check($name, 'nc')) {
            $this->error('微信昵称的格式不正确，请重新输入');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        $user = M('User')->where(['id' => userid()])->find();

        if (md5($paypassword) != $user['paypassword']) {
            $this->error('交易密码错误！');
        }

        if (M('Weixin')->add(['userid' => userid(), 'name' => $name, 'waccount' => $wxaccount, 'addtime' => time(), 'wximg' => $payimg])) {
            $info['info'] = '微信绑定成功！';
            $info['status'] = '0000';
            $this->ajaxReturn($info);
        } else {
            $info['info'] = '微信绑定失败！';
            $info['status'] = '1001';
            $this->ajaxReturn($info);
        }
    }

    public function wxupdate()
    {
        if (!userid()) {
            $this->error('您没有登录请先登录！');
        }
        if (!check($_POST['name'], 'nc')) {
            $info['info'] = '微信昵称的格式不正确，请重新输入';
            $info['status'] = '7000';
            $this->ajaxReturn($info);
        }
        if (!check($_POST['wxaccount'], 'wx')) {
            $info['info'] = '微信账号格式错误！';
            $info['status'] = '7000';
            $this->ajaxReturn($info);
        }

        $arr = ['name' => $_POST['name'], 'waccount' => $_POST['wxaccount'], 'wximg' => $_POST['payimg']];
        $save = M('Weixin')->where(['userid' => userid()])->save($arr);
        if ($save) {
            $info['info'] = '微信修改绑定成功！';
            $info['status'] = '0000';
            $this->ajaxReturn($info);
        } else {
            $info['info'] = '微信修改绑定失败！';
            $info['status'] = '1001';
            $this->ajaxReturn($info);
        }
    }

    //图片上传返回地址
    public function imgupload()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = ['jpg', 'gif', 'png', 'jpeg'];
        $upload->rootPath = './Upload/pay/';
        $upload->autoSub = FALSE;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'] . $v['savename'];
            echo $path;
            exit();
        }
    }

    public function tpwdset()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $user = M('User')->where(['id' => userid()])->find();
        $this->assign('prompt_text', D('Text')->get_content('user_tpwdset'));
        $this->assign('user', $user);
        $this->display();
    }

    public function tpwdsetting()
    {
        if (userid()) {
            $tpwdsetting = M('User')->where(['id' => userid()])->getField('tpwdsetting');
            exit($tpwdsetting);
        }
    }

    public function uptpwdsetting($paypassword, $tpwdsetting)
    {
        if (!userid()) {
            $this->error('请先登录！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (($tpwdsetting != 1) && ($tpwdsetting != 2) && ($tpwdsetting != 3)) {
            $this->error('选项错误！' . $tpwdsetting);
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        $rs = M('User')->where(['id' => userid()])->save(['tpwdsetting' => $tpwdsetting]);

        if ($rs) {
            $this->success('成功！');
        } else {
            $this->error('失败！');
        }
    }

    public function delzfb($id)
    {
        if (M('Alipay')->where(['id' => $id])->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function delwxin($id)
    {
        if (M('Weixin')->where(['id' => $id])->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function forgetpwd()
    {
        $this->display();
    }

    /**
     * [bank 用户的银行卡管理].
     * @return [type] [description]
     */
    public function bank()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $UserBankType = M('UserBankType')->where(['status' => 1])->order('id desc')->select();
        $this->assign('UserBankType', $UserBankType);
        $truename = M('User')->where(['id' => userid()])->getField('truename');
        $this->assign('truename', $truename);
        $UserBank = M('UserBank')->where(['userid' => userid(), 'status' => 1])->order('id desc')->limit(1)->select();

        $this->assign('UserBank', $UserBank);
        $this->assign('prompt_text', D('Text')->get_content('user_bank'));
        $this->display();
    }

    public function getbank()
    {
        $UserBankType = M('UserBankType')->where(['status' => 1])->order('id desc')->select();

        $truename = M('User')->where(['id' => userid()])->getField('truename');

        $UserBank = M('UserBank')->where(['userid' => userid(), 'status' => 1])->order('id desc')->limit(1)->select();

        $this->ajaxReturn($UserBank);
    }

    public function upbank($type, $name, $bank, $bankprov, $bankcity, $bankaddr, $bankcard, $paypassword)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (empty($name)) {
            $this->error('开户姓名不能为空！');
        }

        if (!check($name, 'a')) {
            $this->error('开户姓名输入格式错误！');
        }

        if (!check($bank, 'a')) {
            $this->error('开户银行格式错误！');
        }

        if (!check($bankprov, 'c')) {
            $this->error('开户省市格式错误！');
        }

        if (!check($bankcity, 'c')) {
            $this->error('开户省市格式错误2！');
        }

        if (!check($bankaddr, 'a')) {
            $this->error('开户行地址格式错误！');
        }

        if (!check($bankcard, 'd')) {
            $this->error('银行账号格式错误！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserBankType')->where(['title' => $bank])->find()) {
            $this->error('开户银行错误！');
        }

        $userBank = M('UserBank')->where(['userid' => userid()])->select();

        if ($type == 1) {
            foreach ($userBank as $k => $v) {
                if ($v['name'] == $name) {
                    $this->error('请不要使用相同的备注名称！');
                }

                if ($v['bankcard'] == $bankcard) {
                    $this->error('银行卡号已存在！');
                }
            }
            if (M('UserBank')->add(['userid' => userid(), 'name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1])) {
                $this->success('添加成功！');
            } else {
                $this->error('添加失败！');
            }
        } else {
            if (M('UserBank')->where(['userid' => userid()])->save(['name' => $name, 'bank' => $bank, 'bankprov' => $bankprov, 'bankcity' => $bankcity, 'bankaddr' => $bankaddr, 'bankcard' => $bankcard, 'addtime' => time(), 'status' => 1])) {
                $this->success('保存成功！');
            } else {
                $this->error('保存失败！');
            }
        }
    }

    public function delbank($id, $paypassword)
    {
        $this->error('警告:非法操作！');
        die();
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserBank')->where(['userid' => userid(), 'id' => $id])->find()) {
            $this->error('非法访问！');
        } else if (M('UserBank')->where(['userid' => userid(), 'id' => $id])->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function qianbao($coin = NULL)
    {
        if (!userid()) {
            redirect('/login');
        }
        $Coin = M('Coin')->where(['status' => 1])->select();

        if (!$coin) {
            $coin = $Coin[0]['name'];
        }

        $this->assign('xnb', $coin);

        foreach ($Coin as $k => $v) {
            $coin_list[$v['name']] = $v;
        }
        $this->assign('coin_list', $coin_list);

        $number = M('Bz')->where(['market' => $coin])->getField('number');

        $userQianbaoList = M('UserQianbao')->where(['userid' => userid(), 'status' => 1, 'coinname' => $number])->order('id desc')->select();
        $this->assign('userQianbaoList', $userQianbaoList);
        $this->assign('prompt_text', D('Text')->get_content('user_qianbao'));
        $this->display();
    }

    public function upqianbao($coin, $name, $addr, $paypassword)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (empty($name)) {
            $this->error('钱包标示输入不能为空！');
        }

        if (!check($name, 'a')) {
            $this->error('钱包标示输入格式错误！');
        }

        if (empty($addr)) {
            $this->error('钱包地址不能为空！');
        }

        if (!check($addr, 'dw')) {
            $this->error('钱包地址格式错误！');
        }

        if (empty($paypassword)) {
            $this->error('交易密码不能为空！');
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('Coin')->where(['name' => $coin])->find()) {
            $this->error('币种错误！');
        }

        //验证钱包地址
        if (!getValidWayByCoinname($coin, $addr)) {
            $this->error($coin . '地址格式错误！');
        }

        $userQianbao = M('UserQianbao')->where(['userid' => userid(), 'coinname' => $coin])->select();

        foreach ($userQianbao as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('请不要使用相同的钱包标识！');
            }

            if ($v['addr'] == $addr) {
                $this->error('钱包地址已存在！');
            }
        }

        if (10 <= count($userQianbao)) {
            $this->error('每个人最多只能添加10个地址！');
        }

        $number = M('Bz')->where(['market' => $coin])->getField('number');

        if (M('UserQianbao')->add(['userid' => userid(), 'name' => $name, 'addr' => $addr, 'coinname' => $number, 'addtime' => time(), 'status' => 1])) {
            $this->success('添加成功！');
        } else {
            $this->error('添加失败！');
        }
    }

    public function delqianbao($id, $paypassword)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserQianbao')->where(['userid' => userid(), 'id' => $id])->find()) {
            $this->error('非法访问！');
        } else if (M('UserQianbao')->where(['userid' => userid(), 'id' => $id])->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function goods()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $userGoodsList = M('UserGoods')->where(['userid' => userid(), 'status' => 1])->order('id desc')->select();

        foreach ($userGoodsList as $k => $v) {
            $userGoodsList[$k]['moble'] = substr_replace($v['moble'], '****', 3, 4);
            $userGoodsList[$k]['idcard'] = substr_replace($v['idcard'], '********', 6, 8);
        }

        $this->assign('userGoodsList', $userGoodsList);
        $this->assign('prompt_text', D('Text')->get_content('user_goods'));
        $this->display();
    }

    public function upgoods($name, $truename, $idcard, $moble, $addr, $paypassword)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (!check($name, 'a')) {
            $this->error('备注名称格式错误！');
        }

        if (!check($truename, 'truename')) {
            $this->error('联系姓名格式错误！');
        }

        if (!check($idcard, 'idcard')) {
            $this->error('身份证号格式错误！');
        }

        if (!check($moble, 'moble')) {
            $this->error('联系电话格式错误！');
        }

        if (!check($addr, 'a')) {
            $this->error('联系地址格式错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        $userGoods = M('UserGoods')->where(['userid' => userid()])->select();

        foreach ($userGoods as $k => $v) {
            if ($v['name'] == $name) {
                $this->error('请不要使用相同的地址标识！');
            }
        }

        if (10 <= count($userGoods)) {
            $this->error('每个人最多只能添加10个地址！');
        }

        if (M('UserGoods')->add(['userid' => userid(), 'name' => $name, 'addr' => $addr, 'idcard' => $idcard, 'truename' => $truename, 'moble' => $moble, 'addtime' => time(), 'status' => 1])) {
            $this->success('添加成功！');
        } else {
            $this->error('添加失败！');
        }
    }

    public function delgoods($id, $paypassword)
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        if (!check($paypassword, 'password')) {
            $this->error('交易密码格式错误！');
        }

        if (!check($id, 'd')) {
            $this->error('参数错误！');
        }

        $user_paypassword = M('User')->where(['id' => userid()])->getField('paypassword');

        if (md5($paypassword) != $user_paypassword) {
            $this->error('交易密码错误！');
        }

        if (!M('UserGoods')->where(['userid' => userid(), 'id' => $id])->find()) {
            $this->error('非法访问！');
        } else if (M('UserGoods')->where(['userid' => userid(), 'id' => $id])->delete()) {
            $this->success('删除成功！');
        } else {
            $this->error('删除失败！');
        }
    }

    public function log()
    {
        if (!userid()) {
            redirect(U('Login/index'));
        }

        $where['status'] = ['egt', 0];
        $where['userid'] = userid();
        $Model = M('UserLog');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('prompt_text', D('Text')->get_content('user_log'));
        $this->display();
    }
}
