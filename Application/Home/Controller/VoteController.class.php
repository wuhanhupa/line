<?php

namespace Home\Controller;

class VoteController extends HomeController
{
    public function index()
    {
        $this->display();
    }

    public function add()
    {
     
        $data['userid'] ='11111';
        $data['email'] = I('post.email');
        $data['projectname'] = I('post.projectname');
        $data['website'] = I('post.website');
        $data['symbol'] = I('post.symbol');
        $data['whitepaperlink'] = I('post.whitepaperlink');
        $data['projectinfo'] = I('post.projectinfo');
        $data['advisorinfo'] = I('post.advisorinfo');
        $data['icostatus'] = I('post.icostatus');
        $data['symboltotal'] = I('post.symboltotal');
        $data['dismechan'] = I('post.dismechan');
        $data['symbolcisupply'] = I('post.symbolcisupply');
        $data['marketlistd'] = I('post.marketlistd');
        $data['tradevolume'] = I('post.tradevolume');
        $data['teaminfo'] = I('post.teaminfo');
        $data['privatesaleinfo'] = I('post.privatesaleinfo');
        $data['projectprogress'] = I('post.projectprogress');
        $data['numberusers'] = I('post.numberusers');
        $data['squsernum'] = I('post.squsernum');
        $data['codeopensource'] = I('post.codeopensource');
        $data['opensourcelink'] = I('post.opensourcelink');
        $data['addtime'] = time();
        $data['status'] = 1;
        $user = M('Vote')->where(array('userid'=>userid()))->find();

        if ($user) {
            $info['code']='2002';
            $info['msg']='您已经提交过申请，不能再次操作！';
            $this->ajaxReturn($info);
        } elseif (M('Vote')->add($data)) {
            $info['code']='0000';
            $info['msg']='提交申请成功！';
            $this->ajaxReturn($info);
        } else {
            $info['code']='2003';
            $info['msg']='提交申请失败！';
            $this->ajaxReturn($info);
        }
    }
}
