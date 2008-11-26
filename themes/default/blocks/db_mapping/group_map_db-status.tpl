
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="32%" height="20"><b>Local Group</b></td>
    <td width="50%" height="20"><b>Mapped To Group</b></td>
    <td width="18%" height="20"><b>Authentication Rank</b></td>
  </tr>
  
  {$list->unserial($db_mapping.group_rank, "group_rank")} 
  {foreach from=$db_mapping_groups item=db_mapping_groups_local}
  {if $db_mapping_groups_local.id != 0}
  
  
  <tr valign="top"> 
    <td width="32%" height="20"> 
      {$db_mapping_groups_local.name}
    </td>
    <td width="50%" height="20"> 
      {foreach from=$db_mapping_groups_local.remote item=db_mapping_groups_remote}
      <input type="checkbox" name="db_mapping_group_map[{$db_mapping_groups_local.id}][{$db_mapping_groups_remote.id}]" value="{$db_mapping_groups_remote.id}" { if $db_mapping_groups_remote.check != false }checked{/if}>
      {$db_mapping_groups_remote.name}
      <br>
      {/foreach}
      <br>
    </td>
    <td width="18%" height="20"> 
      <div align="center">
        {foreach from=$group_rank item=rank}
        {if $db_mapping_groups_local.id == $rank.id}
        {assign var=rank_val value=$rank.rank}
        {if $rank_val == ""}
        {assign var=rank_val value=0}
        {/if}
        {/if}
        {/foreach}
        <input type="text" name="db_mapping_group_rank[{$db_mapping_groups_local.id}][rank]" size="2"  value="{$rank_val}">
		<input type="hidden" name="db_mapping_group_rank[{$db_mapping_groups_local.id}][id]" size="2"  value="{$db_mapping_groups_local.id}">
      </div>
    </td>
  </tr>
  {/if}
  {/foreach}
</table>

