<?php

namespace Admin\Controller;

class AppController extends AdminController
{
    /**
     * Notice: APP版本列表
     * @author: hxq
     */
    public function index()
    {
        $apps = M('Version')->order('create_time desc')->select();

        $this->assign('list', $apps);
        $this->display();
    }

    /**
     * Notice:发布版本
     * @author: hxq
     */
    public function create()
    {
        $this->display();
    }

    /**
     * Notice: 保存新增
     * @author: hxq
     * @param     $name
     * @param     $number
     * @param     $title
     * @param     $log
     * @param int $status
     */
    public function store($name, $number, $title, $log, $status = 0)
    {
        if (empty($name) || empty($number) || empty($title) || empty($log)) {
            return $this->error('缺少参数！');
        }
        $data['name'] = $name;
        $data['number'] = $number;
        $data['title'] = $title;
        $data['log'] = $log;
        $data['status'] = $status;
        $data['create_time'] = time();
        $res = M('Version')->add($data);

        if ($res) {
            return $this->success('', "/Admin/App/index");
        } else {
            return $this->error('发布失败');
        }
    }

    /**
     * Notice:编辑
     * @author: hxq
     * @param $id
     */
    public function edit($id)
    {
        if (empty($id)) {
            return $this->error('缺少参数！');
        }

        $data = M('Version')->where(['id' => $id])->find();

        $this->assign('data', $data);
        $this->display();
    }

    /**
     * Notice:保存修改
     * @author: hxq
     */
    public function update()
    {
        $input = I('post.');

        if (empty($input['name']) || empty($input['number']) || empty($input['title']) || empty($input['log']) || empty($input['id'])) {
            return $this->error('缺少参数！');
        }

        $data['name'] = $input['name'];
        $data['number'] = $input['number'];
        $data['title'] = $input['title'];
        $data['log'] = $input['log'];
        $data['status'] = $input['status'];

        $res = M('Version')->where(['id' => $input['id']])->save($data);

        if ($res) {
            return $this->success('', "/Admin/App/index");
        } else {
            return $this->error('更新失败');
        }
    }
}

