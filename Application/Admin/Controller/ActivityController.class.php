<?php

namespace Admin\Controller;

/**
 * 活动管理
 * Class ActivityController
 * @package Admin\Controller
 */
class ActivityController extends AdminController
{
    public function index()
    {
        $where = '';

        $count = M('Activity')->where($where)->count();
        $Page = new \Think\Page($count, 15);
        $show = $Page->show();
        $list = M('Activity')->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    public function create()
    {
        $this->display();
    }

    public function store($title, $url, $img, $is_show)
    {
        if (empty($title)) {
            $this->error('标题不能为空');
        }
        if (empty($url)) {
            $this->error('链接不能为空');
        }
        if (empty($img)) {
            $this->error('图片不能为空');
        }
        if (!in_array($is_show, [0, 1])) {
            $this->error('是否显示值错误');
        }

        $add = M('Activity')->add(array(
            'title' => $title,
            'url' => $url,
            'img' => $img,
            'is_show' => $is_show,
            'addtime' => time()
        ));

        if ($add) {
            $this->success('', U('index'));
        } else {
            $this->error('新增失败');
        }
    }

    public function edit($id)
    {
        $data = M('Activity')->where(array('id' => $id))->find();

        $this->assign('data', $data);
        $this->display();
    }

    public function update($id,$title, $url, $img, $is_show)
    {
        $data = M('Activity')->where(array('id' => $id))->find();
        if (!$data) {
            $this->error('活动不存在');
        }

        $save = M('Activity')->where(array('id' => $id))->save(array(
            'title' => $title,
            'url' => $url,
            'img' => $img,
            'is_show' => $is_show,
        ));

        if ($save) {
            $this->success('', U('index'));
        } else {
            $this->error('数据没有修改');
        }
    }

    public function upload()
    {
        $upload = new \Think\Upload();
        $upload->maxSize = 3145728;
        $upload->exts = array('jpg', 'gif', 'png', 'jpeg');
        $upload->rootPath = './Upload/ad/';
        $upload->autoSub = false;
        $info = $upload->upload();

        foreach ($info as $k => $v) {
            $path = $v['savepath'].$v['savename'];
            echo $path;
            exit();
        }
    }
}