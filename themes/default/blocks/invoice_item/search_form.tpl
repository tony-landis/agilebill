
{ $method->exe("invoice_item","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="invoice_item_search" method="post" action="">
  
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice_item}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_sku
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_sku" value="{$VAR.invoice_item_sku}" {if $invoice_item_sku == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_item_type
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("invoice_item_item_type", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_product_attr
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_product_attr" value="{$VAR.invoice_item_product_attr}" {if $invoice_item_product_attr == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_base
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_price_base" value="{$VAR.invoice_item_price_base}" {if $invoice_item_price_base == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_setup
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_price_setup" value="{$VAR.invoice_item_price_setup}" {if $invoice_item_price_setup == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_recurring_schedule
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_recurring_schedule" value="{$VAR.invoice_item_recurring_schedule}" {if $invoice_item_recurring_schedule == true}class="form_field_error"{/if} size="5"> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_name" value="{$VAR.invoice_item_domain_name}" {if $invoice_item_domain_name == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_tld
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_tld" value="{$VAR.invoice_item_domain_tld}" {if $invoice_item_domain_tld == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_term
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_term" value="{$VAR.invoice_item_domain_term}" {if $invoice_item_domain_term == true}class="form_field_error"{/if} size="5"> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_type
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_type" value="{$VAR.invoice_item_domain_type}" {if $invoice_item_domain_type == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text"  name="limit" size="5" value="{$invoice_item_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select  name="order_by">
        		          {foreach from=$invoice_item item=record}
                            <option value="{$record.field}">{$record.translate}</option>
        		          {/foreach}
                      </select>
                    </td>
                  </tr>

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="invoice_item">
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
