
{ $method->exe("file","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'file';
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
{foreach from=$file item=file} <a name="{$file.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="file_view" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=file}
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
                    {translate module=file}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time("")}
                    <input type="hidden" name="file_date_last" value="{$smarty.now}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_size 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$file.size}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$file.type}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_file_category_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "file_file_category_id", "file_category", "name", $file.file_category_id, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="file_name" value="{$file.name}" class="form_field" size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="file_description" cols="40" rows="5" class="form_field">{$file.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("file_status", $file.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="file_sort_order" value="{$file.sort_order}" class="form_field" size="5">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->select_groups($file.group_avail,"file_group_avail","form_field","10","") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("file_date_start", $file.date_start, "form_field", $file.id) }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_view("file_date_expire", $file.date_expire, "form_field", $file.id) }
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
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$file.id}','{$VAR.id}');">
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
              <table width="100%" border="0" cellspacing="0" cellpadding="6" class="row2">
                <tr> 
                  <td><a href="{$URL}?_page=file:download&id={$file.id}&_escape=1" target="_blank"> 
                    {translate module=file}
                    test 
                    {/translate}
                    </a> </td>
                  <td align="right">&nbsp; </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="file:view">
    <input type="hidden" name="file_id" value="{$file.id}">
    <input type="hidden" name="do[]" value="file:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
