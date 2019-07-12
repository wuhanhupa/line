<?php

namespace Home\Controller;

class ArticleController extends HomeController
{
    //帮助中心首页
    public function index($id = 19)
    {
        if (empty($id)) {
            redirect(U('Article/detail'));
        }

        if (!check($id, 'd')) {
            redirect(U('Article/detail'));
        }

        $Articletype = M('ArticleType')->where(array('id' => $id))->find();
        $ArticleTypeList = M('ArticleType')->where(array('status' => 1, 'index' => 1, 'shang' => $Articletype['shang']))->order('sort asc ,id asc')->select();
        $Articleaa = M('Article')->where(array('id' => $ArticleTypeList[0]['id']))->find();
       
        foreach ($ArticleTypeList as $k => $v) {
            $ArticleTypeLista[$v['name']] = $v;
        }
        $where = ['type' => $Articletype['name'],'status'=>1];
        $Model = M('Article');
        $count = $Model->where($where)->count();
        $Page = new \Think\Page($count, 10);
        $show = $Page->show();
        $list = $Model->where($where)->order('id desc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('shang', $Articletype);
        $this->assign('ArticleTypeList', $ArticleTypeLista);
        $this->assign('data', $Articleaa);
        $this->assign('list', $list);
        $this->assign('page', $show);
        $this->display();
    }

    //文章详情页
    public function detail($id = null)
    {
        if (empty($id)) {
            $id = 1;
        }

        if (!check($id, 'd')) {
            $id = 1;
        }

        $data = M('Article')->where(array('id' => $id))->find();
        $ArticleType = M('ArticleType')->where(array('status' => 1, 'index' => 1))->order('sort asc ,id desc')->select();

        foreach ($ArticleType as $k => $v) {
            $ArticleTypeList[$v['name']] = $v;
        }

        $this->assign('ArticleTypeList', $ArticleTypeList);
        $this->assign('data', $data);
        $this->assign('type', $data['type']);
        $this->display();
    }

    //首页公告
    public function notice()
    {
        $Articleaa = M('Article')->where(array('type' => 'communique','index' => 1))->order('sort asc ,id desc')->find();
        $info['id'] = $Articleaa['id'];
        $info['title'] = $Articleaa['title'];
        $info['url'] = '/Article/detail&id='.$info['id'];
        $this->ajaxReturn($info);
    }
}
