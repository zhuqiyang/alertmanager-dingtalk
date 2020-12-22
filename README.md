# alertmanager-dingtalk

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prometheus 对接钉钉报警的时候需要一个中间服务。因为prometheus没法直接发送编排好的格式，钉钉报警需要发送固定的格式，所以需要这个中间服务将要发送的消息编排好再发送给钉钉。

###### prometheus -> alertmanager -> this server -> dingding -> phone/pc client

##### 脚本列表：
+ index.php 主服务脚本
+ functions.php 用到的一些函数
+ send.php 测试脚本


##### 制作镜像：
```console
docker build -t dingtalk:v1.0 .
```

##### 启动容器：
```console
docker run --name dingtalk -d -p 80:80 -e ACCESS_TOKEN=ad6c4b36695c1670ed27421f877c593c50d9ff9078d537fe2ff1201537961ea1 docker.io/zhuqiyang/dingtalk:1.0
```

##### 测试请求：
如果成功会从钉钉里看到报警
```console
http://192.168.0.10/send.php
```

##### 查看日志：
发送第一个请求后日志文件才会创建
```console
docker exec -it dingtalk tail -f /tmp/access.log
```

镜像没有问题之后可以上传到镜像仓库，然后在kubernetes中使用即可。

##### 在kubernetes中的使用
打开 dingtalk-deployment.yaml 文件修改环境变量 ACCESS_TOKEN 的值，保存后创建资源。
```console
kubectl apply -f dingtalk-deployment.yaml
```

##### 将请求地址写到alertmanager的配置中即可
如果是使用prometheus-operator部署的prometheus则可以修改 alertmanager-secret.yaml 文件，修改后应用资源文件即可。
```yaml
apiVersion: v1
kind: Secret
metadata:
  name: alertmanager-main
  namespace: monitoring
stringData:
  alertmanager.yaml: |-
    global:
    route:
      group_by: ["alertname"]
      group_wait: 30s
      group_interval: 5m
      repeat_interval: 10m
      receiver: "web.hook"
    receivers:
    - name: "web.hook"
      webhook_configs:
      - url: "http://dingtalk.default.svc.cluster.local:5001" # 请求地址,也可以使用ClusterIP
type: Opaque
```
##### 查看配置是否被加载
需要将alertmanager的ClusterIP改成NodePort类型，如果新配置没有被加载一般都是配置格式写错了。
```console
http://192.168.1.10:30808/#/status
```
