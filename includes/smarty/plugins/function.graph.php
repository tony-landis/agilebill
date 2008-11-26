<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     graph
 * Purpose:  displays a specific flash graph
 * -------------------------------------------------------------
 */
function smarty_function_graph($params, &$this)
{
	$width = 500;
	$height = 300;
	$color = "FFFFFF";
	$transparency = true;
	
    extract($params);       
    if(empty($module) || empty($method)) return false;   
    
    include_once (PATH_INCLUDES .'charts/charts.php');
    
    if($title) {
    	global $C_translate; 
    	$trans = $C_translate->translate($title,$module);
    	if(!empty($trans)) $title = $trans;
    }    
     
    if($show === false) $display = 'style="display:none"';
         
	global $VAR;
	$vars = '';
	foreach($VAR as $a => $b) $vars .= "&{$a}={$b}";
     
	
	echo "<div id=\"{$id}\" class=\"graph\" $display>";
	echo "<h3>$title</h3>";
	echo InsertChart ( "includes/charts/charts.swf", "includes/charts/charts_library", URL."ajax.php?do[]={$module}:{$method}{$vars}", $width, $height, $color, $transparency);    	
    echo "</div>";
} 
?>