<HTML>
<HEAD>
<meta http-equiv="expires" content="Wed, 26 Feb 2004 08:21:57 GMT">
<meta http-equiv="no-cache">

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
	{if $smarty.const.REDIRECT_PAGE!='REDIRECT_PAGE' && $smarty.const.REDIRECT_PAGE!=''}document.location.href='{$smarty.const.REDIRECT_PAGE}';{/if}
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

<!-- Load the popup class -->
{popup_init src="$URL/includes/overlib/overlib.js"}

<!-- prototype  -->
{if $VAR._page != 'email_template_translate:add' && $VAR._page != 'email_template_translate:view' &&
	$VAR._page != 'newsletter:add' && $VAR._page != 'newsletter:send' && $VAR._page != 'newsletter:view' &&
	$VAR._page != 'product:add' && $VAR._page != 'product_translate:edit' &&
	$VAR._page != 'product_cat_translate:add' && $VAR._page != 'product_cat_translate:view' &&
	$VAR._page != 'static_page_translate:add' && $VAR._page != 'static_page_translate:view' }
<script src="includes/javascript/prototype.js" type="text/javascript"></script>
<script src="includes/javascript/effects.js" type="text/javascript"></script>
<script src="includes/javascript/dragdrop.js" type="text/javascript"></script> 
<script src="includes/javascript/controls.js" type="text/javascript"></script>
{/if}

</HEAD>

<body bgcolor="#FFFFFF" text="#000000" leftmargin="0" rightmargin="0" topmargin="0" bottommargin="0" marginwidth="0" marginheight="0" class="body">

            <!-- display the alert block -->
            {if $alert}
            { $block->display("core:alert") }
            {/if}