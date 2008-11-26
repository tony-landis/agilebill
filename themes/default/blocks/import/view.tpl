
{ $method->exe("import","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'import';
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
{foreach from=$import item=import} <a name="{$import.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="import_view" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=import}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_date_orig" value="{$import.date_orig}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_plugin
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_plugin" value="{$import.plugin}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_module
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_module" value="{$import.module}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_local_table
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_local_table" value="{$import.local_table}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_ab_table
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_ab_table" value="{$import.ab_table}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_remote_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_remote_id" value="{$import.remote_id}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=import}
                            field_ab_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="import_ab_id" value="{$import.ab_id}" size="32">
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
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$import.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="import:view">
    <input type="hidden" name="import_id" value="{$import.id}">
    <input type="hidden" name="do[]" value="import:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
