<?php

if (!function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = NULL)
    {
        $result = [];

        if (NULL === $indexKey) {
            if (NULL === $columnKey) {
                $result = array_values($input);
            } else {
                foreach ($input as $row) {
                    $result[] = $row[$columnKey];
                }
            }
        } else if (NULL === $columnKey) {
            foreach ($input as $row) {
                $result[$row[$indexKey]] = $row;
            }
        } else {
            foreach ($input as $row) {
                $result[$row[$indexKey]] = $row[$columnKey];
            }
        }

        return $result;
    }
}

function authgame($name)
{
    if (!check($name, 'w')) {
        return 0;
        exit();
    }

    if (M('VersionGame')->where(['name' => $name, 'status' => 1])->find()) {
        return 1;
    } else {
        return 0;
        exit();
    }
}

function curl_get_https($url)
{
    $curl = curl_init(); // 启动一个CURL会话
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HEADER, 0);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 跳过证书检查
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, TRUE);  // 从证书中检查SSL加密算法是否存在
    $tmpInfo = curl_exec($curl);     //返回api的json对象
    //关闭URL请求
    curl_close($curl);

    return $tmpInfo;    //返回json对象
}

function curl_post_http($url, $post_data)
{
    //dump($url);
    //dump($post_data);
    //初始化
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

    $tmpInfo = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch); exit;
    }
    curl_close($ch);

    return $tmpInfo;

    //关闭URL请求
    //curl_close($curl);
}


function auth_idcard($idcard, $realname)
{
    $array = [
        'idcard' => $idcard,
        'realname' => $realname,
        'key' => '74f862649cddd42baf9bdb2ab47f7750',
    ];
    $url = 'http://op.juhe.cn/idcard/query';
    $result = curl_post_http($url, $array);
    $result = json_decode($result, TRUE);

    return $result;
}

//验证token
function access_token($token)
{
    if (!isset($token)) {
        return FALSE;
    }
    if ($token != S('api_token')) {
        return FALSE;        // 拒绝访问
    } else {
        return TRUE;
    }
}

function get_token()
{
    $client_secret = M('Client')->where(['client_id' => '5d90515d1c837a4d3290e11999f33a5c'])->getField('client_secret');
    $api_token = md5($_SERVER['SERVER_ADDR'] . $client_secret);

    return $api_token;
}

//发送短信
function sendSMS($mobile, $body, $rType, $extno)
{
    //Vendor('PHPQRcode.phpqrcode');
    Vendor('Sms.KXTSmsSDK');
    $Address = 'apis.laidx.com'; //IP地址 不加http://
    $Port = '80'; //端口
    $Account = '6D7D1DAC77584E459FE813A49CCF91BA'; //账户
    $Token = 'c49613b593f04d0cbe449319f4efac14'; //token
    //初始化SDK
    $rest = new KXTSmsSDK($Address, $Port, $Account, $Token);
    // 发送短信
    $result = $rest->send($mobile, $body, $rType, $extno);

    if ($result == NULL) {
        return ' ';
    }
    $result = json_decode($result, TRUE);

    //自己代码业务逻辑
    return $result;
}

function rlog($text)
{
    //先读取文件内容
    $text = addtime(time()) . ', 内容:' . $text . "\n";
    $return = file_put_contents(APP_PATH . '/rbot.log', $text, FILE_APPEND);

    return $return;
}

function huafei($moble = NULL, $num = NULL, $orderid = NULL)
{
    if (empty($moble)) {
        return NULL;
    }

    if (empty($num)) {
        return NULL;
    }

    if (empty($orderid)) {
        return NULL;
    }

    header('Content-type:text/html;charset=utf-8');
    $appkey = C('huafei_appkey');
    $openid = C('huafei_openid');
    $recharge = new \Common\Ext\Recharge($appkey, $openid);
    $telRechargeRes = $recharge->telcz($moble, $num, $orderid);

    if ($telRechargeRes['error_code'] == '0') {
        return 1;
    } else {
        return NULL;
    }
}

function mlog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../move.log', $text, FILE_APPEND);
}

function aaa($item, $pattern, $fun)
{
    $pattern = str_replace('###', $item['id'], $pattern);
    $view = new \Think\View();
    $view->assign($item);
    $pattern = $view->fetch('', $pattern);

    return $fun($pattern);
}

function authUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    $data = curl_exec($ch);

    return $data;
}

function userid($username = NULL, $type = 'username')
{
    if ($username && $type) {
        $userid = (APP_DEBUG ? NULL : S('userid' . $username . $type));

        if (!$userid) {
            $userid = M('User')->where([$type => $username])->getField('id');
            S('userid' . $username . $type, $userid);
        }
    } else {
        $userid = session('userId');
    }

    return $userid ? $userid : NULL;
}

function username($id = NULL, $type = 'id')
{
    if ($id && $type) {
        $username = (APP_DEBUG ? NULL : S('username' . $id . $type));

        if (!$username) {
            $username = M('User')->where([$type => $id])->getField('username');
            S('username' . $id . $type, $username);
        }
    } else {
        $username = session('userName');
    }

    return $username ? $username : NULL;
}

function check_dirfile()
{
    define('INSTALL_APP_PATH', realpath('./') . '/');
    $items = [
        ['dir', '可写', 'ok', './Database'],
        ['dir', '可写', 'ok', './Database/Backup'],
        ['dir', '可写', 'ok', './Database/Cloud'],
        ['dir', '可写', 'ok', './Database/Temp'],
        ['dir', '可写', 'ok', './Database/Update'],
        ['dir', '可写', 'ok', './Runtime'],
        ['dir', '可写', 'ok', './Runtime/Logs'],
        ['dir', '可写', 'ok', './Runtime/Cache'],
        ['dir', '可写', 'ok', './Runtime/Temp'],
        ['dir', '可写', 'ok', './Runtime/Data'],
        ['dir', '可写', 'ok', './Upload/ad'],
        ['dir', '可写', 'ok', './Upload/ad'],
        ['dir', '可写', 'ok', './Upload/bank'],
        ['dir', '可写', 'ok', './Upload/coin'],
        ['dir', '可写', 'ok', './Upload/face'],
        ['dir', '可写', 'ok', './Upload/footer'],
        ['dir', '可写', 'ok', './Upload/game'],
        ['dir', '可写', 'ok', './Upload/link'],
        ['dir', '可写', 'ok', './Upload/public'],
        ['dir', '可写', 'ok', './Upload/shop'],
    ];

    foreach ($items as &$val) {
        if ('dir' == $val[0]) {
            if (!is_writable(INSTALL_APP_PATH . $val[3])) {
                if (is_dir($items[1])) {
                    $val[1] = '可读';
                    $val[2] = 'remove';
                    session('error', TRUE);
                } else {
                    $val[1] = '不存在或者不可写';
                    $val[2] = 'remove';
                    session('error', TRUE);
                }
            }
        } else if (file_exists(INSTALL_APP_PATH . $val[3])) {
            if (!is_writable(INSTALL_APP_PATH . $val[3])) {
                $val[1] = '文件存在但不可写';
                $val[2] = 'remove';
                session('error', TRUE);
            }
        } else if (!is_writable(dirname(INSTALL_APP_PATH . $val[3]))) {
            $val[1] = '不存在或者不可写';
            $val[2] = 'remove';
            session('error', TRUE);
        }
    }

    return $items;
}

function op_t($text, $addslanshes = FALSE)
{
    $text = nl2br($text);
    $text = real_strip_tags($text);

    if ($addslanshes) {
        $text = addslashes($text);
    }

    $text = trim($text);

    return $text;
}

function text($text, $addslanshes = FALSE)
{
    return op_t($text, $addslanshes);
}

function html($text)
{
    return op_h($text);
}

