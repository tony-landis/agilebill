<?php

	include_once("nusoap.php");

	/**
	* @access private
	*/
	$debugfunction = $DEBUG;

	/**
	* This class consist of functions related to .US contacts.
	*/
	class DomUsContact
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
		function DomUsContact($wsdlurl="wsdl/domainuscontact.wsdl")
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
		* This function need to be called for any contact used as Registrant contact for .US.
		*
		* @return void
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The Registrant contactId for which applicationPurposedetails, nexusCategory are required. 
		* @param string applicationPurpose e.g P1,P2 etc.
		* @param string nexusCategory e.g. C31
		*
		*/
		function setContactDetails($userName,$password,$role,$langpref,$parentid,$contactId,$applicationPurpose,$nexusCategory)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$contactId,$applicationPurpose,$nexusCategory);
			$return = $this->s->call("setContactDetails",$para);
			$this->debugfunction();
			return $return;
		}
	}
?>