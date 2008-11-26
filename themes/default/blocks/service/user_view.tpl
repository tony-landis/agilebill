{if $smarty.const.SESS_LOGGED == false}
{$block->display("account:login")}
{else}
{ $method->exe("service","user_view") } { if ($method->result == FALSE) } { $block->display("core:method_error") } {else}
 
<!-- Loop through each record -->
{foreach from=$service item=service} 

<!-- Display the field validation -->
{if $form_validation}
   { $block->display("core:alert_fields") }
{/if}

<!-- Display each record --> 
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=service}title_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row1"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_date_orig 
                    {/translate}
                    </b> </td>
                  <td width="40%"> <b> 
                    {translate module=service}
                    field_date_last 
                    {/translate}
                    </b> </td>
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_invoice_id 
                    {/translate}
                    </b> </td>
                </tr>
                <tr valign="top"> 
                  <td width="30%"> 
                    {$list->date_time($service.date_orig)}
                  </td>
                  <td width="40%"> 
                    {$list->date_time($service.date_last)}
                  </td>
                  <td width="30%"> <a href="?_page=invoice:user_view&id={$service.invoice_id}"> 
                    {$service.invoice_id}
                    </a></td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_active 
                    {/translate}
                    </b> </td>
                  <td width="40%"> <b> 
                    {translate module=service}
                    field_sku 
                    {/translate}
                    </b> </td>
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_type 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  
                <td width="30%"> 
                  {if $service.active == 1}
                  {translate}
                  true 
                  {/translate}
                  {else}
                  {translate}
                  false{/translate} 
                  {/if}
                </td>
                  <td width="40%"> 
                    {$service.sku}
                  </td>
                  <td width="30%"> 
                    {translate module=service}
                    {$service.type}
                    {/translate}
                  </td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_price 
                    {/translate}
                    </b></td>
                  <td width="40%"> <b> 
                    {translate module=service}
                    field_price_type 
                    {/translate}
                    </b></td>
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_taxable 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="30%"> 
                    {$list->format_currency_num($service.price, '')}
                  </td>
                  <td width="40%"> 
                    {if $service.price_type == "0"}
                    {translate module=product}
                    price_type_one 
                    {/translate}
                    {/if}
                    {if $service.price_type == "1"}
                    {translate module=product}
                    price_type_recurr 
                    {/translate}
                    {/if}
                  </td>
                  
                <td width="30%"> 
                  {if $service.taxable == 1}
                  {translate}
                  true 
                  {/translate}
                  {else}
                  {translate}
                  false 
			 	  {/translate} 
                  {/if}
              </tr>
                {if $service.account_billing_id > 0}
                <tr valign="top"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_account_billing_id 
                    {/translate}
                    </b> </td>
                  <td width="40%"><a href="{$SSL_URL}?_page=account_billing:user_view&id={$service.account_billing_id}">
                    {translate}
                    view
                    {/translate}
                    </a></td>
                  <td width="30%">&nbsp; </td>
                </tr>
                {/if}
                {if $service.prod_plugin_name eq "VOIP"}
                <tr valign="top">
                  <td width="30%"> <b>
                  {translate module=service}
                  field_service_did
                  {/translate}
                    </b></td>
                  <td width="40%">&nbsp; </td>
                  <td width="30%">&nbsp; </td>
                </tr>
                <tr valign="top">
                  <td width="30%">{voip_did service_id=$service.id}</td>
                  <td width="40%">&nbsp; </td>
                  <td width="30%">&nbsp; </td>
                </tr>
                {/if}
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
 
 
{if $service.active == 1 }
{if $service.recur_modify == 1 && $service.suspend_billing != 1}<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <center>
              {translate module=service}
              title_modify 
              {/translate}
            </center>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top" class="row1"> 
                <td width="50%"> 
                  {translate module=service id=$service.id}
                  modify_explain 
                  {/translate}
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

