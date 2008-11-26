

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="discount_add" name="discount_add" method="post" action="">

  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=discount}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_date_start 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->calender_add("discount_date_start", $VAR.discount_date_start, "form_field") }
                    <input type="hidden" name="discount_date_orig" value="{$smarty.now}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->calender_add("discount_date_expire", $VAR.discount_date_expire, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
				   {if $VAR.discount_status == ''}
				    { $list->bool("discount_status", 1, "form_menu") }
				   {else}
                    { $list->bool("discount_status", $VAR.discount_status, "form_menu") }
				   {/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_name" value="{$VAR.discount_name}" {if $discount_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_notes 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <textarea name="discount_notes" cols="40" rows="5" {if $discount_notes == true}class="form_field_error"{/if}>{$VAR.discount_notes}</textarea>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b>{translate module=discount}discount_restrictions{/translate}</b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_max_usage_account 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_max_usage_account" value="{$VAR.discount_max_usage_account}" {if $discount_max_usage_account == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_max_usage_global 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="discount_max_usage_global" value="{$VAR.discount_max_usage_global}" {if $discount_max_usage_global == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_account_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {html_select_account name="discount_avail_account_id" default=$VAR.discount_avail_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_product_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.discount_avail_product_id, "discount_avail_product_id", "product", "sku", "", "10", "form_menu") }
                  </td>
                </tr>
				{if $list->is_installed("host_tld")}
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_tld_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
					{ $list->menu_multi($VAR.discount_avail_tld_id, "discount_avail_tld_id", "host_tld", "name", "", "10", "form_menu") }
                  </td>
                </tr>
				{/if}				
                <tr valign="top"> 
                  <td width="50%"> 
                    {translate module=discount}
                    field_avail_group_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_multi($VAR.discount_avail_group_id, 'discount_avail_group_id', 'group', 'name', '', '10', 'form_menu') }
                  </td>
                </tr>									
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b> 
                      {translate module=discount}
                      discount_new 
                      {/translate}
                      </b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_status 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    { $list->bool("discount_new_status", $VAR.discount_new_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_type 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <select name="discount_new_type" >
                      <option value="0"{if $VAR.discount_new_type == "0"} selected{/if}> 
                      {translate module=discount}
                      percent 
                      {/translate}
                      </option>
                      <option value="1"{if $VAR.discount_new_type == "1"} selected{/if}> 
                      {translate module=discount}
                      flat 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_rate 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_rate" value="{$VAR.discount_new_rate}" {if $discount_new_rate == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_max_discount 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_max_discount" value="{$VAR.discount_new_max_discount}" {if $discount_new_max_discount == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_new_min_cost 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_new_min_cost" value="{$VAR.discount_new_min_cost}" {if $discount_new_min_cost == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="1" cellpadding="10" class="row2">
                <tr> 
                  <td> 
                    <div align="center"><b> 
                      {translate module=discount}
                      discount_recurr 
                      {/translate}
                      </b></div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_status 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    { $list->bool("discount_recurr_status", $VAR.discount_recurr_status, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_type 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <select name="discount_recurr_type" >
                      <option value="0"{if $VAR.discount_recurr_type == "0"} selected{/if}> 
                      {translate module=discount}
                      percent 
                      {/translate}
                      </option>
                      <option value="1"{if $VAR.discount_recurr_type == "1"} selected{/if}> 
                      {translate module=discount}
                      flat 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_rate 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_rate" value="{$VAR.discount_recurr_rate}" {if $discount_recurr_rate == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_max_discount 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_max_discount" value="{$VAR.discount_recurr_max_discount}" {if $discount_recurr_max_discount == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="70%"> 
                    {translate module=discount}
                    field_recurr_min_cost 
                    {/translate}
                  </td>
                  <td width="30%"> 
                    <input type="text" name="discount_recurr_min_cost" value="{$VAR.discount_recurr_min_cost}" {if $discount_recurr_min_cost == true}class="form_field_error"{/if} size="12">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row2">
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <div align="right"> 
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="discount:view">
                      <input type="hidden" name="_page_current" value="discount:add">
                      <input type="hidden" name="do[]" value="discount:add">
                    </div>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table> 
      </td>
    </tr>
  </table>
  <p>&nbsp;</p></form>
