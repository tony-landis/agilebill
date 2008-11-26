<?php

	include_once("nusoap.php");

	/**
	* @access private
	*/
	$debugfunction = $DEBUG;

	/**
	* Fund class contains Transaction related functions.
	*/
	class Fund
	{
		/**
		* @access private
		* @var object
		*/
		var $s;	// This will hold an instance of soapclient class in nusoap.php
		
		/**
		* @access private
		* @var string
		*/
		var $wsdl; // wsdl URL

		/**
		* The constructor which takes soap-url as a parameter.
		*
		* @param string url of wsdl
		*
		* wsdlurl can be passed explicitly.
		* <br>By default wsdl in wsdl dir is used.
		*
		*/
		function Fund($wsdlurl="wsdl/fund.wsdl")
		{
			$this->wsdl = $wsdlurl;
			$this->s = new soapclient($this->wsdl,"wsdl");
		}
		
		/**
		* @access private
		*/
		//This function is to diaplay xml Request/Response.
		function debugfunction()
		{
			global $debugfunction;
			if($debugfunction)
			{
				print "<b>XML Sent:</b><br><br>";
				print "<xmp>" . $this->s->request . "</xmp>";
				print "<br><b>XML Received:</b><br><br>";
				print "<xmp>" . $this->s->response . "</xmp>";
				print "<br>";
			}

		}
		
		/**
		* This function allows you to pay a Customer's pending Invoices or Debit Notes. 
		* 
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int[] int[] of Invoice Ids for which you wish to make the payment.
		* @param int[] int[] of Debit Note Ids for which you wish to make the payment.
		* <br><br><b>Returns:</b>
		*<br>AssociativeArray with the payment details for each of the Invoices and Debit Notes.
		*<br>The returned AssociativeArray will have the Invoice/DebitNote Id as the key, and a AssociativeArray as the value. This inner AssociativeArray will have all the details of the transaction. Each inner AssociativeArray will have a key "status", which will be set to either "Success" or "Error". After payment, an Invoice/Debit Note can be either fully paid or partly paid. If it is partly paid, then the AssociativeArray will also include a key called "pendingamount".
		*
		*/
		function payCustomerTransaction($userName,$password,$role,$langpref,$parentid,$invoiceTransIdArr,$debitNoteIdArr)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$invoiceTransIdArr,$debitNoteIdArr);
			$return = $this->s->call("payCustomerTransaction",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* This function returns the available balance of a customer.
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int Customer id.
		* <br><br><b>Returns:</b>
		* <br>An Associative Array with balance details.
		*
		*/
		function getCustomerAvailableBalance($userName,$password,$role,$langpref,$parentid,$customerId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId);
			$return = $this->s->call("getCustomerAvailableBalance",$para);
			$this->debugfunction();
			return $return;
		}
	}

?>