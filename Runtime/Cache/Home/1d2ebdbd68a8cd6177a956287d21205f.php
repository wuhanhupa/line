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
    <link rel="stylesheet" href="/Public/Home/css/style.css?v=<?php echo ($randVersion); ?>"/>
    <link rel="stylesheet" href="/Public/Home/css/ui.css?v=<?php echo ($randVersion); ?>"/>
    <link rel="stylesheet" href="/Public/Home/css/new_style.css?v=<?php echo ($randVersion); ?>"/>
    <link rel="stylesheet" href="/Public/Home/css/font-awesome.min.css"/>
    <script type="text/javascript" src="/Public/Home/js/script.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.flot.js"></script>
    <script type="text/javascript" src="/Public/Home/js/jquery.cookies.2.2.0.js"></script>
    <script type="text/javascript" src="/Public/layer/layer.js"></script>
    <script type="text/javascript" src="/Public/Home/js/vue.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/md5.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/echarts.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/k.js"></script>
</head>
<body class="trade-page">
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

                    <!--<li>
                        <a id="trade_box"><span>交易中心</span>
                            <img src="/Public/Home/images/down.png"></a>

                        <div class="deal_list " style="display: none;   top: 36px;">
                            <dl id="menu_list_json"></dl>
                            <div class="sj"></div>
                            <div class="nocontent"></div>
                        </div>
                    </li>-->

                    <li>
                        <a id="<?php echo ($daohang[4]['name']); ?>_box" class="active" href="/<?php echo ($daohang[4]['url']); ?>"><?php echo ($daohang[4]['title']); ?></a>
                    </li>

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
    $(function () {
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
<div id="app" style="height:100%;margin-top: 50px;">
    <app-component></app-component>
</div>
<script id="app-template" type="text/x-template">
    <div class="gui-trade-container">
        <div class="left gui-trade-left">
            <!-- <div class="base-coin-tabs">
                <span class="active" id="USDT">USDT</span>
            </div> -->
            <div class="coins-list-box">
                <div class="coins-header gui-coins-title">
                    <span>币种</span>
                    <span>价格(USDT)</span>
                    <span>日涨跌</span>
                </div>
                <ul class="g-scrollbar list-content">
                    <li v-for="item in coinListDatas">
                        <a :href="'/Contract/index?market=' + item.name">
                            <span :style="'background-image:url(/Upload/coin/'+ item.img +')'">
                                <b v-html="item.title"></b>
                            </span>
                            <span :class="item.change > 0 ? 'color-red': 'color-green' ">{{ item.new_price }}</span>
                            <span :class="item.change > 0 ? 'color-red': 'color-green' ">{{ item.change * 1 + '%' }}</span>
                    </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="right gui-trade-right">
            <div class="gui-trade-top">
                <div class="gui-data-box left">
                    <div>
                        <div class="market-title left">
                            <span :style="'background-image:url(/Upload/coin/'+ marketTitleData.img +')'">{{ symbolNum }}</span>
                            <span>${{ marketTitleData.new_price ? marketTitleData.new_price * 1 : '0.0000'  }}</span>
                            <span :class="marketTitleData.change > 0 ? 'color-red':'color-green'">{{ marketTitleData.change ? marketTitleData.change * 1 : '0.00'  }}%</span>
                        </div>
                        <div class="market-price-info left">
                            <span>24H最高 ${{ klineHeaderData.max_price }}</span>
                            <span>24H最低 ${{ klineHeaderData.min_price }}</span>
                            <span>标记价格 ${{ klineHeaderData.new_price }}</span>
                            <span>24H成交量 {{ klineHeaderData.volume }}</span>
                        </div>
                    </div>
                    <!-- k线 -->
                    <div class="kline-box clearfix" style="height:400px;width:100%;">
                        <div id="main" style="width:100%;"></div>
                        <ul class="time-container">
                            <li data-time="1440">日线</li>
                            <li data-time="360">6小时</li>
                            <li data-time="60">1小时</li>
                            <li data-time="30">30分钟</li>
                            <li data-time="15">15分钟</li>
                            <li class="active" data-time="5">5分钟</li>
                        </ul>
                        <div class="gui-loading">
                            <img src="/Public/Home/images/loading.gif" alt="加载中" width="30">
                            <p>加载中...</p>
                        </div>
                    </div>
                    <div class="gui-form-box">
                        <div class="input-form buy-form left">
                            <h4>限价</h4>
                            <ul>
                                <li>
                                    可用余额：{{ balanceValue }} <span>{{ symbolPrice }}</span>
                                    <!-- <a href="javascript:;" class="transfer-button">资金划转</a> -->
                                </li>
                                <li>
                                    <span>限价:</span>
                                    <input type="text" placeholder="0.00" v-model= "orderPrice" id="buyPrice">
                                    <i>{{ symbolPrice }}</i>
                                </li>
                                <li>
                                    <span>仓位:</span>
                                    <input type="text" placeholder="0" v-model="orderNum" id="buyNum">
                                    <i>{{ symbolPrice }}</i>
                                </li>
                                <li>
                                    <p>委托价值：{{ buyEntrust }} <span>{{ symbolNum }}</span></p>
                                    
                                </li>
                                <li>
                                    <p>合约张数：{{ MaxOrderAmount }} 张</p>
                                </li>
                            </ul>
                            <div class="sub-button-box">
                                <div class="buy-button sub-button left" @click="createdOrder(1,$event)">买入(做多)</div>
                                <div class="sell-button sub-button right" @click="createdOrder(0,$event)">卖出(做空)</div>
                            </div>
                        </div>
                        <div class="input-form position-info-box left">
                            <h4>持仓信息</h4>
                            <ul>
                                <li>
                                    <span>合约</span>
                                    <span>回报率</span>
                                </li>
                                <li>
                                    <span :class="position_info.bid_flag > 0 ? 'color-red': 'color-green'">{{ position_info.num ? position_info.num : '0' }}</span>
                                    <span :class="position_info.color >= 0 ? 'color-red': 'color-green'">{{ position_info.rate ? position_info.rate : '0' }}</span>
                                </li>
                                <li>
                                    <span>开仓价格</span>
                                    <span>强平价格</span>
                                </li>
                                <li>
                                    <span>{{ position_info.price ? position_info.price : '0'  }}</span>
                                    <span>{{ position_info.liquidation_price_plan ? position_info.liquidation_price_plan : '0' }}</span>
                                </li>
                            </ul>   
                            <div class="Multiple-box">
                                <span data-value="2" class="active pointer-2"><i>2倍</i></span>
                                <span data-value="3" class="pointer-3"><i>3倍</i></span>
                                <span data-value="5" class="pointer-5"><i>5倍</i></span>
                                <span data-value="10" class="pointer-10"><i>10倍</i></span>
                                <span data-value="25" class="pointer-25"><i>25倍</i></span>
                                <span data-value="50" class="pointer-50"><i>50倍</i></span>
                                <span data-value="100" class="pointer-100"><i>100倍</i></span>
                                <!-- <div class="edit-box"></div> -->
                            </div>
                            <!-- <div class="edit-input-box">
                                <span>杠杆倍率:</span>
                                <input type="text">
                                <i>倍</i>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="gui-data-table right">
                    <div class="table-box">
                        <h4>委托列表</h4>
                        <table class="Entrust-list">
                            <thead>
                                <tr>
                                    <th width="20%">买/卖</th>
                                    <th width="30%">价格(USDT)</th>
                                    <th width="30%">数量(USDT)</th>
                                    <!-- <th width="30%">总计(USDT)</th> -->
                                </tr>
                            </thead>
                            <tbody class="g-scrollbar" id="scrollBottom">
                                <tr v-for="(item,index) in selllistData">
                                    <td width="20%" class="sell">卖 {{ selllistData.length - index }}</td>
                                    <td width="30%"  @click="setBuyPrice(item.price)">{{  item.price }}</td>
                                    <td width="30%" @click="setBuyNum(item.num)">{{  item.num }}</td>
                                    <!-- <td width="30%">{{ (item[0] * item[1]).toFixed(6) * 1 }}</td> -->
                                </tr>
                            </tbody>
                        </table>
                        <hr>
                        <table class="Entrust-list">
                            <tbody class="g-scrollbar">
                                <tr v-for="(item,index) in buylistData">
                                    <td width="20%" class="buy">买 {{ index + 1 }}</td>
                                    <td width="30%" @click="setSellPrice(item.price)">{{  item.price }}</td>
                                    <td width="30%" @click="setSellNum(item.num)">{{  item.num }}</td>
                                    <!-- <td width="30%">{{ (item[0] * item[1]).toFixed(6) * 1 }}</td> -->
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="table-box">
                        <h4>最近成交</h4>
                        <table class="trade-log" id="orderlist">
                            <div style="padding-right:12px;">
                                <thead>
                                    <tr>
                                        <th width="18%">时间</th>
                                        <th width="16%">买/卖</th>
                                        <th width="18%">成交价</th>
                                        <th width="20%">成交量</th>
                                        <th width="28%">总额</th>
                                    </tr>
                                </thead>
                            </div>
                            
                            <tbody class="g-scrollbar">
                                <tr v-for="(item,index) in tradeLogData">
                                    <td width="18%">{{ item.time }}</td>
                                    <td width="16%" :class="item.type < 1 ? 'sell' : 'buy'">{{ item.type > 0 ? '买' : '卖' }}</td>
                                    <td width="18%">{{ parseFloat(item.price).toFixed(2) }}</td>
                                    <td width="20%">{{ item.num * 1 }}</td>
                                    <td width="28%">{{ parseFloat(item.total).toFixed(2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                
            </div>
            <div class="gui-trade-footer clearfix">
                <div class="gui-record-tabs">
                    <span class="active" data-name="table1">仓位</span>
                    <span data-name="table2">已平仓仓位</span>
                    <span data-name="table3">活动委托</span>
                    <span data-name="table4">已成交</span>
                    <span data-name="table5">委托历史</span>
                    <div class="type-notice-box right">
                        <i class="buy">买入</i>
                        <i class="sell">卖出</i>
                    </div>
                </div>
                <div class="gui-record-table">
                    <table class="position-table" data-name="table1" v-show="tableName == 'table1' ? true : false ">
                        <thead>
                            <tr>
                                <th width="9%">合约</th>
                                <th width="10%">目前仓位数量</th>
                                <th width="5%">价值</th>
                                <th width="8%">开仓价格</th>
                                <th width="8%">标记价格</th>
                                <th width="8%">强平价格</th>
                                <th width="12%">保证金</th>
                                <th width="15%">未实现盈亏(回报率%)</th>
                                <th width="10%">已实现盈亏</th>
                                <th width="15%">平仓</th>
                            </tr>
                        </thead>
                        <tbody class="g-scrollbar">
                            <tr v-for="item in position_list" :class="item.bid_flag > 0 ? 'buy-type': 'sell-type'">
                                <td width="9%" :class="item.bid_flag > 0 ? 'color-red': 'color-green'">{{ item.market }}</td>
                                <td width="10%" :class="item.bid_flag > 0 ? 'color-red': 'color-green'">{{ item.num }}</td>
                                <td width="5%">{{ item.value }}</td>
                                <td width="8%">{{ item.price}}</td>
                                <td width="8%">{{ item.now_price }}</td>
                                <td width="8%">{{ item.liquidation_price_plan }}</td>
                                <td width="12%"><span class="moneny-button" @click="bondBox(item.id,item.num,item.position_margin)"></span>{{ item.position_margin }}</td>
                                <td width="15%" :class="parseFloat(item.income.split(' ')[0]) >= 0 ? 'color-red': 'color-green'">{{ item.income }}({{ item.rate }})</td>
                                <td width="10%" :class="parseFloat(item.already_income.split(' ')[0]) >= 0 ? 'color-red': 'color-green'">{{ item.already_income }}</td>
                                <td width="15%"><input :id="'position-' + item.id" @focus="positionFocus" type="text" v-model="item.position_price"><span  @click="limitPositionBox(item.id,item.num,item.close)" :class="item.close > 0 ? 'disabled':''">平仓</span><i v-if="item.close > 0" class="cancle-position" @click="cancleContract(item.order_id)">X</i></td>
                            </tr>
                        </tbody>
                    </table>
    
                    <table class="already-positon" data-name="table2" v-show="tableName == 'table2' ? true : false ">
                        <thead>
                            <tr>
                                <th>合约</th>
                                <th>已实现盈亏</th>
                            </tr>
                        </thead>
                        <tbody class="g-scrollbar">
                            <tr v-for="item in been_liquidated">
                                <td width="50%">{{ item.market }}</td>
                                <td width="50%">{{ item.already_income }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="entrust-table" data-name="table3" v-show="tableName == 'table3' ? true : false ">
                        <thead>
                            <tr>
                                <th width="10%">合约</th>
                                <th width="10%">数量</th>
                                <th width="10%">委托价格</th>
                                <th width="10%">完全成交</th>
                                <th width="10%">剩余</th>
                                <th width="10%">委托价值</th>
                                <th width="10%">成交价格</th>
                                <th width="10%">类型</th>
                                <th width="10%">状态</th>
                                <th width="10%">时间</th>
                            </tr>
                        </thead>
                        <tbody class="g-scrollbar">
                            <tr v-for="item in order_list" :class="item.bid_flag > 0 ? 'buy-type': 'sell-type'">
                                <td width="10%">{{ item.market }}</td>
                                <td width="10%">
                                    <div>
                                        <!-- <i class="icon-edit" @click="eidtOption($event)"></i> -->
                                        {{ item.amount }}
                                    </div>
                                    <div class="edit-input-box">
                                        <b class="del-btn" @click="delOption($event)"></b>
                                        <input type="text" v-model="item.amount_1" v-on:keyup="inputChange($event)">
                                        <b class="ok-btn" @click="okOption($event,item.id)"></b>
                                    </div>
                                </td>
                                <td width="10%">
                                    <div>
                                        <!-- <i class="icon-edit" @click="eidtOption($event)"></i> -->
                                        {{ item.price }}
                                    </div>
                                    <div class="edit-input-box">
                                        <b class="del-btn" @click="delOption($event)"></b>
                                        <input type="text" v-model="item.price_1" v-on:keyup="inputChange($event)">
                                        <b class="ok-btn" @click="okOption($event,item.id)"></b>
                                    </div>
                                </td>
                                <td width="10%">---</td>
                                <td width="10%">{{ item.remaining }}</td>
                                <td width="10%">{{ item.value }}</td>
                                <td width="10%">---</td>
                                <td width="10%">{{ item.order_type }}</td>
                                <td width="10%">{{ item.status }}</td>
                                <td width="10%">{{ item.time }} <span @click="cancleContract(item.id,$event)">取消</span></td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="already-trade" data-name="table4" v-show="tableName == 'table4' ? true : false ">
                        <thead>
                            <tr>
                                <th width="10%">合约</th>
                                <th width="10%">数量</th>
                                <th width="10%">成交数量</th>
                                <th width="10%">剩余</th>
                                <th width="10%">成交价格</th>
                                <th width="10%">委托价格</th>
                                <th width="10%">价值</th>
                                <th width="5%">类型</th>
                                <th width="10%">委托ID</th>
                                <th width="15%">时间</th>
                            </tr>
                        </thead>
                        <tbody class="g-scrollbar">
                            <tr v-for="item in deal_list" :class="item.bid_flag > 0 ? 'buy-type': 'sell-type'">
                                <td width="10%">{{ item.market }}</td>
                                <td width="10%">{{ item.num }}</td>
                                <td width="10%">{{ item.deal_num }}</td>
                                <td width="10%">{{ item.remaining }}</td>
                                <td width="10%">{{ item.price }}</td>
                                <td width="10%">{{ item.trade_price }}</td>
                                <td width="10%">{{ item.value }}</td>
                                <td width="5%">{{ item.trade_type }}</td>
                                <td width="10%">{{ item.trade_id }}</td>
                                <td width="15%">{{ item.time }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <table class="entrust-histroy" data-name="table5" v-show="tableName == 'table5' ? true : false ">
                        <thead>
                            <tr>
                                <th width="10%">合约</th>
                                <th width="10%">数量</th>
                                <th width="10%">委托价格</th>
                                <th width="10%">完全成交</th>
                                <th width="10%">触发价格</th>
                                <th width="10%">成交价格</th>
                                <th width="10%">类型</th>
                                <th width="10%">状态</th>
                                <th width="20%">时间</th>
                            </tr>
                        </thead>
                        <tbody class="g-scrollbar">
                            <tr v-for="item in trade_history" :class="item.bid_flag > 0 ? 'buy-type': 'sell-type'">
                                <td width="10%">{{ item.market }}</td>
                                <td width="10%">{{ item.amount }}</td>
                                <td width="10%">{{item.price}}</td>
                                <td width="10%">----</td>
                                <td width="10%">----</td>
                                <td width="10%">----</td>
                                <td width="10%">{{ item.order_type }}</td>
                                <td width="10%">{{ item.status }}</td>
                                <td width="20%">{{ item.time }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div>
</script>

<script>
    var ws = new WebSocket("wss://wss.gte.io:3346");

    Vue.component('app-component', {
        template: '#app-template',
        data: function () {
            return {
                USERID: <?php echo (session('userId')); ?>,
                MaxOrderAmount: '0', // 最大可用合约张数
                marketTitleData: '',// k线头部币种数据
                market: '<?php echo ($market); ?>', // 交易对
                symbolNum: '<?php echo ($market); ?>'.split('_')[0].toUpperCase(), // 仓位单位
                symbolPrice: '<?php echo ($market); ?>'.split('_')[1].toUpperCase(), // 限价单位
                balanceValue: '0.0000', // 可用余额
                balanceMoney: '0.0000', // 资金划转余额
                selllistData: '', // 委托列表 卖单
                buylistData: '',  // 委托列表 买单
                tradeLogData: '', // 最近成交
                
                position_info: '',
                orderPrice: '',
                orderNum: '',

                buyNum: '', // 买入数量
                buyPrice: '', // 买入价格
                sellNum: '', // 卖出数量
                sellPrice: '', // 卖出价格,
                buyEntrust: '', // 买入委托价值，
                sellEntrust: '',  // 卖出委托价值,
                position_list: '', // 仓位列表数据
                been_liquidated: '', // 以平仓仓位数据
                order_list: '', // 活动委托列表数据
                deal_list: '', // 已成交列表数据,
                trade_history: '', // 委托历史数据
                tableName: 'table1', // 底部仓位列表导航栏
                t: null, // 定时器
                t1: null, // 定时器1
                t2: null, // 定时器2
                coinListDatas: '', // 币种列表数据
                klineHeaderData: '', 
                buyPrice_on: false,// input框焦点 默认没有获取焦点
                buyNum_on: false,
                sellPrice_on: false,
                sellNum_on: false, 
                liquidationOption_on: true, // 平仓按钮开关 限制连续点击
                loading: '', // loading动画弹出层
                ws_timer: null, // ws定时器

                k_time: 5,// k线默认时间

            }
        },
        created: function () {
            var vm = this;

            // 循环定时请求数据
            vm.loopFun();

            if(vm.USERID) {
                // 获取用户可用usdt
                vm.getAccountUsdt();
                // // 最大可用合约张数
                vm.getMaxOrderAmount(vm.market,2);

            }
            
        },
        watch: {

            orderNum: function(val,old) {
                var vm = this;
                vm.orderNum = val.replace(/^0/,"").replace(/[^\w_]/g,"").replace(/[a-zA-Z]/g,"");
            },
            orderPrice: function(val,old) {
                var vm = this;
                vm.orderPrice = vm.NumberCheck(val,2);
            },
            sellNum: function(val,old) {
                var vm = this;
                vm.sellNum = val.replace(/^0/,"").replace(/[^\w_]/g,"").replace(/[a-zA-Z]/g,"");
            },
            sellPrice: function(val,old) {
                var vm = this;
                vm.sellPrice = vm.NumberCheck(val,2);
            },
            balanceValue: function(va,old) {
                var vm = this;
                var leveraged = $('.Multiple-box').find('span.active').data('value');
                vm.getMaxOrderAmount(vm.market,leveraged);
            }
        },
        computed: {
            buyEntrust: function() {
                var vm = this;
                if(vm.orderNum > 0 && vm.orderPrice > 0) {
                    return  parseFloat(this.orderNum / this.orderPrice).toFixed(4) * 1;
                } else {
                    return '0.0000'
                }
            },
            sellEntrust: function() {
                var vm = this;
                if(vm.sellNum > 0 && vm.sellPrice > 0) {
                    return parseFloat(this.sellNum / this.sellPrice).toFixed(4) * 1;
                } else {
                    return '0.0000'
                }
            },
        },
        methods: {
            // 循环定时请求数据
            loopFun: function() {
                var vm = this;
                // clearTimeout(vm.t);
                // vm.t = null;
                
                // 获取交易对列表数据
                vm.getCoinsData();

                // 获取24H最高 最低价格
                vm.kline_head();

                // 获取深度数据
                vm.getDepth();
                
                // 获取最近成交记录
                vm.getRecentRecode();

                if(vm.USERID) {
                    // 获取仓位列表数据
                    vm.getPositionList();

                    // 活动委托列表数据
                    // vm.getEntrustData();
                }
                // vm.t = setTimeout(function() {
                //     vm.loopFun();
                // },5000);
            },
            // 获取24H最高 最低价格
            kline_head: function() {
                var vm = this;
                $.get('/Contract/kline_head?market='+vm.market, function(res) {

                    if(res.status == 1) {
                        vm.klineHeaderData = res.data;
                    }

                },'json')
            },

            // 获取仓位列表数据
            getPositionList: function(cb) {
                var vm = this;
                // vm.position_list = '';
                clearTimeout(vm.t2);
                vm.t2 = null;
                
                $.get('/Contract/position_list?market='+vm.market, function(res) {
                    layer.close(vm.loading);
                    if(res.status == 1) {
                        if(res.data && res.data.length) {
                            for(var i = 0;i<res.data.length;i++) {
                                res.data[i]['position_price'] = res.data[i].now_price;
                                res.data[i]['color'] = res.data[i].rate.replace(/\%/,'');
                            }
                            
                            
                        }
                        if(res.data && res.data.length || cb) { // 此处'cb'表示 创建订单时 仓位列表（不管有没有数据）需要定时请求接口
                            vm.t2 = setTimeout(function() {
                                vm.getPositionList(cb);
                            },5000)
                        }
                        
                        vm.position_list = res.data;
                        vm.position_info = res.data && res.data.length ? res.data[0] : '' ;
                    }
                },'json')
            },
            // 已平仓仓位列表数据
            getAlreadyPosition: function() {
                var vm = this;
                // vm.been_liquidated = '';
                $.get('/Contract/been_liquidated?market='+vm.market, function(res) {
                    layer.close(vm.loading);
                    if(res.status == 1) {
                        vm.been_liquidated = res.data;
                    }
                },'json')
            },
            // 活动委托列表数据
            getEntrustData: function() {
                var vm = this;
                // vm.order_list = '';
                clearTimeout(vm.t1);
                vm.t1 = null;

                $.get('/Contract/order_list?market='+vm.market, function(res) {
                    layer.close(vm.loading);
                    if(res.status == 1) {
                        if(res.data && res.data.length) {
                            for(var i = 0;i<res.data.length;i++) {
                                res.data[i]['amount_1'] = res.data[i].amount;
                                res.data[i]['price_1'] = res.data[i].price;
                            }

                            vm.t1 = setTimeout(function() {
                                vm.getEntrustData();
                            },5000);
                        }
                        vm.order_list = res.data;

                        
                    }
                },'json')

            },
            // 已成交列表数据
            getAlreadyTrade: function() {
                var vm = this;
                // vm.deal_list = '';
                $.get('/Contract/deal_list?market='+vm.market, function(res) {
                    layer.close(vm.loading);
                    if(res.status == 1) {
                        vm.deal_list = res.data;
                    }
                },'json')
            },
            // 委托历史列表数据
            getEntrustHistory: function() {
                var vm = this;
                // vm.trade_history = '';
                $.get('/Contract/trade_history?market='+vm.market, function(res) {
                    layer.close(vm.loading);
                    if(res.status == 1) {
                        vm.trade_history = res.data;
                    }
                },'json')
            },
            // 获取深度数据
            getDepth: function() {
                var vm = this;
                $.get('/Contract/getDepth?market='+vm.market, function(data) {
                    var data = data;
                    if(data.depth) {
                        // if(!vm.buyPrice_on && !vm.buyNum_on) {
                        //     var slen = data.depth.sell.length;
                        //     vm.buyPrice = data.depth.sell[slen -1][0];
                        // }
                        // if(!vm.sellPrice_on && !vm.sellNum_on) {
                        //     vm.sellPrice = data.depth.buy[0][0];
                        // }

                        vm.selllistData = data.depth.sell;
                        vm.buylistData = data.depth.buy;

                        // 委托列表中滚动条默认滚到底部
                        vm.fixScrollBarTobottom();
                    }
                },'json')
            },

            // 获取用户可用usdt
            getAccountUsdt: function() {
                var vm = this;
                $.get('/Contract/user_balance',function(res) {
                    
                    if(res.status == 1) {
                        vm.balanceValue = res.data.balance ? res.data.balance : '0.0000';
                        vm.balanceMoney = res.data.money ? res.data.money : '0.0000';
                    } 
                },'json')
            },
            // 获取最大合约张数
            getMaxOrderAmount: function(pair,leveraged) {
                var vm = this;
                $.get('/Contract/max_order_amount?pair='+ pair +'&leveraged='+ leveraged ,function(res) {
                    if(res.status == 1) {
                        vm.MaxOrderAmount = res.data.number ? parseFloat(res.data.number).toFixed(0) : '0';
                    } else {
                        layer.msg(res.info,{icon: 2});
                    }
                },'json')
            },
            // 调整杠杆接口
            changeLeveraged: function(pair,leveraged,callback) {
                var vm = this;
                $.get('/Contract/change_leveraged?pair='+ pair +'&leveraged='+ leveraged ,function(res) {
                    if(res.status == 1) {
                        layer.msg(res.info,{icon: 1});

                        callback();

                        vm.getAccountUsdt();
                    } else {
                        layer.msg(res.info,{icon: 2});
                    }
                },'json')
            },
             // 获取最近成交
             getRecentRecode: function() {
                var vm  = this;
                $.get('/Contract/recent_record?market='+vm.market,function(res) {
                    if(res.status == 1) {
                        vm.tradeLogData = res.data;
                    }
                },'json')
            },
            // 资金划转弹窗
            capitalTransfer: function () {   
                var vm = this;
                layer.open({
                type: 1,
                area: '575px',
                title: false,
                closeBtn: 2,
                shadeClose: false,
                skin: 'gui-mask-class',
                content: '<div class="gui-mask-contanier">'+
                            '<h4 class="gui-mask-title">资金划转</h4>'+
                            '<div class="form-box">'+
                                '<ul>'+
                                    '<li>'+
                                        '<span class="gui-input-title">币种</span>'+
                                        '<select name="select-symbol-box" id="select-symbol-box">'+
                                            '<option value="USDT">USDT</option>'+
                                        '</select>'+
                                    '</li>'+
                                    '<li class="transfer-guide">'+
                                        '<span class="gui-input-title">资金划转方向</span>'+
                                        '<p><span>币币账户</span><b>转至</b><span>合约账户</span></p>'+
                                    '</li>'+
                                    '<li>'+
                                        '<span class="gui-input-title">划转数量</span>'+
                                        '<div>'+
                                            '<input type="text" value="" placeholder="输入金额" />'+
                                            '<p>可转数量'+ vm.balanceMoney + vm.symbolPrice +'<i class="getAll">全部划转</i></p>'+
                                        '</div>'+
                                    '</li>'+
                                '</ul>'+
                                '<button type="button" class="sub-btn ui-button ui-button-block" onclick="submitCapitalData(this)">确定</button>'+
                            '</div>'+            
                        '</div>'
                });
            },
            // 保证金弹窗
            bondBox: function(id,num,position_margin) {
                var vm = this;

                layer.open({
                type: 1,
                area: '575px',
                title: false,
                closeBtn: 2,
                shadeClose: false,
                skin: 'gui-mask-class',
                content: '<div class="gui-mask-contanier center">'+
                            '<span class="gui-mask-logo" style=""></span>' + 
                            '<h4 class="gui-mask-header">增加/减少仓位保证金</h4>'+
                            '<div class="form-box-content">'+
                                '<div class="radios-box">'+
                                    '<span class="active" data-type="1">增加保证金</span>'+
                                    '<span data-type="2">减少保证金</span>'+
                                '</div>'+
                                '<p>你的当前仓位: '+ num +' 张合约</p>'+
                                '<p>当前分配的保证金: '+ position_margin + vm.symbolPrice +'</p>'+
                                '<p>可用保证金: '+ vm.balanceValue + vm.symbolPrice+'</p>'+
                            '</div>'+
                            '<div class="input-box">'+
                                '<input type="text" placeholder="0.00">'+
                                '<span>'+ vm.symbolPrice +'</span>'+
                            '</div>' +
                            '<button type="button" class="sub-btn ui-button ui-button-block" onclick="subBond('+ id +',this)">确定</button>'+            
                        '</div>'
                });
            },
            // 平仓弹窗
            limitPositionBox: function(id,num,close) {

                var price = $('#position-' + id ).val();
                if(close < 1) {
                    layer.open({
                    type: 1,
                    area: '575px',
                    title: false,
                    closeBtn: 2,
                    shadeClose: false,
                    skin: 'gui-mask-class',
                    content: '<div class="gui-mask-contanier center">'+
                                '<span class="gui-mask-logo" style="background-position:-388px 0;"></span>' + 
                                '<h4 class="gui-mask-header">现价平仓?</h4>'+
                                '<div class="form-box-content">'+
                                    '<p>买入在<span>'+ price +'</span>价格<span>'+ num +'</span>张 BTCUSDT合约。在执行时,将平掉您的整个仓位</p>'+
                                '</div>'+
                                '<button type="button" class="sub-btn ui-button ui-button-block" onclick="liquidationOption('+ id +','+ price +',this)">确定</button>'+            
                            '</div>'
                    });
                }
                
            },
            // 创建仓位买入、卖出订单
            createdOrder: function(type,event) {
                var vm = this;
                var el = $(event.currentTarget);
                var leveraged = $('.Multiple-box').find('span.active').data('value');
                var order_type = 1;
                var data = {
                    market: vm.market,
                    type: type,
                    num: vm.orderNum,
                    price: vm.orderPrice,
                    leveraged: leveraged,
                    order_type: order_type
                }
                if(!vm.USERID) {
                    layer.msg('请先登录',{icon: 2});
                    return;
                } else if(!data.num || data.num <= 0) {
                    layer.msg('请填写仓位',{icon: 2});
                    return;
                } else if(!data.price || data.price <=0) {
                    layer.msg('请填写限价',{icon: 2});
                    return;
                }

                layer.load(0, {
                    shade: [0.3,'#fafafa'] //0.1透明度的白色背景
                });

                $.ajax({
                    url:'/Contract/create_order',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(res) {

                        if(res.status == 1) {
                            layer.msg(res.info,{icon: 1},function() {

                                vm.orderNum = '';
                                vm.orderPrice = '';


                                layer.closeAll();

                                vm.getAccountUsdt();
                                vm.getPositionList(true); // 此处传入参数表示 创建订单时 仓位列表（不管有没有数据）需要定时请求接口
                                vm.getEntrustData();

                            });
                        } else{
                            layer.msg(res.info,{icon: 2},function() {
                                layer.closeAll();
                            });
                        }
                    },
                    error: function(msg) {
                        //console.log(msg);
                    }
                })
            },
            // 获取交易对列表数据
            getCoinsData: function() {
                var vm  = this;
                $.ajax({
                    url: '/Contract/coin_list',
                    type: 'GET',
                    dataType: 'json',
                    success: function (res) {
                        if(res.status == 1) {
                            if(res.data) {
                                for(var i= 0; i < res.data.length;i++) {
                                    if(res.data[i].name == vm.market) {
                                        vm.marketTitleData = res.data[i];
                                    }
                                }
                            }
                            
                            vm.coinListDatas = res.data;
                        }
                    },
                    error: function (msg) {
                        //console.log(msg);
                    }
                })
            },
            // 活动委托列表 委托取消
            cancleContract: function(id,event) {
                var vm = this;
                // var el = $(event.currentTarget);

                var data = {
                    order_id: id,
                }

                $.ajax({
                    url:'/Contract/cancel_order',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(res) {
                        if(res.status == 1) {
                            layer.msg(res.info,{icon:1});
                            // el.parents('tr').remove();

                            vm.getAccountUsdt();
                            vm.getPositionList();
                        } else {
                            layer.msg(res.info,{icon:2});
                        }

                    },
                    error: function(msg) {
                        //console.log(msg);
                    }
                })
            },
            // 活动委托列表 数据编辑
            eidtOption: function(event) {
                var vm = this;
                var el = $(event.currentTarget);
                el.parent().hide().siblings().show();
            },
            // 活动委托列表 数据编辑取消
            delOption: function(event) {
                var vm = this;
                var el = $(event.currentTarget);
                el.parent().hide().siblings().show();
            },
            // 活动委托列表 数据编辑确定
            okOption: function(event,id) {
                var vm = this;
                var el = $(event.currentTarget);
            },
            // input输出框输入字符限制
            NumberCheck: function(num,limit) {
                var str = num.toString();
                var len1 = str.substr(0, 1);
                var len2 = str.substr(1, 1);
                //如果第一位是0，第二位不是点，就用数字把点替换掉
                if (str.length > 1 && len1 == 0 && len2 != ".") {
                    str = str.substr(1, 1);
                }
                //第一位不能是.
                if (len1 == ".") {
                    str = "";
                }
                //限制只能输入一个小数点
                if (str.indexOf(".") != -1) {
                    var str_ = str.substr(str.indexOf(".") + 1);
                    if (str_.indexOf(".") != -1) {
                    str = str.substr(0, str.indexOf(".") + str_.indexOf(".") + 1);
                    }
                }
                //正则替换，保留数字和小数点
                str = str.replace(/[^\d^\.]+/g,'')
                //如果需要保留小数点后两位，则用下面公式
                // str = str.replace(/\.\d\d$/,'')
                if(limit > 0) {
                    str = str.replace(/^(\-)*(\d+)\.(\d\d).*$/,'$1$2.$3');
                }
                
                return str;
            },
            // 输入框限制
            inputChange: function(event) {
                var vm = this;
                var val = $(event.currentTarget).val();
                $(event.currentTarget).val(vm.NumberCheck(val));
            },
            // 设置买入价格
            setBuyPrice: function(price) {
                var vm = this;
                vm.orderPrice = price;
                vm.buyPrice_on = true;
            },
            // 设置买入仓位
            setBuyNum: function(num) {
                var vm = this;
                vm.orderNum = num;
                vm.buyNum_on = true;
            },
             // 设置卖出价格
            setSellPrice: function(price) {
                var vm = this;
                vm.orderPrice = price;
                vm.buyPrice_on = true;
            },
             // 设置卖出仓位
            setSellNum: function(num) {
                var vm = this;
                vm.orderNum = num;
                vm.buyNum_on = true;
            },
            // 平仓输入框焦点事件
            positionFocus: function() {
                var vm = this;
                clearTimeout(vm.t2);
                vm.t2 = null;
            },
            positionBlur: function() {
                var vm = this;
                vm.t2 = setTimeout(function() {
                    vm.getPositionList();
                },5000)
            },
            // ws 向服务器定时发送消息
            sendWsNews: function() { 
                var vm = this;
                clearTimeout(vm.ws_timer);
                vm.ws_timer = null;

                ws.send('contract:depth:'+ vm.market +':5:1');// 合约深度数据
                ws.send('contract:trade:'+ vm.market +':5:1');// 合约最近成交记录
                ws.send('contract:head:'+ vm.market +':5:1'); // 获取24H最高 最低价格
                ws.send('contract:list:'+ vm.market +':5:1'); // 获取交易对列表数据5'); // 获取交易对列表数据

                if(kline.kdatas.length > 0) {
                    var len = kline.kdatas.length;
                    ws.send('contract:kline:'+ vm.market +':'+ vm.k_time + ':'+ kline.kdatas[len-1][0]); // k线数据
                }

                vm.ws_timer = setTimeout(function(){
                    vm.sendWsNews();
                },5000);
            },
            // 委托列表滚动条默认底部
            fixScrollBarTobottom: function() {
                var table = document.getElementById('scrollBottom');
                table.scrollTop = table.scrollHeight;
            }


            
        },
        mounted: function () {
            var vm = this;

            // 窗口发生变化 k线画布重新渲染
            $(window).resize(function(){
                window.onresize = kline.myChart.resize;
            });

            // k线初始化
            kline.init({
                el: document.getElementById("main"),
                time: vm.k_time,
                market: vm.market
            })

            // 选择不同时间段按钮K线
            $('.time-container').on('click','li',function() {
                if(!$(this).hasClass('active')) {
                    $(this).addClass('active').siblings().removeClass('active');
                    vm.k_time = $(this).data('time');
                    
                    kline.getTrendData({
                        time: vm.k_time,
                        market: vm.market
                    })
                }
            })

            ws.onopen = function() {
                console.log('已连接')
                kline.ws_on = true;

                vm.sendWsNews();
            }
            ws.onmessage = function(res) {
                res = JSON.parse(res.data);
                if(res.depth) {
                    vm.selllistData = res.depth.sell;
                    vm.buylistData = res.depth.buy;

                    // 委托列表中滚动条默认滚到底部
                    vm.fixScrollBarTobottom();
                } else if(res.tradelog) {
                    vm.tradeLogData = res.tradelog;
                } else if(res.head) {
                    vm.klineHeaderData = res.head;
                } else if(res.list)  {
       
                        for(var i= 0; i < res.list.length;i++) {
                            if(res.list[i].name == vm.market) {
                                vm.marketTitleData = res.list[i];
                            }
                        }
                    vm.coinListDatas = res.list;

                } else if (res['kline'+ $('.time-container li.active').data('time')]) {

                    $('.gui-loading').hide();

                    var res = res['kline'+ $('.time-container li.active').data('time')];

                    if(res.length > 0) {

                        kline.kdatas.pop();

                        for(var i = 0;i<res.length;i++) {
                            kline.kdatas.push(res[i]);
                        }
                        var res = kline.kdatas;

                    } else {
                        var res = kline.kdatas;
                    }

                    var kdatas = kline.formatData(res);

                    kline.myChart.setOption( kline.echart_option(kdatas.data,kdatas.dates,kdatas.volumes));
                }
                
            }
            ws.onclose = function() {
                console.log('已关闭');
                ws = null;
            }
            
            

            // 创建仓位表单输入框焦点事件 

            $('#buyPrice').focus(function() {
                vm.buyPrice_on = true;
            })
            $('#buyPrice').blur(function() {
                vm.buyPrice_on = false;
            })
            $('#buyNum').focus(function() {
                vm.buyNum_on = true;
            })
            $('#buyNum').blur(function() {
                vm.buyNum_on = false;
            })

            $('#sellPrice').focus(function() {
                vm.sellPrice_on = true;
            })
            $('#sellPrice').blur(function() {
                vm.sellPrice_on = false;
            })
            $('#sellNum').focus(function() {
                vm.sellNum_on = true;
            })
            $('#sellNum').blur(function() {
                vm.sellNum_on = false;
            })

            // 仓位记录导航栏
            $('.gui-record-tabs').on('click','span',function() {
                if(!vm.USERID) {
                    layer.msg('请先登录',{icon:2});
                    return;
                }
                if(!$(this).hasClass('active')) {
                    $(this).addClass('active').siblings().removeClass('active');
                    vm.tableName = $(this).data('name');
                    
                    vm.loading = layer.load(0, {
                        shade: [0.3,'#fafafa'] //0.1透明度的白色背景
                    });

                    switch (vm.tableName) {
                        case 'table1':
                            // 仓位列表数据
                            vm.getPositionList();
                            clearTimeout(vm.t1);
                            vm.t1 = null;
                            break;
                        case 'table2':
                            // 已平仓仓位列表数据
                            vm.getAlreadyPosition();
                            break;
                        case 'table3':
                            // 活动委托列表数据 
                            vm.getEntrustData();
                            clearTimeout(vm.t2);
                            vm.t2 = null;
                            break;
                        case 'table4':
                            // 已成交列表数据 
                            vm.getAlreadyTrade();
                            break;
                        case 'table5':
                            // 委托历史数据
                            vm.getEntrustHistory();
                    }
                }
                

            })

            // 资金划转
            $('.transfer-button').on('click',function() {
                vm.capitalTransfer();
            })
            
            // 资金划转数据提交
            window.submitCapitalData = function(_this) {
                var _this = _this;
                var num = $(_this).parents('.form-box').find('input').val();
                var coin =  $(_this).parents('.form-box').find('option:selected').val();
                var data = {
                    coin: coin,
                    num: num,
                }
                $.ajax({
                    url:'/Contract/funds_transfer',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(res) {
                        if(res.status == 1) {
                            layer.msg(res.info,{icon:1},function() {
                                layer.closeAll();
                            });
                            vm.getAccountUsdt();
                        } else {

                            layer.msg(res.info,{icon:2});
                            
                        }
                        
                    },
                    error: function(msg) {
                        //console.log(msg);
                    }
                })
            }
            // 资金划转 全部划转
            $('body').on('click','.getAll',function() {
                $(this).parent().siblings('input').val(vm.balanceMoney);
            })


            // 合约倍率操作
            $('.Multiple-box').on('click','span',function() {
                
                if(!$(this).hasClass('active')) {
                    var id = $(this).data('value');
                    // $(this).addClass('active').siblings('span').removeClass('active');
                    $('.pointer-' + id).addClass('active').siblings('span').removeClass('active');
                    
                    if(vm.USERID) {
                        var leveraged = $('.pointer-' + id).data('value');

                        vm.getMaxOrderAmount(vm.market,leveraged);
                        if(vm.position_list.length > 0) {
                            vm.changeLeveraged(vm.market,leveraged,vm.getPositionList);
                        }   
                    }
                }
            })

            // 增加保证金按钮选择
            $('body').on('click','.radios-box span',function() {
                if(!$(this).hasClass('active')) {
                    $(this).addClass('active').siblings().removeClass('active');
                }
            })
            // 增加保证金数据提交
            window.subBond = function(id,_this) {
                var _this = _this;
                var el = $(_this);
                var type = el.parents('.gui-mask-contanier').find('.radios-box span.active').data('type');
                var add_margin = el.parents('.gui-mask-contanier').find('input').val();
                var data = {
                    type: type,
                    position_id: id,
                    add_margin: add_margin,
                    pair: vm.market
                }
                if(add_margin <= 0 || !add_margin) {
                    layer.msg('请填写保证金额',{icon:2});
                    return;
                }
                $.ajax({
                    url:'/Contract/add_pos_margin',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(res) {
                        if(res.status == 1) {
                            layer.msg(res.info,{icon:1},function() {
                                layer.closeAll();
                            });

                            vm.getPositionList();
                        } else {
                            layer.msg(res.info,{icon:2},function() {
                                layer.closeAll();
                            });
                        }
                        
                    },
                    error: function(msg) {
                        //console.log(msg);
                    }
                })

            }

            // 仓位列表 平仓操作 平仓按钮数据提交
            window.liquidationOption = function(id,price,_this) {
                if(!vm.liquidationOption_on) {
                    return false;
                } else {
                    vm.liquidationOption_on = false;
                }
                var _this = _this;
                var el = $(_this);
                var data = {
                    position_id: id,
                    price: price
                }
                $.ajax({
                    url:'/Contract/liquidation',
                    dataType: 'json',
                    type: 'POST',
                    data: data,
                    success: function(res) {
                        if(res.status == 1) {

                            layer.msg(res.info,{icon:1},function() {
                                layer.closeAll();
                                vm.liquidationOption_on = true;
                            });
                            vm.getPositionList();
                            vm.getAccountUsdt();
                        } else {
                            layer.msg(res.info,{icon:2},function() {
                                vm.liquidationOption_on = true;
                                layer.closeAll();
                            });
                        }
                        

                    },
                    error: function(msg) {
                        //console.log(msg);
                        vm.liquidationOption_on = true;
                    }
                })
                
            }
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