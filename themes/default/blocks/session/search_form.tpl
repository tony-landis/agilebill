{ $method->exe("session","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}
<form name="session_search" method="post" action="">  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=session}
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
                    {translate module=session}
                    field_date_orig 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("session_date_orig", $VAR.session_date_orig, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_date_last 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("session_date_last", $VAR.session_date_last, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_date_expire 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->calender_search("session_date_expire", $VAR.session_date_expire, "form_field", "") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_logged 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->bool("session_logged", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_ip 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="session_ip" value="{$VAR.session_ip}" {if $session_ip == true}class="form_field_error"{/if}>
                    &nbsp;&nbsp; 
                    {translate}
                    search_partial 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_theme_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "session_theme_id", "all", "theme", "", ".user_theme", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_country_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "session_country_id", "country", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_language_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu_files("", "session_language_id", "all", "theme", "", "_core.xml", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_currency_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    { $list->menu("", "session_currency_id", "currency", "name", "all", "form_menu") }
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_account_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_account name="session_account_id" default=$VAR.session_account_id}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=session}
                    field_affiliate_id 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {html_select_affiliate name="session_affiliate_id" default=$VAR.session_affiliate_id}
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
                    <input type="text"  name="limit" size="5" value="{$session_limit}">
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
                      {foreach from=$session item=record}
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
                    <input type="hidden" name="module" value="session">
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
{ $block->display("core:saved_searches") }
{ $block->display("core:recent_searches") }
{/if}
