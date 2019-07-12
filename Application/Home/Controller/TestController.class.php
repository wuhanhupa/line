<?php

namespace Home\Controller;

use Common\Design\Contract\ContractSimulation;
use Common\Design\SetTrade\Collection as SetCollection;

class TestController extends HomeController
{
    public $usdtpay_cid = '6v7nRBnJw5J6yEdcNDwi7sBQpYCNZFlq';
    public $usdtpay_md5key = 'g0rPhIh3BvnyLcwIcGG42SMfk5oigvSr';

    public function index()
    {
        $url = 'http://we.fupaipay.com/admin/auth/login';
        $doc = new \DOMDocument();
        $doc->loadHTMLFile($url);
        $xpath = new \DOMXPath($doc);
        $inputs = $xpath->query("//input[@type='hidden']");
        foreach ($inputs as $input) {
            foreach ($input->attributes as $attr) {
                if ($attr->name == 'value') {
                    $_token = $attr->value;
                }
            }
        }
        $data['username'] = '13798005374';
        $data['password'] = '13798005374';
        $data['_token'] = $_token;
        var_dump($data);
        $res = curl_post_http($url, $data);
        var_dump($res);
    }

    //生成入金url
    public function usdtpay_MakePaymentUrl($request)
    {
        $request['sign'] = $this->usdtpay_Sign($request, $this->usdtpay_md5key);

        //return sprintf('http://api.usdtpay.io/payment/%s?%s', $this->usdtpay_cid, http_build_query($request));
        return sprintf('http://we.fupaipay.com/payment/%s?%s', $this->usdtpay_cid, http_build_query($request));
    }

    //生成签名方法
    public function usdtpay_Sign($request, $salt)
    {
        $signStr = $request['pickupUrl'];
        $signStr .= $request['receiveUrl'];
        $signStr .= $request['signType'];
        $signStr .= $request['orderNo'];
        $signStr .= $request['orderAmount'];
        $signStr .= $request['orderCurrency'];
        $signStr .= $request['customerId'];
        $signStr .= $salt;

        return md5($signStr);
    }

    //入金回调
    public function callback()
    {
        $orderNo = $_POST['orderNo']; //商户订单号
        $orderAmount = $_POST['orderAmount']; //订单金额
        $orderCurrency = $_POST['orderCurrency']; //订单币种， 固定值： USDT
        $paymentAmount = $_POST['paymentAmount']; //用户支付金额，单位：元，币种：人民币
        $transactionId = $_POST['transactionId']; //平台流水号
        $status = $_POST['status']; //状态说明，固定值：success
        $signType = $_POST['signType']; //签名算法，固定值：MD5
        $sign = $_POST['sign']; //参数校验签名
        //sign = md5( signType + orderNo + orderAmount + orderCurrency + transactionId + sta tus + md5Key );

        //验证签名是否伪造
        $str = md5($signType . $orderNo . $orderAmount . $orderCurrency . $transactionId . $status . $this->usdtpay_md5key);
        if ($str != $sign) {
            echo '签名错误';
        } else {
            echo 'success';
        }
    }

    public function test()
    {
        $data['channel_id'] = $this->usdtpay_cid;
        $data['currency'] = 'CNY';
        $data['cards'] = json_encode([["name" => 'king', 'cardno' => '23432', 'bankname' => 'gg', 'amount' => 10]]);
        $data['channel_remark'] = 'test';
        $data['sign_type'] = 'MD5';
        $sign = $this->getSign($data, $this->usdtpay_md5key);
        $data['sign'] = $sign;
        $url = 'http://we.fupaipay.com/api/withdrawal';

        $res = curl_post_http($url, $data);
        var_dump($res);
    }

    //出金签名
    public function getSign($data, $key)
    {
        ksort($data);
        $data['key'] = $key;
        $blankStr = http_build_query($data);
        $sign = md5($blankStr);

        return strtoupper($sign);
    }

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
     * 获取币种代码
     */
    public function getSymbol($market)
    {
        return M('Bz')->where(['market' => $market])->getField('number');
    }

