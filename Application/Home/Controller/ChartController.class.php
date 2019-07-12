<?php

namespace Home\Controller;

class ChartController extends HomeController
{
    public function getJsonData($market = NULL, $ajax = 'json')
    {
        $data = [];
        if ($market) {
            //$data = (APP_DEBUG ? NULL : S('ChartgetJsonData' . $market));

            //if (!$data) {
            $data[0] = $this->getTradeBuy($market);
            $data[1] = $this->getTradeSell($market);
            $data[2] = $this->getTradeLog($market);

            //S('ChartgetJsonData' . $market, $data);
            //}

            exit(json_encode($data));
        }
    }

    //获取买入深度
    protected function getTradeBuy($market)
    {
        $redis = $this->connectRedis();
        $key = 'exp:depth:' . strtoupper($market) . ':buy';
        $redisBuy = $redis->hGetAll($key);
        //处理redis数据
        $buyArr = [];
        foreach ($redisBuy as $k => $v) {
            $array['price'] = $k;
            $array['nums'] = $v;
            $buyArr[] = $array;
        }

        $mo = M();
        $buyModel = $mo->query('select id,price,sum(num-deal)as nums from btchanges_trade  where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit 100;');
        //去掉小数点
        foreach ($buyModel as &$v) {
            $v['price'] = floatval($v['price']);
        }
        //合并数组
        $buy = array_merge($buyArr, $buyModel);
        //排序数组
        $buy = array_sort($buy, 'price', 'desc');
        $buy = array_slice($buy, 0, 50, FALSE);

        $data = '';
        if ($buy) {
            $maxNums = maxArrayKey($buy, 'nums') / 2;
            //获取小数点
            $marketData = M('Market')->where(['name' => $market])->find();
            foreach ($buy as $k => $v) {
                $data .= '<tr><td class=\'buy\'  width=\'50\'>买' . ($k + 1) . '</td><td class=\'buy\'  width=\'80\'>' . round($v['price'], $marketData['price_round']) . '</td><td class=\'buy\'  width=\'120\'>' . round($v['nums'], $marketData['num_round']) . '</td><td  width=\'100\'><span class=\'buySpan\' style=\'width: ' . ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100) . 'px;\' ></span></td></tr>';
            }
        }

