<?php
include('functions.php');

// 消息格式
$type = 3;

// TOKEN
$TOKEN = '';

// 钉钉接口
$webhook = "https://oapi.dingtalk.com/robot/send?access_token=$TOKEN";


// $data = $GLOBALS['HTTP_RAW_POST_DATA'];
$data = file_get_contents('php://input');
file_put_contents("/tmp/access.log", $data."\n", FILE_APPEND);

$message = getMsg($data);
if(!$message){
	file_put_contents("/tmp/access.log", 'error getMsg($data): do not have data'."\n", FILE_APPEND);
}

$data = getData($message, $type);
$data_string = json_encode($data);
$result = request_by_curl($webhook, $data_string);

file_put_contents("/tmp/access.log", $result."\n", FILE_APPEND);
