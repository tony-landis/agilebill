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

class plg_chout_IBILL extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_IBILL($checkout_id=false) {
		$this->name 		= 'IBILL';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'secure.ibill.com';
		$this->url			= '/cgi-win/ccard/tpcard.exe';
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
		$test = "FALSE";
		else
		$test = "TRUE";

		# Set the post vars:
		$vars = Array (
		Array ('reqtype', 		"authorize"),
		Array ('saletype', 		"sale"),
		Array ('account',		$this->cfg['account']),
		Array ('password', 		$this->cfg['password']),
		Array ('amount', 		$amount),
		Array ('crefnum', 		$invoice),
		Array ('cardnum', 		$this->billing["cc_no"]),
		Array ('cardexp', 		$this->billing["exp_month"] . '' . $this->billing["exp_year"]),
		Array ('noc', 			$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('address1', 		$this->account["address1"] . ' ' . $this->account["address2"]),
		Array ('zipcode',		$this->account["zip"]) 
		); 

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);

		# Get return response
		if(!$response)
		return false;
		else
		$response = explode(',', $response);

		# Transaction Status:
		if ($response[0] == 'authorized')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Auth code:
		$ret['authorization_id'] = $response[1];

		# Transaction ID:
		$ret['transaction_id'] = $response[4];

		# Message:
		$ret['msg'] = $response[1];

		# AVS Details:
		if ( $response[6] == 'N' )
		$ret['avs'] = 'avs_no_match';
		elseif ( $response[6] == 'X' )
		$ret['avs'] = 'avs_na';
		elseif ( $response[6] == 'Y' )
		$ret['avs'] = 'avs_address_zip';
		else
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