<?php
require_once __DIR__ . '/require.php';

use GuzzleHttp\Client;

$client = new Client(
    [\GuzzleHttp\RequestOptions::VERIFY => false]
);

// POSTデータのバリデーション
$postData = json_decode(file_get_contents('php://input'), true) ?? [];


// Notionのプロパティから値を取得
$properties = $postData['data']['properties'];

// 名前の取得
$name = '名前なし';
if (!empty($properties['Name']['title'][0]['plain_text'])) {
    $name = $properties['Name']['title'][0]['plain_text'];
}

// IDの取得
$id = 'ID未設定';
if (!empty($properties['ID']['unique_id']['number'])) {
    $id = 'ID-' . $properties['ID']['unique_id']['number'];
}

// ステータスの取得
$status = 'ステータス未設定';
if (!empty($properties['Status']['status']['name'])) {
    $status = $properties['Status']['status']['name'];
}

$editor = '編集者不明';
if (!empty($properties['LastEditedBy']['people'][0]['name'])) {
    $editor = $properties['LastEditedBy']['people'][0]['name'];
}

$message = "$id 「 $name 」が
「 $editor 」 さんによって
「 $status 」に移動されました。";

// デバッグログ
error_log("Sending message: " . $message);
error_log("POST data: " . print_r($postData, true));

$pushMessageUrl = 'https://api.line.me/v2/bot/message/push';

$res = $client->post($pushMessageUrl, [
    'headers' => [
        'Authorization' => 'Bearer ' . $_ENV['LINE_CHANNEL_ACCESS_TOKEN'],
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'to' => $_ENV['LINE_TO_ID'],
        'messages' => [
            [
                'type' => 'text',
                'text' => $message . "\n" . date('Y-m-d H:i:s'),
            ],
        ],
    ],
]);

// デバッグログ
error_log("Response: " . $res->getBody());
error_log("Response status: " . $res->getStatusCode());