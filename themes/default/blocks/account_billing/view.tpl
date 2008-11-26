{ $method->exe("account_billing","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 
{literal}
	<script src="themes/{/literal}{$THEME_NAME}{literal}/view.js"></script>
    <script language="JavaScript"> 
        var module 		= 'account_billing';
    	var locations 	= '{/literal}{$VAR.module_id}{literal}';		
		var id 			= '{/literal}{$VAR.id}{literal}';
		var ids 		= '{/literal}{$VAR.ids}{literal}';    	 
		var array_id    = id.split(",");
		var array_ids   = ids.split(",");		
		var num=0;
		if(array_id.length > 2) {				 
			document.location = '?_page='+module+':view&id='+array_id[0]+'&ids='+id;				 		
		}else if (array_ids.length > 2) {
			document.write(view_nav_top(array_ids,id,ids));
		}
		
    	// Mass update, view, and delete controller
    	function delete_record(id,ids)
    	{				
    		temp = window.confirm("{/literal}{translate}alert_delete{/translate}{literal}");
    		if(temp == false) return;
    		
    		var replace_id = id + ",";
    		ids = ids.replace(replace_id, '');		
    		if(ids == '') {
    			var url = '?_page=core:search&module=' + module + '&do[]=' + module + ':delete&delete_id=' + id + COOKIE_URL;
    			window.location = url;
    			return;
    		} else {
    			var page = 'view&id=' +ids;
    		}		
    		
    		var doit = 'delete';
    		var url = '?_page='+ module +':'+ page +'&do[]=' + module + ':' + doit + '&delete_id=' + id + COOKIE_URL;
    		window.location = url;	
    	}
    //  END -->
    </script>
{/literal} 

<!-- Loop through each record -->
{foreach from=$account_billing item=account_billing}
<!-- Display the field validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}
<!-- Display each record -->
<form name="account_billing_view" method="post" action="">

  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=account_billing}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                {if $account_billing.card_type != ""}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="account_billing_accout_id" default=$account_billing.account_id}
                  </td>
                </tr>
                {/if}                {if $account_billing.card_type == "amex"}                {/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="35%"> 
                    {translate module=account_billing}
                    field_checkout_plugin_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "account_billing_checkout_plugin_id", "checkout", "name", $account_billing.checkout_plugin_id, "form_field") }
                  </td>
                </tr>
  
                <!-- start credit card form -->
  {if $account_billing.card_type != 'eft'} {if $account_billing.card_type != ""}
                  <tr valign="top">
                    <td width="35%"> {translate module=account_billing} field_card_type {/translate} </td>
                    <td width="65%"> {if $account_billing.checkout_plugin_id != "" && $account_billing.card_type != ""} { $list->card_type_menu($account_billing.card_type, $account_billing.checkout_plugin_id,'account_billing_card_type','') } {elseif $account_billing.checkout_plugin_id == "" && $VAR.account_billing_card_type != "" } { $list->card_type_menu($VAR.account_billing_card_type, $account_billing.checkout_plugin_id,'account_billing_card_type','') } {else}
                          <select name="account_billing_card_type" >
                            <option value="visa" {if $account_billing.card_type == "visa"}selected{/if}> {translate module=checkout} card_type_visa {/translate} </option>
                            <option value="mc" {if $account_billing.card_type == "mc"}selected{/if}> {translate module=checkout} card_type_mc {/translate} </option>
                            <option value="amex" {if $account_billing.card_type == "amex"}selected{/if}> {translate module=checkout} card_type_amex {/translate} </option>
                          </select>
      {/if} </td>
                  </tr>
  {/if}  <tr valign="top">
    <td width="35%"> {translate module=account_billing} card_num_new {/translate} </td>
    <td width="65%"><b>
      <input type="text" name="account_billing_card_num" value="{ $list->decrypt($account_billing.card_num) }"  size="24" maxlength="16">
    </b></td>
  </tr>
  <tr valign="top">
    <td width="35%"> {translate module=account_billing} field_card_exp_month {/translate} </td>
    <td width="65%">&nbsp;</td>
  </tr>
  <tr valign="top">
    <td width="35%"><select name="account_billing_card_exp_month" >
        <option value="01" {if $account_billing.card_exp_month == "1"}selected{/if}>1 (Jan)</option>
        <option value="02" {if $account_billing.card_exp_month == "2"}selected{/if}>2 (Feb)</option>
        <option value="03" {if $account_billing.card_exp_month == "3"}selected{/if}>3 (Mar)</option>
        <option value="04" {if $account_billing.card_exp_month == "4"}selected{/if}>4 (Apr)</option>
        <option value="05" {if $account_billing.card_exp_month == "5"}selected{/if}>5 (May)</option>
        <option value="06" {if $account_billing.card_exp_month == "6"}selected{/if}>6 (Jun)</option>
        <option value="07" {if $account_billing.card_exp_month == "7"}selected{/if}>7 (Jul)</option>
        <option value="08" {if $account_billing.card_exp_month == "8"}selected{/if}>8 (Aug)</option>
        <option value="09" {if $account_billing.card_exp_month == "9"}selected{/if}>9 (Sep)</option>
        <option value="10" {if $account_billing.card_exp_month == "10"}selected{/if}>10 (Oct)</option>
        <option value="11" {if $account_billing.card_exp_month == "11"}selected{/if}>11 (Nov)</option>
        <option value="12" {if $account_billing.card_exp_month == "12"}selected{/if}>12 (Dec)</option>
      </select>
      {translate module=account_billing} field_card_exp_year {/translate} </td>
    <td width="65%">20
        <input type="text" name="account_billing_card_exp_year" value="{$account_billing.card_exp_year}"  size="2" maxlength="2">
      (ex: 08) </td>
  </tr>
  {if $account_billing.card_type == "amex"}
  <tr class="row1" valign="middle" align="left">
    <td width="35%"> {translate module=account_billing} field_card_start_month {/translate} </td>
    <td width="65%"> {translate module=account_billing} field_card_start_year {/translate} </td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td width="35%"><select name="account_billing_card_start_month" >
        <option value="01" {if $account_billing.card_start_month == "1"}selected{/if}>1 (Jan)</option>
        <option value="02" {if $account_billing.card_start_month == "2"}selected{/if}>2 (Feb)</option>
        <option value="03" {if $account_billing.card_start_month == "3"}selected{/if}>3 (Mar)</option>
        <option value="04" {if $account_billing.card_start_month == "4"}selected{/if}>4 (Apr)</option>
        <option value="05" {if $account_billing.card_start_month == "5"}selected{/if}>5 (May)</option>
        <option value="06" {if $account_billing.card_start_month == "6"}selected{/if}>6 (Jun)</option>
        <option value="07" {if $account_billing.card_start_month == "7"}selected{/if}>7 (Jul)</option>
        <option value="08" {if $account_billing.card_start_month == "8"}selected{/if}>8 (Aug)</option>
        <option value="09" {if $account_billing.card_start_month == "9"}selected{/if}>9 (Sep)</option>
        <option value="10" {if $account_billing.card_start_month == "10"}selected{/if}>10 (Oct)</option>
        <option value="11" {if $account_billing.card_start_month == "11"}selected{/if}>11 (Nov)</option>
        <option value="12" {if $account_billing.card_start_month == "12"}selected{/if}>12 (Dec)</option>
      </select>
    </td>
    <td width="65%">20
        <input type="text" name="account_billing_card_start_year" value="{$account_billing.card_start_year}"  size="2" maxlength="2">
      (example: 08) </td>
  </tr>
  {/if} {/if}
  <!-- end credit card form -->
  <!-- start eft form -->
  {if $account_billing.card_type == "eft"}
  <tr class="row1" valign="middle" align="left">
    <td width="35%">{translate module=account_billing} field_eft_trn {/translate}</td>
    <td width="65%"><b>
      <input type="text" name="account_billing_eft_trn" value="{ $list->decrypt($account_billing.eft_trn) }">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td width="35%">{translate module=account_billing} field_eft_check_acct {/translate}</td>
    <td width="65%"><input type="text" name="account_billing_eft_check_acct" value="{ $list->decrypt($account_billing.eft_check_acct) }">
    </td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td width="35%">{translate module=checkout} eft_check_acct_type {/translate}</td>
    <td width="65%"><b>
      <select name="account_billing_eft_check_acct_type" >
        <option value="p" {if $account_billing.eft_check_acct_type == "p"}selected{/if}>{translate module=checkout}eft_type_p{/translate}</option>
        <option value="b" {if $account_billing.eft_check_acct_type == "b"}selected{/if}>{translate module=checkout}eft_type_b{/translate}</option>
      </select>
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td width="35%"><b></b></td>
    <td width="65%"><b> </b></td>
  </tr>
  {/if}
  <!-- end eft form -->
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_first_name{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_first_name" value="{$account_billing.first_name}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_last_name{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_last_name" value="{$account_billing.last_name}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_address1{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_address1" value="{$account_billing.address1}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_address2{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_address2" value="{$account_billing.address2}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_city{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_city" value="{$account_billing.city}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_state{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_state" value="{$account_billing.state}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_zip{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_zip" value="{$account_billing.zip}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_country_id{/translate} </td>
    <td> 
	  { $list->menu("no", "account_billing_country_id", "country", "name", $account_billing.country_id, "") }
	</td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=account} field_email{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_email" value="{$account_billing.email}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td>{translate module=checkout} phone{/translate} </td>
    <td><b>
      <input type="text" name="account_billing_phone" value="{$account_billing.phone}">
    </b></td>
  </tr>
  <tr class="row1" valign="middle" align="left">
    <td><input type="button" name="Delete" value="{translate}delete{/translate}" class="form_button" onClick="user_delete({$account_billing.id})"></td>
    <td align="right">&nbsp;&nbsp;
        <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
    </td>
  </tr>
              </table>              
            </td>
        </tr>
        </table>
      </td>
    </tr>
  </table>
    <input type="hidden" name="_page" value="account_billing:view">
    <input type="hidden" name="account_billing_id" value="{$account_billing.id}">
    <input type="hidden" name="do[]" value="account_billing:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}
{/if}
