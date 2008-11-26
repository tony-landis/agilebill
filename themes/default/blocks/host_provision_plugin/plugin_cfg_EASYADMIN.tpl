{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> *easyAdmin Host </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}" class="form_field" size="30">
      eg: http://x.x.x.x:88/.easy/main/mysql.cgi</td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *easyAdmin Login</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[user]" value="{$plugin_data.user}" class="form_field">
      eg: admin </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *easyAdmin Password</td>
    <td width="50%"> 
      <input type="password" name="host_server_provision_plugin_data[pass]" value="{$plugin_data.pass}" class="form_field">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> *easyAdmin Reseller</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[reseller]" value="{$plugin_data.reseller}" class="form_field">
      eg: admin</td>
  </tr>
  <tr valign="top">
    <td width="50%">* = required fields</td>
    <td width="50%">&nbsp;</td>
  </tr>
</table>
 