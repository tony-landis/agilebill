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
	
/*
Be sure to set the Return Url to point to this script under 'Cart Details' and
Direct Return to 'Yes' in the 2checkout admin area
*/

if(defined('PATH_MODULES')) include_once(PATH_MODULES.'checkout/base_checkout_plugin.class.php'); else include_once('../../modules/checkout/base_checkout_plugin.class.php');

class plg_chout_2CHECKOUT_V2 extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_2CHECKOUT_V2($checkout_id=false) {

		$this->name 		= '2CHECKOUT_V2';
		$this->type 		= 'redirect';
		$this->recurr_only	= false;
		$this->support_cur  = Array ('USD','GBP', 'EUR');
		$this->success_url  = URL . '?_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Validate the currency:
		if(!$this->validate_currency($currency_iso)) return false;

		if($this->cfg['mode'] == "Y") {
			$demo1 = 'demo';
			$demo2 = 'Y';
		} else {
			$demo1 = '';
			$demo2 = '';
		}

		$url =	"https://www2.2checkout.com/2co/buyer/purchase";
		$vals = Array (
		Array ('sid', 				$this->cfg['id']),
		Array ('total', 			$amount),
		Array ('cart_order_id', 	$invoice),
		Array ('card_holder_name',	$acct_fields['first_name'] . ' ' . $acct_fields['last_name']),
		Array ('street_address',	$acct_fields['address1']),
		Array ('city', 				$acct_fields['city']),
		Array ('state', 			$acct_fields['state']),
		Array ('zip', 				$acct_fields['zip']),
		Array ('email', 			$acct_fields['email']),
		Array ($demo1, 				$demo2)
		);

		$this->post_vars($url, $vals);
		return true;
	}

	# Stores new billing details, & return account_billing_id (gateway only)
	function store_billing($VAR) {
		return 0;
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

	# Postback Validation
	function postback($VAR)
	{
		# needed for return
		$ret['invoice_id']		= $VAR['merchant_product_id'];
		$ret['transaction_id']	= $VAR['order_number'];
		$ret['amount']	 		= $VAR['total'];
		$ret['status']	 		= true;
		$ret['currency'] 		= DEFAULT_CURRENCY;

		# needed for verification
		$order_number	= $VAR['merchant_product_id'];	// invoice_id
		$order_id 		= $VAR['order_number'];		// transaction id
		$amount			= $VAR['total'];  			// total

		# get the processor details:
		$db = &DB();
		$q  = "SELECT id,active,plugin_data FROM ".AGILE_DB_PREFIX."checkout WHERE
			        site_id 		= ".$db->qstr(DEFAULT_SITE)." AND
			        checkout_plugin	= ".$db->qstr($this->name);	
		$rs = $db->Execute($q);
		while(!$rs->EOF)
		{
			$ret['checkout_id'] = $rs->fields["id"];
			$do = true;
			$this->cfg = unserialize($rs->fields["plugin_data"]);

			# Get the 2checkout settings
			$sid 			= $this->cfg['id'];		// store id
			$secret_word 	= $this->cfg['secret']; // secret word

			# Test for demo mode
			if (($VAR['demo'] == "Y") && ($this->cfg['mode'] != "Y")) {
				$do = $false;
			} elseif ($VAR['demo'] == "Y") {
				$oid = '1';
			} else {
				$oid = $order_id;
			}

			# If the secret word is set, validate it against what is posted
			if(!empty($secret_word))  {
				$hash_remote 	= strtoupper($VAR['key']);
				$string 		= $secret_word.$sid.$oid.$amount;
				$hash_local 	= strtoupper(md5($string));
				if($hash_local != $hash_remote) {
					$do = false;
				}
			}

			# Validate agains the posted 2checkout id:
			if($sid != $VAR['sid']) {
				$do = false;
			}

			if($do) {
				include_once(PATH_MODULES.'checkout/checkout.inc.php');
				$checkout = new checkout;
				$checkout->postback($ret);
				echo '<SCRIPT LANGUAGE="JavaScript">
							window.location="?_page=invoice:thankyou&_next_page=invoice:user_view&id='.$ret['invoice_id'].'";
						  </script>';					
				return true;
			}
			$rs->MoveNext();
		}

		echo '<SCRIPT LANGUAGE="JavaScript">
					window.location="'.$this->decline_url.$ret['invoice_id'].'";
				  </script>';			 
	}
}

# Postback Function
if(empty($VAR) && empty($VAR['do']))
{
	include_once('../../config.inc.php');
	require_once(PATH_ADODB  . 'adodb.inc.php');
	require_once(PATH_CORE   . 'database.inc.php');
	require_once(PATH_CORE   . 'setup.inc.php');
	require_once(PATH_CORE   . 'vars.inc.php');

	$C_debug 	= new CORE_debugger;
	$C_vars 	= new CORE_vars;
	$VAR        = $C_vars->f;
	$C_db       = &DB();
	$C_setup 	= new CORE_setup;

	$plg = new plg_chout_2CHECKOUT_V2;
	$plg->postback($VAR);
}
?>