function op_h($text, $type = 'html')
{
    $text_tags = '';
    $link_tags = '<a>';
    $image_tags = '<img>';
    $font_tags = '<i><b><u><s><em><strong><font><big><small><sup><sub><bdo><h1><h2><h3><h4><h5><h6>';
    $base_tags = $font_tags . '<p><br><hr><a><img><map><area><pre><code><q><blockquote><acronym><cite><ins><del><center><strike>';
    $form_tags = $base_tags . '<form><input><textarea><button><select><optgroup><option><label><fieldset><legend>';
    $html_tags = $base_tags . '<ul><ol><li><dl><dd><dt><table><caption><td><th><tr><thead><tbody><tfoot><col><colgroup><div><span><object><embed><param>';
    $all_tags = $form_tags . $html_tags . '<!DOCTYPE><meta><html><head><title><body><base><basefont><script><noscript><applet><object><param><style><frame><frameset><noframes><iframe>';
    $text = real_strip_tags($text, $$type . '_tags');

    if ($type != 'all') {
        while (preg_match('/(<[^><]+)(ondblclick|onclick|onload|onerror|unload|onmouseover|onmouseup|onmouseout|onmousedown|onkeydown|onkeypress|onkeyup|onblur|onchange|onfocus|action|background[^-]|codebase|dynsrc|lowsrc)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }

        while (preg_match('/(<[^><]+)(window\\.|javascript:|js:|about:|file:|document\\.|vbs:|cookie)([^><]*)/i', $text, $mat)) {
            $text = str_ireplace($mat[0], $mat[1] . $mat[3], $text);
        }
    }

    return $text;
}

function real_strip_tags($str, $allowable_tags = '')
{
    return strip_tags($str, $allowable_tags);
}

function clean_cache($dirname = './Runtime/')
{
    $dirs = [$dirname];

    foreach ($dirs as $value) {
        rmdirr($value);
    }

    @(mkdir($dirname, 511, TRUE));
}

function getSubByKey($pArray, $pKey = '', $pCondition = '')
{
    $result = [];

    if (is_array($pArray)) {
        foreach ($pArray as $temp_array) {
            if (is_object($temp_array)) {
                $temp_array = (array)$temp_array;
            }

            if ((('' != $pCondition) && ($temp_array[$pCondition[0]] == $pCondition[1])) || ('' == $pCondition)) {
                $result[] = ('' == $pKey ? $temp_array : isset($temp_array[$pKey]) ? $temp_array[$pKey] : '');
            }
        }

        return $result;
    } else {
        return FALSE;
    }
}

function debug($value, $type = 'DEBUG', $verbose = FALSE, $encoding = 'UTF-8')
{
    if (M_DEBUG || MSCODE) {
        if (!IS_CLI) {
            Common\Ext\FirePHP::getInstance(TRUE)->log($value, $type);
        }
    }
}

function CoinClient($username, $password, $ip, $port, $timeout = 3, $headers = [], $suppress_errors = FALSE)
{
    return new \Common\Ext\CoinClient($username, $password, $ip, $port, $timeout, $headers, $suppress_errors);
}

function createQRcode($save_path, $qr_data = 'PHP QR Code :)', $qr_level = 'L', $qr_size = 4, $save_prefix = 'qrcode')
{
    if (!isset($save_path)) {
        return '';
    }

    $PNG_TEMP_DIR = &$save_path;
    vendor('PHPQRcode.class#phpqrcode');

    if (!file_exists($PNG_TEMP_DIR)) {
        mkdir($PNG_TEMP_DIR);
    }

    $filename = $PNG_TEMP_DIR . 'test.png';
    $errorCorrectionLevel = 'L';

    if (isset($qr_level) && in_array($qr_level, ['L', 'M', 'Q', 'H'])) {
        $errorCorrectionLevel = &$qr_level;
    }

    $matrixPointSize = 4;

    if (isset($qr_size)) {
        $matrixPointSize = &min(max((int)$qr_size, 1), 10);
    }

    if (isset($qr_data)) {
        if (trim($qr_data) == '') {
            exit('data cannot be empty!');
        }

        $filename = $PNG_TEMP_DIR . $save_prefix . md5($qr_data . '|' . $errorCorrectionLevel . '|' . $matrixPointSize) . '.png';
        QRcode::png($qr_data, $filename, $errorCorrectionLevel, $matrixPointSize, 2, TRUE);
    } else {
        QRcode::png('PHP QR Code :)', $filename, $errorCorrectionLevel, $matrixPointSize, 2, TRUE);
    }

    if (file_exists($PNG_TEMP_DIR . basename($filename))) {
        return basename($filename);
    } else {
        return FALSE;
    }
}

function NumToStr($num)
{
    if (!$num) {
        return $num;
    }

    if ($num == 0) {
        return 0;
    }

    $num = round($num, 8);
    $min = 0.0001;

    if ($num <= $min) {
        $times = 0;

        while ($num <= $min) {
            $num *= 10;
            ++$times;

            if (10 < $times) {
                break;
            }
        }

        $arr = explode('.', $num);
        $arr[1] = str_repeat('0', $times) . $arr[1];

        return $arr[0] . '.' . $arr[1] . '';
    }

    return ($num * 1) . '';
}

function Num($num)
{
    if (!$num) {
        return $num;
    }

    if ($num == 0) {
        return 0;
    }

    $num = round($num, 8);
    $min = 0.0001;

    if ($num <= $min) {
        $times = 0;

        while ($num <= $min) {
            $num *= 10;
            ++$times;

            if (10 < $times) {
                break;
            }
        }

        $arr = explode('.', $num);
        $arr[1] = str_repeat('0', $times) . $arr[1];

        return $arr[0] . '.' . $arr[1] . '';
    }

    return ($num * 1) . '';
}

function check_verify($code, $id = 1)
{
    $verify = new \Think\Verify();

    return $verify->check($code, $id);
}

function get_city_ip($ip = NULL)
{
    if (empty($ip)) {
        $ip = get_client_ip();
    }

    $Ip = new Org\Net\IpLocation();
    $area = $Ip->getlocation($ip);
    $str = $area['country'] . $area['area'];
    $str = mb_convert_encoding($str, 'UTF-8', 'GBK');

    if (($ip == '127.0.0.1') || ($str == FALSE) || ($str == 'IANA保留地址用于本地回送')) {
        $str = '未分配或者内网IP';
    }

    return $str;
}

function send_post($url, $post_data)
{
    $postdata = http_build_query($post_data);
    $options = [
        'http' => ['method' => 'POST', 'header' => 'Content-type:application/x-www-form-urlencoded', 'content' => $postdata, 'timeout' => 15 * 60],
    ];
    $context = stream_context_create($options);
    $result = file_get_contents($url, FALSE, $context);

    return $result;
}

function request_by_curl($remote_server, $post_string)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 'mypost=' . $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_USERAGENT, 'qianyunlai.com\'s CURL Example beta');
    $data = curl_exec($ch);
    curl_close($ch);

    return $data;
}

function tradeno()
{
    return substr(str_shuffle('ABCDEFGHIJKLMNPQRSTUVWXYZ'), 0, 2) . substr(str_shuffle(str_repeat('123456789', 4)), 0, 6);
}

function tradenoa()
{
    return substr(str_shuffle('ABCDEFGHIJKLMNPQRSTUVWXYZ'), 0, 6);
}

function tradenob()
{
    return substr(str_shuffle(str_repeat('123456789', 4)), 0, 2);
}

function get_user($id, $type = NULL, $field = 'id')
{
    $key = md5('get_user' . $id . $type . $field);
    $data = S($key);

    if (!$data) {
        $data = M('User')->where([$field => $id])->find();
        S($key, $data);
    }

    if ($type) {
        $rs = $data[$type];
    } else {
        $rs = $data;
    }

    return $rs;
}

