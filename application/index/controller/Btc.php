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
        dd($this->client->getblockchaininfo());
    }

    // 创建一个钱包账户
    public function getnewaddress()
    {
        $address = $_GET['address'] ?? NULL;

        if($address === NULL){
            dd("address can`t be null");exit;
        }

        dd($this->client->getnewaddress($address));
    }  

    // list账户
    public function listlabels()
    {
        dd($this->client->listlabels());
    }

    // 钱包总余额
    public function getbalance()
    {
        dd($this->client->getbalance());
    }  

    // 每个账户的余额
    public function listunspent()
    {
        dd($this->client->listunspent());
    }

}