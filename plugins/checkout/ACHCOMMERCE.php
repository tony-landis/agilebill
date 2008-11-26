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

class plg_chout_ACHCOMMERCE extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_ACHCOMMERCE($checkout_id=false) { 
		$this->name 		= 'ACHCOMMERCE';
		$this->type 		= 'gateway';
		$this->eft			= true;
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->getDetails($checkout_id);
		$this->host 		= 'gateway.achcommerce.com';
		$this->url 			= '/achgate/achgate.cgi';
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}

	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {
   
		#if(!$this->validate_currency($currency_iso)) return false;
		$ret=false;
		$this->validate_eft_details($ret);
  
		/** Determine $acctType 
			27 => Debit to checking account.
			37 => Debit to savings account.
		*/
		if( $this->billing['eft_check_acct_type'] == 'b') $acctType = 27; else $acctType = 37;

		if(empty($this->cfg['batchid'])) 
		$batchid=$invoice.time();
		else
		$batchid=$this->cfg['batchid'];

		# Set the post vars:
		$vars = Array (
		Array ('usermode', 		"cgi"),
		Array ('action', 		"submit"),
		Array ('replymode', 		"csv"),
		Array ('login', 		$this->cfg['login']),
		Array ('password',		$this->cfg['password']),
		Array ('merchantid', 	$this->cfg['merchantid']),
		Array ('verstr',		$this->cfg['verstr']), 
		Array ('sec', 		$this->cfg['sec']), 
		Array ('batchid',		$batchid),
		Array ('routing', 		$this->billing['eft_trn']),
		Array ('account', 		$this->billing['eft_check_acct']),
		Array ('checknum', 		$this->billing['eft_check_checkno']),
		Array ('fname', 		$this->account['first_name']),
		Array ('lname', 		$this->account['last_name']),
		Array ('individualid', 	$invoice),
		Array ('amount', 		round($amount,2)),
		Array ('trancode', 		$acctType),
		Array ('paymentdesc', 	"Payment for Invoice No. {$invoice}"), 
		);

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $vars, true, 1); 
		 
		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			$response = explode('|', $response);
		}
 
		$return_codes = array(
			'000' => 'Accepted',
		    '001' => 'STAR Accepted: Non PPS participant',
		    '002' => 'STAR Accepted: Non-DDA',
		    '003' => 'STAR Accepted: No account info',
		    '100' => 'Failed modulus check',
		    '101' => 'Failed Thompson Financial lookup',
		    '110' => 'Failed Equifax check',
		    '200' => 'Authentication Error',
		    '300' => 'Missing or invalid routing number',
		    '301' => 'Missing or invalid merchant id',
		    '302' => 'Missing or invalid batch id',
		    '303' => 'Missing or invalid account number',
		    '304' => 'Missing or invalid fname',
		    '305' => 'Missing or invalid lname',
		    '306' => 'Missing or invalid individual id',
		    '307' => 'Missing or invalid check number',
		    '308' => 'Missing or invalid amount',
		    '309' => 'Invalid transaction code',
		    '310' => 'Invalid standard entry class',
		    '311' => 'Invalid effective entry date',
		    '401' => 'STAR Failed: STAR CHEK error',
		    '402' => 'STAR Failed: Non-DDA',
		    '403' => 'STAR Failed: No account info',
		    '500' => 'Invalid Data, no action ',
		    '900' => 'Unable to connect to database',
		    '901' => 'Unable to query database',
		    '902' => 'Unable to prepare query',
		    '903' => 'Unable to contact STAR'
	    );
	    
	    $response_code = $response[0];
	    if($response_code == '000') {
	    	$ret['status'] = 1;	    	
	    } else {
	    	$ret['status'] = 0;
	    	foreach($return_codes as $code=>$msg) if($code == $response_code) $ret["msg"] = $msg;  
	    }
	    
		/*
		echo "<pre>";
		print_r($vars);
		print_r($response);
		exit;
		*/

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