<?php

namespace Home\Controller;

class AjaxController extends HomeController
{
    //获取菜单
    public function getJsonMenu($ajax = 'json')
    {
        $data = S('getJsonMenu');

        if (!$data) {
            $markets = D('Market')->getOnlineMarkets();
            foreach ($markets as $k => $v) {
                $data[$k]['name'] = $v['name'];
                $data[$k]['img'] = $v['xnbimg'];
                $data[$k]['title'] = $v['title'];
            }

            S('getJsonMenu', $data);
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    //计算用户总资产
    public function allfinance($ajax = 'json')
    {
        if (!userid()) {
            return FALSE;
        }
        //查询合约总资产
        //$usdt = M('ContractUserCoinResult')->where(['user_id' => userid(), 'coin_name' => 'usdt'])->getField('total');
        $UserCoin = M('UserCoin')->where(['userid' => userid()])->find();
        //获取所有开放的币种
        $coins = M('Coin')->where(['status' => 1])->select();
        //美元
        $usdt = 0;
        foreach ($coins as $coin) {
            if ($coin['name'] == 'usdt') {
                $usdt = bcadd($usdt, bcadd($UserCoin['usdt'], $UserCoin['usdtd'], 8), 8);
            } else {
                //获取市场最新成交价
                $newPrice = M('Market')->where(['name' => $coin['name'] . '_usdt'])->getField('new_price');
                //换算成usdt
                $usdt = bcadd($usdt, bcmul(bcadd($UserCoin[$coin['name']], $UserCoin[$coin['name'] . 'd'], 8), $newPrice, 8), 8);
            }
        }
        $data = sprintf('%.2f', $usdt);
        //如果需要换算成人民币
        //$data = bcmul($usdt, getRate(), 2);

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 所有在线币种交易信息.
     */
    public function allcoin($ajax = 'json')
    {
        $markets = D('Market')->getOnlineMarkets();

        foreach ($markets as $k => $v) {
            $data[$k][0] = $v['title'];
            $data[$k][1] = round($v['new_price'], $v['price_round']);
            $data[$k][2] = round($v['buy_price'], $v['price_round']);
            $data[$k][3] = round($v['sell_price'], $v['price_round']);
            //$data[$k][4] = $this->sum_makert($k);
            $data[$k][4] = round(D('Market')->getVolumeByMarket($k));
            $data[$k][5] = '';
            $jyd = explode('_', $v['name'])[0];
            $cnum = M('Coin')->where(['name' => $jyd])->getField('cs_cl');
            $data[$k][6] = round($data[$k][1] * $cnum);
            $data[$k][7] = round($v['change'], 2);
            $data[$k][8] = $v['name'];
            $data[$k][9] = trim($v['xnbimg']);
            $data[$k][10] = '';
            if (!userid()) {
                $data[$k][11] = 0;
            } else {
                $data[$k][11] = M('sicon')->where(['userid' => userid(), 'market' => $v['name']])->getField('status');
            }
        }

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    //合约首页行情
    public function contractList()
    {
        $list = M('ContractMarket')->select();
        $data = [];
        foreach ($list as $k => $v) {
            $xnb = explode('_', $v['market'])[0];
            $rmb = explode('_', $v['market'])[1];
            $data[$k]['market'] = $v['market'];
            $data[$k]['title'] = strtoupper($xnb) . ' ' . strtoupper($xnb) . '/' . strtoupper($rmb);
            $data[$k]['img'] = D('Coin')->get_img($xnb);
            $data[$k]['new_price'] = round($v['new_price'], 2); //最新成交价
            $data[$k]['total'] = $v['total']; //总成交量
            $data[$k]['min_price'] = $v['min_price']; //24H最低价
            $data[$k]['max_price'] = $v['max_price']; //24H最高价
            $data[$k]['volume'] = $v['volume']; //24H成交量
            $data[$k]['change'] = round($v['change'], 2); //24H涨跌幅
        }

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

        exit(json_encode($data));
    }

    /**
     * 交易对成交总量（成交价*成交数）.
     */
    public function sum_makert($m)
    {
        $redis = $this->connectRedis();

        $json = $redis->hget('exp:coin:market:volume', $m);

        $arr = json_decode($json, TRUE);

        return $arr['volume'];
    }

    /**
     * 其他交易所行情数据.(假).
     */
    public function globals_market($ajax = 'json')
    {
        $global = M('Market_global')->order('id desc')->limit(20)->select();

        header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        if ($ajax) {
            exit(json_encode($global));
        } else {
            return $global;
        }
    }

    /**
     * 3日趋势线.
     */
    public function trends($ajax = 'json')
    {
        //$data = S('trends');

        //if (!$data) {
        $data = [];
        $markets = D('Market')->getOnlineMarkets();

        foreach ($markets as $k => $v) {
            $tendency = json_decode($v['tendency'], TRUE);

            $data[$k]['data'] = $tendency;
            $data[$k]['yprice'] = $v['new_price'];
        }

        //S('trends', $data);
        //}

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 获取虚拟币行情列表.
     */
    public function getJsonTop($market = NULL, $ajax = 'json')
    {
        //$data = S('getJsonTop' . $market);

        //if (!$data) {
        if ($market) {
            $markets = D('Market')->getOnlineMarkets();

            foreach ($markets as $k => $v) {
                $data['list'][$k]['name'] = $v['name'];
                $data['list'][$k]['img'] = $v['xnbimg'];
                $data['list'][$k]['title'] = $v['title'];
                $data['list'][$k]['new_price'] = round($v['new_price'], $v['price_round']);
            }

            $data['info']['img'] = $markets[$market]['xnbimg'];
            $data['info']['title'] = $markets[$market]['title'];
            $data['info']['new_price'] = $markets[$market]['new_price'];
            $data['info']['max_price'] = $markets[$market]['max_price'];
            $data['info']['min_price'] = $markets[$market]['min_price'];
            $data['info']['buy_price'] = $markets[$market]['buy_price'];
            $data['info']['sell_price'] = $markets[$market]['sell_price'];
            $data['info']['volume'] = $markets[$market]['volume'];
            $data['info']['change'] = $markets[$market]['change'];
            //S('getJsonTop' . $market, $data);
        }
        //}

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 获取成交记录.
     */
    public function getTradelog($market = NULL, $ajax = 'json')
    {
        //$data = S('getTradeLog'.$market);

        //if (!$data) {
        $tradeLog = M('TradeLog')->where(['market' => $market])->order('id desc')->limit(50)->select();

        //获取小数点
        $marketData = M('Market')->where(['name' => $market])->find();

        if ($tradeLog) {
            $data = [];
            foreach ($tradeLog as $k => $v) {
                $data['tradelog'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['tradelog'][$k]['type'] = $v['type'];
                $data['tradelog'][$k]['price'] = round($v['price'], $marketData['price_round']);
                $data['tradelog'][$k]['num'] = round($v['num'], $marketData['num_round']);
                $data['tradelog'][$k]['mum'] = round($v['mum'], 2);
            }
            //S('getTradeLog'.$market, $data);
        }
        //}

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 获取当前买十和卖十
     * @param null   $market
     * @param int    $trade_moshi
     * @param string $ajax
     * @return mixed
     */
    /*public function getDepth($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
        if (in_array($market, ['qtum_usdt','bts_usdt','ae_usdt','hc_usdt'])) {
            $data = $this->getDepthTest($market, $trade_moshi, $ajax);
        } else {
            $data = $this->getDepthBak($market, $trade_moshi, $ajax);
        }

        exit(json_encode($data));
    }*/

    public function getDepth($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
        if ($trade_moshi == 1) {
            $limt = 10;
        }
        if (($trade_moshi == 3) || ($trade_moshi == 4)) {
            $limt = 25;
        }
        $redis = $this->connectRedis();
        $buyKey = 'spot:depth:' . strtoupper($market) . ':buy';
        $sellKey = 'spot:depth:' . strtoupper($market) . ':sell';

        $length = $limt - 1;

        $buy = $redis->ZREVRANGE($buyKey, 0, $length);
        $sell = $redis->zRange($sellKey, 0, $length);

        //获取小数点
        $marketData = M('Market')->where(['name' => $market])->find();

        if ($buy) {
            foreach ($buy as $k => $v) {
                $arr = explode('@', $v);
                $data['depth']['buy'][$k] = [round($arr[1], $marketData['price_round']), round($arr[0], $marketData['num_round'])];
            }
        } else {
            $data['depth']['buy'] = '';
        }

        if ($sell) {
            $sell = array_reverse($sell);
            foreach ($sell as $k => $v) {
                $arr = explode('@', $v);
                $data['depth']['sell'][$k] = [round($arr[1], $marketData['price_round']), round($arr[0], $marketData['num_round'])];
            }
        } else {
            $data['depth']['sell'] = '';
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    //原深度方法，备用
    public function getDepthBak($market = NULL, $trade_moshi = 1, $ajax = 'json')
    {
        if ($trade_moshi == 1) {
            $limt = 10;
        }
        if (($trade_moshi == 3) || ($trade_moshi == 4)) {
            $limt = 25;
        }
        $redis = $this->connectRedis();
        $buyKey = 'exp:depth:' . strtoupper($market) . ':buy';
        $sellKey = 'exp:depth:' . strtoupper($market) . ':sell';
        $redisBuy = $redis->hGetAll($buyKey);
        $redisSell = $redis->hGetAll($sellKey);
        //处理redis数据
        $buyArr = $sellArr = $array = $arr = [];
        foreach ($redisBuy as $k => $v) {
            $array['price'] = $k;
            $array['nums'] = $v;
            $buyArr[] = $array;
        }
        foreach ($redisSell as $key => $val) {
            $arr['price'] = $key;
            $arr['nums'] = $val;
            $sellArr[] = $arr;
        }

        $mo = M();
        $buyModel = $mo->query('select id,price,sum(num-deal)as nums from btchanges_trade where status=0 and type=1 and market =\'' . $market . '\' group by price order by price desc limit ' . $limt . ';');
        $sellModel = array_reverse($mo->query('select id,price,sum(num-deal)as nums from btchanges_trade where status=0 and type=2 and market =\'' . $market . '\' group by price order by price asc limit ' . $limt . ';'));
        //去掉小数点
        foreach ($buyModel as &$v) {
            $v['price'] = floatval($v['price']);
        }
        foreach ($sellModel as &$vv) {
            $vv['price'] = floatval($vv['price']);
        }
        //合并数组
        $buyMerge = array_merge($buyArr, $buyModel);
        $sellMerage = array_merge($sellArr, $sellModel);
        //排序数组
        $buySort = array_sort($buyMerge, 'price', 'desc');
        $sellSort = array_sort($sellMerage, 'price', 'asc');
        //取前十个
        $buy = array_slice($buySort, 0, $limt, FALSE);
        $sellSlice = array_slice($sellSort, 0, $limt, FALSE);
        //重新排序卖
        $sell = array_sort($sellSlice, 'price', 'desc');
        $sell = array_slice($sell, 0, $limt, FALSE);

        //获取小数点
        $marketData = M('Market')->where(['name' => $market])->find();

        if ($buy) {
            foreach ($buy as $k => $v) {
                $data['depth']['buy'][$k] = [round($v['price'], $marketData['price_round']), round($v['nums'], $marketData['num_round'])];
            }
        } else {
            $data['depth']['buy'] = '';
        }

        if ($sell) {
            foreach ($sell as $k => $v) {
                $data['depth']['sell'][$k] = [round($v['price'], $marketData['price_round']), round($v['nums'], $marketData['num_round'])];
            }
        } else {
            $data['depth']['sell'] = '';
        }

        return $data;
        /*if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }*/
    }

    public function getEntrustAndUsercoin($market = NULL, $ajax = 'json')
    {
        if (!userid()) {
            return NULL;
        }

        $result = M()->query('select id,price,num,deal,mum,type,fee,status,addtime from btchanges_trade where status=0 and market=\'' . $market . '\' and userid=' . userid() . ' order by id desc limit 10;');

        $marketData = M('Market')->where(['name' => $market])->find();

        if ($result) {
            foreach ($result as $k => $v) {
                $data['entrust'][$k]['addtime'] = date('m-d H:i:s', $v['addtime']);
                $data['entrust'][$k]['type'] = $v['type'];
                $data['entrust'][$k]['price'] = round($v['price'], $marketData['price_round']);
                $data['entrust'][$k]['num'] = round($v['num'], $marketData['num_round']);
                $data['entrust'][$k]['deal'] = round($v['deal'], $marketData['num_round']);
                $data['entrust'][$k]['id'] = $v['id'];
            }
        } else {
            $data['entrust'] = NULL;
        }

        $userCoin = M('UserCoin')->where(['userid' => userid()])->find();

        if ($userCoin) {
            $xnb = explode('_', $market)[0];
            $rmb = explode('_', $market)[1];
            $data['usercoin']['xnb'] = floatval($userCoin[$xnb]);
            $data['usercoin']['xnbd'] = floatval($userCoin[$xnb . 'd']);
            $data['usercoin']['cny'] = floatval($userCoin[$rmb]);
            $data['usercoin']['cnyd'] = floatval($userCoin[$rmb . 'd']);
        } else {
            $data['usercoin'] = NULL;
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 聊天记录.
     * @param string $ajax
     * @return array|string
     */
    public function getChat($ajax = 'json')
    {
        $chat = (APP_DEBUG ? NULL : S('getChat'));

        if (!$chat) {
            $chat = M('Chat')->where(['status' => 1])->order('id desc')->limit(500)->select();
            S('getChat', $chat);
        }

        asort($chat);

        if ($chat) {
            foreach ($chat as $k => $v) {
                $data[] = [(int)$v['id'], $v['username'], $v['content']];
            }
        } else {
            $data = '';
        }

        if ($ajax) {
            exit(json_encode($data));
        } else {
            return $data;
        }
    }

    /**
     * 保存聊天信息.
     * @param $content
     */
    public function upChat($content)
    {
        if (!userid()) {
            $this->error('请先登录...');
        }

        $content = msubstr($content, 0, 250, 'utf-8', FALSE);

        if (!$content) {
            $this->error('请先输入内容');
        }

        if (time() < (session('chat' . userid()) + 10)) {
            $this->error('不能发送过快');
        }

        $id = M('Chat')->add(['userid' => userid(), 'username' => username(), 'content' => $content, 'addtime' => time(), 'status' => 1]);

        if ($id) {
            S('getChat', NULL);
            session('chat' . userid(), time());
            $this->success($id);
        } else {
            $this->error('发送失败');
        }
    }

    /**
     * 获取热门币种（4个）.
     */
    public function getHotCoin()
    {
        //根据总销量查询热门币种
        $coins = M('Market')->where(['status' => 1])->field('name,new_price,change')->select();
        //处理名称
        $data = [];
        foreach ($coins as $key => $v) {
            if (in_array($v['name'], ['btc_usdt', 'ltc_usdt', 'eth_usdt', 'eos_usdt'])) {
                $arr['name'] = strtoupper(explode('_', $v['name'])[0]) . '/' . strtoupper(explode('_', $v['name'])[1]);
                $arr['new_price'] = $v['new_price'];
                $arr['change'] = $v['change'];
                $data[] = $arr;
            }
        }

        //获取合约最新行情
       /* $array = ['btc_usdt', 'eth_usdt'];
        $data = [];
        foreach ($array as $v) {
            $market = M('ContractMarket')->where(['market' => $v])->find();
            $arr['name'] = strtoupper(explode('_', $v)[0]) . '/' . strtoupper(explode('_', $v)[1]);
            //期货最新成交价
            $arr['new_price'] = round($market['new_price'], 4);
            $arr['change'] = $market['change'];
            $data[] = $arr;
        }*/

        $this->ajaxReturn($data);
    }

    /**
     * 获取用户当前法币资产，header头部使用.
     * @return [type] [description]
     */
    public function getUserCoin()
    {
        if (!userid()) {
            $this->ajaxReturn(['status' => 0, 'msg' => '用户未登录']);
        }

        $userCoin = M('UserCoin')->where(['userid' => userid()])->field('usdt, usdtd')->find();

        if (count($userCoin) == 0) {
            $this->ajaxReturn(['status' => 0, 'msg' => '用户资产不存在']);
        }

        $data['usdt'] = $userCoin['usdt'];
        $data['usdtd'] = $userCoin['usdtd'];

        $this->ajaxReturn(['status' => 1, 'msg' => 'success', 'data' => $data]);
    }
}
