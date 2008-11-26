
{ $method->exe("radius","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'radius';
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
{foreach from=$radius item=radius} <a name="{$radius.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="radius_view" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=radius}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_account_id
                        {/translate}</td>
                    <td width="67%">
{html_select_account name="radius_account_id" default=$radius.account_id} </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_active
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_active", $radius.active, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_sku
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_sku" value="{$radius.sku}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_auth
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_auth" value="{$radius.auth}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_username
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_username" value="{$radius.username}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_password
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_password" value="{$radius.password}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_service_type
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_service_type" value="{$radius.service_type}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_session_limit
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_session_limit" value="{$radius.session_limit}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_idle_limit
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_idle_limit" value="{$radius.idle_limit}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_port_limit
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_port_limit" value="{$radius.port_limit}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_analog
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_analog", $radius.analog, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_digital
                        {/translate}</td>
                    <td width="67%">
                        { $list->bool("radius_digital", $radius.digital, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_filter_id
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_filter_id" value="{$radius.filter_id}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_netmask
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_netmask" value="{$radius.netmask}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_framed_route
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_framed_route" value="{$radius.framed_route}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_speed_limit
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_speed_limit" value="{$radius.speed_limit}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_static_ip
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_static_ip" value="{$radius.static_ip}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_profiles
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_profiles" value="{$radius.profiles}" size="32">
                    </td>
                  </tr>
                  <tr valign="top">
                    <td width="33%">
                        {translate module=radius}
                            field_time_bank
                        {/translate}</td>
                    <td width="67%">
                        <input type="text" name="radius_time_bank" value="{$radius.time_bank}" size="32">
                    </td>
                  </tr>
          <tr class="row1" valign="middle" align="left">
                    <td width="33%"></td>
                    <td width="67%">
                      <table width="100%" border="0" cellspacing="0" cellpadding="0">
                        <tr>
                          <td>
                            <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                          </td>
                          <td align="right">
                            <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$radius.id}','{$VAR.id}');">
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
    <input type="hidden" name="_page" value="radius:view">
    <input type="hidden" name="radius_id" value="{$radius.id}">
    <input type="hidden" name="do[]" value="radius:update">
    <input type="hidden" name="id" value="{$VAR.id}">
</form>
  {/foreach}
{/if}
