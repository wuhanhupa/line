(function(w,d){
    var kline = {
        options: {
            el: '',
            market: '',
            time: '5',
        },

        myChart: '', // echart 实例化对象
        sw: true, // 控制开关
        timer: null, // 接口定时器
        upcolor: '#d45858', // 上涨k线颜色
        downcolor: '#008069', // 下跌K线颜色
        kws: null, // ws实例化对象
        ws_on: false, // ws 是否开启
        kws_timer: null, // ws 定时器
        kdatas: '',

        init: function(option) {
            var _this = this;
            _this.getTrendData(option);
        },
        echart_option: function(data,dates,volumes) {
            var _this = this;

            var colorList = ['#c23531','#2f4554', '#61a0a8', '#d48265', '#91c7ae','#749f83',  '#ca8622', '#bda29a','#6e7074', '#546570', '#c4ccd3'];

            var dataMA5 = _this.calculateMA(5, data);
            var dataMA10 = _this.calculateMA(10, data);
            var dataMA20 = _this.calculateMA(20, data);
            var options = {
                animation: false,
                color: colorList,
                backgroundColor: '#fff',
                tooltip: {
                    axisPointer: {
                        type: 'cross',
                    },
                    trigger: 'axis',
                    formatter: function (params) {
                        var res = '<span style="color: #999;">时间：</span>' + params[0].name ;
                        for (var i = 0; i < params.length; i++) {
                            if (params[i].value instanceof Array) {
                                res += ' <span style="color: #999;">开盘 : </span>' + params[i].value[1] + ' <span style="color: #999;">最高 : </span>' + params[i].value[4] + '';
                                res += ' <span style="color: #999;">收盘 : </span>' + params[i].value[2] + ' <span style="color: #999;">最低 : </span>' + params[i].value[3];
    
                            } else if(params[i].seriesName == 'Volume') {
                                res += ' <span style="color: #999;">成交量 : </span>' + params[i].value;
                            } 
                        }
    
                        return res;
                    },
    
                    // triggerOn: 'none',
                    transitionDuration: 0,
                    confine: true,
                    // bordeRadius: 4,
                    // borderWidth: 1,
                    // borderColor: '#333',
                    backgroundColor: 'rgba(255,255,255,0.5)',
                    
                    textStyle: {
                        fontSize: 12,
                        color: '#333',
                    },
                    position: {
                        top: 30,
                        left: 50,
                    }
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
                    end: 30,
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
                    show: false,
                    type: 'category',
                    data: dates,
                    boundaryGap : true,
                    splitLine: {show: false},
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
                    boundaryGap : true,
                    splitLine: {show: false},
                    axisLabel: {
                        formatter: function (value) {
                            return echarts.format.formatTime('hh:mm', value);
                        }
                    },
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
                        // inside: true,
                        align: 'right',
                        margin: 1,
                        showMinLabel: false,
                        formatter: '{value}\n'
                    }
                }, {
                    scale: true,
                    gridIndex: 1,
                    splitNumber: 2,
                    axisLabel: {
                        // inside: true,
                        align: 'right',
                        margin: 1,
                        showMaxLabel: false,
                        // formatter: '{value}',
                        formatter: function(value) {
                            var str = value.toString();
                            if(str.indexOf('.') < 0) {
                                if(str.length > 3) {
                                    str = parseFloat(str / 1000).toFixed(0) + 'k';
                                }
                            }
    
                            return str;
                        }
                    },
                    axisLine: {lineStyle: { color: '#777' }},
                    axisTick: {show: false},
                    splitLine: {show: false},
    
                }],
                grid: [{
                    left: 50,
                    right: 10,
                    top: 50,
                    height: '75%'
                }, {
                    left: 50,
                    right: 10,
                    top: '75%',
                    height: '20%'
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
                        style: {fill: colorList[1], font: 'bold 12px Sans-serif'},
                        left: 0
                    }, {
                        id: 'MA10',
                        type: 'text',
                        style: {fill: colorList[2], font: 'bold 12px Sans-serif'},
                        left: 'center'
                    }, {
                        id: 'MA20',
                        type: 'text',
                        style: {fill: colorList[3], font: 'bold 12px Sans-serif'},
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
                    data: _this.setVolumeColor(volumes,data)
                }, {
                    type: 'candlestick',
                    name: '日K',
                    data: data,
                    itemStyle: {
                        normal: {
                            color: _this.upcolor,
                            color0:  _this.downcolor,
                            borderColor:  _this.upcolor,
                            borderColor0:  _this.downcolor
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
            }

            return options;
        },
        
        kinit: function(data,dates,volumes) {
            var _this = this;
            // 使用刚指定的配置项和数据显示图表。
            _this.myChart.setOption( _this.echart_option(data,dates,volumes));
        },
        // 调用K线数据接口
        getTrendData: function(option){ 
            var _this = this;

            if(_this.sw) {
                _this.sw = false;
                _this.myChart = echarts.init(option.el);
            }
            $.get("/Contract/getKline?market="+ option.market +"&time="+ option.time + "&t="+(new Date().getTime()),function(res){

                if(res.length) {

                    $('.gui-loading').hide();

                    _this.kdatas = res;

                    var kdatas = _this.formatData(res);

                    
                    _this.myChart.setOption( _this.echart_option(kdatas.data,kdatas.dates,kdatas.volumes));
                    
                }
            },'json');
        },

        // K线数据处理
        formatData: function(myDatas) {
            var _this = this;
            var dates = []; // 时间
            var data = [];
            var volumes = [];

            for(var i=0;i<myDatas.length;i++) {

                dates.push(_this.timeStamp2String(myDatas[i][0] * 1000));

                data.push([myDatas[i][2],myDatas[i][5],myDatas[i][4],myDatas[i][3],myDatas[i][1]]);

                volumes.push(myDatas[i][1]);
            }
            
            return {
                dates: dates,
                data: data,
                volumes: volumes
            }
        },
        
        // 时间格式化处理
        timeStamp2String: function(time){

            var datetime = new Date();
            datetime.setTime(time);
            var year = datetime.getFullYear();
            var month = datetime.getMonth() + 1 < 10 ? "0" + (datetime.getMonth() + 1) : datetime.getMonth() + 1;
            var date = datetime.getDate() < 10 ? "0" + datetime.getDate() : datetime.getDate();
            var hour = datetime.getHours()< 10 ? "0" + datetime.getHours() : datetime.getHours();
            var minute = datetime.getMinutes()< 10 ? "0" + datetime.getMinutes() : datetime.getMinutes();
            var second = datetime.getSeconds()< 10 ? "0" + datetime.getSeconds() : datetime.getSeconds();

            return year + "-" + month + "-" + date+" "+hour+":"+minute+":"+second;
        },
        // 均线数据处理
        calculateMA: function (dayCount, data) {
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
                result.push((sum / dayCount).toFixed(5));
            }
            return result;
        },
        // 交易量柱状图的颜色
        setVolumeColor: function(volumes,data) {
            var _this = this;
            var newVolumes = new Array();
            var obj;
            var colors = _this.getVolumeBarColor(data);

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
        },


        getVolumeBarColor: function (data) {

            var colorArr = [];

            for(var i=0;i < data.length;i++) {

                if(data[i][0] - data[i][1] > 0) {

                    colorArr.push('#d4f1eb'); // downcolor

                } else {

                    colorArr.push('#efd8d8'); // upcolor
                }
            }
            return colorArr;
        }

    }
    w.kline = kline;

})(window,document)