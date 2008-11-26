 
<!-- Display the form to collect the input values for bulk pin creation -->
<form id="voip_prepaid_add" name="voip_prepaid_add" method="post" action=""> 
{$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_prepaid}title_add_bulk{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">
                     {html_select_account name="voip_prepaid_account_id" default=$VAR.voip_prepaid_account_id}  
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_product_id
                        {/translate}</td>
                  <td width="65%"> 
                    { $list->menu('no', "voip_prepaid_product_id", "product", "sku", $VAR.voip_prepaid_product_id, "", "form_menu") }                    
					</td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            bulk_ref
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_bulk" value="{$VAR.voip_prepaid_bulk}" {if $voip_prepaid_pin == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            bulk_qty
                        {/translate}</td>
                    <td width="65%">
                        <input name="voip_prepaid_qty" type="text" value="{$VAR.voip_prepaid_qty}" size="5" {if $voip_prepaid_balance == true}class="form_field_error"{/if}>
                    </td>
                </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_balance
                        {/translate}</td>
                    <td width="65%">
                        <input name="voip_prepaid_balance" type="text" value="{$VAR.voip_prepaid_balance}" size="5" {if $voip_prepaid_balance == true}class="form_field_error"{/if}>
                    </td>
                </tr>				
           <tr valign="top">
                    <td width="35%">{translate module=voip_prepaid} field_date_expire {/translate}</td>
                    <td width="65%">{ $list->calender_add("voip_prepaid_date_expire", $VAR.voip_prepaid_date_expire,"") }
                    </td>
            </tr>
           <tr valign="top">
             <td></td>
             <td><input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
               <input type="hidden" name="_page" value="voip_prepaid:view">
               <input type="hidden" name="_page_current" value="voip_prepaid:add">
               <input type="hidden" name="do[]" value="voip_prepaid:add">
               <input type="hidden" name="bulk" value="true"></td>
           </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>
