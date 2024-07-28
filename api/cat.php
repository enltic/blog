<?php
function getMimeType($extension) {
    $mimeTypes = [
        'webp' => 'image/webp',
        'png' => 'image/png',
        'jpg' => 'image/jpg'
    ];
    return $mimeTypes[$extension] ?? 'application/octet-stream';
}

function getJsonLink() {
    return 'https://new-api-1.pages.dev/image/三次元/cat/.json';
}

function getImageLink($value) {
    return "https://new-api-1.pages.dev/image/三次元/cat/{$value}";
}

$return = $_GET['return'] ?? 'image';

$jsonLink = getJsonLink();

$jsonData = file_get_contents($jsonLink);

if ($jsonData === false) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 500, 'message' => 'Unable to fetch JSON data']);
    exit;
}

$imageList = json_decode($jsonData, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    header('Content-Type: application/json');
    echo json_encode(['status' => 500, 'message' => 'Invalid JSON data']);
    exit;
}

$randomImage = $imageList[array_rand($imageList)];
$imageUrl = getImageLink($randomImage);

if ($return === 'json') {
    header('Content-Type: application/json');
    echo json_encode(['status' => 200, 'imageurl' => $imageUrl]);
} else {
    $extension = pathinfo($randomImage, PATHINFO_EXTENSION);
    $mimeType = getMimeType($extension);
    header('Content-Type: ' . $mimeType);
    echo file_get_contents($imageUrl);
}
?>