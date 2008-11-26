<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}


{$method->exe("service","add_tpl")}
{if ($method->result == FALSE)}
    {$block->display("core:method_error")}
{else}

{literal}
<script language="javascript">
 
	function hideServiceType(type) {
		document.getElementById('service_'+type+'Tag').style.color='999999'; 	
		document.getElementById('service_'+type).checked=false; 
		document.getElementById('service_'+type).disabled=true;  	
	}
	
	function unhideServiceType(type) {
		document.getElementById('service_'+type+'Tag').style.color='000000';  
		document.getElementById('service_'+type).disabled=false;  
	}
	
	function updateTR() {
		var a = new Array(5);
		a[0] = 'none';
		a[1] = 'group';
		a[2] = 'product';
		a[3] = 'hosting';
		a[4] = 'domain';
		for(i=0;i<5;i++) {
			if(document.getElementById('service_'+a[i]).checked == true) {
				document.getElementById(a[i]).style.display = 'block';
			} else {
				document.getElementById(a[i]).style.display = 'none';
			}
		}
		
		// display recurring? 
		if( document.getElementById('billing_type1').checked == true) {
			document.getElementById("recurring").style.display = 'block';
			document.getElementById('vrecurring').value = '1';
		} else {
			document.getElementById("recurring").style.display = 'none';
			document.getElementById('vrecurring').value = '0';
		}			
	}
	
	function ServiceType(type) 
	{  
		var service_type = 'service_'+type;  
		if(type == 'none') { 
			if(document.getElementById(service_type).checked == true) {
				unhideServiceType(type);
				hideServiceType('group'); 
				hideServiceType('product'); 
				hideServiceType('hosting'); 
				hideServiceType('domain');
				document.getElementById('billing_type1').checked = true;
				document.getElementById('vnone').value='1';
			} else { 
				unhideServiceType('group'); 
				unhideServiceType('product'); 
				unhideServiceType('hosting'); 
				unhideServiceType('domain'); 
				document.getElementById('vnone').value='0';
			} 
		} else if (type == 'group') {
			if(document.getElementById(service_type).checked == true) {
				unhideServiceType(type);
				hideServiceType('none');  
				hideServiceType('domain');
				document.getElementById('vgroup').value='1';
			} else { 
				if(document.getElementById('service_product').checked == false 
					&& document.getElementById('service_hosting').checked == false) {
					unhideServiceType('none'); 
					unhideServiceType('product'); 
					unhideServiceType('hosting'); 
					unhideServiceType('domain'); 
					document.getElementById('vgroup').value='0';
				}
			} 
		} else if (type == 'product') {
			if(document.getElementById(service_type).checked == true) {
				unhideServiceType(type);
				hideServiceType('none');  
				hideServiceType('hosting');
				hideServiceType('domain');
				document.getElementById('vproduct').value='1';
			} else { 
				if(document.getElementById('service_group').checked == true) {
					unhideServiceType('hosting'); 
				} else {
					unhideServiceType('none'); 
					unhideServiceType('group'); 
					unhideServiceType('hosting'); 
					unhideServiceType('domain'); 
				}
				document.getElementById('vproduct').value='0';
			} 
		} else if (type == 'hosting') {
			if(document.getElementById(service_type).checked == true) {
				unhideServiceType(type);
				hideServiceType('none');  
				hideServiceType('product');
				hideServiceType('domain');
				document.getElementById('vhosting').value='1';
			} else { 
				if(document.getElementById('service_group').checked == true) {
					unhideServiceType('product'); 
				} else {
					unhideServiceType('none'); 
					unhideServiceType('group'); 
					unhideServiceType('product'); 
					unhideServiceType('domain'); 
				}
				document.getElementById('vhosting').value='0';
			} 
		} else if (type == 'domain') {
			if(document.getElementById(service_type).checked == true) {
				unhideServiceType(type);
				hideServiceType('group'); 
				hideServiceType('product'); 
				hideServiceType('hosting'); 
				hideServiceType('none');
				document.getElementById('billing_type').style.color='999999';
				document.getElementById('billing_type0').checked=true;
				document.getElementById('billing_type0').disabled=true;
				document.getElementById('billing_type1').checked=false;
				document.getElementById('billing_type1').disabled=true;
				document.getElementById('vdomain').value='0';
			} else { 
				unhideServiceType('group'); 
				unhideServiceType('product'); 
				unhideServiceType('hosting'); 
				unhideServiceType('none'); 
				document.getElementById('billing_type').style.color='000000'; 
				document.getElementById('billing_type0').disabled=false; 
				document.getElementById('billing_type1').disabled=false;
				document.getElementById('vdomain').value='0';			
			} 
		} 
		updateTR();
	} 
	
	function clearall()
	{
		document.getElementById('do').value='service:add_tpl';
		document.getElementById('clearall').value='1'; 
		document.getElementById('vgroup').value='0'; 
		document.getElementById('vproduct').value='0'; 
		document.getElementById('vhosting').value='0'; 
		document.getElementById('vdomain').value='0'; 
		document.forms.service_add.submit();
	} 
	
	function domainUpdate(domain,tld,type)
	{
		document.forms.service_add.domain_name.value   = domain;
		document.forms.service_add.domain_tld.value    = tld;
		document.forms.service_add.domain_option.value = type;
	} 		
	
