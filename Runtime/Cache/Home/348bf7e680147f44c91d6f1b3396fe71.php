<?php if (!defined('THINK_PATH')) exit();?><!--    顶部通知   -->
<!DOCTYPE html>
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
<!--头部结束--><!--焦点图-->
<div class="index_pic_wrap po_re">
    <div id="myCarousel" class="my-carousel">
        <!--<div class="my-carousel-indicators">-->
        <ol class="my-carousel-indicators">
            <?php if(is_array($indexAdver)): $i = 0; $__LIST__ = $indexAdver;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li data-target="#myCarousel" data-slide-to="<?php echo ($i - 1); ?>"
                <?php if(($i) == "1"): ?>class="active"<?php endif; ?>>
                </li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ol>
        <div class="my-carousel-inner">
            <?php if(is_array($indexAdver)): $i = 0; $__LIST__ = $indexAdver;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><div class="item hand <?php if(($i) == "1"): ?>active<?php endif; ?>" onclick="window.open('<?php echo ($vo['url']); ?>')" style="background-image: url(/Upload/ad/<?php echo ($vo["img"]); ?>);"></div><?php endforeach; endif; else: echo "" ;endif; ?>
        </div>
    </div>
    <div class="login_wrap">
        <div class="login_box">
            <div class="login_bg"></div>
            <!-- 未登录状态 -->
            <?php if(($_SESSION['userId']) > "0"): ?><div id="login-bar" class="login_box_2">
                    <h2>欢迎登录<?php echo C('web_name');?>交易平台</h2>
                    <dl>
                        <dt>您正在使用的账号为:</dt>
                        <dd>
                            <a href="/finance/" class="user-email"><?php echo (session('userName')); ?></a>
                        </dd>
                        <dd>
                            ID：
                            <span class="user-id"><?php echo (session('userId')); ?></span>
                        </dd>
                        <dd>
                            总资产：
                            <span class="user-finance" id="user_finance">loading...</span>
                        </dd>
                    </dl>
                    <div class="login_box_2_btn">
                        <a href="/finance/">充值</a>
                        <a href="/finance/">提现</a>
                        <a href="/finance/mywt.html" class="w82">委托管理</a>
                    </div>
                    <div class="gotocenter">
                        <a href="/finance/" class="center">去财务中心</a>
                    </div>
                    <div class="service_qq"></div>
                </div>
                <?php else: ?>
                <form id="form-login-i">
                    <div class="login_box_1">
                        <!-- <div class="login_title">登录</div> -->
                        <div class="login_text zin90">
                            <input type="text" id='username' value="" placeholder="请输入手机号/会员名" autocomplete="off" spellcheck="false"/>

                            <div id="email-err-i" class="prompt" style="display: none"></div>
                        </div>
                        <div class="login_text zin80">
                            <input type="password" id="password" value="" placeholder="请输入登录密码" autocomplete="off"/>

                            <div id="pw-err-i" class="prompt" style="display: none"></div>
                        </div>
                            <div class="login_text zin70" id="ga-box-i">
                                <img id="codeImg" src="<?php echo U('Verify/code');?>" width="120" height="38" onclick="this.src=this.src+'?t='+Math.random()" onload="!$('#codeImg').data('first') && $('#codeImg').click().data('first','1')" style="margin-top: 1px; cursor: pointer;float: right;" title="换一张">
                                <input type="text" class="code" id="verify" name="code" placeholder="请输入验证码" style="width: 106px; float: left;" autocomplete="off">
                            </div>

                        <div class="login_button">
                            <button class="ui-button ui-button-block" id="loginSubmit" type="button" onclick="user.login()">登录</button>
                        </div>
                    </div>
                </form>
                <div class="login-footer">
                        <!--<a href="/"> <img src="/Public/Home/images/qq2.png">QQ登录</a> -->
  <span> <a href="<?php echo U('Login/register');?>">免费注册</a> ｜ <a href="<?php echo U('Login/findpwd');?>">忘记密码</a>
  </span>
                    </div><?php endif; ?>
        </div>
    </div>
