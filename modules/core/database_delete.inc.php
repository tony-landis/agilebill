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
	
function CORE_database_delete($VAR, &$construct, $type)
{
	global $C_debug, $C_translate;

	# set the id
	$id = $construct->table . '_id';

	# generate the full query
	$q = "DELETE FROM
			".AGILE_DB_PREFIX."$construct->table
			WHERE
			id 		= '".$db->qstr($VAR["id"], get_magic_quotes_gpc())."'
			AND
			site_id = '" . DEFAULT_SITE . "'";

	# execute the query
	$db = &DB();
	$result = $db->Execute($q);

	# Alert
	$C_debug->value["id"] = $VAR[$id];
	$C_debug->value["module_name"] = $C_translate->translate('menu',$construct->module,"");
	$alert = $C_translate->translate('alert_delete_id',"","");
	$C_debug->alert($alert);

	# error reporting
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('database.inc.php','delete', $db->ErrorMsg());


		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 0, $VAR);
		}	        	
	} else {

		if(isset($construct->trigger["$type"]))
		{
			include_once(PATH_CORE   . 'trigger.inc.php');
			$trigger    = new CORE_trigger;
			$trigger->trigger($construct->trigger["$type"], 0, $VAR);
		}	
	}
}
?>