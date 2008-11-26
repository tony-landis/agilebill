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
	
/*
USAGE:

url/to/modules/asset/generator.php?prefix=MyPrefix&start=1&end=100

Where prefix a text prefix (defaults to asset- )
Where start is the start of the count
Where end is the end of the count

Paste to a text file and save for loading into the asset import tool.

*/

$prefix='asset-';
$start=0;
$end=1000;

@$prefix = $_GET['prefix'];
@$start = $_GET['start'];
@$end = $_GET['end'];

echo "<pre>";

while($start < $end) {
	echo $prefix.$start."\r\n";
	$start++;
}

?>