<?php
		
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */
	
function list_menu_files($id, $name, $default, $path, $pre, $ext, $class)
{
	global $C_translate;
	if($path     == 'product_cat')      $path = PATH_THEMES  . '' . DEF_THEME_N . '/blocks/product_cat/';
	elseif($path == 'whois_plugin')     $path = PATH_PLUGINS . '/whois/';
	elseif($path == 'product')     		$path = PATH_PLUGINS . '/product/';
	elseif($path == 'e911')     		$path = PATH_PLUGINS . '/e911/';
	elseif($path == 'provision_plugin') $path = PATH_PLUGINS . '/provision/';
	elseif($path == 'affiliate_plugin') $path = PATH_PLUGINS . '/affiliate/';
	elseif($path == 'checkout_plugin')  $path = PATH_PLUGINS . '/checkout/';
	elseif($path == 'theme') 			$path = PATH_THEMES;
	elseif($path == 'language') 		$path = PATH_LANGUAGE . '/core/';

	$count = 0;			
	chdir($path);
	$dir = opendir($path);
	while ($file_name = readdir($dir))
	{
		$display = true;
		if($file_name != '..' && $file_name != '.')
		{
			if(!empty($ext))
			{
				$cute = preg_replace('/'.$ext.'$/i', "", $file_name);
				if(!preg_match('/'.$ext.'$/', $file_name)) $display = false;
			}
			if(!empty($pre))
			{
				$cute = preg_replace('/^'.$pre .'/', "", $cute);
				if(!preg_match('/^'.$pre.'/', $file_name))  $display = false;
			}
			if($display)
			{
				$arr[]  = $cute;
				$cute = preg_replace("/_/"," ",$cute);
				$cute = preg_replace("/-/"," ",$cute);
				$arrc[] = $cute;
				$count++;
			}
		}
	}
	$return = '<select id="'.$name.'_'. $id.'" name="'. $name .'" value="'.$default.'">';			
	if($id == "all" || $default == "all")
		$return .= '<option value="" selected></option>';	
	$i = 0;
	for($i=0; $i<$count; $i++)
	{		 			
		$return .= '<option value="' . $arr[$i] . '"';
		if($default == $arr[$i])
			$return .= "selected";
		$return .= '>' . $arrc[$i] . '</option>
		';				
	}								
	if($count==0)
		$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
	$return .= '</select>';
	echo $return;
}
?>