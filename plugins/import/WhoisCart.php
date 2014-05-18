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
		$this->db	= 'whoiscart';
		$this->user = 'root';
		$this->pass = ''; 
		$this->type	= 'mysql';
		
		# If importing CC details, enter the gateway plugin name to use for recurring charges:
		$this->gateway = 'AUTHORIZE_NET';
		
		# Do not change anything past this line:
		$this->name 		= 'WhoisCart';
		$this->plugin		= 'WhoisCart';
		$this->select_limit	= 50;
		
		$this->instructions = '<p>Preliminary Instructions:</p>
		
								<p>1) Open '. __FILE__ .' and edit the database settings.</p>
								
								<p>2) If you will be importing credit card details, paste the Checkout Plugin
								name from the checkout plugin list page to the "$this->gateway" value
								that will be used to process all recurring charges... 
								this should be a gateway such as AUTHORIZE_NET or
								LINKPOINT. Note: The actual credit card numbers will not be imported since
								Whois.Cart uses an unknown algorithm and salt file. For any imported users that
								have a credit card on file, a blank credit card record will be created in AB
								and associated with their subscriptions so they can simply update it in their
								AB account area. (or you can enter them manually via the admin interface)</p>						
																
								<p>3) Before starting with the import, you must be sure your currency settings,
								checkout plugins, etc,. are all configured to the proper defaults.</p>
								
								<p>4) You can then run steps 1 - 3 below...</p>
								
								<p>5) IMPORTANT: After completing step 3 below and BEFORE running any of the other steps,
								go to Hosting Setup > List Servers and select the proper Provisioning Plugin and enter your
								IP for Name based accounts, as this is the IP that will be assigned to the imported hosting services.
								Also, go to Products > List and for every imported hosting plan, click on "Hosting" and setup
								the hosting details. These are the settings that will be assigned to the imported hosting plans.</p>
								  
								<p>6) You can now continue with steps 4 - 7.</p>
								
								';
						
		
		$this->actions[]	= Array (	'name' => 'test',
										'desc' => '<b>Step 1:</b> Test the database connection',
										'depn' => false );
																				
		$this->actions[]	= Array (	'name' => 'accounts',
										'desc' => '<b>Step 2:</b> Import the WhoisCart accounts',
										'depn' => Array('test') );
																	
		$this->actions[]	= Array (	'name' => 'product',
										'desc' => '<b>Step 3:</b> Import the WhoisCart servers and hosting packages',
										'depn' => Array('accounts') );
										
		$this->actions[]	= Array (	'name' => 'host_tld',
										'desc' => '<b>Step 4:</b> Import the WhoisCart TLD settings',
										'depn' => Array('accounts','product') );										
 					 
		$this->actions[]	= Array (	'name' => 'invoices',
										'desc' => '<b>Step 5:</b> Import the WhoisCart invoices',
										'depn' => Array('accounts','product','host_tld') );

		$this->actions[]	= Array (	'name' => 'domains',
										'desc' => '<b>Step 6:</b> Import the WhoisCart domain records',
										'depn' => Array('accounts','product','host_tld','invoices') );	
										
		$this->actions[]	= Array (	'name' => 'hosting',
										'desc' => '<b>Step 7:</b> Import the WhoisCart hosting records',
										'depn' => Array('accounts','product','host_tld','invoices','domains') );										

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
		$sql = "SELECT customers.*, 
					authorization.password as password 
				FROM 
					customers,authorization 
				WHERE
					customers.email = authorization.email ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'customers' failed!");	
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
			$msg.= "<BR>Processing account: {$rs->fields['email']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local account id
			$id = $db->GenID($p.'account_id');
			
			# Get orig date
			if(!empty($rs->fields['last_access'])) {
				$dat = explode(' ', $rs->fields['last_access']);
				$date = explode('-', $dat[0]);
				$min = explode(':', $dat[1]);
				$date_orig = mktime($min[0], $min[1], $min[2], $date[1], $date[2], $date[0]);				
			} else {
				$date_orig = time();
			}
			 
			## Get the country:
			$country = $rs->fields['country'];
			$db = &DB();
			$dbm = new CORE_database;
			$rscountry = $db->Execute( $dbm->sql_select('country','id','name = {}', '', &$db) );
			if($rscountry && $rscountry->RecordCount() == 1)
				$country = $rscountry->fields['id'];
			else
				$country = DEFAULT_COUNTRY;
			
			# Insert the account
			$sql = "INSERT INTO {$p}account SET
					id 			= $id,
					site_id		= $s,
					date_orig	= $date_orig,
					date_last	= ".time().",
					language_id	= ".$db->qstr(DEFAULT_LANGUAGE).",
					currency_id	= ".DEFAULT_CURRENCY.",
					theme_id	= ".$db->qstr(DEFAULT_THEME).",
					username	= ".$db->qstr($rs->fields['email']).",
					password	= ".$db->qstr(md5(@$rs->fields['password'])).",
					status		= 1,
					country_id	= {$country},
					first_name	= ".$db->qstr($rs->fields['first_name']).",
					last_name	= ".$db->qstr($rs->fields['last_name']).",
					company		= ".$db->qstr($rs->fields['company']).",
					address1	= ".$db->qstr($rs->fields['address1']).",
					address2	= ".$db->qstr($rs->fields['address2']).",
					city		= ".$db->qstr($rs->fields['city']).",
					state		= ".$db->qstr($rs->fields['region']).",
					zip			= ".$db->qstr($rs->fields['code']).",
					email		= ".$db->qstr($rs->fields['email']).",
					email_type	= 0";
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'account', $id, 'customers', $rs->fields['email'], &$db);		
 	 			
			
			# If cc details exist, import an account_billing record:
			if( !empty($rs->fields['cc_number']) && !empty($rs->fields['cc_expiry']) )
			{
				# Get a local account_billing id
				$bill_id = $db->GenID($p.'account_billing_id');	

				# the whois.cart encryption method is secret, so we have no way to decrypt the cc details
				# we will create a blank CC record that the user or admin can manually update...		 
				$sql = "INSERT INTO {$p}account_billing SET
						id 					= $bill_id,
						site_id				= $s,  
						account_id			= $id,
						checkout_plugin_id 	= $checkout_plugin_id,
						card_type			= 'visa', 
						card_num4			= '0000',
						card_exp_month		= 0,
						card_exp_year		= 0";
				$db->Execute($sql);
				
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'account_billing', $bill_id, 'customers', $rs->fields['email'], &$db);		
 	 						
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
	
	  
	
	
	
	
	
	# Import any products  
	function product()
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
		$sql = "SELECT * FROM hosting_assoc";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'hosting_assoc' failed!");	
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
			unset($recur_price);
			$msg.= "<BR>Processing Server: {$rs->fields['pkey']}...";
			 		
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get server_id from AB or insert now...
			$sql = "SELECT id FROM {$p}host_server WHERE site_id = {$s} AND  name = '{$rs->fields['plug']}'";
			$rshost = $db->Execute($sql);
			if(!$rshost || $rshost->RecordCount() == 0) {
				# Create the server record in AB now:
				$host_server_id = $db->GenID($p.'host_server_id');	
				$sql = "INSERT INTO {$p}host_server SET
						id = {$host_server_id},
						site_id = {$s},						  
						name = ".$db->qstr($rs->fields['plug']).",
						status = 1,
						debug = 1,
						provision_plugin = 'MANUAL',
						notes = 'Imported from whois.cart - be sure to edit to the proper settings for AgileBill',
						name_based_ip = '127.0.0.1',
						name_based = 1";
				$db->Execute($sql);		 
				$this->import_transaction($this->plugin, $VAR['action'], 'host_server', $host_server_id, 'hosting_assoc', $rs->fields['id'], &$db);						
			} else {
				$host_server_id = $rshost->fields['id'];
			}
			
			 
			#### LOOP through each hosting record for this
			#### server and insert product records  
			
			### Connect to the remote Db;
			$dbr = &NewADOConnection($this->type);
			$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
					
			$sql = "SELECT * FROM hosting WHERE package = ".$dbr->qstr($rs->fields['pkey']);
			$rs2 = $dbr->Execute($sql);
			if($rs2 === false) {
				$C_debug->alert("Query to the table 'hosting' failed!");	
				return false;
			}	 
				
			while(!$rs2->EOF)
			{		 				
				if($rs2->fields['active'] == "1")
				$status = 1;
				else
				$status = 0; 

			  
				# Determine the recurring schedule:
				$freq = $rs2->fields['months'];
				if ($freq=="0") 		{ $price_recurr_schedule = "0"; }	// Weekly 
				elseif ($freq=="1")     { $price_recurr_schedule = "1"; }	// Monthly 
				elseif ($freq=="2")  	{ $price_recurr_schedule = "1"; }	// Bi-Monthly
				elseif ($freq=="3")  	{ $price_recurr_schedule = "2"; }	// Quarterly
				elseif ($freq=="6") 	{ $price_recurr_schedule = "3"; }	// Semi-Annually
				elseif ($freq=="12") 	{ $price_recurr_schedule = "4"; }	// Annually
				elseif ($freq=="24") 	{ $price_recurr_schedule = "5"; }	// Annually
				else { $price_recurr_schedule = '1'; } 						// monthly
 
				# Set default recurring prices:  
				$db = &DB();
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
	                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
	                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql);
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[$price_recurr_schedule][$i]['price_base']  = $rs2->fields['price'];
					$recur_price[$price_recurr_schedule][$i]['price_setup'] = $rs2->fields['setup_fee'];
					$recur_price[$price_recurr_schedule]['show'] = "1"; 
					$rsg->MoveNext();
				}
 
				$recur_price[$price_recurr_schedule]['show'] = "1"; 
				$price = $rs2->fields['price'];
				$setup = $rs2->fields['setup_fee'];
				$sku   = $rs2->fields['package'];
				$name  = $rs2->fields['package']; 
				$desc  = $rs2->fields['hosting_features'];
				
				$rs2->MoveNext();
			}
	             	 
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
						sku			= ".$db->qstr($sku).",
						taxable		= 1, 
						active		= $status,
						  
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
			$this->import_transaction($this->plugin, $VAR['action'], 'product', $product_id, 'hosting', $rs->fields['id'], &$db);


			### Insert the description:
			$idx = $db->GenID($p.'product_translate_id');

			$sql = "INSERT INTO {$p}product_translate SET
						id 					= $idx,
						site_id				= $s, 
						product_id			= $product_id,
						language_id 		= '".DEFAULT_LANGUAGE."',  
						name				= ".$db->qstr( $name ).",
						description_short	= ".$db->qstr( $desc ).", 
						description_full	= ".$db->qstr( $desc ) ;
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_translate', $idx, 'hosting', $rs->fields['id'], &$db);
 
					 
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
		$sql = "SELECT * FROM domains ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'domains' failed!");	
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
			$msg.= "<BR>Processing TLD: {$rs->fields['domain']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# delete TLD if it exists already:
			$sql = "DELETE FROM {$p}host_tld WHERE site_id = {$s} AND name = '{$rs->fields['domain']}'";
			$db->Execute($sql);
			
			# Get a local id
			$id = $db->GenID($p.'host_tld_id');
			
			# determine whois_plugin_data
			$whois_plugin_data = serialize( array( 'whois_server' => $rs->fields['server'], 'avail_response' => $rs->fields['recognizer']) );
 
			# determine price group
			$start = $rs->fields['min'];
			$end   = $rs->fields['max'];
			$cost  = $rs->fields['cost'];
			$tcost = $rs->fields['tcost'];
			
			empty($price_group);
			$price_group = Array();
			for($i=$start; $i<=$end; $i++)
			{ 
				# Set default recurring prices:  
				$db = &DB();
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
	                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
	                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql);
				while(!$rsg->EOF) {
					$group = $rsg->fields['id'];
					$price_group["$i"]["show"] = 1;
					$price_group["$i"]["$group"]["register"] = round($cost * $i, 2);
					$price_group["$i"]["$group"]["renew"] = round($cost * $i, 2);
					$price_group["$i"]["$group"]["transfer"] = round( $tcost * $i, 2);
					$rsg->MoveNext();
				}
			}
				 
			# Insert the record
			$sql = "INSERT INTO {$p}host_tld SET
					id 						= $id,
					site_id					= $s, 
					status 					= '1',
					name					= '{$rs->fields['domain']}',
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
			$this->import_transaction($this->plugin, $VAR['action'], 'host_tld', $id, 'domains', $rs->fields['domain'], &$db);		
 	 			 
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
		$sql = "SELECT *, UNIX_TIMESTAMP(date_placed) as date_orig FROM order_list";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'order_list' failed!");	
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
			$msg.= "<BR>Processing Order: {$rs->fields['OID']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			
			
			# Get a local id
			$id = $db->GenID($p.'invoice_id');
			
			# Get orig date
			if(!empty($rs->fields['date_placed'])) { 
				$date_orig = 	$rs->fields['date_orig'];		
			} else {
				$date_orig = time();
			}			
			 			 
			### Get the default checkout plugin id:		
			$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
			$ch = $db->Execute($sql);		
			$checkout_plugin_id = $ch->fields['id'];
						 
			# get the process & billing status
			$process_status = 0;
			$billing_status = 0;
			$billed_amt		= 0;				
			if($rs->fields['processed'] == "1") 
			{ 
				$billing_status = 1;
				$billed_amt		= $rs->fields['total']; 
				if($rs->fields['verified'] == "1") 
					$process_status = 1;   
			} 
			
			# get the account id 
			$sql = "SELECT id FROM {$p}account WHERE site_id = {$s} AND email = ".$db->qstr($rs->fields["customer"]);
			$account = $db->Execute($sql); 
			$account_id = $account->fields['id'];
			
			if($account_id > 0) {
				$db->StartTrans();
			
				# get the billing id
				$sql = "SELECT id FROM {$p}account_billing WHERE site_id = {$s} AND account_id = $account_id";
				$billing = $db->Execute($sql); 
				$billing_id = $billing->fields['id'];			 
		  		
				# Insert the record
				$sql = "INSERT INTO {$p}invoice SET
						id 					= $id,
						site_id				= $s, 
						date_orig			= ".$db->qstr($date_orig).",
						date_last			= ".$db->qstr(time()).", 
						
						process_status		= ".$db->qstr(@$process_status).",
						billing_status		= ".$db->qstr(@$billing_status).",
						account_id			= ".$db->qstr(@$account_id).",
						account_billing_id 	= ".$db->qstr(@$billing_id).", 
						checkout_plugin_id 	= ".$db->qstr(@$checkout_plugin_id).", 
						
						tax_amt				= ".$db->qstr(@$rs->fields['taxes']).",
						discount_amt 		= ".$db->qstr(@$rs->fields['discount']).",
						total_amt			= ".$db->qstr(@$rs->fields['total']).",
						billed_amt			= ".$db->qstr(@$billed_amt).",
						billed_currency_id 	= ".$db->qstr(DEFAULT_CURRENCY).",
						actual_billed_amt 	= ".$db->qstr(@$billed_amt).",
						actual_billed_currency_id = ".$db->qstr(DEFAULT_CURRENCY).",
						
						notice_count		= 0,
						notice_max 			= 1,
						notice_next_date	= ".$db->qstr(time()).",
						grace_period		= 7,
						due_date 			= ".$db->qstr(time());
				$db->Execute($sql);
				 
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'invoice', $id, 'order_list', $rs->fields['OID'], &$db);		
					  
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
		$sql = "SELECT 
				domain_list.*,
				UNIX_TIMESTAMP(domain_list.date) as date_orig,
				UNIX_TIMESTAMP(domain_list.anniversary) as date_anniversary,				
				order_list.domain_list,
				order_list.domain_year_list,
				order_list.domain_price,
				order_list.transfer_list,
				order_list.transfer_price								
				FROM 
				domain_list, order_list
				WHERE 
				domain_list.OID = order_list.OID";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'domain_list, order_list' failed!");	
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
			$msg.= "<BR>Processing Domain: {$rs->fields['domain']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'service_id');
			
			# Get orig date
			if(!empty($rs->fields['date_orig'])) { 	
				$date_orig = $rs->fields['date_orig'];			
			} else {
				$date_orig = time();
			}
			
			# Get anniversary date
			if(!empty($rs->fields['date_anniversary'])) { 				
				$anniversary_date = $rs->fields['date_anniversary'];
			} else {
				$anniversary_date = $date_orig + (86400*31* $rs->fields['months']) ;
			}		 
						 
			### Get the default checkout plugin id:		
			$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
			$ch = $db->Execute($sql);		
			$checkout_plugin_id = $ch->fields['id'];
				 
			# get the account id 
			$sql = "SELECT id FROM {$p}account WHERE site_id = {$s} AND email = '{$rs->fields['customer']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['id'];
			
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
			
			if($invoice_id > 0 && $account_id > 0)
			{
 		 			
				# Determine the domain TLD & Name:
				$domain_name = $rs->fields['domain'];
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
				
				# Determine the domain type (DOMAIN-REGISTER or DOMAIN-TRANSFER or DOMAIN-PARK)
				if(!empty ($rs->fields['domain_list']) && preg_match('@'.$domain.'@', $rs->fields['domain_list'])) {
					$sku = 'DOMAIN-REGISTER';
					$domain_type = 'register';
				} elseif(!empty($rs->fields['transfer_list']) && preg_match('@'.$domain.'@', $rs->fields['transfer_list'])) {
					$sku = 'DOMAIN-TRANSFER';
					$domain_type = 'transfer';
				} else {
					$sku = false;
					$domain_type = 'ns_transfer';
				}
				
				
				# Determine the cost		 
				if($sku == 'DOMAIN-REGISTER')
					$price = $rs->fields['domain_price'];
				elseif ($sku == 'DOMAIN-TRANSFER')
					$price = $rs->fields['transfer_price'];
					
					
				# Determine the term
				$term = $rs->fields['months'] / 12; 
				
				
				# Determine the tld_id 
				$sql = "SELECT id,registrar_plugin_id FROM {$p}host_tld WHERE site_id = {$s} AND name= '{$tld}'";
				$tldrs = $db->Execute($sql); 
				$domain_host_tld_id = $tldrs->fields['id'];	
				$domain_host_registrar_id = $tldrs->fields['registrar_plugin_id'];	
				
		 
	 
				# Insert the record
				if(!empty($sku))
				{
					$sql = "INSERT INTO {$p}service SET
							id 					= $id,
							site_id				= $s, 
							queue				= 'none',
							date_orig			= ".$db->qstr($date_orig).",
							date_last			= ".$db->qstr(time()).",  
							invoice_id			= ".$db->qstr(@$invoice_id).", 
							account_id			= ".$db->qstr(@$account_id).",
							account_billing_id	= ".$db->qstr(@$billing_id).",
							product_id			= ".$db->qstr(@$product_id).",
							sku					= ".$db->qstr($sku).", 
							type				= ".$db->qstr('domain').", 
							active				= 1,  
							price				= ".$db->qstr($price).",
							price_type			= ".$db->qstr('0').",
							taxable				= ".$db->qstr('0').",
							
							host_username		= ".$db->qstr( $rs->fields['username'] ).",
							host_password		= ".$db->qstr( $rs->fields['password'] ).",
							
							domain_date_expire	= ".$db->qstr( $anniversary_date ).",
							domain_host_tld_id	= ".$db->qstr( $domain_host_tld_id ).",
							domain_host_registrar_id = ".$db->qstr( $domain_host_registrar_id ).",
							
							domain_name 		= ".$db->qstr( $domain ).",
							domain_term		  	= ".$db->qstr( $term ).",
							domain_tld  		= ".$db->qstr( $tld ).",
							domain_type			= ".$db->qstr( $domain_type ); 										
					$db->Execute($sql);
					 
					# Insert the import record
					$this->import_transaction($this->plugin, $VAR['action'], 'service', $id, 'domain_list', $rs->fields['BID'], &$db);							  			 
				}
				 
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
		$sql = "SELECT DISTINCT
					host_list.*,
					order_list.hosting_plans,
					order_list.hosting_durations,
					order_list.hosting_prices,
					order_list.hosting_setup_fee,
					order_list.transfer_price,
					UNIX_TIMESTAMP(order_list.date_placed) as date_orig,
					UNIX_TIMESTAMP(order_list.date_processed) as date_processed,
					subscriptions.initial,
					subscriptions.cycle,
					subscriptions.amount,
					subscriptions.bid_list				
				FROM 
					host_list,subscriptions,order_list 
				WHERE 
					host_list.OID = order_list.OID
				AND
				host_list.subscription = subscriptions.id";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'host_list,order_list' failed!");	
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
			$msg.= "<BR>Processing Hosting Plan ID {$rs->fields['BID']} For Customer {$rs->fields['customer']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'service_id');
			
			# Get orig date
			if(!empty($rs->fields['date_orig'])) { 
				$date_orig = $rs->fields['date_orig'];				
			} else {
				$date_orig = time();
			}
			
			# Get anniversary date
			/*
			if(!empty($rs->fields['anniversary'])) {
				$dat = explode(' ', $rs->fields['anniversary']);
				$date = explode('-', $dat[0]); 
				$date_next = mktime(0,0,0, $date[1], $date[2], $date[0]);	
				$date_orig = $rs->fields['date_orig'];				
			} else {
				$date_next = $date_orig + (86400*31* $rs->fields['months']) ;
			}	 
			*/
						 
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
			$sql = "SELECT id FROM {$p}account WHERE site_id = {$s} AND email = '{$rs->fields['customer']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['id'];
			
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
		
}		 		
?>