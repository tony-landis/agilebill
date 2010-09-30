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
	
function list_card_type_menu($default_selected, $checkout_id, $field, $class)
{ 	 
	// define default list of accepted CC types
	$def_accepted_arr = Array ( 
	0 => 'visa',
	1 => 'mc',
	2 => 'amex',
	3 => 'discover',
	4 => 'delta',
	5 => 'solo',
	6 => 'switch',
	7 => 'jcb',
	8 => 'diners',
	9 => 'carteblanche',
	10 => 'enroute' );

	$db = &DB();
	$q  = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE
				site_id = ".$db->qstr(DEFAULT_SITE)." AND
				id		= ".$db->qstr($checkout_id);
	$rs = $db->Execute($q);
	if($rs == false || $rs->RecordCount() == 0)  
		$accepted_arr = $def_accepted_arr; 

	@$cfg = unserialize($rs->fields["plugin_data"]);  
	$accepted_arr = $cfg['card_type'];  

	if(count($accepted_arr) <= 0) 
		$accepted_arr = $def_accepted_arr;

	global $C_translate;
	$data = '<select id="'.$field.'" name="'.$field.'" value="'.$default.'">';		
	for($i=0; $i<count($accepted_arr); $i++) {
		$data .=  '<option value="'.$accepted_arr[$i].'"';
		if($default_selected == $accepted_arr[$i])
		$data .=   ' selected';			
		$data .=   '>'.
				   $C_translate->translate('card_type_'. $accepted_arr[$i],'checkout','');
				   '</option>
				   ';
	}
	$data .= '</select>';	
	return $data;    	           
}
?>