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
      <a href="/action.php?action=archive&item_id={$value.item_id}&page={$page}">[アーカイブ]</a>
    </td>
  </tr>
{/foreach}
</tbody>
</table>
<br>

<div class="u-full-width">
<a href="/list.php?page=1">[1]</a>
<a href="/list.php?page=2">[2]</a>
<a href="/list.php?page=3">[3]</a>
<a href="/list.php?page=4">[4]</a>
<a href="/list.php?page=5">[5]</a>
</div>

</div>