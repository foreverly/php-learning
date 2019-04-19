<?php
namespace app\index\model;
use app\index\model\gbookModel;

/**
 * 留言本业务逻辑处理
 */
class leaveModel
{

	/*
	* 写入留言本
	*/
	public function write(gbookModel $gb, $data)
	{
		$book = $gb->getBookPath(); // 
		$gb->write($data);
	}
}