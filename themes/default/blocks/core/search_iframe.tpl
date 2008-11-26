<HTML>
<HEAD>
<SCRIPT LANGUAGE="JavaScript">
<!-- START
	var sess_expires 	= "{$smarty.const.SESSION_EXPIRE}";
	var cookie_name		= "{$smarty.const.COOKIE_NAME}";
	var SESS	 		= "{$SESS}";
	var URL				= "{$URL}";
	var SSL_URL			= "{$SSL_URL}";
	var THEME_NAME  	= "{$THEME_NAME}";
	var REDIRECT_PAGE 	= "{$smarty.const.REDIRECT_PAGE}";
//  END -->
</SCRIPT>

<!-- Load the main stylesheet -->
<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css">

<!-- Load the main javascript code -->
<SCRIPT SRC="themes/{$THEME_NAME}/top.js"></SCRIPT>
 
</HEAD>
<BODY leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
{ $method->exe($VAR.module,"search") }
{ if ($method->result == FALSE) }
{ $block->display("core:method_error") }
{else}
{if $results > 0}

	{if $results == "1" && $VAR._next_page_one != "" }
	<SCRIPT LANGUAGE="JavaScript"> 
			  var url   = '?_page={$VAR.module}:{$VAR._next_page_one}&id={$record_id},{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1{/if}';        	
			  document.location = url;
	</SCRIPT>
	{else}
	<SCRIPT LANGUAGE="JavaScript">
        var module= "{$VAR.module}"; 
	    {if $VAR._next_page == ""}
        var url   = '?_page='+module+':search_show&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1&{$VAR.name_id1}={$VAR.val_id1}&{$VAR.name_id2}={$VAR.val_id2}{/if}';
		{else}
        var url   = '?_page='+module+':{$VAR._next_page}&search_id={$search_id}&order_by={$order_by}&limit={$limit}&page=1{if $VAR._escape_next == "1" }&_escape=1&_escape_next=1&{$VAR.name_id1}={$VAR.val_id1}&{$VAR.name_id2}={$VAR.val_id2}{/if}';
		{/if}        	
		document.location = url;
 	</SCRIPT>	  	  
  	{/if}
	
{else}
  	  <SCRIPT LANGUAGE="JavaScript">
        var module= "{$VAR.module}"; 
	    {if $VAR._next_page_none == ""}
			document.close();
        {else}
       	 var url   = '?_page='+module+':{$VAR._next_page_none}&_escape=1&_escape_next=1&{$VAR.name_id1}={$VAR.val_id1}&{$VAR.name_id2}={$VAR.val_id2}';
		{/if}        	
		document.location = url;
  	  </SCRIPT>
{/if}
{/if}
</body>
</html>
