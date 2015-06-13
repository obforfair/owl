# owl
相信大家在对基于HTTP协议的服务进行性能优化时，第一头疼的是如何简单的探测到哪些URL存在性能瓶颈。<br>
本项目就是为了解决这个需求。<br>
通过分析httpd服务器的访问日志，可以快速定位到最消耗时间的请求，然后使用语言分析工具对指定URL进行分析。<br>

#分析结果展示
``` bash
$ ./owl.php /var/log/nginx/response_time.log 
平均处理时间(s) 请求地址 
28.368       /controller/action1 
5.045        /controller/action2 
0.070        /controller/action3 
0.010        /controller/action4 
```

#安装步骤
本公司的生成环境是Nginx + PHP-FPM，所以这里以分析nginx日志为例进行介绍：<br>
###1.格式化nginx请求日志
在nginx的http指令中增加
``` bash
log_format  response_time '$request_uri - $upstream_response_time';
access_log /var/log/nginx/response_time.log response_time;
```
讲解：<br>
    定义一个名为response_time的log_format，只包含两个变量：<br>
    $request_uri  请求的URL地址<br>
    $upstream_response_time  PHP处理这个请求消耗的时间<br>
###2.执行分析脚本
reload nginx配置文件，等待nginx处理一些请求之后，运行分析脚本，获得结果：<br>
``` bash
$ ./owl.php /var/log/nginx/response_time.log 
平均处理时间(s) 请求地址 
28.368       /controller/action1 
5.045        /controller/action2 
0.070        /controller/action3 
0.010        /controller/action4 
```
###3.使用xhprof轻松定位PHP中性能bug(图片来自网络)
xhprof确实是个神奇的工具，可以快速定位到那些函数方法最耗时间：<br>
![](https://raw.githubusercontent.com/freemanCD/owl/master/Images/xhprof-2.jpg)
![](https://raw.githubusercontent.com/freemanCD/owl/master/Images/xhprof-1.jpg)

#小技巧
###1.记录多个nginx日志
可能因为其他原因不能随便修改nginx日志格式，不用担心，nginx支持记录多个nginx日志，主要再增加一行`access_log`指令即可：<br>
``` bash
access_log /var/log/nginx/response_time.log response_time;
```

