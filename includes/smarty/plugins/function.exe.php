<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.exe.php
 * Type:       function
 * Name:       exe  
 * -------------------------------------------------------------
 */
function smarty_function_exe($params, &$smarty)
{  
	extract($params);
	include_once(PATH_CORE.'method.inc.php');
	$m = new CORE_method;
	
	if(!empty($noauth))
		$m->exe_noauth($module,$method);
	else
		echo $m->exe($module,$method);
}
?>