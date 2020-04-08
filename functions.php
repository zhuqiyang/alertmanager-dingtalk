<?php

/**
* 排盘数据格式，符合钉钉接口发送的格式
* @param array $message  原始数组
* @param int $type       类型
* @return array $data    带有格式的数组
*/
function getData($message, $type = 3){
	if($type == 1){
		$data = array (
			'msgtype' => 'text',
			'text' => array ('content' => json_encode($message))
		);
	}else if($type == 2){
		$data = array(
			'msgtype' => 'link',
			'link' => array(
				'text' => $message['message'],
				'title' => $message['alertname'],
				'picUrl' => '',
				'messageUrl' => $message['generatorURL']
			),
		);
	}else if($type == 3){
		$keys = array("receiver", "generatorURL", "version", "status", "fingerprint");
		$msg = "### [".ucfirst($message['status'])."] ".$message['alertname']."\n";
		$msg .= "> [".$message['message']."](".$message['generatorURL'].")\n\n";
		$msg .= "> ----------------------------\n\n";
		foreach ($message as $key => $value){
			if (in_array($key, $keys)){
				continue;
			}
			$msg .= "- ".$key.": ".$value."\n\n";
		}
		//"> ![screenshot](https://gw.alicdn.com/tfs/TB1ut3xxbsrBKNjSZFpXXcXhFXa-846-786.png)\n";
		$data = array(
			'msgtype' => 'markdown',
			'markdown' => array(
				'title' => "[".ucfirst($message['status'])."] ".$message['alertname'],
				'text' => $msg
			),
		);
	}
	return $data;
}


/**
* 获取键值信息,json_decode解析不了prometheus发来的json
* @param string $str
* @return array
*/
function getMsg($str){
	$reg = '/\"\w+\":\"(?:(?!{}).)*?\"/';
	preg_match_all($reg, $str, $ret);

	$msg = [];
	foreach ($ret[0] as $key => $value){
		$pos = strpos($value, ':');
		$msg[substr($value, 1, $pos-2)] = rtrim(substr($value, $pos+2), '"');
	}
	$msg['generatorURL'] = preg_replace("/prometheus-k8s-\d+:9090/", "192.168.0.71:31674", $msg['generatorURL']);
	return $msg;
}


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
    // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
?>
