<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     plugin
 * Purpose:  Displays & Populates an AB plugin template
 * -------------------------------------------------------------
 */
function smarty_function_plugin($params, &$smarty)
{	 	
	$conditions='';
	extract($params);  
	if(empty($type)) return;	
	if(empty($name)) return;
	if(empty($name_prefix)) $name_prefix='';
	
	if(!empty($data)) $smarty->assign('plugin', unserialize($data));
	
	// pass any other vars to smarty
	foreach($params as $var=>$val)  $smarty->assign($var,$val);
	
	// get full template file-path:
	switch($type) {
		case 'affiliate':
			$_tpl = "affiliate:plugin_{$name_prefix}{$name}";
			break;
		case 'checkout':
			$_tpl = "checkout_plugin:plugin_{$name_prefix}{$name}";
			break;
		case 'db_mapping':
			$_tpl = "db_mapping:group_map_{$name_prefix}{$name}";
			break;
		case 'import':
			$_tpl = "";	// todo
			break;
		case 'product':
			$_tpl = "product_plugin:plugin_{$name_prefix}{$name}";
			break;	 
		case 'provision':
			$_tpl = "host_provision_plugin:plugin_{$name_prefix}{$name}";
			break;
		case 'registrar':
			$_tpl = "host_registrar_plugin:plugin_{$name_prefix}{$name}";
			break;
		case 'whois':
			$_tpl = "host_whois_plugin:plugin_{$name_prefix}{$name}";
			break;
		case 'voip_did':
			$_tpl = "voip_did_plugin:config_{$name_prefix}{$name}";
			break;
	}								
		 
	// check if file exists:
	$_template_full = PATH_THEMES.DEF_THEME_N."/blocks/". ereg_replace(":", "/", $_tpl).".tpl";
 
	if(!is_file($_template_full)) {
		$_template_full = PATH_THEMES."default/blocks/". ereg_replace(":", "/", $_tpl).".tpl";
		if(!is_file($_template_full)) {
			if($debug) echo "Error loading plugin template: $_template_full";
			return;	
		}
	}

	// load file
	$smarty->display("file:$_template_full");
	 
}
?>