<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.translate.php
 * Type:     block
 * Name:     translate
 * Purpose:  translate a block of text
 * -------------------------------------------------------------
 */
function smarty_block_translate($params, $resource, &$smarty)
{
	global $C_translate;
	
	if($params["module"] != '')
	{
		$module = $params["module"];
	}
	else
	{
		$module = 'CORE';
	}
		

	while(list ($key, $val) = each ($params)) 	
	{
	 	$C_translate->value["$module"]["$key"] = $val;
	}
	

	
	$resource = trim($resource);
    if ($resource != '') {
		# strip whitespaces from the resouce identifier				
        echo $C_translate->translate($resource,$module,'');
    }
}
?>