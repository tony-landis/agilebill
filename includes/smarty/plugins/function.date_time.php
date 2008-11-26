<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.date_time
 * Type:       function
 * Name:       date_time 
 * -------------------------------------------------------------
 */
function smarty_function_date_time($params, &$smarty)
{  
	extract($params); 
	echo date(UNIX_DATE_FORMAT,$time);
	echo "  ";
	echo date(DEFAULT_TIME_FORMAT,$time); 
}
?>