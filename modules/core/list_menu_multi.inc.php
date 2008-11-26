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
	
function list_menu_multi($default, $name, $table, $field, $id, $max, $class)
{
	global $C_translate;
	if(!isset($default))
		$default = Array('');
	else if (gettype($default) == 'array')
		$default = $default;
	else if (gettype($default) == 'string')
		$default = unserialize($default);
	else
		$default = Array('');
	$db = &DB();
	$sql= "SELECT id, $field FROM ".AGILE_DB_PREFIX."$table WHERE site_id = '" . DEFAULT_SITE . "' ORDER BY $field";
	$result = $db->Execute($sql);
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('list.inc.php','menu_list', $db->ErrorMsg());
	}
	if (@$result->RecordCount() > $max && @$result->RecordCount() != 0)
	$size = $max;
	else
	$size = $result->RecordCount();
	$return = '<select id="'.$name.'" name="'. $name .'[]" size="' . $size . '" value="'.$default.'" multiple>';	
	$i = 0;
	while (!$result->EOF) {	 			
		$return .= '<option value="' . $result->fields["id"] . '"';
		for($ii=0; $ii<count($default); $ii++)
		{
			if($default[$ii] == $result->fields["id"])
			{
				$return .= " selected";
				$ii = count($default);
			}
		}

		$return .= '>' . $result->fields["$field"] . '</option>
		';				
		$i++;
		$result->MoveNext();
	}								
	if($i==0)
		$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
	$return .= '</select>';
	echo $return;
}
?>