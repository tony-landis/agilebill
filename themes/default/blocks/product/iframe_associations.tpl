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
                    <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                      <tr> 
                        <td width="98%" valign="top"> <b> </b> 
                          <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                            <tr> 
                              <td> 
                                {translate module=product}
                                field_assoc_req_prod 
                                {/translate}
                              </td>
                            </tr>
                            <tr> 
                              <td><b> </b></td>
                            </tr>
                            <tr> 
                              <td> 
                                <input type="radio" name="product_assoc_req_prod_type" value="0" {if $product.assoc_req_prod_type == "0"}checked{/if}>
                                {translate module=product}
                                assoc_req_all 
                                {/translate}
                                <br>
                                <input type="radio" name="product_assoc_req_prod_type" value="1" {if $product.assoc_req_prod_type == "1"}checked{/if}>
                                {translate module=product}
                                assoc_req_one 
                                {/translate}
                              </td>
                            </tr>
                          </table>
                          <b> </b> </td>
                        <td width="2%" align="right" valign="top"> 
                          { $list->menu_multi($product.assoc_req_prod, "product_assoc_req_prod", "product", "sku", "10", "", "form_menu") }
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr class="row2"> 
                  <td width="99%" valign="top"> 
                    {translate module=product}
                    field_assoc_grant_prod 
                    {/translate}
                  </td>
                  <td width="1%" align="right"> 
                    { $list->menu_multi($product.assoc_grant_prod, "product_assoc_grant_prod", "product", "sku", "10", "", "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr> 
                  <td width="98%" valign="top"> <b> </b> 
                    <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                      <tr> 
                        <td> 
                          {translate module=product}
                          field_assoc_grant_group 
                          {/translate}
                        </td>
                      </tr>
                      <tr> 
                        <td><b> </b></td>
                      </tr>
                      <tr> 
                        <td> 
                          <p> 
                            <input type="radio" name="product_assoc_grant_group_type" value="0" {if $product.assoc_grant_group_type == "0"}checked{/if}>
                            {translate module=product}
                            assoc_group_limited 
                            {/translate}
                            <input type="text" name="product_assoc_grant_group_days" value="{$product.assoc_grant_group_days}"  size="3">
                            <br>
                            <input type="radio" name="product_assoc_grant_group_type" value="1" {if $product.assoc_grant_group_type == "1"}checked{/if}>
                            {translate module=product}
                            assoc_group_subscription 
                            {/translate}
                            <br>
                            <input type="radio" name="product_assoc_grant_group_type" value="2" {if $product.assoc_grant_group_type == "2"}checked{/if}>
                            {translate module=product}
                            assoc_group_forever 
                            {/translate}
                          </p>
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td width="2%" align="left" valign="top"> 
                    { $list->menu_multi($product.assoc_grant_group, "product_assoc_grant_group", "group", "name", "10", "", "form_menu") }
                  </td>
                </tr>
              </table>
              
            </td>
          </tr>
		  
		  {if $product.price_type == "1"}
          <tr valign="top"> 
            <td width="65%" class="row1">  
              <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                <tr> 
                  <td width="96%" valign="top"> <b> </b> 
                    <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                      <tr> 
                        <td> 
                          {translate module=product}
                          field_price_recurr_modify 
                          {/translate}
                          { $list->bool("product_price_recurr_modify", $product.price_recurr_modify, "\" onChange=\"document.forms.product_view.submit()\"") }
                        </td>
                      </tr>
                      <tr> 
                        <td><b>
                           </b></td>
                      </tr>
                      <tr> 
                        <td> 
                          <!-- 
                            {translate module=product}
                            field_modify_waive_setup 
                            {/translate}
                            { $list->bool("product_modify_waive_setup", $product.modify_waive_setup, "form_menu") }
                          -->
                        </td>
                      </tr>
                    </table>
                  </td>
                  <td width="4%" align="left" valign="top"> 
                    {if $product.host == 1}
                    {html_menu_product_host name="product_modify_product_arr" default=$product.modify_product_arr exclude=$product.id}
                    {else}
                    {html_menu_product_subscription name="product_modify_product_arr" default=$product.modify_product_arr exclude=$product.id}
                    {/if}
                  </td>
                </tr>
              </table> 
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
  <input type="hidden" name="_page" value="product:iframe_associations">
    <input type="hidden" name="_page_current" value="product:iframe_associations">	
    <input type="hidden" name="product_id" value="{$product.id}">
	<input type="hidden" name="id" value="{$product.id}">
	<input type="hidden" name="product_price_base" value="{$product.price_base}">
	<input type="hidden" name="product_sku" value="{$product.sku}">
	<input type="hidden" name="product_avail_category_id" value="IGNORE-ARRAY-VALUE"> 
	<input type="hidden" name="product_price_group" value="IGNORE-ARRAY-VALUE"> 
	<input type="hidden" name="product_host_discount_tld" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_host_provision_plugin_data" value="IGNORE-ARRAY-VALUE">
	<input type="hidden" name="product_prod_plugin_data" value="IGNORE-ARRAY-VALUE">
    <input type="hidden" name="product_group_avail" value="IGNORE-ARRAY-VALUE">
</form>

{/foreach}{/if}
