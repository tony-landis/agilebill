

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="invoice_commission_add" name="invoice_commission_add" method="post" action="">

<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice_commission}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_date_orig" value="{$VAR.invoice_commission_date_orig}" {if $invoice_commission_date_orig == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_date_last" value="{$VAR.invoice_commission_date_last}" {if $invoice_commission_date_last == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_invoice_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_invoice_id" value="{$VAR.invoice_commission_invoice_id}" {if $invoice_commission_invoice_id == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_commission
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_commission" value="{$VAR.invoice_commission_commission}" {if $invoice_commission_commission == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="invoice_commission:view">
                      <input type="hidden" name="_page_current" value="invoice_commission:add">
                      <input type="hidden" name="do[]" value="invoice_commission:add">
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
