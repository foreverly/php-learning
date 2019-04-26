<?php

$url = "http://regbtc:regbtc@127.0.0.1:18334";
$data = [
	'json' => [
        'jsonrpc' => '1.0',
        'method' => 'getnetworkinfo',
        'params' => [],
        'id' => time()
    ]
];

$res = curlPost($url, $data);

var_dump($res);
// curl -X POST -d '{"jsonrpc":"1.0","method":"getnetworkinfo","params":[],"id":"123"}'  http://admin:admin@127.0.0.1:8332
// curl --user admin --data-binary '{"jsonrpc": "1.0", "id":"curltest", "method": "getnetworkinfo", "params": [] }'   -H 'content-type: text/plain;' http://127.0.0.1:8332/
// curl -X POST -H "Content-Type: text/plain" 127.0.0.1:8332 -u admin:admin --data '{"method": "getblockhash","params": [0],"id": "foo"}'
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
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $ret = curl_exec($ch);

    if ($ret === false) {
    	var_dump(curl_error($ch));  //查看报错信息
    }    

    curl_close($ch);

    return $ret;
}