<?php

namespace Papi\Controller;

class CtwocController extends CommonController
{


   //获取当前汇率价格
   public function unit_cost(){

       $unit_cost = getRate();
       if($unit_cost){
          $info['msg'] = '当前汇率价';
          $info['code']= '0000';
          $info['data']= ['rate' => $unit_cost];
          $this->ajaxReturn($info);
       }else{
          $info['msg'] = '当前汇率价';
          $info['code']= '5656';
          $info['data']= ['rate' => $unit_cost];
          $this->ajaxReturn($info); 
       }
   }


    /**
     * [upTrade C2C交易].
     *
     * @param [type] $paypassword [交易密码]
     * @param [type] $marketid    [货币]
     * @param [type] $price       [买卖价格]
     * @param [type] $num         [交易数量]
     * @param [type] $type        [交易类型1.买2.卖]
     * @param [type] $uid         [当前用户id]
     * @param [type] $parities    [当前页面显示的汇率]
     *
     * @return [type] [交易是否成功]
     */
    public function c2cTrade($jmcode)
    {
        $result = $this->rests($jmcode);

         if ($result['result']) {
                $uid = $result['data']['userid'];
                $num = $result['data']['num'];
                $price = $result['data']['price'];
                $type = $result['data']['type'];
                $paytype = $result['data']['paytype'] ;
                $paypassword = $result['data']['paypassword'];
                $parities = $result['data']['parities'];

                $marketid = 68; //usdt_cny 

                $this->isorder($uid);
                if ($num < 1) {
                    $info['msg']  = '数量不能小于1';
                    $info['code'] = '8019';
                    $info['data'] = array('data'=>'error');
                    $this->ajaxReturn($info);
                }

                if ($price < 1) {
                    $info['msg']   = '价格不能不能小于1';
                    $info['code'] = '8029';
                    $info['data'] = array('data'=>'error');
                    $this->ajaxReturn($info);
                }

                $this->ifcards($uid);

                $user = M('User')->where(array('id' => $uid))->find();

                if ( MD5($paypassword) != $user['paypassword']) {
                    $info['msg'] = '交易密码错误！';
                    $info['code'] = '10003';
                    $info['data'] = array('data'=>'error');
                    $this->ajaxReturn($info);
                }

                $usdt = M('User_coin')->where(array('userid' => $uid))->getField('usdt');

                if ($type == 2 && $usdt < $num) {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40001';
                    $info['msg']   = '您的余额不够不能进行出售';
                    $this->ajaxReturn($info);
                }

                if (!check($price, 'double')) {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40002';
                    $info['msg']  = '交易价格格式错误';
                    $this->ajaxReturn($info);
                    
                }

                if (!check($num, 'double')) {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40003';
                    $info['msg']  = '交易数量格式错误';
                    $this->ajaxReturn($info);
                }

                if (($type != 1) && ($type != 2)) {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40004';
                    $info['msg']  = '交易类型格式错误';
                    $this->ajaxReturn($info);
                }

                if (!$marketid) {
                    return $this->ajaxShow(array('marketid 不能为空'), -1);
                }

                $marketid = intval($marketid);

                if (!($marketData = M('Market')->where(array('id' => $marketid))->find())) {
                    return $this->ajaxShow(array('marketid 不存在'), -1);
                }

                $market = $marketData['name'];
                $xnb    = explode('_', $market)[0];
                $rmb    = explode('_', $market)[1];

                $mo = M();
                if (isset($type)) {
                    $cwid = $mo->table('btchanges_ctwoc')->add(array('userid' => $uid, 'market' => $market, 'price' => $price, 'num' => $num, 'rest' => $num, 'type' => $type, 'paytype' => $paytype, 'parities' => $parities, 'addtime' => time(), 'status' => 0));
                    if ($type==2) {
                        //$uid 当前用户;
                        $this->ctwo_center($uid, $num, $price, 0, $cwid, 0, 2);
                    }
                } else {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40005';
                    $info['msg']  = '交易类型错误';
                    $this->ajaxReturn($info);
                }

                if ($cwid) {
                    $info['data'] = array('data'=>'success');
                    $info['code'] = '0000';
                    $info['msg']  = '下单成功';
                    $this->SendYun($uid);
                    $this->ajaxReturn($info);
                } else {
                    $info['data'] = array('data'=>'error');
                    $info['code'] = '40006';
                    $info['msg']  = '交易失败';
                    $this->ajaxReturn($info);
                }
        }else {
            $this->apierror();
        } 
    }

    /**
     * [match_order 匹配交易订单支付信息]
     *
     * @return [type] [description]
     */
    public function match_order($peerid, $pay)
    {
        //1.根据匹配支付方式查询对应的用户支付信息 1.支付宝2.微信3.银行卡
    
        if ($pay[0] == 1 && $pay[1] == 2 && $pay[2] == 3) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 1 && $pay[1] == 2) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();

