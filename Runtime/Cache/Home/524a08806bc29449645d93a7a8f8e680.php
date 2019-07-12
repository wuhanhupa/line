<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<meta name="renderer" content="webkit">
	<meta name="format-detection" content="telephone=no">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title><?php echo C('web_title');?></title>
	<meta name="Keywords" content="<?php echo C('web_keywords');?>">
	<meta name="Description" content="<?php echo C('web_description');?>">
	<meta name="author" content="zuocoin.com">
	<meta name="coprright" content="zuocoin.com">
	<link rel="shortcut icon" href=" /favicon.ico"/>
	<link rel="stylesheet" href="/Public/Home/css/zuocoin.css"/>
	<link rel="stylesheet" href="/Public/Home/css/style.css?v=<?php echo ($randVersion); ?>"/>
	<link rel="stylesheet" href="/Public/Home/css/ui.css?v=<?php echo ($randVersion); ?>"/>
	<link rel="stylesheet" href="/Public/Home/css/new_style.css?v=<?php echo ($randVersion); ?>"/>
	<link rel="stylesheet" href="/Public/Home/css/slide-unlock.css"/>
	<link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css"/>
    <script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
	<script type="text/javascript" src="/Public/Home/js/jquery.flot.js"></script>
	<script type="text/javascript" src="/Public/Home/js/jquery.cookies.2.2.0.js"></script>
	<script type="text/javascript" src="/Public/Home/js/jquery.slideunlock.js"></script>
	<script type="text/javascript" src="/Public/layer/layer.js"></script>
	<script type="text/javascript" src="/Public/Home/js/util.js?v=<?php echo ($randVersion); ?>"></script>
	<script type="text/javascript" src="/Public/Home/js/chat.js"></script>
