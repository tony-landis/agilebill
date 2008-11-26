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

class plg_chout_EPROCESSING_NETWORK extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_EPROCESSING_NETWORK($checkout_id=false) {
		$this->name 		= 'EPROCESSING_NETWORK';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'www.eprocessingnetwork.com';
		$this->url			= '/cgi-bin/an/order.pl';
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
		Array ('x_ADC_URL', 			"FALSE"),
		Array ('x_ADC_Delim_Data', 		"TRUE"),
		Array ('x_Version', 			"3.0"),
		Array ('x_Version', 			"3.0"),
		Array ('x_Currency_Code',		$currency_iso),
		Array ('x_Method', 				"CC"),
		Array ('x_Transaction_Type',	$this->cfg['x_Transaction_Type']),
		Array ('x_Test_Request',		$test),
		Array ('x_Password', 			$this->cfg['x_Password']),
		Array ('x_Login', 				$this->cfg['x_Login']),
		Array ('x_Amount', 				$amount),
		Array ('x_Invoice_Num', 		$invoice),
		Array ('x_Description', 		"Payment for Invoice No. ".$invoice),
		Array ('x_Card_Code', 			$this->billing["ccv"]),
		Array ('x_Card_Num', 			$this->billing["cc_no"]),
		Array ('x_Exp_Date', 			$this->billing["exp_month"] . '/' . $this->billing["exp_year"]),
		Array ('x_Cust_ID', 			$acct_fields["id"]),
		Array ('x_First_Name', 			$this->account["first_name"]),
		Array ('x_Last_Name', 			$this->account["last_name"]),
		Array ('x_Company', 			$this->account["company"]),
		Array ('x_Address', 			$this->account["address1"] . ' ' . $this->account["address2"]),
		Array ('x_City', 				$this->account["city"]),
		Array ('x_State', 				$this->account["state"]),
		Array ('x_Zip', 				$this->account["zip"]),
		Array ('x_Email', 				$acct_fields["email"]),
		Array ('x_Country', 			$country),
		Array ('x_Ship_To_First_Name', 	$this->account["first_name"]),
		Array ('x_Ship_To_Last_Name', 	$this->account["last_name"]),
		Array ('x_Ship_To_Company', 	$this->account["company"]),
		Array ('x_Ship_To_Address', 	$this->account["address1"] . ' ' . $this->account["address2"]),
		Array ('x_Ship_To_City', 		$this->account["city"]),
		Array ('x_Ship_To_State', 		$this->account["state"]),
		Array ('x_Ship_To_Zip', 		$this->account["zip"]),
		Array ('x_Ship_To_Country', 	$country),
		Array ('x_Customer_IP', 		USER_IP)
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
		if ($response[0] == '1')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		$ret['avs'] = $response[4];

		# Message:
		$ret['msg'] = $response[3];

		# AVS Details:
		if ( $response[5] == 'A' )
		$ret['avs'] = 'avs_address_only';
		elseif ( $response[5] == 'E' )
		$ret['avs'] = 'avs_error';
		elseif ( $response[5] == 'N' )
		$ret['avs'] = 'avs_no_match';
		elseif ( $response[5] == 'P' )
		$ret['avs'] = 'avs_na';
		elseif ( $response[5] == 'R' )
		$ret['avs'] = 'avs_retry';
		elseif ( $response[5] == 'S' )
		$ret['avs'] = 'avs_not_supported';
		elseif ( $response[5] == 'U' )
		$ret['avs'] = 'avs_address_unavail';
		elseif ( $response[5] == 'W' )
		$ret['avs'] = 'avs_fullzip_only';
		elseif ( $response[5] == 'X' )
		$ret['avs'] = 'avs_exact';
		elseif ( $response[5] == 'Y' )
		$ret['avs'] = 'avs_address_zip';
		elseif ( $response[5] == 'Z' )
		$ret['avs'] = 'avs_partzip_only';
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