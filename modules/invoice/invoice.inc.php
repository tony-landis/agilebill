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
	
	
/**
 * The main AgileBill Invoice Class 
 */
class invoice
{
	/**  Enable summary invoice view that rolls multiple instances of the same sku w/identical base&setup price & attributes into one line item */
	var $summarizeInvoice=true;	
	
	/**
	 * Holds sku's of line items to exclude from invoice summarys (pdf view only)
	 *
	 * @var array
	 */
	var $summarizeInvoiceExclude;	 
 
	/** Invoice type, 0=new, 1=recurr */
	var $type=0;
	
	/** Invoice Creation Timestamp */
	var $date_orig;
	
	/** Last modification Timestamp */
	var $date_last;
	
	/** Invoice Id */
	var $record_id;
	
	/** Invoice Item Id */
	var $item_id=1;
	
	/** Parent Invoice Id for recurring */
	var $parent_id;	
	
	/** Account Id for invoice */
	var $account_id;
	
	/** Affiliate Id of Invoice */
	var $affiliate_id;
	
	/** Account Billing Id */
	var $account_billing_id;
	
	/** Campaign Id */
	var $campaign_id;
	
	/** Reseller Id */
	var $reseller_id;

	/** Billed Currency Id */
	var $billed_currency_id;
	
	/** Actual Billed Currency selected by Client */
	var $actual_billed_currency_id;
	 
	/** Checkout Plugin Id */
	var $checkout_plugin_id;
	
	/** Array of checkout plugin data returned by checkout plugin */
	var $checkout_plugin_data;
	
	/** Current Notice Count */
	var $notice_count=0;
			
	/** Last Notice Timestamp */
	var $notice_date;	
	
	/** Due Date Timestamp */
	var $due_date;
	
	/** Net Term Id */
	var $net_term_id=false;
	
	/** Net Term Last Notice/Late fee Timestamp */
	var $net_term_date_last;
	
	/** Net Term Interval Count */
	var $net_term_intervals=0;
	
	/** Process Status */
	var $process_status=0;
	
	/** Billing Status */
	var $billing_status=0;
	
	/** Suspend Billing Status */
	var $suspend_billing=0;
	
	/** Printed Invoice Status */
	var $print_status=0;
	
	/** Refunded Invoice Status */
	var $refund_status=0;
	
	/** Calcuate Taxes */
	var $tax=true;		

	/** Calculate Discounts */
	var $discount=true;			 
	
	/** Total Invoice Amount */
	var $total_amt=0;
	
	/** Total Amount Billed */
	var $billed_amt=0;
	
	/** Actual total amount billed (converted to local) */
	var $actual_billed_amt=0;
	
	/** Total Tax Amount */
	var $tax_amt=0;
	
	/** Total Discount Amount */
	var $discount_amt=0;
 
	/** Recurring Amount */
	var $recur_amt;
	
	/** Recurring Array for Later Processing */
	var $recur_arr;
	
	/** IP Address of User */
	var $ip=USER_IP;
	
	/** Array of the Invoice items  */
	var $invoice_item;
	
	/** Array of the discounts for the Invoice items */
	var $item_discount;
	
	/** Array of the taxes for the Invoice Items */
	var $item_tax;
	
	/** Tracking to determine payment options */
 	var $any_new=false;
 	var $any_trial=false;
 	var $any_recurring=false;
 	
 	/** Invoice Config Global Options */
 	var $invoice_delivery=1;
 	var $invoice_format=0; 
	var $notice_max=MAX_BILLING_NOTICE; 
	var $grace_period=GRACE_PERIOD; 	
 	 
	
	
	/**
	 * Get the global level invoice settings 
	 */
	function setupGlobal() {
		$db=&DB();
		$invopt=$db->Execute(sqlSelect($db,"setup_invoice","*","")); 
		if($invopt && $invopt->RecordCount()) { 
			$this->invoice_delivery=$invopt->fields['invoice_delivery'];
			$this->invoice_format=$invopt->fields['invoice_show_itemized'];
		}
	}
	
	/**
	 * Get the account level invoice options
	 *
	 * @param int $id Account Id
	 */
	function setupAccount() {
		if(!$this->account_id) return; 
		$db=&DB();
		$acctrs=$db->Execute(sqlSelect($db,"account","invoice_grace,invoice_advance_gen","id=$this->account_id"));
		if($acctrs && $acctrs->RecordCount()) {
			$this->advance_gen=$acctrs->fields['invoice_advance_gen'];
			if($this->grace_period == GRACE_PERIOD && !empty($acctrs->fields['invoice_grace'])) $this->grace_period=$acctrs->fields['invoice_grace'];
		}  
	}	
	
	/**
	 * Initialize new invoice creation
	 *
	 * @param bool $type 0=new 1=recur
	 */
	function initNew($type=0) {	
		$this->type=$type;
		$this->date_orig=time();
		$this->date_last=time(); 
		
		global $C_list;
		$this->net_term = $C_list->is_installed('net_term'); 
		 
		// get account invoice defaults
		$this->setupAccount();
	}
		
	/**
	 * Commit the current invoice/items/discounts/taxes
	 *
	 * @param object $taxObj		Object for Tax Calculation
	 * @param object $discountObj	Object for Discount Calculation	  
	 * @param bool $email			Send customer/admin e-mails 
	 */
	function commitNew(&$taxObj, &$discountObj, $email=true) {
		
		// init DB transaction
		$db=&DB(); 
		$db->BeginTrans();
		
		// get invoice id
		if(empty($this->record_id)) $this->record_id = sqlGenID($db,"invoice");
		
		// serialized records:
		if(is_array($this->checkout_plugin_data)) $this->checkout_plugin_data=serialize($this->checkout_plugin_data);
		if(is_array($this->recur_arr)) $this->recur_arr=serialize($this->recur_arr);
		
		// dates & defaults
		if(empty($this->due_date)) $this->due_date=time();
		if(empty($this->date_orig)) $this->date_orig=time();
		if(empty($this->date_last)) $this->date_last=time();
		if(empty($this->notice_next_date)) $this->notice_next_date=$this->due_date+86400;
	  					
		// net terms  
		if ($this->net_term && !$this->billing_status && $this->total_amt>0) {
			include_once(PATH_MODULES.'net_term/net_term.inc.php');
			$net=new net_term; 
			$this->net_term_id = $net->termsAllowed($this->account_id, $this->checkout_plugin_id);
			if(empty($this->net_term_date_last)) $this->net_term_date_last=time();
		} 
		 			
		// insert invoice
		$fields=Array(
			'date_orig'=>$this->date_orig,
			'date_last'=>$this->date_last, 
			'parent_id'=>$this->parent_id, 
			'type'=>$this->type, 
			'process_status'=>$this->process_status, 
			'billing_status'=>$this->billing_status, 
			'suspend_billing'=>$this->suspend_billing,  
			'refund_status'=>$this->refund_status, 
			'print_status'=>$this->print_status, 
			'account_id'=>$this->account_id, 
			'account_billing_id'=>$this->account_billing_id, 
			'affiliate_id'=>$this->affiliate_id, 
			'campaign_id'=>$this->campaign_id, 
			'reseller_id'=>$this->reseller_id, 
			'checkout_plugin_id'=>$this->checkout_plugin_id, 
			'checkout_plugin_data'=>$this->checkout_plugin_data,
			'tax_amt'=>$this->tax_amt, 
			'discount_amt'=>$this->discount_amt, 
			'total_amt'=>$this->total_amt, 
			'billed_amt'=>$this->billed_amt, 
			'recur_amt'=>$this->recur_amt, 
			'recur_arr'=>$this->recur_arr, 
			'actual_billed_amt'=>$this->actual_billed_amt, 
			'billed_currency_id'=>$this->billed_currency_id, 
			'actual_billed_currency_id'=>$this->actual_billed_currency_id, 
			'notice_count'=>$this->notice_count, 
			'notice_max'=>$this->notice_max, 
			'notice_next_date'=>$this->notice_next_date,
			'due_date'=>$this->due_date, 
			'grace_period'=>$this->grace_period,   
			'net_term_id'=>$this->net_term_id, 
			'net_term_date_last'=>$this->net_term_date_last, 
			'net_term_intervals'=>$this->net_term_intervals, 
			'ip'=>$this->ip 
		);
		$db->Execute($sql=sqlInsert($db, "invoice", $fields, $this->record_id));
		
		// loop through invoice items
		if(is_array($this->invoice_item)) {
			foreach($this->invoice_item as $id=>$fields) {
				// get an invoice_item id
				$invoice_item_id = sqlGenID($db, "invoice_item");
				
				// domain sku's
				if ($fields['item_type'] == 2) {
					$fields['sku'] = "DOMAIN-".strtoupper($fields['domain_type']);	
					$fields['price_type'] = '0';
				}
				
				// build e-mail item details
				if($email) {
					$email_instructions='';
					if($fields['item_type']<2 && !empty($fields['product_id'])) {  
						// product, get email instructions and translated name
						$translate_prod=$db->Execute(sqlSelect($db,"product_translate","email_template,name","product_id={$fields['product_id']} and language_id=::".SESS_LANGUAGE."::"));
						if($translate_prod && $translate_prod->RecordCount()) {
							$instructions=$translate_prod->fields['email_template'];
							$name=$translate_prod->fields['name'];
						} else {
							$name=$fields["sku"];
						}
					} elseif ($fields['item_type'] == 2) { 
						$name=strtoupper($fields['domain_name'].".".$fields['domain_tld']);
					} else {
						if(!empty($fields['product_name']))	$name=$fields['product_name']; else $name=$fields['sku'];
					}
					// add to e-mail array	
					$email_arr[] = array('Qty' => '('.$fields["quantity"].')', 'Item' => 'SKU '.$fields["sku"], 'Price' => number_format($fields["total_amt"],2), 'Name' => $name, 'Instructions' => $instructions);											
				} 
				
				// insert the invoice item_id 
				$fields['invoice_id']=$this->record_id;
				$fields['date_orig']=time();
				$attr = serialize($fields['product_attr']);
				$fields['product_attr']=$fields['product_attr_cart'];
				$fields['product_attr_cart']=$attr; 
				$db->Execute($sql=sqlInsert($db, "invoice_item", $fields, $invoice_item_id));
			 
				// insert taxes
				if($this->tax && $this->tax_amt > 0 && !empty($this->tax_arr[$id])) {
					$taxObj->invoice_item($this->record_id, $invoice_item_id, $this->account_id, $this->tax_arr[$id]);
				}
				
				// insert discounts
				if($this->discount && $this->discount_amt>0 && !empty($this->discount_arr[$id])) {
					$discountObj->invoice_item($this->record_id, $invoice_item_id, $this->account_id, $this->discount_arr[$id]);				
				} 
			}
		}
		 
		// complete DB transaction
		$db->CompleteTrans();
		
		// complete building e-mail notices and send
		if($email) { 
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			
        	// Create the products order list for the e-mail:
        	$e_itm_usr = '';
        	$e_itm_adm = '';
        	if(is_array($email_arr)) {
	        	foreach($email_arr as $i=>$em) {
	        		$e_itm_usr .= $em['Qty'].'  '.$em['Item'].' ('.$em['Name'].')  '.$em['Price'];
	        		$e_itm_adm .= $em['Qty'].'  '.$em['Item'].' ('.$em['Name'].')  '.$em['Price']."\r\n";
	        		if(!empty($email_arr[$i]['Instructions'])) $e_itm_usr .= "\r\n	* " . $email_item_arr[$i]['Instructions'];	        		
	        		$e_itm_usr .= "\r\n";   		
	        	} 
	        	$e_arr_user = Array('%products%' => $e_itm_usr);
	        	$e_arr_adm  = Array('%products%' => $e_itm_adm);
        	}
        	
        	// e-mail invoice creation confirmation 
        	$mail = new email_template;
    		$mail->send('invoice_confirm_user',  $this->account_id, $this->record_id, $this->checkout_plugin_id, $e_arr_user); 
    		$email = new email_template;
    		$email->send('admin->invoice_confirm_admin', $this->account_id, $this->record_id, $this->checkout_plugin_id, $e_arr_adm); 
		}
		
		// net terms?
		if($this->net_term_id) {
			$this->approveInvoice(array('id'=>$this->record_id), $this);  
			return $this->record_id;
		}	
    	   
		// Determine the approval status by checkout plugin type & settings:
		if($email && $this->billing_status == 0 && $this->billed_amt > 0 ) {
			global $C_list;
			if($this->checkout_type == 'redirect') {
				// User e-mail alert of due invoice
				$email = new email_template;
				$email->send('invoice_due_user', $this->account_id, $this->record_id, $user_currency, $C_list->date($this->due_date));
			} elseif ($this->checkout_type == 'other') {
				// Admin e-mail alert of manual payment processing
				$email = new email_template;
				$email->send('admin->invoice_due_admin', $this->account_id, $this->record_id, $admin_currency, $C_list->date($this->due_date));
			}
		} elseif($this->billed_amt>0 ) {
			if($email) {
				// User alert of payment processed
				$email = new email_template;
				$email->send('invoice_paid_user', $this->account_id, $this->record_id, $this->billed_currency_id, '');
				// Admin alert of payment processed
				$email = new email_template;
				$email->send('admin->invoice_paid_admin', $this->account_id, $this->record_id, $this->billed_currency_id, '');
			}
			$this->autoApproveInvoice($this->record_id);
		} elseif($this->billed_amt == 0 && $this->billing_status == 1 ) {
			$this->autoApproveInvoice($this->record_id);
		}
		
		// return invoice id
		return $this->record_id;
	}
  
	/**
	 * Add an invoice item
	 *
	 * @param int $id 					Reference ID for use in Cart or false
	 * @param object $taxObj			Object for Tax Calculation
	 * @param object $discountObj		Object for Discount Calculation
	 * @param int $item_type 			0/1=Product/Service/Hosting  2=Domain  3=Add Hoc 
	 * @param string $taxable			True, False, or 'validate' to locate the specified $product id and verify
	 * @param int $service_id			If this is for a service upgrade, this will be defined
	 * @param int $parent_id			Item Parent Id
	 * @param int $product_id			Item Product Id
	 * @param array $product_attr		Item attributes from the cart/prev service
	 * @param string $product_name		Item product name
	 * @param string $sku				Item Product SKU
	 * @param int $quantity				Item Quantity
	 * @param float $price_base			Item Base price 
	 * @param float $price_setup		Item Setup Price
	 * @param float $discount_manual	Ad Hoc Discount Amount
	 * @param int $recurring_schedule	Item recurring schedule, 0=week, 1=month, 2=quarter, 3=semi-annual, 4=annual, 5=bi-year
	 * @param int $date_start			Date service started
	 * @param int $date_stop			Date service stops
	 * @param string $domain_name		Domain name
	 * @param string $domain_tld		Domain TLD
	 * @param int $domain_term			Domain Term
	 * @param string $domain_type		Domain Type (register, transfer, renew, park, ns_transfer)	 
	 */
	function addItem($id, &$taxObj, &$discountObj, $item_type, $taxable=false, $service_id=false, $parent_id=false, $product_id=false, $product_attr=false, $product_name=false, $sku=false, $quantity=1, $price_base=false, $price_setup=false, $discount_manual=false, $recurring_schedule=false, $date_start=false, $date_stop=false, $domain_name=false, $domain_tld=false, $domain_term=false, $domain_type=false) {
		$tax_amt=0;
		$total_amt=0;
		$discount_amt=0;
		
		// define correct qty
		if($quantity<=0) $quantity=1;
		
		// determine the reference id for this item 
		if($id>0) {
			$this->item_id=$id;
		} else {
			$this->item_id++;
		}
		  
		// get the product details
		if($product_id && $item_type<2) { 
			$db=&DB();
			$product=$db->Execute(sqlSelect($db,"product","*","id=$product_id"));
			if($product && $product->RecordCount()) {
				$taxable = $product->fields['taxable'];
				$this->product["$this->item_id"] = $product->fields;
			}
		
		// get the tld details
		} elseif($item_type==2) { 
			$db=&DB();
			$tld=$db->Execute(sqlSelect($db,"host_tld","*","name=::$domain_tld::"));	
			if($tld && $tld->RecordCount())
				$taxable = $tld->fields['taxable'];
		}
		
		// get the product pricing details if product
		$price_type=0;
		if($price_base===false && $price_setup===false && $product_id && $item_type<2) {						
			if($product && $product->RecordCount()) {
				$price_type=$product->fields['price_type'];
				$sku=$product->fields['sku'];
				include_once(PATH_MODULES.'product/product.inc.php');
				$productObj=new product;

				// get pricing for this product:
				$prod_price  = $productObj->price_prod($product->fields, $recurring_schedule, $this->account_id);
				$price_base  = $prod_price["base"];
				$price_setup = $prod_price["setup"];
				
				// get the recurring price (do NOT prorate!)
				$prod_price  = $productObj->price_prod($product->fields, $recurring_schedule, $this->account_id, false);
				$recur_price  = $prod_price["base"];
				 			
				// calculate any product attributes fees
	 			$attr_price  = $productObj->price_attr($product->fields, $product_attr, $recurring_schedule, $this->account_id);
				$price_base  += $attr_price["base"];
				$price_setup += $attr_price["setup"];

				// determine price type for checkout
				if ($product->fields["price_type"] == '0')
					$this->any_new=true;
				else if ($product->fields["price_type"] == '1')
					$this->any_recurring=true;
				else if ($product->fields["price_type"] == '2')
					$this->any_trial=true;	
							
			} else { 
				$this->any_new=true;
			}
		} else { 
			$this->any_new=true;
		}
		
		// get the TLD pricing details if domain
		if($price_base===false && $price_setup===false && $domain_tld && $domain_term && $domain_type) {
			include_once(PATH_MODULES.'host_tld/host_tld.inc.php');
			$tldObj = new host_tld; 
			$tldprice = $tldObj->price_tld_arr($domain_tld, $domain_type, false, false, false, $this->account_id);   
			if($domain_type == "park") {
				$price_base = $tldprice;
			} else {
				$price_base = $tldprice["$domain_term"];
				$this->tld_arr["$this->item_id"] = $tldprice;
			}
		}
		 	
		// set total amount for this line item before attributes, taxes, or discounts
		$price_base *= $quantity;
		$price_setup *= $quantity;
		$total_amt = ($price_setup + $price_base);
		
		// set the total recurring amount
		$recur_price *= $quantity;
		
		// format product attributes for storage
		$product_attr_cart=false; 
		if(($item_type==0 || $item_type>2) && is_array($product_attr)) $product_attr_cart = $this->get_product_attr_cart($product_attr); 
			 
		// recurring taxes and arrays
		if($recur_price>0 && $price_type==1) 
		{		
			// increment the total invoice recurring amount
			$this->recur_amt += $recur_price;
							
			// determine taxes for the recurring amount
			if($this->tax && $taxable && $recur_price>0 && $this->account_id) { 
				$recur_tax_arr = $taxObj->calculate($recur_price, $this->country_id, $this->state);				   
				if(is_array($recur_tax_arr)) foreach($recur_tax_arr as $tx) $this->recur_amt += $tx['rate'];
			}			
 
			// get the recurring arrays for price and invoice
			if($product && $product->RecordCount()) {
				$this->price_arr["$this->item_id"] = $productObj->price_recurr_arr($product->fields, $this->account_id);  
				$this->recur_arr[] = Array (
					'price' 		 => $recur_price,
					'recurr_schedule'=> $recurring_schedule,
					'recurr_type' 	 => $product->fields['price_recurr_type'],
					'recurr_weekday' => $product->fields['price_recurr_weekday'],
					'recurr_week' 	 => $product->fields['price_recurr_week']
				);		
			}	 								
		}
		 			
		// calculate any ad-hoc line item level (admin) discounts
		if($this->discount && $discount_manual>0) {
			$total_amt -= $discount_manual;
			$discount_amt += $discount_manual;
			$this->discount_amt += $discount_amt; 
			$discountObj->add_manual_discount($discount_manual,'MISC',$this->item_id);
		}
				
		// account level discounts
		if($this->discount && $this->account_id)  {		
			// calculate any database level discounts for this item (both account specific and session specific)
			$discount_amt = $discountObj->calc_all_discounts(0, $this->item_id, $product_id, $total_amt, $this->account_id, $this->total_amt+$total_amt);
			$total_amt -= $discount_amt; 
			$this->discount_amt += $discount_amt; 			
		}
				
		// add to total discount array
		if(is_array($discountObj->discount_arr)) {
			$this->discount_arr["$this->item_id"] = $discountObj->discount_arr;
		}
		
		// increment invoice total amount
		$this->total_amt += $total_amt;
 
		// calculate any taxes for current item 
		if($this->tax && $taxable && $total_amt>0 && $this->account_id) {  
			$tax_arr = $taxObj->calculate($total_amt, $this->country_id, $this->state);				   
			if(is_array($tax_arr)) {
				foreach($tax_arr as $tx) $tax_amt += $tx['rate']; 
				$this->item_tax["$this->item_id"] = $tax_arr;
				$this->tax_arr["$this->item_id"] = $tax_arr; 
			}
			$this->tax_amt += $tax_amt;
			$this->total_amt += $tax_amt;		
		}
		
		// store the fields to an array
		$this->invoice_item["$this->item_id"]=Array(
			'item_type'=>$item_type,
			'price_type'=>$price_type,
			'taxable'=>$taxable,
			'service_id'=>$service_id,
			'parent_id'=>$parent_id,
			'product_id'=>$product_id,
			'product_attr'=>$product_attr,
			'product_attr_cart'=>$product_attr_cart,
			'product_name'=>$product_name,
			'sku'=>$sku,
			'quantity'=>$quantity,
			'price_base'=>$price_base,
			'price_setup'=>$price_setup,
			'recurring_schedule'=>$recurring_schedule,
			'date_start'=>$date_start,
			'date_stop'=>$date_stop,
			'domain_name'=>$domain_name,
			'domain_tld'=>$domain_tld,
			'domain_term'=>$domain_term,
			'domain_type'=>$domain_type, 
			'total_amt'=>$total_amt,
			'tax_amt'=>$tax_amt,
			'discount_amt'=>$discount_amt
		);
	}
	
