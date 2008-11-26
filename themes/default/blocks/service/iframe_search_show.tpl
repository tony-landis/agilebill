{ $block->display("core:top_clean") }

{$method->exe("service","search_show")}
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
    	var module 		= 'service';		
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
          <table id="main2" width="100%" border="0" cellspacing="1" cellpadding="2">
            <!-- DISPLAY THE SEARCH HEADING -->
            <!-- LOOP THROUGH EACH RECORD -->
            {foreach from=$service item=record}
            <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1); parent.window.location='?_page=service:view&id={$record.id}';" onMouseOver="row_mouseover('{$record.id}', 'row_mouse_over_select', 'row_mouse_over');" onMouseOut="row_mouseout('{$record.id}', '{$record._C}', 'row_select');" class="{$record._C}"> 
              <td align="center" width="5%" height="20"> 
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');">
              </td>
              <td width="29%" height="20">&nbsp; 
                {$list->date_time($record.date_orig)}
              </td>
              <td width="54%" height="20">&nbsp; 
                {$record.sku}
                &gt; 
                {if $record.type == 'domain'}
                <u> 
                {$record.domain_name|upper|trim:16}
                . 
                {$record.domain_tld|upper}
                </u> 
                {elseif $record.type == 'host' || $record.type == 'host_group'}
                <i> 
                {$record.server_name|trim:25}
                </i> 
                {else}
                {translate module=service}
                {$record.type}
                {/translate}
                {/if}
              </td>
              <td width="12%" height="20" valign="middle"> 
                {if $record.active == "1"}
                <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0"> 
                {else}
                <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0"> 
                {/if}
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
 
  {/if}
</div>
