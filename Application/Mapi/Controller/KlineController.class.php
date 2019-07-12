<?php

namespace Mapi\Controller;

use Common\Design\SetTrade\ModelHandle;

/**
 * 为了K线
 */
class KlineController extends CommonController
{
    /**
     * 计算所有.
     */
    public function chartAll()
    {
        //获取所有在线交易对
        $coins = M('Market')->where(['status' => 1])->field('name')->select();
        $start = time();
        foreach ($coins as $k => $v) {
            $this->setTradeJson($v['name']);
            echo $v['name'] . '计算成功--';
            echo "\n";
        }
        $end = time();
        echo '花费时间' . ($end - $start);
    }

    /**
     * Notice: 计算所有
     * @author: hxq
     * @param $market
     */
    public function setTradeJson($market)
    {
        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        foreach ($timearr as $k => $v) {
            $tradeJson = M('TradeJson')->where(['market' => $market, 'type' => $v])->order('addtime desc')->field('addtime')->find();
            if ($tradeJson) {
                $addtime = $tradeJson['addtime'];
            } else {
                $addtime = M('TradeLog')->where(['market' => $market])->order('addtime asc')->getField('addtime');
            }
            if ($addtime) {
                //开始时间之后是否有成交
                $youtradelog = M('TradeLog')->where('addtime >=' . $addtime . '  and market =\'' . $market . '\'')->field('id')->find();
            }
            //echo json_encode($youtradelog);
            //有成交数据则计算json
            if ($youtradelog) {
                if ($v == 1) {
                    $start_time = $addtime;
                } else {
                    $start_time = mktime(date('H', $addtime), floor(date('i', $addtime) / $v) * $v, 0, date('m', $addtime), date('d', $addtime), date('Y', $addtime));
                }
                $x = 0;
                for (; $x <= 2; ++$x) {
                    $na = $start_time + (60 * $v * $x);
                    $nb = $start_time + (60 * $v * ($x + 1));
                    //超过当前时间，跳出循环
                    if (time() < $na) {
                        break;
                    }
                    //查询时间区间内交易量
                    try {
                        $sum = $this->sum_market($na, $nb, $market);

                        if ($sum > 0) {
                            $sta = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('addtime asc')->getField('price');
                            $max = $this->max_market($na, $nb, $market);
                            $min = $this->min_market($na, $nb, $market);
                            $end = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('addtime desc')->getField('price');
                            $d = [$na, (float)$sum, floatval($sta), floatval($max), floatval($min), floatval($end)];
                        } else {
                            $test = M('Market')->where(['name' => $market])->find();
                            $sta = $test['new_price'];
                            $max = $test['new_price'];
                            $min = $test['new_price'];
                            $end = $test['new_price'];
                            $d = [$na, 0, floatval($sta), floatval($max), floatval($min), floatval($end)];
                        }

                        if (M('TradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->field('id')->find()) {
                            M('TradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->save(['data' => json_encode($d)]);
                            M('TradeJson')->execute('commit');
                        } else {
                            M('TradeJson')->add(['market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v]);
                            M('TradeJson')->execute('commit');
                        }
                        //echo $market . 'success';
                    } catch (\Exception $e) {
                        $msg = $e->getMessage();
                        echo $msg;
                    }
                }
            }
        }
    }

    //计算合约k线
    public function setContractTradeJson()
    {
        $markets = ['btc_usdt', 'eth_usdt'];

        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];
        foreach ($markets as $market) {
            foreach ($timearr as $k => $v) {
                $tradeJson = M('ContractTradeJson')->where(['market' => $market, 'type' => $v])->order('addtime desc')->field('addtime')->find();
                if ($tradeJson) {
                    $addtime = $tradeJson['addtime'];
                } else {
                    $ctime = M('ContractTrade')->where(['pair' => $market])->order('ctime asc')->getField('ctime');
                    //毫秒转秒
                    $addtime = strtotime(get_microtime_format($ctime));
                }
                if ($addtime) {
                    //秒转毫秒
                    $ms = get_data_format(date('Y-m-d H:i:s', $addtime));
                    //开始时间之后是否有成交
                    $log = M('ContractTrade')->where('ctime >=' . $ms . '  and pair =\'' . $market . '\'')->field('id')->find();
                }
                //有成交数据则计算json
                if ($log) {
                    if ($v == 1) {
                        $start_time = $addtime;
                    } else {
                        $start_time = mktime(date('H', $addtime), floor(date('i', $addtime) / $v) * $v, 0, date('m', $addtime), date('d', $addtime), date('Y', $addtime));
                    }
                    $x = 0;
                    for (; $x <= 2; ++$x) {
                        $na = $start_time + (60 * $v * $x);
                        $nb = $start_time + (60 * $v * ($x + 1));
                        //超过当前时间，跳出循环
                        if (time() < $na) {
                            break;
                        }

                        $mna = get_data_format(date('Y-m-d H:i:s', $na));
                        $mnb = get_data_format(date('Y-m-d H:i:s', $nb));
                        //echo $mna . '--' . $mnb . '|||';
                        //查询时间区间内交易量
                        $sum = M('ContractTrade')->where('ctime >=' . $mna . ' and ctime <' . $mnb . ' and pair =\'' . $market . '\'')->sum('amount');

                        if ($sum > 0) {
                            $sta = M('ContractTrade')->where('ctime >=' . $mna . ' and ctime <' . $mnb . ' and pair =\'' . $market . '\'')->order('ctime asc')->getField('price');
                            $max = M('ContractTrade')->where('ctime >=' . $mna . ' and ctime <' . $mnb . ' and pair =\'' . $market . '\'')->max('price');
                            $min = M('ContractTrade')->where('ctime >=' . $mna . ' and ctime <' . $mnb . ' and pair =\'' . $market . '\'')->min('price');
                            $end = M('ContractTrade')->where('ctime >=' . $mna . ' and ctime <' . $mnb . ' and pair =\'' . $market . '\'')->order('ctime desc')->getField('price');
                            $d = [$na, (float)$sum, floatval($sta), floatval($max), floatval($min), floatval($end)];
                            try {
                                if (M('ContractTradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->field('id')->find()) {
                                    M('ContractTradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->save(['data' => json_encode($d)]);
                                    M('ContractTradeJson')->execute('commit');
                                } else {
                                    M('ContractTradeJson')->add(['market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v]);
                                    M('ContractTradeJson')->execute('commit');
                                }
                            } catch (\Exception $e) {
                                $msg = $e->getMessage();
                                echo $msg;
                            }
                        }
                    }
                }
            }
        }
    }

    //合约k线数据存入redis
    public function setContractKlineToRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(5);

        $types = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        $names = ['btc_usdt', 'eth_usdt'];
        foreach ($names as $name) {
            $x = 0;
            for (; $x < 2; ++$x) {
                foreach ($types as $type) {
                    $key = 'kline:' . $name . ':' . $type;
                    //获取最后一个分值对应的时间戳
                    $score = $redis->zrevrange($key, 0, 1, 'withscores');

                    //没有记录
                    if (count($score) == 0) {
                        //第一次执行
                        $json = M('ContractTradeJson')->where(['market' => $name, 'type' => $type])->order('id asc')->find();
                        //防止没有记录导致有序集合不能正确读取
                        if ($json) {
                            echo $res = $redis->zAdd($key, $json['addtime'], $json['data']);
                        }
                    } else {
                        $init = 0;
                        foreach ($score as $v) {
                            //查找下一个节点数据
                            if ($v > $init) {
                                $init = $v;
                            }
                        }

                        //更新当前分值，防止数据为0的统计
                        $now = M('ContractTradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $init])->find();
                        if ($now) {
                            $check = $redis->Zrangebyscore($key, $init, $init);
                            if ($check) {
                                $redis->zDeleteRangeByScore($key, $init, $init);
                            }
                            echo $res = $redis->zAdd($key, $init, $now['data']);
                        }

                        //更新下一条分值
                        $addtime = $init + ($type * 60);
                        //超过当前时间，不统计
                        if ($addtime > time()) {
                            break;
                        }

                        $json = M('ContractTradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $addtime])->find();
                        //还是没有记录，找下一条
                        if (!$json) {
                            $where['market'] = $name;
                            $where['type'] = $type;
                            $where['addtime'] = ['gt', $addtime];
                            $json = M('ContractTradeJson')->where($where)->order('id asc')->limit(1)->find();
                        }
                        $data = $json['data'];
                        $time = $json['addtime'];
                        if ($data) {
                            //验证，数据为0，不存redis，防止k线乱了
                            //$arr = json_decode($data, TRUE);
                            //有成交量
                            //if ($arr[1] > 0) {
                            $check = $redis->Zrangebyscore($key, $time, $time);
                            if ($check) {
                                $redis->zDeleteRangeByScore($key, $time, $time);
                            }
                            echo $res = $redis->zAdd($key, $time, $data);
                            //}
                        }
                    }
                }
            }
        }
    }

    /**
     * Notice: 手动更新币种k线数据
     * @author: hxq
     * @param string $market
     */
    public function setTradeJsonByHand($market = 'eosdac_usdt')
    {
        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        foreach ($timearr as $k => $v) {
            $tradeJson = M('TradeJson')->where(['market' => $market, 'type' => $v])->order('id desc')->field('addtime')->find();
            if ($tradeJson) {
                $addtime = $tradeJson['addtime'];
            } else {
                $addtime = M('TradeLog')->where(['market' => $market])->order('id asc')->getField('addtime');
            }
            if ($addtime) {
                //开始时间之后是否有成交
                $youtradelog = M('TradeLog')->where('addtime >=' . $addtime . '  and market =\'' . $market . '\'')->field('id')->find();
            } else {
                $youtradelog = FALSE;
            }
            //有成交数据则计算json
            if ($youtradelog) {
                if ($v == 1) {
                    $start_time = $addtime;
                } else {
                    $start_time = mktime(date('H', $addtime), floor(date('i', $addtime) / $v) * $v, 0, date('m', $addtime), date('d', $addtime), date('Y', $addtime));
                }
                $x = 0;
                for (; $x <= 360; ++$x) {
                    $na = $start_time + (60 * $v * $x);
                    $nb = $start_time + (60 * $v * ($x + 1));
                    //超过当前时间，跳出循环
                    if (time() < $na) {
                        break;
                    }
                    //查询时间区间内交易量
                    $sum = $this->sum_market($na, $nb, $market);
                    if ($sum > 0) {
                        $sta = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id asc')->getField('price');
                        $max = $this->max_market($na, $nb, $market);
                        $min = $this->min_market($na, $nb, $market);
                        $end = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->order('id desc')->getField('price');
                        $d = [$na, $sum, floatval($sta), floatval($max), floatval($min), floatval($end)];

                        if (M('TradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->field('id')->find()) {
                            $rs = M('TradeJson')->where(['market' => $market, 'addtime' => $na, 'type' => $v])->save(['data' => json_encode($d)]);
                            M('TradeJson')->execute('commit');
                        } else {
                            $rs = M('TradeJson')->add(['market' => $market, 'data' => json_encode($d), 'addtime' => $na, 'type' => $v]);
                            M('TradeJson')->execute('commit');
                            M('TradeJson')->where(['market' => $market, 'data' => '', 'type' => $v])->delete();
                            M('TradeJson')->execute('commit');
                        }
                        echo $rs;
                        echo "\n";
                    } else {
                        M('TradeJson')->add(['market' => $market, 'data' => '', 'addtime' => $na, 'type' => $v]);
                        M('TradeJson')->execute('commit');
                    }
                }
            }
        }
    }

    //计算总数量
    public function sum_market($na, $nb, $market)
    {
        $s = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->getField('num', TRUE);
        $num = 0;
        foreach ($s as $v) {
            $num = bcadd($num, $v, 8);
        }

        return $num;
    }

    /**
     * 手动修复k线数据
     */
    public function repair_kline()
    {
        $market = 'bys_usdt';
        $redis = $this->connectRedis();
        $types = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];
        $keys = [];

        foreach ($types as $type) {
            $keys[] = 'kline:' . $market . ':' . $type;
        }

        foreach ($keys as $key) {
            //键名下所有集合
            $type = explode(':', $key)[2];
            //dump($type);
            $score = $redis->zRange($key, 0, -1);
            foreach ($score as $val) {
                //json字符串转数组
                $arr = json_decode($val, TRUE);
                $addtime = $arr[0];
                $num = $arr[1];
                if ($num == 0) {
                    $where = ['market' => 'bys_usdt', 'type' => $type, 'addtime' => $addtime];
                    $tradeJson = M('TradeJson')->where($where)->find();
                    $start = $addtime;
                    $end = $addtime + ($type * 60);
                    $mum = $this->sum_market($start, $end, $market);
                    $jsonArr = json_decode($tradeJson['data'], TRUE);
                    $jsonArr[1] = $mum;
                    $reJson = json_encode($jsonArr);
                    //修复json表数据
                    $rs = M('TradeJson')->where($where)->save(['data' => $reJson]);
                    echo $rs;
                    //修复redis表数据
                    echo $redis->zDeleteRangeByScore($key, $addtime, $addtime);
                    echo $redis->zAdd($key, $addtime, $reJson);
                }
            }
        }
    }

    //最大价格
    public function max_market($na, $nb, $market)
    {
        $s = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->getField('price', TRUE);
        $num = max($s);

        return $num;
    }

    //最小价格
    public
    function min_market($na, $nb, $market)
    {
        $s = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $market . '\'')->getField('price', TRUE);
        $num = min($s);

        return $num;
    }

    /**
     * 计算趋势
     */
    public function tendency()
    {
        //获取所有在线交易对
        $coins = M('Market')->where(['status' => 1])->select();
        foreach ($coins as $k => $v) {
            echo '----计算趋势----' . $v['name'] . '------------';
            $tendency_time = 4;
            $t = time();
            $tendency_str = $t - (24 * 60 * 60 * 3);
            $x = 0;
            $temp = [];
            for (; $x < 18; ++$x) {
                $na = $tendency_str + (60 * 60 * $tendency_time * $x);
                $nb = $tendency_str + (60 * 60 * $tendency_time * ($x + 1));

                $arr = M('TradeLog')->where('addtime >=' . $na . ' and addtime <' . $nb . ' and market =\'' . $v['name'] . '\'')->getField('price', TRUE);

                //如果时间段内没有数据
                if (count($arr) > 0) {
                    $b = max($arr);
                    $temp[$x] = $b;
                } else {
                    $b = $temp[$x - 1] ? $temp[$x - 1] : $v['hou_price'];
                }

                $rs[] = [$na, $b];
            }
            unset($temp);
            M('Market')->where(['name' => $v['name']])->setField('tendency', json_encode($rs));
            unset($rs);
            echo '计算成功!';
            echo "\n";
        }

        echo '趋势计算0k ' . "\n";
    }

    //计算合约行情
    public function contractMarket()
    {
        $arr = ['btc_usdt', 'eth_usdt'];
        foreach ($arr as $market) {
            //期货最新成交价
            $data['new_price'] = M('ContractTrade')->where(['pair' => $market])->order('id desc')->limit(1)->getField('price');
            $time = (time() - (60 * 60 * 24)) . '000';
            //24H最低价
            $data['min_price'] = round(M('ContractTrade')->where([
                'pair' => $market,
                'ctime' => ['gt', $time],
            ])->min('price'), 4);
            //24H最高价
            $data['max_price'] = round(M('ContractTrade')->where([
                'pair' => $market,
                'ctime' => ['gt', $time],
            ])->max('price'), 4);
            //24H成交量
            $data['volume'] = round(M('ContractTrade')->where([
                'pair' => $market,
                'ctime' => ['gt', $time],
            ])->sum('amount'), 2);
            //总成交量
            $data['total'] = round(M('ContractTrade')->where(['pair' => $market])->sum('amount'), 2);
            //24H收盘价
            $start = strtolower('-1 days') . '000';
            $hou_price = M('ContractTrade')->where([
                'pair' => $market,
                'ctime' => ['gt', $start]
            ])->order('id asc')->limit(1)->getField('price');
            $change = round((($data['new_price'] - $hou_price) / $hou_price) * 100, 2);
            //24H涨跌幅
            $data['change'] = $change;

            $find = M('ContractMarket')->where(['market' => $market])->find();
            if ($find) {
                $res = M('ContractMarket')->where(['market' => $market])->save($data);
            } else {
                $data['market'] = $market;
                $res = M('ContractMarket')->where(['market' => $market])->add($data);
            }
            echo $res;
            echo "\n";
        }
    }

    //更新价格
    public function updateMarket()
    {
        $markets = M('Market')->where(['status' => 1])->field('name')->select();

        foreach ($markets as $market) {
            if (!$market['hou_price'] || (date('H', time()) == '00')) {
                $t = time();
                $start = mktime(0, 0, 0, date('m', $t), date('d', $t), date('Y', $t));
                $hou_price = M('TradeLog')->where([
                    'market' => $market['name'],
                    'addtime' => ['lt', $start],
                ])->order('addtime desc')->getField('price');

                if (!$hou_price) {
                    $hou_price = M('TradeLog')->where(['market' => $market['name']])->order('addtime asc')->getField('price');
                }

                M('Market')->where(['name' => $market['name']])->setField('hou_price', $hou_price);
            }
            //调用模型处理方法
            //if ($market['name'] != 'yhet_usdt') {
                $res = ModelHandle::UpdateMarketPrice($market['name']);
                echo $res;
            //}
        }
    }

    /**
     * 手动执行.
     */
    public function setJsonToRedisByHand()
    {
        $keys = $this->getRedisSortedKeys();

        $redis = $this->connectRedis();
        $x = 0;
        for (; $x < 2000; ++$x) {
            foreach ($keys as $key) {
                //获取最后一个分值对应的时间戳
                $score = $redis->zrevrange($key, 0, 1, 'withscores');
                foreach ($score as $k => $v) {
                    //$v是最后一个分值，也就是时间戳
                    //拆分name和type
                    $name = explode(':', $key)[1];
                    $type = explode(':', $key)[2];
                    $addtime = $v + ($type * 60);
                    $data = M('TradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $addtime])
                        ->getField('data');
                    //如果json表数据出现断层了
                    if (!$data) {
                        $next = M('TradeJson')->where([
                            'market' => $name,
                            'type' => $type,
                            'addtime' => ['gt', $addtime],
                        ])->limit(1)->find();
                        $data = $next['data'];
                        $addtime = $next['addtime'];
                    }

                    //如果还是为null,则刷新数据
                    if (!$data) {
                        $data = M('TradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $v])
                            ->getField('data');
                        $addtime = $v;
                    }

                    if ($data) {
                        $check = $redis->Zrangebyscore($key, $addtime, $addtime);
                        if ($check) {
                            $redis->zDeleteRangeByScore($key, $addtime, $addtime);
                        }
                        $res = $redis->zAdd($key, $addtime, $data);
                        echo $res;
                    }
                }
            }
        }
    }

    /**
     * 定时写入redis.
     */
    public function setJsonToRedis()
    {
        $keys = $this->getRedisSortedKeys();

        $redis = $this->connectRedis();
        $x = 0;
        for (; $x < 2; ++$x) {
            foreach ($keys as $key) {
                //获取最后一个分值对应的时间戳
                $score = $redis->zrevrange($key, 0, 1, 'withscores');
                foreach ($score as $k => $v) {
                    //$v是最后一个分值，也就是时间戳
                    //拆分name和type
                    $name = explode(':', $key)[1];
                    $type = explode(':', $key)[2];
                    $addtime = $v + ($type * 60);
                    //为了那边逗比，伪造yhet数据
                    //if ($name == 'yhet_usdt') {
                        //$data = json_encode([time(), 500, 0.04, 0.04, 0.04, 0.04]);
                        //$res = $redis->zAdd($key, $addtime, $data);
                        //echo $res;
                    //} else {
                        $data = M('TradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $addtime])
                            ->getField('data');
                        //如果json表数据出现断层了
                        if (!$data) {
                            $next = M('TradeJson')->where([
                                'market' => $name,
                                'type' => $type,
                                'addtime' => ['gt', $addtime],
                            ])->limit(1)->find();
                            $data = $next['data'];
                            $addtime = $next['addtime'];
                        }

                        //如果还是为null,则刷新数据
                        if (!$data) {
                            $data = M('TradeJson')->where(['market' => $name, 'type' => $type, 'addtime' => $v])
                                ->getField('data');
                            $addtime = $v;
                        }

                        if ($data) {
                            $check = $redis->Zrangebyscore($key, $addtime, $addtime);
                            if ($check) {
                                $redis->zDeleteRangeByScore($key, $addtime, $addtime);
                            }
                            $res = $redis->zAdd($key, $addtime, $data);
                            echo $res;
                        }
                    }
                //}
            }
        }
    }

    //初始化交易对redis数据
    public function startRedis()
    {
        //$markets = ['qtum_usdt', 'bch_usdt', 'etc_usdt', 'bts_usdt', 'ae_usdt', 'doge_usdt', 'hc_usdt', 'zrx_usdt','pass_usdt'];
        $markets = ['yhet_usdt'];
        foreach ($markets as $market) {
            //$market = 'pass_usdt';
            $keys = $this->getKeysByMarket($market);
            //$keys = array('kline:bys_usdt:1','kline:eth_usdt:1');
            $redis = $this->connectRedis();

            foreach ($keys as $key) {
                $market = explode(':', $key)[1];
                $type = explode(':', $key)[2];
                //查找第一条成交数据
                $tradeJson = M('TradeJson')->where(['market' => $market, 'type' => $type])->find();

                //$addtime = time();
                //$data = json_encode([time(), 500, 0.04, 0.04, 0.04, 0.04]);

                echo $redis->zAdd($key, $tradeJson['addtime'], $tradeJson['data']);
            }
        }
    }

    protected function getKeysByMarket($pair)
    {
        $types = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];
        $keys = [];
        foreach ($types as $type) {
            $keys[] = 'kline:' . $pair . ':' . $type;
        }

        return $keys;
    }

    /**
     * 删除redis所有空值
     * 手动执行.
     */
    public function deleteRedisAllKey()
    {
        $keys = $this->getRedisSortedKeys();
        $redis = $this->connectRedis();

        foreach ($keys as $k => $v) {
            $total = $redis->zDelete($v, NULL);
            echo $total;
        }
    }

    /**
     * 组装redis键名数组.
     */
    protected function getRedisSortedKeys()
    {
        //获取所有交易对
        $pairs = M('Market')->where(['status' => 1])->field('name')->select();
        //所有type值
        $types = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];
        $keys = [];
        foreach ($pairs as $v) {
            foreach ($types as $type) {
                $keys[] = 'kline:' . $v['name'] . ':' . $type;
            }
        }

        return $keys;
    }

    /**
     * 从redis删除1000条以前的数据.
     */
    public function deleteExpireDataFromRedis()
    {
        $keys = $this->getRedisSortedKeys();
        $redis = $this->connectRedis();

        foreach ($keys as $key) {
            //每个key保留1000条数据
            $count = $redis->ZCARD($key);
            if ($count > 1000) {
                //删除
                $step = $count - 1000;
                $redis->zDeleteRangeByRank($key, 0, $step);
                echo $key . '删除了' . $step . '条';
                echo "\n";
            }
        }
    }

    //清理合约k线超过1000条的redis记录
    public function deleteContractRedis()
    {
        $redis = new \Redis();
        $redis->connect(C('REDIS_HOSTSS'), C('REDIS_PORTSS')); //C('REDIS_PORT')
        $redis->auth(C('REDIS_PWD')); //链接密码
        $redis->select(5);

        $types = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];
        $name = 'btc_usdt';
        foreach ($types as $type) {
            $key = 'kline:btc_usdt:' . $type;
            //每个key保留1000条数据
            $count = $redis->ZCARD($key);
            if ($count > 1000) {
                //删除
                $step = $count - 1000;
                $redis->zDeleteRangeByRank($key, 0, $step);
                echo $key . '删除了' . $step . '条';
                echo "\n";
            }
        }
    }

    /**
     * 初始化币种交易总量
     * 手动执行一次
     */
    public function setVolumeToRedisByHand()
    {
        //查询所有在线币种
        $markets = M('Market')->where(['status' => 1])->field('name')->select();
        //连接redis
        $redis = $this->connectRedis();
        //遍历处理
        foreach ($markets as $k => $v) {
            //最后一条成交记录id
            $lastid = M('TradeLog')->where(['market' => $v['name']])->order('id desc')->getField('id');
            //交易对成交总额
            $volume = M('TradeLog')->where(['market' => $v['name']])->sum('mum');
            //组装json
            $json = json_encode(['id' => $lastid, 'volume' => $volume]);
            //存入redis哈希
            echo $redis->hset('exp:coin:market:volume', $v['name'], $json);
        }
    }

    /**
     * 自动更新redis的虚拟币成交总量
     * 计划任务
     */
    public function updateRedisMarketVolume()
    {
        //查询所有在线币种
        $markets = M('Market')->where(['status' => 1])->field('name')->select();
        //连接redis
        $redis = $this->connectRedis();
        //遍历处理
        foreach ($markets as $k => $v) {
            //从redis获取对应交易对的值
            $redisJson = $redis->hget('exp:coin:market:volume', $v['name']);
            //转为数组
            $arr = json_decode($redisJson, TRUE);
            //查询超过记录id的最新记录
            $logs = M('TradeLog')->where(['market' => $v['name'], 'id' => ['gt', $arr['id']]])->getField('mum', TRUE);
            //如果有新的成交记录
            if (count($logs) > 0) {
                //最后成交id
                $lastid = M('TradeLog')->where(['market' => $v['name']])->order('id desc')->getField('id');
                //成交量
                $volume = bcadd($arr['volume'], array_sum($logs), 8);
                //组装json
                $json = json_encode(['id' => $lastid, 'volume' => $volume]);
                //存入redis哈希
                echo $redis->hset('exp:coin:market:volume', $v['name'], $json);
            }
        }
    }

    //更新人民币对美元价格
    public function setRateByBaidu()
    {
        $price = getRateByUsdt();

        if (!$price) {
            $price = getRateByBaidu();
        }

        if (!$price) {
            $price = 6.83;
        }

        $rate = M('Rate')->where(['id' => 1])->find();

        if ($rate) {
            $res = M('Rate')->where(['id' => 1])->save(['rate' => $price, 'addtime' => time()]);
        } else {
            $res = M('Rate')->add(['rate' => $price, 'addtime' => time()]);
        }

        echo $res;
    }
}
