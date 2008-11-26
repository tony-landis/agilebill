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
	
# check that the username/password are both set
if(empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) 
{
	mail('sales@agileco.com', 'htaccess empty', '');
	header_unauth();
}


#check the database for a match
$pre = AGILE_DB_PREFIX;
$time = time();
$db = &DB();
$q = "	SELECT DISTINCT
			{$pre}account.id AS account_id,
			{$pre}account_group.group_id AS group_id
		FROM 
			{$pre}account 
		INNER JOIN 
			{$pre}account_group
		ON    
			{$pre}account_group.account_id = {$pre}account.id			
		WHERE
		(
			{$pre}account.date_expire IS NULL OR 
			{$pre}account.date_expire = 0 OR 
			{$pre}account.date_expire > ".$db->qstr($time)."
		)
		AND
			{$pre}account.status   = ". $db->qstr(1) . " 
		AND
		(
			{$pre}account.password = ". $db->qstr(md5(@$_SERVER['PHP_AUTH_PW'])) . " 
			OR
			{$pre}account.password = ". $db->qstr(@$_SERVER['PHP_AUTH_PW']) . " 
		)
		AND
			{$pre}account.username = ". $db->qstr(@$_SERVER['PHP_AUTH_USER'] )." 
		AND
			{$pre}account.site_id  = ". $db->qstr(DEFAULT_SITE ) . "
		AND
		( 	
			{$pre}account_group.date_start IS NULL OR 
			{$pre}account_group.date_start = 0 OR
			{$pre}account_group.date_start < ".$db->qstr($time)."
		)
		AND
		( 
			{$pre}account_group.date_expire IS NULL OR 
			{$pre}account_group.date_expire = 0	 OR
			{$pre}account_group.date_expire > ".$db->qstr($time)."					
		)
		AND   
			{$pre}account_group.active = ".$db->qstr(1)." 			
		AND
			{$pre}account_group.site_id = ". $db->qstr( DEFAULT_SITE );

# Check for group permissions:
$result = $db->Execute($q);  
if($result->RecordCount() > 0) { 
	while( !$result->EOF ) { 
		for($i=0; $i<count($GroupArray); $i++) {
			if($GroupArray[$i] == $result->fields["group_id"]) 
			header_auth();  
		} 
		$result->MoveNext();
	}
}

# Not authorized:	 
header_unauth();	


function header_auth() {
	header('HTTP/1.0 201 Authorized'); 	
	exit;
}

function header_unauth()
{
	header('WWW-Authenticate: Basic realm="{$realm}"');
	header('HTTP/1.0 401 Unauthorized'); 
} 	 
?>