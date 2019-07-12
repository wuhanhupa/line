<?php

namespace Papi\Controller;

class CommonController extends \Think\Controller
{
    public function restscode($jmcode)
    {
        if (empty($jmcode)) {
            $info['msg'] = '加密字符串不存在！无法通过';

            $this->ajaxReturn($info);
        }
        $pubKey = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Public/rsa_public_key.pem';
        $priKey = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Public/private_key.pem';
        //手动加載rsa类
        $rsa = new \Org\Util\Rsa($pubKey, $priKey);
        //$ex = json_encode($data);
        //加密
        //$ret_e = $rsa->encrypt($ex);
        //解密 $ret_e $data['jmcode']
        $ret_d = $rsa->decrypt($jmcode);
        if (!$ret_d) {
            $result['result'] = $ret_d;
            return $result;
        }
        $a = $ret_d;
        //签名
        $x = $rsa->sign($a);
        //验证
        $y = $rsa->verify($a, $x);

        $result['result'] = $y;
        $result['data'] = json_decode($ret_d, true);

        return $result;
    }

    public function rests($jmcode){

        if (empty($jmcode)) {
            $info['msg'] = '加密字符串不存在！无法通过';

            $this->ajaxReturn($info);
        }
        $rsa = new \Org\Util\Aes();
        
        $ret = $rsa->aes128cbcHexDecrypt($jmcode);
        //$ret = $rsa->aes128cbcDecrypt($jmcode);
        $ret =  preg_replace('/[\x00-\x1F\x80-\x9F]/u', '', trim($ret));
        
        if (!$ret) {
            $result['result'] = $ret;
            return $result;
        }
        $result['result'] = true;
        $result['data'] = json_decode($ret,true);
        return $result;
        
    }


    public function apisuccess($code, $msg, $data)
    {
        $info['code'] = $code;
        $info['msg'] = $msg;
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }

    public function apierror()
    {
        $info['code'] = '4000';
        $info['msg'] = '加密验证不通过';
        $info['data'] = '';

        $this->ajaxReturn($info);
    }

    //测试调试获取加密数据
    public function endata($data)
    {
        $pubKey = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Public/rsa_public_key.pem';
        $priKey = $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/Public/private_key.pem';
        //手动加載rsa类
        $rsa = new \Org\Util\Rsa($pubKey, $priKey);
        $ex = json_encode($data);
        //加密
        $ret_e = $rsa->encrypt($ex);

        return $ret_e;
    }

    public function userid($type = 0)
    {
        if (!$_SERVER['HTTP_ID'] || !$_SERVER['HTTP_TOKEN']) {
            if ($uid = userid()) {
                return $uid;
            }

            if (!$type) {
                $this->ajaxShow('请登录', -99);
            } else {
                return null;
            }
        }

        $user_id = intval(trim($_SERVER['HTTP_ID']));

        if (S('APP_AUTH_ID_'.$user_id) == trim($_SERVER['HTTP_TOKEN'])) {
            return $user_id;
        } else {
            if ($uid = userid()) {
                return $uid;
            }

            if ($res = M('User')->where(array('id' => $user_id))->field('token')->find()) {
                if ($res['token'] == trim($_SERVER['HTTP_TOKEN'])) {
                    S('APP_AUTH_ID_'.$user_id, $res['token']);

                    return $user_id;
                }
            }

            if (!$type) {
                $this->ajaxShow('请登录', -99);
            } else {
                return null;
            }
        }
    }

    public function mankRand($min = 1, $max = 100, $n = 2)
    {
        $int_n = rand($min, $max);
        $float_n = rand(0, 9) / pow(10, $n);

        return $int_n + $float_n;
    }

    public function doNums($arr, $k = 'width')
    {
        if (!is_array($arr)) {
            return false;
        }

        $nums = $sums = 0;

        foreach ($arr as $val) {
            $sums += $val[$k];
            ++$nums;
        }

        $pres = (2 * $sums) / $nums;

        foreach ($arr as $key => $val) {
            $arr[$key][$k] = ($pres < $val[$k] ? '100' : intval(($val[$k] * 100) / $pres));
        }

        return $arr;
    }

    public function dataHash($var)
    {
        if (!is_array($var)) {
            return md5($var);
        } else {
            return md5(serialize($var));
        }
    }

    //连接redis
    public function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
            $redis->auth(C('REDIS_PWD')); //链接密码
            $redis->select(C('REDIS_DB'));

        return $redis;
    }

    public function pconnectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
            $redis->auth(C('REDIS_PWD')); //链接密码
            $redis->select(C('REDIS_DB'));

        return $redis;
    }

    //获取当前的币种代码
    public function getnumber($market)
    {
        $number = M('Bz')->where(array('market' => $market))->getField('number');

        return $number;
    }

    //获取当前的币种名稱.
    public function getmarket($number)
    {
        $market = M('Bz')->where(array('number' => $number))->getField('market');

        return $market;
    }

    //进入消息队列
    public function redispush($type, $userid, $market, $status = '', $value = '', $hash = '')
    {
        $redis = $this->connectRedis();
        switch ($type) {
            case 1:
                $user['account'] = (int) $userid;
                $user['enum'] = $type;
                $user['id'] = uniqid();
                $user['symbol'] = (int) $market;
                ksort($user);
                $str = $this->getstr($user);
                $user['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
                $userlist = json_encode($user);
                $list = $redis->RPUSH('CAMSG', $userlist);
                break;
            case 3:
                $user['id'] = uniqid();
                $user['enum'] = $type;
                $user['account'] = (int) $userid;
                $user['symbol'] = (int) $market;
                $user['status'] = $status;
                ksort($user);
                $str = $this->getstr($user);
                $user['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
                $userlist = json_encode($user);
                $list = $redis->RPUSH('CAMSG', $userlist);
                break;
            case 5:
                $user['id'] = $hash;
                $user['enum'] = $type;
                $user['status'] = (int) $status;
                ksort($user);
                $str = $this->getstr($user);
                $user['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
                $userlist = json_encode($user);
                $list = $redis->RPUSH('CDMSG', $userlist);
                break;
            case 6:
                $user['id'] = uniqid();
                $user['enum'] = $type;
                $user['account'] = (int) $userid;
                $user['symbol'] = (int) $market;
                $user['value'] = $value;
                $user['address'] = $hash;
                ksort($user);
                $str = $this->getstr($user);
                $user['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
                $userlist = json_encode($user);
                $list = $redis->RPUSH('CWMSG', $userlist);
                break;
            case 8:
                $user['id'] = $hash;
                $user['enum'] = $type;
                $user['status'] = (int) $status;
                ksort($user);
                $str = $this->getstr($user);
                $user['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
                $userlist = json_encode($user);
                $list = $redis->RPUSH('CWMSG', $userlist);
                break;
            default:
                break;
        }
        if ($list) {
            return true;
        } else {
            return false;
        }
    }
}
