<!DOCTYPE html>
<html ⚡>
  <head>
      <meta charset="utf-8">
      <link rel="canonical" href="http://34.70.191.22/my_pocket/list.php">
      <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
{literal}
      <style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
{/literal}
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
      <script async src="https://cdn.ampproject.org/v0.js"></script>
  </head>

<body>

<div class="content">
<table class="table is-striped">
<tbody>
{foreach $list as $value}
  <tr>
    <td>
      <a href="{$value.url}" target="_blank">{$value.title}</a>
    </td>
    <td>
      <a href="./action.php?action=archive&item_id={$value.item_id}&page={$page}">[アーカイブ]</a>
    </td>
  </tr>
{/foreach}
</tbody>
</table>
</div>
<br>

<nav class="pagination is-centered" role="navigation" aria-label="pagination">
  <a class="pagination-previous" href="./list.php?order=oldest">↑</a>
  <a class="pagination-next" href="./list.php?order=newest">↓</a>
  <ul class="pagination-list">
    {for $i=1 to 10}
      <li>
      {if $i == $page}
        <a class="pagination-link is-current" href="#">{$i}</a>
      {else}
        <a class="pagination-link" href="./list.php?page={$i}">{$i}</a>
      {/if}
      </li>
    {/for}
  </ul>
</nav>

</body>