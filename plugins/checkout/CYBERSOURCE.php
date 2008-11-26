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

class plg_chout_CYBERSOURCE extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_CYBERSOURCE($checkout_id=false) {
		$this->name 		= 'CYBERSOURCE';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->success_url  = URL . '?_page=invoice:thankyou';
		$this->getDetails($checkout_id);
	}

	# Is the PHP Extension loaded?
	function validate_env() {
		if (!extension_loaded("cybersource")) {
			global $C_debug;
			$C_debug->error('plg_chout_CYBERSOURCE','validate_env','The Cybersource PHP extension was not found.');
			return false;
		}
		return true;
	}

	function handle_error($status, $request, $reply) {
		switch ($status)
		{
			case CYBS_S_PHP_PARAM_ERROR:
			return "Please check the parameters passed to cybs_run_transaction for correctness.";
			break;

			case CYBS_S_PRE_SEND_ERROR:
			return "The following error occurred before the request could be sent:\n".$reply[CYBS_SK_ERROR_INFO];
			break;

			case CYBS_S_SEND_ERROR:
			return "The following error occurred while sending the request:\n".$reply[CYBS_SK_ERROR_INFO];
			break;

			case CYBS_S_RECEIVE_ERROR:
			return "The following error occurred while waiting for or retrieving the reply:\n".$reply[CYBS_SK_ERROR_INFO];
			#handleCriticalError( $status, $request, $reply );
			break;

			case CYBS_S_POST_RECEIVE_ERROR:
			return "The following error occurred after receiving and during processing of the reply:\n".$reply[CYBS_SK_ERROR_INFO];
			#handleCriticalError( $status, $request, $reply );
			break;

			case CYBS_S_CRITICAL_SERVER_FAULT:
			return "The server returned a CriticalServerError fault:\n".$this->getFaultContent( $reply );
			#handleCriticalError( $status, $request, $reply );
			break;

			case CYBS_S_SERVER_FAULT:
			return "The server returned a ServerError fault:\n%s\n".$this->getFaultContent( $reply );
			break;

			case CYBS_S_OTHER_FAULT:
			return "The server returned a fault:\n".$this->getFaultContent( $reply );
			break;

			case CYBS_S_HTTP_ERROR:
			return "An HTTP error occurred:\n%s\nResponse Body:\n".$reply[CYBS_SK_ERROR_INFO]."\n".$reply[CYBS_SK_RAW_REPLY];
			break;
		}
	}

	function getFaultContent($reply) {
		$requestID = $reply[CYBS_SK_FAULT_REQUEST_ID];
		if ( $requestID == "")
		$requestID = "(unavailable)";

		return( sprintf(
		"Fault code: %s\nFault string: %s\nRequestID: %s\nFault document: %s",
		$reply[CYBS_SK_FAULT_CODE], $reply[CYBS_SK_FAULT_STRING],
		$requestID, $reply[CYBS_SK_FAULT_DOCUMENT] ) );
	}

	# Validate the user submitted billing details at checkout:
	function validate($VAR) {
		return true;
	}


	# Perform the checkout transaction (new purchase):
	function bill_checkout( $amount, $invoice, $currency_iso, $acct_fields, $total_recurring=false, $recurr_bill_arr=false) {

		# Do we have the API available?
		if (!$this->validate_env()) {
			$msg = 'The CYBERSOURCE PHP extension was not detected on this system. Please install the CYBERSOURCE PHP API.';
			$ret = '<script language=Javascript> alert(\''.$msg.'\'); </script>';
			echo $ret;
			return false;
		}

		# Validate currency
		if(!$this->validate_currency($currency_iso)) return false;
 
		$ret=false;
		if(!$this->validate_card_details($ret)) return false;

		# Get the country
		$country = $this->getCountry('name', $this->account["country_id"]);
		
		// setup the configuration
		$config = array();
		$config['merchantID'] = $this->cfg['merchantID'];
		$config['keysDirectory'] = $this->cfg['keysDirectory'];
		$config['targetAPIVersion'] = $this->cfg['targetAPIVersion'];
		if ($this->cfg['mode'] == 1)
		$config['sendToProduction'] = true;
		else
		$config['sendToProduction'] = false;
		if (strlen($this->cfg['sslCertFile']))
		$config['sslCertFile'] = $this->cfg['sslCertFile'];

		// set up the request by creating an array and adding fields to it
		$request = array();

		$request['ccAuthService_run'] = 'true';
		$request['ccCaptureService_run'] = 'true';
		$request['merchantReferenceCode'] = 'INVOICE-'.$invoice;
		$request['billTo_firstName'] = $this->account["first_name"];
		$request['billTo_lastName'] = $this->account["last_name"];
		$request['billTo_street1'] = $this->account["address1"] . ' ' . $this->account["address2"];
		$request['billTo_city'] = $this->account["city"];
		$request['billTo_state'] = $this->account["state"];
		$request['billTo_postalCode'] = $this->account["zip"];
		$request['billTo_country'] = $country;
		$request['billTo_email'] = $acct_fields["email"];
		$request['billTo_ipAddress'] = USER_IP;
		$request['shipTo_firstName'] = $this->account["first_name"];
		$request['shipTo_lastName'] = $this->account["last_name"];
		$request['shipTo_street1'] = $this->account["address1"] . ' ' . $this->account["address2"];
		$request['shipTo_city'] = $this->account["city"];
		$request['shipTo_state'] = $this->account["state"];
		$request['shipTo_postalCode'] = $this->account["zip"];
		$request['shipTo_country'] = $country;
		$request['card_accountNumber'] = $this->billing["cc_no"];
		$request['card_expirationMonth'] = $this->billing["exp_month"];
		$request['card_expirationYear'] = '20'.$this->billing["exp_year"];
		$request['card_cvNumber'] = $this->billing["ccv"];
		$request['purchaseTotals_currency'] = $currency_iso;
		$request['purchaseTotals_grandTotalAmount'] = $amount;

		// add other fields here per your business needs

		// send request now
		$reply = array();
		$status = cybs_run_transaction( $config, $request, $reply );

		$ret['status'] = 0;
		if ($status == 0) {
			$decision = $reply['decision'];
			if (strtoupper($decision) == 'ACCEPT') {
				$ret['status'] = 1;
			} else if (strtoupper($decision) == 'REJECT') {
				@$ret['msg'] = "Card was rejected: Code ".$reply['ccAuthReply_reasonCode'];
				global $C_debug;
				$C_debug->error('plg_chout_CYBERSOURCE','REJECT',"Card was rejected: Code ".$reply['ccAuthReply_reasonCode']);
			} else {
				@$ret['msg'] = "There was an error while processing your card: Code ".$reply['ccAuthReply_reasonCode'];
				global $C_debug;
				$C_debug->error('plg_chout_CYBERSOURCE','ERROR',"Error: Code ".$reply['ccAuthReply_reasonCode']);
			}
		} else {
			global $C_debug;
			$msg = $this->handle_error($status, $request, $reply);
			$C_debug->error('plg_chout_CYBERSOURCE','validate_env',$msg);
			return false;
		}

		# Transaction ID:
		@$ret['avs']            = $reply['requestID'];
		@$ret['transaction_id'] = $reply['ccCaptureReply_reconciliationID'];
		@$ret['authorization']  = $reply['ccAuthReply_authorizationCode'];

		# AVS Details:
		switch (@$reply['ccAuthReply_avsCode']) {
			case 'A':
			$ret['avs'] = 'avs_address_only';
			break;
			case 'E':
			$ret['avs'] = 'avs_error';
			break;
			case 'I':
			$ret['avs'] = 'avs_address_unavail';
			break;
			case 'N':
			$ret['avs'] = 'avs_no_match';
			break;
			case 'S':
			case 'G':
			case 'C':
			$ret['avs'] = 'avs_not_supported';
			break;
			case 'U':
			$ret['avs'] = 'avs_na';
			break;
			case 'X':
			case 'M':
			case 'D':
			$ret['avs'] = 'avs_exact';
			break;
			case 'Y':
			case 'B':
			$ret['avs'] = 'avs_address_zip';
			break;
			case 'Z':
			case 'W':
			case 'P':
			$ret['avs'] = 'avs_fullzip_only';
			break;
			case '1':
			case '2':
			case 'R':
			default:
			$ret['avs'] = 'avs_error';
			break;
		}
		  
		# return
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