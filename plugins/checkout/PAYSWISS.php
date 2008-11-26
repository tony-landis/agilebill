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
	
include_once(PATH_MODULES.'checkout/base_checkout_plugin.class.php');

# Main Class
class plg_chout_PAYSWISS extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_PAYSWISS($checkout_id=false) {
		$this->name 		= 'PAYSWISS';
		$this->type         = 'redirect';
		$this->recurr_only  = false;
		$this->support_cur  = Array ('USD');
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		if(!$this->validate_currency($currency_iso)) return false;
 
		/*
		· action   - use "subscription" if this product is subscription
		use "payment" if this is simple payment transaction
		· period   - subscription rebilling period, days
		· trial    - trial period, days
		· setup    - setup fee, $
		*/
		$url =    "https://www.payswiss.com/process.php";
		$vals = Array (
		Array ('username', 			$this->cfg['email']),
		Array ('action',            'payment'),
		Array ('product', 		    SITE_NAME. ' - Invoice # '.$invoice),
		Array ('price', 			$amount),
		Array ('action',            'payment'),
		Array ('quantity', 			'1'),
		Array ('ucancel', 	        $this->decline_url.$invoice),
		Array ('unotify',		    $this->return_url),
		Array ('ureturn',		    $this->success_url.$invoice)
		); 
		
		$this->post_vars($url, $vals);
		return true;
	}

	# Stores new billing details, & return account_billing_id (gateway only)
	function store_billing($VAR) {
		return 0;
	}

	# Perform a transaction for an (new invoice):
	function bill_invoice($VAR)   {
		return true;
	}

	# Issue a refund for a paid invoice (captured charges w/gateway)
	function refund($VAR) {
		return true;
	}

	# Void a authorized charge (gateways only)
	function void($VAR)  {
		return true;
	}
}
?>