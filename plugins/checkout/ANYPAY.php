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

class plg_chout_ANYPAY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_ANYPAY($checkout_id=false) {

		$this->name 		= 'ANYPAY';
		$this->type 		= 'redirect'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->return_url 	= SSL_URL . 'includes/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou';
		$this->decline_url  = URL . '?_page=invoice:decline';
		$this->support_cur  = Array ('USD');
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Validate the currency:
		if(!$this->validate_currency($currency_iso)) return false;

		$url =	"https://secure.anypay.com/apmain";
		$vals = Array (
		Array ('interfaceId', 		'LinkPaymentAccept'),
		Array ('langCode', 		    strtolower(SESS_CURRENCY)),
		Array ('.email', 			$this->cfg['email']),
		Array ('.description', 		"Payment For Invoice Id. ".$invoice),
		Array ('.refUser', 			$invoice),
		Array ('.amount', 			$amount), 
		Array ('.clearingMode', 	'1'),
		Array ('.quantityEdit', 	"false")
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