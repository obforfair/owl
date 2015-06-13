# owl
相信大家在执行基于HTTP协议性能优化时，第一头疼的是如何简单的探测到哪些URL存在性能瓶颈。
本项目就是为了解决这个需求。
通过分析httpd服务器的访问日志，可以快速定位到最消耗时间的请求，然后使用语言分析工具对指定URL进行分析。

#分析结果展示
``` bash
$ ./owl.php /var/log/nginx/response_time.log 
平均处理时间(s) 请求地址 
28.368       /controller/action1 
5.045        /controller/action2 
0.070        /controller/action3 
0.010        /controller/action4 
```
