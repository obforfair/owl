#!/usr/bin/env php
<?php
isset($argv[1]) or die("请传入需要分析的nginx日志绝对路径\n");
$logHandle = @fopen($argv[1], "r") or die("打开日志文件失败\n");
$pool = [];
//着行读取nginx日志
while (($buffer = fgets($logHandle, 4096)) !== false) {
    $bufferArray = explode(' - ', $buffer);

    if (!$bufferArray) continue;
    if (isset($pool[$bufferArray[0]])) {
        $pool[$bufferArray[0]]['time'] += $bufferArray[1];
        $pool[$bufferArray[0]]['count']++;
    } else {
        $pool[$bufferArray[0]] = [
            'time' => $bufferArray[1],
            'count' => 1
        ];
    }
}

$output = [];
foreach ($pool as $url => $data) {
    $output[$url] = number_format($data['time'] / $data['count'], 3);
}
arsort($output);

//格式化输出
echo str_pad('time(s)', 10, ' ') . str_pad('count(n)', 10, ' ') . str_pad('url', 10, ' ') . "\n";
foreach ($output as $url => $time) {
    echo str_pad($time, 10, ' ') . str_pad($pool[$url]['count'], 10, ' ') . str_pad($url, 10, ' ') . "\n";
}
?>