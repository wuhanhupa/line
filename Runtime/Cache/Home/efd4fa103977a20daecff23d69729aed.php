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
<script src="/Public/Home/js/datepicker.min.js"></script>
<div class="autobox">
	<div class="now">
		<a href="/" class="orange">首页</a> &gt; <a href="/finance/" class="orange">财务中心</a> &gt; 成交查询
	</div>
	<div class="assets_center clear po_re zin70">
		<!--左侧菜单-->
		<div class="coin_menu">
	<div class="coin_menu_box">
		<ul>
			<li id="finance_index"><i class="ui-icon ui-icon-money"></i><a href="/finance/">财务中心</a></li>
			<li id="finance_mywt"><i class="ui-icon ui-icon-weituo"></i><a href="/finance/mywt.html">委托管理</a></li>
			<li id="finance_mycj"><i class="ui-icon ui-icon-search"></i><a href="/finance/mycj.html">成交查询</a></li>
		</ul>
	</div>
	<!--div class="coin_menu_box">
		<ul>
			<li id="finance_mycz"><i class="coin_menu_op_18_1"></i><a href="/finance/mycz.html">人民币充值</a></li>
			<li id="finance_mytx"><i class="coin_menu_op_2_1"></i><a href="/finance/mytx.html">人民币提现</a></li>
		</ul>
	</div-->
	<!-- <div class="coin_menu_box">
		<ul>
			<li id="finance_myzr"><i class="coin_menu_op_4_1"></i><a href="/finance/myzr.html">转入虚拟币</a></li>
			<li id="finance_myzc"><i class="coin_menu_op_5_1"></i><a href="/finance/myzc.html">转出虚拟币</a></li>
		</ul>
	</div> -->
	<!-- <div class="coin_menu_box">
		<ul>
			<li id="finance_mywt"><i class="coin_menu_op_6_1"></i><a href="/finance/mywt.html">委托管理</a></li>
			<li id="finance_mycj"><i class="coin_menu_op_7_1"></i><a href="/finance/mycj.html">成交查询</a></li>
		</ul>
	</div> -->
	<!--<div class="coin_menu_box">
		<ul>
            <li id="finance_mytj"><i class="coin_menu_op_13_1"></i><a href="/finance/mytj.html">推荐用户</a></li>
			<li id="finance_mywd"><i class="coin_menu_op_16_1"></i><a href="/finance/mywd.html">我的推荐</a></li>
			<li id="finance_myjp"><i class="coin_menu_op_19_1"></i><a href="/finance/myjp.html">我的奖品</a></li>
		</ul>
	</div>-->
</div>
<script>
	//顶部菜单高亮
	//$('.coin_menu_box a').hover(function(){var str=str_1=$(this).parent().find('i').attr('class');if(str.length>15)str=str.substring(0,str.length-2);$(this).parent().find('i').attr('class',str)},function(){$(this).parent().find('i').attr('class',str_1)});
