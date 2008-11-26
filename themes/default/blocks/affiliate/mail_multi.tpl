{ $method->exe("affiliate","search_form") } { if ($method->result == FALSE) } 
{ $block->display("core:method_error") } {else} 
<form name="account_admin_mail" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=account_admin}
                title_mail 
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
                    mail_from 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    {if $VAR.mail_email_id != ""}
                    { $list->menu("", "mail_email_id", "setup_email", "name", $smarty.const.DEFAULT_SETUP_EMAIL, "form_menu") }
					{else}
					{ $list->menu("", "mail_email_id", "setup_email", "name", $setup.mail_email_id, "form_menu") }
					{/if}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_to 
                    {/translate}
                  </td>
                  <td width="65%"><a href="?_page=affiliate:search_show&search_id={$VAR.search_id}">{translate 
                    module=account_admin} mail_multi {/translate} </a></td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_subject 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <input type="text" name="mail_subject" value="{$VAR.mail_subject}" {if $mail_subject == true}class="form_field_error"{/if} size="50">
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_message 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <textarea name="mail_message" {if $mail_message == true}class="form_field_error"  {/if} cols="55" rows="6">{$VAR.mail_message}</textarea>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    mail_priority 
                    {/translate}
                  </td>
                  <td width="65%"> 
                    <select name="mail_priority" >
                      <option value="0">{translate}false{/translate}</option> 
                      <option value="1">{translate}true{/translate}</option>
                    </select>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp;</td>
                  <td width="65%"> 
                    <input type="image" border="0" name="imageField" src="themes/{$THEME_NAME}/images/icons/mail_32.gif" alt="{translate}submit{/translate}">
                    <input type="hidden" name="_page" value="affiliate:mail_multi">
                    <input type="hidden" name="do[]" value="affiliate:mail_multi">
                    <input type="hidden" name="search_id" value="{$VAR.search_id}">
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