function ismobile()
{
    if (isset($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return TRUE;
    }

    if (isset($_SERVER['HTTP_CLIENT']) && ('PhoneClient' == $_SERVER['HTTP_CLIENT'])) {
        return TRUE;
    }

    if (isset($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], 'wap') ? TRUE : FALSE;
    }

    if (isset($_SERVER['HTTP_USER_AGENT'])) {
        $clientkeywords = ['nokia', 'sony', 'ericsson', 'mot', 'samsung', 'htc', 'sgh', 'lg', 'sharp', 'sie-', 'philips', 'panasonic', 'alcatel', 'lenovo', 'iphone', 'ipod', 'blackberry', 'meizu', 'android', 'netfront', 'symbian', 'ucweb', 'windowsce', 'palm', 'operamini', 'operamobi', 'openwave', 'nexusone', 'cldc', 'midp', 'wap', 'mobile'];

        if (preg_match('/(' . implode('|', $clientkeywords) . ')/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return TRUE;
        }
    }

    if (isset($_SERVER['HTTP_ACCEPT'])) {
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && ((strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE) || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return TRUE;
        }
    }

    return FALSE;
}

function send_moble($moble, $content)
{
    debug([$content, $moble], 'send_moble');
    if (stripos(C('moble_url'), 'v.juhe.cn') !== FALSE) {
        //$url = C('moble_url') . '/?Uid=' . C('moble_user') . '&Key=' . C('moble_pwd') . '&mobile=' . $moble . '&smsText=' . $content;
        $sendUrl = C('moble_url'); //http://v.juhe.cn/sms/send
        $smsConf = [
            'key' => C('moble_pwd'), //您申请的APPKEY
            'mobile' => $moble, //接受短信的用户手机号码
            'tpl_id' => '23542', //您申请的短信模板ID，根据实际情况修改
            'tpl_value' => '#code#=' . $content . '&#name#=用户', //您设置的模板变量，根据实际情况修改
        ];

        $recharge = new \Common\Ext\Recharge();
        $content = $recharge->juhecurl($sendUrl, $smsConf, 1);

        if ($content) {
            $result = json_decode($content, TRUE);
            $error_code = $result['error_code'];
            if ($error_code == 0) {
                //状态为0，说明短信发送成功
                //echo "短信发送成功,短信ID：".$result['result']['sid'];
                return TRUE;
            } else {
                //状态非0，说明失败
                //$msg = $result['reason'];
                //echo "短信发送失败(".$error_code.")：".$msg;
                return FALSE;
            }
        } else {
            //返回内容异常，以下可根据业务逻辑自行修改
            return FALSE;
        }
    } else {
        //http://utf8.sms.webchinese.cn
        //gaoyuanme
        //e52c8b397c679177fc70
        //$url = C('moble_url') . '/?Uid=' . C('moble_user') . '&Key=' . C('moble_pwd') . '&smsMob=' . $moble . '&smsText=' . $content;
        $url = 'http://utf8.sms.webchinese.cn/?Uid=fhyyh&Key=67eaf4e2b0c532d29f69&smsMob=' . $moble . '&smsText=您好！本次的验证码为：' . $content . '，打死不要告诉别人哦！';
        if (function_exists('file_get_contents')) {
            $file_contents = file_get_contents($url);
        } else {
            $ch = curl_init();
            $timeout = 5;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $file_contents = curl_exec($ch);
            curl_close($ch);
        }

        return $file_contents;
    }
}

function addtime($time = NULL, $type = NULL)
{
    if (empty($time)) {
        return '---';
    }

    if (($time < 2545545) && (1893430861 < $time)) {
        return '---';
    }

    if (empty($type)) {
        $type = 'Y-m-d H:i:s';
    }

    return date($type, $time);
}

function string_remove_xss($html)
{
    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

    $searchs[] = '<';
    $replaces[] = '&lt;';
    $searchs[] = '>';
    $replaces[] = '&gt;';

    if ($ms[1]) {
        $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';
        $ms[1] = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = '&lt;' . $value . '&gt;';

            $value = str_replace('&amp;', '_uch_tmp_str_', $value);
            $value = string_htmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&amp;', $value);

            $value = str_replace(['\\', '/*'], ['.', '/.'], $value);
            $skipkeys = [
                'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
                'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
                'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
                'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
                'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
                'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
                'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart',
                'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop',
                'onsubmit', 'onunload', 'javascript', 'script', 'eval', 'behaviour', 'expression', 'style', 'class',
            ];
            $skipstr = implode('|', $skipkeys);
            $value = preg_replace(["/($skipstr)/i"], '.', $value);
            if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value) ? '' : '<' . str_replace('&quot;', '"', $value) . '>';
        }
    }
    $html = str_replace($searchs, $replaces, $html);

    return $html;
}

function string_htmlspecialchars($string, $flags = NULL)
{
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = string_htmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === NULL) {
            $string = str_replace(['&', '"', '<', '>'], ['&amp;', '&quot;', '&lt;', '&gt;'], $string);
            if (strpos($string, '&amp;#') !== FALSE) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (!defined('CHARSET') || (strtolower(CHARSET) == 'utf-8')) {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }

    return $string;
}

function check($data, $rule = NULL, $ext = NULL)
{
    $data = trim(str_replace(PHP_EOL, '', $data));

    if (empty($data)) {
        return FALSE;
    }

    $validate['require'] = '/.+/';
    $validate['url'] = '/^http(s?):\\/\\/(?:[A-za-z0-9-]+\\.)+[A-za-z]{2,4}(?:[\\/\\?#][\\/=\\?%\\-&~`@[\\]\':+!\\.#\\w]*)?$/';
    $validate['currency'] = '/^\\d+(\\.\\d+)?$/';
    $validate['number'] = '/^\\d+$/';
    $validate['zip'] = '/^\\d{6}$/';
    $validate['cny'] = '/^(([1-9]{1}\\d*)|([0]{1}))(\\.(\\d){1,2})?$/';
    $validate['integer'] = '/^[\\+]?\\d+$/';
    $validate['double'] = '/^[\\+]?\\d+(\\.\\d+)?$/';
    $validate['english'] = '/^[A-Za-z]+$/';
    $validate['idcard'] = '/^([0-9]{15}|[0-9]{17}[0-9a-zA-Z])$/';
    $validate['truename'] = '/^[\\x{4e00}-\\x{9fa5}]{2,4}$/u';
    $validate['username'] = '/^[a-zA-Z]{1}[0-9a-zA-Z_]{2,12}$/';
    $validate['email'] = '/^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$/';
    $validate['moble'] = '/^(((1[0-9][0-9]{1})|159|153)+\\d{8})$/';
    $validate['password'] = '/^[a-zA-Z0-9_\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=]{6,16}$/';
    $validate['paypassword'] = '/^[a-zA-Z0-9_\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=]{6,18}$/';
    $validate['xnb'] = '/^[a-zA-Z]$/';
    $validate['jsz'] = '/<script[\s\S]*?<\/script>/i';
    //$validate['wx'] = '/^[a-zA-Z]([-_a-zA-Z0-9]{5,19})+$/';
    $validate['wx'] = '/^[a-zA-Z\d_]{5,}$/';
    $validate['nc'] = '/^[\x{4e00}-\x{9fa5}A-Za-z0-9_]+$/u';
    if (isset($validate[strtolower($rule)])) {
        $rule = $validate[strtolower($rule)];

        return preg_match($rule, $data);
    }

    $Ap = '\\x{4e00}-\\x{9fff}' . '0-9a-zA-Z\\@\\#\\$\\%\\^\\&\\*\\(\\)\\!\\,\\.\\?\\-\\+\\|\\=';
    $Cp = '\\x{4e00}-\\x{9fff}';
    $Dp = '0-9';
    $Wp = 'a-zA-Z';
    $Np = 'a-z';
    $Tp = '@#$%^&*()-+=';
    $_p = '_';
    $pattern = '/^[';
    $OArr = str_split(strtolower($rule));
    in_array('a', $OArr) && ($pattern .= $Ap);
    in_array('c', $OArr) && ($pattern .= $Cp);
    in_array('d', $OArr) && ($pattern .= $Dp);
    in_array('w', $OArr) && ($pattern .= $Wp);
    in_array('n', $OArr) && ($pattern .= $Np);
    in_array('t', $OArr) && ($pattern .= $Tp);
    in_array('_', $OArr) && ($pattern .= $_p);
    isset($ext) && ($pattern .= $ext);
    $pattern .= ']+$/u';

    return preg_match($pattern, $data);
}

/**
 * Notice:银行卡号正则
 * @author: hxq
 * @param $no
 * @return bool
 */
