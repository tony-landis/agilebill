
{ $method->exe("voip_prepaid","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="voip_prepaid_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=voip_prepaid}title_search{/translate}
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
                        {html_select_account name="voip_prepaid_account_id"} </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_product_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu('no', "voip_prepaid_product_id", "product", "sku", $VAR.voip_prepaid_product_id, "", "form_menu") }                      &nbsp;&nbsp; </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_pin
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_pin" value="{$VAR.voip_prepaid_pin}" {if $voip_prepaid_pin == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_balance
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_balance" value="{$VAR.voip_prepaid_balance}" {if $voip_prepaid_balance == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
				  
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_bulk
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_bulk" value="{$VAR.voip_prepaid_bulk}" {if $voip_prepaid_bulk == true}class="form_field_error"{/if}> 
                        &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
				  				  
                   <tr valign="top">
                    <td width="35%">
                        {translate module=voip_prepaid}
                            field_in_use
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="voip_prepaid_in_use" value="{$VAR.voip_prepaid_in_use}" {if $voip_prepaid_in_use == true}class="form_field_error"{/if} size="5"> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$voip_prepaid_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$voip_prepaid item=record}
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
                      <input type="hidden" name="module" value="voip_prepaid">
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
