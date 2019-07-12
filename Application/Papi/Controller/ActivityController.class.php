<?php

namespace Papi\Controller;

class ActivityController extends CommonController
{
    //获取最新活动
    public function getActivity()
    {
        $data = M('Activity')->order('id desc')->limit(1)->find();

        //处理图片
        $data['img'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Upload/ad/' . $data['img'];
        $data['addtime'] = date('Y-m-d H:i:s', $data['addtime']);

        $info['code'] = 0;
        $info['msg'] = '成功';
        $info['data'] = $data;

        $this->ajaxReturn($info);
    }
}