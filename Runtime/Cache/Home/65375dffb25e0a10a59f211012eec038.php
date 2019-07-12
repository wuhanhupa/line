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

    <div id="app" style="height:100%;">
        <app-component></app-component>
    </div>
    <script id="app-template" type="text/x-template">
        <div style="height:100%;">
            <div class="autobox c2c-page">
                <div class="leftbar">
                    <ul>
                        <li >
                            <a href="/Cztrade/index">
                                <b>USDT/CNY </b><span>彧捷支付</span>
                            </a>
                        </li>
                        <li>
                            <a href="/fupay/index"><b>USDT/CNY </b><span>富π支付</span></a>
                        </li>
                        <li class="active">
                            <a href="/Ctwoc/index">
                                <b>USDT/CNY </b><span>USDT/人民币</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="main-content">
                    <div class="header-title">
                        <h4> USDT 对 CNY</h4>
                        <p>
                            实时价：<span>{{ buyRate }}</span>
                        </p>
                    </div>
                    <div class="form-box trade-main clearfix">
                        <div class="maichu maichu-form">
                            <div class="m_title">
                                <span>买入USDT</span>
                            </div>
                            <div class="m_con_buy">
                                <div>
                                    <a class="red" href="/Article/detail/id/32.html" target="_blank">如何买入？</a>
                                </div>
                                <table class="dealbox">
                                    <tbody>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit ask-bid-price input-title">买入估价 <span>CNY</span></span>
                                                <input id="buy_rate" class="inputRate red" readonly="readonly" :value="buyRate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">买入量 USDT</span>
                                                <input id="buy_vol" type="text" maxlength="10" class="inputRate" v-model="buyNum">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title" id="buy_total_label">金额 CNY</span>
                                                <input id="buy_amount" type="text" maxlength="10"  class="inputRate" v-model="buyPrice">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="pull-left">支付方式：</span>
                                                <ul class="pay-type-ul">
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="buy_pay_ali" type="checkbox" value="1" hidden="">
                                                        <span>支付宝</span>
                                                    </li>
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="buy_pay_wechat" type="checkbox" value="2" hidden="">
                                                        <span>微信</span>
                                                    </li>
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="buy_pay_bank" type="checkbox" value="3" hidden="">
                                                        <span>银行转账</span>
                                                    </li>
                                                </ul>
                                                <span class="red">（必须本人支付）</span>
                                                <a class="jyxz" href="javascript:;">《交易须知》</a>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td" style="border:0">
                                                <input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="ask" id="buy_submit" value="买入 (CNY→USDT)">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <i class="sp-top"></i>
                            <i class="sp-bottom"></i>
                        </div>
                        <div class="mairu mairu-form">
                            <div class="m_title">
                                <span>卖出USDT</span>
                                <span class="yu-e">
                                    <span>(</span>
                                    <span>USDT余额</span>
                                    <span id="balance_sell_able" class="green"></span>
                                    <span>)</span>
                                </span>

                            </div>
                            <div class="m_con">
                                <div>
                                    <a class="red" href="/Article/detail/id/32.html" target="_blank">如何卖出？</a>
                                </div>
                                <table class="dealbox" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit ask-bid-price input-title">卖出估价 <span>CNY</span></span>
                                                <input id="sell_rate" class="inputRate green" maxlength="10" readonly="" :value="sellRate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">卖出量 USDT</span>
                                                <input id="sell_vol" class="inputRate" maxlength="10" v-model="sellNum">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">金额 CNY</span>
                                                <input id="sell_amount" class="inputRate" maxlength="10" v-model="sellPrice">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="pull-left">支付方式：</span>
                                                <ul class="pay-type-ul">
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="sell_pay_ali" type="checkbox" value="1" hidden="">
                                                        <span>支付宝</span>
                                                    </li>
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="sell_pay_wechat" type="checkbox" value="2" hidden="">
                                                        <span>微信</span>
                                                    </li>
                                                    <li>
                                                        <i class="icon-empty"></i>
                                                        <input id="sell_pay_bank" type="checkbox" value="3" hidden="">
                                                        <span>银行转账</span>
                                                    </li>
                                                </ul>
                                                <span class="red">（必须本人支付）</span>
                                                <a class="jyxz" href="javascript:;">《交易须知》</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td" style="border:0">
                                                <input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="bid" id="sell_submit" value="卖出 (USDT→CNY)">
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div id="divMyRecordSection" class="clearfix" >
                            <div class="my-order-box">
                                <div class="m_title">
                                    市场挂单
                                </div>

                                <div class="sectioncont" id="divPushOrder" style="display: block;">
                                    <table class="sf-grid table-inacc table-inacc-head">
                                        <thead>
                                        <tr>
                                            <th align="left"><b>类型</b></th>
                                            <th align="right"><b>价格</b>(CNY)</th>
                                            <th align="right"><b>数量</b>(USDT)</th>
                                            <th align="right"><b>总计</b>(CNY)</th>
                                            <!-- <th align="right"><b>交易限额</b>(USDT)</th> -->
                                            <th align="right"><b>商家在线</b></th>
                                            <th align="right"><b>成交单数</b></th>
                                            <!-- <th align="right"><b>平均用时</b></th> -->
                                            <th align="right"><b>付款方式</b></th>
                                            <th align="right">操作</th>
                                        </tr>
                                        </thead>
                                    </table>
                                    <div class="table-scroll g-scrollbar" id="divPushOrderContent">
                                        <table class="sf-grid table-inacc table-inacc-body" id="pushOrder">
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="my-orders-list sectioncont">
                                    <child :result="result_orders" :user="USERID"></child>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="notice-box mask">
                <div class="inner-box">
                    <h4 class="title"><span ></span><i>请确认</i></h4>
                    <div class="info-box">
                        <!-- <p>买入数量: <span>150.3758</span> <i>USDT</i></p>
                        <p>买入金额: <span>150.3758</span> <i>CNY</i></p>
                        <p>买入估价: <span>6.63</span> <i>CNY</i></p>
                        <p>交易方式: <span>银行转账</span></p> -->
                        <div class="password-box">
                            <label for="password">交易密码：</label>
                            <input type="password"  v-model="password">
                        </div>
                    </div>
                    <div class="sub-box">
                        <button class="ok-btn">确认下单</button>
                        <button class="cancle-btn">取消</button>
                    </div>
                </div>
            </div>
            <div class="list-trade-box mask">
                <div class="inner-box">
                    <h4 class="title"><span >{{ trade_data.tradeType > 1 ? '卖出': '买入' }}</span><i>请确认</i></h4>
                    <div class="info-box">
                        <p>{{ trade_data.tradeType > 1 ? '卖出': '买入' }}数量: <input type="text" v-model="trade_num" id="trade_vol"> <i>USDT</i></p>
                        <p>{{ trade_data.tradeType > 1 ? '卖出': '买入' }}金额: <input type="text" v-model="trade_price" id="trade_amount"> <i>CNY</i></p>
                        <p>{{ trade_data.tradeType > 1 ? '卖出': '买入' }}估价: <span>{{ trade_data.tradeRate }}</span> <i>CNY</i></p>
                        <p>
                            <em>交易方式:</em>
                            <ul class="pay-type-ul">
                                <li>
                                    <i class="icon-empty"></i>
                                    <input id="sell_pay_ali" type="checkbox" value="1" hidden="">
                                    <span>支付宝</span>
                                </li>
                                <li>
                                    <i class="icon-empty"></i>
                                    <input id="sell_pay_wechat" type="checkbox" value="2" hidden="">
                                    <span>微信</span>
                                </li>
                                <li>
                                    <i class="icon-empty"></i>
                                    <input id="sell_pay_bank" type="checkbox" value="3" hidden="">
                                    <span>银行转账</span>
                                </li>
                            </ul>
                        </p>
                        <div class="password-box">
                            <label for="password">交易密码：</label>
                            <input type="password"  v-model="trade_data.password">
                        </div>
                    </div>
                    <div class="sub-box">
                        <button class="sure-btn">确认下单</button>
                        <button class="cancle-btn">取消</button>
                    </div>
                </div>
            </div>
            <div class="layer-box mask">
                <div class="inner-box">
                    <p class="layer-text">确认取消订单吗?</p>
                    <div class="sub-box">
                        <button class="ok-btn">确认</button>
                        <button class="cancle-btn">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </script>
    <script id="child-template" type="text/x-template">
        <div>
            <div class="orders-nav">
                <ul>
                    <li class="active" name="cur">我的当前订单</li>
                    <li name="comp">已完成订单</li>
                    <li name="cancle">已取消订单</li>
                </ul>
            </div>

            <div class="order-list-header">
                <ul>
                    <li>订单号</li>
                    <li>类型</li>
                    <li>价格(CNY)</li>
                    <li>数量(USDT)</li>
                    <li>金额(CNY)</li>
                    <li>状态</li>
                    <li>对方姓名</li>
                    <li>建立时间</li>
                    <li>操作</li>
                </ul>
            </div>
            <div class="list-box">
                <ul>
                    <!-- 登录用户id 与 操作用户id 相同时 -->
                    <li class="order-item" v-for="(item,index) in result" :tradeId="item.trade_id" :buyId="item.buyid" :sellId="item.sell_id" :num="item.num" :type="item.type" :id="item.userid">
                        <div class="order-info">
                            <span class="to-detail" :class="(index < 1 && item.status < 2) ? 'active': ''">{{ item.trade_id }}<i :id="index"></i></span>

                            <span v-if="user == item.userid">{{ item.type > 1 ? '卖出': '买入' }}</span>
                            <span v-if="user != item.userid">{{ item.type > 1 ? '买入': '卖出' }}</span>

                            <span>{{ item.parities ? item.parities : '--' }}</span>
                            <span>{{ item.num }}</span>
                            <span>{{ item.price }}</span>

                            <span v-if="item.status == 0">待完成</span>
                            <span style="color:#ec1414" v-if="item.status == 1">已付款</span>
                            <span v-if="item.status == 2">已完成</span>
                            <span v-if="item.status > 2">已取消</span>

                            <span v-if="user == item.userid">{{ item.type > 1 ? item.buytruename : item.selltruename }}</span>
                            <span v-if="user != item.userid">{{ item.type > 1 ? item.selltruename : item.buytruename }}</span>

                            <span>{{ item.addtime }}</span>

                            <span v-if="user == item.userid">
                                <a href="javascript:;" class="sure-button" v-if="item.type == 1 && item.status < 1">确认付款</a>
                                <a href="javascript:;" class="sure-button complete" v-if="item.type == 1 &&  item.status < 3 && item.status > 0 ">我已付款</a>
                                <a href="javascript:;" class="sure-button disabled" v-if="item.status > 2">已取消</a>
                                <a href="javascript:;" class="sure-button" v-if="item.type == 2 && item.status < 2">确认收款</a>
                                <a href="javascript:;" class="sure-button complete" v-if="item.type == 2 &&  item.status == 2">我已收款</a>
                            </span>
                            <span v-if="user != item.userid">
                                <a href="javascript:;" class="sure-button" v-if="item.type == 1 && item.status < 2">确认收款</a>
                                <a href="javascript:;" class="sure-button complete" v-if="item.type == 1 && item.status == 2">我已收款</a>
                                <a href="javascript:;" class="sure-button disabled" v-if="item.status > 2">已取消</a>
                                <a href="javascript:;" class="sure-button" v-if="item.type == 2 && item.status < 1">确认付款</a>
                                <a href="javascript:;" class="sure-button complete" v-if="item.type == 2 && item.status < 3 && item.status > 0">我已付款</a>
                            </span>

                        </div>

                        <div class="cur_order_detail clearfix" :style="{display: (index < 1 && item.status < 2) ? 'block': 'none'}">
                            <div class="wait-box">
                                <h4><span>等付款</span><i>{{ item.durtime < 0 || item.status > 1 ? '0分0秒': '还剩' + item.minutes + '分' + item.seconds + '秒' }}</i></h4>

                                <div class="wait-button-box" v-if="user == item.userid">
                                        <a href="javascript:;" class="sure-button" v-if="item.type == 1 && item.status < 1">确认付款</a>
                                        <a href="javascript:;" class="sure-button complete" v-if="item.type == 1 && item.status < 3 && item.status > 0">我已付款</a>
                                        <a href="javascript:;" class="sure-button disabled" v-if="item.status > 2">已取消</a>
                                        <a href="javascript:;" class="sure-button" v-if="item.type == 2 && item.status < 2">确认收款</a>
                                        <a href="javascript:;" class="sure-button complete" v-if="item.type == 2 && item.status == 2">我已收款</a>

                                    <a href="javascript:;" class="del-button" :class="item.status > 1 ? 'disabled': ''" :tradeId="item.trade_id">撤销</a>
                                </div>
                                <div class="wait-button-box" v-if="user != item.userid">
                                    <a href="javascript:;" class="sure-button" v-if="item.type == 1 && item.status < 2">确认收款</a>
                                    <a href="javascript:;" class="sure-button complete" v-if="item.type == 1 && item.status == 2">我已收款</a>
                                    <a href="javascript:;" class="sure-button disabled" v-if="item.status > 2">已取消</a>
                                    <a href="javascript:;" class="sure-button" v-if="item.type == 2 && item.status < 1">确认付款</a>
                                    <a href="javascript:;" class="sure-button complete" v-if="item.type == 2 && item.status < 3 && item.status > 0">我已付款</a>

                                    <a href="javascript:;" class="del-button" :class="item.status > 1 ? 'disabled': ''" :tradeId="item.trade_id">撤销</a>
                                </div>

                                <div class="contact-box" v-if="user == item.userid">
                                    <a href="javascript:;" v-on:click="toChat(item)" :title="item.type > 1 ? item.buymoble : item.sellmoble" class="contact-btn" :phone="item.type > 1 ? item.buymoble : item.sellmoble">联系对方</a>
                                    <!-- <a href="javascript:;">我要申诉</a> -->
                                </div>
                                <div class="contact-box" v-if="user != item.userid">
                                    <a href="javascript:;" v-on:click="toChat(item)" :title="item.type > 1 ? item.sellmoble : item.buymoble" class="contact-btn" :phone="item.type > 1 ? item.sellmoble : item.buymoble">联系对方</a>
                                    <!-- <a href="javascript:;">我要申诉</a> -->
                                </div>
                            </div>
                            <div class="pay-type">
                                <h4>请选择以下</h4>
                                <div class="saler-account-info">
                                    <div class="bank-card type-box" v-if="item.payinfo.hasOwnProperty('bankinfo') && item.payinfo.bankinfo">
                                        <h4>银行转账</h4>
                                        <p>卖家名称: <span>{{ item.payinfo.bankinfo.name }}</span><i></i></p>
                                        <p>银行名: <span>{{ item.payinfo.bankinfo.bank }}</span><i></i></p>
                                        <p>银行卡号: <span>{{ item.payinfo.bankinfo.bankcard }}</span><i></i></p>
                                        <p>付款金额: <em>￥{{ item.price }}</em> <i></i></p>
                                        <p>切勿备注"比特币"、"虚拟币"等信息</p>
                                    </div>
                                    <div class="Alipay type-box" v-if="item.payinfo.hasOwnProperty('zfb_info') && item.payinfo.zfb_info">
                                        <h4>支付宝</h4>
                                        <p>卖家名称: <span>{{ item.payinfo.zfb_info.name }}</span><i></i></p>
                                        <p>支付宝账号: <span>{{ item.payinfo.zfb_info.account }}</span><i></i></p>
                                        <p>付款金额: <em>￥{{ item.price }}</em> <i></i></p>
                                        <p>切勿备注"比特币"、"虚拟币"等信息</p>
                                        <p>请留意与卖家姓名是否一致</p>
                                        <a href="javascript:;" class="payimg-btn"></a>
                                        <div class="payimg-show">
                                            <i class="close-btn"></i>
                                            <img :src="item.payinfo.zfb_info.payimg" alt="">
                                        </div>
                                    </div>
                                    <div class="wxpay type-box" v-if="item.payinfo.hasOwnProperty('wxinfo') && item.payinfo.wxinfo">
                                        <h4>微信</h4>
                                        <p>卖家名称: <span>{{ item.payinfo.wxinfo.name }}</span><i></i></p>
                                        <p>微信账号: <span>{{ item.payinfo.wxinfo.waccount }}</span><i></i></p>
                                        <p>付款金额: <em>￥{{ item.price }}</em> <i></i></p>
                                        <p>切勿备注"比特币"、"虚拟币"等信息</p>
                                        <p>请留意与卖家姓名是否一致</p>
                                        <a href="javascript:;" class="payimg-btn"></a>
                                        <div class="payimg-show">
                                            <i class="close-btn"></i>
                                            <img :src="item.payinfo.wxinfo.wximg" alt="">
                                        </div>
                                    </div>
                                </div>

                                <div class="trade-warning">
                                    <h4></h4>
                                </div>
                            </div>
                            <p class="warn-text">非自动扣款，请本人收款成功后点击“确认收款”。对方付款后未及时确认收款，经核实，将会暂停账号功能。（转账切勿备注“比特币”、“虚拟币”等信息，否则禁止C2C交易）</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="/Public/Home/js/vue.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/md5.min.js"></script>
    <script>
        var socket_addr ='<?php echo ($socket_addr); ?>';
        var chat = chatFactory.getInstance();
        chat.setUserId(<?php echo (session('userId')); ?>);
        chat.connectServer(socket_addr);
        chatFactory.setMsgbox(function(msg){
            layer.msg(msg,{icon: 2});
        });
    </script>
    <script>
        Vue.component('app-component', {
            template: '#app-template',
            components: {
                'child': {
                    props: ['result','user'],
                    template: '#child-template',
                    data: function(){
                        return {

                        }
                    },
                    methods: {
                        //启动聊天窗口
                        toChat: function(data){
                            //打开聊天
                            if(<?php echo (session('userId')); ?> != data.userid){
                                chat.init({
                                    toid : data.userid,
                                    truename : data.type > 1 ? data.selltruename : data.buytruename,
                                    myid : data.peerid,
                                    username : data.type > 1 ? data.buytruename : data.selltruename,
                                    tradeid : data.trade_id
                                }).open();
                                chat.sendMessage({
                                    type: 'hsy',
                                    myid: data.peerid,
                                    toid: data.userid,
                                    tradeid : data.trade_id
                                })
                            }else{
                                chat.init({
                                    toid : data.peerid,
                                    truename : data.type > 1 ? data.buytruename : data.selltruename,
                                    myid : data.userid,
                                    username : data.type > 1 ? data.selltruename : data.buytruename,
                                    tradeid : data.trade_id
                                }).open();
                                chat.sendMessage({
                                    type: 'hsy',
                                    myid: data.userid,
                                    toid: data.peerid,
                                    tradeid : data.trade_id
                                })
                            }
                            
                        }
                    }
                }
            },
            data: function() {
                return {
                    USERID: <?php echo (session('userId')); ?>,
                    count_time: 30, // 交易倒计时
                    imgs: [1,2,3],
                    buyRate: 0,
                    sellRate: 0,
                    buyNum_on: false,
                    buyPrice_on: false,
                    buyNum: '',
                    buyPrice: '',
                    sellNum_on: false,
                    sellPrice_on: false,
                    sellNum: '',
                    sellPrice: '',
                    buy_paytype: [],
                    sell_paytype: [],
                    sum_price: 0,
                    pay_type: false,
                    buy_list: [],
                    sell_list: [],
                    password: '',
                    timer: null,
                    timer2: null,
                    notice_data: { // 下单提交数据
                        num: 0,
                        price: 0,
                        rate: 0,
                        paytype: [],
                        type: '',
                    },
                    trade_data: { // 挂单列表手动交易下单数据
                        tradeNum: 0,
                        tradePrice: 0,
                        tradeRate: 0,
                        tradePaytype: [],
                        tradeType: '',
                        password: '',
                        peerid: 0,
                        gdid: 0,
                    },
                    trade_num: 0,
                    trade_price: 0,
                    trade_num_on: false,
                    trade_price_on: false,

                    cancle_data : { //撤销订单提交数据
                        trade_id: 0, // 订单id
                        userid: 0, // 用户id
                    },
                    sure_data : {
                        userid: 0,
                        id: 0,
                        buyid: 0,
                        sellid: 0,
                        num: 0,
                        status: 0
                    },
                    result_orders: [],// child模块数据
                    dur_time: 0,
                    ifcard: false,
                    index: '',
                    tradeStatus: true,
                    listTradeStatus: true,
                    //聊天需要的数据
                    chat_trade_data: {}
                }
            },
            created: function() {
                var vm = this;

                $.ajax({ // 获取汇率
                    url:'/Mapi/Bcb/globalTrade',
                    dataType: 'json',
                    type: 'GET',
                    success: function(res) {

                        vm.buyRate =  parseFloat(res.CNY).toFixed(2);
                        vm.sellRate = parseFloat(res.CNY * 6.35 / 6.5 ).toFixed(2);
                    },
                    error: function(msg) {
                        console.log(msg);
                    }
                })
            },
            watch: {
                trade_num: function(curVal,old) {
                    var vm = this;
                    if(vm.trade_num_on && !vm.trade_price_on) {
                        vm.trade_num = vm.filerInput(curVal);
                        vm.trade_price = Math.round(parseFloat(vm.trade_data.tradeRate * 100 * curVal)) / 100;
                    }
                },
                trade_price: function(curVal,old) {
                    var vm = this;
                    if(!vm.trade_num_on && vm.trade_price_on) {
                        vm.trade_price = vm.filerInput(curVal);
                        vm.trade_price = curVal >= 1 ? curVal : 1 ;

                        vm.trade_num = Math.round(parseFloat( (curVal / vm.trade_data.tradeRate) * 1000000 )) / 1000000;
                    }
                },
                buyNum: function(curVal,oldVal) { // 监听下单买入数量
                    var vm = this;
                    if(vm.buyNum_on && !vm.buyPrice_on) {
                        vm.buyNum = vm.filerInput(curVal);
                        vm.buyPrice = Math.round(parseFloat(vm.buyRate * 100 * vm.buyNum)) / 100;
                    }
                },
                buyPrice: function(curVal,oldVal) { // 监听买入金额
                    var vm = this;
                    if(!vm.buyNum_on && vm.buyPrice_on) {
                        vm.buyPrice = vm.filerInput(curVal);
            
                        vm.buyPrice < 1 ? vm.buyPrice = '' : '';
                        vm.buyNum = Math.round(parseFloat( (vm.buyPrice / vm.buyRate) * 1000000 )) / 1000000;
                    }
                },
                sellNum: function(curVal,oldVal) { // 监听卖出数量
                    var vm = this;
                    if(vm.sellNum_on && !vm.sellPrice_on) {
                        vm.sellNum = vm.filerInput(curVal);

                        vm.sellPrice = Math.round(parseFloat(vm.sellRate * 100 * vm.sellNum)) / 100;
                    }
                },
                sellPrice: function(curVal,oldVal) { // 监听卖出金额
                    var vm = this;
                    if(!vm.sellNum_on && vm.sellPrice_on) {
                        vm.sellPrice = vm.filerInput(curVal);

                        vm.sellPrice < 1 ? vm.sellPrice = '' : '';
                        vm.sellNum = Math.round(parseFloat( (vm.sellPrice / vm.sellRate) * 1000000 )) / 1000000;
                    }
                },

                result_orders: function(curVal,oldVal) {
                    var vm = this;
                    vm.result_orders = curVal;
                }

            },
            methods: {
                filerInput: function(val) {
                    var str ;
                    str = val.toString();
                    return str.replace(/^0[0-9]/,'0').replace(/\.+/g,'.').replace(/[a-zA-Z]|\+|\-/g,'').replace(/[\u4e00-\u9fa5]/g,'');

                },
                cur_order: function(type) { // 获取我的当前订单
                    var vm = this;
                    var data = {
                        type: type, // 1 为完成订单 2 已完成订单 3 已取消订单
                        userid: vm.USERID
                    }
                    clearInterval(vm.timer2);
                    vm.timer2 = null;
                    vm.timer2 = setInterval(function() {

                        $.ajax({ // 查询当前订单
                            url:'/home/Ctwoc/nowtrade',
                            dataType: 'json',
                            type: 'POST',
                            crossDomain: true,
                            data: data,
                            success: function(res) {

                                for(var i= 0;i<res.data.length;i++) {
                                    res.data[i].addtime = res.data[i].addtime.replace(/-/g,'/');
                                    res.data[i].endtime =   vm.count_time * 60 * 1000 + new Date(res.data[i].addtime).getTime();
                                    res.data[i].durtime = vm.count_time * 60 * 1000 + new Date(res.data[i].addtime).getTime() - new Date().getTime();
                                    res.data[i].minutes = new Date(res.data[i].durtime).getMinutes();
                                    res.data[i].seconds = new Date(res.data[i].durtime).getSeconds();

                                    if(res.data[i].durtime < 0 && res.data[i].status < 1) {
                                        var data = {
                                            trade_id: res.data[i].trade_id, // 订单id
                                            userid: vm.USERID,
                                        }
                                        vm.delOrder(data);
                                    }
                                }
                                vm.result_orders = res.data;

                                layer.closeAll("loading");
                            },
                            error: function(msg) {
                                console.log(msg);
                            }
                        })
                    },1000)


                },
                sub_buy: function() { // 买入
                    var vm = this;
                    vm.buy_paytype = [];
                    $('.maichu').find('.pay-type-ul').children('.checked-item').each(function() {
                        vm.buy_paytype.push($(this).children('input').val())
                    })
                    if(!vm.USERID) {
                        layer.msg('请先登录',{icon: 2});
                        return;
                    } else if (vm.buy_paytype.length < 1) {
                        layer.msg('请选择支付方式',{icon: 2});
                        return;
                    } else if ( !vm.buyNum || vm.buyNum < 0 ) {
                        layer.msg('请输入买入量',{icon: 2});
                        return;
                    } else if (!vm.buyPrice || vm.buyPrice < 0) {
                        layer.msg('请输入金额',{icon: 2});
                        return;
                    } else {

                        $('.notice-box').show();
                        $('.notice-box').addClass('buy-pass-box').find('.title span').html('买入');

                    }

                },
                sub_sell: function() { // 卖出
                    var vm = this;
                     vm.sell_paytype = [];
                        $('.mairu').find('.pay-type-ul').children('.checked-item').each(function() {
                            vm.sell_paytype.push($(this).children('input').val())
                        })
                        if(!vm.USERID) {
                            layer.msg('请先登录',{icon: 2});
                            return;
                        } else if (vm.sell_paytype.length < 1) {
                            layer.msg('请选择支付方式',{icon: 2});
                            return;
                        } else if ( !vm.sellNum || vm.sellNum < 0 ) {
                            layer.msg('请输入买入量',{icon: 2});
                            return;
                        } else if (!vm.sellPrice || vm.sellPrice < 0) {
                            layer.msg('请输入金额',{icon: 2});
                            return;
                        } else {
                            $('.notice-box').show();
                            $('.notice-box').addClass('sell-pass-box').find('.title span').html('卖出');
                        }

                },
                trade: function(data) { // 下单
                    var vm = this;
                    vm.tradeStatus = false;

                    $.ajax({
                        url:'/home/Ctwoc/c2cTrade',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {
                            vm.cancle();
                            vm.removPayType();
                            vm.tradeStatus = true;
                            if(res.status == "5002") {
                                layer.msg(res.info);
                                vm.getTradeList();
                            } else if(res.status == "0000") {
                                layer.msg(res.info,{icon: 1})
                            } else {
                                layer.msg(res.info,{icon: 2})
                            }
                            getAccountInfo()
                        },
                        error: function(msg) {
                            vm.cancle()
                            vm.removPayType();
                            vm.tradeStatus = true;
                            console.log(msg);
                        }
                    })

                },
                checkPayType: function(uid,type,el) {
                    var vm = this;
                    var data = {
                        userid: uid,
                        type: type
                    }
                    $.ajax({
                            url:'/home/Ctwoc/ispay',
                            dataType: 'json',
                            type: 'POST',
                            crossDomain: true,
                            data: data,
                            success: function(res) {
                                if(res.status == "success") {
                                    el.addClass('checked-item');
                                } else {

                                    layer.msg(res.info,{icon: 2});
                                }
                            },
                            error: function(msg) {
                                console.log(msg);
                            }
                        })
                },
                getTradeList: function() { // 获取挂单列表
                    var vm = this;
                    vm.buy_list = [];
                    vm.sell_list = [];
                    var buy_html = '';
                    var sell_html='';

                    $.ajax({
                        url:'/Ctwoc/getdata',
                        dataType: 'json',
                        type: 'GET',
                        crossDomain: true,
                        success: function(res) {

                            if(res.data.length) {
                                for(var i = 0;i < res.data.length ;i++) {
                                    res.data[i]['num'] = parseFloat(res.data[i].num);
                                    res.data[i]['price'] = parseFloat(res.data[i].price).toFixed(2);
                                    res.data[i]['truename'] = ui.hideName(res.data[i].truename,0,1) + "**";
                                    if(res.data[i].paytype.length > 1) {
                                        res.data[i].paytype = res.data[i].paytype.split(',');
                                    } else {
                                        res.data[i].paytype = res.data[i].paytype.split('');
                                    }

                                   if(res.data[i].type == 1) {
                                        vm.buy_list.push(res.data[i]);
                                        var paytype_html = '';
                                        for(var n = 0;n < res.data[i].paytype.length; n++) {
                                            paytype_html += '<img class="pay_icon2" width="25px" height="25px" src ="/Upload/coin/' +
                                                (function(){
                                                    var img = '';
                                                    if(res.data[i].paytype[n] == "1") {
                                                        img = "alipay";
                                                    } else if(res.data[i].paytype[n] == "2") {
                                                        img = "wx";
                                                    } else {
                                                        img = "bank";
                                                    }
                                                    return img;
                                                })()

                                            + '.png">'
                                        }
                                        buy_html += '<tr class="odd" id="'+ res.data[i].userid +'">' +
                                                    '<td align="left"><span class="red">买入</span></td>' +
                                                    '<td align="right">'+ res.data[i].parities +'</td>' +
                                                    '<td align="right">'+ res.data[i].num +'</td>' +
                                                    '<td align="right">'+ res.data[i].price +'</td>' +
                                                    '<td align="right">'+ res.data[i].truename +'</td>' +
                                                    '<td align="right">'+ res.data[i].cdan +'</td>' +
                                                    '<td align="right">' +
                                                        paytype_html +
                                                    '</td>' +
                                                    '<td align="right">' +
                                                        '<a href="javascript:;" class="od-btn list_sell_btn" peerid="' + res.data[i].userid + '"type="' + res.data[i].type +'" price="' + res.data[i].price +'"num="'+ res.data[i].num + '"paytype="'+ res.data[i].paytype + '"parities="' + res.data[i].parities + '"gdid="' + res.data[i].id + '">卖出</a>'+
                                                    '</td>' +
                                                '</tr>'

                                   } else if(res.data[i].type == 2) {
                                       vm.sell_list.push(res.data[i]);
                                       var s_paytype_html = '';
                                        for(var n = 0;n < res.data[i].paytype.length; n++) {
                                            s_paytype_html += '<img class="pay_icon2" width="25px" height="25px" src ="/Upload/coin/' +
                                                (function(){
                                                    var img = '';
                                                    if(res.data[i].paytype[n] == "1") {
                                                        img = "alipay";
                                                    } else if(res.data[i].paytype[n] == "2") {
                                                        img = "wx";
                                                    } else {
                                                        img = "bank";
                                                    }
                                                    return img;
                                                })()

                                            + '.png">'
                                        }
                                        sell_html += '<tr class="odd" id="'+ res.data[i].userid +'">' +
                                                    '<td align="left"><span class="green">卖出</span></td>' +
                                                    '<td align="right">'+ res.data[i].parities +'</td>' +
                                                    '<td align="right">'+ res.data[i].num +'</td>' +
                                                    '<td align="right">'+ res.data[i].price +'</td>' +
                                                    '<td align="right">'+ res.data[i].truename +'</td>' +
                                                    '<td align="right">'+ res.data[i].cdan +'</td>' +
                                                    '<td align="right">' +
                                                        s_paytype_html +
                                                    '</td>' +
                                                    '<td align="right">' +
                                                        '<a href="javascript:;" class="od-btn list_buy_btn" peerid="' + res.data[i].userid + '"type="' + res.data[i].type +'" price="' + res.data[i].price +'"num="'+ res.data[i].num + '"paytype="'+ res.data[i].paytype + '"parities="' + res.data[i].parities + '"gdid="' + res.data[i].id +'">买入</a>'+
                                                    '</td>' +
                                                '</tr>'

                                   }
                                }
                                //
                                //$('#pushOrder').find('tbody').html(sell_html  + buy_html);
                            }
                            $('#pushOrder').find('tbody').html(sell_html  + buy_html);
                        },
                        error: function(msg) {
                            console.log(msg);
                        }
                    })
                },

                delOrder: function(data) { // 撤销当前订单
                    var vm = this;
                    $.ajax({
                        url:'/Ctwoc/cxtrde',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {
                            if(res.status == "8105") {
                                layer.msg(res.info,{icon: 1});
                            } else {
                                layer.msg(res.info,{icon: 2});
                            }
                            getAccountInfo();
                            vm.cancle();
                        },
                        error: function(msg) {
                            vm.cancle();
                            console.log(msg);
                        }
                    })
                },
                surePay: function(data) { // 确定收付款
                    var vm = this;
                    $.ajax({
                        url:'/Ctwoc/paymoney',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {
                            if(res.status == "0000") {
                                layer.msg(res.info,{icon: 1});
                            } else {
                                layer.msg(res.info,{icon: 2});
                            }
                            vm.cancle();
                            getAccountInfo();

                        },
                        error: function(msg) {
                            vm.cancle();
                            console.log(msg);

                        }
                    })
                },
                order_trade: function() { // 挂单列表交易
                    var vm = this;
                    $('body').on('click','.list_buy_btn,.list_sell_btn',function(e) {
                        var o =  $(e.target);
                        var text = '';

                        vm.trade_data.gdid = o.attr('gdid');
                        // vm.trade_data.tradeNum = o.attr('num');
                        // vm.trade_data.tradePrice = o.attr('price');
                        vm.trade_num = o.attr('num');
                        vm.trade_price = o.attr('price');
                        vm.trade_data.tradeRate = o.attr('parities') ;
                        vm.trade_data.peerid = o.attr('peerid');
                        // vm.trade_data.tradePaytype = o.attr('paytype').length > 1 ? o.attr('paytype').split(',') : o.attr('paytype').split('');

                        if(o.attr('type') == 1) {
                            text = '卖出';
                            vm.trade_data.tradeType = 2;
                        } else if(o.attr('type') == 2) {
                            text = '买入';
                            vm.trade_data.tradeType = 1;
                        }

                        var userid = vm.USERID;

                        if(!vm.USERID) {
                            layer.msg('请先登录',{icon: 2});
                            return;
                        } else {
                            $('.list-trade-box').show();


                            // for(var i = 0;i < vm.trade_data.tradePaytype.length;i++) {

                            //     $('.list-trade-box').find('.pay-type-ul li:nth-child('+ vm.trade_data.tradePaytype[i] +')').addClass('checked-item');
                            // }
                        }

                    })
                },
                list_trade: function(data) { // 挂单列表下单
                    var vm = this;
                    vm.listTradeStatus = false;
                    $.ajax({
                        url:'/Ctwoc/manualtrade',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {
                            if(res.status == "success") {
                                layer.msg(res.info,{icon: 1});
                                //打开聊天
                                
                                chat.sendMessage({
                                    type: "order_suc",
                                    order: res.data.trade_id
                                })
                            } else {
                                layer.msg(res.info,{icon: 2})
                            }

                            vm.listTradeStatus = true;
                            vm.cancle();
                            vm.removPayType();
                        },
                        error: function(msg) {
                            vm.cancle();
                            vm.removPayType();
                            vm.listTradeStatus = true;
                            console.log(msg);
                        }
                    })


                },
                type_choose: function() { // 支付方式选择
                    var vm = this;
                    $('body').on('click','.pay-type-ul li',function() {
                        var o = $(this);
                        var uid = vm.USERID;
                        var type = o.find('input').val();

                        if(!uid) {
                            layer.msg('请先登录',{icon: 2});
                            return;
                        }

                        if(o.hasClass('checked-item')) {
                            o.removeClass('checked-item');
                        } else {
                            vm.checkPayType(uid,type,o);
                        }
                    })
                },
                cancle: function() { // 取消按钮
                    var vm = this;
                    vm.password = '';
                    vm.trade_data.password = '';
                    $('.notice-box').removeClass('buy-pass-box');
                    $('.notice-box').removeClass('sell-pass-box');
                    $('.notice-box').hide();
                    $('.list-trade-box').hide();
                    $('.layer-box').removeClass('layer-sure');
                    $('.layer-box').removeClass('layer-cancle');
                    $('.layer-box').hide();
                },
                removPayType: function() {
                    $('.pay-type-ul li').each(function(index,item) {
                        $(item).removeClass('checked-item');
                    })
                }

            },
            mounted: function() {
                var vm = this;

                // input onfocuse  输入框焦点控制
                $('#buy_vol').focus(function() {
                    vm.buyNum_on = true;
                })
                $('#buy_vol').blur(function() {
                    vm.buyNum_on = false;
                })
                $('#buy_amount').focus(function() {
                    vm.buyPrice_on = true;
                })
                $('#buy_amount').blur(function() {
                    vm.buyPrice_on = false;
                })

                $('#sell_vol').focus(function() {
                    vm.sellNum_on = true;
                })
                $('#sell_vol').blur(function() {
                    vm.sellNum_on = false;
                })
                $('#sell_amount').focus(function() {
                    vm.sellPrice_on = true;
                })
                $('#sell_amount').blur(function() {
                    vm.sellPrice_on = false;
                })

                $('#trade_vol').focus(function() {
                    vm.trade_num_on = true;
                })
                $('#trade_vol').blur(function() {
                    vm.trade_num_on = false;
                })
                $('#trade_amount').focus(function() {
                    vm.trade_price_on = true;
                })
                $('#trade_amount').blur(function() {
                    vm.trade_price_on = false;
                })


                // 获取挂单交易列表
                vm.timer = setInterval(vm.getTradeList,1000);
                // vm.getTradeList();

                 //用户当前订单
                if(vm.USERID) {
                    vm.cur_order(1); // 初始化显示未完成当前订单
                }

                // 下单 买/卖
                $('#buy_submit').click(function() {
                    vm.sub_buy();
                })
                $('#sell_submit').click(function() {
                    vm.sub_sell();
                })

                $('body').on('click','.buy-pass-box .ok-btn',function(e) { // 下单 买入 确认
                    if(!vm.password) {
                        layer.msg('请输入密码',{icon: 2});
                        return;
                    }

                    var data = {
                        uid: vm.USERID,
                        num: vm.buyNum,
                        price: vm.buyPrice,
                        type: 1,
                        paytype: vm.buy_paytype.join(','),
                        paypassword: md5(vm.password),
                        parities: vm.buyRate
                    }

                    if(vm.tradeStatus) {
                        vm.trade(data);

                    } else {

                        layer.msg('请勿重复提交！',{icon: 2})

                    }

                })
                $('body').on('click','.sell-pass-box .ok-btn',function(e) { // 下单 卖出 确认
                    if(!vm.password) {
                        layer.msg('请输入密码',{icon: 2});
                        return;
                    }

                    var data = {
                        uid: vm.USERID,
                        num: vm.sellNum,
                        price: vm.sellPrice,
                        type: 2,
                        paytype: vm.sell_paytype.join(','),
                        paypassword: md5(vm.password),
                        parities: vm.sellRate
                    }

                    if(vm.tradeStatus) {

                        vm.trade(data);

                    } else {

                        layer.msg('请勿重复提交！',{icon: 2});

                    }
                })

                // 挂单 买、卖
                vm.order_trade();

                $('body').on('click','.sure-btn',function() { // 挂单 买、卖表单提交
                    vm.trade_data.tradePaytype = [];
                    $('.list-trade-box').find('.pay-type-ul').children('.checked-item').each(function() {
                        vm.trade_data.tradePaytype.push($(this).children('input').val())
                    })

                    if(!vm.trade_data.password) {
                        layer.msg('请输入密码',{icon: 2});
                        return;
                    }  else if(!vm.trade_data.tradePaytype.length) {
                        layer.msg('请选择支付方式',{icon: 2});
                        return;
                    } else if (!vm.trade_price) {
                        layer.msg('请填写金额',{icon: 2});
                        return;
                    }else if (!vm.trade_num) {
                        layer.msg('请填写数量',{icon: 2});
                        return;
                    }

                    var data = {
                        userid: vm.USERID,
                        peerid: vm.trade_data.peerid,
                        num: vm.trade_num,
                        price: vm.trade_price,
                        parities: vm.trade_data.tradeRate,
                        type: vm.trade_data.tradeType,
                        paytype: vm.trade_data.tradePaytype.join(','),
                        paypassword: md5(vm.trade_data.password),
                        gdid: vm.trade_data.gdid
                    }

                    if(vm.listTradeStatus) {

                        vm.list_trade(data);

                    } else {

                        layer.msg('请勿重复提交！',{icon: 2});

                    }

                })
-1
                // 支付方式选择
                vm.type_choose();

                // 取消按钮
                $('body').on('click','.cancle-btn',function() {
                    vm.cancle()
                })

                // 撤销订单
                $('body').on('click','.del-button',function() {
                   var o = $(this);

                    vm.cancle_data.trade_id =  o.attr('tradeId'), // 订单id
                    vm.cancle_data.userid = vm.USERID ;

                   if(!o.hasClass('disabled')) {
                       $('.layer-box').show().addClass('layer-cancle').find('.layer-text').html('确认取消订单吗?');
                   } else {
                       return;
                   }
                })
                // 撤销订单确认
                $('body').on('click','.layer-cancle .ok-btn',function() {

                    vm.delOrder(vm.cancle_data);

                })

                // 确认收、付款 发短信通知
                $('body').on('click','.sure-button',function() {
                    var o = $(this).parents('.order-item');
                    var text = $(this).html();
                    var status;
                    if(vm.USERID == o.attr('id')) {
                        status = o.attr('type') > 1 ? '2': '1'
                    } else {
                        status = o.attr('type') > 1 ? '1': '2'
                    }

                    vm.sure_data.userid = vm.USERID,
                    vm.sure_data.id = o.attr('tradeId'),
                    vm.sure_data.buyid = o.attr('buyId'),
                    vm.sure_data.sellid = o.attr('sellId'),
                    vm.sure_data.num = o.attr('num'),
                    vm.sure_data.status = status


                    if(!$(this).hasClass('disabled') && !$(this).hasClass('complete')) {
                        $('.layer-box').show().addClass('layer-sure').find('.layer-text').html('确认' + text);
                        // vm.surePay(data);
                    } else {
                        return;
                    }
                })

                // 确认收、付款 发短信通知
                $('body').on('click','.layer-sure .ok-btn',function() {

                   vm.surePay(vm.sure_data);

                })


                // 展示当前订单详情
                $('body').on('click','.to-detail',function() {
                    if(!$(this).hasClass('active')) {
                        $(this).addClass('active');
                        $(this).parent().siblings('.cur_order_detail').show();
                    } else {
                        $(this).removeClass('active');
                        $(this).parent().siblings('.cur_order_detail').hide();
                    }
                })

                // if($('.to-detail').hasClass('active')) {
                //     $('.to-detail.active').parent().siblings('.cur_order_detail').show();
                // }

                // 支付宝，微信收款码
                $('body').on('click','.payimg-btn',function() {
                    $(this).siblings('.payimg-show').show();
                })

                // 支付宝，微信收款码 close
                $('body').on('click','.payimg-show .close-btn',function() {
                    $(this).parent('.payimg-show').hide();
                })

                // nav (当前订单，已完成订单，撤销订单)切换
                $('body').on('click','.orders-nav li',function() {

                    var name = $(this).attr('name');
                    if(!$(this).hasClass('active')) {

                        vm.index = layer.load(1, {
                            shade: [0.1,'#fff'] //0.1透明度的白色背景
                        });

                        $(this).addClass('active').siblings().removeClass('active');
                    }
                    switch(name){
                        case 'cur':
                            vm.cur_order(1);
                            break;
                        case 'comp':
                           vm.cur_order(2);
                            break;
                        case 'cancle':
                            vm.cur_order(3);
                            break;
                    }
                })

                // 联系对方
                // $('body').on('click','.contact-btn',function(e) {
                //     var o = e.target;
                //     console.log($(o).data('name'));
                //     var phone = $(this).attr('phone');
                //     layer.tips('联系对方: ' + phone,o, { tips: [1, '#6b6565'],time: 4000})
                // })

                // 交易须知提示
                $('body').on('click','.jyxz',function() {
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-molv',
                        title: '交易须知',
                        shade: false,
                        colseBtn: 1,
                        area: ['600px','640px'],
                        content: '<div class="ui-layer-page"><p>1、C2C交易是用户之间点对点交易，买方场外转账付款，卖方收到款后进行确认发币。</p>'+
                                '<p>2、买卖商家均需要实名认证，提供保证金，可放心兑换。</p>' +
                                '<p>3、市场挂单推荐在线商家挂出的最优价格，可以选择商家“买入”或者“卖出”。</p>' +
                                '<p>4、转账付款时必须使用本人绑定的银行账户或支付宝、微信进行转账。</p>' +
                                '<p>5、交易时最优价格是当前最高买价，在市场挂单可以查询对方挂单的金额，交易限额，收/付款方式等。</p>' +
                                '<p>6、C2C订单根据交易量，付款方式等会进行匹配，如果超出最优价格交易量或者支付方式不同，会匹配其他订单。价格跟估价不同时，下单时会有提示，务必确认订单数量和金额后下单。</p>' +
                                '<p>7、成交后务必在30分钟之内付款，转账后立即点击“我已付款”。</p>' +
                                '<p>8、完成付款后，打开订单可以”联系对方“，提醒对方查询收款账户，确认收款。</p>' +
                                '<p>9、C2C支持全天24小时交易，卖家收到款后1小时之内进行确认收款，系统自动发币。</p>' +
                                '<p>10、付款成功后1小时对方没有确认收款，打开订单进行申诉，提供付款证明帮您联系商家核实处理。</p>' +
                                '<p>11、买卖双方本着诚实守信的原则进行交易，交易款项直接转到卖家收款账户（注意：1.卖家收款账户必须与本人实名一致 2. 买家转款必须核对收款人姓名与卖家实名一致），务必根据订单信息进行付款，对此平台不做担保。</p>' +
                                '<p>12、买家必须在支付完成后，点击完成付款。若未付款点击已付款，在订单下面联系对方说明情况，并申诉告知客服取消订单。</p>' +
                                '<p>13、卖家务必在核实收到款项后，再点击确认收款。如果误操作，在没有收到款项的情况下点击确认收款，在订单下面联系对方，请求对方尽快付款。双方协商不成的，打开订单进行申诉。</p>' +
                                '<p>14、参与场外交易时，务必保持电话通畅，关注短信和邮件提醒。</p>' +
                                '<p>15、在产生异议时对方申请仲裁，平台多次联系不到的，平台有权根据对方提交的有利于证据，判断将数字资产转给提供充份证据的一方。</p>' +
                                '<p>16、对于多次联系不到，并且给对方造成损失的，平台有权将您的联系方式提供给对方，并禁止C2C交易。</p>' +
                                '<p>17、买家没有及时完成付款操作，或卖家没有收到款的情况下点击确认收款，都会造成钱币两空，请务必按照流程操作避免造成经济损失。</p>' +
                                '<p>18、如未付款，可以撤销交易，撤销后切勿再付款；如果已经付款，切勿撤销。否则有可能造成损失，请务必确认无误再进行操作。</p>' +
                                '<p>19、在交易过程中请遵循诚实信用原则进行操作，如果您被投诉次太多，或者由于您本人原因多次取消交易的，将限制交易资格。</p>' +
                                '<p>20、有多个买卖单未完成，暂停继续下单，完成后恢复。下单买入后超时不付款，超过三单将禁止两天C2C交易。</p></div>'
                    })
                })
            }
        });
        new Vue({
            el: '#app',
        });
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