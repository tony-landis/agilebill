{ $method->exe("setup","view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}

{literal}
    <!-- Define the update delete function -->
    <script language="JavaScript">
    <!-- START
        var module = 'setup';
    	var locations = '{/literal}{$VAR.module_id}{literal}';
    	if (locations != "")
    	{
    		refresh(0,'#'+locations)
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
{foreach from=$setup item=setup} <a name="{$setup.id}"></a>

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record -->
<form name="setup_update" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=setup}
                title_view
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_name"  value="{$setup.site_name}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_address 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_address"  value="{$setup.site_address}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_city 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_city"  value="{$setup.site_city}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_state 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_state"  value="{$setup.site_state}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_zip 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_zip"  value="{$setup.site_zip}" size="32">
                  </td>
                </tr>
                <tr class="row0" valign="top"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_phone 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_phone"  value="{$setup.site_phone}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_fax 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_fax"  value="{$setup.site_fax}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_site_email 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_site_email"  value="{$setup.site_email}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_country_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("no", "setup_country_id", "country", "name", $setup.country_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_currency_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "setup_currency_id", "currency", "name", $setup.currency_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "setup_language_id", $setup.language_id, "language", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_theme_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <p> 
                      { $list->menu_files("", "setup_theme_id", $setup.theme_id, "theme", "", ".user_theme", "form_menu") }
                    </p>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_admin_theme_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu_files("", "setup_admin_theme_id", $setup.admin_theme_id, "theme", "", ".admin_theme", "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_group_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "setup_group_id", "group", "name", $setup.group_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_setup_email_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "setup_setup_email_id", "setup_email", "name", $setup.setup_email_id, "form_menu") }
                  </td>
                </tr>
                {if $list->is_installed('affiliate')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_affiliate_template_id 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->menu("", "setup_affiliate_template_id", "affiliate_template", "name", $setup.setup_affiliate_template_id, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_auto_affiliate 
                    {/translate}
                  </td>
                  <td width="50%">
                    {$list->bool("setup_auto_affiliate", $setup.auto_affiliate, "form_menu")}
                  </td>
                </tr>				
                {/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_default_account_status 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_default_account_status", $setup.default_account_status, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_db_cache 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_db_cache", $setup.db_cache, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_cache_sessions 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_cache_sessions", $setup.cache_sessions, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_newsletter_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_newsletter_link", $setup.show_newsletter_link, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_newsletter_registration 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_newsletter_registration", $setup.newsletter_registration, "form_menu")}
                  </td>
                </tr>				
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_contact_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_contact_link", $setup.show_contact_link, "form_menu")}
                  </td>
                </tr>
                { if $list->is_installed('host_tld') }
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_domain_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_domain_link", $setup.show_domain_link, "form_menu")}
                  </td>
                </tr>
                {/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_cart_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_cart_link", $setup.show_cart_link, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_checkout_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_checkout_link", $setup.show_checkout_link, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_product_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_product_link", $setup.show_product_link, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_cat_block 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_cat_block", $setup.show_cat_block, "form_menu")}
                  </td>
                </tr>
				
				{if $list->is_installed('file')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_file_block 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_file_block", $setup.show_file_block, "form_menu")}
                  </td>
                </tr>
				{/if}
				
                {if $list->is_installed('static_page')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_static_block 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_static_block", $setup.show_static_block, "form_menu")}
                  </td>
                </tr>
                {/if}
				
                {if $list->is_installed('affiliate')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_affiliate_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_affiliate_link", $setup.show_affiliate_link, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_affiliate_code 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_affiliate_code", $setup.show_affiliate_code, "form_menu")}
                  </td>
                </tr>
                {/if}
                {if $list->is_installed('ticket')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_ticket_link 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_ticket_link", $setup.show_ticket_link, "form_menu")}
                  </td>
                </tr>
                {/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_show_discount_code 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_show_discount_code", $setup.show_discount_code, "form_menu")}
                  </td>
                </tr>
                {if $list->is_installed('weblog')}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_weblog 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_weblog", $setup.weblog, "form_menu")}
                  </td>
                </tr>
                {/if}
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_os 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="setup_os" >
                      <option value="0" {if $setup.os == 0}selected{/if}> 
                      Linux </option>
                      <option value="1" {if $setup.os == 1}selected{/if}> 
                      Windows </option>
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_path_curl 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_path_curl"  value="{$setup.path_curl}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_nonssl_url 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_nonssl_url"  value="{$setup.nonssl_url}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_ssl_url 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_ssl_url"  value="{$setup.ssl_url}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_login_expire 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_login_expire"  value="{$setup.login_expire}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_cookie_name 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_cookie_name"  value="{$setup.cookie_name}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_cookie_expire 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_cookie_expire"  value="{$setup.cookie_expire}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_error_reporting 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <select name="setup_error_reporting" >
                      <option value="0"> 
                      {translate module=setup}
                      e_none 
                      {/translate}
                      </option>
                      <option value="1" {if $setup.error_reporting == '1'}selected{/if}> 
                      {translate module=setup}
                      e_error 
                      {/translate}
                      </option>
                      <option value="2"{if $setup.error_reporting == '2'}selected{/if}> 
                      {translate module=setup}
                      e_warning 
                      {/translate}
                      </option>
                      <option value="4"{if $setup.error_reporting == '4'}selected{/if}> 
                      {translate module=setup}
                      e_parse 
                      {/translate}
                      </option>
                      <option value="8"{if $setup.error_reporting == '8'}selected{/if}> 
                      {translate module=setup}
                      e_notice 
                      {/translate}
                      </option>
                      <option value="16"{if $setup.error_reporting == '16'}selected{/if}> 
                      {translate module=setup}
                      e_core_error 
                      {/translate}
                      </option>
                      <option value="32"{if $setup.error_reporting == '32'}selected{/if}> 
                      {translate module=setup}
                      e_core_warning 
                      {/translate}
                      </option>
                      <option value="64"{if $setup.error_reporting == '64'}selected{/if}> 
                      {translate module=setup}
                      e_compile_error 
                      {/translate}
                      </option>
                      <option value="128"{if $setup.error_reporting == '128'}selected{/if}> 
                      {translate module=setup}
                      e_compile_warning 
                      {/translate}
                      </option>
                      <option value="256"{if $setup.error_reporting == '256'}selected{/if}> 
                      {translate module=setup}
                      e_user_error 
                      {/translate}
                      </option>
                      <option value="512"{if $setup.error_reporting == '512'}selected{/if}> 
                      {translate module=setup}
                      e_user_warning 
                      {/translate}
                      </option>
                      <option value="1024"{if $setup.error_reporting == '1024'}selected{/if}> 
                      {translate module=setup}
                      e_user_notice 
                      {/translate}
                      </option>
                      <option value="E_ALL"{if $setup.error_reporting == 'E_ALL'}selected{/if}> 
                      {translate module=setup}
                      e_all 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_debug 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    {$list->bool("setup_debug", $setup.debug, "form_menu")}
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_search_expire 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_search_expire"  value="{$setup.search_expire}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_decimal_place 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_decimal_place"  value="{$setup.decimal_place}" size="32">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%" valign="top"> 
                    {translate module=setup}
                    field_time_format 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <p> 
                      <input type="text" name="setup_time_format"  value="{$setup.time_format}" size="32">
                    </p>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%" valign="top"> 
                    {translate module=setup}
                    field_date_format 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    { $list->setup_default_date($setup.date_format, "form_menu") }
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_login_attempt_try 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_login_attempt_try"  value="{$setup.login_attempt_try}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_login_attempt_time 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_login_attempt_time"  value="{$setup.login_attempt_time}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_login_attempt_lock 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_login_attempt_lock"  value="{$setup.login_attempt_lock}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_billing_weekday 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_billing_weekday"  value="{$setup.billing_weekday}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_grace_period 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_grace_period"  value="{$setup.grace_period}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_max_billing_notice 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_max_billing_notice"  value="{$setup.max_billing_notice}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_max_inv_gen_period 
                    {/translate}
                  </td>
                  <td width="50%"> 
                    <input type="text" name="setup_max_inv_gen_period"  value="{$setup.max_inv_gen_period}" size="4">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%"> 
                    {translate module=setup}
                    field_license_key 
                    {/translate}
                  </td>
                  <td width="50%">
                    <input type="text" name="setup_license_key"  value="{$setup.license_key}" size="40">
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="50%" valign="top"> 
                    {translate module=setup}
                    field_license_code 
                    {/translate}
                  </td>
                  <td width="50%">
                    <textarea name="setup_license_code"  cols="65" rows="10" wrap="Yes" >{$setup.license_code}</textarea>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left">
                  <td width="50%"></td>
                  <td width="50%">
                    <table width="100%" border="0" cellspacing="0" cellpadding="0">
                      <tr> 
                        <td> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                        <td align="right"> 
                          <input type="button" name="delete" value="{translate}delete{/translate}" class="form_button" onClick="delete_record('{$setup.id}','{$VAR.id}');">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="setup:view">
    <input type="hidden" name="setup_id" value="{$setup.id}">
    <input type="hidden" name="do[]" value="setup:update">
    <input type="hidden" name="id" value="{$VAR.id}">
  </form>
  {/foreach}    
{/if}
