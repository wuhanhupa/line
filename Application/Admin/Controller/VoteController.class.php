<?php

namespace Admin\Controller;

class VoteController extends AdminController
{
    public function index($field = null, $name = null)
    {
        $where = array();

        if ($field && $name) {
            if ($field == 'username') {
                $where['userid'] = M('User')->where(array('username' => $name))->getField('id');
            } else {
                $where[$field] = $name;
            }
        }

        $count = M('Vote')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Vote')->where($where)->order('id desc')->select();

        foreach ($list as $k => $v) {
            $list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
            $list[$k]['addtime'] = date('Y-m-d H:i:s');
        }

        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //审核详情页面
    public function checkinfo($id)
    {
        $vote = M('Vote')->where(array('id' => $id))->find();
        $vote['username'] = M('User')->where(array('id' => $vote['userid']))->getField('username');
        $this->assign('user', $vote);
        $this->display();
    }

    //审核操作
    public function shenhe($id, $status)
    {
        $vote = M('Vote')->where(array('id' => $id))->save(array('status' => $status));
        if ($vote) {
            $this->success('操作成功');
        } else {
            $this->error('操作失败');
        }
    }
}
