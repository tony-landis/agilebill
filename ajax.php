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
define('AJAX', 1); 
require_once('config.inc.php');
require_once('modules/core/vars.inc.php');
$C_vars     = new CORE_vars;
$VAR        = $C_vars->f;  
require_once('includes/adodb/adodb.inc.php'); 
require_once('modules/core/auth.inc.php');
require_once('modules/core/database.inc.php'); 
require_once('modules/core/method_ajax.inc.php');
require_once('modules/core/session.inc.php'); 
require_once('modules/core/setup.inc.php');  
$C_debug 	= new CORE_debugger;  
$C_setup 	= new CORE_setup;
$C_sess 	= new CORE_session;
$C_sess->session_constant(); 
$C_method 	= new CORE_method;	 
if ((isset($VAR['_login'])) && (isset($VAR['_username'])) && (isset($VAR['_password']))) {
	require_once(PATH_CORE   . 'login.inc.php');
	$C_login = new CORE_login_handler();
	$C_login->login($VAR);
}  
$C_sess->session_constant_log(); 
$C_auth  	    = new CORE_auth (false);  
$C_method->do_all();  
ob_end_flush();

?>