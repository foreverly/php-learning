<?php
namespace app\index\controller;

class Btc
{
    public function test()
    {
    	$request_data = [
		  	'json' => [
		    	'jsonrpc' => '2.0',
		    	'method' => 'sendtoaddress',
		    	'params' => ['1LeiqzD6jCwPcdNNAPiT8ayKgdHJMP2EpZ', 0.23],
		    	'id' => time()
		  	]
		];

		$request_url = "http://testbtc:testbtc@127.0.0.1:18332";

		$res = $this->requestPost($request_url, $request_data);

        var_dump($res);
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
        
        $result = curl_exec($ch);//运行curl

        if ($result) {
            curl_close($ch);

            return json_decode($result, true);
        } else {
            $error = curl_errno($ch);
            curl_close($ch);

            return 'curl出错，错误码('.$error.')，couldn`t connect to host。';
        }
    }
}
