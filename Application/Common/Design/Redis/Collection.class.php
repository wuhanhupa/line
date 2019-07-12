<?php

namespace Common\Design\Redis;

/**
 * redis操作.
 */
class Collection
{
    /**
     * 进入消息队列.
     * @param        $type
     * @param        $userid
     * @param        $market
     * @param string $status [状态]
     * @param string $value [值]
     * @param string $hash
     * @param string $unqid
     * @return bool [bool] [布尔值]
     * @internal param $ [int]  $type   [类型]
     * @internal param $ [int]  $userid [用户ID]
     * @internal param $ [int]  $market [币种代号]
     */
    public function redispush($type, $userid, $market, $status = '', $value = '', $hash = '', $unqid = '')
    {
        //获取队列名称
        $queue = $this->getQueueName($type, $market);
        //打包数据
        $data = $this->packageData($type, $userid, $market, $status, $value, $hash, $unqid);
        //加密数据并推送给redis
        $res = $this->pushRedis($data, $queue);
        //dump($res);
        if ($res) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * 创建地址成功返回操作.
     * @param $userid
     * @param $symbol
     */
    public function createAddressHandle($userid, $symbol)
    {
        $redis = $this->pconnectRedis();
        //获取队列
        $queue = $this->getQueueName(2, $symbol);
        redislog($queue);
        //list类型出队操作
        while (TRUE) {
            try {
                $pop = $redis->LPOP($queue);
                if (!$pop) {
                    break;
                }
                //存入redis返回数据
                redislog($pop);
                $pop = json_decode($pop, TRUE);
                //2-创建地址返回，4-充值成功返回 7-提现成功返回
                M()->startTrans();
                if ($pop['enum'] == 2) {
                    $res = $this->redispush(3, $pop['account'], $pop['symbol'], TRUE);
                    if ($res) {
                        if ($pop['account']) {
                            //查询用户是否有地址
                            $check = M('UserQianbao')->where(['userid' => $pop['account'], 'coinname' => $pop['symbol'], 'type' => 1])->find();
                            if (!$check) {
                                $add = M('UserQianbao')->add(['userid' => $pop['account'], 'coinname' => $pop['symbol'], 'addr' => $pop['address'], 'addtime' => time(), 'type' => 1]);

                                if ($add) {
                                    M()->commit();
                                    //如果是当前用户
                                    if ($userid == $pop['account']) {
                                        return $pop['address'];
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (Exception $e) {
                M()->rollback();
                redislog($e->getMessage());
            }
            sleep(1);
        }
    }

    /**
     * 充值成功返回操作.
     * @param $symbol
     * @throws \Exception
     */
    public function depositHandle($symbol)
    {
        $redis = $this->pconnectRedis();
        //获取队列
        $queue = $this->getQueueName(4, $symbol);
        echo $queue . '--' . ($redis->lSize($queue)) . '--';
        //list类型出队操作
        while (TRUE) {
            try {
                $pop = $redis->LPOP($queue);
                //dump($pop);
                if (!$pop) {
                    break;
                }
                //存入redis返回数据
                redislog($pop);
                $pop = json_decode($pop, TRUE);
                //2-创建地址返回，4-充值成功返回 7-提现成功返回
                M()->startTrans();
                //每条消息给反馈
                $this->redispush(5, $pop['account'], $pop['symbol'], TRUE, '', $pop['txshash']);

                //判断hash是否已经记录，也就是这条充值记录已经接受并修改了用户对应币种余额
                $find = M('Mycz')->where(['tradeno' => $pop['txshash']])->find();
                //没有记录hash代表新的充值记录
                if (count($find) == 0) {
                    $market = $this->getMarket($pop['symbol']);
                    //echo $market;
                    //如何获取的msg消息里面的id是5 那么给用户添加充值记录
                    $sql = 'insert into btchanges_mycz (userid,num,mum,type,tradeno,addtime,coinname,remark,status) values(' .
                        '\'' . $pop['account'] . '\', '
                        . '\'' . $pop['value'] . '\', '
                        . '\'' . $pop['value'] . '\', '
                        . '\'' . 'cz' . '\', '
                        . '\'' . $pop['txshash'] . '\','
                        . '\'' . time() . '\','
                        . '\'' . $pop['symbol'] . '\','
                        . '\'' . 'cz' . '\', '
                        . '\'' . 1 . '\')';
                    //echo $sql;
                    $m = M('Mycz');
                    $cz = $m->execute($sql);

                    if ($cz) {
                        //充值记录添加完成之后,给对应的币种添加1.先查询原来的值在进行加
                        $property = M('User_coin')->where(['userid' => $pop['account']])->setInc($market, $pop['value']);
                        if ($property) {
                            //发送消息告诉redis当前用户已经充值完成
                            //if ($this->redispush(5, $pop['account'], $pop['symbol'], TRUE, '', $pop['txshash'])) {
                            echo "充值成功 \n";
                            //}
                        } else {
                            throw new \Exception("充值失敗 \n");
                        }
                    } else {
                        throw new \Exception($cz);
                    }
                } else {
                    //dump('没有消息');
                    throw new \Exception('error hash');
                }
                M()->commit();
            } catch (\Exception $e) {
                M()->rollback();
                echo $e->getMessage();
                redislog($e->getMessage());
            }
            sleep(1);
        }
    }

    /**
     * 提现成功返回.
     * @param $symbol
     * @throws \Exception
     */
    public function withdrawHandle($symbol)
    {
        $redis = $this->pconnectRedis();
        //获取队列
        $queue = $this->getQueueName(7, $symbol);
        echo $queue . '--' . ($redis->lSize($queue)) . '--';
        //list类型出队操作
        while (TRUE) {
            try {
                $pop = $redis->LPOP($queue);
                if (!$pop) {
                    break;
                }
                //存入redis返回数据
                redislog($pop);
                $pop = json_decode($pop, TRUE);
                //2-创建地址返回，4-充值成功返回 7-提现成功返回
                M()->startTrans();
                //如果錢包服務器提現返回成功,講凍結的數量減去
                if ($pop['txshash'] && $pop['enum'] == 7) {
                    //每条消息都给反馈
                    $this->redispush(8, $pop['account'], $pop['symbol'], TRUE, '', $pop['txshash'], $pop['id']);

                    //查找提现记录表
                    $where = [
                        'mark' => $pop['id'],
                        'status' => 3,
                    ];

                    $tx = M('Mytx')->where($where)->find();

                    if ($tx) {
                        $cz = M('Mytx')->where($where)->save([
                            'tradeno' => $pop['txshash'],
                            'status' => 1,
                            'endtime' => time(),
                        ]);

                        $market = $this->getMarket($pop['symbol']);
                        $markets = $market . 'd';

                        $czb = $tx['num'];
                        //减去冻结资产
                        $result = M('UserCoin')->where(['userid' => $pop['account']])->setDec($markets, $czb);

                        if ($cz && $result) {
                            M()->commit();
                            echo '提現成功';
                        } else {
                            throw new \Exception('提現失敗，講數量返還給用戶');
                        }
                    } else {
                        throw new \Exception('没有这条记录');
                    }
                } else {
                    throw new \Exception('钱包服务器返回失败');
                }
            } catch (\Exception $e) {
                M()->rollback();
                echo $e->getMessage();
                redislog($e->getMessage());
            }
            sleep(1);
        }
    }

    /**
     * 获取队列名称.
     * @param $type
     * @param $symbol
     * @return string
     */
    public function getQueueName($type, $symbol)
    {
        //队列名称前缀
        $prifix = $this->getPrefixBySymbol($symbol);
        //队列名称
        $name = $this->getNameByType($type);

        return $prifix . $name . 'MSG';
    }

    /**
     * 根据币代码返回队列名称前缀
     * @param $symbol
     * @return null|string
     */
    public function getPrefixBySymbol($symbol)
    {
        //以太坊币链
        if ($symbol > 0 && $symbol < 1000) {
            return NULL;
        }
        //EOS币链
        if ($symbol > 999 && $symbol < 2000) {
            return 'EOS';
        }
        //比特币币链
        if ($symbol > 1999 && $symbol != 2013) {
            return 'BTC';
        }
    }

    /**
     * 根据类型获取队列中间名称.
     * @param $type
     * @return string
     */
    public function getNameByType($type)
    {
        switch ($type) {
            case 1:
            case 3:
                $name = 'CA';
                break;
            case 2:
                $name = 'SA';
                break;
            case 4:
                $name = 'SSD';
                break;
            case 5:
                $name = 'CD';
                break;
            case 6:
            case 8:
                $name = 'CW';
                break;
            case 7:
                $name = 'SW';
                break;
        }

        return $name;
    }

    /**
     * 推送给redis队列.
     * @param $data
     * @param $queue
     * @return int
     */
    public function pushRedis($data, $queue)
    {
        $redis = $this->connectRedis();

        ksort($data);
        $str = $this->getstr($data);
        $data['sign'] = hash_hmac('sha256', $str, 'BAYESIN987');
        $userlist = json_encode($data);

        //dump($userlist);

        return $redis->RPUSH($queue, $userlist);
    }

    /**
     * 打包数据.
     * @param $type
     * @param $userid
     * @param $market
     * @param $status
     * @param $value
     * @param $hash
     * @param $unqid
     * @return mixed
     */
    public function packageData($type, $userid, $market, $status, $value, $hash, $unqid)
    {
        switch ($type) {
            case 1:
                $user['account'] = (int)$userid;
                $user['enum'] = $type;
                $user['id'] = uniqid();
                $user['symbol'] = (int)$market;
                break;
            case 3:
                $user['id'] = uniqid();
                $user['enum'] = $type;
                $user['account'] = (int)$userid;
                $user['symbol'] = (int)$market;
                $user['status'] = $status;
                break;
            case 5:
                $user['id'] = $hash;
                $user['enum'] = $type;
                $user['status'] = (int)$status;
                break;
            case 6:
                $user['id'] = $unqid;
                $user['enum'] = $type;
                $user['account'] = (int)$userid;
                $user['symbol'] = (int)$market;
                $user['value'] = $value;
                $user['address'] = $hash;
                break;
            case 8:
                $user['id'] = $unqid;
                $user['enum'] = $type;
                $user['status'] = (int)$status;
                $user['account'] = (int)$userid;
                $user['symbol'] = (int)$market;
                break;
            default:
                break;
        }

        return $user;
    }

    /**
     * 获取币种代码
     * @param $market
     * @return mixed
     */
    public function getSymbol($market)
    {
        return M('Bz')->where(['market' => $market])->getField('number');
    }

    /**
     * 根据代码获取交易对.
     * @param $symbol
     * @return mixed
     */
    public function getMarket($symbol)
    {
        return M('Bz')->where(['number' => $symbol])->getField('market');
    }

    /**
     * 签名数组转化方法.
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
     * 连接redis
     * @return \Redis
     */
    public function connectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS'));
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }

    /**
     * 连接redis
     * @return \Redis
     */
    public function pconnectRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS'));
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(C('REDIS_DB'));

        return $redis;
    }
}
