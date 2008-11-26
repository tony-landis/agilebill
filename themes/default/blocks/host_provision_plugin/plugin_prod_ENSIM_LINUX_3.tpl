{$list->unserial($product.host_provision_plugin_data, "plugin_data")}
 
<table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
  <tr valign="top" class="row2"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      service 
      {/translate}
    </td>
    <td width="17%"> 
      {translate module=host_provision_plugin}
      enabled 
      {/translate}
    </td>
    <td width="34%"> 
      {translate module=host_provision_plugin}
      options 
      {/translate}
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      ipinfo_namebased 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <select name="product_host_provision_plugin_data[ipinfo_namebased]" class="form_menu">
        <option value="1" {if $plugin_data.ipinfo_namebased == "1"} selected{/if}>Name 
        Based</option>
        <option value="0" {if $plugin_data.ipinfo_namebased == "0"} selected{/if}>IP 
        Based</option>
      </select>
    </td>
  </tr>
  <tr valign="top"> 
    <td width="49%"> 
      {translate module=host_provision_plugin}
      assoc_plan 
      {/translate}
    </td>
    <td width="17%">--- </td>
    <td width="34%"> 
      <input type="text" name="product_host_provision_plugin_data[plan]" value="{$plugin_data.plan}" class="form_field" size="10">
    </td>
  </tr>
</table>
 