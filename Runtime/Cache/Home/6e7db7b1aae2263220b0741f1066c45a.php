<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php echo C('web_title');?></title>
    <meta name="Keywords" content="<?php echo C('web_keywords');?>">
    <meta name="Description" content="<?php echo C('web_description');?>">
    <meta name="robots" content="index,follow"/>
    <meta name="author" content="zuocoin.com">
    <meta name="coprright" content="zuocoin.com">
    <link rel="shortcut icon" href=" /favicon.ico"/>
    <link rel="stylesheet" href="/Public/Home/css/zuocoin.css"/>
    <link rel="stylesheet" href="/Public/Home/css/style.css"/>
    <link rel="stylesheet" href="/Public/Home/css/ui.css"/>
    <link rel="stylesheet" href="/Public/Home/css/new_style.css"/>
    <link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css"/>
    <script type="text/javascript" src="/Public/Home/js/script.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.flot.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.cookies.2.2.0.js"></script>
    <script type="text/javascript" src="/Public/layer/layer.js"></script>
</head>
<body>
<div class="header bg_w" style="position: fixed; z-index: 99;height: auto;">
    <div class="hearder_top">
        <div class="autobox po_re zin100" id="header" style="width: 100%; padding: 0 50px 0 10px;box-sizing: border-box;">
            <div class="logo-small" style="max-height: 25px;">
                <a href="/"><img src="/Upload/public/<?php echo ($C['web_llogo_small']); ?>" alt="" width="70"></a>
            </div>
            <div class="nav fz_12 nav-tabs">
                <ul>
                    <li style="text-align: right; padding-right: 20px;">
                        <a href="/" id="index_box">首页</a>
                    </li>
                    <li>
                        <a id="<?php echo ($daohang[0]['name']); ?>_box" href="/<?php echo ($daohang[0]['url']); ?>"><?php echo ($daohang[0]['title']); ?></a>
                    </li>

                    <li>
                        <a id="trade_box" class="active"><span class="active">交易中心</span>
                            <img src="/Public/Home/images/down.png"></a>

                        <div class="deal_list " style="display: none;   top: 36px;">
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
                    <li>
                        <a href="/Vote/index">上币申请</a>
                    </li>
                    <li>
                        <a id="<?php echo ($daohang[3]['name']); ?>_box" href="/<?php echo ($daohang[3]['url']); ?>"><?php echo ($daohang[3]['title']); ?></a>
                    </li>
                </ul>
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
                        <a class="orange" href="<?php echo U('Login/index');?>">登录</a>
                    </div><?php endif; ?>
            </div>
            <div class="gui-app-download right">
                <a href="https://www.gte.io/papi/help/download" >APP下载</a>
                <div class="gui-app-qrcode">
                    <h4>扫码下载gte.io App</h4>
                    <img src="/Public/Home/images/app-qrcode.png" alt="app下载">
                </div>
            </div>
        </div>
    </div>
    <div class="autobox" style="display: none">
        <div class="all_coin_price">
            <div class="all_coin_show">
                <a href=""><img src="" style="float: left; width: 40px; height: 40px; margin-right: 5px;"><span><?php echo ($title); ?></span><em></em></a>
            </div>
            <div class="all_coin_list" style="display: none;">
                <div class="all_coin_box">
                    <ul id="all_coin"></ul>
                </div>
            </div>
        </div>
        <dl class="all_coin_info">
            <dt id="market_new_price"></dt>
            <dd>
                <p class="orange" id="market_max_price"></p>
                最高价
            </dd>
            <dd>
                <p class="green" id="market_min_price"></p>
                最低价
            </dd>
            <dd>
                <p id="market_buy_price"></p>
                买一价
            </dd>
            <dd>
                <p id="market_sell_price"></p>
                卖一价
            </dd>
            <dd class="w150">
                <p id="market_volume"></p>
                成交量
            </dd>
            <dd class="w150">
                <p id="market_change"></p>
                日涨跌
            </dd>
        </dl>
    </div>
