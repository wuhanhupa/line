(function(w, d){
    var accountBind = {
        isImageUploaded : false,
        options : {
            saveUrl : "",
            updateUrl : "",
            name : "alipay"
        },
        /**
         * accountBind.init
         * 账号绑定
         * @param {*} options
         * {
         *   saveUrl [String] 保存提交地址
         *   updateUrl [String] 更新提交地址
         *   name [String] 绑定名称
         * }
         * @returns
         */
        init : function(options){
            //如果没有传入saveUrl不能初始化
            if(options.saveUrl === '' || options.saveUrl === null || options.saveUrl === undefined ){
                console.error('The saveUrl attribute is necessary');
                return false;
            }
            //如果没有传入updateUrl不能初始化
            if(options.updateUrl === '' || options.updateUrl === null || options.updateUrl === undefined ){
                console.error('The updateUrl attribute is necessary');
                return false;
            }
            //初始化操作
            var _self = this;
            this.options = $.extend(this.options, options, true);
            this.name = this.options.name;
            this.saveUrl = this.options.saveUrl;
            this.updateUrl = this.options.updateUrl;
            //处理上传图片
            var imgUpload = new upload({
                uploadId : 'inputfile',
                uploadButtonId : 'uploadButton',
                onUploadSuccess : function(data){
                    _self.payimg = '/Upload/pay/' + data;
                    $('#qcodeImg').attr("src", _self.payimg);
                    $('.image-area').removeClass('hide').addClass('show');
                    _self.isImageUploaded = true;   //图片上传成功
                }
            });
            imgUpload.init();
        },
        
        /**
         * accountBind.save
         * 更新绑定账号信息
         * @returns
         */
        save : function(){
            var _self = this;
            var param = _self.createQueryParam();
            if(!_self.isImageUploaded){
                layer.msg('没有上传收款码图片', {icon:2});
                return false;
            }
            if(!_self.checkQueryParam(param)){
                return false;
            }
            $.post(_self.saveUrl, param, function (data) {
                if (data.status === '0000') {
                    layer.msg(data.info, {icon: 1});
                    setTimeout(function(){
                        location.reload();
                    },3000);
                } else {
                    layer.msg(data.info, {icon: 2});
                    if (data.url) {
                        window.location = data.url;
                    }
                }
            }, "json");
        },
        /**
         * accountBind.update
         * 更新绑定账号信息
         */
        update : function(){
            var _self = this;
            var param = _self.createQueryParam();
            $.ajax({
                url: _self.updateUrl,
                type: 'POST',
                data: param,
                success: function(data) {
                    if (data.status === '0000') {
                        layer.msg(data.info, {icon: 1});
                    } else {
                        layer.msg(data.info, {icon: 2});
                    }
                },
                error: function () {
                    console.error("ajax error");
                }
            });
        },
        /**
         * accountBind.createQueryParam
         * 生成查询参数
         */
        createQueryParam : function(){
            var _self = this;
            var account = $("#bindAccount").val();
            var name = $("#name").val();
            var paypassword = $("#paypassword").val();
            var payimg = $('#qcodeImg').attr("src") || _self.payimg;
            var param = {
                name : name,
                payimg : payimg,
                paypassword : paypassword
            };
            param[_self.name] = account;
            return param;
        },
        /**
         * accountBind.checkQueryParam
         * 检查参数是否合法
         * @param {*} param
         * @returns
         */
        checkQueryParam : function(param){
            var _self = this;
            if (param[_self.name] == "" || param[_self.name] == null) {
                layer.tips('请输入账号', '#bindAccount', {tips: 3});
                return false;
            }
            if (param.name == "" || param.name == null) {
                layer.tips('请输入昵称', '#name', {tips: 3});
                return false;
            }
        
            if (param.paypassword == "" || param.paypassword == null) {
                layer.tips('请输入交易密码', '#paypassword', {tips: 3});
                return false;
            }
        
            if (param.paypassword == "" || param.paypassword == null) {
                layer.tips('请输入交易密码', '#paypassword', {tips: 3});
                return false;
            }
            return true;
        }
    }

    

    w.accountBind = accountBind;
})(window, document);

