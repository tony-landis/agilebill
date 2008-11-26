{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> Webmin Hostname or IP </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Webmin Port</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[port]" value="{$plugin_data.port}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Webmin User Account</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[user]" value="{$plugin_data.user}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Webmin Password</td>
    <td width="50%"> 
      <input type="password" name="host_server_provision_plugin_data[pass]" value="{$plugin_data.pass}"  size="40">
    </td>
  </tr>
  <tr valign="top">
    <td width="50%">Connect Type</td>
    <td width="50%">
      <select name="host_server_provision_plugin_data[ssl]" >
        <option value="0" {if $plugin_data.ssl == "0"}selected{/if}>Standard</option>
        <option value="1" {if $plugin_data.ssl == "1"}selected{/if}>SSL</option>
      </select>
    </td>
  </tr>
</table>
 
