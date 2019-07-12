<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>移动端交易所K线</title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <link rel="stylesheet" href="/Public/Home/css/mobile.css"/>
    <style>
        body {
            background: #1D1F2C;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            -khtml-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
		#main {
			width: 100%;
			height: 75%;
			margin: 0 auto;
		}
        .time-container {
            overflow: hidden;
            margin: 5px 0  0 5px;
        }
        .time-container li {
            padding: 2px 10px;
            float: left;
            color: #fff;
            font-size: 14px;
        }
       
        .time-container li.active {
            color: #fd5667;
            border-bottom: 1px solid #fd5667;
        }
        .header-box {
            overflow: hidden;
            height: 80px;
            padding: 5px 10px 0;
            box-sizing: content-box;
        }
        .header-box .item{
            /* float: left; */
            color: #999;
            box-sizing: content-box;
            overflow: hidden;
        }
        /* .header-box .item:nth-child(1) {
            width: 50%;
        }
        .header-box .item:nth-child(2) {
            width: 25%;
        }
        .header-box .item:nth-child(3) {
            width: 25%;
        } */
        .header-box .market-price {
            line-height: 32px;
            text-align: left;
            color: #fd5667;
            font-size: 26px;
        }
        .header-box .chang-info p{
            float: left;
            font-size: 14px;
            line-height: 20px;
        }
        .header-box .chang-info .item p:last-child{
            float: right;
            text-align: right;
        }
        .header-box .chang-info i {
            margin-left: 5px;
        }
        .header-box .chang-info span{ 
            color: #6e6e75;
            margin-right: 5px;
        }
        .color_red {
            color: #fd5667;
        }
        .color_green {
            color: #57e6a4
        }
        

	</style>
