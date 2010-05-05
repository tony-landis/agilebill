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

class plg_chout_PAYPAL_RECURRING extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_PAYPAL_RECURRING($checkout_id=false) {

		$this->name 		= 'PAYPAL_RECURRING';
		$this->type 		= 'redirect'; 			 // redirect, gateway, or other
		$this->recurr_only	= true;
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

		# Get the regular period for this subscription:
		$sched = $recurr_bill_arr[0]["recurr_schedule"];
		if($sched == 0) {
			$p3 = '1';
			$t3 = 'W';
		} elseif ($sched == 1) {
			$p3 = '1';
			$t3 = 'M';
		} elseif ($sched == 2) {
			$p3 = '3';
			$t3 = 'M';
		} elseif ($sched == 3) {
			$p3 = '6';
			$t3 = 'M';
		} elseif ($sched == 4) {
			$p3 = '1';
			$t3 = 'Y';
		} elseif ($sched == 5) {
			$p3 = '2';
			$t3 = 'Y';
		}

		$url = "https://www.paypal.com/cgi-bin/webscr"; 

		# Get the next bill date for this subscription:
		if($recurr_bill_arr[0]["recurr_type"] == "1")
		{
			# Pro-rate billing:
			include_once ( PATH_MODULES . 'product/product.inc.php' );
			$product = new product;
			$arr = $product->recurrDates($recurr_bill_arr[0]["recurr_schedule"], $recurr_bill_arr[0]["recurr_weekday"], $recurr_bill_arr[0]["recurr_week"]);
			$remain_time = $arr['end'] - time();
			$period1 	 = round($remain_time/86400);
			$subscr_date = date("Y-m-d", $arr["end"]);

			$vals = Array (
			Array ('cmd', 				'_xclick-subscriptions'),
			Array ('bn', 				'Agileco.AgileBill'),
			Array ('business', 			$this->cfg['email']),
			Array ('item_name', 		"Invoice No:". $invoice),
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
			Array ('payer_id', 			$acct_fields['id'])	,
			Array ('txn_type', 			'subscr_signup'),

			Array ('a1', 				$amount),
			Array ('p1', 				$period1),
			Array ('t1', 				'D'),

			Array ('a3', 				$total_recurring),
			Array ('p3', 				$p3),
			Array ('t3', 				$t3),

			Array ('src', 				"1"),
			Array ('sra', 				"1")
			);


		}
		else
		{

			# Bill on anniversary:
			$vals = Array (
			Array ('cmd', 				'_xclick-subscriptions'),
			Array ('business', 			$this->cfg['email']),
			Array ('item_name', 		"Invoice No:". $invoice),
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
			Array ('payer_id', 			$acct_fields['id'])	,
			Array ('txn_type', 			'subscr_signup'),

			Array ('a1', 				$amount),
			Array ('p1', 				$p3),
			Array ('t1', 				$t3),

			Array ('a3', 				$total_recurring),
			Array ('p3', 				$p3),
			Array ('t3', 				$t3),

			Array ('src', 				"1"),
			Array ('sra', 				"1")
			);
		}

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
	function postback()
	{
		# read the post from PayPal system and add 'cmd'
		global $_POST, $C_debug;

		# Log paypal postback:
		foreach ($_POST as $key => $value) @$debug .= "\r\n$key=$value";
		$C_debug->error('PAYPAL_RECUR:'. $_POST['txn_type'], 'Invoice: '. $_POST['invoice'], "$debug" );

		# Assemble postback string
		$req = 'cmd=_notify-validate';
		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		# post back to PayPal system to validate
		$header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$domain = 'www.paypal.com';
		#$domain = 'www.sandbox.paypal.com';
		$fp = fsockopen ($domain, 80, $errno, $errstr, 30);

		# needed for validation
		$ret['invoice_id']		= $_POST['invoice'];
		$ret['transaction_id']	= $_POST['txn_id'];
		$ret['currency'] 		= $_POST['mc_currency'];
		$ret['subscription_id']	= $_POST['subscr_id'];
		if (!empty($_POST['mc_gross']))
		$ret['amount']	 		= $_POST['mc_gross'];
		else
		$ret['amount']	 		= $_POST['payment_gross'];


		# validate
		$do = true;
		$force = true; // force approved reply
		if (!$fp)
		{
			# HTTP ERROR:
			$C_debug->error('PAYPAL_RECURRING.php', 'postback()', "Unable to connect to domain $domain" );
		}
		else
		{
			fputs ($fp, $header . $req);
			while (!feof($fp))
			{
				$res = fgets ($fp, 1024);
				if (!$force && strcmp ($res, "INVALID") == 0)
				{
					# Log for manual investigation:
					$C_debug->error('PAYPAL_RECURRING.php', 'postback()', "Postback for Invoice {$ret['invoice_id']} is INVALID, PayPal subscription id {$ret['subscription_id']}");
					header("HTTP/1.0 404 Not Found");
					return false;
				}
				else if ($force || strcmp ($res, "VERIFIED") == 0)
				{
					# get the payment status
					$ret['status'] = true;
					switch($_POST['txn_type']) {
						case "subscr_cancel": 	$ret['status'] = false; break;
						case "subscr_failed": 	$ret['status'] = false; break;
						case "subscr_eot": 		$ret['status'] = false; break;
					}

					if($ret['status'] != false) {
						switch($_POST['payment_status']) {
							case "Canceled_Reversal": $ret['status'] = true; break;
							case "Completed": 	$ret['status'] = true; break;
							case "Denied": 		$ret['status'] = false; break;
							case "Failed": 		$ret['status'] = false; break;
							case "Pending": 	$ret['status'] = false; break;
							case "Refunded": 	$ret['status'] = false; break;
							case "Reversed": 	$ret['status'] = false; break;
						}
					}

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
						if($_POST['business'] == $this->cfg['email'])
						{
							include_once(PATH_MODULES.'checkout/checkout.inc.php');
							$checkout = new checkout;
							$checkout->postback($ret);

							header("HTTP/1.1 200 OK");
							header("Status: 200 OK");

							fclose ($fp);
							return;
						}
						$rs->MoveNext();
					}
				}
			}
			fclose ($fp);
		}
		header("HTTP/1.0 404 Not Found");
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
	$plg = new plg_chout_PAYPAL_RECURRING;
	$plg->postback();
}
?>