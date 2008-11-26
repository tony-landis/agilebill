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
	
function list_menu_theme ($name, $id, $class)
{
	global $C_translate, $C_auth;

	if(!isset($this->id)) $this->id = 100;

	# set the input id
	if($id = '') $id = DEFAULT_THEME;


	# get the records
	$db = &DB();
	$sql= "SELECT * FROM "
		.AGILE_DB_PREFIX."theme WHERE site_id = '" . DEFAULT_SITE . "' ORDER BY name,description";
	$result = $db->Execute($sql);	

	# error handling
	if ($result === false)
	{
		global $C_debug;
		$C_debug->error('list.inc.php','menu', $db->ErrorMsg());
		return false;
	}		

	# start the return variable
	$return = '<select id="'. $field  .'_'. $input_id .'" name="'. $name .'">';			
	if($id == "all")
		$return .= '<option value="" selected></option>';	

	# loop through the records
	$ii = 0;
	while (!$result->EOF)
	{				
		### Verify that the current account is authorized for this theme
		@$arr = unserialize($result->fields['group_avail']);
		$this_show = false;

		for($i=0; $i<count($arr); $i++)
		{
			if($C_auth->auth_group_by_id($arr[$i]))
			{
				$this_show = true;
				$i=count($arr);
			}
		}

		if($this_show)
		{
			# Create the return code for Smarty				
			$return .= '<option value="' . $result->fields["id"] . '"';
			if($id == $result->fields["id"])
				$return .= "selected";
			$return .= '>' . $result->fields["name"] . ' .. ' . $result->fields["description"] . '</option>
			';				
			$ii++;
		}

		$result->MoveNext();
	}								
	if($ii==0)
	$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
	$return .= '</select>';
	return $return;
}
?>