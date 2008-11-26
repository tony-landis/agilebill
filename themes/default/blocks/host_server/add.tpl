

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="host_server_add" name="host_server_add" method="post" action="">

<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=host_server}title_add{/translate}
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
                    {if $VAR.host_server_status != ""}
                    { $list->bool("host_server_status", $VAR.host_server_status, "form_menu") }
                    {else}
                    { $list->bool("host_server_status", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_debug 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_debug", $VAR.host_server_debug, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_name" value="{$VAR.host_server_name}" {if $host_server_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="host_server_notes" cols="40" rows="5" {if $host_server_notes == true}class="form_field_error"{/if}>{$VAR.host_server_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_provision_plugin 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "host_server_provision_plugin", $VAR.host_server_provision_plugin, "provision_plugin", "", ".php", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name_based 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {if $VAR.host_server_name_based != "" }
                    { $list->bool("host_server_name_based", $VAR.host_server_name_based, "form_menu") }
                    {else}
                    { $list->bool("host_server_name_based", "1", "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_name_based_ip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_name_based_ip" value="{$VAR.host_server_name_based_ip}" {if $host_server_name_based_ip == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_ip_based 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->bool("host_server_ip_based", $VAR.host_server_ip_based, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_server}
                    field_ip_based_ip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="host_server_ip_based_ip" cols="40" rows="5" {if $host_server_ip_based_ip == true}class="form_field_error"{/if}>{$VAR.host_server_ip_based_ip}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    primary_ns 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_ns_primary" value="{$VAR.host_server_ns_primary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    secondary_ns 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_ns_secondary" value="{$VAR.host_server_ns_secondary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    primary_nsip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_ns_ip_primary" value="{$VAR.host_server_ns_ip_primary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=host_registrar_plugin}
                    secondary_nsip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="host_server_ns_ip_secondary" value="{$VAR.host_server_ns_ip_secondary}"  size="32">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"></td>
                  <td width="50%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="host_server:view">
                    <input type="hidden" name="_page_current" value="host_server:add">
                    <input type="hidden" name="do[]" value="host_server:add">
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
