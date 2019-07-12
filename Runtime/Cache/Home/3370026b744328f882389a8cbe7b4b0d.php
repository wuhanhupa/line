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
                        <li class="active">
                            <a href="/cztrade/index"><b>USDT/CNY </b><span>彧捷支付</span></a>
                        </li>
                        <!--<li>
                            <a href="/fupay/index"><b>USDT/CNY </b><span>富π支付</span></a>
                        </li>
                        <li>
                            <a href="/Ctwoc/index">
                                <b>USDT/CNY </b><span>USDT/人民币</span>
                            </a>
                        </li>-->
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
                                    <a class="red" href="/Article/detail/id/44.html" target="_blank">如何买入？</a>
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
                                                <span class="b-unit input-title" id="buy_total_label">金额 CNY</span>
                                                <input id="buy_amount" type="text" maxlength="10"  placeholder="请输入100的整数倍" class="inputRate" v-model="buyPrice" style="padding-right: 25px;" onkeyup='if(event.keyCode!=8 && event.keyCode!=37 && event.keyCode!=16 && event.keyCode!=20 && event.keyCode!=39 && (event.keyCode<48 || event.keyCode>105) && (!event.shiftKey && event.keyCode != 189))this.value=this.value.replace(/[^\w_]/g,"");'>
                                                <i style="
                                                    position: absolute;
                                                    right: 11px;
                                                    top: 21px;
                                                    font-size: 14px;
                                                    font-style: normal;
                                                    display:none;
                                                ">00</i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">买入量 USDT</span>
                                                <input id="buy_vol" type="text" maxlength="10" readonly="readonly" class="inputRate" v-model="buyNum">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="pull-left">支付方式：</span>
                                                <ul class="pay-type-ul yj-pay-type">
                                                    <li>
                                                       <!-- <i class="icon-empty"></i> -->
                                                       <!-- <input id="buy_pay_ali" type="checkbox" value="1" hidden=""> -->
                                                        <span>支付宝</span>
                                                    </li>
                                                    <li>
                                                       <!-- <i class="icon-empty"></i> -->
                                                       <!-- <input id="buy_pay_wechat" type="checkbox" value="2" hidden=""> -->
                                                        <span>微信</span>
                                                    </li>
                                                    <li>
                                                        <!-- <i class="icon-empty"></i> -->
                                                        <!-- <input id="buy_pay_bank" type="checkbox" value="3" hidden=""> -->
                                                        <span>银行转账</span>
                                                    </li>
                                                </ul>
                                                <span class="red">（必须本人支付）</span>
                                                <a class="jyxz" href="javascript:;">《交易须知》</a>

                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td" style="border:0">
                                                <?php if($buy == true ): ?><input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="ask" id="buy_submit" value="买入 (CNY→USDT)">
                                                <?php else: ?>
                                                <input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="ask" id="buy_submit" disabled="disabled" value="买入 (CNY→USDT)"><?php endif; ?>
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
                                    <a class="red" href="/Article/detail/id/44.html" target="_blank">如何卖出？</a>
                                </div>
                                <table class="dealbox" cellspacing="0">
                                    <tbody>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit ask-bid-price input-title">卖出估价 <span>CNY</span></span>
                                                <input id="sell_rate" class="inputRate green" maxlength="10" readonly="readonly" :value="sellRate">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">金额 CNY</span>
                                                <input id="sell_amount" class="inputRate" maxlength="10" placeholder="请输入100的整数倍" v-model="sellPrice" style="padding-right: 25px;" onkeyup='if(event.keyCode!=8 && event.keyCode!=37 && event.keyCode!=16 && event.keyCode!=20 && event.keyCode!=39 && (event.keyCode<48 || event.keyCode>105) && (!event.shiftKey && event.keyCode != 189))this.value=this.value.replace(/[^\w_]/g,"");'>
                                                <i style="
                                                    position: absolute;
                                                    right: 11px;
                                                    top: 21px;
                                                    font-size: 14px;
                                                    font-style: normal;
                                                    display:none;
                                                ">00</i>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="b-unit input-title">卖出量 USDT</span>
                                                <input id="sell_vol" class="inputRate" readonly="readonly" maxlength="10" v-model="sellNum">
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td">
                                                <span class="pull-left">支付方式：</span>
                                                <ul class="pay-type-ul yj-pay-type">
                                                    <li>
                                                        <!-- <i class="icon-empty"></i> -->
                                                        <!-- <input id="sell_pay_ali" type="checkbox" value="1" hidden=""> -->
                                                        <span>支付宝</span>
                                                    </li>
                                                    <li>
                                                          <!-- <i class="icon-empty"></i> -->
                                                          <!-- <input id="sell_pay_wechat" type="checkbox" value="2" hidden=""> -->
                                                        <span>微信</span>
                                                    </li>
                                                    <li>
                                                       <!-- <i class="icon-empty"></i> -->
                                                       <!-- <input id="sell_pay_bank" type="checkbox" value="3" hidden=""> -->
                                                        <span>银行转账</span>
                                                    </li>
                                                </ul>
                                                <span class="red">（必须本人支付）</span>
                                                <a class="jyxz" href="javascript:;">《交易须知》</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="input-td" style="border:0">
                                                <?php if($sell == true ): ?><input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="bid" id="sell_submit" value="卖出 (USDT→CNY)">
                                                <?php else: ?>
                                                <input type="button" class="btnAskBid jiaoyi_btn  button button-flat-action" t="bid" id="sell_submit" disabled="disabled" value="卖出 (USDT→CNY)"><?php endif; ?>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="clearfix">
                              
                            <div class="c2c-notice-text-box ">
                                    <p> 温馨提示</p>

                                    <p>1.买卖商户均为实地考察认证商户，您每次兑换会冻结商户资产，商户资产不够时，不能接单，可放心兑换</p>
                                    <p> 2.买卖商家均为实名认证商户，并提供保证金，可放心兑换</p>
                                    <p>3.请使用本人绑定的银行卡、支付宝或微信付款</p>
                                    <p>4.商家全天候处理订单，一般接单后24小时会完成打款</p>
                                    <p> 5.单笔订单不得超过￥20000.00，单日不得超过￥20000.00</p>
                                    <p>6.支付过程中出现异常时（未完成支付），请重新下单</p>
                            </div>
                        </div>
                        
                        <div id="divMyRecordSection" class="clearfix" >
                            <div class="my-order-box">
                                <div class="my-orders-list sectioncont">
                                    <child :result="result_orders" :user="USERID" :type="orderType"></child>
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

            <div class="order-list-header Cztrade-order-list-header">
                <ul>
                    <li>订单号</li>
                    <li>类型</li>
                    <li>价格(CNY)</li>
                    <li>数量(USDT)</li>
                    <li>金额(CNY)</li>
                    <li>状态</li>
                    <li>建立时间</li>
                    <li v-if="type < 2">操作</li>
                </ul>
            </div>
            <div class="list-box Cztrade-list-box">
                <ul>
                    <li class="order-item " v-for="(item,index) in result" :tradeId="item.trade_id" :buyId="item.buyid" :sellId="item.sell_id" :num="item.num" :type="item.type" :id="item.userid">
                        <div class="order-info">
                            <span>{{ item.order_number }}</span>
                            <span>{{ item.type > 1 ? '卖出': '买入' }}</span>
                            <span>{{ item.parities ? item.parities : '--' }}</span>
                            <span>{{ item.num }}</span>
                            <span>{{ item.price }}</span>
                            <span>{{ item.order_status }}</span>
                            <span>{{ item.crtime }}</span>
                            <span v-if="type < 2" ><a :href="item.url">点击跳转</a></span>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </script>
    <script type="text/javascript" src="/Public/Home/js/vue.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/md5.min.js"></script>
    <script>
        Vue.component('app-component', {
            template: '#app-template',
            components: {
                'child': {
                    props: ['result','user','type'],
                    template: '#child-template',
                    data: function(){
                        return {

                        }
                    },
                }
            },
            data: function() {
                return {
                    USERID: <?php echo (session('userId')); ?>,
                    orderType: '', // 我的订单导航栏状态
                    count_time: 30, // 交易倒计时

                    buyRate: 0, // 买入汇率
                    sellRate: 0, // 卖出汇率

                    buyNum_on: false, // 买入数量输入框是否获取焦点
                    buyPrice_on: false,
                    buyNum: '', // 买入数量
                    buyPrice: '',
                    sellNum_on: false, // 卖出数量输入框是否获取焦点
                    sellPrice_on: false,
                    sellNum: '',
                    sellPrice: '',
                    buy_paytype: [], // 买入支付方式存储数组
                    sell_paytype: [],
                    sum_price: 0,
                    pay_type: false, 
                    password: '', // 交易密码

                    notice_data: { // 下单提交数据
                        num: 0,
                        price: 0,
                        rate: 0,
                        paytype: [],
                        type: '',
                    },

                    result_orders: [],// child模块数据

                    index: '',
                    tradeStatus: true,
                    
                }
            },
            created: function() {
                var vm = this;
                
                
                $.ajax({ // 调取汇率接口
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
                
                // 进页面调取注册接口
                $.getJSON('/Cztrade/yj_register',function(data) {
                    console.log(data);
                })


            },
            watch: {
                buyNum: function(curVal,oldVal) { // 监听下单买入数量
                    var vm = this;
                    if(vm.buyNum_on && !vm.buyPrice_on) {
                        vm.buyNum = vm.filerInput(curVal);
                        vm.buyPrice = Math.round(parseFloat(vm.buyRate * 100 * curVal)) / 100;
                    }
                },
                buyPrice: function(curVal,oldVal) { // 监听买入金额
                    var vm = this;
                    if(!vm.buyNum_on && vm.buyPrice_on) {
                        vm.buyPrice = vm.filerInput(curVal);
                        vm.buyPrice < 1 ? vm.buyPrice = '' : '';
                        vm.buyNum = Math.round(parseFloat( (vm.buyPrice * 100 / vm.buyRate) * 1000000 )) / 1000000;
                    }
                },
                sellNum: function(curVal,oldVal) { // 监听卖出数量
                    var vm = this;
                    if(vm.sellNum_on && !vm.sellPrice_on) {
                        vm.sellNum = vm.filerInput(curVal);

                        vm.sellPrice = Math.round(parseFloat(vm.sellRate * 100 * curVal)) / 100;
                    }
                },
                sellPrice: function(curVal,oldVal) { // 监听卖出金额
                    var vm = this;
                    if(!vm.sellNum_on && vm.sellPrice_on) {
                        vm.sellPrice = vm.filerInput(curVal);
                        vm.sellPrice < 1 ? vm.sellPrice = '' : '';
                        vm.sellNum = Math.round(parseFloat( (vm.sellPrice * 100 / vm.sellRate) * 1000000 )) / 1000000;
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
                    // return str.replace(/^0[0-9]/,'0').replace(/\.+/g,'.').replace(/[a-zA-Z]|\+|\-/g,'').replace(/[\u4e00-\u9fa5]/g,'');

                    return str.replace(/^0[0-9]/,'').replace(/\.+/g,'').replace(/[a-zA-Z]|\+|\-/g,'').replace(/[\u4e00-\u9fa5]/g,'');

                },
                cur_order: function(type) { // 获取我的当前订单
                    var vm = this;
                    vm.result_orders = [];

                    vm.orderType = type;
                    var data = {
                        type: type, // 1 为完成订单 2 已完成订单 3 已取消订单
                        userid: vm.USERID
                    }
                    $.ajax({ // 查询当前订单
                        url:'/Cztrade/order_list',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {

                            vm.result_orders = res.data;

                            layer.closeAll("loading");
                        },
                        error: function(msg) {
                            console.log(msg);
                        }
                    })


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
                    } else if ( !vm.buyNum || vm.buyNum < 0 ) {
                        layer.msg('请输入买入量',{icon: 2});
                        return;
                    } else if (!vm.buyPrice || vm.buyPrice < 0) {
                        layer.msg('请输入金额',{icon: 2});
                        return;
                    } 
                    // else if (vm.buy_paytype.length < 1) {
                    //     layer.msg('请选择支付方式',{icon: 2});
                    //     return;
                    // } 
                    else {

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
                        } else if ( !vm.sellNum || vm.sellNum < 0 ) {
                            layer.msg('请输入买入量',{icon: 2});
                            return;
                        } else if (!vm.sellPrice || vm.sellPrice < 0) {
                            layer.msg('请输入金额',{icon: 2});
                            return;
                        } 
                        // else if (vm.sell_paytype.length < 1) {
                        //     layer.msg('请选择支付方式',{icon: 2});
                        //     return;
                        // } 
                        else {
                            $('.notice-box').show();
                            $('.notice-box').addClass('sell-pass-box').find('.title span').html('卖出');
                        }

                },
                trade: function(data,n) { // input表单下单（买入、卖出）
                    var vm = this;
                        
                    n = n ? n : 0;

                    vm.tradeStatus = false;

                    vm.cancle();

                    $.ajax({
                        url:'/Cztrade/member_order',
                        dataType: 'json',
                        type: 'POST',
                        crossDomain: true,
                        data: data,
                        success: function(res) {
                           
                            vm.removPayType();

                            vm.tradeStatus = true;

                            if(res.code == '0000' && res.data) {

                                window.location.href = res.data;

                            }else if(res.code != '0000' && !res.code_status){         
                                    layer.closeAll();
                                    layer.msg(res.msg,{icon: 2});

                            } else if(res.code_status) {
                                n++
                                if(n < 10) {
                                    vm.trade(data,n);
                                } else {
                                    layer.closeAll();
                                    layer.msg('提交失败，请稍后再试',{icon: 2});
                                }
                                
                            }

                        },
                        error: function(msg) {

                            vm.cancle()

                            vm.removPayType();

                            vm.tradeStatus = true;

                            layer.closeAll();

                            console.log(msg);
                        }
                    })

                },
                checkPayType: function(uid,type,el) { // 检测支付类型
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
                
                type_choose: function() { // 支付方式选择
                    var vm = this;
                    // $('body').on('click','.pay-type-ul li',function() {
                    //     var o = $(this);
                    //     var uid = vm.USERID;
                    //     var type = o.find('input').val();

                    //     if(!uid) {
                    //         layer.msg('请先登录',{icon: 2});
                    //         return;
                    //     }

                    //     if(o.hasClass('checked-item')) {
                    //         o.removeClass('checked-item');
                    //     } else {
                    //         vm.checkPayType(uid,type,o);
                    //     }
                    // })
                },
                cancle: function() { // 取消按钮
                    var vm = this;
                    vm.password = '';

                    $('.notice-box').removeClass('buy-pass-box');
                    $('.notice-box').removeClass('sell-pass-box');
                    $('.notice-box').hide();
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
                    $('#buy_amount').attr('placeholder','').siblings('i').show();
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
                    $('#sell_amount').attr('placeholder','').siblings('i').show();
                })
                $('#sell_amount').blur(function() {
                    vm.sellPrice_on = false;
                })


                 //用户当前订单
                if(vm.USERID) {
                    vm.cur_order(1);
                }

                // input表单下单 买/卖
                $('#buy_submit').click(function() {
                    vm.sub_buy();
                })
                $('#sell_submit').click(function() {
                    vm.sub_sell();
                })

                $('body').on('click','.buy-pass-box .ok-btn',function(e) { // input表单下单 买入 确认
                    if(!vm.password) {
                        layer.msg('请输入密码',{icon: 2});
                        return;
                    }

                    var data = {
                        // uid: vm.USERID,
                        num: vm.buyNum,
                        price: vm.buyPrice * 100,
                        type: 1,
                        // paytype: vm.buy_paytype.join(','),
                        paytype: 2,
                        paypassword: md5(vm.password),
                        parities: vm.buyRate
                    }

                    if(vm.tradeStatus) {

                        layer.load(0, {
                            shade: [0.3,'#fafafa'] //0.1透明度的白色背景
                        });

                        vm.trade(data);

                    } else {

                        layer.msg('请勿重复提交！',{icon: 2})

                    }

                })
                $('body').on('click','.sell-pass-box .ok-btn',function(e) { // input表单单 卖出 确认
                    if(!vm.password) {
                        layer.msg('请输入密码',{icon: 2});
                        return;
                    }

                    var data = {
                        // uid: vm.USERID,
                        num: vm.sellNum,
                        price: vm.sellPrice * 100,
                        type: 2,
                        // paytype: vm.sell_paytype.join(','),
                        paytype: 2,
                        paypassword: md5(vm.password),
                        parities: vm.sellRate
                    }

                    if(vm.tradeStatus) {

                        layer.load(0, {
                            shade: [0.3,'#fafafa'] //0.1透明度的白色背景
                        });

                        vm.trade(data);

                    } else {

                        layer.msg('请勿重复提交！',{icon: 2});

                    }
                })


                // 支付方式选择
                vm.type_choose();

                // 取消按钮
                $('body').on('click','.cancle-btn',function() {
                    vm.cancle()
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