	/**
	 * Group all taxes to lump sums
	 */
	function group_taxes() { 
		if(is_array($this->tax_arr)) {
			foreach($this->tax_arr as $taxarr) foreach($taxarr as $taxes) $arr[$taxes["name"]]+=$taxes["rate"];  
			if(is_array($arr)) {
				foreach($arr as $a=>$b) $ret[] = Array('name'=>$a, 'rate'=>$b);	
				return $ret;		 
			} 
		} 
	}
	  
	/**
	 * Group all discounts to lump sums
	 */
	function group_discounts() { 
		if(is_array($this->discount_arr)) { 
			foreach($this->discount_arr as $discarr) foreach($discarr as $discounts) $arr[$discounts["discount"]]+=$discounts["amount"];  
			if(is_array($arr)) {
				foreach($arr as $a=>$b) $ret[] = Array('name'=>$a, 'total'=>$b);	
				return $ret;		 
			}
		} 
	}	
	
	/**
	 * Build a formatted product attribute list
	 *
	 * @param array $attributes
	 * @return string Formatted product attribute list
	 */
	function get_product_attr_cart($attributes) {
		# Set the attribute array:
		if(!empty($attributes) && is_array($attributes)) {
			$db=&DB();
			$product_attr = false; 
			foreach($attributes as $id=>$value) {
				if (!empty($value)) {
					if(is_numeric($id)) { 
						$attr = $db->Execute(sqlSelect($db,"product_attr","name","id=$id"));
						if ($attr && $attr->RecordCount()) $product_attr .= "{$attr->fields['name']}==".preg_replace("/\r\n/", "<br>", $value)."\r\n";
					} else {
						$product_attr .= "{$id}=={$value}\r\n";
					}
				}
			}
		}
		return $product_attr;
	}
	
	    			
	
	/** Custom Tracking
	*/
	function custom_tracking($VAR)
	{
		# Get the invoice id
		if(SESS_LOGGED == false)
		return false;

		# Check if we are in the iframe
		if(empty($VAR['_escape']) || empty($VAR['confirm']))
		{
			echo '<iframe id="custom_ecom_track" style="border:0px; width:0px; height:0px;"scrolling="auto" '.
			'frameborder="0" SRC="?_page=core:blank&_escape=1&confirm=1&do[]=invoice:custom_tracking&rand='.md5(microtime()).'"></iframe>';
			return;
		}

		# Get the un-tracked invoice details
		$db = &DB();
		$sql = "SELECT * FROM ".AGILE_DB_PREFIX."invoice WHERE
					(   custom_affiliate_status IS NULL OR
						custom_affiliate_status = 0 ) 
					AND billing_status = ".$db->qstr(1)." 
					AND site_id = ".$db->qstr(DEFAULT_SITE)." 
					AND account_id = ".$db->qstr(SESS_ACCOUNT); 
		$result = $db->Execute($sql);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('','', $db->ErrorMsg(). "\r\n\r\n". $sql);
			return false;
		}
		if($result->RecordCount() == 0) {
			echo 'none';
			return false;
		}

		# Get the totals
		$invoice = '';
		$total_amount = false;
		while(!$result->EOF)
		{
			if(!empty($invoice))
			$invoice .= '-';
			$invoice .= $result->fields['id'];
			$amt = $result->fields["total_amt"];
			$total_amount += $amt;
			$result->MoveNext();
		}

		# echo the custom tracking code to the screen:
		if(!is_file(PATH_FILES.'tracking.txt')) return false;
		$tracking = file_get_contents(PATH_FILES.'tracking.txt');
		$tracking = preg_replace('/%%amount%%/i', "$total_amount", $tracking);
		$tracking = preg_replace('/%%invoice%%/i', $invoice, $tracking);
		$tracking = preg_replace('/%%affiliate%%/i', SESS_AFFILIATE, $tracking);
		$tracking = preg_replace('/%%campaign%%/i', SESS_CAMPAIGN, $tracking);
		$tracking = preg_replace('/%%account%%/i', SESS_ACCOUNT, $tracking);
		echo $tracking;
 
