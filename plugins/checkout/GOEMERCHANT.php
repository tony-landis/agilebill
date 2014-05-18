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

class plg_chout_GOEMERCHANT extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_GOEMERCHANT($checkout_id=false) {
		$this->name 		= 'GOEMERCHANT';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'secure.goemerchant1.com';
		$this->url			= '/cgi-bin/gateway/gateway.cgi';
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
		if($this->cfg['mode'] != "1")
		$test = "TRUE";
		else
		$test = "FALSE";

		# Split up the credit card number the way goemerchant likes it. :(
		# They sure are weird aren't they? Who else does this? It sure is
		# harder to take a string apart than to put it together. End of rant.
		$cc1 = substr($this->billing['cc_no'], 0, 4);
		$cc2 = substr($this->billing['cc_no'], 4, 4);
		$cc3 = substr($this->billing['cc_no'], 8, 4);
		$cc4 = substr($this->billing['cc_no'], 12, 4);

		# Get the card type, apparently goemerchant does not know how to do
		# this on their end. Woe is me! (Visa, Amex, Discover, MasterCard)
		if (preg_match('/^4(.{12}|.{15})$/', $cc_no))
		$cardname = 'Visa';
		elseif (preg_match('/^5[1-5].{14}$/', $cc_no))
		$cardname = 'MasterCard';
		elseif (preg_match('/^3[47].{13}$/', $cc_no))
		$cardname = 'Amex';
		elseif (preg_match('/^6011.{12}$/', $cc_no))
		$cardname = 'Discover';

		# Set the post vars:
		$vars = Array (
		Array ('operation_type',	'auth'),
		Array ('password', 			$this->cfg['x_Password']),
		Array ('merchant', 			$this->cfg['x_Login']),
		Array ('total',				$amount),
		Array ('orderid',	 		$invoice),
		Array ('cardname',	 		$cardname),
		Array ('cardnum1',	 		$cc1),
		Array ('cardnum2',	 		$cc2),
		Array ('cardnum3',	 		$cc3),
		Array ('cardnum4',	 		$cc4),
		Array ('CCV2', 				$this->billing["ccv"]),
		Array ('cardexpm', 			$this->billing["exp_month"]),
		Array ('cardexpy', 			$this->billing["exp_year"]),
		Array ('nameoncard', 		$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('cardstreet', 		$this->account["address1"]  .' '.$this->account["address2"]),
		Array ('cardcity', 			$this->account["city"]),
		Array ('cardstate', 		$this->account["state"]),
		Array ('cardzip', 			$this->account["zip"]),
		Array ('cardcountry', 		$country)
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);

		# Test Mode
		if($this->cfg['mode'] == "1")
		echo '<script language=Javascript>alert(\'Gateway response: '.$response.'\') </script>';

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			$response = explode('|', $response);
		}

		# Transaction Status:
		if ($response[0] == '1')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		@$ret['avs'] = @$response[4];

		# Message:
		@$ret['msg'] = @$response[2];

		# AVS Details:
		if ( @$response[3] == 'Y' )
		$ret['avs'] = 'avs_exact';
		else
		$ret['avs'] = 'avs_no_match';
 
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