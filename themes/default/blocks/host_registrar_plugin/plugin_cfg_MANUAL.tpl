{ if $thistype != "add" } 
	{$list->unserial($host_registrar_plugin.plugin_data, "plugin_data")}
{ else} 
	{assign var=plugin_data 	value=$VAR.host_registrar_plugin_data} 
{/if}
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      primary_ns 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[ns1]" value="{$plugin_data.ns1}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      secondary_ns 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[ns2]" value="{$plugin_data.ns2}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      primary_nsip 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[ns1ip]" value="{$plugin_data.ns1ip}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      secondary_nsip 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[ns2ip]" value="{$plugin_data.ns2ip}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      manual_add_email 
      {/translate}
    </td>
    <td width="50%"> 
      { $list->menu("", "host_registrar_plugin_plugin_data[manual_add_email]", "staff", "nickname", $plugin_data.manual_add_email, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      manual_renew_email 
      {/translate}
    </td>
    <td width="50%"> 
      { $list->menu("", "host_registrar_plugin_plugin_data[manual_renew_email]", "staff", "nickname", $plugin_data.manual_renew_email, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      manual_transfer_email 
      {/translate}
    </td>
    <td width="50%"> 
      { $list->menu("", "host_registrar_plugin_plugin_data[manual_transfer_email]", "staff", "nickname", $plugin_data.manual_transfer_email, "form_menu") }
    </td>
  </tr>
  <tr valign="top">
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      manual_park_email 
      {/translate}
    </td>
    <td width="50%"> 
      { $list->menu("", "host_registrar_plugin_plugin_data[manual_park_email]", "staff", "nickname", $plugin_data.manual_park_email, "form_menu") }
    </td>
  </tr>
</table>
