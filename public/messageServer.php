<?php
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

require_once __DIR__ . '/../vendor/workerman/workerman/Autoloader.php';

// message worker，使用Text协议
$message_worker = new Worker('websocket://0.0.0.0:10086');

// 进程数可以根据需要多开一些
$message_worker->count = 20;
$message_worker->name = 'MessageWorker';

$message_worker->onMessage = function($connection, $data)
{
    $connection->send(json_encode(['type' => 'msg', 'content' => "收到message服务端的消息"]));
    // 与远程task服务建立异步连接，ip为远程task服务的ip，如果是本机就是127.0.0.1，如果是集群就是lvs的ip
    $task_connection = new AsyncTcpConnection('Text://127.0.0.1:12345');

    $data = json_decode($data, true);
    if (($data['refer'] ?? null) == 'task') {
        $connection->send(json_encode(['type' => 'msg', 'content' => "收到task服务端的消息"]));
    }

    $pdata = $data['content'];
    $batchId = $data['batchId'];
    $mobile = $pdata['mobile'];
    $content = $pdata['content'];
    $times = (int)$pdata['times'];
    
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

        $connection->send(json_encode(['type' => 'msg', 'content' => "消息入队第".($i+1)."条\n"]));         
    }
		    
};

$message_worker->onConnect = function($connection){
    $connection->send(json_encode(['type' => 'msg', 'content' => "message服务端连上了"]));
};

// 模拟消息发送
function doSendMessage()
{
	return true;
}

Worker::runAll();
