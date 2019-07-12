<?php

namespace Papi\Controller;

class ArticleController extends CommonController
{
    /**
     * Notice:首页公告
     * @author: hxq
     */
    public function notice()
    {
        $Articleaa = M('Article')->where(['type' => 'communique', 'index' => 1])->order('sort asc ,id desc')->find();
        $info['id'] = $Articleaa['id'];
        $info['title'] = $Articleaa['title'];
        $info['url'] = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . '/Papi/Help/detail?id=' . $info['id'];

        $data['code'] = 0;
        $data['msg'] = '成功';
        $data['data'] = $info;

        $this->ajaxReturn($data);
    }

    /**
     * Notice:获取app版本号
     * @author: hxq
     * @param $jmcode
     */
    public function app_version($jmcode)
    {
        $result = $this->rests($jmcode);

        if ($result['result']) {
            $sys = $result['data']['sys'];
            $sys = strtolower($sys);
            //最新版本号
            $version = M('Version')->where(['number' => $sys])->order('create_time desc')->find();
            if (!$version) {
                $version = array();
            }

            $data['code'] = 0;
            $data['msg'] = '成功';
            $data['data'] = $version;

            $this->ajaxReturn($data);
        } else {
            $this->apierror();
        }
    }
}
