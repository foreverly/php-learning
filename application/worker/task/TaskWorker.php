<?php
use Workerman\Worker;
use lib\SendMessage;

require_once __DIR__ . '/Workerman/Autoloader.php';

// task worker，使用Text协议
$task_worker = new Worker('Text://0.0.0.0:12345');

// task进程数可以根据需要多开一些
$task_worker->count = 60;
$task_worker->name = 'TaskWorker';

//只有php7才支持task->reusePort，可以让每个task进程均衡的接收任务
//$task->reusePort = true;
$task_worker->onMessage = function($connection, $task_data)
{
    // 直接返回ok，避免调用端长时间等待
    // $connection->send('ok'); 
    // 假设发来的是json数据
    $task_data = json_decode($task_data, true);
    // 根据task_data发消息，如果需要失败重发，
    // 可以把失败的消息任务放到mysql里面，
    // 做个定时器定时扫描失败消息重新发送
    SendMessage::do($task_data);
    // 发送结果
    // $connection->send(json_encode($task_result));
    $connection->send('ok');
};

Worker::runAll();