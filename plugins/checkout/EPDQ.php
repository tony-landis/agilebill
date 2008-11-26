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

class plg_chout_EPDQ extends base_checkout_plugin
{
	function plg_chout_EPDQ($checkout_id=false) {

		$this->name 		= 'EPDQ';
		$this->type 		= 'redirect';
		$this->recurr_only	= false;
		$this->return_url 	= SSL_URL . 'plugins/checkout/'. $this->name .'.php';
		$this->success_url  = URL . '?_page=invoice:thankyou&_next_page=invoice:user_view&id=';
		$this->decline_url  = URL . '?_page=invoice:user_view&id=';
		$this->support_cur  = Array ('GBP');
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
 
	 	$currencycode=826;
		
	 	
	 	
	 	/************ start ePDQ encryption ***************/ 
		$server="secure2.epdq.co.uk";
		$url="/cgi-bin/CcxBarclaysEpdqEncTool.e";
		
		#the following parameters have been obtained earlier in the merchant's webstore clientid, passphrase, oid, currencycode, total
		$params="clientid={$this->cfg['clientid']}";		/* ePDQ administrative service ClientID (also Store ID) */
		$params.="&password={$this->cfg['passphrase']}";	/* ePDQ administrative service passphrase */
		$params.="&oid=$invoice";
		$params.="&chargetype={$this->cfg['chargetype']}";	/* Auth (immediate shipment) , PreAuth (delayed shipment) */
		$params.="&currencycode=$currencycode";
		$params.="&total=1.00";
		
		#perform the HTTP Post
		$response = epdqPullpage( $server,$url,$params );
		   
		#split the response into separate lines
		$response_lines=explode("\n",$response);
		
		print_r($response_lines);
		#exit;
		
		#for each line in the response check for the presence of the string 'epdqdata' this line contains the encrypted string
		$response_line_count=count($response_lines);
		for ($i=0;$i<$response_line_count;$i++){
		    if (preg_match('/epdqdata/',$response_lines[$i])){
		        $strEPDQ=$response_lines[$i];
		    }
		}	 
		/************** end ePDQ encryption ***************/
				 
		$this->redirect = '<form name="checkout_redirect" method="POST" action="https://secure2.epdq.co.uk/cgi-bin/CcxBarclaysEpdqEncTool.e" target="_parent">'; 
		$this->redirect .= "$strEPDQ";
		$this->redirect .= '<INPUT type="hidden" name="returnurl" value="'.$this->success_url.$invoice.'">';
		$this->redirect .= '<INPUT type="hidden" name="merchantdisplayname" value="'.SITE_NAME.'">';
		$this->redirect .= '<INPUT TYPE="submit" VALUE="purchase">';
		$this->redirect .= '<script language="JavaScript">document.checkout_redirect.submit();</script>';
		 	
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
	function postback()
	{
		# read the post from PayPal system and add 'cmd'
		global $_POST;
		$req = 'cmd=_notify-validate';

		foreach ($_POST as $key => $value) {
			$value = urlencode(stripslashes($value));
			$req .= "&$key=$value";
		}

		# post back to PayPal system to validate
		$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
		$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
		$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
		$fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

		# needed for validation
		$this->status	 	= $_POST['payment_status'];

		# needed for return
		$ret['invoice_id']		= $_POST['invoice'];
		$ret['transaction_id']	= $_POST['txn_id'];
		$ret['amount']	 		= $_POST['mc_gross'];
		$ret['currency'] 		= $_POST['mc_currency'];

		$do = true;

		# validate vars
		if ($fp)  {
			fputs ($fp, $header . $req);
			while (!feof($fp))  {
				$res = fgets ($fp, 1024);
				if (strcmp ($res, "VERIFIED") == 0)
				{
					# check the payment_status is Completed
					if($this->status == 'Completed' || $this->status == 'Canceled_Reversal')
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
						if($_POST['receiver_email'] == $this->cfg['email'])
						{
							include_once(PATH_MODULES.'checkout/checkout.inc.php');
							$checkout = new checkout;
							$checkout->postback($ret);
							return;
						}
						$rs->MoveNext();
					}
				}
			}
			fclose ($fp);
		}
	}
}

# Postback Function
if(empty($VAR) && empty($VAR['do'])) {
	include_once('../../config.inc.php');
	require_once(PATH_ADODB  . 'adodb.inc.php');
	require_once(PATH_CORE   . 'database.inc.php');
	require_once(PATH_CORE   . 'setup.inc.php'); 
	$C_debug 	= new CORE_debugger;
	$C_db       = &DB();
	$C_setup 	= new CORE_setup; 
	$plg = new plg_chout_PAYPAL;
	$plg->postback(); 
} 


#the following function performs a HTTP Post and returns the whole response
function epdqPullpage( $host, $usepath, $postdata = "" ) {
 
	# open socket to filehandle(epdq encryption cgi)
	 $fp = fsockopen( $host, 80, &$errno, &$errstr, 60 );
	
	#check that the socket has been opened successfully
	 if( !$fp ) {
	    print "$errstr ($errno)<br>\n";
	 }
	 else {
	
	    #write the data to the encryption cgi
	    fputs( $fp, "POST $usepath HTTP/1.0\n");
	    $strlength = strlen( $postdata );
	    fputs( $fp, "Content-type: application/x-www-form-urlencoded\n" );
	    fputs( $fp, "Content-length: ".$strlength."\n\n" );
	    fputs( $fp, $postdata."\n\n" );
	
	    #clear the response data
	   $output = "";
	 
	 
	    #read the response from the remote cgi 
	    #while content exists, keep retrieving document in 1K chunks
	    while( !feof( $fp ) ) {
	        $output .= fgets( $fp, 1024);
	    }
	
	    #close the socket connection
	    fclose( $fp);
	 }
	
	#return the response
	 return $output;
}
?>