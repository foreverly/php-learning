<?php
namespace app\index\controller;
vendor('jsonRPC.jsonRPCClient');
// $client = new \jsonRPCClient('http://localhost:8534');
// dump($client->personal_newAccount("123456"));
class Btc
{
    // 查看网络状态
    public function getnetworkinfo()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'getnetworkinfo',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://regbtc:regbtc@127.0.0.1:18334";

        $res = $this->requestPost($request_url, $request_data);

        var_dump($res);
    }

    // 查看区块状态，同步进度等
    public function getblockchaininfo()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'getblockchaininfo',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://regbtc:regbtc@127.0.0.1:18334";

        $res = $this->requestPost($request_url, $request_data);

        var_dump($res);
    }

    // 创建一个钱包账户
    public function getnewaddress()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'getnewaddress',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://regbtc:regbtc@127.0.0.1:18334";

        $res = $this->requestPost($request_url, $request_data);

        var_dump($res);
    }  

    // 钱包总余额
    public function getbalance()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'getbalance',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://regbtc:regbtc@127.0.0.1:18334";

        $res = $this->requestPost($request_url, $request_data);

        var_dump($res);
    }  

    // 每个账户的余额
    public function listunspent()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'getnetworkinfo',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://regbtc:regbtc@127.0.0.1:18332";

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
