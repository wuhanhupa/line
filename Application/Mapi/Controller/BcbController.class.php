<?php

namespace Mapi\Controller;

class BcbController extends CommonController
{
    /**
     * [access_token 验证token]
     * @return [type] [description]
     */

    //app获取token值
    public function getToken($client_id)
    {
        //$client_id = MD5(uniqid(8));
        //$client_secret = MD5(uniqid().date('Y-m-d'));
        if (!isset($client_id)) {
            $info['info'] = "client_id不存在";

            $this->ajaxReturn($info);
        }
        $client_secret = M('Client')->where(['client_id' => $client_id])->getField('client_secret');
        $api_token = MD5($_SERVER['SERVER_ADDR'] . $client_secret);
        S('api_token', $api_token);
        $info['token'] = $api_token;
        $this->ajaxReturn($info);
    }

    //获取首页货币实时数据
    public function index()
    {
        echo "index";
        //$result=curl_get_https('https://data.block.cc/api/v1/price');
        //首先可以将数据添加到币种表当中
        //$result=curl_get_https('https://data.block.cc/api/v1/tickers?currency=USDT&market=huobipro&page=2');//USTD支持的货币
        //$result=curl_get_https('https://data.block.cc/api/v1/currencies');
        //$result=curl_get_https('https://data.block.cc/api/v1/symbols');
        //$result=json_decode($result,true);
        /* echo "<pre/>"; var_dump($result); exit();*/
        /*  $data = array();
          $arr = array();

          foreach ($result['data'] as $key => $value) {
                     if ($result['data'][$key]=="USDT" || $result['data'][$key]=="USD" || $result['data'][$key]=="LTC" || $result['data'][$key]=="ETC" || $result['data'][$key]=="QTUM" || $result['data'][$key]=="ETH" || $result['data'][$key]=="BTC") {
                            $arr['name']= $result['data'][$key];
                      $arr['title'] = $result['data'][$key];
                      $arr['type'] = 'qbb';
                      $arr['img'] = $result['data'][$key].'.png';
                      $arr['dj_zj'] = '0';
                      $arr['dj_dk'] = '0';
                      $arr['dj_yh'] = '0';
                      $arr['dj_mm'] = '0';
                      $arr["fee_bili"]='30';
                      $arr["fee_meitian"]='50';
                      $arr["js_yw"] = $result['data'][$key];
                      $arr["zr_zs"]= '0.05';
                      $arr["zr_jz"]= '1';
                      $arr["zr_dz"]= '1';
                      $arr["zc_fee"]= "";
                      $arr["zc_min"]= '0.00001';
                      $arr["zc_max"]= '10000';
                      $arr["zc_jz"]=  '1';
                      $arr["zc_zd"]=  '20';
                      array_push($data, $arr);
                     }
          }
          $return = $this->addCoin($data);*/
        /*$data = array();
        $arr = array();  foreach ($result['data']['list'] as $key => $value){
               $arr['name']= explode("_",$value['symbol_pair'])[0];
            $arr['title'] = explode("_",$value['symbol_pair'])[0];
            $arr['js_yw'] = $value['symbol_name'];
            $arr['type'] = 'qbb';
            $arr['img'] = $value['symbol_name'].'.png';
            $arr['dj_zj'] = '127.0.0.1';
            $arr['dj_dk'] = '5001';
            $arr['dj_yh'] = 'hanzheng';
            $arr['dj_mm'] = '123456';
            $arr["fee_bili"]='30';
            $arr["fee_meitian"]='50' ;
            $arr["zr_zs"]= '0.05';
            $arr["zr_jz"]= '1';
            $arr["zr_dz"]= '1';
            $arr["zc_fee"]= "";
            $arr["zc_min"]= '0.00001';
            $arr["zc_max"]= '10000';
            $arr["zc_jz"]=  '1';
            $arr["zc_zd"]=  '20';
            array_push($data, $arr);
        }*/
        /*   echo "<pre/>";
           var_dump($data); exit();    $return=$this->addCoin($data);*/
    }


    //将拉取的币种存放到数据库
    /*	public function addCoin($data = null)
        {

           foreach ($data as $key => $value) {
                    $data[$key]['name'] = strtolower($value['name']);

                   if (M('Coin')->where(array('name' => $value['name']))->find()) {
                       return '币种存在！';
                    }

                    $rea = M()->execute('ALTER TABLE  `btchanges_user_coin` ADD  `' . strtolower($value['name']) . '` DECIMAL(20,8) UNSIGNED NOT NULL');

                    $reb = M()->execute('ALTER TABLE  `btchanges_user_coin` ADD  `' . strtolower($value['name']). 'd` DECIMAL(20,8) UNSIGNED NOT NULL ');

                    $rec = M()->execute('ALTER TABLE  `btchanges_user_coin` ADD  `' . strtolower($value['name']). 'b` VARCHAR(200) NOT NULL ');

             }

             $rs = M('Coin')->addAll($data);

             if($rs){
                    echo '操作成功3！';
             }else {
                    echo '数据未修改3！';
             }

        }*/
    /**
     * [marketTrade 交易所币种]
     * @return [type] [description]
     */
    public function marketTrade()
    {
        $result = curl_get_https('https://data.block.cc/api/v1/tickers?currency=USDT&market=huobipro&size=46');

        $result = json_decode($result, TRUE);

        $this->saveMarket($result['data']['list']);
    }

    /*  public function test_market()
        {
            $result=curl_get_https('https://data.block.cc/api/v1/tickers?currency=USDT&market=huobipro&size=46');//USTD支持的货币
            $result=json_decode($result,true);

            //dump($result['data']);
            dump($result['data']['list']));

        }*/

    //修改数据的方法

    public function saveMarket($result = NULL)
    {
        $data = [];
        foreach ($result as $key => $value) {
            $data['new_price'] = $value['last'];
            $data['buy_price'] = $value['bid'];
            $data['sell_price'] = $value['ask'];
            $data['volume'] = $value['base_volume'];
            $data['change'] = $value['change_daily'];
            $data['max_price'] = $value['high'];
            $data['min_price'] = $value['low'];
            $where = explode("_", strtolower($value['symbol_pair']))[0] . '_' . 'usdt';

            $return = M('Market')->where(['name' => $where])->save($data);

            if ($return) {
                file_put_contents('/www/wwwroot/bnbjy/saveMarket.log', date('Y-m-d H:i:s') . "\r\n", FILE_APPEND);
                echo "success";
            } else {
                file_put_contents('/www/wwwroot/bnbjy/saveMarket.log', date('Y-m-d H:i:s') . "\r\n", FILE_APPEND);
                echo "false";
            }
        }
    }

    //其他交易所行情
    public function globalTrade()
    {
        $info['CNY'] = getRate();

        $this->ajaxReturn($info);
    }
}
