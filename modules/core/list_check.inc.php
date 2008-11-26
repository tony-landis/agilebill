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
	
function list_check($input_id, $name, $table, $field, $default, $class)
{
	global $C_translate;

	# get default
	if(!isset($default))
	{
		$default = Array('');
	}
	else if (gettype($default) == 'array')
	{
		$default = $default;
	}
	else if (gettype($default) == 'string')
	{
		$default = unserialize($default);
	}
	else
	{
		$default = Array('');
	}

	# get the records
	$db = &DB();
	$sql= "SELECT id, $field FROM ".AGILE_DB_PREFIX."$table
				WHERE site_id = " . $db->qstr(DEFAULT_SITE) . "
				ORDER BY $field";
	$result = $db->Execute($sql);

	# error handling
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('list.inc.php','check', $db->ErrorMsg());
	}			

	# loop through the records
	$i = 0;
	while (!$result->EOF) {

		#  Create the return code for Smarty
		$checked  = '';
		for($ii=0; $ii<count($default); $ii++)
		{
			if($default[$ii] == $result->fields["id"] || $default[$i] == 'all') $checked = ' checked';
		}

		$return .= '<input id="'. $input_id .'" type="checkbox" name="'. $name .
		'[]" class="'. $class .'" value="' . $result->fields["id"] .
		'"'. $checked .'> ' . $result->fields["$field"] . '<BR>';

		$i++;
		$result->MoveNext();
	}

	if($i==0)
		$return .= 'None Defined'; ### TRANSLATE

	echo $return;
}
?>