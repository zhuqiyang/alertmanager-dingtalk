# alertmanager-dingtalk

&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Prometheus 对接钉钉报警的时候需要一个中间服务。因为prometheus没法直接发送编排好的格式，钉钉报警需要发送固定的格式，所以需要这个中间服务将要发送的消息编排好再发送给钉钉。

###### prometheus -> alertmanager -> this server -> dingding -> phone/pc client

##### 脚本列表：
+ index.php 主服务脚本
+ send.php 测试脚本
+ token 钉钉的token
+ test.txt 测试用的数据


##### 制作镜像：
```console
docker build -t dingtalk:v1.0 .
```

##### 启动容器：
```console
docker run --name dingtalk -d -p 80:80 dingtalk:1.0
```

##### 使用已近构建好的镜像：
需要修改 /var/www/html/token 文件
```console
docker run --name dingtalk -d -p 5002:80  docker.io/zhuqiyang/dingtalk:2.0
docker exec -it dingtalk bash
echo xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx > /var/www/html/token
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
