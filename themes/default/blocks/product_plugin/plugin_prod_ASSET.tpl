{$list->unserial($product.prod_plugin_data, "plugin_data")} 
<table width="100%" border="0" cellspacing="0" cellpadding="4">
  {if $service.id}
  <tr valign="top" class="row2">
    <td>Link to assigned Assets:</td>
    <td><a href="?_page=core:search&module=asset&asset_service_id={$service.id}">Click Here</a></td>
  </tr> 
  {/if}
  <tr valign="top" class="row2">
    <td width="34%">Asset Pool </td>
    <td width="66%">	 
	{ $list->menu("no", "product_prod_plugin_data[AssetPool]", "asset_pool", "name", $plugin_data.AssetPool, "") }
	The pool to assign available assets from 	</td>
  </tr>
  <tr valign="top" class="row2">
    <td>Allow Manual Assignment </td>
    <td><label>
    <input type="checkbox" name="product_prod_plugin_data[manual]" value="1" {if $plugin_data.manual}checked{/if}>
    Allow manual assignment from any asset pool 
    </label></td>
  </tr>
  <tr valign="top" class="row2">
    <td>Asset Quanity </td>
    <td><label>
      <input name="product_prod_plugin_data[AssetQty]" type="text" value="{$plugin_data.AssetQty}" size="4" maxlength="3">
    </label>
    Number of assets to assign to this service  </td>
  </tr>  
  <tr valign="top" class="row2">
    <td>Verify Availibility on Checkout </td>
    <td>
	<input type="checkbox" name="product_prod_plugin_data[CartCheck]" value="1" {if $plugin_data.CartCheck}checked{/if}>
	Verifies availibility of assets when adding this item to the cart </td>
  </tr>
  <tr valign="top" class="row2">
    <td>Un-assign asset on service suspension </td>
    <td>
	  <input type="checkbox" name="product_prod_plugin_data[OnSuspend]" value="1" {if $plugin_data.OnSuspend}checked{/if}>	</td>
  </tr>
  <tr valign="top" class="row2">
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr> 
</table>
