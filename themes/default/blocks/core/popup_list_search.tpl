{ $method->exe($VAR.module,"search") }
{ if ($method->result == FALSE) } 
    { $block->display("core:method_error") }
{else}
    {if $results > 0}
<HTML>
<HEAD>
<TITLE>{$smarty.const.SITE_NAME}</TITLE>

<SCRIPT LANGUAGE="JavaScript">
<!-- START
	var sess_expires 	= "{$smarty.const.SESSION_EXPIRE}";
	var cookie_name		= "{$smarty.const.COOKIE_NAME}";
	var SESS	 	= "{$SESS}";
	var URL			= "{$URL}";
	var SSL_URL		= "{$SSL_URL}";
	var THEME_NAME  	= "{$THEME_NAME}";
	var REDIRECT_PAGE 	= "{$smarty.const.REDIRECT_PAGE}";
//  END -->
</SCRIPT>

<!-- Load the main javascript code -->
<SCRIPT SRC="themes/{$THEME_NAME}/top.js"></SCRIPT>

<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css">
</HEAD> 
<BODY class="row1">
    <center>
        <br>
        {translate}search_results{/translate}
        <br>
        {translate}alert_refresh_click{/translate}
        <br>
        <br>
        <a href="?_page={$VAR.module}:popup_list_show&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1&_escape=true&field_name={$VAR.field_name}">{translate}alert_click{/translate}</a><br> 
        <SCRIPT LANGUAGE="JavaScript">
            var module= "{$VAR.module}";
        	var delay = 0;
        	var url   = '?_page='+module+':popup_list_show&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1&_escape=true&field_name={$VAR.field_name}';
        	refresh(delay,url);
        </SCRIPT>
    </center>
<center>
        {translate}search_no_results{/translate}
        <form>
    <input type="button" value="{translate}window_close{/translate}" onclick="window.close()" class="form_button">
        </form>
    </center>
    {/if}
{/if}
</BODY>	
 
    
