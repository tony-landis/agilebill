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

class plg_chout_NETBILLING extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_NETBILLING($checkout_id=false) {

		$this->name 		= 'NETBILLING';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'secure.netbilling.com';
		$this->url			= '/gw/native/direct2.1';
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
		Array ('GEN_DESCRIPTION', 			"Payment For Invoice ".$invoice),
		Array ('GEN_TRANS_TYPE', 			"SALE"),
		Array ('GEN_PAYMENT_TYPE', 			"C"),
		Array ('GEN_ACCOUNT', 				$this->cfg['account']),
		Array ('GEN_SITETAG', 				$this->cfg['sitetag']),
		Array ('CARD_NUMBER', 				$this->billing["cc_no"]),
		Array ('CARD_EXPIRE', 				$this->billing["exp_month"] . '/' . $this->billing["exp_year"]),
		Array ('CVV2', 						$this->billing["ccv"]),
		Array ('GEN_AMOUNT', 				$amount),
		Array ('CUST_NAME1', 				$this->account["first_name"]),
		Array ('CUST_NAME2',				$this->account["last_name"]),
		Array ('CUST_ADDR_STREET', 			$this->account["address1"].' '.$this->account["address2"]),
		Array ('CUST_ADDR_CITY', 			$this->account["city"]),
		Array ('CUST_ADDR_STATE', 			$this->account["state"]),
		Array ('CUST_ADDR_ZIP', 			$this->account["zip"]),
		Array ('CUST_ADDR_COUNTRY', 		$country),
		Array ('CUST_EMAIL', 				$acct_fields["email"]),
		Array ('CUST_IP', 					USER_IP)
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);

		# Transaction Status:
		if (eregi("RET_STATUS=1", $response)) {
			$ret['status'] = 1;
		} elseif (eregi("RET_STATUS=0",$response)) {
			$ret['status'] = 0;
			$mydata = explode("\&",$response);
			foreach($mydata as $key=>$value)
			{
				$newdata = explode('=', $value);
				$ret[$newdata[0]] = $newdata[1];
			}
			$reason = urldecode($ret['RET_AUTH_MSG']);
			$ret['msg'] = @$reason;
		} else {
			$ret['status'] = 0;
			$ret['msg'] = 'SORRY, THE TRANSACTION FAILED';
		}

		# Transaction ID:
		$ret['transaction_id']   = 0;

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