

<!-- Display the form validation -->
{if $form_validation}
	{ $block->display("core:alert_fields") }
{/if}

<!-- Display the form to collect the input values -->
<form id="email_queue_add" name="email_queue_add" method="post" action="">
{$COOKIE_FORM}
<table width="500" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_queue}title_add{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_date_orig
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time("")}  <input type="hidden" name="email_queue_date_orig" value="{$smarty.now}">
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_date_last
                        {/translate}</td>
                    <td width="65%">
                        {$list->date_time("")}  <input type="hidden" name="email_queue_date_last" value="{$smarty.now}">
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_status
                        {/translate}</td>
                    <td width="65%">
                        { $list->bool("email_queue_status", $VAR.email_queue_status, "form_menu") }
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_account_id
                        {/translate}</td>
                    
                  <td width="65%"> 
                    {html_select_account name="email_queue_account_id" default=$VAR.email_queue_account_id}
                  </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_email_template
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_email_template" value="{$VAR.email_queue_email_template}" {if $email_queue_email_template == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql1
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql1" value="{$VAR.email_queue_sql1}" {if $email_queue_sql1 == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql2
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql2" value="{$VAR.email_queue_sql2}" {if $email_queue_sql2 == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql3
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql3" value="{$VAR.email_queue_sql3}" {if $email_queue_sql3 == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql4
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql4" value="{$VAR.email_queue_sql4}" {if $email_queue_sql4 == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_sql5
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_sql5" value="{$VAR.email_queue_sql5}" {if $email_queue_sql5 == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
                <tr valign="top">
                    <td width="35%">
                        {translate module=email_queue}
                            field_var
                        {/translate}</td>
                    <td width="65%">
                        <input type="text" name="email_queue_var" value="{$VAR.email_queue_var}" {if $email_queue_var == true}class="form_field_error"{/if}>
                    </td>
                  </tr>
           <tr valign="top">
                    <td width="35%"></td>
                    <td width="65%">
                      <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                      <input type="hidden" name="_page" value="email_queue:view">
                      <input type="hidden" name="_page_current" value="email_queue:add">
                      <input type="hidden" name="do[]" value="email_queue:add">
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
