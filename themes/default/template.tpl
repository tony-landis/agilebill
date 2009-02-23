<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" >
<title>{$smarty.const.SITE_NAME} - Powered by AgileBill</title>
<head>

<SCRIPT LANGUAGE="JavaScript">
<!-- START
	var pgescape		= "";
	var sess_expires 	= "{$smarty.const.SESSION_EXPIRE}";
	var cookie_name		= "{$smarty.const.COOKIE_NAME}";
	var SESS	 		= "{$SESS}";
	var URL				= "{$URL}";
	var SSL_URL			= "{$SSL_URL}";
	var THEME_NAME  	= "{$THEME_NAME}";
	{if $smarty.const.REDIRECT_PAGE!='REDIRECT_PAGE' && $smarty.const.REDIRECT_PAGE!=''}document.location.href='{$smarty.const.REDIRECT_PAGE}';{/if}
{literal}
  if (top.location != location) {
    top.location.href = document.location.href ;
  }{/literal}
//  END -->
</SCRIPT>

<style type="text/css">
	@import url('themes/{$THEME_NAME}/style.css');	
</style>
	
<!-- Load the main javascript code -->
<SCRIPT SRC="themes/{$THEME_NAME}/top.js"></SCRIPT>

</head> 

<body>
<div id="content">
<table width="750" border="0" align="center">
  <tr>
    <td width="100%" align="center"><a href="http://www.agilebill.com/" style="background: none;"><img src='themes/{$THEME_NAME}/images/logo.gif' border='0' /></a></td>
  </tr>
  <tr>
    <td>
	  <div id="menu">
	    
	    <a href="?_page=product_cat:menu">{translate}products{/translate}</a>
		{if $smarty.const.SHOW_CART_LINK} | <a href="?_page=cart:cart">{translate}cart{/translate}</a> {/if} 
		{if $smarty.const.SHOW_CONTACT_LINK} | <a href="?_page=staff:staff">{translate}contact{/translate}</a> {/if}   
		{if $smarty.const.SHOW_AFFILIATE_LINK} | <a href="?_page=affiliate:affiliate">{translate}affiliates{/translate}</a> {/if} 
		{if $smarty.const.SHOW_TICKET_LINK} | <a href="?_page=ticket:ticket">{translate}tickets{/translate}</a> {/if} 
		 | <a href="?_page=faq:faq">{translate}faqs{/translate}</a>
		
		{if $SESS_LOGGED}
		| <a href="?_page=account:account">{translate}account{/translate}</a>
		| <a href="?_page=account:account&_logout=Y">{translate}logout{/translate}</a>  
		
		{else}
		| <a href="index.php?_page=account:login">Login</a> 
		| <a href="index.php?_page=account:add">Register</a>  
		{if $smarty.const.SHOW_CHECKOUT_LINK} 
		| <a href="{$SSL_URL}?_page=checkout:checkout&s={$SESS}">{translate}checkout{/translate}</a>{/if} 
	    {/if}
		

	  </div>
	</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>
 	  <!-- Display the main block --> 
	  <div id="block">	
	      <!-- display the alert block -->
	      {if $alert}
	      { $block->display("core:alert") }
	      {/if}
	      <!-- display the main page -->
	      {if $VAR._page != ''}
	      { $block->display($smarty.const.THEME_PAGE) }
	      {elseif $SESS_LOGGED == "1"}
	      { $block->display("account:account") }
	      {else}
	      { $block->display("account:login") }
	      {/if}
	  </div>
	</td>
  </tr>
  <tr>
    <td><div id="copywrite">		
		<a href="http://www.agileco.com">Billing Software</a> Powered by AgileBill.
		Copyright 2004-2009 <a href="http://www.agileco.com/">Agileco, LLC</a>
	</div></td>
  </tr>
</table>

</div>
<center> 
<font size="-2">
	    {if $list->auth_method_by_name("account_admin","view") &&  $SESS_LOGGED == "1" }
        <a href="admin.php" style="text-decoration: none; color: black; font-weight: normal;">{translate}admin{/translate}</a>
        {/if}
</font>
</center>
</body>
</html>
