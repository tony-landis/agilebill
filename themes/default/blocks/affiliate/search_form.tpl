{ $method->exe("affiliate","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else} 
<form name="affiliate_search" method="post" action=""> 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=affiliate}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="affiliate_id" value="{$VAR.affiliate_id}" {if $affiliate_id == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("affiliate_date_orig", $VAR.affiliate_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("affiliate_status", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="affiliate_account_id" default=$VAR.affiliate_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_parent_affiliate_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_affiliate name="affiliate_parent_affiliate_id" default=$VAR.affiliate_parent_affiliate_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=affiliate}
                    field_recurr_max_commission_periods 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="affiliate_recurr_max_commission_periods" value="{$VAR.affiliate_recurr_max_commission_periods}" {if $affiliate_recurr_max_commission_periods == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
				
				{ $method->exe("affiliate","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="50%" height="18"> 
                    {$record.name}
                  </td>
                  <td width="65%" height="18"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
				
								
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="50%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$affiliate_limit}">
                  </td>
                </tr>
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="50%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$affiliate item=record}
                      <option value="{$record.field}"> 
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="50%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="module" value="affiliate">
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
