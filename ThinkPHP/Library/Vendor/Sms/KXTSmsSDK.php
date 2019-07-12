<?php
/*
 * 功能：短信来了SDK核心文件
 * 版本：1.3
 * 日期：2016-07-01
 * 说明：
 * 以下代码只是为了方便客户测试而提供的样例代码，客户可以根据自己网站的需要，按照技术文档自行编写,并非一定要使用该代码。
 * 该代码仅供学习和研究使用，只是提供一个参考。
 * 网址：http://www.laidx.com
 */
 class KXTSmsSDK{
	private $Address;
	private $Port;
	private $Account;
	private $Token;
	function __construct($Address,$Port,$Account,$Token){
		$this->Address = $Address;
		$this->Port = $Port;
		$this->Account = $Account;
		$this->Token = $Token;
	}
	/**
     * 发起HTTP请求
     */
     function curl_post($url,$data,$header,$post=1)
     {
       //初始化curl
       $ch = curl_init();
       //参数设置  
       $res= curl_setopt($ch, CURLOPT_URL,$url);//请求地址
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);//不验证host
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);//不验证证书
       curl_setopt($ch, CURLOPT_HEADER, 0);
	     curl_setopt($ch, CURLOPT_TIMEOUT, 50);
       curl_setopt($ch, CURLOPT_POST, $post);//请求方式
       if($post){
          curl_setopt($ch, CURLOPT_POSTFIELDS, $data);//post数据
	     }
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
       curl_setopt($ch,CURLOPT_HTTPHEADER,$header);//设置头信息
       $result = curl_exec ($ch);
       //连接失败
       if($result == FALSE){
          $result = "";
       }
       curl_close($ch);
       return $result;
     }
	/**
    * 发送短信
    * @param mobile 手机号码
    * @param body 内容
	* @param rType 响应数据类型   0 Json 类型，1 xml 类型
    */       
    function send($mobile, $body,$rType,$extno)
    {
        // 生成请求
		$body = urlencode($body);//url编码
		$url = "http://$this->Address:$this->Port/SMS/Send";
		$data = "account=$this->Account&token=$this->Token&mobile=$mobile&content=$body&type=$rType&extno=$extno";
        // 生成包头
        $header = array("charset=utf-8");
        // 发送请求
        return $this->curl_post($url,$data,$header,1);
    }
	/**
    * 获取短信状态
    * @param smsId 短信Id
	* @param rType 响应数据类型 0 Json 类型，1 xml 类型
    */
    function smsStatus($smsId, $rType)
    {
        // 生成请求
		$url = "http://$this->Address:$this->Port/SMS/SMSStatus";
		$data = "account=$this->Account&token=$this->Token&smsid=$smsId&type=$rType";
        // 生成包头
        $header = array("charset=utf-8");
        // 发送请求
        return $this->curl_post($url,$data,$header,1);
    }
	/**
    * 获取剩余可发短信条数
	* @param rType 响应数据类型 0 Json 类型，1 xml 类型
    */
    function smsNum($rType)
    {
        // 生成请求
		$url = "http://$this->Address:$this->Port/SMS/SMSNum";
		$data = "account=$this->Account&token=$this->Token&type=$rType";
        // 生成包头
        $header = array("charset=utf-8");
        // 发送请求
        return $this->curl_post($url,$data,$header,1);
    }
 }
 ?>