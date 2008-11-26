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
		# Configure the location of the aMember salt file:
		#$this->salt = PATH_AGILE . 'salt.php';
		
		# Configure the database name, host, and login:
		$this->host	= 'localhost';
		$this->db	= 'amember';
		$this->user = 'root';
		$this->pass = ''; 
		$this->prefix = 'amember_';
		$this->type	= 'mysql';
		
		# If importing CC details, enter the gateway plugin to use for recurring charges:
		$this->gateway = 'AUTHORIZE_NET';
		
		# Do not change anything past this line:
		$this->name 		= 'Amember_2_3_6';
		$this->plugin		= 'Amember_2_3_6';
		$this->select_limit	= 100;
		
		$this->instructions = '<P><B>Preliminary Instructions:</B></P>
		
								<P>Open '. __FILE__ .' and edit the  database settings... </P>
								
								<P>If you will be importing credit card details, paste the Checkout Plugin
								name from the checkout plugin list page to the "$this->gateway" value
								that will be used to process all recurring charges... 
								this should be a gateway such as AUTHORIZE_NET or LINKPOINT.</P>
								
								<P>After completing the import, you should setup any htaccess and/or protected files
								associated with the new groups created during the import.</P>
								
								<P>Then go to Tasks > Service Queue > and run the task to update the 
								group settings for all the imported accounts.</P>
								
								
								';
						
		$this->actions[]	= Array (	'name' => 'test',
										'desc' => '<b>Step 1:</b> Test the aMember database connection',
										'depn' => false );
												 
		$this->actions[]	= Array (	'name' => 'accounts',
										'desc' => '<b>Step 4:</b> Import the aMember accounts and billing details',
										'depn' => Array ('test') );
										
		$this->actions[]	= Array (	'name' => 'products',
										'desc' => '<b>Step 2:</b> Import the aMember products',
										'depn' => Array('accounts') );
 
		$this->actions[]	= Array (	'name' => 'invoices',
										'desc' => '<b>Step 3:</b> Import the aMember invoices and services',
										'depn' => Array('products') );
										  
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
			
			$sql = "SELECT * FROM {$this->prefix}products";
			$rs = Execute($sql);
			if($result === false) 
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
 
		# select each account from aMember
		echo $sql = "SELECT *, UNIX_TIMESTAMP(added) as date_orig FROM {$this->prefix}members";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table '{$this->prefix}members' failed!");	
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
			$msg.= "<BR>Processing account: {$rs->fields['login']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
			
			# Get a local account id
			$id = $db->GenID($p.'account_id');
			
			# Get orig date
			if(!empty($rs->fields['date_orig'])) { 
				$date_orig = $rs->fields['date_orig'];
			} else {
				$date_orig = time();
			}
			 
			# Insert the account
			$sql = "INSERT INTO {$p}account SET
					id 			= $id,
					site_id		= $s,
					date_orig	= $date_orig,
					date_last	= ".time().",
					language_id	= ".$db->qstr(DEFAULT_LANGUAGE).",
					currency_id	= ".DEFAULT_CURRENCY.",
					theme_id	= ".$db->qstr(DEFAULT_THEME).",
					username	= ".$db->qstr($rs->fields['login']).",
					password	= ".$db->qstr(md5($rs->fields['pass'])).",
					status		= 1,
					country_id	= ".$db->qstr($rs->fields['country']).",
					first_name	= ".$db->qstr($rs->fields['name_f']).",
					last_name	= ".$db->qstr($rs->fields['name_l']).", 
					address1	= ".$db->qstr($rs->fields['street']).",
					city		= ".$db->qstr($rs->fields['city']).",
					state		= ".$db->qstr($rs->fields['state']).",
					zip			= ".$db->qstr($rs->fields['zip']).",
					email		= ".$db->qstr($rs->fields['email']).",
					email_type	= 0";
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'account', $id, 'members', $rs->fields['member_id'], &$db);		
 	 			 
			if(!empty($rs->fields['data']))
			{
				$data = unserialize($rs->fields['data']);
				if(is_array($data))
				{
					if( !empty($data['cc']) && !empty($data['cc-expire'])   )
					{
						### Insert a CC record for this user:		
						$idx = $db->GenID($p.'account_billing_id');
						
						$exp_month	= substr($data['cc-expire'], 0,2);
						$exp_year 	= substr($data['cc-expire'], 2,2);
						$cc_num   	= $data['cc'];
						$last_four	= substr($cc_num,(strlen($cc_num) - 4),4);
						$card_type	= $this->cc_identify($cc_num);
						$card_num 	= CORE_encrypt ($cc_num);
						
						# Insert local billing record 
						$sql = "INSERT INTO {$p}account_billing SET
								id 					= $idx,
								site_id				= $s,  
								account_id			= $id,
								checkout_plugin_id 	= $checkout_plugin_id,
								card_type			= '$card_type',
								card_num			= ".$db->qstr($card_num).",
								card_num4			= '$last_four',
								card_exp_month		= '$exp_month',
								card_exp_year		= '$exp_year'";
						$db->Execute($sql);
						 	
						# Update the account
						$sql = "UPDATE {$p}account SET    
								address1	= ".$db->qstr( @$data['cc_street'] ).",
								city		= ".$db->qstr( @$data['cc_city'] ).",
								state		= ".$db->qstr( @$data['cc_state'] ).",
								zip			= ".$db->qstr( @$data['cc_zip'] )."
								WHERE   id  = $id";
						$db->Execute($sql);
 
						# Insert the import record
						$this->import_transaction($VAR['plugin'], $VAR['action'], 'account_billing', $idx, 'billing', $rs->fields['member_id'], &$db);											
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
 
		 
		# select each product from aMember that is NOT a trial
		$sql = "SELECT * FROM {$this->prefix}products ";
		$rs = $dbr->SelectLimit($sql, $offset);
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
			$msg.= "<BR>Processing Product: {$rs->fields['title']}...";
			
			# start a new transaction for the insert:
			$db = &DB();
			$db->StartTrans();
					
			unset($recur_price);
				 
			$status 	= 1; 
			$price_type = 1; 
			$categories = serialize ( Array( 0,1,2,3) );
			
			
			# Determine the recurring schedule:
			$data = unserialize($rs->fields['data']); 
			if(ereg("y$", $data['expire_days']))
			{
				$years 	= ereg_replace("y$", "", $data['expire_days']);
				switch ($years) {
					case '1':
						$price_recurr_schedule = "4";
						break;
					case '2':
						$price_recurr_schedule = "5";
						break;		
					default:
						$price_recurr_schedule = "4"; 
				}									
					 
			}
			else if(ereg("m$", $data['expire_days']))
			{
				$months = ereg_replace("m$", "", $data['expire_days']);
				switch ($months) {
					case '1':
						$price_recurr_schedule = "1";
						break;
					case '2':
						$price_recurr_schedule = "1";
						break;		
					case '2':
						$price_recurr_schedule = "1";
						break;
					case '3':
						$price_recurr_schedule = "2";
						break;
					case '4':
						$price_recurr_schedule = "2";
						break;
					case '6':
						$price_recurr_schedule = "3";
						break;
					case '12':
						$price_recurr_schedule = "4";
						break;																														
					default:
						$price_recurr_schedule = "1"; 
				}				
			}
 			else 
 			{
 				$days	= $data['expire_days'];
				switch ($days) {
					case '7':
						$price_recurr_schedule = "0";
						break;
					case '30':
						$price_recurr_schedule = "1";
						break;		
					case '31':
						$price_recurr_schedule = "1";
						break;
					case '60':
						$price_recurr_schedule = "2";
						break;
					case '90':
						$price_recurr_schedule = "2";
						break;
					case '120':
						$price_recurr_schedule = "3";
						break;
					case '365':
						$price_recurr_schedule = "4";
						break;																														
					default:
						$price_recurr_schedule = "0"; 
				}		 				
 			}
 			 
			$price = $rs->fields['price'];
			$price_recurr_type 		= "0";
			$price_recurr_week 		= "1";
			$price_recurr_weekday 	= "1";
			$price_recurr_default 	= $price_recurr_schedule;


			# Set default group pricing:  
			for($i=0;$i<6;$i++) $recur_price[$i]['show'] = "0"; 
			$recur_price[$price_recurr_schedule]['show'] = "1";
						
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
                            site_id         	= ' . $db->qstr(DEFAULT_SITE) . ' AND
                            pricing		        = ' . $db->qstr('1');
			$rsg = $db->Execute($sql);
			while(!$rsg->EOF) {
				$i = $rsg->fields['id']; 
				$recur_price[$price_recurr_schedule][$i]['price_base']  = $price; 
				$recur_price[$price_recurr_schedule][$i]['price_setup'] = 0;  
				$rsg->MoveNext();
			} 
			 
			
            # Create a new group for this product:
            $group_id = $db->GenID($p.'group_id');   
			$assoc_grant_group_type = 1; 
			$assoc_grant_group = serialize ( Array( $group_id ) );  
			
	  
			# Insert the record
			$id = $db->GenID($p.'product_id');   
			$sql = "INSERT INTO {$p}product SET
					id 			= $id,
					site_id		= $s, 
					sku			= 'AMEMBER-$id',
					taxable		= 0, 
					active		= 1,
					  
					price_type	= '$price_type',
					price_base	= '{$price}', 
					price_group	= ".$db->qstr( serialize(@$recur_price) ).",	 
					
					price_recurr_default 	= '".@$price_recurr_default."',
					price_recurr_type		= '".@$price_recurr_type."',
					price_recurr_weekday 	= '".@$price_recurr_weekday."',
					price_recurr_week		= '".@$price_recurr_week."',
					price_recurr_schedule 	= '".@$price_recurr_schedule."',
					price_recurr_cancel 	= 1,
					   
					assoc_grant_group		= ".$db->qstr( @$assoc_grant_group ).",
					assoc_grant_group_type	= ".$db->qstr( @$assoc_grant_group_type ).", 
					 
					avail_category_id 		= ".$db->qstr($categories);
			$db->Execute($sql);
			 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product', $id, 'products', $rs->fields['product_id'], &$db);		
			  
			
			### Insert the description:
			$idx = $db->GenID($p.'product_translate_id');
			
			$sql = "INSERT INTO {$p}product_translate SET
					id 					= $idx,
					site_id				= $s, 
					product_id			= $id,
					language_id 		= '".DEFAULT_LANGUAGE."',  
					name				= ".$db->qstr( $rs->fields['title'] ).",
					description_short	= ".$db->qstr( $rs->fields['description'] ).", 
					description_full	= ".$db->qstr( $rs->fields['description'] ) ;
			$db->Execute($sql);
 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'product_translate', $idx, 'products', $rs->fields['product_id'], &$db);
				  
			### Insert the group: 
			$sql = "INSERT INTO {$p}group SET 
					id 			= $group_id,
					site_id		= $s, 
					date_orig	= 0,
					date_start  = 0,
					date_expire = 0,					
					status 		= 1,
					parent_id	= 2,
					pricing		= 0,				
					name		= ".$db->qstr($rs->fields['title']).", 
					notes		= ".$db->qstr('Imported from aMember Product ID' . $rs->fields['product_id'] );
			$db->Execute($sql);
 
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'group', $group_id, 'products', $rs->fields['product_id'], &$db);
			 
			
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

	 
	 
	 
	
	 
	
	### Import all invoices from aMember
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
 
		# select each order from aMember 
		$sql = "SELECT *,
				UNIX_TIMESTAMP(tm_added) as date_orig,
				UNIX_TIMESTAMP(tm_completed) as date_completed,
				UNIX_TIMESTAMP(begin_date) as date_begin,
				UNIX_TIMESTAMP(expire_date) as date_expire 
				FROM {$this->prefix}payments ";
		$rs = $dbr->SelectLimit($sql, $offset);
		if($rs === false) {
			$C_debug->alert("Query to the table '{$this->prefix}payments' failed!");	
			return false;
		}	
		
		if($rs->RecordCount() == 0) {
			$C_debug->alert("No more records to process!");	
			echo "<script language=javascript>setTimeout('document.location=\'?_page=import:import&plugin={$VAR['plugin']}\'', 1500); </script>";			
			return;
		}
		 
		$msg = "Processing ".$rs->RecordCount()." Records...<BR>";
		
		
		### Get the default checkout plugin id:
		$db = &DB();
		$sql = "SELECT id FROM {$p}checkout WHERE
					site_id = $s AND
					checkout_plugin = '{$this->gateway}'";
		$ch = $db->Execute($sql);
		$checkout_plugin_id = $ch->fields['id'];
					
		
		# loop through each remote record
		while(!$rs->EOF)
		{
			$msg.= "<BR>Processing Order: {$rs->fields['payment_id']}...";
			
			# start a new transaction for the insert: 
			$db->StartTrans();
			
			# Get a local id
			$id = $db->GenID($p.'invoice_id');
			  	 
			# get the process & billing status
			if($rs->fields['completed'] == 1) {
				$process_status = 1;
				$billing_status = 1;
				$billed_amt		= $rs->fields['amount'];
			} else  {
				$process_status = 0;
				$billing_status = 0;
				$billed_amt		= 0;				
			} 
			
			# get the account id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'account' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['member_id']}'";
			$account = $db->Execute($sql); 
			$account_id = $account->fields['ab_id'];
			
			# get the billing id
			$sql = "SELECT id FROM {$p}account_billing WHERE site_id = {$s} AND account_id = '{$account_id}'";
			$billing = $db->Execute($sql); 
			$billing_id = $billing->fields['id'];			 
	  		
			# Insert the record
			$sql = "INSERT INTO {$p}invoice SET
					id 					= $id,
					site_id				= $s, 
					date_orig			= ".$db->qstr($rs->fields['date_orig']).",
					date_last			= ".$db->qstr(time()).", 
					
					process_status		= ".$db->qstr(@$process_status).",
					billing_status		= ".$db->qstr(@$billing_status).",
					account_id			= ".$db->qstr(@$account_id).",
					account_billing_id 	= ".$db->qstr(@$billing_id).", 
					checkout_plugin_id 	= ".$db->qstr(@$checkout_plugin_id).", 
					
					tax_amt				= 0,
					discount_amt 		= 0,
					total_amt			= ".$db->qstr(@$rs->fields['amount']).",
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
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice', $id, 'payments', $rs->fields['payment_id'], &$db);		
					  
			
			 
			# Get the product id 
			$sql = "SELECT ab_id FROM {$p}import WHERE site_id = {$s} AND
						ab_table = 'product' AND
						plugin = '{$this->plugin}' AND
						remote_id = '{$rs->fields['product_id']}'";
			$producta = $db->Execute($sql); 
			$product_id = $producta->fields['ab_id'];
			 
			$sql = "SELECT * FROM {$p}product WHERE site_id = {$s} AND id = '{$product_id}'";
			$product = $db->Execute($sql);  		
 
			# Insert the invoice item:
			$idx = $db->GenID($p.'invoice_item_id');
			
			$sql = "INSERT INTO {$p}invoice_item SET
					id 					= $idx,
					site_id				= $s,  
					invoice_id			= $id, 
					product_id			= ".$db->qstr(@$product_id).",
					date_orig			= ".$db->qstr( $rs->fields['date_orig'] ).", 
					sku					= ".$db->qstr($product->fields['sku']).",
					quantity			= 1,
					item_type			= 0,
					price_type			= ".$db->qstr($product->fields['price_recurr_type']).",
					price_base			= ".$db->qstr($product->fields['price_base']).",
					price_setup			= ".$db->qstr($product->fields['price_setup']).",
					recurring_schedule  = ".$db->qstr($product->fields['price_recurr_schedule']); 					 
			$db->Execute($sql);
			
			# Insert the import record
			$this->import_transaction($this->plugin, $VAR['action'], 'invoice_item', $id, 'payments', $rs->fields['payment_id'], &$db);		
			
			
			
			### CREATE THE SERVIEC
			if($process_status)
			{
				
				if(time() <= $rs->fields['date_expire']) 
				{
					$active = 1;
					$queue = 'active';
				} else {
					$active = 0;
					$queue = 'none';
				} 
				
				# Insert the record
				$service_id = $db->GenID($p.'service_id');
				$sql = "INSERT INTO {$p}service SET
						id 					= $service_id,
						site_id				= $s, 
						queue				= '{$queue}',
						date_orig			= ".$db->qstr( $rs->fields['date_orig'] ).",
						date_last			= ".$db->qstr(time()).",  
						invoice_id			= ".$db->qstr(@$id).", 
						account_id			= ".$db->qstr(@$account_id).",
						account_billing_id	= ".$db->qstr(@$billing_id).",
						product_id			= ".$db->qstr(@$product_id).",
						sku					= ".$db->qstr($product->fields['sku']).", 
						type				= ".$db->qstr('group').", 
						active				= ".$db->qstr($active).",   
						date_last_invoice	= ".$db->qstr( $rs->fields['date_orig'] ).",
						date_next_invoice	= ".$db->qstr( $rs->fields['date_expire'] ).", 
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
				$this->import_transaction($this->plugin, $VAR['action'], 'service', $service_id, 'payments', $rs->fields['payment_id'], &$db);		
				 
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
         $cc_no = ereg_replace ('[^0-9]+', '', $cc_no);

        // Get card type based on prefix and length of card number
        if (ereg ('^4(.{12}|.{15})$', $cc_no)) {
            return 'visa';
        } elseif (ereg ('^5[1-5].{14}$', $cc_no)) {
            return 'mc';
        } elseif (ereg ('^3[47].{13}$', $cc_no)) {
            return 'amex';
        } elseif (ereg ('^3(0[0-5].{11}|[68].{12})$', $cc_no)) {
            return 'diners';
        } elseif (ereg ('^6011.{12}$', $cc_no)) {
            return 'discover';
        } elseif (ereg ('^(3.{15}|(2131|1800).{11})$', $cc_no)) {
            return 'jcb';
        } elseif (ereg ('^2(014|149).{11})$', $cc_no)) {
            return 'enrout';
       } else {
 		 return "";
       }
	}	
}		 		
?>