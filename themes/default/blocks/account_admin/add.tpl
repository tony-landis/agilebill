<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="account_admin_add" name="account_admin_add" method="post" action="">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=account_admin}
                title_add 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_username 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    <input type="text" name="account_admin_username"  value="{$VAR.account_admin_username}">
                    </b></td>
                  <td width="33%"> 
                    {translate module=account_admin}
                    blank_to_autogen 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"><b> 
                    {translate module=account_admin}
                    field_password 
                    {/translate}
                    </b> </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_password"  value="{$VAR.account_admin_password}">
                  </td>
                  <td width="33%"> 
                    {translate module=account_admin}
                    blank_to_autogen 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top">
                  <td width="33%"><b> 
                    {translate module=account_admin}
                    welcome_email 
                    {/translate}
                    </b></td>
                  <td width="33%">
                    <input type="checkbox" name="welcome_email" value="1" checked>
                  </td>
                  <td width="33%">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_status 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_email 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_company 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {if  $VAR.account_admin_status == "" }
                    {if $smarty.const.DEFAULT_ACCOUNT_STATUS != "1"}
                    { $list->bool("account_admin_status", "1", "form_menu") }
                    {else}
                    { $list->bool("account_admin_status", "0", "form_menu") }
                    {/if}
                    {else}
                    { $list->bool("account_admin_status", $VAR.account_admin_status, "form_menu") }
                    {/if}
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_email" value="{$VAR.account_admin_email}" {if $account_admin_email == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_company" value="{$VAR.account_admin_company}" {if $account_admin_company == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
						
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_first_name 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_middle_name 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_last_name 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    <input type="text" name="account_admin_first_name" value="{$VAR.account_admin_first_name}" {if $account_admin_first_name == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_middle_name" value="{$VAR.account_admin_middle_name}" {if $account_admin_middle_name == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_last_name" value="{$VAR.account_admin_last_name}" {if $account_admin_last_name == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_title 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_address1 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_address2 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    <select name="account_admin_title" >
                      <option value="Mr"{if $VAR.account_admin_title == "Mr"} selected{/if}> 
                      {translate module=account_admin}
                      mr 
                      {/translate}
                      </option>
                      <option value="Ms"{if $VAR.account_admin_title == "Ms"} selected{/if}> 
                      {translate module=account_admin}
                      ms
                      {/translate}
                      </option>					  
                      <option value="Mrs"{if $VAR.account_admin_title == "Mrs"} selected{/if}> 
                      {translate module=account_admin}
                      mrs 
                      {/translate}
                      </option>
                      <option value="Miss"{if $VAR.account_admin_title == "Miss"} selected{/if}> 
                      {translate module=account_admin}
                      miss 
                      {/translate}
                      </option>					  
                      <option value="Dr"{if $VAR.account_admin_title == "Dr"} selected{/if}> 
                      {translate module=account_admin}
                      dr 
                      {/translate}
                      </option>
                      <option value="Prof"{if $VAR.account_admin_title == "Prof"} selected{/if}> 
                      {translate module=account_admin}
                      prof 
                      {/translate}
                      </option>
                    </select>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_address1"  value="{$VAR.account_admin_address1}" {if $account_admin_address1 == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_address2"  value="{$VAR.account_admin_address2}" {if $account_admin_address2 == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_city 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_state 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_zip 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    <input type="text" name="account_admin_city"  value="{$VAR.account_admin_city}" {if $account_admin_city == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_state" value="{$VAR.account_admin_state}" {if $account_admin_state == true}class="form_field_error"{/if}>
                  </td>
                  <td width="33%"> 
                    <input type="text" name="account_admin_zip"  value="{$VAR.account_admin_zip}" {if $account_admin_zip == true}class="form_field_error"{/if}>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_country_id 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_date_expire 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_email_html 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {if $VAR.account_admin_country_id != ""}
                    { $list->menu("no", "account_admin_country_id", "country", "name", $VAR.account_admin_country_id, "form_field\" onChange=\"taxIdsDisplay(this.value)") }
                    {else}
                    { $list->menu("no", "account_admin_country_id", "country", "name", $smarty.const.DEFAULT_COUNTRY, "form_field\" onChange=\"taxIdsDisplay(this.value)") }
                    {/if}
                  </td>
                  <td width="33%"> 
                    { $list->calender_add("account_admin_date_expire", $VAR.account_admin_date_expire, "form_field") }
                  </td>
                  <td width="33%"> 
                    { $list->bool("account_admin_email_html", $VAR.account_email_type, "form_menu") }
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_language_id 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_theme_id 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_currency_id 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    {if $VAR.account_admin_language_id != ""}
                    { $list->menu_files("", "account_admin_language_id", $VAR.account_admin_language_id, "language", "", "_core.xml", "form_menu") }
                    {else}
                    { $list->menu_files("", "account_admin_language_id", $smarty.const.DEFAULT_LANGUAGE, "language", "", "_core.xml", "form_menu") }
                    {/if}
                  </td>
                  <td width="33%"> 
                    {if $VAR.account_admin_theme_id != ""}
                    { $list->menu_files("", "account_admin_theme_id", $VAR.account_admin_theme_id, "theme", "", ".user_theme", "form_menu") }
                    {else}
                    { $list->menu_files("", "account_admin_theme_id", $smarty.const.DEFAULT_THEME, "theme", "", ".user_theme", "form_menu") }
                    {/if}
                  </td>
                  <td width="33%"> 
                    {if $VAR.account_admin_currency_id != ""}
                    { $list->menu("no", "account_admin_currency_id", "currency", "name", $VAR.account_admin_currency_id, "form_menu") }
                    {else}
                    { $list->menu("no", "account_admin_currency_id", "currency", "name", $smarty.const.DEFAULT_CURRENCY, "form_menu") }
                    {/if}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row2"> 
                  <td width="33%"> <b> 
                    {translate}
                    authorized_groups 
                    {/translate}
                    </b></td>
                  <td width="33%"> <b> 
                    {if $list->is_installed('affiliate') }
                    {translate module=account_admin}
                    field_affiliate_id 
                    {/translate}
                    {/if}
                    </b></td>
                  <td width="33%"> <b> 
                    {translate module=account_admin}
                    field_misc 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="33%"> 
                    { $list->select_groups($VAR.groups,"groups","form_field","10","") }
                  </td>
                  <td width="33%"> 
                    {if $list->is_installed('affiliate') }
                    {html_select_affiliate name="account_admin_affiliate_id" default=$VAR.account_admin_affiliate_id}
                    <br>
                    <br>
					<b> 
                    {translate module=account_admin}
                    field_campaign_id 
                    {/translate}</b>
                    <br>
                    { $list->menu("no", "account_admin_campaign_id", "campaign", "name", $VAR.account_admin_campaign_id, "", all) }
                    {/if}
                     </td>
                  <td width="33%"> 
                    <textarea name="account_admin_misc" cols="25" rows="2" {if $account_admin_misc == true}class="form_field_error"{/if}>{$VAR.account_admin_misc}</textarea>
                  </td>
                </tr>
              </table>
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
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1"> 
				{foreach from=$tax_ids item=tax}  
                <tr valign="top" id="tax_country_id_{$tax.country_id}" {if $VAR.account_admin_country_id !=''}{if $VAR.account_admin_country_id!=$tax.country_id}{style_hide}{/if}{else}{if $smarty.const.DEFAULT_COUNTRY!=$tax.country_id}{style_hide}{/if}{/if}> 
                  <td width="33%"> 
                    {$tax.tax_id_name}
                  </td>
                  <td width="67%"> 
                    <input type="text" name="account_admin_tax_id[{$tax.country_id}]" {if $account_admin_tax_id == true}class="form_field_error"{/if}> 
					<!--  {if $tax.tax_id_exempt}
                  	(or) exempt 
					<input type="checkbox" name="account_tax_id_exempt[{$tax.country_id}]" value="1">
					{/if} -->
				  </td>
                </tr>
				{/foreach}
              </table>
            </td>
          </tr>
		  {/if}
		  
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                { $method->exe("account","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="33%"> 
                    {$record.name}
                  </td>
                  <td width="67%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
              </table>
            </td>
          </tr> 
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                { $method->exe("account","static_var")}
                { $block->display("core:method_error") }
                {foreach from=$static_var item=record}
                {/foreach}
                <tr valign="top"> 
                  <td width="33%">&nbsp;</td>
                  <td width="67%"> 
                    <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="account_admin:view">
                    <input type="hidden" name="_page_current" value="account_admin:add">
                    <input type="hidden" name="do[]" value="account_admin:add">
					{if $VAR.account_admin_account_id}
					<input type="hidden" name="account_admin_parent_id" value="{$VAR.account_admin_account_id}">
					{/if}
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
