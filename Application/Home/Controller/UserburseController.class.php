<?php

namespace Home\Controller;

use Common\Design\Redis\Collection;
use Think\Page;

class UserburseController extends HomeController
{
    /**
     * Notice: 进入消息队列
     * author: hxq
     * @param        $type
     * @param        $userid
     * @param        $market
     * @param string $status
     * @param string $value
     * @param string $hash
     * @return bool
     */
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

    /**
     * 创建地址出队列.
     * @author hxq
     * @date   2018-09-13T14:17:07+080
     */
    public function redisput()
    {
        $redis = $this->pconnectRedis();
        //list类型出队操作
        while (true) {
            try {
                $pop = $redis->LPOP('SAMSG');

                if (!$pop) {
                    break;
                }
                //存入redis返回数据
                redislog($pop);
                $pop = json_decode($pop, true);
                //已经收到了返回的消息
                if ($pop['enum'] == 2) {
                    $this->redispush(3, $pop['account'], $pop['symbol'], true);
                }
                //根据用户的id在给用户添加钱包地址
                if (count($pop) == count($pop, 1)) {
                    if ($pop['account']) {
                        //查询用户是否有地址
                        $check = M('UserQianbao')->where(array('userid' => $pop['account'], 'coinname' => $pop['symbol'], 'type' => 1))->find();
                        if ($check) {
                            //break;
                        } else {
                            $add = M('User_qianbao')->add(array('userid' => $pop['account'], 'coinname' => $pop['symbol'], 'addr' => $pop['address'], 'addtime' => time(), 'type' => 1));

                            if ($add) {
                                //如果是当前用户
                                if (userid() == $pop['account']) {
                                    return $pop['address'];
                                }
                            }
                        }
                    }
                }
                //可能消息学会有多个
            } catch (Exception $e) {
                redislog($e->getMessage());
            }
            sleep(1);
        }
    }

    /**
     * Notice: 充值
     * author: hxq
     */
    public function mycz()
    {
        $market = $_GET['market'];

        $number = $this->getnumber($market);
        //强制使用eth地址
        if ($number > 0 && $number < 1000) {
            $numberOne = 1;
        } else {
            $numberOne = $number;
        }

        //1判断当前的用户对应的这个币种是否已经有地址如果有直接返回如果没有就生成
        $userAddr = M('User_qianbao')->where(array('userid' => userid(), 'coinname' => $numberOne, 'type' => 1))->getField('addr');

        if (empty($userAddr)) {
            //发送一次请求
            //$push = $this->redispush(1, userid(), $number); //请求生成对应的钱包地址写入消息队列
            $redis = new Collection();
            $push = $redis->redispush(1, userid(), $number);
            //获取出队消息
            if ($push) {
                $userAddr = $redis->createAddressHandle(userid(), $number);
            } else {
                //查看队列返回
                $this->error('网络不给力，请刷新再试');
            }
        }
        //查询当前币种的充值记录
        $where['userid'] = userid();
        $where['coinname'] = $number;
        $count = M('Mycz')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Mycz')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $prompt_text = '1. 往该地址充值完成，等待网络自动确认（12个确认）后系统自动到账，同一个地址可多次充值，不影响到账。<br/>
        2. 为了快速到账，充值时可以适当提高网络手续费。<br/>
        3. 部分充币可能会有延迟，请您谅解。
        ';
        $this->assign('market', strtoupper($market));
        $this->assign('list', $list);
        $this->assign('addr', $userAddr);
        $this->assign('prompt_text', $prompt_text);
        $this->assign('page', $show);
        $this->display();
    }

