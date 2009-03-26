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
	
// generate the admin menu
function auth_generate_admin_menu($menu_obj)
{                   
	$menu_obj->auth_update();
	global $C_translate, $smarty, $C_list;

	$i=1;
	$js='';
	$arr  = $menu_obj->module;
	$arr2 = $menu_obj->module;

	// loop through the modules
	while (list($module, $val) = each ($arr)) {
		if(!empty($val[2])) {
			if($val[1] == $val[0] || empty($val[0]) || empty($val[1])) 
			{
				$module_name = $C_translate->translate('menu',$module,'');
				$parent = $val[0];
				$module_id = $val[0];
				$module_arr[$i]["name"] = $module_name;
				$module_arr[$i]["module"] = $module;

				// loop through the methods
				while (list($method, $meth_arr) = each ($arr[$module])) {
					if(gettype($meth_arr) == 'array' && !empty($meth_arr[1])) {
						$method_name = $C_translate->translate('menu_'.$method,$module,'');
						if(empty($meth_arr[2]))
							$page = $module.':'.$method;
						else
							$page = preg_replace('/%%/', $module, $meth_arr[2]);

						$module_arr[$i]["methods"][] = Array('name' => $method_name, 'page' => $page);

					}
				}

				// Loop through the sub-modules:
				reset($arr2);
				$ii=0;
				while (list($module, $val) = each ($arr2)) {
					if(!empty($val[2])) {
						if($val[1] == $parent && $module_id != $val[0]) 
						{    
								$module_name = $C_translate->translate('menu',$module,'');
								$module_arr[$i]["sub_name"][$ii] = $module_name;

								// loop through the methods
								while (list($method, $meth_arr) = each ($arr2[$module])) {
									if(gettype($meth_arr) == 'array' && !empty($meth_arr[1])) {
										$method_name = $C_translate->translate('menu_'.$method,$module,'');
										if(empty($meth_arr[2]))
											$page = $module.':'.$method;
										else
											$page = preg_replace('/%%/', $module, $meth_arr[2]);
										$module_arr[$i]["sub_methods"][$ii][] = Array('name' => $method_name, 'page' => $page);
									}
								}
								$ii++;       
							}
						}
					}
				$i++;

			} 
		}  
	}




	 // Generate the main modules:
	$js = '';
	$js .= ".|Overview|javascript:openUrl('?_page=core:admin');\n";
	$js .= ".|Exit Administration|javascript:exitAdmin();\n";
	$js .= ".|Misc\n";
	$js .= "..|Documentation|http://agilebill.com/documentation|||mainFrame\n";
	$js .= "..|Agileco News|http://forum.agileco.com/forumdisplay.php?f=26|||mainFrame\n";
	$js .= "..|Version Check|?_page=module:upgrade|||mainFrame\n";

	 for($i=1; $i<=count($module_arr); $i++)
	 {
		$name = $module_arr[$i]['name'];
		$js .= ".|{$name}\n";

		// Generate the main methods:
		for($ii=0; $ii<count($module_arr[$i]['methods']); $ii++) {
			$name = $module_arr[$i]['methods'][$ii]['name'];
			$page  = $module_arr[$i]['methods'][$ii]['page'];

			$js .= "..|{$name}|javascript:openUrl('?_page={$page}')\n";
		}

		// Generate the sub modules:
		for($ii=0; $ii<count(@$module_arr[$i]['sub_name']); $ii++) {
			$name = $module_arr[$i]['sub_name'][$ii];
			$js .= "..|{$name}|#\n";
			// Generate the sub methods:
			for($iii=0; $iii<count($module_arr[$i]['sub_methods'][$ii]); $iii++)
			{
				$name = $module_arr[$i]['sub_methods'][$ii][$iii]['name'];
				$page = $module_arr[$i]['sub_methods'][$ii][$iii]['page'];
				$js .= "...|{$name}|javascript:openUrl('?_page={$page}')\n";
			}
		}
	 } 

	# set the dates for the quicksearch
	$smarty->assign('today_start', $C_list->date(mktime(0,0,0,date("m"),date("d"), date("Y"))));
	$smarty->assign('week_start',  $C_list->date(mktime(0,0,0,date("m"),date("d")-7, date("Y"))));
	$smarty->assign('month_start', $C_list->date(mktime(0,0,0,date("m"),1, date("Y"))));

	# Generate the menu
	require_once(PATH_INCLUDES."phplayers/PHPLIB.php");
	require_once(PATH_INCLUDES."phplayers/layersmenu-common.inc.php");
	require_once(PATH_INCLUDES."phplayers/treemenu.inc.php");

	// unstoppable agileco logo ;)
	echo '<img src="http://www.agileco.com/images/poweredby.gif" border="0" style="position: absolute; top: 8px; left: 45px;"/>';

	$mnu = new TreeMenu();
	$mnu->setMenuStructureString($js);
	$mnu->setIconsize(16, 16);
	$mnu->parseStructureForMenu('treemenu1');
	$mnu->setTreemenuTheme("kde_");
	return $mnu->newTreeMenu('treemenu1'); 
	return $js;

}
?>
