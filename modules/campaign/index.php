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
	
ob_start();  

# Required includes: 
require_once('../../config.inc.php');
require_once(PATH_ADODB  . 'adodb.inc.php');
require_once(PATH_CORE   . 'database.inc.php'); 
require_once(PATH_CORE   . 'vars.inc.php');

$C_debug 	= new CORE_debugger;
$C_vars 	= new CORE_vars;
$VAR        = $C_vars->f; 


$db     	= &DB();
$sql    	= 'SELECT * FROM ' . AGILE_DB_PREFIX . 'campaign WHERE
			   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
			   id          = ' . $db->qstr(@$VAR['id']);
$result = $db->Execute($sql);

if( !empty($VAR['file']) ) { 
	$file_no = $VAR['file'];
} 
else
{
	# random file: 
	$last	= $result->fields['last_served'];
	if(empty($last)) $last = 1;
	$next 	= false;  

	for($i=1; $i<=12; $i++) {
		if(!empty($result->fields["file".$i])  && !$next) { 
			if($i == $last)
				$next = true;
		} else if(!empty($result->fields["file".$i]) &&   $next) { 
			$file_no = $i;
			$i = 20;
		}
	} 
}

if(empty($file_no))
	$file_no = '1';	

$file       = PATH_FILES . 'campaign_' . $VAR['id'] . '_' . $file_no .'.dat';
$type       = 'type'.$file_no;
$name       = 'file'.$file_no;
$count_field= 'served'.$file_no;
$count      = $result->fields[$count_field] + 1; 
if($result->RecordCount() > 0)
{
	### Open the file
	if (@$file=fopen($file, 'r'))
	{
		### Display the correct headers:
		header ("Content-type: " . $result->fields[$type]);
		header ("Content-Disposition: inline; filename=" . $result->fields[$name]);
		header ("Content-Description: PHP/INTERBASE Generated Data" );
		fpassthru($file);

		### Increment the file
		if(!isset($VAR['_log']))
		{
			$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'campaign SET
								'.$count_field.' = ' . $db->qstr($count) . ',
								last_served 	= ' . $db->qstr($file_no) . '
								WHERE
								site_id     	= ' . $db->qstr(DEFAULT_SITE) . ' AND
								id          	= ' . $db->qstr(@$VAR['id']);
			$result = $db->Execute($sql);
		}
		exit;
	}
}
echo 'Sorry, the campaign or required file does not exist!';

ob_end_flush();
?>