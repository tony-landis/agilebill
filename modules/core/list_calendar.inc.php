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
	
function list_calender_add($field, $default, $css)
{
	# set the date to current date if 'now' is set as $default
	if($default == 'now')
	{
		 $default = date(UNIX_DATE_FORMAT, time());	
	}

	$id = rand(9,999);
	$ret = '
		<input type="text" id="data_'.$field.'_'.$id.'" name="'.$field.'" class="'.$css.'" size="10" value="'.$default.'" />&nbsp;						                 
		<input type="button" id="trigger_'.$field.'_'.$id.'" value="+">
		<script type="text/javascript">
		  Calendar.setup(
			{
			  inputField  : "data_'.$field.'_'.$id.'",
			  ifFormat    : "'.DEFAULT_DATE_FORMAT.'",
			  button      : "trigger_'.$field.'_'.$id.'"
			}
		  );
		</script>
		';
	return $ret;
}        		


function list_calender_add_static($field, $default, $css)
{	 
	# set the date to current date if 'now' is set as $default
	if($default == 'now')
	{
		 $default = date(UNIX_DATE_FORMAT);	
	}

	$id = rand(9,999);
	$ret = '
		<input type="text" id="data_'.$field.'_'.$id.'" name="'.$field.'" class="'.$css.'" size="10" value="'.$default.'" />&nbsp;						
		<input type="button" id="trigger_'.$field.'_'.$id.'" value="+">
		<script type="text/javascript">
		  Calendar.setup(
			{
			  inputField  : "data_'.$field.'_'.$id.'",
			  ifFormat    : "'.DEFAULT_DATE_FORMAT.'",
			  button      : "trigger_'.$field.'_'.$id.'"
			}
		  );
		</script>
		';
	return $ret;
}
?>