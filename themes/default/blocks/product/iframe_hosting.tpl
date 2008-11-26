{ $block->display("core:top_clean") }
 
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product item=product}

{if $product.prod_plugin == "1"}
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
                  <td width="55%">
                    {translate module=product}
                    field_host 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    { $list->bool("product_host", $product.host, "onchange=\"submit();\"") }
                  </td>
                </tr>
                <tr> 
                  <td width="55%">
                    {translate module=product}
                    field_host_server_id 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    { $list->menu("no", "product_host_server_id", "host_server", "name", $product.host_server_id, "\" onchange=\"document.product_view.submit();") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <b><table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="55%"> 
                    {translate module=product}
                    field_host_allow_domain 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    {if $product.host_allow_domain == ""}
                    { $list->bool("product_host_allow_domain", "1", "form_menu") }
                    {else}
                    { $list->bool("product_host_allow_domain", $product.host_allow_domain, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr> 
                  <td width="55%"> 
                    {translate module=product}
                    field_host_allow_host_only 
                    {/translate}
                  </td>
                  <td width="45%"> 
                    {if $product.host_allow_host_only != ""}
                    { $list->bool("product_host_allow_host_only", $product.host_allow_host_only, "form_menu") }
                    {else}
                    { $list->bool("product_host_allow_host_only", "0", "form_menu") }
                    {/if}
                  </td>
                </tr>
              </table>
            </b></td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                <tr> 
                  <td width="55%">
                    {translate module=product}
                    field_host_discount_tld 
                    {/translate}
                  </td>
                  <td width="45%">
                    {translate module=product}
                    field_host_discount_tld_amount 
                    {/translate}
                  </td>
                </tr>
                <tr> 
                  <td width="55%"><b> 
                    { $list->menu_multi($product.host_discount_tld, "product_host_discount_tld", "host_tld", "name", "5", "5", "form_menu") }
                    </b></td>
                  <td width="45%"> 
                    <input type="text" name="product_host_discount_tld_amt" value="{$product.host_discount_tld_amt}"  size="4">
                    (example: 0.10 = 10%)</td>
                </tr>
              </table>
            </td>
          </tr> 
         { if ($list->smarty_array("host_server","provision_plugin", "", "plugin")) } {foreach from=$plugin item=arr} {if $product.host_server_id == $arr.id}
          <tr valign="top"> 
            <td width="65%" class="row1">  
              {assign var="afile" 	value=$arr.provision_plugin}
              {assign var="ablock" 	value="host_provision_plugin:plugin_prod_"}
              {assign var="blockfile" value="$ablock$afile"}
              { $block->display($blockfile) } 
            </td>
          </tr>						
		 {/if}  {/foreach} {/if} 
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
  <input type="hidden" name="_page" value="product:iframe_hosting">
    <input type="hidden" name="_page_current" value="product:iframe_hosting">	
    <input type="hidden" name="product_id" value="{$product.id}">
	<input type="hidden" name="id" value="{$product.id}">
	<input type="hidden" name="product_price_base" value="{$product.price_base}">
	<input type="hidden" name="product_sku" value="{$product.sku}">
	<input type="hidden" name="product_avail_category_id" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_req_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_prod" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_assoc_grant_group" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_price_group" value="IGNORE-ARRAY-VALUE"> 
	<input type="hidden" name="product_prod_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_group_avail" value="IGNORE-ARRAY-VALUE">
  <input type="hidden" name="product_modify_product_arr" value="IGNORE-ARRAY-VALUE">
</form>
{/if}
{/foreach}
{/if}
