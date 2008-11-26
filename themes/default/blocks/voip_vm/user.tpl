{$method->exe("voip_vm","vm_list")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}

{if $results == 1}
<h2>{translate results=$results}search_result_count{/translate}</h2>
{elseif $results > 1}
<h2>{translate results=$results}search_results_count{/translate}</h2>
{else}
<h2>{translate module=voip_vm}no_results{/translate}</h2>
{/if} 

{literal}
<style type="text/css">
#v { background-color:#FFFFCC; } 
</style>
{/literal}

{if $results > 0}
<div id="search_results">
 <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <form id="form1" name="form1" method="post" action="">
    <tr>
      <td>
        <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2"> 
          <tr valign="middle" align="center" class="table_heading">
            <td width="6%" class="table_heading">{translate module=voip_vm}listen{/translate}</td>
            <td width="8%" class="table_heading">{translate module=voip_vm}delete{/translate}</td>
            <td width="20%" class="table_heading">
              {translate module=voip_vm}date{/translate}</td>
            <td width="41%" class="table_heading">
              {translate module=voip_vm}from{/translate}</td>
            <td width="13%" class="table_heading">
              {translate module=voip_vm}filesize{/translate}</td>
            <td width="12%" class="table_heading">
              {translate module=voip_vm}length{/translate}</td> 
			 {foreach from=$voip_fax item=record}
          <tr id="v" class="row1">             
              <td align="center" width="6%">&nbsp;<a href="?_page=core:blank&do[]=voip_vm:vm_listen&id={$record.id}&did={$record.origmailbox}&_escape=1" target="_blank">{translate module=voip_vm}listen{/translate}</a>                 
              </td>
	            <td><div align="center"><a href="?_page=voip_vm:user&do[]=voip_vm:user_delete&id={$record.id}&did={$record.origmailbox}">{translate module=voip_vm}delete{/translate}</a></div></td>
	            <td>&nbsp;{$list->date_time($record.origtime)}</td>
	            <td>&nbsp;{$record.callerid}</td>
	            <td>&nbsp;{$record.size}KB</td>
	            <td>&nbsp;{$record.duration} Sec</td> 
          </tr> 
			{/foreach} 	 
        </table>
      </td>
    </tr>
  </form>
 </table>
 {else}

<center>
  <p>&nbsp;</p>
  <p><h3> 
    {translate}
    search_no_results 
    {/translate}
    </h3> </p>
  <form>
            
    <p>&nbsp;</p>
    <p>
      <input type="button" value="{translate}back{/translate}" onclick="history.back()" class="form_button">
      <input type="button" value="{translate}refresh{/translate}" onclick="location.reload()" class="form_button">
    </p>
  </form>
</center> 
{/if}
{/if}


