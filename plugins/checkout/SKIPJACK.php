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

class plg_chout_SKIPJACK extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_SKIPJACK($checkout_id=false) {


		$this->name 		= 'SKIPJACK';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->url 			= '/scripts/evolvcc.dll';
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
		$country = $this->getCountry('three_code', $acct_fields["country_id"]);

		# Get the data submitted from the customer:
		$billing = @$VAR['checkout_plugin_data'];

		if($this->cfg['mode'] != '100') {
			define("SJPHPAPI_ROOT_URL", "https://developer.skipjackic.com"); // test
		} else {
			define("SJPHPAPI_ROOT_URL", "https://www.skipjackic.com"); // production
		}

		require_once (PATH_PLUGINS . 'checkout/CLASS_SKIPJACK/skipjack.php');
		$request = array(
		"sjname" => $this->account["first_name"],
		"Email" => $acct_fields["email"],
		"Streetaddress" => $this->account["address1"],
		"City" => $this->account["city"],
		"State" => $this->account["state"],
		"Zipcode" => $this->account["zip"],
		"Country" => $country,
		"Ordernumber" => $invoice,
		"Accountnumber" => $this->billing["cc_no"],
		"Month" => $this->billing["exp_month"],
		"Year" => "20".$this->billing["exp_year"],
		"Serialnumber" => $this->cfg['account'],  // html Vital, NBova or production
		"Transactionamount" => round($amount,2),
		"Orderstring" => "1~NotDefined~NotDefined~1~N||",
		"Shiptophone" => "888-555-1212");


		$response = SkipJack_Authorize($request);

		/*
		echo "<pre>";
		var_dump($request);


		var_dump($response);
		echo "</pre>";
		exit;
		*/

		# Transaction Status:
		if ($response['szIsApproved'] == '1')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		$ret['transaction_id']   = $response['szAuthorizationResponseCode'];
		$ret['authorization_id'] = $response['szAuthorizationResponseCode'];

		# Message:
		$ret['msg'] = $response['textReturnCode'];

		# AVS Details:
		$ret['avs'] =  $response['szAVSResponseCode'];
 
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