</head>
<body>
    <!-- 为ECharts准备一个具备大小（宽高）的Dom -->
    <div class="header-box">
        <!-- <div class="market-price">
            <p>$2567.3</p>
        </div>
        <div class="chang-info">
            <div class="item">
                <p>≈24568.00CNY <i class="color_green">-4.8%</i></p>
                <p><span>最高</span>25879.67</p>
            </div>
            <div class="item">
                <p><span>24H量</span>998.88</p>
                <p><span>最低</span>24013.86</p>
            </div>
        </div> -->
    </div>
    <ul class="time-container">
        <li data-time="1440">日线</li>
        <li data-time="360">6小时</li>
        <li data-time="60">1小时</li>
        <li data-time="30">30分钟</li>
        <li class="active" data-time="15">15分钟</li>
        <li data-time="5">5分钟</li>
    </ul>
    <div id="main"></div>
    

    <div class="gui-loading">
        <img src="/Public/Home/images/loading.gif" alt="加载中">
        <p>加载中...</p>
    </div>

    <!-- 引入 echarts.js -->
    <script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
    <script type="text/javascript" src="/Public/Home/js/echarts.min.js"></script>
    <script type="text/javascript">
        var myChart;
        var k_Datas;
        var sw = true;
        var time = '15';
        var market = queryparms(window.location.search).market;
        var path_type = queryparms(window.location.search).path_type;
        var timer = null;
        var upcolor = '#fe5363';
        var downcolor = '#57e6a4';
        var rate = 7.07;

        getMarketData()

		getTrendData();
        
        function queryparms(str) {
                var market,path_type;
            if(str.indexOf('&') > 0) {
                market = str.replace(/\?/,'').split('&')[0].split('=')[1];
                path_type = str.replace(/\?/,'').split('&')[1].split('=')[1];
            } else {
                market = str.replace(/\?/,'');
                path_type = 1;
            }
            return {
                market: market,
                path_type: path_type
            }
        }

        // 当前币种价格数据
        function getMarketData () {
            $.get("/Papi/Market/getMarket?market=" + market,function(res){
                var html;
                if(res.data) {
                    // html = '<div class="item market-price">'+
                    //         '<p>$'+ res.data.new_price +'</p>'+
                    //         '</div>'+
                    //         '<div class="item">'+
                    //             '<span>幅 '+ res.data.change +'%</span>'+
                    //             '<span>量 '+ res.data.volume +'</span>'+
                    //         '</div>'+
                    //         '<div class="item">'+
                    //             '<span>高 '+ res.data.max_price +'</span>'+
                    //             '<span>低 '+ res.data.min_price +'</span>'+
                    //         '</div>'


                    html =  '<div class="market-price">' +
                                '<p>$'+ res.data.new_price +'</p>'+
                            '</div>'+
                            '<div class="chang-info">' +
                                '<div class="item">' +
                                    '<p>≈'+ (res.data.new_price *  res.data.rate).toFixed(2) +'CNY <i class="'+ (res.data.change >= 0 ? 'color_red':'color_green') +'">'+ res.data.change +'%</i></p>' +
                                    '<p><span>最高</span>'+ res.data.max_price +'</p>'+
                                '</div>'+
                                '<div class="item">'+
                                    '<p><span>24H量</span>'+ res.data.volume +'</p>'+
                                    '<p><span>最低</span>'+ res.data.min_price +'</p>'+
                                '</div>'+
                            '</div>'
                }
                $('.header-box').html(html);
            },'json');
        }

		// 调用K线数据接口
        function getTrendData(){
            if(path_type == '1') {
                var path = "/Chart/getMarketOrdinaryJson?market="+ market +"&time="+ time + "&t="+(new Date().getTime());
            } else if(path_type == '2') {
                var path = "/Contract/getKline?market="+ market +"&time="+ time + "&t="+(new Date().getTime());
            }

	        $.get(path,function(res){

	        	if(res.length) {

                    $('.gui-loading').hide();

                    var kdatas = formatData(res);
                    if(sw) {
                        sw = false;
                        init(kdatas.data,kdatas.dates,kdatas.volumes);
                    } else {
                        window.option.xAxis[0].data = kdatas.dates;
                        window.option.xAxis[1].data = kdatas.dates;
                        window.option.series[0].data =  setVolumeColor(kdatas.volumes,kdatas.data);
                        window.option.series[1].data =  kdatas.data;
                        window.option.series[2].data =  calculateMA(5, kdatas.data);
                        window.option.series[3].data =  calculateMA(10, kdatas.data);
                        window.option.series[4].data = calculateMA(20, kdatas.data);

                        myChart.setOption(option);
                    }

                    // timer = setTimeout(function() {

                    //     getTrendData();

                    // },5000);
	        	}
	        },'json');

        }

       // K线数据处理
		function formatData (myDatas) {

            var dates = []; // 时间
            var data = [];
            var volumes = [];

			for(var i=0;i<myDatas.length;i++) {

				dates.push(timeStamp2String(myDatas[i][0] * 1000));

				data.push([myDatas[i][2],myDatas[i][5],myDatas[i][4],myDatas[i][3],myDatas[i][1]]);

				volumes.push(myDatas[i][1]);
			}
            return {
                dates: dates,
                data: data,
                volumes: volumes
            }
		}
        
        // 时间格式化处理
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

	    
		function init(data,dates,volumes) {

			var colorList = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];
			var labelFont = 'bold 12px Sans-serif';

			var dataMA5 = calculateMA(5, data);
			var dataMA10 = calculateMA(10, data);
			var dataMA20 = calculateMA(20, data);

			option = window.option = {
			    animation: false,
			    color: colorList,
			    backgroundColor: '#1D1F2C',
			    // title: {
			    //     left: 'center',
			    //     text: '移动端 K线图',
			    //     textStyle: {
			    //     	color: '#fff',
			    //     	fontSize: 30
			    //     },
			    // },
			    // legend: {
			    //     top: 30,
			    //     data: ['日K', 'MA5', 'MA10', 'MA20', 'MA30']
			    // },
			    tooltip: {
			    	axisPointer: {
			            type: 'cross',
			        },
			        trigger: 'axis',
			        formatter: function (params) {
						var res = params[0].name ;
						for (var i = 0; i < params.length; i++) {
							if (params[i].value instanceof Array) {
                                console.log(params[i])
								// res += '<br/>' + params[i].seriesName + '<br/>';
								res += '<br/>开盘 : ' + params[i].value[1] + '<br/>  最高 : ' + params[i].value[4] + '<br/>';
								res += '收盘 : ' + params[i].value[2] + '<br/>  最低 : ' + params[i].value[3];

							} else if(params[i].seriesName == 'Volume') {
								res += '<br/>成交量 : ' + params[i].value;
							} else {
                                res += '<br/>' + params[i].seriesName;
								res += ' : ' + params[i].value;
                            }
						}
						return res;
					},
			        // triggerOn: 'none',
			        transitionDuration: 0,
			        confine: true,
			        bordeRadius: 4,
			        borderWidth: 1,
			        borderColor: '#333',
			        backgroundColor: 'rgba(0,0,0,0.5)',
			       
			        textStyle: {
			            fontSize: 12,
			            color: '#999',
			        },
                    position: [10,0],
			        // position: function (pos, params, el, elRect, size) {
			        //     var obj = {
			        //         top: 0
			        //     };
			        //     obj[['left', 'right'][+(pos[0] < size.viewSize[0] / 2)]] = 5;
			        //     return obj;
			        // }
			    },
			    axisPointer: {
			        link: [{
			            xAxisIndex: [0,1]
			        }]
			    },
			    dataZoom: [{
			        type: 'slider',
			        show: false,
			        xAxisIndex: [0, 1],
			        realtime: false,
			        start: 100,
			        end: 65,
			        top: 65,
			        height: 20,
			        handleIcon: 'M10.7,11.9H9.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4h1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
			        handleSize: '120%'
			    }, {
			        type: 'inside',
			        xAxisIndex: [0, 1],
			        start: 40,
			        end: 70,
			        top: 30,
			        height: 20
			    }],
			    xAxis: [{
			        type: 'category',
			        data: dates,
			        boundaryGap : true,
			        splitLine: {
                                    show: false,
                                    lineStyle: {
                                        color: '#777',
                                        type: 'solid',
                                        opacity: 0.1
                                    }
                                },
			        axisLine: { lineStyle: { color: '#777' }},
			        axisLabel: {
			            formatter: function (value) {
			                return echarts.format.formatTime('hh:mm', value);
			            }
			        },
			        min: 'dataMin',
			        max: 'dataMax',
			        axisPointer: {
			            show: true,
                        label: {
                            show: false
                        },
                        lineStyle: {
                            type: 'dashed'
                        }
			        }
			    }, {
			        type: 'category',
			        gridIndex: 1,
			        data: dates,
			        scale: true,
			        boundaryGap : false,
			        splitLine: {show: false},
			        axisLabel: {show: false},
			        axisTick: {show: false},
			        axisLine: { lineStyle: { color: '#777' }},
			        splitNumber: 20,
			        min: 'dataMin',
			        max: 'dataMax',
                    axisPointer: {
			            show: true,
                        lineStyle: {
                            type: 'dashed'
                        }
			        }
			       
			    }],
			    yAxis: [{
			        scale: true,
			        // splitNumber: 2,
			        axisLine: { lineStyle: { color: '#777' } },
			        splitLine: {
                                    show: true,
                                    lineStyle: {
                                        color: '#777',
                                        type: 'solid',
                                        opacity: 0.1
                                    }
                                },
			        axisTick: { show: false },
			        axisLabel: {
			            inside: true,
			            formatter: '{value}\n'
			        }
			    }, {
			        scale: true,
			        gridIndex: 1,
			        splitNumber: 2,
			        axisLabel: {show: false},
			        axisLine: {show: false},
			        axisTick: {show: false},
			        splitLine: {show: false}
			    }],
			    grid: [{
			        left: 0,
			        right: 0,
			        top: 0,
			        height: '75%',
			    }, {
			        left: 0,
			        right: 0,
			        top: '80%',
			        height: '20%',
			    }],
			    graphic: [{
			        type: 'group',
			        left: 'center',
			        top: 70,
			        width: 300,
			        bounding: 'raw',
			        children: [{
			            id: 'MA5',
			            type: 'text',
			            style: {fill: colorList[1], font: labelFont},
			            left: 0
			        }, {
			            id: 'MA10',
			            type: 'text',
			            style: {fill: colorList[2], font: labelFont},
			            left: 'center'
			        }, {
			            id: 'MA20',
			            type: 'text',
			            style: {fill: colorList[3], font: labelFont},
			            right: 0
			        }]
			    }],
			    series: [{
			        name: 'Volume',
			        type: 'bar',
			        xAxisIndex: 1,
			        yAxisIndex: 1,
			        itemStyle: {
			            normal: {
			                color: '#7fbe9e',
			            },
			            // emphasis: {
			            //     color: '#140'
			            // }
			        },
			        data: setVolumeColor(volumes,data)
			    }, {
			        type: 'candlestick',
			        name: '日K',
			        data: data,
			        itemStyle: {
			            normal: {
			                color: upcolor,
			                color0: downcolor,
			                borderColor: upcolor,
			                borderColor0: downcolor
			            },
			            // emphasis: {
			            //     color: 'black',
			            //     color0: '#444',
			            //     borderColor: 'black',
			            //     borderColor0: '#444'
			            // }
			        }
			    }, {
			        name: 'MA5',
			        type: 'line',
			        data: dataMA5,
			        smooth: true,
			        showSymbol: false,
			        lineStyle: {
			            normal: {
			                width: 1
			            }
			        }
			    }, {
			        name: 'MA10',
			        type: 'line',
			        data: dataMA10,
			        smooth: true,
			        showSymbol: false,
			        lineStyle: {
			            normal: {
			                width: 1
			            }
			        }
			    }, {
			        name: 'MA20',
			        type: 'line',
			        data: dataMA20,
			        smooth: true,
			        showSymbol: false,
			        lineStyle: {
			            normal: {
			                width: 1
			            }
			        }
			    }]
			};

			// 基于准备好的dom，初始化echarts实例
        	myChart = echarts.init(document.getElementById('main'));


        	// 使用刚指定的配置项和数据显示图表。
        	myChart.setOption(option);
            
		}

		// 均线数据处理
		function calculateMA(dayCount, data) {
		    var result = [];
		    for (var i = 0, len = data.length; i < len; i++) {
		        if (i < dayCount) {
		            result.push('-');
		            continue;
		        }
		        var sum = 0;
		        for (var j = 0; j < dayCount; j++) {
		            sum += data[i - j][2];
		        }
		        result.push((sum / dayCount).toFixed(5) * 1);
		    }
		    return result;
		}

		
		// 交易量柱状图的颜色
		function setVolumeColor (volumes,data) {
			
			var newVolumes = new Array();
			var obj;
			var colors = getVolumeBarColor(data);

			for(var i= 0; i< volumes.length;i++) {
				obj = {
					value: volumes[i],
					itemStyle: {
						color: colors[i]
					}
				}

				newVolumes.push(obj)
			}

			return newVolumes;
		}


		function getVolumeBarColor(data) {

        	var colorArr = [];

        	for(var i=0;i < data.length;i++) {

        		if(data[i][0] - data[i][1] > 0) {

        			colorArr.push(downcolor);

        		} else {

        			colorArr.push(upcolor);
        		}
        	}

        	return colorArr;
		}


        $('.time-container').on('touchstart','li',function() {
            if(!$(this).hasClass('active')) {
                $(this).addClass('active').siblings().removeClass('active');
                time = $(this).data('time');
                
                // clearTimeout(timer);
                // timer = null;

                getTrendData();
            }
            
        })

        
        
    </script>
</body>
</html>