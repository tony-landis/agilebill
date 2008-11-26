
{ $method->exe("service","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="service_search" method="post" action="">
  
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=service}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_id" value="{$VAR.service_id}" {if $service_parent_id == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_invoice_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_invoice_id" value="{$VAR.service_invoice_id}" {if $service_invoice_id == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_sku 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_sku" value="{$VAR.service_sku}" {if $service_sku == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="service_account_id" default=$VAR.service_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_active 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("service_active", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="service_type" >
                      <option value=""></option>
                      {if $list->is_installed("host_server") }
                      <option value="host"> 
                      {translate module=service}
                      host 
                      {/translate}
                      </option>
                      <option value="host_group"> 
                      {translate module=service}
                      host_group 
                      {/translate}
                      </option>
                      <option value="domain"> 
                      {translate module=service}
                      domain 
                      {/translate}
                      </option>
                      {/if}
                      {if $list->is_installed("db_mapping") || $list->is_installed("htaccess") }
                      <option value="group"> 
                      {translate module=service}
                      group 
                      {/translate}
                      </option>
                      {/if}
                      <option value="none"> 
                      {translate module=service}
                      none 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_queue 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="select" >
                      <option value=""></option>
                      <option value="new"> 
                      {translate module=service}
                      new 
                      {/translate}
                      </option>
                      <option value="active"> 
                      {translate module=service}
                      active 
                      {/translate}
                      </option>
                      <option value="inactive"> 
                      {translate module=service}
                      inactive 
                      {/translate}
                      </option>
                      <option value="delete"> 
                      {translate module=service}
                      delete 
                      {/translate}
                      </option>
                      <option value="edit"> 
                      {translate module=service}
                      edit 
                      {/translate}
                      </option>
                      <option value="delete"> 
                      {translate module=service}
                      delete 
                      {/translate}
                      </option>
                      <option value="queue_none"> 
                      {translate module=service}
                      queue_none 
                      {/translate}
                      </option>
                    </select>
                    &nbsp;&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_recur_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="service_recur_type" >
                      <option value=""></option>
                      <option value="0"> 
                      {translate module=product}
                      recurr_type_aniv 
                      {/translate}
                      </option>
                      <option value="1"> 
                      {translate module=product}
                      recurr_type_fixed 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_recur_schedule 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="service_recur_schedule" >
                      <option value=""></option>
                      <option value="0"> 
                      {translate module=product}
                      recurr_week 
                      {/translate}
                      </option>
                      <option value="1"> 
                      {translate module=product}
                      recurr_month 
                      {/translate}
                      </option>
                      <option value="2"> 
                      {translate module=product}
                      recurr_quarter 
                      {/translate}
                      </option>
                      <option value="3"> 
                      {translate module=product}
                      recurr_semianual 
                      {/translate}
                      </option>
                      <option value="4"> 
                      {translate module=product}
                      recurr_anual 
                      {/translate}
                      </option>
                      <option value="5"> 
                      {translate module=product}
                      recurr_twoyear 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("service_date_orig", $VAR.service_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_date_last_invoice 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("service_date_last_invoice", $VAR.service_date_last_invoice, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_date_next_invoice 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("service_date_next_invoice", $VAR.service_date_next_invoice, "form_field", "") }
                  </td>
                </tr>
                <!-- Define the results per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_results_per 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text"  name="limit" size="5" value="{$service_limit}">
                  </td>
                </tr>
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"> 
                    {translate}
                    search_order_by 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select  name="order_by">
                      {foreach from=$service item=record}
                      <option value="{$record.field}"> 
                      {$record.translate}
                      </option>
                      {/foreach}
                    </select>
                  </td>
                </tr>
                <tr class="row1" valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit" value="{translate}search{/translate}" class="form_button">
                    <input type="hidden" name="_page" value="core:search">
                    <input type="hidden" name="_escape" value="Y">
                    <input type="hidden" name="module" value="service">
                    <input type="hidden" name="_next_page_one" value="view">
                  </td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  
  
  {if $list->is_installed('host_server') }
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
                + 
                {translate module=service}
                title_domain 
                {/translate}
              </center>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1"> 
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_server_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("no", "service_host_server_id", "host_server", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_host_ip" value="{$VAR.service_host_ip}" {if $service_host_ip == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_host_username 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_host_username" value="{$VAR.service_host_username}" {if $service_host_username == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_name 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="service_domain_name" value="{$VAR.service_domain_name}" {if $service_domain_name == true}class="form_field_error"{/if}>
                    . 
                    { $list->menu("no", "service_domain_host_tld_id", "host_tld", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_type 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="service_domain_type" >
                      <option value=""></option>
                      <option value="register"> 
                      {translate module=cart}
                      register 
                      {/translate}
                      </option>
                      <option value="transfer"> 
                      {translate module=cart}
                      transfer 
                      {/translate}
                      </option>
                      <option value="park"> 
                      {translate module=cart}
                      park 
                      {/translate}
                      </option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_host_registrar_id
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "service_domain_host_registrar_id", "host_registrar_plugin", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=service}
                    field_domain_date_expire 
                    {/translate}
                  </td>
                  <td width="65%">
                    { $list->calender_search("service_domain_date_expire", $VAR.service_domain_date_expire, "form_field", "") }
                  </td>
                </tr>
                <!-- Define the results per page -->
                <!-- Define the order by field per page -->
                <tr class="row1" valign="top"> 
                  <td width="35%"></td>
                  <td width="65%"> 
                    <input type="submit" name="Submit2" value="{translate}search{/translate}" class="form_button">
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
  
  
</form>
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
