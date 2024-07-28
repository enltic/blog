<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

function getTextLink($type) {
    $baseUrl = 'https://new-api-1.pages.dev/text/';
    return $baseUrl . $type . '.txt';
}

function getRandomLine($url) {
    $lines = @file($url, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    return ($lines !== false && count($lines) > 0) ? $lines[array_rand($lines)] : null;
}

$type = $_GET['type'] ?? null;

if ($type) {
    $textLink = getTextLink($type);
    $randomLine = getRandomLine($textLink);
    echo $randomLine ? $randomLine : "无法获取或读取提供链接中的内容。";
} else {
    echo "需要提供 'type' 参数。";
}

?>