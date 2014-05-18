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

class plg_chout_BLUEPAY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_BLUEPAY($checkout_id=false) { 
		$this->name 		= 'BLUEPAY';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->success_url  = URL . '?_page=invoice:thankyou';
		$this->host			= 'secure.bluepay.com';
		$this->url			= '/interfaces/bp10emu';
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
 
		if($this->cfg['mode'])
		$mode = 'LIVE';
		else
		$mode = 'TEST';

		# Set the post vars:
		$vars = Array (
		Array ('TAMPER_PROOF_SEAL',	md5($this->cfg['secret'] . $this->cfg['account'] . $this->cfg['type'] . $amount . $mode)),
		Array ('MODE', 				$mode),
		Array ('MERCHANT', 			$this->cfg['account']),
		Array ('TRANSACTION_TYPE', 	$this->cfg['type']),
		Array ('AMOUNT', 			$amount),
		Array ('AMOUNT_TAX', 		0),
		Array ('AMOUNT_TYPE', 		0),
		Array ('COMMENT', 			''),
		Array ('PHONE', 			'18885551212'),
		Array ('CC_NUM', 			$this->billing["cc_no"]),
		Array ('CC_EXPIRES', 		$this->billing["exp_month"] . '/' . $this->billing["exp_year"]),
		Array ('CVCCVV2', 			$this->billing["ccv"]),
		Array ('ORDER_ID', 			$invoice),
		Array ('INVOICE_ID', 		$invoice),
		Array ('NAME', 				$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('ADDR1', 			$this->account["address1"]),
		Array ('ADDR2', 			$this->account["address2"]),
		Array ('CITY', 				$this->account["city"]),
		Array ('STATE', 			$this->account["state"]),
		Array ('ZIPCODE', 			$this->account["zip"]),
		Array ('EMAIL', 			$acct_fields["email"])
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);
 
		# Transaction Status:
		if (preg_match("/Result=APPROVED/i", $response)) {
			$ret['status'] = 1;
			$ret['msg'] = 'Approved!';
		} else {
			$ret['status'] = 0;
			$ret['msg'] = $response;
		}

		# Transaction ID:
		$tran = explode('ApprovalCode=', $response);
		$tran1= explode('&', $response);
		$ret['transaction_id']   = @$tran1[0];

		# AVS Details:
		$tran = explode('AVS=', $response);
		$tran1= explode('&', $response);
		if(@$tran1[0] == 'y')
		$ret['avs'] = 'avs_exact';
		else
		$ret['avs'] = 'avs_no_match';
		  
		# return
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