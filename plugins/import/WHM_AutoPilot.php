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
		$this->db	= 'autopilot';
		$this->user = 'root';
		$this->pass = ''; 
		$this->type	= 'mysql';
		
		# If importing CC details, enter the gateway plugin name to use for recurring charges:
		$this->gateway = 'AUTHORIZE_NET';
		
		# Do not change anything past this line:
		$this->name 		= 'WHM_AutoPilot';
		$this->plugin		= 'WHM_AutoPilot';
		$this->select_limit	= 250;
		
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
										'desc' => '<b>Step 2:</b> Import the WHM Autopilot accounts',
										'depn' => Array('test') );
																	
		$this->actions[]	= Array (	'name' => 'product',
										'desc' => '<b>Step 3:</b> Import the WHM Autopilot servers and hosting packages',
										'depn' => Array('accounts') );
										 					 
		$this->actions[]	= Array (	'name' => 'invoices',
										'desc' => '<b>Step 4:</b> Import the WHM Autopilot invoices, domains, and services',
										'depn' => Array('accounts','product','test') ); 
										 									

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
		$sql = "SELECT * FROM user ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'user' failed!");	
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
			$msg.= "<BR>Processing account: {$rs->fields['username']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local account id
			$id = $db->GenID($p.'account_id');
			
		  
			## Get the country:
			$country = $rs->fields['country'];
			$db = &DB();
			$dbm = new CORE_database;
			$rscountry = $db->Execute(sqlSelect($db,"country","id","two_code = ::{$rs->fields['country']}::")); 
			if($rscountry && $rscountry->RecordCount() == 1)
				$country = $rscountry->fields['id'];
			else
				$country = DEFAULT_COUNTRY;
			
			# Insert the account
			$sql = "INSERT INTO {$p}account SET
					id 			= $id,
					site_id		= 1,
					date_orig	= ".$db->qstr($rs->fields['ogcreate']).",
					date_last	= ".time().",
					language_id	= ".$db->qstr(DEFAULT_LANGUAGE).",
					currency_id	= ".DEFAULT_CURRENCY.",
					theme_id	= ".$db->qstr(DEFAULT_THEME).",
					username	= ".$db->qstr($rs->fields['username']).",
					password	= ".$db->qstr(md5(@$rs->fields['password'])).",
					status		= 1,
					country_id	= {$country},
					first_name	= ".$db->qstr($rs->fields['first_name']).",
					last_name	= ".$db->qstr($rs->fields['last_name']).",
					company		= ".$db->qstr($rs->fields['organization_name']).",
					address1	= ".$db->qstr($rs->fields['street_address_1']).",
					address2	= ".$db->qstr($rs->fields['street_address_2']).",
					city		= ".$db->qstr($rs->fields['city']).",
					state		= ".$db->qstr($rs->fields['state']).",
					zip			= ".$db->qstr($rs->fields['zip_code']).",
					email		= ".$db->qstr($rs->fields['email']).",
					email_type	= 0";
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'account', $id, 'customers', $rs->fields['uid'], &$db);		
 	 			
			
			# If cc details exist, import an account_billing record:
			$dbr = &NewADOConnection($this->type);
			$dbr->Connect($this->host, $this->user, $this->pass, $this->db);  
			
			$rscc = $dbr->Execute(sqlSelect($dbr,"authnet_master_cc","card_info","uid=".$rs->fields['uid']));
			if($rscc && $rscc->RecordCount())
			{
				# Get a local account_billing id
				$bill_id = $db->GenID($p.'account_billing_id');	

				# the WHMA encryption method is secret, so we have no way to decrypt the cc details
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
		$sql = "SELECT * FROM plan_groups";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'plan_groups' failed!");	
			return false;
		}		
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each hosting server
		$i=0;
		while(!$rs->EOF)
		{
			unset($recur_price);
			$msg.= "<BR>Processing Category: {$rs->fields['gid']}...";
			 		
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Insert a WHM server now 
			$sql = "SELECT id FROM {$p}host_server WHERE site_id = {$s} AND  name = 'WHM'";
			$rshost = $db->Execute($sql);
			if(!$rshost || $rshost->RecordCount() == 0) {
				# Create the server record in AB now:
				$host_server_id = $db->GenID($p.'host_server_id');
				$sql = "INSERT INTO {$p}host_server SET
							id = {$host_server_id},
							site_id = {$s},						  
							name = ".$db->qstr('WHM Autopilot Server').",
							status = 1,
							debug = 1,
							provision_plugin = 'WHM',
							notes = 'Imported from WHM Autopilot - be sure to edit to the proper settings for AgileBill',
							name_based_ip = '127.0.0.1',
							name_based = 1";
				$db->Execute($sql);
				$this->import_transaction($this->plugin, $VAR['action'], 'host_server', $host_server_id, 'plan_groups', $rs->fields['gid'], &$db);
			} else {
				$host_server_id = $rshost->fields['id'];
			}			
			
			# Insert this group as a category
			$category_id=sqlGenID($db,"product_cat");
			$fields=Array(
				'group_avail'=>'a:1:{i:0;s:1:"0";}', 
				'name'=>$rs->fields['name'], 
				'status'=>$rs->fields['group_status'],
				'template'=> 'Paged Listing',
				'position'=>1,
				'max'=>10 );
			$db->Execute(sqlInsert($db,"product_cat",$fields,$category_id));
			$this->import_transaction($this->plugin, $VAR['action'], 'product_cat', $category_id, 'plan_groups', $rs->fields['gid'], &$db);
			 
			
			
			#### LOOP through each product record for this server and insert product records  
			$dbr = &NewADOConnection($this->type);
			$dbr->Connect($this->host, $this->user, $this->pass, $this->db);  
			$sql = "SELECT * FROM plan_specs WHERE gid = ".$dbr->qstr($rs->fields['gid']);
			$rs2 = $dbr->Execute($sql);
			if($rs2 === false) {
				$C_debug->alert("Query to the table 'plan_specs' failed!");	
				return false;
			}	 
				
			while(!$rs2->EOF)
			{		  
				# Determine the recurring schedule(s): 
				$price_recurr_schedule = "1";
			    $recur_price=false;
				# Set default recurring prices:  
				$db = &DB();
				$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
	                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
	                            pricing		        = ' . $db->qstr('1');
				$rsg = $db->Execute($sql);
				while(!$rsg->EOF) {
					$i = $rsg->fields['id'];
					$recur_price[1][$i]['price_base']  = $rs2->fields['monthly_cost'];
					$recur_price[1][$i]['price_setup'] = $rs2->fields['setup_cost'];
					$recur_price[1]['show'] = "1"; 
					
					if($rs2->fields['quarterly_cost'] > 0) {
						$recur_price[2][$i]['price_base']  = $rs2->fields['quarterly_cost'];
						$recur_price[2][$i]['price_setup'] = $rs2->fields['setup_cost'];
						$recur_price[2]['show'] = "1"; 						
					}
					
					if($rs2->fields['semi_annual_cost'] > 0) {
						$recur_price[3][$i]['price_base']  = $rs2->fields['semi_annual_cost'];
						$recur_price[3][$i]['price_setup'] = $rs2->fields['semi_annual_cost'];
						$recur_price[3]['show'] = "1"; 						
					}			
					
					if($rs2->fields['annual_cost'] > 0) {
						$recur_price[4][$i]['price_base']  = $rs2->fields['annual_cost'];
						$recur_price[4][$i]['price_setup'] = $rs2->fields['annual_cost'];
						$recur_price[4]['show'] = "1"; 						
					}							
					
					$rsg->MoveNext();
				}
  
				$price = $rs2->fields['monthly_cost'];
				$setup = $rs2->fields['setup_cost'];
				$sku   = $rs2->fields['package_name'];
				$name  = $rs2->fields['package_name']; 
				$desc  = $rs2->fields['display_text'];
				
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
						id 				= $product_id,
						site_id			= 1, 
						sku				= ".$db->qstr($name).",
						taxable			= 1, 
						active			= 1,
						  
						price_type		= 1,
						price_base		= '{$price}',
						price_setup		= '{$setup}',
						price_group		= ".$db->qstr( serialize( @$recur_price ) ).",	 
						
						price_recurr_default 	= '".@$price_recurr_schedule."',
						price_recurr_type		= '".@$price_recurr_type."',
						price_recurr_weekday 	= '".@$price_recurr_weekday."',
						price_recurr_week		= '".@$price_recurr_week."',
						price_recurr_schedule 	= '".@$price_recurr_schedule."',
						price_recurr_cancel 	= 1, 
						
						host 					= 1,
						host_server_id 			= '{$host_server_id}',
						
						avail_category_id 		= ".$db->qstr( serialize(array($category_id)));
			$db->Execute($sql);

			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product', $product_id, 'plan_specs', $rs2->fields['pid'], &$db);


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
			$this->import_transaction($this->plugin, $VAR['action'], 'product_translate', $idx, 'plan_specs', $rs2->fields['pid'], &$db);
 
					 
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
		$sql = "SELECT * FROM invoice";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table 'invoice' failed!");	
			return false;
		}	 
		
		$db = &DB();
			 
		### Get the default checkout plugin id:		
		$sql = "SELECT id FROM {$p}checkout WHERE
				site_id = $s AND
				checkout_plugin = '{$this->gateway}'";
		$ch = $db->Execute($sql);		
		$checkout_plugin_id = $ch->fields['id'];
		
		### Get the server
		$sql = "SELECT id FROM {$p}host_server WHERE site_id = {$s} AND  name = 'WHM'";
		$rshost = $db->Execute($sql);		
		$host_server_id = $rshost->fields['id'];
							
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		# loop through each remote account
		while(!$rs->EOF)
		{ 	 
			# get the process & billing status
			$process_status = 0;
			$billing_status = 0;
			$billed_amt		= 0;				
 
			# get the account id  
			$p = AGILE_DB_PREFIX;
			$dbn = &DB();			
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = 1 AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['uid']}'";
			$account = $dbn->Execute($sql);  
			if($account && $account->RecordCount()) 
			{ 
				$msg.= "<BR>Processing Invoice: {$rs->fields['iid']}...";
				
				$account_id = $account->fields['ab_id'];
				 
				# get the billing id
				$dba=&DB();
				$sql = "SELECT id FROM {$p}account_billing WHERE site_id = {$s} AND account_id = $account_id";
				$billing = $dba->Execute($sql); 
				$billing_id = $billing->fields['id'];	
	  
				if($rs->fields['status'] != 1) {
					$billed_amt=0;
				} else {
					$billed_amt=$rs->fields['total_due_today'];
				}
				
				if($rs->fields['due_date'] > 1)
				$due_date = $rs->fields['due_date'];
				else
				$due_date = $rs->fields['created'];
				 
				# Get a local id
				$id = $db->GenID($p.'invoice_id');
				$invoice_id=$id;
							
				# Insert the record
				$sql = "INSERT INTO {$p}invoice SET
						id 					= $id,
						site_id				= 1, 
						date_orig			= ".$db->qstr($rs->fields['created']).",
						date_last			= ".$db->qstr(time()).", 
						
						process_status		= ".$db->qstr($rs->fields['status']).",
						billing_status		= ".$db->qstr($rs->fields['status']).",
						
						account_id			= ".$db->qstr(@$account_id).",
						account_billing_id 	= ".$db->qstr(@$billing_id).", 
						checkout_plugin_id 	= ".$db->qstr(@$checkout_plugin_id).", 
						
						tax_amt				= ".$db->qstr(@$rs->fields['charge_tax']).", 
						total_amt			= ".$db->qstr(@$rs->fields['total_due_today']).",
						billed_amt			= ".$db->qstr(@$billed_amt).",
						billed_currency_id 	= ".$db->qstr(DEFAULT_CURRENCY).",
						actual_billed_amt 	= ".$db->qstr(@$billed_amt).",
						actual_billed_currency_id = ".$db->qstr(DEFAULT_CURRENCY).",
						
						notice_count		= 0,
						notice_max 			= 1,
						notice_next_date	= ".$db->qstr(time()).",
						grace_period		= 7,
						due_date 			= ".$db->qstr( $due_date );
				$db->Execute($sql);
				 
				# Insert the import record
				$this->import_transaction($this->plugin, $VAR['action'], 'invoice', $id, 'order_list', $rs->fields['iid'], &$db);
				 
			 
				### Get / Insert Invoice Items:
				$dbr = &NewADOConnection($this->type);
				$dbr->Connect($this->host, $this->user, $this->pass, $this->db);  
				$sql = "SELECT * FROM hosting_order WHERE oid = ".$dbr->qstr($rs->fields['oid']);
				$rs2 = $dbr->Execute($sql);
				if($rs2 === false) {
					$C_debug->alert("Query to the table 'hosting_order' failed!");	
					return false;
				}
								
				while(!$rs2->EOF) {
					
					# Determine the domain type (DOMAIN-REGISTER or DOMAIN-TRANSFER or DOMAIN-PARK)
					if(!empty ($rs2->fields['domain_registration'])) {
						$sku = 'DOMAIN-REGISTER';
						$domain_type = 'register';
					}  else {
						$sku = "DOMAIN-TRANSFER";
						$domain_type = 'ns_transfer';
					}
					$domain_sku = $sku;
					
					$domain = ereg_replace("^www.", "", $rs2->fields['domain_name']); 					
					$parking=ereg("(\.)([a-zA-Z0-9.-]+)", $domain, $ret);
					 
					$tld=ereg_replace("^\.", "", $ret[0]);
					$domain = ereg_replace("$tld$", "", $domain);
					$domain = ereg_replace("\.", "", $domain);
					 
					$domain_host_tld_id=0;
					$tldrs = $db->Execute(sqlSelect($db,"host_tld","id","name='$tld'"));	 
					if($tldrs && $tldrs->recordCount) 
						$domain_host_tld_id = $tldrs->fields['id'];
			 
						
					// get product details 
					$pid =$rs2->fields['pid'];
					$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
								ab_table = 'product' AND
								plugin = '{$this->plugin}' AND
								remote_id = '{$pid}'";
					$produrs = $dba->Execute($sql);  
					if($produrs && $produrs->RecordCount() && !empty($product_id)) {	
						$product_id = $produrs->fields['ab_id'];				
						$product = $db->Execute(sqlSelect($db,"product","*","id=$product_id"));
						if($product && $product->RecordCount()) {					
							$sku = $product->fields['sku'];
						}
					}

					// insert invoice item
					$dbo=&DB();
					$idx = sqlGenID($dbo, "invoice_item");
					$sql = "INSERT INTO {$p}invoice_item SET
								id 					= $idx,
								site_id				= 1,  
								parent_id			= 0,
								invoice_id			= ".$db->qstr(@$invoice_id).", 
								product_id			= ".$db->qstr(@$product_id).",
								date_orig			= ".$db->qstr($rs->fields['created']).",  
								sku					= ".$db->qstr($sku).",
								quantity			= 1,
								item_type			= 1,  
								price_type			= 1,
								price_base			= ".$db->qstr( $rs->fields['total_due_today'] ).", 
								domain_name 		= ".$db->qstr( $domain ).", 
								domain_tld  		= ".$db->qstr( $tld ); 					 
					$dbo->Execute($sql);
					
					 
					# Insert the import record
					$this->import_transaction($this->plugin, $VAR['action'], 'invoice_item', $idx, 'hosting_order', $rs2->fields['oid'], &$db);
										
					
					// SERVICE: insert domain record 
					if($rs->fields['status'] ==1 && empty($rs2->fields['domain_registration']))
					{
						if(!empty($rs2->fields['domain_expire'])) { 
							$d=explode("/", $rs2->fields['domain_expire']);
							@$anniversary_date = mktime(0,0,0,$d[0],$d[1],$d[2]);
						} else {
							$anniversary_date = $rs2->fields['ogcreate']+86400*1;
						}
						
						$dby=&DB();
						$s_id = sqlGenID($dby, "service");
						$sql = "INSERT INTO {$p}service SET
								id 					= $s_id,
								site_id				= 1, 
								queue				= 'none',
								date_orig			= ".$db->qstr($rs->fields['created']).",
								date_last			= ".$db->qstr(time()).",  
								invoice_id			= ".$db->qstr(@$invoice_id).", 
								account_id			= ".$db->qstr(@$account_id).",
								account_billing_id	= ".$db->qstr(@$billing_id).", 
								sku					= ".$db->qstr($domain_sku).", 
								type				= ".$db->qstr('domain').", 
								active				= 1,  
								price				= ".$db->qstr($rs->fields['total_due_today']).",
								price_type			= ".$db->qstr('0').",
								taxable				= ".$db->qstr('0').",
								 
								domain_date_expire	= ".$db->qstr( $anniversary_date ).",
								domain_host_tld_id	= ".$db->qstr( $domain_host_tld_id ).",
								domain_host_registrar_id = 1,
								
								domain_name 		= ".$db->qstr( $domain ).",
								domain_term		  	= ".$db->qstr( 1 ).",
								domain_tld  		= ".$db->qstr( $tld ).",
								domain_type			= ".$db->qstr( $domain_type ); 										
						$dby->Execute($sql);
						 
						# Insert the import record
						$this->import_transaction($this->plugin, $VAR['action'], 'service', $s_id, 'hosting_order', $rs2->fields['oid'], &$db);							  			 
					}
				 			
					
					//	SERVICE: Insert hosting record	
					if($rs->fields['status'] ==1)
					{ 
						$recur_schedule=1;
						switch($rs2->fields['payment_term']) {
							case "Monthly":
								$recur_schedule=1;
							break;
							
							case "Quarterly":
								$recur_schedule=2;
							break;
							
							case "Semi-Annual":
								$recur_schedule=3;
							break;
														
							case "Annual":
								$recur_schedule=4;
							break;  
						}
						
						
						$dbx=&DB();
						$s2_id = sqlGenID($dbx, "service");
						$sql = "INSERT INTO {$p}service SET
									id 					= $s2_id,
									site_id				= 1, 
									queue				= 'active',
									date_orig			= ".$db->qstr($rs->fields['created']).",
									date_last			= ".$db->qstr(time()).",  
									invoice_id			= ".$db->qstr(@$invoice_id).", 
									account_id			= ".$db->qstr(@$account_id).",
									account_billing_id	= ".$db->qstr(@$billing_id).",
									product_id			= ".$db->qstr(@$product_id).", 
									sku					= ".$db->qstr('HOSTING').",  
									type				= ".$db->qstr('host').", 
									
									active				= 1, 
									suspend_billing		= 0, 
									
									date_last_invoice	= ".$db->qstr( $rs->fields['created'] ).",
									date_next_invoice	= ".$db->qstr( $rs2->fields['next_due_date'] ).",
									 
									price				= ".$db->qstr( $rs2->fields['total_due_reoccur'] ).",
									price_type			= 1,
									taxable				= 1,	
													
									recur_type			= ".$db->qstr( @$product->fields['price_recurr_type'] ).",
									recur_schedule		= ".$db->qstr( $recur_schedule ).",
									recur_weekday		= ".$db->qstr( @$product->fields['price_recurr_weekday']).",
									recur_week			= ".$db->qstr( @$product->fields['price_recurr_week']).",
									recur_cancel		= ".$db->qstr( @$product->fields['price_recurr_cancel']).",
									recur_schedule_change = ".$db->qstr( @$product->fields['price_recurr_modify']).",
			
									host_username		= ".$db->qstr( $rs2->fields['whm_username'] ).",
									host_password		= ".$db->qstr( $rs2->fields['whm_password'] ).",
									
									host_server_id			= ".$db->qstr( @$product->fields['host_server_id'] ).",
									host_provision_plugin_data 	= ".$db->qstr( @$product->fields['host_provision_plugin_data'] ).",
									host_ip					= ".$db->qstr( $rs2->fields['ip'] ).",
									 
									domain_host_tld_id		= ".$db->qstr( $domain_host_tld_id ).",
									domain_host_registrar_id = 1,
									
									domain_name 			= ".$db->qstr( $domain ).", 
									domain_tld  			= ".$db->qstr( $tld ) ;										
						$dbx->Execute($sql);		
						
						# Insert the import record
						$this->import_transaction($this->plugin, $VAR['action'], 'service', $s2_id, 'hosting_order', $rs2->fields['oid'], &$db);										
						
						 
					} 
					$rs2->MoveNext(); 
				}	 
			} else {
				echo "<BR><BR> No Account: <BR> $sql";
			}  
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