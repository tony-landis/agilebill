

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="account_billing_add" name="account_billing_add" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=account_billing}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="account_billing_accout_id" default=$VAR.account_billing_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_card_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="account_billing_card_type" >
                      <option value="" {if $VAR.account_billing_card_type == ""}selected{/if}></option>
                      <option value="visa" {if $VAR.account_billing_card_type == "visa"}selected{/if}> 
                      {translate module=checkout}
                      card_type_visa 
                      {/translate}
                      </option>
                      <option value="mc" {if $VAR.account_billing_card_type == "mc"}selected{/if}> 
                      {translate module=checkout}
                      card_type_mc 
                      {/translate}
                      </option>
                      <option value="amex" {if $VAR.account_billing_card_type == "amex"}selected{/if}> 
                      {translate module=checkout}
                      card_type_amex 
                      {/translate}
                      </option>
                      <option value="discover" {if $VAR.account_billing_card_type == "discover"}selected{/if}> 
                      {translate module=checkout}
                      card_type_discover 
                      {/translate}
                      </option>
                      <option value="delta" {if $VAR.account_billing_card_type == "delta"}selected{/if}> 
                      {translate module=checkout}
                      card_type_delta 
                      {/translate}
                      </option>
                      <option value="solo" {if $VAR.account_billing_card_type == "solo"}selected{/if}> 
                      {translate module=checkout}
                      card_type_solo 
                      {/translate}
                      </option>
                      <option value="switch" {if $VAR.account_billing_card_type == "switch"}selected{/if}> 
                      {translate module=checkout}
                      card_type_switch 
                      {/translate}
                      </option>
                      <option value="jcb" {if $VAR.account_billing_card_type == "jcb"}selected{/if}> 
                      {translate module=checkout}
                      card_type_jcb 
                      {/translate}
                      </option>
                      <option value="diners" {if $VAR.account_billing_card_type == "diners"}selected{/if}> 
                      {translate module=checkout}
                      card_type_diners 
                      {/translate}
                      </option>
                      <option value="carteblanche" {if $VAR.account_billing_card_type == "carteblanche"}selected{/if}> 
                      {translate module=checkout}
                      card_type_carteblanche 
                      {/translate}
                      </option>
                      <option value="enroute" {if $VAR.account_billing_card_type == "enroute"}selected{/if}> 
                      {translate module=checkout}
                      card_type_enroute 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_card_num 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_billing_card_num" value="{$VAR.account_billing_card_num}" {if $account_billing_card_num == true}class="form_field_error"{/if} maxlength="16" size="24">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_card_exp_month 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {translate module=account_billing}
                    field_card_exp_year 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    <select name="account_billing_card_exp_month" >
                      <option value="01" {if $VAR.account_billing_card_exp_month == "1"}selected{/if}>1 
                      (Jan)</option>
                      <option value="02" {if $VAR.account_billing_card_exp_month == "2"}selected{/if}>2 
                      (Feb)</option>
                      <option value="03" {if $VAR.account_billing_card_exp_month == "3"}selected{/if}>3 
                      (Mar)</option>
                      <option value="04" {if $VAR.account_billing_card_exp_month == "4"}selected{/if}>4 
                      (Apr)</option>
                      <option value="05" {if $VAR.account_billing_card_exp_month == "5"}selected{/if}>5 
                      (May)</option>
                      <option value="06" {if $VAR.account_billing_card_exp_month == "6"}selected{/if}>6 
                      (Jun)</option>
                      <option value="07" {if $VAR.account_billing_card_exp_month == "7"}selected{/if}>7 
                      (Jul)</option>
                      <option value="08" {if $VAR.account_billing_card_exp_month == "8"}selected{/if}>8 
                      (Aug)</option>
                      <option value="09" {if $VAR.account_billing_card_exp_month == "9"}selected{/if}>9 
                      (Sep)</option>
                      <option value="10" {if $VAR.account_billing_card_exp_month == "10"}selected{/if}>10 
                      (Oct)</option>
                      <option value="11" {if $VAR.account_billing_card_exp_month == "11"}selected{/if}>11 
                      (Nov)</option>
                      <option value="12" {if $VAR.account_billing_card_exp_month == "12"}selected{/if}>12 
                      (Dec)</option>
                    </select>
                  </td>
                  <td width="65%"> 20 
                    <input type="text" name="account_billing_card_exp_year" value="{$VAR.account_billing_card_exp_year}"  size="2" maxlength="2">
                    </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_card_start_month 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {translate module=account_billing}
                    field_card_start_year 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    <select name="account_billing_card_start_month" >
                      <option value="01" {if $VAR.account_billing_card_start_month == "1"}selected{/if}>1 
                      (Jan)</option>
                      <option value="02" {if $VAR.account_billing_card_start_month == "2"}selected{/if}>2 
                      (Feb)</option>
                      <option value="03" {if $VAR.account_billing_card_start_month == "3"}selected{/if}>3 
                      (Mar)</option>
                      <option value="04" {if $VAR.account_billing_card_start_month == "4"}selected{/if}>4 
                      (Apr)</option>
                      <option value="05" {if $VAR.account_billing_card_start_month == "5"}selected{/if}>5 
                      (May)</option>
                      <option value="06" {if $VAR.account_billing_card_start_month == "6"}selected{/if}>6 
                      (Jun)</option>
                      <option value="07" {if $VAR.account_billing_card_start_month == "7"}selected{/if}>7 
                      (Jul)</option>
                      <option value="08" {if $VAR.account_billing_card_start_month == "8"}selected{/if}>8 
                      (Aug)</option>
                      <option value="09" {if $VAR.account_billing_card_start_month == "9"}selected{/if}>9 
                      (Sep)</option>
                      <option value="10" {if $VAR.account_billing_card_start_month == "10"}selected{/if}>10 
                      (Oct)</option>
                      <option value="11" {if $VAR.account_billing_card_start_month == "11"}selected{/if}>11 
                      (Nov)</option>
                      <option value="12" {if $VAR.account_billing_card_start_month == "12"}selected{/if}>12 
                      (Dec)</option>
                    </select>
                  </td>
                  <td width="65%"> 20 
                    <input type="text" name="account_billing_card_start_year" value="{$VAR.account_billing_card_start_year}"  size="2" maxlength="2">
                    </td>
                </tr>
				<tr>
					<td width="17%">{translate module=account} field_first_name {/translate}{if $VAR.first_name_error}<font color="#FF0000">*</font>{/if}</td>
					<td width="17%"><input type="text" name="checkout_plugin_data[first_name]"  value="{$VAR.first_name}" size="12"></td>
				<tr>
					<td width="16%">{translate module=account} field_last_name {/translate}{if $VAR.last_name_error}<font color="#FF0000">*</font>{/if}</td>
					<td width="16%"><input type="text" name="checkout_plugin_data[last_name]"  value="{$VAR.last_name}" size="12"></td>
				</tr>
				<tr>
					<td width="17%">{translate module=account} field_company {/translate}</td>
					<td width="17%"><input type="text" name="checkout_plugin_data[company]"  value="{$VAR.company}" size="20"></td>
				</tr>
				<tr>
					<td width="17%">{translate module=account} field_address1 {/translate}{if $VAR.address1_error}<font color="#FF0000">*</font>{/if}</td>
					<td width="17%"><input type="text" name="checkout_plugin_data[address1]"  value="{$VAR.address1}" size="12"></td>
				</tr>
				<tr>
					<td width="16%">{translate module=account} field_address2{/translate}</td>
					<td width="16%"><input type="text" name="checkout_plugin_data[address2]"  value="{$VAR.address2}" size="12"></td>
				</tr>
				<tr>
					<td width="17%">{translate module=account} field_city {/translate}</td>
					<td width="17%"><input type="text" name="checkout_plugin_data[city]"  value="{$VAR.city}" size="20"></td>
				</tr>
				<tr>
					<td width="17%">{translate module=account} field_state {/translate}{if $VAR.state_error}<font color="#FF0000">*</font>{/if}</td>
					<td width="17%"><input type="text" name="checkout_plugin_data[state]"  value="{$VAR.state}" size="12"></td>
				</tr>
				<tr>
					<td width="16%">{translate module=account} field_zip{/translate}{if $VAR.zip_error}<font color="#FF0000">*</font>{/if}</td>
					<td width="16%"><input type="text" name="checkout_plugin_data[zip]"  value="{$VAR.zip}" size="12"></td>
				</tr>
				<tr>
					<td width="17%">{translate module=account} field_country_id{/translate}</td>
					<td width="17%">
						{if $VAR.country_id != ""}
						{ $list->menu("no", "checkout_plugin_data[country_id]", "country", "name", $VAR.country_id, "") }
						{else}
						{ $list->menu("no", "checkout_plugin_data[country_id]", "country", "name", $smarty.const.DEFAULT_COUNTRY, "") }
						{/if}
					</td>
				</tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_checkout_plugin_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "account_billing_checkout_plugin_id", "checkout", "name", $VAR.account_billing_checkout_plugin_id, "form_field") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="account_billing:view">
                    <input type="hidden" name="_page_current" value="account_billing:add">
                    <input type="hidden" name="do[]" value="account_billing:add">
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
