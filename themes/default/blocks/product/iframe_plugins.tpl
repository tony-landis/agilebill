{ $block->display("core:top_clean") }
 
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product item=product}

{if $product.host == "1"}
Both hosting and product plugins cannot be configured for the same product.
{else}

<form name="product_view" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="55%"> Enable Product Plugins?</td>
                  <td width="45%"> 
                    { $list->bool("product_prod_plugin", $product.prod_plugin, "onchange=\"submit();\"") }
                  </td>
                </tr>
                <tr> 
                  <td width="55%"> Plugin to Enable</td>
                  <td width="45%"> 
                    { $list->menu_files("", "product_prod_plugin_file", $product.prod_plugin_file, "product", "", ".php", "\" onchange=\"document.product_view.submit();") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          { if $product.prod_plugin_file != ""} 
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              {assign var="afile" 	value=$product.prod_plugin_file}
              {assign var="ablock" 	value="product_plugin:plugin_prod_"}
              {assign var="blockfile" value="$ablock$afile"}
              { $block->display($blockfile) }
            </td>
          </tr> 
          {/if}
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row2">
                <tr> 
                  <td valign="middle" align="right"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
    
  <input type="hidden" name="do[]" value="product:update">
  <input type="hidden" name="_page" value="product:iframe_plugins">
    <input type="hidden" name="_page_current" value="product:iframe_plugins">	
    <input type="hidden" name="product_id" value="{$product.id}">
	<input type="hidden" name="id" value="{$product.id}">
	<input type="hidden" name="product_price_base" value="{$product.price_base}">
	<input type="hidden" name="product_sku" value="{$product.sku}">
	<input type="hidden" name="product_avail_category_id" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_req_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_group" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_price_group" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_price_group" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_host_discount_tld" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_host_provision_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_group_avail" value="IGNORE-ARRAY-VALUE">
  <input type="hidden" name="product_modify_product_arr" value="IGNORE-ARRAY-VALUE">
</form>

{/if}
{/foreach}
{/if}
