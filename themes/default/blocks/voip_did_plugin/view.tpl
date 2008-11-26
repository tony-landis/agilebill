
{ $method->exe("voip_did_plugin","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'voip_did_plugin';
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
{foreach from=$voip_did_plugin item=voip_did_plugin} <a name="{$voip_did_plugin.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="voip_did_plugin_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_did_plugin}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_plugin_name" value="{$voip_did_plugin.name}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_plugin
                        {/translate}</td>
                    <td width="65%"> 
                    	{html_menu_files path=voip_did field=voip_did_plugin_plugin default=$voip_did_plugin.plugin}                   
					 </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_avail_countries
                        {/translate}</td>
                    <td width="65%"> 
						{html_menu_multi name=voip_did_plugin_avail_countries assoc_table="voip_iso_country_code" assoc_field="name" size=15 default=$voip_did_plugin.avail_countries} 
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="35%">
                        {translate module=voip_did_plugin}
                            field_release_minutes
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_did_plugin_release_minutes" value="{$voip_did_plugin.release_minutes}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td colspan="2">{plugin type=voip_did name=$voip_did_plugin.plugin data=$voip_did_plugin.plugin_data} 
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="35%"><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button"></td>
                    <td width="65%">
                      <div align="right">
                        <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$voip_did_plugin.id}','{$VAR.id}');">
                      </div></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="voip_did_plugin:view">
    <input type="hidden" name="voip_did_plugin_id" value="{$voip_did_plugin.id}">
    <input type="hidden" name="do[]" value="voip_did_plugin:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