function checkBank($no)
{
    $arr_no = str_split($no);
    $last_n = $arr_no[count($arr_no) - 1];
    krsort($arr_no);
    $i = 1;
    $total = 0;
    foreach ($arr_no as $n) {
        if ($i % 2 == 0) {
            $ix = $n * 2;
            if ($ix >= 10) {
                $nx = 1 + ($ix % 10);
                $total += $nx;
            } else {
                $total += $ix;
            }
        } else {
            $total += $n;
        }
        ++$i;
    }
    $total -= $last_n;
    $total *= 9;
    if ($last_n == ($total % 10)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

function check_arr($rs)
{
    foreach ($rs as $v) {
        if (!$v) {
            return FALSE;
        }
    }

    return TRUE;
}

function maxArrayKey($arr, $key)
{
    $a = 0;

    foreach ($arr as $k => $v) {
        $a = max($v[$key], $a);
    }

    return $a;
}

function arr2str($arr, $sep = ',')
{
    return implode($sep, $arr);
}

function str2arr($str, $sep = ',')
{
    return explode($sep, $str);
}

function url($link = '', $param = '', $default = '')
{
    return $default ? $default : U($link, $param);
}

function rmdirr($dirname)
{
    if (!file_exists($dirname)) {
        return FALSE;
    }

    if (is_file($dirname) || is_link($dirname)) {
        return unlink($dirname);
    }

    $dir = dir($dirname);

    if ($dir) {
        while (FALSE !== $entry = $dir->read()) {
            if (($entry == '.') || ($entry == '..')) {
                continue;
            }

            rmdirr($dirname . DIRECTORY_SEPARATOR . $entry);
        }
    }

    $dir->close();

    return rmdir($dirname);
}

function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
{
    $tree = [];

    if (is_array($list)) {
        $refer = [];

        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }

        foreach ($list as $key => $data) {
            $parentId = $data[$pid];

            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else if (isset($refer[$parentId])) {
                $parent = &$refer[$parentId];
                $parent[$child][] = &$list[$key];
            }
        }
    }

    return $tree;
}

function tree_to_list($tree, $child = '_child', $order = 'id', &$list = [])
{
    if (is_array($tree)) {
        $refer = [];

        foreach ($tree as $key => $value) {
            $reffer = $value;

            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }

            $list[] = $reffer;
        }

        $list = list_sort_by($list, $order, $sortby = 'asc');
    }

    return $list;
}

function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = [];

        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc':
                asort($refer);
                break;

            case 'desc':
                arsort($refer);
                break;

            case 'nat':
                natcasesort($refer);
        }

        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }

    return FALSE;
}

function list_search($list, $condition)
{
    if (is_string($condition)) {
        parse_str($condition, $condition);
    }

    $resultSet = [];

    foreach ($list as $key => $data) {
        $find = FALSE;

        foreach ($condition as $field => $value) {
            if (isset($data[$field])) {
                if (0 === strpos($value, '/')) {
                    $find = preg_match($value, $data[$field]);
                } else if ($data[$field] == $value) {
                    $find = TRUE;
                }
            }
        }

        if ($find) {
            $resultSet[] = &$list[$key];
        }
    }

    return $resultSet;
}

function d_f($name, $value, $path = DATA_PATH)
{
    if (APP_MODE == 'sae') {
        return FALSE;
    }

    static $_cache = [];
    $filename = $path . $name . '.php';

    if ('' !== $value) {
        if (is_null($value)) {
        } else {
            $dir = dirname($filename);

            if (!is_dir($dir)) {
                mkdir($dir, 493, TRUE);
            }

            $_cache[$name] = $value;
            $content = strip_whitespace('<?php' . "\t" . 'return ' . var_export($value, TRUE) . ';?>') . PHP_EOL;

            return file_put_contents($filename, $content, FILE_APPEND);
        }
    }

    if (isset($_cache[$name])) {
        return $_cache[$name];
    }

    if (is_file($filename)) {
        $value = include $filename;
        $_cache[$name] = $value;
    } else {
        $value = FALSE;
    }

    return $value;
}

function DownloadFile($fileName)
{
    ob_end_clean();
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Length: ' . filesize($fileName));
    header('Content-Disposition: attachment; filename=' . basename($fileName));
    readfile($fileName);
}

function download_file($file, $o_name = '')
{
    if (is_file($file)) {
        $length = filesize($file);
        $type = mime_content_type($file);
        $showname = ltrim(strrchr($file, '/'), '/');

        if ($o_name) {
            $showname = $o_name;
        }

        header('Content-Description: File Transfer');
        header('Content-type: ' . $type);
        header('Content-Length:' . $length);

        if (preg_match('/MSIE/', $_SERVER['HTTP_USER_AGENT'])) {
            header('Content-Disposition: attachment; filename="' . rawurlencode($showname) . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $showname . '"');
        }

        readfile($file);
        exit();
    } else {
        exit('文件不存在');
    }
}

function wb_substr($str, $len = 140, $dots = 1, $ext = '')
{
    $str = htmlspecialchars_decode(strip_tags(htmlspecialchars($str)));
    $strlenth = 0;
    $output = '';
    preg_match_all('/[' . "\x1" . '-]|[' . "\xc2" . '-' . "\xdf" . '][' . "\x80" . '-' . "\xbf" . ']|[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}|[' . "\xf0" . '-' . "\xff" . '][' . "\x80" . '-' . "\xbf" . ']{3}/', $str, $match);

    foreach ($match[0] as $v) {
        preg_match('/[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}/', $v, $matchs);

        if (!empty($matchs[0])) {
            ++$strlenth;
        } else if (is_numeric($v)) {
            $strlenth += 0.54500000000000004;
        } else {
            $strlenth += 0.47499999999999998;
        }

        if ($len < $strlenth) {
            $output .= $ext;
            break;
        }

        $output .= $v;
    }

    if (($len < $strlenth) && $dots) {
        $output .= '...';
    }

    return $output;
}

