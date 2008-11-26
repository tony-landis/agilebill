{ $method->exe("invoice","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else} 
<form name="invoice_search" method="post" action="">  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=invoice}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="invoice_id" value="{$VAR.invoice_id}" {if $invoice_id == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>  
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_account_id
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="invoice_account_id" default=$VAR.invoice_account_id}
                  </td>
                </tr>
				
				{if $list->is_installed('affiliate')}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_affiliate_id
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_affiliate name="invoice_affiliate_id" default=$VAR.invoice_affiliate_id}
                  </td>
                </tr>
				{/if}
				
				{if $list->is_installed('campaign')}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_campaign_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "invoice_campaign_id", "campaign", "name", "all", "form_menu") }
                  </td>
                </tr>
				{/if}
								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_process_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("invoice_process_status", "all", "form_menu") }
                  </td>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_refund_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("invoice_refund_status", "all", "form_menu") }
                  </td>				  
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    paid
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("invoice_billing_status", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_print_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("invoice_print_status", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_checkout_plugin_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "invoice_checkout_plugin_id", "checkout", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_billed_currency_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "invoice_billed_currency_id", "currency", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("invoice_date_orig", $VAR.invoice_date_orig, "form_field", "") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_due_date
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("invoice_due_date", $VAR.invoice_due_date, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    field_notice_next_date 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->calender_search("invoice_notice_next_date", $VAR.invoice_notice_next_date, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=invoice}
                    search_memo
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="join_memo_text" value="{$VAR.join_memo_text}">
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
				
								
				{if $list->is_installed('host_server') }
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="join_domain_name" value="{$VAR.join_domain_name}">
                    . 
                    <input type="text" name="join_domain_tld" value="{$VAR.join_domain_tld}" size="6">
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
				{/if}
				
								
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate module=product}
                    field_sku 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->menu("", "join_product_id", "product", "sku", "all", "\" onchange=\"showAttributes(this)") }
					{literal}<script language="javascript">
						function showAttributes(obj)
						{
							if(obj.value == '')
							{
								document.getElementById("attributes1").style.display='none';
								document.getElementById("attributes2").style.display='none';
							}
							else
							{
								document.getElementById("attributes1").style.display='block';
								document.getElementById("attributes2").style.display='block';
							}
						}
					</script>
                    {/literal}
                  </td>
                </tr>
				 
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
				  <DIV id="attributes1" style="display:none">
                    {translate module=product}
                    attributes 
                    {/translate}
					</DIV>
                  </td>
                  <td width="65%">
				  <DIV id="attributes2" style="display:none">
                    <input type="text" name="item_attributes[0][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[0][1]" class=form_field size="16"> <br>
                    <input type="text" name="item_attributes[1][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[1][1]" class=form_field size="16"> <br>
                    <input type="text" name="item_attributes[2][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[2][1]" class=form_field size="16"> <br>
                    <input type="text" name="item_attributes[3][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[3][1]" class=form_field size="16"> <br>
                    <input type="text" name="item_attributes[4][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[4][1]" class=form_field size="16"> <br>
                    <input type="text" name="item_attributes[5][0]" class=form_field size="16"> = 
                    <input type="text" name="item_attributes[5][1]" class=form_field size="16">  																									
					</DIV>
                  </td>
                </tr> 
				
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$invoice_limit}">
                  </td>
                </tr>								
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$invoice item=record}
                      <option value="{$record.field}"> 
                      {$record.translate}
                      </option>
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
                    <input type="hidden" name="module" value="invoice">
                    <input type="hidden" name="_next_page_one" value="view">
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
