<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.html_bool
 * Type:       function
 * Name:       html_bool 
 * -------------------------------------------------------------
 */
function smarty_function_html_bool($params, &$smarty)
{  
	extract($params); 
	
	if(empty($id)) $id = $field;
	if(empty($value)) $value = '1';
	
	$extra=' ';
	if(!empty($onclick)) $extra .= ' onClick="'.$onclick.'" ';
	
	if($default == $value) $extra .= 'checked ';
	    
	$ret = '<input type="checkbox" name="'.$field.'" id="'.$id.'" value="'.$value.'"'.$extra.'/>';
	 
	return $ret;
            	
}
?>