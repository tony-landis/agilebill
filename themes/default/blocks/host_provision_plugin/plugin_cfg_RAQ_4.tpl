{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      connection_mode 
      {/translate}
    </td>
    <td width="50%"> 
      <select name="host_server_provision_plugin_data[mode]" >
        <option value="ftp" {if $plugin_data.mode == "ftp"}selected{/if}>{translate module=host_server}ftp{/translate}</option>
        <option value="http" {if $plugin_data.mode == "http"}selected{/if}>{translate module=host_server}http{/translate}</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      host 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      path 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[path]" value="{$plugin_data.path}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      username 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[username]" value="{$plugin_data.username}" >
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_server}
      password 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="password" name="host_server_provision_plugin_data[password]" value="{$plugin_data.password}" >
    </td>
  </tr>
</table>
 
