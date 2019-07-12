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

<div class="autobox mt20 clear middle-content-box" id="Kline-change">
    <div class="current-data-box">
        <!-- <div class="base-coin-tabs">
            <span class="active" id="USDT">USDT</span>
            <span id="BTC">BTC</span>
            <span id="ETH">ETH</span>
            <span id="QTUM">QTUM</span>
            <span id="1">限时</span>
            <span id="2">自选</span>
        </div> -->
        <div class="coins-list-box">
            <div class="coins-header">
                <span>币种</span>
                <span>价格(USDT)</span>
                <span>日涨跌</span>
            </div>
            <ul class="g-scrollbar" id="scrollbox"> </ul>
        </div>
    </div>
    <div class="trade-conatiner-box">
        <!-- 实时价格 -->
        <div class="market-title">
            <div class="masket-price">
                <span style="background-image:url()">
                  <a href="">BTC</a>
                </span>
                <span>$0.0000</span>
                <span>0.00%</span>
                <span style="color: #666;font-size: 12px;">成交量：00000</span>
            </div>
            <div class="masket-notice">

            </div>
        </div>
        <!--行情图-->
        <div id="kline">
            <div id="paint_chart" style="height: 400px">
                <iframe style="border-style: none;" border="0" width="100%" height="400" id="market_chart"
                        src="/Trade/ordinary?market=<?php echo ($market); ?>"></iframe>
                <!-- <iframe style="border-style: none;" border="0" width="100%" height="400" id="market_chart"
                src="/Contract/chart?<?php echo ($market); ?>"></iframe> -->
            </div>
        </div>
        <!--行情图结束-->
        <div class="fast_tr clear">
            <a name="mark-trade"></a>
            <form class="ft_box" id="form-buy">
                <dl>
                    <dd>
                        <p>最佳买价：</p>
                        <span class="orange" id="buy_best_price" onclick="setMaiRu();" style="cursor:pointer;font-size: 20px;margin-right: 3px;">--</span>
                        <?php echo (strtoupper($rmb)); ?>/<?php echo (strtoupper($xnb)); ?>
                    </dd>
                    <dd>
                        <p>最大可买：</p>
                        <span class="col_333" id="buy_max" title="满仓(全买)，设置买入数量为最大值">--</span>
                        <?php echo (strtoupper($xnb)); ?>
                    </dd>
                    <dd>
                        <p>买入价格：</p>
                        <input type="text" id="buy_price" name="price" maxlength="10" placeholder="此出价为1个币的价格" value="" style="color: #333;" value="<?php echo (strtoupper($rmb)); ?>"/>
                    </dd>
                    <dd>
                        <p>买入数量：</p>
                        <input type="text" id="buy_num" maxlength="10" name="num">
                    </dd>
                    <dd>
                        <p>总价(<?php echo (strtoupper($rmb)); ?>)：</p>
                        <input type="text" id="buy_mum" readonly>
                        <!-- <span class="col_333" id="buy_mum">-</span> <?php echo (strtoupper($rmb)); ?> -->
                    </dd>
                    
                    <!-- <dd class="pwdtrade">
                        <p>交易密码：</p>
                        <input id="buy_paypassword" name="pwtrade" type="password"> 
                    </dd> -->
                    <dd>
                        <p>手续费：</p>0.3%(成交才收，撤销退回)
                    </dd>
                </dl>
                <div>
                    <div class="trader_btn">
                        <div class="tan_btn" id="tm-buy"></div>
                        <input type="button" value="买入" onclick="tradeadd_buy();">
                    </div>
                </div>
            </form>
            <form class="ft_box nobr" id="form-sell">
                <dl>
                    <dd>
                        <p>最佳卖价：</p>
                        <span class="orange" id="sell_best_price"  onclick="setMaiChu();"  style="color: #008069!important;cursor:pointer;font-size: 20px;margin-right: 3px;">--</span><?php echo (strtoupper($rmb)); ?>/<?php echo (strtoupper($xnb)); ?>

                    </dd>
                    <dd>
                        <p>最大可卖：</p>
                        <span id="sell_max" class="col_333">--</span> <?php echo (strtoupper($xnb)); ?>
                    </dd>
                    <dd>
                        <p>卖出价格：</p>
                        <input type="text" id="sell_price" maxlength="10" name="price" placeholder="此出价为1个币的价格" style="color: #333;"/>
                    </dd>
                    
                    <dd>
                        <p>卖出数量：</p>
                        <input type="text" id="sell_num" maxlength="10" name="num">
                    </dd>
                    <dd>
                        <p>总价(<?php echo (strtoupper($rmb)); ?>)：</p>
                        <input type="text" id="sell_mum" readonly>
                        <!-- <span class="col_333" id="sell_mum">--</span> <?php echo (strtoupper($rmb)); ?> -->
                    </dd>
                    
                    <!-- <dd class="pwdtrade">
                        <p>交易密码：</p>
                        <input id="sell_paypassword" name="pwtrade" type="password"> 
                    </dd> -->
                    <dd>
                        <p>手续费：</p>0.3%(成交才收，撤销退回)
                    </dd>
                </dl>
                <div>
                    <div class="trader_btn">
                        <div class="tan_btn" id="tm-sell"></div>
                        <input class="bg_green" type="button" value="卖出" onclick="tradeadd_sell();">
                    </div>
                </div>
            </form>
        </div>
        <div class="trade-info-box">
            <!-- 委托 -->
            <div class="clear over_auto  account_table autobox mt20" style="margin-top: 20px;margin: 0;width: 100%;min-width: auto;float: left;border: 1px solid #efefef;margin-bottom: 20px;">
                <div id="entrust_over" class=" over_auto" style="margin-bottom: 10px;">
                    <div class="TitleBox">
                        <h3 class="PlateTitle">我的委托</h3>
                    </div>
                    <div class="over_auto">
                        <table class="Transaction no_border_right no_border_left_right">
                            <thead>
                            <tr>
                                <th width="20%">时间</th>
                                <th width="10%">买/卖</th>
                                <th width="15">价格</th>
                                <th width="15">数量</th>
                                <th width="10">已成交</th>
                                <th width="10">总额</th>
                                <th >操作</th>
                            </tr>
                            </thead>
                            <tbody id="entrustlist" class="no-border-left-right">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <!-- 成交记录 -->
            <div class=" autobox" style="border: 1px solid #efefef; margin-top: 20px;margin: 0;width: 100%;min-width: auto;float: left;">
                <div class="clear">
                    <div class="TitleBox">
                        <h3 class="PlateTitle">最新成交记录(全站)</h3>
                    </div>
                    <div class=" over_hidden gui-trade-over-container">
                        <div class="over_auto gui-trade-over-list">
                            <table class="Transaction  no_border_right no_border_left_right">
                                <thead>
                                <tr>
                                    <th width="30%">时间</th>
                                    <th width="10%">买/卖</th>
                                    <th width="20%">成交价</th>
                                   
                                    <th width="20%">成交量</th>
                                    <th><span style="padding_right: 18px;">总额</span></th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="over_auto g-scrollbar" style="max-height: 375px; overflow-x: hidden; overflow-y: auto;">
                            <table class="Transaction  no_border_right no_border_left_right">
                                <tbody id="orderlist" class="no-border-left-right">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="" style="margin-left: 20px;position: absolute;right: 20px;top: 40px;width: 340px;">
        <div class="global-price-box">
            <h4>全球行情</h4>
            <div class="global-markets">
                <div class="global-head-tabs">
                    <span>交易所</span>
                    <span>交易对</span>
                    <span>交易价</span>
                    <span>成交量(ETH)</span>
                </div>
                <ul class="g-scrollbar"></ul>
            </div>
            <!-- <p>数据来源<a href="https://block.cc/q/ETH?exchange=gate-io" target="_blank">block.cc</a></p> -->
        </div>
        <div class="zcxx" style="margin-bottom: 20px;background-color: #fff;padding: 10px;">
            <div class="right_table user-account-info-box">
                <h4 class="zcxx-title">账户信息</h4>
                <table style="width: 100%" class="trade-right-user-info">
                    <tbody>
                    <tr>
                        <th>可用<?php echo (strtoupper($xnb)); ?></th>
                        <td><span id="my_xnb">0</span></td>
                    </tr>
                    <tr>
                        <th>冻结<?php echo (strtoupper($xnb)); ?></th>
                        <td><font id="my_xnbd">0</font></td>
                    </tr>
                    <tr>
                        <th>可用<?php echo (strtoupper($rmb)); ?></th>
                        <td><span id="my_rmb">0</span></td>
                    </tr>
                    <tr>
                        <th>冻结<?php echo (strtoupper($rmb)); ?></th>
                        <td><font id="my_rmbd">0</font></td>
                    </tr>
                    <tr>
                        <th>账户总资产</th>
                        <td><font id="user_finance">0</font></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tradeBox">
            <div class="slideHd">
                <!-- 下面是前/后按钮代码，如果不需要删除即可 -->
                <ul class="active">
                    <li id="trade_moshi_1" class="trade_moshi on"><a href="javascript:void(0);" onclick="moshi(1)">
                        默认模式 </a></li>
                    <?php if(($C['trade_moshi']) == "1"): ?><li id="trade_moshi_2" class="trade_moshi"><a href="javascript:void(0);" onclick="moshi(2)">
                            聊天模式 </a></li><?php endif; ?>
                    <li id="trade_moshi_3" class="trade_moshi"><a href="javascript:void(0);" onclick="moshi(3)">
                        只看买入 </a></li>
                    <li id="trade_moshi_4" class="trade_moshi"><a href="javascript:void(0);" onclick="moshi(4)">
                        只看卖出 </a></li>
                </ul>
            </div>
        </div>
        <div class="right" style="display: none;" id="trade_moshi_liaotian_1">
            <div class="coinBox buyonesellone">
                <div class="coinBoxBody buybtcbody2">
                    <div id="marqueebox1" class="">
                        <ul id="chat_content">
                        </ul>
                    </div>
                    <div id="marqueebox2">
                        <ul class="clearfix">
                            <li id="face" class="left"><a href="javascript:void(0);" class="face faceBtn" id="face1">
                                <img src="/Public/Home/images/face.gif" width="20">
                            </a></li>
                            <li id="msg" class="left"><input type="text" name="msg" class="text" id="chat_text"
                                                             style="width: 288px;"></li>
                            <li id="send" class="right"><input class="tijiao" type="button" value="发送"
                                                               onclick="upChat()"></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="entrust" style="max-height: 685px;" id="trade_moshi_liaotian_2">
            <div class="entrust_list">
                <ul>
                    <li class="first" style="width:20%">买/卖</li>
                    <li class="w85" style="width: 30%">价格</li>
                    <li class="w64" style="width: 20%">数量</li>
                    <li class="w62" style="width: 30%">总额</li>
                </ul>
                <div class="el_dl g-scrollbar" id="selllist"></div>
                <div class="el_dl g-scrollbar" id="buylist" style="border-bottom: 1px dotted #fff;"></div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

    var market = "<?php echo ($market); ?>"; // 交易对
    var symbol_market = '<?php echo ($market); ?>'.split('_')[0].toUpperCase();// 币种 如 btc
    var symbol_base = '<?php echo ($market); ?>'.split('_')[1].toUpperCase(); // 基础货币 usdt 

    var num_round = "<?php echo ($num_round); ?>"; // 数量小数点位数
    var price_round = "<?php echo ($price_round); ?>"; // 价格小数点位数

    var userid = "<?php echo (session('userId')); ?>";
    var trade_moshi = 1;
    var getDepth_tlme = null;
    var trans_lock = 0;
    var coinsData = null;
    var coins_symbol = 'ETH,EOS,GSE,SKM,ONT,BTC,NKN,AE,LYM,BTM,GTC,THETA,QTUM,RATING,IHT,TSL,ETC,TRX,ZIL,OCN,ADA,NAS,LRC,RUFF,BCH,XRP,BOT,BTS,HSR,IOTX,DDD,VEN,DOGE,GXS,TIO,JNT,BCD,EOSDAC,MDT,XMR,LRN,NEO,DOCK,SENC,SMT,MAN,MED,ABT';
    var buy_price_focus = false;
    var sell_price_focus = false;

    // 初始化
    $(function () {

        // 添加全球行情板块
        globalDatas('ETH');
        // 获取左侧交易对列表数据
        getCoinsData();

        // 获取全站交易记录
        getTradelog();
        // 获取交易深度数据
        getDepth();

        if (userid > 0) {
            getEntrustAndUsercoin();
        } else {
            
            $('#entrust_over').hide();
        }
    });

    var ws = new WebSocket("wss://wss.gte.io:3346");

    ws.onopen = function() {
        console.log('已连接');

        // 获取交易深度数据
        ws.send('spot:depth:'+ market +':5');
        // 获取全站交易记录
        ws.send('spot:trade:'+ market +':5');
        // 获取左侧交易对列表数据
        ws.send('spot:list:'+ market +':5');
    }

    ws.onmessage = function(res) {
        res = JSON.parse(res.data);
        if(res.depth) {
            var data = res;
            var list = '';
            var sellk = data['depth']['sell'].length;
            if (data['depth']['sell']) {
                for (i = 0; i < data['depth']['sell'].length; i++) {
                    list += '<dl title="以这个价格买入" style="cursor: pointer;" onclick="autotrust(this,\'sell\',1)">'+
                                '<dt class="sell"  style="width: 20%;">卖' + (sellk - i) + '</dt>'+
                                '<dd style="width: 30%">' + data['depth']['sell'][i][0] + '</dd>'+
                                '<dd style="width: 20%">' + data['depth']['sell'][i][1] + '</dd>'+
                                '<dd style="width: 30%">' + (data['depth']['sell'][i][0] * data['depth']['sell'][i][1]).toFixed(6) + '</dd>'+
                            '</dl>';
                }
                var len = data.depth.sell.length;
                var $buy_num = parseFloat($('#buy_num').val());

                $('#buy_best_price').html('$ ' + parseFloat(data.depth.sell[len -1][0]).toFixed(price_round) * 1); // 最佳买入价格

                if(!buy_price_focus && !$buy_num) {
                    $('#buy_price').val(parseFloat(data.depth.sell[len -1][0]).toFixed(price_round) * 1); // 最佳买入价格
                }

            }

            $("#selllist").html(list);

            list = '';
            if (data['depth']['buy']) {
                for (i = 0; i < data['depth']['buy'].length; i++) {
                    list += '<dl title="以这个价格卖出" style="cursor: pointer;" onclick="autotrust(this,\'buy\',1)">'+
                                '<dt class="buy"  style="width: 20%;">买' + (i + 1) + '</dt>'+
                                '<dd style="width: 30%">' + data['depth']['buy'][i][0] + '</dd>'+
                                '<dd style="width: 20%">' + data['depth']['buy'][i][1] + '</dd>'+
                                '<dd style="width: 30%">' + (data['depth']['buy'][i][0] * data['depth']['buy'][i][1]).toFixed(6) + '</dd>'+
                            '</dl>';
                }
                var $sell_num = parseFloat($('#sell_num').val());

                $('#sell_best_price').html('$ ' + parseFloat(data.depth.buy[0][0]).toFixed(price_round) * 1); // 最佳卖出价格

                if(!sell_price_focus && !$sell_num) {
                    $('#sell_price').val(parseFloat(data.depth.buy[0][0]).toFixed(price_round) * 1); // 最佳买入价格
                }

            }
            $("#buylist").html(list);

        } else if(res.tradelog) {

            var data = res;
            var list = '';
            var type = '';
            var typename = '';
            for (var i in data['tradelog']) {
                if (data['tradelog'][i]['type'] == 1) {
                    list += '<tr title="以这个价格卖出" onclick="autotrust(this,\'buy\',2)">'+
                                '<td width="30%">' + data['tradelog'][i]['addtime'] + '</td>'+
                                '<td class="buy"   width="10%">买</td>'+
                                '<td width="20%">' + data['tradelog'][i]['price'] + '</td>'+
                                '<td width="20%">' + data['tradelog'][i]['num'] + '</td>'+
                                '<td >' + data['tradelog'][i]['mum'] + '</td>'+
                            '</tr>';
                } else {
                    list += '<tr title="以这个价格买入" onclick="autotrust(this,\'sell\',2)">'+
                                '<td width="30%">' + data['tradelog'][i]['addtime'] + '</td>'+
                                '<td class="sell"   width="10%">卖</td>'+
                                '<td width="20%">' + data['tradelog'][i]['price'] + '</td>'+
                                '<td width="20%">' + data['tradelog'][i]['num'] + '</td>'+
                                '<td >' + data['tradelog'][i]['mum'] + '</td>'+
                            '</tr>';
                }
            }
            $("#orderlist").html(list);
        } else if(res.list) {
            var data = res.list;
            var html = '';
            var market_title_html = '';
            var color = '';
            var coin_icon = '';

            // data[data.length -1] = {id: "95", name: "yhet_usdt", title: "YHET<span>Yhetgame</span>",img: "5c10cbff92521.png", new_price: 0.04, change: 0.01,volume: 56822 };

            for (var item in data) {

                color = data[item].change > 0 ? 'color-red' : 'color-green';
                coin_icon = data[item].img;
                if(data[item].name == market) {
                    market_title_html = '<span style="background-image:url(/Upload/coin/'+coin_icon+')">' +
                                            '<a href="/Trade/info/market/'+ market +'">'+ symbol_market +'</a>' +
                                        '</span>'+
                                        '<span>$'+ data[item].new_price +'</span>'+
                                        '<span class=" ' + color +'">'+ (data[item].change) + '%' +'</span>'+
                                        '<span style="color: #666;font-size: 12px;">成交量：'+ data[item].volume +'</span>'
                }

                html += '<li>'+
                            '<a href="' + '/trade/index/market/'+ data[item].name +'/' + '">'+
                                '<span style="background-image:url(/Upload/coin/'+coin_icon+')"><b>'+ data[item].title + '</b>' + '</span>' +
                                '<span class=" ' + color +'">'+ data[item].new_price +'</span>' +
                                '<span class=" ' + color +'">'+ (data[item].change) + '%' +'</span>' +
                            '</a>'+
                        '</li>'
            }
            $('.masket-price').html(market_title_html);

            $('.coins-list-box ul').html(html);
            
           var el = document.getElementById('scrollbox');
           if(el.scrollHeight > el.clientHeight) {
               $('.coins-header').css('padding-right','10px');
           }
            
        }
    }

    ws.onclose = function() {

        console.log('已关闭');
        ws = null;

    }
    
    
    // 判断买入价格是否获取过焦点
    $('#buy_price').focus(function() {
        buy_price_focus = true;
    })
    // 判断卖出价格是否获取过焦点
    $('#sell_price').focus(function() {
        sell_price_focus = true;
    })


    // 添加全球行情板块
    function globalDatas(symbol) {
        $.ajax({
            url: '/Ajax/globals_market',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if(data.length) {
                    var arry = data;
                    var html = '';
                    for(var i= 0;i < arry.length; i++) {
                        html += '<li class="clearfix">'+
                                    '<span class="ex-name">' + arry[i].market + '</span>' +
                                    '<span class="ex-pair">'+ arry[i].symbol_pair.replace('_','/') + '</span>' +
                                    '<span class="ex-price">' + (arry[i].last * arry[i].usd_rate).toFixed(2) + '</span>' +
                                    '<span class="ex-amount">'+ (arry[i].vol / 10000).toFixed(1) + '万' + '</span>' + 
                                '</li>'
                    }
                    $('.global-markets ul').html(html);

                }
            },
            error: function(msg) {
                console.log(msg);
            }
        })
    }
    

    // 获取左侧交易对列表数据
    var getCoinsData = function() {

        $.ajax({
            url: '/Ajax/allcoin?t='+ Math.random(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var html = '';
                var market_title_html = '';
                var color = '';
                var coin_icon = '';

                // data[data.length -1] = {id: "95", name: "yhet_usdt", title: "YHET<span>Yhetgame</span>",img: "5c10cbff92521.png", new_price: 0.04, change: 0.01,volume: 56822 };
                for (var item in data) {

                    color = data[item][7] > 0 ? 'color-red' : 'color-green';
                    coin_icon = data[item][9];
                    if(data[item][8] == market) {
                        market_title_html = '<span style="background-image:url(/Upload/coin/'+coin_icon+')">' +
                                                '<a href="/Trade/info/market/'+ market +'">'+ symbol_market +'</a>' +
                                            '</span>'+
                                            '<span>$'+ data[item][1] +'</span>'+
                                            '<span class=" ' + color +'">'+ (data[item][7]) + '%' +'</span>'+
                                            '<span style="color: #666;font-size: 12px;">成交量：'+ data[item][4] +'</span>'
                    }

                    html += '<li>'+
                                '<a href="' + '/trade/index/market/'+ data[item][8] +'/' + '">'+
                                    '<span style="background-image:url(/Upload/coin/'+coin_icon+')"><b>'+ data[item][0] + '</b>' + '</span>' +
                                    '<span class=" ' + color +'">'+ data[item][1] +'</span>' +
                                    '<span class=" ' + color +'">'+ (data[item][7]) + '%' +'</span>' +
                                '</a>'+
                            '</li>'
                }
                $('.masket-price').html(market_title_html);

                $('.coins-list-box ul').html(html);

                var el = document.getElementById('scrollbox');
                if(el.scrollHeight > el.clientHeight) {
                    $('.coins-header').css('padding-right','10px');
                }
            },
            error: function(msg) {
                console.log(msg);
            }
        })
        // t = setTimeout(getCoinsData,5000);
    }
   
    //撤销
    function cancelaa(id) {
        $.post("<?php echo U('Trade/chexiao');?>", {id: id}, function (data) {
            if (data.status == 1) {
                getEntrustAndUsercoin();
                layer.msg(data.info, {icon: 1});
                getAccountInfo();
            } else {
                layer.msg(data.info, {icon: 2});
            }
            
        });
    }

    // 获取全站交易记录
    function getTradelog() {
        $.getJSON("/Ajax/getTradelog?market=" + market + "&t=" + Math.random(), function (data) {
            if (data) {
                if (data['tradelog']) {
                    var list = '';
                    var type = '';
                    var typename = '';
                    for (var i in data['tradelog']) {
                        if (data['tradelog'][i]['type'] == 1) {
                            list += '<tr title="以这个价格卖出" onclick="autotrust(this,\'buy\',2)">'+
                                        '<td width="30%">' + data['tradelog'][i]['addtime'] + '</td>'+
                                        '<td class="buy"   width="10%">买</td>'+
                                        '<td width="20%">' + data['tradelog'][i]['price'] + '</td>'+
                                        '<td width="20%">' + data['tradelog'][i]['num'] + '</td>'+
                                        '<td >' + data['tradelog'][i]['mum'] + '</td>'+
                                    '</tr>';
                        } else {
                            list += '<tr title="以这个价格买入" onclick="autotrust(this,\'sell\',2)">'+
                                        '<td width="30%">' + data['tradelog'][i]['addtime'] + '</td>'+
                                        '<td class="sell"   width="10%">卖</td>'+
                                        '<td width="20%">' + data['tradelog'][i]['price'] + '</td>'+
                                        '<td width="20%">' + data['tradelog'][i]['num'] + '</td>'+
                                        '<td >' + data['tradelog'][i]['mum'] + '</td>'+
                                    '</tr>';
                        }
                    }
                    $("#orderlist").html(list);
                }
            }
        });
        // setTimeout('getTradelog()', 5000);
    }

    // 获取交易深度数据
    function getDepth() {
        if (trade_moshi != 2) {

            $.getJSON("/Ajax/getDepth?market=" + market + "&trade_moshi=" + trade_moshi + "&t=" + Math.random(), function (data) {
                if (data) {

                    if (data['depth']) {
                        var list = '';
                        var sellk = data['depth']['sell'].length;
                        if (data['depth']['sell']) {
                            for (i = 0; i < data['depth']['sell'].length; i++) {
                                list += '<dl title="以这个价格买入" style="cursor: pointer;" onclick="autotrust(this,\'sell\',1)">'+
                                            '<dt class="sell"  style="width: 20%;">卖' + (sellk - i) + '</dt>'+
                                            '<dd style="width: 30%">' + data['depth']['sell'][i][0] + '</dd>'+
                                            '<dd style="width: 20%">' + data['depth']['sell'][i][1] + '</dd>'+
                                            '<dd style="width: 30%">' + (data['depth']['sell'][i][0] * data['depth']['sell'][i][1]).toFixed(6) + '</dd>'+
                                        '</dl>';
                            }
                            var len = data.depth.sell.length;
                            var $buy_num = parseFloat($('#buy_num').val());

                            $('#buy_best_price').html('$ ' + parseFloat(data.depth.sell[len -1][0]).toFixed(price_round) * 1); // 最佳买入价格

                            if(!buy_price_focus && !$buy_num) {
                                $('#buy_price').val(parseFloat(data.depth.sell[len -1][0]).toFixed(price_round) * 1); // 最佳买入价格
                            }

                        }

                        $("#selllist").html(list);

                        list = '';
                        if (data['depth']['buy']) {
                            for (i = 0; i < data['depth']['buy'].length; i++) {
                                list += '<dl title="以这个价格卖出" style="cursor: pointer;" onclick="autotrust(this,\'buy\',1)">'+
                                            '<dt class="buy"  style="width: 20%;">买' + (i + 1) + '</dt>'+
                                            '<dd style="width: 30%">' + data['depth']['buy'][i][0] + '</dd>'+
                                            '<dd style="width: 20%">' + data['depth']['buy'][i][1] + '</dd>'+
                                            '<dd style="width: 30%">' + (data['depth']['buy'][i][0] * data['depth']['buy'][i][1]).toFixed(6) + '</dd>'+
                                        '</dl>';
                            }
                            var $sell_num = parseFloat($('#sell_num').val());

                            $('#sell_best_price').html('$ ' + parseFloat(data.depth.buy[0][0]).toFixed(price_round) * 1); // 最佳卖出价格

                            if(!sell_price_focus && !$sell_num) {
                                $('#sell_price').val(parseFloat(data.depth.buy[0][0]).toFixed(price_round) * 1); // 最佳买入价格
                            }

                        }

                        
                        $("#buylist").html(list);
                    }

                }
            });
            // clearInterval(getDepth_tlme);

            // var wait = second = 5;
            // getDepth_tlme = setInterval(function () {
            //     wait--;
            //     if (wait < 0) {
            //         clearInterval(getDepth_tlme);
            //         getDepth();
            //         wait = second;
            //     }
            // }, 1000);
        }
    }

    // 获取委托订单列表
    function getEntrustAndUsercoin() {
        $.getJSON("/Ajax/getEntrustAndUsercoin?market=" + market + "&t=" + Math.random(), function (data) {
            if (data) {
                if (data['entrust']) {
                    $('#entrust_over').show();
                    var list = '';
                    var cont = data['entrust'].length;
                    for (i = 0; i < data['entrust'].length; i++) {
                        if (data['entrust'][i]['type'] == 1) {
                            list += '<tr title="以这个价格卖出" onclick="autotrust(this,\'buy\',2)">'+
                                        '<td width="20%">' + data['entrust'][i]['addtime'] + '</td>'+
                                        '<td class="buy" width="10%">买</td>'+
                                        '<td width="15%">' + data['entrust'][i]['price'] + '</td>'+
                                        '<td width="15%">' + data['entrust'][i]['num'] + '</td>'+
                                        '<td width="10%">' + data['entrust'][i]['deal'] + '</td>'+
                                        '<td width="10%">' + (data['entrust'][i]['price'] * data['entrust'][i]['num']).toFixed(6) + '</td>'+
                                        '<td><a style="color: #2674FF;" class="cancelaa" id="' + data['entrust'][i]['id'] + '" onclick="cancelaa(\'' + data['entrust'][i]['id'] + '\')" href="javascript:void(0);">撤销</a></td>'+
                                    '</tr>';
                        } else {
                            list += '<tr title="以这个价格买入" onclick="autotrust(this,\'sell\',2)">'+
                                        '<td width="20%">' + data['entrust'][i]['addtime'] + '</td>'+
                                        '<td class="sell" width="10%">卖</td>'+
                                        '<td width="15%">' + data['entrust'][i]['price'] + '</td>'+
                                        '<td width="15%">' + data['entrust'][i]['num'] + '</td>'+
                                        '<td width="10%">' + data['entrust'][i]['deal'] + '</td>'+
                                        '<td width="10%">' + (data['entrust'][i]['price'] * data['entrust'][i]['num']).toFixed(6) + '</td>'+
                                        '<td><a style="color: #2674FF;" class="cancelaa" id="' + data['entrust'][i]['id'] + '" onclick="cancelaa(\'' + data['entrust'][i]['id'] + '\')" href="javascript:void(0);">撤销</a></td>'+
                                    '</tr>';
                        }
                    }
                    if (cont == 10) {
                        list += '<tr><td style="text_align:center;" colspan="7"><a href="/Finance/mywt" style="color: #2674FF;">更多委托信息</a>&nbsp;&nbsp;</td></tr>';
                    }
                    $('#entrustlist').html(list);
                } else {
                    $('#entrust_over').hide();
                }

                if (data['usercoin']) {
                    if (data['usercoin']['cny']) {
                        $("#my_rmb").html(data['usercoin']['cny']);
                        
                    } else {
                        $("#my_rmb").html('0.00');
                    }

                    if (data['usercoin']['cnyd']) {
                        $("#my_rmbd").html(data['usercoin']['cnyd']);
                    } else {
                        $("#my_rmbd").html('0.00');
                    }

                    if (data['usercoin']['xnb']) {
                        $("#my_xnb").html(data['usercoin']['xnb']);
                    } else {
                        $("#my_xnb").html('0.00');
                    }

                    if (data['usercoin']['xnbd']) {
                        $("#my_xnbd").html(data['usercoin']['xnbd']);
                    } else {
                        $("#my_xnbd").html('0.00');
                    }
                    // 最大可买、卖
                    setMax();
                }

            }
        });


        $.getJSON("/Ajax/allfinance?t=" + Math.random(), function (data) {

            $('#user_finance').html('¥ ' + data);
        });
        setTimeout('getEntrustAndUsercoin()', 5000);
    }
    function setMax () {
        var buyprice = parseFloat($('#buy_price').val());
        var myrmb = $("#my_rmb").html();
        var myxnb = $("#my_xnb").html();
        var buykenum = 0;
        var sellkenum = 0;

        if (myrmb > 0) {
            buykenum = myrmb / buyprice;
        }
        if (myxnb > 0) {
            sellkenum = myxnb;
        }
        if (buykenum != null && buykenum > 0 && buykenum != 'Infinity') {
            $('#buy_max').html(sliceString(buykenum, num_round));
        }
        if (sellkenum != null && sellkenum > 0 && sellkenum != 'Infinity') {
            $('#sell_max').html(sellkenum);
        }
    }

    function tradeadd_buy() {
        if (trans_lock) {
            layer.msg('不要重复提交', {icon: 2});
            return;
        }
        trans_lock = 1;

        var price = parseFloat($('#buy_price').val());
        var num = parseFloat($('#buy-num').val());
        // var paypassword = $('#buy_paypassword').val();
        if (price == "" || price == null) {
            layer.tips('请输入内容', '#buy_price', {tips: 3});
            return false;
        }
        if (num == "" || num == null) {
            layer.tips('请输入内容', '#buy_num', {tips: 3});
            return false;
        }

        //加载层-风格3
        layer.load(2);


        //此处演示关闭
        setTimeout(function () {
            layer.closeAll('loading');
            trans_lock = 0;
        }, 10000);
        $.post("<?php echo U('Trade/upTrade');?>", {
            price: $('#buy_price').val(),
            num: $('#buy_num').val(),
            // paypassword: $('#buy_paypassword').val(),
            market: market,
            type: 1
        }, function (data) {
            layer.closeAll('loading');
            trans_lock = 0;
            
            if (data.status == 1) {

                $("#buy_price").val('0');
                $("#buy_num").val('0');
                $("#buy_mum").val('0');
                $('#buy_paypassword').val('');

                $("#sell_price").val('0');
                $("#sell_num").val('0');
                $("#sell_mum").val('0');
                $('#sell_paypassword').val('');

                layer.msg(data.info, {icon: 1});
                getAccountInfo();
            } else {
                layer.msg(data.info, {icon: 2});
            }
            
        }, 'json');
    }

    function tradeadd_sell() {
        if (trans_lock) {
            layer.msg('不要重复提交', {icon: 2});
            return;
        }
        trans_lock = 1;
        var price = parseFloat($('#sell_price').val());
        var num = parseFloat($('#sell_num').val());
        // var paypassword = $('#sell_paypassword').val();
        if (price == "" || price == null) {
            layer.tips('请输入内容', '#sell_price', {tips: 3});
            return false;
        }
        if (num == "" || num == null) {
            layer.tips('请输入内容', '#sell_num', {tips: 3});
            return false;
        }
        layer.load(2);
        //此处演示关闭
        setTimeout(function () {
            layer.closeAll('loading');
            trans_lock = 0;
        }, 10000);


        $.post("<?php echo U('Trade/upTrade');?>", {
            price: $('#sell_price').val(),
            num: $('#sell_num').val(),
            // paypassword: $('#sell_paypassword').val(),
            market: market,
            type: 2
        }, function (data) {
            layer.closeAll('loading');
            trans_lock = 0;
            if (data.status == 1) {
                $("#buy_price").val('0');
                $("#buy_num").val('0');
                $("#buy_mum").val('0');
                $('#buy_paypassword').val('');

                $("#sell_price").val('0');
                $("#sell_num").val('0');
                $("#sell_mum").val('0');
                $('#sell_paypassword').val('');
                layer.msg(data.info, {icon: 1});
                getAccountInfo();
            } else {
                layer.msg(data.info, {icon: 2});
            }
            
        }, 'json');
    }

    function toNum(num, round) {
        return Math.round(num * Math.pow(10, round) - 1) / Math.pow(10, round);
    }

    function sliceString(value,num) { //根据币种限制输入框限时小数点位数

        var str = value.toString();
        
        var len =  str.split(".")[1].length - num;

        return str.slice(0,str.length - len);
    };
	
    // 自动填价格
    function autotrust(_this, type, cq) {

        if (type == 'sell') {
            $('#buy_price').val(parseFloat($(_this).children().eq(cq).html()).toFixed(price_round) * 1).css({'font_size': '14px'});
            if ($("#my_rmb").html() > 0) {
                $("#buy_max").html(sliceString(($("#my_rmb").html() / $('#buy_price').val()), num_round));
            }
            if ($('#buy_num').val()) {
                $("#buy_mum").val(($('#buy_num').val() * $('#buy_price').val()).toFixed(8) * 1 );
            }

        }
        if (type == 'buy') {
            $('#sell_price').val(parseFloat($(_this).children().eq(cq).html()).toFixed(price_round) * 1).css({'fontSize': '14px'});
            if ($("#my_xnb").html() > 0) {
                $("#sell_max").html($("#my_xnb").html());
            }
            if ($('#sell_num').val()) {
                $("#sell_mum").val(($('#sell_num').val() * $('#sell_price').val()).toFixed(8) * 1 );
            }
        }

    }
	
	function  setMaiChu(){ 
		$('#sell_price').val($.trim($('#sell_best_price').html().replace('$', '')));
        var buyprice = parseFloat($('#buy_price').val());
        var buynum = parseFloat($('#buy_num').val());
        var sellprice = parseFloat($('#sell_price').val());
        var sellnum = parseFloat($('#sell_num').val());
        var buymum = buyprice * buynum;
        var sellmum = sellprice * sellnum;
        var myrmb = $("#my_rmb").html();
        var myxnb = $("#my_xnb").html();
        var buykenum = 0;
        var sellkenum = 0;
        if (myrmb > 0) {
            buykenum = myrmb / buyprice;
        }
        if (myxnb > 0) {
            sellkenum = myxnb;
        }
		
        if (buyprice != null && buyprice.toString().split(".") != null && buyprice.toString().split(".")[1] != null) {
            if (buyprice.toString().split('.')[1].length > price_round) {
                $('#buy_price').val(buyprice.toFixed(price_round));
            }
        }
        if (buynum != null && buynum.toString().split(".") != null && buynum.toString().split(".")[1] != null) {
            if (buynum.toString().split('.')[1].length > num_round) {
                $('#buy_num').val(sliceString(buynum, num_round));
            }
        }
        if (sellprice != null && sellprice.toString().split(".") != null && sellprice.toString().split(".")[1] != null) {
            if (sellprice.toString().split('.')[1].length > price_round) {
                $('#sell_price').val(sellprice.toFixed(price_round));
            }
        }
        if (sellnum != null && sellnum.toString().split(".") != null && sellnum.toString().split(".")[1] != null) {
            if (sellnum.toString().split('.')[1].length > num_round) {
                $('#sell_num').val(sliceString(sellnum, num_round));
            }
        }
        if (buymum != null && buymum > 0) {
            $('#buy_mum').val(buymum.toFixed(8) * 1 );
        }
        if (sellmum != null && sellmum > 0) {
            $('#sell_mum').val(sellmum.toFixed(8) * 1 );
        }
        if (buykenum != null && buykenum > 0 && buykenum != 'Infinity') {
            $('#buy_max').html(sliceString(buykenum, num_round));
        }
        if (sellkenum != null && sellkenum > 0 && sellkenum != 'Infinity') {
            $('#sell_max').html(sellkenum);
        }
	
	}
	
	
	
	function setMaiRu(){
		$('#buy_price').val($.trim($('#buy_best_price').html().replace('$', '')));
        var buyprice = parseFloat($('#buy_price').val());
        var buynum = parseFloat($('#buy_num').val());
        var sellprice = parseFloat($('#sell_price').val());
        var sellnum = parseFloat($('#sell_num').val());
        var buymum = buyprice * buynum;
        var sellmum = sellprice * sellnum;
        var myrmb = $("#my_rmb").html();
        var myxnb = $("#my_xnb").html();
        var buykenum = 0;
        var sellkenum = 0;
        if (myrmb > 0) {
            buykenum = myrmb / buyprice;
        }
        if (myxnb > 0) {
            sellkenum = myxnb;
        }
		
        if (buyprice != null && buyprice.toString().split(".") != null && buyprice.toString().split(".")[1] != null) {
            if (buyprice.toString().split('.')[1].length > price_round) {
                $('#buy_price').val(buyprice.toFixed(price_round));
            }
        }
        if (buynum != null && buynum.toString().split(".") != null && buynum.toString().split(".")[1] != null) {
            if (buynum.toString().split('.')[1].length > num_round) {
                $('#buy_num').val(sliceString(buynum, num_round));
            }
        }
        if (sellprice != null && sellprice.toString().split(".") != null && sellprice.toString().split(".")[1] != null) {
            if (sellprice.toString().split('.')[1].length > price_round) {
                $('#sell_price').val(sliceString(sellprice,price_round));
                
            }
        }
        if (sellnum != null && sellnum.toString().split(".") != null && sellnum.toString().split(".")[1] != null) {
            if (sellnum.toString().split('.')[1].length > num_round) {
                $('#sell_num').val(sliceString(sellnum, num_round));
            }
        }
        if (buymum != null && buymum > 0) {
            $('#buy_mum').val(buymum.toFixed(8) * 1 );
        }
        if (sellmum != null && sellmum > 0) {
            $('#sell_mum').val(sellmum.toFixed(8) * 1 );
        }
        if (buykenum != null && buykenum > 0 && buykenum != 'Infinity') {
            $('#buy_max').html(sliceString(buykenum, num_round));
        }
        if (sellkenum != null && sellkenum > 0 && sellkenum != 'Infinity') {
            $('#sell_max').html(sellkenum);
        }
		
		
		
	}


	
    // 监听输入框数据变化
    $('#buy_price,#buy_num,#sell_price,#sell_num').css("ime-mode", "disabled").bind('keyup change', function () { 
        var buyprice = parseFloat($('#buy_price').val());
        var buynum = parseFloat($('#buy_num').val());
        var sellprice = parseFloat($('#sell_price').val());
        var sellnum = parseFloat($('#sell_num').val());
        var buymum = buyprice * buynum;
        var sellmum = sellprice * sellnum;
        var myrmb = $("#my_rmb").html();
        var myxnb = $("#my_xnb").html();
        var buykenum = 0;
        var sellkenum = 0;
        if (myrmb > 0) {
            buykenum = myrmb / buyprice;
        }
        if (myxnb > 0) {
            sellkenum = myxnb;
        }
		
        if (buyprice != null && buyprice.toString().split(".") != null && buyprice.toString().split(".")[1] != null) {

            if (buyprice.toString().split('.')[1].length > price_round) {

                 $('#buy_price').val(sliceString(buyprice,price_round));

            }
        }
        if (buynum != null && buynum.toString().split(".") != null && buynum.toString().split(".")[1] != null) {
            if (buynum.toString().split('.')[1].length > num_round) {

                $('#buy_num').val(sliceString(buynum,num_round));
            }
        }
        if (sellprice != null && sellprice.toString().split(".") != null && sellprice.toString().split(".")[1] != null) {
            if (sellprice.toString().split('.')[1].length > price_round) {
                $('#sell_price').val(sliceString(sellprice,price_round));
            }
        }
        if (sellnum != null && sellnum.toString().split(".") != null && sellnum.toString().split(".")[1] != null) {
            if (sellnum.toString().split('.')[1].length > num_round) {
                $('#sell_num').val(sliceString(sellnum, num_round));
            }
        }
        if (buymum != null && buymum >= 0) {
            $('#buy_mum').val(buymum.toFixed(8) * 1 );
        }
        if (sellmum != null && sellmum >= 0) {
            $('#sell_mum').val(sellmum.toFixed(8) * 1 );
        }
        if (buykenum != null && buykenum > 0 && buykenum != 'Infinity') {
            $('#buy_max').html(toNum(buykenum, num_round));
        }
        if (sellkenum != null && sellkenum > 0 && sellkenum != 'Infinity') {
            $('#sell_max').html(sellkenum);
        }
    }).bind("paste", function () {
        return false;
    }).bind("blur", function () {
        if (this.value.slice(-1) == ".") {
            this.value = this.value.slice(0, this.value.length - 1);
        }
    }).bind("keypress", function (e) {
        var code = (e.keyCode ? e.keyCode : e.which); //兼容火狐 IE
        if (this.value.indexOf(".") == -1) {
            return (code >= 48 && code <= 57) || (code == 46);
        } else {
            return code >= 48 && code <= 57
        }
    });

    // 深度列表模式切换
    function moshi(id) {
        trade_moshi = id;
        $('.trade_moshi').removeClass('on');
        $('#trade_moshi_' + id).addClass('on');
        if (id == 3) {
            $('#selllist').hide();
            $('#buylist').addClass('gui-only-see');
        } else {
            $('#selllist').show();
            $('#buylist').removeClass('gui-only-see'); 
        }

        if (id == 4) {
            $('#buylist').hide();
            $('#selllist').addClass('gui-only-see');
        } else {
            $('#buylist').show();
            $('#selllist').removeClass('gui-only-see');
        }

        if (id == 2) {
            $('#trade_moshi_liaotian_2').hide();
            $('#trade_moshi_liaotian_1').show();
            getChat();
        } else {
            $('#trade_moshi_liaotian_2').show();
            $('#trade_moshi_liaotian_1').hide();
            // getDepth();
        }
    }


