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

class plg_chout_VERISIGN_PFPRO extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_VERISIGN_PFPRO($checkout_id=false) {


		$this->name 		= 'VERISIGN_PFPRO';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		//putenv("PFPRO_CERT_PATH=/path/to/certs/");	// set if you get "Verisign response code was -31
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}


	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Validate currency
		if(!$this->validate_currency($currency_iso)) return false;
 
		$ret=false;
		if(!$this->validate_card_details($ret)) return false;

		# Get the country
		$country = $this->getCountry('name', $this->account["country_id"]);

		# Test Transaction
		if($this->cfg['mode'] = "0")
		$host = 'test-payflow.verisign.com';
		else
		$host = 'payflow.verisign.com';

		# Set the post vars:
		$vars = Array (
		'TENDER' 	=>	"C",
		'TRXTYPE' 	=>	$this->cfg['type'],
		'USER'		=>	$this->cfg['user'],
		'PWD' 		=>	$this->cfg['password'],
		'PARTNER' 	=>	$this->cfg['partner'],
		'AMT' 		=>	$amount,
		'ACCT'	 	=>	$this->billing["cc_no"],
		'EXPDATE' 	=>	$this->billing["exp_month"] . '' . $this->billing["exp_year"],
		'CCV2'   	=> 	$this->billing["ccv"],
		'STREET' 	=>	ereg_replace("'", "", $this->account["address1"] . ' ' . $this->account["address2"]),
		'CITY' 		=>	$this->account["city"],
		'STATE' 	=>	$this->account["state"],
		'ZIP' 		=>	$this->account["zip"],
		'INVNUM' 	=> 	$invoice,
		'COMMENT1'	=>  "AB Invoice # $invoice for {$this->account["first_name"]} {$this->account["last_name"]}",
		'FIRSTNAME' =>  $this->account["first_name"],
		'LASTNAME'  =>  $this->account["last_name"],
		'NAME'		=>  $this->account["first_name"] . ' ' . $this->account["last_name"],
		'EMAIL'		=>  $acct_fields["email"] 
		);


		# Create the SSL connection & get response from the gateway:
		pfpro_init();
		$response = pfpro_process($vars, $host );
		pfpro_cleanup();


		# Transaction Status:
		if ($response['RESULT'] == '0')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		@$ret['transaction_id']   = $response['PNREF'];
		@$ret['authorization_id'] = $response['AUTHCODE'];

		# Message:
		$ret['msg'] = $response['RESPMSG'];

		# AVS Details:
		$ret['avs'] = 'avs_na';
 
		if($ret['status'] == 1) {
			return $ret;
		} else {
			global $VAR;
			@$VAR['msg']=$ret["msg"];
			return false;
		}
	}

	# Stores new billing details, & return account_billing_id (gateway only)
	function store_billing($VAR, $account=SESS_ACCOUNT) {
		return $this->saveCreditCardDetails($VAR);
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