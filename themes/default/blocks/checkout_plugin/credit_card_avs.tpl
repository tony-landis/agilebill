<form id="checkout_form" name="checkout_form" method="post" action="" onSubmit="javascript:void(0)"> 
<input type="hidden" name="option" value="{$VAR.option}">
{if $VAR.invoice_id == ""}
	{if $VAR.admin != '' && $VAR.account_id != '' }
	<input type="hidden" name="admin" value="1">
	<input type="hidden" name="do[]" value="checkout:admin_checkoutnow">
	<input type="hidden" name="_page" value="checkout:admin_checkout">
	<input type="hidden" name="account_id" value="{$VAR.account_id}">
	{else}
	<input type="hidden" name="do[]" value="checkout:checkoutnow">
	<input type="hidden" name="_page" value="checkout:checkout">
	{/if}
{else}
	{if $VAR.admin != '' && $VAR.account_id != '' }
	<input type="hidden" name="admin" value="1">
	<input type="hidden" name="do[]" value="checkout:admin_checkoutnow">
	<input type="hidden" name="_page" value="checkout:admin_checkout">
	<input type="hidden" name="account_id" value="{$VAR.account_id}">	
	{else}
	<input type="hidden" name="do[]" value="invoice:checkoutnow">
	{if $VAR.invoice_id > 0}
		<input type="hidden" name="_page" value="invoice:user_view"> 
	{else}
		<input type="hidden" name="_page" value="invoice:checkout_multiple"> 
	{/if}
	{/if}
	<input type="hidden" name="invoice_id" value="{$VAR.invoice_id}"> 
{/if}
  
<!-- get cards on file -->
{$method->exe("account_billing","list_on_file")}
<input type="hidden" name="new_card" id="new_card" value="{if $VAR.new_card!=''}{$VAR.new_card}{elseif $onfile}2{else}1{/if}">

<!-- show old card on file -->
<div id="onfile" {if !$onfile || $VAR.new_card == 1}{style_hide}{/if}>
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td class="row2"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="1" class="table_heading">
                <tr> 
                  <td valign="top" align="center" class="body"> 
                    {if $VAR.msg != ""}
                    {$VAR.msg|upper}
                    {else}
                    {translate module=checkout}
                    enter_card_onfile
                    {/translate}
                    {/if} </td>
                  </tr>
                </table> 
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="body">
                <tr>  
                  <td width="35%"> 
                    <select name="account_billing_id" id="account_billing_id">{html_options options=$onfile selected=$VAR.account_billing_id}</select>
                    <a href="javascript:void(0);" onClick="editSavedCard('{$VAR.admin}')">{translate}edit{/translate}</a>
                  </td>
                  <td width="25%"><b>
                    <input type="submit" id="submitform" name="submitform" value="{translate module=checkout}process_ord{/translate}"  onClick="javascript:checkoutNow(2)">
                  </b>  
                  </td>
                  <td width="20%"><a href="javascript:void(0);" onClick="enter_new_card()"><U>{translate module=checkout} enter_new_card {/translate}</U></a></td>
                </tr>  
		      </table>
		    </tr> 
	      </table>
        </tr>
     </td>
   </table> 
</div>



