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
	
function list_setup_default_date($default, $css)
{
	global $C_translate;
	$arr = unserialize($default);
	$ret ='';

	# loop through the menus 		
	for($i=0; $i<3; $i++)
	{


	$ret .= '
	  <select name="setup_date_format[]" id="setdate1">
		  <option value=""';
		  if($arr[$i] == '') $ret .= " selected";
		  $ret .='>-- '.$C_translate->translate('date_option', 'setup','').' --</option>

		  <option value="d"';
		  if($arr[$i] == 'd') $ret .= " selected";
			$ret .='>'.$C_translate->translate('date_month_day', 'setup','').'</option>

		  <option value="m"';
		  if($arr[$i] == 'm') $ret .= " selected";
		  $ret .='>'.$C_translate->translate('date_month', 'setup','').'</option>

		  <option value="Y"';
		  if($arr[$i] == 'Y') $ret .= " selected";
		  $ret .='>'.$C_translate->translate('date_year_four', 'setup','').'</option>

		</select>
		';
	}


			$ret .= '
	   <select name="setup_date_format[]" id="setdate2">
		  <option value=" "';
		  if($arr[$i] == '') $ret .= " selected";

		  $ret .='>-- separator --</option>
		  <option value=" "';
		  if($arr[$i] == ' ') $ret .= " selected";

		  $ret .='>'.$C_translate->translate('sep_space', 'setup','').' [ ]</option>
		  <option value="-"';
		  if($arr[$i] == '-') $ret .= " selected";

		  $ret .='>'.$C_translate->translate('sep_dash', 'setup','').' [-]</option>
		  <option value="/"';
		  if($arr[$i] == '/') $ret .= " selected";

		  $ret .='>'.$C_translate->translate('sep_slash', 'setup','').' [/]</option>
		  <option value="."';
		  if($arr[$i] == '.') $ret .= " selected";

		  $ret .='>'.$C_translate->translate('sep_period', 'setup','').' [.]</option>
		</select>
		<br>             	
		';
	return $ret;
}
?>