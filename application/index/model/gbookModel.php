<?php
namespace app\index\model;

/**
 * 留言本模型 负责管理留言本
 */
class gbookModel
{
	
	private $bookPath; 	// 留言本文件
	private $data; 		// 留言本数据

	/*
	* 设置留言本路径
	*/
	public function setBookPath($bookPath)
	{
		$this->bookPath = $bookPath;
	}

	/*
	* 获取留言本路径
	*/
	public function getBookPath()
	{
		return $this->bookPath;
	}

	/*
	* 打开留言本
	*/
	public function open()
	{
		// TO DO
	}

	/*
	* 关闭留言本
	*/
	public function close()
	{
		// TO DO
	}

	/*
	* 读取留言本
	*/
	public function read()
	{
		return file_get_contents($this->bookPath);
	}

	/*
	* 写入留言本
	*/
	public function write($data)
	{
		$this->data = self::safe($data)->name . "&" . self::safe($data)->email . "\r\nsaid:\r\n" . self::safe($data)->content . "\r\n\r\n";

		return file_put_contents($this->bookPath, $this->data, FILE_APPEND);
	}

	/*
	* 数据安全处理，先拆包，再打包
	*/
	public static function safe($data)
	{
		$reflect = new \ReflectionObject($data);
		$props = $reflect->getProperties();
		$messageBox = new \stdClass();

		foreach ($props as $prop) {
			$ivar = $prop->getName();
			$messageBox->$ivar = trim($prop->getValue($data));
		}
		
		return $messageBox;
	}

	/*
	* 删除留言本
	*/
	public function delete()
	{
		file_put_contents($this->bookPath, 'it`s empty now');
	}
}