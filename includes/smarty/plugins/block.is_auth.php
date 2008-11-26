<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.is_auth.php
 * Type:     block
 * Name:     is_auth
 * Purpose:  display content to authenticated groups based on module & method
 * -------------------------------------------------------------
 */
function smarty_block_is_auth($params, $resource, &$smarty)
{
    if(empty($resource)) return;
  
   	global $C_auth; 
	if(!is_object($C_auth)) return false;            
     
    if($C_auth->auth_method_by_name($params["module"], $params["method"]))
    echo $resource; 
    else
    echo $params["alt"];
}
?>