{ $block->display("core:top_clean") }

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="product_attr_add" name="product_attr_add" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="product_attr_name" value="{$VAR.product_attr_name}" {if $product_attr_name == true}class="form_field_error"{/if} size="45">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_description 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="product_attr_description" size="45"    value="{$VAR.product_attr_description}">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_collect_type 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_collect_default 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    <input type="radio" name="product_attr_collect_type" value="0" {if $product_attr.collect_type == "0" || $product_attr.collect_type == "" }checked{/if}>
                    {translate module=product_attr}
                    type_checkbox 
                    {/translate}
                    <br>
                    <input type="radio" name="product_attr_collect_type" value="1" {if $product_attr.collect_type == "1"}checked{/if}>
                    {translate module=product_attr}
                    type_text 
                    {/translate}
                    <br>
                    <input type="radio" name="product_attr_collect_type" value="2" {if $product_attr.collect_type == "2"}checked{/if}>
                    {translate module=product_attr}
                    type_menu 
                    {/translate}
                    <br>
                    <input type="radio" name="product_attr_collect_type" value="3" {if $product_attr.collect_type == "3"}checked{/if}>
                    {translate module=product_attr}
                    type_textarea 
                    {/translate}
                    <br>
                  </td>
                  <td width="50%"> 
                    <textarea name="product_attr_collect_default" cols="40" rows="2" {if $product_attr_collect_default == true}class="form_field_error"{/if}>{$VAR.product_attr_collect_default}</textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_price_base 
                    {/translate}
                    &nbsp; &nbsp; 
                    <input type="text" name="product_attr_price_base" value="{$VAR.product_attr_price_base}" {if $product_attr_price_base == true}class="form_field_error"{/if} size="5">
                    {$list->currency_iso("")}
                     </td>
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_price_setup 
                    {/translate}
                    &nbsp;&nbsp; 
                    <input type="text" name="product_attr_price_setup" value="{$VAR.product_attr_price_setup}" {if $product_attr_price_setup == true}class="form_field_error"{/if} size="5">
                    {$list->currency_iso("")}
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
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%" class="row2"> <b> </b> 
                    { if ($list->smarty_array("group","name"," AND pricing='1' ", "group_array")) }  
                    {foreach from=$group_array item=arr}
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="150">  
                          {$arr.name}
                         </td>
                        <td width="187"> 
                          {translate module=product_attr}
                          field_price_base 
                          {/translate}
                          <input type="text" name="product_attr_price_group[{$arr.id}][price_base]"   size="5">
                          {$list->currency_iso("")}
                        </td>
                        <td width="153"> 
                          {translate module=product_attr}
                          field_price_setup 
                          {/translate}
                          <input type="text" name="product_attr_price_group[{$arr.id}][price_setup]"    size="5">
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
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="65%" align="right"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="product_attribute_product_price_type" value="{$VAR.product_attribute_product_price_type}">
                    <input type="hidden" name="product_attribute_price_type"         value="{$VAR.product_attribute_product_price_type}">
                    <input type="hidden" name="_page" value="product_attr:view">
                    <input type="hidden" name="_page_current" value="product_attr:add">
                    <input type="hidden" name="do[]" value="product_attr:add">
                    <input type="hidden" name="_escape" value="1">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <br>
</form>
