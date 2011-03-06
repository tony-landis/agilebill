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
	
//$list = new CORE_list;
//
//# get version
//$fp = @fopen(PATH_AGILE.'Version.txt', "r");
//if($fp) {
//	$ver = fread($fp, 255);
//	fclose($fp);
//} else {		
//	$ver = "SVN";		
//}
//
//# get latest version
//$fp = fopen('http://agileco.com/Version.txt', "r");
//$abv = fread($fp, 255);
//fclose($fp);
//# get encoding version
//$tmp = file_get_contents(PATH_AGILE.'index.php');
//
//
//
//# get installed optional modules: 
//$modules   = Array ('affiliate'  	=> Array ('affiliate', 'campaign', 'affiliate_commission', 'affiliate_template'),
//					'charge'	 	=> Array ('charge'),
//					'db_mapping' 	=> Array ('db_mapping'),
//					'email_queue'	=> Array ('email_queue'),
//					'file' 		 	=> Array ('file', 'file_category'),
//					'faq'			=> Array ('faq','faq_translate', 'faq_category'),
//					'htaccess'   	=> Array ('htaccess', 'htaccess_dir', 'htaccess_exclude'),
//					'import'		=> Array ('import'),
//					'hosting'		=> Array ('host_server', 'host_registrar_plugin', 'host_tld'),
//					'ticket'     	=> Array ('ticket', 'ticket_department', 'ticket_message'),  
//					'login_share'	=> Array ('login_share'), 
//					'static_page'	=> Array ('static_page', 'static_page_category','static_page_translate') );	
//
//foreach($modules as $name => $m) {
//	foreach($m as $module)  {  
//		if( empty($avail["$name"]) && $list->is_installed( $module ) ) {
//			$avail["$name"] = true; 
//			$module_arr[] = $name;
//		}
//	}
//}   
//
//# set smarty vars
//global $smarty;
//$smarty->assign('version',$ver);
//$smarty->assign('ab_version',$abv);
//$smarty->assign('encoding_version',$enc);
//$smarty->assign('modules',@$module_arr);
?>
