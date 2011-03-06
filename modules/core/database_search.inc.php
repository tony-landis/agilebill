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
	
function CORE_database_search($VAR, &$construct, $type)
{
	$db = &DB();	 
	include_once(PATH_CORE . 'validate.inc.php');
	$validate = new CORE_validate;

	# set the search criteria array
	$arr = $VAR;

	# loop through the submitted field_names to get the WHERE statement
	$where_list = '';
	$i=0;
	while (list ($key, $value) = each ($arr))
	{
		if($i == 0)
		{
			if($value != '')
			{
				$pat = "^" . $construct->module . "_";
				if(preg_match('/'.$pat.'/i', $key))
				{
					$field = preg_replace('/'.$pat.'/i',"",$key);
					if(preg_match('/%/',$value))
					{
					   # do any data conversion for this field (date, encrypt, etc...)
					   if(isset($construct->field["$field"]["convert"]))
					   {
							$value = $validate->convert($field, $value, $construct->field["$field"]["convert"]);
					   }

					   $where_list .= " WHERE " . $field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
					   $i++;
					}
					else
					{
						# check if array
						if(is_array($value))
						{	
							for($i_arr=0; $i_arr < count($value); $i_arr++)
							{
							   if($value["$i_arr"] != '')
							   {
									# determine any field options (=, >, <, etc...)
									$f_opt = '=';
									$pat_field = $construct->module.'_'.$field;
									$VAR['field_option']["$pat_field"]["$i_arr"];
									if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
									{
									   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];
									   # error checking, safety precaution
									   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
										   $f_opt = '=';
									}

									# do any data conversion for this field (date, encrypt, etc...)
									if(isset($construct->field["$field"]["convert"]))
									{
										$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $construct->field["$field"]["convert"]);
									}


									if($i_arr == 0)
									{
										$where_list .= " WHERE " . $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
									}
									else
									{
										$where_list .= " AND " . $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
									}	
							   }
							}
						}
						else
						{
							$where_list .= " WHERE " . $field . " = " . $db->qstr($value, get_magic_quotes_gpc());
							$i++;
						}
					}
				}
			}
		}
		else
		{
			if($value != '')
			{
				$pat = "^" . $construct->module . "_";
				if(preg_match('/'.$pat.'/', $key))
				{
					$field = preg_replace('/'.$pat.'/i',"",$key);
					if(preg_match('/%/',$value))
					{
					   # do any data conversion for this field (date, encrypt, etc...)
					   if(isset($construct->field["$field"]["convert"]))
					   {
							$value = $validate->convert($field, $value, $construct->field["$field"]["convert"]);
					   }

					   $where_list .= " AND " . $field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
					   $i++;
					}
					else
					{
						# check if array
						if(is_array($value))
						{	
							for($i_arr=0; $i_arr < count($value); $i_arr++)
							{
							   if($value["$i_arr"] != '')
							   {
									# determine any field options (=, >, <, etc...)
									$f_opt = '=';
									$pat_field = $construct->module.'_'.$field;
									if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
									{
									   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];

									   # error checking, safety precaution
									   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
										   $f_opt = '=';
									}

									# do any data conversion for this field (date, encrypt, etc...)
									if(isset($construct->field["$field"]["convert"]))
									{
										$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $construct->field["$field"]["convert"]);
									}

									$where_list .= " AND " . $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
									$i++;
							   }
							}
						}
						else
						{		
						   $where_list .=  " AND " . $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
					}
				}
			}
		}
	}


	#### finalize the WHERE statement
	if($where_list == '')
	{
		$where_list .= ' WHERE ';
	}
	else
	{
		$where_list .= ' AND ';
	}


	# get limit type
	if(isset($VAR['limit']))
	{
		$limit = $VAR['limit'];
	}
	else
	{
		$limit = $construct->limit;
	}

	# get order by
	if(isset($VAR['order_by']))
	{
		$order_by = $VAR['order_by'];
	}
	else
	{
		$order_by = $construct->order_by;
	}

	### Get any addition fields to select:
	if(isset($construct->custom_EXP))
	{            	
		for($ei=0; $ei<count($construct->custom_EXP); $ei++)
		{
			if($ei == 0)
			$field_list = "," . $construct->custom_EXP[$ei]['field'];
		}
	}

	# generate the full query
	$q = "SELECT id".$field_list." FROM
		 ".AGILE_DB_PREFIX."$construct->table
		 $where_list
		 site_id = '" . DEFAULT_SITE . "'";

	$q_save = "SELECT %%fieldList%% FROM %%tableList%% ".$where_list." %%whereList%% ";



		$result = $db->Execute($q);


	//////////////// DEBUG ////
	 #echo "<PRE>$q</PRE>";
			 #exit;

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

	# get the result count:
	$results = $result->RecordCount();

	# get the first record id:
	if($results == 1)  $record_id = $result->fields['id'];

	### Run any custom validation on this result for
	### this module	
	if(isset($construct->custom_EXP))
	{
		$results = 0;
		while(!$result->EOF)
		{
			for($ei=0; $ei<count($construct->custom_EXP); $ei++)
			{
				$field = $construct->custom_EXP[$ei]["field"];
				$value = $construct->custom_EXP[$ei]["value"];
				if($result->fields["$field"] == $value)
				{
					//$result->MoveNext();
					$ei = count($construct->custom_EXP);
					$results++;            			
				}            	
			}
			$result->MoveNext();
		}
	}


	# define the DB vars as a Smarty accessible block
	global $smarty;

	# Create the definition for fast-forwarding to a single record:
	if ($results == 1 && !isset($construct->fast_forward))
	{
		$smarty->assign('record_id', $record_id);
	}

	# create the search record:
	if($results > 0)
	{
		# create the search record
		include_once(PATH_CORE   . 'search.inc.php');
		$search = new CORE_search;
		$arr['module'] 	= $construct->module;
		$arr['sql']	= $q_save;
		$arr['limit']  	= $limit;
		$arr['order_by']= $order_by;
		$arr['results']	= $results;
		$search->add($arr);

		# define the search id and other parameters for Smarty
		$smarty->assign('search_id', $search->id);

		# page:
		$smarty->assign('page', '1');

		# limit:
		$smarty->assign('limit', $limit);

		# order_by:
		$smarty->assign('order_by', $order_by);
	}


	# define the result count
	$smarty->assign('results', $results);

	if(isset($construct->trigger["$type"]))
	{
		include_once(PATH_CORE   . 'trigger.inc.php');
		$trigger    = new CORE_trigger;
		$trigger->trigger($construct->trigger["$type"], 1, $VAR);
	}
}
?>