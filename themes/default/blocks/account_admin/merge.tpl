 
<form name="account_admin_merge" method="post" action="">
  
  <table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
    <tr> 
      <td> 
        <table width="100%" border="0" cellspacing="1" cellpadding="0">
          <tr valign="top"> 
            <td width="65%" class="table_heading"> 
              <div align="center"> 
                {translate module=account_admin}
                title_merge 
                {/translate}
              </div>
            </td>
          </tr>
          <tr valign="top"> 
            <td width="65%" class="row1">
              <table width="100%" border="0" cellspacing="6" cellpadding="1" class="row1">
                <tr valign="top"> 
                  <td width="35%"> 
                    {translate module=account_admin}
                    merge_account 
                    {/translate}
                  </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%">&nbsp; </td>
                </tr>
                <tr valign="top"> 
                  <td width="35%" align="right"> 
                    <table width="100%" border="0" cellspacing="0" cellpadding="1">
                      <tr> 
                        <td width="48%"> 
                          {html_select_account name="merge_acct_id"}
                        </td>
                        <td align="right" width="52%"> 
                          <input type="submit" name="Submit2" value="{translate}submit{/translate}" class="form_button">
                          <input type="hidden" name="_page" value="account_admin:merge">
                          <input type="hidden" name="do[]" value="account_admin:merge">
                          <input type="hidden" name="id" value="{$VAR.id}">
                        </td>
                      </tr>
                    </table>
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
 