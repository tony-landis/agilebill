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
	
class checkout
{ 
	var $account_id;
	var $session_id;
	var $affiliate_id;
	var $campaign_id;
	
	var $admin_view=false;
	var $admin_checkout=false;
	var $admin_checkout_option=false; 
	
	/**
	 * Add Discount for Admin Checkout 
	 */
	function admin_adddiscount($VAR) {
		if(empty($VAR['amount'])) return false;
		if(empty($VAR['id'])) return false; 
		$db=&DB();
		$fields=Array('ad_hoc_discount'=>round($VAR['amount'],2));
		$db->Execute(sqlUpdate($db,"cart",$fields,"id = ::{$VAR['id']}:: ")); 
		return true;
	}

	/**
	 * Add Discount Code to Sess	 
	 */
	function adddiscount($VAR)
	{
		include_once(PATH_MODULES.'discount/discount.inc.php');
		$dsc=new discount();
		$dsc->add_cart_discount($VAR);
	}

	/**
	 * Admin Create the Invoice Record 
	 */
	function admin_checkoutnow($VAR)
	{		 
		# Get the account id & session_id
		if(!empty($VAR['account_id']))
		{
			$this->account_id = $VAR['account_id'];
			$db     = &DB();
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'session WHERE account_id   =  ' . $db->qstr( $this->account_id ) . ' AND  site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			if(!empty($rs->fields['id'])) {
				$this->session_id = $rs->fields['id'];
			} else {
				$this->session_id = SESS;
			}
		} else {
			return false;
		}

		# Get the affiliate details
		global $C_list;
		if(!$C_list->is_installed('affiliate')) {
			$this->affiliate_id = '';
		} else {
			if(SESS_AFFILIATE != "") {
				$this->affiliate_id = SESS_AFFILIATE;
			} else {
				# Get the affiliate details for this account
				$db     = &DB();
				$sql    = 'SELECT affiliate_id FROM ' . AGILE_DB_PREFIX . 'account WHERE id = ' . $db->qstr( $this->account_id ) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
				$rs = $db->Execute($sql);
				if(!empty($rs->fields['affiliate_id']))
				{
					$this->affiliate_id = $rs->fields['affiliate_id'];
				} else {
					# Get the affiliate account for the admin creating this invoice
					$db = &DB();
					$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE account_id =  ' . $db->qstr( SESS_ACCOUNT ) . ' AND  site_id =  ' . $db->qstr(DEFAULT_SITE);
					$rs = $db->Execute($sql);
					if(!empty($rs->fields['id']))
					$this->affiliate_id = $rs->fields['id'];
					else
					$this->affiliate_id = '';
				}
			}
		}
		$this->redirect = true;
		$this->admin_checkout = true;
		 
		# Is processor free checkout?
		if(@$VAR['option'] == '999') $this->admin_checkout_option = true;

