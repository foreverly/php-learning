<?php
namespace app\index\model;

/**
 * 留言本实体
 */
class messageModel
{

	public $name;		// 留言者姓名
	public $email;		// 留言者联系方式
	public $content;	// 留言内容
	public $time;		// 留言时间

	public function __set($name, $value)
	{
		$this->$name = $value;
	}

	public function __get($name)
	{
		if (!isset($this->$name)) {
			$this->$name = NULL;
		}
	}
}