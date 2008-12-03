<?php 
require_once('config.inc.php');
require_once('modules/core/vars.inc.php');
$C_vars     = new CORE_vars;
$VAR        = $C_vars->f;
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
require_once(PATH_CORE.'crypt.inc.php');
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
    

require_once(PATH_MODULES   . 'task/task.inc.php');
$task = new task;

if(!empty($VAR['id'])) $task->id=$VAR['id'];
elseif(!empty($_SERVER["argv"])) $task->id = $_SERVER["argv"][0];

set_time_limit(0);

if(@$task->id) { 
	$task->run($VAR, &$task);	 
} else {
	$task->run_all(); 
}	 
?>