</head>
<body>
<div class="header bg_w" id="trade_aa_header">
	<div class="hearder_top">
		<div class="autobox po_re zin100" id="header">
			<div class="hot-coins-price">
				<ul class="topprice"></ul>
            </div>
			<div class="right orange" id="login">
				<?php if(($_SESSION['userId']) > "0"): ?><dl class="mywallet">
						<dt id="user-finance">
						<div class="mywallet_name clear">
							<a href="/finance/"><?php echo (session('userName')); ?></a><i></i>
						</div>
						<div class="mywallet_list" style="display: none;">
							<div class="clear">
								<ul class="balance_list">
									<h4>可用余额</h4>
									<li>
										<a href="javascript:void(0)"><em style="margin-top: 5px;" class="deal_list_pic_cny"></em><strong>USDT：</strong><span><?php echo ($userCoin_top['cny']*1); ?></span></a>
									</li>
								</ul>
								<ul class="freeze_list">
									<h4>委托冻结</h4>
									<li>
										<a href="javascript:void(0)"><em style="margin-top: 5px;" class="deal_list_pic_cny"></em><strong>USDT：</strong><span><?php echo ($userCoin_top['cnyd']*1); ?></span></a>
									</li>
								</ul>
							</div>
							<div class="mywallet_btn_box">
								<a href="/finance/index">充值</a>
								<a href="/finance/index">提现</a>
								<!--<a href="/finance/index">转入</a>
								<a href="/finance/index">转出</a>-->
								<a href="/finance/mywt.html">委托管理</a>
								<a href="/finance/mycj.html">成交查询</a>
							</div>
						</div>
						</dt>
						<dd>
							ID：<span><?php echo (session('userId')); ?></span>
						</dd>
						<dd>
							<a href="<?php echo U('Login/loginout');?>">退出</a>
						</dd>
					</dl>
					<?php else: ?> <!-- 登陆前 -->
					<div class="orange">
						<span class="zhuce"><a class="orange" href="<?php echo U('Login/register');?>">注册</a></span> |
						<a class="orange" href="<?php echo U('Login/index');?>" >登录</a>
					</div><?php endif; ?>
            </div>
            <div class="gui-app-download right">
                <a href="https://www.gte.io/papi/help/download" >APP下载</a>
                <div class="gui-app-qrcode">
                    <h4>扫码下载gte.io App</h4>
                    <img src="/Public/Home/images/app-qrcode.png" alt="app下载">
                </div>
            </div>
			<div class="nav  nav_po_1" id="menu_nav" style=" height: 30px;">
				<ul>
					<li>
						<a href="/" id="index_box">首页</a>
					</li>
					<li>
						<a id="<?php echo ($daohang[0]['name']); ?>_box" href="/<?php echo ($daohang[0]['url']); ?>"><?php echo ($daohang[0]['title']); ?></a>
					</li>

					<li>
						<a id="trade_box" href="<?php echo U('Trade/index');?>"><span>交易中心</span>
							<img src="/Public/Home/images/down.png"></a>
						<div class="deal_list " style="display: none;    top: 36px;">
							<dl id="menu_list_json"></dl>
							<div class="sj"></div>
							<div class="nocontent"></div>
						</div>
					</li>

					<!--<li>
						<a id="<?php echo ($daohang[4]['name']); ?>_box" href="/<?php echo ($daohang[4]['url']); ?>"><?php echo ($daohang[4]['title']); ?></a>
					</li>-->

					<li>
						<a id="<?php echo ($daohang[1]['name']); ?>_box" href="/<?php echo ($daohang[1]['url']); ?>"><?php echo ($daohang[1]['title']); ?></a>
					</li>
					<li>
						<a id="<?php echo ($daohang[2]['name']); ?>_box" href="/<?php echo ($daohang[2]['url']); ?>"><?php echo ($daohang[2]['title']); ?></a>
					</li>
					<!--<li>
						<a href="/Vote/index">上币申请</a>
					</li>-->
					<li>
						<a id="<?php echo ($daohang[3]['name']); ?>_box" href="/<?php echo ($daohang[3]['url']); ?>"><?php echo ($daohang[3]['title']); ?></a>
					</li>
					<!--<?php if(is_array($daohang)): $i = 0; $__LIST__ = $daohang;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li>
							<a id="<?php echo ($vo['name']); ?>_box" href="/<?php echo ($vo['url']); ?>"><?php echo ($vo['title']); ?></a>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>-->

				</ul>
			</div>
		</div>
	</div>
	<div style="clear: both;"></div>
	<div class="autobox clear" id="trade_clear">
		<div class="logo">
			<a href="/"><img src="/Upload/public/<?php echo ($C['web_logo']); ?>" alt=""/></a>
		</div>
		<!-- <div class="phone right">
			<span class="iphone" style=""></span><a href="http://wpa.qq.com/msgrd?V=3&amp;uin=<?php echo C('contact_qq')[0];?>&amp;Site=QQ客服&amp;Menu=yes" target="_blank" class="qqkefu"></a>
		</div> -->
	</div>
</div>

