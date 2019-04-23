<?php
namespace app\index\controller;

class Btc
{
    public function test()
    {
    	$request_data = [
		  	'json' => [
		    	'jsonrpc' => '1.0',
		    	'method' => 'sendtoaddress',
		    	'params' => ['1LeiqzD6jCwPcdNNAPiT8ayKgdHJMP2EpZ', 0.23],
		    	'id' => time()
		  	]
		];

		$request_url = "http://user:pass@localhost:18332";

		$res = $this->requestPost($request_url, $request_data);
    }

    /**
     * 模拟post进行url请求
     * @param string $url
     * @param string $param
     */
    function requestPost($url = '', $param = '') {
        if (empty($url) || empty($param)) {
            return false;
        }
        
        $postUrl = $url;
        $curlPost = $param;
        $ch = curl_init();//初始化curl
        curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
        curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($curlPost));
        $data = curl_exec($ch);//运行curl
        curl_close($ch);
        
        return $data;
    }
}