		# Update the record so it is not tracked again
		$sql = "UPDATE ".AGILE_DB_PREFIX."invoice
					SET 
						custom_affiliate_status = ".$db->qstr('1')."
					WHERE 
						account_id = ".$db->qstr(SESS_ACCOUNT)." 
					AND
						billing_status = ".$db->qstr(1)." 
					AND
						site_id = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		if ($rs === false) {
			global $C_debug;
			$C_debug->error('','', $db->ErrorMsg(). "\r\n\r\n". $sql);
		}
		return true;
	}

 


	/** Performance:  (for the admin dashboard)
	*/
	function performance($VAR)
	{
		global $smarty, $C_list, $C_translate;

			
		# Get the period type, default to month
		if (empty($VAR['period']))
		$p = 'm';
		else
		$p = $VAR['period'];

		# Determine the correct period language:
		if($p=='' or $p == 'm') 
		{
			$pTrans = $C_translate->translate('thismonth','invoice','') . ' '.
					  $C_translate->translate('vs','invoice','') . ' ' .
					  $C_translate->translate('lastmonth','invoice','');
			$pFore = $C_translate->translate('thismonth','invoice','');
		} 
		elseif ($p == 'w') 
		{
			$pTrans = $C_translate->translate('thisweek','invoice','') . ' '.
					  $C_translate->translate('vs','invoice','') . ' ' .
					  $C_translate->translate('lastweek','invoice','');	
			$pFore = $C_translate->translate('thisweek','invoice','');	
		}
		elseif ($p == 'y') 
		{
			$pTrans = $C_translate->translate('thisyear','invoice','') . ' '.
					  $C_translate->translate('vs','invoice','') . ' ' .
					  $C_translate->translate('lastyear','invoice','');	
			$pFore = $C_translate->translate('thisyear','invoice','');
		}
		
		$smarty->assign('period_compare', $pTrans);
		$smarty->assign('period_forcast', $pFore); 
		
		 	
		
		# Get the period start & end
		switch ($p) {
			case 'w':
			$dow   = date('w');
			$this_start = mktime(0,0,0,date('m'),      date('d')-$dow,             date('y'));
			$this_end   = mktime(23,59,59,date('m'),   date('d'),                  date('y'));
			$last_start = mktime(0,0,0,date('m'),      date('d',  $this_start)-7,  date('y'));
			$last_end   = $this_start-1;
			break;

			case 'm':
			$this_start = mktime(0,0,0,date('m'), 1,                                date('y'));
			$this_end   = mktime(23,59,59,date('m'),   date('d'),                   date('y'));
			$last_start = mktime(0,0,0,                date('m', $this_start)-1, 1, date('y'));
			$last_end   = $this_start-1;
			break;

			case 'y':
			$this_start = mktime(0,0,0,1,1,                            date('y', time()));
			$this_end   = mktime(23,59,59,     date('m'),  date('d'),  date('y'));
			$last_start = mktime(0,0,0,1,1,                            date('y', $this_start)-1);
			$last_end   = $this_start-1;
			break;
		}

		##############################
		# Get sales for this period
		##############################
		$db     = &DB();
		$this_amt = 0;
		$sql    = 'SELECT total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                       date_orig    >=  ' . $db->qstr( $this_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $this_end ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		while( !$rs->EOF ) {
			$this_amt += $rs->fields['total_amt'];
			$rs->MoveNext();
		}
		$smarty->assign('sales_current', $this_amt);

		###############################
		# Get sales for last period
		###############################
		$last_amt = 0;
		$sql    = 'SELECT total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                       date_orig    >=  ' . $db->qstr( $last_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $last_end ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		while( !$rs->EOF ) {
			$last_amt += $rs->fields['total_amt'];
			$rs->MoveNext();
		}
		$smarty->assign('sales_previous', $last_amt);

		###############################
		# Get sales change percentage
		###############################
		if($last_amt > 0)
		$sales_change = $this_amt/$last_amt *100 -100;
		else
		$sales_change = 0;

		if($sales_change == 0)
		$sales_change = '';
		elseif($sales_change < 0)
		$sales_change = '<font color="#990000">' . number_format($sales_change, 1). '%</font>';
		else
		$sales_change = '+'.number_format($sales_change, 1). '%';


		$smarty->assign('sales_change', $sales_change);

		#################################
		# Get forcast for current period
		#################################
		switch ($p) {
			case 'w':
			$dow = date('w')+1;
			$forcast_daily = $this_amt/$dow ;
			$forcast_l_daily = $last_amt / 7;
			@$forcast_change  = $forcast_daily / $forcast_1_daily *100 -100;
			$forcast_current = $forcast_daily * 7;
			break;

			case 'm':
			$forcast_daily = $this_amt / date('d');
			$forcast_1_daily = $last_amt / date('t', mktime(0,0,0,date('m')-1, 1, date('y')));
			@$forcast_change  = $forcast_daily / $forcast_1_daily *100 -100;
			$forcast_current = $forcast_daily * date('t');
			break;

			case 'y':
			$forcast_daily = $this_amt / date('z');
			$forcast_1_daily = $last_amt / 356;
			@$forcast_change  = $forcast_daily / $forcast_1_daily *100 -100;
			$forcast_current = $forcast_daily * 365;
			break;
		}

		$smarty->assign('forcast_current', $forcast_current);

		###############################
		# Get forcast change percentage
		###############################
		if($last_amt > 0  )
		@$forcast_change = $forcast_daily/$forcast_1_daily *100;
		else
		$forcast_change = 0;


		if($forcast_change == 0)
		$forcast_change = '-';
		elseif($forcast_change < 0)
		$forcast_change = '<font color="#990000">' . number_format($forcast_change, 1). '%</font>';
		else
		$forcast_change = '+'.number_format($forcast_change, 1). '%';


		$smarty->assign('forcast_change', $forcast_change);


		####################################################
		# Get Quota for Today to meet Forcasted sales:
		####################################################
		$smarty->assign('quota_current',  $forcast_daily);

		##############################
		# Get AR credits for this period
		##############################
		$this_billed_amt = 0;
		$sql    = 'SELECT billed_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                       date_orig    >=  ' . $db->qstr( $this_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $this_end ) . ' AND
                       billed_amt   >  ' . $db->qstr( 0 ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		while( !$rs->EOF ) {
			$this_billed_amt += $rs->fields['billed_amt'];
			$rs->MoveNext();
		}
		$smarty->assign('ar_credits_current', $this_billed_amt);

		###############################
		# Get AR credits for last period
		###############################
		$last_billed_amt = 0;
		$sql    = 'SELECT billed_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                       date_orig    >=  ' . $db->qstr( $last_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $last_end ) . ' AND
                       billed_amt   >  ' . $db->qstr( 0 ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		while( !$rs->EOF ) {
			$last_billed_amt += $rs->fields['billed_amt'];
			$rs->MoveNext();
		}
		$smarty->assign('ar_credits_previous', $last_billed_amt);

		###############################
		# Get AR Credits change percentage
		###############################
		if($last_billed_amt > 0)
		$ar_change = $this_billed_amt/$last_billed_amt *100 -100;
		else
		$ar_change = 0;

		if($ar_change == 0)
		$ar_change = '-';
		elseif($ar_change < 0)
		$ar_change = '<font color="#990000">' . number_format($ar_change, 1). '%</font>';
		else
		$ar_change = '+'.number_format($ar_change, 1). '%';

		$smarty->assign('ar_credit_change', $ar_change);

		##########################################
		# Get AR Balance
		##########################################
		$this_ar_balance = $this_billed_amt - $this_amt;
		$last_ar_balance = $last_billed_amt - $last_amt;

		$smarty->assign('ar_balance_current', $this_ar_balance);
		$smarty->assign('ar_balance_last',    $last_ar_balance);

		#########################################
		# Get Users  (current)
		#########################################
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'account WHERE
                       date_orig    >=  ' . $db->qstr( $this_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $this_end ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		$users_current = $rs->RecordCount();
		$smarty->assign('users_current', $users_current);

		#########################################
		# Get Users  (previous)
		#########################################
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'account WHERE
                       date_orig    >=  ' . $db->qstr( $last_start ) . ' AND
                       date_orig    <=  ' . $db->qstr( $last_end ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		$users_previous = $rs->RecordCount();
		$smarty->assign('users_previous', $users_previous);

		###############################
		# Get users change percentage
		###############################
		if($users_previous > 0)
		@$users_change = $users_current/$users_current *100 -100;
		else
		$users_change = 0;

		if($users_change == 0)
		$users_change = '-';
		elseif($users_change < 0)
		$users_change = '<font color="#990000">' . number_format($users_change, 1). '%</font>';
		else
		$users_change = '+'.number_format($users_change, 1). '%';

		$smarty->assign('users_change', $users_change);

		# Get Tickets
		if( $C_list->is_installed('ticket') )
		{
			$smarty->assign('show_tickets', true);

			#########################################
			# Get Tickets  (current)
			#########################################
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
                           date_orig    >=  ' . $db->qstr( $this_start ) . ' AND
                           date_orig    <=  ' . $db->qstr( $this_end ) . ' AND
                           site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			$tickets_current = $rs->RecordCount();
			$smarty->assign('tickets_current', $tickets_current);

			#########################################
			# Get Tickets  (previous)
			#########################################
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'ticket WHERE
                           date_orig    >=  ' . $db->qstr( $last_start ) . ' AND
                           date_orig    <=  ' . $db->qstr( $last_end ) . ' AND
                           site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			$tickets_previous = $rs->RecordCount();
			$smarty->assign('tickets_previous', $tickets_previous);

			###############################
			# Get Tickets change percentage
			###############################
			if($tickets_previous > 0)
			@$tickets_change = $tickets_current/$tickets_current *100 -100;
			else
			$tickets_change = 0;

			if($tickets_change == 0)
			$tickets_change = '-';
			elseif($tickets_change < 0)
			$tickets_change = '<font color="#990000">' . number_format($tickets_change, 1). '%</font>';
			else
			$tickets_change = '+'.number_format($tickets_change, 1). '%';

			$smarty->assign('tickets_change', $tickets_change);
		}

		# Get Affiliate stats
		if( $C_list->is_installed('affiliate') )
		{
			$smarty->assign('show_affiliates', true);

			###########################################
			# Get affiliate sales for this period
			###########################################
			$this_amt = 0;
			$sql    = 'SELECT total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                           date_orig    >=  ' . $db->qstr( $this_start ) . ' AND
                           date_orig    <=  ' . $db->qstr( $this_end ) . ' AND
                           affiliate_id !=  ' . $db->qstr( 0 ) . ' AND
                           affiliate_id !=  ' . $db->qstr( '' ) . ' AND
                           site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			while( !$rs->EOF ) {
				$this_amt += $rs->fields['total_amt'];
				$rs->MoveNext();
			}
			$smarty->assign('affiliate_sales_current', $this_amt);

			##########################################
			# Get affiliate sales for last period
			##########################################
			$last_amt = 0;
			$sql    = 'SELECT total_amt FROM ' . AGILE_DB_PREFIX . 'invoice WHERE
                           date_orig    >=  ' . $db->qstr( $last_start ) . ' AND
                           date_orig    <=  ' . $db->qstr( $last_end ) . ' AND
                           affiliate_id !=  ' . $db->qstr( 0 ) . ' AND
                           affiliate_id !=  ' . $db->qstr( '' ) . ' AND
                           site_id      =  ' . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			while( !$rs->EOF ) {
				$last_amt += $rs->fields['total_amt'];
				$rs->MoveNext();
			}
			$smarty->assign('affiliate_sales_previous', $last_amt);

			###########################################
			# Get affiliate sales change percentage
			###########################################
			if($last_amt > 0)
			$sales_change = $this_amt/$last_amt *100 -100;
			else
			$sales_change = 0;

			if($sales_change == 0)
			$sales_change = '-';
			elseif($sales_change < 0)
			$sales_change = '<font color="#990000">' . number_format($sales_change, 1). '%</font>';
			else
			$sales_change = '+'.number_format($sales_change, 1). '%';

			$smarty->assign('affiliate_sales_change', $sales_change);
		}
		 
		
		/** Get VoIP Performance Data
		*/ 
		if( $C_list->is_installed('voip') )
		{		
			 
			# Avg. Call Duration for this period
			$rs = $db->Execute(sqlSelect($db, "voip_cdr", "avg(ceiling(billsec/60))", "disposition='ANSWERED' AND date_orig >= $this_start AND date_orig <= $this_end"));
			if(empty($rs->fields[0])) $acd=0; else $acd = $rs->fields[0]; 
			$smarty->assign('acd',$acd);		
			
			# Avg. Call Duration for last period
			$rs = $db->Execute(sqlSelect($db, "voip_cdr", "avg(ceiling(billsec/60))", "disposition='ANSWERED' AND date_orig >= $last_start AND date_orig <= $last_end"));
			if(empty($rs->fields[0])) $acd_last=0; else $acd_last = $rs->fields[0]; 
			$smarty->assign('acd_last',$acd_last);
			
			# Get Avg. Call Duration change Percentage
			if($acd > 0) $acd_change = $acd/$acd_last*100-100; else $acd_change = 0;
			if($acd_change == 0) 
				$acd_change = '-';
			elseif ($acd_change < 0)
				$acd_change = '<font color="#990000">' . number_format($acd_change, 1). '%</font>';
			else 
				$acd_change  = '+'.number_format($acd_change, 1). '%';
			$smarty->assign('acd_change', $acd_change);
			
			
			# Avg. Successful Rate for this period
			$rs = $db->Execute(sqlSelect($db, "voip_cdr", "count(*)", "disposition='ANSWERED' AND date_orig >= $this_start AND date_orig <= $this_end"));
			$rs1 = $db->Execute(sqlSelect($db, "voip_cdr", "count(*)", "date_orig >= $this_start AND date_orig <= $this_end"));
			if ($rs->fields[0])
				$asr = number_format(($rs->fields[0] / $rs1->fields[0]) * 100,3)." %";
			else
				$asr = "-";
			$smarty->assign('asr', $asr); 
			
			# Number of CDRs for this period
			$cdrs = $rs1->fields[0];
			$smarty->assign('cdrs', number_format($cdrs,0));		
			 
			# Avg. Successful Rate for last period
			$rs = $db->Execute(sqlSelect($db, "voip_cdr", "count(*)", "disposition='ANSWERED' AND date_orig >= $last_start AND date_orig <= $last_end"));
			$rs1 = $db->Execute(sqlSelect($db, "voip_cdr", "count(*)", "date_orig >= $last_start AND date_orig <= $last_end"));
			if ($rs->fields[0])
				$asr_last = number_format(($rs->fields[0] / $rs1->fields[0]) * 100,3)." %";
			else
				$asr_last = "-";
			$smarty->assign('asr_last', $asr_last); 
			
			# Number of CDRS for last period
			$cdrs_last = $rs1->fields[0];
			$smarty->assign('cdrs_last', number_format($cdrs_last,0));			
			 
			# Get Avg. Successful Rate change Percentage
			if($asr > 0) $asr_change = $asr/$asr_last*100-100; else $asr_change = 0;
			if($asr_change == 0) 
				$asr_change = '-';
			elseif ($asr_change < 0)
				$asr_change = '<font color="#990000">' . number_format($asr_change, 1). '%</font>';
			else 
				$asr_change  = '+'.number_format($asr_change, 1). '%';
			$smarty->assign('asr_change', $asr_change);
	
				
			# Get Number of CDRs change Percentage
			if($cdrs > 0) $cdrs_change = $cdrs/$cdrs_last*100-100; else $cdrs_change = 0;
			if($cdrs_change == 0) 
				$cdrs_change = '-';
			elseif ($cdrs_change < 0)
				$cdrs_change = '<font color="#990000">' . number_format($cdrs_change, 1). '%</font>';
			else 
				$cdrs_change  = '+'.number_format($cdrs_change, 1). '%';
			$smarty->assign('cdrs_change', $cdrs_change);
		}
					
		
		# Generate the Calendar Overview
		include_once(PATH_MODULES.'core/calendar.inc.php');
		$calendar = new calendar;		 
		$start = $calendar->start;
		$end   = $calendar->end; 
		
		global $C_list;
		$C_list->currency(DEFAULT_CURRENCY);
		$currency_symbol=$C_list->format_currency[DEFAULT_CURRENCY]['symbol'];
 		
		# Get the paid/due invoice statistics
		$rs = $db->Execute($sql=sqlSelect($db,"invoice","date_orig,total_amt,billing_status,refund_status,billed_amt,suspend_billing","date_orig >= $start and date_orig <= $end"));
		if($rs && $rs->RecordCount()) { 
			while(!$rs->EOF) {
				$day = date("j", $rs->fields['date_orig']);
				
				if($rs->fields['billed_amt'] > 0 && ($rs->fields['billing_status'] == 1 || $rs->fields['refund_status'] != 1)) {					
					$paid[$day] += $rs->fields['billed_amt'];
				}
				if ($rs->fields['billing_status'] != 1 && $rs->fields['refund_status'] != 1 ) {
					$amt = $rs->fields['total_amt'] - $rs->fields['billed_amt'];
					$due[$day] += $amt;
				}				
				
				$rs->MoveNext();
			} 
			
			if(is_array($paid))
				foreach($paid as $day=>$item)  
					$calendar->add("<b>Paid</b> - {$currency_symbol}". number_format($item,2), $day, 'green', 'green');
			
			if(is_array($due))
				foreach($due as $day=>$item) 
					$calendar->add("<b>Due</b> - {$currency_symbol}". number_format($item,2), $day,'red','red');			
		}
	 
		# Get the upcoming due services		
		$rs = $db->Execute($sql=sqlSelect($db,"service","date_next_invoice,price","price > 0 AND date_next_invoice >= $start AND date_next_invoice <= $end AND suspend_billing <> 1"));
		if($rs && $rs->RecordCount()) { 
			while(!$rs->EOF) {
				$day = date("j", $rs->fields['date_next_invoice']); 
				$sdue[$day] += $rs->fields['price']; 	
				$rs->MoveNext();
			} 
			foreach($sdue as $day=>$item) {
				$calendar->add("<b>Recurring</b> - {$currency_symbol}". number_format($item,2), $day, 'grey', 'grey');
			}			
		}  
		
		$calout=  $calendar->generate();
		$smarty->assign("calendar", $calout);
				
		return;
	}

	/** AUTO APPROVE INVOICE
	*/
	function autoApproveInvoice($invoice_id)
	{
		$do = false;

		# Get the invoice details:
		$db = &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice WHERE
	        		id = ".$db->qstr($invoice_id)." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$invoice = $db->Execute($q);
		if ($invoice === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','autoApproveInvoice', $db->ErrorMsg());
			return false;
		}

		# Get the checkout details:
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE
	        		id = ".$db->qstr($invoice->fields['checkout_plugin_id'])." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$checkout = $db->Execute($q);
		if ($checkout === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','autoApproveInvoice', $db->ErrorMsg());
			return false;
		}

		# Get the account details:
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."account WHERE
	        		id = ".$db->qstr($invoice->fields['account_id'])." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($q);
		if ($account === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','autoApproveInvoice', $db->ErrorMsg());
			return false;
		}

		# is this a recurring invoices, and is manual approvale req?
		if ( $invoice->fields['type'] == 1 && $checkout->fields['manual_approval_recur'] != 1) {
			$do = true;
		}

		# Manual approval required for all?
		if( $invoice->fields['type'] != 1 && $checkout->fields['manual_approval_all'] != 1) {
			$do = true;
		}

		if(!$do) {

			# manual approval req. for invoice amount?
			if(!empty($checkout->fields['manual_approval_amount']) && $do == true)
			if($checkout->fields['manual_approval_amount'] <= $invoice->fields['total_amt'])
			$do = false;

			# manual approval req. for user's country?
			if(!empty($checkout->fields['manual_approval_country']) && $do == true) {
				$arr = unserialize($checkout->fields['manual_approval_country']);
				for($i=0; $i<count($arr); $i++) {
					if($account->fields['country_id'] == $arr[$i])
					$do = false;
				}
			}

			# manual approval req. for user's currency?
			if(!empty($checkout->fields['manual_approval_currency']) && $do == true) {
				$arr = unserialize($checkout->fields['manual_approval_currency']);
				for($i=0; $i<count($arr); $i++) {
					if($invoice->fields['actual_billed_currency_id'] == $arr[$i])
					$do = false;
				}
			}

			# manual approval req. for user's group(s)?
			if(!empty($checkout->fields['manual_approval_group']) && $do == true) {
				# Get the group details:
				$q = "SELECT group_id FROM ".AGILE_DB_PREFIX."account_group WHERE
    		        		account_id = ".$db->qstr($invoice->fields['account_id'])." AND
    		        		active	   = ".$db->qstr('1')." AND
    		        		site_id = ".$db->qstr(DEFAULT_SITE);
				$groups = $db->Execute($q);
				if ($groups === false) {
					global $C_debug;
					$C_debug->error('invoice.inc.php','autoApproveInvoice', $db->ErrorMsg());
				}

				$arr = unserialize($checkout->fields['manual_approval_group']);
				while(!$groups->EOF) {
					for($i=0; $i<count($arr); $i++) {
						$idx = $groups->fields["group_id"];
						if($idx == $arr[$i])
						$do = false;
					}
					$groups->MoveNext();
				}
			}
		}

		if ($do)
		{
			# Approve the invoice
			$arr['id'] = $invoice_id;
			$this->approveInvoice($arr, $this);
		}
		else
		{
			# Admin manual approval notice
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('invoice_manual_auth_admin',  $invoice->fields['account_id'], $invoice->fields['id'], $invoice->fields['checkout_plugin_id'], '');
		}
	}


	/*	APPROVE INVOICE
	*/
	function approveInvoice($VAR)
	{
		# Get the invoice details:
		$db = &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice WHERE
	        		id = ".$db->qstr($VAR['id'])." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$invoice = $db->Execute($q);
		if ($invoice === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
			return false;
		}

		# Validate invoice exists & needs approval:
		if($invoice->fields['id'] != $VAR['id'] || $invoice->fields['process_status'] == '1') return false;

		# Update the invoice approval status:
		$q = "UPDATE ".AGILE_DB_PREFIX."invoice SET
	        		date_last 		= ".$db->qstr(time()).",
	        		process_status 	= ".$db->qstr('1')." WHERE
	        		id		 		= ".$db->qstr($VAR['id'])." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
			return false;
		}

		# Send approval notice to user:
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$mail = new email_template;
		$mail->send('invoice_approved_user',  $invoice->fields['account_id'], $VAR['id'], '', '');

		# Include the service class
		include_once(PATH_MODULES.'service/service.inc.php');
		$srvc = new service;

		# Determine if services have already been created for this invoice:
		if($invoice->fields['type'] != 1 )
		{
			$q = "SELECT id FROM ".AGILE_DB_PREFIX."service WHERE
    	        		invoice_id = ".$db->qstr($VAR['id'])." AND
    	        		site_id = ".$db->qstr(DEFAULT_SITE);
			$service = $db->Execute($q);
			if ($service === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
				return false;
			}
			if ($service->RecordCount() > 0)
			{
				# Update services to approved status:
				while(!$service->EOF)
				{
					$srvc->approveService($service->fields['id']);
					$service->MoveNext();
				}
				return true;
			}

			# Get the parent items in this invoice :
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice_item WHERE
    	        	  ( parent_id = 0 OR parent_id IS NULL OR parent_id = '') AND
    	        	  invoice_id =  ".$db->qstr($VAR['id'])." AND
    	        	  site_id 	 =  ".$db->qstr(DEFAULT_SITE);
			$ii = $db->Execute($q);
			if ($ii === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
				return false;
			}
			while(!$ii->EOF)
			{
				if(empty($ii->fields['service_id']))
				{
					# Add the service
					$srvc->invoiceItemToService($ii->fields['id'], $invoice);

					# Check for any children items in this invoice:
					$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice_item WHERE
	    		        	  parent_id  =  ".$db->qstr($ii->fields['id'])." AND
	    		        	  invoice_id =  ".$db->qstr($VAR['id'])." AND
	    		        	  site_id 	 =  ".$db->qstr(DEFAULT_SITE);
					$iii = $db->Execute($q);
					if ($iii === false) {
						global $C_debug;
						$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
						return false;
					}
					while(!$iii->EOF)
					{
						# Add the service
						$srvc->invoiceItemToService($ii->fields['id'], $invoice);
						$iii->MoveNext();
					}
				}
				else
				{
					$srvc = new service;
					if($ii->fields['item_type'] == 2 && $ii->fields['domain_type'] == 'renew' ) {
						# this is a domain renewal
						$srvc->renewDomain(  $ii, $invoice->fields['account_billing_id'] );
					} else {
						# this is an upgrade for an existing service
						$srvc->modifyService( $ii, $invoice->fields['account_billing_id'] );
					}
				}
				$ii->MoveNext();
			}
		}
		elseif ($invoice->fields['type'] == 1 )
		{
			# recurring invoice, just update assoc services
			# Loop through invoice items & approve assoc services
			$q = "SELECT service_id FROM ".AGILE_DB_PREFIX."invoice_item WHERE
                        invoice_id = ".$db->qstr($VAR['id'])." AND
                        site_id = ".$db->qstr(DEFAULT_SITE);
			$service = $db->Execute($q);
			if ($service === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','voidInvoice', $db->ErrorMsg());
				return false;
			}
			if ($service->RecordCount() > 0)
			{
				# Include the service class
				include_once(PATH_MODULES.'service/service.inc.php');
				$srvc = new service;

				# Update services to inactive status:
				while(!$service->EOF) {
					$srvc->approveService($service->fields['service_id']);
					$service->MoveNext();
				}
			}
		}

		# get account id
		if(defined("SESS_ACCOUNT"))
		$account_id = SESS_ACCOUNT;
		else
		$account_id = 0;

		# if approved, create a memo
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."invoice_memo
		    		SET
		    		id 		= ".$db->qstr($id).",
		    		site_id 	= ".$db->qstr(DEFAULT_SITE).",
		    		date_orig 	= ".$db->qstr(time()).",
		    		invoice_id	= ".$db->qstr($VAR['id']).",
		    		account_id	= ".$db->qstr($account_id).",
		    		type		= ".$db->qstr('approval').",
		    		memo		= ".$db->qstr('NA');
		$memo = $db->Execute($q);
		if ($memo === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','approveInvoice', $db->ErrorMsg());
			return false;
		}
		return true;
	}


	/** VOID INVOICE
	*/
	function voidInvoice($VAR)
	{
		# Update the invoice approval status:
		$db = &DB();
		$q = "UPDATE ".AGILE_DB_PREFIX."invoice SET
	        		date_last 		= ".$db->qstr(time()).",
	        		process_status 	= ".$db->qstr('0')." WHERE
	        		id		 		= ".$db->qstr($VAR['id'])." AND
	        		site_id 		= ".$db->qstr(DEFAULT_SITE);
		$update = $db->Execute($q);
		if ($update === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','voidInvoice', $db->ErrorMsg());
			return false;
		}

		# Determine if services have already been created for this invoice and deactivate:
		$q = "SELECT id FROM ".AGILE_DB_PREFIX."service WHERE
	        		invoice_id = ".$db->qstr($VAR['id'])." AND
	        		site_id = ".$db->qstr(DEFAULT_SITE);
		$service = $db->Execute($q);
		if ($service === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','voidInvoice', $db->ErrorMsg());
			return false;
		}
		if ($service->RecordCount() > 0)
		{
			# Include the service class
			include_once(PATH_MODULES.'service/service.inc.php');
			$srvc = new service;

			# Update services to inactive status:
			while(!$service->EOF) {
				$srvc->voidService($service->fields['id']);
				$service->MoveNext();
			}
		}

		# Loop through invoice items & delete assoc services
		$q = "SELECT service_id FROM ".AGILE_DB_PREFIX."invoice_item WHERE
                    invoice_id = ".$db->qstr($VAR['id'])." AND
                    site_id = ".$db->qstr(DEFAULT_SITE);
		$service = $db->Execute($q);
		if ($service === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','voidInvoice', $db->ErrorMsg());
			return false;
		}
		if ($service->RecordCount() > 0)
		{
			# Include the service class
			include_once(PATH_MODULES.'service/service.inc.php');
			$srvc = new service;

			# Update services to inactive status:
			while(!$service->EOF) {
				$srvc->voidService($service->fields['service_id']);
				$service->MoveNext();
			}
		} 

		# if voided, create a memo
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."invoice_memo
		    		SET
		    		id 			= ".$db->qstr($id).",
		    		site_id 	= ".$db->qstr(DEFAULT_SITE).",
		    		date_orig 	= ".$db->qstr(time()).",
		    		invoice_id	= ".$db->qstr($VAR['id']).",
		    		account_id	= ".$db->qstr(SESS_ACCOUNT).",
		    		type		= ".$db->qstr('void').",
		    		memo		= ".$db->qstr('NA');
		$insert = $db->Execute($q);
		if ($insert === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','voidInvoice', $db->ErrorMsg());
			return false;
		}
		return true;
	}


	/** RECONCILE INVOICE
	*/
	function reconcile($VAR)
	{
		global $C_translate, $C_debug;

		# validate amt
		if( $VAR['amount'] <= 0) {
			$C_debug->alert( "Payment amount to low!" );
			return false;
		}

		# get the invoice details
		$db     = &DB();
		$sql    = 'SELECT * FROM   ' . AGILE_DB_PREFIX . 'invoice WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		if ($rs === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','reconcileInvoice', $db->ErrorMsg());
			return false;
		}

		if(@$rs->RecordCount() == 0) return false;

		$amt                        = $VAR['amount'];
		$total_amt                  = $rs->fields['total_amt'];
		$billed_amt                 = $rs->fields['billed_amt'];
		$billed_currency_id         = $rs->fields['billed_currency_id'];
		$actual_billed_amt          = $rs->fields['actual_billed_amt'];
		$actual_billed_currency_id  = $rs->fields['actual_billed_currency_id'];
		$due                        = $total_amt - $billed_amt;

		$overpaid = false;
		if($amt > $due)
		{
			$billed = 1;
			$update   = $total_amt;

			$overpaid = $amt - $due;

			$C_translate->value['invoice']['amt'] = number_format($overpaid, 2);
			$alert = $C_translate->translate('rec_over','invoice','');
		}
		elseif ($amt == $due)
		{
			$billed = 1;
			$update = $total_amt;
		}
		else
		{
			$billed = 0;
			$update = $amt + $billed_amt;
		}

		# Update the invoice record
		$sql    = 'UPDATE  ' . AGILE_DB_PREFIX . 'invoice
                       SET
                       billed_amt   =  ' . $db->qstr( $update ) . ',
                       billing_status =  ' . $db->qstr( $billed ) . '
                       WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$db->Execute($sql);
	  
		# Create a memo
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."invoice_memo
                    SET
                    id          = ".$db->qstr($id).",
                    site_id     = ".$db->qstr(DEFAULT_SITE).",
                    date_orig   = ".$db->qstr(time()).",
                    invoice_id  = ".$db->qstr($VAR['id']).",
                    account_id  = ".$db->qstr(SESS_ACCOUNT).",
                    type        = ".$db->qstr('reconcile').",
                    memo        = ".$db->qstr('+ '.number_format($VAR['amount'],2) . " \r\n" . @$VAR['memo']);
		$db->Execute($q);
	 
		
		# Reciept printing
		include_once PATH_MODULES.'invoice/receipt_print.php';
		$receipt = new receipt_print;
		$receipt->add($rs, number_format($VAR['amount'],2), number_format($update,2));
		

		# Auto update if billed complete
		if($billed)
		{
			$this->autoApproveInvoice($VAR['id']);

			# Get the default currency ISO
			$q = "SELECT ".AGILE_DB_PREFIX."invoice_memo
	                    SET
	                    id          = ".$db->qstr($id).",
	                    site_id     = ".$db->qstr(DEFAULT_SITE).",
	                    date_orig   = ".$db->qstr(time()).",
	                    invoice_id  = ".$db->qstr($VAR['id']).",
	                    account_id  = ".$db->qstr(SESS_ACCOUNT).",
	                    type        = ".$db->qstr('reconcile').",
	                    memo        = ".$db->qstr('+ '.number_format($VAR['amount'],2) . " \r\n" . @$VAR['memo']);
			$currency = $db->Execute($q);

			# User invoice creation confirmation
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$email = new email_template;
			$email->send('invoice_paid_user', $rs->fields['account_id'], $VAR['id'], $rs->fields['billed_currency_id'], '');

			# Admin alert of payment processed
			$email = new email_template;
			$email->send('admin->invoice_paid_admin', $rs->fields['account_id'], $VAR['id'], $rs->fields['billed_currency_id'], '');
		}

		# Redirect
		if(!empty($VAR['redirect']))
		{
			echo '
                <script language="JavaScript">
                    window.parent.location=\''.$VAR['redirect'].'\';  ';
			if(!empty($alert))
			echo 'alert(\''.$alert.'\');';
			echo '</script>';
			exit;
		}

		$msg = $C_translate->translate('ref_comp','invoice','');
		$C_debug->alert( $msg );
		return;
	}


	/** REFUND INVOICE 
	*/
	function refund($VAR)
	{
		global $C_translate, $C_debug;

		# validate amt
		if( $VAR['amount'] <= 0) {
			$C_debug->alert( "Refund amount to low!" );
			return false;
		}

		# get the invoice details
		$db     = &DB();
		$sql    = 'SELECT * FROM   ' . AGILE_DB_PREFIX . 'invoice WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		if(@$rs->RecordCount() == 0) return false;

		$amt                        = $VAR['amount'];
		$total_amt                  = $rs->fields['total_amt'];
		$billed_amt                 = $rs->fields['billed_amt'];
		$billed_currency_id         = $rs->fields['billed_currency_id'];
		$actual_billed_amt          = $rs->fields['actual_billed_amt'];
		$actual_billed_currency_id  = $rs->fields['actual_billed_currency_id'];
		$update                     = $billed_amt - $amt;

		if($update>0) $billing_status=1; else $billing_status=0;
		
		# Update the invoice record
		echo $sql    = 'UPDATE  ' . AGILE_DB_PREFIX . 'invoice
                       SET
                       billed_amt   =  ' . $db->qstr( $update ) . ',
                       billing_status = '.$billing_status.', 
                       suspend_billing = 1,
                       refund_status = 1                   
                       WHERE
                       id           =  ' . $db->qstr( $VAR['id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
		$update2 = $db->Execute($sql);
		if ($update2 === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','refundInvoice', $db->ErrorMsg());
			return false;
		}

		# Create a memo
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_memo_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."invoice_memo
                    SET
                    id          = ".$db->qstr($id).",
                    site_id     = ".$db->qstr(DEFAULT_SITE).",
                    date_orig   = ".$db->qstr(time()).",
                    invoice_id  = ".$db->qstr($VAR['id']).",
                    account_id  = ".$db->qstr(SESS_ACCOUNT).",
                    type        = ".$db->qstr('refund').",
                    memo        = ".$db->qstr('- '.number_format($VAR['amount'],2) . " \r\n" . @$VAR['memo']);
		$insert = $db->Execute($q);
		if ($insert === false) {
			global $C_debug;
			$C_debug->error('invoice.inc.php','refundInvoice', $db->ErrorMsg());
			return false;
		}

		# Void:
		$this->voidInvoice($VAR, $this);
		
		# Call into the checkout plugin and attempt realtime refund 
		$billing = $db->Execute($sql=sqlSelect($db, array('account_billing','checkout'), 'A.*,B.checkout_plugin',
					"A.id = ::{$rs->fields['account_billing_id']}:: AND A.checkout_plugin_id=B.id"));
		if($billing && $billing->RecordCount() && !empty($billing->fields['checkout_plugin'])) {
			$plugin_file = PATH_PLUGINS.'checkout/'. $billing->fields['checkout_plugin'] .'.php';
			if(is_file($plugin_file)) {
				include_once ( $plugin_file );
				eval( '$PLG = new plg_chout_' . $billing->fields['checkout_plugin'] . '("'.$billing->fields['checkout_plugin_id'].'");');
				if(is_callable(Array($PLG,"refund"))) $PLG->refund($rs->fields, $billing->fields, $amt);
			}
		}

		# Redirect
		if(!empty($VAR['redirect'])) {
			echo ' <script language="JavaScript">  window.parent.location=\''.$VAR['redirect'].'\'; </script>';
			return;
		}
		 
		$msg = $C_translate->translate('ref_comp','invoice','');
		$C_debug->alert( $msg );
		return;
	}

	# Get translated/hardcoded line item description for PDF invoice
	function getLineItemDesc($sku,$id,$domain=false,$item_name) 
	{
		if(!empty($item_name)) return $item_name;
		global $C_translate;
		if(!empty($sku) && $sku == 'DOMAIN-PARK' || $sku == 'DOMAIN-TRANSFER' || $sku == 'DOMAIN-REGISTER' || $sku == 'DOMAIN-RENEW') {			 
			if($sku == 'DOMAIN-REGISTER') $name=$C_translate->translate('register','cart',''); 
			elseif ($sku == 'DOMAIN-TRANSFER') $name=$C_translate->translate('transfer','cart',''); 
			elseif ($sku == 'DOMAIN-PARK') $name=$C_translate->translate('park','cart','');
			elseif ($sku == 'DOMAIN-RENEW') $name=$C_translate->translate('renew','cart','');
			if($domain) return "$domain \r\n ( $name )";
			else return $name;			
		} else { 
			include_once(PATH_CORE.'list.inc.php');
			$C_list=new CORE_list;
			if(empty($this->product_desc["$id"])) {
				$desc = $C_list->translate("product_translate", "name", "product_id", $id, "translate_product");
				$this->product_desc["$id"] = $desc['name'];
			}
			if(!empty($this->product_desc["$id"]))
			return $this->product_desc["$id"];
			else 
			return $sku;
		} 
		return $sku;
	}

	/**
	* Task based function to e-mail or store printable PDF of all unprinted invoices
	*/
	function delivery_task() 
	{		
		# get all unprinted invoices
		$db=&DB();
		$invcfg = $db->Execute(sqlSelect($db,"setup_invoice","*","id=::".DEFAULT_SITE."::"));				
		$rs = $db->SelectLimit($sql=sqlSelect($db,Array("invoice","account"),
			"A.id,B.email,B.first_name,B.last_name,B.invoice_delivery,B.invoice_show_itemized",
			"(A.billing_status=0 OR A.billing_status IS NULL) AND (A.print_status=0 OR A.print_status=NULL) and A.account_id=B.id and (B.invoice_delivery is not null AND B.invoice_delivery>0)"),100);		 
		if($rs && $rs->RecordCount()) 
		{ 
			//define('FPDF_FONTPATH', PATH_INCLUDES.'pdf/font/');
			require_once(PATH_INCLUDES.'pdf/fpdi.php');
			require_once(PATH_INCLUDES.'pdf/fpdf_tpl.php');
			require_once(PATH_INCLUDES.'pdf/fpdf.php');
			require_once(PATH_INCLUDES.'pdf/pdf_invoice_'.$invcfg->fields['invoice_pdf_plugin'].'.inc.php');
			
			// Send the e-mail....
			require_once(PATH_INCLUDES."phpmailer/class.phpmailer.php");

			$mail = new PHPMailer();
			$mail->From     = SITE_EMAIL;
			$mail->FromName = SITE_NAME;

			/*
			$mail->SMTPAuth = true;
			$mail->Host     = "smtp.domain.com";
			$mail->Username = "user";
			$mail->Password = "pass";
			$mail->Mailer   = "smtp";
			$mail->Debug	= true;
			*/
				 
			while(!$rs->EOF) 
			{ 
				$pdf = new pdf_invoice_overview();  
		 	 	$pdf->companyName = SITE_NAME;
		 	 	$pdf->companyAddress = SITE_ADDRESS;
		 	 	$pdf->companyCity = SITE_CITY;
		 	 	$pdf->companyState = SITE_STATE;
		 	 	$pdf->companyZip = SITE_ZIP;
 	 	
		 	 	# load the setup_invoice fields into the pdf class
		 	 	$pdf->load_setup($invcfg);
 	 			 	 					
				$pagecount = $pdf->setSourceFile($pdf->getTemplate());
				$tplidx = $pdf->ImportPage(1);					
 
				$pdf->addPage();
				$pdf->useTemplate($tplidx);
				
				# override the show itemized, based on the customers choice
				if($rs->fields['invoice_show_itemized'] == 0 || $rs->fields['invoice_show_itemized'] == 1)
					$pdf->show_itemized = $rs->fields['invoice_show_itemized'];
				 
				$this->pdfInvoiceSummary($rs->fields['id'], $pdf); 

				$file = tempnam(PATH_FILES, "pdf_inv_".$rs->fields['id']).".pdf"; 
				$pdf->Output($file,'F'); 
				$pdf->closeParsers();	
				
				if($rs->fields['invoice_delivery'] == 1) { 
					$mail->AddAddress($rs->fields['email'], $rs->fields['first_name']. ' ' . $rs->fields['last_name']);
					$mail->Subject 	  = "Printable Invoice No. ". $rs->fields['id'];
					$mail->Body    	  = "Please find the printable version of invoice number {$rs->fields['id']} attached.\r\n\r\nThank you,\r\n".SITE_NAME;
					$mail->AddAttachment($file, "INVOICE_{$rs->fields['id']}.pdf");   
 			
				    if($mail->Send()) {
				    	$fields=Array('print_status'=>1);
				    	$db->Execute(sqlUpdate($db,"invoice",$fields,"id=".$rs->fields['id']));
				    } else {
				    	echo "Unable to email invoice # {$rs->fields['id']} to {$rs->fields['email']}<BR>";				    
				    }
				    
					$mail->ClearAddresses();
					$mail->ClearAttachments();
				} else if($rs->fields['invoice_delivery'] == 2) {
					if(copy($file,AGILE_PDF_INVOICE_PATH."invoice_".$rs->fields['id'].".pdf")) {
						$fields=Array('print_status'=>1);
				    	$db->Execute(sqlUpdate($db,"invoice",$fields,"id=".$rs->fields['id']));
					}
				}
								
				// delete tmp file and clean up vars used
				unlink($file);				
				unset($pdf->itemsSummary); 
				unset($pdf); unset($tplidx); unset($pagecount);
									
				$rs->MoveNext();	
			}	 			
		}		
	}
	
	
	/** Display a PDF invoice
	*/
	function pdf($VAR)
	{ 
		# Check invoice
		if(empty($VAR['id'])) {
			echo 'No Invoice Specified.';
			return false;
		}

		# Check admin authentication:
		global $C_auth;
		if ($C_auth->auth_method_by_name('invoice','view') == false) {
			# Validate on account level
			$db = &DB();
			$rs = $db->Execute(sqlSelect($db,"invoice","account_id", "id = ::{$VAR['id']}::")); 
			if ($rs->fields['account_id'] != SESS_ACCOUNT) {
				// todo: redirect to login page if not logged
				return false;
			}
		}

		$db =& DB();
		$invcfg = $db->Execute(sqlSelect($db,"setup_invoice","*","id=::".DEFAULT_SITE."::"));
		
		if (!defined('FPDF_FONTPATH'))
			define('FPDF_FONTPATH', PATH_INCLUDES.'pdf/font/');
		require_once(PATH_INCLUDES.'pdf/fpdi.php');
		require_once(PATH_INCLUDES.'pdf/fpdf_tpl.php');
		require_once(PATH_INCLUDES.'pdf/fpdf.php');
		require_once(PATH_INCLUDES.'pdf/pdf_invoice_'.$invcfg->fields['invoice_pdf_plugin'].'.inc.php');
		
		ob_start();
		
		$pdf = new pdf_invoice_overview();  
 	 	$pdf->companyName = SITE_NAME;
 	 	$pdf->companyAddress = SITE_ADDRESS;
 	 	$pdf->companyCity = SITE_CITY;
 	 	$pdf->companyState = SITE_STATE;
 	 	$pdf->companyZip = SITE_ZIP;
 	 	
 	 	# load the setup_invoice
 	 	$pdf->load_setup($invcfg);
 	 			
		$pagecount = $pdf->setSourceFile($pdf->getTemplate());
		$tplidx = $pdf->ImportPage(1);
		$pdf->addPage();
		$pdf->useTemplate($tplidx); 
		$this->pdfInvoiceSummary($VAR['id'], $pdf); 
		$pdf->Output('invoice.pdf','D');
		$pdf->closeParsers();
		
		ob_end_flush(); 
	}

	/** Export multiple invoices */
	function pdfExport(&$rs) 
	{				
		$db =& DB();
		$invcfg = $db->Execute(sqlSelect($db,"setup_invoice","*",""));
				
		define('FPDF_FONTPATH', PATH_INCLUDES.'pdf/font/');
		require_once(PATH_INCLUDES.'pdf/fpdi.php');
		require_once(PATH_INCLUDES.'pdf/fpdf_tpl.php');
		require_once(PATH_INCLUDES.'pdf/fpdf.php');
		require_once(PATH_INCLUDES.'pdf/pdf_invoice_'.$invcfg->fields['invoice_pdf_plugin'].'.inc.php');
		
		ob_start(); 
		$pdf = new pdf_invoice_overview(); 
		$pdf->companyName = SITE_NAME;
		$pdf->companyAddress = SITE_ADDRESS;
		$pdf->companyCity = SITE_CITY;
		$pdf->companyState = SITE_STATE;
		$pdf->companyZip = SITE_ZIP; 	
 	 	$pdf->load_setup($invcfg);			
		$pagecount = $pdf->setSourceFile($pdf->getTemplate());
		$tplidx = $pdf->ImportPage(1);					
		while(!$rs->EOF) { 
			$pdf->addPage();
			$pdf->useTemplate($tplidx); 
			$this->pdfInvoiceSummary($rs->fields['id'], $pdf); 
 			$rs->MoveNext();
 			unset($pdf->itemsSummary);
		} 
		$pdf->Output();
		$pdf->closeParsers();	  
		ob_end_flush();  
	}
	
	

	function pdfInvoiceSummary($id, &$pdf)
	{  
		# Invoice details:
		$db = &DB(); 
		$invoice = $db->Execute( $sql = sqlSelect($db, array("invoice", "currency"), "A.*, B.symbol", "A.id = ::$id:: AND B.id = A.billed_currency_id"));		
		$pdf->setInvoiceFields($invoice->fields);
		$pdf->setDueAmt($invoice->fields['total_amt'] - $invoice->fields['billed_amt']);	
		$pdf->setCurrency($invoice->fields['symbol']);
		$pdf->setDateRange( mktime(0,0,0,date('m',$invoice->fields['due_date'])-1, date('d',$invoice->fields['due_date']), date('Y',$invoice->fields['due_date'])), $invoice->fields['due_date']);		
		$pdf->drawInvoiceNo();
		$pdf->drawInvoiceDueDate();
		$pdf->drawInvoiceTotalAmt();
		$pdf->drawInvoiceDueAmt();
		$pdf->drawInvoicePaidAmt();
		$pdf->drawInvoiceDiscountAmt();
		$pdf->drawInvoiceTaxAmt();
		$pdf->drawInvoiceShippingAmt();
		$pdf->drawInvoiceRange();		 
		if($invoice->fields['billing_status'] !=1 && $invoice->fields['suspend_billing'] != 1 && $invoice->fields['due_date'] <= time()) 
		$pdf->drawInvoiceDueNotice();
		elseif($invoice->fields['billing_status'] == 1)
		$pdf->drawInvoicePaidNotice();		
			 
		# Account details:
		$account = $db->Execute("SELECT * FROM ".AGILE_DB_PREFIX."account WHERE id = ".$db->qstr($invoice->fields['account_id'])." AND site_id = ".$db->qstr(DEFAULT_SITE));
 	 	$pdf->setAccountFields($account->fields);
		$pdf->drawAccountId();
 	 	$pdf->drawAccountUsername();
 	 	$pdf->drawAccountName();
 	 	$pdf->drawAccountMailing();
 	 	 
		# Company details:
		$pdf->drawCompanyAddress(); 
		$pdf->drawCompanyLogo(); 
		
		## Get the summary items
		$items = & $db->Execute("select sku, item_type, product_name, product_id, sum(quantity) as quantity, (sum(total_amt)) as amount, price_setup, price_base from ".AGILE_DB_PREFIX."invoice_item where invoice_id=".$db->qstr($id)." group by sku, item_type");		
		$i = 0;
		if($items && $items->RecordCount()) {
			while (!$items->EOF) {
				$items_arr[$i] = $items->fields;
				$desc = $this->getLineItemDesc($items->fields['sku'], $items->fields['product_id'], false, $items->fields['product_name']);
				$items_arr[$i]['name'] = $desc;		
				$i++;
				if ($items->fields['price_setup']) {
					$items_arr[$i]['name'] = $desc." Set-up Charge";
					$items_arr[$i]['amount'] = $items->fields['price_setup'];
					$i++;
				}
				$items->MoveNext();
			}
		}
		if ($invoice->fields['discount_amt']) {
			$items_arr[$i]['name'] = 'Discount';
			$items_arr[$i]['amount'] = -($invoice->fields['discount_amt']);
			$i++;
		}
		if ($invoice->fields['tax_amt']) { 
			$trs = $db->Execute($sql=sqlSelect($db, Array('invoice_item_tax','tax'),"A.amount,B.description","A.tax_id=B.id AND A.invoice_id=::$id::")); 
            if($trs && $trs->RecordCount()) { 
            	unset($taxes);
            	while(!$trs->EOF) { 
                	$taxes["{$trs->fields['description']}"] += $trs->fields["amount"]; 
                    $trs->MoveNext(); 
                } 
                foreach($taxes as $txds=>$txamt) {
                    $items_arr[$i]['name'] = $txds;
					$items_arr[$i]['amount'] = $txamt;
					$i++;
                }
            } 
		} 		
		$pdf->drawSummaryLineItems($items_arr);
		unset($items_arr);
		unset($pdf->itemsSummary);
		
		## BEGIN loop for enumerating information in multiple ways on the invoice
		$iteration = 0;
		while($pdf->drawLineItems_pre($iteration)) {		
			## Get the line items: 
			$items = & $db->Execute( sqlSelect($db, "invoice_item", "*", "invoice_id = ::$id::") );
			if ($items && $items->RecordCount()) { 
				while ( !$items->EOF ) {
					#$items_arr[] = $items->fields;
					// get the date range if set
					if(!empty($items->fields['date_start']) && !empty($items->fields['date_stop'])) {
						global $C_translate;
						$C_translate->value('invoice','start', date(UNIX_DATE_FORMAT,$items->fields['date_start']));
						$C_translate->value('invoice','stop', date(UNIX_DATE_FORMAT,$items->fields['date_stop']));
						#$smart_items[$ii]['range'] = $C_translate->translate('recur_date_range','invoice','');
					}
					
					$cost = $items->fields['price_base'];
					$total = $cost * $items->fields['quantity'];
					$desc = $this->getLineItemDesc($items->fields['sku'],$items->fields['product_id'], strtolower($items->fields['domain_name'].'.'.$items->fields['domain_tld']), $items->fields['product_name']); 
					$line = array(
						"name" => $desc, 
						'amount' => $cost, 
						'sku'=>$items->fields['sku'], 
						'qty'=>$items->fields['quantity'], 
						'cost'=>$cost, 
						'attr'=>$items->fields['product_attr'], 
						'price_type'=>$items->fields['price_type'], 
						'price_base'=>$items->fields['price_base'], 
						'item_type'=>$items->fields['item_type'],
						'total_amt'=>$items->fields['total_amt']
					); 
					$pdf->drawLineItems($db, $line);
					if ($items->fields['price_setup']) {
						$desc .= " Set-up Charge";
						$total = $items->fields['price_setup'];
						$line = array("name" => $desc, 'amount' => $total, 'qty'=>'1', 'sku'=>$items->fields['sku'], 'cost'=>$total, 'price_base'=>$total, 'price_type'=>999); 
						$pdf->drawLineItems($db, $line);
					}
					$items->MoveNext();
				}
			}		 
			if ($invoice->fields['discount_amt']) {
				$desc = 'Discount';
				$total = -($invoice->fields['discount_amt']);
				$line = array("name" => $desc, 'amount' => $total, 'qty'=>'1', 'cost'=>$total, 'price_base'=>$total, 'price_type'=>999); 
				$pdf->drawLineItems($db, $line);
			}
			if ($invoice->fields['tax_amt']) { 
				$trs = $db->Execute($sql=sqlSelect($db, Array('invoice_item_tax','tax'),"A.amount,B.description","A.tax_id=B.id AND A.invoice_id=::$id::")); 
	            if($trs && $trs->RecordCount()) { 
	            	unset($taxes);
	            	while(!$trs->EOF) { 
	                	$taxes["{$trs->fields['description']}"] += $trs->fields["amount"]; 
	                    $trs->MoveNext(); 
	                } 
	                foreach($taxes as $txds=>$txamt) {
						$line = array("name" => $txds, 'amount' => $txamt, 'qty'=>'1', 'cost'=>$txamt, 'price_base'=>$txamt, 'price_type'=>999);
						$pdf->drawLineItems($db, $line);
	                }
	            } 
			}
			# Increment the iteration
			++$iteration;
		}
		# Custom functions:
		$pdf->drawCustom();	 
		unset($db); 	 
	}


	/** RESEND DUE NOTICE 
	*/
	function resend($VAR)
	{
		global $C_debug;
	
		# User invoice creation confirmation
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$mail = new email_template;
		$mail->send('invoice_resend', $VAR['account_id'],  $VAR['id'], '', '');
	
		# Alert
		$C_debug->alert('Sent payment due notice to user');
	
		# Update invoice
		$db = &DB();
		$q  = "SELECT notice_count FROM ".AGILE_DB_PREFIX."invoice  WHERE
	    				id = ".$db->qstr($VAR['id'])." AND
	    				site_id = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		$count = $rs->fields['notice_count'] + 1;
		$q  = "UPDATE ".AGILE_DB_PREFIX."invoice SET
	    				notice_count = ".$db->qstr($count)." WHERE
	    				id = ".$db->qstr($VAR['id'])." AND
	    				site_id = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
	}

  
	/**
	 * Generate all invoices for recurring services/charges/domains 
	 */
	function generate() {
		
		// check if charge module installed
		global $C_list;
		$charge_installed = $C_list->is_installed('charge');
				 
		// get services to be billed grouped by account and date
		if(MAX_INV_GEN_PERIOD <= 0) $max_date = time()+86400; else $max_date = time()+(MAX_INV_GEN_PERIOD*86400); 
		$p=AGILE_DB_PREFIX; $s=DEFAULT_SITE;
		$ids=false;
		$account=false;
		$date=false;
		$invoice=false;
		$sql = "SELECT DISTINCT service.id as serviceId, account.id as accountId, invoice.id as invoiceId, from_unixtime(service.date_next_invoice,'%Y-%m-%d') as dayGroup 
				FROM {$p}service as service 
				JOIN {$p}account as account ON ( service.account_id=account.id and account.site_id={$s} )
				LEFT JOIN {$p}invoice as invoice ON ( service.invoice_id=invoice.id and invoice.site_id={$s} )
				WHERE service.site_id={$s} 
				AND service.active = 1  
				AND ( service.suspend_billing IS NULL OR service.suspend_billing = 0 )  
				AND ( service.date_next_invoice > 0 AND service.date_next_invoice IS NOT NULL )
				AND  
				((
				    ( account.invoice_advance_gen!='' OR account.invoice_advance_gen is not null ) AND service.date_next_invoice <= (UNIX_TIMESTAMP(CURDATE())+(account.invoice_advance_gen*86400))
				 ) OR (
				    ( account.invoice_advance_gen='' OR account.invoice_advance_gen is null ) AND service.date_next_invoice <= {$max_date}
				))
				ORDER BY accountId, dayGroup, serviceId";   			
		$db=&DB();
		#$db->debug=true;
		$rs=$db->Execute($sql);
		if($rs === false) {global $C_debug; $C_debug->error('invoice.inc.php','generate()', $sql . " \r\n\r\n " . @$db->ErrorMsg()); }
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) { 
				if( $ids && ($rs->fields['accountId'] != $account ) || ($rs->fields['dayGroup'] != $date) ) { 
					$this->generateInvoices($ids, $account, $invoice, $charge_installed);
					$ids=false;
				}
				
				// set the current account and date
				$account=$rs->fields['accountId'];
				$invoice=$rs->fields['invoiceId'];
				$date=$rs->fields['dayGroup'];
				 
				// add to id list 
				if($ids) $ids.=",";
				$ids.=$rs->fields['serviceId']; 
				$rs->MoveNext();
			}
			if($ids) $this->generateInvoices($ids, $account, $invoice, $charge_installed); 
		}
		
		// Generate invoices for any domains expiring in X days. 
		if($C_list->is_installed('host_tld')) $this->generateDomains();  
		
		return true;
	}
	
	
	function generateInvoices($ids, $account_id, $invoice_id, $charge_installed=false) {

		if(empty($ids)) return false;
		
		# load required elements
		include_once(PATH_MODULES . 'service/service.inc.php');
		include_once(PATH_MODULES . 'discount/discount.inc.php');
		include_once(PATH_MODULES . 'tax/tax.inc.php');
		$taxObj = new tax;
		$serviceObj = new service;		
		   	 
		# start a transaction
		$db=&DB();
		#$db->debug=true;	
		if(AGILE_DB_TYPE == 'mysqlt') {
			$db->StartTrans();
			if(!$db->hasTransactions) { 
				global $C_debug;
				$msg=  "Transactions not supported in 'mysql' driver. Use 'mysqlt' or 'mysqli' driver";
				$C_debug->alert($msg);
				$C_debug->error('invoice.inc.php','generateInvoices()',  "Transactions not supported in 'mysql' driver. Use 'mysqlt' or 'mysqli' driver");  
				return false;  
			}
		}
	
		# generate an invoice id		
		$invoice = sqlGenID($db, 'invoice');
	
		# check for any discounts for the parent invoice or account_id 
		# applied at checkout and should continue to be applied if recurring type discount
		$discountObj = new discount;  
		$discountObj->available_discounts($account_id, 1, $invoice_id);
		 	
		# beginning totals
		$sub_total=0;
		$taxable_amount=0;
		$tax_amt=0;
		$discount_amt=0;	 	
				
		# get the full account and service and invoice details
		$p=AGILE_DB_PREFIX; $s=DEFAULT_SITE;
		$sql = "SELECT DISTINCT 
		service.id, service.parent_id, service.invoice_id, service.invoice_item_id, service.account_id, service.account_billing_id, service.product_id,
		service.sku, service.active, service.bind, service.type, service.price, service.price_type, service.taxable, service.date_last_invoice, service.date_next_invoice,
		service.recur_type, service.recur_schedule, service.recur_weekday, service.recur_week, service.domain_name,
		service.domain_tld, service.domain_type, service.domain_term, service.prod_attr, service.prod_attr_cart,
		account.currency_id, account.first_name, account.last_name, account.country_id, account.state, account.invoice_grace, account.invoice_advance_gen, account.affiliate_id as account_affiliate_id,
		invoice.affiliate_id, invoice.campaign_id, invoice.reseller_id, invoice.checkout_plugin_id, invoice.checkout_plugin_data, invoice.billed_currency_id, invoice.actual_billed_currency_id 
		FROM {$p}service as service 
		JOIN {$p}account as account ON ( service.account_id=account.id and account.site_id={$s} )
		LEFT JOIN {$p}invoice as invoice ON ( service.invoice_id=invoice.id and invoice.site_id={$s} )
		WHERE service.id in ({$ids})"; 
		$service=$db->Execute($sql); 
		if($service === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()1', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }
		if($service && $service->RecordCount()) {
			while(!$service->EOF) {
				
				if(empty($service->fields['billed_currency_id'])) $service->fields['billed_currency_id'] = DEFAULT_CURRENCY; 
				if(empty($service->fields['actual_billed_currency_id'])) $service->fields['actual_billed_currency_id'] = $service->fields['billed_currency_id'];
				
				$this->account_id=$service->fields['account_id'];
				$this->parent_id=$service->fields['invoice_id'];
				$this->account_billing_id=$service->fields['account_billing_id'];				
				if(!empty($service->fields['account_affiliate_id']))
				$this->affiliate_id=$service->fields['account_affiliate_id']; 
				else 
				$this->affiliate_id=$service->fields['affiliate_id']; 				
				$this->campaign_id=$service->fields['campaign_id'];
				$this->reseller_id=$service->fields['reseller_id'];
				$this->checkout_plugin_id=$service->fields['checkout_plugin_id'];
				$this->checkout_plugin_data=$service->fields['checkout_plugin_data'];
				$this->billed_currency_id=$service->fields['billed_currency_id'];
				$this->actual_billed_currency_id=$service->fields['actual_billed_currency_id'];
				$this->invoice_grace=$service->fields['invoice_grace'];
				
				$item_tax_amt=0;
				$item_total_amt=0;
				$item_discount_amt=0;
				
				# gen item_id
				$item = sqlGenID($db, "invoice_item"); 
				  
				# Calculate any recurring discounts for this item
				$item_total_amt = $service->fields['price']; 
				$item_discount_amt = $discountObj->calc_all_discounts(1, $item, $service->fields['product_id'], $service->fields['price'], $service->fields['account_id'], $sub_total+$service->fields['price']);
				$item_total_amt -= $item_discount_amt;
				$sub_total += $item_total_amt;
				$discount_amt += $item_discount_amt;
			 								
				# calculate any taxes for this item
				if($service->fields['taxable'] == 1)  {
					$item_tax_amt=0;   
					$item_tax_arr = $taxObj->calculate($item_total_amt, $service->fields['country_id'], $service->fields['state']); 
					if(is_array($item_tax_arr)) foreach($item_tax_arr as $tx) $item_tax_amt += $tx['rate']; 
					$tax_amt += $item_tax_amt; 
				}
		
				# Calculate next invoice date
				$next_invoice = $serviceObj->calcNextInvoiceDate($service->fields['date_next_invoice'], $service->fields['recur_schedule'], $service->fields['recur_type'], $service->fields['recur_weekday']);
				$due_date = $service->fields['date_next_invoice'];
				$recur_schedule=0;
				if(!empty($service->fields['recur_schedule'])) $recur_schedule = $service->fields['recur_schedule'];
				
				// create the invoice item
				$sql="INSERT INTO {$p}invoice_item SET
					id=$item,
					site_id=$s, 
					date_orig=".time().", 
					invoice_id = $invoice, 
					account_id={$service->fields['account_id']}, 
					service_id={$service->fields['id']}, 	
					product_id={$service->fields['product_id']}, 
					product_attr=".$db->qstr($service->fields['prod_attr']).", 
					product_attr_cart=".$db->qstr($service->fields['prod_attr_cart']).", 	
					sku=".$db->qstr($service->fields['sku']).", 
					quantity=1, 
					item_type=0, 
					price_type={$service->fields['price_type']}, 
					price_base={$service->fields['price']}, 
					price_setup=0, 
					recurring_schedule={$recur_schedule}, 
					date_start={$service->fields['date_next_invoice']}, 
					date_stop=$next_invoice, 
					domain_name=".$db->qstr($service->fields['domain_name']).", 
					domain_tld=".$db->qstr($service->fields['domain_tld']).", 
					domain_type=".$db->qstr($service->fields['domain_type']).", 
					tax_amt=$tax_amt, 
					total_amt=$item_total_amt, 
					discount_amt=$item_discount_amt";
				$itemrs=$db->Execute($sql);
			  	if($itemrs === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()2', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }
			  	
				// Insert tax records
				$taxObj->invoice_item($invoice, $item, $service->fields['account_id'], @$item_tax_arr);		
		
				# Insert discount records
				$discountObj->invoice_item($invoice, $item, $service->fields['account_id'], @$discountObj->discount_arr);
									
				// Update the last & next invoice date for this service
				$sql="UPDATE {$p}service 
					SET 
					date_last_invoice = {$service->fields['date_next_invoice']}, 
					date_next_invoice = $next_invoice 
					WHERE
					site_id=$s AND id = {$service->fields['id']} ";
				$srvsrs = $db->Execute($sql);
			 	if($srvsrs === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()3', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }
			 	
				// get any charges for this service and create them as invoice items 	
				if($charge_installed) {
					$sql = "SELECT * FROM ".AGILE_DB_PREFIX."charge WHERE (status=0 or status is null) and site_id=".DEFAULT_SITE." AND service_id = ".$service->fields['id']." AND date_orig < ". $service->fields['date_next_invoice'];
					$charge = $db->Execute($sql);
					if($charge && $charge->RecordCount()) {  
						while(!$charge->EOF) {  
							$item_tax_amt=0;
							$item_total_amt=0;
							$item_discount_amt=0;
										
							// Calculate any recurring discounts for this charge item
							$item_total_amt = ($charge->fields['quantity']*$charge->fields['amount']); 
							$item_discount_amt = $discountObj->calc_all_discounts(1, $item, $charge->fields['product_id'], $item_total_amt, $service->fields['account_id'], $sub_total+$item_total_amt);
							$item_total_amt -= $item_discount_amt;
							$sub_total += $item_total_amt;
							$discount_amt += $item_discount_amt;		
							 	 
							// calculate any taxes for this item
							if($charge->fields['taxable'] == 1)  {
								$item_tax_amt=0;   
								$item_tax_arr = $taxObj->calculate($chargeamt, $service->fields['country_id'], $service->fields['state']); 
								if(is_array($item_tax_arr)) foreach($item_tax_arr as $tx) $item_tax_amt += $tx['rate']; 
								$tax_amt += $item_tax_amt; 
							}				
			  
							// create the invoice item
							$charge_item_id = sqlGenID($db, 'invoice_item');
							
							$sql = "INSERT INTO {$p}invoice_item SET 
								id			= $charge_item_id,		
								site_id		= $s,
								charge_id	= {$charge->fields['id']},
								date_orig 	= ".time().",
								invoice_id 	= $invoice, 
								account_id 	= ".$this->account_id.", 
								service_id 	= ".$db->qstr($service->fields['id']).",
								product_id 	= ".$db->qstr($charge->fields['product_id']).", 
								product_attr= ".$db->qstr($charge->fields['attributes']).",  	
								sku 		= ".$db->qstr($service->fields['sku']).", 
								price_base 	= ".$db->qstr($charge->fields['amount']).", 
								quantity 	= ".$charge->fields['quantity'].",  
								item_type 	= 5,
								price_type 	= 0,
								price_setup = 0,  
								tax_amt 	= $item_tax_amt, 
								total_amt 	= $item_total_amt, 
								discount_amt= $item_discount_amt";
							$itemrs=$db->Execute($sql);						
							if($itemrs === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()4', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }
							
							# Insert tax records
							$taxObj->invoice_item($invoice, $charge_item_id, $charge->fields['account_id'], @$item_tax_arr);	
							
							# Insert discount records
							$discountObj->invoice_item($invoice, $charge_item_id, $charge->fields['account_id'], @$discountObj->discount_arr);
																								
							# update charge status
							$chargers=$db->Execute("UPDATE ".AGILE_DB_PREFIX."charge set status=1 WHERE id={$charge->fields['id']} AND site_id=".DEFAULT_SITE);
							if($chargers === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()2', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }
							
							$charge->MoveNext();
						}			
					} 		
				}	 
				$service->MoveNext(); 
			}
			
			// add any taxes
			@$total = $sub_total + $tax_amt; 
			
			// get invoice grace period from global/account
			if(!empty($this->invoice_grace)) $grace_period=$this->invoice_grace; else $grace_period=GRACE_PERIOD;
			 
			$sql = "INSERT INTO {$p}invoice SET 
				id=$invoice,
				site_id=$s,
				date_orig = ".time().", 
				date_last = ".time().",  
				notice_next_date = ".time().",
				type = 1, 
				process_status = 0, 
				billing_status = 0, 
				suspend_billing = 0, 
				print_status = 0, 
				refund_status = 0, 
				billed_amt = 0, 
				actual_billed_amt = 0, 
				notice_count = 0, 
				parent_id = ".$db->qstr($this->parent_id).",
				account_id = {$this->account_id},  
				account_billing_id = ".$db->qstr($this->account_billing_id).",
				affiliate_id = ".$db->qstr($this->affiliate_id).",
				campaign_id = ".$db->qstr($this->campaign_id).",
				reseller_id = ".$db->qstr($this->reseller_id).",
				checkout_plugin_id = ".$db->qstr($this->checkout_plugin_id).",
				checkout_plugin_data = ".$db->qstr($this->checkout_plugin_data).",
				actual_billed_currency_id = ".$db->qstr($this->actual_billed_currency_id).",
				billed_currency_id = ".$db->qstr($this->billed_currency_id).",
				notice_max = ".$db->qstr(MAX_BILLING_NOTICE).",  
				grace_period = ".$db->qstr($grace_period).",
				tax_amt = ".$tax_amt.",  
				discount_amt = ".$discount_amt.",  
				total_amt = ".$total.",     
				due_date = $due_date"; 
			$invoicers=$db->Execute($sql); 
			if($invoicers === false) {global $C_debug; $C_debug->error('invoice.inc.php','generateInvoices()2', $sql . " \r\n\r\n " . @$db->ErrorMsg()); $db->FailTrans(); return false; }		
		}				 
		
		if(AGILE_DB_TYPE == 'mysqlt') $db->CompleteTrans();
	}
 	
 
	
	
	/** Invoice expiring domains 
	*/
	function generateDomains()
	{ 
		$db = &DB();
		define('DEFAULT_DOMAIN_INVOICE', 30); //how far out to generate expiring domain invoices
		$expire = time() + (DEFAULT_DOMAIN_INVOICE*86400);
	
		### Get domains expiring soon:
		$rs = $db->Execute( sqlSelect( $db, 'service', '*', " active=1 AND domain_date_expire <= $expire AND type = 'domain' AND queue = 'none' AND
	        	( domain_type = 'register' OR domain_type = 'transfer' OR domain_type = 'renew' ) AND
	        	( suspend_billing = 0 OR suspend_billing IS NULL) " ) );
	
		if($rs && $rs->RecordCount() > 0 ) {
			while(!$rs->EOF)  {
				# Check that this domain has not already been invoiced
				$invoiced = $db->Execute( sqlSelect ($db, Array('invoice_item','invoice'), Array('A.*','B.*'),
				" A.invoice_id = B.id AND A.service_id = {$rs->fields['id']} AND A.sku = 'DOMAIN-RENEW' AND domain_type = 'renew' AND
			        			  date_start = {$rs->fields['date_last_invoice']} AND date_stop = {$rs->fields['domain_date_expire']}" ) );        			
	
				if($invoiced && $invoiced->RecordCount() == 0) {
					# Not previously invoiced, generate now!
					$this->generatedomaininvoice( $rs->fields, $this );
		        }	        	
	       		$rs->MoveNext();
	       	}
	    }
	}
		        
	/** Invoice expiring domains p2 
	*/
	function generatedomaininvoice( $VAR )
	{
		include_once(PATH_MODULES . 'tax/tax.inc.php');
		$taxObj = new tax;
	
		$db = &DB();
	
		if( is_array( $VAR ) )  {
			$expire = time();
			$rs = $db->Execute( sqlSelect($db, 'service', '*', " id = ::{$VAR['id']}:: AND active=1
	        				AND type = 'domain' AND queue = 'none' AND
	        				( domain_type = 'register' OR domain_type = 'transfer' OR domain_type = 'renew'  ) AND
	        				( suspend_billing = 0 OR suspend_billing IS NULL ) " ));
			$service = $rs->fields;
		} else {
			$service = $VAR;
		} 
	
		if(empty($service['id'])) {
			global $C_debug;
			$C_debug->alert("Unable to generate domain renweal invoice due to domain status.");
			return false;
		}
	
		# Get the parent invoice details:
		if(!empty($service['invoice_id'])) {
			$rs = $db->Execute( sqlSelect($db, 'invoice', '*', " id = {$service['invoice_id']} ",  "" ) );
			$invoice = $rs->fields;
		} else {
			$invoice = false;
		}
	
		# Get the account details:
		$rs = $db->Execute( sqlSelect($db, 'account', '*', " id = {$service['account_id']} ", ""  ) );
		$account = $rs->fields;
	
	  	# Get the account price
		include_once(PATH_MODULES.'host_tld/host_tld.inc.php');
		$tldObj=new host_tld;			
		$tld_arr = $tldObj->price_tld_arr($service['domain_tld'], 'renew', false, false, false, $service['account_id']);			
		foreach($tld_arr as $term => $price) break;
		 
		# Calculate taxes:
		$rs = $db->Execute($sql=sqlSelect($db,"host_tld","taxable","name = ::{$service['domain_tld']}::")); 
		if( $service['taxable'] || @$rs->fields['taxable'] ) {
			$tax_arr = $taxObj->calculate($price, $account["country_id"], $account["state"]);
		} else {
			$tax_arr = false;
		}
		  
		$total = $price;
	
		$tax_amt = 0;
		if(is_array($tax_arr)) {
			foreach($tax_arr as $tx) {
				$tax_amt += $tx['rate'];
			}
			$total += $tax_amt;
		}
	
		# calculate the dates
		$expire = $service['domain_date_expire'] + ($term*86400);
		$due_date = $service['domain_date_expire'] - (86400*3);
	
		# Create the invoice
		$id = sqlGenID( $db, "invoice" );
		$insert = $db->Execute( $sql = sqlInsert($db, "invoice",
		Array(
		'date_orig' 		=> time(),
		'date_last' 		=> time(),
		'type' 				=> 2,
		'process_status' 	=> 0,
		'billing_status' 	=> 0,
		'suspend_billing' 	=> 0,
		'print_status' 		=> 0,
		'parent_id' 		=> $service['invoice_id'],
		'account_id' 		=> $service['account_id'],
		'account_billing_id'=> $service['account_billing_id'],
		'affiliate_id' 		=> @$invoice['affiliate_id'],
		'campaign_id' 		=> @$invoice['campaign_id'],
		'reseller_id' 		=> @$invoice['reseller_id'],
		'checkout_plugin_id'=> @$invoice['checkout_plugin_id'],
		'tax_amt' 			=> $tax_amt,
		'discount_arr' 		=> serialize(@$discount_arr),
		'discount_amt' 		=> @$discount_amt,
		'total_amt' 		=> $total,
		'billed_amt' 		=> 0,
		'billed_currency_id'=> DEFAULT_CURRENCY,
		'actual_billed_amt' => 0,
		'actual_billed_currency_id' => @$invoice['actual_billed_currency_id'],
		'notice_count' 		=> 0,
		'notice_next_date' 	=> time(),
		'notice_max' 		=> MAX_BILLING_NOTICE,
		'grace_period' 		=> 0,
		'due_date' 			=> $due_date
		),  $id ) ) ;
	
		# create the invoice item:
		if($insert) {
			$db->Execute ( $idx = sqlInsert($db, "invoice_item",
			Array(
			'date_orig' 		=> time(),
			'invoice_id'		=> $id,
			'account_id'		=> $service['account_id'],
			'service_id' 		=> $service['id'],
			'sku' 				=> 'DOMAIN-RENEW',
			'quantity' 			=> 1,
			'item_type' 		=> 2,
			'price_type' 		=> 0,
			'price_base'		=> $price,
			'price_setup' 		=> 0,
			'domain_type' 		=> 'renew',
			'date_start' 		=> $service['domain_date_expire'],
			'date_stop'			=> $expire,
			'domain_name' 		=> $service['domain_name'],
			'domain_tld' 		=> $service['domain_tld'],
			'domain_term' 		=> $term,
			'tax_amt'			=> $tax_amt,
			'total_amt'			=> $price
			) ) );
			
			# Insert tax records
			$taxObj->invoice_item($id, $idx, $service['account_id'], @$item_tax_arr);			
		
			# Update the service record
			$fields=Array('active' => 0);
			$db->Execute(sqlUpdate($db,"service",$fields,"id = {$service['id']}"));
			
			global $C_debug;
			$C_debug->alert("Generated domain renewal invoice for {$service['domain_name']}.{$service['domain_tld']}");
			return $id;
		}
	}
  
 

	/** Run AutoBilling and Due Notices
        */
	function autobill($VAR)
	{
		global $VAR, $C_debug, $C_list;

		# User invoice creation confirmation
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$mail = new email_template;

		# get all due invoices
		$db = &DB(); 
		#$db->debug = true;
				
		if(empty($VAR['invoice_id']))
		{
			$this->bill_one = false;
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'invoice
	          		   WHERE  notice_next_date <=  ' . $db->qstr( time() ) . ' 
	                   AND (
	                      	billing_status =  0 OR
	                       	billing_status IS NULL
	                   ) AND (
	                   		suspend_billing =  0 OR
	                   		suspend_billing IS NULL
	                   )
	                   AND  site_id  =   ' . $db->qstr(DEFAULT_SITE);
			$invoice = $db->Execute($sql);
			if($invoice->RecordCount() == 0) {
				$C_debug->alert('No Invoices to Autobill');
				return false;
			}
		} else {
			# get the specified invoice:
			$this->bill_one = true;
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'invoice
	                   WHERE (
	                    	billing_status =  0 OR
	                      	billing_status IS NULL
	                   )  
	                   AND  id  =   ' . $db->qstr($VAR['invoice_id']) . '
	                   AND  site_id =   ' . $db->qstr(DEFAULT_SITE);
			$invoice = $db->Execute($sql);
		}

		# Check for results
		if($invoice->RecordCount() == 0) {
			$C_debug->alert('Invoice could not be billed!');
			return false;
		}

		# Loop through results
		while(!$invoice->EOF)
		{
			$db->StartTrans();
			
			$due = true;

			# get currency code
			$cyid = $invoice->fields['actual_billed_currency_id'];
			$billed_currency_id = $invoice->fields['billed_currency_id'];
			if(empty($this->currency_iso[$cyid]))
			{
				$q = "SELECT three_digit,convert_array FROM ". AGILE_DB_PREFIX ."currency WHERE
                    id       = ". $db->qstr($cyid)." AND
                    site_id     = ". $db->qstr(DEFAULT_SITE);
				$currb   = $db->Execute($q);
				$this->format_currency[$cyid] = Array ( 'convert' => unserialize($currb->fields["convert_array"]),
				'iso' => $currb->fields["three_digit"]);
			}

			# get the currency codes (default)
			if(empty($this->format_currency[$billed_currency_id]))
			{
				# Get the billed currency id currency info:
				$q  = "SELECT three_digit,convert_array FROM ".AGILE_DB_PREFIX."currency WHERE
                    id       = ".$db->qstr($billed_currency_id)." AND
                    site_id = ".$db->qstr(DEFAULT_SITE);
				$currb = $db->Execute($q);
				$this->format_currency[$billed_currency_id] = Array ( 'convert' => unserialize($currb->fields["convert_array"]),
				'iso' => $currb->fields["three_digit"]);
			}

			# attempt to autobill?
			if(!empty( $invoice->fields['account_billing_id'] ))
			{
				# get checkout plugin details:
				$billing =& $db->Execute($sql=sqlSelect($db, array('account_billing','checkout'), 'A.*,B.checkout_plugin',
				"A.id = ::{$invoice->fields['account_billing_id']}:: AND A.checkout_plugin_id=B.id"));

				if($billing && $billing->RecordCount() == 1 && !empty($billing->fields['checkout_plugin'])) {
					$plugin_file = PATH_PLUGINS.'checkout/'. $billing->fields['checkout_plugin'] .'.php';
					if(!is_file($plugin_file)) {
						$err = $plugin_file .' missing when autobilling invoice id ' . $invoice->fields['id'];
						$C_debug->error('invoice.inc.php','autobill()', $err);
					} else {
						include_once ( $plugin_file );
						eval( '$PLG = new plg_chout_' . $billing->fields['checkout_plugin'] . '("'.$billing->fields['checkout_plugin_id'].'");');
					}
				} else {
					$err = 'account_billing.id '.$invoice->fields['account_billing_id'].' empty or not associated with a checkout plugin when autobilling invoice id ' . $invoice->fields['id'];
					$C_debug->error('invoice.inc.php','autobill()', $err);
				}
			}

			# get the actual billed amount
			$amount = $invoice->fields['total_amt'] - $invoice->fields['billed_amt'];
			$billed_amt = $invoice->fields['total_amt'];
			$actual_billed_amt = $invoice->fields['total_amt'];
			if($amount <= 0) $due = false;

			if(!empty($PLG) && is_object($PLG) && $PLG->type == 'gateway' && $amount > 0)
			{ 
				# attempt autobilling if account billing exists and gateway plugin
				if($invoice->fields['account_billing_id'] > 0 )
				{
					/* get the account details */
					$account = $db->Execute(sqlSelect($db,"account","id,email","id=::{$invoice->fields['account_id']}"));
				  
					/* Convert the invoice amount to the actual billed currency amount */
					if($cyid != $invoice->fields['billed_currency_id']) { 
						$conversion = $this->format_currency[$billed_currency_id]["convert"][$cyid]["rate"];
						$amount     *= $conversion;
						$actual_billed_amt = $invoice->fields['actual_billed_amt'] + $amount;
					}
					
					/* load the billing details from the database */ 
					$PLG->setBillingFromDBObj($billing, true);
		  
					/* attempt to auto-bill */
					if(!$checkout_plugin_data = $PLG->bill_checkout( number_format($amount,2), $invoice->fields['id'], $this->format_currency[$cyid]['iso'], $account->fields, 0,0) ) {
						$due = true; 
						$email = new email_template;
						$email->send('invoice_decline_user', $invoice->fields['account_id'], $invoice->fields['id'],$C_list->format_currency($invoice->fields['total_amt'],$cyid), $C_list->date($invoice->fields['due_date']));
						$email = new email_template;
						$email->send('admin->invoice_decline_admin', $invoice->fields['account_id'], $invoice->fields['id'], $C_list->format_currency($invoice->fields['total_amt'],''), $C_list->date($invoice->fields['due_date']));
					} else {
						$due = false;
					}
				}
			}

			# send proper alert & manage services
			if ($due)
			{
				# determine if overdue
				$due = $invoice->fields['due_date'];
				$grace = $invoice->fields['grace_period'];
				if(time() < $due+(86400*$grace))
				{
					if($invoice->fields['notice_count'] <= 0)
					{
						# send out first alert - new invoice created!
						$email = new email_template;
						$email->send('invoice_recur_user', $invoice->fields['account_id'], $invoice->fields['id'], $C_list->format_currency($invoice->fields['total_amt'],$cyid), $C_list->date($invoice->fields['due_date']));
						$email = new email_template;
						$email->send('admin->invoice_recur_admin', $invoice->fields['account_id'], $invoice->fields['id'], $C_list->format_currency($invoice->fields['total_amt'],''), $C_list->date($invoice->fields['due_date']));
					}
					else
					{
						# send out payment due notice
						if(empty($PLG) || $PLG->type == 'gateway') {
							$email = new email_template;
							$email->send('invoice_due_user', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$cyid]["iso"], $C_list->date($invoice->fields['due_date']));
							$email = new email_template;
							$email->send('admin->invoice_due_admin', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$billed_currency_id]["iso"], $C_list->date($invoice->fields['due_date']));
						}
						elseif($PLG->type == 'redirect')  {
							$email = new email_template;
							$email->send('invoice_due_user', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$cyid]["iso"], $C_list->date($invoice->fields['due_date']));
						} elseif ($PLG->type == 'other') {
							$email = new email_template;
							$email->send('admin->invoice_due_admin', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$billed_currency_id]["iso"], $C_list->date($invoice->fields['due_date']));
						}
					}

					# increment notice counter
					$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'invoice SET
                            notice_count    =  ' . $db->qstr($invoice->fields['notice_count']+1) . ',
                            notice_next_date = ' . $db->qstr(time()+86400*3) . '
                            WHERE
                            id           =  ' . $db->qstr( $invoice->fields['id'] ) . ' AND
                            site_id      =  ' . $db->qstr(DEFAULT_SITE);
					$db->Execute($sql);
				}
				else
				{
					# send service cancelation notice
					$email = new email_template;
					$email->send('service_suspend_user', $invoice->fields['account_id'], $invoice->fields['id'], $C_list->format_currency($invoice->fields['total_amt'],$cyid), $C_list->date($invoice->fields['due_date']));
					$email = new email_template;
					$email->send('admin->service_suspend_admin', $invoice->fields['account_id'], $invoice->fields['id'], $C_list->format_currency($invoice->fields['total_amt'],''), $C_list->date($invoice->fields['due_date']));

					# overdue - cancel services
					$vara['id'] = $invoice->fields['id'];
					$this->voidInvoice($vara, $this);

					# suspend billing activity
					$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'invoice SET
                            notice_count    =  ' . $db->qstr($invoice->fields['notice_count']+1) . ',
                            suspend_billing = ' . $db->qstr('1') . '
                            WHERE
                            id           =  ' . $db->qstr( $invoice->fields['id'] ) . ' AND
                            site_id      =  ' . $db->qstr(DEFAULT_SITE);
					$db->Execute($sql); 
				}			
			}
			else
			{
				# update billing stauts
				$sql = 'UPDATE ' . AGILE_DB_PREFIX . 'invoice SET
                            notice_count    =  ' . $db->qstr($invoice->fields['notice_count']+1) . ',
                            billing_status  = ' . $db->qstr('1') . ',
                            billed_amt       = ' . $db->qstr($billed_amt) . ',
                            actual_billed_amt = ' . $db->qstr($actual_billed_amt) . '
                            WHERE
                            id           =  ' . $db->qstr( $invoice->fields['id'] ) . ' AND
                            site_id      =  ' . $db->qstr(DEFAULT_SITE);
				$db->Execute($sql);

				# update invoice via autoapproveInvoice
				$this->autoApproveInvoice($invoice->fields['id']);

				# User alert of payment processed
				$email = new email_template;
				$email->send('invoice_paid_user', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$cyid]['iso'], '');

				# Admin alert of payment processed
				$email = new email_template;
				$email->send('admin->invoice_paid_admin', $invoice->fields['account_id'], $invoice->fields['id'], $this->format_currency[$billed_currency_id]['iso'], '');
			}
			$invoice->MoveNext();
			unset($PLG);
			
			/* finish transaction */
			$db->CompleteTrans();
		}
	}
 
	

	/**
	 * Find out if a user has unpaid invoices
	 */
	function has_unpaid($VAR) {
		if(!SESS_LOGGED) return false;
		$db=&DB();
		$inv=$db->Execute($sql=sqlSelect($db,"invoice","SUM(total_amt-billed_amt) as total",
		"account_id=".SESS_ACCOUNT." AND billing_status=0 AND refund_status=0"));  
		if($inv && $inv->RecordCount() && $inv->fields['total']>0) {
			global $smarty, $C_list;
			//$act= $db->Execute(sqlSelect($db,"account","currency_id","id=));
			$smarty->assign('has_unpaid', $C_list->format_currency_num($inv->fields['total'],SESS_CURRENCY)); 
		}			
	}

	
	/**
	 * Get the totals for multiple invoices or for a group of invoices stored in temp
	 */
	function multiple_invoice_total($invoice,$account_id=SESS_ACCOUNT) 
	{
		$db = &DB();
		if(empty($invoice) || preg_match("/,/", $invoice)) { 
			$id_list='';
			if(!empty($invoice)) {
				$id = explode(',', $invoice);
				for($i=0; $i<count($id); $i++) {
					if($id[$i] != '') {
						if($i == 0) {
							$id_list .= " id = " .$db->qstr($id[$i])." ";
							$ii++;
						} else {
							$id_list .= " OR id = " .$db->qstr($id[$i]). " ";
							$ii++;
						}
					}
				}
				if(!empty($id_list)) $id_list = "( $id_list ) AND ";
			}
			// get invoice totals
			$total=0;
			$inv=$db->Execute($sql=sqlSelect($db,"invoice","id,total_amt,billed_amt", "$id_list account_id=".SESS_ACCOUNT." AND billing_status=0 AND refund_status=0"));
			if($inv && $inv->RecordCount()) {
				while(!$inv->EOF) {
					$this->invoice[] = $inv->fields['id'];
					$total += ($inv->fields['total_amt']-$inv->fields['billed_amt']);
					$inv->MoveNext();
				}
				return $total;
			} else {
				return false;
			}
		} else {
			// get total from temp data
			$inv=$db->Execute($sql=sqlSelect($db,"temporary_data","data,field1 as total","field2=::$invoice::")); 
			if($inv && $inv->RecordCount() && $inv->fields['total'] > 0) {
				if(!empty($inv->fields['field2'])) $this->invoice=unserialize($inv->fields['data']);
				return $inv->fields['total'];
			} else {
				return false;
			}			
		}
		return false;		
	}

	/**
	 * Preview checkout of multiple invoices
	 */
	function checkout_multiple_preview($VAR) 
	{
		global $smarty,$C_list;
		if(!SESS_LOGGED) return false;
		 
		$db=&DB();
		$total = $this->multiple_invoice_total(@$VAR['id'],SESS_ACCOUNT);
		if($total > 0 && count($this->invoice) > 1) 
		{
			// get country id for checkout options
			$account=sqlSelect($db, "account", "country_id", "id=".SESS_ACCOUNT);
			 			
			// get payment options
			include_once(PATH_MODULES.'checkout/checkout.inc.php');
			$checkout = new checkout;
			$checkoutoptions = $checkout->get_checkout_options(SESS_ACCOUNT, $total, false, $account->fields['country_id'],true);
			 
			// get a temporary id (48 hours)
			$id=sqlGenID($db, "temporary_data");
			$invoice["id"] = "MULTI-$id";
			$invoice["total"] = $total;
			$fields=Array('date_orig'=>time(), 'date_expire'=>time()+86400*3, 'field2'=> $invoice['id'], 'field1'=>$total, 'data'=>serialize($this->invoice));
			$id = & $db->Execute(sqlInsert($db,"temporary_data",$fields));
			
			$smarty->assign('invoice', $invoice); 
			$smarty->assign('total', $C_list->format_currency_num($total,SESS_CURRENCY)); 
			$smarty->assign('checkoutoptions', $checkoutoptions);
		
		} elseif (count($this->invoice) == 1) {	
			$id = $this->invoice[0];
			echo "<script language=javascript>document.location.href='?_page=invoice:user_view&id=".$id."';</script>";
		} else {
			echo "No due invoices selected for payment.";
		}
	}
		
	
	/** Run checkout plugin for billing
    */
	function checkoutnow($VAR)
	{
		global $C_translate, $smarty, $C_list, $VAR;
 
		# Validate user logged in:
		if(SESS_LOGGED != '1') {
			echo '<script language="JavaScript">alert("You must be logged in to complete this purchase! Please refresh this page in your browser to login now...");</script>';
			return false;
		}
		 
		$db     = &DB();
		if(preg_match("/MULTI-/", @$VAR['invoice_id'])) {
			// get multi-invoice details
			$total = $this->multiple_invoice_total(@$VAR['invoice_id'],SESS_ACCOUNT);
			if(!$total) return false;
			$recur_amt=false;
			$recur_arr=false;
			$account_id=SESS_ACCOUNT;
			$this->invoice[]=$VAR['invoice_id'];
			$this->invoice_id=$VAR['invoice_id'];
			$CURRENCY = DEFAULT_CURRENCY;
			$multi=true;
		} else {
			# Validate the invoice selected, & get the totals:
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'invoice WHERE site_id = '.$db->qstr(DEFAULT_SITE).' AND id = ' . $db->qstr($VAR['invoice_id']);
			$result = $db->Execute($sql); 
			if(!$result  || $result->RecordCount() == 0) return false;
	
			# Determine the price & currency
			if($result->fields['billed_currency_id'] != $result->fields['actual_billed_currency_id']) {
				global $C_list;
				$CURRENCY = $result->fields['actual_billed_currency_id'];
				if($result->fields['billed_amt'] <= 0)
				$total = $C_list->format_currency_decimal ($result->fields['total_amt'], $CURRENCY);
				else
				$total = $C_list->format_currency_decimal ($result->fields['total_amt'], $CURRENCY) - $result->fields['actual_billed_amt'];
			} else {
				$CURRENCY = $result->fields['billed_currency_id'];
				$total = $result->fields['total_amt']-$result->fields['billed_amt'];
			}
			$recur_amt=$result->fields['recur_amt'];
			if($recur_amt>0) $recur_amt = $C_list->format_currency_decimal ($recur_amt, $CURRENCY);
			@$recur_arr=unserialize($result->fields['recur_arr']);
			$account_id=$result->fields['account_id'];
			$this->invoice_id=$result->fields['id'];
			$this->invoice[]=$result->fields['id'];
			$multi=false;
		}
		$amount = round($total, 2);

		# Get the account details:
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account WHERE site_id = ' . $db->qstr(DEFAULT_SITE) . ' AND id = ' . $db->qstr($account_id);
		$account = $db->Execute($sql);
		if (!$account || !$account->RecordCount()) return false;
  
		# Validate checkout option selected is allowed for purchase:
		$q  = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE site_id = ".$db->qstr(DEFAULT_SITE)." AND id = ".$db->qstr(@$VAR['option'])." AND active = 1 AND ";
		if($recur_amt>0 && @$billed_amt == 0 ) $q .= "allow_recurring = 1 "; else $q .= "allow_new = 1 ";
		$chopt = $db->Execute($q);
		if (!$chopt || !$chopt->RecordCount()) return false;
		if($chopt && $chopt->RecordCount()) {
			$show = true;
			if ( @$chopt->fields["total_maximum"] != "" && $total > $chopt->fields["total_maximum"] )   $show = false;  
			if ( @$chopt->fields["total_miniumum"] != "" && $total < $chopt->fields["total_miniumum"] ) $show = false; 
		}
		if(!$show) {
			echo '<script language=Javascript> alert("Unable to checkout with the selected method, please select another."); </script> ';
			return false;
		}		 
		
		# Load the checkout plugin:
		$plugin_file = PATH_PLUGINS . 'checkout/'. $chopt->fields["checkout_plugin"] . '.php';
		include_once ( $plugin_file );
		eval ( '$PLG = new plg_chout_' .   $chopt->fields["checkout_plugin"] . '("'.@$VAR["option"].'");');
 
		if(!empty($VAR['account_billing_id']) && @$VAR['new_card']==2) {  
			/* validate credit card on file details */
			$account_billing_id=$VAR['account_billing_id']; 
			if(!$PLG->setBillingFromDB($account_id, $account_billing_id, $VAR['option'])) { 
				global $C_debug;
				$C_debug->alert("Sorry, we cannot use that billing record for this purchase.");
				return false;
			}
		} else {
			/* use passed in vars */
			$PLG->setBillingFromParams($VAR);
		}
				
		# Set Invoice Vars:
		$this->total_amt					= $amount;
		$this->currency_iso 				= $C_list->currency_iso($CURRENCY);
		$this->currency_iso_admin			= $C_list->currency_iso($CURRENCY); 
		$this->account_id					= $account_id; 
		$this->actual_billed_currency_id	= $CURRENCY;
		$this->billed_currency_id			= $CURRENCY;
		$this->checkout_plugin_id           = @$VAR["option"];
				
		# Run the plugin bill_checkout() method:
		$this->checkout_plugin_data = $PLG->bill_checkout( $amount, $this->invoice_id, $this->currency_iso, $account->fields, $recur_amt, $recur_arr);

		# redirect
		if(!empty($this->checkout_plugin_data['redirect'])) echo $this->checkout_plugin_data['redirect'];
  
		# determine results
		if( $this->checkout_plugin_data === false ) {
			if(!empty($PLG->redirect)) echo $PLG->redirect; 
			return false; 
		} elseif ($PLG->type == "gateway" && empty($PLG->redirect)) {  		 
			if(empty($this->admin_checkout)) {
				$VAR['_page'] = "invoice:thankyou";
			} else {
				$VAR['_page'] = "invoice:view";
			}   
		} elseif ($PLG->type == "redirect") {
			echo "<html><head></head><body><center>
				Please wait while we redirect you to the secure payment site....
				{$PLG->redirect}</center></body></html>"; 
		} 
		 
		# Call the Plugin method for storing the checkout data, if new data entered:
		$this->account_billing_id = $PLG->store_billing($VAR, $PLG);

		# Load the email template module
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$mail = new email_template;

		# Update billing details for this invoice, if realtime billing succeeded:
		if($PLG->type == 'gateway' || $amount == 0) {
			$q  = "UPDATE ".AGILE_DB_PREFIX."invoice
		        		SET
		        			account_billing_id		= " .$db->qstr($this->account_billing_id). ",
		        			billing_status			= " .$db->qstr(1). ",
		        			billed_amt 		  		= " .$db->qstr($total). ",
		        			actual_billed_amt 		= " .$db->qstr($amount). ",
		        			date_last		  		= " .$db->qstr(time()). ",
		        			checkout_plugin_id		= " .$db->qstr($this->checkout_plugin_id) .",
		        			checkout_plugin_data	= " .$db->qstr(serialize($this->checkout_plugin_data)). "
		        		WHERE
		        			site_id   = ".$db->qstr(DEFAULT_SITE)." AND
		        			id 		  = ".$db->qstr($this->invoice_id);	 
			$rst = $db->Execute($q);
			if ($rst === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','checkoutnow', $db->ErrorMsg());
				return false;
			}

			// loop through each invoice paid
			foreach($this->invoice as $this->invoice_id) { 
				# Send billed e-mail notice to user
				$email = new email_template;
				$email->send('invoice_paid_user', $this->account_id, $this->invoice_id, $this->currency_iso, '');
	
				# Admin alert of payment processed
				$email = new email_template;
				$email->send('admin->invoice_paid_admin', $this->account_id, $this->invoice_id, $this->currency_iso_admin, '');
	
				# Submit the invoice for approval
				$arr['id'] = $this->invoice_id;
				$this->approveInvoice($arr, $this);
			}

		} else {

			# Just update the last_date and plugin data
			$q  = "UPDATE ".AGILE_DB_PREFIX."invoice
		        		SET 
		        			account_billing_id		= " .$db->qstr($this->account_billing_id). ",
		        			date_last		  		= " .$db->qstr(time()). ",
		        			checkout_plugin_id		= " .$db->qstr($this->checkout_plugin_id) .",
		        			checkout_plugin_data	= " .$db->qstr(serialize($this->checkout_plugin_data)). "
		        		WHERE
		        			site_id   = ".$db->qstr(DEFAULT_SITE)." AND
		        			id 		  = ".$db->qstr($this->invoice_id);	 
			$rst = $db->Execute($q);
			if ($rst === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','checkoutnow', $db->ErrorMsg());
				return false;
			}

			# Admin e-mail alert of manual payment processing
			if ( $PLG->name == 'MANUAL' ) { 
				$date_due = $C_list->date(time());
				foreach($this->invoice as $this->invoice_id) { 
					$email = new email_template;
					$email->send('admin->invoice_due_admin', $this->account_id, $this->invoice_id, '', $date_due);
				}
				
				global $C_debug;
				$C_debug->alert($C_translate->translate('manual_alert','checkout'));				
			}  
		}
	}	
		

	/** create modified array for invoice summarization
    */
	function summarizeLineItems($smart_items)
	{
		//$ignore['SKU']=true;
		$sum=false;
		if(is_array($smart_items)) {
			foreach($smart_items as $it)  {
				if(empty($sum["{$it["sku"]}"])) {
					// unique line item
					if(empty($ignore["{$it["sku"]}"])) $sum["{$it["sku"]}"][] = $it;
				} else {
					// is unique price/attributes?
					$unique=true;
					foreach($sum["{$it["sku"]}"] as $sid => $flds) {
						if(	$flds["price_base"] == $it["price_base"] && $flds["price_setup"] == $it["price_setup"] && $flds['product_attr'] == $it['product_attr']  ) {
							$sum["{$it["sku"]}"]["$sid"]["quantity"] += 1;
							$unique = false;
							break;
						}
					}
					// unique line item
					if($unique) $sum["{$it["sku"]}"][] = $it;
				}
			}
		}

		if(!empty($sum)) {
			unset($smart_items);
			foreach($sum as $sku => $item) foreach($item as $sitem)  $smart_items[] = $sitem;
			return $smart_items;
		}
	}
        
            	
    	
	/** VIEW
    */
	function view($VAR)
	{
		global $C_translate, $C_list;
		$this->invoice_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		$db = &DB();

		# set the field list for this method:
		$arr = $this->method[$type];

		# loop through the field list to create the sql queries
		$field_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= $value;
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . $value;
			}
			$i++;
		}

		if(isset($VAR["id"]))
		{
			$id = explode(',',$VAR["id"]);
			for($i=0; $i<count($id); $i++)
			{
				if($id[$i] != '')
				{
					if($i == 0)
					{
						$id_list .= " id = " .$db->qstr($id[$i])." ";
						$ii++;
					}
					else
					{
						$id_list .= " OR id = " .$db->qstr($id[$i]). " ";
						$ii++;
					}
				}
			}
		}

		if($ii>0)
		{ 
			$any_trial			= false;
			$any_recurring		= false;
			$any_new			= false;

			# generate the full query
			$q = "SELECT
		        	  $field_list
		        	  FROM
		        	  ".AGILE_DB_PREFIX."$this->table
					  WHERE					
		        	  $id_list
		        	  AND site_id = '" . DEFAULT_SITE . "'
		        	  ORDER BY $this->order_by 
		        	  LIMIT 0,1";
			$result = $db->Execute($q);
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('invoice.inc.php','view', $db->ErrorMsg());

				if(isset($this->trigger["$type"]))
				{
					include_once(PATH_CORE   . 'trigger.inc.php');
					$trigger    = new CORE_trigger;
					$trigger->trigger($this->trigger["$type"], 0, $VAR);
				}
				return;
			}

			### Set it as a class variable:
			$this->result = $result;

			# put the results into a smarty accessable array
			$i=0;
			$ii=0;
			$class_name = TRUE;
			while (!$result->EOF)
			{
				$smart[$i] = $result->fields;

				## get the product plugin name
				if(!empty($result->fields['checkout_plugin_id'])) {
					$cplg = $db->Execute(sqlSelect($db,"checkout","name","id = {$result->fields['checkout_plugin_id']}"));
					if($cplg && $cplg->RecordCount()) $smart[$i]['checkout_plugin'] = $cplg->fields['name'];
				}

				if($result->fields['total_amt'] == 0)
				$smart[$i]['balance'] = 0;
				else
				$smart[$i]['balance'] = $result->fields['total_amt'] - $result->fields['billed_amt'];

				## Get the tax details
				if( !empty($result->fields['tax_amt']) ) {
					$trs = $db->Execute($sql=sqlSelect($db, Array('invoice_item_tax','tax'),"A.amount,B.description","A.tax_id=B.id AND A.invoice_id={$result->fields['id']}"));
					if($trs && $trs->RecordCount()) {
						while(!$trs->EOF) {
							$taxes["{$trs->fields['description']}"] += $trs->fields["amount"];
							$trs->MoveNext();
						}
						foreach($taxes as $txds=>$txamt)
						$smart[$i]["tax_arr"][] = Array('description'=>$txds, 'amount'=>$txamt);
					}
				}

				## Get the discount details
				if( !empty($result->fields['discount_amt']) ) {
					$drs = $db->Execute($sql=sqlSelect($db, 'invoice_item_discount',"amount,discount","invoice_id={$result->fields['id']}"));
					if($drs && $drs->RecordCount()) {
						while(!$drs->EOF) {
							$discounts["{$drs->fields['discount']}"] += $drs->fields["amount"];
							$drs->MoveNext();
						}
						$dhtml='';
						foreach($discounts as $dsds=>$dsamt) $dhtml .= '<a href=\'?_page=core:search&module=discount&discount_name='.$dsds.'\'>'.$dsds.'</a> - '. number_format($dsamt, 2) . "<br>";					
						$smart[$i]['discount_popup'] = $dhtml; 
						$dhtml='';
						foreach($discounts as $dsds=>$dsamt) $dhtml .= $dsds.' - '. number_format($dsamt, 2) . "<br>";					
						$smart[$i]['discount_popup_user'] = $dhtml; 
						
					}
				} 

				## Get the checkout plugin details:
				if(!empty($result->fields['checkout_plugin_data'])) {
					$plugin_data = unserialize($result->fields['checkout_plugin_data']);
					if(is_array($plugin_data)) {
						$smart[$i]['checkout_plugin_data'] = $plugin_data;
					} else {
						$smart[$i]['checkout_plugin_data'] = Array(0 => $result->fields['checkout_plugin_data']);
					}
				}

				## Get the line items:
				$q = "SELECT * FROM ".AGILE_DB_PREFIX."invoice_item WHERE
                                invoice_id          = ". $db->qstr($result->fields['id'])." AND
                                site_id           = ". $db->qstr(DEFAULT_SITE);
				if($C_list->is_installed('voip')) {
					$q .= " AND item_type!=5";
				}				
				$items = $db->Execute($q);
				if ($items === false) {
					global $C_debug;
					$C_debug->error('invoice.inc.php','view', $db->ErrorMsg());
					return false;
				}
				$ii =0;
				while ( !$items->EOF )
				{
					$smart_items[$ii] = $items->fields;

					// get the product attribs
					if(!empty($items->fields['product_attr']))
					{
						@$attrib = explode("\r\n", $items->fields['product_attr']);
						$js='';
						for($attr_i = 0; $attr_i < count( $attrib ); $attr_i++)
						{
							$attributei = explode('==', $attrib[$attr_i]);
							if(!empty($attributei[0]) && !empty($attributei[1])) {
								$js .= "<u>" . $attributei[0] . "</u> : ". $attributei[1] . " <BR>";
							}
						}
						$smart_items[$ii]['attribute_popup'] = $js;
					}

					// get the date range if set
					if(!empty($items->fields['date_start']) && !empty($items->fields['date_stop']))
					{
						$C_translate->value('invoice','start', date(UNIX_DATE_FORMAT,$items->fields['date_start']));
						$C_translate->value('invoice','stop', date(UNIX_DATE_FORMAT,$items->fields['date_stop']));
						$smart_items[$ii]['range'] = $C_translate->translate('recur_date_range','invoice','');
					}

					// Set charge type for payment option list:
					$any_new = true;
					if ($items->fields["price_type"] == '1' && !empty($result->fields['recurr_arr']) && is_array(unserialize($result->fields['recurr_arr'])))
					$any_recurring 	= true;			
					$items->MoveNext();
					$ii++;
				}

				## Create a summary (for duplicate skus w/identical price,and attributes, roll into a single value
				if($this->summarizeInvoice) {
					$tmp = $smart_items;
					unset($smart_items);
					$smart_items = $this->summarizeLineItems($tmp);
				}

				### GET THE CHECKOUT (PAYMENT) OPTIONS
				if($VAR['_page'] != 'invoice:view')
				{
					# get the converted amount due:
					if($result->fields['billed_currency_id'] != $result->fields['actual_billed_currency_id'])
					{
						global $C_list;
						$CURRENCY = $result->fields['actual_billed_currency_id'];
						if($result->fields['billed_amt'] <= 0)
						$total = $C_list->format_currency_decimal ($result->fields['total_amt'], $CURRENCY);
						else
						$total = $C_list->format_currency_decimal ($result->fields['total_amt'], $CURRENCY) - $result->fields['actual_billed_amt'];
					} else {
						$CURRENCY = $result->fields['billed_currency_id'];
						$total = $result->fields['total_amt']-$result->fields['billed_amt'];
					}

					$q  = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE site_id = ". DEFAULT_SITE ." AND active = 1";
					if($any_trial) 		$q .= " AND allow_trial		= ".$db->qstr('1');
					if($any_recurring) 	$q .= " AND allow_recurring	= ".$db->qstr('1');
					if($any_new) 		$q .= " AND allow_new		= ".$db->qstr('1');
					$chopt = $db->Execute($q);
					if ($chopt === false) {
						global $C_debug;
						$C_debug->error('invoice.inc.php','view', $db->ErrorMsg());
						return false;
					}
					if($chopt != false && $chopt->RecordCount() > 0) {
						while( !$chopt->EOF ) {
							$show = true;

							# Check that the cart total is not to high:
							if ( $chopt->fields["total_maximum"] != "" &&
							$result->fields['total_amt'] >= $chopt->fields["total_maximum"] ) {
								$show = false;
							}

							# Check that the cart total is not to low:
							if ( $chopt->fields["total_miniumum"] != "" &&
							$result->fields['total_amt'] <= $chopt->fields["total_miniumum"] ) {
								$show = false;
							}

							# Check that the group requirement is met:
							if ( $show && !empty ( $chopt->fields["required_groups"] ) ) {
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
							if ( $show && !empty ( $chopt->fields["excluded_products"] ) ) {
								$arr = unserialize ( $chopt->fields["excluded_products"] );
								if(count($arr) > 0)  {
									for($i=0; $i<count($smart_items); $i++)  {
										for($isk=0; $isk<count($arr); $isk++) {
											if($smart_items[$i]['product_id'] == $arr[$isk] && !empty($arr[$isk]) && !empty($smart_items[$i]['product_id']) ) {
												$show = false;
												$i=count($smart);
												$isk=count($arr);
											}
										}
									}
								}
							}


							$list_ord = 100;
							if ( $show ) {

								# Check if this method should be the default method:
								# By Amount:
								if ( !empty ( $chopt->fields["default_when_amount"] ) ) {
									$arr = unserialize ( $chopt->fields["default_when_amount"] );
									for ( $idx=0; $idx<count($arr); $idx++ ) {
										if ( $total >= $arr[$idx] ) $list_ord--;
										$idx=count($arr);
									}
								}

								# By Currency
								if ( !empty ( $chopt->fields["default_when_currency"] ) ) {
									$arr = unserialize ( $chopt->fields["default_when_currency"] );
									for ( $idx=0; $idx<count($arr); $idx++ ) {
										if ( $CURRENCY == $arr[$idx] ) $list_ord--;
										$idx=count($arr);
									}
								}

								# By Group
								if ( !empty ( $chopt->fields["default_when_group"] ) ) {
									$arr = unserialize ( $chopt->fields["default_when_group"] );
									global $C_auth;
									for ( $idx=0; $idx<count($arr); $idx++ ) {
										if ( $C_auth->auth_group_by_id( $arr[$idx] ) ) $list_ord--;
										$idx=count($arr);
									}
								}

								# By Country
								if ( !empty ( $chopt->fields["default_when_country"] ) ) {
									$arr = unserialize ( $chopt->fields["default_when_country"] );
									for ( $idx=0; $idx<count($arr); $idx++ ) {
										if ( $account->fields["country_id"] == $arr[$idx] ) $list_ord--;
										$idx=count($arr);
									}
								}

								# Add to the array
								$checkout_optionsx[] = Array ('sort'   => $list_ord,
								'fields' => $chopt->fields);
							}
							$chopt->MoveNext();
						}

						# Sort the checkout_options array by the [fields] element
						if(count($checkout_optionsx) > 0 ) {
							foreach ( $checkout_optionsx as $key => $row )
							$sort[$key] = $row["sort"];
							array_multisort ( $sort, SORT_ASC, $checkout_optionsx );
						}
					}
				}

				$result->MoveNext();
				$i++;
			}


			# get the result count:
			$results = $result->RecordCount();

			### No results:
			if($result->RecordCount() == 0)
			{
				global $C_debug;
				$C_debug->error("CORE:database.inc.php", "view()", "The selected record does not
                    exist any longer, or your account is not authorized to view it");
				return;
			}

			# define the DB vars as a Smarty accessible block
			global $smarty;

			# define the results
			$smarty->assign('cart', $smart_items);
			$smarty->assign($this->table, $smart);
			#$smarty->assign('results', 	$search->results);
			$smarty->assign('checkoutoptions', $checkout_optionsx);
		}
	}

	/** UPDATE
        */
	function update($VAR)
	{
		$this->invoice_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	/** DELETE
        */
	function delete($VAR)
	{
		$this->invoice_construct();
		$dbx = new CORE_database;
		$db = &DB();

		### Get the array
		if(isset($VAR["delete_id"]))
		$id = explode(',', $VAR["delete_id"]);
		elseif (isset($VAR["id"]))
		$id = explode(',', $VAR["id"]);

		### Load the service module
		include_once(PATH_MODULES.'service/service.inc.php');
		$service = new service;

		### Loop:
		for($i=0; $i<count($id); $i++)
		{
			### Loop through all services for this invoice and delete:
			$q = "SELECT * FROM  ".AGILE_DB_PREFIX."service WHERE
				        invoice_id  = ".$db->qstr( $id[$i]  )." AND
				        site_id     = ".$db->qstr(DEFAULT_SITE);;
			$rs = $db->Execute($q);
			if ($rs === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','delete', $db->ErrorMsg());
				return false;
			}
			if (@$rs->RecordCount() > 0) {
				while ( !$rs->EOF ) {
					$arr['id'] = $rs->fields['id'];
					$service->delete($arr, $service);
					$rs->MoveNext();
				}
			}

			### Delete the service record
			$arr['id'] = $id[$i];
			$this->associated_DELETE[] = Array ('table' => 'invoice_commission', 'field' => 'invoice_id');
			$this->associated_DELETE[] = Array ('table' => 'invoice_item',       'field' => 'invoice_id');
			$this->associated_DELETE[] = Array ('table' => 'invoice_memo',       'field' => 'invoice_id');
			$this->associated_DELETE[] = Array ('table' => 'service',	 		 'field' => 'invoice_id');
			$this->associated_DELETE[] = Array ('table' => 'invoice_item_tax',	 'field' => 'invoice_id');
			$this->associated_DELETE[] = Array ('table' => 'invoice_item_discount', 'field' => 'invoice_id');
			$dbx->mass_delete($arr, $this, "");
		}
	}

	/** SEARCH FORM
        */
	function search_form($VAR)
	{
		$this->invoice_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	/** SEARCH
        */
	function search($VAR)
	{
		$this->invoice_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		$db = &DB();

		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;

		# set the search criteria array
		$arr = $VAR;

		# convert invoice_discount_arr
		if(!empty($VAR['invoice_discount_arr']))
		$arr['invoice_discount_arr'] = '%"'.$VAR['invoice_discount_arr'].'"%';


		# loop through the submitted field_names to get the WHERE statement
		$where_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(preg_match('/'.$pat.'/', $key))
					{
						$field = preg_replace('/'.$pat.'/',"",$key);
						if(preg_match('/%/',$value))
						{
							# do any data conversion for this field (date, encrypt, etc...)
							if(isset($this->field["$field"]["convert"])  && $this->field["$field"]["convert"] != 'array')
							{
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
							}

							$where_list .= " WHERE ".AGILE_DB_PREFIX."invoice.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
							$i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
									if($value["$i_arr"] != '')
									{
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										$VAR['field_option']["$pat_field"]["$i_arr"];
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
											$f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];
											# error checking, safety precaution
											if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											$f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]) && $this->field["$field"]["convert"] != 'array')
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}


										if($i_arr == 0)
										{
											$where_list .= " WHERE ".AGILE_DB_PREFIX."invoice.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
											$where_list .= " AND ".AGILE_DB_PREFIX."invoice.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
									}
								}
							}
							else
							{
								$where_list .= " WHERE ".AGILE_DB_PREFIX."invoice.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
								$i++;
							}
						}
					}
				}
			}
			else
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(preg_match('/'.$pat.'/', $key))
					{
						$field = preg_replace('/'.$pat.'/',"",$key);
						if(preg_match('/%/',$value))
						{
							# do any data conversion for this field (date, encrypt, etc...)
							if(isset($this->field["$field"]["convert"])  && $this->field["$field"]["convert"] != 'array')
							{
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
							}

							$where_list .= " AND ".AGILE_DB_PREFIX."invoice.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
							$i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
									if($value["$i_arr"] != '')
									{
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
											$f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];

											# error checking, safety precaution
											if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											$f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]) && $this->field["$field"]["convert"] != 'array')
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}

										$where_list .= " AND ".AGILE_DB_PREFIX."invoice.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
									}
								}
							}
							else
							{
								$where_list .=  " AND ".AGILE_DB_PREFIX."invoice.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
								$i++;
							}
						}
					}
				}
			}
		}



		# Code for attribute searches:
		if(!empty($VAR['join_product_id']) && !empty($VAR['item_attributes']))
		{
			$attr_arr = $VAR['item_attributes'];
			for($ati=0; $ati<count($attr_arr); $ati++)
			{
				if(!empty($attr_arr[$ati]['0']))
				{
					if($where_list == '')
					$where_list .= ' WHERE ';
					else
					$where_list .= ' AND ';
					$where_list .= AGILE_DB_PREFIX."invoice_item.product_attr LIKE " .
					$db->qstr("%{$attr_arr[$ati]['0']}=={$attr_arr[$ati]['1']}%");
				}
			}
		}

		# get limit type
		if(isset($VAR['limit']))
		{
			$limit = $VAR['limit'];
		}
		else
		{
			$limit = $this->limit;
		}

		# get order by
		if(isset($VAR['order_by']))
		{
			$order_by = $VAR['order_by'];
		}
		else
		{
			$order_by = $this->order_by;
		}


		## SELECT FROM
		$p 		=		AGILE_DB_PREFIX;
		$q 		= 		"SELECT DISTINCT {$p}invoice.id FROM ".AGILE_DB_PREFIX."invoice ";
		$q_save = 		"SELECT DISTINCT %%fieldList%%,{$p}invoice.id FROM {$p}invoice ";

		## LEFT JOIN
		if( !empty($VAR['join_product_id']) || !empty($VAR['join_service_id']) ||
		!empty($VAR['join_domain_name']) || !empty($VAR['join_domain_tld']) ||
		!empty($VAR['join_memo_text']) )
		{
			# JOIN ON PRODUCT DETAILS:
			if(!empty($VAR['join_product_id']) || !empty($VAR['join_service_id']) || !empty($VAR['join_domain_name']) || !empty($VAR['join_domain_tld']))
			{
				$q .= 		" LEFT JOIN {$p}invoice_item ON {$p}invoice_item.invoice_id = {$p}invoice.id";
				$q_save .= 	" LEFT JOIN {$p}invoice_item ON {$p}invoice_item.invoice_id = {$p}invoice.id";

				if($where_list == '') {
					$q .= 		" WHERE {$p}invoice_item.site_id  = " . $db->qstr(DEFAULT_SITE);
					$q_save .= 	" WHERE {$p}invoice_item.site_id  = " . $db->qstr(DEFAULT_SITE);
				} else {
					$q .= 		$where_list ." AND {$p}invoice_item.site_id  = " . $db->qstr(DEFAULT_SITE);
					$q_save .= 	$where_list ." AND {$p}invoice_item.site_id  = " . $db->qstr(DEFAULT_SITE);
				}

				# AND (invoice_item.product_id)
				if(!empty($VAR['join_product_id'])) {
					$q .= 		" AND {$p}invoice_item.product_id = " . $db->qstr($VAR['join_product_id']);
					$q_save .=	" AND {$p}invoice_item.product_id = " . $db->qstr($VAR['join_product_id']);
				}

				# AND (invoice_item.service_id)
				if(!empty($VAR['join_service_id'])) {
					$q .= 		" AND {$p}invoice_item.service_id = " . $db->qstr($VAR['join_service_id']);
					$q_save .=	" AND {$p}invoice_item.service_id = " . $db->qstr($VAR['join_service_id']);
				}

				# AND (invoice_item.domain_name)
				if(!empty($VAR['join_domain_name'])) {
					if(!preg_match('/%/',$VAR['join_domain_name']) ) $qtype = ' = '; else $qtype = ' LIKE ';
					$q .= 		" AND {$p}invoice_item.domain_name $qtype " . $db->qstr($VAR['join_domain_name']);
					$q_save .=	" AND {$p}invoice_item.domain_name $qtype " . $db->qstr($VAR['join_domain_name']);

				}

				# AND (invoice_item.domain_tld)
				if(!empty($VAR['join_domain_tld'])) {
					if(!preg_match('/%/',$VAR['join_domain_tld']) ) $qtype = ' = '; else $qtype = ' LIKE ';
					$q .= 		" AND {$p}invoice_item.domain_tld $qtype " . $db->qstr($VAR['join_domain_tld']);
					$q_save .=	" AND {$p}invoice_item.domain_tld $qtype " . $db->qstr($VAR['join_domain_tld']);
				}
			}

			# JOIN ON MEMO TEXT:
			if(!empty($VAR['join_memo_text']))
			{
				$q .= 		" LEFT JOIN {$p}invoice_memo ON {$p}invoice_memo.invoice_id = {$p}invoice.id";
				$q_save .= 	" LEFT JOIN {$p}invoice_memo ON {$p}invoice_memo.invoice_id = {$p}invoice.id";

				if($where_list == '') {
					$q .= 		" WHERE {$p}invoice_memo.site_id  = " . $db->qstr(DEFAULT_SITE);
					$q_save .= 	" WHERE {$p}invoice_memo.site_id  = " . $db->qstr(DEFAULT_SITE);
				} else {
					$q .= 		$where_list ." AND {$p}invoice_memo.site_id  = " . $db->qstr(DEFAULT_SITE);
					$q_save .= 	$where_list ." AND {$p}invoice_memo.site_id  = " . $db->qstr(DEFAULT_SITE);
				}

				$q .= 		" AND {$p}invoice_memo.memo LIKE " . $db->qstr('%'. $VAR['join_memo_text'] .'%');
				$q_save .=	" AND {$p}invoice_memo.memo LIKE " . $db->qstr('%'. $VAR['join_memo_text'] .'%');
			}

			$q .= " AND {$p}invoice.site_id = ". DEFAULT_SITE;
			$q_save .=  ' AND ';
		}
		else
		{
			if($where_list == '') {
				$q .= "WHERE {$p}invoice.site_id = ". DEFAULT_SITE;
				$q_save .=  ' WHERE ';
			}
			else
			{
				$q .= $where_list . " AND {$p}invoice.site_id = ". DEFAULT_SITE;
				$q_save .= $where_list . ' AND ';
			}
		}


		///////////////// debug
		#echo $q;
		#exit;

		# run the database query
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('invoice.inc.php','search', $db->ErrorMsg());
			return false;
		}

		# get the result count:
		$results = $result->RecordCount();

		# get the first record id:
		if($results == 1)  $record_id = $result->fields['id'];

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# Create the definition for fast-forwarding to a single record:
		if ($results == 1 && !isset($this->fast_forward))
		{
			$smarty->assign('record_id', $record_id);
		}

		# create the search record:
		if($results > 0)
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module'] 	= $this->module;
			$arr['sql']		= $q_save;
			$arr['limit']  	= $limit;
			$arr['order_by']= $order_by;
			$arr['results']	= $results;
			$search->add($arr);

			# define the search id and other parameters for Smarty
			$smarty->assign('search_id', $search->id);

			# page:
			$smarty->assign('page', '1');

			# limit:
			$smarty->assign('limit', $limit);

			# order_by:
			$smarty->assign('order_by', $order_by);
		}

		# define the result count
		$smarty->assign('results', $results);
	}

	/** SEARCH SHOW
        */
	function search_show($VAR)
	{
		$this->invoice_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		# set the field list for this method:
		$arr = $this->method[$type];
		$field_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= AGILE_DB_PREFIX . "invoice" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "invoice" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			$i++;
		}


		# get the search details:
		if(isset($VAR['search_id'])) {
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		} else {
			# invalid search!
			echo '<BR> The search terms submitted were invalid!';       # translate... # alert

			if(isset($this->trigger["$type"])) {
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "") {
			$order_by = ' ORDER BY ' . $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . $this->order_by;
			$smarty_order =  $search->order_by;
		}


		# determine the sort order
		if(isset($VAR['desc'])) {
			$order_by .= ' DESC';
			$smarty_sort = 'desc=';
		} else if(isset($VAR['asc']))  {
			$order_by .= ' ASC';
			$smarty_sort = 'asc=';
		} else {
			if (!preg_match('/date/i',$smarty_order)) {
				$order_by .= ' ASC';
				$smarty_sort = 'asc=';
			} else {
				$order_by .= ' DESC';
				$smarty_sort = 'desc=';
			}
		}


		# determine the offset & limit
		$current_page=1;
		$offset=-1;
		if (!empty($VAR['page'])) $current_page = $VAR['page'];
		if (empty($search->limit)) $search->limit=25; 
        if($current_page>1) $offset = (($current_page * $search->limit) - $search->limit);            
		 	  
		# generate the full query
		$db = &DB();
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . "invoice.site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;

		//////////////////
		#echo "<BR> $q <BR>";

		$result = $db->SelectLimit($q, $search->limit, $offset);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('invoice.inc.php','search', $db->ErrorMsg());

			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
			return;
		}


		# put the results into a smarty accessable array
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF) {
			$smart[$i] = $result->fields;
			$amount += $result->fields['total_amt'];
			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}

			$result->MoveNext();
			$i++;
		}


		# get any linked fields
		if($i > 0)
		{
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($smart, $this->linked);
		}
		else
		{
			$this->result = $smart;
		}

		# get the result count:
		$results = $result->RecordCount();

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# define the results
		$smarty->assign($this->table, $this->result);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	$smarty_order);
		$smarty->assign('sort',		$smarty_sort);
		$smarty->assign('limit',	$search->limit);
		$smarty->assign('search_id',$search->id);
		$smarty->assign('results', 	$search->results);

		global $C_list;
		$smarty->assign('total_amount', $C_list->format_currency($amount,DEFAULT_CURRENCY));

		# get the total pages for this search:
		if(empty($search->limit))
		$this->pages = 1;
		else
		$this->pages = intval($search->results / $search->limit);
		if ($search->results % $search->limit) $this->pages++;

		# total pages
		$smarty->assign('pages', 	$this->pages);

		# current page
		$smarty->assign('page', 	$current_page);
		$page_arr = '';
		for($i=0; $i <= $this->pages; $i++)
		{
			if ($this->page != $i) 	$page_arr[] = $i;
		}

		# page array for menu
		$smarty->assign('page_arr',	$page_arr);
	}

	/** USER SEARCH
        */ 
	function user_search($VAR)
	{
		if(!SESS_LOGGED) return false; 
		$VAR['invoice_account_id'] = SESS_ACCOUNT;
		$this->invoice_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	/** USER SEARCH SHOW
        */
	function user_search_show($VAR)
	{
		if(!SESS_LOGGED) return false;
		$this->invoice_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}

	/** USER VIEW
        */
	function user_view($VAR)
	{
		global $C_auth;
		if(!SESS_LOGGED) return false;

		// verify the account_id for this order is the SESS_ACCOUNT
		if ( $C_auth->auth_method_by_name('invoice','view') == false)
		{
			$id = explode(',',$VAR['id']);
			$db = &DB();
			$q = "SELECT account_id FROM ".AGILE_DB_PREFIX."invoice WHERE
	        			id = ".$db->qstr($id[0])." AND
	        			site_id = ".$db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($q);
			if ($rs === false) {
				global $C_debug;
				$C_debug->error('invoice.inc.php','user_view', $db->ErrorMsg());
				return false;
			}
			if ($rs->fields['account_id'] != SESS_ACCOUNT) return false;
		}
		$this->view($VAR, $this);
	}

	/** SEARCH EXPORT
        */    	
	function search_export($VAR)
	{
		$this->invoice_construct();

		# require the export class
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
			$type = "export_pdf";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->pdf_invoice($VAR, $this, $type);
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

	function invoice_construct()
	{
		$this->module = "invoice";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
		include_once(PATH_CORE.'xml.inc.php');
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