</div>
<div class="zhanwei"></div>
<!--    公告   -->
<div class="autobox">
    <div class="ui-notice">
        <i class="ui-icon ui-icon-tongzhi"></i>
        <div class="ui-notice-content" id="uiNotice"></div>
        <a class="ui-more" href="/Article/index">更多...</a>
    </div>
</div>
<!--<div class="trade-type-tab-box autobox">
    <div class="tab-item-btn left active" data-value="1">合约交易</div>
    <div class="tab-item-btn left" data-value="2">USDT交易</div>
</div>-->
<div class="clearfix contract-trade-box trade-container content-1 common-container" style="display: none;">
    <div class="price_today">
        <ul class="price_today_ull autobox">
            <li data-sort="0" style="cursor: default;width: 20%;">交易市场</li>
            <li class="click-sort" data-sort="1" data-flaglist="0" data-toggle="0" style="width: 10%">价格
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="6" data-flaglist="0" data-toggle="0" style="width: 15%">交易量
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="4" data-flaglist="0" data-toggle="0" style="width: 15%">总市值
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="7" data-flaglist="0" data-toggle="0" style="width: 10%">日涨跌
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li data-sort="0" style="width: 10%;text-align: left;">24小时最高</li>
            <li data-sort="0" style="width: 10%;text-align: left;">24小时最低</li>
            <li data-sort="0" style="width: 10%">操作</li>
            <!-- <li data-sort="0" style="width: 10%">收藏</li> -->
        </ul>
    </div>
    <ul class="price_today_ul autobox" id="contract-list"></ul>
</div>
<div class="clearfix usdt-trade-box trade-container content-2 common-container">
    <div class="price_today">
        <ul class="price_today_ull autobox">
            <li data-sort="0" style="cursor: default;width: 20%;">交易市场</li>
            <li class="click-sort" data-sort="1" data-flaglist="0" data-toggle="0" style="width: 15%">价格
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="6" data-flaglist="0" data-toggle="0" style="width: 15%">交易量
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="4" data-flaglist="0" data-toggle="0" style="width: 20%">总市值
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li class="click-sort" data-sort="7" data-flaglist="0" data-toggle="0" style="width: 15%">日涨跌
                <i class="cagret cagret-down"></i>
                <i class="cagret cagret-up"></i>
            </li>
            <li data-sort="0" style="width: 15%">价格趋势(3日)</li>
            <!--<li data-sort="0" style="width: 10%">操作</li>-->
            <!-- <li data-sort="0" style="width: 10%">收藏</li> -->
        </ul>
    </div>
    <ul class="price_today_ul autobox" id="price_today_ul"></ul>
</div>

<input type="hidden" name="coin_type" value="cny_btc"/>
<input type="hidden" name="amount" value="1000000"/>

<script>
    (function (w, d, n, a, j) {
        w[n] = w[n] || function () {
            (w[n].a = w[n].a || []).push(arguments);
        };
        j = d.createElement('script');
        j.async = true;
        j.src ='https://qiyukf.com/script/c73f013995be91903732f0462dc4be17.js';
        d.body.appendChild(j);
    })(window, document, 'ysf');
</script>

