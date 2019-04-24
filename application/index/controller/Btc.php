<?php
namespace app\index\controller;
use GuzzleHttp\Client;

class Btc
{
    private $reg_url;
    private $test_url;

    public function __construct()
    {
        $this->regnet = 'http://regbtc:regbtc@127.0.0.1:18334';
        $this->testnet = 'http://testbtc:testbtc@127.0.0.1:18332';
    }

    public function phpinfo()
    {
        echo phpinfo();
    }

    // 查看网络状态
    public function getnetworkinfo()
    {
        $request_data = $this->initData('getnetworkinfo');

        // $client = new Client();
        // $res = $client->post($this->regnet, $request_data);
        $res = $this->curlPost($this->regnet, $request_data);

        var_dump($res);
    }

    // 查看区块状态，同步进度等
    public function getblockchaininfo()
    {
        $request_data = $this->initData('getblockchaininfo');

        // $request_url = "http://regbtc:regbtc@127.0.0.1:18334";

        $client = new Client();
        $res = $client->post($this->regnet, $request_data);

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

        $client = new Client();
        $res = $client->post($request_url, $request_data);

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

        $client = new Client();
        $res = $client->post($request_url, $request_data);

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

        $client = new Client();
        $res = $client->post($request_url, $request_data);

        var_dump($res);
    }

    function initData($method = null, $params = [], $jsonrpc = null, $id = null)
    {
        if (!$method) {
            echo 'method can`t be null';exit;
        }

        return [
            'json' => [
                'jsonrpc' => $jsonrpc ?: '1.0',
                'method' => $method,
                'params' => $params ?: [],
                'id' => $id ?: []
            ]
        ];
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

    function curlPost($url, $data = [], $timeout = 30)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout-2);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 信任任何证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 检查证书中是否设置域名
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Expect:']); //避免data数据过长问题
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));

        $ret = curl_exec($ch);
        var_dump(curl_error($ch));  //查看报错信息

        curl_close($ch);

        return $ret;
    }

}
