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
	
function CORE_database_view($VAR, &$construct, $type)
{
	$db = &DB();

	# set the field list for this method:
	$arr = $construct->method[$type];

	# loop through the field list to create the sql queries
	$field_list = '';
	$i=0;
	while (list ($key, $value) = each ($arr))
	{
		if($i == 0)
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= $value;
		}
		else
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= "," . $value;
		}
		$i++;
	}

	if(isset($VAR["id"]))
	{
		$id = explode(',',$VAR["id"]);
		for($i=0; $i<count($id); $i++)
		{
			if($id[$i] != '')
			{
				if($i == 0)
				{				 			
					$id_list .= " id = " .$db->qstr($id[$i])." ";
					$ii++;
				}
				else
				{
					$id_list .= " OR id = " .$db->qstr($id[$i]). " ";
					$ii++;
				}	
			}
		}
	}

	if($ii>0)
	{
		# generate the full query
		$q = "SELECT
			  $field_list
			  FROM
			  ".AGILE_DB_PREFIX."$construct->table
			  WHERE
			  $id_list
			  AND site_id = '" . DEFAULT_SITE . "'
			  ORDER BY $construct->order_by ";

		$result = $db->Execute($q);

		///////////////////////
		# echo $q;
		# echo "<BR>" . $db->ErrorMsg();

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','view', $db->ErrorMsg());

			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 0, $VAR);
			}
			return;   		        	
		} 

		# put the results into a smarty accessable array
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF)
		{
			### Run any custom validation on this result for
			### this module
			if(isset($construct->custom_EXP))
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
							$smart[$i]["i"] = $i;
						} else {
							$smart[$i]["i"] = $i;
						}
						$result->MoveNext();
						$ei = count($construct->custom_EXP);
						$i++;            			
					}            	
				}
				$result->MoveNext();
			}
			else
			{
				$smart[$i] = $result->fields;
				if($class_name)
				{
					$smart[$i]["i"] = $i;
				} else {
					$smart[$i]["i"] = $i;
				}
				$result->MoveNext();
				$i++;
			}
		}

		# get the result count:
		$results = $i;

		### No results:
		if($i == 0)
		{
			global $C_debug;
			$C_debug->error("CORE:database.inc.php", "view()", "The selected record does not
							 exist any longer, or your account is not authorized to view it");
			return;
		} 

		# define the results
		global  $smarty; 
		$smarty->assign($construct->table, $smart);
		$smarty->assign('results', 	$search->results);

		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 1, $VAR);
		}

		return $smart;
	}
}
?>