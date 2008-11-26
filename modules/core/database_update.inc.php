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
	
function CORE_database_update($VAR, &$construct, $type)
{
	global $C_translate;

	# set the field list for this method:
	$arr = $construct->method["$type"];		

	# define the validation class
	include_once(PATH_CORE . 'validate.inc.php');
	$validate = new CORE_validate;		

	$construct->validated = true;	

	# define this record id
	$id = $VAR[$construct->module . '_id'];

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

		# check if the conversion type required is not one ignored on updates:
		$ignore_con = false;
		$ignore_convert = Array('sha', 'md5','rc5','crypt');		
		for ($ic=0; $ic < count($ignore_convert); $ic++)
		{
			if (isset($construct->field["$value"]["convert"]))
				if ($construct->field["$value"]["convert"] == $ignore_convert[$ic]) $ignore_con = true;
		}

		if(!$ignore_con)
		{ 			
			# check if this value is unique
			if(isset($construct->field["$value"]["unique"]))
			{
				if(isset($VAR["$field_var"]))
				{
					if(!$validate->validate_unique($construct->table, $field_name, $id, $VAR["$field_var"]))
					{
						$construct->validated = false;
						$construct->val_error[] =  array('field' 	=> $construct->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),							# translate
													'error' 		=> $C_translate->translate('validate_unique',"", ""));	 				
					}
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
							$construct->val_error[] =  array('field' 	=> $construct->module . '_' . $field_name,
														'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
														'error' 		=> $validate->error["$field_name"] );								
						}					
					}
					else
					{
						$construct->validated = false;
						$construct->val_error[] =  array('field' 	=> $construct->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
													'error' 		=> $C_translate->translate('validate_any',"", "")); 	
					}
				}
				else
				{
					$construct->validated = false;
					$construct->val_error[] =  array('field' 	=> $construct->module. '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $construct->module, ""),
												'error' 		=> $C_translate->translate('validate_any',"", "")); 		 																		
				}
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

		# change the page to be loaded
		global $VAR;
		$VAR['_page'] = $construct->module . ':view';

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
		$db = &DB();
		$field_list = '';
		$i = 0;
		reset($arr);
		while (list ($key, $value) = each ($arr))
		{
			# get the field value
			$field_var  	= $construct->module . '_' . $value;
			$field_name 	= $value;

			if(isset($VAR["$field_var"]) && $VAR["$field_var"] != 'IGNORE-ARRAY-VALUE') 
			{ 
				# check if html allowed:
				if(@$construct->field["$value"]["html"] != 1 && !is_array($VAR["$field_var"]))
					$insert_value = htmlspecialchars($VAR["$field_var"]);
				else
					$insert_value = $VAR["$field_var"];

				# perform data conversions
				if(isset($construct->field["$value"]["convert"] ))
				$insert_value = $validate->convert($field_name, $insert_value, $construct->field["$value"]["convert"]);

				if($i == 0) 			
					$field_list .= $value . "=" . $db->qstr($insert_value, get_magic_quotes_gpc());
				else
					$field_list .= ", " . $value . "=" . $db->qstr($insert_value, get_magic_quotes_gpc());
				$i++;
			}  
			elseif ( @$construct->field["$value"]["convert"] == "array" && @$VAR["$field_var"] != 'IGNORE-ARRAY-VALUE') 
			{
				# Handle blank array string...
				$insert_value = serialize(Array(""));
				if($i == 0) 			
					$field_list .= $value . "=" . $db->qstr($insert_value, get_magic_quotes_gpc());
				else
					$field_list .= ", " . $value . "=" . $db->qstr($insert_value, get_magic_quotes_gpc());
				$i++;						
			}				
		}

		# generate the full query
		$q = "UPDATE " . AGILE_DB_PREFIX . "$construct->table SET
				$field_list
				WHERE
				id 		= ". $db->qstr($id) ."
				AND
				site_id = " . $db->qstr(DEFAULT_SITE);
		# execute the query
		$db = &DB();
		$result = $db->Execute($q);

		# echo "<PRE>$q</PRE>";

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','update', $db->ErrorMsg());

			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 0, $VAR);
			}
			return false;         	
		}
		else
		{ 
			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 1, $VAR);
			}
			return true;
		}
	}			
}
?>