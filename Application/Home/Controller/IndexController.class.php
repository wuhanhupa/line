<?php

namespace Home\Controller;

class IndexController extends HomeController
{
    public function index()
    {
        $indexAdver = (APP_DEBUG ? null : S('index_indexAdver'));

        if (!$indexAdver) {
            $indexAdver = M('Adver')->where(array('status' => 1))->order('sort asc')->select();
            S('index_indexAdver', $indexAdver);
        }

        $this->assign('indexAdver', $indexAdver);
        $indexArticleType = (APP_DEBUG ? null : S('index_indexArticleType'));

        if (!$indexArticleType) {
            $indexArticleType = M('ArticleType')->where(array('status' => 1, 'index' => 1))->order('sort asc ,id desc')->limit(3)->select();
            S('index_indexArticleType', $indexArticleType);
        }

        $this->assign('indexArticleType', $indexArticleType);
        $indexArticle = (APP_DEBUG ? null : S('index_indexArticle'));

        if (!$indexArticle) {
            foreach ($indexArticleType as $k => $v) {
                $indexArticle[$k] = M('Article')->where(array('type' => $v['name'], 'status' => 1, 'index' => 1))->order('id desc')->limit(6)->select();
            }

            S('index_indexArticle', $indexArticle);
        }

        $this->assign('indexArticle', $indexArticle);
        $indexLink = (APP_DEBUG ? null : S('index_indexLink'));

        if (!$indexLink) {
            $indexLink = M('Link')->where(array('status' => 1))->order('sort asc ,id desc')->select();
        }

        $this->assign('indexLink', $indexLink);

        $ajaxMenu = new AjaxController();
        $indexMenu = $ajaxMenu->getJsonMenu('');
        $this->assign('indexMenu', $indexMenu);

        if (C('index_html')) {
            $this->display('Index/'.C('index_html').'/index');
        } else {
            $this->display();
        }
    }

    //片段
    public function fragment()
    {
        $ajax = new AjaxController();
        $data = $ajax->allcoin('');
        $this->assign('data', $data);
        $this->display('Index/d/fragment');
    }


   
    /**
     * 收藏图标.
     *
     * @author hxq
     * @date   2018-09-13T14:01:20+080
     *
     * @param [type] $market [description]
     *
     * @return [type] [description]
     */
    public function collectionIcon($market)
    {
        if (!userid()) {
            $info['info'] = '请登录后才能关注';
            $info['status'] = '4009';
            $this->ajaxReturn($info);
        }

        //1.判断当前用户是否已经收藏
        $user_coin = M('Sicon')->where(array('userid' => userid(), 'market' => $market))->field('id,status')->find();

        if ($user_coin['id'] > 0) {
            if ($user_coin['status'] == 1) {
                //如果收藏之后又点击就取消关注
                $user_coin = M('Sicon')->where(array('userid' => userid(), 'market' => $market))->save(array('status' => 0));
                $info['info'] = '已取消';
                $info['status'] = '0000';
                $this->ajaxReturn($info);
            } else {
                $user_coin = M('Sicon')->where(array('userid' => userid(), 'market' => $market))->save(array('status' => 1));
                $info['info'] = '已关注';
                $info['status'] = '0000';
                $this->ajaxReturn($info);
            }
        } else {
            //添加交易市场
            $data = array('userid' => userid(), 'market' => $market, 'status' => 1);
            $user_coin = M('Sicon')->add($data);
            if ($user_coin) {
                $info['info'] = '交易市场关注成功';
                $info['status'] = '0000';
                $this->ajaxReturn($info);
            } else {
                $info['info'] = '交易市场关注失败';
                $info['status'] = '20001';
                $this->ajaxReturn($info);
            }
        }
    }
}
