<?php
	
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */


if(@$do == 'phpinfo' || @$_GET['do'] == 'phpinfo' || @$HTTP_GET_VARS['do'] == 'phpinfo'){
	phpinfo();
	exit;
}	


error_reporting(0);
$disabled_functions = ini_get('disable_functions');
ob_start();
phpinfo();
$php_info .= ob_get_contents();
ob_end_clean();


###############################################################
# GD  
if (extension_loaded('gd')) 
{
	$gd['font'] = "FFFFFF";
	$gd['back'] = "009900";
	$gd['text'] = "OK"; 
} else {
	$gd['font'] = "FFFFFF";
	$gd['back'] = "990000";
	$gd['text'] = "Failed.";
}	

###############################################################
#  SSL 

$sslx = false;				
if ( function_exists('curl_init') && !eregi('curl_init', $disabled_functions) && $curl_version = curl_version())
{
	if (eregi('openssl', @$curl_version['ssl_version'] ))
	{
		$sslx = true; 
	} 
} else {
	if ( phpversion() >= '4.3.0' && function_exists("fsockopen") &&
	!eregi('fsockopen', $disabled_functions) && function_exists("openssl_public_decrypt"))		
		$sslx = true; 
}


if ($sslx == true) 
{ 
	$ssl['font'] = "FFFFFF";
	$ssl['back'] = "009900";
	$ssl['text'] = "OK"; 
} else {
	$ssl['font'] = "FFFFFF";
	$ssl['back'] = "990000";
	$ssl['text'] = "Failed!";
} 

###############################################################
#  PHP 
if(phpversion() >= '5.0' )
{
	$php['font'] = "FFFFFF";
	$php['back'] = "009900";
	$php['text'] = "OK"; 
} else {
	$php['font'] = "FFFFFF";
	$php['back'] = "990000";
	$php['text'] = "Failed! (PHP 5.0 or later is required)";
}	 

###############################################################
#  MYSQL 			
if(is_callable("mysql_connect") && is_callable("mysql_get_client_info") && mysql_get_client_info() >= 4)
{
	$mysql['font'] = "FFFFFF";
	$mysql['back'] = "009900";
	$mysql['text'] = "OK"; 
} else {
	$mysql['font'] = "FFFFFF";
	$mysql['back'] = "990000";
	$mysql['text'] = "Failed! (MySQL 4.0 or later is required)";
}	


###############################################################
#  XML 			
if(is_callable("xml_parser_create"))
{
	$xml['font'] = "FFFFFF";
	$xml['back'] = "009900";
	$xml['text'] = "OK"; 
} else {
	$xml['font'] = "FFFFFF";
	$xml['back'] = "990000";
	$xml['text'] = "Failed!";
}

###############################################################
#  IMAP 			
if(is_callable("imap_open"))
{
	$imap['font'] = "FFFFFF";
	$imap['back'] = "009900";
	$imap['text'] = "OK"; 
} else {
	$imap['font'] = "FFFFFF";
	$imap['back'] = "990000";
	$imap['text'] = "Failed!";
}


?> 
<title>AgileBill Compatibility Test</title>

<script language="javascript">
function help(location) {
document.getElementById("help").style.width   = '650px';
document.getElementById("help").style.height  = '300px';
document.getElementById("help").src  = "http://agilebill.com/Requirements#"+location;
}
</script> 
<body bgcolor="#FFFFFF">
<br>
<table width="650" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#333333">
<tr> 
<td>
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr>
	  <td bgcolor="#000000"> 
		<div align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Absolute 
		  Requirements</font></b></div>
	  </td>
	</tr>
  </table>
</td>
</tr>
<tr> 
<td>
  <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="#FFFFFF" bordercolor="#CCCCCC">
	<tr bgcolor="<?php echo $mysql["back"]; ?>"> 
	  <td width="223"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">MySQL</font></b></td>
	  <td width="192"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#<?php echo $mysql["font"]; ?>"> 
		<?php echo $mysql["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('mysql');"><b>?</b></a></font></div>
	  </td>
	</tr>
	<tr bgcolor="<?php echo $php["back"]; ?>"> 
	  <td width="223"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">PHP</font></b></td>
	  <td width="192"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#<?php echo $php["font"]; ?>"> 
		<?php echo $php["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('php');"><b>?</b></a></font></div>
	  </td>
	</tr>
	<tr bgcolor="<?php echo $xml["back"]; ?>"> 
	  <td width="223"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">XML</font></b></td>
	  <td width="192"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#<?php echo $xml["font"]; ?>"> 
		<?php echo $xml["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('xml');"><b>?</b></a></font></div>
	  </td>
	</tr>        
  </table>
</td>
</tr>
</table>
<br>
<br>

<table width="650" border="0" cellspacing="0" cellpadding="1" align="center" bgcolor="#333333">
<tr> 
<td> 
  <table width="100%" border="0" cellspacing="0" cellpadding="2">
	<tr> 
	  <td bgcolor="#000000"> 
		<div align="center"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">Optional 
		  Requirements</font></b></div>
	  </td>
	</tr>
  </table>
</td>
</tr>
<tr> 
<td> 
  <table width="100%" border="0" cellspacing="0" cellpadding="3" bgcolor="#FFFFFF">
	<tr bgcolor="<?php echo $ssl["back"]; ?>"> 
	  <td width="225"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">SSL 
		Connectivity</font></b></td>
	  <td width="190"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#<?php echo $ssl["font"]; ?>"> 
		<?php echo $ssl["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('ssl');"><b>?</b></a></font></div>
	  </td>
	</tr>
	<tr bgcolor="<?php echo $gd["back"]; ?>"> 
	  <td width="225"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">GD</font></b></td>
	  <td width="190"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><font color="#<?php echo $gd["font"]; ?>"> 
		<?php echo $gd["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('gd');"><b>?</b></a></font></div>
	  </td>
	</tr>
  </table>
</td>
</tr>
</table>



<br> <br><center>
<iframe id=help align=center frameborder=1 height=500 width=650 src="test.php?do=phpinfo"></iframe></center>
