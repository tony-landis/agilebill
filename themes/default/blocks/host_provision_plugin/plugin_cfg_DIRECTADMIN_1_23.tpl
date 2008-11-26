{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">* DirectAdmin Port</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[port]" value="{$plugin_data.port}" class="form_field" size="30">
      eg: 2222</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *DirectAdmin Host </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}" class="form_field" size="30">
      eg: server.com</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *DirectAdmin Login</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[user]" value="{$plugin_data.user}" class="form_field">
      eg: admin </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *DirectAdmin Password</td>
    <td width="50%"> 
      <input type="password" name="host_server_provision_plugin_data[pass]" value="{$plugin_data.pass}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Allow DirectAdmin to notify user?</td>
    <td width="50%"> 
      {$list->bool("host_server_provision_plugin_data[notify]", $plugin_data.notify, "form_menu")}
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">* = required fields</td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">&nbsp;</td>
    <td width="50%">&nbsp;</td>
  </tr>
</table>
 