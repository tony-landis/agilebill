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
    <td width="50%"> Live Mode </td>
    <td width="50%"> 
      {$list->bool("host_registrar_plugin_plugin_data[mode]", $plugin_data.mode, "form_menu")}
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
    <td width="50%"> Reseller ID</td>
    <td width="50%"> 
      <input type="text" name="host_registrar_plugin_plugin_data[user]" value="{$plugin_data.user}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Password</td>
    <td width="50%"> 
      <input type="password" name="host_registrar_plugin_plugin_data[pass]" value="{$plugin_data.pass}" class="form_field">
    </td>
  </tr>
</table>