</script>
{/literal}
<form id="service_add" name="service_add" method="post" action=""> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td class="table_heading"> 
              <center>
                <input type="hidden" name="_page" value="invoice:add">
                <input type="hidden" name="_page_current" value="invoice:add">
                {translate module=service}
                title_add 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td class="row1"> 
              <table width="100%" border="0" cellspacing="5" cellpadding="1" class="body">
                <tr> 
                  <td><b> 
                    {translate module=service}
                    field_account_id 
                    {/translate}
                    </b> </td>
                </tr>
                <tr> 
                  <td> 
                    {html_select_account name="service_account_id" default=$VAR.service_account_id}
                  </td>
                </tr>
                <tr> 
                  <td><b> 
                    {translate module=service}
                    field_product_id 
                    {/translate}
                    </b></td>
                </tr>
                <tr> 
                  <td> 
                    <select name="product_id">
                      {foreach from=$prod_menu item=p}
                      <option value={$p.id}{if $VAR.product_id == $p.id} selected{/if}> 
                      {$p.sku}
                      </option>
                      {/foreach}
                    </select>
                    <a href="javascript:document.getElementById('changeproduct').value='1'; javascript:document.getElementById('do').value='service:add_tpl'; javascript:document.getElementById('clearall').value=0; document.forms.service_add.submit()">Select 
                    Product</a></td>
                </tr>
                <tr> 
                  <td><b>
                    {translate module=service}
                    field_sku 
                    {/translate}
                    </b></td>
                </tr>
                <tr>
                  <td> 
				  	{if $product.sku != ""}
                    <input type="text" name="service_sku" value="{$product.sku}" maxlength="32">
					{else}
					<input type="text" name="service_sku" value="{$VAR.service_sku}" maxlength="32">
					{/if} 
                    <a href="javascript:clearall()">Clear &amp; Configure Manually</a> 
                  </td>
                </tr>
                <tr> 
                  <td><b> 
                    {translate module=service}
                    field_type 
                    {/translate}
                    </b></td>
                </tr>
                <tr> 
                  <td> <service_none id="service_noneTag"> 
                    <input type="checkbox" id="service_none" name="service_none" value="none" onChange="ServiceType(this.value);">
                    None </service_none> <service_group id="service_groupTag"> 
                    <input type="checkbox" id="service_group" name="service_group" value="group" onChange="ServiceType(this.value);">
                    Group Access </service_group> <service_product id="service_productTag"> 
                    <input type="checkbox" id="service_product" name="service_product" value="product" onChange="ServiceType(this.value);">
                    Product Plugin </service_product> <service_hosting id="service_hostingTag"> 
                    <input type="checkbox" id="service_hosting" name="service_hosting" value="hosting" onChange="ServiceType(this.value);">
                    Hosting <service_domain id="service_domainTag"> 
					<input type="hidden" id="service_domain" name="service_domain" value="domain">
                    <!-- not ready for production
					<input type="checkbox" id="service_domain" name="service_domain" value="domain"	onChange="ServiceType(this.value);">
                    Domain </service_domain> 
					-->
					</td>
                </tr>
                <tr> 
                  <td><b>
                    {translate module=service}
                    field_price_type 
                    {/translate}
                    </b></td>
                </tr>
                <tr> 
                  <td> 
                    <div id="billing_type"> 
                      <input type="radio" id="billing_type0" name="billing_type" value="0" onChange="updateTR()" checked>
                      {translate module=product}
                      price_type_one 
                      {/translate}
                      <input type="radio" id="billing_type1" name="billing_type" value="1" onChange="updateTR()" {if $product.price_type == 1}checked{/if}>
                      {translate module=product}
                      price_type_recurr 
                      {/translate}
                    </div>
                  </td>
                </tr>
                <tr> 
                  <td valign="middle" align="center"> <b><a href="javascript:document.getElementById('do').value='service:add';  javascript:document.getElementById('page').value='service:add'; document.forms.service_add.submit();"> 
                    {translate}
                    submit 
                    {/translate}
                    </a></b> 
                    <input type="hidden" id="do" name="do[]" value="service:add_tpl">
                    <input type="hidden" id="page" name="_page" value="service:add">
                    <input type="hidden" name="_page_current" value="service:add">
                    <input type="hidden" id="changeproduct" name="changeproduct" value="0">
                    <input type="hidden" id="clearall" name="clearall" value="0">
                    <input type="hidden" id="vnone" name="vnone" value="0">
                    <input type="hidden" id="vhosting" name="vhosting" value="0">
                    <input type="hidden" id="vproduct" name="vproduct" value="0">
                    <input type="hidden" id="vgroup" name="vgroup" value="0">
                    <input type="hidden" id="vdomain" name="vdomain" value="0">
                    <input type="hidden" id="vrecurring" name="vrecurring" value="0">
                    <input type="hidden" id="domain_name" name="domain_name" value="0">
                    <input type="hidden" id="domain_tld" name="domain_tld" value="0">
                    <input type="hidden" id="domain_option" name="domain_option" value="0">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table> 
