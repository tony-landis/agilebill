

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="radius_add" name="radius_add" method="post" action="">
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=radius}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_account_id
                        {/translate}</td>
                    <td width="69%">
                        {html_select_account name="radius_account_id" default=$VAR.radius_account_id}</td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_active
                        {/translate}</td>
                    <td width="69%">
                        { $list->bool("radius_active", 1, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_sku
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_sku" value="{$VAR.radius_sku}" {if $radius_sku == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_auth
                        {/translate}</td>
                  <td width="69%"> 
                        <select name="radius_auth">
                          <option value="login" {if $VAR.radius_auth=="login"}selected{/if}>Login (user/pass)</option>
                          <option value="wireless" {if $VAR.radius_auth=="wireless"}selected{/if}>Wireless (MAC ID)</option>
                        </select>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_username
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_username" value="{$VAR.radius_username}" {if $radius_username == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_password
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_password" value="{$VAR.radius_password}" {if $radius_password == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_service_type
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_service_type" value="{$VAR.radius_service_type}" {if $radius_service_type == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_session_limit
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_session_limit" value="{$VAR.radius_session_limit}" {if $radius_session_limit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_idle_limit
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_idle_limit" value="{$VAR.radius_idle_limit}" {if $radius_idle_limit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_port_limit
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_port_limit" value="{$VAR.radius_port_limit}" {if $radius_port_limit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_analog
                        {/translate}</td>
                    <td width="69%">
                        { $list->bool("radius_analog", $VAR.radius_analog, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_digital
                        {/translate}</td>
                    <td width="69%">
                        { $list->bool("radius_digital", $VAR.radius_digital, "form_menu") }
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_filter_id
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_filter_id" value="{$VAR.radius_filter_id}" {if $radius_filter_id == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_netmask
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_netmask" value="{$VAR.radius_netmask}" {if $radius_netmask == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_framed_route
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_framed_route" value="{$VAR.radius_framed_route}" {if $radius_framed_route == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_speed_limit
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_speed_limit" value="{$VAR.radius_speed_limit}" {if $radius_speed_limit == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_static_ip
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_static_ip" value="{$VAR.radius_static_ip}" {if $radius_static_ip == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_profiles
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_profiles" value="{$VAR.radius_profiles}" {if $radius_profiles == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="31%">
                        {translate module=radius}
                            field_time_bank
                        {/translate}</td>
                    <td width="69%">
                        <input type="text" name="radius_time_bank" value="{$VAR.radius_time_bank}" {if $radius_time_bank == true}class="form_field_error"{/if}>
                    </td>
                </tr>
           <tr valign="top">
                    <td width="31%"></td>
                    <td width="69%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="radius:view">
                      <input type="hidden" name="_page_current" value="radius:add">
                      <input type="hidden" name="do[]" value="radius:add">
                    </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
