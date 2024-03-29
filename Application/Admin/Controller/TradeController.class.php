<?php

namespace Admin\Controller;

use Think\Page;

class TradeController extends AdminController
{
    public function index($field = NULL, $name = NULL, $market = NULL, $status = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($market) {
            $where['market'] = $market;
        }

        if ($status) {
            $where['status'] = $status;
        }

        $where['userid'] = ['not in', '46,47'];
        $count = M('Trade')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Trade')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function chexiao($id = NULL)
    {
        $rs = D('Trade')->chexiao($id);

        if ($rs[0]) {
            $this->success($rs[1]);
        } else {
            $this->error($rs[1]);
        }
    }

    public function log($field = NULL, $name = NULL, $market = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else if ($field == 'peername') {
                $where['peerid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($market) {
            $where['market'] = $market;
        }
        $where['userid'] = ['not in', '46,47'];
        $where['peerid'] = ['not in', '46,47'];
        $count = M('TradeLog')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('TradeLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['peername'] = M('User')->where(['id' => $v['peerid']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function chat($field = NULL, $name = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Chat')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Chat')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function chatStatus($id = NULL, $type = NULL, $moble = 'Chat')
    {
        if (APP_DEMO) {
            $this->error('测试站暂时不能修改！');
        }

        if (empty($id)) {
            $this->error('参数错误！');
        }

        if (empty($type)) {
            $this->error('参数错误1！');
        }

        if (strpos(',', $id)) {
            $id = implode(',', $id);
        }

        $where['id'] = ['in', $id];

        switch (strtolower($type)) {
            case 'forbid':
                $data = ['status' => 0];
                break;

            case 'resume':
                $data = ['status' => 1];
                break;

            case 'repeal':
                $data = ['status' => 2, 'endtime' => time()];
                break;

            case 'delete':
                $data = ['status' => -1];
                break;

            case 'del':
                if (M($moble)->where($where)->delete()) {
                    $this->success('操作成功！');
                } else {
                    $this->error('操作失败！');
                }

                break;

            default:
                $this->error('操作失败！');
        }

        if (M($moble)->where($where)->save($data)) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    public function comment($field = NULL, $name = NULL, $coinname = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        if ($coinname) {
            $where['coinname'] = $coinname;
        }

        $count = M('CoinComment')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('CoinComment')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function commentStatus($id = NULL, $type = NULL, $moble = 'CoinComment')
    {
        if (APP_DEMO) {
            $this->error('测试站暂时不能修改！');
        }

        if (empty($id)) {
            $this->error('参数错误！');
        }

        if (empty($type)) {
            $this->error('参数错误1！');
        }

        if (strpos(',', $id)) {
            $id = implode(',', $id);
        }

        $where['id'] = ['in', $id];

        switch (strtolower($type)) {
            case 'forbid':
                $data = ['status' => 0];
                break;

            case 'resume':
                $data = ['status' => 1];
                break;

            case 'repeal':
                $data = ['status' => 2, 'endtime' => time()];
                break;

            case 'delete':
                $data = ['status' => -1];
                break;

            case 'del':
                if (M($moble)->where($where)->delete()) {
                    $this->success('操作成功！');
                } else {
                    $this->error('操作失败！');
                }

                break;

            default:
                $this->error('操作失败！');
        }

        if (M($moble)->where($where)->save($data)) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    public function market($field = NULL, $name = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Market')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Market')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function marketEdit($id = NULL)
    {
        if (empty($_POST)) {
            if (empty($id)) {
                $this->data = [];
            } else {
                $this->data = M('Market')->where(['id' => $id])->find();
            }

            $this->display();
        } else {
            if (APP_DEMO) {
                $this->error('测试站暂时不能修改！');
            }

            $round = [0, 1, 2, 3, 4, 5, 6, 7, 8];

            if (!in_array($_POST['round'], $round)) {
                $this->error('小数位数格式错误！');
            }

            if ($_POST['id']) {
                $rs = M('Market')->save($_POST);
            } else {
                $_POST['name'] = $_POST['sellname'] . '_' . $_POST['buyname'];
                unset($_POST['buyname']);
                unset($_POST['sellname']);

                if (M('Market')->where(['name' => $_POST['name']])->find()) {
                    $this->error('市场存在！');
                }

                $rs = M('Market')->add($_POST);
            }

            if ($rs) {
                $this->success('操作成功！');
            } else {
                $this->error('操作失败！');
            }
        }
    }

    public function marketStatus($id = NULL, $type = NULL, $moble = 'Market')
    {
        if (APP_DEMO) {
            $this->error('测试站暂时不能修改！');
        }

        if (empty($id)) {
            $this->error('参数错误！');
        }

        if (empty($type)) {
            $this->error('参数错误1！');
        }

        if (strpos(',', $id)) {
            $id = implode(',', $id);
        }

        $where['id'] = ['in', $id];

        switch (strtolower($type)) {
            case 'forbid':
                $data = ['status' => 0];
                break;

            case 'resume':
                $data = ['status' => 1];
                break;

            case 'repeal':
                $data = ['status' => 2, 'endtime' => time()];
                break;

            case 'delete':
                $data = ['status' => -1];
                break;

            case 'del':
                if (M($moble)->where($where)->delete()) {
                    $this->success('操作成功！');
                } else {
                    $this->error('操作失败！');
                }

                break;

            default:
                $this->error('操作失败！');
        }

        if (M($moble)->where($where)->save($data)) {
            $this->success('操作成功！');
        } else {
            $this->error('操作失败！');
        }
    }

    public function invit($field = NULL, $name = NULL)
    {
        $where = [];

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(['username' => $name])->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Invit')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Invit')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(['id' => $v['userid']])->getField('username');
            $list[$k]['invit'] = M('User')->where(['id' => $v['invit']])->getField('username');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    public function checkUpdata()
    {
        if (!S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata')) {
            $list = M('Menu')->where([
                'url' => 'Trade/index',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/index', 'title' => '委托管理', 'pid' => 5, 'sort' => 1, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/index',
                    'pid' => ['neq', 0]
                ])->save(['title' => '委托管理', 'pid' => 5, 'sort' => 1, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            $list = M('Menu')->where([
                'url' => 'Trade/log',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/log', 'title' => '成交记录', 'pid' => 5, 'sort' => 2, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/log',
                    'pid' => ['neq', 0]
                ])->save(['title' => '成交记录', 'pid' => 5, 'sort' => 2, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            $list = M('Menu')->where([
                'url' => 'Trade/chat',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/chat', 'title' => '交易聊天', 'pid' => 5, 'sort' => 3, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/chat',
                    'pid' => ['neq', 0]
                ])->save(['title' => '交易聊天', 'pid' => 5, 'sort' => 3, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            $list = M('Menu')->where([
                'url' => 'Trade/comment',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/comment', 'title' => '币种评论', 'pid' => 5, 'sort' => 4, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/comment',
                    'pid' => ['neq', 0]
                ])->save(['title' => '币种评论', 'pid' => 5, 'sort' => 4, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            $list = M('Menu')->where([
                'url' => 'Trade/market',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/market', 'title' => '交易市场', 'pid' => 5, 'sort' => 5, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/market',
                    'pid' => ['neq', 0]
                ])->save(['title' => '交易市场', 'pid' => 5, 'sort' => 5, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            $list = M('Menu')->where([
                'url' => 'Trade/invit',
                'pid' => ['neq', 0]
            ])->select();

            if ($list[1]) {
                M('Menu')->where(['id' => $list[1]['id']])->delete();
            } else if (!$list) {
                M('Menu')->add(['url' => 'Trade/invit', 'title' => '交易推荐', 'pid' => 5, 'sort' => 6, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            } else {
                M('Menu')->where([
                    'url' => 'Trade/invit',
                    'pid' => ['neq', 0]
                ])->save(['title' => '交易推荐', 'pid' => 5, 'sort' => 6, 'hide' => 0, 'group' => '交易', 'ico_name' => 'stats']);
            }

            if (M('Menu')->where(['url' => 'Chat/index'])->delete()) {
                M('AuthRule')->where(['status' => 1])->delete();
            }

            if (M('Menu')->where(['url' => 'Tradelog/index'])->delete()) {
                M('AuthRule')->where(['status' => 1])->delete();
            }

            S(MODULE_NAME . CONTROLLER_NAME . 'checkUpdata', 1);
        }
    }
}

?>