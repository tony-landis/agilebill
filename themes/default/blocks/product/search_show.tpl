{$method->exe("product","search_show")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}
    {if $results == 1}
        {translate results=$results}search_result_count{/translate}
    {else}
        {translate results=$results}search_results_count{/translate}
    {/if}	 
  <BR>
  {popup_init src="$URL/includes/overlib/overlib.js"}
		
  {literal}
    <script language="JavaScript">
    <!-- START
    	var module 		= 'product';		
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
            <tr valign="middle" class="table_heading"> 
              <td width="5%" class="table_heading" align="center">&nbsp;</td>
              <td width="25%" class="table_heading" align="center"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=product}field_sku{/translate}{literal}','sku'));
				 </script>
                {/literal}
              </td>
              <td width="15%" class="table_heading" align="center"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=product}field_price_base{/translate}{literal}','price_base'));
				 </script>
                {/literal}
              </td>
              <td width="5%" class="table_heading" align="center"> 
                {literal}
                <script language="JavaScript">
					document.write(search_heading('{/literal}{translate module=product}field_active{/translate}{literal}','active'));
				 </script>
                {/literal}
              </td>
              <td width="50%" class="table_heading" align="center"> 
                {translate 
                module=product_translate}
                field_description_short 
                {/translate}
              </td>
              <td width="2%" class="table_heading" align="center">&nbsp;</td>
              <!-- LOOP THROUGH EACH RECORD -->
              {foreach from=$product item=record}
            <tr id="row{$record.id}" onClick="row_sel('{$record.id}',1);" onDblClick="window.location='?_page=product:view&id={$record.id},';" onMouseOver="row_mouseover('{$record.id}', 'row_mouse_over_select', 'row_mouse_over');" onMouseOut="row_mouseout('{$record.id}', '{$record._C}', 'row_select');" class="{$record._C}"> 
              <td align="center" width="5%"> 
                <input type="checkbox" name="record{$record.id}" value="{$record.id}" onClick="row_sel('{$record.id}',1,'{$record._C}');">
              </td>
              <td width="25%"> 
                <p>&nbsp; 
                  {$record.sku}
              </td>
              <td width="15%"> &nbsp; 
                {$list->format_currency($record.price_base,"")}
              </td>
              <td width="5%" align="center"> 
                {if $record.active == "1"}
                <img src="themes/{$THEME_NAME}/images/icons/go_16.gif" border="0" width="16" height="16"> 
                {else}
                <img src="themes/{$THEME_NAME}/images/icons/stop_16.gif" border="0" width="16" height="16"> 
                {/if}
              </td>
              <td width="50%"> &nbsp; 
                {if $list->translate("product_translate","name,description_short","id",$record.id, "name")}
                {$name.name|truncate:50}
                {else}
                --- 
                {/if} 
              </td>
              <td width="2%"> 
                {if {$name.short_description != ""}
                {assign var="descshort" value=$name.description_short|strip_tags}
                <a href="javascript:showTranslations({$record.id},'{$smarty.const.DEFAULT_LANGUAGE}')" {if $descshort != ""}{popup capcolor="ffffff" textcolor="333333" bgcolor="506DC7" fgcolor="FFFFFF" sticky=false width="250" caption="Short Description" text="$descshort" snapx=1 snapy=1 sticky=1}{/if}>
				<img src="themes/{$THEME_NAME}/images/icons/edit_16.gif" border="0" width="16" height="16"> 
                </a> 
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
{if $VAR._print != TRUE}<br>
<center>
<input type="submit" name="Submit" value="{translate}view_edit{/translate}" 	onClick="mass_do('', module+':view', limit, module);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}delete{/translate}" 		onClick="mass_do('delete', module+':search_show&search_id={$search_id}&page={$page}&order_by={$order}&{$sort}', limit, module);" class="form_button">
<input type="submit" name="Submit" value="{translate}select_all{/translate}" 	onClick="all_select(record_arr);" 		class="form_button">
<input type="submit" name="Submit" value="{translate}deselect_all{/translate}" 	onClick="all_deselect(record_arr);" 	class="form_button">
<input type="submit" name="Submit" value="{translate}range_select{/translate}" 	onClick="all_range_select(record_arr,limit);" class="form_button">
<br>
</center>
{/if}
{/if}
</div>

{literal}
<script language=javascript>
function  showTranslations(product_id,language_id)
{    
	var url = '?_page=core:search_iframe&module=product_translate&product_translate_language_id='+
			   language_id+'&product_translate_product_id='+
			   product_id+'&_escape=1&_escape_next=1&_next_page_one=view&_next_page_none=add&name_id1=product_translate_product_id&val_id1='
			   +product_id+'&name_id2=product_translate_language_id&val_id2='+language_id;		  	 			   
	window.open(url,'ProductLanguage','scrollbars=yes,toolbar=no,status=no,width=700,height=600'); 
}  
</script>{/literal}
