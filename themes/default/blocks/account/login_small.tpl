 <form name="form1" method="post" action="">
  <table width="140" border="0" cellspacing="3" cellpadding="3" align="center" class="body" bordercolor="#FFFFE1">
    <tr> 
      <td width="48%"> <b> 
        {translate}
        username 
        {/translate}
        </b><br>
        <input type="text" name="_username" value="{$VAR._username}" size="10" />
      </td>
    </tr>
    <tr> 
      <td width="48%"> <b> 
        {translate}
        password 
        {/translate}
        </b><br>
        <input type="password" name="_password" size="10">
      </td>
    </tr>
    <tr> 
      <td width="48%"> 
        <input type="hidden" name="_login" value="Y">
        {if $VAR._page == ""}
        <input type="hidden" name="_page" value="account:account">
        {else}
        <input type="hidden" name="_page" value="{$VAR._page}">
        {/if}
		{if $VAR._htaccess_id != "" || $VAR._htaccess_dir_id != ""}
        <input type="hidden" name="_htaccess_id" value="{$VAR._htaccess_id}"> 
        <input type="hidden" name="_htaccess_dir_id" value="{$VAR._htaccess_dir_id}">
		{/if}
        <input type="submit" name="_login" value="{translate}login{/translate}" class="form_button">
      </td>
    </tr>
    <tr> 
      <td width="48%"><a href="{$SSL_URL}?_page=account:add"> 
        {translate}
        register 
        {/translate}
        </a></td>
    </tr>
    <tr> 
      <td width="48%"> <a href="{$SSL_URL}?_page=account:password"> 
        {translate}
        reset_password 
        {/translate}
        </a></td>
    </tr>
    {if $smarty.const.DEFAULT_ACCOUNT_STATUS == "1"}
    <tr> 
      <td width="48%"> <a href="{$SSL_URL}?_page=account:verify">
        {translate}
        verify 
        {/translate}
        </a> </td>
    </tr>
    {/if}
  </table>
  </form>
 
