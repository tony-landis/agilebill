<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  {foreach from=$db_mapping_groups item=db_mapping_groups_local}
  <tr valign="top"> 
    <td width="35%" height="20"> 
      {$db_mapping_groups_local.name}
    </td>
    <td width="65%" height="20"> 
      {foreach from=$db_mapping_groups_local.remote item=db_mapping_groups_remote}
      <input type="checkbox" name="db_mapping_group_map[{$db_mapping_groups_local.id}][{$db_mapping_groups_remote.id}]" value="1" { if $db_mapping_groups_remote.check != false }checked{/if}>
      {$db_mapping_groups_remote.name}
      <br>
      {/foreach}
      <br>
    </td>
  </tr>
  {/foreach}
</table>

