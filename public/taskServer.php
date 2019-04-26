<?php
use Workerman\Worker;
// use lib\SendMessage;

require_once __DIR__ . '/../vendor/workerman/workerman/Autoloader.php';

// task worker，使用Text协议
$task_worker = new Worker('Text://0.0.0.0:12345');

// task进程数可以根据需要多开一些
$task_worker->count = 20;
$task_worker->name = 'TaskWorker';

//只有php7才支持task->reusePort，可以让每个task进程均衡的接收任务
//$task->reusePort = true;
$task_worker->onMessage = function($connection, $task_data)
{
    // 直接返回ok，避免调用端长时间等待
    // $connection->send(json_encode(['content' => 'ok'])); 
    // 假设发来的是json数据
    $task_data = json_decode($task_data, true);
    $tash_func = $task_data['function'];

    switch ($tash_func) {
    	case 'send_message':

    		// 根据task_data发消息，如果需要失败重发，
    		$args = $task_data['args'];

		    // 可以把失败的消息任务放到mysql里面，
		    // 做个定时器定时扫描失败消息重新发送
		    doSendMessage($args);

		    // 发送结果
		    $ret_data = ['batchId' => $args['batchId']];
		    $connection->send(json_encode($ret_data));

    		break;
    	
    	default:
    		$connection->send(json_encode([]));
    		break;
    }
    // $contents = json_decode($task_data['contents'], true);
		    
};

$task_worker->onConnect = function($connection){
    $connection->send(json_encode(['type' => 'msg', 'refer' => 'task','content' => "task服务端连上了"]));
};

// 模拟消息发送
function doSendMessage()
{
	return true;
}

Worker::runAll();
