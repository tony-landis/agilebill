

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="invoice_item_add" name="invoice_item_add" method="post" action="">

<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice_item}title_add{/translate}
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
                        <input type="text" name="invoice_item_sku" value="{$VAR.invoice_item_sku}" {if $invoice_item_sku == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_item_type
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("invoice_item_item_type", $VAR.invoice_item_item_type, "form_menu") }
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_product_attr
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_product_attr" value="{$VAR.invoice_item_product_attr}" {if $invoice_item_product_attr == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_base
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_price_base" value="{$VAR.invoice_item_price_base}" {if $invoice_item_price_base == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_price_setup
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_price_setup" value="{$VAR.invoice_item_price_setup}" {if $invoice_item_price_setup == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_recurring_schedule
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_recurring_schedule" value="{$VAR.invoice_item_recurring_schedule}" {if $invoice_item_recurring_schedule == true}class="form_field_error"{/if} size="5">
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_name
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_name" value="{$VAR.invoice_item_domain_name}" {if $invoice_item_domain_name == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_tld
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_tld" value="{$VAR.invoice_item_domain_tld}" {if $invoice_item_domain_tld == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_term
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_term" value="{$VAR.invoice_item_domain_term}" {if $invoice_item_domain_term == true}class="form_field_error"{/if} size="5">
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_item}
                            field_domain_type
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_item_domain_type" value="{$VAR.invoice_item_domain_type}" {if $invoice_item_domain_type == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="invoice_item:view">
                      <input type="hidden" name="_page_current" value="invoice_item:add">
                      <input type="hidden" name="do[]" value="invoice_item:add">
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
