<?php
include 'lib/IpLocation.php';

// 获取IP地址
$ip = isset($_GET['ip']) ? $_GET['ip'] : $_SERVER["REMOTE_ADDR"];

// 实例化IP地址查询类
$ipadress = new IpLocation();
$location = $ipadress->getlocation($ip);

// 处理返回的地理位置信息
$city = str_replace('–', '', $location['country']);

// 构造响应数据
$response = array(
    "code" => 200,
    "data" => array(
        "ip" => $ip,
        "city" => $city,
    )
);

// 返回JSON响应
exit(json_encode($response, JSON_UNESCAPED_UNICODE));
?>