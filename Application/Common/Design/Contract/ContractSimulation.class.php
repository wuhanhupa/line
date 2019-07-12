<?php

namespace Common\Design\Contract;

//合约模拟
class ContractSimulation
{
    public $host = '192.168.0.71:18100';

    //合约版
    //public $host = '192.168.8.65:18100';

    /**
     * Notice: 计算当前杠杆数下的需要的委托保证金
     * @author: hxq
     * @param $amount BigDecimal 合约数 10000
     * @param $pair String 交易对 小写 btc_usdt
     * @param $leveraged BigDecimal 杠杆数 10.05
     * @return mixed
     */
    public function calc_order_margin($amount, $pair, $leveraged)
    {
        $url = $this->host . '/v1/contract/calc_order_margin';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'amount' => $amount,
            'pair' => $pair,
            'leveraged' => $leveraged,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 新增委托单
     * @author: hxq
     * @param 必填|int    $user_id 用户id
     * @param 必填|String $pair 交易对 小写 btc_usdt
     * @param 必填|int    $bid_flag 1.买 0.卖
     * @param 必填|float  $leveraged BigDecimal 杠杆数 10.05
     * @param 必填|float  $amount BigDecimal 数量 1.1234
     * @param 必填|float  $price BigDecimal 价格 6500.5
     * @param 必填|int    $order_type 1.限价 2.市价
     * @param 必填|int    $orderType 1:诱单;2:真单;3:三方委托单
     * @return mixed
     */
    public function create_order($user_id, $pair, $bid_flag, $leveraged, $amount, $price, $order_type, $orderType = 2)
    {
        $url = $this->host . '/v1/contract/create_order';
        //mlog($url);
        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$user_id,
            'pair' => (string)$pair,
            'bid_flag' => (int)$bid_flag,
            'amount' => $amount,
            'leveraged' => $leveraged,
            'price' => $price,
            'order_type' => (int)$order_type,
            'type' => (int)$orderType,
            't' => (string)$t,
            'token' => (string)$token
        ];

        $json = curl_post_http($url, $data);

        mlog($json);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 获取委托单
     * @author: hxq
     * @param null $order_id
     * @return mixed
     */
    public function get_order($order_id)
    {
        $url = $this->host . '/v1/contract/get_order';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = TRUE; //必填 String 验证字符串

        $data = [
            'order_id' => (int)$order_id,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_get_https($url . '?' . http_build_query($data));

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 撤销委托
     * @author: hxq
     * @param null $order_id
     * @return mixed
     */
    public function cancel_order($order_id = NULL)
    {
        $url = $this->host . '/v1/contract/cancel_order';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'order_id' => (int)$order_id,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 获取委托单列表,分页
     * @author: hxq
     * @param null $order_id 选填 long 订单id
     * @param      $user_id 必填 int 用户id
     * @param      $pair 选填 String 交易对 小写 btc_usdt 支持多个交易对,分隔
     * @param      $bid_flag  选填 int 1.买 0.卖
     * @param      $leveraged 选填 BigDecimal 杠杆数 10.05
     * @param      $order_type 选填 int 1.限价 2.市价
     * @param int  $current_page 必填 long 当前页码
     * @param int  $page_size 必填 long 页行数
     * @return mixed
     */
    public function get_order_page($user_id, $current_page = 1, $page_size = 20, $order_id = NULL, $pair = NULL, $bid_flag = NULL, $leveraged = NULL, $order_type = NULL)
    {
        $url = $this->host . '/v1/contract/get_order_page';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$user_id,
            'current_page' => (int)$current_page,
            'page_size' => (int)$page_size,
            't' => (string)$t,
            'token' => (string)$token,
        ];
        if (isset($order_id)) {
            $data['order_id'] = (int)$order_id;
        }
        if (isset($pair)) {
            $data['pair'] = (string)$pair;
        }
        if (isset($bid_flag)) {
            $data['bid_flag'] = (int)$bid_flag;
        }
        if (isset($leveraged)) {
            $data['leveraged'] = (int)$leveraged;
        }
        if (isset($order_type)) {
            $data['order_type'] = (int)$order_type;
        }

        $json = curl_get_https($url . '?' . http_build_query($data));

        $res = json_decode($json, TRUE);

        return $res;
    }

    //获取历史委托单列表,分页
    public function get_history_order_page($user_id, $current_page = 1, $page_size = 20, $order_id = NULL, $pair = NULL, $bid_flag = NULL, $leveraged = NULL, $order_type = NULL)
    {
        $url = $this->host . '/v1/contract/get_history_order_page';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$user_id,
            'current_page' => (int)$current_page,
            'page_size' => (int)$page_size,
            't' => (string)$t,
            'token' => (string)$token,
        ];
        if (isset($order_id)) {
            $data['order_id'] = (int)$order_id;
        }
        if (isset($pair)) {
            $data['pair'] = (string)$pair;
        }
        if (isset($bid_flag)) {
            $data['bid_flag'] = (int)$bid_flag;
        }
        if (isset($leveraged)) {
            $data['leveraged'] = (int)$leveraged;
        }
        if (isset($order_type)) {
            $data['order_type'] = (int)$order_type;
        }

        $json = curl_get_https($url . '?' . http_build_query($data));

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 平仓结算
     * @author: hxq
     * @param $position_id 仓位id
     * @param $order_type 1.限价 2.市价
     * @param $price 价格
     * @return mixed
     */
    public function liquidation($position_id, $order_type, $price)
    {
        $url = $this->host . '/v1/contract/liquidation';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'position_id' => (int)$position_id,
            'order_type' => (int)$order_type,
            'price' => $price,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);
        //mlog($json);
        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 追加仓位保证金
     * @author: hxq
     * @param $position_id 仓位id
     * @param $user_id 用户id
     * @param $pair 交易对
     * @param $add_margin 追加保证金 +新增 -减少
     * @return mixed
     */
    public function add_pos_margin($position_id, $user_id, $pair, $add_margin)
    {
        $url = $this->host . '/v1/contract/add_pos_margin';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'position_id' => (int)$position_id,
            'user_id' => (int)$user_id,
            'pair' => (string)$pair,
            'add_margin' => $add_margin,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        //mlog($json);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 获取仓位信息
     * @author: hxq
     * @param        $userid
     * @param string $pair
     * @return mixed
     */
    public function get_position($userid, $pair = 'btc_usdt')
    {
        $url = $this->host . '/v1/contract/get_position';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$userid,
            'pair' => (string)$pair,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        //mlog($json);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 调整杠杆
     * @author: hxq
     * @param        $userid
     * @param string $pair
     * @param        $leveraged
     * @return mixed
     */
    public function change_leveraged($userid, $pair = 'btc_usdt', $leveraged)
    {
        $url = $this->host . '/v1/contract/change_leveraged';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$userid,
            'pair' => (string)$pair,
            'leveraged' => $leveraged,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        //mlog($json);

        $res = json_decode($json, TRUE);

        return $res;
    }

    /**
     * Notice: 查询历史仓位
     * @author: hxq
     * @param        $userid
     * @param string $pair
     * @param int    $current_page
     * @param int    $page_size
     * @return mixed
     */
    public function get_history_pos_page($userid, $pair = 'btc_usdt', $current_page = 1, $page_size = 10)
    {
        $url = $this->host . '/v1/contract/get_history_pos_page';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$userid,
            'pair' => (string)$pair,
            'current_page' => (int)$current_page,
            'page_size' => (int)$page_size,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        //mlog($json);

        $res = json_decode($json, TRUE);

        return $res;
    }

    //最大可用张数
    public function max_order_amount($userid, $pair, $leveraged, $bid_flag)
    {
        $url = $this->host . '/v1/contract/max_order_amount';

        $t = msectime(); // 必填 long 请求时间戳(毫秒):1536837797123
        $token = '123'; //必填 String 验证字符串

        $data = [
            'user_id' => (int)$userid,
            'pair' => (string)$pair,
            'leveraged' => $leveraged,
            'bid_flag' => (int)$bid_flag,
            't' => (string)$t,
            'token' => (string)$token,
        ];

        $json = curl_post_http($url, $data);

        $res = json_decode($json, TRUE);

        return $res;
    }
}
