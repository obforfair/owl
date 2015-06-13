#!/usr/bin/env php
<?php
isset($argv[1]) or die("请传入需要分析的nginx日志绝对路径\n");
$logHandle = @fopen($argv[1], "r") or die("打开日志文件失败\n");
$pool = [];
//着行读取nginx日志
while (($buffer = fgets($logHandle, 4096)) !== false) {
    $bufferArray = explode(' - ', $buffer);
    count($bufferArray) == 3 or die("你的log_format确定是'\$request_uri - \$request_time - \$upstream_response_time'?\n");
    if (!$bufferArray) continue;
    if (isset($pool[$bufferArray[0]])) {
        $pool[$bufferArray[0]]['request_time'] += $bufferArray[1];
        $pool[$bufferArray[0]]['upstream_response_time'] += $bufferArray[2];
        $pool[$bufferArray[0]]['count']++;
    } else {
        $pool[$bufferArray[0]] = [
            'request_time' => $bufferArray[1],
            'upstream_response_time' => $bufferArray[2],
            'count' => 1
        ];
    }
}

$requestTime = [];
foreach ($pool as $url => $data) {
    $requestTime[$url] = number_format($data['request_time'] / $data['count'], 3);
}
arsort($requestTime);
//格式化输出
echo str_pad('', 4, ' ') . str_pad('request_time(s)', 20, ' ') . str_pad('upstream_response_time(s)', 30, ' ') . str_pad('count(n)', 10, ' ') . str_pad('url', 10, ' ') . "\n";
$i = 1;
foreach ($requestTime as $url => $time) {
    $requestTimePad = str_pad($time, 20, ' ');
    $upstreamResponseTimePad = str_pad(number_format($pool[$url]['upstream_response_time'] / $pool[$url]['count'], 3), 30, ' ');
    $countPad = str_pad($pool[$url]['count'], 10, ' ');
    $urlPad = str_pad($url, 10, ' ');
    echo str_pad($i, 4, ' ') . $requestTimePad . $upstreamResponseTimePad . $countPad . $urlPad . "\n";
    $i++;
}
?>