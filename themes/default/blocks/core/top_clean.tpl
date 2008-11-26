<HTML>
<HEAD>
<TITLE>{$smarty.const.SITE_NAME}</TITLE>


<SCRIPT LANGUAGE="JavaScript">
<!-- START
	var pgescape		= "&_escape=1&_escape_next=1";
	var sess_expires 	= "{$smarty.const.SESSION_EXPIRE}";
	var cookie_name		= "{$smarty.const.COOKIE_NAME}";
	var SESS	 	= "{$SESS}";
	var URL			= "{$URL}";
	var SSL_URL		= "{$SSL_URL}";
	var THEME_NAME  	= "{$THEME_NAME}";
	var REDIRECT_PAGE 	= "{$smarty.const.REDIRECT_PAGE}";
	{literal}
	if(pgescape != "") {
		pgescape = "&_escape=1&_escape_next=1";
	} else {
		pgescape = "";
	}
	{/literal}	
//  END -->
</SCRIPT>

<!-- Load the main stylesheet -->
<link rel="stylesheet" href="themes/{$THEME_NAME}/iframe.css" type="text/css">
 
<!-- Load the main javascript code -->
<SCRIPT SRC="themes/{$THEME_NAME}/top.js"></SCRIPT>

<!-- Load the JSCalender code -->
<link rel="stylesheet" type="text/css" media="all" href="includes/jscalendar/calendar-blue.css" title="win2k-1" />
<script type="text/javascript" src="includes/jscalendar/calendar_stripped.js"></script>
<script type="text/javascript" src="includes/jscalendar/lang/calendar-{$smarty.const.LANG}.js"></script>
<script type="text/javascript" src="includes/jscalendar/calendar-setup_stripped.js"></script>


</HEAD>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="body">

            <!-- display the alert block -->
            {if $alert}
            { $block->display("core:alert") }
            {/if}