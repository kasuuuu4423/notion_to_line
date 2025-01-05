<?php
require_once __DIR__ . '/vendor/autoload.php';

use GuzzleHttp\Client;

$client = new Client();
$pushMessageUrl = 'https://api.line.me/v2/bot/message/push';

$res = $client->post($pushMessageUrl, [
    'headers' => [
        'Authorization' => 'Bearer ' . getenv('LINE_SECRET'),
        'Content-Type' => 'application/json',
    ],
    'json' => [
        'to' => getenv('LINE_TO_ID'),
        'messages' => [
            [
                'type' => 'text',
                'text' => 'Hello, world!',
            ],
        ],
    ],
]);

var_dump($res);