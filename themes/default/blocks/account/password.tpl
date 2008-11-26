<form name="update_form" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=account}
                title_password_reset 
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
                    password_reset 
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
                          field_email 
                          {/translate}
                        </td>
                        <td width="71%"> 
                          <input type="text" name="account_email"  value="{$VAR.account_email}" size="22">
                        </td>
                      </tr>
                      <tr class="row1" valign="middle" align="left"> 
                        <td width="29%"> 
                          {translate module=account}
                          field_username 
                          {/translate}
                        </td>
                        <td width="71%"> 
                          <input type="text" name="account_username"  value="{$VAR.account_username}" size="22">
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
  <input type="hidden" name="_page" value="account:password">
  <input type="hidden" name="do[]" value="account:password">
</form>
 
