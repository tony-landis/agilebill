
{ $method->exe("static_page","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'static_page';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
    	if (locations != "")
    	{
    		refresh(0,'#'+locations)
    	}
    	// Mass update, view, and delete controller
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$static_page item=static_page} <a name="{$static_page.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="static_page_view" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=static_page}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($static_page.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($static_page.date_orig)}
                  </td>
                </tr>							  
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%">
                    <input type="text" name="static_page_sort_order" value="{$static_page.sort_order}"  size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->calender_view("static_page_date_start", $static_page.date_start, "form_field", $static_page.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("static_page_date_expire", $static_page.date_expire, "form_field", $static_page.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_static_page_category_id 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "static_page_static_page_category_id", "static_page_category", "name", $static_page.static_page_category_id, "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("static_page_status", $static_page.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_page_name" value="{$static_page.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=static_page}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_page_description" cols="40" rows="5" >{$static_page.description}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$static_page.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="5" class="row1">
                <tr> 
                  <td width="42%"> <a href="?_page=core:search&module=static_page_translate&static_page_translate_static_page_id={$static_page.id}&_next_page_one=view"> 
                    {translate module=static_page}
                    view_all 
                    {/translate}
                    </a></td>
                  <td align="right" width="39%"> <a href="?_page=static_page_translate:add&id={$static_page.id}"> 
                    {translate module=static_page}
                    add_translation 
                    {/translate}
                    </a> </td>
                  <td align="right" width="19%"><a href="{$UR}?_page=static_page_translate:add&id={$static_page.id}"> 
                    </a><a href="{$URL}?_page=static_page:show&name={$static_page.name}" target="_blank"> 
                    {translate module=static_page}
                    preview 
                    {/translate}
                    </a></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="static_page:view">
    <input type="hidden" name="static_page_id" value="{$static_page.id}">
    <input type="hidden" name="do[]" value="static_page:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <input type="hidden" name="static_page_date_last" value="{$smarty.now}">
</form>
  {/foreach}
{/if}
