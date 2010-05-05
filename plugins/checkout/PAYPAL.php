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
	
if(defined('PATH_MODULES')) include_once(PATH_MODULES.'checkout/base_checkout_plugin.class.php'); else include_once('../../modules/checkout/base_checkout_plugin.class.php');

class plg_chout_PAYPAL extends base_checkout_plugin
{
	function plg_chout_PAYPAL($checkout_id=false) {

		$this->name 		= 'PAYPAL';
		$this->type 		= 'redirect';
		$this->recurr_only	= false;
		$this->return_url 	= SSL_URL . 'plugins/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->support_cur  = Array ('AUD', 'USD', 'GBP', 'EUR', 'CAD', 'JPY');
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

		# Special JPY formatting:
		if($currency_iso == 'JPY') $amount = round($amount);

		# Set the vars
		$vals = Array (
		Array ('cmd', 				'_xclick'),
		Array ('bn', 				'Agileco.AgileBill'),
		Array ('business', 			$this->cfg['email']),
		Array ('item_name', 		SITE_NAME. ' - Invoice # '.$invoice),
		Array ('amount', 			$amount),
		Array ('return', 			$this->success_url.$invoice),
		Array ('cancel_return', 	$this->decline_url.$invoice),
		Array ('notify_url',		$this->return_url),
		Array ('currency_code',		$currency_iso),
		Array ('invoice', 			$invoice),
		Array ('first_name', 		$acct_fields['first_name']),
		Array ('last_name', 		$acct_fields['last_name']),
		Array ('payer_business_name', $acct_fields['company']),
		Array ('address_street',	$acct_fields['address1']),
		Array ('address_city', 		$acct_fields['city']),
		Array ('address_state', 	$acct_fields['state']),
		Array ('address_zip', 		$acct_fields['zip']),
		Array ('address_country', 	$acct_fields['country_id']),
		Array ('payer_email', 		$acct_fields['email']),
		Array ('payer_id', 			$acct_fields['id'])
		);

		$this->post_vars("https://www.paypal.com/cgi-bin/webscr", $vals);		
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
	function postback()
	{
		# read the post from PayPal system and add 'cmd'
		global $_POST;
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		# post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

		# needed for validation
		$this->status	 	= $_POST['payment_status'];

		# needed for return
		$ret['invoice_id']		= $_POST['invoice'];
		$ret['transaction_id']	= $_POST['txn_id'];
		$ret['amount']	 		= $_POST['mc_gross'];
		$ret['currency'] 		= $_POST['mc_currency'];

		$do = true;

		# validate vars
		if ($fp)  {
			fputs ($fp, $header . $req);
			while (!feof($fp))  {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0)
				{
					# check the payment_status is Completed
					if($this->status == 'Completed' || $this->status == 'Canceled_Reversal')
					$ret['status'] = true;
					else
					$ret['status'] = false;

					# get the processor details:
					$db = &DB();
					$q  = "SELECT id,active,plugin_data FROM ".AGILE_DB_PREFIX."checkout WHERE
						        site_id 		= ".$db->qstr(DEFAULT_SITE)." AND
						        checkout_plugin	= ".$db->qstr($this->name);	
					$rs = $db->Execute($q);
					while(!$rs->EOF)
					{
						$ret['checkout_id'] = $rs->fields["id"];
						$this->cfg = unserialize($rs->fields["plugin_data"]);
						if($_POST['receiver_email'] == $this->cfg['email'])
						{
							include_once(PATH_MODULES.'checkout/checkout.inc.php');
							$checkout = new checkout;
							$checkout->postback($ret);
							return;
						}
						$rs->MoveNext();
					}
				}
			}
			fclose ($fp);
		}
	}
}

# Postback Function
if(empty($VAR) && empty($VAR['do'])) {
	include_once('../../config.inc.php');
	require_once(PATH_ADODB  . 'adodb.inc.php');
	require_once(PATH_CORE   . 'database.inc.php');
	require_once(PATH_CORE   . 'setup.inc.php'); 
	require_once(PATH_CORE   . 'xml.inc.php');
	$C_debug 	= new CORE_debugger;
	$C_db       = &DB();
	$C_setup 	= new CORE_setup; 
	$plg = new plg_chout_PAYPAL;
	$plg->postback(); 
} 
?>