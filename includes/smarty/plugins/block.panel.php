<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.panel.php
 * Type:     block
 * Name:     panel
 * Purpose:  Creates a div/span element around html / text
 * -------------------------------------------------------------
 */
function smarty_block_panel($params, $resource, &$smarty)
{
 
	$type = 'div';
	$show = true;
	
	$_ignore['show'] = true;
	$_ignore['type'] = true;
	 
	$vals='';	
   	foreach($params as $_key => $_val) 
   		if(empty($_ignore["$_key"]))
   			$vals .= " $_key=\"$_val\"";
   		else
   			$$_key = $_val; 
	
	$pre = "<{$type}";
	if(!$show)
	$pre .= " style=\"display:none\"";
	$pre .= $vals;
	$pre .= ">\r\n";
	
    $pre .= $resource;
    
    $pre .= "\r\n</$type>";
    
    return $pre;
}
?>