    /**
     * 获取用户充值消息(定时任务)
     */
    public function getczInfo()
    {
        $redis = new Collection();
        //获取比特币和以太坊父级代码
        $btc = M('Bz')->where(array('market' => 'btc'))->getField('number');
        $eth = M('Bz')->where(array('market' => 'eth'))->getField('number');
        //执行充值队列
        $redis->depositHandle($btc);

        $redis->depositHandle($eth);

        /*$redis = $this->pconnectRedis();
        do {
            try {
                $pop = $redis->LPOP('SSDMSG');
                if (!$pop) {
                    //echo "redis當中暫時沒有數據 \n";
                } else {
                    redislog($pop);
                    $pop = json_decode($pop, true);

                    //已经收到了返回的消息
                    if (count($pop) == count($pop, 1)) {
                        //判断hash是否已经记录，也就是这条充值记录已经接受并修改了用户对应币种余额
                        $find = M('Mycz')->where(array('tradeno' => $pop['txshash']))->find();
                        //没有记录hash代表新的充值记录
                        if (count($find) == 0) {
                            $market = $this->getmarket($pop['symbol']);
                            //如何获取的msg消息里面的id是5 那么给用户添加充值记录
                            $sql = 'insert into btchanges_mycz (userid,num,mum,type,tradeno,addtime,coinname,status) values('.
                            '\''.$pop['account'].'\', '
                            .'\''.$pop['value'].'\', '
                            .'\''.$pop['value'].'\', '
                            .'\''.'cz'.'\', '
                            .'\''.$pop['txshash'].'\','
                            .'\''.time().'\','
                            .'\''.$pop['symbol'].'\','
                            .'\''. 1 .'\')';
                            //echo $sql;
                            $m = M('Mycz');
                            $cz = $m->execute($sql);

                            if ($cz) {
                                echo $market;
                                //充值记录添加完成之后,给对应的币种添加1.先查询原来的值在进行加
                                $czb = M('User_coin')->where(array('userid' => $pop['account']))->getField($market);
                                $czb = bcadd($czb, $pop['value'], 8);
                                $property = M('User_coin')->where(array('userid' => $pop['account']))->save(array($market => $czb));
                                if ($property) {
                                    //发送消息告诉redis当前用户已经充值完成
                                    if ($this->redispush(5, $pop['account'], '', true, '', $pop['txshash'])) {
                                        echo "充值成功 \n";
                                    }
                                } else {
                                    echo "充值失敗 \n";
                                }
                            } else {
                                echo $cz;
                            }
                        } else {
                            echo 'error hash';
                        }
                    }
                }
                //可能消息学会有多个
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            sleep(1);
        } while ($redis->LSIZE('SSDMSG') > 0);*/
    }

    /**
     * 获取当前的币种代码
     * @param $market
     * @return mixed
     */
    public function getnumber($market)
    {
        $number = M('Bz')->where(array('market' => $market))->getField('number');

        return $number;
    }

    /**
     * 获取当前的币种名稱
     * @param $number
     * @return mixed
     */
    public function getmarket($number)
    {
        $market = M('Bz')->where(array('number' => $number))->getField('market');

        return $market;
    }

    /**
     * ajax检测是否支持充值
     */
    public function checkcoin()
    {
        //检测用户有没有绑定该币种的钱包地址
        $market = I('post.market');

        //币种充提支持过滤
        if (!checkCoin($market)) {
            $this->error(strtoupper($market).'正在跟区块对接中，暂不支持充值功能。');
        } else {
            $this->success();
        }
    }

    /**
     * ajax检测是否绑定钱包
     */
    public function checkwallet()
    {
        //检测用户有没有绑定该币种的钱包地址
        $market = I('post.market');
        $number = $this->getnumber($market);

        //币种充提支持过滤
        if (!checkCoin($market)) {
            $this->error(strtoupper($market).'正在跟区块对接中，暂不支持提现功能。');
        }

        //1判断当前的用户对应的这个币种是否已经有地址如果有直接返回如果没有就生成
        $userAddr = M('User_qianbao')->where(array('userid' => userid(), 'coinname' => $number, 'type' => 0))->getField('addr');

        if (empty($userAddr)) {
            $this->error('请先绑定钱包地址');
        } else {
            $this->success();
        }
    }

