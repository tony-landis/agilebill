<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.date
 * Type:       function
 * Name:       date 
 * -------------------------------------------------------------
 */
function smarty_function_date($params, &$smarty)
{  
	extract($params); 
	echo date(UNIX_DATE_FORMAT,$date); 
}
?>