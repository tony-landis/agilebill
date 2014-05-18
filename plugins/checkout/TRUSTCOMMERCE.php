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

class plg_chout_TRUSTCOMMERCE extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_TRUSTCOMMERCE($checkout_id=false) {


		$this->name 		= 'TRUSTCOMMERCE';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'vault.trustcommerce.com';
		$this->url			= '/trans/';
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
		if(preg_match('/100/', $this->cfg['mode']))
		$demo = "n";
		else
		$demo = "y";

		# Set the post vars:
		$vars = Array (
		Array ('custid', 	$this->cfg['x_Login']),
		Array ('password', 	$this->cfg['x_Password']),
		Array ('action', 	$this->cfg['x_Transaction_Type']),
		Array ('avs', 		$this->cfg['x_AVS']),
		Array ('demo',		$demo),
		Array ('ticket',	$invoice),
		Array ('media',		'cc'),
		Array ('cc', 		$this->billing["cc_no"]),
		Array ('exp',		$this->billing["exp_month"].$this->billing["exp_year"]),
		Array ('cvv',		$this->billing["ccv"]),
		Array ('amount', 	$amount*100),
		Array ('name', 		$this->account["first_name"].' '.$this->account["last_name"]),
		Array ('address1', 	$this->account["address1"]  .' '.$this->account["address2"]),
		Array ('city', 		$this->account["city"]),
		Array ('state', 	$this->account["state"]),
		Array ('zip',		$this->account["zip"])
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$return = $n->connect($this->host, $this->url, $vars, true, 1);

		# Get return response
		if(!$return)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			$response = explode("\n", trim($return));
			for($i=0; $i<count($response); $i++)
			{
				if(!empty($response[$i]))
				{
					unset($thisone);
					$thisone = explode("=", $response[$i]);
					$varr[$thisone[0]] = $thisone[1];
				}
			}
		}

		# Message:
		@$ret['msg'] = $varr["transid"];

		# AVS:
		@$ret['avs'] = $varr["avs"];

		# Transaction Status:
		if ($varr["status"] == 'approved' || $varr["status"] == 'accepted')
		{
			$ret['status'] = 1;
		}
		else
		{
			$ret['status'] = 0;
			@$ret['msg'] = 'Charge declined, please double check your card details.';
		}
 
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