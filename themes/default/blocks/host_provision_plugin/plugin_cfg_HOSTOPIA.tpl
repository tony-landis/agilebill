{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> Hostopia Username </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[user]" value="{$plugin_data.user}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Hostopia Password </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[pass]" value="{$plugin_data.pass}" >
    </td>
  </tr>
</table>
 
