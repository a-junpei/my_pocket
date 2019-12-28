<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/skeleton/2.0.4/skeleton.min.css">

<div class="container">

<table class="u-full-width">
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
<br>

<div class="u-full-width">
{for $i=1 to 10}
  {if $i == $page}
    [{$i}]
  {else}
    <a href="./list.php?page={$i}">[{$i}]</a>
  {/if}
{/for}
</div>

</div>