<script>
	// 货币汇率

	// var usd_Rate = {};

	// var exchangeRata = function () {
	// 	$.ajax({
	// 		url: ' https://data.block.cc/api/v1/exchange_rate',
    //         type: 'GET',
    //         dataType: 'jsonp',
    //         jsonp: 'callback',
    //         crossDomain: true,
    //         success: function(data) {
    //             console.log(data);
	// 			if(data.message == 'success') {
	// 				usd_Rate = data.data.rates;
	// 			}
    //         },
    //         error: function(msg) {
    //             console.log(msg);
    //         }
	// 	})
	// }
	// exchangeRata();

	// 添加热门数字货币实时价格

    var coinsData = null;

	var getCoinsData = function() {
        
        clearTimeout(coinsData);
        coinsData = null;

        $.ajax({
            url: '/Ajax/getHotCoin',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                    
                    var array = data;
                    var html = '';
					var arrow = '';
                    var color = '';
                    if(data.length) {
                        for(var i= 0;i < array.length; i++) {
                            color = array[i].change > 0 ? 'color-red' : 'color-green';
                            arrow = array[i].change > 0 ? '↑' : '↓';
                            html += '<li>'+ array[i].name +': <span class="topnum '+ color +'">'+ parseFloat(array[i].new_price).toFixed(4) +'</span><i class="icon-arrow-down '+ color +'">'+ arrow +'</i> </li>'
                        }
                        $('.hot-coins-price ul').html(html);
                    }
                    
            },
            error: function(error) {
                console.log(error);
            }
        })
        
        coinsData = setTimeout(function() {
            getCoinsData();
        },5000)

    }
    getCoinsData();

	$.getJSON("/Ajax/getJsonMenu?t=" + Math.random(), function (data) {
		if (data) {
			var list = '';
			for (var i in data) {
				list += '<dd><a href="/Trade/index/market/' + data[i]['name'] + '"><img src="/Upload/coin/' + data[i]['img'] + '" style="width: 18px; margin-right: 5px;">' + data[i]['title'] + '</a></dd>';
			}
			$("#menu_list_json").html(list);
		}
	});

	$('#trade_box').hover(function () {
		$('.deal_list').show()
	}, function () {
		$('.deal_list').hide()
	});
	$('.deal_list').hover(function () {
		$('.deal_list').show()
	}, function () {
		$('.deal_list').hide()
	});
    
	$('#user-finance').hover(function () {
		$('.mywallet_list').show();
	}, function () {
		$('.mywallet_list').hide()
	});
</script>
<!--头部结束-->
<style>
    .form_sub_btn {
        cursor: pointer;
        width: 100%;
        display: block;
        line-height: 40px;
        text-align: center;
        font-size: 14px;
        background-color: #0a8668;
        color: #fff;
        border: 0;
    }
	.reg_box1 {
		margin-top: 11px;
		margin-bottom: 48px;
		padding-bottom: 38px;
		min-height: 400px;
	}


	
	/* input::-webkit-input-placeholder, textarea::-webkit-input-placeholder { 
	color: #ff0000; 
	} 
	input:-moz-placeholder, textarea:-moz-placeholder { 
	color: #ff0000;
	} 
	input::-moz-placeholder, textarea::-moz-placeholder { 
	color: #ff0000;
	} 
	input:-ms-input-placeholder, textarea:-ms-input-placeholder { 
	color: #ff0000;
	}  */


	
