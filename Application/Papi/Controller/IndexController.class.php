<?php

namespace Papi\Controller;

class IndexController extends CommonController
{
    //例子
    public function index($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $data['username'] = $result['data']['username'];
            $data['password'] = $result['data']['password'];
            $this->apisuccess('0000', '请求解密成功', $data);
        } else {
            $this->apierror();
        }
    }


    //加密参数，获取加密后字符串，调试接口使用
    public function aestest()
    {
        //$jmcode = 'BB6DF048A990F915BC25DE096F07506417A804B2B35D4C0E712BE24C9028D42DF9FBB4FE382FDFE20B0D8CE3AC3E3CFA1BFA24428B5F7B97F95271586389113E9B8FB5951525F1F1F0DDBB6219C065B8';
        $jmcode = 'B6DF901322DE8479C6EABFF2EEDF7CCE4378BDB2839525BA881BD0DA87BC5EEF76BBE7C531B7AEFF66833D7B94736FBB';
        //JSON_ERROR_CTRL_CHAR
        $result = $this->rests($jmcode);
        var_dump($result);
    }

    public function test()
    {
        //调用
        $user = M('User')->where(array('id' => 45))->field('username')->find();

        $content = '【GTE数字平台】用户'.$user['username'].'在c2c中进行了挂单请及时处理';
        $result = sendSMS('15827439217', $content, 0, '');
        if ($result['Code'] == 0) {
            echo '发送成功';
        } else {
            echo '发送失败';
        }
    }


    

    //获取平台商token
    public function yjToken(){
        
        $url = C('YJ_ADDR')."/api/v1/merchant_gettoken";
        $data = array('username'=>'hupa','password'=>'123456');
        
        $ret = curl_post_http($url,$data);
        $result = json_decode($ret,true);
        
        return $result;
    }

    //获取平台商详情
    public function merchant_getdetails(){
        $url = "http://demo.yuudidi.com/api/v1/merchant_getdetails";
        $data = array('merchant_id'=>'541','api_token'=>'OdFwrYAzhhlElpknn1RPg1c5GzcYq91bCvIzYlaWF90RNnMpH4SsUb84qkYr');
        //$data = json_encode($data);
        $result = curl_post_http($url,$data);
        var_dump($result);
    }

    //获取平台商BTUSD 
    public function merchant_getcoins(){
        $url = "http://demo.yuudidi.com/api/v1/merchant_getcoins";
        $data = array('merchant_id'=>'541','api_token'=>'OdFwrYAzhhlElpknn1RPg1c5GzcYq91bCvIzYlaWF90RNnMpH4SsUb84qkYr');
        //$data = json_encode($data);
        $result = curl_post_http($url,$data);
        var_dump($result);
    }

     //注册新会员 
    public function member_register(){
        $result = $this->yjToken();
        $data = array('name'=>'xiaoquan',//会员姓名	
                    'username' =>'quanquan666',//用户名
                    'password' =>'a84511076',//密码
                    'mobile_phone'=>'13554209006',//手机号码
                    'email'=>'2827361255@qq.com',//邮箱
                    'member_number'=>'1140',//会员号
                    'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                    'api_token'=>$result['Result']['api_token'],//Api Token	
        );

        $url = "http://demo.yuudidi.com/api/v1/member_register";
         
        $result = curl_post_http($url,$data);
        var_dump($result);
    }

    //获取会员详情
    public function get_memberdetails(){

        $data=array('member_username' =>'kanye123',//用户名
                    'member_id'=>'6601',//会员号
                    'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                    'api_token'=>'tjLJN9p9h4pIzmn23C2mGrv3w4NPNctg0TkSVSPuF0TQe66u5ndkdqaPVIPA',//Api Token	
        );

        
        $url = "http://demo.yuudidi.com/api/v1/get_memberdetails";
         
        $result = curl_post_http($url,$data);
        var_dump($result);
    }
    //获取会员BTUSD
    public function get_membercoins(){
        $data=array('member_username' =>'kanye123',//用户名
                    'member_id'=>'6601',//会员号
                    'merchant_id'=>'541',//平台商编号
                    'api_token'=>'OdFwrYAzhhlElpknn1RPg1c5GzcYq91bCvIzYlaWF90RNnMpH4SsUb84qkYr',//Api Token	
                  );

        $url = "http://demo.yuudidi.com/api/v1/get_membercoins";
        $result = curl_post_http($url,$data);
        var_dump($result);
    }


    //更新会员详情
    public function member_update(){

            $result = $this->yjToken();
            $data = array('member_id' =>'1135',//会员编号
                              'name' =>'kanye',//会员姓名
                              'member_username'=>'kanye123',//用户名
                              'password' =>'a84511076',//密码
                              'mobile_phone'=>'1527439227',//手机号码
                              'email'=>'494178252@qq.com',//邮箱
                              'member_number'=>'1110',//会员号
                              'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                              'api_token'=>$result['Result']['api_token'],//API Token	
                              'account_holder_name'=>'韩政',//持卡人姓名
                              'bank_name'=>'中国银行',//银行名称
                              'branch_name'=>'武汉支行',//开户支行
                              'bank_account_no'=>'623210231547895412',//银行账号
                              'alipay_account'=>'15827439217',//阿里账号
                );
        
                $url = "http://demo.yuudidi.com/api/v1/member_update";

                $result = curl_post_http($url,$data);
                var_dump($result);        
            		
    }


     //跳转会员到Yuudidi.com
     public function redirect_web($yuudidid_username,$member_password,$gotopage,$merchant_id,$merchant_token){
           // $result = $this->yjToken();

            $param = "member_username=".$yuudidid_username."&password=".$member_password."&gotopage=".$gotopage."&merchant_id=".$merchant_id."&api_token=".$merchant_token."" ;
            var_dump($param);
            $redirect_param = base64_encode( $param ) ;
            var_dump($redirect_param);

            header( 'Location:http://demo.yuudidi.com/api/v1/redirect_web?' . $redirect_param ) ;

      }

      /*会员购买订单
        备注
        1.登录到平台商后台管理后，您可以设置默认返回URL和默认通知URL。
        2.如果有提交参数的返回URL和通知URL将被优先处理，否则将使用默认值。
        3.订单完成后将通过返回URL跳转会员到平台商网站。
        4.通知URL用于将订单信息通过通知（POST）到平台商服务器。
        5.同一会员身份不能在1分钟内提交相同的货币价值金额。
        6.所有金额必须是整数，至少100和100的倍数。*/
      public function member_buy(){

                $result = $this->yjToken();
                $data = array('member_id' => '1136',//会员编号
                              'member_username'=>'quange66',//用户名
                              'currency_value' =>500,//人民币购买数额
                              'min_currency_value'=>200,//最小人民币数额
                              'max_currency_value'=>400,//最大人民币数额
                              'member_number'=>'1140',//会员号
                              'merchant_order_no'=>'MN6451120132',//平台商订单号
                              'api_token'=>$result['Result']['api_token'],//API Token	
                              'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                              'return_url'=>'http://139.224.64.194:9002/papi/Index/reurldate',//返回网址
                              'notify_url'=>'http://139.224.64.194:9002/papi/Index/notiyurl',//通知网址
                              'alipay_account'=>'15827439217',//阿里账号
                );
                $merchant_id = $data['merchant_id'];
                $apitoken = $data['api_token'];
                $url = "http://demo.yuudidi.com/api/v1/member_buy";
                $result = curl_post_http($url,$data);
                $ret = json_decode($result,true);
                
                if($ret['Status']=="Success"){
                    $this->redirect_web('quange66','a84511076','/zh/member/currentorder',$merchant_id,$apitoken);
                }else{
                    var_dump($ret);
                    return false;
                }
                
                  
      }

      //获取订单详情
      public function get_orderdetails(){
            $result = $this->yjToken();
            $data = array('order_no' =>'BU20181137-1346',//订单号           
                          'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                          'api_token'=>$result['Result']['api_token'],//Api Token	
            );
            $url = "http://demo.yuudidi.com/api/v1/get_orderdetails";
            $result = curl_post_http($url,$data);
            var_dump($result);
      }

      //获取转账详情
      public function get_transferdetails(){
            $result = $this->yjToken();
            $data=array('transfer_id' =>'BU20181137-1346',//转账编号     
                        'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                        'api_token'=>$result['Result']['api_token'],//Api Token	
                );
                $url = "http://demo.yuudidi.com/api/v1/get_transferdetails";
                $result = curl_post_http($url,$data);
                var_dump($result);
      }

      //按日期获取转账概况
      public function membertransfer_summary(){
            $data=array('fromdate' =>'BK2018114-10123',//从日期     
                        'todate'=>'541',//到日期
                        'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                        'api_token'=>$result['Result']['api_token'],//Api Token	
             );
            $url = "http://demo.yuudidi.com/api/v1/membertransfer_summary";
            $result = curl_post_http($url,$data);
            var_dump($result);
      }

     //平台商转账BTUSD给会员
     public function merchant2member(){
        $result = $this->yjToken();
                $data=array('member_id' =>'1135',//会员编号     
                            'member_username'=>'kanye123',//用户名
                            'btusd'=>'20',//BTUSD
                            'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                            'api_token'=>$result['Result']['api_token'],//Api Token	
                 );
                $url = "http://demo.yuudidi.com/api/v1/membertransfer_summary";
                $result = curl_post_http($url,$data);
                var_dump($result);
     }
    //会员转账BTUSD给平台商
    public function member2merchant(){
        $result = $this->yjToken();
        $data=array('member_id' =>'	1135',//会员编号     
                    'member_username'=>'kanye123',//用户名
                    'btusd'=>'10',//BTUSD
                    'merchant_id'=>$result['Result']['merchant_id'],//平台商编号
                    'api_token'=>$result['Result']['api_token'],//Api Token	
         );
        $url = "http://demo.yuudidi.com/api/v1/member2merchant";
        $result = curl_post_http($url,$data);
        var_dump($result);
     } 

      //会员出售订单
      public function order_sell(){
        $data = array('member_id' =>'',//会员编号
                      'member_username'=>'',//用户名
                      'currency_value' =>'',//人民币购买数额
                      'min_currency_value'=>'',//最小人民币数额
                      'max_currency_value'=>'',//最大人民币数额
                      'member_number'=>'',//会员号
                      'api_token'=>'',//API Token	
                      'return_url'=>'',//返回网址
                      'notify_url'=>'',//通知网址
                      'merchant_id'=>'',//平台商编号
                      'alipay_account'=>'',//阿里账号
                      'withdraw_no'=>'',//平台商提款号
                      'account_holder_name'=>'',//持卡人姓名
                      'bank_name'=>'',//银行名称
                      'branch_name'=>'',//开户支行
                      'bank_account_no'=>'',//银行账号
        );
        $url = "http://demo.yuudidi.com/api/v1/member2merchant";
        $result = curl_post_http($url,$data);
        var_dump($result);
     } 


     public function reurldate(){
         $data = $_POST;
         var_dump($data);
     }

     public function notiyurl(){
         $data = $_POST;
         var_dump($data);
     }

 
  

    
}
