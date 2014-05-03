<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu_files
 * Purpose:  Get list of files from the filesystem
 * -------------------------------------------------------------
 */
function smarty_function_html_menu_files($params, &$smarty)
{	 	
	extract($params);  
	if(empty($field)) $field = $name;	
	if(empty($path)) $path = $dir;
	if(empty($id)) $id = $field;
		
	 
	if($path     == 'product_cat') 		{	$path = PATH_THEMES  . '' . DEF_THEME_N . '/blocks/product_cat/'; }
	elseif($path == 'product')   		{	$path = PATH_PLUGINS . '/product/'; }
	elseif($path == 'theme') 			{	$path = PATH_THEMES; }
	elseif($path == 'static_template')	{	$path = PATH_THEMES  . '/default/blocks/static_page/'; 	$ext = "_template.tpl"; $cap=1; }
	elseif($path == 'language') 		{	$path = PATH_LANGUAGE. '/core/'; 	$ext = "_core.xml"; $cap=1; }
	elseif($path == 'whois_plugin') 	{ 	$path = PATH_PLUGINS . '/whois/'; 	  }
	elseif($path == 'provision_plugin')	{	$path = PATH_PLUGINS . '/provision/'; }
	elseif($path == 'affiliate_plugin')	{	$path = PATH_PLUGINS . '/affiliate/'; }
	elseif($path == 'checkout_plugin') 	{ 	$path = PATH_PLUGINS . '/checkout/';  }
	elseif($path == 'voip_did')			{	$path = PATH_PLUGINS . '/voip_did/';  $ext = ".php"; }
	elseif($path == 'invoice_pdf')		{	$path = PATH_INCLUDES. '/pdf/'; $ext = ".inc.php"; $pre = "pdf_invoice_"; }
	 
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
				if(!preg_match('/'.$ext.'$/i', $file_name)) $display = false;
			}
			if(!empty($pre))
			{
				$cute = preg_replace('/^'.$pre.'/i', "", $cute);
				if(!preg_match('/^'.$pre.'/', $file_name))  $display = false;
			}
			if($display)
			{
				$arr[]  = $cute;
				$cute = preg_replace("/_/"," ",$cute);
				$cute = preg_replace("/-/"," ",$cute);
				
				if($cap==1) $cute = ucfirst(strtolower($cute));
				elseif($cap==2) $cute = ucwords(strtolower($cute));
				elseif($cap) $cute = strtoupper($cute);
				
				$arrc[] = $cute;
				$count++;
			}
		}
	}
	$return = '<select id="'.$id.'" name="'. $field .'" value="'.$default.'">';
	if($default == "all")
	$return .= '<option value="" selected></option>';
	$i = 0;
	for($i=0; $i<$count; $i++)
	{
		$return .= '<option value="' . $arr[$i] . '"';
		if($default == $arr[$i])
		$return .= "selected";
		$return .= '>' . $arrc[$i] . '</option>';				
	}
	if($count==0 && $default != 'all') $return .= '<option value=""></option>';
	$return .= '</select>';
	
	echo $return;
} 
?>
