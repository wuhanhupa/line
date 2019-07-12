<?php

namespace Admin\Controller;

use Think\Page;

class LegalController extends AdminController
{
    /**
     * 法币挂单列表
     * @param null $field
     * @param null $name
     * @param null $type
     */
	public function index($field = NULL, $name = NULL, $type = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($type) {
			$where['type'] = $type;
		}

		$count = M('Ctwoc')->where($where)->count();
		$Page = new Page($count, 15);
		$show = $Page->show();
		$list = M('Ctwoc')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['usernamea'] = M('User')->where(array('id' => $v['userid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

    /**
     * 法币交易单列表
     * @param null $field
     * @param null $name
     * @param null $status
     */
	public function trade($field = NULL, $name = NULL, $status = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($status) {
			$where['status'] = $status;
		}

		$count = M('CtwocLog')->where($where)->count();
		$Page = new Page($count, 15);
		$show = $Page->show();
		$list = M('CtwocLog')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['buyname'] = M('User')->where(array('id' => $v['userid']))->getField('username');
			$list[$k]['sellname'] = M('User')->where(array('id' => $v['peerid']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}

    /**
     * 交易明细
     * @param null $field
     * @param null $name
     * @param null $type
     */
	public function details($field = NULL, $name = NULL, $type = NULL)
	{
		$where = array();

		if ($field && $name) {
			if ($field == 'username') {
				$where['userid'] = M('User')->where(array('username' => $name))->getField('id');
			}
			else {
				$where[$field] = $name;
			}
		}

		if ($type) {
			$where['type'] = $type;
		}

		$count = M('CtwocCenter')->where($where)->count();
		$Page = new Page($count, 15);
		$show = $Page->show();
		$list = M('CtwocCenter')->where($where)->order('id desc')->limit($Page->firstRow . ',' . $Page->listRows)->select();

		foreach ($list as $k => $v) {
			$list[$k]['username'] = M('User')->where(array('id' => $v['userid']))->getField('username');
			$list[$k]['sellname'] = M('User')->where(array('id' => $v['usersell']))->getField('username');
		}

		$this->assign('list', $list);
		$this->assign('page', $show);
		$this->display();
	}
}
