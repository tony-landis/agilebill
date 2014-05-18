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

class plg_chout_EWAY extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_EWAY($checkout_id=false) {
		$this->name 		= 'EWAY';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('AUD');
		$this->host			= 'www.eway.com.au';
		$this->url			= '/gateway/xmlpayment.asp';
		$this->url_test     = '/gateway/xmltest/testpage.asp';
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
		if($this->cfg['mode'] == "100")
		$this->url = $this->url;
		else
		$this->url = $this->url_test;

		$amount *= 100;

		$xml_request = '<ewaygateway>'.
		'<ewayCustomerID>'.$this->cfg['customer_id'].'</ewayCustomerID>'.
		'<ewayTotalAmount>'.$amount.'</ewayTotalAmount>'.
		'<ewayCustomerFirstName>'.$this->account["first_name"].'</ewayCustomerFirstName>'.
		'<ewayCustomerLastName>'.$this->account["last_name"].'</ewayCustomerLastName>'.
		'<ewayCustomerEmail>'.$acct_fields["email"].'</ewayCustomerEmail>'.
		'<ewayCustomerAddress>'.$this->account["address1"].'</ewayCustomerAddress>'.
		'<ewayCustomerPostcode>'.$this->account["zip"].'</ewayCustomerPostcode>'.
		'<ewayCustomerInvoiceDescription>Payment for Invoice No. '.$invoice.'</ewayCustomerInvoiceDescription>'.
		'<ewayCustomerInvoiceRef>'.$invoice.'</ewayCustomerInvoiceRef>'.
		'<ewayCardHoldersName>'.$this->account["first_name"].' '.$this->account["last_name"].'</ewayCardHoldersName>'.
		'<ewayCardNumber>'.$this->billing["cc_no"].'</ewayCardNumber>'.
		'<ewayCardExpiryMonth>'.$this->billing["exp_month"].'</ewayCardExpiryMonth>'.
		'<ewayCardExpiryYear>'.$this->billing["exp_year"].'</ewayCardExpiryYear>'.
		'<ewayTrxnNumber></ewayTrxnNumber>'.
		'<ewayOption1>'.$invoice.'</ewayOption1>'.
		'<ewayOption2></ewayOption2>'.
		'<ewayOption3></ewayOption3>'.
		'</ewaygateway>';


		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, $this->url, $xml_request, true, 1);

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		}

		# Transaction Status:
		if (preg_match('@<ewayTrxnStatus>True</ewayTrxnStatus>@i', $response))  {
			$ret['status'] = 1;
		}
		else
		{
			$ret['status'] = 0;
			$ret['msg'] = 'Transaction failed, please verify your details.';
		}

		# Transaction ID:
		$ret['transaction_id'] = '';

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
