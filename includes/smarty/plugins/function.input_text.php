<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.input_text.php
 * Type:     function
 * Name:     input_text
 * Purpose:  display a text form with the proper css & onchange settings
 * -------------------------------------------------------------
 */
function smarty_function_input_text($params, &$this)
{  
	extract($params);
	$c_class = $class ? $class : 'form_field';
	$s_size = $size ? $size : '12';
	$s_id   = $id ? $id : $name;
	
	echo "<input type=\"text\" name=\"{$name}\" id=\"{$id}\" class=\"{$c_class}\" value=\"{$value}\" size=\"{$s_size}\" onFocus=\"class_change(this.id,'form_field_focus')\" onBlur=\"class_change(this.id,'form_field')\">";
}  
?>
