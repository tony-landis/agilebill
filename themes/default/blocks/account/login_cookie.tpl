{if $SESS_LOGGED != "1"} {translate}login_required{/translate}<br>
<br>
{ $block->display("account:login")}
{else}
<br>
<table width=100% border="0" cellspacing="0" cellpadding="0" class="table_background">
  <tr> 
    <td> 
      <table width="100%" border="0" cellspacing="1" cellpadding="5">
        <tr valign="top"> 
          <td width="65%" class="row1"> 
            { $method->exe("htaccess","check_smarty") }
            {if $htaccess_auth == "1" }
            {translate module=htaccess}
            success_login 
            {/translate}
            <a href="{$VAR._next_page}"><br>
            <br>
            {$VAR._next_page}
            </a> 
            <script language="JavaScript">
            var module= "";
        	var delay = 3;
        	var url   = '{$VAR._next_page}';
        	refresh(delay,url);
        </script>
            <br>
            {else}
            {translate module=htaccess}
            failed_login 
            {/translate}
            {/if}
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<br>
{/if}
