{if $VAR.validate == ""} {translate module=account here="test"}password_reset_bad_url 
{/translate}
<br>
<br>



<a href="?_page=account:password">{translate}submit{/translate}</a><br>
{elseif $pw_changed != true}

{if $VAR.type == 'expired'}
  <p><b>Your current password has expired. Please select a new password below in order to login.</b></p>
{/if}

<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center">
                {translate module=account}
                title_password_new 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    {translate module=account}
                    password_reset_instructions 
                    {/translate}
                    <br>
                    <br>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"> 
                    <table width="100%" border="0" cellspacing="3" cellpadding="1" class="row1">
                      <tr class="row1" valign="middle" align="left"> 
                        <td width="29%"> 
                          {translate module=account}
                          field_update_password 
                          {/translate}
                        </td>
                        <td width="71%"> 
                          <input type="password" name="account_password"  value="" size="32">
                        </td>
                      </tr>
                      <tr class="row1" valign="middle" align="left"> 
                        <td width="29%"> 
                          {translate module=account}
                          field_confirm_password 
                          {/translate}
                        </td>
                        <td width="71%"> 
                          <input type="password" name="confirm_password"  value="" size="32">
                        </td>
                      </tr>
                      <tr class="row1" valign="middle" align="left"> 
                        <td width="29%"></td>
                        <td width="71%"> 
                          <input type="submit" name="Submit" value="{translate}submit{/translate}" class="form_button">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
                <tr class="row1" valign="middle" align="left"> 
                  <td width="31%"></td>
                </tr>
              </table>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
  <input type="hidden" name="_page" value="account:password_reset">
    <input type="hidden" name="do[]" value="account:password_reset">
	<input type="hidden" name="validate" value="{$VAR.validate}">
  </form>  
 {else} 
  {html_button name=account action="document.location='?_page=account:account'"} 
 {/if}