</div>
<!-- <div class="list-tab-box">
    <ul class="list-tab">
        <li id="list-tab_index" style="width: auto; margin: 0px;">
            <a href="<?php echo U('Trade/index','market='.$market);?>"><?php echo ($title); ?>交易</a>
        </li>
        <li id="list-tab_chart" style="width: auto;">
            <a href="<?php echo U('Trade/chart','market='.$market);?>"><?php echo ($title); ?>行情</a>
        </li>
        <li id="list-tab_info" style="width: auto;">
            <a href="<?php echo U('Trade/info','market='.$market);?>"><i class="arrow-tab"></i>了解<?php echo ($title); ?></a>
        </li>
    </ul>
</div> -->
<!--头部结束-->

<script>
    $.getJSON("/Ajax/getJsonMenu?t=" + Math.random(), function (data) {
        if (data) {
            var list = '';
            for (var i in data) {
                list += '<dd><a style="color: #333" href="/Trade/index/market/' + data[i]['name'] + '"><img src="/Upload/coin/' + data[i]['img'] + '" style="width: 18px; margin-right: 5px;">' + data[i]['title'] + '</a></dd>';
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



<script>
    function getJsonTop() {
        $.getJSON("/Ajax/getJsonTop?market=<?php echo ($market); ?>&t=" + Math.random(), function (data) {
            if (data) {
                if (data['info']['new_price']) {
                    $('#market_new_price').removeClass('buy');
                    $('#market_new_price').removeClass('sell');
                    if ($("#market_new_price").html() > data['info']['new_price']) {
                        $('#market_new_price').addClass('sell');
                    }
                    if ($("#market_new_price").html() < data['info']['new_price']) {
                        $('#market_new_price').addClass('buy');
                    }
                    $("#market_new_price").html(data['info']['new_price']);
                }
                if (data['info']['buy_price']) {
                    $('#market_buy_price').removeClass('buy');
                    $('#market_buy_price').removeClass('sell');
                    if ($("#market_buy_price").html() > data['info']['buy_price']) {
                        $('#market_buy_price').addClass('sell');
                    }
                    if ($("#market_buy_price").html() < data['info']['buy_price']) {
                        $('#market_buy_price').addClass('buy');
                    }
                    $("#market_buy_price").html(data['info']['buy_price']);
                    // $("#sell_best_price").html('￥' + data['info']['buy_price']);
                }
                if (data['info']['sell_price']) {
                    $('#market_sell_price').removeClass('buy');
                    $('#market_sell_price').removeClass('sell');
                    if ($("#market_sell_price").html() > data['info']['sell_price']) {
                        $('#market_sell_price').addClass('sell');
                    }
                    if ($("#market_sell_price").html() < data['info']['sell_price']) {
                        $('#market_sell_price').addClass('buy');
                    }
                    $("#market_sell_price").html(data['info']['sell_price']);
                    // $("#buy_best_price").html('￥' + data['info']['sell_price']);
                }
                if (data['info']['max_price']) {
                    $("#market_max_price").html(data['info']['max_price']);
                }
                if (data['info']['min_price']) {
                    $("#market_min_price").html(data['info']['min_price']);
                }
                if (data['info']['volume']) {
                    if (data['info']['volume'] > 10000) {
                        data['info']['volume'] = (data['info']['volume'] / 10000).toFixed(2) + "万"
                    }
                    if (data['info']['volume'] > 100000000) {
                        data['info']['volume'] = (data['info']['volume'] / 100000000).toFixed(2) + "亿"
                    }
                    $("#market_volume").html(data['info']['volume']);
                }
                if (data['info']['change']) {
                    $('#market_change').removeClass('buy');
                    $('#market_change').removeClass('sell');
                    if (data['info']['change'] > 0) {
                        $('#market_change').addClass('buy');
                    } else {
                        $('#market_change').addClass('sell');
                    }
                    $("#market_change").html(data['info']['change'] + "%");
                }


                if (data['list']) {
                    var list = '';
                    for (var i in data['list']) {
                        list += '<li><a href="/Trade/index/market/' + data['list'][i]['name'] + '"> <img src="/Upload/coin/' + data['list'][i]['img'] + '" style="width: 40px; float: left; margin-right: 5px;"> <span class="all_coin_name"><p>' + data['list'][i]['title'] + '</p> <span id="all_coin_' + data['list'][i]['name'] + '">' + data['list'][i]['new_price'] + '</span></span></a></li>';
                    }
                    $("#all_coin").html(list);
                }


            }
        });
        setTimeout('getJsonTop()', 5000);
    }
    $(function () {
        // getJsonTop();
        $('.all_coin_price').hover(function () {
            $('.all_coin_list').show()
        }, function () {
            $('.all_coin_list').hide()
        });
    });
</script>
<?php if(!empty($prompt_text)): ?><div class="mytips">
        <h6 style="color: #ff8000;">温馨提示</h6>
        <?php echo ($prompt_text); ?>
    </div><?php endif; ?>
<div class="wrapbody" style="padding: 0px; margin-top: 20px;">
 <div class="main">
  <div class="span span8  content">
   <div id="content">
    <div id="klineImage" class="newKlineImage ">
     <div class="marketImageNew" id="marketImageNew" style="">
      <iframe style="border-style: none;" border="0" width="100%" height="600" id="market_chart" src="/Trade/specialty?market=<?php echo ($market); ?>"></iframe>
     </div>
    </div>
   </div>
   <div id="content-trade" class="content">
    <div class="box mt20" id="Titletrd">
     <div class="clear over-auto  account_table">
      <div class="left   over-auto" style="width: 32%; margin-right: 5px; border-right: 1px solid #D5D5D5; border-left: 1px solid #D5D5D5;">
       <div class="over-auto account_table">
        <table class="Transaction no-border-left">
         <thead>
          <tr>
           <th width="50">买入</th>
           <th width="80">价格</th>
           <th width="120">数量</th>
           <th width=""><span style="padding-right: 18px;">比例</span></th>
          </tr>
         </thead>
        </table>
        <div style="_height: 852px; max-height: 852px; overflow-x: hidden; overflow-y: auto;">
         <table class="Transaction no-border-left">
          <tbody id="marketbuylist">
          </tbody>
         </table>
        </div>
       </div>
      </div>
      <div class="left  over-auto" style="width: 32%; border-right: 1px solid #D5D5D5; border-left: 1px solid #D5D5D5;">
       <div class="over-auto">
        <table class="Transaction no-border-right">
         <thead>
          <tr>
           <th width="50">卖出</th>
           <th width="80">价格</th>
           <th width="120">数量</th>
           <th width=""><span style="padding-right: 18px;">比例</span></th>
          </tr>
         </thead>
        </table>
        <div style="_height: 852px; max-height: 852px; overflow-x: hidden; overflow-y: auto;">
         <table class="Transaction no-border-right">
          <tbody id="marketselllist">
          </tbody>
         </table>
        </div>
       </div>
      </div>
      <div class="right" style="width: 34.7%; border-right: 1px solid #D5D5D5; border-left: 1px solid #D5D5D5;">
       <div class="clear">
        <div class=" over-hidden">
         <div class="over-auto">
          <table class="Transaction no-border-left  no-border-left-right">
           <thead>
            <tr>
             <th width="70">时间</th>
             <th width="70">价格</th>
             <th width="100">数量</th>
             <th><span style="padding-right: 18px;">金额</span></th>
            </tr>
           </thead>
          </table>
          <div style="_height: 852px; max-height: 852px;; overflow-x: hidden; overflow-y: auto;">
           <table class="Transaction no-border-left  no-border-left-right">
            <tbody id="marketorderlist">
            </tbody>
           </table>
          </div>
         </div>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>
<br>
<br>
<script type="text/javascript">
	var market="<?php echo ($market); ?>";


	function getJsonData(){
		$.getJSON("/Chart/getJsonData?market="+market+"&t="+Math.random(),function(data){
			if(data){
				if(data[0]){
					$("#marketbuylist").html(data[0]);
				}
				if(data[1]){
					$("#marketselllist").html(data[1]);
				}
				if(data[2]){
					$("#marketorderlist").html(data[2]);
				}
			}
		});
	}
	getJsonData();

	// 倒计时
	var wait=second=5;
	var go=setInterval(function(){
		wait--;
		if(wait<0){
			getJsonData();
			wait=second;
		}
	},1000);
</script>
<script>
	//菜单高亮
	$('#list-tab_chart').addClass('on');
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