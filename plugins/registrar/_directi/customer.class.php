<?php

	include_once("nusoap.php");

	/**
	* @access private
	*/
	$debugfunction = $DEBUG;
	
	/**
	* This class consist of functions related to Customer.
	*/
	class Customer
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
		function Customer($wsdlurl="wsdl/customer.wsdl")
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
		* Adds a Customer under you using the details provided. 
		*
		* @return int
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param string Customer User Name.
		* @param string Customer Password.
		* @param string Name.
		* @param string Company.
		* @param string Address1.
		* @param string Address2.
		* @param string Address3.
		* @param string City.
		* @param string State.
		* @param string Country.
		* @param string Zip.
		* @param string Country Code.
		* @param string Tel. No.
		* @param string Alternate Country Code.
		* @param string Alternate Tel. No.
		* @param string Country Code.
		* @param string Fax No.
		* @param string Customer Language Preferance.
		* <br><br><b>Returns:</b>
		* <br>customerId: The Customer Id of the newly added Customer.
		*
		*/
		function addCustomer($userName,$password,$role,$langpref,$parentid,$customerUserName,$customerPassword,$name,$company,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$altTelNoCc,$altTelNo,$faxNoCc,$faxNo,$customerLangPref)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerUserName,$customerPassword,$name,$company,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$altTelNoCc,$altTelNo,$faxNoCc,$faxNo,$customerLangPref);
			$return = $this->s->call("addCustomer",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Changes the password for the specified customer. 
		*
		* @return void
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The Customer whose details are to be modified.
		* @param string The New Password.
		*
		*/
		function changePassword($userName,$password,$role,$langpref,$parentid,$customerId,$newPasswd)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId,$newPasswd);
			$return = $this->s->call("changePassword",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Returns the complete details of the specified Customer. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int
		* @param Array Array with no keys specified.
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray  with the following information: 
		* <br>	customerid=123
		* <br>	name=Customer
		* <br>	company=FAPI
		* <br>	parentkey=1
		* <br>	emailaddr=customer@fapi.com
		* <br>	address1=Somewhere
		* <br>	address2=
		* <br>	address3=
		* <br>	city=Someplace
		* <br>	state=
		* <br>	country=SH
		* <br>	zip=12345
		* <br>	telnocc=123
		* <br>	telno=135435456
		* <br>	faxnocc=
		* <br>	faxno=
		* <br>	langpref=en
		* <br>	customerstatus=Active
		* <br>	totalreceipts=20
		*
		*/
		function getDetails($userName,$password,$role,$langpref,$parentid,$customerId,$options)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId,$options);
			$return = $this->s->call("getDetails",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Modifies a Customer's profile using the details provided. 
		*
		* @return void
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The Customer whose details are to be modified.
		* @param string Customer User Name.
		* @param string Name.
		* @param string Company.
		* @param string Language Preferance.
		* @param string Address1.
		* @param string Address2.
		* @param string Address3.
		* @param string City.
		* @param string State.
		* @param string Country.
		* @param string Zip.
		* @param string Country Code.
		* @param string Tel. No.
		* @param string Alternate Country Code.
		* @param string Alternate Tel. No.
		* @param string Country Code.
		* @param string Fax No.
		*
		*/
		function modDetails($userName,$password,$role,$langpref,$parentid,$customerId,$customerUserName,$name,$company,$customerLangPref,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$altTelNoCc,$altTelNo,$faxNoCc,$faxNo)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId,$customerUserName,$name,$company,$customerLangPref,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$altTelNoCc,$altTelNo,$faxNoCc,$faxNo);
			$return = $this->s->call("modDetails",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* This function returns the Customer id of an existing Customer.
		*
		* @return int
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param string Customer User Name.
		*
		*/
		function getCustomerId($userName,$password,$role,$langpref,$parentid,$customerUserName)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerUserName);
			$return = $this->s->call("getCustomerId",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* This function authenticates the Customer.This fuction is invoked only if the role is customer.
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray
		* <br>A AssociativeArray with the Customer information.
		*
		*/
		function authenticateCustomer($userName,$password,$role,$langpref,$parentid,$customerUserName,$customerPasswd)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerUserName,$customerPasswd);
			$return = $this->s->call("authenticateCustomer",$para);
			$this->debugfunction();
			return $return;
		}
	}

?>
