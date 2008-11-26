{if $smarty.const.SESS_LOGGED == true }
	{ $block->display("account:account") }
{else}

<table width="100%" border="0" cellspacing="0" cellpadding="0" class="body">
  <tr> 
    <td valign="top" align="center" width="35%"> 
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr> 
          <td>
            <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table_background">
              <form name="form1" method="post" action="">
                <tr> 
                  <td> 
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
                                <input type="text" name="_username" id="loginUsername" value="{$VAR._username}" size="12">
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
                                {if $VAR._page != ""}
                                <input type="hidden" name="_page" value="{$VAR._page}">
                                {else}
                                <input type="hidden" name="_page" value="account:account">
                                {/if}
								
								{if $VAR._htaccess_id != "" || $VAR._htaccess_dir_id != ""}
                                <input type="hidden" name="_htaccess_id" value="{$VAR._htaccess_id}">
                                <input type="hidden" name="_htaccess_dir_id" value="{$VAR._htaccess_dir_id}">
								{/if}
                              </td>
                              <td width="75%"> 
                                <input type="submit" name="_login2" value="{translate}login{/translate}" class="form_button">
                              </td>
                            </tr>
                            <tr align="right">
                              <td colspan="2"><a href="?_page=account:password"> 
								{translate}
								reset_password 
								{/translate}
								</a></td>
                            </tr>
							{if $smarty.const.DEFAULT_ACCOUNT_STATUS == "1"}
                            <tr align="right">
                              <td colspan="2"><a href="?_page=account:verify">
								{translate}
								verify 
								{/translate}
								</a></td>
                            </tr>
							{/if}
                          </table>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </form>
            </table>
          </td>
        </tr>
      </table>
      <script language="javascript">document.getElementById('loginUsername').focus()</script>
    </td>
    <td width="1" height="1"><img src="themes/{$THEME_NAME}/images/invisible.gif" width="15" height="1"></td>
    <td align="center" valign="top" width="65%">
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td>
            { $block->display("account:add")   }
          </td>
        </tr>
      </table> 
    </td>
  </tr>
</table>
{/if} 