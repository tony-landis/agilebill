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

class plg_chout_MONERIS_ESELECT extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_MONERIS_ESELECT($checkout_id=false) { 
		$this->name 		= 'MONERIS_ESELECT';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->success_url  = URL . '?_page=invoice:thankyou';
		$this->host			= 'secure.authorize.net';
		$this->url			= '/gateway/transact.dll';
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
		if($this->cfg['mode'] == "1")
		$test = "TRUE";
		else
		$test = "FALSE";
 
		require_once("CLASS_MONERIS/mpgClasses.php");

		$storeid=$this->cfg['user'];
		$apitoken=$this->cfg['pass'];

		$txnArray=array('type'	=>$this->cfg['type'],
		'order_id'		=> $invoice,
		'amount'		=> $amount,
		'pan'			=> $this->billing["cc_no"],
		'expdate'		=> $this->billing["exp_year"].$this->billing["exp_month"],
		'crypt_type'	=> '7' );

		$mpgTxn = new mpgTransaction($txnArray);

		/*

		$mpgCustInfo = new mpgCustInfo();
		$billing = Array( 	'first_name' => $this->account["first_name"],
		'last_name' => $this->account["last_name"],
		'company_name' => $this->account["company"],
		'address' => $this->account["address1"] . ' ' . $this->account["address2"],
		'city' => $this->account["city"],
		'province' => $this->account["state"],
		'postal_code' => $this->account["zip"],
		'country' => $country);
		$mpgCustInfo->setBilling($billing);
		$mpgCustInfo->setEmail($acct_fields["email"]);
		$mpgTxn->setCustInfo($mpgCustInfo);
		*/
 
		$mpgRequest = new mpgRequest($mpgTxn);

		$mpgHttpPost = new mpgHttpsPost($storeid,$apitoken,$mpgRequest);

		$mpgResponse=$mpgHttpPost->getMpgResponse();

		## step 6) retrieve data using get methods

		print ("\nCardType = " . $mpgResponse->getCardType());
		print("\nTransAmount = " . $mpgResponse->getTransAmount());
		print("\nTxnNumber = " . $mpgResponse->getTxnNumber());
		print("\nReceiptId = " . $mpgResponse->getReceiptId());
		print("\nTransType = " . $mpgResponse->getTransType());
		print("\nReferenceNum = " . $mpgResponse->getReferenceNum());
		print("\nResponseCode = " . $mpgResponse->getResponseCode());
		print("\nISO = " . $mpgResponse->getISO());
		print("\nMessage = " . $mpgResponse->getMessage());
		print("\nAuthCode = " . $mpgResponse->getAuthCode());
		print("\nComplete = " . $mpgResponse->getComplete());
		print("\nTransDate = " . $mpgResponse->getTransDate());
		print("\nTransTime = " . $mpgResponse->getTransTime());
		print("\nTicket = " . $mpgResponse->getTicket());
		print("\nTimedOut = " . $mpgResponse->getTimedOut());
  
		# Test Mode
		if($this->cfg['mode'] == "1")
		echo '<script language=Javascript>alert(\'Gateway response: '.$response.'\') </script>';

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			$response = explode(',', $response);
		}

		# Transaction Status:
		if ($response[0] == '1')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		@$ret['avs'] = @$response[4];

		# Message:
		@$ret['msg'] = @$response[3];

		# AVS Details:
		if ( @$response[5] == 'A' )
		$ret['avs'] = 'avs_address_only';
		elseif ( @$response[5] == 'E' )
		$ret['avs'] = 'avs_error';
		elseif ( @$response[5] == 'N' )
		$ret['avs'] = 'avs_no_match';
		elseif ( @$response[5] == 'P' )
		$ret['avs'] = 'avs_na';
		elseif ( @$response[5] == 'R' )
		$ret['avs'] = 'avs_retry';
		elseif ( @$response[5] == 'S' )
		$ret['avs'] = 'avs_not_supported';
		elseif ( @$response[5] == 'U' )
		$ret['avs'] = 'avs_address_unavail';
		elseif ( @$response[5] == 'W' )
		$ret['avs'] = 'avs_fullzip_only';
		elseif ( @$response[5] == 'X' )
		$ret['avs'] = 'avs_exact';
		elseif ( @$response[5] == 'Y' )
		$ret['avs'] = 'avs_address_zip';
		elseif ( @$response[5] == 'Z' )
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