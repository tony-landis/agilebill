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

class plg_chout_ECHO extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_ECHO($checkout_id=false) {
		$this->name 		= 'ECHO';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
		$this->host			= 'https://wwws.echo-inc.com';
		$this->url			= '/scripts/INR200.EXE';
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
		$country = $this->getCountry('three_code', $acct_fields["country_id"]);

		if($this->cfg['mode'] = "0")
		$test = "F";
		else
		$test = "T";

		include_once (PATH_PLUGINS . 'checkout/CLASS_ECHO/echophp.class');
		$echoPHP = new EchoPHP;
		$echoPHP->set_order_type("S");
		$echoPHP->set_debug($test);
		$echoPHP->set_EchoServer($this->host .''. $this->url);
		$echoPHP->set_transaction_type($this->cfg["type"]);
		$echoPHP->set_merchant_echo_id($this->cfg["id"]);
		$echoPHP->set_merchant_pin($this->cfg["pin"]);
		$echoPHP->set_billing_ip_address(USER_IP);
		$echoPHP->set_billing_first_name($this->account["first_name"]);
		$echoPHP->set_billing_last_name($this->account["last_name"]);
		$echoPHP->set_billing_address1($this->account["address1"] . ' ' . $this->account["address2"]);
		$echoPHP->set_billing_city($this->account["city"]);
		$echoPHP->set_billing_state($this->account["state"]);
		$echoPHP->set_billing_zip($this->account["zip"]);
		$echoPHP->set_billing_country($country);
		$echoPHP->set_billing_email($acct_fields["email"]);
		$echoPHP->set_grand_total($amount);
		$echoPHP->set_ccexp_month($this->billing["exp_month"]);
		$echoPHP->set_ccexp_year($this->billing["exp_year"]);
		$echoPHP->set_cnp_security($this->billing["ccv"]);
		$echoPHP->set_cc_number($this->billing["cc_no"]);
		$echoPHP->set_counter($echoPHP->getRandomCounter());


		# Set the return codes:
		if (!$echoPHP->Submit())  {
			if ($echoPHP->decline_code == "1013") {
				$ret['status'] = 0;
				$ret['msg'] = $echoPHP->avs_result . 'Echo account '.$echoPHP->merchant_echo_id.' could not be found, failed!';
			} else {
				$ret['status'] = 0;
				$ret['msg'] = $echoPHP->echotype1;
			}
		} else {
			$ret['status'] 		   = 1;
			$ret['transaction_id'] = $echoPHP->reference;
			$ret['authorization']  = $echoPHP->authorization;

			# AVS Details:
			if ( $echoPHP->avs_result == 'A' )
			$ret['avs'] = 'avs_address_only';
			elseif ( $echoPHP->avs_result == 'E' )
			$ret['avs'] = 'avs_error';
			elseif ( $echoPHP->avs_result == 'N' )
			$ret['avs'] = 'avs_no_match';
			elseif ( $echoPHP->avs_result == 'P' )
			$ret['avs'] = 'avs_na';
			elseif ( $echoPHP->avs_result == 'R' )
			$ret['avs'] = 'avs_retry';
			elseif ( $echoPHP->avs_result == 'S' || $echoPHP->avs_result == 'G')
			$ret['avs'] = 'avs_not_supported';
			elseif ( $echoPHP->avs_result == 'U' )
			$ret['avs'] = 'avs_address_unavail';
			elseif ( $echoPHP->avs_result == 'W' )
			$ret['avs'] = 'avs_fullzip_only';
			elseif ( $echoPHP->avs_result == 'X' )
			$ret['avs'] = 'avs_exact';
			elseif ( $echoPHP->avs_result == 'D' || $echoPHP->avs_result == 'M' )
			$ret['avs'] = 'avs_address_zip';
			elseif ( $echoPHP->avs_result == 'Z' )
			$ret['avs'] = 'avs_partzip_only';
			else
			$ret['avs'] = 'avs_na';
		} 

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