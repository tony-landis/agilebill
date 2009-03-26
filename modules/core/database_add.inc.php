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
 * @version 1.4.94
 */
	
function CORE_database_add($VAR, $construct, $type)
{		
	global $C_translate;

	# set the field list for this method:
	$arr = $construct->method["$type"];

	# define the validation class
	include_once(PATH_CORE . 'validate.inc.php');
	$validate = new CORE_validate;
	$construct->validated = true;


	####################################################################
	# loop through the field list to validate the required fields
	####################################################################

	while (list ($key, $value) = each ($arr))
	{
		# get the field value
		$field_var  	= $construct->module . '_' . $value;
		$field_name 	= $value;
		$construct->validate = true;

		####################################################################
		# perform any field validation...
		####################################################################

		# check if this value is unique
		if(isset($construct->field["$value"]["unique"]) && isset($VAR["$field_var"]))
		{
			if(!$validate->validate_unique($construct->table, $field_name, "record_id", $VAR["$field_var"]))
			{
				$construct->validated = false;
				$construct->val_error[] =  array('field' 		=> $construct->table . '_' . $field_name,
											'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),							# translate
											'error' 		=> $C_translate->translate('validate_unique',"", ""));	 				
			}
		}

		# check if the submitted value meets the specifed requirements
		if(isset($construct->field["$value"]["validate"]))
		{
			if(isset($VAR["$field_var"]))
			{
				if($VAR["$field_var"] != '')
				{
					if(!$validate->validate($field_name, $construct->field["$value"], $VAR["$field_var"], $construct->field["$value"]["validate"]))
					{
						$construct->validated = false;
						$construct->val_error[] =  array('field' 		=> $construct->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
													'error' 		=> $validate->error["$field_name"] );								
					}					
				}
				else
				{
					$construct->validated = false;
					$construct->val_error[] =  array('field' 		=> $construct->module . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
												'error' 		=> $C_translate->translate('validate_any',"", "")); 	
				}
			}
			else
			{
				$construct->validated = false;
				$construct->val_error[] =  array('field' 		=> $construct->module . '_' . $field_name,
											'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
											'error' 		=> $C_translate->translate('validate_any',"", "")); 		 																		
			}
		}
	}


	####################################################################
	# If validation was failed, skip the db insert &
	# set the errors & origonal fields as Smarty objects,
	# and change the page to be loaded.
	####################################################################

	if(!$construct->validated)
	{
		global $smarty;	

		# set the errors as a Smarty Object
		$smarty->assign('form_validation', $construct->val_error);	

		# set the page to be loaded
		if(!defined("FORCE_PAGE"))
		{
		   define('FORCE_PAGE', $VAR['_page_current']);
		}

		# define any triggers				
		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 0, $VAR);
		}

		# strip slashes
		global $C_vars;
		$C_vars->strip_slashes_all();
		return false;
	}
	else
	{                		
		# begin the new database class:
		$db = &DB();

		# loop through the field list to create the sql queries
		$field_list = '';
		$i = 0;
		reset($arr);
		while (list ($key, $value) = each ($arr))
		{
			# get the field value
			$field_var  	= $construct->module . '_' . $value;
			$field_name 	= $value;			
			if(isset($VAR["$field_var"]))
			{ 
				# check if html allowed:
				if(@$construct->field["$value"]["html"] != 1 && !is_array($VAR["$field_var"]))
										{
										  $insert_value = htmlspecialchars($VAR["$field_var"]);
				} else {
										  $insert_value = $VAR["$field_var"];
										}

				# perform data conversions
				if(isset( $construct->field["$value"]["convert"] ))
				$insert_value = $validate->convert($field_name, $insert_value, $construct->field["$value"]["convert"]);

				# create the sql statement
				if(!is_null($insert_value))
				  $field_list .= ", " . $value . "=" . $db->qstr($insert_value, get_magic_quotes_gpc());
			}					
		}

		# add a comma before the site_id if needed
		if($field_list != '')
		{
			$field_list .= ',';
		}

		# determine the record id:
		$construct->record_id = $db->GenID(AGILE_DB_PREFIX . "" . $construct->table.'_id');

		# define the new ID as a constant
		define(strtoupper('NEW_RECORD_'.$construct->table.'_ID'), $construct->record_id);

		# generate the full query
		$q = "INSERT INTO ".AGILE_DB_PREFIX."$construct->table
				SET
				id = ". $db->qstr($construct->record_id)."
				$field_list
				site_id = " . $db->qstr(DEFAULT_SITE);

		# execute the query
		$result = $db->Execute($q);

		## echo $q;

		# error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','add', $db->ErrorMsg());

			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 0, $VAR);
				return false;
			}
		}

		# define any triggers:
		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 1, $VAR);
		}

		global $VAR;
		$VAR["id"] = $construct->record_id;
		@$redirect_page = $VAR['_page'];
		if(isset($VAR["_escape"]) || isset($VAR["_escape_next"])) $_escape = '&_escape=1&_escape_next=1';
		define('REDIRECT_PAGE', '?_page=' . $redirect_page . '&id=' . $construct->record_id . '' . @$_escape);
		return $construct->record_id;
	}
}
?>