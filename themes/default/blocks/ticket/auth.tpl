 
<table width="100%" border="0" cellspacing="0" cellpadding="5" class="body">
  <tr> 
    <td> 
      <p><br>
        {translate module=ticket}
        user_ticket_auth 
        {/translate}
      </p>
      </td>
  </tr>
  <tr>
    <td>
      <table width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr valign="top"> 
          <td width="50%"> 
            <form name="form2" method="get" action="">
              <table width="100%" border="0" cellspacing="5" cellpadding="1" class="body">
                <tr> 
                  <td width="25%"> 
                    {translate module=ticket}
                    field_email 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <input type="text" name="email" value="{$VAR.email}" size="32">
                  </td>
                </tr>
                <tr> 
                  <td width="25%"> 
                    {translate module=ticket}
                    key 
                    {/translate}
                  </td>
                  <td width="75%"> 
                    <input type="text" name="key" value="{$VAR.key}" size="32">
                  </td>
                </tr>
                <tr> 
                  <td width="25%"> 
                    <input type="hidden" name="_page" value="{$VAR._page}">
                    <input type="hidden" name="id" value="{$VAR.id}">
                  </td>
                  <td width="75%"> 
                    <input type="submit" name="" value="{translate module=ticket}verify{/translate}" class="form_button">
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
        <tr valign="top"> 
          <td width="50%">&nbsp;</td>
        </tr>
        <tr valign="top"> 
          <td width="50%"> 
            <form name="form1" method="post" action="">
              <table width="100%" border="0" cellspacing="1" cellpadding="0">
                <tr valign="top"> 
                  <td width="65%" class="table_heading"> 
                    <div align="center"> 
                      {translate}
                      account_login 
                      {/translate}
                    </div>
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="65%" class="row1"> 
                    <table width="100%" border="0" cellspacing="5" cellpadding="1" class="row1">
                      <tr> 
                        <td width="25%"> 
                          {translate}
                          username 
                          {/translate}
                        </td>
                        <td width="75%"> 
                          <input type="text" name="_username" value="{$VAR._username}" size="12">
                        </td>
                      </tr>
                      <tr> 
                        <td width="25%"> 
                          {translate}
                          password 
                          {/translate}
                        </td>
                        <td width="75%"> 
                          <input type="password" name="_password" size="12">
                        </td>
                      </tr>
                      <tr> 
                        <td width="25%"> 
                          <input type="hidden" name="_login" value="Y">
                          <input type="hidden" name="_page2" value="{$VAR._page}">
                          <input type="hidden" name="id2" value="{$VAR.id}">
                        </td>
                        <td width="75%"> 
                          <input type="submit" name="_login2" value="{translate}login{/translate}" class="form_button">
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </form>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>

