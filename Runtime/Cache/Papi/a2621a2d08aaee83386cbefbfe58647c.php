<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>gte.io app 下载</title>
    <meta name="description" content="description">
    <meta name="keywords" content="keywords">
    <link rel="stylesheet" href="/Public/Home/css/mobile.css"/>
    <script type="text/javascript" src="/Public/Home/js/jquery.min.js"></script>
    <style>
        html{
            font-size: 62.5%
        }
    </style>
</head>

<body>
    <div class="downloadpage">
        <img class="img-response" src="/Public/Home/images/papi/img1.png" alt="">
        <a href="<?php echo ($ios_addr); ?>" class="download-button ios-button"></a>
        <a href="<?php echo ($android_addr); ?>" class="download-button android-button"></a>
        <p style="margin-top: 20px;text-align: center">微信内请点击右上角的“...”按钮，使用浏览器打开</p>
        <div class="download-tips">
            <h2>iOS 9以上机型安装指南</h2>
            <p>1、由于iOS9 ，首次安装gte.io App的用户需要进入系统“设置”</p>
            <p>2、选择“通用”——“描述文件”/“设备管理”</p>
            <p>3、选择“一个英文的公司名称”，设置信任</p>
            <p>4、点击“信任”完成验证</p>
        </div>
        <div class="download-ft">
            <p>CopyRight© 2018 GTE货币交易所交易平台</p>
            <p>All Rights Reserved</p>
        </div>
    </div>
    <script>
        
    </script>
</body>

</html>