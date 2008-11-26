
{ $method->exe("file_category","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'file_category';
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
{foreach from=$file_category item=file_category} <a name="{$file_category.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="file_category_view" method="post" action="">
{$COOKIE_FORM}
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=file_category}
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
                    {translate module=file_category}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {$list->date_time($file_category.date_orig)}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file_category}
                    field_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="file_category_name" value="{$file_category.name}" class="form_field" size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file_category}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="file_category_description" cols="40" rows="5" class="form_field">{$file_category.description}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file_category}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("file_category_status", $file_category.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file_category}
                    field_group_avail 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->select_groups($file_category.group_avail,"file_category_group_avail","form_field","10","") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=file_category}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="file_category_sort_order" value="{$file_category.sort_order}" class="form_field" size="3">
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
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$file_category.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="file_category:view">
    <input type="hidden" name="file_category_id" value="{$file_category.id}">
    <input type="hidden" name="do[]" value="file_category:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
