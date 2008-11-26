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

class plg_chout_EFT_MANUAL extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_EFT_MANUAL($checkout_id=false) { 
		$this->name 		= 'EFT_MANUAL';
		$this->type 		= 'other';
		$this->eft			= true;
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->getDetails($checkout_id);
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {
  
		# validate the card type and number, and exp date:
		$ret=false;
		$this->validate_eft_details($ret);
 
		# AVS Details:
		$ret['avs'] = 'avs_na';
		
		# return
		if($ret['status'] == 1) {
			$this->redirect = '<script language=Javascript>document.location = "?_page=invoice:thankyou&_next_page=checkout_plugin:plugin_ord_MANUAL_ALERT&id='.$invoice.'";</script>';
			return $ret;
		} else { 
			global $VAR;
			@$VAR['msg']=$ret["msg"];
			return false; 
		}
	}

	# Stores new billing details, & return account_billing_id (gateway only)
	function store_billing($VAR, $account=SESS_ACCOUNT) {
		return $this->saveEFTDetails($VAR);
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