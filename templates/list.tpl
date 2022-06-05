<!DOCTYPE html>
<html>
  <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css">
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