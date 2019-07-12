<?php

namespace Common\Design\SetTrade;

use Think\Exception;

/**
 * 撮合结果写入数据库
 * Class Collection
 * @package Common\Design\SetTrade
 */
class Collection
{
    public $taker; //委托单
    public $maker; //匹配到的委托单
    public $type; //类型（1买2卖）
    public $market;
    public $persisted; //是否入库

    public function __construct($taker, $maker)
    {
        $this->taker = $taker;

        $this->maker = $maker;

        if ($taker['bidFlag'] == 0) {
            $this->type = 2;
        } else {
            $this->type = 1;
        }

        $this->market = strtolower($taker['pair']);
    }

    //处理订单
    public function entry()
    {
        //第一步，生成委托单
        //$res = $this->setTradeModel();
        //生成失败，返回错误提示
        //if ($res['status'] == 0) {
            //return $res;
        //} else {
            //第二步，执行匹配
            if (count($this->maker) > 0) {
                return $this->matchTrade();
            } else {
                //没有匹配返回成功提示
                //return $res;
                return ['status' => 1, 'msg' => '没有要匹配的对象'];
            }
        //}
    }

    public function matchTrade()
    {
        //如果是买单
        if ($this->type == 1) {
            $res = BuyMatch::handle($this->taker, $this->maker, $this->market);
        } else {
            $res = SellMatch::handle($this->taker, $this->maker, $this->market);
        }

        return $res;
    }

    //生成委托单
    public function setTradeModel()
    {
        try {
            //不需要新增
            if ($this->taker['persisted'] == TRUE) {
                return ['status' => 1, 'msg' => 'success'];
            }
            //检查orderid
            $check = M('Trade')->where(['id' => $this->taker['orderId']])->find();
            if ($check) {
                return ['status' => 1, 'msg' => 'success'];
            }

            //开启事物
            M()->startTrans();

            $fee = bcmul($this->taker['price'], $this->taker['amount'], 8);

            $add = M('Trade')->add([
                'id' => $this->taker['orderId'],
                'userid' => $this->taker['userId'],
                'market' => $this->market,
                'price' => $this->taker['price'],
                'num' => $this->taker['amount'],
                'deal' => 0,
                'mum' => $this->taker['amount'],
                'fee' => $fee,
                'type' => $this->type,
                'addtime' => time(),
                'status' => 0,
                'trade_type' => $this->taker['orderType'],
            ]);

            if (!$add) {
                throw new Exception('新增失败');
            }

            $coin = explode('_', $this->market)[0]; //交易货币
            $legal = explode('_', $this->market)[1]; //法币

            //非真实用户，不修改资产
            if ($this->taker['orderType'] == 2) {
                //冻结资产
                $where = ['userid' => $this->taker['userId']];
                //买入，冻结usdt
                if ($this->type == 1) {
                    //判断可用资产是否足够
                    $able = M('UserCoin')->where($where)->getField($legal);
                    if ($able < $fee) {
                        throw new Exception($legal . '资产不足');
                    }
                    //减去可用资产
                    ModelHandle::UpdateFieldNum('UserCoin', $where, $legal, $fee, FALSE);
                    //增加冻结资产
                    ModelHandle::UpdateFieldNum('UserCoin', $where, $legal . 'd', $fee, TRUE);
                } else {
                    //判断资产
                    $able = M('UserCoin')->where($where)->getField($coin);
                    if ($able < $this->taker['amount']) {
                        throw new Exception($coin . '资产不足');
                    }
                    //减去可用虚拟币
                    ModelHandle::UpdateFieldNum('UserCoin', $where, $coin, $this->taker['amount'], FALSE);
                    //增加冻结虚拟币
                    ModelHandle::UpdateFieldNum('UserCoin', $where, $coin . 'd', $this->taker['amount'], TRUE);
                }
            }

            M()->commit();

            return ['status' => 1, 'msg' => 'success'];
        } catch (Exception $e) {
            $msg = $e->getMessage();

            modellog($msg);

            M()->rollback();

            return ['status' => 0, 'msg' => $msg];
        }
    }
}