</script>
<script type="text/javascript" src="/Public/Home/js/jquery.qqFace.js"></script>
<script type="text/javascript">
    //@他
    function atChat(_this) {
        $("#chat_text").val('@' + $(_this).html() + ' :');
    }
    // 回车提交聊天
    $("#chat_text").keyup(function (e) {
        if (e.keyCode === 13) {
            upChat();
            return false;
        }
    });
    // 提交聊天
    function upChat() {
        var content = $("#chat_text").val();
        if (content == "" || content == null) {
            layer.tips('请输入内容', '#chat_text', {tips: 3});
            return false;
        }
        $.post("/Ajax/upChat", {content: content}, function (data) {
            if (data.status == 1) {
                $("#chat_text").val('');
                getChat();
            } else {
                layer.tips(data.info, '#chat_text', {tips: 3});
                return false;
            }
        }, 'json');
    }
    //表情盒子的ID//给那个控件赋值//表情存放的路径
    $('#face1').qqFace({id: 'facebox1', assign: 'chat_text', path: '/Upload/face/'})

    $('#face1').css({
        'top': '1263px',
        'left': 'initial',
        'right': '35px'
    });
    function getChat() {
        if (trade_moshi == 2) {

            $.getJSON("/Ajax/getChat?t=" + Math.random(), function (data) {
                if (data) {


                    var list = '';
                    for (i = 0; i < data.length; i++) {
                        list += '<li><a href="javascript:void(0);" onclick="atChat(this)">' + data[i][1] + '</a>：' + data[i][2] + '</li>';
                    }
                    list = list.replace(/\[\/#([0-9]*)\]/g, '<img src="/Upload/face/$1.gif"  width="18">');


                    if ($("#chat_content").html() != list) {
                        $("#chat_content").html(list);
                        $("#marqueebox1").scrollTop(40000);
                    }


                }
            });
            setTimeout('getChat()', 5000);
        }
    }

</script>


<script type="text/javascript" src="/Public/Home/js/jquery-ui.js"></script>
<script type="text/javascript">
    $(function () {
        slider();
    });
    // 买入/卖出 比例
    function slider() {
        var type = ['sell', 'buy'];
        for (var i in type) {
            $("#slider_" + type[i]).slider({
                value: 0, min: 0, max: 100, step: 10, range: "min", slide: function (a, t) {
                    var type = $(t.handle).attr('data_slide');
                    var e = parseFloat($("#" + type + '_max').text());
                    if (isNaN(e)) e = 0;
                    $("#" + type + ' .ui-slider-handle').addClass('ui-state-focus ui-state-active');
                    $("#" + type + "_num").val((e / 100 * t.value).toFixed(num_round));
                    $("#ratio_num_" + type).text(t.value + "%");
                    if ($('#' + type + '_price').val()) {
                        $("#" + type + "_mum").html(((e / 100 * t.value * $('#' + type + '_price').val())).toFixed(2) * 1);
                    }
                }
            })
        }
    }
</script>
<script>
    //菜单高亮
    $('#list-tab_index').addClass('on');
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