<br>
  
<div id="recurring" {style_hide}>  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <center>
                {translate module=service}
                title_recurring 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
              <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                  <tr valign="top" class="row1"> 
                    <td width="49%"><b> 
                      {translate module=product}
                      field_price_base 
                      {/translate}
                      </b></td>
                    <td width="51%"> 
                      <input type="text" name="product_price_base" value="{$product.price_base}"  size="5">
                      {$list->currency_iso("")}
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="49%"><b> 
                      {translate module=service}
                      field_date_last_invoice 
                      {/translate}
                      </b></td>
                    <td width="51%"> 
                      { $list->calender_view("date_last_invoice", 0, "form_field", $service.id) }
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="49%"><b> 
                      {translate module=product}
                      field_price_recurr_default 
                      {/translate}
                      </b></td>
                    <td width="51%"> 
                      <select name="product_price_recurr_default" >
                        <option value="0" {if $product.price_recurr_default == "0"} selected{/if}> 
                        {translate module=product}
                        recurr_week 
                        {/translate}
                        </option>
                        <option value="1" {if $product.price_recurr_default == "1"} selected{/if}> 
                        {translate module=product}
                        recurr_month 
                        {/translate}
                        </option>
                        <option value="2" {if $product.price_recurr_default == "2"} selected{/if}> 
                        {translate module=product}
                        recurr_quarter 
                        {/translate}
                        </option>
                        <option value="3" {if $product.price_recurr_default == "3"} selected{/if}> 
                        {translate module=product}
                        recurr_semianual 
                        {/translate}
                        </option>
                        <option value="4" {if $product.price_recurr_default == "4"} selected{/if}> 
                        {translate module=product}
                        recurr_anual 
                        {/translate}
                        </option>
                        <option value="5" {if $product.price_recurr_default == "5"} selected{/if}> 
                        {translate module=product}
                        recurr_twoyear 
                        {/translate}
                        </option>
                        <option value="6" {if $product.price_recurr_default == "6"} selected{/if}> 
                        {translate module=product}
                        recurr_threeyear 
                        {/translate}
                        </option>
                      </select>
                    </td>
                  </tr>				  
                  <tr valign="top" class="row1"> 
                    <td width="49%"><b>
                      {translate module=product}
                      field_taxable 
                      {/translate}
                      </b></td>
                    <td width="51%">
                      { $list->bool("product_taxable", $product.taxable, "form_menu") }
                    </td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="49%"><b> 
                      {translate module=product}
                      field_price_recurr_type 
                      {/translate}
                      </b></td>
                    <td width="51%"><b> 
                      {translate module=product}
                      user_options 
                      {/translate}
                      </b></td>
                  </tr>
                  <tr valign="top" class="row1"> 
                    <td width="49%"> 
                      <input type="radio" id="recurr_type0" name="product_price_recurr_type" value="0" {if $product.price_recurr_type == "0" || $product.price_recurr_type == ""}checked{/if} onClick="document.getElementById('billing_weekday').style.display = 'none'">
                      {translate module=product}
                      recurr_type_aniv 
                      {/translate}
                      <br>
                      <input type="radio" id="recurr_type1" name="product_price_recurr_type" value="1" {if $product.price_recurr_type == "1"}checked{/if} onClick="document.getElementById('billing_weekday').style.display='block'">
                      {translate module=product}
                      recurr_type_fixed 
                      {/translate}
                      <br>
					  <div id="billing_weekday" style="dispay:none">
                      {translate module=product}
                      field_price_recurr_weekday 
                      {/translate}
                      <input type="text" name="product_price_recurr_weekday" value="{if $product.price_recurr_weekday != ""}{$product.price_recurr_weekday}{else}{$smarty.const.BILLING_WEEKDAY}{/if}"  size="2" maxlength="2">
                      (1-28) 
					  </div>
					  <script language=javascript>
					  if(document.getElementById('recurr_type0').checked == true) document.getElementById('billing_weekday').style.display='none';
					  </script>
					</td>
                    <td width="51%"> 
                      { $list->bool("product_price_recurr_schedule", $product.price_recurr_schedule, "form_menu") }
                      {translate module=product}
                      field_price_recurr_schedule 
                      {/translate}
                      <br>
                      { $list->bool("product_price_recurr_cancel", $product.price_recurr_cancel, "form_menu") }
                      {translate module=product}
                      field_price_recurr_cancel 
                      {/translate}
                      <br>
                      { $list->bool("product_price_recurr_modify", $product.price_recurr_modify, "form_menu") }
                      {translate module=product}
                      field_price_recurr_modify 
                      {/translate}
                    </td>
                  </tr>
                  <tr valign="top" class="row1">
                    <td width="49%"><b> 
                      {translate module=service}
                      field_account_billing_id 
                      {/translate}
                      </b> </td>
                    <td width="51%"> 
                      { $list->menu_cc_admin("account_billing_id", $VAR.service_account_id, $VAR.ccnum, "form_menu") }
                    </td>
                  </tr>
                </table> 
              </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
 <br>
