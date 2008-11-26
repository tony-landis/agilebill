{ $block->display("core:top_clean") }

{$method->exe("module_method","view_methods")}
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
    	var module 		= 'module_method';		
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
    	var page 		= '{/literal}{$page}{literal}';
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
            <tr valign="middle" align="center" class="table_heading"> 
              <td width="19%" class="table_heading"> 
                {translate module=module_method}
                authorized 
                {/translate}
              </td>
              <td width="33%" class="table_heading"> 
                {translate module=module_method}
                field_name 
                {/translate}
              </td>
              <td width="48%" class="table_heading"> 
                {translate module=module_method}
                field_notes 
                {/translate}
              </td>
            </tr>
            <!-- LOOP THROUGH EACH RECORD -->
            {foreach from=$module_method item=record}
			
		    {if $record.checked == "2"}
              <tr id="row{$record.id}" class="{$record._C}"> 
              <td align="center" width="19%"> 
                <input type="checkbox" name="hidden_record{$record.id}" value="{$record.id}" checked disabled="Yes">
			  </td>
			  {else}
              <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1);" onMouseOver="row_mouseover('{$record.id}', 'row_mouse_over_select', 'row_mouse_over');" onMouseOut="row_mouseout('{$record.id}', '{$record._C}', 'row_select');" class="{$record._C}"> 
              <td align="center" width="19%"> 
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');">
			  </td>			  
			  {/if}     
              			  		  
              <td width="33%"> &nbsp; 
                {$record.name}
              </td>
              <td width="48%">&nbsp; 
                {$record.notes|truncate:"65"}
              </td>
            </tr>
			{if $record.checked == "1"}
			  {literal}
            	<script language="JavaScript">row_sel('{/literal}{$record.id}{literal}', 1, '{/literal}{$record._C}{literal}'); record_arr[i] = '{/literal}{$record.id}{literal}'; i++; </script>
			  {/literal}
			{elseif $record.checked == "3"}
			  {literal}
            	<script language="JavaScript">row_sel('{/literal}{$record.id}{literal}', 0, '{/literal}{$record._C}{literal}'); record_arr[i] = '{/literal}{$record.id}{literal}'; i++; </script>	
              {/literal}			
            {/if}
			{/foreach}
			
            <!-- END OF RESULT LOOP -->
          </table>
      </td>
    </tr>
  </form>
 </table>
  
  {if $VAR._print != TRUE}
  <div align="center">
    {translate module=module_method}
    note_inherit 
    {/translate}<br>
	<br>
  </div>
  <center>
    <input type="submit" name="Submit2" value="{translate module=module_method}update_relation{/translate}" onClick="mass_do('update_relations', module+':view_methods&module_method_group_id={$VAR.module_method_group_id}&module_method_module_id={$VAR.module_method_module_id}&_escape=1', limit, module);" class="form_button">
    <br>
    <br>
    <form name="form2" method="post" action="">
      <input type="submit" name="Deleteall" value="{translate module=module_method}delete_all{/translate}" class="form_button">
      <input type="hidden" name="_page" value="module_method:view_methods">
      <input type="hidden" name="do[]" value="module_method:update_relations">
      <input type="hidden" name="id" value="0,">
      <input type="hidden" name="module_method_module_id" value="{$VAR.module_method_module_id}">
      <input type="hidden" name="module_method_group_id" value="{$VAR.module_method_group_id}">
      <input type="hidden" name="_escape" value="1">
    </form>
    <input type="submit" name="Submit" value="{translate}select_all{/translate}" 	onClick="all_select(record_arr);" 		class="form_button">
		<input type="submit" name="Submit" value="{translate}deselect_all{/translate}" 	onClick="all_deselect(record_arr);" 	class="form_button">
    <br>
    <br>
  </center>
{/if}
{/if}
</div>