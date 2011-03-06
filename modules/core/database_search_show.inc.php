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
	
function CORE_database_search_show($VAR, &$construct, $type)
{

	# set the field list for this method:
	$arr = $construct->method[$type];
	$field_list = '';
	$i=0;
	while (list ($key, $value) = each ($arr))
	{
		if($i == 0)
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= AGILE_DB_PREFIX . $construct->table . "." . $value;

			// determine if this record is linked to another table/field
			if($construct->field[$value]["asso_table"] != "")
			{
				$construct->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
			}
		}
		else
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

			// determine if this record is linked to another table/field
			if($construct->field[$value]["asso_table"] != "")
			{
				$construct->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
			}
		}
		$i++;
	}


	# get the search details:
	if(isset($VAR['search_id']))
	{
		include_once(PATH_CORE   . 'search.inc.php');
		$search = new CORE_search;
		$search->get($VAR['search_id']);
	}
	else
	{
		# invalid search!
		echo '<BR> The search terms submitted were invalid!<BR>';       # translate... # alert

		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 0, $VAR);
		}
	}

	# Check that this search has not been taken over by another account
	if ($search->session != SESS && $search->account != SESS_ACCOUNT) {
		global $C_debug;
		$C_debug->alert('You are not authorized to view this search!');
		return false;
	}

	# get the sort order details:
	if(isset($VAR['order_by']) && $VAR['order_by'] != "")
	{
		$order_by = ' ORDER BY ' . $VAR['order_by'];
		$smarty_order =  $VAR['order_by'];
	}
	else
	{
		$order_by = ' ORDER BY ' . $construct->order_by;
		$smarty_order =  $search->order_by;
	}


	# determine the sort order
	if(isset($VAR['desc'])) {
		$order_by .= ' DESC';
		$smarty_sort = 'desc=';
	} else if(isset($VAR['asc'])) {
		$order_by .= ' ASC';
		$smarty_sort = 'asc=';
	} else {
		if (!preg_match('/date/i',$smarty_order))      {
			$order_by .= ' ASC';
			$smarty_sort = 'asc=';
		} else  {
			$order_by .= ' DESC';
			$smarty_sort = 'desc=';
		}
	} 


	# generate the full query
	$db = &DB();  
	$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
	$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
	$q = preg_replace("/%%whereList%%/i", "", $q);
	$q .= " site_id = '" . DEFAULT_SITE . "'";
	$q .= $order_by;

	/////////////////////// 

	# determine the offset & limit
	$current_page=1;
	$offset=-1;
	if (!empty($VAR['page'])) $current_page = $VAR['page'];
	if (empty($search->limit)) $search->limit=25; 
	if($current_page>1) $offset = (($current_page * $search->limit) - $search->limit);            
	$result = $db->SelectLimit($q, $search->limit, $offset);

	# error reporting
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('database.inc.php','search', $db->ErrorMsg());

		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 0, $VAR);
		}
		return;                    	        	
	}


	### Put the results into a smarty accessable array
	### Run any custom validation on this result for
	### this module	
	if(isset($construct->custom_EXP))
	{
		$i=0;
		$class_name = TRUE;
		$results = 0;
		while(!$result->EOF)
		{
			for($ei=0; $ei<count($construct->custom_EXP); $ei++)
			{
				$field = $construct->custom_EXP[$ei]["field"];
				$value = $construct->custom_EXP[$ei]["value"];
				if($result->fields["$field"] == $value)
				{            				
					$smart[$i] = $result->fields;

					if($class_name)
					{
						$smart[$i]['_C'] = 'row1';
						$class_name = FALSE;
					} else {
						$smart[$i]['_C'] = 'row2';
						$class_name = TRUE;
					}            			   	
					$i++;            				
					$ei = count($construct->custom_EXP);
					$results++;            			
				}            	
			}        				
			$result->MoveNext();
		}
	}
	else
	{			
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF) {
			$smart[$i] = $result->fields;

			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}
			$result->MoveNext();
			$i++;
		}
	}

	# get any linked fields
	if($i > 0)  {
		$db_join = new CORE_database;
		$construct->result = $db_join->join_fields($smart, $construct->linked);
	} else {
		$construct->result = $smart;
	}  

	# get the result count:
	$results = $result->RecordCount();

	# define the DB vars as a Smarty accessible block
	global $smarty;

	# define the results
	$smarty->assign($construct->table, $construct->result);
	$smarty->assign('page',		$VAR['page']);
	$smarty->assign('order',	$smarty_order);
	$smarty->assign('sort',		$smarty_sort);
	$smarty->assign('limit',	$search->limit);
	$smarty->assign('search_id',$search->id);
	$smarty->assign('results', 	$search->results);

	# get the total pages for this search:
	if (empty($search->limit))
		$construct->pages = 1;
	else
		$construct->pages = intval($search->results / $search->limit);
	if ($search->results % $search->limit) $construct->pages++;

	# total pages
	$smarty->assign('pages', 	$construct->pages);

	# current page
	$smarty->assign('page', 	$current_page);
	$page_arr = '';
	for($i=0; $i <= $construct->pages; $i++)
		if ($construct->page != $i) 	$page_arr[] = $i;

	# page array for menu
	$smarty->assign('page_arr',	$page_arr);

	if(isset($construct->trigger["$type"]))
	{
		include_once(PATH_CORE   . 'trigger.inc.php');
		$trigger    = new CORE_trigger;
		$trigger->trigger($construct->trigger["$type"], 1, $VAR);
	} 
	return $construct->result;            	
}
?>