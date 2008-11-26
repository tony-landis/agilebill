<form action="" method="get">
<h3>Found {$count} Record(s)...</h3>
{if $count > 0}
<div class="results">
<table width="100%" border="0" cellspacing="0">
  {foreach from=$results item=ticket} 
  <tr id="ticket_id_{$ticket.id}">
  	<td align="left" valign="top" width="150"><div style="overflow:hidden"><b>{$ticket.email}</b></div></td>
    <td align="left" valign="top" width="350">
		<div style="overflow:hidden">
		<font color="#{if $ticket.status==1}CC9900{elseif $ticket.status==2}666666{elseif $ticket.status==3}0099CC{else}990000{/if}">
		<b>{$ticket.subject}</b> 		 
		</font>  
		</div>  
	</td>
    <td align="right" valign="top" width="200">{$ticket.department} | {$list->date($ticket.date_orig)}</td> 
  </tr> 
  <tr id="ticket_id2_{$ticket.id}">
      <td colspan="2" align="left" valign="top"><font color="#666666">&nbsp;&nbsp;{$ticket.body|truncate:115:"..."}</font></span></td>
      <td align="right" valign="top">	  
 	    <a href="?_page=ticket:view_quick&_escape=1&id={$ticket.id}" target="_blank">View</a> | 
		<a href="?_page=ticket:main&do[]=ticket:delete&delete_id={$ticket.id}&department={$VAR.department}&status={$VAR.status}&query={$VAR.query}&query_type={$VAR.query_type}">Delete</a> 
	  </td> 
  </tr>  
  {/foreach}
</table> 
</div>  
{/if}
<input type="hidden" name="_page" value="ticket:main">
<input type="hidden" name="url" value="{$VAR.url}">
<input type="hidden" name="do[]" value="ticket">
</form>