</style>
<div class="autobox" id="reg-step1" style="position:absolute;left:50%;margin-left:-600px;">
	<div class="login_step">
		<ul class="order clear">
			<li class="orange"><i class="order_1_1"></i>用户注册
				<div class="order_line"></div></li>
			<li><i class="order_2"></i>设置交易密码
				<div class="order_line"></div></li>
			<li><i class="order_3"></i>实名认证
				<div class="order_line"></div></li>
			<li><i class="order_4"></i>成功</li>
		</ul>
	</div>
	<div class="reg_box1">
		<div>
			<div id="reg-control" class="btn-group" style="text-align: center; ">
				
			</div>
			<div id="reg_index" class="reg_wrap">
				<div class="reg_input_box reg-fb" id="username_reg">
					<div class="reg_title">用户名：</div>
					<input type="text" id="username" value="" onblur="regusername()" />
					<div id="username-msg" class="form_explain" data-explain="6-18个字符,可使用字母、数字,需以字母开头<em></em>" style="display: none;">
						3-13个字符,可使用字母、数字,需以字母开头<em></em>
					</div>
                </div>
                <!-- 新增手机号验证 -->
                <div class="reg_input_box reg-fb" id="phone_reg">
					<div class="reg_title">手机号：</div>
					<input type="text" id="phone" value="" onblur="regphone()" />
					<div id="phone-msg" class="form_explain" data-explain="11位有效手机号<em></em>" style="display: none;">
						11位有效手机号<em></em>
					</div>
                </div>
                <div class="reg_input_box reg-fb" id="">
					<div class="reg_title">验证码：</div>
					<input type="text" id="verify" value="" onblur="regverify()" style="width: 165px; box-shadow: none; " />
					<div id="verify-msg" class="form_explain" data-explain="输入验证码<em></em>" style="display: none;">
						输入验证码<em></em>
					</div>
					<a href="javascript:;" class="codeImg ui-send-code" onclick="getCode()">点击获取验证码</a>
                </div>
                <!-- 手机号验证end -->

				<!-- <div class="reg_input_box reg-fb" id="reg_first_verify">
					<div class="reg_title">验证码：</div>
					<input type="text" id="verify" value="" onblur="regverify()" style="width: 165px; box-shadow: none; " />
					<div id="verify-msg" class="form_explain" data-explain="输入验证码<em></em>" style="display: none;">
						输入验证码<em></em>
					</div>
					<img class="codeImg" src="<?php echo U('Verify/code');?>" title="换一张" onclick="this.src=this.src+'?t='+Math.random()">
				</div> -->
				<div class="reg_input_box reg-fb">
					<div class="reg_title">登录密码：</div>
					<input type="password" id="password" value="" onblur="regpassword()" autocomplete="off" />
					<div id="password-msg" class="form_explain" data-explain="密码六位以上<em></em>" style="display: none;">
						密码六位以上<em></em>
					</div>
				</div>
				<!-- <div class="reg_input_box reg-fb">
					<div class="reg_title">重复密码：</div>
					<input type="password" id="repassword" value="" onblur="regrepassword()" autocomplete="off" />
					<div id="repassword-msg" class="form_explain" data-explain="重复输入密码，两次需要一致<em></em>" style="display: none;">
						重复输入密码，两次需要一致<em></em>
					</div>
				</div> -->
				<div class="reg_input_box reg-fb">
					<div class="reg_title">邀请码：</div>
					<input id="sharecode" name="sharecode" type="text" placeholder="非必填" value="" />
				</div>
				<div class="reg_radio_box">
					<label>注册即视为同意 <a href="javascript:void(0)" onclick="registerAgreement();">用户注册协议</a></label>
                </div>
                <div class="formbody">
                    <button href="javascript:;" class="form_sub_btn">提交</button>
                </div>
				<!-- <div class="formbody">
						<div id="slider">
							<div id="slider_bg"></div>
							<span id="label">>></span> <span id="labelTip"></span>
						</div>	
					   <input style="display:none;" type="button" class="zuocoin_btn" name="index_submit" id="loginSubmin" onclick="Update();" value="立即注册" title="立即注册" style="width: 320px;">
				</div> -->
				
			</div>
		</div>
	</div>
</div>

	<section class="canvas-wrap" style="min-height:700px;">
		<div id="canvas" class="gradient"></div>
	</section>


<!-- <div class="autobox" style="margin-bottom: 30px">
	<ul class="safety_tips_ul clear">
		<li>
			<div class="safety_img safety_img_1"></div>
			<h4>系统可靠</h4>
			<p>银行级用户数据加密、动态身份验证，多级风险识别控制，保障交易安全</p>
		</li>
		<li>
			<div class="safety_img safety_img_2"></div>
			<h4>资金安全</h4>
			<p>钱包多层加密，离线存储于银行保险柜，资金第三方托管，确保安全</p>
		</li>
		<li>
			<div class="safety_img safety_img_3"></div>
			<h4>快捷方便</h4>
			<p>充值即时、提现迅速，每秒万单的高性能交易引擎，保证一切快捷方便</p>
		</li>
		<li>
			<div class="safety_img safety_img_4"></div>
			<h4>服务专业</h4>
			<p>专业的客服团队，400电话和24小时在线QQ，VIP一对一专业服务</p>
		</li>
	</ul>
</div> -->

