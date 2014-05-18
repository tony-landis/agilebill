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
		# Configure the location of the dreamaccount salt file:
		$this->salt = PATH_AGILE . 'salt.php';
		
		# Configure the database name, host, and login:
		$this->host	= 'localhost';
		$this->db	= '';
		$this->user = '';
		$this->pass = ''; 
		$this->type	= 'mysql';
		
		# If importing CC details, enter the gateway plugin to use for recurring charges:
		$this->gateway = 'AUTHORIZE_NET';
		
		# Do not change anything past this line:
		$this->name 		= 'DreamAccount';
		$this->plugin		= 'DreamAccount';
		$this->select_limit	= 50;
		
		$this->instructions = '<B>Preliminary Instructions:</B><BR><BR>Open '. __FILE__ .' and edit the 
								database and salt file settings...<BR><BR>
								If you will be importing credit card details, paste the Checkout Plugin
								name from the checkout plugin list page to the "$this->gateway" value
								that will be used to process all recurring charges... 
								this should be a gateway such as AUTHORIZE_NET or
								LINKPOINT.<BR><BR>';
						
		$this->actions[]	= Array (	'name' => 'test',
										'desc' => '<b>Step 1:</b> Test the database connection',
										'depn' => false );
																				
		$this->actions[]	= Array (	'name' => 'accounts',
										'desc' => '<b>Step 2:</b> Import the DreamAccount accounts',
										'depn' => Array('test') );

		$this->actions[]	= Array (	'name' => 'billing',
										'desc' => '<b>Step 3:</b> Import the DreamAccount account billing details',
										'depn' => Array('accounts') );
																	
		$this->actions[]	= Array (	'name' => 'categories',
										'desc' => '<b>Step 4:</b> Import the DreamAccount product categories',
										'depn' => Array('accounts') );
										
		$this->actions[]	= Array (	'name' => 'directory',
										'desc' => '<b>Step 5:</b> Import the DreamAccount protected directories',
										'depn' => Array('accounts') );										
 					
		$this->actions[]	= Array (	'name' => 'products',
										'desc' => '<b>Step 6:</b> Import the DreamAccount product definitions',
										'depn' => Array('accounts','directory','categories') );

		$this->actions[]	= Array (	'name' => 'invoices',
										'desc' => '<b>Step 7:</b> Import the DreamAccount invoices',
										'depn' => Array('accounts','directory','categories','products') );

		$this->actions[]	= Array (	'name' => 'services',
										'desc' => '<b>Step 8:</b> Import the DreamAccount subscriptions',
										'depn' => Array('accounts','directory','categories','products','invoices') );	
										
		$this->actions[]	= Array (	'name' => 'notes',
										'desc' => '<b>Step 9:</b> Import the DreamAccount notes for services, accounts, and invoices',
										'depn' => Array('accounts','services','invoices') );											

	}
	
	# test remote database connectivity
	function test()
	{
		
		### Connect to the remote Db;
		$dbr = &NewADOConnection($this->type);
		$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 
		
		global $C_debug, $VAR;
		
		if(!empty($dbr->_errorMsg))
		{  
			$C_debug->alert('Failed: ' . $dbr->_errorMsg);
		} 
		else
		{
			$C_debug->alert('Connected OK!');
			 
			# Write success to database
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
 
		# select each account from Dreamaccount
		$sql = "SELECT * FROM account"; 
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'account' failed!");	
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
			$msg.= "<BR>Processing account: {$rs->fields['account_username']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local account id
			$id = $db->GenID($p.'account_id');
			
			# Get orig date
			if(!empty($rs->fields['orig_date'])) {
				$date = explode('-', $rs->fields['orig_date']);
				$date_orig = mktime(0,0,0,$date[1], $date[2], $date[0]);
			} else {
				$date_orig = time();
			}
			
			# Get the first/last name
			$name = explode(' ', $rs->fields['account_name']);
			@$firstn = $name[0];
			@$c = count($name) -1;
			@$lastn = $name[$c];
			
			# Insert the account
			$sql = "INSERT INTO {$p}account SET
					id 			= $id,
					site_id		= $s,
					date_orig	= $date_orig,
					date_last	= ".time().",
					language_id	= ".$db->qstr(DEFAULT_LANGUAGE).",
					currency_id	= ".DEFAULT_CURRENCY.",
					theme_id	= ".$db->qstr(DEFAULT_THEME).",
					username	= ".$db->qstr($rs->fields['account_username']).",
					password	= ".$db->qstr(md5($rs->fields['account_password'])).",
					status		= 1,
					country_id	= {$rs->fields['account_country']},
					first_name	= ".$db->qstr($firstn).",
					last_name	= ".$db->qstr($lastn).",
					company		= ".$db->qstr($rs->fields['account_company']).",
					address1	= ".$db->qstr($rs->fields['account_address']).",
					city		= ".$db->qstr($rs->fields['account_city']).",
					state		= ".$db->qstr($rs->fields['account_state']).",
					zip			= ".$db->qstr($rs->fields['account_zip']).",
					email		= ".$db->qstr($rs->fields['account_email']).",
					email_type	= 0";
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'account', $id, 'account', $rs->fields['account_id'], &$db);		
 	 			
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
	
	
	
	
	
	### Import the billing details for each account
	function billing()
	{
		global $VAR, $C_debug;
		$p = AGILE_DB_PREFIX;
		$s = DEFAULT_SITE;
		
		# validate the salt file...
		if(!is_file($this->salt)) {
			$C_debug->alert('The path to the salt file set in the plugin script '. __FILE__.' is incorrect');
			return;
		}
		
		### Determine the offset for the account
		if(empty($VAR['offset'])) $VAR['offset'] = 0;
		@$offset = $VAR['offset'].",".$this->select_limit;		
		
		### Select from the imported accounts
		$db = &DB();
		$sql = "SELECT * FROM {$p}import WHERE
				plugin 		= '{$this->plugin}' AND
				action 		= 'accounts' AND
				ab_table 	= 'account' AND
				site_id		= $s";
		$rs = $db->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'import' failed!");	
			return false;
		}	
  
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		} 
		
		### Include AB Encryption class:
		include_once(PATH_CORE.'crypt.inc.php');
		
		
		### Get the default checkout plugin id:		
		$sql = "SELECT id FROM {$p}checkout WHERE
				site_id = $s AND
				checkout_plugin = '{$this->gateway}'";
		$ch = $db->Execute($sql);		
		$checkout_plugin_id = $ch->fields['id'];
		
		
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote billing record
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing Account Id: {$rs->fields['ab_id']}...";
			
			# start a new transaction for the insert: 
			$db->StartTrans();
			
			# Get the local account id
			$ab_account_id = $rs->fields['ab_id'];
			$remote_account_id = $rs->fields['remote_id'];
			 
			# Connect to the remote DB and get all billing records for this
			# account, where the cc_num is not blank
			$dbr = &NewADOConnection($this->type);
			$dbr->Connect($this->host, $this->user, $this->pass, $this->db); 		
			$sql = "SELECT * FROM billing WHERE
					billing_account_id = $remote_account_id AND
					billing_cc_num != ''";
			$billing = $dbr->Execute($sql);
			if($billing != false && $billing->RecordCount() > 0)
			{
				while(!$billing->EOF)
				{ 
					# Get local billing id
					$db = &DB();
					$id = $db->GenID($p.'account_billing_id');

 			
					# Decrypt the remote CC 
					$cc_num_plain = $this->RC4($billing->fields['billing_cc_num'], 'de');
					
					# Encrypt to local algorythm
					$card_num = CORE_encrypt ($cc_num_plain);
							 		
					# get the last 4 digits:
					$last_four = preg_replace('/^............/', '', $cc_num_plain);
					 
					# Identify the card type:
					$card_type = $this->cc_identify($cc_num_plain);
					
					# Get the month  & year
					$exp = explode('20', trim($billing->fields['billing_cc_exp']));
					$exp_month = @$exp[0]; 
					$exp_year = @$exp[1];
					 
					if($card_type != '') 
					{ 
						# Start transaction
						$db->StartTrans();
											
						# Insert local billing record 
						$sql = "INSERT INTO {$p}account_billing SET
								id 					= $id,
								site_id				= $s,  
								account_id			= $ab_account_id,
								checkout_plugin_id 	= $checkout_plugin_id,
								card_type			= '$card_type',
								card_num			= ".$db->qstr($card_num).",
								card_num4			= '$last_four',
								card_exp_month		= '$exp_month',
								card_exp_year		= '$exp_year'";
						$db->Execute($sql);
						
						# Insert the import record
						$this->import_transaction($VAR['plugin'], $VAR['action'], 'account_billing', $id, 'billing', $billing->fields['billing_id'], &$db);		
			 	 			
						# Complete the transaction
			        	$db->CompleteTrans(); 
					}
					$billing->MoveNext(); 
				}
			}  
			$rs->MoveNext();
		}	

		$C_debug->alert($msg);	
		$offset =  $VAR['offset'] + $this->select_limit;
		echo "<script language=javascript> 
			 setTimeout('document.location=\'?_page=core:blank&offset={$offset}&action={$VAR['action']}&plugin={$VAR['plugin']}&do[]=import:do_action\'', 1500);
			 </script>"; 		
	}
	
	
	  	
	# Import any categories
	function categories()
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
 
		# select each account from Dreamaccount
		$sql = "SELECT * FROM category";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'category' failed!");	
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
			$msg.= "<BR>Processing category: {$rs->fields['name']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'product_cat_id');
	  
			# Insert the record
			$sql = "INSERT INTO {$p}product_cat SET
					id 			= $id,
					site_id		= $s, 
					name		= ".$db->qstr($rs->fields['name']).",
					notes		= ".$db->qstr($rs->fields['description']).",
					status		= 1,
					template	= 1,
					position	= 1";
			$db->Execute($sql);
			 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_cat', $id, 'categories', $rs->fields['id'], &$db);		
			 
			# Get a local id
			$idx = $db->GenID($p.'product_cat_translate_id');
						
			# Insert the translation
			$sql = "INSERT INTO {$p}product_cat_translate SET
					id 			= $id,
					site_id		= $s, 
					product_cat_id = $id,
					language_id = ".$db->qstr(DEFAULT_LANGUAGE).",
					name		= ".$db->qstr($rs->fields['name']).",
					description	= ".$db->qstr($rs->fields['description']);
			$db->Execute($sql);			
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_cat_translate', $idx, 'categories', $rs->fields['id'], &$db);		
 	 			
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
	
	
	
	
	
	
	# Import any groups, htaccess groups, and directories
	function directory()
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
 
		# select each account from Dreamaccount
		$sql = "SELECT * FROM directory";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'directory' failed!");	
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
			$msg.= "<BR>Processing protected directory: {$rs->fields['directory_name']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'group_id');
	  
			# Insert the record
			$sql = "INSERT INTO {$p}group SET
					id 			= $id,
					site_id		= $s, 
					date_orig	= 0,
					date_start  = 0,
					date_expire = 0,					
					status 		= 1,
					parent_id	= 2,
					pricing		= 0,				
					name		= ".$db->qstr($rs->fields['directory_name']).", 
					notes		= ".$db->qstr('Imported from DreamAccount');
			$db->Execute($sql);
			  
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'group', $id, 'directory', $rs->fields['directory_id'], &$db);		
			  
			# add access to the new group from the users account:  
    		$record_id = $db->GenID(AGILE_DB_PREFIX . 'account_group_id');
    		$sql= "INSERT INTO ". AGILE_DB_PREFIX ."account_group SET
    			   	id			= ".$db->qstr($record_id).",
    				site_id 	= ".$db->qstr(DEFAULT_SITE).", 
    			  	date_orig	= ".$db->qstr(time()).",
                    date_expire = ".$db->qstr('0').",
    				group_id	= ".$db->qstr($id).",
    				account_id	= ".$db->qstr(SESS_ACCOUNT).",
    				active		= ".$db->qstr(1);
    		$result = $db->Execute($sql); 
  
    		$this->import_transaction($this->plugin, $VAR['action'], 'account_group', $record_id, 'directory', $rs->fields['directory_id'], &$db);		
    		
            # update the current user's authentication so the newly added group appears
            # as available to them
            global $C_auth;
            $C_auth->auth_update();
             
			
			if($rs->fields['directory_type'])
			{
				### Create HTACCESS GROUP
				global $C_list;
				if($C_list->is_installed('htaccess'))
				{
					# Get a local id
					$idx = $db->GenID($p.'htaccess_id');		
					
					# Insert the record
					$sql = "INSERT INTO {$p}htaccess SET
							id 			= $idx,
							site_id		= $s,  
							status		= 1,
							group_avail = ".$db->qstr( serialize ( Array($id,1001)) ).", 
							name		= ".$db->qstr($rs->fields['directory_name']).", 
							description	= ".$db->qstr('Imported from DreamAccount');
					$db->Execute($sql);													 
				 
					# Insert the import record
					$this->import_transaction($this->plugin, $VAR['action'], 'htaccess', $idx, 'directory', $rs->fields['directory_id'], &$db);		

					if($rs->fields['directory_type'] == '0')
					{  
						# Get a local id
						$idxx = $db->GenID($p.'htaccess_dir_id');		
						
						# Insert the record
						$sql = "INSERT INTO {$p}htaccess_dir SET
								id 			= $idxx,
								site_id		= $s,  
								htaccess_id = $idx,
								status		= 1,
								type		= 1,
								recursive	= 1,
								url			= ".$db->qstr($rs->fields['directory_url']).", 
								path		= ".$db->qstr($rs->fields['directory_path']).",   
								name		= ".$db->qstr($rs->fields['directory_name']).", 
								description	= ".$db->qstr('Imported from DreamAccount');
						$db->Execute($sql);													 
						 
						# Insert the import record
						$this->import_transaction($this->plugin, $VAR['action'], 'htaccess_dir', $idxx, 'directory', $rs->fields['directory_id'], &$db);								 
					}			
				}
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
 
		# select each product from Dreamaccount that is NOT a trial
		$sql = "SELECT * FROM membership ORDER BY trial ASC";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'membership' failed!");	
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
			$msg.= "<BR>Processing Product: {$rs->fields['membership_name']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
						
			if($rs->fields['membership_active'] == "Y")
			$status = 1;
			else
			$status = 0;
			
			# get category
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND 
					action = 'categories' AND
					plugin = '{$this->plugin}' AND
					remote_id = '{$rs->fields['category']}'";
			$cat = $db->Execute($sql);
			$categories = serialize ( Array( $cat->fields['ab_id'] ) );
			
			# price type (trial, one-time, recurring)
			if($rs->fields['trial'] == "Y")
			{
				# trial
				$price_type = '2';
			} elseif($rs->fields['membership_recurring'] == "Y") {
				# recurring
				$price_type = '1';
			} else {
				# one-time
				$price_type = '0';
			}
			
            # defaults for 'recurring' product
            if($price_type == "1")
            { 
                # Determine the recurring schedule:
                $freq = $rs->fields['membership_frequency']; 
				if ($freq=="7") 		{ $price_recurr_schedule = "0"; }	// weekly
				elseif ($freq=="14") 	{ $price_recurr_schedule = "0"; }	// Bi-Weekly
				elseif ($freq=="30")    { $price_recurr_schedule = "1"; }	// Monthly
				elseif ($freq=="31")  	{ $price_recurr_schedule = "1"; }	// Monthly
				elseif ($freq=="60")  	{ $price_recurr_schedule = "1"; }	// Bi-Monthly
				elseif ($freq=="90")  	{ $price_recurr_schedule = "2"; }	// Quarterly
				elseif ($freq=="180") 	{ $price_recurr_schedule = "3"; }	// Semi-Annually
				elseif ($freq=="360") 	{ $price_recurr_schedule = "4"; }	// Annually
				elseif ($freq=="365") 	{ $price_recurr_schedule = "4"; }	// Annually 
				else { $price_recurr_schedule = '1'; } 						// monthly
				
				
                $price_recurr_type 		= "0"; 
                $price_recurr_week 		= "1";
                $price_recurr_weekday 	= "1";				
				$price_recurr_default 	= $price_recurr_schedule; 
                
				
                # Set default recurring prices: (monthly only) 
                $sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
                            pricing		        = ' . $db->qstr('1');
                $rsg = $db->Execute($sql); 
                while(!$rsg->EOF) { 
                	$i = $rsg->fields['id'];  
	                $recur_price[0][$i]['price_base']  = $rs->fields['membership_price'];
					$recur_price[0][$i]['price_setup'] = $rs->fields['membership_setup']; 
					@$recur_price[1][$i]['price_base'] = $rs->fields['membership_price'];
					@$recur_price[1][$i]['price_setup']= $rs->fields['membership_setup']; 
	                $recur_price[2][$i]['price_base']  = $rs->fields['membership_price'];
					$recur_price[2][$i]['price_setup'] = $rs->fields['membership_setup']; 
	                $recur_price[3][$i]['price_base']  = $rs->fields['membership_price'];
					$recur_price[3][$i]['price_setup'] = $rs->fields['membership_setup']; 
	                $recur_price[4][$i]['price_base']  = $rs->fields['membership_price'];
					$recur_price[4][$i]['price_setup'] = $rs->fields['membership_setup'];
	                $recur_price[5][$i]['price_base']  = $rs->fields['membership_price'];
					$recur_price[5][$i]['price_setup'] = $rs->fields['membership_setup'];
                	$rsg->MoveNext();	
                } 
                
                $recur_price[0]['show'] = "0"; 					
                $recur_price[1]['show'] = "0";
                $recur_price[2]['show'] = "0"; 
                $recur_price[3]['show'] = "0"; 
                $recur_price[4]['show'] = "0"; 
                $recur_price[5]['show'] = "0";   
                $recur_price[$price_recurr_schedule]['show'] = "1";                 
            }			
            
            # defaults for trial products
            if($price_type == "2")
            {
				# get trial plan id
				$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND 
						action = 'product' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['trial_plan']}'";
				$cat = $db->Execute($sql);
				$price_trial_prod = serialize ( Array( $cat->fields['ab_id'] ) );             	
            }
				
            # Get associated group
            if($rs->fields['membership_directory_id'] > 0)
            {
            	# get directory (group) id
				$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND 
						ab_table = 'group' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['membership_directory_id']}'";
				$cat = $db->Execute($sql);
				$assoc_grant_group = serialize ( Array( $cat->fields['ab_id'] ) );  
 
				$assoc_grant_group_type = 1; 
            }  	
             
			# Get a local id
			$id = $db->GenID($p.'product_id');            
	  
			# Insert the record
			$sql = "INSERT INTO {$p}product SET
					id 			= $id,
					site_id		= $s, 
					sku			= 'DA-$id',
					taxable		= 0, 
					active		= $status,
					  
					price_type		= '$price_type',
					price_base		= '{$rs->fields['membership_price']}',
					price_setup		= '{$rs->fields['membership_setup']}',
					price_group		= ".$db->qstr( serialize(@$recur_price) ).",	 
					
					price_recurr_default 	= '".@$price_recurr_default."',
					price_recurr_type		= '".@$price_recurr_type."',
					price_recurr_weekday 	= '".@$price_recurr_weekday."',
					price_recurr_week		= '".@$price_recurr_week."',
					price_recurr_schedule 	= '".@$price_recurr_schedule."',
					price_recurr_cancel 	= 1,
					
					price_trial_length_type = 0,
					price_trial_length 		= 30,
					price_trial_prod 		= '".@$price_trial_prod."',
					
					assoc_grant_group		= ".$db->qstr( @$assoc_grant_group ).",
					assoc_grant_group_type	= ".$db->qstr( @$assoc_grant_group_type ).", 
					 
					avail_category_id 		= ".$db->qstr($categories);
			$db->Execute($sql);
			 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product', $id, 'membership', $rs->fields['membership_id'], &$db);		
			  
			
			### Insert the description:
			$idx = $db->GenID($p.'product_translate_id');
			
			$sql = "INSERT INTO {$p}product_translate SET
					id 					= $idx,
					site_id				= $s, 
					product_id			= $id,
					language_id 		= '".DEFAULT_LANGUAGE."',  
					name				= ".$db->qstr( $rs->fields['membership_name'] ).",
					description_short	= ".$db->qstr( $rs->fields['membership_desc'] ).", 
					description_full	= ".$db->qstr( $rs->fields['membership_desc'] ) ;
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_translate', $idx, 'membership', $rs->fields['membership_id'], &$db);
				 
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
	
	
	
	
	
	
	
	### Import all invoices from DreamAccount
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
 
		# select each account from Dreamaccount
		$sql = "SELECT * FROM orders";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'orders' failed!");	
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
			$msg.= "<BR>Processing Order: {$rs->fields['order_id']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'invoice_id');
			
			# Get orig date
			if(!empty($rs->fields['order_date'])) {
				$date = explode('-', $rs->fields['order_date']);
				$date_orig = mktime(0,0,0,$date[1], $date[2], $date[0]);
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
			if($rs->fields['order_status'] == 1)
			{
				$process_status = 1;
				$billing_status = 1;
				$billed_amt		= $rs->fields['order_amount'];
			}
			else 
			{
				$process_status = 0;
				$billing_status = 0;
				$billed_amt		= 0;				
			} 
			
			# get the account id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['order_account_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
			
			# get the billing id
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account_billing' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['order_billing_id']}'";
			$billing = $db->Execute($sql); 
			$billing_id = $billing->fields['ab_id'];			 
	  		
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
					
					tax_amt				= ".$db->qstr(@$rs->fields['tax_amount']).",
					discount_amt 		= ".$db->qstr(@$rs->fields['credit_amount'] + $rs->fields['coupon_amount']).",
					total_amt			= ".$db->qstr(@$rs->fields['order_amount']).",
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
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice', $id, 'invoices', $rs->fields['order_id'], &$db);		
					  
			
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
	
	
	
	
	## Import DA subscriptions & line items
	function services()
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
 
		# select each account from Dreamaccount
		$sql = "SELECT * FROM domains";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
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
			$msg.= "<BR>Processing Subscription: {$rs->fields['domain_id']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'service_id');
			
			# Get orig date
			if(!empty($rs->fields['domain_start_date'])) {
				$date = explode('-', $rs->fields['domain_start_date']);
				$date_orig = mktime(0,0,0,$date[1], $date[2], $date[0]);
			} else {
				$date_orig = time();
			} 
			
			# Get last billed date date
			if(!empty($rs->fields['domain_host_last_billed'])) {
				$date = explode('-', $rs->fields['domain_host_last_billed']);
				$date_last = mktime(0,0,0,$date[1], $date[2], $date[0]);
			} else {
				$date_last = $date_orig;
			} 
						 
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
						remote_id = '{$rs->fields['domain_account_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
			
			# get the billing id
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account_billing' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['domain_billing_id']}'";
			$billing = $db->Execute($sql); 
			$billing_id = $billing->fields['ab_id'];	

			# get the invoice id
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'invoice' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['domain_order_id']}'";
			$invoice = $db->Execute($sql); 
			$invoice_id = $invoice->fields['ab_id'];	
							 
			# get the product id
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'product' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['domain_host_id']}'";
			$product = $db->Execute($sql); 
			$product_id = $product->fields['ab_id'];
			
			# Get the product details	
			$sql = "SELECT * FROM {$p}product WHERE site_id = {$s} AND id = {$product_id}";
			$product = $db->Execute($sql);  
			
			
			# Status
			if($rs->fields['domain_host_status'] == 1) {
				$active = 1;
				$suspend = 0;
			} else {
				$active = 0;
				$suspend = 1;				
			}
			 
			# Calculate next bill date:  
			include_once(PATH_MODULES . 'service/service.inc.php');
			$service = new service;
			$date_next = $service->calcNextInvoiceDate( $date_last,
													    $product->fields['price_recurr_default'],
														$product->fields['price_recurr_type'],
														$product->fields['price_recurr_weekday'],
														$product->fields['price_recurr_week'] );
 
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
					sku					= ".$db->qstr($product->fields['sku']).", 
					type				= ".$db->qstr('group').", 
					active				= ".$db->qstr($active).", 
					suspend_billing		= ".$db->qstr($suspend).", 
					date_last_invoice	= ".$db->qstr($date_last).",
					date_next_invoice	= ".$db->qstr($date_next).", 
					price				= ".$db->qstr($product->fields['price_base']).",
					price_type			= ".$db->qstr($product->fields['price_type']).",
					taxable				= ".$db->qstr($product->fields['taxable']).",					
					recur_type			= ".$db->qstr($product->fields['price_recurr_type']).",
					recur_schedule		= ".$db->qstr($product->fields['price_recurr_schedule']).",
					recur_weekday		= ".$db->qstr($product->fields['price_recurr_weekday']).",
					recur_week			= ".$db->qstr($product->fields['price_recurr_week']).",
					recur_cancel		= ".$db->qstr($product->fields['price_recurr_cancel']).",
					group_grant			= ".$db->qstr($product->fields['assoc_grant_group']).",
					group_type			= ".$db->qstr($product->fields['assoc_grant_group_type']);
			
			$db->Execute($sql);
			 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'service', $id, 'services', $rs->fields['domain_id'], &$db);		
					  
			 
			# Insert the invoice item:
			$idx = $db->GenID($p.'invoice_item_id');
			
			$sql = "INSERT INTO {$p}invoice_item SET
					id 					= $id,
					site_id				= $s,  
					invoice_id			= ".$db->qstr(@$invoice_id).", 
					product_id			= ".$db->qstr(@$product_id).",
					date_orig			= ".$db->qstr($date_orig).", 
					sku					= ".$db->qstr($product->fields['sku']).",
					quantity			= 1,
					item_type			= 0,
					price_type			= ".$db->qstr($product->fields['price_recurr_type']).",
					price_base			= ".$db->qstr($product->fields['price_base']).",
					price_setup			= ".$db->qstr($product->fields['price_setup']).",
					recurring_schedule  = ".$db->qstr($product->fields['price_recurr_schedule']); 					 
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice_item', $id, 'services', $rs->fields['domain_id'], &$db);		
			  
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
	
	
	# Import any notes  
	function notes()
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
		$sql = "SELECT * FROM notes";
		$rs = $dbr->SelectLimit($sql, $this->select_limit, $VAR['offset']);
		if($rs === false) {
			$C_debug->alert("Query to the table 'notes' failed!");	
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
			$msg.= "<BR>Processing Note ID: {$rs->fields['note_id']}...";
			 		
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			

			if(!empty($rs->fields['note_account_id'])) 
			{
				### ACCOUNT MEMO
				# get the account id 
				$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
							ab_table = 'account' AND
							plugin = '{$this->plugin}' AND
							remote_id = '{$rs->fields['note_account_id']}'";
				$account = $db->Execute($sql); 
				$account_id = $account->fields['ab_id'];		
				
				# Create the server record in AB now:
				$id = $db->GenID($p.'host_server_id');
				$sql = "INSERT INTO {$p}account_memo SET
							id = {$id},
							site_id = {$s},
							date_orig = ".time().",
							staff_id = 1,
							account_id = {$account_id},
							type = 'admin',
							memo = ".$db->qstr( $rs->fields['note_message'] );
				$db->Execute($sql);
			 
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'account_memo', $id, 'notes', $rs->fields['note_id'], &$db);
 
			}
			elseif(!empty( $rs->fields['note_order_id'] ))
			{
				### Invoice Memo
				# get the invoice id
				$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
							ab_table = 'invoice' AND
							plugin = '{$this->plugin}' AND
							remote_id = '{$rs->fields['note_order_id']}'";
				$invoice = $db->Execute($sql); 
				$invoice_id = $invoice->fields['ab_id'];				
				
				# Create the server record in AB now:
				$id = $db->GenID($p.'invoice_memo_id');
				$sql = "INSERT INTO {$p}invoice_memo SET
							id = {$id},
							site_id = {$s},
							date_orig = ".time().",
							account_id = 1,
							invoice_id  = {$invoice_id},
							type = '',
							memo = ".$db->qstr( $rs->fields['note_message'] );
				$db->Execute($sql);
			 
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'invoice_memo', $id, 'notes', $rs->fields['note_id'], &$db);
				
			} 
			elseif(!empty( $rs->fields['note_domain_id'] ))
			{
				### Service Memo
				# get the service id
				$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
							ab_table = 'service' AND
							plugin = '{$this->plugin}' AND
							remote_id = '{$rs->fields['note_domain_id']}'";
				$service = $db->Execute($sql); 
				$service_id = $invoice->fields['ab_id'];				
				
				# Create the server record in AB now:
				$id = $db->GenID($p.'service_memo_id');
				$sql = "INSERT INTO {$p}service_memo SET
							id = {$id},
							site_id = {$s},
							date_orig = ".time().",
							staff_id = 1,
							service_id  = {$service_id},
							type = 'admin',
							memo = ".$db->qstr( $rs->fields['note_message'] );
				$db->Execute($sql);
			 
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'service_memo_id', $id, 'notes', $rs->fields['note_id'], &$db);				
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
	
	
	
	// decryption function for old DA credit cards
	function RC4($data, $case) {
 
		include($this->salt);

		if ($case == 'de') {
			$data = urldecode($data);
		}
		$key[] = "";
		$box[] = "";
		$temp_swap = "";
		$pwd_length = 0;
		$pwd_length = strlen($pwd);

		for ($i = 0; $i <= 255; $i++) {
			$key[$i] = ord(substr($pwd, ($i % $pwd_length), 1));
			$box[$i] = $i;
		}
		$x = 0;

		for ($i = 0; $i <= 255; $i++) {
			$x = ($x + $box[$i] + $key[$i]) % 256;
			$temp_swap = $box[$i];
			$box[$i] = $box[$x];
			$box[$x] = $temp_swap;
		}
		$temp = "";
		$k = "";
		$cipherby = "";
		$cipher = "";
		$a = 0;
		$j = 0;
		for ($i = 0; $i < strlen($data); $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$temp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $temp;
			$k = $box[(($box[$a] + $box[$j]) % 256)];
			$cipherby = ord(substr($data, $i, 1)) ^ $k;
			$cipher .= chr($cipherby);
		}

		if ($case == 'de') {
			$cipher = urldecode(urlencode($cipher));
		} else {
			$cipher = urlencode($cipher);
		}
		return $cipher;
	}	
	
	
	// DETERMINE CREDIT CARD TYPE
	function cc_identify($cc_no)     {
         $cc_no = preg_replace ('/[^0-9]+/', '', $cc_no);

        // Get card type based on prefix and length of card number
        if (preg_match ('/^4(.{12}|.{15})$/', $cc_no)) {
            return 'visa';
        } elseif (preg_match ('/^5[1-5].{14}$/', $cc_no)) {
            return 'mc';
        } elseif (preg_match ('/^3[47].{13}$/', $cc_no)) {
            return 'amex';
        } elseif (preg_match ('/^3(0[0-5].{11}|[68].{12})$/', $cc_no)) {
            return 'diners';
        } elseif (preg_match ('/^6011.{12}$/', $cc_no)) {
            return 'discover';
        } elseif (preg_match ('/^(3.{15}|(2131|1800).{11})$/', $cc_no)) {
            return 'jcb';
        } elseif (preg_match ('/^2(014|149).{11})$/', $cc_no)) {
            return 'enrout';
       } else {
 		 return "";
       }
	}	
}		 		
?>