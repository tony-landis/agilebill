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

class plg_chout_MONEYBOOKERS_RECURRING extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_MONEYBOOKERS_RECURRING($checkout_id=false) {

		$this->name 		= 'MONEYBOOKERS_RECURRING';
		$this->type 		= 'redirect'; 			 // redirect, gateway, or other
		$this->recurr_only	= true;
		$this->return_url 	= SSL_URL . 'plugins/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Get the regular period for this subscription:
		$sched = $recurr_bill_arr[0]["recurr_schedule"];
		if($sched == 0) {
			$rec_period = '7';
		} elseif ($sched == 1) {
			$rec_period = '30';
		} elseif ($sched == 2) {
			$rec_period = '91';
		} elseif ($sched == 3) {
			$rec_period = '182';
		} elseif ($sched == 4) {
			$rec_period = '365';
		} elseif ($sched == 5) {
			return false;
		}

		$url =	"https://www.moneybookers.com/app/payment.pl";

		# Get the next bill date for this subscription:
		if($recurr_bill_arr[0]["recurr_type"] == "1")
		{
			# Pro-rate billing:
			include_once ( PATH_MODULES . 'product/product.inc.php' );
			$product = new product;
			$arr = $product->recurrDates($recurr_bill_arr[0]["recurr_schedule"], $recurr_bill_arr[0]["recurr_weekday"], $recurr_bill_arr[0]["recurr_week"]);
			$rec_start_date = date("d/m/Y", $arr["end"]);
			$end = $arr["end"]+(86400*365*10);
			$rec_end_date   = date("d/m/Y", $end);

			$vals = Array (
			Array ('pay_to_email', 		$this->cfg['account']),
			Array ('detail1_description',"Payment For Invoice No. ". $invoice),
			Array ('amount', 			$amount),
			Array ('currency', 			$currency_iso),
			Array ('status_url', 		$this->return_url),
			Array ('return_url', 		$this->success_url.$invoice),
			Array ('cancel_url', 		$this->decline_url.$invoice),
			Array ('firstname', 		$acct_fields["first_name"]),
			Array ('lastname', 			$acct_fields["last_name"]),
			Array ('address', 			$acct_fields["address1"]),
			Array ('postal_code', 		$acct_fields["zip"]),
			Array ('city', 				$acct_fields["city"]),
			Array ('state', 			$acct_fields["state"]),
			Array ('transaction_id', 	$invoice),
			Array ('rec_amount',    	$total_recurring),
			Array ('rec_start_date', 	$rec_start_date),
			Array ('rec_end_date', 		$rec_end_date),
			Array ('rec_period', 	    $rec_period),
			);
		}
		else
		{ 
			# Bill on anniversary:
			$rec_start_date = date("d/m/Y", time());
			$end = time()+(86400*365*10);
			$rec_end_date   = date("d/m/Y", $end);
			$vals = Array (
			Array ('pay_to_email', 		$this->cfg['account']),
			Array ('detail1_description',"Payment For Invoice No. ". $invoice),
			Array ('amount', 			$amount),
			Array ('currency', 			$currency_iso),
			Array ('status_url', 		$this->return_url),
			Array ('return_url', 		$this->success_url),
			Array ('cancel_url', 		$this->decline_url),
			Array ('firstname', 		$acct_fields["first_name"]),
			Array ('lastname', 			$acct_fields["last_name"]),
			Array ('address', 			$acct_fields["address1"]),
			Array ('postal_code', 		$acct_fields["zip"]),
			Array ('city', 				$acct_fields["city"]),
			Array ('state', 			$acct_fields["state"]),
			Array ('transaction_id', 	$invoice),
			Array ('rec_amount',    	$total_recurring),
			Array ('rec_start_date', 	$rec_start_date),
			Array ('rec_end_date', 		$rec_end_date),
			Array ('rec_period', 	    $rec_period),

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
	function postback($VAR)
	{
		# needed for return
		$ret['invoice_id']		= $VAR['transaction_id'];
		$ret['transaction_id']	= $VAR['mb_transaction_id'];
		$ret['amount']	 		= $VAR['mb_amount'];
		$ret['currency'] 		= $VAR['mb_currency'];
		$ret['status'] 			= true;
		$ret['subscription_id']	= $VAR['transaction_id'];

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

			# If the secret word is set, validate it against what is posted
			if(!empty($this->cfg['secret']))  {
				$hash  =  $VAR['merchant_id'];
				$hash  .= $VAR['transaction_id'];
				$hash  .= strtoupper(md5($this->cfg['secret']));
				$hash  .= $VAR['mb_amount'];
				$hash  .= $VAR['mb_currency'];
				$hash  .= $VAR['status'];
				$hash = strtoupper(md5($hash));
				if($hash != strtoupper($VAR['md5sig'])) {
					$do = false;
				}
			}

			# Validate against the posted seller:
			if($this->cfg['account'] != $VAR['pay_to_email']) {
				$do = false;
			}

			if($do) {
				include_once(PATH_MODULES.'checkout/checkout.inc.php');
				$checkout = new checkout;
				$checkout->postback($ret);
				echo '<SCRIPT LANGUAGE="JavaScript">
							window.location="'.$this->success_url.$ret['invoice_id'].'";
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
	$plg = new plg_chout_MONEYBOOKERS_RECURRING;
	$plg->postback($VAR);
}
?>