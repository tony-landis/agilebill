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

class plg_chout_SECPAY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_SECPAY ($checkout_id=false) {


		$this->name 		= 'SECPAY';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'www.secpay.com';
		$this->url			= '/java-bin/ValCard';
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

		# Get the data submitted from the customer:
		$billing = @$VAR['checkout_plugin_data'];

		# Test mode:
		if ( $this->cfg['mode'] )
		$test = 'false';
		else
		$test = 'true';

		# Set the post vars:
		$vars = Array (
		Array ('merchant', 				$this->cfg['account']),
		Array ('options', 				"dups=false,test_status=".$test),
		Array ('currency',				$currency_iso),
		Array ('amount', 				$amount),
		Array ('trans_id', 				$invoice),
		Array ('cardtype',				$this->billing["card_type"]),
		Array ('card_no', 				$this->billing["cc_no"]),
		Array ('expiry', 				$this->billing["exp_month"] .    '/'.$this->billing["exp_year"]),
		Array ('customer', 				$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('shipping', 				$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('bill_name', 			$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('bill_addr_1',			$this->account["address1"] . ' '.$this->account["address2"]),
		Array ('bill_post_code',		$this->account["city"]),
		Array ('bill_city',				$this->account["state"]),
		Array ('bill_state',			$this->account["zip"]),
		Array ('bill_email',			$acct_fields["email"]),
		Array ('bill_country',			$country)
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);


		# Get return response
		if(!$response)
		return false;
		else
		$respond = explode('&', $response);

		for ($i=0; $i<count($respond); $i++) {
			@$arr1 = explode('=', $respond[$i]);
			@$response1[urldecode($arr1[0])] = urldecode($arr1[1]);
		}

		# Transaction Status:
		if (trim($response1['?valid']) == 'true')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		$ret['transaction_id'] = $response1["trans_id"];
		$ret['authorization_id'] = $response1["auth_code"];

		# Message:
		$ret['msg'] = $response1['message'];

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