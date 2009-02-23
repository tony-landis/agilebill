<HTML>
<HEAD>
<meta http-equiv="no-cache">
<TITLE>{$smarty.const.SITE_NAME}</TITLE>
<link rel="stylesheet" href="includes/phplayers/layerstreemenu.css" type="text/css"></link>
<style type="text/css">
<!--
@import url("includes/phplayers/layerstreemenu-hidden.css");
//-->
</style>

<script language="JavaScript" type="text/javascript" src="includes/phplayers/libjs/layersmenu-browser_detection.js"></script>
<script language="JavaScript" type="text/javascript" src="includes/phplayers/libjs/layerstreemenu-cookies.js"></script>

{literal}
<SCRIPT LANGUAGE="JavaScript">
<!-- START 
	function openUrl(u) {
		var random1=Math.round(Math.random()+4*123);
		var random2=Math.round(Math.random()+ random1 * 321);
 		window.open('admin.php'+u+'&random='+random1+'='+random2,'mainFrame','');
	}
	 
	function exitAdmin() {
		var random1=Math.round(Math.random()+4*123);
		var random2=Math.round(Math.random()+ random1 * 321); 
		parent.document.location='index.php';
	}  
//  END --> 
</SCRIPT>
</HEAD>
<body bgcolor="#CCCCCC" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<div style="height:68px;">&nbsp;</div>
<table width="230px" border="0" align="center" cellpadding="0" cellspacing="0">
<tr> 
	<td bgcolor="#f1f1f1" style="border: 1px solid #999; border-top: 1px solid #999; border-bottom: 0px; padding: 5px; padding-left:10px;">
		<table width="100%" cellspacing="0" cellpadding="1" border="0">
			<tr>
				<td align="center"><img src="themes/default_admin/images/icons/user_16.gif" border="0"></td>
				<td><nobr><input id='account_admin' value='all' size=22 style='font-size:9' onKeyPress='if(checkEnter(event)) { openQuickSearch_account(); }' onfocus='javascript:this.select()'>&nbsp;<input type='button' value='Go' style='font-size:9' onclick='javascript:openQuickSearch_account()'></nobr>
				</td>
			</tr>
			<tr>
				<td align="center"><img src="themes/default_admin/images/icons/calc_16.gif" border="0"></td>
				<td><nobr><input id='invoice' value='all' size=22 style='font-size:9' onKeyPress='if(checkEnter(event)) { openQuickSearch_invoice(); }' onfocus='javascript:this.select()'>&nbsp;<input type='button' value='Go' style='font-size:9' onclick='javascript:openQuickSearch_invoice()'></nobr>
				</td>
			</tr>
			<tr>
				<td align="center"><img src="themes/default_admin/images/icons/tools_16.gif" border="0"></td>
				<td><nobr><input id='service' value='all' size=22 style='font-size:9' onKeyPress='if(checkEnter(event)) { openQuickSearch_service(); }' onfocus='javascript:this.select()'>&nbsp;<input type='button' value='Go' style='font-size:9' onclick='javascript:openQuickSearch_service()'></nobr>
				</td>
			</tr>						
		</table>
	</td> 
</tr>
<tr> 
	<td bgcolor="#f9f9f9" cellpadding="1" style="border: 1px solid #999; border-top: 1px solid #ccc; border-bottom: 1px solid #ccc; color:#003399; padding: 5px;">
	{/literal}
	{$list->generate_admin_menu()} 
	</td> 
</tr>
<tr> 
<td bgcolor="#dddddd" style="border: 1px solid #999; border-top: 1px solid #fff; font-size:9px; font-family:Arial, Helvetica, sans-serif; color:#999; text-align:center; padding: 2px;">
  Copyright 2004-2009, Agileco, LLC.<br> 
  All Rights Reserved.
</td>
</tr>
</table>
<br />
  
