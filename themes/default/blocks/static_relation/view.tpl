{ $block->display("core:top_clean") }

{ $method->exe("static_relation","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else} <br>

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'static_relation';
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
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&_escape=1&delete_id=' + id ;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&_escape=1&do[]=' + module + ':' + doit + '&delete_id=' + id;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal}

<!-- Loop through each record -->
{foreach from=$static_relation item=static_relation} <a name="{$static_relation.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=static_relation}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_static_var_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "static_relation_static_var_id", "static_var", "name", $static_relation.static_var_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_module_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "static_relation_module_id", "module", "name", $static_relation.module_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_default_value 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_relation_default_value" cols="45" rows="2" >{$static_relation.default_value}</textarea>
                  </td>
                </tr>
                <tr class="row3" valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_description 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="static_relation_description" cols="45" rows="2" >{$static_relation.description}</textarea>
                  </td>
                </tr>
                <tr class="row4" valign="top"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_required 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("static_relation_required",$static_relation.required, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=static_relation}
                    field_sort_order 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="static_relation_sort_order" value="{$static_relation.sort_order}"  size="3">
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
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$static_relation.id}','{$VAR.id}');">
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
  <input type="hidden" name="_page" value="static_relation:view">
    <input type="hidden" name="static_relation_id" value="{$static_relation.id}">
    <input type="hidden" name="do[]" value="static_relation:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  	<input type="hidden" name="_escape" value="1">
</form>
  {/foreach}    
{/if}
