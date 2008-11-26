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

	$plg = new plg_chout_COMMERCEPAYMENTWINDOW;
	$plg->postback($VAR);
}

include_once(PATH_MODULES.'checkout/base_checkout_plugin.class.php');

class plg_chout_COMMERCEPAYMENTWINDOW extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_COMMERCEPAYMENTWINDOW($checkout_id=false) {

		$this->name 		= 'COMMERCEPAYMENTWINDOW';
		$this->type 		= 'redirect';
		$this->recurr_only	= false;
		$this->support_cur  = Array ('USD','RM');
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

		$url =	"https://www.commercepayment.com/PaymentWindowStd_{$currency_iso}.jsp";
		$vals = Array (
		Array ('MERCHANTID', 		$this->cfg['id']),
		Array ('AMOUNT', 			$amount),
		Array ('MERCHANT_TRANID', 	$invoice),
		Array ('TRANSACTIONTYPE', 	'2'),
		Array ('NEWTRANSACTION', 	'Y'),
		Array ('DESCRIPTION', 		'INVOICE #' . $invoice),
		Array ('RETURN_URL', 		SSL_URL.'plugins/checkout/COMMERCEPAYMENTWINDOW.php'),
		Array ('REMOTEIP',			USER_IP),
		Array ('BILLADDRESS',		$acct_fields['first_name']." ".$acct_fields['last_name'].", ".$acct_fields['address1'].", ".$acct_fields['city'].", ".$acct_fields['state']." ".$acct_fields['zip']),
		Array ('SHIPADDRESS',		$acct_fields['first_name']." ".$acct_fields['last_name'].", ".$acct_fields['address1'].", ".$acct_fields['city'].", ".$acct_fields['state']." ".$acct_fields['zip']),
		Array ('FRAUDRISK_EMAIL', 	$acct_fields['email'])
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
		$ret['invoice_id']		= $VAR['MERCHANT_TRANID'];
		$ret['transaction_id']	= $VAR['TRANSACTIONID'];
		$ret['amount']	 		= $VAR['AMOUNT'];
		$ret['currency'] 		= $VAR['CURRENCYCODE'];

		if($VAR['TXN_STATUS'] == 'Y')
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

			if($ret['status']) {
				include_once(PATH_MODULES.'checkout/checkout.inc.php');
				$checkout = new checkout;
				$checkout->postback($ret);
				echo '<SCRIPT LANGUAGE="JavaScript">
							window.location="'.SSL_URL.'?_page=invoice:thankyou&_next_page=invoice:user_view&id='.$ret['invoice_id'].'";
						  </script>';					
				return true;
			}
			$rs->MoveNext();
		}

		echo '<SCRIPT LANGUAGE="JavaScript">
					window.location="'.SSL_URL.'?_page=invoice:thankyou&_next_page=invoice:user_view&id='.$ret['invoice_id'].'";
				  </script>';			 
	}
}
?>