</div>  
  
  <div id="group" {style_hide}>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=service}
                  title_group 
                  {/translate}
                </center>
              </td>
            </tr>
            <tr valign="top"> 
              <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="2" cellpadding="3" class="row1">
                  <tr> 
                    <td width="98%" valign="top"> <b> </b> 
                      <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                        <tr> 
                          <td> 
                            <p> 
                              <input type="radio" name="product_assoc_grant_group_type" value="0" {if $product.assoc_grant_group_type == "0" || $product.assoc_grant_group_type == ""}checked{/if}>
                              {translate module=product}
                              assoc_group_limited 
                              {/translate}
                              <input type="text" name="product_assoc_grant_group_days" value="{$product.assoc_grant_group_days}"  size="3">
                              <br>
                              <input type="radio" name="product_assoc_grant_group_type" value="1" {if $product.assoc_grant_group_type == "1"}checked{/if}>
                              {translate module=product}
                              assoc_group_subscription 
                              {/translate}
                              <br>
                              <input type="radio" name="product_assoc_grant_group_type" value="2" {if $product.assoc_grant_group_type == "2"}checked{/if}>
                              {translate module=product}
                              assoc_group_forever 
                              {/translate}
                            </p>
                          </td>
                        </tr>
                      </table>
                    </td>
                    <td width="2%" align="left" valign="top">
                      { $list->menu_multi($product.assoc_grant_group, "product_assoc_grant_group", "group", "name", "10", "", "form_menu") }
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table> 
	<br>
  </div>
  
  
  <div id="hosting" {style_hide}>
  {if $list->is_installed('host_server')}
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=service}
                  title_hosting 
                  {/translate}
                </center>
              </td>
            </tr>
            <tr valign="top"> 
              <td width="65%" class="row1" height="55"> 
                <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                  <tr> 
                    <td width="50%"> 
                      {translate module=product}
                      field_host_server_id 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      { $list->menu("no", "product_host_server_id", "host_server", "name", $product.host_server_id, "\" onchange=\"submit();") }
                      <a href="javascript:document.forms.service_add.submit()">Configure</a> 
                    </td>
                  </tr>
                  <tr> 
                    <td width="50%"> 
                      {translate module=service}
                      field_domain_name 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="host_domain_name" value="{$VAR.host_domain_name}">
                      . 
                      <input type="text" name="host_domain_tld" size="5" value="{$VAR.host_domain_tld}">
                    </td>
                  </tr>
                  <tr> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_ip 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="host_ip" value="{$VAR.host_ip}">
                    </td>
                  </tr>
                  <tr> 
                    <td width="50%"> 
                      {translate module=service}
                      field_host_username 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="host_username" value="{$VAR.host_username}">
                    </td>
                  </tr>
                  <tr>
                    <td width="50%"> 
                      {translate module=service}
                      field_host_password 
                      {/translate}
                    </td>
                    <td width="50%"> 
                      <input type="text" name="host_password" value="{$VAR.host_password}">
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
                  { if ($list->smarty_array("host_server","provision_plugin", "", "plugin")) }
                  {foreach from=$plugin item=arr}
                  {if $product.host_server_id == $arr.id}
                  <tr valign="top"> 
                    
              <td width="65%" class="row1"> 
                {assign var="afile" 	value=$arr.provision_plugin}
                {assign var="ablock" 	value="host_provision_plugin:plugin_prod_"}
                {assign var="blockfile" value="$ablock$afile"}
                { $block->display($blockfile) }
                <iframe name="iframeDomainDetails" id="iframeDomainDetails" style="border:0px; width:100%; height:0px;" scrolling="no" width="100%" allowtransparency="false" frameborder="0" class="body" src="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"></iframe> 
              </td>
                  </tr>
                  {/if}
                  {/foreach}
                  {/if}			
          </table>
        </td>
      </tr>
    </table>
	<br>
	{/if}
    </div>
  
  
  
  
  
  <div id="product" {style_hide}>
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=service}
                  title_product 
                  {/translate}
                </center>
              </td>
            </tr>
            <tr valign="top"> 
              <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                  <tr> 
                    <td width="50%"> Plugin to Enable</td>
                    <td width="50%"> 
                      { $list->menu_files("", "product_prod_plugin_file", $product.prod_plugin_file, "product", "", ".php", "\" onchange=\"document.product_view.submit();") }
                      <a href="javascript:document.forms.service_add.submit()">Configure</a> 
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
			  { if $product.prod_plugin_file != ""} 
			  <tr valign="top"> 
				<td width="65%" class="row1"> 
				  {assign var="afile" 	value=$product.prod_plugin_file}
				  {assign var="ablock" 	value="product_plugin:plugin_prod_"}
				  {assign var="blockfile" value="$ablock$afile"}
				  { $block->display($blockfile) }
				</td>
			  </tr> 
			  {/if}			
          </table>
        </td>
      </tr>
    </table>  
  <br>
  </div>
  
   
  <div id="domain" {style_hide}> 
    {if $list->is_installed('host_tld')}
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
      <tr> 
        <td> 
          <table width="100%" border="0" cellspacing="1" cellpadding="0">
            <tr valign="top"> 
              <td width="65%" class="table_heading"> 
                <center>
                  {translate module=service}
                  title_domain 
                  {/translate}
                </center>
              </td>
            </tr>
            <tr valign="top"> 
              <td width="65%" class="row1"> 
                <table width="100%" border="0" cellspacing="2" cellpadding="1" class="row1">
                  <tr> 
                    <td width="29%" bgcolor="#FFFFFF"> 
                      <p> 
                        <input type="radio" id="register" name="domain_type" value="register" onClick="domainUpdate('0','0','register'); showIFrame('iframeDomainDetails',480,95,'?_page=host_tld:iframe_register&_escape=1');">
                        {translate module=product}
                        domain_register 
                        {/translate}
                        <br>
                        <input type="radio" id="transfer" name="domain_type" value="transfer" onClick="domainUpdate('0','0','transfer'); showIFrame('iframeDomainDetails',480,110,'?_page=host_tld:iframe_transfer&_escape=1');">
                        {translate module=product}
                        domain_transfer 
                        {/translate}
                        {/if}
                        <br>
                        <input type="radio" id="ns_transfer" name="domain_type" value="ns_transfer" onClick="domainUpdate('0','0','ns_transfer'); showIFrame('iframeDomainDetails',480,70,'?_page=host_tld:iframe_ns_transfer&_escape=1');">
                        {translate module=product}
                        domain_ns_transfer 
                        {/translate}
                        {if $product.host_allow_host_only}
                      </p> 
                    </td>
                  </tr>
                </table>
              </td>
            </tr> 
          </table>
        </td>
      </tr>
    </table>
    {/if}
  <br>
  </div> 
  <center>					<iframe name="iframeDomainDetails" id="iframeDomainDetails" style="border:0px; width:100%; height:0px;" scrolling="no" width="100%" allowtransparency="false" frameborder="0" class="body" SRC="themes/{$THEME_NAME}/IEFrameWarningBypass.htm"><br></iframe>  </center>

  
  <div id="none" style="display:none"></div>

  </form> 
{/if} 
<script language=javascript> 
{if $VAR.vnone == 1}
	document.getElementById('service_none').checked = true;
	ServiceType('none'); 
{elseif $product.group || $VAR.vgroup == 1} 
	document.getElementById('service_group').checked = true;
	ServiceType('group'); 
{/if} 
{if $product.host == 1 || $VAR.vhosting == 1}
	document.getElementById('service_hosting').checked = true;
	ServiceType('hosting');
{elseif $product.prod_plugin == 1 || $VAR.vproduct == 1}
	document.getElementById('service_product').checked = true;
	ServiceType('product');
{/if}   
{if $VAR.vrecurring == 1}
document.getElementById('billing_type1').checked = true;
{/if} 	
updateTR();
</script>