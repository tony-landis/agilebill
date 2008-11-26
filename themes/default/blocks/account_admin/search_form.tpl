{ $method->exe("account_admin","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="search_form" method="post" action="">  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=account_admin}
                title_search 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_username 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_username" value="{$VAR.account_admin_username}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_email 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_email" value="{$VAR.account_admin_email}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_first_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_first_name" value="{$VAR.account_admin_first_name}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_middle_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_middle_name" value="{$VAR.account_admin_middle_name}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_last_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_last_name" value="{$VAR.account_admin_last_name}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_company 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_company" value="{$VAR.account_admin_company}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_address1 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_address1" value="{$VAR.account_admin_address1}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_address2 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_address2" value="{$VAR.account_admin_address2}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_city 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_city" value="{$VAR.account_admin_city}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_state 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_state" value="{$VAR.account_admin_state}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_zip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_zip" value="{$VAR.account_admin_zip}" >
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_misc 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="account_admin_misc" value="{$VAR.account_admin_misc}" {if $account_admin_misc == true}class="form_field_error"{/if}>
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("account_admin_date_orig", 
                    $VAR.account_admin_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("account_admin_date_expire", 
                    $VAR.account_admin_date_expire, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_affiliate_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_affiliate name="account_admin_affiliate_id" default=$VAR.account_admin_affiliate_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_campaign_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "account_admin_campaign_id", "campaign", "name", "all", "form_menu") }
                  </td>
                </tr>				
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_country_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "account_admin_country_id", 
                    "country", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "account_admin_language_id", 
                    "all", "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_currency_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "account_admin_currency_id", 
                    "currency", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_theme_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "account_admin_theme_id", 
                    "all", "theme", "", ".user_theme", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_status 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("account_admin_status", "all", 
                    "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    account_group
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "account_group", "group", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    field_title 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="account_admin_title" >
                      <option value=""></option>
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
                </tr>
				
                { $method->exe("account","static_var")} 
                {foreach from=$static_var item=record}
                <tr valign="top"> 
                  <td width="35%"> 
                    {$record.name}
                  </td>
                  <td width="65%"> 
                    {$record.html}
                  </td>
                </tr>
                {/foreach}
				
								
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$account_admin_limit}">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$account_admin item=record}
                      <option value="{$record.field}"> 
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp;</td>
                  <td width="65%">&nbsp;</td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp;</td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
                    <input type="hidden" name="_next_page_one" value="view">
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="module" value="account_admin">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp;</td>
                  <td width="65%">&nbsp;</td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</form>

<form name="search_form" method="post" action=""> 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading"><div align="center"> Custom Group Search </div></td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1"><table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top">
                <td width="35%"> {translate module=account_admin} field_date_orig {/translate} </td>
                <td width="65%">				
				<select name="dates[expr][]"> 
                  <option value="<"><</option> 
                  <option value=">">></option>  
                </select>
				{ $list->calender_add("dates[val][]", '', "") } <br>
				<select name="dates[expr][]"> 
                  <option value="<"><</option>
                  <option value=">">></option>  
                </select>				 { $list->calender_add("dates[val][]", '', "") } </td>
              </tr>
              <tr valign="top">
                <td width="35%"> {translate module=account_admin} account_group {/translate} </td>
                <td width="65%"> { $list->menu_multi('', "groups", "group", "name", "10", "all", "form_menu") } 
                </td>
              </tr>
              { $method->exe("account","static_var")} {foreach from=$static_var item=record}              {/foreach}              <tr valign="top">
                <td width="35%">&nbsp;</td>
                <td width="65%">&nbsp;</td>
              </tr>
              <tr valign="top">
                <td width="35%">&nbsp;</td>
                <td width="65%"><input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:blank"> 
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="do[]" value="account_admin:group_search">
                </td>
              </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>




<form name="search_form" method="post" action=""> 
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td><table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading"><div align="center"> Custom Product Search </div></td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1"><table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top">
                <td width="35%"> {translate module=account_admin} field_date_orig {/translate} </td>
                <td width="65%">				
				<select name="dates[expr][]"> 
                  <option value="<"><</option> 
                  <option value=">">></option>  
                </select>
				{ $list->calender_add("dates[val][]", '', "") } <br>
				<select name="dates[expr][]"> 
                  <option value="<"><</option>
                  <option value=">">></option>  
                </select>				 { $list->calender_add("dates[val][]", '', "") } </td>
              </tr>
              <tr valign="top">
                <td width="35%"> {translate module=account_admin} account_group {/translate} </td>
                <td width="65%"> { $list->menu_multi('', "products", "product", "sku", "10", "all", "form_menu") } 
                </td>
              </tr>
              { $method->exe("account","static_var")} {foreach from=$static_var item=record}              {/foreach}              <tr valign="top">
                <td width="35%">&nbsp;</td>
                <td width="65%">&nbsp;</td>
              </tr>
              <tr valign="top">
                <td width="35%">&nbsp;</td>
                <td width="65%"><input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:blank"> 
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="do[]" value="account_admin:product_search">
                </td>
              </tr>
          </table></td>
        </tr>
    </table></td>
  </tr>
</table>
</form>
<br> 

<form name="account_billing_search" method="post" action="">
  
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=account_billing}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=account_billing}
                            field_card_num4
                        {/translate}</td>
                    <td width="65%">
                     <input name="account_billing_card_num4" type="text" size="6" maxlength="4"> </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=account_billing}
                            field_checkout_plugin_id
                        {/translate}</td>
                    <td width="65%">
                        { $list->menu("no", "account_billing_checkout_plugin_id", "checkout", "name", "all", "form_menu") }                         </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=account_billing}
                            field_card_type
                        {/translate}</td>
                    <td width="65%">
                        <select name="account_billing_card_type" >
                          <option value="" {if $VAR.account_billing_card_type == ""}selected{/if}></option>
                          <option value="visa" {if $VAR.account_billing_card_type == "visa"}selected{/if}> {translate module=checkout} card_type_visa {/translate} </option>
                          <option value="mc" {if $VAR.account_billing_card_type == "mc"}selected{/if}> {translate module=checkout} card_type_mc {/translate} </option>
                          <option value="amex" {if $VAR.account_billing_card_type == "amex"}selected{/if}> {translate module=checkout} card_type_amex {/translate} </option>
                          <option value="discover" {if $VAR.account_billing_card_type == "discover"}selected{/if}> {translate module=checkout} card_type_discover {/translate} </option>
                          <option value="delta" {if $VAR.account_billing_card_type == "delta"}selected{/if}> {translate module=checkout} card_type_delta {/translate} </option>
                          <option value="solo" {if $VAR.account_billing_card_type == "solo"}selected{/if}> {translate module=checkout} card_type_solo {/translate} </option>
                          <option value="switch" {if $VAR.account_billing_card_type == "switch"}selected{/if}> {translate module=checkout} card_type_switch {/translate} </option>
                          <option value="jcb" {if $VAR.account_billing_card_type == "jcb"}selected{/if}> {translate module=checkout} card_type_jcb {/translate} </option>
                          <option value="diners" {if $VAR.account_billing_card_type == "diners"}selected{/if}> {translate module=checkout} card_type_diners {/translate} </option>
                          <option value="carteblanche" {if $VAR.account_billing_card_type == "carteblanche"}selected{/if}> {translate module=checkout} card_type_carteblanche {/translate} </option>
                          <option value="enroute" {if $VAR.account_billing_card_type == "enroute"}selected{/if}> {translate module=checkout} card_type_enroute {/translate} </option>
                        </select> 
                      &nbsp; </td>
                  </tr>
                           <!-- Define the results per page -->

                  <!-- Define the order by field per page -->

                  <tr class="row1" valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="core:search">
                      <input type="hidden" name="_escape" value="Y">
                      <input type="hidden" name="module" value="account_billing">
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
</p>