<!-- show new card form -->
<div id="newcard" {if $onfile && $VAR.new_card != 1 }{style_hide}{/if} >
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background" align="center">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="row2"> 
              <table width="100%" border="0" cellspacing="0" cellpadding="2" class="row1">
                <tr> 
                  <td valign="top" align="center" class="table_heading"> 
                    {if $VAR.msg != ""}
                    {$VAR.msg|upper}
                    {else}
                    {translate module=checkout}
                    enter_card 
                    {/translate}
                    {/if} </td>
                  </tr>
                </table> 
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%">
              <table width="100%" border="0" cellspacing="1" cellpadding="3" class="body">
                <tr> 
                  <td width="17%"> 
                    {translate module=checkout}
                    card_type 
                    {/translate}
                  </td>
                  <td width="17%">{ $list->card_type_menu($VAR.card_type, $VAR.option,'checkout_plugin_data[card_type]','form_menu') } </td>
                  <td width="16%">&nbsp; 
                    </td>
                  <td width="16%">&nbsp;</td>
                  <td width="17%"> 
                    {translate module=checkout}
                    card_no 
                    {/translate}
                  </td>
                  <td width="17%"> 
                    <input type="text" name="checkout_plugin_data[cc_no]"  value="{$VAR.cc_no}" size="20" maxlength="16">
                  </td>
                </tr>
                <tr> 
                  <td width="17%"> 
                    {translate module=checkout}
                    exp_month 
                    {/translate}
                  </td>
                  <td width="17%"><select name="checkout_plugin_data[exp_month]" >
                    <option value="01" {if $VAR.exp_month == "1"}selected{/if}>1 (Jan)</option>
                    <option value="02" {if $VAR.exp_month == "2"}selected{/if}>2 (Feb)</option>
                    <option value="03" {if $VAR.exp_month == "3"}selected{/if}>3 (Mar)</option>
                    <option value="04" {if $VAR.exp_month == "4"}selected{/if}>4 (Apr)</option>
                    <option value="05" {if $VAR.exp_month == "5"}selected{/if}>5 (May)</option>
                    <option value="06" {if $VAR.exp_month == "6"}selected{/if}>6 (Jun)</option>
                    <option value="07" {if $VAR.exp_month == "7"}selected{/if}>7 (Jul)</option>
                    <option value="08" {if $VAR.exp_month == "8"}selected{/if}>8 (Aug)</option>
                    <option value="09" {if $VAR.exp_month == "9"}selected{/if}>9 (Sep)</option>
                    <option value="10" {if $VAR.exp_month == "10"}selected{/if}>10 (Oct)</option>
                    <option value="11" {if $VAR.exp_month == "11"}selected{/if}>11 (Nov)</option>
                    <option value="12" {if $VAR.exp_month == "12"}selected{/if}>12 (Dec)</option>
                  </select></td>
                  <td>{translate module=checkout} exp_year {/translate} </td>
                  <td><select name="checkout_plugin_data[exp_year]" >
                    <option value="09" {if $VAR.exp_year == "9"}selected{/if}>2009</option>
                    <option value="10" {if $VAR.exp_year == "10"}selected{/if}>2010</option>
                    <option value="11" {if $VAR.exp_year == "11"}selected{/if}>2011</option>
                    <option value="12" {if $VAR.exp_year == "12"}selected{/if}>2012</option>
                    <option value="13" {if $VAR.exp_year == "13"}selected{/if}>2013</option>
                    <option value="14" {if $VAR.exp_year == "14"}selected{/if}>2014</option>
                    <option value="15" {if $VAR.exp_year == "15"}selected{/if}>2015</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2016</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2017</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2018</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2019</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2020</option>
                    <option value="15" {if $VAR.exp_year == "16"}selected{/if}>2021</option>
                  </select></td>
                  <td width="17%"><a href="javascript:NewWindow('ccv_help','','{$SSL_URL}?_page=checkout:ccv_help&_escape=1');"> 
                    </a><a href="javascript:NewWindow('ccv_help','','{$SSL_URL}?_page=checkout:ccv_help&_escape=1');">{translate module=checkout} ccv {/translate} </a></td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[ccv]" value="{$VAR.ccv}"  size="3" maxlength="3"> 
					</td>
                </tr>
                <tr>
                  <td width="17%">{translate module=account} field_first_name {/translate}{if $VAR.first_name_error}<font color="#FF0000">*</font>{/if}</td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[first_name]"  value="{$VAR.first_name}" size="12">                    </td>
                  <td width="16%">{translate module=account} field_last_name {/translate}{if $VAR.last_name_error}<font color="#FF0000">*</font>{/if}</td>
                  <td width="16%"><input type="text" name="checkout_plugin_data[last_name]"  value="{$VAR.last_name}" size="12">
                    </td>
                  <td width="17%">{translate module=account} field_company {/translate}</td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[company]"  value="{$VAR.company}" size="20"></td>
                  </tr>
                <tr>
                  <td width="17%">{translate module=account} field_address1 {/translate}{if $VAR.address1_error}<font color="#FF0000">*</font>{/if}</td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[address1]"  value="{$VAR.address1}" size="12"></td>
                  <td width="16%">{translate module=account} field_address2{/translate}</td>
                  <td width="16%"><input type="text" name="checkout_plugin_data[address2]"  value="{$VAR.address2}" size="12"></td>
                  <td width="17%">{translate module=account} field_city {/translate}</td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[city]"  value="{$VAR.city}" size="20"></td>
                  </tr>
                <tr>
                  <td width="17%">{translate module=account} field_state {/translate}{if $VAR.state_error}<font color="#FF0000">*</font>{/if}</td>
                  <td width="17%"><input type="text" name="checkout_plugin_data[state]"  value="{$VAR.state}" size="12"></td>
                  <td width="16%">{translate module=account} field_zip{/translate}{if $VAR.zip_error}<font color="#FF0000">*</font>{/if}</td>
                  <td width="16%"><input type="text" name="checkout_plugin_data[zip]"  value="{$VAR.zip}" size="12"></td>
                  <td width="17%">{translate module=account} field_country_id{/translate}</td>
                  <td width="17%">
                    {if $VAR.country_id != ""}
                    { $list->menu("no", "checkout_plugin_data[country_id]", "country", "name", $VAR.country_id, "") }
                    {else}
                    { $list->menu("no", "checkout_plugin_data[country_id]", "country", "name", $smarty.const.DEFAULT_COUNTRY, "") }
                    {/if} 
                  </tr>
                <tr>
                  <td width="17%">
				   <input type="button" id="submit_checkout_form" name="submit_checkout_form" value="{translate module=checkout}process_ord{/translate}" onClick="javascript:checkoutNow(1)">
				  </td>
                  <td width="17%"><input type="hidden" name="email" value="{$VAR.email}">                    
                    <input type="hidden" name="detailsnocopy" value="1"></td>
                  <td width="16%">&nbsp;</td>
                  <td width="16%">&nbsp;</td>
                  <td width="17%">&nbsp;</td>
                  <td width="17%" align="right">&nbsp;                    
				  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
 </div> 
</form>
<br>