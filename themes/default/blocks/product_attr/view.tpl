{ $block->display("core:top_clean") }

{ $method->exe("product_attr","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 
<!-- Loop through each record -->
{foreach from=$product_attr item=product_attr} <a name="{$product_attr.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="product_attr_view" method="post" action="">
  
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
                    <input type="text" name="product_attr_name" value="{$product_attr.name}" {if $product_attr_name == true}class="form_field_error"{/if} size="45">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_description 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="product_attr_description" size="45"    value="{$product_attr.description}">
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
                  </td>
                  <td width="50%"> 
                    <textarea name="product_attr_collect_default" cols="40" rows="2"  >{$product_attr.collect_default}</textarea>
                  </td>
                </tr>
				
				{if $product_attr.collect_type != "0" && $product_attr.collect_type != "" }
                <tr valign="top">
                  <td width="50%"> 
                    {translate module=product_attr}
                    input_req 
                    {/translate}
                  </td>
                  <td width="50%">
                    { $list->bool("product_attr_required", $product_attr.required, "form_menu") }
                  </td>
                </tr>
				{/if}
				
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
                    <input type="text" name="product_attr_price_base" value="{$product_attr.price_base}" {if $product_attr_price_base == true}class="form_field_error"{/if} size="5">
                    {$list->currency_iso("")}
                   </td>
                  <td width="50%"> 
                    {translate module=product_attr}
                    field_price_setup 
                    {/translate}
                    &nbsp;&nbsp; 
                    <input type="text" name="product_attr_price_setup" value="{$product_attr.price_setup}" {if $product_attr_price_setup == true}class="form_field_error"{/if} size="5">
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
                  <td width="65%" class="row2">  
				  {$list->unserial($product_attr.price_group, "attr_group_array")}
				   
				   {$attr_group_array[$arr.id].price_base}
				   <BR>
				   
                    { if ($list->smarty_array("group","name"," AND pricing='1' ", "group_array")) } 
                    {foreach from=$group_array item=arr}
					{assign var="idx" value=$arr.id}
                    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="row1">
                      <tr> 
                        <td width="150"> 
                          {$arr.name}
                          </td>
                        <td width="187"> 
                          {translate module=product_attr}
                          field_price_base 
                          {/translate}
						  
                          <input type="text" name="product_attr_price_group[{$arr.id}][price_base]" value="{$attr_group_array[$idx].price_base}"  size="5">
                          {$list->currency_iso("")}
                        </td>
                        <td width="153"> 
                          {translate module=product_attr}
                          field_price_setup 
                          {/translate}
                          <input type="text" name="product_attr_price_group[{$arr.id}][price_setup]" value="{$attr_group_array[$idx].price_setup}"   size="5">
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
                <tr valign="middle"> 
                  <td width="40%" align="left"> <a href="?_page=product_attr:add&product_attr_product_id={$product_attr.product_id}&_escape=1&_escape_next=1">
                    {translate module=product_attr}
                    title_add 
                    {/translate}
                    </a> </td>
                  <td width="26%"> <a href="?_page=core:search_iframe&module=product_attr&product_attr_product_id={$product_attr.product_id}&_next_page_none=add&name_id1=product_attr_product_id&val_id1={$product_attr.product_id}&_escape=1&_escape_next=1&do%5B%5D=product_attr:delete&id={$product_attr.id}">
                    {translate module=product_attr}
                    title_delete 
                    {/translate}
                    </a> </td>
                  <td width="34%" align="right"> 
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
  <input type="hidden" name="_page" value="product_attr:view">
    <input type="hidden" name="product_attr_id" value="{$product_attr.id}">
    <input type="hidden" name="do[]" value="product_attr:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
