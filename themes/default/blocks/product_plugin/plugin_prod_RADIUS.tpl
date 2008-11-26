{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top">
    <td>Account Type </td>
    <td><select name="product_prod_plugin_data[auth]">
      <option value="login" {if $plugin_data.auth=="login"}selected{/if}>Login (user/pass)</option>
      <option value="wireless" {if $plugin_data.auth=="wireless"}selected{/if}>Wireless (MAC ID)</option>
    </select></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="45%"> Accounts Allowed</td>
    <td width="21%"> 
      <input type="text" name="product_prod_plugin_data[max]" value="{$plugin_data.max}"  size="10"> 
      </td>
    <td width="34%"> Example: 1 </td>
  </tr>
  <tr valign="top">
    <td>Service Type </td>
    <td><input type="text" name="product_prod_plugin_data[service_type]" value="{$plugin_data.service_type}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Profiles</td>
    <td><input type="text" name="product_prod_plugin_data[session_limit]" value="{$plugin_data.session_limit}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Time Bank </td>
    <td><input type="text" name="product_prod_plugin_data[time_bank]" value="{$plugin_data.time_bank}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Speed Limit </td>
    <td><input type="text" name="product_prod_plugin_data[speed_limit]" value="{$plugin_data.speed_limit}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Session Limit </td>
    <td><input type="text" name="product_prod_plugin_data[session_limit]" value="{$plugin_data.session_limit}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Idle Limit </td>
    <td><input type="text" name="product_prod_plugin_data[idle_limit]" value="{$plugin_data.idle_limit}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Port Limit </td>
    <td><input type="text" name="product_prod_plugin_data[port_limit]" value="{$plugin_data.port_limit}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Filter Id </td>
    <td><input type="text" name="product_prod_plugin_data[filter_id]" value="{$plugin_data.filter_id}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Netmask</td>
    <td><input type="text" name="product_prod_plugin_data[netmask]" value="{$plugin_data.netmask}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Framed Route </td>
    <td><input type="text" name="product_prod_plugin_data[framed_route]" value="{$plugin_data.framed_route}"  size="10"></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Analog</td>
    <td><input type="checkbox" name="product_prod_plugin_data[analog]" value="1" {if $plugin_data.analog}checked{/if}></td>
    <td>&nbsp;</td>
  </tr>
  <tr valign="top">
    <td>Digital</td>
    <td><input type="checkbox" name="product_prod_plugin_data[digital]" value="1" {if $plugin_data.digital}checked{/if}></td>
    <td>&nbsp;</td>
  </tr>  
  <tr valign="top">
    <td>Static IP</td>
    <td><input type="text" name="product_prod_plugin_data[static_ip]" value="{$plugin_data.static_ip}"  size="10"></td>
    <td>Enter this value only after the service has been provisioned</td>
  </tr>
</table>
