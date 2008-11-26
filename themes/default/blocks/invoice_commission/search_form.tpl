
{ $method->exe("invoice_commission","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="invoice_commission_search" method="post" action="">
  
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice_commission}title_search{/translate}
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
                        <input type="text" name="invoice_commission_date_orig" value="{$VAR.invoice_commission_date_orig}" {if $invoice_commission_date_orig == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_date_last" value="{$VAR.invoice_commission_date_last}" {if $invoice_commission_date_last == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_invoice_id
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_invoice_id" value="{$VAR.invoice_commission_invoice_id}" {if $invoice_commission_invoice_id == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=invoice_commission}
                            field_commission
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="invoice_commission_commission" value="{$VAR.invoice_commission_commission}" {if $invoice_commission_commission == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text"  name="limit" size="5" value="{$invoice_commission_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select  name="order_by">
        		          {foreach from=$invoice_commission item=record}
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
                      <input type="hidden" name="module" value="invoice_commission">
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
