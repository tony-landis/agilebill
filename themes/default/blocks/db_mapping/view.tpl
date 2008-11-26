{ $method->exe("db_mapping","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'db_mapping';
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
{foreach from=$db_mapping item=db_mapping} 
<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="db_mapping_view" method="post" action="">

  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=db_mapping}
                title_view 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%" height="20"> 
                    {translate module=db_mapping}
                    field_map_file 
                    {/translate}
                  </td>
                  <td width="65%" height="20"> 
                    {$db_mapping.map_file}
                    .php </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("db_mapping_status", $db_mapping.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_name" value="{$db_mapping.db_name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_host 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_host" value="{$db_mapping.db_host}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_user 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_user" value="{$db_mapping.db_user}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_pass 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_pass" value="{$db_mapping.db_pass}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_db_prefix 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_db_prefix" value="{$db_mapping.db_prefix}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=db_mapping}
                    field_cookie_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="db_mapping_cookie_name" value="{$db_mapping.cookie_name}"  size="32">
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
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$db_mapping.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="35%"></td>
                  <td width="65%"><a href="?_page=db_mapping:view&id={$VAR.id}&do%5B%5D=db_mapping:sync">Sync 
                    all accounts and groups</a></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=db_mapping}
                field_group_map 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              {if $db_mapping_result != false}
              <div align="center"> 
                {$db_mapping_result}
              </div>
              {else} 
              { $block->display($db_mapping_template) }
              {/if}
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="db_mapping:view">
    <input type="hidden" name="db_mapping_id" value="{$db_mapping.id}">
    <input type="hidden" name="do[]" value="db_mapping:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <input type="hidden" name="db_mapping_map_file" value="{$db_mapping.map_file}"  size="32">
  <input type="hidden" name="db_mapping_name" value="{$db_mapping.name}"  size="32">
</form>
  
{/foreach}
{/if}
