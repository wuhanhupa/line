<?php

namespace Home\Controller;

class HomeController extends \Think\Controller
{
    protected function _initialize()
    {
        defined('APP_DEMO') || define('APP_DEMO', 0);

        if (!session('userId')) {
            session('userId', 0);
        } elseif (CONTROLLER_NAME != 'Login') {
            $user = D('user')->where('id = '.session('userId'))->find();

            if (!$user['paypassword']) {
                redirect('/Login/register2');
            }

            if (!$user['truename']) {
                redirect('/Login/register3');
            }
        }

        if (userid()) {
            $userCoin_top = M('UserCoin')->where(array('userid' => userid()))->find();
            $userCoin_top['cny'] = round($userCoin_top['usdt'], 2);
            $userCoin_top['cnyd'] = round($userCoin_top['usdtd'], 2);
            $this->assign('userCoin_top', $userCoin_top);
        }

        $config = (APP_DEBUG ? null : S('home_config'));

        if (!$config) {
            $config = M('Config')->where(array('id' => 1))->find();
            S('home_config', $config);
        }

        C($config);

        $C = C();

        foreach ($C as $k => $v) {
            $C[strtolower($k)] = $v;
        }

        $this->assign('C', $C);

        if (!S('daohang_aa')) {
            $tables = M()->query('show tables');
            $tableMap = array();

            foreach ($tables as $table) {
                $tableMap[reset($table)] = 1;
            }

            S('daohang_aa', 1);
        }

        if (!S('daohang')) {
            $this->daohang = M('Daohang')->where(array('status' => 1))->order('sort asc')->select();
            S('daohang', $this->daohang);
        } else {
            $this->daohang = S('daohang');
        }

        $footerArticleType = (APP_DEBUG ? null : S('footer_indexArticleType'));

        if (!$footerArticleType) {
            $footerArticleType = M('ArticleType')->where(array('status' => 1, 'footer' => 1, 'shang' => ''))->order('sort asc ,id desc')->limit(3)->select();
            S('footer_indexArticleType', $footerArticleType);
        }

        $this->assign('footerArticleType', $footerArticleType);
        $footerArticle = (APP_DEBUG ? null : S('footer_indexArticle'));

        if (!$footerArticle) {
            foreach ($footerArticleType as $k => $v) {
                $footerArticle[$v['name']] = M('ArticleType')->where(array('shang' => $v['name'], 'footer' => 1, 'status' => 1))->order('id asc')->limit(4)->select();
            }

            S('footer_indexArticle', $footerArticle);
        }

        $this->assign('footerArticle', $footerArticle);

        //版本号
        $version = '1.0.2';
        $this->assign('randVersion', $version);
    }

    public function _empty()
    {
        send_http_status(404);
        $this->error();
        echo '模块不存在！';
        die();
    }

    //连接redis
    protected function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }

    protected function pconnectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }
}
