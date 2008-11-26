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
	
function list_radio($input_id, $name, $table, $field, $id, $class)
{
	global $C_translate;
	$db = &DB();
	$sql= "SELECT id, $field FROM ".AGILE_DB_PREFIX."$table WHERE site_id = '" . DEFAULT_SITE . "' ORDER BY $field";
	$result = $db->Execute($sql);
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('list.inc.php','radio', $db->ErrorMsg());
	}			
	if($result->RecordCount() >= 5)
	{
		$count = 5;
	}else{
		$count = $result->RecordCount();
	}
	if($id == "all")
		$return .= '<input id="'. $input_id .'" type="radio" name="'. $name .'" class="'. $class .'" value=""> All <BR>';	
	$i = 0;
	while (!$result->EOF) {
		if($id == $result->fields["id"])
		{
			$return .= '<input id="'. $input_id .'" type="radio" name="'. $name .'" class="'. $class .'" value="' . $result->fields["id"] . '" checked> ' . $result->fields["$field"] . '<BR>';
		}
		else
		{
			$return .= '<input id="'. $input_id .'" type="radio" name="'. $name .'" class="'. $class .'" value="' . $result->fields["id"] . '"> ' . $result->fields["$field"] . '<BR>';
		}
		$i++;
		$result->MoveNext();
	}								
	if($i==0)
		$return .= '<input id="'. $input_id .'" type="radio" name="'. $name .'" class="'. $class .'" value="">'. $C_translate->translate('lists_none_defined','CORE','');
	return $return;
}
?>