<script>
    var lock = true;

    function getCode() {
        var phone = $('#phone').val();
        if(!phone) {
            layer.msg('请输入手机号',{icon: 2});
            return false;
        }
        if(!lock) {
            layer.msg("请勿重复提交",{icon: 2});
            return false;
        } else {
            lock = false;
        }

        if(!$('.codeImg').hasClass('active') && !$('.codeImg').hasClass('re-send')) {
            $.ajax({
                url: '/home/login/userSmS',
                type: 'POST',
                data: {
                    phone: phone
                },
                dataType: 'json',
                success: function(res) {
                    //console.log(res);
                    var timer = null;
                    var count = 60;
                    if(res.status== "0000") {

                        timer = setInterval(function() {
                            count-- ;
                            if(count < 0) {
                                clearInterval(timer);
                                timer = null;
                                $('.codeImg').removeClass('active').html('重新发送').addClass('re-send').css({'color':'#fff','background':'#de5959'});
                            } else {
                                $('.codeImg').addClass('active').html(count + '秒').css({'color':'#333','background':'none'});
                            }
                            
                        },1000);
                    } else if(res.status == "2005") {
                        layer.msg('验证码失效',{icon: 2});
                    } else if(res.status == '2008') {
                    	layer.msg('手机号码已注册',{icon: 2});
                    } else {
                        layer.msg(res.info,{icon: 2});
                    }
                    lock = true;
                },
                error: function(msg) {  
                    //console.log('error',msg);
                }
            })
        } else if($('.codeImg').hasClass('re-send') && !$('.codeImg').hasClass('active')){
            $.ajax({
                url: '/home/login/reSmS',
                type: 'POST',
                data: {
                    phone: phone
                },
                dataType: 'json',
                success: function(res) {
                    //console.log(res);
                    var timer = null;
                    var count = 60;
                    if(res.status== "0000") {

                        timer = setInterval(function() {
                            count-- ;
                            if(count < 0) {
                                clearInterval(timer);
                                timer = null;
                                $('.codeImg').removeClass('active').html('重新发送').addClass('re-send').css({'color':'#fff','background':'#de5959'});
                            } else {
                                $('.codeImg').addClass('active').html(count + '秒').css({'color':'#333','background':'none'});
                            }
                            
                        },1000);
                    } else if(res.status == "2005") {
                        layer.msg('验证码失效',{icon: 2});
                    } else {
                        layer.msg(res.info,{icon: 2});
                    }

                    lock = true;
                },
                error: function(msg) {  
                    //console.log('error',msg);
                }
            })
        } else {
            return false;
        }
        
    }

	function registerAgreement(){
		layer.open({
			type:2,
			skin:'layui-layer-rim', //加上边框
			area:['800px','600px'], //宽高
			title:'注册协议', //不显示标题
			content:"<?php echo U('Login/webreg');?>"
		});
	}


	$('input').focus(function(){
		var t=$(this);
		if(t.attr('type')=='text'||t.attr('type')=='password')
			t.css({'box-shadow':'0px 0px 3px #1583fb','border':'1px solid #1583fb'});
		if(t.val()==t.attr('placeholder'))
			t.val('');
	});
	$('input').blur(function(){
		var t=$(this);
		if(t.attr('type')=='text'||t.attr('type')=='password')
			t.css({'box-shadow':'none','border':'1px solid #e1e1e1'});
		if(t.attr('type')!='password'&&!t.val())
			t.val(t.attr('placeholder'));
	});
	$('.reg_input_box input').each(function(i,d){
		$(d).focus(function(){
			var oRegMsg=$('#'+$(this).attr('id')+'-msg');
			oRegMsg.attr('class')=='form_explain_error'?oRegMsg.attr('class','form_explain').html(oRegMsg.attr('data-explain')).show():oRegMsg.show();
		})
		$(d).blur(function(){
			var oRegMsg=$('#'+$(this).attr('id')+'-msg');
			$(this).parent().find('.form_explain').hide();
		})
	})
	var mbTest_username=/^(?![^a-zA-Z]+$)(?!\D+$).{5,15}$/;
	var mbTest_password=/^[a-zA-Z0-9_]{5,15}$/;


	//输入框消息
	function formMsg(o, status, msg){
		$('#'+o+'-msg').attr('class', 'form_explain_'+(status?'pass':'error')).html((typeof msg == 'undefined'? '': msg)+'<em></em>').show();
		return true;
	}


	function regusername(){
		var username = $('#username').val();
		if(username==""||username==null){
			formMsg('username', 0, '请输入用户名');
			return false;
		}
		$.post("<?php echo U('Login/chkUser');?>",{username:username},function(ret){
			if(ret.status){
				formMsg('username', 1);
				return true;
			}else{
				formMsg('username', 0, ret.info);
				return false;
			}
		},'json');
	}

    function regphone(){
        var reg = /^1[3|4|5|7|8][0-9]{9}$/;
		var phone = $('#phone').val();
		if(phone==""||phone==null){
			formMsg('phone', 0, '请输入手机号');
			return false;
		} else if(!reg.test(phone)){
            formMsg('phone', 0, '手机号不正确');
			return false;
        } else {
            formMsg('phone', 1);
			return true;
        }
		
	}

	function regverify(){
		var verify = $('#verify').val();
		if(verify==""||verify==null){
			formMsg('verify', 0, '请输入验证码');
			return false;
		}else{
			formMsg('verify', 1);
			return true;
		}
	}

	function regpassword(){
		var password = $('#password').val();
		if(password==""||password==null){
			formMsg('password', 0, '请输入登录密码');
			return false;
		}else{
			if(!mbTest_password.test(password)){
				formMsg('password', 0, '登录密码格式错误 (6~16个字符)');
				return false;
			}else{
				formMsg('password', 1);
				return true;
			}
		}
	}
	
	// function regrepassword(){
	// 	var password = $('#password').val();
	// 	var repassword = $('#repassword').val();
	// 	if(repassword==""||repassword==null){
	// 		formMsg('repassword', 0, '请输入确认密码');
	// 		return false;
	// 	}else{
	// 		if(!mbTest_password.test(repassword)){
	// 			formMsg('repassword', 0, '登录密码格式错误 (6~16个字符)');
	// 			return false;
	// 		}else{
	// 			if(password!=repassword){
	// 				formMsg('repassword', 0, '确认登录错误');
	// 				return false;
	// 			}else{
	// 				formMsg('repassword', 1);
	// 				return true;
	// 			}
	// 		}
	// 	}
	// }
	

	function Update(){
		var username=$("#username").val();
        var phone = $("#phone").val();
		var password=$("#password").val();
		// var repassword=$("#repassword").val();
		var sharecode=$("#sharecode").val();
		var verify=$("#verify").val();

		if(username==""||username==null){
			formMsg('username', 0, '请输入用户名');
			// slider.init();
			return false;
		}
        if(phone==""||phone==null){
			formMsg('phone', 0, '请输入手机号');
			// slider.init();
			return false;
		}
		if(password==""||password==null){
			formMsg('password', 0, '请输入登录密码');
			// slider.init();
			return false;
		}
		// if(repassword==""||repassword==null){
		// 	formMsg('repassword', 0, '请输入确认密码');
		// 	slider.init();
		// 	return false;
		// }
		// if(password!=repassword){
		// 	formMsg('repassword', 0, '确认密码错误');
		// 	slider.init();
		// 	return false;
		// }
		
		if(verify==""||verify==null){
			formMsg('verify', 0, '请输入验证码');
			// slider.init();
			return false;
		}
		$.post("<?php echo U('Login/upregister');?>",{username:username,password:password,phone:phone,sharecode:sharecode,verify:verify},function(data){
			if(data.status==1){

				layer.msg(data.info,{icon:1});
				$.cookies.set('cookie_username',username);
				window.location='/Login/register2';

			}else{
				layer.msg(data.info,{icon:2});
				if(data.url){
					window.location=data.url;
				}else{
					// slider.reset();
				}
			}
		},"json");
	}
	
	$('.form_sub_btn').click(function(){
        Update();
    })

	//$(function () {
		// var slider = new SliderUnlock("#slider",{
		// 		initLabelTip : "拖动滑块提交",
		// 		successLabelTip : "欢迎注册本网站"	
		// 	},function(){
		// 		Update();
		// 	});
		// slider.init();
	//})

	
	