<script>
    //顶部通知
    ui.message();
    //通知
    ui.notice("#uiNotice");

    a = "654";

    //轮播图
    var $allItems = $('.my-carousel .my-carousel-inner .item');
    var $allIndicators = $('.my-carousel .my-carousel-indicators li');
    var currentIndex = 0;
    var currentItem = null;
    var nextItem = null;
    var time = null;


    $(".my-carousel").hover(function () {
        time = window.clearInterval(time);
        time = null;
    }, function () {
        time = setInterval(function () {
                    currentItem = $allItems.filter('.active');
                    if (currentIndex + 1 === $allItems.length) {
                        nextItem = $allItems.eq(0);
                        currentIndex = 0;
                    } else {
                        nextItem = $allItems.eq(currentIndex + 1);
                        currentIndex += 1;
                    }
                    nextItem.addClass('active').fadeIn(500);
                    $allIndicators.removeClass('active').eq(currentIndex).addClass('active');
                    currentItem.removeClass('active').fadeOut(1000);
                },5000);
    }).trigger("mouseleave");



    $(".my-carousel-indicators li").click(function () {

        var nextIndex = parseInt($(this).attr('data-slide-to'));
        if (nextIndex == currentIndex) return false;
        currentIndex = nextIndex;
        currentItem = $allItems.filter('.active');
        currentItem.removeClass('active').fadeOut(1000);
        $allItems.eq(currentIndex).addClass('active').fadeIn(500);

        $allIndicators.removeClass('active').eq(currentIndex).addClass('active');

    });


    $('.price_today_ull > .click-sort').each(function () {
        $(this).click(function () {
            click_sortList(this);
        })
    })
    
    function allcoin_callback(priceTmp) {
        for (var i in priceTmp) {
            var c = priceTmp[i][8];
            if (typeof (trends[c]) != 'undefined' && typeof (trends[c]['data']) != 'undefined' && trends[c]['data'].length > 0) {
                $.plot($("#" + c + "_plot"), [
                    {
                        shadowSize: 0,
                        data: trends[c]['data']
                    }
                ], {
                    grid: {borderWidth: 0},
                    xaxis: {
                        mode: "time",
                        ticks: false
                    },
                    yaxis: {
                        tickDecimals: 0,
                        ticks: false
                    },
                    colors: ['#DA4F49']
                });
            }
        }
    }

    function click_sortList(sortdata) {
        var a = $(sortdata).attr('data-toggle');
        var b = $(sortdata).attr('data-sort');
        $(".price_today_ull > li").find('.cagret-up').css('border-bottom-color', '#848484');
        $(".price_today_ull > li").find('.cagret-down').css('border-top-color', '#848484');
        $(".price_today_ull > li").attr('data-flaglist', 0).attr('data-toggle', 0);
        $(".price_today_ull > li").css('color', '');
        $(sortdata).css('color', '#ff7950');

        if (a == 0) {
            priceTmp = priceTmp.sort(sortcoinList('dec', b));
            $(sortdata).find('.cagret-down').css('border-top-color', '#ff7950');
            $(sortdata).find('.cagret-up').css('border-bottom-color', '#848484');
            $(sortdata).attr('data-flaglist', 1).attr('data-toggle', 1)
        }
        else if (a == 1) {
            $(sortdata).attr('data-toggle', 0).attr('data-flaglist', 2);
            ;
            $(sortdata).find('.cagret-up').css('border-bottom-color', '#ff7950');
            $(sortdata).find('.cagret-down').css('border-top-color', '#848484');
            priceTmp = priceTmp.sort(sortcoinList('asc', b));
        }
        renderPage(priceTmp);
        allcoin_callback(priceTmp);
    }

    
    // 获取合约行情数据
    var getContractList_timer = null;
    function getContractList() {
        clearTimeout(getContractList_timer);
        getContractList_timer = null;

        var html = '';

        $.getJSON('/Ajax/contractList', function (res) {
            
            if(res.length) {
                for(var i = 0;i<res.length;i++) {
                    html += '<li>'+
                        '<dl class="autobox clear">'+
                            '<dd style="width:20%">'+
                                '<a href="/contract/index/market/' + res[i].market + '/">' +
                                    '<img src="/Upload/coin/' + res[i].img + '">' + 
                                        res[i].title + 
                                '</a>'+
                            '</dd>'+
                            '<dd style="width:10%">' + formatPrice(res[i].new_price) + '</dd>'+
                            '<dd style="width:15%">' + formatPrice(formatCount(res[i].volume)) + '</dd>'+
                            '<dd style="width:15%">' + formatPrice(formatCount(res[i].total)) + '</dd>'+
                            '<dd style="width:10%" class="' + (res[i].change >= 0 ? 'red' : 'green') + '">' + res[i].change + '%</dd>'+
                            '<dd style="width:10%;padding-left:0;text-align:left;">' + formatPrice(res[i].max_price) + '</dd>'+
                            '<dd style="width:10%;text-align:left;">' + formatPrice(res[i].min_price) + '</dd>'+
                            '<dd style="width:10%">'+
                               '<input type="button" value="去交易" onclick="toMarket(\'' + res[i].market + '\')" />'+
                            '</dd>'+
                        '</dl>'+
                    '</li>'
                }
                $('#contract-list').html(html);
            }
        },'json');

        getContractList_timer = setTimeout(function() {
            getContractList();
        },5000)
        
    }
    //getContractList();

    function trends() {
        $.getJSON('/Ajax/trends?t=' + rd(), function (d) {
            trends = d;
            allcoin();
        });
    }

    // 现货行情
    var allcoin_timer = null;
    function allcoin(cb) {
        clearTimeout(allcoin_timer);
        allcoin_timer = null;
        $.get('/Ajax/allcoin?t=' + rd(), cb ? cb : function (d) {
            ALLCOIN = d;
            var t = 0;
            var img = '';
            priceTmp = [];
            //把json转换为二维数组 进行渲染
            // d.yhet_usdt = ["YHET<span>Yhetgame</span>", 0.04, 0.04, 0.04, 56822, "", 4000000, 0.01, "yhet_usdt", "5c10cbff92521.png", "", null];
            for (var x in d) {
                if (typeof(trends[x]) != 'undefined' && parseFloat(trends[x]['yprice']) > 0) {
                    rise1 = (((parseFloat(d[x][4]) + parseFloat(d[x][5])) / 2 - parseFloat(trends[x]['yprice'])) * 100) / parseFloat(trends[x]['yprice']);
                    rise1 = rise1.toFixed(2);
                } else {
                    rise1 = 0;
                }
                img = d[x].pop();
                d[x].push(rise1);
                d[x].push(x);
                d[x].push(img);
                priceTmp.push(d[x]);
            }
            //二次排序
            $('.price_today_ull > .click-sort').each(function () {
                var listId = $(this).attr('data-sort');
                if ($(this).attr('data-flaglist') == 1 && $(this).attr('data-sort') !== 0) {
                    priceTmp = priceTmp.sort(sortcoinList('dec', listId))
                } else if ($(this).attr('data-flaglist') == 2 && $(this).attr('data-sort') !== 0) {
                    priceTmp = priceTmp.sort(sortcoinList('asc', listId))
                }
            });
            
            
            renderPage(priceTmp);
            allcoin_callback(priceTmp);
            
            allcoin_timer = setTimeout('allcoin()', 5000);
        }, 'json');
    }

    function rd() {
        return Math.random()
    }
    //渲染函数
    function renderPage(ary) {
        var html = '';
        
        for (var i in ary) {
            var coinfinance = 0;
            if (typeof FINANCE == 'object') coinfinance = parseFloat(FINANCE.data[ary[i][8] + '_balance']);
            html += '<li>'+
                        '<dl class="autobox clear">'+
                            '<dd style="width:20%;">'+
                                '<a href="javascript:;">' +
                                    '<img src="/Upload/coin/' + ary[i][9] + '">' + 
                                        ary[i][0] + 
                                '</a>'+
                                // '<a href="/trade/index/market/' + ary[i][8] + '/">' +
                                //     '<img src="/Upload/coin/' + ary[i][9] + '">' + 
                                //         ary[i][0] + 
                                // '</a>'+
                            '</dd>'+
                            '<dd style="width:15%;">' + formatPrice(ary[i][1]) + '</dd>'+
                            '<dd style="width:15%;">' + formatPrice(formatCount(ary[i][4])) + '</dd>'+
                            '<dd style="width:20%;">' + formatPrice(formatCount(ary[i][6])) + '</dd>'+
                            '<dd style="width:15%;" class="' + (ary[i][7] >= 0 ? 'red' : 'green') + '">' + (ary[i][1] == 0 ? '--' : (parseFloat(ary[i][7]) < 0 ? '' : '+') + ((parseFloat(ary[i][7]) < 0.01 && parseFloat(ary[i][7]) > -0.01) ? "0.00" : ary[i][7]) + '%') + '</dd>'+
                            '<dd style="width:15%;" id="' + ary[i][8] + '_plot"></dd>'+
                            //'<dd>'+
                              //  '<input type="button" value="去交易" onclick="toMarket(\'' + ary[i][8] + '\')" /></dd>'+
                            // '<dd>'+
                            //     '<div class="add-fav ' + (ary[i][13] == "1" ? "add-fav-on" : "") + '" data-collection="' + ary[i][13] + '" data-market="' + ary[i][8] + '" onclick="ui.collection()"></div>'+
                            // '</dd>'+
                        '</dl>'+
                    '</li>'
            
        }
        $('#price_today_ul').html(html);

    }
    
    function toMarket(name){
        top.location='/contract/index/market/' + name + '/';
    }

    //保留2位小鼠
    function formatCount(num) {
        var result = '', counter = 0;
        num = (num || 0).toString();
        for (var i = num.length - 1; i >= 0; i--) {
            result = num.charAt(i) + result;
            if(num.indexOf('.') != -1 && i >= num.indexOf('.') ){
                continue;
            }else{
                counter++;
            }
            if (!(counter % 3) && i != 0) { result = ',' + result; }
        }
        return result;
    }
    //格式化价格
    function formatPrice(price){
        return  price.toString() == '0' ? '--' : "$" + price;
    }

    //排序函数
    function sortcoinList(order, sortBy) {
        var ordAlpah = (order == 'asc') ? '>' : '<';
        var sortFun = new Function('a', 'b', 'return parseFloat(a[' + sortBy + '])' + ordAlpah + 'parseFloat(b[' + sortBy + '])? 1:-1');
        return sortFun;
    }


    trends();


    var cookieValue = $.cookies.get('cookie_username');

    if (cookieValue != '' && cookieValue != null) {
        $("#username").val(cookieValue);
    }


    // 合约交易与usdt交易切换
    $('body').on('click','.tab-item-btn',function() {
        if(!$(this).hasClass('active')) {
            $(this).addClass('active').siblings().removeClass('active');
            var value = $(this).data('value');
            $('.content-'+ value).show().siblings('.trade-container').hide();
        }
    })
    
</script>
<script>
    //菜单高亮
    $('#index_box').addClass('active');

    var verfiy = function() {
        if ($('#index_username').val() == '' || $('#index_username').val().length == 0) {
            layer.tips('请输入用户名', '#index_username', {tips: 3});
            return;
        }
        if ($('#index_password').val() == '' || $('#index_password').val().length == 0) {
            layer.tips('请输入登录密码', '#index_password', {tips: 3});
            return;
        }
        if ($('#index_verify').val() == '' || $('#index_verify').val().length == 0) {
            layer.tips('图形验证码不能为空!', '#index_verify', {tips: 3});
            return;
        }
        return true;
    }
    var loginClick = function() {
        var e = event || window.event || arguments.callee.caller.arguments[0];
        if (e && e.keyCode == 13) { // enter 键
            // if (!verfiy()) {
            //     return;
            // }
            user.login();
        }
    }
    $('#username').bind('keydown', loginClick);
    $('#password').bind('keydown', loginClick);
    $('#verify').bind('keydown', loginClick);


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