{ $method->exe("host_server","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'host_server';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
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
{foreach from=$host_server item=host_server} <a name="{$host_server.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="host_server_view" id="host_server_view" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=host_server}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_status", $host_server.status, "onChange=\"submit()\"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_debug 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_debug", $host_server.debug, "onChange=\"submit()\"") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_name" value="{$host_server.name}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="host_server_notes" cols="35" rows="2" >{$host_server.notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_max_accounts 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_max_accounts" value="{$host_server.max_accounts}"  size="5">
                  </td>
                </tr>
				
				{if $next_server == true}
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_next_host_server_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
				  <select name=host_server_next_host_server_id >
					{html_options options=$next_server_options selected=$host_server.next_host_server_id}
				  </select> 
                  </td>
                </tr>
				{/if}
				
				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name_based 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_name_based", $host_server.name_based, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name_based_ip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_name_based_ip" value="{$host_server.name_based_ip}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_ip_based 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_ip_based", $host_server.ip_based, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_ip_based_ip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="host_server_ip_based_ip" cols="35" rows="5" >{$host_server.ip_based_ip}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_provision_plugin 
                    {/translate}
                  </td>
                  <td width="50%">  
                    { $list->menu_files("", "host_server_provision_plugin", $host_server.provision_plugin, "provision_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    {translate module=host_registrar_plugin}
                    primary_ns 
                    {/translate}
                  </td>
                  <td width="50%">
                    <input type="text" name="host_server_ns_primary" value="{$host_server.ns_primary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    {translate module=host_registrar_plugin}
                    secondary_ns 
                    {/translate}
                  </td>
                  <td width="50%">
                    <input type="text" name="host_server_ns_secondary" value="{$host_server.ns_secondary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%">
                    {translate module=host_registrar_plugin}
                    primary_nsip 
                    {/translate}
                  </td>
                  <td width="50%">
                    <input type="text" name="host_server_ns_ip_primary" value="{$host_server.ns_ip_primary}"  size="32">
                  </td>
                </tr>
                <tr valign="top">
                  <td width="50%">
                    {translate module=host_registrar_plugin}
                    secondary_nsip 
                    {/translate}
                  </td>
                  <td width="50%">
                    <input type="text" name="host_server_ns_ip_secondary" value="{$host_server.ns_ip_secondary}"  size="32">
                  </td>
                </tr>
              </table>
			  
            {assign var="ablock" 	value="host_provision_plugin:plugin_cfg_"}
			{assign var="afile" 	value=$host_server.provision_plugin}
			{assign var="blockfile" value="$ablock$afile"}		   
			{ $block->display($blockfile) } 
			
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%">
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                  <td width="50%"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td>&nbsp; </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$host_server.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="host_server:view">
    <input type="hidden" name="host_server_id" value="{$host_server.id}">
    <input type="hidden" name="do[]" value="host_server:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
