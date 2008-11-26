<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     ab_version
 * Purpose:  display current AB version
 * -------------------------------------------------------------
 */
function smarty_function_ab_version($params, &$smarty)
{
	include_once(PATH_CORE.'version.inc.php');
	$ver = new CORE_version;
	$ver->smarty();	
	
} 

?>
