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

class plg_chout_PAYSYSTEMS_CHECK_RECUR extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_PAYSYSTEMS_CHECK_RECUR($checkout_id=false) {

		$this->name 		= 'PAYSYSTEMS_CHECK_RECUR';
		$this->type 		= 'redirect'; 			 // redirect, gateway, or other
		$this->recurr_only	= true;
		$this->return_url 	= SSL_URL . 'plugins/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->support_cur  = Array ('USD');
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		if(!$this->validate_currency($currency_iso)) return false;

		# PaySystems cannot handle pro-rated subscriptions:
		if($recurr_bill_arr[0]["recurr_type"] != "0") {
			global $C_translate;
			$msg = $C_translate->translate('prorated_not_supported','checkout','');
			echo 	'<script language=Javascript> alert(\''.$msg.'\'); </script>';
			return false;
		}

		# Get the regular period for this subscription:
		$sched = $recurr_bill_arr[0]["recurr_schedule"];
		if($sched == 0) {
			$cycle = '7';
		} elseif ($sched == 1) {
			$cycle = '30';
		} elseif ($sched == 2) {
			$cycle = '91';
		} elseif ($sched == 3) {
			$cycle = '182';
		} elseif ($sched == 4) {
			$cycle = '365';
		} elseif ($sched == 5) {
			$cycle = '730';
		}

		if($amount < $total_recurring) {
			$setup_fee = '0';
		} else {
			$setup_fee = $amount-$total_recurring;
		}

		$url =	"https://secure.paysystems1.com/cgi-v310/payment/onlinesale-tpppro_check.asp";
		$vals = Array (
		Array ('companyid',			$this->cfg['id']),
		Array ('product1', 			"Payment For Invoice No. ". $invoice),
		Array ('total', 			$amount),
		Array ('redirect', 			$this->return_url),
		Array ('redirectfail', 		$this->decline_url.$invoice),
		Array ('currency_code',		$currency_iso),
		Array ('option1', 			$invoice),
		Array ('b_firstname', 		$acct_fields['first_name']),
		Array ('b_middlename', 		$acct_fields['middle_name']),
		Array ('b_lastname', 		$acct_fields['last_name']),
		Array ('b_address',			$acct_fields['address1']),
		Array ('b_city', 			$acct_fields['city']),
		Array ('b_state', 			$acct_fields['state']),
		Array ('b_zip', 			$acct_fields['zip']),
		Array ('b_country', 		$acct_fields['country_id']),
		Array ('email', 			$acct_fields['email']),
		Array ('delivery', 			'N'),
		Array ('reoccur', 			"Y"),
		Array ('cycle', 			$cycle),
		Array ('totalperiod',		'0'),
		Array ('repeatamount',		$total_recurring)
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
		$ret['invoice_id']		= $VAR['option1'];
		$ret['transaction_id']	= $VAR['order_id'];
		$ret['amount']	 		= $VAR['amount'];
		$ret['currency'] 		= DEFAULT_CURRENCY;
		$ret['status'] 			= true;
		$ret['subscription_id']	= $VAR['option1'];

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

	$plg = new plg_chout_PAYSYSTEMS_CHECK_RECUR;
	//$plg->postback($VAR);
	$plg->bill_checkout('','','','','','');
}
?>