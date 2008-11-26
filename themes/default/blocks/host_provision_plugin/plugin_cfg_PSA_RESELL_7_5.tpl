{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">* Plesk Port</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[port]" value="{$plugin_data.port}" class="form_field" size="30">
      eg: 8443</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> * Plesk Host </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}" class="form_field" size="30">
      eg: server.com</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> * Plesk Login</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[user]" value="{$plugin_data.user}" class="form_field">
      eg: admin </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> * Plesk Password</td>
    <td width="50%"> 
      <input type="password" name="host_server_provision_plugin_data[pass]" value="{$plugin_data.pass}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%" height="18">* Plesk Client ID</td>
    <td width="50%" height="18">
      <input type="text" name="host_server_provision_plugin_data[acct]" value="{$plugin_data.acct}" class="form_field" size="3">
      This is the numeric client ID of the PSA user to create all domains under. 
      To get this ID, create a new client in PSA and on the Client page, move 
      your mouse over the client name and in the status bar you will see the client 
      id like so: ...?cl_id=X (where X is the client id)</td>
  </tr>
  <tr valign="top">
    <td width="50%">* = required fields</td>
    <td width="50%">&nbsp;</td>
  </tr>
</table>
 