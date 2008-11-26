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
	
function CORE_database_search_form($VAR, $construct, $type)
{
	global $C_translate, $smarty;		

	# set the field list for this method:
	$arr = $construct->method[$type];

	# loop through the field list to create the order_by list
	$field_list = '';
	$i = 0;
	while (list ($key, $value) = each ($arr))
	{
		$field_list["$i"]['translate']      = $C_translate->translate('field_' . $value, $construct->module, "");											
		$field_list["$i"]['field']  = $value;
		$i++;
	}

	# define the field list as a Smarty accessible array
	$smarty->assign($construct->module, $field_list);

	# define the default ORDER BY field
	$smarty->assign($construct->module . '_order_by', $construct->order_by);

	# define the default LIMIT count
	$smarty->assign($construct->module . '_limit', $construct->limit);

	# define the recent search menu & javascript
	include_once(PATH_CORE   . 'search.inc.php');
	$search = new CORE_search;

	# build the RECENT SEARCH menu & JS		
	$search->build_recent($construct->module);

	# send the RECENT SEARCH menu to Smarty
	$smarty->assign($construct->module . "_recent_menu", $search->recent_menu);

	# send the finished RECENT SEARCH JavaScript to Smarty
	$smarty->assign($construct->module . "_recent_js", $search->recent_js);	

	# build the SAVED SEARCH menu & JS
	$search->build_saved($construct->module);

	# send the SAVED SEARCH menu to Smarty
	$smarty->assign($construct->module . "_saved_menu", $search->saved_menu);

	# send the finished SAVED SEARCH JavaScript to Smarty
	$smarty->assign($construct->module . "_saved_js", $search->saved_js);	 	
}
?>