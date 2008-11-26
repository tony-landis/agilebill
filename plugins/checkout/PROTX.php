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

class plg_chout_PROTX extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_PROTX($checkout_id=false) { 
		$this->name 		= 'PROTX';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD','GBP');
		$this->host			= 'ukvps.protx.com';
		$this->url			= '/VPSDirectAuth/PaymentGateway.asp';
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}
 
	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Get the submitted billing details:
		global $VAR;
		@$billing = $VAR['checkout_plugin_data'];

		# Validate the currency:
		if(!$this->validate_currency($currency_iso)) return false;

		# validate the card type and number, and exp date: 
		$ret=false;
		if(!$this->validate_card_details($ret)) return false;
				 
		# Get the country name:
		$country = $this->getCountry('name', $this->account["country_id"]);

		# Get the data submitted from the customer:
		$billing = @$VAR['checkout_plugin_data'];

		# Set the post vars:
		$vars = Array (
		Array ('Vendor', 				$this->cfg['account']),
		Array ('VPSProtocol', 			"2.20"),
		Array ('TxType', 				"PAYMENT"),
		Array ('Description',			"Payment For Invoice ".$invoice),
		Array ('Currency',				$currency_iso),
		Array ('Amount', 				$amount),
		Array ('VendorTxCode', 			$invoice),
		Array ('CardType',				$this->billing["card_type"]),
		Array ('CV2', 					$this->billing["ccv"]),
		Array ('CardNumber', 			$this->billing["cc_no"]),
		Array ('ExpiryDate', 			$this->billing["exp_month"] . '' . $this->billing["exp_year"]),
		Array ('CardHolder', 			$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('ClientNumber',			$acct_fields["id"] ),
		Array ('Address', 				$this->account["address1"] . ' ' . $this->account["address2"]),
		Array ('PostCode', 				$this->account["zip"])
		);
 
		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);
		$retval="\r\n";

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else {
			$respond = explode($retval, $response);
		}

		for ($i=0; $i<count($respond); $i++) {
			@$arr1 = explode('=', $respond[$i]);
			@$response1[urldecode($arr1[0])] = urldecode($arr1[1]);
		}

		/*
		echo $response1['StatusDetail'];
		print_r($response1);
		exit;
		*/

		# Transaction Status:
		if (trim($response1['Status']) == 'OK')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		$ret['transaction_id'] = $response1["TxAuthNo"];

		# Message:
		$ret['msg'] = $response1['StatusDetail'];

		# AVS Details:
		$ret['avs'] = $response1["AVSCV2"];
		 
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