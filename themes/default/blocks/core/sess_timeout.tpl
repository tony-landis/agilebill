<HTML>
<HEAD>
<TITLE>{translate}alert_session_timeout{/translate}</TITLE>
<link rel="stylesheet" href="themes/{$THEME_NAME}/style.css" type="text/css">
{literal}
  <SCRIPT LANGUAGE="JavaScript">
<!-- START	
 	ident=window.setTimeout("sess_logout()",60000);
	
	// Logout the current session:
	function sess_logout()
	{
		var url 		= '?_page=core:logout&_logout=true&_escape=true';
		var win 		= 'SessLogoutWin';
		var settings 	= 'toolbar=no,status=no,width=200,height=200';
		NewWindow(win,settings,url);
		window.close();
	}
	
	// Keep the current session active:
	function sess_renew()
	{
		var url 		= '?_page=core:sess_renew&_escape=true';
		var win 		= 'SessLogoutWin';
		var settings 	= 'toolbar=no,status=no,width=200,height=200';
		NewWindow(win,settings,url);
		window.close();
	}	
	
	// MINI WINDOW CONTROLLER
	function NewWindow(win,settings,url)
	{
		var eval1;
		eval1 = win + '=window.open("' + url + '","' + win + '","' + settings + '");';
		eval(eval1);
	}	
//  END -->
</SCRIPT>
{/literal} 
  </HEAD> 
  <BODY class="row1">
	  <center>
  {translate}login_sess_timeout{/translate}<br>
		  <br>
		  <a href="#" onClick="sess_logout();">{translate}logout{/translate}</a><br>
		  <br>
		  <a href="#" onClick="sess_renew();">{translate}login_renew{/translate}</a><br>
		  <br>
	</center>
  </BODY>
 </HTML>
