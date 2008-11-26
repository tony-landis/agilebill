<!-- Make sure user is logged out -->
{if $smarty.const.SESS_LOGGED}
{literal}<script language="javascript">document.location='?_page=account:account';</script>{/literal}
{else}
 
<!-- Load the JSCalender code -->
<link rel="stylesheet" type="text/css" media="all" href="includes/jscalendar/calendar-blue.css" title="win2k-1" />
<script type="text/javascript" src="includes/jscalendar/calendar_stripped.js"></script>
<script type="text/javascript" src="includes/jscalendar/lang/calendar-{$smarty.const.LANG}.js"></script>
<script type="text/javascript" src="includes/jscalendar/calendar-setup_stripped.js"></script>

<!-- Display the form validation -->
{if $form_validation}
{ $block->display("core:alert_fields") }
{/if}
<!-- Display the form to collect the input values -->
<form id="account_add" name="account_add" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=account}
                title_add
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" >
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1" >
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_username 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_username" value="{$VAR.account_username}" {if $account_username == true}class="form_field_error"{else}{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_password 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="password" name="account_password" {if $account_password == true}class="form_field_error"{/if} value="{$confirm_account_password}">
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_confirm_password 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="password" name="confirm_password" {if $account_password == true}class="form_field_error"{/if} value="{$confirm_account_password}">
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_email 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_email" value="{$VAR.account_email}" {if $account_email == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_company 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_company" value="{$VAR.account_company}" {if $account_company == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_first_name" value="{$VAR.account_first_name}" {if $account_first_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_middle_name 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_middle_name" value="{$VAR.account_middle_name}" {if $account_middle_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_last_name" value="{$VAR.account_last_name}" {if $account_last_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_title 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <select name="account_title">
                      <option value="Mr"{if $VAR.account_title == "Mr"} selected{/if}> 
                      {translate module=account}
                      mr 
                      {/translate}
                      </option>
                      <option value="Ms"{if $VAR.account_title == "Ms"} selected{/if}> 
                      {translate module=account}
                      ms 
                      {/translate}
                      </option>								  
                      <option value="Mrs"{if $VAR.account_title == "Mrs"} selected{/if}> 
                      {translate module=account}
                      mrs 
                      {/translate}
                      </option>
                      <option value="Miss"{if $VAR.account_title == "Miss"} selected{/if}> 
                      {translate module=account}
                      miss 
                      {/translate}
                      </option>					  
                      <option value="Dr"{if $VAR.account_title == "Dr"} selected{/if}> 
                      {translate module=account}
                      dr 
                      {/translate}
                      </option>
                      <option value="Prof"{if $VAR.account_title == "Prof"} selected{/if}> 
                      {translate module=account}
                      prof 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
				
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_address1 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_address1" value="{$VAR.account_address1}" {if $account_address1 == true}class="form_field_error"{/if}>
                  </td>
                </tr>
				
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_address2 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_address2" value="{$VAR.account_address2}" {if $account_address2 == true}class="form_field_error"{/if}>
                  </td>
                </tr>
				
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_city 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_city" value="{$VAR.account_city}" {if $account_city == true}class="form_field_error"{/if}>
                  </td>
                </tr>
				
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_state 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_state" value="{$VAR.account_state}" {if $account_state == true}class="form_field_error"{/if}>
                  </td>
                </tr>
				
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_zip 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_zip" value="{$VAR.account_zip}" {if $account_zip == true}class="form_field_error"{/if}>
                  </td>
                </tr>
																								
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    field_country_id 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    {if $VAR.account_country_id != ""}
                    { $list->menu("no", "account_country_id", "country", "name", $VAR.account_country_id, "form_field\" onChange=\"taxIdsDisplay(this.value)") }
                    {else}
                    { $list->menu("no", "account_country_id", "country", "name", $smarty.const.DEFAULT_COUNTRY, "form_field\" onChange=\"taxIdsDisplay(this.value)") }
                    {/if}
                  </td>
                </tr>
				
				{ $method->exe_noauth("tax","get_tax_ids")} 
				{if $tax_ids}
				<script language="javascript">
				{if $VAR.account_country_id != ""}
				var countryId='{$VAR.account_country_id}';  
				{else}
				var countryId='{$smarty.const.DEFAULT_COUNTRY}'; 
				{/if} 
				{literal}
				function taxIdsDisplay(id) {    
					try{ document.getElementById('tax_country_id_'+id).style.display='block'; } catch(e) {} 
					try{ document.getElementById('tax_country_id_'+countryId).style.display='none'; } catch(e) {}
					countryId=id;
				}
				{/literal}
				</script> 
				{foreach from=$tax_ids item=tax}  
                <tr valign="top" id="tax_country_id_{$tax.country_id}" {if $VAR.account_country_id !=''}{if $VAR.account_country_id!=$tax.country_id}{style_hide}{/if}{else}{if $smarty.const.DEFAULT_COUNTRY!=$tax.country_id}{style_hide}{/if}{/if}> 
                  <td width="29%"> 
                    {$tax.tax_id_name}
                  </td>
                  <td width="71%"> 
                    <input type="text" name="account_tax_id[{$tax.country_id}]" value="{$VAR.account_tax_id}" {if $account_tax_id == true}class="form_field_error"{/if}> 
					<!--  {if $tax.tax_id_exempt}
                  	(or) exempt 
					<input type="checkbox" name="account_tax_id_exempt[{$tax.country_id}]" value="1"> -->
					{/if}
				  </td>
                </tr> 
				{/foreach}
                {/if}
				 
			    { $method->exe("account","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="29%"> 
                    {$record.name}
                  </td>
                  <td width="71%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
				
                { if $smarty.const.NEWSLETTER_REGISTRATION == "1"}
                <tr valign="top" > 
                  <td width="43%"> 
                    {translate module=account}
                    subscribe_newsletters 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    { $method->exe("newsletter", "check_list_registration") } 
                  </td>
                  {/if}
                </tr>
                <tr valign="top"  > 
                  <td width="43%"> 
                    {translate module=account}
                    field_email_html 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    { $list->bool("account_email_type", $VAR.account_email_type, "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="43%">&nbsp;</td>
                  <td width="57%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    
					<input type="hidden" name="_page" value="{$VAR._page}">
					
					{if $VAR._page == ""}
                    <input type="hidden" name="_page_current" value="account:account">
					{else}
					<input type="hidden" name="_page_current" value="{$VAR._page}">
					{/if}
					
                    <input type="hidden" name="do[]" value="account:add">
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
{/if}
