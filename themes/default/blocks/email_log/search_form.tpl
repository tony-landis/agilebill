
{ $method->exe("email_log","search_form") }
{ if ($method->result == FALSE) }
    { $block->display("core:method_error") }
{else}

<form name="email_log_search" method="post" action="">
  {$COOKIE_FORM}
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_log}title_search{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        { $list->calender_search("email_log_date_orig", $VAR.email_log_date_orig, "form_field", "") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_account_id
                        {/translate}</td>
                    <td width="65%">{html_select_account name="email_log_account_id" default=$VAR.email_log_account_id} </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_email
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_log_email" value="{$VAR.email_log_email}" {if $email_log_email == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_subject
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_log_subject" value="{$VAR.email_log_subject}" {if $email_log_subject == true}class="form_field_error"{/if}> 
                        &nbsp;&nbsp;{translate}search_partial{/translate}</td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_message
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_log_message" value="{$VAR.email_log_message}" {if $email_log_message == true}class="form_field_error"{/if}> &nbsp;&nbsp;{translate}search_partial{/translate}
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_urgent
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("email_log_urgent", "all", "form_menu") }
                    </td>
                  </tr>
                   <tr valign="top">
                    <td width="35%">
                        {translate module=email_log}
                            field_userread
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("email_log_userread", "all", "form_menu") }
                    </td>
                  </tr>
                           <!-- Define the results per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_results_per{/translate}</td>
                    <td width="65%">
                      <input type="text" name="limit" size="5" value="{$email_log_limit}">
                    </td>
                  </tr>

                  <!-- Define the order by field per page -->
                  <tr class="row1" valign="top">
                    <td width="35%">{translate}search_order_by{/translate}</td>
                    <td width="65%">
                      <select class="form_menu" name="order_by">
        		          {foreach from=$email_log item=record}
                            <option value="{$record.field}">{$record.translate}</option>
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
                      <input type="hidden" name="module" value="email_log">
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