            return $data;
        } elseif ($pay[0] == 2 && $pay[1] == 3) {
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 2 && $pay[1] == 1) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();

            return $data;
        } elseif ($pay[0] == 2 && $pay[1] == 1 && $pay[2] == 3) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 1 && $pay[1] == 3) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 1 && $pay[1] == 3) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 3 && $pay[1] == 1 && $pay[1] == 2) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['wxinfo']   = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 3 && $pay[1] == 2) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif ($pay[0] == 3 && $pay[1] == 2) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } elseif (count($pay) == 1 && $pay[0] == 1) {
            $data['zfb_info'] = M('Alipay')->where(array('userid' => $peerid))->field('account,name,payimg')->find();

            return $data;
        } elseif (count($pay) == 1 && $pay[0] == 2) {
            $data['wxinfo'] = M('Weixin')->where(array('userid' => $peerid))->field('waccount,name,wximg')->find();

            return $data;
        } elseif (count($pay) == 1 && $pay[0] == 3) {
            $data['bankinfo'] = M('User_bank')->where(array('userid' => $peerid))->field('bank,name,bankcard,bankaddr')->find();

            return $data;
        } else {
            return '';
        }
    }

    //挂单详情
    public function c2cdetail($jmcode){

        $result = $this->rests($jmcode);

        if($result['result']){
            $gid = $result['data']['gid'];
            $data = M('Ctwoc')->where(array('id'=>$gid))->field('rest,price,parities')->find();
            if($data){
               $info['msg']='返回数据';
               $info['code']='0000';
               $info['data']= $data ;
               $this->ajaxReturn($info);
            }
        }else{
            return $this->apierror();
        }
        
    }



    /**
     * [tradec 手动交易].
     *
     * @param [type] $userid  [用户id]
     * @param [type] $peerid  [卖家id]
     * @param [type] $price   [价格]
     * @param [type] $num     [数量]
     * @param [type] $paytype [支付匹配方式]
     * @param [type] $type    [交易类型]
     *
     * @return [type] [description]
     */
    public function manualtrade($jmcode)
    {
        $result = $this->rests($jmcode);
        if ($result['result']) {
            $userid = $result['data']['userid'];
            $type = $result['data']['type'];
            $peerid=$result['data']['peerid'];
            $price=$result['data']['price'];
            $num=$result['data']['num'];
            $paytype = $result['data']['paytype'];
            $paypassword=  $result['data']['paypassword'];
            $parities= $result['data']['parities'];
            $gdid=$result['data']['gdid'];

        $this->isorder($userid);
        $this->ifcards($userid);

        if ($num <= 0) {
            $info['code'] = '8019';
            $info['msg']  = '数量不能小于等于0';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        if ($price <= 0) {
            $info['code'] = '8029';
            $info['msg']  = '价格不能小于等于0';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        if ($userid == $peerid) {
            $info['code'] = '1001';
            $info['msg']  = '不能自己与自己交易';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        //判断当前用户下的订单是属于买单还是卖单
        $user = M('User')->where(array('id' =>$userid))->find();

        if (MD5($paypassword) != $user['paypassword']) {
            $info['code'] = '6101';
            $info['msg']  = '交易密码错误！';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        if ($type == 2) {
            $mau['prreid'] = $userid;
            $mau['userid'] = $peerid;
            $usdt = M('User_coin')->where(array('userid' => $userid))->getField('usdt');
            if ($usdt < $num) {
                $info['code'] = '7001';
                $info['msg']  = '您的余额不够不能进行出售';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }

            $buy = M('Ctwoc')->where(array('userid' => $mau['userid'], 'id' => $gdid))->find();
            if ($buy['rest'] < $num) {
                $info['msg']  = '您所选择的当前订单满足不了您的出售量';
                $info['code'] = '3001';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }
            //跟进挂单ID来判断交易方式是否符合当前对比
            $pay_type = explode(',', $paytype);
            $buy['paytype'] = explode(',', $buy['paytype']);
            $pay_user=array_intersect($pay_type, $buy['paytype']);
            if (empty($pay_user)) {
                $info['msg']  = '您当前选择的交易方式与挂单不匹配请重新选择!';
                $info['code'] = '3021';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }

            $gid = M('Ctwoc')->add(array('userid' => $mau['prreid'], 'market' => 'usdt_cny', 'price' => $price, 'num' => $num, 'rest' => $num, 'type' => $type, 'paytype' => $paytype, 'parities' => $parities, 'addtime' => time(), 'status' => 5));
            $sell['id'] = $gid; //当前操作人下的是出售订单
            //查询当前出售者的挂单id
            $this->rest($buy, $mau, $type, $num, $gdid);
        }
        //判断当前挂单是够满足交易
        if ($type == 1) {
            //买入的时候判断卖家的剩余数量是否够
            $mau['userid'] = $userid;
            $mau['prreid'] = $peerid;

            //查询当前出售者的挂单id
            $sell = M('Ctwoc')->where(array('userid' => $mau['prreid'], 'id' => $gdid))->find();

            if ($sell['rest'] < $num) {
                $info['msg']  = '您所选择的当前订单满足不了您的购买量';
                $info['code'] = '3001';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }

            //跟进挂单ID来判断交易方式是否符合当前对比
            $pay_type = explode(',', $paytype);
            $sell['paytype'] = explode(',', $sell['paytype']);
            $pay_user=array_intersect($pay_type, $sell['paytype']);
            if (empty($pay_user)) {
                $info['msg']   = '您当前选择的交易方式与挂单不匹配请重新选择!';
                $info['code'] = '3021';
                $info['data'] =array('data' => 'error'); 
                $this->ajaxReturn($info);
            }

            $gid = M('Ctwoc')->add(array('userid' => $mau['userid'], 'market' => 'usdt_cny', 'price' => $price, 'num' => $num, 'rest' => $num, 'type' => $type, 'paytype' => $paytype, 'parities' => $parities, 'addtime' => time(), 'status' => 5));

            $buy['id'] = $gid; //=>表示购买的订单

            $this->rest($sell, $mau, $type, $num, $gdid);
        }

        $pay = explode(',', $paytype);

        //2.如何条件符合就匹配下单
        $data = array('userid' => $userid,
                      'peerid' => $peerid,
                      'market'=> 'usdt_cny',
                      'price'=> $price,
                      'num' => $num,
                      'type'=> $type,
                      'paytype'=> $paytype,
                      'addtime'=> time(),
                      'trade_id'=> rand(100, 999) . time(),
                      'status'=> 0,
                      'buyid'=> $buy['id'],
                      'sell_id'=> $sell['id'],
                      'parities'=> $parities);

        $cwid = M('Ctwoc_log')->add($data);
        M('Ctwoc_log')->execute('commit');

        $data['id']           = $cwid;
        $data['buytruename']  = $this->truename($data['userid']);
        $data['selltruename'] = $this->truename($data['peerid']);
        $data['buymoble']     = $this->moble($data['userid']);
        $data['sellmoble']    = $this->moble($data['peerid']);
        $data['addtime']      = date('Y-m-d H:i:s', $data['addtime']);
        if ($cwid) {
            //下单买单的时候不需要冻结卖单因为在挂卖但的时候已经就冻结了
            if ($type == 1) {
                //$info['pay_info'] = $this->match_order($peerid, $pay); //如果type类型等于1那么userid就为买家，
                $this->Sendsms($userid,1,$data['trade_id']);
                $this->Sendsms($peerid,2,$data['trade_id']);
            } else {
                $this->ctwo_center($userid, $num, $price, $peerid, $sell['id'], $cwid, $type);
              //  $info['pay_info'] = $this->match_order($userid, $pay); //如果type类型等于2那么userid就为卖家家，
                $this->Sendsms($userid,2,$data['trade_id']);
                $this->Sendsms($peerid,1,$data['trade_id']);
            }
            
            //返回数据时返回当前当前操作人的userid
            //$info['data']   = $data;
                $info['msg']  = '下单成功';
                $info['code'] = '0000';
                $info['data'] = array('data' => 'success');
            //$info['buyid']  = $buy['id']; //买单ID
            //$info['sellid'] = $sell['id']; //卖单ID*/
               $this->ajaxReturn($info);
            } else {
                $info['code'] = '4506';
                $info['msg']   = '下单失败';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }
        }else{
            return $this->apierror();
        }
    }

    //当用户买入，卖出的时候直接扣除剩余数量
    public function rest($order, $mau, $type, $num, $gdid)
    {
        //如果当前购买者是买入的话
        if ($type==1) {
            //当购买者下买入单的时候冻结挂单的售量
            $rest = $order['rest'] - $num;
            $smoney = $rest * $order['parities'];
            $rest_num = $order['rest_num'] + $num;
            //对应的卖单
            $sell = M('Ctwoc')->where(array('userid' => $mau['prreid'], 'id' => $gdid))->save(array('rest_num'=>$rest_num,'rest'=>$rest,'price'=>$smoney));
            return $sell;
        }
        //如果当前购买者是卖出的话
        if ($type==2) {
            //当购买者下买入单的时候冻结挂单的售量
            $rest = $order['rest'] - $num;
            $smoney = $rest * $order['parities'];
            $rest_num = $order['rest_num'] + $num;
            //对应的买单
            $buy = M('Ctwoc')->where(array('userid' => $mau['userid'], 'id' => $gdid))->save(array('rest_num'=>$rest_num,'rest'=>$rest,'price'=>$smoney));
            return $buy;
        }
    }

    /**
     * [getTradedata 获取交易数据].
     *
     * @param [type] $marketid [description]
     *
     * @return [type] [description]
     */
    public function getdata($jmcode)
    {
        $result = $this->rests($jmcode);
        if ($result['result']) {
            $type = $result['data']['type'];
            $data = M('Ctwoc')->join('btchanges_user as u ON btchanges_ctwoc.userid = u.id')
            ->field('btchanges_ctwoc.userid,btchanges_ctwoc.price,btchanges_ctwoc.parities,btchanges_ctwoc.rest,btchanges_ctwoc.type,btchanges_ctwoc.paytype,u.truename,btchanges_ctwoc.id')->where(array('btchanges_ctwoc.status' => 0,'btchanges_ctwoc.type' => $type,'btchanges_ctwoc.rest'=>array('gt',0)))->order('btchanges_ctwoc.addtime desc')->select();

            foreach ($data as $key => $value) {
                $data[$key]['cdan'] = $this->cdan($value['userid']);
                $data[$key]['num']  = (string)round((float)$data[$key]['rest'],2);
                $data[$key]['price'] = (string)round((float)$data[$key]['price'],2);
            }

            $info['msg']   = '市场挂单';
            $info['code'] = '0000';
            $info['data']   = $data;
            $this->ajaxReturn($info);
        }else{
            return $this->apierror();
      }

    }

    /**
     * [cdan 当前用户的成单数量].
     *
     * @param [type] $userid [description]
     *
     * @return [type] [description]
     */
    public function cdan($userid)
    {
        $count = M('Ctwoc_log')->where(array('userid' => $userid, 'status' => 2))->count();

        if (isset($count)) {
            return $count;
        } else {
            return 0;
        }
    }

    //订单详情$jmcode
   public function orderdetail($jmcode){

        $result = $this->rests($jmcode);

        if($result['result']){
            $trade_id = $result['data']['trade_id'];
            
            $list = M('Ctwoc_log')->where(array('trade_id'=>$trade_id))->find();
        
                if ($list['type'] == 1) {
                    $list['buytruename']  = $this->truename($list['userid']);
                    $list['selltruename'] = $this->truename($list['peerid']);
                    $list['buymoble']     = $this->moble($list['userid']);
                    $list['sellmoble']    = $this->moble($list['peerid']);
                    $list['byid'] = $list['userid'];
                    $list['syid'] = $list['peerid'];
                } else {
                    $list['buytruename']  = $this->truename($list['peerid']);
                    $list['selltruename'] = $this->truename($list['userid']);
                    $list['buymoble']     = $this->moble($list['peerid']);
                    $list['sellmoble']    = $this->moble($list['userid']);
                    $list['byid'] = $list['peerid'];
                    $list['syid'] = $list['userid'];
                }
                
                $list['addtime'] = date('Y-m-d H:i:s', $list['addtime']);


                if (strlen($list['paytype']) == 1) {
                    $pay[0] = $list['paytype'];
                } else {
                    $pay = explode(',', $list['paytype']);
                }

             
                if ($list['type'] == 1) {
                  
                    $arr = $this->match_order($list['peerid'], $pay);
                } else {
                 
                    $arr = $this->match_order($list['userid'], $pay);
                }
               
                $list['payinfo'] = $arr;
            


            if($list){
                $info['msg']='返回数据';
                $info['code']='0000';
                $info['data']= $list ;
                $this->ajaxReturn($info);
            }
        }else{
            return $this->apierror();
        }
  }

    //当前订单接口
    public function nowtrade($jmcode)
    {
        $result = $this->rests($jmcode);
        if ($result['result']) {
                $userid = $result['data']['userid'];
                $type = $result['data']['type'];
        
                //当前用户
                $where = 'userid =' . $userid . ' OR peerid=' . $userid;
                if ($type == 1) {
                    $where = 'status <  2 and (userid =' . $userid . ' OR peerid=' . $userid . ')';
                    //$where['status'] = array('LT', 2);
                    //订单小于2 说明买家还没有
                }
                if ($type == 2) {
                    $where = 'status = 2 and (userid =' . $userid . ' OR peerid=' . $userid . ')'; //当前订单等于2 说明用户之间的交易流程已经完成
                }
                if ($type == 3) {
                    //取消的订单
                    $where = 'status = 4 and (userid =' . $userid . ' OR peerid=' . $userid . ')';
                }
                $list = M('Ctwoc_log')->where($where)->order('addtime desc')->select();
        
                if (empty($list)) {
                    
                    $info['msg']  = '当前用户暂无数据';
                    $info['code'] = '0000';
                    $info['data'] = $list;

                    $this->ajaxReturn($info);

                } else {
                    $newlist = array();
                    if($type == 1 || $type == 2 || $type == 3){
                        foreach($list as $key => $value){
                                $newlist[$key]['trade_id'] = $value['trade_id'];
                                $newlist[$key]['type'] = $value['type'];
                                $newlist[$key]['num'] = $value['num'];
                                $newlist[$key]['parities'] = $value['parities'];
                                $newlist[$key]['status'] = $value['status'];
                                $newlist[$key]['addtime'] = $value['addtime'];  
                                $newlist[$key]['price'] = $value['price'];
                                if($value['type'] == 1){
                                    $newlist[$key]['byid'] = $value['userid'];
                                    $newlist[$key]['syid'] = $value['peerid'];
                                }else{
                                    $newlist[$key]['byid'] = $value['peerid'];
                                    $newlist[$key]['syid'] = $value['userid'];
                                }

                        }   
                        $info['msg']   = '查询成功';
                        $info['code'] = '0000';
                        $info['data']   = $newlist;
                        $this->ajaxReturn($info);
                    }else{
                        foreach ($list as $key => $value) {
                            if ($value['type'] == 1) {
                                $list[$key]['buytruename']  = $this->truename($list[$key]['userid']);
                                $list[$key]['selltruename'] = $this->truename($list[$key]['peerid']);
                                $list[$key]['buymoble']     = $this->moble($list[$key]['userid']);
                                $list[$key]['sellmoble']    = $this->moble($list[$key]['peerid']);
                            } else {
                                $list[$key]['buytruename']  = $this->truename($list[$key]['peerid']);
                                $list[$key]['selltruename'] = $this->truename($list[$key]['userid']);
                                $list[$key]['buymoble']     = $this->moble($list[$key]['peerid']);
                                $list[$key]['sellmoble']    = $this->moble($list[$key]['userid']);
                            }

                            $list[$key]['addtime'] = date('Y-m-d H:i:s', $list[$key]['addtime']);

                            if (strlen($list[$key]['paytype']) == 1) {
                                $pay[0] = $list[$key]['paytype'];
                            } else {
                                $pay = explode(',', $list[$key]['paytype']);
                            }
                            if ($value['type'] == 1) {
                                $arr = $this->match_order($list[$key]['peerid'], $pay);
                            } else {
                                $arr = $this->match_order($list[$key]['userid'], $pay);
                            }

                            $list[$key]['payinfo'] = $arr;
                        }
                    }
                    $info['msg']   = '查询成功';
                    $info['code'] = '0000';
                    $info['data']   = $list;
                    $this->ajaxReturn($info);
                }
         }else{
            return $this->apierror();
         }

    }

    

    //确认收付款  //当前模块流程必须是先付款才收款
    public function paymoney($jmcode)
    {
        $result = $this->rests($jmcode);
        if ($result['result']) {
            
          
            $userid = $result['data']['userid'];
            $id = $result['data']['trade_id'];
            $num = $result['data']['num'];
            $status = $result['data']['status'];
        
     
        $count = M('Ctwoc_log')->where(array('status' => 2, 'trade_id' => $id))->find();

        if ($count > 0) {
            $info['msg']   = '当前订单已经完成无法在进行操作';
            $info['code'] = '8501';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        $qxtype = M('Ctwoc_log')->where(array('status' => 4, 'trade_id' => $id))->find();

        if ($qxtype > 0) {
            $info['msg']   = '当前订单已经取消无法在进行操作';
            $info['code'] = '8502';
            $info['data'] = array('data' => 'error');
            $this->ajaxReturn($info);
        }

        $ctwoc = M('Ctwoc_log');

        if ($status == 1) {
            if ($ctwoc->where(array('trade_id' => $id))->save(array('status' => $status))) {
                $info['msg']   = '已经付款';
                $info['code'] = '0000';
                $info['data'] = array('data' => 'success');
                $this->ajaxReturn($info);
            } else {
                $info['msg']   = '付款失败';
                $info['code'] = '41204';
                $info['data'] = array('data' => 'success');
                return  $this->ajaxReturn($info);
            }
        } else {

            //确认收款后改比订单才是完结  已经收款状态2
            if ($ctwoc->where(array('trade_id' => $id))->save(array('status' => $status, 'endtime' => time()))) {
                $ctwoc->execute('commit');
                //成交完成之后把原本挂单的冻结数量取消
                $payfu = M('Ctwoc_log')->where(array('trade_id' => $id))->find();
                //如果type=1 那么对应的就是挂单就是卖单ID
                if ($payfu['type']==1) {
                    //查询对应的挂单
                    $gd = M('Ctwoc')->where(array('id'=>$payfu['sell_id']))->find();
                    $rest_num = $gd['rest_num'] - $payfu['num'];
                    //如果交易成功金额就不用改变
                    $gd = M('Ctwoc')->where(array('id'=>$payfu['sell_id']))->save(array('rest_num'=>$rest_num));
                    if (!$gd) {
                        $info['msg']   = '交易解冻挂单数量失败';
                        $info['code'] = '4009';
                        $info['data'] = array('data' => 'error');
                        return  $this->ajaxReturn($info);
                      
                    }
                }
                //如果type=2 那么对应的就是挂单就是买单ID
                if ($payfu['type']==2) {
                    $gd = M('Ctwoc')->where(array('id'=>$payfu['buyid']))->find();
                    $rest_num = bcsub($gd['rest_num'], $payfu['num'], 8);
                    //如果交易成功金额就不用改变
                    $gd = M('Ctwoc')->where(array('id'=>$payfu['buyid']))->save(array('rest_num'=>$rest_num));
                    if (!$gd) {
                        $info['msg']   = '交易解冻挂单数量失败';
                        $info['code'] = '4409';
                        $info['data'] = array('data' => 'error');
                        return  $this->ajaxReturn($info);
                       
                    }
                }

                    $this->center($payfu, 2); //2表示交易顺利完成
                }
            }
        }else{
            $this->apierror();
        }
    }

    /**
     * [ctwo_center 将卖家的虚拟币存放到系统中].
     *
     * @param [type] $userid [用户id]
     * @param [type] $num    [数量]
     * @param [type] $price  [价格]
     * @param [type] $cwid   [卖家下单id]
     *
     * @return [type] [description]
     */
    public function ctwo_center($userid, $num, $price, $usersell, $sellid, $trade_id, $type)
    {
        //冻结卖家账户
        $com = $this->djzh($userid, $num);

        return $com;
    }

    //冻结卖家账户
    public function djzh($prreid, $num)
    {
        $yust = M('User_coin')->where(array('userid' => $prreid))->getField('usdt');
        //2.蒋原来的uset扣去现在的数量
        $nust = $yust - $num;
        $ds   = M('User_coin')->where(array('userid' => $prreid))->save(array('usdt' => $nust));

        //1.蒋销售用的数量冻结,首先查询用户的原冻结的数量
        $yusdt = M('User_coin')->where(array('userid' => $prreid))->getField('usdtd');
        //2.蒋原来的冻结的uset+现在需要冻结的数量
        $nusdt = $yusdt + $num;
        //3.冻结
        $d = M('User_coin')->where(array('userid' => $prreid))->save(array('usdtd' => $nusdt));

        return $d;
    }

    //解冻卖家账户,判断类型是交易成功还是取消交易 2.交易成功  1.取消交易
    public function jdzh($prreid, $num, $type, $payfu)
    {

        //如果是订单交易成功
        if ($type == 2) {

            //1.蒋销售用的数量解冻
            $yusdt = M('User_coin')->where(array('userid' => $prreid))->getField('usdtd');
            //2.蒋原来的冻结的uset-现在的数量
            $nusdt = $yusdt - $num;
            //3.清除冻结
            $d = M('User_coin')->where(array('userid' => $prreid))->save(array('usdtd' => $nusdt));

            return $d;
        } else {
            //取消交易之后将原挂单的冻结数量，金额返回
            if ($payfu['type']==2) {

                //3.清除冻结
                $yusdtd = M('User_coin')->where(array('userid' => $prreid))->getField('usdtd');
                $nusdtd = $yusdtd - $num;

                $td = M('User_coin')->where(array('userid' => $prreid))->save(array('usdtd' => $nusdtd));

                //如果是取消订单那么就把用户账户中的冻结数据返回
                $yust = M('User_coin')->where(array('userid' => $prreid))->getField('usdt');
                //2.蒋原来的冻结的uset+现在的数量
                $nust = $yust + $num;

                //4.返回数量
                $d = M('User_coin')->where(array('userid' => $prreid))->save(array('usdt' => $nust));

                return $d;
            } else {
                return true;
            }
        }
    }

    public function center($payfu, $type)
    {
        //2.如果用户已经付款了,系统把虚拟币转给购买者   1查询购买者的改虚拟余额
        //如果type类型等于1 那么购买者就是userid  如果类型为二那么购买者就是peerid
        if ($payfu['type'] == 1) {
            $peerid = $payfu['peerid'];
            $userid = $payfu['userid'];
        } else {
            $peerid = $payfu['userid'];
            $userid = $payfu['peerid'];
        }

        if ($type == 2) {
            $buyuser = M('User_coin')->where(array('userid' => $userid))->getField('usdt');

            $buyusers = bcadd($buyuser, $payfu['num'], 8);

            $coin = M('User_coin')->where(array('userid' => $userid))->save(array('usdt' => $buyusers));


            if ($coin>0) {
                //交易成功之后将原来冻结的资产解冻
                $result = $this->jdzh($peerid, $payfu['num'], $type, $payfu);
                //交易完成之后写入买家跟卖家的交易记录

                $data[0] = array('userid' => $userid,'price'=> round($price),'num'=> $payfu['num'],'addtime'=> time(),'selloder'=> rand(10, 99).time(),'trade_id'=> $payfu['id'],'type'=> $payfu['type']);

                $data[1] = array('userid' => $peerid,'price'=> round($price),'num'=> $payfu['num'],'addtime'=> time(),'selloder'=> rand(10, 99).time(),'trade_id'=> $payfu['id'],'type'=> $payfu['type']);

                //卖家冻结记录
                $center = M('Ctwoc_center')->addAll($data);
                M('Ctwoc_center')->execute('commit');

            
                $info['msg']  = '交易成功';
                $info['code'] = '0000';
                $info['data'] = array('data' => 'success');
                $this->ajaxReturn($info);
            } else {
                $info['msg']   = '交易失败系统未能转出,交易失败';
                $info['code'] = '8005';
                $info['data'] = array('data' => 'error');
                $this->ajaxReturn($info);
            }
        } else {

             //取消交易之后将原挂单的冻结数量，金额返回
            if ($payfu['type']==1) {
                //查询对应的挂单
                $gd = M('Ctwoc')->where(array('id'=>$payfu['sell_id']))->find();
                $rest_num = $gd['rest_num'] - $payfu['num'];
                $rest = $gd['rest'] + $payfu['num'];
                $price = $rest * $gd['parities'];
                //如果交易成功金额就不用改变
                $gd = M('Ctwoc')->where(array('id'=>$payfu['sell_id']))->save(array('rest'=>$rest,'price'=>$price));
            }
            //如果type=2 那么对应的就是挂单就是买单ID
            if ($payfu['type']==2) {
                $gd = M('Ctwoc')->where(array('id'=>$payfu['buyid']))->find();
                $rest_num = $gd['rest_num'] - $payfu['num'];
                //如果交易成功金额就不用改变
                $rest = $gd['rest'] + $payfu['num'];
                $price = $rest * $gd['parities'];
                //如果交易成功金额就不用改变
                $gd = M('Ctwoc')->where(array('id'=>$payfu['buyid']))->save(array('rest'=>$rest,'price'=>$price));
            }
            $this->jdzh($peerid, $payfu['num'], $type, $payfu);

            $info['msg']   = '交易取消成功';
            $info['code'] = '0000';
            $info['data'] = array('data' => 'success');
            $this->ajaxReturn($info);
        }
    }

    /**取消订单
     * $trade_id 单号
     * $userid 用户id
     */
    public function cxtrde($jmcode)
    {
        
        $result = $this->rests($jmcode);
        if ($result['result']) {
            $userid = $result['data']['userid'];
            $trade_id = $result['data']['trade_id'];
        
            //判断当前用户是买家还是卖家

            $user = M('Ctwoc_log')->where(array('trade_id' => $trade_id))->find();

            //1.如果当前订单已经付款是无法取消的并且时间还没到
            if (!empty($user)) {
                if ($user['status'] == 1) {

                    $info['msg']  = '当前订单已经付款无法取消,请联系客服！';
                    $info['code'] = '5006';
                    $info['data'] = array('data' => 'error');
                    $this->ajaxReturn($info);

                }
                //判断如果原本交易订单已没有付款也没有收款的话那么就直接取消
                if ($user['status'] == 0) {
                    $result = $this->qxorder($user, $userid, $trade_id);

                    if ($result['code'] == '0000') {
                        $this->center($user, 1); //这里1表示用户要取消交易
                    } else {
                        $this->ajaxReturn($result);
                    }
                }
            } else {
                $info['msg']   = '当前用户不存在';
                $info['code'] = '5012';
                $info['data'] =  array('data' => 'error');
                $this->ajaxReturn($info);
            }
        }else{
            $this->apierror();
        }
    }

    public function qxorder($user, $userid, $trade_id)
    {
        if ($user['userid'] == $userid) {
            $uid = $user['userid'];
            //当前用户是买家
            $where = array('userid' => $user['userid'], 'trade_id' => $trade_id);
        }
        if ($user['peerid'] == $userid) {
            //当前用户是卖家
            $uid   = $user['peerid'];
            $where = array('peerid' => $user['peerid'], 'trade_id' => $trade_id);
        }

        $qx = M('Ctwoc_log')->where($where)->save(array('status' => 4));
        //取消订单成功之后需要返回卖家被冻结的币,同时修改冻结记录

        if ($qx) {
            $info['msg']   = '已取消';
            $info['code'] = '0000';
            if ($istrade == 1) {
                $info['data'] = array('uid'=>$uid);
            }else{
                $info['data'] =  array('data' => 'success');
            }
            return $info;
        } else {
            $info['msg']  = '取消失败';
            $info['code'] = '5009';
            $info['data'] =  array('data' => 'success');
            return $info;
        }
    }

    /**
     * [ispay 当前选择的支付方式是否存在].
     *
     * @return [type] [description]
     */
    public function ispay($jmcode)
    {
        $result = $this->rests($jmcode);
        if ($result['result']) {
            $userid = $result['data']['userid'];
            $type = $result['data']['type'];
    
           //1支付宝是否绑定
            if ($type == 1) {
                $alipay = M('Alipay')->where(array('userid' => $userid))->count();
                if ($alipay == 0 || !isset($alipay)) {
                                $info['msg']   = '用户没有绑定支付宝';
                                $info['code'] = '21004';
                                $info['data'] = array('data' => 'error');
                                $this->ajaxReturn($info);
                            } else {
                                $info['msg']   = '已绑定';
                                $info['code'] = '0000';
                                $info['data'] = array('data' => 'success');
                                $this->ajaxReturn($info);
                            }
            }
           //2微信是否绑定
          if ($type == 2) {
                    $weixin = M('Weixin')->where(array('userid' => $userid))->count();
                    if ($weixin == 0 || !isset($weixin)) {
                        $info['msg']   = '用户没有绑定微信';
                        $info['code'] = '21003';
                        $info['data'] = array('data' => 'error');
                        $this->ajaxReturn($info);
                    } else {
                        $info['msg']   = '已绑定';
                        $info['code'] = '0000';
                        $info['data'] = array('data' => 'success');
                    
                     
                        $this->ajaxReturn($info);
                    }
           }
           //3银行卡是否绑定
           if ($type == 3) {
                $bank = M('User_bank')->where(array('userid' => $userid))->count();
                if ($bank == 0 || !isset($bank)) {
                    $info['msg']   = '用户没有绑定银行卡';
                    $info['code'] = '21002';
                    $info['data'] = array('data' => 'error');
                    $this->ajaxReturn($info);
                } else {
                    $info['msg']   = '已绑定';
                    $info['code'] = '0000';
                    $info['data'] =  array('data' => 'success');
                    $this->ajaxReturn($info);
                }
          }
        }else{
            $this->apierror();
        }

    }

    public function truename($userid)
    {
        $truename = M('User')->where(array('id' => $userid))->getField('truename');

        return $truename;
    }

    public function moble($userid)
    {
        $moble = M('User')->where(array('id' => $userid))->getField('moble');

        return $moble;
    }

    /**
     * [Sendsms 发送短息].
     */
    public function Sendsms($userid,$type,$order)
    {
        //【GTE数字平台】您的XXXXXX号买单已经生成，卖家资金已经锁定，请及时完成订单，超时（30分钟）自动取消交易。
        //【GTE数字平台】您的XXXXXX号卖单已经生成，您的资金已经锁定，请联系买家及时完成订单，超时（30分钟）自动取消交易。
        //调用
        $user = M('User')->where(array('id' => $userid))->field('moble,truename')->find();
        if($type == 1){
            $content = '【GTE数字平台】尊敬的用户您的'.$order.'号买单已经生成，卖家资金已经锁定，请及时完成订单，超时（30分钟）自动取消交易';
        }else{
            $content = '【GTE数字平台】尊敬的用户您的'.$order.'号卖单已经生成，您的资金已经锁定，请联系买家及时完成订单，超时（30分钟）自动取消交易';
        }
       
        $result  = sendSMS($user['moble'], $content, 0, '');
     
        if ($result['Code'] == 0) {
            if (M('Usersms')->add(array('phone' => $user['moble'], 'yzm' => '0000', 'type' => 2, 'endtime' => time(), 'status' => 0))) {
                return true;
            }
        } else {
            if (M('Usersms')->add(array('phone' => $user['moble'], 'yzm' => '0000', 'type' => 2, 'endtime' => time(), 'status' => 3))) {
                return false;
            }
        }
    }

   /**
     * [Sendsms 发送短息].
     */
    public function SendYun($userid)
    {
        //调用
        $user = M('User')->where(array('id' => $userid))->field('username')->find();

        $content = '【GTE数字平台】用户'.$user['username'].'在c2c中进行了挂单请及时处理';
        $result  = sendSMS('18516760143', $content, 0, '');
        if ($result['Code'] == 0) {
                return true;
        } else {
                return false;
        }
    }

    /**
     * 判断用户是否绑定了身份证真实信息 如果没有不能进行交易
     * 天王盖地虎，小鸡炖蘑菇
     * @author hxq
     * @date   2018-09-13T13:37:11+080
     * @return [type] [description]
     */
    public function ifcards($userid)
    {
        $user = M('User')->where(array('id' => $userid ))->find();

        if ($user['ident_status']==0 || $user['ident_status']==3) {
            $info['msg']  = '您的身份证未上传审核或审核未通过,请到安全中心，实名认证上传';
            $info['code'] = '3005';
            $info['data'] =  array('data' => 'error');
            $this->ajaxReturn($info);
        }
        if ($user['ident_status']==1) {
            $info['msg']  = '您的身份待正在验证中,审核通过后才能交易';
            $info['code'] = '3006';
            $info['data'] =  array('data' => 'error');
            $this->ajaxReturn($info);
        }
    }
    

      
    /**
     * 判断当前用户是否有未完成的订单
     */
    public function isorder($userid){

        $where = ' status !=5 and rest > 0 and userid = '.$userid;
        $order = M('Ctwoc')->where($where)->field('id')->select();
   
        if ($order) {
            $info['msg']  = '您在挂单市场有挂单未处理完，请处理完后在挂单';
            $info['code'] = '8625';
            $info['data'] =  array('data' => 'error');
            $this->ajaxReturn($info);
            //在判断是否有正在交易的订单如果
        }else{
            $where = ' status <= 1 and (userid = '.$userid.' or peerid = '.$userid.')';
            $order = M('Ctwoc_log')->where($where)->field('id')->select();
            if($order){
                $info['msg']  = '您有买单或者卖单尚未处理完，请处理完后在交易';
                $info['code'] = '8626';
                $info['data'] =  array('data' => 'error');
                $this->ajaxReturn($info);
            }
           
        }

    }

}