{if $service.date_next_invoice > 0 && $service.suspend_billing == 1 }
<br>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top"> 
          <td width="65%" class="table_heading"> 
            <center>
              {translate module=service}
              title_suspended_billing
              {/translate}
            </center>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
              <tr valign="top" class="row1"> 
                <td width="50%"> 
                  {translate module=service}
                  suspended_billing_explain 
                  {/translate}{$list->date($service.date_next_invoice)} &nbsp; &nbsp;
				   <a href="?_page=service:user_view&id={$service.id}&do[]=service:user_reactivate"><strong>{translate module=service}reactivate{/translate}</strong></a></td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>


{elseif $service.date_next_invoice > 0 && $service.suspend_billing != 1 }
<br>
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
                  <td width="50%"> <b> 
                    {translate module=service}
                    field_date_last_invoice 
                    {/translate}
                    </b></td>
                  <td width="50%"> <b> 
                    {translate module=service}
                    field_date_next_invoice 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="50%"> 
                    {$list->date($service.date_last_invoice)}
                  </td>
                  <td width="50%"> 
                    {$list->date($service.date_next_invoice)}
                  </td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="50%"> <b> 
                    {translate module=service}
                    field_recur_schedule 
                    {/translate}
                    </b></td>
                  <td width="50%"> <b> </b></td>
                </tr>
                <tr valign="top"> 
                 
                <td width="50%"> 
				{if $recur_price }
                  <select name="service_recur_schedule"  {if $service.recur_schedule_change == "1"}onChange="if (confirm('{translate module=service}confirm_changeschedule{/translate}')) {literal}{ document.location='?_page=service:user_view&id={/literal}{$service.id}{literal}&do[]=service:user_changeschedule&service_recur_schedule='+this.value; }{/literal}{else} disabled="disabled"{/if}">
				  {foreach from=$recur_price item=price_recurr key=key}
                    <option value="{$key}" {if $service.recur_schedule == $key} selected{/if}> 
                    {$list->format_currency_num($price_recurr.base, $smarty.const.SESS_CURRENCY)}
                    &nbsp;&nbsp; 
                    {if $key == "0" }
                    {translate module=cart}
                    recurr_week 
                    {/translate}
                    {/if}
                    {if $key == "1" }
                    {translate module=cart}
                    recurr_month 
                    {/translate}
                    {/if}
                    {if $key == "2" }
                    {translate module=cart}
                    recurr_quarter 
                    {/translate}
                    {/if}
                    {if $key == "3" }
                    {translate module=cart}
                    recurr_semianual 
                    {/translate}
                    {/if}
                    {if $key == "4" }
                    {translate module=cart}
                    recurr_anual 
                    {/translate}
                    {/if}
                    {if $key == "5" }
                    {translate module=cart}
                    recurr_twoyear 
                    {/translate}
                    {/if}
                    {if $key == "6" }
                    {translate module=cart}
                    recurr_threeyear 
                    {/translate}						
                    </option>
					{/if}
                    {/foreach}
                  </select>
				  {else}
				  
				  
 						{if $service.recur_schedule == "0"}   
                        {translate module=product}
                        recurr_week 
                        {/translate}
                        {/if}
						
                        {if $service.recur_schedule == "1"}  
                        {translate module=product}
                        recurr_month 
                        {/translate}
                        {/if}
						
                        {if $service.recur_schedule == "2"}  
                        {translate module=product}
                        recurr_quarter 
                        {/translate}
                        {/if}
						
                        {if $service.recur_schedule == "3"}  
                        {translate module=product}
                        recurr_semianual 
                        {/translate}
                        {/if}
						
                        {if $service.recur_schedule == "4"}  
                        {translate module=product}
                        recurr_anual 
                        {/translate}
                        {/if}
						
                        {if $service.recur_schedule == "5"}  
                        {translate module=product}
                        recurr_twoyear 
                        {/translate}
                        {/if}
                        
						{if $service.recur_schedule == "6"}  
						{translate module=product}
						recurr_threeyear 
						{/translate} 
						{/if}
										  
				  {/if}
                </td>
                  
                <td width="50%"> 
                  {if $service.recur_cancel == 1}
                  <input type="button" name="cancelservice" value="{translate module=service}cancel{/translate}" class="form_button" onClick="if (confirm('{translate module=service}confirm_cancel{/translate}')) {literal}{ document.location='?_page=service:user_view&id={/literal}{$service.id}{literal}&do[]=service:user_cancelservice'; }{/literal}">
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
  {/if}
  
  {if $service.type == 'group' || $service.type == 'host_group'}
  <br>
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
                            <input type="radio" name="service_group_type" value="0" {if $service.group_type == "0"}checked{/if}>
                            {translate module=product}
                            assoc_group_limited 
                            {/translate}
                            <input type="text" name="service_group_days" value="{$service.group_days}"  size="3">
                            <br>
                            <input type="radio" name="service_group_type" value="1" {if $service.group_type == "1"}checked{/if}>
                            {translate module=product}
                            assoc_group_subscription 
                            {/translate}
                            <br>
                            <input type="radio" name="service_group_type" value="2" {if $service.group_type == "2"}checked{/if}>
                            {translate module=product}
                            assoc_group_forever 
                            {/translate}
                          </p>
                        </td>
                      </tr>
                    </table>
                    </td>
                  <td width="2%" align="left" valign="top"> 
                    { $list->menu_multi($service.group_grant, "service_group_grant", "group", "name", "10", "", "form_menu") }
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
  
  {if $service.type == 'domain' }
  <br>
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
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top" class="row1"> 
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_domain_name 
                    {/translate}
                    </b></td>
                  <td width="40%"> <b> 
                    {translate module=service}
                    field_domain_term 
                    {/translate}
                    </b></td>
                  <td width="30%"> <b> 
                    {translate module=service}
                    field_domain_date_expire 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="30%"> 
                    { $service.domain_name|upper }.{ $service.domain_tld|upper }
                    <b>&nbsp; <a href="?_page=core:search&module=service&service_domain_name={$service.domain_name}&service_domain_tld={$service.domain_tld}"><img src="themes/{$THEME_NAME}/images/icons/zoomi_16.gif" border="0" width="16" height="16" alt="Resend Invoice"></a> 
                    </b> </td>
                  <td width="40%"> 
                    { $service.domain_term }
                    Year(s) <a href="?_page=service:user_view&id={$service.id}&do[]=service:user_renew_domain">
                    {translate module=cart}
