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

class plg_chout_SUREPAY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_SUREPAY ($checkout_id=false) {
		$this->name 		= 'SUREPAY';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host_test	= 'xml.test.surepay.com';
		$this->host_live	= 'xml.surepay.com';
		$this->url			= '/';
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

		# Get the data submitted from the customer:
		$billing = @$VAR['checkout_plugin_data'];

		# Test mode:
		if ( $this->cfg['mode'] )
		$this->host = $this->host_live;
		else
		$this->host = $this->host_test;

		# Assemble the XML request
		$xml_request = '<!DOCTYPE pp.request PUBLIC "-//IMALL//DTD PUREPAYMENTS 1.0//EN" "http://www.purepayments.com/dtd/purepayments.dtd">';
		$xml_request.='
<pp.request merchant="' . $this->cfg['account'] . '" password="' . $this->cfg['password'] . '">
<pp.auth ordernumber="' . $invoice .'" ecommerce="true" ecommercecode="07" ponumber="' . $this->account["first_name"].' '.$this->account["last_name"] .'" ipaddress="' . USER_IP .'" shippingcost="0.00USD" taxamount="0.00USD" referringurl="NA" browsertype="NA">
<pp.lineitem quantity="1" sku="NA" description="Invoice ' . $invoice .'" unitprice="' . $amount .'USD" taxrate="0.00" />
<pp.creditcard number="' . $this->billing["cc_no"] .'" expiration="' . $this->billing["exp_month"] .    '/'.$this->billing["exp_year"] .'" cvv2="'.$this->billing["ccv"].'" cvv2status="1">
<pp.address type="billing" fullname="' . $this->account["first_name"].' '.$this->account["last_name"] .'" address1="' . $this->account["address1"] .'" address2="'.$this->account["address2"].'" city="' . $this->account["city"] .'" state="' . $this->account["state"] .'" zip="' . $this->account["zip"] .'" country="' . $country .'" phone="" email="' . $acct_fields["email"] .'" />
</pp.creditcard>
<pp.ordertext type="instructions">Payment for order ' . $invoice .'</pp.ordertext>
<pp.address type="shipping" fullname="' . $this->account["first_name"].' '.$this->account["last_name"] .'" address1="' . $this->account["address1"] .'" address2="'.$this->account["address2"].'" city="' . $this->account["city"] .'" state="' . $this->account["state"] .'" zip="' . $this->account["zip"] .'" country="' . $country .'" phone="" email="' . $acct_fields["email"] .'" />
</pp.auth>
</pp.request>';

		# Set the post vars:
		$vars = Array ( Array ('xml', $xml_request) );
 
		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);
 
		# Get return response
		if(!$response)
		return false;
		else
		$respond = explode('&', $response);

		for ($i=0; $i<count($respond); $i++) {
			@$arr1 = explode('=', $respond[$i]);
			@$response1[urldecode($arr1[0])] = urldecode($arr1[1]);
		}

		# Transaction Status:
		if (trim($response1['?valid']) == 'true')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# Transaction ID:
		$ret['transaction_id'] = $response1["trans_id"];
		$ret['authorization_id'] = $response1["auth_code"];

		# Message:
		$ret['msg'] = $response;

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
