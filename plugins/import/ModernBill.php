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
	
class import_plugin extends import
{
	function import_plugin()
	{		
		# Configure the database name, host, and login:
		$this->host	= 'localhost';
		$this->db	= 'modernbill';
		$this->user = 'root';
		$this->pass = ''; 
		$this->type	= 'mysql';
		
		# If importing CC details, enter the gateway plugin name to use for recurring charges:
		$this->gateway = 'AUTHORIZE_NET';
		
		# Do not change anything past this line:
		$this->name 		= 'ModernBill';
		$this->plugin		= 'ModernBill';
		$this->select_limit	= 100;
		
		$this->instructions = '<p>Preliminary Instructions:</p>
		
								<p>1) Open '. __FILE__ .' and edit the modernbill database settings.</p>
								
								<p>2) If you will be importing credit card details, paste the Checkout Plugin
								name from the checkout plugin list page to the "$this->gateway" value
								that will be used to process all recurring charges... 
								this should be a gateway such as AUTHORIZE_NET or
								LINKPOINT. </p>
								
								<p>Note: The actual credit card numbers will not be imported since
								modernbill uses an unknown algorithm and salt file. For any imported users that
								have a credit card on file, a blank credit card record will be created in AB
								and associated with their subscriptions so they can simply update it in their
								AB account area. (or you can enter them manually via the admin interface)</p>						
																
								<p>3) Before starting with the import, you must be sure your currency settings,
								checkout plugins, etc,. are all configured to the proper defaults.</p>
								
								<p>4) You can then run steps 1 - 6 below...</p>
								
								<p>5) IMPORTANT: After completing step 6 below and BEFORE running any of the other steps,
								go to Hosting Setup > List Servers and select the proper Provisioning Plugin and enter your
								IP for Name based accounts as well as any other settings for your server type.
								
								Also, go to Products > List and for every imported hosting plan, click on "Hosting" and setup
								the hosting details. These are the settings that will be assigned to the imported hosting plans.
								Also, go to Hosting Setup > Registrar Plugins and check the configurations, as well as
								the TLD settings at Setup > TLD Setup.
								</p>
								  
								<p>6) You can now continue with the other steps.</p>
								
								';
						
		
		$this->actions[]	= Array (	'name' => 'test',
										'desc' => '<b>Step 1:</b> Test the ModernBill database connection',
										'depn' => false );
																				
		$this->actions[]	= Array (	'name' => 'accounts',
										'desc' => '<b>Step 2:</b> Import the ModernBill accounts',
										'depn' => Array('test') );
										 
		$this->actions[]	= Array (	'name' => 'blocked_email',
										'desc' => '<b>Step 2:</b> Import the ModernBill banned config ',
										'depn' => Array('accounts') );	
										
		$this->actions[]	= Array (	'name' => 'tax',
										'desc' => '<b>Step 3:</b> Import the ModernBill tax zones',
										'depn' => Array('accounts') );
														 
		$this->actions[]	= Array (	'name' => 'host_tld',
										'desc' => '<b>Step 4:</b> Import the ModernBill TLD settings',
										'depn' => Array('accounts') );	
 	
												
		$this->actions[]	= Array (	'name' => 'servers',
										'desc' => '<b>Step 5:</b> Import the ModernBill servers',
										'depn' => Array('host_tld') );
																									
		$this->actions[]	= Array (	'name' => 'products',
										'desc' => '<b>Step 6:</b> Import the ModernBill hosting packages',
										'depn' => Array('servers') );
					    
										
		/*								
		$this->actions[]	= Array (	'name' => 'invoices',
										'desc' => '<b>Step 7:</b> Import the ModernBill invoices',
										'depn' => Array('products') );

		*/								
										
										
		$this->actions[]	= Array (	'name' => 'domains',
										'desc' => '<b>Step 7:</b> Import the ModernBill domain records',
										'depn' => Array('products') );

		/*																	
		$this->actions[]	= Array (	'name' => 'services',
										'desc' => '<b>Step 8:</b> Import the ModernBill hosting subscriptions',
										'depn' => Array('domains') );	
		*/										
	}
	
	# test remote database connectivity
	function test()
	{
		global $C_debug, $VAR;
		
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db);  		 
		if( empty($this->host) || empty($this->user) || empty($this->db) || $dbr === false || @$dbr->_errorMsg != "") {  
			$C_debug->alert('Failed: ' . $dbr->_errorMsg);
		}  else {
			$C_debug->alert('Connected OK!'); 
			$db  = &DB();
			$id  = $db->GenID(AGILE_DB_PREFIX.'import_id');
        	$sql = "INSERT INTO ".AGILE_DB_PREFIX."import 
        			SET
        			id 			= $id,
        			site_id 	= ".DEFAULT_SITE.",
        			date_orig	= ".time().",
					plugin 		= ".$db->qstr($VAR['plugin']).",
					action 		= ".$db->qstr($VAR['action']);
        	$db->Execute($sql); 
		} 
		
		# return to main import page
		echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";
	}
	
	
	
	
	# import the account and billing details 
	function accounts()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each account from remote db
		$sql = "SELECT * FROM client_info  ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'client_info' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		
		### Get the default checkout plugin id:	
		$db = &DB();	
		$sql = "SELECT id FROM {$p}checkout WHERE
				site_id = $s AND
				checkout_plugin = '{$this->gateway}'";
		$ch = $db->Execute($sql);		
		$checkout_plugin_id = $ch->fields['id'];
 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing account: {$rs->fields['client_fname']} {$rs->fields['client_lname']}";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local account id
			$id = $db->GenID($p.'account_id');
			 			 
			## Get the country:
			$country = $rs->fields['client_country'];
			$db = &DB();
			$dbm = new CORE_database;
			$rscountry = $db->Execute( $dbm->sql_select('country','id',"two_code = $country", '', &$db) );
			if($rscountry && $rscountry->RecordCount() == 1)
				$country = $rscountry->fields['id'];
			else
				$country = DEFAULT_COUNTRY;
				
			if(empty($rs->fields['client_username']))
			$username = $rs->fields['client_email'];
			else
			$username = $rs->fields['client_username'];
			
			# Insert the account
			$sql = "INSERT INTO {$p}account SET
					id 			= $id,
					site_id		= $s,
					date_orig	= ".$db->qstr($rs->fields['client_stamp']).",
					date_last	= ".time().",
					language_id	= ".$db->qstr(DEFAULT_LANGUAGE).",
					currency_id	= ".DEFAULT_CURRENCY.",
					theme_id	= ".$db->qstr(DEFAULT_THEME).",
					username	= ".$db->qstr($username).",
					password	= ".$db->qstr($rs->fields['client_password']).",
					misc		= ".$db->qstr("Phone: ". $rs->fields['client_phone1'] ."\r\nPhone #2: ". $rs->fields['client_phone2'] . "\r\n". $rs->fields['client_comments']).",
					status		= 1,
					country_id	= {$country},
					first_name	= ".$db->qstr($rs->fields['client_fname']).",
					last_name	= ".$db->qstr($rs->fields['client_lname']).",
					company		= ".$db->qstr($rs->fields['client_company']).",
					address1	= ".$db->qstr($rs->fields['client_address']).",
					address2	= ".$db->qstr($rs->fields['client_address_2']).",
					city		= ".$db->qstr($rs->fields['client_city']).",
					state		= ".$db->qstr($rs->fields['client_state']).",
					zip			= ".$db->qstr($rs->fields['client_zip']).",
					email		= ".$db->qstr($rs->fields['client_email']).",
					email_type	= 0";
			$db->Execute($sql);
			 
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'account', $id, 'client_info', $rs->fields['client_id'], &$db);		
 	 			
			
			# If cc details exist, import an account_billing record:
			if( !empty($rs->fields['billing_cc_type']) && !empty($rs->fields['billing_cc_exp']) )
			{
				# Get a local account_billing id
				$bill_id = $db->GenID($p.'account_billing_id');	
				
				$type = explode("-", $rs->fields['billing_cc_type']);
				$exp = explode("/", $rs->fields['billing_cc_exp']);

				# the modernbill encryption method is unknown, so we have no way to decrypt the cc details
				# we will create a blank CC record that the user or admin can manually update...		 
				$sql = "INSERT INTO {$p}account_billing SET
						id 					= $bill_id,
						site_id				= $s,  
						account_id			= $id,
						checkout_plugin_id 	= $checkout_plugin_id,
						card_type			= ". $db->qstr( strtolower( $type[0] ) ) .", 
						card_num4			= ". $db->qstr( $type[1] ) .", 
						card_exp_month		= ". $db->qstr( $exp[0] ) .", 
						card_exp_year		= ". $db->qstr( $exp[1] );
				$db->Execute($sql);
				
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'account_billing', $bill_id, 'client_info', $rs->fields['client_id'], &$db);		 	 						
			} 			
			
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>"; 
	}
	
	  
	
	
	
	# import the banned config
	function blocked_email()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each account from remote db
		$sql = "SELECT * FROM banned_config  ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'banned_config' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		} 
 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing banned config: {$rs->fields['ban_string']} ";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			 		  
			
			# Insert the record
			$id = $db->GenID($p.'blocked_email_id');
			$sql = "INSERT INTO {$p}blocked_email SET
					id 			= $id,
					site_id		= $s,
					date_orig	= ".$db->qstr($rs->fields['ban_last_stamp']).",
					date_last	= ".time().", 
					email			= ".$db->qstr($rs->fields['ban_string']).",
					notes		= ".$db->qstr($rs->fields['ban_message']) ;
			$db->Execute($sql);
			  
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'blocked_email', $id, 'banned_config', $rs->fields['ban_id'], &$db);		
 	  
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>"; 
	}
		
	
	
	
	# import the banned config
	function tax()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each account from remote db
		$sql = "SELECT * FROM tax_zones  ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'tax_zones' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		} 
 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing tax_zone: {$rs->fields['tax_zone']} ";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans(); 
			
			# get the country 
			$sql = "SELECT id FROM {$p}country WHERE site_id = ".DEFAULT_SITE." AND two_code = '{$rs->fields['countries_iso_2']}'";
			$crs = $db->Execute($sql);
			$country = $crs->fields['id'];
			
			if( empty($rs->fields['tax_zone']) || $rs->fields['tax_zone'] == 'none')
			$zone = '*';
			else
			$zone = $rs->fields['tax_zone'];
			
			# Insert the record
			$id = $db->GenID($p.'tax_id');
			$sql = "INSERT INTO {$p}tax SET
					id 			= $id,
					site_id		= $s,
					zone		= ".$db->qstr( $zone ).",
					rate		= ".$db->qstr( $rs->fields['tax_amount'] * .01 ).", 
					description	= ".$db->qstr($rs->fields['tax_desc']).",
					country_id	= ".$db->qstr($country);
			$db->Execute($sql);
			  
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'tax', $id, 'tax_zones', $rs->fields['tid'], &$db);		
 	  
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>"; 
	}

	
	
	
	# import the account and billing details 
	function host_tld()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each account from remote db
		$sql = "SELECT * FROM tld_config ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'tld_config' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing TLD: {$rs->fields['tld_extension']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# delete TLD if it exists already:
			$sql = "DELETE FROM {$p}host_tld WHERE site_id = {$s} AND name = '{$rs->fields['tld_extension']}'";
			$db->Execute($sql);
			
			# Get a local id
			$id = $db->GenID($p.'host_tld_id');
			
			# determine whois_plugin_data
			$whois_plugin_data = serialize( array( 'whois_server' => $rs->fields['tld_whois_server'], 'avail_response' => $rs->fields['tld_whois_response']) );
  
			# determine price group 
			$start = false;
			empty($price_group);
			$price_group = Array();
			for($i=1; $i<=10; $i++)
			{ 
				# Set default recurring prices:  
				$cost = $rs->fields["tld_{$i}y"];
				if($cost != "") {
					$db = &DB();
					$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
		                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
		                            pricing		        = ' . $db->qstr('1');
					$rsg = $db->Execute($sql);
					while(!$rsg->EOF) {
						$group = $rsg->fields['id'];
						$price_group["$i"]["show"] = 1;
						$price_group["$i"]["$group"]["register"] 	= round( $cost, 2);
						$price_group["$i"]["$group"]["renew"] 		= '';
						
						if($i==1 && $rs->fields['tld_transfer'] > 0)
							$price_group["$i"]["$group"]["transfer"] 	=  round( $rs->fields['tld_transfer'], 2);
						else 
							$price_group["$i"]["$group"]["transfer"] 	= '';
						
						$rsg->MoveNext();
					}
					if($start == false) $start = $i;
				}
			}
				 
			# Insert the record
			$sql = "INSERT INTO {$p}host_tld SET
					id 						= $id,
					site_id					= $s, 
					status 					= '1',
					name					= '{$rs->fields['tld_extension']}',
					taxable					= '1',
					whois_plugin			= 'DEFAULT', 
					whois_plugin_data 		= ".$db->qstr( $whois_plugin_data ).",
					registrar_plugin_id 	= 1,
					registrar_plugin_data 	= ".$db->qstr( serialize(array())).",
					auto_search 			= 1,
					default_term_new 		= $start, 
					price_group 			= ".$db->qstr( serialize( $price_group ) ); 
			$db->Execute($sql);
			
			
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'host_tld', $id, 'tld_config', $rs->fields['tld_id'], &$db);		
 	 			 
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext(); 
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>"; 
	}	
		
	
	

	
	
	
	
	# Import any servers  
	function servers()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each hosting server
		$sql = "SELECT * FROM hosting_servers";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'hosting_servers' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each hosting server
		while(!$rs->EOF)
		{ 
			$msg.= "<BR>Processing Server: {$rs->fields['server_name']}...";
			 		
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Determine the plugin type 
			$plugin = 'MANUAL';				
			   	
			# Create the server record in AB now:
			$host_server_id = $db->GenID($p.'host_server_id');
			$sql = "INSERT INTO {$p}host_server SET
					id 				= {$host_server_id},
					site_id 		= {$s},						  
					name 			= ".$db->qstr($rs->fields['server_name']).",
					status 			= 1,
					debug 			= 1,
					provision_plugin= ".$db->qstr($plugin).",
					notes 			= ".$db->qstr( 'Imported from Modernbill' ).", 
					name_based_ip 	= ".$db->qstr($rs->fields['server_ip']).",
					name_based 		= 1";
			$db->Execute($sql);
			$this->import_transaction($this->plugin, $VAR['action'], 'host_server', $host_server_id, 'hosting_servers', $rs->fields['id'], &$db);
	 
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>";		
	}	 
	
	
	
	
	
	# Import any products  
	function products()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each hosting server
		$sql = "SELECT * FROM hosting_plans";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'hosting_plans' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		while(!$rs->EOF)
		{  
			$msg.= "<BR>Processing Plan: {$rs->fields['planname']}...";
			
			unset($recur_price);
			unset($price);
			unset($price_recurr_schedule); 
			 		
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get server_id from AB
			$sql = "SELECT id FROM {$p}host_server WHERE site_id = {$s} AND  name = '{$rs->fields['default_server']}'";
			$rshost = $db->Execute($sql);
			$host_server_id = $rshost->fields['id'];
			   			   
			# Set default recurring prices:
			$db = &DB();  
			
			# Set the monthly prices
			if($rs->fields['monthly'] > 0) {
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
		                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
		                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql); 
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[1]['show'] = "1";
					$recur_price[1][$i]['price_base']  = $rs->fields['monthly'];
					$recur_price[1][$i]['price_setup'] = $rs->fields['setupfee'];				
					$rsg->MoveNext();
				}	
				if(empty($price_recurr_schedule)) $price_recurr_schedule = '1';	
				if(empty($price)) $price = $rs->fields['monthly'];	
			}
			
			# Set the quarterly prices
			if($rs->fields['quarterly'] > 0) {
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
		                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
		                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql); 
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[2]['show'] = "1";
					$recur_price[2][$i]['price_base']  = $rs->fields['quarterly'];
					$recur_price[2][$i]['price_setup'] = $rs->fields['setupfee'];				
					$rsg->MoveNext();
				}		
				if(empty($price_recurr_schedule)) $price_recurr_schedule = '2';	
				if(empty($price)) $price = $rs->fields['v'];			
			}			
			
			# Set the biannual prices
			if($rs->fields['biannually'] > 0) {
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
		                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
		                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql); 
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[3]['show'] = "1";
					$recur_price[3][$i]['price_base']  = $rs->fields['biannually'];
					$recur_price[3][$i]['price_setup'] = $rs->fields['setupfee'];				
					$rsg->MoveNext();
				}			
				if(empty($price_recurr_schedule)) $price_recurr_schedule = '3';	
				if(empty($price)) $price = $rs->fields['biannually'];		
			}			
			
			# Set the annual prices
			if($rs->fields['annually'] > 0) {
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
		                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
		                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql); 
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[4]['show'] = "1";
					$recur_price[4][$i]['price_base']  = $rs->fields['annually'];
					$recur_price[4][$i]['price_setup'] = $rs->fields['setupfee'];				
					$rsg->MoveNext();
				}
				if(empty($price_recurr_schedule)) $price_recurr_schedule = '4';	
				if(empty($price)) $price = $rs->fields['annually'];					
			}	 
			  
			$setup = $rs->fields['setupfee'];  
			$price_recurr_type 		= "0";
			$price_recurr_week 		= "1";
			$price_recurr_weekday 	= "1";
			$price_recurr_default 	= $price_recurr_schedule;
					             
			# Get a local id
			$product_id = $db->GenID($p.'product_id');

			# Insert the record
			$sql = "INSERT INTO {$p}product SET
						id 			= $product_id,
						site_id		= $s, 
						sku			= ".$db->qstr( $rs->fields['planname']).",
						taxable		= 1, 
						active		= 1,
						  
						price_type		= 1,
						price_base		= '{$price}',
						price_setup		= '{$setup}',
						price_group		= ".$db->qstr( serialize(@$recur_price ) ).",	 
						
						price_recurr_default 	= '".@$price_recurr_default."',
						price_recurr_type		= '".@$price_recurr_type."',
						price_recurr_weekday 	= '".@$price_recurr_weekday."',
						price_recurr_week		= '".@$price_recurr_week."',
						price_recurr_schedule 	= '".@$price_recurr_schedule."',
						price_recurr_cancel 	= 1, 
						
						host 					= 1,
						host_server_id 			= '{$host_server_id}',
						
						avail_category_id 		= ".$db->qstr( serialize(array(0,1,2)));
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product', $product_id, 'hosting_plans', $rs->fields['id'], &$db);


			### Insert the description:
			$idx = $db->GenID($p.'product_translate_id');

			$sql = "INSERT INTO {$p}product_translate SET
						id 					= $idx,
						site_id				= $s, 
						product_id			= $product_id,
						language_id 		= '".DEFAULT_LANGUAGE."',  
						name				= ".$db->qstr( $rs->fields['planname'] ).",
						description_short	= ".$db->qstr( $rs->fields['description'] ).", 
						description_full	= ".$db->qstr( $rs->fields['description'] ) ;
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_translate', $idx, 'hosting_plans', $rs->fields['id'], &$db);
 
					 
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>";		
	}
	
		 
	
	/*
	
	### Import all invoices  
	function invoices()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each invoice
		$sql = "SELECT * FROM client_invoice";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'client_invoice' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing Order: {$rs->fields['invoice_id']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'invoice_id');
			  		 
			### Get the default checkout plugin id:		
			$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
			$ch = $db->Execute($sql);		
			$checkout_plugin_id = $ch->fields['id'];
						 
			# get the process & billing status
			$process_status = 0;
			$billing_status = 0; 	
						
			if($rs->fields['invoice_amount'] <= $rs->fields['invoice_amount_paid'])  { 
				$billing_status = 1;   
				$process_status = 1;   
			} 
			
			# get the account id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['client_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
			
			# get the tax id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'tax' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['tax_zone_id']}'";
			$tax = $db->Execute($sql); 
			$tax_id = $tax->fields['ab_id'];			
			
			# get the billing id
			$sql = "SELECT id FROM {$p}account_billing WHERE site_id = {$s} AND account_id = $account_id";
			$billing = $db->Execute($sql); 
			$billing_id = $billing->fields['id'];
						 
			if( $rs->fields['invoice_date_entered'] < (time() - 86400*7) )
			$suspend_billing = 1;
			else
			$suspend_billing = 0;
			
			# Insert the record
			$sql = "INSERT INTO {$p}invoice SET
					id 					= $id,
					site_id				= $s, 
					date_orig			= ". $rs->fields['invoice_date_entered'] .",
					date_last			= ".$db->qstr(time()).", 
					
					process_status		= ".$db->qstr(@$process_status).",
					billing_status		= ".$db->qstr(@$billing_status).",
					
					account_id			= ".$db->qstr(@$account_id).", 
					account_billing_id 	= ".$db->qstr(@$billing_id).",  
					checkout_plugin_id 	= ".$db->qstr(@$checkout_plugin_id).",  
					tax_amt				= ".$db->qstr(@$rs->fields['invoice_tax']).", 
					tax_id				= ".$db->qstr(@$tax_id).", 
					discount_amt		= ".$db->qstr(@$rs->fields['invoice_discount']).", 
					total_amt			= ".$db->qstr(@$rs->fields['invoice_amount']).",
					billed_amt			= ".$db->qstr(@$rs->fields['invoice_amount_paid']).",
					billed_currency_id 	= ".$db->qstr(DEFAULT_CURRENCY).",
					actual_billed_amt 	= ".$db->qstr(@$rs->fields['invoice_amount_paid']).",
					actual_billed_currency_id = ".$db->qstr(DEFAULT_CURRENCY).",
					
					notice_count		= 0,
					notice_max 			= 1,
					notice_next_date	= ".$db->qstr(time()).",
					grace_period		= 7,
					suspend_billing		= $suspend_billing,
					due_date 			= ". $rs->fields['invoice_date_due'];
			$db->Execute($sql);
			 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice', $id, 'client_invoice', $rs->fields['invoice_id'], &$db);		
					  
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>";		
	}
	
	*/
	
	
	## Import domains
	function domains()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each record
		$sql = "SELECT * FROM domain_names";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'domain_names' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing Domain: {$rs->fields['domain_name']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'service_id');
 	 
			# get the account id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['client_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
 
			# Determine the domain TLD & Name:
			$domain_name = $rs->fields['domain_name'];
			$arr = explode('\.', $domain_name);
			$tld = '';
			$domain =  $arr[0];
			for($i=0; $i<count($arr); $i++)  {
				if($i>0) {
					if($i>1) 
					$tld .= ".";
					$tld .= $arr[$i];
				}
			}
			 
			$sku = 'DOMAIN-REGISTER';
			$domain_type = 'register'; 
			
			if(time() <= $rs->fields['domain_expires'])
			$active = 1;
			else
			$active = 0;
			  	 
			# Determine the term
			$start = $rs->fields['domain_created'];
			$end   = $rs->fields['domain_expires']; 
			$diff = $end - $start; 
			$term = round( $diff / (86400*365) ); 
			 
			# Determine the tld_id 
			$sql = "SELECT id,registrar_plugin_id FROM {$p}host_tld WHERE site_id = {$s} AND name= '{$tld}'";
			$tldrs = $db->Execute($sql); 
			$domain_host_tld_id = $tldrs->fields['id'];	
			$domain_host_registrar_id = $tldrs->fields['registrar_plugin_id'];	
			  
			# Insert the record
			$sql = "INSERT INTO {$p}service SET
						id 					= $id,
						site_id				= $s, 
						queue				= 'none',
						date_orig			= ".$db->qstr($rs->fields['domain_created']).",
						date_last			= ".$db->qstr(time()).",   
						account_id			= ".$db->qstr(@$account_id).",  
						sku					= ".$db->qstr($sku).", 
						type				= ".$db->qstr('domain').", 
						active				= $active,   
						price_type			= ".$db->qstr('0').",
						taxable				= ".$db->qstr('0').", 
						
						domain_date_expire	= ".$db->qstr( $rs->fields['domain_expires'] ).",
						domain_host_tld_id	= ".$db->qstr( $domain_host_tld_id ).",
						domain_host_registrar_id = ".$db->qstr( $domain_host_registrar_id ).",
						
						domain_name 		= ".$db->qstr( $domain ).",
						domain_term		  	= ".$db->qstr( $term ).",
						domain_tld  		= ".$db->qstr( $tld ).",
						domain_type			= ".$db->qstr( $domain_type ); 										
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'service', $id, 'domain_names', $rs->fields['domain_id'], &$db);
			 
			
			/*
			 
			# Insert the invoice item:
			$idx = $db->GenID($p.'invoice_item_id'); 
			$sql = "INSERT INTO {$p}invoice_item SET
						id 					= $id,
						site_id				= $s,  
						invoice_id			= ".$db->qstr(@$invoice_id).",  
						date_orig			= ".$db->qstr($date_orig).", 
						sku					= ".$db->qstr($sku).",
						quantity			= 1,
						item_type			= 2,
						price_type			= 0,
						price_base			= ".$db->qstr( $price ).", 
						domain_name 		= ".$db->qstr( $domain ).",
						domain_term		  	= ".$db->qstr( $term ).",
						domain_tld  		= ".$db->qstr( $tld ).",
						domain_type			= ".$db->qstr( $domain_type ); 					 
			$db->Execute($sql);
						
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice_item', $idx, 'domain_list', $rs->fields['BID'], &$db);		
			   
			*/
			
			
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>";				
	}
	 
	
	

	/*
	
	## Import hosting subscriptions
	function hosting()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		  
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		  
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;
 
		# select each record
		$sql = "SELECT * FROM client_package";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'client_package' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing Hosting Plan ID {$rs->fields['cp_id']} ...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'service_id');
	  
					 
			### Get the default checkout plugin id:		
			$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
			$ch = $db->Execute($sql);		
			$checkout_plugin_id = $ch->fields['id'];
				  	 
			### Get the default checkout plugin id:		
			$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
			$ch = $db->Execute($sql);		
			$checkout_plugin_id = $ch->fields['id'];
				 
			# get the account id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['client_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
			
			# get the billing id
			$sql = "SELECT id FROM {$p}account_billing WHERE site_id = {$s} AND account_id = $account_id";
			$billing = $db->Execute($sql); 
			$billing_id = $billing->fields['id'];	
 
			# get the invoice id
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'invoice' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['OID']}'";
			$invoice = $db->Execute($sql); 
			$invoice_id = $invoice->fields['ab_id'];
			 
 	 
			# get the product id
			$sql = "SELECT * FROM {$p}product WHERE site_id = {$s} AND sku = '{$rs->fields['pkey']}'";
			$product = $db->Execute($sql); 
			$product_id = $product->fields['id'];
			  
			 			
			# Billing Status
			if($rs->fields['discontinue'] == 1)
				$suspend = 1;
			 else 
				$suspend = 0;				
			 
			# Active?	
			if($rs->fields['suspended'] == 1) 
				$active = 0; 
			else 
				$active = 1; 				
						
			  
			# Determine the domain TLD & Name:
			$domain_name = $rs->fields['target'];
			$arr = explode('\.', $domain_name);
			$tld = '';
			$domain = $arr[0];
			for($i=0; $i<count($arr); $i++)  {
				if($i>0) {
					if($i>1) 
					$tld .= ".";
					$tld .= $arr[$i];
				}
			}
			
			# SKU
			$sku = $rs->fields['plan'];
  
			# Determine the cost		
			$price = $rs->fields['price'] -  $rs->fields['discount'];
			
			# Determine the HOST IP 
			$sql = "SELECT name_based_ip FROM {$p}host_server WHERE site_id = {$s} AND id = '{$product->fields['host_server_id']}'";
			$hostrs = $db->Execute($sql); 
			$ip = $hostrs->fields['name_based_ip'];	
			
			# Determine the tld_id 
			$sql = "SELECT id,registrar_plugin_id FROM {$p}host_tld WHERE site_id = {$s} AND name= '{$tld}'";
			$tldrs = $db->Execute($sql); 
			$domain_host_tld_id = $tldrs->fields['id'];	
			$domain_host_registrar_id = $tldrs->fields['registrar_plugin_id'];				
			
			# Get the subscription details: 
			$freq = $rs->fields['cycle'];
			if ($freq=="0") 		{ $recur_schedule = "0"; }	// Weekly 
			elseif ($freq=="1")     { $recur_schedule = "1"; }	// Monthly 
			elseif ($freq=="2")  	{ $recur_schedule = "1"; }	// Bi-Monthly
			elseif ($freq=="3")  	{ $recur_schedule = "2"; }	// Quarterly
			elseif ($freq=="6") 	{ $recur_schedule = "3"; }	// Semi-Annually
			elseif ($freq=="12") 	{ $recur_schedule = "4"; }	// Annually
			elseif ($freq=="24") 	{ $recur_schedule = "5"; }	// Annually
			else { $recur_schedule = '1'; } 						 
							 
			# Insert the record 
			$sql = "INSERT INTO {$p}service SET
						id 					= $id,
						site_id				= $s, 
						queue				= 'active',
						date_orig			= ".$db->qstr($date_orig).",
						date_last			= ".$db->qstr(time()).",  
						invoice_id			= ".$db->qstr(@$invoice_id).", 
						account_id			= ".$db->qstr(@$account_id).",
						account_billing_id	= ".$db->qstr(@$billing_id).",
						product_id			= ".$db->qstr(@$product_id).", 
						sku					= ".$db->qstr($sku).",  
						type				= ".$db->qstr('host').", 
						
						active				= ".$db->qstr($active).", 
						suspend_billing		= ".$db->qstr($suspend).", 
						
						date_last_invoice	= ".$db->qstr($date_orig).",
						date_next_invoice	= ".$db->qstr($date_next).",
						 
						price				= ".$db->qstr( $rs->fields['amount'] ).",
						price_type			= 1,
						taxable				= 1,	
										
						recur_type			= ".$db->qstr($product->fields['price_recurr_type']).",
						recur_schedule		= ".$db->qstr( $recur_schedule ).",
						recur_weekday		= ".$db->qstr($product->fields['price_recurr_weekday']).",
						recur_week			= ".$db->qstr($product->fields['price_recurr_week']).",
						recur_cancel		= ".$db->qstr($product->fields['price_recurr_cancel']).",
						recur_schedule_change = ".$db->qstr($product->fields['price_recurr_modify']).",

						host_username		= ".$db->qstr( $rs->fields['username'] ).",
						host_password		= ".$db->qstr( $rs->fields['password'] ).",
						
						host_server_id				= ".$db->qstr( $product->fields['host_server_id'] ).",
						host_provision_plugin_data 	= ".$db->qstr( $product->fields['host_provision_plugin_data'] ).",
						host_ip						= ".$db->qstr( $ip ).",
						 
						domain_host_tld_id			= ".$db->qstr( $domain_host_tld_id ).",
						domain_host_registrar_id 	= ".$db->qstr( $domain_host_registrar_id ).",
						
						domain_name 		= ".$db->qstr( $domain ).", 
						domain_tld  		= ".$db->qstr( $tld ) ;										
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'service', $id, 'host_list', $rs->fields['BID'], &$db);
			 
			 
			# Insert the invoice item:
			$idx = $db->GenID($p.'invoice_item_id');
			
			$sql = "INSERT INTO {$p}invoice_item SET
					id 					= $id,
					site_id				= $s,  
					invoice_id			= ".$db->qstr(@$invoice_id).", 
					product_id			= ".$db->qstr(@$product_id).",
					date_orig			= ".$db->qstr($date_orig).", 
					product_id			= ".$db->qstr(@$product_id ).",
					sku					= ".$db->qstr($sku).",
					quantity			= 1,
					item_type			= 1, 
					
					price_type			= 1,
					price_base			= ".$db->qstr( $rs->fields['initial'] ).", 
					domain_name 		= ".$db->qstr( $domain ).", 
					domain_tld  		= ".$db->qstr( $tld ).", 	 
					
					recurring_schedule  = ".$db->qstr( $recur_schedule ); 					 
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice_item', $id, 'host_list', $rs->fields['BID'], &$db);		
			  
			
			# Complete the transaction
        	$db->CompleteTrans(); 
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			  setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1200);
			 </script>";				
	}
	
	*/
		
}		 		
?>