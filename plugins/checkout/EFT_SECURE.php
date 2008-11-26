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

class plg_chout_EFT_SECURE extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_EFT_SECURE($checkout_id=false) { 
		$this->name 		= 'EFT_SECURE';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'va.eftsecure.net';
		$this->url			= '/cgi-bin/eftBankcard.dll?transaction';
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

		# Set the post vars:
		$vars = Array (
		Array ('M_id', 					$this->cfg['M_id']),
		Array ('M_key', 				$this->cfg['M_key']),
		Array ('C_name', 				$this->account["first_name"] . ' ' . $this->account["last_name"]),
		Array ('C_address', 			$this->account["address1"] . ' ' . $this->account["address2"]),
		Array ('C_city',				$this->account["city"]),
		Array ('C_state', 				$this->account["state"]),
		Array ('C_zip',					$this->account["zip"]),
		Array ('C_country',				$country),
		Array ('C_email', 				$acct_fields["email"]),
		Array ('C_cardnumer', 			$this->billing["cc_no"]),
		Array ('C_exp', 				$this->billing["exp_month"] . $this->billing["exp_year"]),
		Array ('C_ccv', 				$this->billing["ccv"]),
		Array ('T_amt', 				$amount),
		Array ('T_code', 				"01"),
		Array ('T_ordernum', 			$invoice)
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		echo $response = $n->connect($this->host, $this->url, $vars, true, 1);

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		}

		# Transaction Status:
		if ($response{1} == 'A')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# AVS
		@$ret['avs'] = @$response{45};

		# Message:
		$ret['msg'] = $response{2}.$response{3}.$response{4}.$response{5}.$response{6}.$response{7}. " - ";
		for($i=8; $i<=39; $i++)
		$ret['msg'] .= 	$response{$i};
	 		 
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