</script>
<div class="footer">
    <div class="fkicon">
		<div class="ft-links">
			<a href="/Article/index" title="Ticket"><i class="ft-icon ui-icon ui-icon-qm"></i></a>
			<a href="https://twitter.com/gteioex" target="_blank"  rel="noopener noreferrer" title="Twitter"><i class="ft-icon ui-icon ui-icon-twitter"></i></a>
			<a href="https://t.me/gteioex" target="_blank"  rel="noopener noreferrer" title="Telegram"><i class="ft-icon ui-icon ui-icon-telegram"></i></a>
			<!-- <a href="https://www.instagram.com" target="_blank"  rel="noopener noreferrer" title="Instagram"><i class="ft-icon ui-icon ui-icon-instagram"></i></a>-->
			<a href="mailto:pr@gte.io"  rel="noopener noreferrer" title="Email"><div class="ft-icon ui-icon ui-icon-email"></div></a>
			<!-- <a href="https://github.com" target="_blank"  rel="noopener noreferrer" title="Github"><div class="ft-icon ui-icon ui-icon-github"></div></a>-->
		</div>
        <div class="fxts">
            <p><i>!</i>比特币等密码币的交易存在风险，在全球范围内一周7天，一天24小时无休止交易，没有每日涨停跌停限制，价格受到新闻事件，各国政策，市场需求等多种因素影响，浮动很大。我们强烈建议您事先调查了解，在自身所能承受的风险范围内参与交易。</p>
        </div>
    </div>
