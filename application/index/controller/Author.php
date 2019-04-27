<?php
namespace app\index\controller;
use app\index\model\leaveModel;
use app\index\model\gbookModel;
use app\index\model\messageModel;
use app\index\model\SmoothWarmingUp;
use think\Controller;
use think\Request;
use Redis;
use Workerman\Worker;
use Workerman\Connection\AsyncTcpConnection;

class Author extends Controller
{
    protected $request;

    public function init()
    {
        $this->request = Request::instance();
    }

    public function redis()
    {
        $redis = new Redis(); 
        $redis->connect('127.0.0.1',6379);

        return $redis;
    }

    public function index()
    {
        $this->assign('domain','tp-dev.com');

        $hlist = $this->redis()->keys('MsgSendList_*');
        $hdata = [];
        foreach ($hlist as $hname) {
            $hdata[] = $this->redis()->hgetall($hname);            
        }
        echo "<pre>";
        print_r($hdata);
        print_r($hdata[0]['batchId']);
        echo "</pre>";
        $this->assign('hdata', $hdata);
        //渲染模板
        return $this->fetch();
    }

    public function test()
    {
        $mobile = $this->request->post('mobile');
        $content = $this->request->post('content');
        $times = (int)$this->request->post('times', 1);
        $pdata = $this->request->post();
        
        $batchId = $this->redis()->get('sendBatch') + 1;
        $this->redis()->set('sendBatch', $batchId);

        for ($i=0; $i < $times; $i++) {
            $rdata = [
                'batchId' => $batchId,
                'data' => [
                    'mobile' => $mobile,
                    'content' => $content
                ]
            ];

            $this->redis->rpush('taskSendMessage', json_encode($rdata));            
        }

        // $task_connection = new AsyncTcpConnection('Text://127.0.0.1:10086');

        // $task_connection->onMessage = function($task_connection, $task_result) use ($pdata)
        // {            
        //     $task_connection->send(json_encode(['type' => 'msg', 'batchId' => $batchId, 'content' => $pdata]));
        // };

        // // 执行异步连接
        // $task_connection->connect();

        // Worker::runAll();
    
        echo json_encode(['batchId' => $batchId, 'total' => $times, 'process' => 0]);
    }

    public function message()
    {
    	$message = new messageModel;
    	$message->name = '张三';
    	$message->email = 'phper@php.net';
    	$message->content = 'I love you Miss H';
    	$message->time = 123;

    	// 单一职责
    	$pen = new leaveModel();
    	$book = new gbookModel();
    	$book->setBookPath("D:\\temp\\test\\message.txt");

    	$this->write($pen, $book, $message);

    	// $bucket = new SmoothWarmingUp();
    	// for ($i=0; $i < 100; $i++) { 
    	// 	echo $i, "\t", var_dump($bucket->grant()); echo "剩余：" . ($bucket->capacity - $bucket->token) . "余位<br>";
    	// 	usleep(50000);
    	// }
    	
    	echo $this->view($book);
    	// $this->delete($book);
    }

    public function write(leaveModel $l, gbookModel $g, messageModel $data)
    {
    	$l->write($g, $data);
    }

    /*
	* 查看留言本内容
    */
    public static function view(gbookModel $g)
    {
    	echo "<form method='post'>
		 名字:<br>
		<input type=\"text\" name=\"name\">
		<br>
		 邮箱:<br>
		<input type=\"text\" name=\"email\">
		<br>
		 内容:<br>
		<input type=\"text\" name=\"content\">
		<br><br>
		<input type=\"button\" value=\"提交\">
		</form> ";

		$res = $g->read();
		$contents  = $res ? explode("\r\n\r\n", $res) : [];
		
		foreach ($contents as $content) {
			echo $content . '<br>';
		}
    }

    public function delete(gbookModel $g)
    {
    	$g->delete();
    	echo self::view($g);
    }
}
