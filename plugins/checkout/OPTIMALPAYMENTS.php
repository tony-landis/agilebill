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

class plg_chout_OPTIMALPAYMENTS extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_OPTIMALPAYMENTS($checkout_id=false) {


		$this->name 		= 'OPTIMALPAYMENTS';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD','CAD','GBP');
		$this->success_url  = URL . '?_page=invoice:thankyou';
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
		$country = $this->getCountry('two_code', $acct_fields["country_id"]);

		# Test Transaction
		if($this->cfg['mode'] != "1") {
			$this->host = 'realtime.test.firepay.com';
			$this->url = '/servlet/DPServlet';
		} else {
			$this->host = 'realtime.firepay.com';
			$this->url = '/servlet/DPServlet';
		}

		if(empty($this->billing["ccv"]))
		$cvdind = 0;
		else
		$cvdind = 1;

		switch (@$this->billing["card_type"]) {
			case 'visa':
			$ctype = 'VI'; break;
			case 'mc':
			$ctype = 'MC'; break;
			case 'amex':
			$ctype = 'AM'; break;
			case 'discover':
			$ctype = 'DI'; break;
			case 'delta':
			$ctype = ''; break;
			case 'solo':
			$ctype = 'SO'; break;
			case 'switch':
			$ctype = 'SW'; break;
			case 'jcb':
			$ctype = ''; break;
			case 'diners':
			$ctype = 'DC'; break;
			case 'carteblanche':
			$ctype = ''; break;
			case 'enroute':
			$ctype = ''; break;
		}

		# Set the post vars:
		$vars = Array (
		Array ('account', 			$this->cfg['account']),
		Array ('merchantTxn',		$invoice . microtime()),
		Array ('merchantId',		$this->cfg['merchantId']),
		Array ('merchantPwd',		$this->cfg['merchantPwd']),
		Array ('merchantData',		"AgileBill Invoice Id : " . $invoice),
		Array ('amount',			$amount * 100),
		Array ('cardNumber',		$this->billing["cc_no"]),
		Array ('cardExp',			$this->billing["exp_month"] . '/' . $this->billing["exp_year"]),
		Array ('cvdIndicator',		$cvdind),
		Array ('cvdValue',			$this->billing["ccv"]),
		Array ('cardType',			$ctype),
		Array ('operation',			'P'),
		Array ('clientVersion',		'1.1'),
		Array ('custName1',			$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('StreetAddr',		$this->account["address1"]),
		Array ('StreetAddr2',		$this->account["address2"]),
		Array ('email',				$acct_fields["email"]),
		Array ('city',				$this->account["city"]),
		Array ('province',			$this->account["state"]),
		Array ('zip',				$this->account["zip"]),
		Array ('country',			$country),
		Array ('phone',			    '18885551212')
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);


		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			$response = ereg_replace('\+', ' ', $response);
			$response = explode('&', $response);
			foreach($response as $var)
			$item[] = explode("=", $var);
			foreach($item as $var)
			$$var[0] = $var[1];
		}

		/*
		if($this->cfg['mode'] != "1") {
		print_r($response);
		print_r($item);
		exit;
		}
		*/


		# Transaction Status:
		if (@$status == 'SP')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		@$ret['transaction_id'] = @$authCode;

		# Message:
		@$ret['msg'] = @$avsInfo;
 
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