</div>
<div class="footer_bottom">
	<div class="autobox" style="height: 40px;margin-top: 5px;">
		<span style="display: inline-block;color:#A6A9AB;">CopyRight© 2013-2016 <?php echo ($C['web_name']); ?>交易平台 All Rights Reserved &nbsp;&nbsp;|&nbsp;&nbsp;<a href="http://www.miitbeian.gov.cn/publish/query/indexFirst.action" target="_blank"  rel="noopener noreferrer"><?php echo ($C['web_icp']); ?></a><span style="display: inline-block; color:#A6A9AB"></span></span>
	</div>
	<!-- 原安全验证位置 -->
</div>
<!--代码部分begin-->
<!--<div id="floatTools" class="rides-cs" style="height: 200px;">
	<div class="floatL">
		<a id="aFloatTools_Show" class="btnOpen" title="查看在线客服" style="top: 20px; display: block" href="javascript:void(0);">展开</a>
		<a id="aFloatTools_Hide" class="btnCtn" title="关闭在线客服" style="top: 20px; display: none" href="javascript:void(0);">收缩</a>
	</div>
	<div id="divFloatToolsView" class="floatR" style="display: none; width: 140px; background: #d45858; height: 160px;">
		<div class="cn" style="margin-top: 36px;">
			<h3 class="titZx">官方在线客服</h3>
			<ul id="jisuan_qq">
                <li><span>QQ </span><a target="_blank" href="tencent://message/?Menu=yes&uin=737045314&Site=www.gte.io&Service=300&sigT=45a1e5847943b64c6ff3990f8a9e644d2b31356cb0b4ac6b24">737045314</a></li>
				<li><span>电报</span> <a target="_blank" href="https://t.me/gteioex">@gteioex</a></li>
			</ul>
		</div>
	</div>
</div>-->
<script>
	$(function () {
		$("#floatTools").hover(function () {
			$('#divFloatToolsView').animate({
				width: 'show',
				opacity: 'show'
			}, 100, function () {
				$('#divFloatToolsView').show();
			});
			$('#aFloatTools_Show').hide();
			$('#aFloatTools_Hide').show();
		}, function () {
			$('#divFloatToolsView').animate({
				width: 'hide',
				opacity: 'hide'
			}, 100, function () {
				$('#divFloatToolsView').hide();
			});
			$('#aFloatTools_Show').show();
			$('#aFloatTools_Hide').hide();
		});
		//$("#divFloatToolsView").height(36 + $("#jisuan_qq li").length * 40);
	});
