<?php

namespace Mapi\Controller;

class IndexController extends CommonController
{
    public function index()
    {
        echo 'OK';
    }

    public function up()
    {
        $User = M('User')->getDbFields();

        if (!in_array('token', $User)) {
            echo 'add token field to user table;';
            $end = end($User);
            M()->execute('ALTER TABLE `btchanges_user` ADD COLUMN `token` VARCHAR(50) NULL AFTER `'.$end.'`;');
        } else {
            echo 'add had add token field;';
        }
    }

    public function initinfo()
    {
        $info = array();
        $info['WITHDRAW_NOTICE'] = '会员您好,提现金额在￥100--￥10000的只能在每个星期的星期二和星期六申请提现,提现金额在￥10000以上的每天都可以申请提现,提现最低￥100,最高￥10000,同时提现时需要扣除一定比例作为手续费';
        $info['CHARGE_NOTICE'] = '用户您好,RMB充值范围为￥10--￥10000,目前支持支付宝手机扫码充值和网页端充值,任选其一,充值时请在备注里填写好正确的订单编号,系统自动到账.';
        $info['WEB_NAME'] = C('WEB_NAME');
        $info['WEB_TITLE'] = C('WEB_TITLE');
        $info['WEB_ICP'] = C('WEB_ICP');
        $info['INDEX_IMG'] = '';
        $News = M('Article')->where(array('type' => 'news'))->select();

        foreach ($News as $val) {
            $title = (50 < mb_strlen($val['title']) ? mb_substr($val['title'], 0, 50, 'utf-8').'...' : $val['title']);
            $info['News'][] = array('id' => $val['id'], 'title' => $title);
        }

        $info['charge_account'] = array(
            'alipay' => array('bank' => '支付宝', 'name' => "\t".'动说科技', 'card_num' => "\t".'123456@alipay.com'),
            'bank' => array('bank' => '中国银行', 'name' => "\t".'动说科技', 'card_num' => '8888 8888 8888'),
            );
        $myczType = M('MyczType')->where(array('status' => 1))->select();

        foreach ($myczType as $k => $v) {
            $myczTypeList[] = array('type' => $v['name'], 'title' => $v['title']);
        }

        $info['myczTypeList'] = $myczTypeList;
        $this->ajaxShow($info);
    }
}
