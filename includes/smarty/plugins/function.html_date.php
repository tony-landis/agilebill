<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.html_date
 * Type:       function
 * Name:       html_date 
 * -------------------------------------------------------------
 */
function smarty_function_html_date($params, &$smarty)
{  
	extract($params); 

	if($disabled) return '<input type="text" id="'.$id.'" name="'.$field.'" value="'.$default.'" disabled />';
	
	# set the date to current date if 'now' is set as default
	if($default == 'now') $default = date(UNIX_DATE_FORMAT);
	 
	if(empty($id)) $id = $field;
	if(empty($trigger)) $trigger = $id;
	
	$ret =  '<input type="text" id="'.$id.'" name="'.$field.'" value="'.$default.'" />';
	$ret .= '<script type="text/javascript"> Calendar.setup({inputField: "'.$id.'", ifFormat: "'.DEFAULT_DATE_FORMAT.'", button: "'.$trigger.'"}); </script> ';
	return $ret;
            	
}
?>