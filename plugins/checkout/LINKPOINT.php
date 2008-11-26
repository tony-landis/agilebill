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

class plg_chout_LINKPOINT extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_LINKPOINT ($checkout_id=false) {
		$this->name 		= 'LINKPOINT';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
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
  
		# Linkpoint Class
		include_once (PATH_PLUGINS . 'checkout/CLASS_LINKPOINT/lphp.php');
		$mylphp=new lphp;
		$myorder["host"]       	= "secure.linkpt.net";
		$myorder["port"]       	= "1129";
		$myorder["keyfile"]    	= PATH_PLUGINS . 'checkout/CLASS_LINKPOINT/'.$this->cfg["cert"];
		$myorder["configfile"] 	= $this->cfg["account"];
		$myorder["ordertype"]   = "SALE";
		$myorder["cardnumber"]  = $this->billing["cc_no"];
		$myorder["cardexpmonth"]= $this->billing["exp_month"];
		$myorder["cardexpyear"] = $this->billing["exp_year"];
		$myorder["cvmindicator"]= "provided";
		$myorder["cvmvalue"]    = $this->billing["ccv"];
		$myorder["addrnum"]     = $this->account["address1"];
		$myorder["zip"]         = $this->account["zip"];
		$myorder["chargetotal"] = $amount;
		$myorder["name"]     	= $this->account['first_name'].' '.$this->account['last_name'];
		$myorder["company"]  	= $this->account['company'];
		$myorder["address1"] 	= $this->account['address1'];
		$myorder["address2"] 	= $this->account['address2'];
		$myorder["city"]     	= $this->account['city'];
		$myorder["state"]    	= $this->account['state'];
		$myorder["email"]    	= $acct_fields['email'];
		$myorderp["ip"]		 	= USER_IP;
		$myorder["comments"] 	= "Invoice $invoice";

		#if($this->cfg['mode'] 	== "1")
		#$myorder["result"]   	= "GOOD"; 	# For a test, set result to GOOD, DECLINE, or DUPLICATE

		#if($this->cfg['mode'] 	== "1")
		#$myorder["debugging"]	= true;
		#$myorder["cbin"]		= false; // use binary curl?

		# Send transaction. Use one of two possible methods  #
		$result = $mylphp->process($myorder);        # use shared library model
		#$result = $mylphp->curl_process($myorder);    # use curl methods

		if ($result["r_approved"] != "APPROVED") {
			$ret['status'] 			= 0;
			$ret['msg'] 			= 'The information provided is invalid or has declined';
		} else {
			$ret['status'] = 1;
			$ret['avs'] 			= $result['r_code'];
			$ret['transaction_id'] 	= $result['r_ordernum'];
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