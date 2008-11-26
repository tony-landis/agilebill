<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="charge_add" name="charge_add" method="post" action="">
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=charge}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("charge_status", $VAR.charge_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_sweep_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="charge_sweep_type" >
                      <option value="0" {if $VAR.charge_sweep_type == "0"}selected{/if}>Daily</option>
                      <option value="1" {if $VAR.charge_sweep_type == "1"}selected{/if}>Weekly</option>
                      <option value="2" {if $VAR.charge_sweep_type == "2"}selected{/if}>Monthly</option>
                      <option value="3" {if $VAR.charge_sweep_type == "3"}selected{/if}>Quarterly</option>
                      <option value="4" {if $VAR.charge_sweep_type == "4"}selected{/if}>Semi-anually</option>
                      <option value="5" {if $VAR.charge_sweep_type == "5"}selected{/if}>Anually</option>
                      <option value="6" {if $VAR.charge_sweep_type == "6"}selected{/if}>On 
                      Service Rebill</option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="charge_account_id" default=$VAR.charge_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_product_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.charge_product_id == ''}
                    { $list->menu("", "charge_product_id", "product", "sku", "all", "form_menu") }
                    {else}
                    { $list->menu("", "charge_product_id", "product", "sku", $VAR.charge_product_id, "form_menu") }
                    {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_service_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="charge_service_id" value="{$VAR.charge_service_id}" {if $charge_service_id == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_amount 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="charge_amount" value="{$VAR.charge_amount}" {if $charge_amount == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_quantity 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="charge_quantity" value="{$VAR.charge_quantity}" {if $charge_quantity == true}class="form_field_error"{/if} size="5">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=charge}
                    field_taxable 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("charge_taxable", $VAR.charge_taxable, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%">  
                    {translate module=charge}
                    field_attributes 
                    {/translate} 
                  </td>
                  <td width="65%"> 
                    <input type="text" name="attributes[0][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[0][1]" class=form_field size="16"> <br>
                    <input type="text" name="attributes[1][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[1][1]" class=form_field size="16"> <br>
                    <input type="text" name="attributes[2][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[2][1]" class=form_field size="16"> <br>
                    <input type="text" name="attributes[3][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[3][1]" class=form_field size="16"> <br>
                    <input type="text" name="attributes[4][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[4][1]" class=form_field size="16"> <br>
                    <input type="text" name="attributes[5][0]" class=form_field size="16"> = 
                    <input type="text" name="attributes[5][1]" class=form_field size="16"> 
                  </td>
                </tr> 
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="charge:view">
                    <input type="hidden" name="_page_current" value="charge:add">
                    <input type="hidden" name="do[]" value="charge:add">
                    <input type="hidden" name="charge_date_orig" value="{$smarty.now}">
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
