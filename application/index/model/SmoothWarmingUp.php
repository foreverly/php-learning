<?php
namespace app\index\model;

/**
 * 令牌桶
 */
class SmoothWarmingUp
{
	private $timeStamp;
	public $capacity; 		// 桶的总容量
	public $rate; 			// token流出的速度
	public $token; 			// 当前容量（当前累积的请求量）

	public function __construct()
	{
		$this->timeStamp = time();
		$this->capacity = 30;
		$this->rate = 5;
	}

	public function grant()
	{
		$now = time();
		$this->token = max(0, $this->token - ($now - $this->timeStamp) * $this->rate);
		$this->timeStamp = $now;

		if (($this->token) < $this->capacity) {
			// 尝试加入token，并且容器还未满
			$this->token += 1;
			return true;
		}else{
			return false;
		}
	}
}