renew
{/translate}
                    </a></td>
                  <td width="30%"> 
                    { $list->calender_view("service_domain_date_expire", $service.domain_date_expire, "form_field", $service.id) }
                  </td>
                </tr>
                <tr valign="top" class="row1"> 
                  <td width="30%" height="22"> <b> 
                    {translate module=service}
                    field_domain_type 
                    {/translate}
                    </b></td>
                  <td width="40%" height="22"> <b> 
                    {translate module=service}
                    field_domain_host_registrar_id 
                    {/translate}
                    </b></td>
                  <td width="30%" height="22"> <b> 
                    {translate module=service}
                    field_domain_host_tld_id 
                    {/translate}
                    </b></td>
                </tr>
                <tr valign="top"> 
                  <td width="30%" height="27"> 
                   {translate module=cart}{$service.domain_type}{/translate} 
                  </td>
                  <td width="40%" height="27"> 
                    { $list->menu("", "service_domain_host_registrar_id", "host_registrar_plugin", "name", $service.domain_host_registrar_id, "form_menu") }
                  </td>
                  <td width="30%" height="27"> 
                    { $list->menu("", "service_domain_host_tld_id", "host_tld", "name", $service.domain_host_tld_id, "form_menu") }
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
  
  {if $service.type == 'host' || $service.type == 'host_group' }
  <br>
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
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  { if $service.domain_name != "" }
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_name 
                    {/translate}
                  </td>
                  <td width="65%"> <b> 
                    { $service.domain_name|upper }
                    . 
                    { $service.domain_tld|upper }
                    </b> </td>
                </tr>
                {/if}
                { if $service.host_ip != "" }
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $service.host_ip }
                  </td>
                </tr>
                {/if}
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_username 
                    {/translate}
                  </td>
                  <td width="65%"> {$service.host_username} </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_password 
                    {/translate}
                  </td>
                  <td width="65%">{$service.host_password} </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
{/if}
{/if} 
{/foreach}
{/if}
{/if}