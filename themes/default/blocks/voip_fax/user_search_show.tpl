{$method->exe("voip_fax","user_search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        <h2>{translate results=$results}search_result_count{/translate}</h2>
    {else}
        <h2>{translate results=$results}search_results_count{/translate}</h2>
    {/if}
   {literal}
    <script language="JavaScript">
    <!-- START
    	var module 		= 'voip_fax';		
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

	{literal}
	<style type="text/css"> 
	#f { background-color:#DDE6F9; }
	</style>
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
          <tr valign="middle" align="center" class="table_heading">
            <td width="2%" class="table_heading">&nbsp;</td>
            <td width="10%" class="table_heading">
			   {translate module=voip_fax}view{/translate}
			</td>
            <td width="25%" class="table_heading">
               {translate module=voip_fax}field_date_orig{/translate} 
            </td>
            <td width="30%" class="table_heading">
               {translate module=voip_fax}field_src{/translate} 
            </td>
            <td width="15%" class="table_heading">
              {translate module=voip_fax}field_dst{/translate} 
            </td>
            <td width="10%" class="table_heading">
              {translate module=voip_fax}field_pages{/translate} 
            </td>
			 <!-- LOOP THROUGH EACH RECORD -->
			 {foreach from=$voip_fax item=record}
            <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1);" class="row1">             
              <td align="center" width="2%"><input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');"></td>
	            <td>&nbsp;<a href="?_page=core:blank&do[]=voip_fax:user_view&id={$record.id}&_escape=1">{translate module=voip_fax}view{/translate}</a></td>
	            <td>&nbsp;{$list->date_time($record.date_orig)}</td>
	            <td>&nbsp;{$record.clid}</td>
	            <td>&nbsp;{$record.dst}</td>
	            <td>&nbsp;{$record.pages}</td> 
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
{if $VAR._print != TRUE}<br>
<center>
<input type="submit" name="Submit" value="{translate}delete{/translate}" 		onClick="mass_do('user_delete', module+':search_show&search_id={$search_id}&page={$page}&order_by={$order}&{$sort}{$COOKIE_URL}', limit, module);" class="form_button">
<input type="submit" name="Submit" value="{translate}select_all{/translate}" 	onClick="all_select(record_arr);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}deselect_all{/translate}" 	onClick="all_deselect(record_arr);" 	class="form_button">
<input type="submit" name="Submit" value="{translate}range_select{/translate}" 	onClick="all_range_select(record_arr,limit);" class="form_button">
<br>
<br>
</center>
{/if}
{/if}
</div>
