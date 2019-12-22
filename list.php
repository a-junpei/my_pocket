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

$page = $_GET['page'];
if ($page > 0) {
  $offset = ($page - 1) * 10;
} else {
  $offset = 0;
}

$url = 'https://getpocket.com/v3/get';
$content = http_build_query([
    'consumer_key' => $consumer_key,
    'access_token' => $access_token,
    'state' => 'unread',
    'count' => 10,
    'offset' => $offset,
    'sort' => 'newest',
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
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">

<table class="u-full-width">
<tbody>
<?php foreach($json['list'] as $value) {
  $url = $value['resolved_url'] ? $value['resolved_url'] : $value['given_url'];
  $title = $value['resolved_title'] ? $value['resolved_title'] : $value['given_title'];
?>
  <tr>
    <td>
      <a href="<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a>
    </td>
    <td>
      <a href="/action.php?action=archive&item_id=<?php echo $value['item_id']; ?>&page=<?php echo $page; ?>">[アーカイブ]</a>
    </td>
  </tr>
<?php } ?>
</tbody>
</table>

<br>
<a href="/list.php?page=1">[1]</a>
<a href="/list.php?page=2">[2]</a>
<a href="/list.php?page=3">[3]</a>
<a href="/list.php?page=4">[4]</a>
<a href="/list.php?page=5">[5]</a>