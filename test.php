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
### ENCODING ###

if (eregi('Zend Optimizer', $php_info))
	$zend = true;
else
	$zend = false; 


if (function_exists("mmcache"))
	$mmcache = true;
else
	$mmcache = false; 

if (extension_loaded('ionCube Loader')) 
{
	$ioncube = true;
} 
else 
{
	$ion = new ioncube_test;
	$ioncube_arr = $ion->test();
	if($ioncube_arr[0] == true)
		$ioncube = true;
	else
		$ioncube = false; 
}	

if($ioncube || $mmcache  || $zend ) 
{
	$encoding['font'] = "FFFFFF";
	$encoding['back'] = "009900";
	$encoding['text'] = "OK:";
	if($ioncube) 
		$encoding['text'] .= " [Ioncube] ";
	if ($mmcache) 
		$encoding['text'] .= " [MMCache] ";
	if ($zend)
		$encoding['text'] .= " [Zend] ";
} else {
	$encoding['font'] = "FFFFFF";
	$encoding['back'] = "990000";
	$encoding['text'] =	"Failed.";
}

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
if(phpversion() >= '4.3' )
{
	$php['font'] = "FFFFFF";
	$php['back'] = "009900";
	$php['text'] = "OK"; 
} else {
	$php['font'] = "FFFFFF";
	$php['back'] = "990000";
	$php['text'] = "Failed!";
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
	$mysql['text'] = "Failed!";
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
	<tr bgcolor="<?php echo $encoding["back"]; ?>"> 
	  <td width="223"><b><font size="2" face="Verdana, Arial, Helvetica, sans-serif" color="#FFFFFF">ENCODING</font></b></td>
	  <td width="192"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"> 
		<font color="#<?php echo $encoding["font"]; ?>"> 
		<?php echo $encoding["text"]; ?>
		</font></font></td>
	  <td width="15"> 
		<div align="center"><font size="2" face="Verdana, Arial, Helvetica, sans-serif"><a href="javascript:help('encoding');"><b>?</b></a></font></div>
	  </td>
	</tr>
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





<?php

//
// ionCube Run Time Loading Compatibility Tester 1.9
//
// Last modified 2004-6-17
//
// Copyright (c) 2002-2004 ionCube Ltd.
//

//
// Detect some system parameters
//

class ioncube_test
{
function ic_system_info()
{
  $thread_safe = false;
  $debug_build = false;
  $cgi_cli = false;
  $php_ini_path = '';

  ob_start();
  phpinfo(INFO_GENERAL);
  $php_info = ob_get_contents();
  ob_end_clean();

  foreach (split("\n",$php_info) as $line) {
	if (eregi('command',$line)) {
	  continue;
	}

	if (eregi('thread safety.*(enabled|yes)',$line)) {
	  $thread_safe = true;
	}

	if (eregi('debug.*(enabled|yes)',$line)) {
	  $debug_build = true;
	}

	if (eregi("configuration file.*(</B></td><TD ALIGN=\"left\">| => |v\">)([^ <]*)(.*</td.*)?",$line,$match)) {
	  $php_ini_path = $match[2];

	  if (!@file_exists($php_ini_path)) {
	$php_ini_path = '';
	  }
	}

	$cgi_cli = ((strpos(php_sapi_name(),'cgi') !== false) ||
		(strpos(php_sapi_name(),'cli') !== false));
  }

  return array('THREAD_SAFE' => $thread_safe,
		   'DEBUG_BUILD' => $debug_build,
		   'PHP_INI'     => $php_ini_path,
		   'CGI_CLI'     => $cgi_cli);
}

function test()
{

	$nl =  ((php_sapi_name() == 'cli') ? "\n" : '<br>'); 
	$ok = true;
	$already_installed = false; 
	$here = dirname(__FILE__);

	$sys_info = $this->ic_system_info();

	  if ($sys_info['THREAD_SAFE'] && !$sys_info['CGI_CLI']) {
		$msg = "Your PHP install appears to have threading support and run-time Loading
	is only possible on threaded web servers if using the CGI, FastCGI or
	CLI interface.$nl${nl}To run encoded files please install the Loader in the php.ini file.$nl";
		$ok = false;
	  }

	  if ($sys_info['DEBUG_BUILD']) {
		$msg = "Your PHP installation appears to be built with debugging support
	enabled and this is incompatible with ionCube Loaders.$nl${nl}Debugging support in PHP produces slower execution, is
	not recommended for production builds and was probably a mistake.${nl}${nl}You should rebuild PHP without the --enable-debug option and if
	you obtained your PHP install from an RPM then the producer of the
	RPM should be notified so that it can be corrected.$nl";
		$ok = false;
	  }

	  if (ini_get('safe_mode')) {
		$msg = "PHP safe mode is enabled and run time loading will not be possible.$nl";
		$ok = false;
	  } 


	  // If ok to try and find a Loader
	  if ($ok) { 

		// Old style naming should be long gone now
		$test_old_name = false;

		$_u = php_uname();
		$_os = substr($_u,0,strpos($_u,' '));
		$_os_key = strtolower(substr($_u,0,3));

		$_php_version = phpversion();
		$_php_family = substr($_php_version,0,3);

		$_loader_sfix = (($_os_key == 'win') ? '.dll' : '.so');

		$_ln_old="ioncube_loader.$_loader_sfix";
		$_ln_old_loc="/ioncube/$_ln_old";

		$_ln_new="ioncube_loader_${_os_key}_${_php_family}${_loader_sfix}";
		$_ln_new_loc="/ioncube/$_ln_new";

		#echo "${nl}Looking for Loader '$_ln_new'";
		if ($test_old_name) {
		  #echo " or '$_ln_old'";
		}
		#echo $nl.$nl;

		$_extdir = ini_get('extension_dir');
		if ($_extdir == './') {
		  $_extdir = '.';
		}

		$_oid = $_id = realpath($_extdir);

		$_here = dirname(__FILE__);
		if ((@$_id[1]) == ':') {
		  $_id = str_replace('\\','/',substr($_id,2));
		  $_here = str_replace('\\','/',substr($_here,2));
		}
		$_rd=str_repeat('/..',substr_count($_id,'/')).$_here.'/';

		if ($_oid !== false) {
		  #echo "Extensions Dir: $_extdir ($_id)$nl";
		  #echo "Relative Path:  $_rd$nl";
		} else {
		  #echo "Extensions Dir: $_extdir (NOT FOUND)$nl$nl";

		  #echo "The directory set for the extension_dir entry in the
			#	php.ini file may not exist, and run time loading will not be possible.
			#	The system administrator should create the $_extdir directory,
			#	or install the Loader in the php.ini file.$nl";
		  $ok = false;
		}

		if ($ok) {
		  $_ln = '';
		  $_i=strlen($_rd);
		  while($_i--) {
		if($_rd[$_i]=='/') {
		  if ($test_old_name) {
			// Try the old style Loader name
			$_lp=substr($_rd,0,$_i).$_ln_old_loc;
			$_fqlp=$_oid.$_lp;
			if(@file_exists($_fqlp)) {
			  $msg = "Found Loader:   $_fqlp$nl";
			  return Array (true, $msg);
			  $_ln=$_lp;
			  break;
			}
		  }
		  // Try the new style Loader name
		  $_lp=substr($_rd,0,$_i).$_ln_new_loc;
		  $_fqlp=$_oid.$_lp;
		  if(@file_exists($_fqlp)) {
			$msg = "Found Loader:   $_fqlp$nl";
			return Array (true, $msg);
			$_ln=$_lp;
			break;
		  }
		}
		  }

		  //
		  // If Loader not found, try the fallback of in the extensions directory
		  //
		  if (!$_ln) {
		if ($test_old_name) {
		  if (@file_exists($_id.$_ln_old_loc)) {
			$_ln = $_ln_old_loc;
		  }
		}
		if (@file_exists($_id.$_ln_new_loc)) {
		  $_ln = $_ln_new_loc;
		}

		if ($_ln) {
		  $msg = "Found Loader $_ln in extensions directory.$nl";
		  return Array (true, $msg);
		}
		  }

		  echo $nl;

		  if ($_ln) {
		#echo "Trying to install Loader - this may produce an error...$nl$nl";
		dl($_ln);

		if(extension_loaded('ionCube Loader')) {
		  $msg = "The Loader was successfully installed and encoded files should be able to
	automatically install the Loader when needed. No changes to your php.ini file
	are required to use encoded files on this system.${nl}";
		return Array (true, $msg);
		} else {
		  $msg = "The Loader was not installed.$nl";
		  return Array (false, $msg);
		} 
		  } else {
		$msg = "Run-time loading should be possible on your system but no suitable Loader
	was found.$nl$nl . The $_os Loader for PHP $_php_family releases is required.$nl";
		return Array (true, $msg);
		  }
		}
	  } 
	return Array (false, $msg);
}
}
?>