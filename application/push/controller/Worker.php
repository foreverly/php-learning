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
        // $connection->send('我收到你的信息了');
        // 与远程task服务建立异步连接，ip为远程task服务的ip，如果是本机就是127.0.0.1，如果是集群就是lvs的ip
        $task_connection = new AsyncTcpConnection('Text://127.0.0.1:12345');

        $pdata = json_decode($data, true);        
        $mobile = $pdata['mobile'];
        $content = $pdata['content'];
        $times = (int)$pdata['times'];
        
        $batchId = $this->redis()->get('sendBatch') + 1;
        $this->redis()->set('sendBatch', $batchId);

        // $connection->send('消息入队完毕');
        for ($i = 0; $i < $times; $i++) { 
            $args = ['batchId' => $this->redis()->get('sendBatch'), 'mobile' => $mobile, 'content' => $content];

            // 任务及参数数据
            $task_data = array(
                'function' => 'send_message',
                'args'       => $args,
            );

            // 发送数据
            $task_connection->send(json_encode($task_data));
            // echo $re;
            $connection->send(json_encode(['type' => 'msg', 'content' => "消息入队第".($i+1)."条"]));         
        }

        $connection->send(json_encode(['type' => 'msg', 'content' => '消息入队完毕']));
        
        // 异步获得结果
        $t = 0;
        $task_connection->onMessage = function($task_connection, $task_result) use ($connection, $batchId, $times, &$t)
        {
            ++$t;
            // 结果
            // var_dump($task_result);
            // 获得结果后记得关闭异步连接
            // $task_connection->close();
            // 通知对应的websocket客户端任务完成
            $connection->send(json_encode(['type' => 'process', 'batchId' => $batchId, 'total' => $times, 'process' => $t]));
        };

        // 执行异步连接
        $task_connection->connect();
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