function msubstr($str, $start = 0, $length, $charset = 'utf-8', $suffix = TRUE)
{
    if (function_exists('mb_substr')) {
        $slice = mb_substr($str, $start, $length, $charset);
    } else if (function_exists('iconv_substr')) {
        $slice = iconv_substr($str, $start, $length, $charset);

        if (FALSE === $slice) {
            $slice = '';
        }
    } else {
        $re['utf-8'] = '/[' . "\x1" . '-]|[' . "\xc2" . '-' . "\xdf" . '][' . "\x80" . '-' . "\xbf" . ']|[' . "\xe0" . '-' . "\xef" . '][' . "\x80" . '-' . "\xbf" . ']{2}|[' . "\xf0" . '-' . "\xff" . '][' . "\x80" . '-' . "\xbf" . ']{3}/';
        $re['gb2312'] = '/[' . "\x1" . '-]|[' . "\xb0" . '-' . "\xf7" . '][' . "\xa0" . '-' . "\xfe" . ']/';
        $re['gbk'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . '][@-' . "\xfe" . ']/';
        $re['big5'] = '/[' . "\x1" . '-]|[' . "\x81" . '-' . "\xfe" . ']([@-~]|' . "\xa1" . '-' . "\xfe" . '])/';
        preg_match_all($re[$charset], $str, $match);
        $slice = join('', array_slice($match[0], $start, $length));
    }

    return $suffix ? $slice . '...' : $slice;
}

function highlight_map($str, $keyword)
{
    return str_replace($keyword, '<em class=\'keywords\'>' . $keyword . '</em>', $str);
}

function del_file($file)
{
    $file = file_iconv($file);
    @(unlink($file));
}

function status_text($model, $key)
{
    if ($model == 'Nav') {
        $text = ['无效', '有效'];
    }

    return $text[$key];
}

function user_auth_sign($user)
{
    ksort($user);
    $code = http_build_query($user);
    $sign = sha1($code);

    return $sign;
}

function get_link($link_id = NULL, $field = 'url')
{
    $link = '';

    if (empty($link_id)) {
        return $link;
    }

    $link = D('Url')->getById($link_id);

    if (empty($field)) {
        return $link;
    } else {
        return $link[$field];
    }
}

function get_cover($cover_id, $field = NULL)
{
    if (empty($cover_id)) {
        return FALSE;
    }

    $picture = D('Picture')->where(['status' => 1])->getById($cover_id);

    if ($field == 'path') {
        if (!empty($picture['url'])) {
            $picture['path'] = $picture['url'];
        } else {
            $picture['path'] = __ROOT__ . $picture['path'];
        }
    }

    return empty($field) ? $picture : $picture[$field];
}

function get_admin_name()
{
    $user = session(C('USER_AUTH_KEY'));

    return $user['admin_name'];
}

function is_login()
{
    $user = session(C('USER_AUTH_KEY'));

    if (empty($user)) {
        return 0;
    } else {
        return session(C('USER_AUTH_SIGN_KEY')) == user_auth_sign($user) ? $user['admin_id'] : 0;
    }
}

function is_administrator($uid = NULL)
{
    $uid = (is_null($uid) ? is_login() : $uid);

    return $uid && (intval($uid) === C('USER_ADMINISTRATOR'));
}

function show_tree($tree, $template)
{
    $view = new View();
    $view->assign('tree', $tree);

    return $view->fetch($template);
}

function int_to_string(&$data, $map = [
    'status' => [1 => '正常', -1 => '删除', 0 => '禁用', 2 => '未审核', 3 => '草稿'],
])
{
    if (($data === FALSE) || ($data === NULL)) {
        return $data;
    }

    $data = (array)$data;

    foreach ($data as $key => $row) {
        foreach ($map as $col => $pair) {
            if (isset($row[$col]) && isset($pair[$row[$col]])) {
                $data[$key][$col . '_text'] = $pair[$row[$col]];
            }
        }
    }

    return $data;
}

function hook($hook, $params = [])
{
    return \Think\Hook::listen($hook, $params);
}

function get_addon_class($name)
{
    $type = (strpos($name, '_') !== FALSE ? 'lower' : 'upper');

    if ('upper' == $type) {
        $dir = \Think\Loader::parseName(lcfirst($name));
        $name = ucfirst($name);
    } else {
        $dir = $name;
        $name = \Think\Loader::parseName($name, 1);
    }

    $class = 'addons\\' . $dir . '\\' . $name;

    return $class;
}

function get_addon_config($name)
{
    $class = get_addon_class($name);

    if (class_exists($class)) {
        $addon = new $class();

        return $addon->getConfig();
    } else {
        return [];
    }
}

function addons_url($url, $param = [])
{
    $url = parse_url($url);
    $case = C('URL_CASE_INSENSITIVE');
    $addons = ($case ? parse_name($url['scheme']) : $url['scheme']);
    $controller = ($case ? parse_name($url['host']) : $url['host']);
    $action = trim($case ? strtolower($url['path']) : $url['path'], '/');

    if (isset($url['query'])) {
        parse_str($url['query'], $query);
        $param = array_merge($query, $param);
    }

    $params = ['_addons' => $addons, '_controller' => $controller, '_action' => $action];
    $params = array_merge($params, $param);

    return U('Addons/execute', $params);
}

function get_addonlist_field($data, $grid, $addon)
{
    foreach ($grid['field'] as $field) {
        $array = explode('|', $field);
        $temp = $data[$array[0]];

        if (isset($array[1])) {
            $temp = call_user_func($array[1], $temp);
        }

        $data2[$array[0]] = $temp;
    }

    if (!empty($grid['format'])) {
        $value = preg_replace_callback('/\\[([a-z_]+)\\]/', function ($match) use ($data2) {
            return $data2[$match[1]];
        }, $grid['format']);
    } else {
        $value = implode(' ', $data2);
    }

    if (!empty($grid['href'])) {
        $links = explode(',', $grid['href']);

        foreach ($links as $link) {
            $array = explode('|', $link);
            $href = $array[0];

            if (preg_match('/^\\[([a-z_]+)\\]$/', $href, $matches)) {
                $val[] = $data2[$matches[1]];
            } else {
                $show = (isset($array[1]) ? $array[1] : $value);
                $href = str_replace(['[DELETE]', '[EDIT]', '[ADDON]'], ['del?ids=[id]&name=[ADDON]', 'edit?id=[id]&name=[ADDON]', $addon], $href);
                $href = preg_replace_callback('/\\[([a-z_]+)\\]/', function ($match) use ($data) {
                    return $data[$match[1]];
                }, $href);
                $val[] = '<a href="' . U($href) . '">' . $show . '</a>';
            }
        }

        $value = implode(' ', $val);
    }

    return $value;
}

function get_config_type($type = 0)
{
    $list = C('CONFIG_TYPE_LIST');

    return $list[$type];
}

function get_config_group($group = 0)
{
    $list = C('CONFIG_GROUP_LIST');

    return $group ? $list[$group] : '';
}

function parse_config_attr($string)
{
    $array = preg_split('/[,;\\r\\n]+/', trim($string, ',;' . "\r\n"));

    if (strpos($string, ':')) {
        $value = [];

        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }

    return $value;
}

function parse_field_attr($string)
{
    if (0 === strpos($string, ':')) {
        return eval(substr($string, 1) . ';');
    }

    $array = preg_split('/[,;\\r\\n]+/', trim($string, ',;' . "\r\n"));

    if (strpos($string, ':')) {
        $value = [];

        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }

    return $value;
}

function api($name, $vars = [])
{
    $array = explode('/', $name);
    $method = array_pop($array);
    $classname = array_pop($array);
    $module = ($array ? array_pop($array) : 'Common');
    $callback = $module . '\\Api\\' . $classname . 'Api::' . $method;

    if (is_string($vars)) {
        parse_str($vars, $vars);
    }

    return call_user_func_array($callback, $vars);
}

function think_encrypt($data, $key = '', $expire = 0)
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = base64_encode($data);
    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = '';
    $i = 0;

    for (; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        ++$x;
    }

    $str = sprintf('%010d', $expire ? $expire + time() : 0);
    $i = 0;

    for (; $i < $len; ++$i) {
        $str .= chr(ord(substr($data, $i, 1)) + (ord(substr($char, $i, 1)) % 256));
    }

    return str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($str));
}

function think_decrypt($data, $key = '')
{
    $key = md5(empty($key) ? C('DATA_AUTH_KEY') : $key);
    $data = str_replace(['-', '_'], ['+', '/'], $data);
    $mod4 = strlen($data) % 4;

    if ($mod4) {
        $data .= substr('====', $mod4);
    }

    $data = base64_decode($data);
    $expire = substr($data, 0, 10);
    $data = substr($data, 10);

    if ((0 < $expire) && ($expire < time())) {
        return '';
    }

    $x = 0;
    $len = strlen($data);
    $l = strlen($key);
    $char = $str = '';
    $i = 0;

    for (; $i < $len; ++$i) {
        if ($x == $l) {
            $x = 0;
        }

        $char .= substr($key, $x, 1);
        ++$x;
    }

    $i = 0;

    for (; $i < $len; ++$i) {
        if (ord(substr($data, $i, 1)) < ord(substr($char, $i, 1))) {
            $str .= chr((ord(substr($data, $i, 1)) + 256) - ord(substr($char, $i, 1)));
        } else {
            $str .= chr(ord(substr($data, $i, 1)) - ord(substr($char, $i, 1)));
        }
    }

    return base64_decode($str);
}

function data_auth_sign($data)
{
    if (!is_array($data)) {
        $data = (array)$data;
    }

    ksort($data);
    $code = http_build_query($data);
    $sign = sha1($code);

    return $sign;
}

function format_bytes($size, $delimiter = '')
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    $i = 0;

    for (; $i < 5; ++$i) {
        $size /= 1024;
    }

    return round($size, 2) . $delimiter . $units[$i];
}

function set_redirect_url($url)
{
    cookie('redirect_url', $url);
}

function get_redirect_url()
{
    $url = cookie('redirect_url');

    return empty($url) ? __APP__ : $url;
}

function time_format($time = NULL, $format = 'Y-m-d H:i')
{
    $time = ($time === NULL ? NOW_TIME : intval($time));

    return date($format, $time);
}

function create_dir_or_files($files)
{
    foreach ($files as $key => $value) {
        if ((substr($value, -1) == '/') && !is_dir($value)) {
            mkdir($value);
        } else {
            @(file_put_contents($value, ''));
        }
    }
}

function get_table_name($model_id = NULL)
{
    if (empty($model_id)) {
        return FALSE;
    }

    $Model = M('Model');
    $name = '';
    $info = $Model->getById($model_id);

    if ($info['extend'] != 0) {
        $name = $Model->getFieldById($info['extend'], 'name') . '_';
    }

    $name .= $info['name'];

    return $name;
}

function get_model_attribute($model_id, $group = TRUE)
{
    static $list;

    if (empty($model_id) || !is_numeric($model_id)) {
        return '';
    }

    if (empty($list)) {
        $list = S('attribute_list');
    }

    if (!isset($list[$model_id])) {
        $map = ['model_id' => $model_id];
        $extend = M('Model')->getFieldById($model_id, 'extend');

        if ($extend) {
            $map = [
                'model_id' => [
                    'in',
                    [$model_id, $extend],
                ],
            ];
        }

        $info = M('Attribute')->where($map)->select();
        $list[$model_id] = $info;
    }

    $attr = [];

    foreach ($list[$model_id] as $value) {
        $attr[$value['id']] = $value;
    }

    if ($group) {
        $sort = M('Model')->getFieldById($model_id, 'field_sort');

        if (empty($sort)) {
            $group = [1 => array_merge($attr)];
        } else {
            $group = json_decode($sort, TRUE);
            $keys = array_keys($group);

            foreach ($group as &$value) {
                foreach ($value as $key => $val) {
                    $value[$key] = $attr[$val];
                    unset($attr[$val]);
                }
            }

            if (!empty($attr)) {
                $group[$keys[0]] = array_merge($group[$keys[0]], $attr);
            }
        }

        $attr = $group;
    }

    return $attr;
}

