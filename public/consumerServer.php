<?php
use Workerman\Worker;

require_once __DIR__ . '/../vendor/workerman/workerman/Autoloader.php';
 
$handle_worker = new Worker('websocket://0.0.0.0:10088');
$handle_worker->count = 20;
$handle_worker->name = 'ConsumerWorker';
//在进程开启之时
$handle_worker->onWorkerStart = function($handle_worker) {
	
	\Workerman\Lib\Timer::add(0.5, function() use ($handle_worker){
		$redis = new Redis();
		$redis -> connect('127.0.0.1', '6379');
		$value = $redis->rpop('taskSendMessage');

		if ($value) {
			$result = json_decode($value, true);
			$data = $result['data'];

			$batchId = $result['batchId'];
			$pdata = json_decode($redis->get('taskSendMessage_' . $batchId), true);
			$total = $pdata['total'];
			$process = $pdata['process'] + 1;

			// 进度+1
			$redis->set('taskSendMessage_' . $batchId, json_encode(['total' => $total, 'process' => $process]));
			$redis->hset('MsgSendList_' . $batchId, 'process', $process);

			// 任务处理
			$status = sendMessage($result);
			if ($status) {
		 		foreach($handle_worker->connections as $connection) {
		 			// 实时返回处理进度
		            $connection->send(json_encode(['type' => 'process', 'batchId' => $batchId, 'total' => $total, 'process' => $process]));
		        }
		 	}
		}
	});
	
	
};

function sendMessage($data)
{
	usleep(1000);
	return true;
}
 
// 运行worker
Worker::runAll();
