{$list->unserial($product.host_provision_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> DirectAdmin Package Name</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[package]" value="{$plugin_data.package}" class="form_field" size="20">
      eg: newpackage</td>
  </tr>
  <tr valign="top">
    <td width="50%">IP Based?</td>
    <td width="50%"> 
      {$list->bool("product_host_provision_plugin_data[type]", $plugin_data.type, "form_menu")}
    </td>
  </tr>
</table> 
