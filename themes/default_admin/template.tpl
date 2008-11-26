{if $SESS_LOGGED == true } 
<html>
<head>
<title>{$smarty.const.SITE_NAME} Administration</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head> 
<frameset rows="*" cols="255,*" frameborder="NO" border="0" framespacing="0">  
  <frame name="leftFrame" id="leftFrame" scrolling="auto" noresize src="?_page=core:leftFrameBlue&_escape=1">
  <frame name="mainFrame" id="mainFrame" src="{if $mainFrameUrl != ""}{$mainFrameUrl}{else}?_page=core:admin{/if}">
</frameset>
<noframes> 
<body bgcolor="#FFFFFF" text="#000000">
</body>
</noframes>
</html> 
{else} 
<script language=javascript>
 parent.location.href = '{$URL}?_page=account:account';
</script>  
{/if}