
var chatFactory = (function(){

    var _instance;

    var _msgbox;  //提示消息

    function create(){
        var Chat = function(options){
            this.userId = null; //用户id
            this.client = null;  //websocket 对象
            this.socketUrl = null  //websocket 连接地址
            this.chatList = [];  //聊天列表
            this.currentChatIndex = 0;  //当前聊天窗口下标
            this.isOpen = false;  //窗口是否打开
            this.unreadCount = 0;  //未读消息数量
            this.connected = false; //是否已连接
            this.ui = {
                box : "chat-box",
                header : "chat-box-header",
                body : "chat-box-body",
                footer : "chat-box-footer",
                chatButton : "chat-button"
            }
            
        }

        /**
         * connectServer
         * 连接聊天服务器
         * @param {*} url
         */
        Chat.prototype.connectServer = function(url){
            var _self = this;
            this.socketUrl = url;
            this.client = this.userId !== 0 && new WebSocket(url);
            this.client.onmessage = function(res){
                _self.onMessage(res);
            }
            this.client.onopen = function(){
                _self.connected = true;
                console.log("ws open");
                _self.onOpen();
            }
            this.client.onclose = function(){
                _self.connected = false;
                console.log("ws closed");
            }
            return this;
        }

        /**
         * disconnectServer
         * 断开聊天服务器
         */
        Chat.prototype.disconnectServer = function(){
            this.client.close();
        }
        
        /**
         * update
         * 更新聊天内置数据，更新界面显示
         * @param {*} data
         */
        Chat.prototype.update = function(data){
            if(data) this.data = Object.assign(this.data, data);
            $(".c-user span").text(this.data.truename);
            $(".c-trade span").text(this.data.tradeid);
        }
    
        /**
         * initChatList
         * 初始化聊天的列表
         */
        Chat.prototype.initChatList = function(){
            var _this = this;
            var chatList = this.chatList;
            //清空列表数据
            this.$chatList.html('');
            for (var index = 0; index < chatList.length; index++) {
                var chatItem = chatList[index];
                var $chatName = $('<span />').text(chatItem.truename);
                var $chatNum = parseInt(chatItem.read_num) ? $('<i />').text(chatItem.read_num) : '';
                var $chatItem = $('<div class="chat-item ' + (index == this.currentChatIndex ? "active" : "") + '" data-index="' + index + '" />')
                    .append($chatName, $chatNum);
                this.$chatList.append($chatItem);
            }
            //绑定事件
            $(".chat-list").on('click', ".chat-item", function(e){
                //改变聊天对象
                var index = $(this).data("index");
                var data = _this.chatList[index];
                _this.currentChatIndex = index;
                //发送已读
                if(parseInt(data.read_num) > 0){
                    _this.sendMessage({
                        type: 'yd',
                        myid: data.myid,
                        toid: data.toid
                    })
                }
                //已读
                $(this).find("i").remove();
                //修改未读数量
                data.read_num = "0";
                _this.updateChatList(data);
                _this.init(data).open();
                //查询历史消息
                _this.getHistoryMessage();
                
            });
        }
    
        /**
         * updateChatList
         * 更新聊天列表
         * @param {*} data
         */
        Chat.prototype.updateChatList = function(data){
            for (var index = 0; index < this.chatList.length; index++) {
                var item = this.chatList[index];
                if(item.toid == data.toid){
                    this.chatList[index] = data;
                    return;
                }
            }
            this.chatList.push(data)
        }
    
        /**
         * getChatFromList
         * 获取指定的聊天信息
         * @param {*} data
         * @returns
         */
        Chat.prototype.getChatFromList = function(data){
            for (var index = 0; index < this.chatList.length; index++) {
                var item = this.chatList[index];
                if(item.toid == data.toid){
                    return item;
                }
            }
        }
    
        /**
         * isInChatList
         * 对象是否在聊天列表里
         * @param {*} data
         */
        Chat.prototype.isInChatList = function(data){
            return this.getChatFromList(data) ? true : false;
        }
    
        Chat.prototype.alert = function(msg){
            if(_msgbox){
                _msgbox(msg);
            }else{
                window.alert(msg);
            }
        }
    
        /**
         * init
         * 聊天室初始化
         */
        Chat.prototype.init = function(options){
            this.finish();
            var _self = this;
            this.data = options;
            this.updateChatList(options);
            var $body = $("body");
            this.$chatBox = $('<div class="chat-close" />')
                .addClass(this.ui.box);
            this.$chatList = $('<div class="chat-list" />');
            this.$chatArea = $('<div class="chat-area" />');
            this.$chatButton = $('<button id="chatButton" class="ui-button" />')
                .addClass(this.ui.chatButton)
                .text('发送')
                .attr({
                    type : "button"
                });
            this.$chatCloseButton = $('<div class="ui-close"><i class="ui-icon ui-icon-close"></i></div>');
            this.$chatOpenButton = $('<h4 id="chat_tag" class="chat-tag"><span>' + this.data.truename + '</span></h4>');
            var $chatHeader = $('<div />')
                .addClass(this.ui.header)
                .append('<div class="chat-title"><p class="c-user">与<span>' + this.data.truename + '</span>交谈</p><p class="c-trade">交易单号:<span>' + this.data.tradeid + '</span></p></div>')
                .append(this.$chatCloseButton)
            var $chatBody = $('<div />')
                .addClass(this.ui.body)
                .addClass('g-scrollbar');
            
            var $chatFooter = $('<div class="' + this.ui.footer + '" />')
                .append('<textarea id="inputMessage" class="chat-input" name="inputMessaeg" ></textarea>')
                .append(this.$chatButton);
            this.$chatArea.append($chatHeader, $chatBody, $chatFooter);
            this.$chatBox.append(this.$chatList, this.$chatArea)
            $body.append(this.$chatBox);
            $body.append(this.$chatOpenButton);
            //将下标调整为当前初始化的聊天
            this.currentChatIndex = this.chatList.map(function(i){
                return i.toid;
            }).indexOf(this.data.toid);
            //初始化聊天列表
            this.initChatList();
    
            //event
            this.$inputMessage = $("#inputMessage");
            this.$chatButton.on('click', function(){
                _self.send();
            })
            this.$chatCloseButton.on('click', function(){
                _self.close();
            });
            this.$chatOpenButton.on('click', function(){
                _self.open();
            });
            this.$inputMessage.on("keydown", function(){
                var e = event || window.event || arguments.callee.caller.arguments[0];
                if (e && e.keyCode == 13) {
                    e.preventDefault();
                    if($(this).val().trim() == ""){
                        $(this).val("");
                    }else{
                        _self.send();
                    }
                }
            })
            return this;
        }
        
        Chat.prototype.open = function(){
            this.isOpen = true;
            //未读消息置空
            this.unreadCount = 0;
            $(".has-new-msg").length > 0 && $(".has-new-msg").empty().remove();
            this.update();
            this.$chatBox.removeClass('chat-close').addClass('chat-open');
            this.$chatOpenButton.css("display", "none");
            return this;
        }
    
        //结束聊天
        Chat.prototype.finish = function(){
            if(this.$chatBox){
                this.$chatBox.empty().remove();
                this.$chatOpenButton.empty().remove();
            }
        }
    
        Chat.prototype.show = function(){
            this.$chatBox.removeClass('chat-close').addClass('chat-open');
            this.$chatOpenButton.css("display", "none");
            return this;
        }
    
        //关闭聊天
        Chat.prototype.close = function(){
            this.isOpen = false;
            this.$chatBox.removeClass('chat-open').addClass('chat-close');
            this.$chatOpenButton.css("display", "block");
            return this;
        }
        
        Chat.prototype.send = function(){
            var message = this.$inputMessage.val();
            message = cleanInput(message);
            if(message != ''){
                this.$inputMessage.val('');
                var obj = {
                    type: "say",
                    username: this.data.username,
                    myid : this.data.myid,
                    toid : this.data.toid,
                    tradeid : this.data.tradeid,
                    msg : message
                }
                this.sendMessage(obj);
            }
        }
    
        Chat.prototype.onOpen = function(){
            //this.connected = true;
            var obj = {
                type: 'con',
                userid: this.userId
            }
            this.sendMessage(obj);
        }
    
        /**
         * sendMessage
         * 发送消息
         * @param {object} obj (传入对象)
         */
        Chat.prototype.sendMessage = function(obj){
            this.client.send(JSON.stringify(obj));
        }
        
        Chat.prototype.onMessage = function(res){
            var _this = this;
            var obj = JSON.parse(res.data);
            switch(obj.type){
                case "rcon":
                    this.disconnectServer();
                    this.alert(obj.data);
                break;
                case "order_suc":
                    if(this.userId != obj.myid){
                        this.init({
                            toid : obj.myid,
                            truename : obj.username,
                            myid : obj.toid,
                            username : obj.truename,
                            tradeid : obj.tradeid
                        }).open();
                    }else{
                        this.init({
                            toid : obj.toid,
                            truename : obj.truename,
                            myid : obj.myid,
                            username : obj.username,
                            tradeid : obj.tradeid
                        }).open();
                    }
                    
                break;
                case "say":
                    // if(this.chatList.length == 0){
                    //     this.init({
                    //         toid : obj.myid,
                    //         truename : obj.username,
                    //         myid : obj.toid,
                    //         username : obj.truename,
                    //         tradeid : obj.tradeid
                    //     }).open()
                    //     this.sendMessage({
                    //         type: 'hsy',
                    //         myid: data.myid,
                    //         toid: data.toid,
                    //         tradeid: data.tradeid
                    //     })
                    // }
                    if( this.data && obj.tradeid == this.data.tradeid){
                        if(!this.isOpen){
                            this.unreadCount++
                        }
                        if(this.unreadCount > 0){
                            $(".has-new-msg").length == 0 ? $(".chat-tag").append('<small class="has-new-msg">' + this.unreadCount + '</small>') : $(".has-new-msg").text(this.unreadCount);
                        }
                        addChatMessage(obj, obj.myid == this.userId);
                        $(".chat-box-body").scrollTop(9999);
                    }else{
                        var data = {
                            toid : obj.myid,
                            truename : obj.username,
                            myid : obj.toid,
                            username : obj.truename,
                            tradeid : obj.tradeid
                        }
                        if(!this.isOpen || !this.isInChatList(data)){
                            this.hasNewMessage(data);
                        }
                        if(this.isOpen && this.isInChatList(data)){
                            var sourceData = this.getChatFromList(data);
                            var unreadcount = sourceData.hasOwnProperty("read_num") ? parseInt(sourceData.read_num) : 0;
                            data.read_num = unreadcount + 1;
                            this.updateChatList(data);
                            this.initChatList();
                        }
                        // if(this.isInChatList(obj)){
                        //     var readNum = this.getChatFromList(obj).read_num || 0;
                        //     obj.read_num = readNum++;
                        //     this.updateChatList(obj);
                        //     this.initChatList();
                        // }else{
                        //     this.updateChatList(obj);
                        //     this.initChatList();
                        // }
                    }
                break;
                //返回未读消息
                case "read":
                    var data = obj.data[this.currentChatIndex];
                    this.chatList = obj.data;
                    this.init(data).open();
                    this.getHistoryMessage();
                break;
            }
            
        }
    
        /**
         * getHistoryMessage
         * 获取历史聊天记录
         * @param {*} data
         */
        Chat.prototype.getHistoryMessage = function(data){
            var data = data || this.data;
            this.sendMessage({
                type: 'hsy',
                myid: data.myid,
                toid: data.toid,
                tradeid: data.tradeid
            })
        }
    
        Chat.prototype.hasNewMessage = function(obj){
            var _this = this;
            var data = {
                toid : obj.toid,
                truename : obj.truename,
                myid : obj.myid,
                username : obj.username,
                tradeid : obj.tradeid
            }
            //判断是否存在来自该用户的消息
            if($(".new-message[data-id=" + data.toid + "]").length > 0){
                // var $newMessage = $(".new-message[data-id=" + data.toid + "]");
                // var unreadCount = $newMessage.data("unread") || 1;
                // $newMessage.
            }else{
                var msgCount = $(".new-message").length;
                var $newMessage = $('<div />')
                    .addClass('new-message')
                    .css("bottom", 20 + msgCount * (20 + 40) + 'px')
                    .text('你有来自' + data.truename + '的新消息')
                    .data("param", data)
                    .attr("data-id", data.toid)
                    .click(function(){
                        _this.updateChatList(data);
                        _this.init(data).open();
                        _this.getHistoryMessage();
                        $(this).remove();
                    });
                $('body').append($newMessage);
                setTimeout(function(){
                    $newMessage.css("right", "15px");
                },200);
            }
            
        }
        
        
        
        /**
         * set userid
         * 设置用户id
         * @param {number} userid
         * @returns
         */
        Chat.prototype.setUserId = function(userid){
            this.userId = userid;
            return this;
        }

        /**
         * get userid
         * 获取用户id
         * @returns
         */
        Chat.prototype.getUserId = function(){
            return this.userId;
        }
        
        /**
         * prevent label injection
         * 防止标签注入
         * @param {*} input
         * @returns
         */
        function cleanInput(input){
            return $("<div/>").text(input).html();
        }
        
        function addChatMessage(obj, isSelf){
            // var $useridDiv = $('<span class="chat-userid"/>')
            //   .text(obj.myid);
            var $messageTextDiv = $('<div class="chat-text">')
                .text(obj.msg);
            var $messageBodyDiv = $('<div class="chat-message-body">')
                .append($messageTextDiv)
                .append('<div class="arrow"></div>');
            var $messageDiv = $('<li class="chat-message ' + (isSelf ? "self" : "other") + '"/>')
                .append('<div class="chat-message-title"><div class="chat-message-time">' + getFormatDate(obj.addtime) + '</div><div class="chat-message-name">' + (isSelf ? "我" : obj.username) + '</div></div>')
                .append($messageBodyDiv);
            $(".chat-box-body").append($messageDiv);
        }
    
        //获取格式化时间
        function getFormatDate(time, fmt){
            var fmt = fmt || "yyyy-MM-dd hh:mm:ss";
            var now = new Date(parseFloat(time) * 1000);
            var o = {
                "M+": now.getMonth() + 1, //月份
                "d+": now.getDate(), //日
                "h+": now.getHours(), //小时
                "m+": now.getMinutes(), //分
                "s+": now.getSeconds(), //秒
                "q+": Math.floor((now.getMonth() + 3) / 3), //季度
                "S": now.getMilliseconds() //毫秒
            };
            if (/(y+)/.test(fmt)) fmt = fmt.replace(RegExp.$1, (now.getFullYear() + "").substr(4 - RegExp.$1.length));
            for (var k in o)
            if (new RegExp("(" + k + ")").test(fmt)) fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            return fmt;
        }

        return new Chat();
    }

    return {
        getInstance : function(){
            if(!_instance){
                _instance = create();
            }
            return _instance;
        },
        setMsgbox : function(fuc){
            _msgbox = fuc;
        }
    }
})()