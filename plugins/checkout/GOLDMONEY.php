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

class plg_chout_GOLDMONEY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_GOLDMONEY($checkout_id=false) {
		$this->name 		= 'GOLDMONEY';
		$this->type 		= 'redirect'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->return_url 	= SSL_URL . 'plugins/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->support_cur  = Array ('AUD', 'BRL', 'GBP', 'CAD', 'CNY', 'DEM', 'EUR', 'FRF', 'HDK', 'INR', 'ITL', 'JPY', 'KWD', 'MXN', 'NZD', 'RUR', 'ZAR', 'CHF', 'TRL', 'USD');
		$this->support_arr  = Array ('36', '986', '826', '124', '156', '280', '978', '250', '344', '356', '380', '392', '414', '484', '554', '810', '710', '756', '792', '840');
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		if(!$this->validate_currency($currency_iso)) return false;

		$url =	"https://www.goldmoney.com/omi/omipmt.asp";
		$vals = Array (
		Array ('OMI_MERCHANT_HLD_NO', 	$this->cfg['account']),
		Array ('OMI_MERCHANT_MEMO',		"Payment For Invoice No. ". $invoice),
		Array ('OMI_CURRENCY_AMT', 		$amount),
		Array ('OMI_CURRENCY_CODE', 	$CURRENCY_CODE),
		Array ('OMI_MODE', 				$this->cfg['mode']),
		Array ('OMI_RESULT_URL', 		$this->return_url),
		Array ('OMI_SUCCESS_URL', 		$this->success_url.$invoice),
		Array ('OMI_SUCCESS_URL_METHOD',"LINK"),
		Array ('OMI_FAIL_URL', 			$this->decline_url.$invoice),
		Array ('OMI_FAIL_URL_METHOD', 	"LINK"),
		Array ('OMI_MERCHANT_REF_NO', 	$invoice)
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
		$ret['invoice_id']		= $VAR['OMI_MERCHANT_REF_NO'];
		$ret['transaction_id']	= $VAR['OMI_TXN_ID'];
		$ret['amount']	 		= $VAR['OMI_CURRENCY_AMT'];
		$ret['currency'] 		= FALSE;
		$ret['status']  		= true;

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

			# Test for test mode
			if($this->cfg['mode'] == 1 && $VAR['OMI_MODE'] != "LIVE")
			$do = false;

			# Create & validate the Hash String
			if(!empty($this->cfg['secret']))
			{
				$con_str = 		 $VAR['OMI_MERCHANT_REF_NO'];
				$con_str.= '?' . $VAR['OMI_MODE'];
				$con_str.= '?' . $VAR['OMI_MERCHANT_HLD_NO'];
				$con_str.= '?' . $VAR['OMI_PAYER_HLD_NO'];
				$con_str.= '?' . $VAR['OMI_CURRENCY_CODE'];
				$con_str.= '?' . $VAR['OMI_CURRENCY_AMT'];
				$con_str.= '?' . $VAR['OMI_GOLDGRAM_AMT'];
				$con_str.= '?' . $VAR['OMI_TXN_ID'];
				$con_str.= '?' . $VAR['OMI_TXN_DATETIME'];
				$con_str.= '?' . $VAR['OMI_MERCHANT_STRG_FEE'];
				$con_str.= '?' . $this->cfg['secret'];
				$str = strtoupper(md5($con_str));
				if($str != $VAR['OMI_HASH'])
				$do = false;
			}

			# Get the currency:
			for($i=0; $i<count($this->support_cur); $i++)
			if ($VAR['OMI_CURRENCY_CODE'] = $this->support_arr[$i])
			$ret['currency'] = $this->support_cur[$i];

			# Validate against the posted payee:
			if($VAR['OMI_MERCHANT_HLD_NO'] != $this->cfg['account'])
			$do = false;

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
	$plg = new plg_chout_GOLDMONEY;
	$plg->postback($VAR);
}
?>