<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.is_auth_method.php
 * Type:     block
 * Name:     is_auth_method
 * Purpose:  display content to authenticated groups based on authentication to module/method
 * -------------------------------------------------------------
 */
function smarty_block_is_auth_method($params, $resource, &$smarty)
{
    if(empty($resource)) return;
    
    if(!empty($params['logged']) && !SESS_LOGGED) return false;
  
   	global $C_auth; 
	if(!is_object($C_auth)) return false;            
     
    if($C_auth->auth_method_by_name($params["module"], $params["method"]))
    	echo $resource; 
    else if(!empty($params["alt"]))
    	echo $params["alt"];
}
?>