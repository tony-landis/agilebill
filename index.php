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

ob_start();   

require_once('config.inc.php');           
require_once('modules/core/vars.inc.php');
$C_vars     = new CORE_vars;
$VAR        = $C_vars->f; 

if(!defined('PATH_AGILE')) {
	if(is_file('install/install.inc'))
		require_once('install/install.inc');
	exit;
}  

require_once('includes/adodb/adodb.inc.php');
require_once('includes/smarty/Smarty.class.php');
require_once(PATH_CORE.'auth.inc.php');
require_once(PATH_CORE.'database.inc.php');
require_once(PATH_CORE.'list.inc.php');
require_once(PATH_CORE.'method.inc.php');
require_once(PATH_CORE.'session.inc.php');
require_once(PATH_CORE.'theme.inc.php');
require_once(PATH_CORE.'translate.inc.php');
require_once(PATH_CORE.'setup.inc.php');
require_once(PATH_CORE.'xml.inc.php');

$C_debug 	= new CORE_debugger;  
$C_setup 	= new CORE_setup;
$C_sess 	= new CORE_session;
$C_sess->session_constant();
$C_translate= new CORE_translate;
$C_method 	= new CORE_method;	 

if ((isset($VAR['_login'])) && (isset($VAR['_username'])) && (isset($VAR['_password']))) {
	require_once(PATH_CORE   . 'login.inc.php');
	$C_login = new CORE_login_handler();
	$C_login->login($VAR);
} elseif (isset($VAR['_logout']))  {
	require_once(PATH_CORE   . 'login.inc.php');
	$C_login = new CORE_login_handler();	
	$C_login->logout($VAR);
}

$C_sess->session_constant_log();

$force          = false;
$C_auth  	    = new CORE_auth ($force); 

$smarty 		= new Smarty;     
$C_list         = new CORE_list;
$C_block        = new CORE_block;	

for($i=0;$i<count(@$_SERVER["argv"]); $i++)
	if(@$_SERVER["argv"][$i] == "_task=1")
		$VAR['_task'] = 1;

if(isset($VAR['_task'])) {
	require_once(PATH_MODULES   . 'task/task.inc.php');
	$task = new task;
	$task->run_all();
	exit;
}  

$C_method->do_all();

if(isset($C_auth2) && $C_auth2 != false && defined("FORCE_SESS_ACCOUNT")) {
	$smarty->assign("SESS_LOGGED",  FORCE_SESS_LOGGED);
	$smarty->assign("SESS_ACCOUNT", FORCE_SESS_ACCOUNT);
} else {
	$smarty->assign("SESS_LOGGED", 	SESS_LOGGED);
	$smarty->assign("SESS_ACCOUNT", SESS_ACCOUNT);
}

$smarty->assign_by_ref("method",    $C_method);
$smarty->assign_by_ref("list", 	    $C_list);
$smarty->assign_by_ref("block",     $C_block);
$smarty->assign_by_ref("alert",     $C_debug->alert);
$smarty->assign("VAR", 			    $VAR);
$smarty->assign("SESS", 		    SESS);
$smarty->assign("SSL_URL", 		    SSL_URL);
$smarty->assign("URL", 			    URL);
$C_theme 	= new CORE_theme;

ob_end_flush();
?>
