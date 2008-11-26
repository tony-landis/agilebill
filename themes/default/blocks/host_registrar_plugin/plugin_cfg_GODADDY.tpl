{ if $thistype != "add" } 
	{$list->unserial($host_registrar_plugin.plugin_data, "plugin_data")}
{ else} 
	{assign var=plugin_data 	value=$VAR.host_registrar_plugin_data} 
{/if}

<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      debug 
      {/translate}
    </td>
    <td width="50%"> 
      {$list->bool("host_registrar_plugin_plugin_data[debug]", $plugin_data.debug, "form_menu")}
    </td>
  </tr>
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
      gd_user 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[gd_user]" value="{$plugin_data.gd_user}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      gd_pass 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="password" name="host_registrar_plugin_plugin_data[gd_pass]" value="{$plugin_data.gd_pass}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_registrar_plugin}
      gd_mode 
      {/translate}
    </td>
    <td width="50%"> 
      {$list->bool("host_registrar_plugin_plugin_data[gd_mode]", $plugin_data.gd_mode, "form_menu")}
    </td>
  </tr>
</table>
 