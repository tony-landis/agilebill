{$method->exe("account_admin","search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}
  <BR>
     

{literal} 
    <script language="JavaScript">
    <!-- START
    	var module 		= 'account_admin';		
    	{/literal}
    	{if $VAR._print == TRUE}
    	var p 			= '&_escape=y&_print=y';
    	{else}
    	var p 			= '';
    	{/if}{literal}
    	var IMAGE 		= '{/literal}{$NONSSL_IMAGE}{literal}';
    	var order 		= '{/literal}{$order}{literal}';
    	var sort1  		= '{/literal}{$sort}{literal}';
    	var search_id 	= '{/literal}{$search_id}{literal}';
    	var page 		= {/literal}{$page}{literal};
    	var pages		= '{/literal}{$pages}{literal}';
    	var results		= '{/literal}{$results}{literal}';
    	var limit 		= '{/literal}{$limit}{literal}';
    	record_arr = new Array ({/literal}{$limit}{literal});
    	var i = 0;	
    //  END -->
    </script>
    <SCRIPT SRC="themes/{/literal}{$THEME_NAME}{literal}/search.js"></SCRIPT>
{/literal}

<!-- SHOW THE SEARCH NAVIGATION MENU -->
<center><script language="JavaScript">document.write(search_nav_top());</script></center>

  
<!-- BEGIN THE RESULTS CONTENT AREA -->
<div id="search_results" onKeyPress="key_handler(event);"> 
  <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <form id="form1" name="form1" method="post" action="">
      <tr> 
        <td> 
          <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="3">
            <!-- DISPLAY THE SEARCH HEADING -->
            <tr valign="middle" align="center" class="table_heading"> 
              <td width="29" class="table_heading">&nbsp;</td>
              <td width="20" class="table_heading">&nbsp; </td>
              <td width="160" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=account_admin}field_last_name{/translate}{literal}','last_name'));
				 </script>
                {/literal}
              </td>
              <td width="188" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=account_admin}field_first_name{/translate}{literal}','first_name'));
				</script>
                {/literal}
              </td>
              <td width="217" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=account_admin}field_username{/translate}{literal}','username'));
				</script>
                {/literal}
              </td>
              <td width="246" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=account_admin}field_email{/translate}{literal}','email'));
				</script>
                {/literal}
              </td>
              <td width="120" class="table_heading">&nbsp;</td>
            </tr>
            <!-- LOOP THROUGH EACH RECORD -->
            {foreach from=$account item=record}
            <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1);" onDblClick="window.location='?_page=account_admin:view&id={$record.id},';" onMouseOver="row_mouseover('{$record.id}', 'row_mouse_over_select', 'row_mouse_over');" onMouseOut="row_mouseout('{$record.id}', '{$record._C}', 'row_select');" class="{$record._C}"> 
              <td align="center" width="29"> 
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');">
              </td>
              <td width="20"> 
                {if $record.status == "1"}
                <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0" width="16" height="16"> 
                {else}
                <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0" width="16" height="16"> 
                {/if}
              </td>
              <td width="160"> &nbsp; 
                {$record.last_name}
              </td>
              <td width="188"> &nbsp; 
                {$record.first_name}
              </td>
              <td width="217"> &nbsp; 
                {$record.username|truncate:25}
              </td>
              <td width="246">&nbsp; 
                {$record.email|truncate:25}
              </td>
              <td width="120" align="center"> <a href="?_page=account_admin:mail_one&mail_account_id={$record.id}"> 
                <img title="Send E-mail" src="themes/{$THEME_NAME}/images/icons/mail_16.gif" border="0" width="16" height="16"></a> 
                <a title="Login Logs" href="?_page=core:search&module=login_log&login_log_account_id={$record.id}"> 
                </a> <a title="Sessions" href="?_page=core:search&module=session&session_account_id={$record.id}&_next_page_one=view"> 
                </a> <a title="Services" href="?_page=core:search&module=service&service_account_id={$record.id}&_next_page_one=view"> 
                <img src="themes/{$THEME_NAME}/images/icons/tools_16.gif" alt="{translate module=account_admin}view_login_logs{/translate}" border="0" width="16" height="16"></a> 
                <a title="Invoices" href="?_page=core:search&module=invoice&invoice_account_id={$record.id}&_next_page_one=view"> 
                <img src="themes/{$THEME_NAME}/images/icons/calc_16.gif" alt="{translate module=account_admin}view_login_logs{/translate}" border="0" width="16" height="16"></a> 
              </td>
            </tr>
            {literal}
            <script language="JavaScript">row_sel('{/literal}{$record.id}{literal}', 0, '{/literal}{$record._C}{literal}'); record_arr[i] = '{/literal}{$record.id}{literal}'; i++; </script>
            {/literal}
            {/foreach}
            <!-- END OF RESULT LOOP -->
          </table>
        </td>
      </tr>
    </form>
  </table>
  
{if $VAR._print != TRUE}
  <div id="search_results" onKeyPress="key_handler(event);"> </div>
<br>

<center>

<input type="submit" name="Submit" value="{translate}view_edit{/translate}" 	onClick="mass_do('', module+':view', limit, module);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}delete{/translate}" 		onClick="mass_do('delete', module+':search_show&search_id={$search_id}&page={$page}&order_by={$order}&{$sort}', limit, module);" class="form_button">
<input type="submit" name="Submit" value="{translate}select_all{/translate}" 	onClick="all_select(record_arr);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}deselect_all{/translate}" 	onClick="all_deselect(record_arr);" 	class="form_button">
<input type="submit" name="Submit" value="{translate}range_select{/translate}" 	onClick="all_range_select(record_arr,limit);" class="form_button">

<br><br>

<table width="100%" border="0" cellspacing="0" cellpadding="5" align="center">
  <tr>
        <td valign="middle" align="center"> <a href="javascript:all_select(record_arr);" onClick="NewWindow('ExportWin','toolbar=no,status=no,width=300,height=175','?_page=core:export_search&module=account_admin&_escape=1&search_id={$search_id}&page={$page}&order={$order}&sort={$sort}');"><img src="themes/{$THEME_NAME}/images/icons/exp_32.gif" alt="{translate}search_export_image{/translate}" border="0"></a> 
          <a href="?_page=account_admin:mail_multi&search_id={$search_id}"><img src="themes/{$THEME_NAME}/images/icons/mail_32.gif" border="0"></a> 
          <a href="?_page=account_admin:search_show&_print=true&order_by={$order}&search_id={$search_id}&limit={$limit}&page={$page}"><img src="themes/{$THEME_NAME}/images/icons/print_32.gif" border="0" alt="{translate}search_print_image{/translate}"></a> 
          <a href="?_page=account_admin:search_form"><img src="themes/{$THEME_NAME}/images/icons/srch_32.gif" border="0" alt="{translate}search_new_image{/translate}"> 
          </a> <a href="?_page=account_admin:add"><img src="themes/{$THEME_NAME}/images/icons/add_32.gif" border="0" alt="{translate module=%%module%%}title_add{/translate}"></a> 
        </td>
  </tr>
</table>

</center>
{/if} {/if} </div>