</script>
<script type="text/javascript" src="/Public/Home/js/jquery.cookies.2.2.0.js"></script>
<script>
    // header里 USDT 用户账户余额及委托冻结
    function getAccountInfo() {
        $.ajax({
            url:'/Ajax/getUserCoin',
            type: 'GET',
            dataType: 'json',
            success: function(res) {
                if(res.status == '1' && res.data) {
                    $('.mywallet_list').find('.balance_list li span').html(res.data.usdt);
                    $('.mywallet_list').find('.freeze_list li span').html(res.data.usdtd);
                } 
            },
            error: function(msg) {
                console.log('error',msg)
            }
        })
    }
    getAccountInfo();

	function NumToStr(num) {
		if (!num) return num;
		num = Math.round(num * 100000000) / 100000000;
		num = num.toFixed(8);
		var min = 0.0001;
		var times = 0;
		var arr;
		if (num <= min) {
			times = 0;
			while (num <= min) {
				num *= 10;
				times++;
				if (times > 100) break;
			}
			num = num + '';
			arr = num.split(".");
			for (var i = 0; i < times; i++) {
				arr['1'] = '0' + arr['1'];
			}
			return arr[0] + '.' + arr['1'] + '';
		}
		return num.toFixed(8) + ' ';
	}

	var is_login = <?php echo (session('userId')); ?>;

	if (window.location.hash == '#login') {
		if (!is_login) {
			window.location.href = "<?php echo U('Login/index');?>";
		}
	}

	if (is_login) {
		$.getJSON("/Ajax/allfinance?t=" + Math.random(), function (data) {

			$('#user_finance').html('¥' + data);
		});
	}


	var cookieValue = $.cookies.get('cookie_username');
	if (cookieValue != '' && cookieValue != null) {
		$("#login_username").val(cookieValue);
	}

	function upLogin() {
		var username = $("#login_username").val();
		var password = $("#login_password").val();
		var verify = $("#login_verify").val();
		if (username == "" || username == null) {
			layer.tips('请输入用户名', '#login_username', {tips: 3});
			return false;
		}
		if (password == "" || password == null) {
			layer.tips('请输入登录密码', '#login_password', {tips: 3});
			return false;
		}

		$.post("<?php echo U('Login/submit');?>", {
			username: username,
			password: password,
			verify: verify,
		}, function (data) {
			if (data.status == 1) {
				$.cookies.set('cookie_username', username);
				layer.msg(data.info, {icon: 1});
				window.location = data.url;
			} else {
				//刷新验证码
				$(".reloadverifyindex").click();
				layer.msg(data.info, {icon: 2});
				// if (data.url) {
				// 	window.location = data.url;
				// }
			}
		}, "json");
	}


    var allverfiy = function() {
        if ($('#login_username').val() == '' || $('#login_username').val().length == 0) {
            layer.tips('请输入用户名', '#login_username', {tips: 3});
            return;
        }
        if ($('#login_password').val() == '' || $('#login_password').val().length == 0) {
            layer.tips('请输入登录密码', '#login_password', {tips: 3});
            return;
        }
        if ($('#login_verify').val() == '' || $('#login_verify').val().length == 0) {
            layer.tips('图形验证码不能为空!', '#login_verify', {tips: 3});
            return;
        }
        return true;
    }
    var allloginClick = function() {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) { // enter 键
            if (!allverfiy()) {
                return;
            }
            upLogin();
        }
    }
    $('#login_username').bind('keydown', allloginClick);
    $('#login_password').bind('keydown', allloginClick);
    $('#login_verify').bind('keydown', allloginClick);
</script></body></html>