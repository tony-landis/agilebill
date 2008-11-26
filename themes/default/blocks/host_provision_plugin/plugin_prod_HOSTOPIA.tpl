{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      Hostopia Package </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[package]" value="{$plugin_data.package}" class="form_field" size="32">
    </td>
  </tr>
  <tr valign="top">
    <td>Hostopia Service </td>
    <td><input type="text" name="product_host_provision_plugin_data[service]" value="{$plugin_data.service}" class="form_field" size="32"></td>
  </tr>
</table>
  