(function(w,d){
    /**
     * Upload  1.0.2 2018年9月3日18:32:39
     * 文件上传
     * @param {*} options
     */
    var Upload = function(options){
        this.options = $.extend(true, {
            uploadUrl : "/Home/User/imgupload",
            uploadId : 'uploadInput',  //文件上传html控件id
            uploadButtonId : 'uploadButton',  //触发上传按钮id
            uploadType : ["jpg", "png","jpeg"],
            size : 0.5 * 1024 * 1024,
            willCheck : true,
            onUploadSuccess : null,  //上传成功回调
            addCheckRule : null
        }, options);
        //检查规则
        this.rules = {
            type : function(file, name){
                var fileType = file.name.replace(/.+\./, "").toLowerCase();
                if(this.options.uploadType.indexOf(fileType) == -1){
                    layer.msg(this.messages[name]);
                    return false;
                }else{
                    return true;
                }
            },
            size : function(file, name){
                if(file.size > this.options.size){
                    layer.msg(this.messages[name]);
                    return false;
                }else{
                    return true;
                }
            }
        }
        this.messages = {
            size : "上传的文件太大",
            type : "上传格式不正确"
        }
    }
    /** 
     * upload.init
     * 文件上传初始化
     */
    Upload.prototype.init = function(){
        var _self = this;
        var opt = this.options;
        var _upload = document.getElementById(opt.uploadId);
        var _upload_button = document.getElementById(opt.uploadButtonId);
        $(_upload).on("change", function(){ 
            //创建FormData对象
            var data = new FormData();
            //为FormData对象添加数据
            $.each($(_upload)[0].files, function (i, file) {
                var file = _getFileData(file);
                data.append('upload_file' + i, file);
                //是否需要检查上传文件
                if(opt.willCheck){
                    if(!_self.check(file)){
                        _upload.value = "";   //重置上传控件的值，避免上传同样文件不会触发事件的问题
                        return false;
                    }
                }
                _uploadAjax(opt.uploadUrl, data, function(data){
                    if (data) {
                        _self.imageUrl = '/Upload/pay/' + data;
                        _self.options.onUploadSuccess && _self.options.onUploadSuccess.call(_self, data);  //上传成功触发回调
                    }else{
                        console.error("upload url is null");
                    }
                });
            });
        });
        $(_upload_button).on('click', function(){
            _upload.click();
        });

        return _self;
    }
    /** 
     * upload.getImageUrl
     * 获取上传文件路径 
     */
    Upload.prototype.getImageUrl = function(){
        return this.imageUrl;
    }

    /**
     * Upload.addRule
     * 添加规则
     * @param {*} name
     * @param {*} func
     */
    Upload.prototype.addRule = function(name, func, message){
        this.rules[name] = func;
        this.messages[name] = message;
        return this;
    }

    /**
     * Upload.check
     * 检查规则
     */
    Upload.prototype.check = function(file){
        for(var i in this.rules){
            if(!this.rules[i].call(this, file, i)){
                return false;
            }
        }
        return true;
    }

    /**
     * _uploadAjax
     * 上传文件ajax
     * @param {*} url 上传地址
     * @param {*} data 上传数据
     * @param {*} successCallback 上传成功回调
     */
    function _uploadAjax(url, data, successCallback){
        //发送数据
        $.ajax({
            url: url,
            type: 'POST',
            data: data,
            cache: false,
            contentType: false,     //不可缺参数
            processData: false,     //不可缺参数
            success: successCallback,
            xhr : function(){
                var xhr = $.ajaxSettings.xhr();
                xhr.upload.addEventListener("progress", function(e){
                    //var percent = Math.floor(100 * e.loaded / e.total);
                }, false);
                return xhr;
            },
            error: function () {
                throw new Error("upload error");
            }
        });
    }

    /**
     * _getFileData
     * 获取上传文件信息
     * @param {*} file
     */
    function _getFileData(file){
        var reader = new FileReader();
        var image = new Image();
        reader.onload = function(e){
            var data = e.target.result;
            image.onload = function(){
                file.width = image.width;
                file.height = image.height;
            }
            image.src = data;
        }
        reader.readAsDataURL(file);
        return file
    }

    w.upload = Upload;
})(window, document);

/** 
 *  ui
 */
