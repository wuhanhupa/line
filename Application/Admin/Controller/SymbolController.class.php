<?php

namespace Admin\Controller;

use Think\Page;

/**
 * 代币编号管理
 * Class SymbolController
 * @package Admin\Controller
 */
class SymbolController extends AdminController
{
    public function index($name = NULL)
    {
        if ($name) {
            $where['market'] = $name;
        }

        $count = M('Bz')->where($where)->count();
        $Page = new Page($count, 15);
        $show = $Page->show();
        $list = M('Bz')->where($where)->order('id asc')->limit($Page->firstRow.','.$Page->listRows)->select();

        $this->assign('page', $show);
        $this->assign('list', $list);
        $this->display();
    }

    public function create()
    {
        $this->display();
    }

    public function store($market, $number)
    {
        if (empty($market)) {
            $this->error('代币名称不能为空');
        }
        if (empty($number)) {
            $this->error('代币编号不能为空');
        }
        if (!check($number, 'number')) {
            $this->error('代币编号格式不正确');
        }

        $checkMarket = M('Bz')->where(array('market' => $market))->find();
        if ($checkMarket) {
            $this->error('代币名称已存在');
        }
        $checkNumber = M('Bz')->where(array('number' => $number))->find();
        if ($checkNumber) {
            $this->error('代币编号已存在');
        }

        $lastId = M('Bz')->order('id desc')->limit(1)->getField('id');

        $add = M('Bz')->add(array(
            'id' => ($lastId + 1),
            'market' => $market,
            'number' => $number
        ));

        if ($add) {
            $this->success('新增成功',U('index'));
        } else {
            $this->error('新增失败');
        }
    }

    public function edit($id)
    {
        $data = M('Bz')->where(array('id' => $id))->find();

        $this->assign('data', $data);
        $this->display();
    }

    public function update($id, $market, $number)
    {
        $bz = M('Bz')->where(array('id' => $id))->find();
        if (!$bz) {
            $this->error('数据不存在');
        }

        $save = M('Bz')->where(array('id' => $id))->save(array(
            'market' => $market,
            'number' => $number
        ));

        if ($save) {
            $this->success('修改成功',U('index'));
        } else {
            $this->error('修改失败');
        }
    }

    public function status($id = null)
    {
        if (empty($id)) {
            $this->error('请选择代币！');
        }
        if (strpos(',', $id)) {
            $id = implode(',', $id);
        }
        $where['id'] = array('in', $id);

        $del = M('Bz')->where($where)->delete();

        if ($del) {
            $this->success('删除成功',U('index'));
        } else {
            $this->error('删除失败');
        }
    }
}