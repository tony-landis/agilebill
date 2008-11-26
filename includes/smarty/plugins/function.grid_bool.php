<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     grid_boo
 * Purpose:  Formats boolean fields to translated true/false value
 * -------------------------------------------------------------
 */
function smarty_function_grid_bool($params, &$smarty)
{	 	 
	extract($params);  
	 
	if(!empty($bool))
	echo translate('true');
	else
	echo translate('false'); 
} 
?>