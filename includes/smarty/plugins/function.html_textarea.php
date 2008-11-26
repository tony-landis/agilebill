<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.html_textarea.php
 * Type:       function
 * Name:       html_textarea
 * Version:    1.0 
 * -------------------------------------------------------------
 */
function smarty_function_html_textarea($params, &$smarty)
{ 
	$action = '';    
	$cols = '50';
	$rows = '4';
	
   	foreach($params as $_key => $_val) $$_key = $_val;  
   	
   	if(empty($id)) $id = $name;   	
   	if($limit) $onKeyPress = " onKeyPress=\"textarea_check_len('{$id}','{$limit}')\"";
      
   	$html = "<textarea id=\"{$id}\" name=\"{$name}\" cols=\"{$cols}\" rows=\"{$rows}\"{$onKeyPress}>{$default}</textarea>";
   	
   	if($onKeyPress) 
   	{
   		$html .= "<div>Remaining Characters: <span id=\"{$id}_remain\">{$limit}</span></div> ".
		"<script language=\"javascript\">function textarea_check_len(element,limit) { ".  
	   	"var len = document.getElementById(element).value.length; ".
	   	"var remain = limit; ".
	   	"if(limit >= len) remain = limit - (len + 1) ; ".
	   	"else remain = 0; ".
	   	"$(element+'_remain').innerHTML = remain; ".
		"} textarea_check_len('{$id}','{$limit}'); </script>";
   	}
 
   	return $html; 
}
?>