function get_table_field($value = NULL, $condition = 'id', $field = NULL, $table = NULL)
{
    if (empty($value) || empty($table)) {
        return FALSE;
    }

    $map[$condition] = $value;
    $info = M(ucfirst($table))->where($map);

    if (empty($field)) {
        $info = $info->field(TRUE)->find();
    } else {
        $info = $info->getField($field);
    }

    return $info;
}

function get_tag($id, $link = TRUE)
{
    $tags = D('Article')->getFieldById($id, 'tags');

    if ($link && $tags) {
        $tags = explode(',', $tags);
        $link = [];

        foreach ($tags as $value) {
            $link[] = '<a href="' . U('/') . '?tag=' . $value . '">' . $value . '</a>';
        }

        return join($link, ',');
    } else {
        return $tags ? $tags : 'none';
    }
}

function addon_model($addon, $model)
{
    $dir = \Think\Loader::parseName(lcfirst($addon));
    $class = 'addons\\' . $dir . '\\model\\' . ucfirst($model);
    $model_path = ONETHINK_ADDON_PATH . $dir . '/model/';
    $model_filename = \Think\Loader::parseName(lcfirst($model));
    $class_file = $model_path . $model_filename . '.php';

    if (!class_exists($class)) {
        if (is_file($class_file)) {
            \Think\Loader::import($model_filename, $model_path);
        } else {
            E('插件' . $addon . '的模型' . $model . '文件找不到');
        }
    }

    return new $class($model);
}

function check_server()
{
    debug('开始checkAuth');

    if (defined('JKHJ<KJJLKNJKHNKJL')) {
        return TRUE;
    }
}

function chkAuthCache()
{
    return TRUE;
    $server_id = S('sjkdfhawefaefawe');
    $key1 = S('eiwuhuqwuiedfasn');
    $key2 = S('ueirfhk32yfsddsf');
    $expir_time = S('klysjdfweiofsetg');
    $times = S('edfqwerfqwrqrrqw');
    $md = S('fgserjkuiwerhwer');
    debug(['$server_id' => $server_id, '$key1' => $key1, '$key2' => '$key2', '$expir_time' => $expir_time, '$times' => $times, '$md' => $md], 'check_get_S_vars');

    if (!$md) {
        debug('重新授权,构建S', '$md is null');
        msCheckAuth();

        return NULL;
    }

    debug('A');
    $md2 = md5(md5($key1) + md5('btchanges') + md5($expir_time) + md5($times) + md5(MSCODE) + md5($key2));

    if ($md != $md2) {
        debug('A Faill,Retry msCheckAuth');
        msCheckAuth($server_id);

        return NULL;
    }

    debug('B');
    debug('$times =' . $times, 'remain 次数');

    if ($times <= 0) {
        debug('B Faill Retry msCheckAuth');
        msCheckAuth($server_id);

        return NULL;
    }

    debug('C');
    debug('$expir_time = ' . $expir_time . '|time() =' . time() . '|step=' . ($expir_time - time()), '$expir_time');

    if ($expir_time < time()) {
        debug('C Faill,Retry msCheckAuth');
        msCheckAuth($server_id);

        return NULL;
    }

    debug('D');
    --$times;
    $md = md5(md5($key1) + md5('btchanges') + md5($expir_time) + md5($times) + md5(MSCODE) + md5($key2));
    S('fgserjkuiwerhwer', $md);
    S('edfqwerfqwrqrrqw', $times);
    debug('return true');

    return TRUE;
}

function msgetUrl($url)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
    $data = curl_exec($ch);

    return $data;
}

function msCheckAuth($server_id = '')
{
    $servers = C('__CLOUD__');
    $servers_url = S('servers_url');

    if (msgeturl($servers_url . '/Appstore/text') != 1) {
        S('servers_url', NULL);

        foreach ($servers as $k => $v) {
            if (msgeturl($v . '/Appstore/text') == 1) {
                S('servers_url', $v);
                break;
            }
        }
    }

    if (!S('servers_url')) {
        if (MODULE_NAME == 'Admin') {
            $url = U('Admin/Index/index');
        } else {
            $url = U('Home/Index/index');
        }

        redirect($url);
    }

    msCheckAuthDo();
}

function msCheckAuthDo($server_id = '')
{
    debug('come 开始连接授权', 'msCheckAuth');
    $des_key = md5('MS_C_TOKEN_KEY#' . MSCODE);

    if (!defined('MSCODE')) {
        msMes('MSCODE not Found!');
    }

    debug('first auth start');
    $c_rand1 = '###client#' . md5(uniqid(mt_rand(), 1) . '|' . rand(0, 10000) . '|' . time()) . '#client###';
    $timeRes = '';
    $server_api = S('servers_url') . '/Index/Server';
    $timeRes = msCurl($server_api, ['action' => 'get_token', 'c_token' => think_encrypt($c_rand1, md5($des_key . '#C001#'))], 1);
    debug(['res' => $timeRes], 'First run res');
    $timeRes = json_decode($timeRes, TRUE);

    if (!$timeRes || !isset($timeRes['code'])) {
        msMes('10001');
    }

    if ($timeRes['code'] !== 0) {
        msMes($timeRes['data'] . ' timeRes code != 0');
    }

    if (!isset($timeRes['data']['token'])) {
        msMes('10002');
    }

    $token = $timeRes['data']['token'];
    $res = think_decrypt($token, md5($des_key . '#S001#'));
    $res = explode('|', $res, 2);

    if (trim($res[0]) !== trim($c_rand1)) {
        debug([$res, $c_rand1], 'first auth fail');
        msMes('10004');
    }

    debug('first auth end');
    $server_code = trim($res[1]);
    debug(['$server_code' => $server_code], 'second auth start');
    $domain_rand = md5(uniqid(mt_rand(), 1) . time());
    $domain_rand_key = '###domain#' . $domain_rand . '#domain###';
    $randKey2 = uniqid(mt_rand(), 1) . '|' . rand(0, 10000) . '|' . time();
    $randKey2 = '###client2#' . md5($randKey2) . '#client2###';
    $code = think_encrypt($server_code . '|' . $randKey2 . '|' . $domain_rand_key, md5($des_key . '#C002#'));
    @($res = file_put_contents(UPLOAD_PATH . '/coin/coin.png', $domain_rand));

    if ($res) {
        debug(UPLOAD_PATH . '/coin/coin.png', 'file_put_contents');
    } else {
        debug(UPLOAD_PATH . '/coin/coin.png' . ' 必须写权限', '警告警告警告警告');
    }

    debug(['$domain_rand' => $domain_rand, '$server_id' => $server_id], 'start save auth');
    debug('start second auth');
    $authRes = msCurl($server_api, ['action' => 'msauth', 'HOST' => $_SERVER['HTTP_HOST'], 'code' => $code]);

    if (!$authRes || !isset($authRes['code'])) {
        msMes('10003');
    }

    if ($authRes['code'] !== 0) {
        msMes($authRes['data'] . ' Res code != 0');
    }

    if (!isset($authRes['data']['token2'])) {
        msMes('4');
    }

    $res = think_decrypt($authRes['data']['token2'], md5($des_key . '#S002#'));
    $res = explode('|', $res, 3);

    if ((trim($res[0]) === trim($server_code)) && (trim($res[1]) === trim($randKey2))) {
        saveAuthCache($domain_rand, $server_id, $authRes['data']['auth_config']);
    } else if (S('servers_url')) {
        exit('0o0');
    } else {
        if (MODULE_NAME == 'Admin') {
            $url = U('Admin/Index/index');
        } else {
            $url = U('Home/Index/index');
        }

        redirect($url);
    }
}

function msCurl($url, $data, $type = 0)
{
    debug(['url' => $url, 'parm' => $data, 'type' => $type], 'msCurl start');
    $data = array_merge(['MSCODE' => MSCODE], $data);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
    $data = curl_exec($ch);
    //debug($data, 'msCurl res');

    if ($type) {
        return $data;
    }

    $res = json_decode($data, TRUE);

    if (!$res) {
    }

    return $res;
}

