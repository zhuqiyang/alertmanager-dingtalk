<?php
error_reporting(E_ALL);
header('content-type:text/html;charset=utf-8');

$url = "http://localhost/index.php";
$json = '{"receiver":"web\\.hook","status":"firing","alerts":[{"status":"firing","labels":{"alertname":"kube-scheduler-Unavailable","endpoint":"http-metrics","instance":"192.168.1.70:10251","job":"kube-scheduler","namespace":"kube-system","prometheus":"monitoring/k8s","service":"kube-scheduler","severity":"critical"},"annotations":{"summary":"kube-scheduler down"},"startsAt":"2020-04-07T05:06:36.264068709Z","endsAt":"0001-01-01T00:00:00Z","generatorURL":"http://prometheus-k8s-0:9090/graph?g0.expr=up%7Bjob%3D%22kube-scheduler%22%7D+%3D%3D+0\u0026g0.tab=1","fingerprint":"f84b1b6b4a3922a5"}],"groupLabels":{"alertname":"kube-scheduler-Unavailable"},"commonLabels":{"alertname":"kube-scheduler-Unavailable","endpoint":"http-metrics","instance":"192.168.1.70:10251","job":"kube-scheduler","namespace":"kube-system","prometheus":"monitoring/k8s","service":"kube-scheduler","severity":"critical"},"commonAnnotations":{"summary":"kube-scheduler down"},"externalURL":"http://alertmanager-main-0:9093","version":"4","groupKey":"{}:{alertname=\"kube-scheduler-Unavailable\"}"}';


//$data = utf8_encode($json);

$data = utf8_encode(file_get_contents('test.txt'));

$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HEADER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
$con = curl_exec($curl);
curl_close($curl);

echo $con;
