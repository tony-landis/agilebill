
{ $method->exe("host_registrar_plugin","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'host_registrar_plugin';
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
{foreach from=$host_registrar_plugin item=host_registrar_plugin}  

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="host_registrar_plugin_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=host_registrar_plugin}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_registrar_plugin_status", $host_registrar_plugin.status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_registrar_plugin_name" value="{$host_registrar_plugin.name}"  size="32">
                  </td>
                </tr>
              </table>

			{assign var=thistype 	value="edit"}
			{assign var="afile" 	value=$host_registrar_plugin.file}
			{assign var="ablock" 	value="host_registrar_plugin:plugin_cfg_"}
			{assign var="blockfile" value="$ablock$afile"}							   
			{ $block->display($blockfile) } 
			
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"></td>
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$host_registrar_plugin.id}','{$VAR.id}');">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>		
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="host_registrar_plugin:view">
    <input type="hidden" name="host_registrar_plugin_id" value="{$host_registrar_plugin.id}">
    <input type="hidden" name="do[]" value="host_registrar_plugin:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  <input type="hidden" name="host_registrar_plugin_file" value="{$host_registrar_plugin.file}">
</form>
  {/foreach}
{/if}