{literal}
<script language="JavaScript1.2">
	
	// function for calling the account quicksearch
	function openQuickSearch_account() {
		var m = 'account_admin';
		var st= "";
		var input = document.getElementById(m).value;		
		if (input.indexOf(" ")== -1) {
			var s = input;
			if(s == '[i]' || s == 'inactive') {
				st += '&account_admin_status=0';				
			} else if(s == '' || s == 'all') {
				st += '';
			} else if(s == '[t]' || s == 'today') {
				st += '&account_admin_date_orig[0]={/literal}{$today_start}{literal}&field_option[account_admin_date_orig][0]=>';
			} else if(s == '[w]' || s == 'week') {
				st += '&account_admin_date_orig[0]={/literal}{$week_start}{literal}&field_option[account_admin_date_orig][0]=>';
			} else if(s == '[m]' || s == 'month') {
				st += '&account_admin_date_orig[0]={/literal}{$month_start}{literal}&field_option[account_admin_date_orig][0]=>';
			} else if(s.match(/fn:/)) {
				var str = s.replace(/fn:/, "");
				st += '&account_admin_first_name='+str;
			} else if(s.match(/ln:/)) {
				var str = s.replace(/ln:/, "");
				st += '&account_admin_last_name='+str;
			} else if(s.match(/co:/)) {
				var str = s.replace(/co:/, "");
				st += '&account_admin_company='+str;
			} else if(s.match(/em:/)) {
				var str = s.replace(/em:/, "");
				st += '&account_admin_email='+str;
			} else {
				st += '&account_admin_username=' +s;
			}	
		} else {
			var array=input.split(" ");
			var num=0;
			while(num < array.length) 
			{
				var s = array[num];
				if(s == '[i]' || s == 'inactive') {
					st += '&account_admin_status=0';				
				} else if(s == '[t]' || s == 'today') {
					st += '&account_admin_date_orig[0]={/literal}{$today_start}{literal}&field_option[account_admin_date_orig][0]=>';
				} else if(s == '[w]' || s == 'week') {
					st += '&account_admin_date_orig[0]={/literal}{$week_start}{literal}&field_option[account_admin_date_orig][0]=>';
				} else if(s == '[m]' || s == 'month') {
					st += '&account_admin_date_orig[0]={/literal}{$month_start}{literal}&field_option[account_admin_date_orig][0]=>';
				} else if(s.match(/fn:/)) {
					var str = s.replace(/fn:/, "");
					st += '&account_admin_first_name='+str;
				} else if(s.match(/ln:/)) {
					var str = s.replace(/ln:/, "");
					st += '&account_admin_last_name='+str;
				} else if(s.match(/co:/)) {
					var str = s.replace(/co:/, "");
					st += '&account_admin_company='+str;
				} else if(s.match(/em:/)) {
					var str = s.replace(/em:/, "");
					st += '&account_admin_email='+str;
				} else {
					st += '&account_admin_username=' +s;
				}	
				num+=1;
			}
		} 	
		var u = '?_page=core:search&module='+m+'&_next_page_one=view&_escape=1'+st;
		openUrl(u);  
	}
	
	// function for calling the invoice quicksearch
	function openQuickSearch_invoice() {
		var m = 'invoice';
		var st= "";
		var input = document.getElementById(m).value;		
		if (input.indexOf(" ")== -1) {
			var s = input;
			if(s == '[d]' || s == 'due') {
				st += '&invoice_billing_status=0';
			} else if(s == '[p]' || s == 'pending') {
				st += '&invoice_billing_status=1&invoice_process_status=0';			
			} else if(s == '' || s == 'all') {
				st += '';
			} else if(s == '[t]' || s == 'today') {
				st += '&invoice_date_orig[0]={/literal}{$today_start}{literal}&field_option[invoice_date_orig][0]=>';
			} else if(s == '[w]' || s == 'week') {
				st += '&invoice_date_orig[0]={/literal}{$week_start}{literal}&field_option[invoice_date_orig][0]=>';
			} else if(s == '[m]' || s == 'month') {
				st += '&invoice_date_orig[0]={/literal}{$month_start}{literal}&field_option[invoice_date_orig][0]=>';
			} else {
				st += '&invoice_id=' +s;
			}	
		} else {
			var array=input.split(" ");
			var num=0;
			while(num < array.length) 
			{
				var s = array[num];
				if(s == '[d]' || s == 'due') {
					st += '&invoice_billing_status=0';
				} else if(s == '[p]' || s == 'pending') {
					st += '&invoice_billing_status=1&invoice_process_status=0';			
				} else if(s == '' || s == 'all') {
					st += '';
				} else if(s == '[t]' || s == 'today') {
					st += '&invoice_date_orig[0]={/literal}{$today_start}{literal}&field_option[invoice_date_orig][0]=>';
				} else if(s == '[w]' || s == 'week') {
					st += '&invoice_date_orig[0]={/literal}{$week_start}{literal}&field_option[invoice_date_orig][0]=>';
				} else if(s == '[m]' || s == 'month') {
					st += '&invoice_date_orig[0]={/literal}{$month_start}{literal}&field_option[invoice_date_orig][0]=>';
				} else {
					st += '&invoice_id=' +s;
				}	
				num+=1;
			}
		} 	
		var u = '?_page=core:search&module='+m+'&_next_page_one=view&_escape=1'+st;
		openUrl(u);  
	}
	
	
	// function for calling the invoice quicksearch
	function openQuickSearch_service() {
		var m = 'service';
		var st= "";
		var input = document.getElementById(m).value;
		var a = parseInt(input);
		var b = parseFloat(input);
		if (input == '' || input == 'all') {
			// do nothing
		} else if (a == b) {
			st += '&service_id='+input;  
		} else if (input.indexOf(".") )   {		
			var array=input.split(".");
			var domain = array[0];
			if(array.length > 1) var tld = array[1]; else var tld = '';
			st += '&service_domain_name='+domain+'&service_domain_tld='+tld;   
		} else if (input.indexOf(" ")== -1) {
			var s = input;
			if(s == '[i]' || s == 'inactive') {
				st += '&service_active=0'; 			
			} else if(s == '' || s == 'all') {
				st += '';
			} else if(s == '[t]' || s == 'today') {
				st += '&service_date_orig[0]={/literal}{$today_start}{literal}&field_option[service_date_orig][0]=>';
			} else if(s == '[w]' || s == 'week') {
				st += '&service_date_orig[0]={/literal}{$week_start}{literal}&field_option[service_date_orig][0]=>';
			} else if(s == '[m]' || s == 'month') {
				st += '&service_date_orig[0]={/literal}{$month_start}{literal}&field_option[service_date_orig][0]=>';
			} else {
				st += '&service_id=' +s;
			}	
		} else {
			var array=input.split(" ");
			var num=0;
			while(num < array.length) 
			{
				var s = array[num];
				if(s == '[i]' || s == 'inactive') {
					st += '&service_active=0'; 			
				} else if(s == '' || s == 'all') {
					st += '';
				} else if(s == '[t]' || s == 'today') {
					st += '&service_date_orig[0]={/literal}{$today_start}{literal}&field_option[service_date_orig][0]=>';
				} else if(s == '[w]' || s == 'week') {
					st += '&service_date_orig[0]={/literal}{$week_start}{literal}&field_option[service_date_orig][0]=>';
				} else if(s == '[m]' || s == 'month') {
					st += '&service_date_orig[0]={/literal}{$month_start}{literal}&field_option[service_date_orig][0]=>';
				} else {
					st += '&service_id=' +s;
				}	
				num+=1;
			}
		} 	
		var u = '?_page=core:search&module='+m+'&_next_page_one=view&_escape=1'+st;
		openUrl(u);  
	}			
	
</script> {/literal} 
</BODY>
</HTML>