    /**
     * Notice: 用户請求提現
     * author: hxq
     */
    public function mytx()
    {
        //检测用户有没有绑定该币种的钱包地址
        $market = $_GET['market'];
        $number = $this->getnumber($market);

        //查询手续费比例
        $coin = M('Coin')->where(array('name' => $market))->find();
        $fee = $coin['zc_fee'];
        if ($fee == 0) {
            $this->error('暂不支持该币种提现！');
        }

        //1判断当前的用户对应的这个币种是否已经有地址如果有直接返回如果没有就生成
        $userAddr = M('User_qianbao')->where(array('userid' => userid(), 'coinname' => $number, 'type' => 0))->select();

        if (count($userAddr) == 0) {
            $this->error('请先绑定钱包地址');
        }

        //查询用户可用余额
        $where['userid'] = userid();
        $balance = M('UserCoin')->where($where)->getField($market);
        //查询最近提现记录
        $where['userid'] = userid();
        $where['coinname'] = $number;
        $count = M('Mytx')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();

        $list = M('Mytx')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        //查询币种提现区间
        $str = '4. '.strtoupper($market).'提现限额为'.$coin['zc_min'].'到'.$coin['zc_max'].'之间。';
        $string = '5. '.strtoupper($market).'单日提现最大限额为'.$coin['zc_zd'].'。';
        $prompt_text = '1. 转出虚拟币需要人工审核，提币申请后我们将在尽快处理。<br/>
        2. 由于网络的不确定性，提币通常需要 2～24 小时完成到账，部分转币可能会有延迟，请您谅解。<br/>
        3. 申请提币之前请您确认提币地址正确，交易一旦发送到区块链网络便不可撤回。
        <br/>'.$str.'<br/>'.$string;

        $this->assign('balance', $balance);
        $this->assign('market', strtoupper($market));
        $this->assign('addr', $userAddr);
        $this->assign('fee', $fee);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->assign('prompt_text', $prompt_text);
        $this->display();
    }

    /**
     * 保存提币申请.
     *
     * @author hxq
     * @date   2018-09-13T14:16:02+080
     */
    public function mytxadd()
    {
        $market = strtolower(I('post.market'));
        $num = I('post.num');
        $paypass = I('post.paypass');
        $addr = I('post.addr');

        if (empty($market)) {
            $this->error('交易对不能为空！');
        }
        if (empty($num)) {
            $this->error('数量不能为空！');
        }
        if (empty($paypass)) {
            $this->error('交易密码不能为空！');
        }
        if (empty($addr)) {
            $this->error('提现地址不能为空！');
        }
        if (!is_numeric($num)) {
            $this->error('数量格式不正确！');
        }
        if (!check($paypass, 'password')) {
            $this->error('交易密码格式错误！');
        }

        //限制重复强求
        if (S('request_time')) {
            $request = S('request_time');
            if ($request['userid'] == userid()) {
                if ((time() - $request['time']) < 1) {
                    $this->error('请谨慎提交！');
                }
            }
        }
        //验证余额是否足够
        $where['userid'] = userid();
        $balance = M('UserCoin')->where($where)->getField($market);
        if ($balance < $num) {
            $this->error('可用余额不足');
        }
        //验证交易密码是否正确
        $pass = M('User')->where(array('id' => userid()))->getField('paypassword');
        if ($pass != md5($paypass)) {
            $this->error('交易密码错误');
        }
        $number = $this->getnumber($market);

        try {
            //开启事物
            M()->startTrans();
            //第一步凍結貨幣當前貨幣數量
            //1.查詢原數量扣除需要提現的數量
            $ynum = M('UserCoin')->where(array('userid' => userid()))->getField($market);
            $xnum = bcsub($ynum, $num, 8);
            $resOne = M('UserCoin')->where(array('userid' => userid()))->save(array($market => $xnum));
            //2 凍結數量
            $marketd = $market.'d';
            $bznum = M('UserCoin')->where(array('userid' => userid()))->getField($marketd);
            $nums = bcadd($bznum, $num, 8);
            $resTwo = M('UserCoin')->where(array('userid' => userid()))->save(array($marketd => $nums));
            //3.修改當前用戶的幣種凍結的數量
            if ($resOne && $resTwo) {
                $mark = uniqid();
                //提现手续费
                $coin = M('Coin')->where(array('name' => $market))->find();
                $fee = $coin['zc_fee'];
                if ($fee >= $num) {
                    throw new \Exception('提现数量不够扣除手续费！');
                }
                //单次限额
                $zc_max = $coin['zc_max'];
                if ($num > $zc_max) {
                    throw new \Exception('超过单次转出限额！');
                }
                //单日限额
                $zc_zd = $coin['zc_zd'];
                //查询今日转出
                $start = strtotime(date('Y-m-d', time()));
                $end = strtotime(date('Y-m-d', strtotime('+1 day', time())));
                $total = M('Mytx')->where(array(
                    'userid' => userid(), 'coinname' => $number, 'status' => array('in', [0, 1]), 'addtime' => array('between', [$start, $end]),
                ))->sum('num');
                if (bcadd($total, $num, 8) > $zc_zd) {
                    throw new \Exception('超过单日转出限额！');
                }

                //实际到账额度
                $mum = bcsub($num, $fee, 8);
                //插入数据库
                $sql = 'insert into btchanges_mytx (userid,coinname,address,num,fee,mum,type,mark,addtime) values('.
                '\''.userid().'\', '.
                '\''.$number.'\', '
                .'\''.$addr.'\', '
                .'\''.$num.'\', '
                .'\''.$fee.'\', '
                .'\''.$mum.'\', '
                .'\''.'tx'.'\', '
                .'\''.$mark.'\', '
                .'\''.time().'\')';

                $m = M('Mytx');
                $cz = $m->execute($sql);

                if ($cz) {
                    //记录请求时间
                    S('request_time', array('userid' => userid(), 'time' => time()));

                    M()->commit();

                    $this->success('提现申请已提交，请等待工作人员处理。');
                } else {
                    throw new \Exception('服务器请求失败');
                }
            } else {
                throw new \Exception('资产修改失败');
            }
        } catch (\Exception $e) {
            M()->rollback();
            $msg = $e->getMessage();
            $this->error($msg);
        }
    }

