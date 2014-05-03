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
	
class service
{
	##############################
	## 	Resend hosting details	##
	##############################
	function resend_hosting_email($VAR)
	{
		if(!empty($VAR['id']))
		{
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$email = new email_template;
			$email->send('host_new_user', $VAR['account_id'], $VAR['id'], '', '');
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('hosting_email_sent','service',''));
		}
	}

	##############################
	## 	Cleanup group access	##
	##############################
	function cleanup($VAR)
	{
		# update services to suspended that meet the following criteria:
		# one-time charge (cannot be subscription)
		# group access of any kind
		# expired by the access days permited

		$db = &DB();
		$sql = 'SELECT id,group_days,date_orig
                     	FROM ' . AGILE_DB_PREFIX . 'service WHERE
						type LIKE 	  ' . $db->qstr("%group%") . ' AND
						queue 	   != ' . $db->qstr("inactive") . ' AND
						active	 	= ' . $db->qstr(1) . ' AND
						price_type 	= ' . $db->qstr(0) . ' AND
						group_type 	= ' . $db->qstr(0) . ' AND
						group_days 	> ' . $db->qstr(0) . ' AND   
                       	site_id     = ' . $db->qstr(DEFAULT_SITE);  		
		$service =  $db->Execute($sql);
		$total   =  $service->RecordCount();

		$i=0;
		while(!$service->EOF)
		{
			# check if expired:
			$exp = $service->fields['date_orig'] + ( $service->fields['group_days'] * 86400 );
			if( time() > $exp )
			{
				# Update the service status:
				# should we delete instead? todo
				# should we send email notification? todo

				$sql = 'UPDATE ' . AGILE_DB_PREFIX . 'service
						SET 
						queue 		= ' . $db->qstr("inactive") . ',
						date_last 	= ' . $db->qstr(time()) . ',
						active	 	= ' . $db->qstr(0) . '
						WHERE 
						id		 	= ' . $db->qstr($service->fields['id']) . ' AND   
                       	site_id     = ' . $db->qstr(DEFAULT_SITE); 	
				$db->Execute($sql);
				$i++;
			}
			$service->MoveNext();
		}

		# Display results:
		$remain = $total - $i;
		$msg = "While cleaning up one-time Services granting group access,
					located $i Services(s) that have expired and were suspended
					and $remain Service(s) are active and will remain active.";


		# void all services that have been canceled by the user
		# or billing has been suspended by the admin that
		# would normally be due for billing now.

		$sql = 'SELECT id
                     	FROM ' . AGILE_DB_PREFIX . 'service WHERE 
						queue 	   		!= ' . $db->qstr("inactive") . ' AND
						active	 		= ' . $db->qstr(1) . ' AND
						suspend_billing	= ' . $db->qstr(1) . ' AND
						date_next_invoice <= ' . $db->qstr(time()) . ' AND    
                       	site_id     	= ' . $db->qstr(DEFAULT_SITE);  		
		$service =  $db->Execute($sql);
		$total   =  $service->RecordCount();
		if($total > 0) {
			while(!$service->EOF) {
				# deactivate:
				$this->voidService($service->fields['id']);
				$service->MoveNext();
			}

			# Display results:
			$remain = $total - $i;
			$msg .= "<BR><BR>While searching for services cancelled by the user or admin,
						$total service(s) were located and have been voided.";
		}

		global $C_debug;
		$C_debug->alert($msg);
		return true;
	}

	
	/** User reactivate */
	function user_reactivate($VAR) {
		if(!SESS_LOGGED || empty($VAR['id'])) return false;
		
		global $C_debug, $C_translate, $smarty;
		
		/* get service details */
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db,"service","*","id=::{$VAR['id']}::"));
		if($rs && $rs->RecordCount()) {
			extract($rs->fields);

			/* can reinstate? */
			if(!$suspend_billing || SESS_ACCOUNT != $account_id || !$recur_cancel) {
				$C_debug->alert('This service cannot be reactivated at this time.');		
				return false;
			}
			 
			/* invoice date needs moved? */
			if($active == 1 && $date_next_invoice >= time()) {
				/* no, change the suspend_billing status */
				$fields=Array('suspend_billing'=>0);
				$db->Execute(sqlUpdate($db,"service",$fields,"id=::{$VAR['id']}:: "));
				$C_debug->alert('This service has been reactivated and will continue to be billed normally without service interruption.');
			} else {
				/* no, change the suspend_billing status */
				$fields=Array('suspend_billing'=>0, 'date_next_invoice'=>time()+86400);
				$db->Execute(sqlUpdate($db,"service",$fields,"id=::{$VAR['id']}:: "));				
				$C_debug->alert('An invoice for this service will be generated within the next 24 hours and service will be reactivated immediately after payment of that invoice is made.');
			}
		}			
	}


	##############################
	## USER MODIFY RECURR SCHED ##
	##############################
	function user_changeschedule($VAR)
	{
		global $C_translate, $C_debug;

		if(!isset($VAR['id']) || !isset($VAR['service_recur_schedule'])) return false;

		# get the account id & confirm changing the schedule is allowed
		$db = &DB();
		$dbm = new CORE_database;
		$sql = $dbm->sql_select("service","*","id = {$VAR['id']}", "", $db);
		$service =  $db->Execute($sql);
		if($service->fields['account_id'] == SESS_ACCOUNT &&  $service->fields['recur_schedule_change'] == 1)
		{
			# prev schedule
			$prev =  $service->fields['recur_schedule'];

			# current schedule
			$cur = $VAR['service_recur_schedule'];
			if(!is_numeric($cur) || $cur > 6)
			return false;

			# validate a change has occurred
			if($cur != $prev)
			{
				$this->changeschedule($cur, $prev, $service, $VAR);
			}
		}
		else
		{
			$msg = $C_translate->translate('changeservice_auth','service', '');
			$C_debug->alert($msg);
		}
	}




	##############################
	## ADMIN MODIFY RECURR SCHED #
	##############################
	function admin_changeschedule($VAR)
	{
		global $C_translate, $C_debug;

		if(!isset($VAR['id']) || !isset($VAR['service_recur_schedule'])) return false;

		# get the account id & confirm changing the schedule is allowed
		$db = &DB();
		$dbm = new CORE_database;
		$sql = $dbm->sql_select("service","*","id = {$VAR['id']}", "", $db);
		$service =  $db->Execute($sql);

		# prev schedule
		$prev =  $service->fields['recur_schedule'];

		# current schedule
		$cur = $VAR['service_recur_schedule'];
		if(!is_numeric($cur) || $cur > 6)
		return false;

		# validate a change has occurred
		if($cur != $prev)
		{
			$this->changeschedule($cur, $prev, $service, $VAR);
		}
	}



	##############################
	##    CANCEL SERVICES       ##
	##############################
	function changeschedule($cur, $prev, &$service, $VAR)
	{
		global $C_translate, $C_debug, $C_auth;
		$db = &DB();

		# Get the associated product:
		$product = $service->fields['product_id'];

		# Validate a product is associated with this service:
		if($product > 0 )
		{
			$dbm = new CORE_database;

			# Get the product details:
			$sql = $dbm->sql_select('product','*',"id = $product", '', $db);
			$prod = $db->Execute($sql);

			# Get the price for the associated product and billing schedule
			if($prod->fields['price_recurr_default'] == $cur)
			{
				# Use default base price:
				$price = $prod->fields['price_base'];
			}
			else
			{
				$arr = unserialize($prod->fields['price_group']);
				if(is_array($arr))
				{
					# Get the base price for the selected period:
					$price = false;
					$parr = $arr["$cur"];

					# Loop through each group price and assign this user the lowest available price:
					while(list($group, $parr2) = each($parr))
					{
						if(isset($parr2["price_base"])) {
							$arr_price = $parr2["price_base"];
							if($arr_price != '' && $C_auth->auth_group_by_id($group))
							if($price == false || $price > $arr_price)
							$price = $arr_price;
						}
					}
				}
			}

			# Update service status
			$q = "UPDATE ".AGILE_DB_PREFIX."service SET
	                            recur_schedule 	= $cur,
	                            price		   	= '$price'
	                            WHERE
	                            id          	= {$VAR['id']} AND
	                            site_id     	= ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);
		} else {
			# Update service status
			$q = "UPDATE ".AGILE_DB_PREFIX."service SET recur_schedule = $cur WHERE id = {$VAR['id']} AND site_id = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);
		}

		# Create a memo
		$fields=Array('date_orig'=>time(), 'staff_id'=> SESS_ACCOUNT, 'service_id'=>$VAR['id'], 'type'=> 'changeschedule', 'memo'=> "Changed recurring schedule from $prev to $cur");
		$db->Execute($sql=sqlInsert($db,"service_memo",$fields));

		return true;
	}




	##############################
	##  USER CANCEL SERVICES    ##
	##############################
	function user_cancelservice($VAR)
	{
		if(!isset($VAR['id']) || SESS_LOGGED == false) return false;

		# get the account id & confirm cancelation allowed
		$db = &DB();
		$sql    = 'SELECT id,account_id,recur_cancel FROM ' . AGILE_DB_PREFIX . 'service WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$service =  $db->Execute($sql);
		if($service->fields['account_id'] == SESS_ACCOUNT &&  $service->fields['recur_cancel'] == 1)
		{
			$VAR['user'] = 1;
			$this->cancelservice($VAR, $this);

			# Create a memo
			$fields=Array('date_orig'=>time(), 'staff_id'=> SESS_ACCOUNT, 'service_id'=>$VAR['id'], 'type'=> 'cancel', 'memo'=> "User Canceled Service");
			$db->Execute($sql=sqlInsert($db,"service_memo",$fields));
		}
		else
		{
			global $C_translate, $C_debug;
			$msg = $C_translate->translate('cancelservice_auth','service', '');
			$C_debug->alert($msg);
		}
	}


	##############################
	##    CANCEL SERVICES       ##
	##############################
	function cancelservice($VAR)
	{
		if(!isset($VAR['id'])) return false;

		# Update service status
		$db = &DB();
		$q = "UPDATE ".AGILE_DB_PREFIX."service SET
                    suspend_billing = ".$db->qstr( '1' )." WHERE
                    id          = ".$db->qstr( $VAR['id']  )." AND
                    site_id     = ".$db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		# get the account id
		$sql    = 'SELECT id,account_id FROM ' . AGILE_DB_PREFIX . 'service WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$service =  $db->Execute($sql);

		# send user email
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$email = new email_template;
		$email->send('service_cancel_user', $service->fields['account_id'], $service->fields['id'], '', '');

		# send admin email only if user canceled
		if(isset($VAR['user']))
		{
			$email = new email_template;
			$email->send('admin->service_cancel_admin', $service->fields['account_id'], $service->fields['id'], '', '');
		}

		# Create a memo
		$fields=Array('date_orig'=>time(), 'staff_id'=> SESS_ACCOUNT, 'service_id'=>$VAR['id'], 'type'=> 'cancel', 'memo'=> "Staff Canceled Service");
		$db->Execute($sql=sqlInsert($db,"service_memo",$fields));
	}


	##############################
	##	ADD/APPROVE SERVICES    ##
	##############################
	function approveService($id)
	{
		# Update service status
		$db = &DB();
		$q = "UPDATE ".AGILE_DB_PREFIX."service SET
			        active		= ".$db->qstr( 1 ).",
			        queue		= ".$db->qstr( 'active' )." WHERE
			        id          = ".$db->qstr(  $id  )." AND
			        site_id     = ".$db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		# Create a memo
		$fields=Array('date_orig'=>time(), 'staff_id'=> SESS_ACCOUNT, 'service_id'=>$id, 'type'=> 'approve', 'memo'=> "Approved Service");
		$db->Execute($sql=sqlInsert($db,"service_memo",$fields));

		# Run queue now 
		$this->queue_one($id, false);
		return true;
	}


	##############################
	##	VOID SERVICE		    ##
	##############################
	function voidService($id)
	{
		# Update service status
		$db = &DB();
		$q = "UPDATE ".AGILE_DB_PREFIX."service SET
			        active		= ".$db->qstr( 0 ).",
			        queue		= ".$db->qstr( 'inactive' )." WHERE
			        id          = ".$db->qstr(  $id  )." AND
			        site_id     = ".$db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		# Create a memo
		$fields=Array('date_orig'=>time(), 'staff_id'=> SESS_ACCOUNT, 'service_id'=>$id, 'type'=> 'void', 'memo'=> "Voided Service");
		$db->Execute($sql=sqlInsert($db,"service_memo",$fields));

		/** call queue now */
		$this->queue_one($id);
		return true;
	}
	
	/** queue all services */
	function queue($VAR) {		
		if(!empty($VAR['id']) && !empty($VAR["do"])) {
			/** queue one */
			$this->queue_one($VAR['id'], false);
		} else {
			/** queue all services */
			$db = &DB();
			$rs = $db->Execute(sqlSelect($db, "service", "*", "queue!='none'"));			 
			if ($rs && $rs->RecordCount()) { 
				while ( !$rs->EOF ) {
					$this->queue_one($rs->fields['id'], $rs->fields);
					$rs->MoveNext();
				}
			}
		}
	}
		
	/** queue one service 
	 * @param int $id
	 * @param array $service Fields of service row 
	 */
	function queue_one($id, $service=false) {
		if(!$service) {
			$db=&DB();
			$rs = $db->Execute(sqlSelect($db, "service", "*", "id=::$id::"));
			if(!$rs || !$rs->RecordCount()) return false;
			$service=$rs->fields;
			$this->service = $rs->fields;
		} else { 
			$this->service = $service;
		}  
		switch($service['type']) 
		{		
			case 'group': 
				$this->queue_group($id);
				break;
			case 'host': 
				$this->queue_host($id);
				break;
			case 'domain': 
				$this->queue_domain($id);
				break;
			case 'product': 
				$this->queue_product($id);
				break;
			case 'host_group': 
				$this->queue_host($id);
				$this->queue_group($id, false);
				break;				
			case 'product_group': 
				$this->queue_product($id);
				$this->queue_group($id, false);
				break;	
		}
	}

	/** set queue action to 'none' 
	 * @param int $id Service ID 
	 */
	function queue_complete($id=false) {
		if(!$id) return false;
		$db =& DB();
		$db->Execute("UPDATE ".AGILE_DB_PREFIX."service SET queue = ".$db->qstr( 'none' )." WHERE id=".$db->qstr( $id )." AND site_id=".$db->qstr(DEFAULT_SITE));
	}

	/** group type service queue 
	 * @param int $id Service ID
	 * @param bool $update Update service queue to 'none' after running
	 */
	function queue_group($id, $update=true)
	{
		# Select Service Details
		$db = &DB();
		$q = "SELECT * FROM  ".AGILE_DB_PREFIX."service WHERE id = ".$db->qstr( $id )." AND site_id = ".$db->qstr(DEFAULT_SITE);;
		$rs = $db->Execute($q);
		if ($rs && $rs->RecordCount()) { 
			
			# Get the groups to grant access to:
			$groups = unserialize($rs->fields['group_grant']);
			if(!is_array($groups)) return false;

			# Get the action to perform:
			include_once(PATH_CORE.'service_group.inc.php');
			$srv = new service_group( $rs->fields, $groups );
			switch ($rs->fields['queue'])
			{
				case 'new':
					$srv->s_new();
					break;
				case 'active':
					$srv->s_active();
					break;
				case 'inactive':
					$srv->s_inactive();
					break;
				case 'edit':
					$srv->s_edit();
					break;
				case 'delete':
					$srv->s_delete();
					break;
				case 'none':
					if($rs->fields['active'])
					$srv->s_active();
				else
					$srv->s_inactive();
					break;
			}

			# Update service queue status
			if($update) $this->queue_complete($id);
		}
	}

	##############################
	##	DOMAIN QUEUE HANDLER    ##
	##############################
	function queue_host($id)
	{
		global $VAR;

		# Get the service type (task based / real time)
		$host_id = $this->service['host_server_id'];
		$db     = &DB();
		$sql    = 'SELECT debug,provision_plugin FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
                       id           =  ' . $db->qstr( $host_id ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		if($rs->RecordCount() > 0)
		{
			$file = $rs->fields['provision_plugin'];
			require_once ( PATH_PLUGINS . 'provision/'.$file.'.php' );
			eval ( '$_plg = new plgn_prov_'.$file.';' );

			#If realtime, load module and run command now
			if(@$_plg->remote_based == true) $_plg->p_one($id);
		}

		return true;
	}

	##############################
	##	DOMAIN QUEUE HANDLER    ##
	##############################
	function queue_domain($id)
	{
		# Select Service Details
		$db = &DB();
		$q = "SELECT * FROM  ".AGILE_DB_PREFIX."service WHERE
			        id			= ".$db->qstr( $id )." AND
			        site_id     = ".$db->qstr(DEFAULT_SITE);;
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 0) {
			return false;
		} else {

			# Get the action to perform:
			include_once(PATH_CORE.'service_domain.inc.php');
			$srv = new service_domain( $rs->fields );

			if ($rs->fields['queue'] == 'new')
			{
				if ( $srv->s_new() )
				$this->queue_complete( $id );
				return;
			}

			# Update service queue status
			$this->queue_complete( $id );
			return;
		}
	}

	######################################
	##	PRODUCT PLUGIN QUEUE HANDLER    ##
	######################################
	function queue_product($id)
	{
		global $VAR;

		# Get the plugin name type (task based / real time) 
		$file = $this->service['prod_plugin_name'];
		if(!empty($file)) {
			$path = PATH_PLUGINS . 'product/'.$file.'.php';
			if(is_file($path)) 
			{
				require_once ($path);
				eval ( '$_plg = new plgn_prov_'.$file.';' );
				 
				# If realtime, load module and run command now 
				if(!empty($_plg) && is_object($_plg))
					if($_plg->remote_based == true) 
						$_plg->p_one($id); 
						 
							
			} else {
				return false;
			}
		} 
		return true;
	}

	##############################
	##	ADD/APPROVE SERVICES    ##
	##############################
	function invoiceItemToService($invoice_item_id, $invoice, $service_id=false)
	{
		include_once(PATH_MODULES.'product/product.inc.php');
		$product  = new product;
					
		$trial = false;

		$db= &DB();

		# Get the invoice_item record
		$item = & $db->Execute( sqlSelect($db, "invoice_item", "*", "id = $invoice_item_id"));

		# Get the product details
		$prod = & $db->Execute ( sqlSelect($db, "product", "*", "id = {$item->fields['product_id']}"));

		# Determine Price, Price Type, and Next Invoice Date:
		if ($item->fields['price_type'] == '2')
		{
			### Item is trial for another item:
			$trial = true;

			# Determine trial length.
			$tl = $prod->fields['price_trial_length_type'];
			if($tl == 0)
			$this->next_invoice = time() + ( $prod->fields['price_trial_length'] * 86400 );
			elseif ($tl == 1)
			$this->next_invoice = time() + ( $prod->fields['price_trial_length'] * 86400 * 7 );
			elseif ($tl == 2)
			$this->next_invoice = mktime(0,0,0,date('m')+$prod->fields['price_trial_length'],date('d'), date('Y'));
			else
			$this->next_invoice = time() + ( 365 * 86400 );

			# get the details of the permanent item
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."product WHERE
		        	  id 		=  ".$db->qstr($prod->fields['price_trial_prod'])." AND
		        	  site_id 	=  ".$db->qstr(DEFAULT_SITE);
			$prod = $db->Execute($q);
			
			/* set the product id to the perm item */
			$item->fields['product_id']= $prod->fields['id'];			
			$this->recurring_schedule = $item->fields['recurring_schedule'];
 
			### Get the price
			$price = $product->price_prod($prod->fields, $prod->fields['price_recurr_default'], $invoice->fields['account_id'], false);
			$this->price  = @$price['base'] / $item->fields['quantity'];

			$this->bind   = '1';
			$item->fields['sku'] = $prod->fields['sku'];
		}
		elseif ($item->fields['price_type'] == '1')
		{
			# Recurring Item
			$this->recurring_schedule = $item->fields['recurring_schedule'];
			$this->price  = $item->fields['price_base'] / $item->fields['quantity'];
			$this->bind   = '1';

			# Determine the next invoice date:
			$this->next_invoice = $this->calcNextInvoiceDate(	$invoice->fields['due_date'],
			$this->recurring_schedule,
			$prod->fields['price_recurr_type'],
			$prod->fields['price_recurr_weekday'],
			$prod->fields['price_recurr_week'] );
		}
		elseif ($item->fields['price_type'] == '0')
		{
			# One-time charge
			$this->recurring_schedule = '';
			$this->next_invoice		  = '';
			$this->price  			  = $item->fields['price_base'] / $item->fields['quantity'];
			$this->bind   			  = '0';
		}
		else
		{
			return false;
		}

		# If set-date type recurring transaction, determine full price:
		if (!$trial && $prod->fields['price_type'] == '1' && $prod->fields['price_recurr_type'] == '1')
		{
			# Get the base product price:
			$price = $product->price_prod($prod->fields, $this->recurring_schedule, $invoice->fields['account_id'], false);
			$this->price  = $price['base'] / $item->fields['quantity'];

			# Get the price of any attributes:
			$price = $product->price_attr($prod->fields, $item->fields['product_attr_cart'], $this->recurring_schedule, $invoice->fields['account_id'], false);
			$this->price += $price['base'] / $item->fields['quantity'];
		}

		# Service settings:
		$this->active 					= '1';
		$this->queue  					= 'new';
		$this->host_ip 		 			= '';
		$this->host_username 			= '';
		$this->host_password 			= '';
		$this->domain_host_tld_id 		= '';
		$this->domain_host_registrar_id = '';
		$this->domain_date_expire 		= '';

		# Parent ID
		$this->parent_id = $service_id;
		
		# determine if groups defined:
		$groups_defined=false;
		if(!empty($prod->fields['assoc_grant_group'])) {
			// type > 0 or num of days defined?
			if($prod->fields['assoc_grant_group_type'] > 0 || $prod->fields['assoc_grant_group_days'] > 0) {
				// actual groups defined?
				$grant_groups=unserialize($prod->fields['assoc_grant_group']);
				if(is_array($grant_groups) && count($grant_groups)>0) {
					foreach($grant_groups as $key=>$group_id) {
						if($group_id>0) {
							$groups_defined=true;
							break;
						}
					}
				}
			}
			if(!$groups_defined) {
				$prod->fields['assoc_grant_group']=false;
				$prod->fields['assoc_grant_group_type']=false;
				$prod->fields['assoc_grant_group_days']=false;
			}
		}

		# Determine the Service Type:
		$this->type = 'none';
		if($item->fields['item_type'] == '0')
		{
			# NONE, GROUP, PRODUCT, OR PRODUCT_GROUP:
			if (!$groups_defined && empty($prod->fields['prod_plugin']))
			{
				$this->type   		 = 'none';
			}
			else
			{
				if( $groups_defined && !empty($prod->fields['prod_plugin']))
				{
					$this->type = 'product_group';
				}
				elseif(!empty($prod->fields['prod_plugin']))
				{
					$this->type = 'product';
				}
				elseif($groups_defined)
				{
					$this->type = 'group';
				}
			}
		}
		elseif($item->fields['item_type'] == '1')
		{
			# HOSTING:
			$this->type   		 = 'host';
			$this->host_ip 		 = '';
			$this->host_username = '';
			$this->host_password = '';

			# Is group access also defined?
			if(!empty($prod->fields['assoc_grant_group']))
			$this->type = 'host_group';
		}
		elseif($item->fields['item_type'] == '2')
		{
			# DOMAIN:
			$this->type   		 = 'domain';
			$this->domain_date_expire 		= time() + ($item->fields['domain_term'] * (86400*365));

			# Get the host_tld_id
			$q = "SELECT id, registrar_plugin_id FROM ".AGILE_DB_PREFIX."host_tld WHERE
		        	  name 		=  ".$db->qstr($item->fields['domain_tld'])." AND
		        	  site_id 	=  ".$db->qstr(DEFAULT_SITE);
			$tld = $db->Execute($q);
			$this->domain_host_tld_id 		= $tld->fields['id'];
			$this->domain_host_registrar_id = $tld->fields['registrar_plugin_id'];
		}

		if($this->type == "none" && $this->recurring_schedule == "") {
			# do not create service for one-time charge with no hosting,domain, or group settings
		} else {
			# Create the service record(s):

			for($iii=0; $iii<$item->fields['quantity']; $iii++)
			{
				$this->id = sqlGenID($db,"service");
				$fields = Array('date_orig' 				=> time(),
				'date_orig'					=> time(),
				'parent_id' 				=> $this->parent_id,
				'invoice_id'				=> $item->fields['invoice_id'],
				'invoice_item_id' 			=> $invoice_item_id,
				'account_id'				=> $invoice->fields['account_id'],
				'account_billing_id'		=> $invoice->fields['account_billing_id'],
				'product_id'				=> $item->fields['product_id'],
				'sku' 						=> $item->fields['sku'],
				'active'					=> $this->active,
				'bind' 						=> $this->bind,
				'type'						=> $this->type,
				'queue' 					=> $this->queue,
				'price'						=> $this->price,
				'price_type' 				=> $item->fields['price_type'],
				'taxable'					=> $prod->fields['taxable'],
				'date_last_invoice' 		=> $invoice->fields['date_orig'],
				'date_next_invoice'			=> $this->next_invoice,
				'recur_schedule' 			=> $this->recurring_schedule,
				'recur_type'				=> $prod->fields['price_recurr_type'],
				'recur_weekday' 			=> $prod->fields['price_recurr_weekday'],
				'recur_week'				=> $prod->fields['price_recurr_week'],
				'recur_schedule_change' 	=> $prod->fields['price_recurr_schedule'],
				'recur_cancel'				=> $prod->fields['price_recurr_cancel'],
				'recur_modify' 				=> $prod->fields['price_recurr_modify'],
				'group_grant'				=> $prod->fields['assoc_grant_group'],
				'group_type' 				=> $prod->fields['assoc_grant_group_type'],
				'group_days'				=> $prod->fields['assoc_grant_group_days'],
				'host_server_id' 			=> $prod->fields['host_server_id'],
				'host_provision_plugin_data'=> $prod->fields['host_provision_plugin_data'],
				'host_ip' 					=> $this->host_ip,
				'host_username'				=> $this->host_username,
				'host_password' 			=> $this->host_password,
				'domain_name'				=> $item->fields['domain_name'],
				'domain_tld' 				=> $item->fields['domain_tld'],
				'domain_term'				=> $item->fields['domain_term'],
				'domain_type' 				=> $item->fields['domain_type'],
				'domain_date_expire'		=> $this->domain_date_expire,
				'domain_host_tld_id'		=> $this->domain_host_tld_id,
				'domain_host_registrar_id'	=> $this->domain_host_registrar_id,
				'prod_attr'					=> $item->fields['product_attr'],
				'prod_attr_cart'			=> $item->fields['product_attr_cart'],
				'prod_plugin_name' 			=> @$prod->fields["prod_plugin_file"],
				'prod_plugin_data'			=> @$prod->fields["prod_plugin_data"]);
				$rs = & $db->Execute( sqlInsert($db, "service", $fields, $this->id));
				if ($rs === false) {
					global $C_debug;
					$C_debug->error('service.inc.php','invoiceItemToService', $q . " | " . @$db->ErrorMsg());
				} else {
					# Run the queue on this item:
					$arr['id'] = $this->id;
					$this->queue($arr, $this);
				}
			}
		}

		# Create any discount codes:
		if($prod->fields['discount'] == '1' && !empty($prod->fields['discount_amount']))
		{
			$id = $db->GenID(AGILE_DB_PREFIX . 'discount_id');
			$q = "INSERT INTO ".AGILE_DB_PREFIX."discount SET
	        	id					= ". $db->qstr( $id ) .",
	        	site_id				= ". $db->qstr( DEFAULT_SITE ) .",
	        	date_orig			= ". $db->qstr( time() ) .",
	        	date_start			= ". $db->qstr( time() ) .",
	        	status				= ". $db->qstr( '1' ) .",
	        	name				= ". $db->qstr( 'DISCOUNT-'.$id ) .",
	        	notes				= ". $db->qstr( 'Autogenerated for Invoice Number '.$item->fields['invoice_id'].', SKU '.$item->fields['sku'] ) .",
	        	max_usage_account 	= ". $db->qstr( '1' ) .",
	        	max_usage_global	= ". $db->qstr( '1' ) .",
	        	avail_account_id	= ". $db->qstr( $invoice->fields['account_id'] ) .",
	        	new_status			= ". $db->qstr( '1' ) .",
	        	new_type			= ". $db->qstr( '1' ) .",
	        	new_rate			= ". $db->qstr( $prod->fields['discount_amount'] ) .",
	        	recurr_status		= ". $db->qstr( '1' ) .",
	        	recurr_type			= ". $db->qstr( '1' ) .",
	        	recurr_rate			= ". $db->qstr( $prod->fields['discount_amount'] );
			$db->Execute($q);
		}

		return true;
	}


	##########################
	###   Renew Domain		##
	##########################
	function renewDomain( $item, $billing_id )
	{
		$db = &DB();
		$dbm = new CORE_database();

		# Get the current service details:
		$service = $db->Execute( $dbm->sql_select('service', '*', "id = {$item->fields['service_id']}",'', $db ) );

		# Get new dates
		$term = $service->fields['domain_term'] + $item->fields['domain_term'];
		$expire = $service->fields['domain_date_expire'] + (86400*365*$item->fields['domain_term']);

		$rs = $db->Execute( $sql = sqlUpdate($db, 'service',
		Array(	'date_last_invoice'  => $service->fields['domain_date_expire'],
		'domain_date_expire' => $expire,
		'domain_term' 		 => $term,
		'domain_type' 		 => 'renew',
		'queue' 			=> 'new',
		'account_billing_id' => $billing_id),
		" id = {$item->fields['service_id']} " ) );
		if($rs) return true;
		return false;
	}


	/** get daily cost for a given recurring schedule
        *  @param $schedule Recurring schedule
        */
	function getDailyCost($schedule,$price) {
		$d=Array(7,30.43685,91.31055,182.6211,365.2422, 730.4844, 1095.7266);
		if($price <= 0) return 0;
		return $price/$d["$schedule"];
	}


	##############################
	##	ADD/APPROVE SERVICES    ##
	##############################
	function modifyService($item, $billing_id )
	{
		global $C_debug;

		# Get the product details
		$db= &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."product WHERE
	        	  id 		=  ".$db->qstr($item->fields['product_id'])." AND
	        	  site_id 	=  ".$db->qstr(DEFAULT_SITE);
		$prod = $db->Execute($q);

		# Get the current service details
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."service WHERE
	        	  id 		=  ".$db->qstr($item->fields['service_id'])." AND
	        	  site_id 	=  ".$db->qstr(DEFAULT_SITE);
		$servrs = $db->Execute($q);
		$service = $servrs->fields;
		$service_id = $service['id'];

		# Determine Price, Price Type, and Next Invoice Date:
		if ($item->fields['price_type'] == '1')
		{
			# Recurring Item
			$this->recurring_schedule = $item->fields['recurring_schedule'];
			$this->price  = $item->fields['price_base'];
			$this->bind   = '1';

			# Determine the next invoice date:
			$this->next_invoice = $this->calcNextInvoiceDate(	time(),
			$this->recurring_schedule,
			$prod->fields['price_recurr_type'],
			$prod->fields['price_recurr_weekday'],
			$prod->fields['price_recurr_week'] );

			# Determine the last invoice date:
			if(empty($service['date_last_invoice']))
			$this->last_invoice = time();
			else
			$this->last_invoice = $service['date_last_invoice'];

			$old_unit = $this->getDailyCost($service['recur_schedule'], $service['price']);
			$new_unit = $this->getDailyCost($item->fields['recurring_schedule'], $this->price);
			//echo "old_unit=$old_unit <br> new_unit=$new_unit <br>";

			$daysLeft = ceil(($service['date_next_invoice'] - time())/86400);
			$prorated = $old_unit * $daysLeft;
			$daysDiff = ceil($prorated / $new_unit);
			//echo "daysLeft=$daysLeft prorated=$prorated daysDiff=$daysDiff <br>";
			//echo "dt=". date("d-m-Y", $this->next_invoice)."<br>";
			$this->next_invoice += ($daysDiff * 86400);
			//echo "final dt=". date("d-m-Y", $this->next_invoice) ."<br>";
		}

		# If set-date type recurring transaction, determine full price:
		if ($prod->fields['price_type'] == '1' && $prod->fields['price_recurr_type'] == '1') {
			include_once(PATH_MODULES.'cart/cart.inc.php');
			$cart  = new cart;
			$price = $cart->price_prod($prod->fields, $this->recurring_schedule, $invoice->fields['account_id'], false);
			$this->price  = $price['base'];
			$price = $cart->price_attr($prod->fields, $item->fields['product_attr_cart'], $this->recurring_schedule, $invoice->fields['account_id'], false);
			$this->price  += $price['base'];
		}

		# Determine the Service Type:
		if(!empty($prod->fields['assoc_grant_group'])) {
			if(!empty($prod->fields['prod_plugin']))
			$this->type = 'product_group';
			elseif(!empty($prod->fields['host']))
			$this->type = 'host_group';
			else
			$this->type = 'group';
		} elseif(!empty($prod->fields['prod_plugin'])) {
			$this->type = 'product';
		} elseif(!empty($prod->fields['host'])) {
			$this->type = 'host';
		} else {
			$this->type = 'none';
		}

		# Reconfigure host data:
		$host_arr = "";
		if($this->type == "host" || $this->type == "host_group") {
			$old = serialize($service['host_provision_plugin_data']);
			$host_arr = $prod->fields['host_provision_plugin_data'];
			if(is_array($old) && count($old) > 0)
			foreach($old as $key => $val)
			if(!isset($host_arr["$key"]) && @$old["$key"] != "")  $host_arr["$key"] = $val;
		}

		# Create the item record:
		$q = "UPDATE ".AGILE_DB_PREFIX."service SET
		        date_last				= ". $db->qstr( time() ) .", 
		        invoice_id				= ". $db->qstr( $item->fields['invoice_id'] ) .",
		        invoice_item_id			= ". $db->qstr( $item->fields['id'] ) .", 
		        account_billing_id 		= ". $db->qstr( $billing_id ) .",
		        product_id				= ". $db->qstr( $prod->fields['id'] ) .",
		        sku						= ". $db->qstr( $item->fields['sku'] ) .",
		        active					= ". $db->qstr( 1 ) .", 
		        type					= ". $db->qstr( $this->type ) .",
		        queue					= ". $db->qstr( 'edit' ) .", 
		        price					= ". $db->qstr( @$this->price ) .",
		        price_type				= ". $db->qstr( $prod->fields['price_type'] ) .",
		        taxable					= ". $db->qstr( $prod->fields['taxable'] ) .", 
		        date_last_invoice		= ". $db->qstr( @$this->last_invoice ) .",
		        date_next_invoice		= ". $db->qstr( @$this->next_invoice ) .",
		        recur_schedule			= ". $db->qstr( @$this->recurring_schedule ) .",
		        recur_type				= ". $db->qstr( $prod->fields['price_recurr_type'] ) .",
		        recur_weekday			= ". $db->qstr( $prod->fields['price_recurr_weekday'] ) .",
		        recur_week				= ". $db->qstr( $prod->fields['price_recurr_week'] ) .",
		        recur_schedule_change 	= ". $db->qstr( $prod->fields['price_recurr_schedule'] ) .",
		        recur_cancel			= ". $db->qstr( $prod->fields['price_recurr_cancel'] ) .", 
		        recur_modify			= ". $db->qstr( $prod->fields['price_recurr_modify'] ) .", 
		        group_grant				= ". $db->qstr( $prod->fields['assoc_grant_group'] ) .",
		        group_type				= ". $db->qstr( $prod->fields['assoc_grant_group_type'] ) .",
		        group_days				= ". $db->qstr( $prod->fields['assoc_grant_group_days'] ) .",  
		        host_provision_plugin_data=".$db->qstr( @$host_arr ) .", 
		        prod_plugin_name		= ". $db->qstr( @$prod->fields["prod_plugin_file"] ) .",
		        prod_plugin_data		= ". $db->qstr( @$prod->fields["prod_plugin_data"] ) . " 
		        WHERE 
		        site_id = ".DEFAULT_SITE . " AND id = $service_id";
		$rs = $db->Execute($q);
		if ($rs === false) {
			global $C_debug;
			$C_debug->error('service.inc.php','invoiceItemToService', $q . " | " . @$db->ErrorMsg());
		}

		# Run the queue on this item: 
		$this->queue_one($service_id, false);
		return true;
	}


	/**
	 * Calculate next invoice date
	 *
	 * @param int $s last billed date
	 * @param int $schedule schedule: 0=weekly, 1=monthly, 2=quarterly, 3=semi-annually, 4=yearly, 5=2year
	 * @param bool $type type: 	0=anniversary, 1=fixed date
	 * @param int $weekday (for fixed date) 1-28  day of month
	 * @param int $week
	 * @return int date
	 */
	function calcNextInvoiceDate($s, $schedule, $type, $weekday, $week=false)
	{
  		# Anniversary billing routine:
		if($type == 0)
		{
			if($schedule == 0)
			return mktime (0, 0, 0, date("m", $s), 	date("d", $s)+7, 	date("Y", $s));
			if($schedule == 1)
			return mktime (0, 0, 0, date("m", $s)+1, date("d", $s), 	date("Y", $s));
			if($schedule == 2)
			return mktime (0, 0, 0, date("m", $s)+3, date("d", $s), 	date("Y", $s));
			if($schedule == 3)
			return mktime (0, 0, 0, date("m", $s)+6, date("d", $s), 	date("Y", $s));
			if($schedule == 4)
			return mktime (0, 0, 0, date("m", $s), 	date("d", $s), 		date("Y", $s)+1);
			if($schedule == 5)
			return mktime (0, 0, 0, date("m", $s), 	date("d", $s), 		date("Y", $s)+2);
			if($schedule == 6)
			return mktime (0, 0, 0, date("m", $s), 	date("d", $s), 		date("Y", $s)+3);
			return false;
		}

		# Set-day/week billing routine:
		if ($type == 1)
		{
			if($schedule == 0) {
				return mktime (0, 0, 0, date("m", $s), 	date("d", $s)+7, 	date("Y", $s));
			} elseif ($type == 1) {
				$inc_months = 1;
			}  elseif ($type == 2) {
				$inc_months = 3;
			} elseif ($type == 3) {
				$inc_months = 6;
			} elseif ($type == 4) {
				$inc_months = 12;
			} elseif ($type == 5) {
				$inc_months = 24;
			} elseif ($type == 6) {
				$inc_months = 36;
			} else {
				return false;
			}

			# calculate the set day of month to bill:
			return mktime(0,0,0,date('m', $s)+$inc_months, $weekday, date('y', $s));
		}
		return 0;
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->construct();
		global $C_debug, $C_translate;
		$validate = true;

		## Set type:
		if(!empty($VAR['service_none'])) {
			$VAR['service_type'] = 'none';
		} elseif(!empty($VAR['service_domain'])) {
			$VAR['service_type'] = 'domain';
		} elseif (!empty($VAR['service_group'])) {
			if(!empty($VAR['service_hosting']))
			$VAR['service_type'] = 'host_group';
			elseif(!empty($VAR['service_product']))
			$VAR['service_type'] = 'product_group';
			else
			$VAR['service_type'] = 'group';
		} elseif (!empty($VAR['service_hosting'])) {
			$VAR['service_type'] = 'host';
		} elseif (!empty($VAR['service_product'])) {
			$VAR['service_type'] = 'product';
		}

		## Set Price Type
		if(!empty($VAR['billing_type']))
		$VAR['service_price_type'] = "1";
		else
		$VAR['service_price_type'] = "0";

		### loop through the field list to validate the required fields
		$type = 'add';
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$arr = $this->method["$type"];
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;
		$this->validated = true;

		while (list ($key, $value) = each ($arr)) {
			# get the field value
			$field_var  	= $this->module . '_' . $value;
			$field_name 	= $value;

			# check if this value is unique
			if(isset($this->field["$value"]["unique"]) && isset($VAR["$field_var"])) {
				if(!$validate->validate_unique($this->table, $field_name, "record_id", $VAR["$field_var"])) {
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->table . '_' . $field_name,
					'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),							# translate
					'error' 		=> $C_translate->translate('validate_unique',"", ""));
				}
			}
			if(isset($this->field["$value"]["validate"])) {
				if(isset($VAR["$field_var"])) {
					if($VAR["$field_var"] != '') {
						if(!$validate->validate($field_name, $this->field["$value"], $VAR["$field_var"], $this->field["$value"]["validate"])) {
							$this->validated = false;
							$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
							'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
							'error' 		=> $validate->error["$field_name"] );
						}
					} else {
						$this->validated = false;
						$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
						'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
						'error' 		=> $C_translate->translate('validate_any',"", ""));
					}
				} else {
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
					'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
					'error' 		=> $C_translate->translate('validate_any',"", ""));
				}
			}
		}

		# If recurring, validate & set defaults
		if($VAR['service_price_type'] == 1)
		{
			if(!empty($VAR['date_last_invoice']))
			$last_invoice = $validate->DateToEpoch(DEFAULT_DATE_FORMAT,$VAR['date_last_invoice']);
			else
			$last_invoice = time();

			# Determine the next invoice date:
			$next_invoice = $this->calcNextInvoiceDate( $last_invoice,
			@$VAR['product_price_recurr_default'],
			@$VAR['product_price_recurr_type'],
			@$VAR['product_price_recurr_weekday'],
			@$VAR['product_price_recurr_week'] );

		}

		$active = 1;
		$queue  = 'new';

		# Product details
		if(!empty($VAR['service_sku'])) {
			$product_id 	= @$VAR['product_id'];
			$product_sku 	= @$VAR['service_sku'];
		}


		# Hosting Details:
		if(@$VAR['service_type'] == 'host' || @$VAR['service_type'] == 'host_group')
		{
			# validate domain/tld set
			if(empty($VAR['host_domain_name']) || empty($VAR['host_domain_tld'])) {
				$this->validated = false;
				$this->val_error[] =  array('field' 		=> 'service_domain_name',
				'field_trans' 	=> $C_translate->translate('field_domain_name', 'service', ""),
				'error' 		=> $C_translate->translate('validate_any',"", ""));
			} else {
				$domain_name = $VAR['host_domain_name'];
				$domain_tld = $VAR['host_domain_tld'];
			}
		} else if ( @$VAR['service_type'] == 'domain' ) {
			# validate domain/tld set
			if(empty($VAR['domain_name']) || empty($VAR['domain_tld']) || empty($VAR['domain_type'])) {
				$this->validated = false;
				$this->val_error[] =  array('field' 		=> 'service_domain_name',
				'field_trans' 	=> $C_translate->translate('field_domain_name', 'service', ""),
				'error' 		=> $C_translate->translate('validate_any',"", ""));
			}
			else
			{
				$domain_name = $VAR['domain_name'];
				$domain_tld  = $VAR['domain_tld'];
				$domain_type = $VAR['domain_type'];

				# Get the host_tld_id
				$db = &DB();
				$q = "SELECT id,default_term_new,registrar_plugin_id FROM ".AGILE_DB_PREFIX."host_tld WHERE
			        	  name 		=  ".$db->qstr($domain_tld)." AND site_id 	=  ".$db->qstr(DEFAULT_SITE);
				$tld = $db->Execute($q);
				$domain_host_tld_id 		= $tld->fields['id'];
				$domain_host_registrar_id 	= $tld->fields['registrar_plugin_id'];
				$domain_term				= $tld->fields['default_term_new'];
				$domain_date_expire 		= time() + ($domain_term * (86400*365));
			}
		}


		if(!$this->validated)
		{
			# errors...
			global $smarty;
			$smarty->assign('form_validation', $this->val_error);
			global $C_vars;
			$C_vars->strip_slashes_all();
			return;
		} else {

			# Generate the SQL:
			$db = &DB();
			$id = $db->GenID(AGILE_DB_PREFIX.'service_id');
			$q = "INSERT INTO ".AGILE_DB_PREFIX."service SET
		        id						= ". $db->qstr( $id ) .",
		        site_id					= ". $db->qstr( DEFAULT_SITE ) .",
		        date_orig				= ". $db->qstr( time() ) .",
		        date_last				= ". $db->qstr( time() ) .",  
		        account_id				= ". $db->qstr( $VAR['service_account_id'] ) .",
		        account_billing_id 		= ". $db->qstr( @$VAR['ccnum'] ) .",
		        product_id				= ". $db->qstr( @$product_id ) .",
		        sku						= ". $db->qstr( @$product_sku ) .",
		        active					= ". $db->qstr( '1' ) .", 
		        type					= ". $db->qstr( $VAR['service_type'] ) .",
		        queue					= ". $db->qstr( 'new' ) .", 
		        price					= ". $db->qstr( @$VAR['product_price_base'] ) .",
		        price_type				= ". $db->qstr( @$VAR['service_price_type'] ) .",
		        taxable					= ". $db->qstr( @$VAR['product_taxable'] ) .", 
		        date_last_invoice		= ". $db->qstr( @$last_invoice ) .",
		        date_next_invoice		= ". $db->qstr( @$next_invoice ) .",
		        recur_schedule			= ". $db->qstr( @$VAR['product_price_recurr_default'] ) .",
		        recur_type				= ". $db->qstr( @$VAR['product_price_recurr_type'] ) .",
		        recur_weekday			= ". $db->qstr( @$VAR['product_price_recurr_weekday'] ) .", 
		        recur_schedule_change 	= ". $db->qstr( @$VAR['product_price_recurr_schedule'] ) .",
		        recur_cancel			= ". $db->qstr( @$VAR['product_price_recurr_cancel'] ) .", 
		        recur_modify			= ". $db->qstr( @$VAR['product_price_recurr_modify'] ) .", 
		        group_grant				= ". $db->qstr( serialize(@$VAR['product_assoc_grant_group']) ) .",
		        group_type				= ". $db->qstr( @$VAR['product_assoc_grant_group_type'] ) .",
		        group_days				= ". $db->qstr( @$VAR['product_assoc_grant_group_days'] ) .", 
		        host_server_id			= ". $db->qstr( @$VAR['product_host_server_id'] ) .",
		        host_provision_plugin_data=".$db->qstr( serialize(@$VAR['product_host_provision_plugin_data']) ) .",
		        host_ip					= ". $db->qstr( @$VAR['host_ip'] ) .",
		        host_username			= ". $db->qstr( @$VAR['host_username'] ) .",
		        host_password			= ". $db->qstr( @$VAR['host_password'] ) .", 
		        domain_name				= ". $db->qstr( @$domain_name ) .",
		        domain_tld				= ". $db->qstr( @$domain_tld ) .",
		        domain_term				= ". $db->qstr( @$domain_term ) .",
		        domain_type				= ". $db->qstr( @$domain_type ) .",
		        domain_date_expire		= ". $db->qstr( @$domain_date_expire ) .",
		        domain_host_tld_id		= ". $db->qstr( @$domain_host_tld_id ) .",
		        domain_host_registrar_id= ". $db->qstr( @$domain_host_registrar_id ) . ",
		        prod_plugin_name		= ". $db->qstr( @$VAR["product_prod_plugin_file"] ) .",
		        prod_plugin_data		= ". $db->qstr( serialize(@$VAR["product_prod_plugin_data"]) );
			$rs = $db->Execute($q);

			if($VAR['service_type'] == 'group' || $VAR['service_type'] = 'product' || $VAR['service_type'] = 'product_group') $this->queue_one($id, false);	

			global $VAR;
			$VAR["id"] = $id;
			define('FORCE_PAGE', 'service:view');
			return;

		}
	}

	##############################
	## 	Data for add template	##
	##############################
	function add_tpl($VAR)
	{
		global $smarty, $C_validate;
		$db 	= &DB();
		$dbm	= new CORE_database;

		if(!empty($VAR['product_id']) && $VAR['clearall'] == 0)
		{
			if(!empty($VAR['changeproduct'])) {
				# Get selected product ID and use it as a template
				$sql = $dbm->sql_select('product', '*', "id = {$VAR['product_id']}", "",$db);
				$rs = $db->Execute($sql);

				# get assoc groups
				if( !empty($rs->fields['assoc_grant_group']) )  {
					$groups = unserialize($rs->fields['assoc_grant_group']);
					if(!empty($groups[0])) $rs->fields['group'] = $groups;
				}
				$fields = $rs->fields;
			}
		}

		# get changes submitted, if product not changed:
		if(empty($VAR['clearall']) && empty($VAR['changeproduct'])) {
			foreach($VAR as $key => $val) {
				if(!empty($val)) {
					$key = preg_replace('/^product_/','', $key);
					if(is_array($val))
					$fields["$key"] = serialize($val);
					else
					$fields["$key"] = $val;
				}
			}
		}
		$smarty->assign('product', @$fields);

		# Get all available products
		$sql = $dbm->sql_select('product',
		'id,sku',
		"prod_plugin = 1 OR
									price_type = 1 OR 
									( assoc_grant_group_type = 0 OR assoc_grant_group_type >= 1 ) OR 
									host = 1",
									"sku",
									$db);
									$rs = $db->Execute($sql);
									while(!$rs->EOF) {
										$prod[]=$rs->fields;
										$rs->MoveNext();
									}
									if(!empty($prod))
									$smarty->assign('prod_menu',$prod);
	}


	##############################
	##	USER MODIFY SERVICE	    ##
	##############################
	function user_modify($VAR)
	{
		global $smarty, $C_debug, $C_translate;

		# Validate user is logged in
		if(empty($VAR['service_id']) || SESS_LOGGED == false) return;

		# Validate user is auth for current service id:
		$service_id = $VAR['service_id'];
		$db = &DB();
		$dbm = new CORE_database;
		$rs = $db->Execute( $sql = $dbm->sql_select('service', '*', "account_id = ".SESS_ACCOUNT." AND id = $service_id AND recur_modify = 1", "", $db ) );
		if($rs === false || $rs->RecordCount() == 0) return false;

		$this->modify($VAR, $this);
	}


	##############################
	##	USER MODIFY SERVICE	    ##
	##############################
	function modify($VAR)
	{
		global $smarty, $C_debug, $C_translate;

		# Get service details:
		$service_id = $VAR['service_id'];
		$db = &DB();
		$dbm = new CORE_database;
		$rs = $db->Execute( $sql = $dbm->sql_select('service', '*', "id = $service_id", "", $db ) );
		if($rs === false || $rs->RecordCount() == 0) return false;

		# if product id not set, generate array
		if(empty($VAR['id']))
		{
			$product_id = $rs->fields['product_id'];
			if(empty($product_id)) return false;
			$prod = $db->Execute( $dbm->sql_select( 'product', 'modify_waive_setup,modify_product_arr', "id = $product_id", "", $db ) );
			if($prod === false || $prod->RecordCount() == 0) return false;
			$arr = unserialize( $prod->fields['modify_product_arr'] );
			if(!is_array($arr) || count($arr) == 0 || empty($arr[0])) return false;

			foreach($arr as $pid) {
				$prod = $db->Execute( $dbm->sql_select( 'product', 'id,sku,price_base', "id = $pid", "", $db ) );
				if($prod === false || $prod->RecordCount() == 0) {} else {
					$smart[] = $prod->fields;
				}
			}
			$smarty->assign('product_arr', $smart);
		}
		elseif(empty($VAR['confirm_modify']))
		{
			# validate selected product is authorized
			$do = true;
			$product_id = $rs->fields['product_id'];
			$sql = $dbm->sql_select( 'product', 'modify_waive_setup,modify_product_arr', "id = $product_id", "", $db ) ;
			$prod = $db->Execute( $sql );
			if($prod === false || $prod->RecordCount() == 0) $do = false;
			$arr = unserialize( $prod->fields['modify_product_arr'] );
			if(!is_array($arr) || count($arr) == 0 || empty($arr[0])) $do = false;
			if($do) { $do = false;
			foreach($arr as $pid)
			if( $pid == $VAR['id'] ) { $do = true; break; }
			}
			$smarty->assign('product_show', $do);

			# determine if setup fees are ignored
			$smarty->assign('waive_setup',  $prod->fields['modify_waive_setup']);
		}
	}


	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{
		global $smarty,$C_auth;
		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$smart = $db->view($VAR, $this, $type);

		$dbm = new CORE_database;
		$db  = &DB();

		# Add the change recur schedule options to the array:
		for($i=0; $i<count($smart); $i++)
		{
			# get recent invoice details for this service
			$p = AGILE_DB_PREFIX;
			$sql = "SELECT A.id, A.date_orig, A.total_amt, A.billed_amt, A.process_status 
    						FROM {$p}invoice A
    						WHERE A.site_id = ".DEFAULT_SITE."
    						AND
    						( 
    						 A.id={$smart[$i]['invoice_id']}
    						OR
    						 A.id in (select distinct invoice_id from {$p}invoice_item where 
    						 service_id={$smart[$i]['id']} )
    						)            		
                    		ORDER BY A.id DESC ";
			
			# Joe rewrote the query, its dog slow
			$sql = "SELECT A.id, A.date_orig, A.total_amt, A.billed_amt, A.process_status 
				FROM {$p}invoice_item B inner join {$p}invoice A on
				(B.invoice_id=A.id and service_id={$smart[$i]['id']}) 
				WHERE A.site_id = ".DEFAULT_SITE." AND B.site_id = ".DEFAULT_SITE." 
				ORDER BY A.id DESC";
			
			$inv = $db->SelectLimit($sql,5);
			if($inv != false && $inv->RecordCount() > 0)  {
				while(!$inv->EOF) {
					if($inv->fields['total_amt'] > $inv->fields['billed_amt'] && $inv->fields['suspend_billing'] != 1) {
						$inv->fields['due'] = $inv->fields['total_amt'] - $inv->fields['billed_amt'];
					}
					$smart[$i]["invoice"][] = $inv->fields;
					$inv->MoveNext();
				}
			}

			# allow modification of service plan?
			if(!empty($VAR['user']) && !empty($smart[$i]['product_id'])) {
			} elseif(empty($VAR['user']) ) {
			} else {
				$smart[$i]['recur_modify'] = "0";
			}

			# get recurring details?
			if(!empty($VAR['user']) && $smart[$i]['recur_schedule_change'] == 1 && !empty($smart[$i]['product_id']))
			$do = true;
			elseif(empty($VAR['user']) && !empty($smart[$i]['product_id']))
			$do = true;
			else
			$do = false;

			if($do && $smart[$i]['date_next_invoice'] > 0 && !empty($smart[$i]['product_id']))
			{
				# Get the product details:
				$sql 	= $dbm->sql_select('product','*',"id = {$smart[$i]['product_id']}", '', $db);
				$prod 	= $db->Execute($sql);
				$fields = $prod->fields;

				global $C_auth;
				$g_ar = unserialize($fields["price_group"]);
				if(is_array($g_arr)) {
					foreach($g_ar as $period => $price_arr) {
						foreach($price_arr as $group => $vals) {
							if(@$price_arr["show"] == "1") {
								if (is_numeric($group) && $C_auth->auth_group_by_account_id($smart[$i]['account_id'], $group)) {
									if($vals["price_base"] != "" && $vals["price_base"] > 0)
									if(empty($ret[$period]['base']) || $vals["price_base"] < $ret[$period]['base'])
									$ret[$period]['base'] = $vals["price_base"];
								}
							}
						}
					}
				}

				if(!is_array($ret))  {
					if(!empty($VAR['user'])) {
						$ret["{$smart[$i]["recur_schedule"]}"]["base"] = $smart[$i]["price"];
						$smarty->assign('recur_price', $ret);
					} else {
						$smarty->assign('recur_price', false);
					}
				} else {
					$smarty->assign('recur_price', $ret);
				}
			} else {
				$smarty->assign('recur_price', false);
			}
		}

		$smarty->clear_assign('service');
		$smarty->assign('service', $smart);
	}


	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$this->construct();
		# provisioning data;
		if(!empty($VAR['product_host_provision_plugin_data']))
		{
			$VAR['service_host_provision_plugin_data'] = $VAR['product_host_provision_plugin_data'];
			$s = serialize($VAR['service_host_provision_plugin_data']);
		}

		# product plugin data;
		if(!empty($VAR['product_prod_plugin_data']))
		{
			$VAR['service_prod_plugin_data'] = $VAR['product_prod_plugin_data'];
		}


		# check if any changes were made that calls for edit queue status
		$queue = true;

		# get the previous data
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
                       id           =  ' . $db->qstr( $VAR['service_id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);

		if(!empty($VAR['queue_force'])) {
			$queue = false;
		} elseif(!empty($VAR['service_host_provision_plugin_data']) ) {
			# compare username
			if($rs->fields['host_username'] != $VAR['service_host_username']) {
				$VAR['service_queue'] = 'edit';
				# compare password
			} elseif ($rs->fields['host_password'] != $VAR['service_host_password']) {
				$VAR['service_queue'] = 'edit';
				# compare ip
			} elseif (!empty($VAR['service_host_ip']) && $rs->fields['host_ip'] != $VAR['service_host_ip']) {
				$VAR['service_queue'] = 'edit';
				# compare plugin data
			} elseif ( $rs->fields['host_provision_plugin_data'] != $s ) {
				$VAR['service_queue'] = 'edit';
			} else {
				# suspend/unsuspend
				if($VAR['service_active'] == 0 && $VAR['service_active'] != $rs->fields['active'] ) {
					$VAR['service_queue'] = 'inactive';
				} elseif ($VAR['service_active'] == 1 && $VAR['service_active'] != $rs->fields['active'] ) {
					$VAR['service_queue'] = 'active';
				} else {
					$VAR['service_queue'] =   $rs->fields['queue'];
					$queue = false;
				}
			}
		} else {
			# suspend/unsuspend
			if($VAR['service_active'] == 0 && $VAR['service_active'] != $rs->fields['active'] ) {
				$VAR['service_queue'] = 'inactive';
			} elseif ($VAR['service_active'] == 1 && $VAR['service_active'] != $rs->fields['active'] ) {
				$VAR['service_queue'] = 'active';
			} else {
				$VAR['service_queue'] =   $rs->fields['queue'];
				$queue = false;
			}
		}

		# update record
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);

		# Run queue now
		if($queue)  {
			$this->queue_one($VAR['service_id'],false);
			return true;
		}
	}

	function delete($VAR)  {
		$this->construct();
		$dbx = new CORE_database;
		$db  = &DB();

		### Get the array
		if(isset($VAR["delete_id"]))
		$id = explode(',', $VAR["delete_id"]);
		elseif (isset($VAR["id"]))
		$id = explode(',', $VAR["id"]);

		### Loop:
		for($i=0; $i<count($id); $i++)
		{
			$arr['id'] = $id[$i];
			$del = true;

			### Update the queue status to 'delete'
			$db = &DB();
			$q = "UPDATE ".AGILE_DB_PREFIX."service SET
				        queue		= ".$db->qstr( 'delete' )." WHERE
				        id          = ".$db->qstr( $id[$i]  )." AND
				        site_id     = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			### Call the appropriate service deletion method
			$this->queue_one($id[$i], false);

			### Determine if this service should be automatically deleted.
			### If it is a non-realtime hosting record, we must leave the record in the db.
			$db = &DB();
			$q = "SELECT type,host_server_id FROM  ".AGILE_DB_PREFIX."service WHERE
				        id	 		= ".$db->qstr( $id[$i] )." AND
				        site_id     = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($q);
			if ($result && $result->RecordCount() == 0) {
				$del = false;
			} else {
				if 	($result->fields['type'] == 'host' || $result->fields['type'] == 'host_group')
				{
					$host_id = $result->fields['host_server_id'];
					$sql    = 'SELECT debug,provision_plugin FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
			                       id           =  ' . $db->qstr( $host_id ) . ' AND
			                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
					$rs = $db->Execute($sql);
					$file = $rs->fields['provision_plugin'];
					if(!empty($file) && is_file(PATH_PLUGINS . 'provision/'.$file.'.php')) {
						require_once ( PATH_PLUGINS . 'provision/'.$file.'.php' );
						eval ( '$_plg = new plgn_prov_'.$file.';' );
						if(@$_plg->remote_based == false)
						$del = false;
					}
				}
			}

			### Delete the service record
			if($del) $dbx->mass_delete($arr, $this, "");
		}
	}

	function search_form($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR)   {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR)  {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$dba = new CORE_database;
		$smart = $dba->search_show($VAR, $this, $type);

		global $smarty, $C_list;
		if($C_list->is_installed('host_server')) $host=true;
		$total_amount=0;
		$db = &DB();

		for($i=0; $i<count($smart); $i++)
		{
			$total_amount += $smart[$i]['price'];
			if($host && !empty($smart[$i]['host_server_id'])) {
				$id = $smart[$i]['host_server_id'];
				if(!empty($this->server[$id])) {
					$smart[$i]['server_name'] = $this->server_id;
				} else {
					$sql = $dba->sql_select("host_server", "name", "id = $id", false, $db);
					$rs = $db->Execute($sql);
					$this->server_id = $rs->fields['name'];
					$smart[$i]['server_name'] = $this->server_id;
				}
			}
		}

		$smarty->assign('service', $smart);
		$smarty->assign('total_amount', $C_list->format_currency($total_amount, ""));
	}

	/**
    	 * User initiate domain renewal 
    	 */
	function user_renew_domain($VAR)
	{
		# Validate user is owner of this domain
		$db = &DB();
		$rs = $db->Execute ( sqlSelect($db, 'service', '*', "id = ::{$VAR['id']}:: AND account_id = ". SESS_ACCOUNT) );
		if(!SESS_LOGGED OR !$rs OR $rs->RecordCount() == 0) {
			global $C_debug;
			$C_debug->alert('Unable to renew domain at this time');
			return;
		}
		include_once (PATH_MODULES.'invoice/invoice.inc.php');
		$invoice = new invoice;
		$id = $invoice->generatedomaininvoice($rs->fields, $invoice);
		if($id) {
			global $VAR;
			$VAR['id'] = $id;
			define('FORCE_PAGE', "invoice:user_view");
		}
	}

	function user_search($VAR) {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED)  {
			return false;
		}

		# Lock the account_id
		$VAR['service_account_id'] = SESS_ACCOUNT;
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function user_search_show($VAR)  {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED)  {
			return false;
		}
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}


	function user_view($VAR) {
		# Check that the correct account owns this billing record
		$dbx     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
                       id           =  ' . $dbx->qstr( @$VAR['id'] ) . ' AND
                       account_id   =  ' . $dbx->qstr( SESS_ACCOUNT ) . ' AND
                       site_id      =  ' . $dbx->qstr(DEFAULT_SITE);
		$rs = $dbx->Execute($sql);
		if (@$rs->RecordCount() == 0)
		{
			return false;
		}
		$this->construct();
		$VAR['user'] = true;
		$this->view($VAR, $this);
	}

	function search_export($VAR) {
		# require the export class
		$this->construct();
		require_once (PATH_CORE   . "export.inc.php");

		# Call the correct export function for inline browser display, download, email, or web save.
		if($VAR["format"] == "excel")
		{
			$type = "export_excel";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_excel($VAR, $this, $type);
		}
		else if ($VAR["format"] == "pdf")
		{
			echo 'Not Supported';
		}
		else if ($VAR["format"] == "xml")
		{
			$type = "export_xml";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_xml($VAR, $this, $type);
		}
		else if ($VAR["format"] == "csv")
		{
			$type = "export_csv";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_csv($VAR, $this, $type);
		}
		else if ($VAR["format"] == "tab")
		{
			$type = "export_tab";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_tab($VAR, $this, $type);
		}
	}

	function construct()  {
		$this->module = "service";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);
		$this->method   = $construct["construct"]["method"];
		$this->trigger  = $construct["construct"]["trigger"];
		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"];
	}
}
?>