</script>

		<!--右侧内容-->
		<div class="assets-content assets_content w900 right bg_w">

			<div class="ui-title">
				<span>成交查询</span>
			</div>
			<?php if(!empty($prompt_text)): ?><div class="ui-alert">
        <h4>温馨提示：</h4>
        <p><?php echo ($prompt_text); ?></p>
    </div><?php endif; ?>


            <!-- <div class="gui-accounts-container">
                <div class="gui-account <?php if(($cate) == "1"): ?>slected<?php endif; ?>" style="padding-top: 0;height: 50px;line-height: 50px;" data-cate="1">
                    <span>币币账户</span>
                </div>
                <div class="gui-account <?php if(($cate) == "2"): ?>slected<?php endif; ?>" style="padding-top: 0;height: 50px;line-height: 50px;" data-cate="2">
                    <span>合约账户</span>
                </div>
            </div> -->
            <?php if($cate == 1): ?><!-- 币币账户 table列表 -->
			<div class="cnyin_record">
				<div class="f_body">
					<div class="f_body_main">
						<div class="f_tab_body">
							<div>
								<table class="f_table" id="investLog_content">
									<thead>
										<tr>
											<th><img src="/Upload/coin/<?php echo ($coin_list[$market_list[$market]['xnb']]['img']); ?>" alt="" style="margin-bottom: -5px; width: 22px;" />
												<select name="market-selectTest" id="market-selectTest">
													<?php if(is_array($market_list)): $i = 0; $__LIST__ = $market_list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i; if(($market) == $key): ?><option value="<?php echo ($vo['name']); ?>" selected="selected" title="<?php echo ($coin_list[$vo['xnb']]['title']); ?>(<?php echo (strtoupper($vo['xnb'])); ?>/<?php echo (strtoupper($vo['rmb'])); ?>)">
                                                                <?php echo ($coin_list[$vo['xnb']]['title']); ?>(<?php echo (strtoupper($vo['xnb'])); ?>/<?php echo (strtoupper($vo['rmb'])); ?>)
                                                            </option>
															<?php else: ?>
															<option value="<?php echo ($vo['name']); ?>" title="<?php echo ($coin_list[$vo['xnb']]['title']); ?>(<?php echo (strtoupper($vo['xnb'])); ?>/<?php echo (strtoupper($vo['rmb'])); ?>)">
                                                                <?php echo ($coin_list[$vo['xnb']]['title']); ?>(<?php echo (strtoupper($vo['xnb'])); ?>/<?php echo (strtoupper($vo['rmb'])); ?>)
                                                            </option><?php endif; endforeach; endif; else: echo "" ;endif; ?>
												</select></th>
											<th>成交时间</th>
											<th><select name="type-selectTest" id="type-selectTest">
													<option value="0" <?php if(($type) == "0"): ?>selected<?php endif; ?>>-全部-
													</option>
													<option value="1" <?php if(($type) == "1"): ?>selected<?php endif; ?>>买入
													</option>
													<option value="2" <?php if(($type) == "2"): ?>selected<?php endif; ?>>卖出
													</option>
												</select></th>
											<th>成交价格</th>
											<th>成交数量</th>
											<th>总金额</th>
											<th>手续费</th>
										</tr>
									</thead>
									<tbody>
										<!-- <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
												<td><?php echo ($coin_list[$market_list[$vo['market']]['xnb']]['title']); ?>
													(<?php echo (strtoupper($market_list[$vo['market']]['xnb'])); ?>/<?php echo (strtoupper($market_list[$vo['market']]['rmb'])); ?>)</td>

												<td><?php echo (date('m-d H:i:s',$vo["addtime"])); ?></td>
												<td>
													<?php if(($vo['userid']) == $vo['peerid']): ?><font class="buy">自买</font>
														<font class="sell">自卖</font>
														<?php else: ?>
														<?php if(($vo['userid'] == $userid) AND ($vo['type'] == 1)): ?><font class="buy">买入</font><?php endif; ?>
														<?php if(($vo['userid'] == $userid) AND ($vo['type'] == 2)): ?><font class="buy">买入</font><?php endif; ?>
														<?php if(($vo['peerid'] == $userid) AND ($vo['type'] == 1)): ?><font class="sell">卖出</font><?php endif; ?>
														<?php if(($vo['peerid'] == $userid) AND ($vo['type'] == 2)): ?><font class="sell">卖出</font><?php endif; endif; ?>
												</td>
												<td><?php echo (NumToStr($vo['price'])); ?></td>
												<td><?php echo (NumToStr($vo['num'])); ?></td>
												<td><?php echo (NumToStr($vo['mum'])); ?></td>
												<td><?php echo ($vo['fee_buy']); ?></td>
											</tr><?php endforeach; endif; else: echo "" ;endif; ?> -->
									</tbody>
								</table>
								<div class="pages"> </div>
							</div>
						</div>
					</div>
				</div>
            </div>
            <?php else: ?>
            <!-- 合约账户 table列表 -->
            <div class="cnyin_record f_body">
                    <table class="f_table" id="investLog_content">
                        <thead>

                            <tr>
                                <th width="">时间</th>
                                <th width="">合约</th>
                                <th width="">成交类型</th>
                                <th width="">方向</th>
                                <th width="">成交数量</th>
                                <th width="">成交价格</th>
                                <th width="">价值</th>
                                <th width="">佣金费率</th>
                                <th width="">已付费用</th>
                                <th width="">委托种类</th>
                                <th width="">委托数量</th>
                                <th width="">为成交数量</th>
                                <th width="">委托价格</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><tr>
                                <td width=""><?php echo ($vo["ctime"]); ?></td>
                                <td width=""><?php echo ($vo["pair"]); ?></td>
                                <td width=""><?php echo ($vo["trade_type"]); ?></td>
                                <td width=""><?php echo ($vo["bid_flag"]); ?></td>
                                <td width=""><?php echo ($vo["amount"]); ?></td>
                                <td width=""><?php echo ($vo["price"]); ?></td>
                                <td width=""><?php echo ($vo["value"]); ?>BTC</td>
                                <td width=""><?php echo ($vo["taker_fee_ratio"]); ?>%</td>
                                <td width="">0BTC</td>
                                <td width=""><?php echo ($vo["close_type"]); ?></td>
                                <td width=""><?php echo ($vo["trade_amount"]); ?></td>
                                <td width=""><?php echo ($vo["remaining"]); ?></td>
                                <td width=""><?php echo ($vo["trade_price"]); ?></td>
                            </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                        </tbody>
                    </table>
                    <div class="pages"><?php echo ($page); ?></div>
                </div><?php endif; ?>

		</div>
	</div>
