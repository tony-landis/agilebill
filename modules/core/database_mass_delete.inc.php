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
	
function CORE_database_mass_delete($VAR, &$construct, $type)
{
	$db = &DB();

	# set the id
	$id = $construct->table . '_id';
	# generate the list of ID's
	$id_list = '';
	$ii=0;

	if(isset($VAR["delete_id"]))
	{
		$id = explode(',',$VAR["delete_id"]);
	}
	elseif (isset($VAR["id"]))
	{
		$id = explode(',',$VAR["id"]);
	}

	for($i=0; $i<count($id); $i++)
	{
		if($id[$i] != '')
		{
			if($i == 0)
			{
				$id_list .= " id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
				$ii++;
			}
			else
			{
				$id_list .= " OR id = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
				$ii++;
			}	
		}					
	}


	if($ii>0)
	{
		# generate the full query
		$q = "DELETE FROM
				".AGILE_DB_PREFIX."$construct->table
				WHERE
				$id_list
				AND
				site_id = '" . DEFAULT_SITE . "'";
		# execute the query
		$result = $db->Execute($q);


		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','mass_delete', $db->ErrorMsg());

			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 0, $VAR);
			}

		}
		else
		{


			### Delete any associated records:
			if(isset($construct->associated_DELETE))
			{

				for($ii=0; $ii<count($construct->associated_DELETE); $ii++)
				{
					$id_list = '';
					for($i=0; $i<count($id); $i++)
					{
						if($id[$i] != '')
						{
							if($i == 0)
							{
								$id_list .= $construct->associated_DELETE[$ii]["field"] ." = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
							}
							else
							{
								$id_list .= " OR " . $construct->associated_DELETE[$ii]["field"] . " = " . $db->qstr($id[$i], get_magic_quotes_gpc()) . " ";
							}	
						}					
					}

					# generate the full query
				   $q = "DELETE FROM
							".AGILE_DB_PREFIX."". $construct->associated_DELETE[$ii]["table"] . "
							WHERE
							$id_list
							AND
							site_id = '" . DEFAULT_SITE . "'";
					# execute the query
					$result = $db->Execute($q);
				}
			}

			# Alert delete message
			if(!defined('AJAX')) {
				global $C_debug, $C_translate; 
				$C_translate->value["CORE"]["module_name"] = $C_translate->translate('name',$construct->module,"");
				$message = $C_translate->translate('alert_delete_ids',"CORE","");
				$message = preg_replace('/%%module_name%%/','', $message);
				$C_debug->alert($message);	
			}

			if(isset($construct->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($construct->trigger["$type"], 1, $VAR);
			}                		
		}
	}
}	
?>