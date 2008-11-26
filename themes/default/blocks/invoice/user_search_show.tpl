{$method->exe("invoice","user_search_show")}
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
    	var module 		= 'invoice';		
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
    <SCRIPT SRC="themes/{/literal}{$THEME_NAME}{literal}/user_search.js"></SCRIPT>
    {/literal}


  {$method->exe("invoice","has_unpaid")}
  {if $has_unpaid}
    <br>
     <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="body">
        <tr> 
          <td valign="top" align="center" width="35%"> 
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
              <tr> 
                <td> 
                  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background"> 
                      <tr> 
                        <td> 
                          <table width="100%" border="0" cellspacing="1" cellpadding="0">
                            <tr valign="top"> 
                              <td width="65%" class="row1"> 
                                <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                                  <tr> 
                                    <td width="74%"> <div align="center">{translate module=invoice total=$has_unpaid}due_invoices_notice{/translate}</div></td>
                                  </tr>
                                </table>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </form>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
    </table>
<br><br>
{/if}


    <!-- SHOW THE SEARCH NAVIGATION MENU -->
    <center><script language="JavaScript">document.write(search_nav_top());</script></center>

<!-- BEGIN THE RESULTS CONTENT AREA -->
<div id="search_results" onKeyPress="key_handler(event);"> 
  <table id="main1" width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <form id="form1" name="form1" method="post" action="">
      <tr> 
        <td> 
          <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
            <!-- DISPLAY THE SEARCH HEADING -->
            <tr valign="middle" align="center" class="table_heading"> 
              <td width="53" class="table_heading">&nbsp;</td>
              <td width="201" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=invoice}field_id{/translate}{literal}','id'));
				 </script>
                {/literal}
              </td>
              <td width="256" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=invoice}field_date_orig{/translate}{literal}','date_orig'));
				 </script>
                {/literal}
              </td>
              <td width="228" class="table_heading"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=invoice}field_total_amt{/translate}{literal}','total_amt'));
				 </script>
                {/literal}
              </td>
              <td width="114" class="table_heading">&nbsp; </td>
              <!-- LOOP THROUGH EACH RECORD -->
              {foreach from=$invoice item=record}
            <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1);" onDblClick="window.location='?_page=invoice:user_view&id={$record.id},';" onMouseOver="row_mouseover('{$record.id}', 'row_mouse_over_select', 'row_mouse_over');" onMouseOut="row_mouseout('{$record.id}', '{$record._C}', 'row_select');" class="{$record._C}"> 
              <td align="center" width="53"> 
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');">
              </td>
              <td width="201">&nbsp; 
                { $record.id}
              </td>
              <td width="256">&nbsp; 
                {$list->date_time($record.date_orig)}
              </td>
              <td width="228"> 
                <div align="right"> 
                  {$list->format_currency_num($record.total_amt, $record.actual_billed_currency_id)}
                  &nbsp; </div>
              </td>
              <td width="114"> 
                <center>
                  &nbsp; 
                  {if $record.billing_status == "1"}
                  <img src="themes/{$THEME_NAME}/images/icons/add_16.gif" border="0"> 
                  {else}
                  <img src="themes/{$THEME_NAME}/images/icons/remov_16.gif" border="0"> 
                  {/if}
                  &nbsp; 
                  {if $record.process_status == "1"}
                  <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0"> 
                  {else}
                  <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0"> 
                  {/if}
                </center>
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
  <br> 
  	{if $has_unpaid} 
	<input value="{translate module=invoice}pay_selected{/translate}" type="button" onClick="mass_do('', module+':checkout_multiple', limit, module);">
   	{/if}
 	<input type="submit" name="Submit" value="{translate}view{/translate}" onClick="mass_do('', module+':user_view', limit, module);" >
{/if} 