    /**
     * 根据代码获取交易对.
     */
    public function getMarket($symbol)
    {
        return M('Bz')->where(['number' => $symbol])->getField('market');
    }

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
                    $b = $temp[$x - 1];
                }

                $rs[] = [$na, $b];
            }
            unset($temp);
            dump($rs);
        }
    }

    public function testKline()
    {
       $data = '{
	"code": 0,
	"msg": "注册成功！",
	"data": {
		"itme": {
			"code": "N225",
			"type": "day"
		},
		"list": {
			"itme": {
				"count": "544"
			},
			"item": [{
				"itme": {
					"date": "20131227",
					"current": "16075.27",
					"open": "16229.43",
					"preclose": "16174.44",
					"change": "-99.17",
					"vol": "115314",
					"close": "16075.27",
					"high": "16232.69",
					"low": "16056.73"
				}
			}, {
				"itme": {
					"date": "20131226",
					"current": "16174.44",
					"open": "16087.07",
					"change": "164.45",
					"vol": "170235",
					"close": "16174.44",
					"high": "16186.04",
					"low": "16082.28"
				}
			}, {
				"itme": {
					"date": "20131225",
					"current": "16009.99",
					"open": "15861.13",
					"change": "120.66",
					"vol": "147360",
					"close": "16009.99",
					"high": "16010.54",
					"low": "15853.51"
				}
			}, {
				"itme": {
					"date": "20131224",
					"current": "15889.33",
					"open": "15955.9",
					"change": "18.91",
					"vol": "162956",
					"close": "15889.33",
					"high": "16029.65",
					"low": "15849"
				}
			}, {
				"itme": {
					"date": "20131220",
					"current": "15870.42",
					"open": "15790.69",
					"change": "11.2",
					"vol": "165044",
					"close": "15870.42",
					"high": "15870.42",
					"low": "15755.36"
				}
			}, {
				"itme": {
					"date": "20131219",
					"current": "15859.22",
					"open": "15809.43",
					"change": "271.42",
					"vol": "190274",
					"close": "15859.22",
					"high": "15891.82",
					"low": "15798.85"
				}
			}, {
				"itme": {
					"date": "20131218",
					"current": "15587.8",
					"open": "15273.24",
					"change": "309.17",
					"vol": "175242",
					"close": "15587.8",
					"high": "15588.42",
					"low": "15268.18"
				}
			}, {
				"itme": {
					"date": "20131217",
					"current": "15278.63",
					"open": "15290.22",
					"change": "125.72",
					"vol": "120362",
					"close": "15278.63",
					"high": "15322.13",
					"low": "15248.42"
				}
			}, {
				"itme": {
					"date": "20131216",
					"current": "15152.91",
					"open": "15408.35",
					"change": "-250.2",
					"vol": "139628",
					"close": "15152.91",
					"high": "15408.35",
					"low": "15146.13"
				}
			}, {
				"itme": {
					"date": "20131213",
					"current": "15403.11",
					"open": "15316.89",
					"change": "61.29",
					"vol": "246243",
					"close": "15403.11",
					"high": "15532.94",
					"low": "15251.45"
				}
			}, {
				"itme": {
					"date": "20131212",
					"current": "15341.82",
					"open": "15377.32",
					"change": "-173.24",
					"vol": "132271",
					"close": "15341.82",
					"high": "15392.66",
					"low": "15255.36"
				}
			}, {
				"itme": {
					"date": "20131211",
					"current": "15515.06",
					"open": "15509.93",
					"change": "-96.25",
					"vol": "129237",
					"close": "15515.06",
					"high": "15562.3",
					"low": "15386.11"
				}
			}, {
				"itme": {
					"date": "20131210",
					"current": "15611.31",
					"open": "15633.97",
					"change": "-38.9",
					"vol": "127197",
					"close": "15611.31",
					"high": "15633.97",
					"low": "15562.06"
				}
			}, {
				"itme": {
					"date": "20131209",
					"current": "15650.21",
					"open": "15556.6",
					"change": "350.35",
					"vol": "136897",
					"close": "15650.21",
					"high": "15650.21",
					"low": "15547.29"
				}
			}, {
				"itme": {
					"date": "20131206",
					"current": "15299.86",
					"open": "15112.54",
					"change": "122.37",
					"vol": "135325",
					"close": "15299.86",
					"high": "15327.37",
					"low": "15112.54"
				}
			}, {
				"itme": {
					"date": "20131205",
					"current": "15177.49",
					"open": "15354.53",
					"change": "-230.45",
					"vol": "150723",
					"close": "15177.49",
					"high": "15430.2",
					"low": "15139.12"
				}
			}, {
				"itme": {
					"date": "20131204",
					"current": "15407.94",
					"open": "15520.2",
					"change": "-341.72",
					"vol": "158990",
					"close": "15407.94",
					"high": "15579.36",
					"low": "15326.06"
				}
			}, {
				"itme": {
					"date": "20131203",
					"current": "15749.66",
					"open": "15747.54",
					"change": "94.59",
					"vol": "167169",
					"close": "15749.66",
					"high": "15794.15",
					"low": "15661.9"
				}
			}, {
				"itme": {
					"date": "20131202",
					"current": "15655.07",
					"open": "15659.74",
					"change": "-6.8",
					"vol": "143189",
					"close": "15655.07",
					"high": "15703.02",
					"low": "15579.54"
				}
			}, {
				"itme": {
					"date": "20131129",
					"current": "15661.87",
					"open": "15661.43",
					"change": "-65.25",
					"vol": "158273",
					"close": "15661.87",
					"high": "15727.96",
					"low": "15507.17"
				}
			}, {
				"itme": {
					"date": "20131128",
					"current": "15727.12",
					"open": "15622.19",
					"change": "277.49",
					"vol": "153986",
					"close": "15727.12",
					"high": "15729.09",
					"low": "15605.73"
				}
			}, {
				"itme": {
					"date": "20131127",
					"current": "15449.63",
					"open": "15414.52",
					"change": "-65.61",
					"vol": "144653",
					"close": "15449.63",
					"high": "15512.76",
					"low": "15414.52"
				}
			}, {
				"itme": {
					"date": "20131126",
					"current": "15515.24",
					"open": "15501.95",
					"change": "-103.89",
					"vol": "156747",
					"close": "15515.24",
					"high": "15577.97",
					"low": "15460.97"
				}
			}, {
				"itme": {
					"date": "20131125",
					"current": "15619.13",
					"open": "15504.78",
					"change": "237.41",
					"vol": "176359",
					"close": "15619.13",
					"high": "15619.13",
					"low": "15469.87"
				}
			}, {
				"itme": {
					"date": "20131122",
					"current": "15381.72",
					"open": "15513.45",
					"change": "16.12",
					"vol": "211268",
					"close": "15381.72",
					"high": "15579.39",
					"low": "15307.44"
				}
			}, {
				"itme": {
					"date": "20131121",
					"current": "15365.6",
					"open": "15176.65",
					"change": "289.52",
					"vol": "168189",
					"close": "15365.6",
					"high": "15377",
					"low": "15168.47"
				}
			}, {
				"itme": {
					"date": "20131120",
					"current": "15076.08",
					"open": "15176.35",
					"change": "-50.48",
					"vol": "142496",
					"close": "15076.08",
					"high": "15209.67",
					"low": "15069.98"
				}
			}, {
				"itme": {
					"date": "20131119",
					"current": "15126.56",
					"open": "15096.63",
					"change": "-37.74",
					"vol": "130226",
					"close": "15126.56",
					"high": "15163.06",
					"low": "15020.33"
				}
			}, {
				"itme": {
					"date": "20131118",
					"current": "15164.3",
					"open": "15253.24",
					"change": "-1.62",
					"vol": "180139",
					"close": "15164.3",
					"high": "15273.61",
					"low": "15106.82"
				}
			}, {
				"itme": {
					"date": "20131115",
					"current": "15165.92",
					"open": "15034.33",
					"change": "289.51",
					"vol": "212603",
					"close": "15165.92",
					"high": "15203.11",
					"low": "14994.7"
				}
			}, {
				"itme": {
					"date": "20131114",
					"current": "14876.41",
					"open": "14665.75",
					"change": "309.25",
					"vol": "190127",
					"close": "14876.41",
					"high": "14966.43",
					"low": "14665.75"
				}
			}, {
				"itme": {
					"date": "20131113",
					"current": "14567.16",
					"open": "14527.97",
					"change": "-21.52",
					"vol": "170203",
					"close": "14567.16",
					"high": "14599.53",
					"low": "14490.92"
				}
			}, {
				"itme": {
					"date": "20131112",
					"current": "14588.68",
					"open": "14289.87",
					"change": "318.84",
					"vol": "157594",
					"close": "14588.68",
					"high": "14588.68",
					"low": "14278.21"
				}
			}, {
				"itme": {
					"date": "20131111",
					"current": "14269.84",
					"open": "14271.48",
					"change": "183.04",
					"vol": "113525",
					"close": "14269.84",
					"high": "14304.29",
					"low": "14208.13"
				}
			}, {
				"itme": {
					"date": "20131108",
					"current": "14086.8",
					"open": "14026.17",
					"change": "-141.64",
					"vol": "117829",
					"close": "14086.8",
					"high": "14122.28",
					"low": "14026.17"
				}
			}, {
				"itme": {
					"date": "20131107",
					"current": "14228.44",
					"open": "14355.56",
					"change": "-108.87",
					"vol": "111624",
					"close": "14228.44",
					"high": "14371.56",
					"low": "14222.03"
				}
			}, {
				"itme": {
					"date": "20131106",
					"current": "14337.31",
					"open": "14155.34",
					"change": "111.94",
					"vol": "142185",
					"close": "14337.31",
					"high": "14407.69",
					"low": "14130.86"
				}
			}, {
				"itme": {
					"date": "20131105",
					"current": "14225.37",
					"open": "14319.75",
					"change": "23.8",
					"vol": "167111",
					"close": "14225.37",
					"high": "14323.24",
					"low": "14141.82"
				}
			}, {
				"itme": {
					"date": "20131101",
					"current": "14201.57",
					"open": "14403.07",
					"change": "-126.37",
					"vol": "170815",
					"close": "14201.57",
					"high": "14411.05",
					"low": "14126.41"
				}
			}, {
				"itme": {
					"date": "20131031",
					"current": "14327.94",
					"open": "14474.01",
					"change": "-174.41",
					"vol": "158446",
					"close": "14327.94",
					"high": "14516.08",
					"low": "14324.17"
				}
			}, {
				"itme": {
					"date": "20131030",
					"current": "14502.35",
					"open": "14464.67",
					"change": "176.37",
					"vol": "156761",
					"close": "14502.35",
					"high": "14526.88",
					"low": "14425.58"
				}
			}, {
				"itme": {
					"date": "20131029",
					"current": "14325.98",
					"open": "14288.72",
					"change": "-70.06",
					"vol": "135627",
					"close": "14325.98",
					"high": "14395.96",
					"low": "14224.59"
				}
			}, {
				"itme": {
					"date": "20131028",
					"current": "14396.04",
					"open": "14261.65",
					"change": "307.85",
					"vol": "109470",
					"close": "14396.04",
					"high": "14400.32",
					"low": "14194.42"
				}
			}, {
				"itme": {
					"date": "20131025",
					"current": "14088.19",
					"open": "14439.14",
					"change": "-398.22",
					"vol": "139009",
					"close": "14088.19",
					"high": "14442.12",
					"low": "14088.19"
				}
			}, {
				"itme": {
					"date": "20131024",
					"current": "14486.41",
					"open": "14344.74",
					"change": "60.36",
					"vol": "144885",
					"close": "14486.41",
					"high": "14499.52",
					"low": "14273.71"
				}
			}, {
				"itme": {
					"date": "20131023",
					"current": "14426.05",
					"open": "14784.41",
					"change": "-287.2",
					"vol": "153457",
					"close": "14426.05",
					"high": "14799.28",
					"low": "14426.05"
				}
			}, {
				"itme": {
					"date": "20131022",
					"current": "14713.25",
					"open": "14677.08",
					"change": "19.68",
					"vol": "99209",
					"close": "14713.25",
					"high": "14747.77",
					"low": "14641.78"
				}
			}, {
				"itme": {
					"date": "20131021",
					"current": "14693.57",
					"open": "14624.03",
					"change": "132.03",
					"vol": "99064",
					"close": "14693.57",
					"high": "14727.85",
					"low": "14624.03"
				}
			}, {
				"itme": {
					"date": "20131018",
					"current": "14561.54",
					"open": "14589.6",
					"change": "-24.97",
					"vol": "120053",
					"close": "14561.54",
					"high": "14610.09",
					"low": "14503.09"
				}
			}, {
				"itme": {
					"date": "20131017",
					"current": "14586.51",
					"open": "14640.08",
					"change": "119.37",
					"vol": "135619",
					"close": "14586.51",
					"high": "14664.22",
					"low": "14492.67"
				}
			}, {
				"itme": {
					"date": "20131016",
					"current": "14467.14",
					"open": "14433.64",
					"change": "25.6",
					"vol": "116190",
					"close": "14467.14",
					"high": "14493.67",
					"low": "14417.61"
				}
			}, {
				"itme": {
					"date": "20131015",
					"current": "14441.54",
					"open": "14510.27",
					"change": "36.8",
					"vol": "125580",
					"close": "14441.54",
					"high": "14510.37",
					"low": "14415.76"
				}
			}, {
				"itme": {
					"date": "20131011",
					"current": "14404.74",
					"open": "14376.89",
					"change": "210.03",
					"vol": "169923",
					"close": "14404.74",
					"high": "14447.87",
					"low": "14320.3"
				}
			}, {
				"itme": {
					"date": "20131010",
					"current": "14194.71",
					"open": "14097.62",
					"change": "156.87",
					"vol": "143205",
					"close": "14194.71",
					"high": "14200.31",
					"low": "14077.03"
				}
			}, {
				"itme": {
					"date": "20131009",
					"current": "14037.84",
					"open": "13789.89",
					"change": "143.23",
					"vol": "162095",
					"close": "14037.84",
					"high": "14037.84",
					"low": "13751.85"
				}
			}, {
				"itme": {
					"date": "20131008",
					"current": "13894.61",
					"open": "13794.74",
					"change": "41.29",
					"vol": "163682",
					"close": "13894.61",
					"high": "13929.64",
					"low": "13748.94"
				}
			}, {
				"itme": {
					"date": "20131007",
					"current": "13853.32",
					"open": "14057.79",
					"change": "-170.99",
					"vol": "146097",
					"close": "13853.32",
					"high": "14073.23",
					"low": "13841.93"
				}
			}, {
				"itme": {
					"date": "20131004",
					"current": "14024.31",
					"open": "14029.73",
					"change": "-132.94",
					"vol": "145325",
					"close": "14024.31",
					"high": "14149.77",
					"low": "13944.27"
				}
			}, {
				"itme": {
					"date": "20131003",
					"current": "14157.25",
					"open": "14140.11",
					"change": "-13.24",
					"vol": "145291",
					"close": "14157.25",
					"high": "14219.89",
					"low": "14082.31"
				}
			}, {
				"itme": {
					"date": "20131002",
					"current": "14170.49",
					"open": "14492.47",
					"change": "-314.23",
					"vol": "173527",
					"close": "14170.49",
					"high": "14569.2",
					"low": "14114.54"
				}
			}, {
				"itme": {
					"date": "20131001",
					"current": "14484.72",
					"open": "14517.98",
					"change": "28.92",
					"vol": "152950",
					"close": "14484.72",
					"high": "14642.97",
					"low": "14471.7"
				}
			}, {
				"itme": {
					"date": "20130930",
					"current": "14455.8",
					"open": "14530.62",
					"change": "-304.27",
					"vol": "157293",
					"close": "14455.8",
					"high": "14619.24",
					"low": "14425.82"
				}
			}, {
				"itme": {
					"date": "20130927",
					"current": "14760.07",
					"open": "14803.99",
					"change": "-39.05",
					"vol": "162124",
					"close": "14760.07",
					"high": "14817.5",
					"low": "14699.21"
				}
			}, {
				"itme": {
					"date": "20130926",
					"current": "14799.12",
					"open": "14553.06",
					"change": "178.59",
					"vol": "156648",
					"close": "14799.12",
					"high": "14799.12",
					"low": "14410.52"
				}
			}, {
				"itme": {
					"date": "20130925",
					"current": "14620.53",
					"open": "14713.03",
					"change": "-112.08",
					"vol": "151019",
					"close": "14620.53",
					"high": "14737.98",
					"low": "14620.53"
				}
			}, {
				"itme": {
					"date": "20130924",
					"current": "14732.61",
					"open": "14626.04",
					"change": "-9.81",
					"vol": "114949",
					"close": "14732.61",
					"high": "14767.72",
					"low": "14607.27"
				}
			}, {
				"itme": {
					"date": "20130920",
					"current": "14742.42",
					"open": "14801.64",
					"change": "-23.76",
					"vol": "162476",
					"close": "14742.42",
					"high": "14816.65",
					"low": "14702.25"
				}
			}, {
				"itme": {
					"date": "20130919",
					"current": "14766.18",
					"open": "14680.4",
					"change": "260.82",
					"vol": "174626",
					"close": "14766.18",
					"high": "14766.18",
					"low": "14581.79"
				}
			}, {
				"itme": {
					"date": "20130918",
					"current": "14505.36",
					"open": "14411.55",
					"change": "193.69",
					"vol": "155852",
					"close": "14505.36",
					"high": "14625.97",
					"low": "14396.41"
				}
			}, {
				"itme": {
					"date": "20130917",
					"current": "14311.67",
					"open": "14456.99",
					"change": "-93",
					"vol": "121265",
					"close": "14311.67",
					"high": "14474.53",
					"low": "14311.67"
				}
			}, {
				"itme": {
					"date": "20130913",
					"current": "14404.67",
					"open": "14316.7",
					"change": "17.4",
					"vol": "180668",
					"close": "14404.67",
					"high": "14439.93",
					"low": "14233.12"
				}
			}, {
				"itme": {
					"date": "20130912",
					"current": "14387.27",
					"open": "14397.9",
					"change": "-37.8",
					"vol": "118414",
					"close": "14387.27",
					"high": "14455.37",
					"low": "14321.57"
				}
			}, {
				"itme": {
					"date": "20130911",
					"current": "14425.07",
					"open": "14511.74",
					"change": "1.71",
					"vol": "169995",
					"close": "14425.07",
					"high": "14561.46",
					"low": "14422.72"
				}
			}, {
				"itme": {
					"date": "20130910",
					"current": "14423.36",
					"open": "14318.72",
					"change": "218.13",
					"vol": "209898",
					"close": "14423.36",
					"high": "14441.81",
					"low": "14296.78"
				}
			}, {
				"itme": {
					"date": "20130909",
					"current": "14205.23",
					"open": "14141.67",
					"change": "344.42",
					"vol": "173546",
					"close": "14205.23",
					"high": "14251.46",
					"low": "14117.68"
				}
			}, {
				"itme": {
					"date": "20130906",
					"current": "13860.81",
					"open": "14088.41",
					"change": "-204.01",
					"vol": "139861",
					"close": "13860.81",
					"high": "14099.13",
					"low": "13834.52"
				}
			}, {
				"itme": {
					"date": "20130905",
					"current": "14064.82",
					"open": "14140.2",
					"change": "10.95",
					"vol": "146622",
					"close": "14064.82",
					"high": "14156.5",
					"low": "13981.52"
				}
			}, {
				"itme": {
					"date": "20130904",
					"current": "14053.87",
					"open": "13875.17",
					"change": "75.43",
					"vol": "138420",
					"close": "14053.87",
					"high": "14056.88",
					"low": "13843.61"
				}
			}, {
				"itme": {
					"date": "20130903",
					"current": "13978.44",
					"open": "13748.68",
					"change": "405.52",
					"vol": "162430",
					"close": "13978.44",
					"high": "13978.44",
					"low": "13748.68"
				}
			}, {
				"itme": {
					"date": "20130902",
					"current": "13572.92",
					"open": "13438.07",
					"change": "184.06",
					"vol": "112595",
					"close": "13572.92",
					"high": "13613.48",
					"low": "13407.53"
				}
			}, {
				"itme": {
					"date": "20130830",
					"current": "13388.86",
					"open": "13573.24",
					"change": "-70.85",
					"vol": "156697",
					"close": "13388.86",
					"high": "13615.98",
					"low": "13335.91"
				}
			}, {
				"itme": {
					"date": "20130829",
					"current": "13459.71",
					"open": "13382.95",
					"change": "121.25",
					"vol": "121435",
					"close": "13459.71",
					"high": "13463.14",
					"low": "13364.82"
				}
			}, {
				"itme": {
					"date": "20130828",
					"current": "13338.46",
					"open": "13285.03",
					"change": "-203.91",
					"vol": "132813",
					"close": "13338.46",
					"high": "13392.57",
					"low": "13188.14"
				}
			}, {
				"itme": {
					"date": "20130827",
					"current": "13542.37",
					"open": "13551.75",
					"change": "-93.91",
					"vol": "116778",
					"close": "13542.37",
					"high": "13678.79",
					"low": "13517.1"
				}
			}, {
				"itme": {
					"date": "20130826",
					"current": "13636.28",
					"open": "13719.56",
					"change": "-24.27",
					"vol": "104111",
					"close": "13636.28",
					"high": "13741.49",
					"low": "13586.84"
				}
			}, {
				"itme": {
					"date": "20130823",
					"current": "13660.55",
					"open": "13583.76",
					"change": "295.38",
					"vol": "164245",
					"close": "13660.55",
					"high": "13774.66",
					"low": "13575.4"
				}
			}, {
				"itme": {
					"date": "20130822",
					"current": "13365.17",
					"open": "13314.05",
					"change": "-59.16",
					"vol": "133207",
					"close": "13365.17",
					"high": "13447.33",
					"low": "13238.73"
				}
			}, {
				"itme": {
					"date": "20130821",
					"current": "13424.33",
					"open": "13431.28",
					"change": "27.95",
					"vol": "148264",
					"close": "13424.33",
					"high": "13499.99",
					"low": "13250.36"
				}
			}, {
				"itme": {
					"date": "20130820",
					"current": "13396.38",
					"open": "13632.96",
					"change": "-361.75",
					"vol": "138033",
					"close": "13396.38",
					"high": "13730.09",
					"low": "13383.18"
				}
			}, {
				"itme": {
					"date": "20130819",
					"current": "13758.13",
					"open": "13669.74",
					"change": "108.02",
					"vol": "96942",
					"close": "13758.13",
					"high": "13758.13",
					"low": "13589.78"
				}
			}, {
				"itme": {
					"date": "20130816",
					"current": "13650.11",
					"open": "13532.61",
					"change": "-102.83",
					"vol": "129556",
					"close": "13650.11",
					"high": "13739.52",
					"low": "13532.61"
				}
			}, {
				"itme": {
					"date": "20130815",
					"current": "13752.94",
					"open": "13845.64",
					"change": "-297.22",
					"vol": "137815",
					"close": "13752.94",
					"high": "13981.16",
					"low": "13711.12"
				}
			}, {
				"itme": {
					"date": "20130814",
					"current": "14050.16",
					"open": "13936.74",
					"change": "183.16",
					"vol": "152594",
					"close": "14050.16",
					"high": "14050.16",
					"low": "13747.18"
				}
			}, {
				"itme": {
					"date": "20130813",
					"current": "13867",
					"open": "13696.36",
					"change": "347.57",
					"vol": "126114",
					"close": "13867",
					"high": "13867",
					"low": "13689.49"
				}
			}, {
				"itme": {
					"date": "20130812",
					"current": "13519.43",
					"open": "13469.7",
					"change": "-95.76",
					"vol": "117725",
					"close": "13519.43",
					"high": "13658.86",
					"low": "13430.64"
				}
			}, {
				"itme": {
					"date": "20130809",
					"current": "13615.19",
					"open": "13673.49",
					"change": "9.63",
					"vol": "159146",
					"close": "13615.19",
					"high": "13754.96",
					"low": "13527.81"
				}
			}, {
				"itme": {
					"date": "20130808",
					"current": "13605.56",
					"open": "13779.47",
					"change": "-219.38",
					"vol": "167697",
					"close": "13605.56",
					"high": "14031.14",
					"low": "13556.65"
				}
			}, {
				"itme": {
					"date": "20130807",
					"current": "13824.94",
					"open": "14155.95",
					"change": "-576.12",
					"vol": "178500",
					"close": "13824.94",
					"high": "14164.7",
					"low": "13824.94"
				}
			}, {
				"itme": {
					"date": "20130806",
					"current": "14401.06",
					"open": "14237.1",
					"change": "143.02",
					"vol": "153955",
					"close": "14401.06",
					"high": "14401.06",
					"low": "14031.61"
				}
			}, {
				"itme": {
					"date": "20130805",
					"current": "14258.04",
					"open": "14318.21",
					"change": "-208.12",
					"vol": "134070",
					"close": "14258.04",
					"high": "14370.98",
					"low": "14225.5"
				}
			}, {
				"itme": {
					"date": "20130802",
					"current": "14466.16",
					"open": "14178.66",
					"change": "460.39",
					"vol": "193493",
					"close": "14466.16",
					"high": "14466.16",
					"low": "14146.92"
				}
			}, {
				"itme": {
					"date": "20130801",
					"current": "14005.77",
					"open": "13674.5",
					"change": "337.45",
					"vol": "195683",
					"close": "14005.77",
					"high": "14005.77",
					"low": "13645.61"
				}
			}, {
				"itme": {
					"date": "20130731",
					"current": "13668.32",
					"open": "13733.55",
					"change": "-201.5",
					"vol": "186496",
					"close": "13668.32",
					"high": "13836.03",
					"low": "13644.21"
				}
			}, {
				"itme": {
					"date": "20130730",
					"current": "13869.82",
					"open": "13634.2",
					"change": "208.69",
					"vol": "183399",
					"close": "13869.82",
					"high": "13909.45",
					"low": "13613.78"
				}
			}, {
				"itme": {
					"date": "20130729",
					"current": "13661.13",
					"open": "13899.27",
					"change": "-468.85",
					"vol": "184659",
					"close": "13661.13",
					"high": "13953.85",
					"low": "13661.13"
				}
			}, {
				"itme": {
					"date": "20130726",
					"current": "14129.98",
					"open": "14339.39",
					"change": "-432.95",
					"vol": "196052",
					"close": "14129.98",
					"high": "14376.01",
					"low": "14114.52"
				}
			}, {
				"itme": {
					"date": "20130725",
					"current": "14562.93",
					"open": "14747.21",
					"change": "-168.35",
					"vol": "158216",
					"close": "14562.93",
					"high": "14748.77",
					"low": "14533.21"
				}
			}, {
				"itme": {
					"date": "20130724",
					"current": "14731.28",
					"open": "14719.64",
					"change": "-47.23",
					"vol": "143027",
					"close": "14731.28",
					"high": "14751.87",
					"low": "14630.68"
				}
			}, {
				"itme": {
					"date": "20130723",
					"current": "14778.51",
					"open": "14555.36",
					"change": "120.47",
					"vol": "163069",
					"close": "14778.51",
					"high": "14820.18",
					"low": "14549.06"
				}
			}, {
				"itme": {
					"date": "20130722",
					"current": "14658.04",
					"open": "14770.02",
					"change": "68.13",
					"vol": "177468",
					"close": "14658.04",
					"high": "14770.02",
					"low": "14514.29"
				}
			}, {
				"itme": {
					"date": "20130719",
					"current": "14589.91",
					"open": "14909.76",
					"change": "-218.59",
					"vol": "269392",
					"close": "14589.91",
					"high": "14953.29",
					"low": "14413.28"
				}
			}, {
				"itme": {
					"date": "20130718",
					"current": "14808.5",
					"open": "14645.25",
					"change": "193.46",
					"vol": "191294",
					"close": "14808.5",
					"high": "14827.73",
					"low": "14645.25"
				}
			}, {
				"itme": {
					"date": "20130717",
					"current": "14615.04",
					"open": "14491.8",
					"change": "15.92",
					"vol": "247676",
					"close": "14615.04",
					"high": "14615.04",
					"low": "14460.56"
				}
			}, {
				"itme": {
					"date": "20130716",
					"current": "14599.12",
					"open": "14594.88",
					"change": "92.87",
					"vol": "186069",
					"close": "14599.12",
					"high": "14638.8",
					"low": "14550.89"
				}
			}, {
				"itme": {
					"date": "20130712",
					"current": "14506.25",
					"open": "14475.17",
					"change": "33.67",
					"vol": "184907",
					"close": "14506.25",
					"high": "14574.17",
					"low": "14417.3"
				}
			}, {
				"itme": {
					"date": "20130711",
					"current": "14472.58",
					"open": "14275.26",
					"change": "55.98",
					"vol": "167488",
					"close": "14472.58",
					"high": "14496.67",
					"low": "14275.26"
				}
			}, {
				"itme": {
					"date": "20130710",
					"current": "14416.6",
					"open": "14464.82",
					"change": "-56.3",
					"vol": "178174",
					"close": "14416.6",
					"high": "14555.33",
					"low": "14287.69"
				}
			}, {
				"itme": {
					"date": "20130709",
					"current": "14472.9",
					"open": "14295.44",
					"change": "363.56",
					"vol": "197560",
					"close": "14472.9",
					"high": "14472.9",
					"low": "14186.03"
				}
			}, {
				"itme": {
					"date": "20130708",
					"current": "14109.34",
					"open": "14491.07",
					"change": "-200.63",
					"vol": "220494",
					"close": "14109.34",
					"high": "14497.65",
					"low": "14109.34"
				}
			}, {
				"itme": {
					"date": "20130705",
					"current": "14309.97",
					"open": "14150.85",
					"change": "291.04",
					"vol": "179370",
					"close": "14309.97",
					"high": "14309.97",
					"low": "14149.5"
				}
			}, {
				"itme": {
					"date": "20130704",
					"current": "14018.93",
					"open": "13970.27",
					"change": "-36.63",
					"vol": "143585",
					"close": "14018.93",
					"high": "14093.02",
					"low": "13962.3"
				}
			}, {
				"itme": {
					"date": "20130703",
					"current": "14055.56",
					"open": "14149.99",
					"change": "-43.18",
					"vol": "200622",
					"close": "14055.56",
					"high": "14164.77",
					"low": "13984.08"
				}
			}, {
				"itme": {
					"date": "20130702",
					"current": "14098.74",
					"open": "13969.15",
					"change": "246.24",
					"vol": "214129",
					"close": "14098.74",
					"high": "14098.74",
					"low": "13898.54"
				}
			}, {
				"itme": {
					"date": "20130701",
					"current": "13852.5",
					"open": "13746.72",
					"change": "175.18",
					"vol": "172266",
					"close": "13852.5",
					"high": "13862.71",
					"low": "13562.7"
				}
			}, {
				"itme": {
					"date": "20130628",
					"current": "13677.32",
					"open": "13383.92",
					"change": "463.77",
					"vol": "234419",
					"close": "13677.32",
					"high": "13724.44",
					"low": "13354.7"
				}
			}, {
				"itme": {
					"date": "20130627",
					"current": "13213.55",
					"open": "12968.72",
					"change": "379.54",
					"vol": "182423",
					"close": "13213.55",
					"high": "13213.55",
					"low": "12873.5"
				}
			}, {
				"itme": {
					"date": "20130626",
					"current": "12834.01",
					"open": "13152.75",
					"change": "-135.33",
					"vol": "173369",
					"close": "12834.01",
					"high": "13189.84",
					"low": "12826.51"
				}
			}, {
				"itme": {
					"date": "20130625",
					"current": "12969.34",
					"open": "13081.62",
					"change": "-93.44",
					"vol": "199154",
					"close": "12969.34",
					"high": "13234.89",
					"low": "12758.22"
				}
			}, {
				"itme": {
					"date": "20130624",
					"current": "13062.78",
					"open": "13417.54",
					"change": "-167.35",
					"vol": "162527",
					"close": "13062.78",
					"high": "13426.13",
					"low": "13026.23"
				}
			}, {
				"itme": {
					"date": "20130621",
					"current": "13230.13",
					"open": "12787.87",
					"change": "215.55",
					"vol": "240984",
					"close": "13230.13",
					"high": "13330.35",
					"low": "12702.67"
				}
			}, {
				"itme": {
					"date": "20130620",
					"current": "13014.58",
					"open": "13101.85",
					"change": "-230.64",
					"vol": "200684",
					"close": "13014.58",
					"high": "13190.82",
					"low": "12966.41"
				}
			}, {
				"itme": {
					"date": "20130619",
					"current": "13245.22",
					"open": "13233.08",
					"change": "237.94",
					"vol": "201912",
					"close": "13245.22",
					"high": "13296.62",
					"low": "13107.65"
				}
			}, {
				"itme": {
					"date": "20130618",
					"current": "13007.28",
					"open": "13015.15",
					"change": "-25.84",
					"vol": "158739",
					"close": "13007.28",
					"high": "13139.48",
					"low": "12919.03"
				}
			}, {
				"itme": {
					"date": "20130617",
					"current": "13033.12",
					"open": "12584.37",
					"change": "346.6",
					"vol": "171869",
					"close": "13033.12",
					"high": "13033.12",
					"low": "12549.82"
				}
			}, {
				"itme": {
					"date": "20130614",
					"current": "12686.52",
					"open": "12668.9",
					"change": "241.14",
					"vol": "278201",
					"close": "12686.52",
					"high": "12900.65",
					"low": "12629.31"
				}
			}, {
				"itme": {
					"date": "20130613",
					"current": "12445.38",
					"open": "13038.02",
					"change": "-843.94",
					"vol": "240881",
					"close": "12445.38",
					"high": "13050.11",
					"low": "12415.85"
				}
			}, {
				"itme": {
					"date": "20130612",
					"current": "13289.32",
					"open": "13087.66",
					"change": "-28.3",
					"vol": "205512",
					"close": "13289.32",
					"high": "13332.72",
					"low": "12994.08"
				}
			}, {
				"itme": {
					"date": "20130611",
					"current": "13317.62",
					"open": "13504.77",
					"change": "-196.58",
					"vol": "260157",
					"close": "13317.62",
					"high": "13584.31",
					"low": "13296.31"
				}
			}, {
				"itme": {
					"date": "20130610",
					"current": "13514.2",
					"open": "13141.85",
					"change": "636.67",
					"vol": "241388",
					"close": "13514.2",
					"high": "13514.2",
					"low": "13141.37"
				}
			}, {
				"itme": {
					"date": "20130607",
					"current": "12877.53",
					"open": "12706.41",
					"change": "-26.49",
					"vol": "324452",
					"close": "12877.53",
					"high": "13106.2",
					"low": "12548.2"
				}
			}, {
				"itme": {
					"date": "20130606",
					"current": "12904.02",
					"open": "12925.29",
					"change": "-110.85",
					"vol": "313954",
					"close": "12904.02",
					"high": "13238.53",
					"low": "12862.02"
				}
			}, {
				"itme": {
					"date": "20130605",
					"current": "13014.87",
					"open": "13566.75",
					"change": "-518.89",
					"vol": "303702",
					"close": "13014.87",
					"high": "13711.42",
					"low": "13011.16"
				}
			}, {
				"itme": {
					"date": "20130604",
					"current": "13533.76",
					"open": "13186.6",
					"change": "271.94",
					"vol": "378253",
					"close": "13533.76",
					"high": "13610.25",
					"low": "13060.94"
				}
			}, {
				"itme": {
					"date": "20130603",
					"current": "13261.82",
					"open": "13551.36",
					"change": "-512.72",
					"vol": "282270",
					"close": "13261.82",
					"high": "13562.87",
					"low": "13261.82"
				}
			}, {
				"itme": {
					"date": "20130531",
					"current": "13774.54",
					"open": "13804.23",
					"change": "185.51",
					"vol": "280502",
					"close": "13774.54",
					"high": "13916.56",
					"low": "13681.39"
				}
			}, {
				"itme": {
					"date": "20130530",
					"current": "13589.03",
					"open": "14072.9",
					"change": "-737.43",
					"vol": "321603",
					"close": "13589.03",
					"high": "14098.16",
					"low": "13555.66"
				}
			}, {
				"itme": {
					"date": "20130529",
					"current": "14326.46",
					"open": "14492.55",
					"change": "14.48",
					"vol": "297025",
					"close": "14326.46",
					"high": "14512.28",
					"low": "14243.49"
				}
			}, {
				"itme": {
					"date": "20130528",
					"current": "14311.98",
					"open": "13943.62",
					"change": "169.33",
					"vol": "336267",
					"close": "14311.98",
					"high": "14399.78",
					"low": "13943.62"
				}
			}, {
				"itme": {
					"date": "20130527",
					"current": "14142.65",
					"open": "14373.82",
					"change": "-469.8",
					"vol": "307385",
					"close": "14142.65",
					"high": "14381.28",
					"low": "14027.42"
				}
			}, {
				"itme": {
					"date": "20130524",
					"current": "14612.45",
					"open": "14731.75",
					"change": "128.47",
					"vol": "461182",
					"close": "14612.45",
					"high": "15007.5",
					"low": "13981.52"
				}
			}, {
				"itme": {
					"date": "20130523",
					"current": "14483.98",
					"open": "15739.98",
					"change": "-1143.28",
					"vol": "595182",
					"close": "14483.98",
					"high": "15942.6",
					"low": "14483.98"
				}
			}, {
				"itme": {
					"date": "20130522",
					"current": "15627.26",
					"open": "15440.69",
					"change": "246.24",
					"vol": "476837",
					"close": "15627.26",
					"high": "15706.63",
					"low": "15432.64"
				}
			}, {
				"itme": {
					"date": "20130521",
					"current": "15381.02",
					"open": "15264.79",
					"change": "20.21",
					"vol": "514107",
					"close": "15381.02",
					"high": "15388.37",
					"low": "15264.42"
				}
			}, {
				"itme": {
					"date": "20130520",
					"current": "15360.81",
					"open": "15260.61",
					"change": "222.69",
					"vol": "368093",
					"close": "15360.81",
					"high": "15381.74",
					"low": "15245.8"
				}
			}, {
				"itme": {
					"date": "20130517",
					"current": "15138.12",
					"open": "14926.42",
					"change": "100.88",
					"vol": "314469",
					"close": "15138.12",
					"high": "15157.32",
					"low": "14902.3"
				}
			}, {
				"itme": {
					"date": "20130516",
					"current": "15037.24",
					"open": "15146.05",
					"change": "-58.79",
					"vol": "375107",
					"close": "15037.24",
					"high": "15155.72",
					"low": "14879.51"
				}
			}, {
				"itme": {
					"date": "20130515",
					"current": "15096.03",
					"open": "14962.34",
					"change": "337.61",
					"vol": "435012",
					"close": "15096.03",
					"high": "15108.83",
					"low": "14956.38"
				}
			}, {
				"itme": {
					"date": "20130514",
					"current": "14758.42",
					"open": "14822.56",
					"change": "-23.79",
					"vol": "304674",
					"close": "14758.42",
					"high": "14839.79",
					"low": "14755.08"
				}
			}, {
				"itme": {
					"date": "20130513",
					"current": "14782.21",
					"open": "14759.5",
					"change": "174.67",
					"vol": "387213",
					"close": "14782.21",
					"high": "14849.01",
					"low": "14727.7"
				}
			}, {
				"itme": {
					"date": "20130510",
					"current": "14607.54",
					"open": "14449.24",
					"change": "416.06",
					"vol": "312488",
					"close": "14607.54",
					"high": "14636.81",
					"low": "14426.74"
				}
			}, {
				"itme": {
					"date": "20130509",
					"current": "14191.48",
					"open": "14366.95",
					"change": "-94.21",
					"vol": "268307",
					"close": "14191.48",
					"high": "14409.82",
					"low": "14191.48"
				}
			}, {
				"itme": {
					"date": "20130508",
					"current": "14285.69",
					"open": "14196.2",
					"change": "105.45",
					"vol": "252595",
					"close": "14285.69",
					"high": "14421.38",
					"low": "14186.83"
				}
			}, {
				"itme": {
					"date": "20130507",
					"current": "14180.24",
					"open": "13960.04",
					"change": "486.2",
					"vol": "217244",
					"close": "14180.24",
					"high": "14196.38",
					"low": "13951.81"
				}
			}, {
				"itme": {
					"date": "20130502",
					"current": "13694.04",
					"open": "13727.25",
					"change": "-105.31",
					"vol": "179919",
					"close": "13694.04",
					"high": "13780.48",
					"low": "13637.96"
				}
			}, {
				"itme": {
					"date": "20130501",
					"current": "13799.35",
					"open": "13837.72",
					"change": "-61.51",
					"vol": "185421",
					"close": "13799.35",
					"high": "13844.82",
					"low": "13782"
				}
			}, {
				"itme": {
					"date": "20130430",
					"current": "13860.86",
					"open": "13854.82",
					"change": "-23.27",
					"vol": "237549",
					"close": "13860.86",
					"high": "13897.06",
					"low": "13778.75"
				}
			}, {
				"itme": {
					"date": "20130426",
					"current": "13884.13",
					"open": "13978.98",
					"change": "-41.95",
					"vol": "248302",
					"close": "13884.13",
					"high": "13983.87",
					"low": "13852.2"
				}
			}, {
				"itme": {
					"date": "20130425",
					"current": "13926.08",
					"open": "13887.53",
					"change": "82.62",
					"vol": "294484",
					"close": "13926.08",
					"high": "13974.26",
					"low": "13827.96"
				}
			}, {
				"itme": {
					"date": "20130424",
					"current": "13843.46",
					"open": "13687.28",
					"change": "313.81",
					"vol": "307447",
					"close": "13843.46",
					"high": "13843.46",
					"low": "13686.78"
				}
			}, {
				"itme": {
					"date": "20130423",
					"current": "13529.65",
					"open": "13545.6",
					"change": "-38.72",
					"vol": "240212",
					"close": "13529.65",
					"high": "13585.35",
					"low": "13505.53"
				}
			}, {
				"itme": {
					"date": "20130422",
					"current": "13568.37",
					"open": "13537.17",
					"change": "251.89",
					"vol": "246225",
					"close": "13568.37",
					"high": "13611.58",
					"low": "13529.44"
				}
			}, {
				"itme": {
					"date": "20130419",
					"current": "13316.48",
					"open": "13268.43",
					"change": "96.41",
					"vol": "236009",
					"close": "13316.48",
					"high": "13338.75",
					"low": "13186.89"
				}
			}, {
				"itme": {
					"date": "20130418",
					"current": "13220.07",
					"open": "13272.22",
					"change": "-162.82",
					"vol": "304972",
					"close": "13220.07",
					"high": "13377.74",
					"low": "13200.85"
				}
			}, {
				"itme": {
					"date": "20130417",
					"current": "13382.89",
					"open": "13330.5",
					"change": "161.45",
					"vol": "277440",
					"close": "13382.89",
					"high": "13397.5",
					"low": "13318.69"
				}
			}, {
				"itme": {
					"date": "20130416",
					"current": "13221.44",
					"open": "13023.91",
					"change": "-54.22",
					"vol": "321610",
					"close": "13221.44",
					"high": "13312.23",
					"low": "13004.46"
				}
			}, {
				"itme": {
					"date": "20130415",
					"current": "13275.66",
					"open": "13345.86",
					"change": "-209.48",
					"vol": "321040",
					"close": "13275.66",
					"high": "13408.29",
					"low": "13257.86"
				}
			}, {
				"itme": {
					"date": "20130412",
					"current": "13485.14",
					"open": "13568.25",
					"change": "-64.02",
					"vol": "351972",
					"close": "13485.14",
					"high": "13568.25",
					"low": "13402.86"
				}
			}, {
				"itme": {
					"date": "20130411",
					"current": "13549.16",
					"open": "13444.95",
					"change": "261.03",
					"vol": "368494",
					"close": "13549.16",
					"high": "13549.16",
					"low": "13384.11"
				}
			}, {
				"itme": {
					"date": "20130410",
					"current": "13288.13",
					"open": "13177.31",
					"change": "95.78",
					"vol": "359840",
					"close": "13288.13",
					"high": "13325.15",
					"low": "13177.31"
				}
			}, {
				"itme": {
					"date": "20130409",
					"current": "13192.35",
					"open": "13309.13",
					"change": "-0.24",
					"vol": "287550",
					"close": "13192.35",
					"high": "13331.39",
					"low": "13151.73"
				}
			}, {
				"itme": {
					"date": "20130408",
					"current": "13192.59",
					"open": "13082.61",
					"change": "358.95",
					"vol": "313898",
					"close": "13192.59",
					"high": "13225.22",
					"low": "13080.29"
				}
			}, {
				"itme": {
					"date": "20130405",
					"current": "12833.64",
					"open": "12880.82",
					"change": "199.1",
					"vol": "477393",
					"close": "12833.64",
					"high": "13225.62",
					"low": "12831.1"
				}
			}, {
				"itme": {
					"date": "20130404",
					"current": "12634.54",
					"open": "12188.22",
					"change": "272.34",
					"vol": "309419",
					"close": "12634.54",
					"high": "12634.54",
					"low": "12075.97"
				}
			}, {
				"itme": {
					"date": "20130403",
					"current": "12362.2",
					"open": "12112.09",
					"change": "358.77",
					"vol": "235069",
					"close": "12362.2",
					"high": "12362.2",
					"low": "12102.05"
				}
			}, {
				"itme": {
					"date": "20130402",
					"current": "12003.43",
					"open": "12051.57",
					"change": "-131.59",
					"vol": "274578",
					"close": "12003.43",
					"high": "12107.4",
					"low": "11805.78"
				}
			}, {
				"itme": {
					"date": "20130401",
					"current": "12135.02",
					"open": "12371.34",
					"change": "-262.89",
					"vol": "198159",
					"close": "12135.02",
					"high": "12384.83",
					"low": "12133"
				}
			}, {
				"itme": {
					"date": "20130329",
					"current": "12397.91",
					"open": "12405.53",
					"change": "61.95",
					"vol": "181517",
					"close": "12397.91",
					"high": "12425.96",
					"low": "12319.75"
				}
			}, {
				"itme": {
					"date": "20130328",
					"current": "12335.96",
					"open": "12457.13",
					"change": "-157.83",
					"vol": "204555",
					"close": "12335.96",
					"high": "12462.86",
					"low": "12286.37"
				}
			}, {
				"itme": {
					"date": "20130327",
					"current": "12493.79",
					"open": "12476.58",
					"change": "22.17",
					"vol": "152916",
					"close": "12493.79",
					"high": "12502.26",
					"low": "12442.39"
				}
			}, {
				"itme": {
					"date": "20130326",
					"current": "12471.62",
					"open": "12461.79",
					"change": "-74.84",
					"vol": "187541",
					"close": "12471.62",
					"high": "12540.12",
					"low": "12456.04"
				}
			}, {
				"itme": {
					"date": "20130325",
					"current": "12546.46",
					"open": "12507.61",
					"change": "207.93",
					"vol": "169889",
					"close": "12546.46",
					"high": "12594.36",
					"low": "12480.42"
				}
			}, {
				"itme": {
					"date": "20130322",
					"current": "12338.53",
					"open": "12498.51",
					"change": "-297.16",
					"vol": "168026",
					"close": "12338.53",
					"high": "12522.05",
					"low": "12338.53"
				}
			}, {
				"itme": {
					"date": "20130321",
					"current": "12635.69",
					"open": "12592",
					"change": "167.46",
					"vol": "193338",
					"close": "12635.69",
					"high": "12650.26",
					"low": "12586.06"
				}
			}, {
				"itme": {
					"date": "20130319",
					"current": "12468.23",
					"open": "12405.61",
					"change": "247.6",
					"vol": "165245",
					"close": "12468.23",
					"high": "12491.16",
					"low": "12401.12"
				}
			}, {
				"itme": {
					"date": "20130318",
					"current": "12220.63",
					"open": "12365.44",
					"change": "-340.32",
					"vol": "195872",
					"close": "12220.63",
					"high": "12373.17",
					"low": "12220.63"
				}
			}, {
				"itme": {
					"date": "20130315",
					"current": "12560.95",
					"open": "12437.68",
					"change": "179.76",
					"vol": "251010",
					"close": "12560.95",
					"high": "12560.95",
					"low": "12434.47"
				}
			}, {
				"itme": {
					"date": "20130314",
					"current": "12381.19",
					"open": "12332.16",
					"change": "141.53",
					"vol": "171506",
					"close": "12381.19",
					"high": "12395.73",
					"low": "12248.65"
				}
			}, {
				"itme": {
					"date": "20130313",
					"current": "12239.66",
					"open": "12252.29",
					"change": "-75.15",
					"vol": "195942",
					"close": "12239.66",
					"high": "12339.45",
					"low": "12234.48"
				}
			}, {
				"itme": {
					"date": "20130312",
					"current": "12314.81",
					"open": "12433.6",
					"change": "-34.24",
					"vol": "306496",
					"close": "12314.81",
					"high": "12461.97",
					"low": "12314.81"
				}
			}, {
				"itme": {
					"date": "20130311",
					"current": "12349.05",
					"open": "12363.09",
					"change": "65.43",
					"vol": "332324",
					"close": "12349.05",
					"high": "12403.95",
					"low": "12300.83"
				}
			}, {
				"itme": {
					"date": "20130308",
					"current": "12283.62",
					"open": "12066.5",
					"change": "315.54",
					"vol": "364600",
					"close": "12283.62",
					"high": "12283.62",
					"low": "12065.09"
				}
			}, {
				"itme": {
					"date": "20130307",
					"current": "11968.08",
					"open": "12037.25",
					"change": "35.81",
					"vol": "215976",
					"close": "11968.08",
					"high": "12069.6",
					"low": "11946.01"
				}
			}, {
				"itme": {
					"date": "20130306",
					"current": "11932.27",
					"open": "11811.06",
					"change": "248.82",
					"vol": "200929",
					"close": "11932.27",
					"high": "11933.82",
					"low": "11803.09"
				}
			}, {
				"itme": {
					"date": "20130305",
					"current": "11683.45",
					"open": "11732.57",
					"change": "31.16",
					"vol": "198879",
					"close": "11683.45",
					"high": "11779.42",
					"low": "11666.38"
				}
			}, {
				"itme": {
					"date": "20130304",
					"current": "11652.29",
					"open": "11695.45",
					"change": "45.91",
					"vol": "205192",
					"close": "11652.29",
					"high": "11767.68",
					"low": "11613.59"
				}
			}, {
				"itme": {
					"date": "20130301",
					"current": "11606.38",
					"open": "11464.71",
					"change": "47.02",
					"vol": "190111",
					"close": "11606.38",
					"high": "11648.63",
					"low": "11464.71"
				}
			}, {
				"itme": {
					"date": "20130228",
					"current": "11559.36",
					"open": "11396.73",
					"change": "305.39",
					"vol": "217250",
					"close": "11559.36",
					"high": "11563.75",
					"low": "11392.56"
				}
			}, {
				"itme": {
					"date": "20130227",
					"current": "11253.97",
					"open": "11418.56",
					"change": "-144.84",
					"vol": "193234",
					"close": "11253.97",
					"high": "11419.62",
					"low": "11253.97"
				}
			}, {
				"itme": {
					"date": "20130226",
					"current": "11398.81",
					"open": "11449.66",
					"change": "-263.71",
					"vol": "267951",
					"close": "11398.81",
					"high": "11520.24",
					"low": "11374.83"
				}
			}, {
				"itme": {
					"date": "20130225",
					"current": "11662.52",
					"open": "11564.55",
					"change": "276.58",
					"vol": "240890",
					"close": "11662.52",
					"high": "11662.52",
					"low": "11562.1"
				}
			}, {
				"itme": {
					"date": "20130222",
					"current": "11385.94",
					"open": "11238.75",
					"change": "76.81",
					"vol": "247910",
					"close": "11385.94",
					"high": "11390.65",
					"low": "11175.67"
				}
			}, {
				"itme": {
					"date": "20130221",
					"current": "11309.13",
					"open": "11404.73",
					"change": "-159.15",
					"vol": "196274",
					"close": "11309.13",
					"high": "11442.11",
					"low": "11301.77"
				}
			}, {
				"itme": {
					"date": "20130220",
					"current": "11468.28",
					"open": "11485.65",
					"change": "95.94",
					"vol": "201966",
					"close": "11468.28",
					"high": "11510.52",
					"low": "11440.1"
				}
			}, {
				"itme": {
					"date": "20130219",
					"current": "11372.34",
					"open": "11336.45",
					"change": "-35.53",
					"vol": "189926",
					"close": "11372.34",
					"high": "11412.86",
					"low": "11336.45"
				}
			}, {
				"itme": {
					"date": "20130218",
					"current": "11407.87",
					"open": "11318.22",
					"change": "234.04",
					"vol": "238308",
					"close": "11407.87",
					"high": "11445.46",
					"low": "11308.83"
				}
			}, {
				"itme": {
					"date": "20130215",
					"current": "11173.83",
					"open": "11239.21",
					"change": "-133.45",
					"vol": "346213",
					"close": "11173.83",
					"high": "11261.58",
					"low": "11065.06"
				}
			}, {
				"item": {
					"date": "20130214",
					"current": "11307.28",
					"open": "11273.4",
					"change": "55.87",
					"vol": "280058",
					"close": "11307.28",
					"high": "11356.54",
					"low": "11243.49"
				}
			}, {
				"itme": {
					"date": "20130213",
					"current": "11251.41",
					"open": "11333.72",
					"change": "-117.71",
					"vol": "275326",
					"close": "11251.41",
					"high": "11365.27",
					"low": "11196.66"
				}
			}, {
				"itme": {
					"date": "20130212",
					"current": "11369.12",
					"open": "11346.72",
					"change": "215.96",
					"vol": "301150",
					"close": "11369.12",
					"high": "11460.64",
					"low": "11343.44"
				}
			}, {
				"itme": {
					"date": "20130208",
					"current": "11153.16",
					"open": "11179.97",
					"change": "-203.91",
					"vol": "328052",
					"close": "11153.16",
					"high": "11299.71",
					"low": "11135.89"
				}
			}, {
				"itme": {
					"date": "20130207",
					"current": "11357.07",
					"open": "11406.32",
					"change": "-106.68",
					"vol": "404321",
					"close": "11357.07",
					"high": "11446.81",
					"low": "11295.62"
				}
			}, {
				"itme": {
					"date": "20130206",
					"current": "11463.75",
					"open": "11236.7",
					"change": "416.83",
					"vol": "353425",
					"close": "11463.75",
					"high": "11498.42",
					"low": "11232.05"
				}
			}, {
				"itme": {
					"date": "20130205",
					"current": "11046.92",
					"open": "11105.24",
					"change": "-213.43",
					"vol": "385150",
					"close": "11046.92",
					"high": "11170.85",
					"low": "11046.92"
				}
			}, {
				"itme": {
					"date": "20130204",
					"current": "11260.35",
					"open": "11254.16",
					"change": "69.01",
					"vol": "354112",
					"close": "11260.35",
					"high": "11285.49",
					"low": "11194.74"
				}
			}, {
				"itme": {
					"date": "20130201",
					"current": "11191.34",
					"open": "11193.72",
					"change": "52.68",
					"vol": "278247",
					"close": "11191.34",
					"high": "11237.84",
					"low": "11142.26"
				}
			}, {
				"itme": {
					"date": "20130131",
					"current": "11138.66",
					"open": "11057.5",
					"change": "24.71",
					"vol": "275718",
					"close": "11138.66",
					"high": "11145.38",
					"low": "11007.77"
				}
			}, {
				"itme": {
					"date": "20130130",
					"current": "11113.95",
					"open": "10913.97",
					"change": "247.23",
					"vol": "202808",
					"close": "11113.95",
					"high": "11113.95",
					"low": "10905.64"
				}
			}, {
				"itme": {
					"date": "20130129",
					"current": "10866.72",
					"open": "10751.01",
					"change": "42.41",
					"vol": "238461",
					"close": "10866.72",
					"high": "10937.63",
					"low": "10751.01"
				}
			}, {
				"itme": {
					"date": "20130128",
					"current": "10824.31",
					"open": "11002.86",
					"change": "-102.34",
					"vol": "198276",
					"close": "10824.31",
					"high": "11002.86",
					"low": "10824.31"
				}
			}, {
				"itme": {
					"date": "20130125",
					"current": "10926.65",
					"open": "10797.3",
					"change": "305.78",
					"vol": "220592",
					"close": "10926.65",
					"high": "10926.65",
					"low": "10790.95"
				}
			}, {
				"itme": {
					"date": "20130124",
					"current": "10620.87",
					"open": "10441.11",
					"change": "133.88",
					"vol": "211153",
					"close": "10620.87",
					"high": "10634.74",
					"low": "10441.11"
				}
			}, {
				"itme": {
					"date": "20130123",
					"current": "10486.99",
					"open": "10575.6",
					"change": "-222.94",
					"vol": "198804",
					"close": "10486.99",
					"high": "10663.09",
					"low": "10486.99"
				}
			}, {
				"itme": {
					"date": "20130122",
					"current": "10709.93",
					"open": "10765.1",
					"change": "-37.81",
					"vol": "248090",
					"close": "10709.93",
					"high": "10859.42",
					"low": "10615.2"
				}
			}, {
				"itme": {
					"date": "20130121",
					"current": "10747.74",
					"open": "10941.45",
					"change": "-165.56",
					"vol": "207319",
					"close": "10747.74",
					"high": "10941.45",
					"low": "10747.74"
				}
			}, {
				"itme": {
					"date": "20130118",
					"current": "10913.3",
					"open": "10791.97",
					"change": "303.66",
					"vol": "286553",
					"close": "10913.3",
					"high": "10913.3",
					"low": "10787.12"
				}
			}, {
				"itme": {
					"date": "20130117",
					"current": "10609.64",
					"open": "10660.94",
					"change": "9.2",
					"vol": "268498",
					"close": "10609.64",
					"high": "10694.85",
					"low": "10432.97"
				}
			}, {
				"itme": {
					"date": "20130116",
					"current": "10600.44",
					"open": "10806.41",
					"change": "-278.64",
					"vol": "226063",
					"close": "10600.44",
					"high": "10806.41",
					"low": "10591.3"
				}
			}, {
				"itme": {
					"date": "20130115",
					"current": "10879.08",
					"open": "10914.65",
					"change": "77.51",
					"vol": "215053",
					"close": "10879.08",
					"high": "10952.31",
					"low": "10851.66"
				}
			}, {
				"itme": {
					"date": "20130111",
					"current": "10801.57",
					"open": "10786.14",
					"change": "148.93",
					"vol": "236589",
					"close": "10801.57",
					"high": "10830.43",
					"low": "10748.06"
				}
			}, {
				"itme": {
					"date": "20130110",
					"current": "10652.64",
					"open": "10635.11",
					"change": "74.07",
					"vol": "268503",
					"close": "10652.64",
					"high": "10686.12",
					"low": "10619.65"
				}
			}, {
				"itme": {
					"date": "20130109",
					"current": "10578.57",
					"open": "10405.67",
					"change": "70.51",
					"vol": "214983",
					"close": "10578.57",
					"high": "10620.7",
					"low": "10398.61"
				}
			}, {
				"itme": {
					"date": "20130108",
					"current": "10508.06",
					"open": "10544.21",
					"change": "-90.95",
					"vol": "211362",
					"close": "10508.06",
					"high": "10602.12",
					"low": "10463.43"
				}
			}, {
				"itme": {
					"date": "20130107",
					"current": "10599.01",
					"open": "10743.69",
					"change": "-89.1",
					"vol": "187737",
					"close": "10599.01",
					"high": "10743.69",
					"low": "10589.7"
				}
			}, {
				"itme": {
					"date": "20130104",
					"current": "10688.11",
					"open": "10604.5",
					"change": "292.93",
					"vol": "219010",
					"close": "10688.11",
					"high": "10734.23",
					"low": "10602.24"
				}
			}, {
				"itme": {
					"date": "20121228",
					"current": "10395.18",
					"open": "10406.36",
					"change": "72.2",
					"vol": "202751",
					"close": "10395.18",
					"high": "10433.63",
					"low": "10374.85"
				}
			}, {
				"itme": {
					"date": "20121227",
					"current": "10322.98",
					"open": "10295.26",
					"change": "92.62",
					"vol": "235248",
					"close": "10322.98",
					"high": "10376.39",
					"low": "10288.85"
				}
			}, {
				"itme": {
					"date": "20121226",
					"current": "10230.36",
					"open": "10131.22",
					"change": "150.24",
					"vol": "182065",
					"close": "10230.36",
					"high": "10230.36",
					"low": "10107.34"
				}
			}, {
				"itme": {
					"date": "20121225",
					"current": "10080.12",
					"open": "10092.35",
					"change": "140.06",
					"vol": "141738",
					"close": "10080.12",
					"high": "10119.35",
					"low": "10030.44"
				}
			}, {
				"itme": {
					"date": "20121221",
					"current": "9940.06",
					"open": "10145.58",
					"change": "-99.27",
					"vol": "256540",
					"close": "9940.06",
					"high": "10175.06",
					"low": "9924.42"
				}
			}, {
				"itme": {
					"date": "20121220",
					"current": "10039.33",
					"open": "10093.11",
					"change": "-121.07",
					"vol": "274835",
					"close": "10039.33",
					"high": "10147.68",
					"low": "10028.65"
				}
			}, {
				"itme": {
					"date": "20121219",
					"current": "10160.4",
					"open": "10025.4",
					"change": "237.39",
					"vol": "277497",
					"close": "10160.4",
					"high": "10160.4",
					"low": "10016.98"
				}
			}, {
				"itme": {
					"date": "20121218",
					"current": "9923.01",
					"open": "9848.87",
					"change": "94.13",
					"vol": "260327",
					"close": "9923.01",
					"high": "9967.24",
					"low": "9848.87"
				}
			}, {
				"itme": {
					"date": "20121217",
					"current": "9828.88",
					"open": "9895.68",
					"change": "91.32",
					"vol": "213620",
					"close": "9828.88",
					"high": "9903.35",
					"low": "9826.3"
				}
			}, {
				"itme": {
					"date": "20121214",
					"current": "9737.56",
					"open": "9703.56",
					"change": "-5.17",
					"vol": "247446",
					"close": "9737.56",
					"high": "9775.75",
					"low": "9687.7"
				}
			}, {
				"itme": {
					"date": "20121213",
					"current": "9742.73",
					"open": "9681.2",
					"change": "161.27",
					"vol": "212007",
					"close": "9742.73",
					"high": "9767.05",
					"low": "9672.47"
				}
			}, {
				"itme": {
					"date": "20121212",
					"current": "9581.46",
					"open": "9606.25",
					"change": "56.14",
					"vol": "129362",
					"close": "9581.46",
					"high": "9606.25",
					"low": "9565.95"
				}
			}, {
				"itme": {
					"date": "20121211",
					"current": "9525.32",
					"open": "9510.6",
					"change": "-8.43",
					"vol": "97212",
					"close": "9525.32",
					"high": "9534.18",
					"low": "9487.95"
				}
			}, {
				"itme": {
					"date": "20121210",
					"current": "9533.75",
					"open": "9584.46",
					"change": "6.36",
					"vol": "115169",
					"close": "9533.75",
					"high": "9584.46",
					"low": "9517.4"
				}
			}, {
				"itme": {
					"date": "20121207",
					"current": "9527.39",
					"open": "9547.14",
					"change": "-17.77",
					"vol": "133827",
					"close": "9527.39",
					"high": "9572.75",
					"low": "9522.13"
				}
			}, {
				"itme": {
					"date": "20121206",
					"current": "9545.16",
					"open": "9535.69",
					"change": "76.32",
					"vol": "123125",
					"close": "9545.16",
					"high": "9565.43",
					"low": "9503.31"
				}
			}, {
				"itme": {
					"date": "20121205",
					"current": "9468.84",
					"open": "9380.37",
					"change": "36.38",
					"vol": "115933",
					"close": "9468.84",
					"high": "9515.86",
					"low": "9376.97"
				}
			}, {
				"itme": {
					"date": "20121204",
					"current": "9432.46",
					"open": "9419.15",
					"change": "-25.72",
					"vol": "104081",
					"close": "9432.46",
					"high": "9457.19",
					"low": "9406.03"
				}
			}, {
				"itme": {
					"date": "20121203",
					"current": "9458.18",
					"open": "9484.2",
					"change": "12.17",
					"vol": "110066",
					"close": "9458.18",
					"high": "9525.82",
					"low": "9453.48"
				}
			}, {
				"itme": {
					"date": "20121130",
					"current": "9446.01",
					"open": "9446.77",
					"change": "45.13",
					"vol": "159777",
					"close": "9446.01",
					"high": "9492.91",
					"low": "9380.25"
				}
			}, {
				"itme": {
					"date": "20121129",
					"current": "9400.88",
					"open": "9370.29",
					"change": "92.53",
					"vol": "113308",
					"close": "9400.88",
					"high": "9412.08",
					"low": "9350.4"
				}
			}, {
				"itme": {
					"date": "20121128",
					"current": "9308.35",
					"open": "9375.48",
					"change": "-114.95",
					"vol": "118668",
					"close": "9308.35",
					"high": "9407.62",
					"low": "9308.35"
				}
			}, {
				"itme": {
					"date": "20121127",
					"current": "9423.3",
					"open": "9371.13",
					"change": "34.36",
					"vol": "136732",
					"close": "9423.3",
					"high": "9449.72",
					"low": "9370.59"
				}
			}, {
				"itme": {
					"date": "20121126",
					"current": "9388.94",
					"open": "9466.06",
					"change": "22.14",
					"vol": "151502",
					"close": "9388.94",
					"high": "9487.94",
					"low": "9388.94"
				}
			}, {
				"itme": {
					"date": "20121122",
					"current": "9366.8",
					"open": "9336.32",
					"change": "144.28",
					"vol": "147351",
					"close": "9366.8",
					"high": "9366.8",
					"low": "9304.72"
				}
			}, {
				"itme": {
					"date": "20121121",
					"current": "9222.52",
					"open": "9213.73",
					"change": "79.88",
					"vol": "134728",
					"close": "9222.52",
					"high": "9248.98",
					"low": "9161.21"
				}
			}, {
				"itme": {
					"date": "20121120",
					"current": "9142.64",
					"open": "9198.42",
					"change": "-10.56",
					"vol": "133675",
					"close": "9142.64",
					"high": "9200.85",
					"low": "9129.43"
				}
			}, {
				"itme": {
					"date": "20121119",
					"current": "9153.2",
					"open": "9141.27",
					"change": "129.04",
					"vol": "143052",
					"close": "9153.2",
					"high": "9183.46",
					"low": "9135.29"
				}
			}]
		}
	}
}';
        echo $data;
        exit;
    }
}