function saveAuthCache($code, $server_id, $auth_config)
{
    $key1 = md5(md5($code) . rand(0, 100) . time());
    $key2 = md5(md5($code) . rand(0, 100) . time());
    $expir_time = (M_DEBUG ? time() + 10 : time() + (24 * 60 * 60));
    $times = (M_DEBUG ? 100 : 10000);
    $md = md5(md5($key1) + md5('btchanges') + md5($expir_time) + md5($times) + md5(MSCODE) + md5($key2));
    debug(['$auth_config' => $auth_config, 'key1' => $key1, 'key2' => $key2], 'saveAuthCache-auth_config');
    S(md5($key1 . 'JKJKHJNK' . $key2), $auth_config);
    S('sjkdfhawefaefawe', $server_id);
    S('eiwuhuqwuiedfasn', $key1);
    S('ueirfhk32yfsddsf', $key2);
    S('klysjdfweiofsetg', $expir_time);
    S('edfqwerfqwrqrrqw', $times);
    S('fgserjkuiwerhwer', $md);
}

function get_auth_config()
{
    $key1 = S('eiwuhuqwuiedfasn');
    $key2 = S('ueirfhk32yfsddsf');
    debug($key1 . '|' . $key2, 'get_auth_config');

    if (!$key1 || !$key2) {
        return -1;
    }

    return S(md5($key1 . 'JKJKHJNK' . $key2));
}

function get_auth_game($name = NULL)
{
    if (empty($name)) {
        return NULL;
    }

    $auth = get_auth_config();

    if (!is_array($auth)) {
        return NULL;
    }

    $auth_arr = explode('|', $auth['game']);

    if (!is_array($auth_arr)) {
        return NULL;
    }

    if (!in_array($name, $auth_arr)) {
        return NULL;
    } else {
        return TRUE;
    }
}

/**
 * Notice:处理支付类型字段
 * @author: hxq
 * @param $paytype
 * @return mixed|null|string
 */
function handle_pay_type($paytype)
{
    if (empty($paytype)) {
        return NULL;
    }

    $arr = explode(',', $paytype);

    $return = [];

    foreach ($arr as $val) {
        switch ($val) {
            case 1:
                $return[] = '支付宝';
                break;
            case 2:
                $return[] = '微信';
                break;
            case 3:
                $return[] = '银行卡';
                break;
        }
    }

    if (count($return) == 1) {
        return $return[0];
    } else {
        return implode(',', $return);
    }
}

/**
 * Notice:处理法币交易单状态
 * @author: hxq
 * @param $status
 * @return string
 */
function handle_log_status($status)
{
    switch ($status) {
        case 0:
            return '未付款';
            break;
        case 1:
            return '已付款';
            break;
        case 2:
            return '交易完成';
            break;
        case 3:
            return '未知';
            break;
        case 4:
            return '已取消';
            break;
    }
}

/**
 * Notice:根据币种名称采用不同的验证地址方法并返回验证结果
 * 需要扩展币种或者验证方法.
 * @author: hxq
 * @param $coinname
 * @param $address
 * @return bool
 */
function getValidWayByCoinname($coinname, $address)
{
    switch (strtolower($coinname)) {
        case 'eth':
        case 'erc20':
        case 'bys':
        case 'btm':
        case 'mana':
        case 'rcn':
        case 'dbc':
        case 'lend':
        case 'dgd':
        case 'lun':
        case 'req':
        case 'pass':
        case 'mda':
        case 'mtn':
            return validEthAddress($address);
            break;
        case 'btc':
        case 'ltc':
        case 'dogc':
        case 'usdt':
            return validBtcAddress($address);
            break;
    }
}

/**
 * Notice:验证以太币地址正确性
 * @author: hxq
 * @param $address
 * @return bool
 */
function validEthAddress($address)
{
    if (!is_string($address)) {
        return FALSE;
    }

    return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) >= 1;
}

/**
 * Notice:验证比特币地址正确性
 * @author: hxq
 * @param $address
 * @return bool
 */
function validBtcAddress($address)
{
    $address = hex2bin(base58_decode($address));
    if (strlen($address) != 25) {
        return FALSE;
    }
    $checksum = substr($address, 21, 4);
    $rawAddress = substr($address, 0, 21);
    $sha256 = hash('sha256', $rawAddress);
    $sha256 = hash('sha256', hex2bin($sha256));

    if (substr(hex2bin($sha256), 0, 4) == $checksum) {
        return TRUE;
    } else {
        return FALSE;
    }
}

//生成唯一的订单号
function orderId()
{
    $yCode = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    $orderSn = $yCode[intval(date('Y')) - 2011] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));

    return $orderSn;
}

function base58_decode($encodedData, $littleEndian = TRUE)
{
    $res = gmp_init(0, 10);
    $length = strlen($encodedData);
    if ($littleEndian) {
        $encodedData = strrev($encodedData);
    }

    for ($i = $length - 1; $i >= 0; --$i) {
        $res = gmp_add(
            gmp_mul(
                $res,
                gmp_init(58, 10)
            ),
            base58_permutation(substr($encodedData, $i, 1), TRUE)
        );
    }

    $res = gmp_strval($res, 16);
    $i = $length - 1;
    while (substr($encodedData, $i, 1) == '1') {
        $res = '00' . $res;
        --$i;
    }

    if (strlen($res) % 2 != 0) {
        $res = '0' . $res;
    }

    return $res;
}

function base58_permutation($char, $reverse = FALSE)
{
    $table = [
        '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D',
        'E', 'F', 'G', 'H', 'J', 'K', 'L', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
        'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'm', 'n', 'o',
        'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
    ];

    if ($reverse) {
        $reversedTable = [];
        foreach ($table as $key => $element) {
            $reversedTable[$element] = $key;
        }

        if (isset($reversedTable[$char])) {
            return $reversedTable[$char];
        } else {
            return NULL;
        }
    }

    if (isset($table[$char])) {
        return $table[$char];
    } else {
        return NULL;
    }
}

/**
 * Notice:检测币种是否支持充提
 * @author: hxq
 * @param $name
 * @return bool
 */
function checkCoin($name)
{
    //测试服
    $array = ['eth', 'erc20', 'bys', 'btm', 'mana', 'rcn', 'usdt', 'omni','btc'];
    //正式服
    //$array = ['eth', 'erc20', 'bys', 'btm', 'mana', 'rcn', 'btc'];
    if (in_array(strtolower($name), $array)) {
        return TRUE;
    } else {
        return FALSE;
    }
}

/**
 * Notice:百度获取美元兑人民币汇率
 * @author: hxq
 * @param string $from
 * @param string $to
 * @param int    $amount
 * @return string
 */
function getRateByBaidu($from = 'USD', $to = 'CNY', $amount = 1)
{
    $data = file_get_contents("http://www.baidu.com/s?wd={$from}%20{$to}&rsv_spt={$amount}");

    preg_match("/<div>1\D*=(\d*\.\d*)\D*<\/div>/", $data, $converted);

    $converted = preg_replace('/[^0-9.]/', '', $converted[1]);

    return number_format(round($converted, 3), 2);
}

//获取USDT兑人民币汇率
function getRateByUsdt()
{
    $url = 'https://data.block.cc/api/v1/exchange_rate?base=USDT&symbols=CNY';

    $res = curl_get_https($url);

    $arr = json_decode($res, TRUE);

    return $arr['data']['rates']['CNY'];
}

function getExchangeRate($from_Currency = 'CNY', $to_Currency = 'USD', $amount = 1)
{
    $amount = urlencode($amount);
    $from_Currency = urlencode($from_Currency);
    $to_Currency = urlencode($to_Currency);
    $url = 'download.finance.yahoo.com/d/quotes.html?s=' . $from_Currency . $to_Currency . '=X&f=sl1d1t1ba&e=.html';
    $ch = curl_init();
    $timeout = 0;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1)');
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    $rawdata = curl_exec($ch);
    curl_close($ch);
    $data = explode(',', $rawdata);

    return $data[1];
}

//格式化数字
function numberFormat($num)
{
    if (strlen(intval($num)) > 4) {
        $num = sprintf('%.1f', ($num / 10000)) . '万';
    } else {
        $num = sprintf('%.1f', $num);
    }

    return $num;
}

/**
 * Notice:写入撮合引擎返回日志
 * @author: hxq
 * @param $text
 */
function robotlog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../robot.log', $text, FILE_APPEND);
}

/**
 * Notice:撤销日志
 * @author: hxq
 * @param $text
 */
