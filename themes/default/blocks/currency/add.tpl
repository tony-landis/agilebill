
<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}


<!-- Display the form to collect the input values -->
<form id="currency_add" name="currency_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=currency}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%">
                    {translate module=currency}
                    field_name
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_name" value="{$VAR.currency_name}" {if $currency_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_symbol 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_symbol" value="{$VAR.currency_symbol}" {if $currency_symbol == true}class="form_field_error"{/if} size="2">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_three_digit 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="currency_three_digit" value="{$VAR.currency_three_digit}" {if $currency_three_digit == true}class="form_field_error"{/if} size="3" maxlength="3">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_status
                    {/translate}
                  </td>
                  <td width="65%">
                    {$list->bool("currency_status",$VAR.currency_status, "form_menu")}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=currency}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="65%">
                    <textarea name="currency_notes" cols="40" rows="2" {if $currency_notes == true}class="form_field_error"{/if}>{$VAR.currency_notes}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="currency:view">
                    <input type="hidden" name="_page_current" value="currency:add">
                    <input type="hidden" name="do[]" value="currency:add">
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