</div>
<script>
    var index = true; // 首次渲染
    var pageCount = 0; // 分页总数
    var count = 0; // 数据总条数
    var current = 1; // 当前页码
    var style = 'default'; //分页显示模式

	$(function() {
        if('<?php echo ($cate); ?>' == 1) {
            init();
        }
	})

    function loading() { // 加载动画
        layer.load(1, {
			shade: [0.1,'#fff'] //0.1透明度的白色背景
		});
    }

	function init() { // 初始化数据
        loading();
        getDatas();
    }

    // 选择查询xnb
    $("#type-selectTest,#market-selectTest").change(function () {
        current = 1;
        loading();
        getDatas();
		// window.location = '/Finance/mycj/type/' + type + '/market/' + market + '.html';
	});

    // 定义获取数据方法
	function getDatas() {

        var data = {
            submit: {
                userid: <?php echo (session('userId')); ?>,
                market: $("#market-selectTest option:selected").val(),
                type: $("#type-selectTest option:selected").val(),
                page: current
            },
            title: $("#market-selectTest option:selected").attr('title')
        }

		$.ajax({
			url: '/finance/cjdata',
			dataType: 'json',
			type: 'GET',
			data: data.submit,
			success: function (res) {

				var html;

				for(var i=0;i< res.data.length;i++) {
					html += '<tr>'+
								'<td>'+ data.title +'</td>'+
								'<td>'+ timeStamp2String(res.data[i].addtime * 1000) +'</td>'+
                                '<td>'+ (res.data[i].type > 1 ? '<font class="sell">卖出</font>' : '<font class="buy">买入</font>') + '</td>'+ 
								'<td>'+ res.data[i].price +'</td>'+
								'<td>'+ res.data[i].num +'</td>'+
								'<td>'+ res.data[i].mum +'</td>'+
								'<td>'+ res.data[i].fee_buy +'</td>'+
							'</tr>'
				}
                $('#investLog_content').find('tbody').html(html);
                
                if(index) {
                    if(res.count_page > 1) {
                        pageCount = res.count_page;
                        count = res.count;

                        $('.pages').html(pageComponent());

                    } else {
                        $('.pages').html(res.count + '条记录 1/1 页');

                        
                    }
                }
				
                layer.closeAll("loading");

                index = false;
			},
			error: function(msg) {
				console.log('error',msg);
			}
		})
    }
    


    // 分页
    function pageComponent() {
            console.log('pageCount',pageCount)
            console.log('count',count)
            console.log('current',current)
            
            
            var page = '';
            if(index) {
                style = pageCount > 9 ? '2' : '1'
            }
        
            if(style == 1) { // 展示全部页码模式

                page =  count +'条记录 '+ current +'/'+ pageCount +' 页'+ 
                    '<a class="next" pageid='+ (parseInt(current) + 1) +'>下一页</a>'+  
                    forPage()  

            } else if(style == 2) { // 含有。。。省略模式

                page =  count +'条记录 '+ current +'/'+ pageCount +' 页'+ 
                        '<a pageid="1" >首页</a>' +
                        '<a class="next" pageid='+ (parseInt(current) + 1) +'>下一页</a>'+
                        forPage() +
                        '<a class="all">...</a>'+
                        '<a pageid='+ pageCount +'>最后一页</a>'

            }

            return  page;
    }

    // 分页页码循环
    function forPage() {
        
        var html = '';

        if(style == 1) {

            for(var i=1;i<=pageCount;i++) {
                    
                html += '<a class="'+ (current == i ? 'current': '') +'" pageid='+ i +'>'+ i +'</a>'
                    
            }
        } else if(style == 2){
            
            for(var i=1;i<=pageCount;i++) {
                if(i<=5) {
                    html += '<a class="'+ (current == i ? 'current': '') +'" pageid='+ i +'>'+ i +'</a>'
                }
            }   
        }
        
        
        return html;
    }

    // 分页DOM操作
    $('body').on('click','.pages a',function() {

        if(!$(this).hasClass('current') && !$(this).hasClass('all')){   

            current = $(this).attr('pageid');
            
            if(current <= pageCount) {
                loading();
                getDatas();
            }
            

            if(!$(this).hasClass('next')) {

                if(style == 2 && current > 5) {
                    style = 1;
                }
                 
                $('.pages').html(pageComponent());

            } else {
                style = 1;

                if(current <= pageCount) {
                    $('.pages').html(pageComponent());
                }
                
            }
            
        } else if($(this).hasClass('all')) { // 点击。。。 展示全部页码
            style = 1;
            $('.pages').html(pageComponent());
        }
    })




    // 日期格式化
    function timeStamp2String(time){
        var datetime = new Date();
        datetime.setTime(time);
        var year = datetime.getFullYear();
        var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
        var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
        var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();
        var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
        var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();
        return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;
    }


    // 选择账户类型 
    $('.gui-accounts-container').on('click','.gui-account',function() {
         var cate = $(this).data('cate');
         window.location.href = "/finance/mycj.html?cate=" + cate;
     })
	
</script>

<script>
	//菜单高亮
	$('#finance_box').addClass('active');
	$('#finance_mycj').addClass('active');
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