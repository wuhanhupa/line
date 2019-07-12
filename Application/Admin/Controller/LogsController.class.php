<?php

namespace Admin\Controller;


class LogsController extends AdminController
{
    public function index()
    {
        $where = '';

        $count = M('Logs')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Logs')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }
}