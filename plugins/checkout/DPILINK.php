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

class plg_chout_DPILINK extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_DPILINK($checkout_id=false) {
		$this->name 		= 'DPILINK';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host 		= 'www.dpisecure.com';
		$this->url 			= '/dpilink/authpd.asp';
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
 
		# Test Transaction
		if($this->cfg['mode'] = "1")
		$test = "Y";
		else
		$test = "N";

		# Set the post vars:
		$vars = Array(
		Array ( "testTransaction",		$test ),
		Array ( "transactionCode", 		$this->cfg['type']),
		Array ( "DPIAccountNum",		$this->cfg['account'] ),
		Array ( "password",				$this->cfg['password'] ),
		Array ( "cardHolderName", 		$this->account["first_name"].''.$this->account["last_name"]),
		Array ( "cardHolderEmail" , 	$this->account["email"] ),
		Array ( "cardHolderAddress", 	$this->account["address1"] ),
		Array ( "cardHolderCity" ,		$this->account["city"] ),
		Array ( "cardHolderState" , 	$this->account["state"] ),
		Array ( "cardHolderZip" ,		$this->account["zip"] ),
		Array ( "customerNume" ,		$acct_fields["id"] ),
		Array ( "orderNum" ,			$invoice ),
		Array ( "cardAccountNum" ,		$this->billing["cc_no"] ),
		Array ( "expirationDate" ,		$this->billing["exp_month"].''.$this->billing["exp_year"] ),
		Array ( "CVV2" ,				$this->billing["ccv"]),
		Array ( "Serialnumber" , 		$this->cfg['account'] ),
		Array ( "transactionAmount", 	$amount )
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$results = $n->connect($this->host, $this->url, $vars, true, 1);

		$response = explode('|', $results);

		# Transaction Status:
		if ($response[10] == '00') {
			$ret['status'] = 1;
		} else {
			$ret['status'] = 0;
			$ret['msg'] = 'The details you provided are invalid or declined.';
		}

		# Transaction ID:
		$ret['transaction_id']   = $response[14];
		$ret['authorization_id'] = $response[13];

		# AVS Details:
		$ret['avs'] =  $response[22];
		 
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