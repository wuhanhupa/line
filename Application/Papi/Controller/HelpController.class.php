<?php

namespace Papi\Controller;
class HelpController extends CommonController
{
    //帮助中心
    public function index()
    {
        $this->display();
    }

    //关于我们
    public function about()
    {
        $this->display();
    }

    //文章内容
    public function detail()
    {
        $this->display();
    }

    //帮助中心首页
    public function herad($id = 19)
    {
        if (empty($id)) {
            redirect(U('Help/details'));
        }

        if (!check($id, 'd')) {
            redirect(U('Help/details'));
        }

        $Articletype = M('ArticleType')->where(['id' => $id])->find();
        $ArticleTypeList = M('ArticleType')->where(['status' => 1, 'index' => 1, 'shang' => $Articletype['shang']])->order('sort asc ,id asc')->select();

        foreach ($ArticleTypeList as $k => $v) {
            $ArticleTypeLista[$v['name']] = $v;
        }
        $where = ['type' => $Articletype['name'],'status'=>1];
        $Model = M('Article');

        $list = $Model->where($where)->order('id desc')->select();
        foreach ($list as $k => $v) {
            $list[$k]['addtime'] = date('Y-m-d H:i:s', $v['addtime']);
        }
        $info['ArticleTypeLista'] = $ArticleTypeLista;
        $info['list'] = $list;

        $this->ajaxReturn($info);
    }

    public function download()
    {
        $ios_addr = C('IOS_ADDR');
        $android_addr = C('Android_ADDR');
        $this->assign('ios_addr', $ios_addr);
        $this->assign('android_addr', $android_addr);
        $this->display();
    }

    public function download_test()
    {
        $ios_addr = C('IOS_ADDR');
        $android_addr = C('Android_ADDR');
        $this->assign('ios_addr', $ios_addr);
        $this->assign('android_addr', $android_addr);
        $this->display();
    }

    //文章详情页
    public function details($id = NULL)
    {
        if (empty($id)) {
            $id = 1;
        }
        if (!check($id, 'd')) {
            $id = 1;
        }
        $data = M('Article')->where(['id' => $id])->find();
        $data['addtime'] = date('Y-m-d H:i:s', $data['addtime']);

        $this->ajaxReturn($data);
    }

    //关于我们的描述
    public function about_index()
    {
        $des = M('Config')->getfield('web_description');
        $info['des'] = $des;
        $this->ajaxReturn($info);
    }

    //C2C帮助页面
    public function c2cdetail(){

        $this->display();
    }

    //富派支付APP页面
    public function yujie(){


        $this->display();
    }

    public function fupai(){

        $info =M('Article')->where(array('id'=>38))->field('content,status')->select();
        $data['content'] = $info[0]['content'];
        $data['status'] = $info[0]['status'];
        //var_dump($info);exit();
         $this->ajaxReturn($data);

    }


}