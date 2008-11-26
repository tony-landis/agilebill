{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top"> 
    <td width="50%"> 
      {translate module=host_provision_plugin}
      whm_plan 
      {/translate}
    </td>
    <td width="50%"> 
      <input type="text" name="product_host_provision_plugin_data[plan]" value="{$plugin_data.plan}" class="form_field" size="32">
    </td>
  </tr>
</table>
  