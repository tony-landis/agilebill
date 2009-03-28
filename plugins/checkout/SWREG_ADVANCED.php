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

class plg_chout_SWREG_ADVANCED extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_SWREG_ADVANCED($checkout_id=false) { 
		$this->name 		= 'SWREG_ADVANCED';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'www.swreg.org';
		$this->url			= '/cgi-bin/c.cgi';
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
		Array ('s',		            $this->cfg['store']),
		Array ('p',	                $this->cfg['store'].'agile'),

		Array ('vp', 				$amount),
		Array ('vt',                $invoice),
		Array ('d',                 '0'),


		Array ('q', 		        '1'),
		Array ('t',                 '(null)'),
		Array ('a',                 ''),
		Array ('pt',                '1'),

		Array ('cn', 			    $this->billing["cc_no"]),
		Array ('in',                @$this->billing["ccv"]),
		Array ('mm', 			    $this->billing["exp_month"]),
		Array ('yy',                $this->billing["exp_year"]),
		Array ('pn',                $this->billing["phone_number"]),

		Array ('fn', 			    $this->account["first_name"]),
		Array ('sn', 			    $this->account["last_name"]),
		Array ('co', 			    $this->account["company"]),
		Array ('a1', 			    $this->account["address1"]),
		Array ('a2',                $this->account["address2"]),
		Array ('a3', 				$this->account["city"]),
		Array ('st', 				$this->account["state"]),
		Array ('zp', 				$this->account["zip"]),
		Array ('em', 				$acct_fields["email"]),
		Array ('ct', 			    $country),
		Array ('ip', 		        USER_IP),
		Array ('dfn',               ''),
		Array ('dsn',               ''),
		Array ('dco',               ''),
		Array ('da1',               ''),
		Array ('da2',               ''),
		Array ('da3',               ''),
		Array ('dst',               ''),
		Array ('dzp',               ''),
		Array ('dct',               ''),
		Array ('ins',               ''),
		Array ('ra',                '')
		);


		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1);

		# Test Mode
		if($this->cfg['mode'] = "1")
		echo '<script language=Javascript>alert(\'Gateway response: '.$response.'\') </script>';

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			foreach(explode("&",$response) as $pair) {
				list($key,$val)=explode("=",$pair);
				$swreg[$key]=$val;
			}
		}

		# IF SWREG RETURNED ERROR CODE OR WE HAVE NO ORDER NUMBER DISPLAY ERROR
		if($swreg["result"]!=0 || $swreg["ordernumber"]=="")
		{
			$ret['status'] = 0;
		} else {
			$ret['status'] = 1;
		}

		# Transaction ID:
		@$ret['transaction_id'] = @$swreg["ordernumber"];


		# DEFINE ERROR CODES
		$messages=array("",
		"You must supply an ip address",
		"You must supply a contact phone number",
		"You must supply an email address",
		"You must supply a first name",
		"You must supply a surname",
		"You must supply an address",
		"You must supply a country",
		"You must supply a country for the delivery address",
		"You must supply your shop id",
		"You must supply a product code ",
		"Attempt to purchase a non existent item",
		"Del_id/Product_id count wrong",
		"Var_id/Product_id count wrong",
		"Qty/Product_id count wrong",
		"Email address used by fraudsters in the past unable to accept this order",
		"IP address used by fraudsters in the past unable to accept this order",
		"Invalid card details",
		"Declined Card",
		"You must supply a payment method",
		"Payment method must be in the range 1 - 5",
		"Call Authorisation Centre",
		"Comms failure - Thankyou for the order, do NOT re-order as we will keep trying every 15 minutes to get an authorisation.",
		"Incorrect expiry date",
		"Value enterted into Switch /Solo issue number field despite not being a Switch or Solo card. (We have also had this result code when the expiry date is way too far in the future).",
		"Affiliate account is closed. <A HREF=\"a.php?affil=\">Click here</a> and try again.",
		"SWREG down for maintenance (probably only for 15 minutes)",
		"Discover card will be processed manually same or following business day. Please do not reorder.",
		"Store closed for sales - get this one and it shows we no longer wish to sell your products!",
		"Problem with the card data - the bank doesn't like it.",
		"Failed country check - On the banned country list",
		"Problem with bin range - we do not (yet) know how to deal with the card (Bin range is first 6 numbers of the card so we do not know who owns it to route the payment request).",
		"Duplicate order trap (if you sent us a value in variable &vt=)",
		"Country name not valid - use our list of country names to avoid this error. A list of country names we accept is available by going to https://usd.swreg.org/cgi-bin/b.cgi?s=2034&p=2034get1&v=0&d=0&q=1&t= and grabbing the list from our html code. ");
		$ret['msg'] = $messages[$swreg["result"]];

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