;(function(w,d){
    
    var ui = {
        notice : function(element){
            //获取公告
            $.ajax({
                type : "post",
                url : "/Article/notice",
                success : function(res){
                    $(element).html('<a href="' + res.url + '">公告:' + res.title + '</a>')
                }
            });
        },
        message : function(){
            $.ajax({
                type : "post",
                url : "/Article/notice",
                success : function(res){
                    var html = '<div class="ui-message ui-message-error">';
                    html += '<p>【重要通知】: <a href="' + res.url + '">' + res.title + '</a></p>';
                    html += '<a class="ui-close" href="javascript:;"><i class="ui-icon ui-icon-close"></i></a>';
                    html += '</div>';
                    $("body").prepend(html);
                }
            });
            //绑定关闭事件
            $("body").on("click", ".ui-close", function(e){
                $(this).parents(".ui-message").remove();
            });
        },
        /**
         * 获取验证码
         * getCode
         * @param {*} option
         * phoneField [String] 手机号的表单id
         * getCodeButton [String] 发送验证码按钮
         * cd [Number] 发送验证码冷却时间
         */
        getCode : function(option){
            var opt = $.extend({
                url : "/User/userSmS",
                phoneField : "moble",
                getCodeButton : "getCodeButton",
                cd : 90
            }, option, true);
            var reg = /^1[3|4|5|7|8][0-9]{9}$/;
            var domMobile = document.getElementById(opt.phoneField);
            var domGetCodeButton = document.getElementById(opt.getCodeButton);
            var url = opt.url;
            var $mobile = $(domMobile);
            var $getCodeButton = $(domGetCodeButton);
            var phoneNumber = $mobile.val();
            var timer, count = opt.cd;

            if(!phoneNumber) {
                layer.msg('请输入手机号',{icon : 2});
                return false;
            }
            if(!reg.test(phoneNumber)){
                layer.msg('手机号不正确',{icon : 2});
                return false
            }
            $.ajax({
                url: url,
                type: 'POST',
                data: {
                    phone: phoneNumber
                },
                dataType: 'json',
                beforeSend : function(xhr){
                    $getCodeButton.attr("disabled", "disabled");
                    timer = setInterval(function(){
                        if(count <= 0){
                            clearInterval(timer);
                            $getCodeButton.removeAttr("disabled");
                            $getCodeButton.text("重新发送验证码");
                        }else{
                            $getCodeButton.text((count--) + "秒后重新发送");
                        }
                    }, 1000);
                },
                success : function(res){
                    layer.msg(res.info);
                }
            })
        },
        /**
         * 首页收藏功能
         * ui.collection
         */
        collection : function(e){
            var event = window.event || arguments.callee.caller.arguments[0];
            var $this = $(event.target);
            var market = $this.data("market");
            if(!$this.hasClass("ui-baned")){
                $.ajax({
                    url : "/index/collectionIcon",
                    type : "post",
                    data : {
                        market : market
                    },
                    beforeSend : function(xhr){
                        $this.addClass("ui-baned");
                    },
                    success : function(res){
                        if(res.status == 4009){
                            layer.msg(res.info, {icon:2});
                        }else{
                            layer.msg(res.info, {icon:1});
                            $this.hasClass("add-fav-on") ? $this.removeClass("add-fav-on") : $this.addClass("add-fav-on");    
                        }
                        $this.removeClass("ui-baned");
                    },
                    error : function(xhr, err){
                        console.error("ajax error:", err);
                    }
                });
            }
        },
        /**
         * c2c隐藏真实姓名
         * ui.hideName
         * val 为string类型
         * eg: a = "123456"  ui.hideName(a,0,2) =>  "12"
         */
        hideName: function(val,start,end) {
            return val.slice(start,end);
        }
    }

    var user = {
        login : function(e){
            var event = window.event || arguments.callee.caller.arguments[0];
            var $loginButton = event.type == "click" ? $(event.target) : $("#loginSubmit");
            var username=$("#username").val();
            var password=$("#password").val();
            var verify=$("#verify").val();
            if(username==""||username==null){
                layer.tips('请输入用户名','#username',{tips:3});
                return false;
            }
            if(password==""||password==null){
                layer.tips('请输入登录密码','#password',{tips:3});
                return false;
            }
            if(verify==""||verify==null){
                layer.tips('请输入验证码','#verify',{tips:3});
                return false;
            }
            if(!$loginButton.hasClass("submitting")){
                $.ajax({
                    url : "/login/submit",
                    type : "post",
                    dataType : "json",
                    data : {
                        username : username,
                        password : password,
                        verify : verify
                    },
                    beforeSend : function(){
                        $loginButton.addClass("submitting").text("登录中...");
                    },
                    success : function(data){
                        if(data.status==1){
                            $.cookies.set('cookie_username', username);
                            window.location = data.url;
                        }else{
                            $("#codeImg").click();
                            layer.msg(data.info, {icon: 2});
                            $loginButton.removeClass("submitting").text("立即登录");
                        }
                    }
                })
            }
        }
    }

    w.ui = ui;
    w.user = user;
    
})(window, document)