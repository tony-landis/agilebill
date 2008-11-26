<!-- Load the JSCalender code -->
<link rel="stylesheet" type="text/css" media="all" href="includes/jscalendar/calendar-blue.css" title="win2k-1" />
<script type="text/javascript" src="includes/jscalendar/calendar_stripped.js"></script>
<script type="text/javascript" src="includes/jscalendar/lang/calendar-{$smarty.const.LANG}.js"></script>
<script type="text/javascript" src="includes/jscalendar/calendar-setup_stripped.js"></script>


{if $smarty.const.SESS_LOGGED != true }
	{ $block->display("account:login") }
{else}

{ $method->exe("account","view") } { if ($method->result == FALSE) } 
{ $block->display("core:method_error") } {else} 

<!-- Loop through each record -->
{foreach from=$account item=account}  

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form id="update_form" name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=account}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row4" valign="top"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    {$list->date_time($account.date_last)}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_username 
                    {/translate}
                  </td>
                  <td width="69%"> <b> 
                    {$account.username}
                    </b> 
                    <input type="hidden" name="account_username" value="{$account.username}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_update_password 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_password"  value="">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_confirm_password 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="confirm_password"  value="">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%" height="20"> 
                    {translate module=account}
                    field_email 
                    {/translate}
                  </td>
                  <td width="69%" height="20"> 
                    <input type="text" name="account_email"  value="{$account.email}">
                  </td>
                </tr>				
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_company 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_company"  value="{$account.company}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_first_name"  value="{$account.first_name}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_middle_name 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_middle_name"  value="{$account.middle_name}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_last_name"  value="{$account.last_name}">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_title 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    <select name="account_title" >
                      <option value="Mr"{if $account.title == "Mr"} selected{/if}> 
                      {translate module=account}
                      mr 
                      {/translate}
                      </option>
                      <option value="Ms"{if $account.title == "Ms"} selected{/if}> 
                      {translate module=account}
                      ms 
                      {/translate}
                      </option>					  
                      <option value="Mrs"{if $account.title == "Mrs"} selected{/if}> 
                      {translate module=account}
                      mrs 
                      {/translate}
                      </option>
                      <option value="Miss"{if $account.title == "Miss"} selected{/if}> 
                      {translate module=account}
                      miss 
                      {/translate}
                      </option>					  
                      <option value="Dr"{if $account.title == "Dr"} selected{/if}> 
                      {translate module=account}
                      dr 
                      {/translate}
                      </option>
                      <option value="Prof"{if $account.title == "Prof"} selected{/if}> 
                      {translate module=account}
                      prof 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>			
                <tr valign="top" class="row1"> 
                  <td width="43%"> 
                    {translate module=account}
                    field_address1 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_address1" value="{$account.address1}" {if $account_address1 == true}class="form_field_error"{/if}>
                  </td>
                </tr> 
                <tr valign="top" class="row1"> 
                  <td width="43%"> 
                    {translate module=account}
                    field_address2 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_address2" value="{$account.address2}" {if $account_address2 == true}class="form_field_error"{/if}>
                  </td>
                </tr> 
                <tr valign="top" class="row1"> 
                  <td width="43%"> 
                    {translate module=account}
                    field_city 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_city" value="{$account.city}" {if $account_city == true}class="form_field_error"{/if}>
                  </td>
                </tr> 
                <tr valign="top" class="row1"> 
                  <td width="43%"> 
                    {translate module=account}
                    field_state 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_state" value="{$account.state}" {if $account_state == true}class="form_field_error"{/if}>
                  </td>
                </tr> 
                <tr valign="top" class="row1"> 
                  <td width="43%"> 
                    {translate module=account}
                    field_zip 
                    {/translate}
                  </td>
                  <td width="57%"> 
                    <input type="text" name="account_zip" value="{$account.zip}" {if $account_zip == true}class="form_field_error"{/if}>
                  </td>
                </tr> 
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_country_id
                    {/translate}
                  </td>
                  <td width="69%">
                    { $list->menu("no", "cid", "country", "name", $account.country_id, "form_field\" onChange=\"taxIdsDisplay(this.value)") }
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
                <tr class="row1" valign="middle" align="left" id="tax_country_id_{$tax.country_id}" {if $account.country_id!=$tax.country_id}{style_hide}{/if}> 
                  <td width="31%"> 
                    {$tax.tax_id_name}
                  </td>
                  <td width="69%"> 
                    <input type="text" name="account_tax_id[{$tax.country_id}]" value="{$account.tax_id}" {if $account_tax_id == true}class="form_field_error"{/if}> 
					<!-- {if $tax.tax_id_exempt} 
                  	(or) exempt 
					<input name="account_tax_id_exempt[{$tax.country_id}]" type="checkbox" value="1" {if !$account.tax_id}checked{/if}>
					{/if} -->
				  </td>
                </tr> 
				{/foreach}
                {/if}
								
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_language_id
                    {/translate}
                  </td>
                  <td width="69%"> 
                    { $list->menu_files("", "lid", $account.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_currency_id 
                    {/translate}
                  </td>
                  <td width="69%"> 
                                       
					{$list->currency_list("cyid_arr")}
                    <select name="cyid">
                      {foreach key=key item=item from=$cyid_arr}
                      <option value="{$key}" {if $key == $account.currency_id}{assign var=currency_thumbnail value=$item.iso}selected{/if}> 
                      {$item.iso}
                      </option>
                      {/foreach} 
                    </select>
					
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_theme_id 
                    {/translate}
                  </td>
                  <td width="69%">   
                    { $list->menu_files("", "tid", $account.theme_id, "theme", "", ".user_theme", "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    field_email_html 
                    {/translate}
                  </td>
                  <td width="69%"> 
                    { $list->bool("account_email_type", $account.email_type, "form_menu") }
                  </td>
                </tr>
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
				
				{ if $issubaccount }
				{ $method->exe_noauth("account","get_auth_groups") } {if $groups}
                <tr valign="top"> 
                  <td width="43%">{translate} authorized_groups {/translate}</td>
                  <td width="57%"> 
				  	{foreach from=$groups item=group} 
						<input name="groups[{$group.id}]" type="checkbox" value="{$group.id}" {if $group.checked}checked{/if}>{$group.name}<br> 
					{/foreach} 
				  </td>
                </tr>
				{/if}
				{/if}
								
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"></td>
                  <td width="69%"> 
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
  <input type="hidden" name="account_id" value="{$account.id}">
  <input type="hidden" name="_page" value="account:view">
  <input type="hidden" name="_page_current" value="account:view">
  <input type="hidden" name="do[]" value="account:update">	
</form>
  
  {if $account.max_child > 0}
  	{ $block->display("account:sub_account") }  
  {else if $issubaccount == true}
	{ $block->display("account:sub_account_view") }
  {/if}
  
  {/foreach}    
{/if}
{/if}
