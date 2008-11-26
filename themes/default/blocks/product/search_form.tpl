
{ $method->exe("product","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="product_search" method="post" action="">
  
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=product}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="49%"> 
                    {translate module=product}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    { $list->calender_search("product_date_orig", $VAR.product_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="49%"> 
                    {translate module=product}
                    field_sku 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    <input type="text" name="product_sku" value="{$VAR.product_sku}" {if $product_sku == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="49%"> 
                    {translate module=product}
                    field_taxable 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    { $list->bool("product_taxable", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="49%"> 
                    {translate module=product}
                    field_active 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    { $list->bool("product_active", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="49%"> 
                    {translate module=product}
                    field_price_type 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    <input type="radio" name="product_price_type" value="0" {if $VAR.product_price_type == "0" }checked{/if}>
                    {translate module=product}
                    price_type_one 
                    {/translate}
                    <br>
                    <input type="radio" name="product_price_type" value="1" {if $VAR.product_price_type == "1"}checked{/if}>
                    {translate module=product}
                    price_type_recurr 
                    {/translate}
                    <br>
                    <input type="radio" name="product_price_type" value="2" {if $VAR.product_price_type == "2"}checked{/if}>
                    {translate module=product}
                    price_type_trial 
                    {/translate}
                  </td>
                </tr>
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="49%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    <input type="text"  name="limit" size="5" value="{$product_limit}">
                  </td>
                </tr>
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="49%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="51%"> 
                    <select  name="order_by">
                      {foreach from=$product item=record}
                      <option value="{$record.field}"> 
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="49%"></td>
                  <td width="51%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="module" value="product">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
