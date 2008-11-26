{ $block->display("core:top_clean") }
 
{ $method->exe("product","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 

<!-- Loop through each record -->
{foreach from=$product item=product}
{$list->unserial($product.price_group, "attr_group_array")}
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
                      {translate module=product}
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
                    <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                      <tr> 
                        <td width="150"> 
                          {translate module=product}
                          weekly 
                          {/translate}
                          &nbsp;&nbsp; 
                          { $list->bool("product_price_group[0][show]", $attr_group_array[0].show, "form_menu") }
                        </td>
                      </tr>
                    </table>
                    { if ($list->smarty_array("group","name"," AND pricing='1' ", "group_array")) } 
                    {foreach from=$group_array item=arr}
                    {assign var="idx" value=$arr.id}
                    <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                      <tr> 
                        <td width="40%"> 
                          {$arr.name}
                        </td>
                        <td width="30%"> 
                          {translate module=product}
                          field_price_base 
                          {/translate}
                          <input type="text" name="product_price_group[0][{$arr.id}][price_base]" value="{$attr_group_array[0][$idx].price_base}"  size="5">
                          {$list->currency_iso("")}
                        </td>
                        <td width="30%"> 
                          {translate module=product}
                          field_price_setup 
                          {/translate}
                          <input type="text" name="product_price_group[0][{$arr.id}][price_setup]" value="{$attr_group_array[0][$idx].price_setup}"   size="5">
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
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    monthly 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[1][show]", $attr_group_array[1].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[1][{$arr.id}][price_base]" value="{$attr_group_array[1][$idx].price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[1][{$arr.id}][price_setup]" value="{$attr_group_array[1][$idx].price_setup}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    quarterly 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[2][show]", $attr_group_array[2].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[2][{$arr.id}][price_base]" value="{$attr_group_array[2][$idx].price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[2][{$arr.id}][price_setup]" value="{$attr_group_array[2][$idx].price_setup}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    semianually 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[3][show]", $attr_group_array[3].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[3][{$arr.id}][price_base]" value="{$attr_group_array[3][$idx].price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[3][{$arr.id}][price_setup]" value="{$attr_group_array[3][$idx].price_setup}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    anually 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[4][show]", $attr_group_array[4].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[4][{$arr.id}][price_base]" value="{$attr_group_array[4][$idx].price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[4][{$arr.id}][price_setup]" value="{$attr_group_array[4][$idx].price_setup}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    twoyear 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[5][show]", $attr_group_array[5].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[5][{$arr.id}][price_base]" value="{$attr_group_array[5][$idx].price_base}"  size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[5][{$arr.id}][price_setup]" value="{$attr_group_array[5][$idx].price_setup}"   size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
         <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="0" class="row2">
                <tr> 
                  <td width="150"> 
                    {translate module=product}
                    threeyear 
                    {/translate}
                    &nbsp;&nbsp; 
                    { $list->bool("product_price_group[6][show]", $attr_group_array[6].show, "form_menu") }
                  </td>
                </tr>
              </table>
              {foreach from=$group_array item=arr}
              {assign var="idx" value=$arr.id}
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="row1">
                <tr> 
                  <td width="40%"> 
                    {$arr.name}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_base 
                    {/translate}
                    <input type="text" name="product_price_group[6][{$arr.id}][price_base]" value="{$attr_group_array[6][$idx].price_base}" size="5">
                    {$list->currency_iso("")}
                  </td>
                  <td width="30%"> 
                    {translate module=product}
                    field_price_setup 
                    {/translate}
                    <input type="text" name="product_price_group[6][{$arr.id}][price_setup]" value="{$attr_group_array[6][$idx].price_setup}" size="5">
                    {$list->currency_iso("")}
                  </td>
                </tr>
              </table>
              {/foreach}
            </td>
          </tr>
		  
          <tr valign="top">
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row2">
                <tr> 
                  <td valign="middle" align="right"> 
                    <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
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
  <input type="hidden" name="_page" value="product:iframe_price_recurring">
    <input type="hidden" name="_page_current" value="product:iframe_price_recurring">	
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