		# Checkout
		if($this->checkoutnow($VAR, $this)) {
			echo '<script language="javascript">
                    window.parent.location = \'?_page=invoice:view&id='.$this->invoice_id.'\';
                    window.parent.window.parent.location = \'?_page=invoice:view&id='.$this->invoice_id.'\';
                    window.close();
                </script>';
		}
	}

	/**
	 * Get available checkout option
	 *
	 * @param int $account_id
	 * @param float $total
	 * @param array $product_arr Array of product_ids being purchased
	 * @param int $country_id
	 * @param bool $any_new
	 * @param bool $any_trial
	 * @param bool $any_recurring
	 * @return array
	 */
	function get_checkout_options($account_id,$total=0,$product_arr=false,$country_id=1,$any_new=false,$any_trial=false,$any_recurring=false) {		 
	 	$options=false;	 	
		if($any_trial) 		$options .= " AND allow_trial=1 ";
		if($any_recurring) 	$options .= " AND allow_recurring=1 ";
		if($any_new) 		$options .= " AND allow_new=1 ";
		if(!$options) return false;		
		$db=&DB();
		$chopt = $db->Execute(sqlSelect($db,"checkout","*","active=1 $options")); 
		if($chopt && $chopt->RecordCount()) {
			while( !$chopt->EOF )  {
				$show = true;
				# Check that the cart total is not to low:
				if ( $show == true && $chopt->fields["total_minimum"] != "" && $total < $chopt->fields["total_minimum"] ) $show = false;				  
				# Check that the cart total is not to high:
				if ( $show == true && $chopt->fields["total_maximum"] != "" && $total > $chopt->fields["total_maximum"] ) {
					$show = false;
				} elseif ($chopt->fields["total_maximum"] == '0' && $total > 0) {
					$show = false;
				} 
				# Check that the group requirement is met:
				if ( $show == true && !$this->admin_view && !empty ( $chopt->fields["required_groups"] ) ) {
					global $C_auth;
					$arr = unserialize ( $chopt->fields["required_groups"] );
					if(count($arr) > 0 && !empty($arr[0])) $show = false;
					for ( $i=0; $i<count($arr); $i++ )  {
						if($C_auth->auth_group_by_id($arr[$i])) {
							$show = true;
							$i=count($arr);
						}
					}
				} 
				# Check that the customer is not ordering a blocked SKU:
				if ( $show == true && !$this->admin_view && !empty ( $chopt->fields["excluded_products"] ) && $product_arr ) {
					$arr = unserialize ( $chopt->fields["excluded_products"] );
					if(count($arr) > 0)  {
						for($i=0; $i<count($product_arr); $i++)  {
							for($isk=0; $isk<count($arr); $isk++) {
								if($product_arr[$i] == $arr[$isk] && !empty($arr[$isk]) && !empty($product_arr[$i]['product_id']) ) {
									$show = false;
									//$i=count($smart);
									$isk=count($arr);
								}
							}
						}
					}
				} 
				$list_ord = 100;
				# Check if this method should be the default method: 
				if ( $show == true) {  
					# By Amount:
					if ( !empty ( $chopt->fields["default_when_amount"] ) ) {
						@$arr = unserialize ( $chopt->fields["default_when_amount"] );
						for ( $i=0; $i<count($arr); $i++ ) if ( $total >= $arr[$i] ) $list_ord--; $i=count($arr);						 
					} 
					# By Currency
					if ( !empty ( $chopt->fields["default_when_currency"] ) ) {
						@$arr = unserialize ( $chopt->fields["default_when_currency"] );
						for ( $i=0; $i<count($arr); $i++ ) if ( SESS_CURRENCY == $arr[$i] ) $list_ord--; $i=count($arr); 
					} 
					# By Group
					if ( !empty ( $chopt->fields["default_when_group"] ) ) {
						@$arr = unserialize ( $chopt->fields["default_when_group"] );
						global $C_auth;
						for ( $i=0; $i<count($arr); $i++ )  if ( $C_auth->auth_group_by_account_id( $account_id, $arr[$i] ) ) $list_ord--; $i=count($arr); 
					} 
					# By Country
					if ( !empty ( $chopt->fields["default_when_country"] ) ) {
						@$arr = unserialize ( $chopt->fields["default_when_country"] );
						for ( $i=0; $i<count($arr); $i++ )  if ( $country_id == $arr[$i] ) $list_ord--; $i=count($arr); 
					} 
					# Add to the array
					$checkout_options[] = Array ('sort'=>$list_ord, 'fields'=>$chopt->fields); 
				}
				$chopt->MoveNext();
			} 
			# Sort the checkout_options array by the [fields] element
			if(count($checkout_options) > 0 ) {
				foreach ( $checkout_options as $key => $row ) $sort[$key] = $row["sort"];
				array_multisort ( $sort, SORT_ASC, $checkout_options );
				return $checkout_options;
			}
		} else {
			return false;
		} 
		return false;
	}
		
	
	/**
     * Admin View Preview / Confirm prior to checkout  
     */
	function admin_preview($VAR) { 
		global $C_auth;
        if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow')) {
        	$this->account_id=$VAR['account_id'];
        	$this->admin_view = true;
        } else {
        	$this->account_id=SESS_ACCOUNT; 
        }
		$this->preview($VAR, $this);
	}
		
	/**
	 * Preview / Confirm prior to checkout
	 */ 
	function preview($VAR) { 		
		if(!SESS_LOGGED) return false;
		$db = &DB();  
		
		if(empty($this->session_id)) $this->session_id = SESS;
		if(empty($this->account_id)) $this->account_id = SESS_ACCOUNT;
				
		include_once ( PATH_MODULES . '/cart/cart.inc.php' );
		$cartObj = new cart;
		$cartObj->account_id=$this->account_id;
		$cartObj->session_id=$this->session_id;
		$result = $cartObj->get_contents($db);
		if($result->RecordCount() == 0) return false;
 
		// load invoice object
		include_once(PATH_MODULES.'invoice/invoice.inc.php');
		$invoice = new invoice;
		$invoice->initNew(0);	 
		$invoice->account_id = $this->account_id;
	  
		// Get the account details: 
		$account = $db->Execute(sqlSelect($db,"account","*","id=::$this->account_id::"));
		$invoice->country_id = $account->fields['country_id'];
		$invoice->state = $account->fields['state'];

		// load tax object for tax calculation 
		include_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;
		 
		// load discount object for discount calculation 
		include_once(PATH_MODULES.'discount/discount.inc.php');
		$discountObj=new discount;
		$discountObj->available_discounts($invoice->account_id);
		    
		// put cart contents into invoice format
		$cartObj->put_contents_invoice($db, $result, $invoice, $smart, $taxObj, $discountObj);	
		
		// get available checkout options
		foreach($invoice->invoice_item as $item) if(!empty($item['product_id'])) $product_arr[]=$item['product_id']; 
		$checkout_options = $this->get_checkout_options($this->account_id,$invoice->total_amt,@$product_arr,$invoice->country_id,$invoice->any_new, $invoice->any_trial, $invoice->any_recurring);
		$checkout_c = count($checkout_options);
		
		global $smarty;
		$smarty->assign('results',  count($invoice->invoice_item));
		$smarty->assign('cart', 	$smart); 
		$smarty->assign('sub_total',($invoice->total_amt+$invoice->discount_amt)-$invoice->tax_amt);
		$smarty->assign('total', 	$invoice->total_amt); 
		$smarty->assign('discount', $invoice->group_discounts());
		$smarty->assign('tax', 	  	$invoice->group_taxes());		
		$smarty->assign('checkout', $checkout_options);
		$smarty->assign('checkout_c', $checkout_c);
		$checkout_c--;
		$smarty->assign('last_checkout_id', $checkout_options["$checkout_c"]['fields']['id']);
		 
	}
	
	/**
	 * Create the Invoice Record and send user to checkout	 
	 */
	function checkoutnow($VAR)
	{
		global $C_translate, $C_list, $smarty;
		$db = &DB();
		
		// Validate user is logged in:
		if(!SESS_LOGGED) {
			echo '<script language="JavaScript">alert("You must be logged in to complete this purchase! Please refresh this page in your browser to login now...");</script>';
			return false;
		}
		
		// check for admin
		if(!$this->admin_checkout && !empty($VAR['account_id'])) {
			global $C_auth;
       		if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow')) {
        		$this->account_id=$VAR['account_id'];
        		$this->admin_checkout=true;
        	} else {
        		$this->account_id=SESS_ACCOUNT; 
        	}		
		}
		 
 		if(empty($this->session_id)) $this->session_id = SESS;
		if(empty($this->account_id)) $this->account_id = SESS_ACCOUNT;
				
		include_once ( PATH_MODULES . '/cart/cart.inc.php' );
		$cartObj = new cart;
		$cartObj->account_id=$this->account_id;
		$cartObj->session_id=$this->session_id;
		$result = $cartObj->get_contents($db);
		if($result->RecordCount() == 0) return false;
 
		// load invoice object
		include_once(PATH_MODULES.'invoice/invoice.inc.php');
		$invoice = new invoice;
		$invoice->account_id = $this->account_id;
		$invoice->initNew(0);	 
	  
		// Get the account details: 
		$account = $db->Execute(sqlSelect($db,"account","*","id=::$this->account_id::"));
		$invoice->country_id = $account->fields['country_id'];
		$invoice->state = $account->fields['state'];

		// load tax object for tax calculation 
		include_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;
		 
		// load discount object for discount calculation 
		include_once(PATH_MODULES.'discount/discount.inc.php');
		$discountObj=new discount;
		$discountObj->available_discounts($invoice->account_id);
		    
		// put cart contents into invoice format
		$cartObj->put_contents_invoice($db, $result, $invoice, $smart, $taxObj, $discountObj);			  
		
 		// Validate and init a checkout plugin
 		$checkout=false;
 		if($this->admin_checkout_option) {
 			// admin checkout option specified
			include_once ( PATH_MODULES . 'checkout/checkout_admin.inc.php' );
			$PLG = new checkout_admin; 		
			$checkout=true;	
			$invoice->checkout_plugin_id=false;
 		} else { 
			// get available checkout options and check against the one provided
			$invoice->checkout_plugin_id=$VAR['option'];		
			foreach($invoice->invoice_item as $item) if(!empty($item['product_id'])) $product_arr[]=$item['product_id']; 
			$checkout_options = $this->get_checkout_options($this->account_id,$invoice->total_amt,@$product_arr,$invoice->country_id,$invoice->any_new, $invoice->any_trial, $invoice->any_recurring);
			if($checkout_options) { 
				foreach($checkout_options as $a) { 
					if($a['fields']['id']==$invoice->checkout_plugin_id) { 
						// load the selected checkout plugin and run pre-validation 	
						$checkout_plugin=$a['fields']['checkout_plugin'];	
						$plugin_file = PATH_PLUGINS . 'checkout/'.$checkout_plugin.'.php';
						include_once ( $plugin_file );
						eval ( '$PLG = new plg_chout_'.$checkout_plugin.'("'.$invoice->checkout_plugin_id.'");'); 
						$plugin_validate = $PLG->validate($VAR, $this);
						if ( $plugin_validate != true ) {
							echo $plugin_validate;
							return false;
						}  		
						$checkout=true;
						break;
					}
				}
			} 
 		}
		if(!$checkout) {
			echo '<script language=Javascript> alert("Unable to checkout with the selected method, please select another."); </script> ';
			return false;
		}
		
		// validate credit card on file details
		global $VAR; 
		if(!empty($VAR['account_billing_id']) && @$VAR['new_card']==2) {  
			$invoice->account_billing_id=$VAR['account_billing_id']; 
			/* validate credit card on file details */ 
			if(!$PLG->setBillingFromDB($this->account_id, $invoice->account_billing_id, $invoice->checkout_plugin_id)) { 
				global $C_debug;
				$C_debug->alert("Sorry, we cannot use that billing record for this purchase.");
				return false;
			}
		} else {
			/* use passed in vars */
			$PLG->setBillingFromParams($VAR);
		}		
		   	
		// validate recurring processing options
		if ($PLG->recurr_only) {
			if ($invoice->recur_amt<=0) {
				echo '<script language=Javascript> alert("Cannot process non-recurring charges with this payment option, please select another payment option."); </script> ';
				return false;
			}
			if(is_array($invoice->recur_arr) && count($invoice->recur_arr)>1) {
				$recurring = true;
				// validate recur day and recurring schedule are the same for both products
				foreach($invoice->recur_arr as $a) { 
					foreach($invoice->recur_arr as $b) { 
						foreach($b as $key=>$val) {	 
							if($key != 'price' && $key != 'recurr_week' && $a[$key] != $val) {
								$recurring=false;
								break;
							}
						}
					}
				} 
				if (!$recurring) {
					echo '<script language=Javascript> alert("This payment option cannot be used when ordering both prorated and non-prorated subscriptions, or when ordering two or more subscriptions with different billing schedules selected. Please make sure all your subscriptions have the same billing schedule selected, try another payment option, or order one subscription at a time. We apologize for any inconvenience."); </script> ';
					return false;
				}
			}
		}
		
		# Affiliate
		if(empty($this->affiliate_id)) {
			if(!empty($account->fields['affiliate_id']))
				$invoice->affiliate_id = $account->fields['affiliate_id'];
			else
				$invoice->affiliate_id = SESS_AFFILIATE;
		}

		# Campaign
		if(empty($this->campaign_id)) {
			if(!empty($account->fields['campaign_id']))
				$invoice->campaign_id = $account->fields['campaign_id'];
			else
				$invoice->campaign_id = SESS_CAMPAIGN;
		}
		 		 
		$invoice->record_id	= sqlGenID($db,"invoice"); 
		$invoice->actual_billed_currency_id	= SESS_CURRENCY;
		$invoice->billed_currency_id = DEFAULT_CURRENCY;
		$invoice->checkout_type = $PLG->type;
		
		// initial invoice status
		if( $invoice->total_amt == 0 || $PLG->type == 'gateway') {
			$invoice->billing_status = 1;   
			$invoice->actual_billed_amt = $C_list->format_currency_decimal($invoice->total_amt, SESS_CURRENCY);
			$invoice->billed_amt = $invoice->total_amt;
		}

		// Currency conversion:
		if (SESS_CURRENCY != DEFAULT_CURRENCY) {
			$bill_amt = $C_list->format_currency_decimal ($invoice->total_amt, SESS_CURRENCY);
			$recur_amt = $C_list->format_currency_decimal ($invoice->recur_amt, SESS_CURRENCY);
		} else {
			$bill_amt = round($invoice->total_amt,2);
			$recur_amt = round($invoice->recur_amt,2);
		}
		
		// Get currency ISO (three_digit) for checkout plugin
		$currrs = $db->Execute(sqlSelect($db,"currency","three_digit","id=".SESS_CURRENCY));
		if($currrs && $currrs->RecordCount()) $currency_iso = $currrs->fields['three_digit'];

		// Run the plugin bill_checkout() method:
		$currency_iso = $C_list->currency_iso(SESS_CURRENCY);
		$invoice->checkout_plugin_data = $PLG->bill_checkout($bill_amt, $invoice->record_id, $currency_iso, $account->fields, $recur_amt, $invoice->recur_arr);
		if($invoice->checkout_plugin_data === false || $invoice->checkout_plugin_data == '' ) {
			if(!empty($PLG->redirect)) echo $PLG->redirect;
			return false; 
		} elseif ($PLG->type == "gateway" || empty($PLG->redirect)) {
			$VAR['id'] = $invoice->record_id;			 
			if(!$this->admin_checkout) $VAR['_page'] = "invoice:thankyou"; 
			$invoice->checkout_plugin_data=false; 
		} elseif(!$this->admin_checkout) { 
			echo "<html><head></head><body><center>
				Please wait while we redirect you to the secure payment site....
				{$PLG->redirect}</center></body></html>";
		}

		// Call the Plugin method for storing the checkout data:
		$invoice->account_billing_id = $PLG->store_billing($VAR, $invoice->account_id);
  
		// clear user discounts		 
		$fields=Array('discounts'=>"");
		$db->Execute(sqlUpdate($db,"session",$fields,"id = ::".SESS."::"));
		  
		// admin options
		$email=true;
		if($this->admin_checkout) { 
			if(empty($VAR['send_email']) || $VAR['send_email']=='false') $email=false; else $email=true;
			if(!empty($VAR['due_date'])) $invoice->due_date=$this->getInputDate($VAR['due_date']);
			if(!empty($VAR['grace_period'])) $invoice->grace_period=$VAR['grace_period'];
			if(!empty($VAR['notice_max'])) $invoice->notice_max=$VAR['notice_max']; 
		}
 		
		if($invoice->commitNew($taxObj, $discountObj, $email)) {
			// delete all cart items 
			$db->Execute(sqlDelete($db,"cart", "(session_id=::".SESS.":: OR account_id=$invoice->account_id)")); 
			// admin redirect
			if($this->admin_checkout) {
				$url = URL.'admin.php?_page=invoice:view&id='.$invoice->record_id; 
				echo '<script language="javascript"> parent.location.href=\''.$url.'\';</script>';
			}		 
		} 
		return false; 		
	}

	/** Convert a localized d,m,y string to epoch timestamp 
	*/
	function getInputDate($date) { 
	 
  		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
        $Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);
        for($i=0; $i<3; $i++)
        {
            if($Arr_format[$i] == 'd') $day = $Arr_date[$i];
            if($Arr_format[$i] == 'm') $month = $Arr_date[$i];
            if($Arr_format[$i] == 'Y') $year = $Arr_date[$i];
        }
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        return $timestamp;		
      
		return time();
	}
	
	/**
	 * Manage postback for multiple invoices 
	 */
	function postback_multiple($arr) {
		$db=&DB();
		include_once(PATH_MODULES.'invoice/invoice.inc.php');
		$invoice=new invoice; 
		
		// get multi-invoice details
		$total = $invoice->multiple_invoice_total($arr['invoice_id']);
		if(!$total) return false; 
		
		$amt = $arr['amount'];
		
		foreach($invoice->invoice_id as $id) 
		{
			if($amt > 0)
			{
				// get total due for this invoice:
				$rs=sqlSelect($db, "invoice","SUM(total_amt-billed_amt) as total","id=$id");
				if($rs && $rs->RecordCount()) {
					$thisamt = $rs->fields["total"];
					
					if($thisamt > $amt)
						$arr['amount'] = $amt;
					else
						$arr['amount'] = $thisamt;
					$arr["invoice_id"] = $id;
					
					$this->postback($arr);
					$amt -= $thisamt;
				}
			} 			 
		} 
	}
	
	/**
	 * Postback for Redirect Pay 
	 */
	function postback($arr)
	{
		global $C_debug;
		
		if(empty($arr['invoice_id'])) return false; 
		if(empty($arr['transaction_id'])) return false; 
		if(empty($arr['amount'])) return false;
		
		if(preg_match("/MULTI-/i", $arr['invoice_id'])) {
			$this->postback_multiple($arr);
			return;
		}

		# Get the latest invoice info:
		$db = &DB(); 

		$sql1 ="";
		if(!empty($arr['subscription_id']))
		$sql1 = "checkout_plugin_data = ".$db->qstr( trim($arr['subscription_id']) )."  OR ";

		$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice WHERE
	       			( 
						$sql1
	       				parent_id = ".$db->qstr(@$arr['invoice_id'])."
						OR
						id        = ".$db->qstr(@$arr['invoice_id'])."  
					)  
					AND
	       			billing_status != 1
					AND 
	       			site_id = ".$db->qstr(DEFAULT_SITE)."
	       			ORDER BY date_orig
	       			LIMIT 0,1";        	
		$invoice = $db->Execute($q);

		if ($invoice === false || $invoice->RecordCount()==0)
		$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

		if($invoice->RecordCount() == 0)
		return false;

		$invoice_id = $invoice->fields['id'];

		# Validate the currency
		$billed_currency_id 		= $invoice->fields['billed_currency_id'];
		$total_amt 					= $invoice->fields['total_amt'];
		$billed_amt 				= $invoice->fields['billed_amt'];
		$actual_billed_amt 			= $invoice->fields['actual_billed_amt'];
		$currency_iso 				= @$arr['currency'];

		if(empty($currency_iso) || !$currency_iso)
		{
			# same as billed_currency_id
			$this->billed_amt 				 = $arr['amount'] + $billed_amt;
			$this->actual_billed_amt 		 = $arr['amount'] + $billed_amt;
			$this->actual_billed_currency_id = $billed_currency_id;
		}
		else
		{
			# Get the actual billed currency id currency info:
			$q  = "SELECT * FROM ".AGILE_DB_PREFIX."currency WHERE
	        			three_digit	= ".$db->qstr($currency_iso)." AND
	        			site_id = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($q);

			if ($result === false)
			$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

			$actual_billed_currency_id = $result->fields['id'];

			if(is_string($result->fields["convert_array"]))
			$convert = unserialize($result->fields["convert_array"]);
			else
			$convert = false;

			$this->format_currency[$actual_billed_currency_id] = Array (
			'symbol'        => $result->fields["symbol"],
			'convert'       => $convert,
			'iso'           => $result->fields["three_digit"]);

			if($result->RecordCount() == 0 || $actual_billed_currency_id == $billed_currency_id)
			{
				# same as billed_currency_id
				$this->billed_amt 				 = $arr['amount'] + $billed_amt;
				$this->actual_billed_amt 		 = $arr['amount'] + $billed_amt;
				$this->actual_billed_currency_id = $actual_billed_currency_id;
			}
			else
			{
				# Get the billed currency id currency info:
				$q  = "SELECT * FROM ".AGILE_DB_PREFIX."currency WHERE
		        			id   	= ".$db->qstr($billed_currency_id)." AND
		        			site_id = ".$db->qstr(DEFAULT_SITE);
				$result = $db->Execute($q);

				if ($result === false)
				$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

				$this->format_currency[$billed_currency_id] = Array (
				'symbol'        => $result->fields["symbol"],
				'convert'       => unserialize($result->fields["convert_array"]),
				'iso'           => $result->fields["three_digit"]);

				# Convert the invoice amount to the actual billed currency amount
				$due_amount = $invoice->fields['total_amt'] - $invoice->fields['billed_amt'];
				$conversion = $this->format_currency[$billed_currency_id]["convert"][$actual_billed_currency_id]["rate"];

				$this->billed_amt 				 = $billed_amt + ($arr['amount'] /= $conversion);
				$this->actual_billed_amt 		 = $actual_billed_amt + $arr['amount'];
				$this->actual_billed_currency_id = $actual_billed_currency_id;
			}
		}


		# Check for any subscription_id
		if(!empty($arr['subscription_id'])) {
			$this->subscription_id = trim($arr['subscription_id']);
		} else {
			$this->subscription_id = trim($invoice->fields['checkout_plugin_data']);
		}

		# Check for the checkout_id
		if(!empty($arr['checkout_id'])) {
			$this->checkout_id = $arr['checkout_id'];
		} else {
			$this->checkout_id = $invoice->fields['checkout_plugin_id'];
		}

		# Check for the billing status:
		if($this->billed_amt >= $invoice->fields['total_amt']) {
			$this->billing_status 	= '1';
		} else {
			$this->billing_status  	= '0';
		}

		# Check if this transaction_id has already been processed:
		$q = "SELECT id FROM ".AGILE_DB_PREFIX."invoice_memo WHERE
        			invoice_id 	= ".$db->qstr($invoice_id)." AND
        			type		= ".$db->qstr('postback')." AND
        			memo		= ".$db->qstr($arr['transaction_id'])." AND
        			site_id 	= ".$db->qstr(DEFAULT_SITE);        	
		$memo = $db->Execute($q);

		if ($memo === false)
		$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

		if ($memo->RecordCount() > 0)  {
			# duplicate post:
			$C_debug->error('Duplicate Postback','checkout.inc.php :: postback()', "Duplicate postback for invoice {$arr['invoice_id']} & transaction id {$arr['transaction_id']}");
		} else {
			# Create the invoice memo:
			$memo_id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
			$q = "INSERT INTO
	        			".AGILE_DB_PREFIX."invoice_memo 
	        	      SET
	        			id 					= ".$db->qstr($memo_id).",
	        			site_id 			= ".$db->qstr(DEFAULT_SITE).",
	        			date_orig 			= ".$db->qstr(time()).", 
	        			invoice_id	 		= ".$db->qstr($invoice_id).", 
	        			account_id			= ".$db->qstr(0).", 
	        			type				= ".$db->qstr('postback').", 
	        			memo				= ".$db->qstr($arr['transaction_id']) ;
			$memosql = $db->Execute($q);

			if ($memosql === false)
			$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

			# Update the invoice billing info:
			$q = "UPDATE
	        			".AGILE_DB_PREFIX."invoice 
	        	      SET
	        			date_last 			= ".$db->qstr(time()).", 
	        			billing_status 		= ".$db->qstr($this->billing_status).", 
	        			checkout_plugin_id	= ".$db->qstr($this->checkout_id).", 
	        			checkout_plugin_data = ".$db->qstr($this->subscription_id).", 
	        			billed_amt			= ".$db->qstr($this->billed_amt).", 
	        			actual_billed_amt	= ".$db->qstr($this->actual_billed_amt).", 
	        			actual_billed_currency_id = ".$db->qstr($this->actual_billed_currency_id)."
	        		   WHERE
	        			id 			= ".$db->qstr($invoice_id)." AND
	        			site_id 	= ".$db->qstr(DEFAULT_SITE);
			$memosql = $db->Execute($q);

			if ($memosql === false)
			$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

			# Update the invoice approval status
			$VAR['id'] = $invoice_id;
			include_once(PATH_MODULES.'invoice/invoice.inc.php');
			$inv = new invoice;
			if(!$arr['status'])
			{
				# void
				$inv->voidInvoice($VAR);

				# create a record of the viod in an invoice memo:
				$memo_id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
				$q = "INSERT INTO
		        			".AGILE_DB_PREFIX."invoice_memo 
		        	      SET
		        			id 					= ".$db->qstr($memo_id).",
		        			site_id 			= ".$db->qstr(DEFAULT_SITE).",
		        			date_orig 			= ".$db->qstr(time()).", 
		        			invoice_id	 		= ".$db->qstr($invoice_id).", 
		        			account_id			= ".$db->qstr(0).", 
		        			type				= ".$db->qstr('void').", 
		        			memo				= ".$db->qstr("Voided due to postback: ".$arr['transaction_id']) ;
				$rsql = $db->Execute($q);

				if ($rsql === false)
				$C_debug->error('checkout.inc.php','postback', $q . " | " . @$db->ErrorMsg());

			} else {

				# approve
				$inv->autoApproveInvoice($invoice_id);

				# User invoice payment confirmation
				include_once(PATH_MODULES.'email_template/email_template.inc.php');
				$email = new email_template;
				$email->send('invoice_paid_user', $invoice->fields['account_id'], $invoice_id, DEFAULT_CURRENCY, '');

				# Admin alert of payment processed
				$email = new email_template;
				$email->send('admin->invoice_paid_admin', $invoice->fields['account_id'], $invoice_id, DEFAULT_CURRENCY, '');
			}
		}
		return true;
	}

	/**
	 * Display Checkout Data Form  
	 */
	function checkoutoption($VAR) {
		global $VAR, $C_translate, $smarty;

		if(SESS_LOGGED != '1') {
			$smarty->assign('plugin_template', false);
			return false;
		}

		// Normal checkout
		$db = &DB();
		$q  = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE site_id=".DEFAULT_SITE." AND id=".$db->qstr(@$VAR["option"]);	
		$rs = $db->Execute($q); 
		if($rs == false || $rs->RecordCount() == 0) {
			$smarty->assign('plugin_template', false);
			return false;
		}
		
		// determine account id
		global $C_auth;
        if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow')) {
        	$this->account_id=$VAR['account_id'];
        	$this->admin_view = true;
        } else {
        	$this->account_id=SESS_ACCOUNT; 
        }		
          
		// Set account options && seed VAR with defaults
		if(empty($VAR['detailsnocopy'])) {
			$acct = $db->Execute($sql=sqlSelect($db,"account","first_name,last_name,address1,address2,city,state,zip,country_id,email,company","id=".$this->account_id));
			if($acct && $acct->RecordCount())  
				foreach($acct->fields as $key=>$val)  
					if(!is_numeric($key) && empty($VAR["$key"]))  
						$VAR["$key"]=stripslashes($acct->fields["$key"]); 		 						
		} 
		  
		global $C_vars;
		$C_vars->strip_slashes_all(); 
		$smarty->assign('VAR', $VAR); 		
		 
		$smarty->assign('plugin_template', 'checkout_plugin:plugin_ord_' . $rs->fields["checkout_plugin"]);
	}

	function add($VAR) {
		$this->checkout_construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	function view($VAR) {
		$this->checkout_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function update($VAR) {
		$this->checkout_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR) {
		$this->checkout_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	function search($VAR) {
		$this->checkout_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR) {
		$this->checkout_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}

	function checkout_construct() {
		$this->module = "checkout";
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