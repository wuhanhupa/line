<?php

namespace Home\Controller;

use Think\Controller;
use Workerman\Worker;

class WorkermanController extends HomeController
{
    //启动入口
    public function index(){
        $clients=[];
        if(!IS_CLI){
            die("无法直接访问，请通过命令行启动");
        }
        // 创建一个Worker监听2346端口，使用websocket协议通讯
        $worker_chart = new \Workerman\Worker('websocket://139.224.64.194:2346');
        $worker_chart->count = 1;
        $worker->uidConnections = array();

        // 当收到客户端发来的数据后返回hello $data给客户端
        $worker_chart->onWorkerStart = function($worker_chart){
            echo "Worker starting...\n";
        };

        //处理业务
        $worker_chart->onMessage = function($connection, $data)
        {   
            global $worker_chart;
            //获取前端数据
            $data = json_decode($data,true);
            $myid = $data['myid'];//发送者
            $toid = $data['toid'];//接受者
            $msg  = $data['msg'];
            $type = $data['type'];
            $username = M('User')->where( array('id'=>$myid) )->getField('truename');
            $addtime = time();
            switch($type){
                case 'con':
                    $connection->uid = $data['userid'];    
                    $worker_chart->uidConnections[$connection->uid] = $connection;
                    echo '指定客户端是:'.$connection->uid;
                    break;
                case 'say':
                    //将用户发送的数据写入到数据库中
                    $arr = array('myid'=>$myid,'toid'=>$toid,'msg'=>$msg,'username'=>$username,'addtime'=>date('Y-m-d H:i:s',$addtime));
                    $content = M('bchart')->add($arr);
                    if($content){
                        //如果写入成功就把消息 返回给客户端
                        $arr = json_encode($arr);
                        $connection = $worker_chart->uidConnections[$toid];
                        return $connection->send($arr);
                    }
                    break;
                case 'sys':
                        //将用户发送的数据写入到数据库中
                        $arr = array('myid'=>$myid,'toid'=>$toid,'msg'=>$msg,'username'=>$username,'addtime'=>date('Y-m-d H:i:s',$addtime));
                        //如果写入成功就把消息 返回给客户端
                        $arr = json_encode($arr);
                        $connection = $worker_chart->uidConnections[$toid];
                        return $connection->send($arr);
                    break;     
            }
            // 设置连接的onClose回调
            $connection->onClose = function($connection) //客户端主动关闭
            {
                echo "connection closed\n";
            };
        };
        // 运行worker
        Worker::runAll();
    }

    //聊天页面
    public function chart(){
         
         $this->display();

    }
    
}