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
<div class="wrapper">
	<div class="ui-block">
		<form action="">
			<div class="sb-form-title">
				<h1>创新项目投票上线申请( <a href="#sb">查看规则</a> )</h1>
				<p>请填写以下项目资料 （请务必提供最详细准确信息，<span style="color: #de5959;">获得审核批准后将有权力参与上币</span>	）</p>
				<p><a href="/Public/file/GTElistapplicationform.docx" style="font-size:10px;color: deepskyblue;">点击可下载文档</a></p>
			</div>
			<div class="ui-form ui-center sb-form">
				<div class="ui-form-field">
					<label for="projectname">联系邮件</label>
					<input class="ui-form-control" type="text" id="email" name="email" placeholder="联系邮件">
				</div>
				<div class="ui-form-field">
					<label for="projectname">项目名</label>
					<input class="ui-form-control" type="text" id="projectname" name="projectname" placeholder="请输入项目名">
				</div>
				<div class="ui-form-field">
					<label for="symbol">代币名</label>
					<input class="ui-form-control" type="text" id="symbol" name="symbol" placeholder="请输入代币名">
				</div>
				<div class="ui-form-field">
					<label for="website">官方网站</label>
					<input class="ui-form-control" type="text" id="website" name="website" placeholder="请输入官方网站">
				</div>
				<div class="ui-form-field">
					<label for="whitepaperlink">白皮书链接</label>
					<input class="ui-form-control" type="text" id="whitepaperlink" name="whitepaperlink" placeholder="请输入白皮书链接">
				</div>
				<div class="ui-form-field">
					<label for="projectinfo">项目介绍</label>
					<input class="ui-form-control" type="text" id="projectinfo" name="projectinfo" placeholder="请输入项目介绍">
				</div>
				<div class="ui-form-field">
					<label for="teaminfo">团队介绍</label>
					<input class="ui-form-control" type="text" id="teaminfo" name="teaminfo" placeholder="请输入团队介绍">
				</div>
				<div class="ui-form-field">
					<label for="advisorinfo">顾问信息</label>
					<input class="ui-form-control" type="text" id="advisorinfo" name="advisorinfo" placeholder="请输入顾问信息">
				</div>
				<div class="ui-form-field">
					<label for="icostatus">ICO状态</label>
					<input class="ui-form-control" type="text" id="icostatus" name="icostatus" placeholder="请输入ICO状态">
				</div>
				<div class="ui-form-field">
					<label for="symboltotal">代币总量</label>
					<input class="ui-form-control" type="text" id="symboltotal" name="symboltotal" placeholder="请输入代币总量">
				</div>
				<div class="ui-form-field">
					<label for="dismechan">代币分配机制</label>
					<input class="ui-form-control" type="text" id="dismechan" name="dismechan" placeholder="请输入代币分配机制">
				</div>
				<div class="ui-form-field">
					<label for="symbolcisupply">流通代币量</label>
					<input class="ui-form-control" type="text" id="symbolcisupply" name="symbolcisupply" placeholder="请输入流通代币量">
				</div>
				<div class="ui-form-field">
					<label for="marketlistd">已上交易所</label>
					<input class="ui-form-control" type="text" id="marketlistd" name="marketlistd" placeholder="请输入已上交易所">
				</div>
				<div class="ui-form-field">
					<label for="tradevolume">24小时交易量</label>
					<input class="ui-form-control" type="text" id="tradevolume" name="tradevolume" placeholder="请输入24小时交易量">
				</div>
				<div class="ui-form-field">
					<label for="privatesaleinfo">私募信息</label>
					<input class="ui-form-control" type="text" id="privatesaleinfo" name="privatesaleinfo" placeholder="请输入私募信息">
				</div>
				<div class="ui-form-field">
					<label for="projectprogress">项目开发进度</label>
					<input class="ui-form-control" type="text" id="projectprogress" name="projectprogress" placeholder="请输入项目开发进度">
				</div>
				<div class="ui-form-field">
					<label for="numberusers">产品用户数量</label>
					<input class="ui-form-control" type="text" id="numberusers" name="numberusers" placeholder="请输入产品用户数量">
				</div>
				<div class="ui-form-field">
					<label for="squsernum">社区及其用户数量</label>
					<input class="ui-form-control" type="text" id="squsernum" name="squsernum" placeholder="请输入社区及其用户数量">
				</div>
				<div class="ui-form-field">
					<label for="codeopensource">源代码是否公开</label>
					<input class="ui-form-control" type="text" id="codeopensource" name="codeopensource" placeholder="请输入源代码是否公开">
				</div>
				<div class="ui-form-field">
					<label for="opensourcelink">公开地址</label>
					<input class="ui-form-control" type="text" id="opensourcelink" name="opensourcelink" placeholder="请输入公开地址">
				</div>
				<div class="ui-form-field">
					<button class="ui-button ui-button-block" type="button" onclick="sbSubmit()" >提交上币表单</button>
				</div>
			</div>
		</form>
		<div class="vt-con" id="sb">
			<h3><i class="warn-iocn">!</i>上币流程：</h3>
			<ol class="vote-warn-tip">
				<li>请将上列问题填好发送邮件到pr@coinx.pro（邮件格式：项目名GTE上币申请表），越详细越好</li>
				<li>我们会及时审阅，符合上币条件的我们将在5至10个工作日内回复</li>
				<li>如果根据提供的信息，评估后不能在GTE上币，将不会被通知</li>
				<li>如果您提交后没有收到通知，欢迎再一次通过申请表提供更加详细和更有说服力的数据和信息</li>							
			</ol>
		</div>
		<div class="vt-con">
			<h3><i class="warn-iocn">!</i>评估标准：</h3>
			<p>一般来说，我们将根据以下标准进行项目评估</p>
			<ol class="vote-warn-tip">
				<li>项目资质审核及政策风险（发行主体、法律合规性、公司背景）</li>
				<li>项目核心团队和投资机构（核心团队信息、投融资背景、团队人员分配）</li>
				<li>项目商业模式和市场规模（与区块链技术结合程度、用户规模及增长情况、存量用户）</li>
				<li>项目代币生态建设及整体分配（代币属性、分配机制、锁仓情况、代币应用、市值及交易情况）</li>	
				<li>项目社区及市场运营推广（社群数量、用户活跃度及反馈、运营类活动、媒体报道）</li>
				<li>项目产品进展及对外披露信息（项目路线图、开发情况、代码更新频次和开放程度）</li>							
			</ol>
		</div>
		<div class="vt-con" style="margin-bottom: 40px;">
			<h3><i class="warn-iocn">!</i>下线币种说明：</h3>
			<p>为保护投资者利益，GTE保留项目下线或继续支持项目在平台上交易的权利，项目方如果触发如下条件，我们会公告通知交易下线，包含但不限于：</p>
			<ol class="vote-warn-tip">
				<li>项目团队解散</li>
				<li>项目方面临重大法律问题</li>
				<li>由于战略调整和发展需要，项目运营团队主动要求下线</li>
				<li>严重的技术或安全问题没有及时得到解决</li>	
				<li>在连续30个交易日，每日交易额小于2万美金</li>
				<li>不满足继续交易的其他事项</li>							
			</ol>
			<p>GTE会对决定下线的项目提前5天发出下线公告，用户有30天的期限从钱包中移出资产</p>
		</div>
	</div>
</div>
<script type="text/javascript">
	function sbSubmit(){
		var data = {}
		var $inputs = $(".sb-form input");
		var error = [];
		for(var i = 0; i < $inputs.length; i++){
			var $input = $($inputs[i]);
			var key = $input.attr("name");
			var value = $input.val();
			if(value == "" || value == null){
				var label = $("label[for='" + key +"']").text();
				error.push(label);
			}
			data[key] = value;
		}
		if(error.length > 0){
			layer.msg("请输入:" + error.join(','), {icon:2});
			return false;
		}
		$.ajax({
			url : "/Vote/add",
			type : "post",
			data : data,
			success : function(res){
				if(res.code == 0){
					layer.msg(res.msg, {icon:1});
					//提交成功清空输入框内容
					$inputs.val("");
				}else{
					layer.msg(res.msg, {icon:2});
				}
			}
		})
	}
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