{$list->unserial($product.host_provision_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%">&nbsp; </td>
    <td width="50%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">IP Based Plan? </td>
    <td width="50%"> 
      { $list->bool("product_host_provision_plugin_data[ip_based]", $plugin_data.ip_based, "form_menu") }
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">&nbsp; </td>
    <td width="50%">&nbsp; </td>
  </tr>
  <tr valign="top"> 
    <td width="50%"><b>Client/Domain Templates</b></td>
    <td width="50%">&nbsp;</td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Client template</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[client_template_name]" value="{$plugin_data.client_template_name}" class="form_field" size="30">
    </td>
  </tr>
  <tr valign="top"> 
    <td width="50%">Domain template</td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[domain_template_name]" value="{$plugin_data.domain_template_name}" class="form_field" size="30">
    </td>
  </tr>
</table>  
<input type="hidden" name="product_host_provision_plugin_data[account_id]" value="{$plugin_data.account_id}">
<input type="hidden" name="product_host_provision_plugin_data[domain_id]" value="{$plugin_data.domain_id}">
 
