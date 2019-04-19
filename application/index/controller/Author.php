<?php
namespace app\index\controller;
use app\index\model\leaveModel;
use app\index\model\gbookModel;
use app\index\model\messageModel;
use app\index\model\SmoothWarmingUp;

class Author
{

    public function message()
    {
    	$message = new messageModel;
    	$message->name = '张三';
    	$message->email = 'phper@php.net';
    	$message->content = 'I love you Ting Huang';
    	$message->time = 123;

    	// 单一职责
    	$pen = new leaveModel();
    	$book = new gbookModel();
    	$book->setBookPath("D:\\temp\\test\\message.txt");

    	$this->write($pen, $book, $message);

    	$bucket = new SmoothWarmingUp();
    	for ($i=0; $i < 100; $i++) { 
    		echo $i, "\t", var_dump($bucket->grant()); echo "剩余：" . ($bucket->capacity - $bucket->token) . "余位<br>";
    		usleep(50000);
    	}
    	
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
