{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  {if $service.id}
  {/if}
  <tr valign="top" class="row2">
    <td width="34%">Product SKU </td>
    <td width="66%">	 
	{ $list->menu("no", "product_prod_plugin_data[sku]", "product", "sku", $plugin_data.sku, "") }
	Example Assoc Menu </td>
  </tr>
  <tr valign="top" class="row2">
    <td>Quanity </td>
    <td><label>
      <input name="product_prod_plugin_data[qty]" type="text" value="{$plugin_data.qty}" size="4" maxlength="3">
    </label>    </td>
  </tr>
  <tr valign="top" class="row2">
    <td>Boolean</td>
    <td>
	<input type="checkbox" name="product_prod_plugin_data[bool]" value="1" {if $plugin_data.bool}checked{/if}>
	</td>
  </tr> 
</table>