        return $data;
    }

    //获取卖出深度
    protected function getTradeSell($market)
    {
        $redis = $this->connectRedis();
        $key = 'exp:depth:' . strtoupper($market) . ':sell';
        $redisSell = $redis->hGetAll($key);
        //处理redis数据
        $sellArr = [];
        foreach ($redisSell as $k => $v) {
            $array['price'] = $k;
            $array['nums'] = $v;
            $sellArr[] = $array;
        }

        $mo = M();
        $sellModel = $mo->query('select id,price,sum(num-deal)as nums from btchanges_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit 100;');
        //去掉小数点
        foreach ($sellModel as &$v) {
            $v['price'] = floatval($v['price']);
        }
        //合并数组
        $sell = array_merge($sellArr, $sellModel);
        //排序数组
        $sell = array_sort($sell, 'price', 'asc');
        $sell = array_slice($sell, 0, 50, FALSE);

        $data = '';

        if ($sell) {
            $maxNums = maxArrayKey($sell, 'nums') / 2;
            //获取小数点
            $marketData = M('Market')->where(['name' => $market])->find();
            foreach ($sell as $k => $v) {
                $data .= '<tr><td class=\'sell\'  width=\'50\'>卖' . ($k + 1) . '</td><td class=\'sell\'  width=\'80\'>' . round($v['price'], $marketData['price_round']) . '</td><td class=\'sell\'  width=\'120\'>' . round($v['nums'], $marketData['num_round']) . '</td><td style=\'width: 100px;\'><span class=\'sellSpan\' style=\'width: ' . ((($maxNums < $v['nums'] ? $maxNums : $v['nums']) / $maxNums) * 100) . 'px;\' ></span></td></tr>';
            }
        }

        return $data;
    }

    //获取成交记录
    protected function getTradeLog($market)
    {
        //$data = S('getTradeLogChart'.$market);
        //if (!$data) {
        $data = '';
        $log = M('TradeLog')->where(['market' => $market])->order('id desc')->limit(100)->select();
        //获取小数点
        $marketData = M('Market')->where(['name' => $market])->find();
        if ($log) {
            foreach ($log as $k => $v) {
                if ($v['type'] == 1) {
                    $type = 'buy';
                } else {
                    $type = 'sell';
                }

                $data .= '<tr><td class=\'' . $type . '\'  width=\'70\'>' . date('H:i:s', $v['addtime']) . '</td><td class=\'' . $type . '\'  width=\'70\'>' . round($v['price'], $marketData['price_round']) . '</td><td class=\'' . $type . '\'  width=\'100\'>' . round($v['num'], $marketData['num_round']) . '</td><td class=\'' . $type . '\'>' . floatval($v['mum']) . '</td></tr>';
            }
            //S('getTradeLogChart'.$market, $data);
        }

        //}

        return $data;
    }

    /**
     * 获取趋势
     * 天王盖地虎，小鸡炖蘑菇.
     * @author hxq
     * @date   2018-09-13T13:34:34+080
     * @return [type] [description]
     */
    public function trend()
    {
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';

        $this->assign('market', $market);
        $this->display();
    }

    /**
     * 没有找到调用，暂定.
     * @return [type] [description]
     */
    public function getMarketTrendJson()
    {
        // TODO: SEPARATE
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';
        $data = (APP_DEBUG ? NULL : S('ChartgetMarketTrendJson' . $market));

        if (!$data) {
            $data = M('TradeLog')->where([
                'market' => $market,
                'addtime' => ['gt', time() - (60 * 60 * 24 * 30 * 2)],
            ])->select();

            S('ChartgetMarketTrendJson' . $market, $data);
        }

        $json_data = [];
        foreach ($data as $k => $v) {
            $json_data[$k][0] = $v['addtime'];
            $json_data[$k][1] = $v['price'];
        }

        exit(json_encode($json_data));
    }

    /**
     * k线
     */
    public function ordinary()
    {
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';

        $this->assign('market', $market);
        $this->display();
    }

    /**
     * K线数据.
     */
    public function getMarketOrdinaryJson()
    {
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';

        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        if (in_array($input['time'], $timearr)) {
            $time = $input['time'];
        } else {
            $time = 5;
        }
        $redis = $this->connectRedis();
        $key = 'kline:' . $market . ':' . $time;
        $list = $redis->zrevrange($key, 0, 99);

        $list = array_reverse($list);

        $json_data = [];
        foreach ($list as $v) {
            $json_data[] = json_decode($v, TRUE);
        }

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        exit(json_encode($json_data));
    }

    /**
     * 行情（k线）.
     */
    public function specialty()
    {
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';

        $this->assign('market', $market);
        $this->display();
    }

    /**
     * 行情k线数据.
     */
    public function getMarketSpecialtyJson()
    {
        $input = I('get.');
        $market = trim($input['market']) ? trim($input['market']) : 'bys_usdt';
        $timearr = [1, 3, 5, 10, 15, 30, 60, 120, 240, 360, 720, 1440, 10080];

        if (in_array($input['step'] / 60, $timearr)) {
            $time = $input['step'] / 60;
        } else {
            $time = 5;
        }
        $redis = $this->pconnectRedis();
        $key = 'kline:' . $market . ':' . $time;
        $count = $redis->ZCARD($key);
        $list = $redis->zrange($key, 0, $count);
        $json_data = [];
        foreach ($list as $v) {
            $json_data[] = json_decode($v, TRUE);
        }

        foreach ($json_data as $k => $v) {
            $data[$k][0] = $v[0];
            $data[$k][1] = 0;
            $data[$k][2] = 0;
            $data[$k][3] = $v[2];
            $data[$k][4] = $v[5];
            $data[$k][5] = $v[3];
            $data[$k][6] = $v[4];
            $data[$k][7] = $v[1];
        }

        exit(json_encode($data));
    }

    public function getSpecialtyTrades()
    {
        $input = I('get.');

        if (!$input['since']) {
            $tradeLog = M('TradeLog')->where(['market' => $input['market']])->order('id desc')->find();
            $json_data[] = ['tid' => $tradeLog['id'], 'date' => $tradeLog['addtime'], 'price' => $tradeLog['price'], 'amount' => $tradeLog['num'], 'trade_type' => $tradeLog['type'] == 1 ? 'bid' : 'ask'];
            exit(json_encode($json_data));
        } else {
            $tradeLog = M('TradeLog')->where([
                'market' => $input['market'],
                'id' => ['gt', $input['since']],
            ])->order('id desc')->select();
            $json_data = [];
            foreach ($tradeLog as $k => $v) {
                $json_data[] = ['tid' => $v['id'], 'date' => $v['addtime'], 'price' => $v['price'], 'amount' => $v['num'], 'trade_type' => $v['type'] == 1 ? 'bid' : 'ask'];
            }

            exit(json_encode($json_data));
        }
    }
}
