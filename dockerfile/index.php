<?php
error_reporting(E_ALL);

/**
* 发送请求
* @param url $remote_server
* @param string $post_string
* @return string
*/
function request_by_curl($remote_server, $post_string) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $remote_server);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}

/**
* 多维数组转一维数组
* @param array $array
* @return array
*/
function toOneArray($array)
{
    static $ret = [];
    foreach ($array as $k => $v) {
        if (is_array($v)) {
            toOneArray($v);
        } else {
            $ret[$k] = $v;
        }
    }
    return $ret;
}

/**
* 发送消息
*/
function sendMsg($msg)
{
    $data = array(
        'msgtype' => 'text',
        'text' => array ('content' => $msg.'.'),
    );
    $jsonMsg = json_encode($data);
    $ACCESS_TOKEN='xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    $webhook = "https://oapi.dingtalk.com/robot/send?access_token=$ACCESS_TOKEN";
    $result = request_by_curl($webhook, $jsonMsg);
    print_r($result);
}


$data = file_get_contents('php://input');
file_put_contents("/tmp/access.log", $data."\n", FILE_APPEND);
//$data = utf8_encode(file_get_contents('test.txt'));
$arrMsg = json_decode($data, true);
$alerts = $arrMsg['alerts'];
unset($arrMsg['alerts']);

foreach($alerts as $val) {
    $alertsMsg[] = toOneArray($val);
}

$alertString = '';
foreach($alertsMsg as $key => $val) {
    foreach($val as $k => $v) {
        if(in_array($k, ['fingerprint', 'generatorURL'])) {
            continue;
        }
        if($k == 'status') {
            $alertString .= '【'.ucfirst($k).': '.$v."】\n";
        }else{
            $alertString .= $k.': '.$v."\n";
        }
    }
    $alertString .= "\n";
}
//print_r($alertString);
sendMsg($alertString);

