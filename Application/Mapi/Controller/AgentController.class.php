<?php

namespace Mapi\Controller;

/**
 * 代理商相关
 * Class AgentController
 * @package Mapi\Controller
 */
class AgentController extends CommonController
{
    /**
     * Notice: 佣金计算，每日执行一次
     * 根据卖单进行计算
     * @author: hxq
     */
    public function CommissionCalculation()
    {
        $where['status'] = 1;
        $logs = M('TradeLog')->where($where)->select();
    }

    //初始化计算
    public function InitializationCalculation()
    {
        $where['userid'] = ['not in', '46,47'];
        $where['peerid'] = ['not in', '46,47'];
        $logs = M('TradeLog')->where($where)->select();
        //佣金计算以卖单结算
        foreach ($logs as $log) {
            //如果是买单，查找peerid用户，否则查找userid用户
            if ($log['type'] == 1) {
                $userid = $log['peerid'];
            } else if ($log['type'] == 2) {
                $userid = $log['userid'];
            } else {
                echo '成交记录type类型错误！';
                echo "\n";
                continue;
            }
            //查找用户
            $user = M('User')->where(['id' => $userid])->find();
            if (!$user) {
                echo '用户' . $userid . '不存在';
                echo "\n";
                continue;
            }
            if (!$user['sharecode']) {
                echo '用户' . $user['username'] . '没有sharecode';
                echo "\n";
                continue;
            }
            //查找用户关联的经纪人
            $sql = 'select rid from admin_users where sharecode=\'' . $user['sharecode'] . '\'';
            $agent = M()->query($sql);
            //如果该经纪人存在
            if (!$agent) {
                echo '经纪人' . $agent['rid'] . '不存在';
                echo "\n";
                continue;
            }
            //查找代理商
            $psql = 'select id from admin_users where id=\'' . $agent['rid'] . '\'';
            $merchants = M()->query($psql);
            //如果代理商存在
            if (!$merchants) {
                echo '代理商' . $merchants['id'] . '不存在';
                echo "\n";
                continue;
            }
            //查找代理商等级（角色）
            $rsql = 'select role_id from admin_role_users where user_id=\'' . $merchants['id'] . '\'';
            $rid = M()->query($rsql);
            //如果角色存在
            if (!$rid) {
                echo '角色关联关系' . $rid['role_id'] . '不存在';
                echo "\n";
                continue;
            }
            $rolesql = 'select slug from admin_roles where id=\'' . $rid['role_id'] . '\'';
            $slug = M()->query($rolesql);
            //代理商角色是否存在
            if (!$slug) {
                echo '代理商角色' . $slug['slug'] . '不存在';
                echo "\n";
                continue;
            }
            //一级代理商
            if ($slug['slug'] == 101) {
                $proportion = 0.8;
            }
            //二级代理商
            if ($slug['slug'] == 102) {
                $proportion = 0.5;
            }
            //三级代理商
            if ($slug['slug'] == 103) {
                $proportion = 0.4;
            }
            //计算佣金(USDT)
            $comm = bcmul($log['mum'], 0.003 * $proportion, 8);
            echo $comm;
        }
    }
}