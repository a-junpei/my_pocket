<?php

// ini_set('display_errors', "On");
// ini_set('error_reporting', E_ALL);

require __DIR__ . '/vendor/autoload.php';

if ($_COOKIE["token"]) {
  $access_token = $_COOKIE["token"];
} else {
  header('Location: auth.php');
  exit;
}

$page = $_GET['page'];
if (!$page) {
  $page = 1;
}
$offset = ($page - 1) * 10;

$order = $_GET['order'];
if ($order !== 'oldest') {
  $order = 'newest';
}

$url = 'https://getpocket.com/v3/get';
$content = http_build_query([
    'consumer_key' => $_ENV['CONSUMER_KEY'],
    'access_token' => $access_token,
    'state' => 'unread',
    'count' => 10,
    'offset' => $offset,
    'sort' => $order,
    'detailType' => 'complete',
    ]);
$opts = array(
    'http'=>array(
          'method'=>"POST",
          'content' => $content,
    ),
);

$context = stream_context_create($opts);
$body = file_get_contents($url, false, $context);
$header = $http_response_header;

// var_dump($body, $header);

// var_dump(json_decode($body, true));
$json = json_decode($body, true);
// var_dump($json);

$list = [];
foreach($json['list'] as $key => $value) {
  $list[$key]['item_id'] = $value['item_id'];
  $list[$key]['url'] = $value['resolved_url'] ? $value['resolved_url'] : $value['given_url'];
  $list[$key]['title'] = $value['resolved_title'] ? $value['resolved_title'] : $value['given_title'];
}

$smarty = new Smarty();
$smarty->setTemplateDir('templates')->setCacheDir('cache')->setCompileDir('templates_c')->setCacheDir('cache')->setConfigDir('configs');
$smarty->assign('list', $list);
$smarty->assign('page', $page);
$smarty->display('list.tpl');
