<?php

require __DIR__ . "/.env";

if (!isset($_SERVER['PHP_AUTH_USER']) || !($_SERVER['PHP_AUTH_USER'] == 'test' && $_SERVER['PHP_AUTH_PW'] == 'test' )) {
    header('WWW-Authenticate: Basic realm="ひみつだよ"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Not allowed to access.';
    exit;
}

if ($_COOKIE["token"]) {
    $access_token = $_COOKIE["token"];
} else {
    header('Location: auth.php');
    exit;
}

$action = $_GET['action'];
$item_id = $_GET['item_id'];
$page = $_GET['page'];

$url = 'https://getpocket.com/v3/send';
$content = http_build_query([
    'consumer_key' => $consumer_key,
    'access_token' => $access_token,
    'actions' => json_encode([
        [
            'action' => $action,
            'item_id' => $item_id,
        ],
    ]),
    ]);
var_dump($content);
$opts = array(
    'http'=>array(
          'method'=>"POST",
          'content' => $content,
    ),
);

$context = stream_context_create($opts);
$body = file_get_contents($url, false, $context);
$header = $http_response_header;

$json = json_decode($body, true);

if ($json['status']) {
    // echo 'OK';
    header('Location: /list.php?page=' . $page);
    exit;
} else {
    echo 'NG';
    var_dump($header, $json);
}

