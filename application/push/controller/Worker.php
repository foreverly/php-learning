<?php
namespace app\push\controller;

use think\worker\Server;
use Workerman\Connection\AsyncTcpConnection;
// use Request;
use Redis;

class Worker extends Server
{
    protected $socket = 'websocket://0.0.0.0:2346';
    // protected $request;

    // public function __construct()
    // {
    //     $this->request = Request::instance();
    // }

    public function redis()
    {
        $redis = new Redis(); 
        $redis->connect('127.0.0.1',6379);

        return $redis;
    }

    /**
     * 收到信息
     * @param $connection
     * @param $data
     */
    public function onMessage($connection, $data)
    {
        // 与远程task服务建立异步连接，ip为远程task服务的ip，如果是本机就是127.0.0.1，如果是集群就是lvs的ip
        // $task_connection = new AsyncTcpConnection('Text://127.0.0.1:12345');

        $pdata = json_decode($data, true);        
        $mobile = $pdata['mobile'];
        $content = $pdata['content'];
        $times = (int)$pdata['times'];
        
        $batchId = $this->redis()->get('sendBatch') + 1;
        $this->redis()->set('sendBatch', $batchId);

        // 直接返回结果防止页面一直等待
        $connection->send(json_encode(['type' => 'process', 'batchId' => $batchId, 'total' => $times, 'process' => 0]));

        for ($i = 0; $i < $times; $i++) { 

            $rdata = [
                'batchId' => $batchId,
                'data' => [
                    'mobile' => $mobile,
                    'content' => $content
                ]
            ];

            $this->redis()->lpush('taskSendMessage', json_encode($rdata));
            $this->redis()->set('taskSendMessage_' . $batchId, json_encode(['total' => $times, 'process' => 0]));

            // $connection->send(json_encode(['type' => 'msg', 'content' => "消息入队第".($i+1)."条"]));         
        }
        
        $connection->send(json_encode(['type' => 'msg', 'content' => '消息入队完毕']));
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function onConnect($connection)
    {
        $connection->send(json_encode(['type' => 'msg', 'content' => '连接成功']));
    }

    /**
     * 当连接断开时触发的回调函数
     * @param $connection
     */
    public function onClose($connection)
    {
        $connection->send(json_encode(['type' => 'msg', 'content' => '连接关闭']));
        $connection->close();
    }

    /**
     * 当客户端的连接上发生错误时触发
     * @param $connection
     * @param $code
     * @param $msg
     */
    public function onError($connection, $code, $msg)
    {
        echo "error $code $msg\n";
    }

    /**
     * 每个进程启动
     * @param $worker
     */
    public function onWorkerStart($worker)
    {

    }
}