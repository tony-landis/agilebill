{$list->unserial($host_server.provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> Connect Type</td>
    <td width="50%"> 
      <select name="host_server_provision_plugin_data[mode]" >
        <option value="0" {if $plugin_data.mode == "0"}selected{/if}>Standard</option>
        <option value="1" {if $plugin_data.mode == "1"}selected{/if}>SSL</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Host</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[host]" value="{$plugin_data.host}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> Path to 'Accounting.php.inc'</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[path]" value="{if $plugin_data.path == ""}/usr/local/cpanel/Cpanel/Accounting.php.inc{else}{$plugin_data.path}{/if}"  size="40">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> WHM User Account</td>
    <td width="50%"> 
      <input type="text" name="host_server_provision_plugin_data[account]" value="{$plugin_data.account}" >
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"> WHM Access Hash</td>
    <td width="50%"> 
      <textarea name="host_server_provision_plugin_data[accesshash]"  cols="40" rows="8">{$plugin_data.accesshash}</textarea>
    </td>
  </tr>
</table>
 
