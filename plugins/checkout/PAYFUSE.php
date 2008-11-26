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

class plg_chout_PAYFUSE extends base_checkout_plugin
{
	# Get the config values for this checkout plugin:
	function plg_chout_PAYFUSE($checkout_id=false) {


		$this->name 		= 'PAYFUSE';
		$this->type 		= 'gateway'; 			 // redirect, gateway, or other
		$this->recurr_only	= false;
		$this->checkout_id  = $checkout_id;
		$this->support_cur  = Array ('USD');
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
		if($this->cfg['mode'] == "0")  {
			$this->host = 'test5x.clearcommerce.com:11500';
			$mode = 'Y';
		} else {
			$mode = 'P';
			$this->host = 'xmlic.payfuse.com';
		}

		$xml = "<EngineDocList>
	<DocVersion>1.0</DocVersion>
	<EngineDoc>
		<ContentType>OrderFormDoc</ContentType>
		<User>
			<Name>{$this->cfg['name']}</Name>
			<Password>{$this->cfg['password']}</Password>
			<Alias>{$this->cfg['alias']}</Alias>
		</User>
		<Instructions>
			<Pipeline>Payment</Pipeline>
		</Instructions>
		<OrderFormDoc>
			<Mode>{$mode}</Mode>
			<Comments/>
			<Consumer>
				<Email/>
				<PaymentMech>
					<CreditCard>
						<Number>{$this->billing["cc_no"]}</Number>
						<Expires DataType=\"ExpirationDate\" Locale=\"840\">{$this->billing["exp_month"]}/{$this->billing["exp_year"]}</Expires>
						<Cvv2Val>{$this->billing["ccv"]}</Cvv2Val>
						<Cvv2Indicator>1</Cvv2Indicator>
					</CreditCard>
				</PaymentMech>
				<BillTo>
					<Location>
						<TelVoice/>
						<TelFax/>
						<Address>
							<Name>{$this->account["first_name"]} {$acct_fields["last_name"]}</Name>
							<Street1>{$this->account["address1"]}</Street1>
							<Street2>{$this->account["address2"]}</Street2>
							<City>{$this->account["city"]}</City>
							<StateProv>{$this->account["state"]}</StateProv>
							<PostalCode>{$this->account["zip"]}</PostalCode>
							<Country>840</Country>
							<Company/>
						</Address>
					</Location>
				</BillTo>
			</Consumer>
			<Transaction>
				<Type>Auth</Type>
				<CurrentTotals>
					<Totals>
						<Total DataType=\"Money\" Currency=\"840\">". $amount * 100 ."</Total>
					</Totals>
				</CurrentTotals>
			</Transaction>
		</OrderFormDoc>
	</EngineDoc>
</EngineDocList>";	            	


		$vars = Array ( Array ('xml', $xml) );

		# Create the SSL connection & get response from the gateway:
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect($this->host, '', $vars, true, 1);

		# Get return response
		if(!$response)  {
			echo '<script language=Javascript>alert(\'SSL Failed!\') </script>';
			return false;
		} else  {
			preg_match_all ("/<(.*?)>(.*?)\</", $response, $out, PREG_SET_ORDER);
			$n = 0;
			while (isset($out[$n])) {
				$arr[$out[$n][1]] = strip_tags($out[$n][0]);
				$n++;
			}
		}

		/*
		echo '<pre>';
		print_r($arr);
		print_r($response);
		exit;
		*/

		# Transaction Status:
		$str = 'CcErrCode DataType="S32"';
		if ($arr["$str"] == '1')
		$ret['status'] = 1;
		else
		$ret['status'] = 0;

		# AVS:
		$str = 'FraudResult DataType="String"';
		@$ret['avs'] = @$arr["$str"];

		# Message:
		$str = 'CcReturnMsg DataType="String"';
		@$ret['msg'] = @$arr["$str"];

		# Transaction ID
		$str = 'TransactionId DataType="String"';
		@$ret['transaction_id'] = $arr["$str"];

 
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