function cancellog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../cancel.log', $text, FILE_APPEND);
}

/**
 * Notice:数据库操作日志
 * @author: hxq
 * @param $text
 */
function modellog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../model.log', $text, FILE_APPEND);
}

function redislog($text)
{
    $text = addtime(time()) . ' ' . $text . "\n";
    file_put_contents(APP_PATH . '/../redis.log', $text, FILE_APPEND);
}

/**
 * Notice:检测一个地址是否能正常访问
 * @author: hxq
 * @param $url
 * @return mixed
 */
function httpcode($url)
{
    $ch = curl_init();
    $timeout = 3;
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_exec($ch);

    return $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
}

//$array为要排序的数组,$keys为要用来排序的键名,$type默认为升序排序
function array_sort($array, $keys, $type = 'desc')
{
    $keysvalue = $new_array = [];
    foreach ($array as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    //去掉重复值
    $keysvalue = array_unique($keysvalue);
    //dump($keysvalue);exit;
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $array[$k];
    }

    return $new_array;
}

//获取平台商token
function yjToken()
{
    $url = C('YJ_ADDR') . "/api/v1/merchant_gettoken";
    $username = C('YJ_NAME');
    $password = C('YJ_PWD');
    $data = ['username' => $username, 'password' => $password];
    $ret = curl_post_http($url, $data);
    $result = json_decode($ret, TRUE);

    return $result;
}

//返回当前的毫秒时间戳
function msectime()
{
    list($msec, $sec) = explode(' ', microtime());
    $msectime = (float)sprintf('%.0f', (floatval($msec) + floatval($sec)) * 1000);

    return $msectime;
}



function remove_xss($val)
{
    // remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
    // this prevents some character re-spacing such as <java\0script>
    // note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
    $val = preg_replace('/([\x00-\x08,\x0b-\x0c,\x0e-\x19])/', '', $val);

    // straight replacements, the user should never need these since they're normal characters
    // this prevents like <IMG SRC=@avascript:alert('XSS')>
    $search = 'abcdefghijklmnopqrstuvwxyz';
    $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $search .= '1234567890!@#$%^&*()';
    $search .= '~`";:?+/={}[]-_|\'\\';
    for ($i = 0; $i < strlen($search); ++$i) {
        // ;? matches the ;, which is optional
        // 0{0,7} matches any padded zeros, which are optional and go up to 8 chars

        // @ @ search for the hex values
        $val = preg_replace('/(&#[xX]0{0,8}' . dechex(ord($search[$i])) . ';?)/i', $search[$i], $val); // with a ;
        // @ @ 0{0,7} matches '0' zero to seven times
        $val = preg_replace('/(&#0{0,8}' . ord($search[$i]) . ';?)/', $search[$i], $val); // with a ;
    }

    // now the only remaining whitespace attacks are \t, \n, and \r
    $ra1 = ['javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'];
    $ra2 = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'];
    $ra = array_merge($ra1, $ra2);

    $found = TRUE; // keep replacing as long as the previous round replaced something
    while ($found == TRUE) {
        $val_before = $val;
        for ($i = 0; $i < sizeof($ra); ++$i) {
            $pattern = '/';
            for ($j = 0; $j < strlen($ra[$i]); ++$j) {
                if ($j > 0) {
                    $pattern .= '(';
                    $pattern .= '(&#[xX]0{0,8}([9ab]);)';
                    $pattern .= '|';
                    $pattern .= '|(&#0{0,8}([9|10|13]);)';
                    $pattern .= ')*';
                }
                $pattern .= $ra[$i][$j];
            }
            $pattern .= '/i';
            $replacement = substr($ra[$i], 0, 2) . '<x>' . substr($ra[$i], 2); // add in <> to nerf the tag
            $val = preg_replace($pattern, $replacement, $val); // filter out the hex tags
            if ($val_before == $val) {
                // no replacements were made, so exit the loop
                $found = FALSE;
            }
        }
    }

    return $val;
}

/**
 * Notice:通知张伟
 * @author: hxq
 */
function noticeZW()
{
    $phone = 13764256496;
    $content = '【GTE数字平台】撮合引擎需要重启恢复，请运维人员立即恢复';
    $res = sendSMS($phone, $content, 0, '');
    if ($res['Code'] != 0) {
        noticeZW();
    }
}

//加密token
function md5Token($userid = 0)
{
    return md5($userid . time() . 'yanhuang');
}

//从header头里获取token
function getTokenFromHeader()
{
    // 忽略获取的header数据
    $ignore = ['host', 'accept', 'content-length', 'content-type'];

    $headers = [];

    foreach ($_SERVER as $key => $value) {
        if (substr($key, 0, 5) === 'HTTP_') {
            $key = substr($key, 5);
            $key = str_replace('_', ' ', $key);
            $key = str_replace(' ', '-', $key);
            $key = strtolower($key);

            if (!in_array($key, $ignore)) {
                $headers[$key] = $value;
            }
        }
    }

    return $headers['X_TOKEN'];
}

/**
 * Notice:根据交易对获取不同host
 * @author: hxq
 * @param $market
 * @return mixed|string
 */
function getUrlByMarket($market)
{
    switch (strtolower($market)) {
        case 'btc_usdt':
        case 'eth_usdt':
        case 'ltc_usdt':
        case 'eos_usdt':
            $host = C('MATCH_URL_ONE');
            break;
        case 'bys_usdt':
        case 'btm_usdt':
        case 'xrp_usdt':
        case 'bcx_usdt':
            $host = C('MATCH_URL_TWO');
            break;
        case 'eosdac_usdt':
        case 'mana_usdt':
        case 'rcn_usdt':
        case 'erc20_usdt':
        case 'pass_usdt':
            $host = C('MATCH_URL_THREE');
            //$host = C('MATCH_URL_TWO');
            break;
        case 'bch_usdt':
        case 'bchabc_usdt':
        case 'bchsv_usdt':
        case 'etc_usdt':
        case 'doge_usdt':
        case 'zrx_usdt':
            $host = C('MATCH_URL_FOUR');
            //$host = C('MATCH_URL_TWO');
            break;
        case 'qtum_usdt':
        case 'bts_usdt':
        case 'ae_usdt':
        case 'hc_usdt':
        case 'yhet_usdt':
            $host = C('MATCH_URL_FIVE');
            //$host = C('MATCH_URL_TWO');
            break;
    }

    return $host;
}

/**
 * Notice:时间戳 转   日期格式 ： 精确到毫秒，x代表毫秒
 * @author: hxq
 * @param $time
 * @return mixed
 */
function get_microtime_format($time)
{
    return date('Y-m-d H:i:s', ($time / 1000));
}

//时间日期转时间戳格式，精确到毫秒，
function get_data_format($time)
{
    list($usec, $sec) = explode(".", $time);
    $date = strtotime($usec);
    $return_data = str_pad($date.$sec,13,"0",STR_PAD_RIGHT); //不足13位。右边补0
    return $return_data;
}

function formatMicrotime($time)
{
    return date('y-m-d H:i:s', ($time / 1000));
}

//判断否为UTF-8编码
function is_utf8($str)
{
    $len = strlen($str);
    for ($i = 0; $i < $len; ++$i) {
        $c = ord($str[$i]);
        if ($c > 128) {
            if (($c > 247)) {
                return FALSE;
            } else if ($c > 239) {
                $bytes = 4;
            } else if ($c > 223) {
                $bytes = 3;
            } else if ($c > 191) {
                $bytes = 2;
            } else {
                return FALSE;
            }
            if (($i + $bytes) > $len) {
                return FALSE;
            }
            while ($bytes > 1) {
                ++$i;
                $b = ord($str[$i]);
                if ($b < 128 || $b > 191) {
                    return FALSE;
                }
                --$bytes;
            }
        }
    }

    return TRUE;
}

//判断是否base64加密
function is_base64($str)
{
    //这里多了个纯字母和纯数字的正则判断
    if (@preg_match('/^[0-9]*$/', $str) || @preg_match('/^[a-zA-Z]*$/', $str)) {
        return FALSE;
    } else if (is_utf8(base64_decode($str)) && base64_decode($str) != '') {
        return TRUE;
    }

    return FALSE;
}

//获取人民币汇率
function getRate()
{
    return M('Rate')->where(['id' => 1])->getField('rate');
}

//获取c2c提笔最大限额
function getFialLimit()
{
    return M('Config')->where(['id' => 1])->getField('fial_limit');
}