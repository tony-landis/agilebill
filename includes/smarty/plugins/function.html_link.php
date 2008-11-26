<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.html_link.php
 * Type:       function
 * Name:       html_link
 * Version:    1.0 
 * -------------------------------------------------------------
 */
function smarty_function_html_link($params, &$smarty)
{  
 
	$name 	= 'submit';  
	$module = 'CORE';
	
	$_ignore['show'] = true;
	$_ignore['hide'] = true;
	$_ignore['name'] = true;
	 
	# Get the values passed...
	$vals='';	
   	foreach($params as $_key => $_val) 
   		if(empty($_ignore["$_key"]))
   			$vals .= " $_key=\"$_val\"";
   		else
   			$$_key = $_val; 
   				 
   	foreach($params as $_key => $_val) $$_key = $_val; 
   	    
 
   	# change state(s) (hide)
   	$t = 0;
   	if($hide) {
   		$e = 'Fade'; 
   		if(ereg(',', $hide)) $hides = explode(',', $hide); else $hides = Array($hide); 
   		foreach($hides as $element) {
   			if(ereg('\|', $element)) {
   				$el = explode('|', $element);  
   				$action .= " new Effect.{$el[2]}('{$el[0]}', {duration: {$el[1]}}); ";
   			} else {
   				$action .= " $('{$element}').style.display='none'; ";
   			}
   		}
   	}
   	
   	# change state(s) (show)
   	if($show) {
   		$e = 'Appear';
   		if(ereg(',', $show)) $shows = explode(',', $show); else $shows = Array($show);
   		foreach($shows as $element) {
   			if(ereg('\|', $element)) {
   				$el = explode('|', $element);  
   				$action .= " new Effect.{$el[2]}('{$el[0]}', {duration: {$el[1]}}); ";
   			} else {
   				$action .= " $('{$element}').style.display='block'; ";
   			}
   		}
   	}   	
   	
   	# translate name
   	global $C_translate;  
   	$trans = $C_translate->translate($name, $module);
   	if(!empty($trans)) $name = $trans;
   	   	
   	if(empty($link)) $link = "#";
   	
   	$html = "<a href=\"$link\"";
   	$html .= $vals;
   	if($action) $html .= " onclick=\"{$action}\">";
   	$html .= $name;
   	$html .= "</a>";
 
   	return $html; 
}
?>