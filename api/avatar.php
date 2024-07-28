<?php

function getJsonLink($type) {
    $baseUrl = 'https://new-api-2.pages.dev/二次元/avatar/';
    return $baseUrl . $type . '/.json';
}

function getImageLink($type, $value) {
    $baseUrl = 'https://new-api-2.pages.dev/二次元/avatar/';
    return $baseUrl . $type . '/' . $value;
}

function getRandomValueFromJson($url) {
    $jsonContent = @file_get_contents($url);
    if ($jsonContent !== false) {
        $values = json_decode($jsonContent, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($values) && count($values) > 0) {
            return $values[array_rand($values)];
        }
    }
    return null;
}

function get_mime_type($imageName) {
    $extension = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
    $mimeTypes = [
        'webp' => 'image/webp',
        'png' => 'image/png',
        'jpg' => 'image/jpg'
    ];
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

function handleError($message) {
    echo $message;
    exit;
}

$type = $_GET['type'] ?? null;
$returnType = $_GET['return'] ?? 'image';

if (!$type || !$returnType) {
    handleError("需要同时提供 'type' 和 'return' 参数。");
}

$jsonLink = getJsonLink($type);
if (!$jsonLink) {
    handleError("无法构造 JSON 链接。");
}

$randomValue = getRandomValueFromJson($jsonLink);
if (!$randomValue) {
    handleError("无法获取或读取 JSON 链接中的值。");
}

$imageLink = getImageLink($type, $randomValue);
if (!$imageLink) {
    handleError("无法构造图片链接。");
}

if ($returnType == 'image') {
    $imageContent = @file_get_contents($imageLink);
    if ($imageContent === false) {
        handleError("无法获取图片内容。");
    }

    $mimeType = get_mime_type($randomValue);
    header('Content-Type: ' . $mimeType);
    echo $imageContent;
} elseif ($returnType == 'json') {
    $response = [
        'status' => '200',
        'imageurl' => $imageLink
    ];
    header('Content-Type: application/json');
    echo json_encode($response);
} else {
    handleError("提供的返回类型无效。");
}

?>