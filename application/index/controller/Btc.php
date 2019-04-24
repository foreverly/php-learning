<?php
namespace app\index\controller;
use GuzzleHttp\Client;
use org\jsonrpcphp\JsonRPCClient;

class Btc
{
    private $regnet;
    private $testnet;
    private $mainnet;

    public function __construct()
    {
        $this->regnet = 'http://regbtc:regbtc@127.0.0.1:18334';
        $this->testnet = 'http://testbtc:testbtc@127.0.0.1:18332';
        $this->mainnet = 'http://admin:admin@127.0.0.1:8332';

        $this->client = new JsonRPCClient($this->mainnet);
    }

    public function phpinfo()
    {
        echo phpinfo();
    }

    // 查看网络状态
    public function getnetworkinfo()
    {
        dd($this->client->getnetworkinfo());
    }

    // 查看区块状态，同步进度等
    public function getblockchaininfo()
    {
        $client = new JsonRPCClient($this->mainnet);

        echo "<pre>\n";
        print_r($client->getblockchaininfo()); echo "\n";
        echo "Received: ".$client->getreceivedbylabel("Your Address")."\n";
        echo "</pre>";
    }

    // 创建一个钱包账户
    public function getnewaddress()
    {
        $address = $_GET['address'] ?? NULL;

        if($address === NULL){
            dd("address can`t be null");exit;
        }

        dd($client->getnewaddress($address));
    }  

    // list账户
    public function listlabels()
    {
        $client = new JsonRPCClient($this->mainnet);
        
        echo "<pre>\n";
        print_r($client->listlabels()); echo "\n";
        echo "Received: ".$client->getreceivedbylabel("Your Address")."\n";
        echo "</pre>";
    }

    // 钱包总余额
    public function getbalance()
    {
        $client = new JsonRPCClient($this->mainnet);
        
        echo "<pre>\n";
        print_r($client->getbalance()); echo "\n";
        echo "Received: ".$client->getreceivedbylabel("Your Address")."\n";
        echo "</pre>";
    }  

    // 每个账户的余额
    public function listunspent()
    {
        $request_data = [
            'json' => [
                'jsonrpc' => '2.0',
                'method' => 'listunspent',
                'params' => [],
                'id' => time()
            ]
        ];

        $request_url = "http://admin:admin@127.0.0.1:8332";

        $client = new Client();
        $res = $client->post($request_url, $request_data);
        echo '<pre>';
        var_dump($res);
    }

}