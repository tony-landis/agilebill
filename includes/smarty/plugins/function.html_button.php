<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.html_button.php
 * Type:       function
 * Name:       html_checkboxes
 * Version:    1.0 
 * -------------------------------------------------------------
 */
function smarty_function_html_button($params, &$smarty)
{  
	$allowdclick = true;
	$name 	= 'submit';  
	$module = 'CORE'; 
   	foreach($params as $_key => $_val) $$_key = $_val; 
   	  	
   	# translate name
   	global $C_translate;  
   	$trans = $C_translate->translate($name, $module);
   	if(!empty($trans)) $name = $trans;
     
   	# allow multiple clicks?
   	if(!$allowdclick) $action = "this.disabled=true; this.value='". $C_translate->translate('processing') ."';" . $action;
   	
   	# change state(s)
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
   	 
   	$html = "<input id=\"{$id}\" type=\"submit\" name=\"{$name}\" value=\"{$name}\" onclick=\"{$action} \">";
 
   	return $html; 
}
?>