    /**
     * Notice: 請求提現出隊列（这个放到定时任务里执行脚本）
     * author: hxq
     */
    public function txlie()
    {
        $redis = new Collection();
        //获取比特币和以太坊父级代码
        $btc = M('Bz')->where(array('market' => 'btc'))->getField('number');
        $eth = M('Bz')->where(array('market' => 'eth'))->getField('number');
        //执行提现队列
        $redis->withdrawHandle($btc);

        $redis->withdrawHandle($eth);

        /*$redis = $this->pconnectRedis();
        do {
            try {
                $pop = $redis->LPOP('SWMSG');

                if (!$pop) {
                    //echo "redis當中暫時沒有數據 \n";
                } else {
                    redislog($pop);
                    $pop = json_decode($pop, true);
                    //如果錢包服務器提現返回成功,講凍結的數量減去
                    if ($pop['txshash'] && $pop['enum'] == 7) {
                        //查找提现记录表
                        $where = array(
                            'mark' => $pop['id'],
                            'status' => 3,
                        );

                        $tx = M('Mytx')->where($where)->find();

                        if ($tx) {
                            M()->startTrans();

                            $cz = M('Mytx')->where($where)->save(array(
                                'tradeno' => $pop['txshash'],
                                'status' => 1,
                                'endtime' => time(),
                            ));

                            $market = $this->getmarket($pop['symbol']);
                            $markets = $market.'d';

                            $czb = $tx['num'];
                            //减去冻结资产
                            $result = M('UserCoin')->where(array('userid' => $pop['account']))->setDec($markets, $czb);

                            if ($cz && $result) {
                                if ($this->redispush(8, '', '', true, '', $pop['txshash'])) {
                                    M()->commit();
                                    echo '提現成功';
                                }
                            } else {
                                throw new \Exception('提現失敗，講數量返還給用戶');
                            }
                        } else {
                            echo '没有这条记录';
                        }
                    } else {
                        echo '钱包服务器返回失败';
                    }
                }
            } catch (\Exception $e) {
                M()->rollback();

                $msg = $e->getMessage();

                echo $msg;
            }
        } while ($redis->LSIZE('SWMSG') > 0);*/
    }

    /**
     * Notice: 签名数组转化方法
     * author: hxq
     * @param $array
     * @return string
     */
    public function getstr($array)
    {
        $res = [];
        foreach ($array as $key => $val) {
            $arr = ['name' => $key, 'value' => $val];
            $res[] = $arr;
        }

        return json_encode($res);
    }

    /**
     * Notice: 二维码生成
     * author: hxq
     * @param null $addr
     */
    public function code($addr = null)
    {
        header('Content-Type: image/png');
        Vendor('PHPQRcode.phpqrcode');
        $level = 3;
        $size = 4;
        $errorCorrectionLevel = intval($level); //容错级别
        $matrixPointSize = intval($size); //生成图片大小
        ob_clean();
        //生成二维码图片
        $object = new \QRcode();
        $object->png($addr, false, $errorCorrectionLevel, $matrixPointSize, 2);
    }
}
