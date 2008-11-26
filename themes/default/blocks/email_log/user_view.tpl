{if $email_log}
 <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="1" cellpadding="0">
        <tr valign="top">
          <td width="65%" class="table_heading">
            <center>
              {translate module=email_log}title_user_view{/translate}
            </center>
          </td>
        </tr>
        <tr valign="top">
          <td width="65%" class="row1">
            <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
          <tr class="row1" valign="middle" align="left">
                    <td width="20%" valign="top">{translate module=email_log}
                            field_date_orig
                    {/translate}</td>
                    <td width="80%">{$list->date_time($email_log.date_orig)}</td>
                </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_email
                  {/translate}</td>
            <td>{$email_log.email}</td>
          </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_subject
                  {/translate}</td>
            <td>{$email_log.subject}</td>
          </tr>
          <tr class="row1" valign="middle" align="left">
            <td valign="top">{translate module=email_log}
                            field_message
                  {/translate}</td>
            <td> <textarea name="textarea2" cols="65" rows="12" disabled="disabled">{$email_log.message}</textarea></td>
          </tr>
              </table>
          </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
{/if}