{ $block->display("core:top_clean") }
 
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product item=product}

<form name="product_view" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row2">
                <tr> 
                  <td> 
                    <div align="center"> 
                      {translate module=product_attr}
                      field_price_group 
                      {/translate}
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row2">
                <tr> 
                  <td> 
                    {$list->unserial($product.price_group, "attr_group_array")}
                    {$attr_group_array[$arr.id].price_base}
                    { if ($list->smarty_array("group","name"," AND pricing='1' ", "group_array")) } 
                    {foreach from=$group_array item=arr}
                    {assign var="idx" value=$arr.id}
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="150"> <b> 
                          {$arr.name}
                          </b> </td>
                        <td width="187"> 
                          {translate module=product_attr}
                          field_price_base 
                          {/translate}
                          <input type="text" name="product_price_group[{$arr.id}][price_base]" value="{$attr_group_array[$idx].price_base}"  size="5">
                          {$list->currency_iso("")}
                        </td>
                        <td width="153"> 
                          {translate module=product_attr}
                          field_price_setup 
                          {/translate}
                          <input type="text" name="product_price_group[{$arr.id}][price_setup]" value="{$attr_group_array[$idx].price_setup}"   size="5">
                          {$list->currency_iso("")}
                        </td>
                      </tr>
                    </table>
                    {/foreach}
                    {/if}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
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
  <input type="hidden" name="_page" value="product:iframe_price_onetime">
    <input type="hidden" name="_page_current" value="product:iframe_price_onetime">	
    <input type="hidden" name="product_id" value="{$product.id}">
	<input type="hidden" name="id" value="{$product.id}">
	<input type="hidden" name="product_price_base" value="{$product.price_base}">
	<input type="hidden" name="product_sku" value="{$product.sku}">
	<input type="hidden" name="product_avail_category_id" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_req_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_group" value="IGNORE-ARRAY-VALUE"> 
	<input type="hidden" name="product_host_discount_tld" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_host_provision_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_prod_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_group_avail" value="IGNORE-ARRAY-VALUE">
  <input type="hidden" name="product_modify_product_arr" value="IGNORE-ARRAY-VALUE">
</form>

 {/foreach}{/if}
