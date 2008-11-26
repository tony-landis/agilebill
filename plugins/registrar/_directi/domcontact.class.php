<?php

	include_once("nusoap.php");

	/**
	* @access private
	*/
	$debugfunction = $DEBUG;

	/**
	* This class consist of functions related to Domain Contacts.
	*/
	class DomContact
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
		function DomContact($wsdlurl="wsdl/domaincontact.wsdl")
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
		* Adds a contact using the details of the customer.
		*
		* @return int
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The customer under whom the contact is to be created 
		* <br><br>A contact is created with the same details (such as email address, postal address, etc) as the customer.
		* <br><br><b>Returns:</b>
		* <br>contactId The Contact Id of the newly added contact.
		*
		*/
		function addDefaultContact($userName,$password,$role,$langpref,$parentid,$customerId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId);
			$return = $this->s->call("addDefaultContact",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Adds a contact using the details provided. 
		*
		* @return int
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param string Name.
		* @param string Company.
		* @param string E-Mail.
		* @param string Address1.
		* @param string Address2.
		* @param string Address3.
		* @param string City.	
		* @param string State.
		* @param string Country.
		* @param string Zip.
		* @param string Country Code.
		* @param string Tel. No.
		* @param string Country Code.
		* @param string Fax No.
		* @param int The customer under whom the contact is to be created.
		* <br><br><b>Returns:</b>
		* <br>contactId: The Contact Id of the newly added contact.
		*
		*/
		function addContact($userName,$password,$role,$langpref,$parentid,$name,$company,$emailAddr,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$faxNoCc,$faxNo,$customerId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$name,$company,$emailAddr,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$faxNoCc,$faxNo,$customerId);
			$return = $this->s->call("add",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Returns the complete details of a particular contact. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The contactId for which details are required
		* @param Array The various details that are required for the order. An Array with no keys specified. Valid entries are: ContactDetails,StatusDetails,All.
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the following information: 
		* <br>	contactid=320
		* <br>	name=Contact
		* <br>	company=FAPI
		* <br>	customerid=21
		* <br>	parentkey=1
		* <br>	emailaddr=contact@fapi.com
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
		* <br>	contacttype=[domorg]
		*
		*/
		function getDetails($userName,$password,$role,$langpref,$parentid,$contactId,$option)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$contactId,$option);
			$return = $this->s->call("getDetails",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* This method returns a list of Contacts that match the specified search criteria. If you do not want to specify a particular criterion, pass null for that parameter. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int Customer Id.
		* @param int[] int[] with no keys specified of ContactIds for listing of specific contacts or null for all contacts
		* @param string[] string[] with no keys specified for listing contacts of specific CurrentStatus or null for all. Valid values are: InActive, Active, Suspended, Deleted
		* @param string UNIX TimeStamp (epoch) for listing contacts created after creationDTRangStart or null for all contacts
		* @param string UNIX TimeStamp (epoch) for listing contacts created before creationDTRangEnd or null for all contacts
		* @param string Searches for contacts whose name contains 'contactName'
		* @param string Searches for contacts whose company contains 'companyName'
		* @param string Searches for contacts whose email address contains 'emailAddr'
		* @param int Number of Records to be returned. The maximum valoue allowed is 50.
		* @param int Page Number for which records are required.
		* @param string[] string[] with no keys specified of Field names for sorting listing of contacts or null for default by contactId 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>AssociativeArray which contains contact details The Key for AssociativeArray is index starting from 1. The Value is another AssociativeArray which contains key-value pairs of contact information.
		*
		*/
		function listContact($userName,$password,$role,$langpref,$parentid,$customerId,$contactId,$currentStatus,$creationDTRangStart,$creationDTRangEnd,$contactName,$companyName,$emailAddr,$numOfRecordPerPage,$pageNum,$orderBy)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId,$contactId,$currentStatus,$creationDTRangStart,$creationDTRangEnd,$contactName,$companyName,$emailAddr,$numOfRecordPerPage,$pageNum,$orderBy);
			$return = $this->s->call("list",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Returns a list of ContactName - CompanyName of all contacts for the specified customerId 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int the customer for whom the list should be returned 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>AssociativeArray which contains contact details The Key for the AssociativeArray is index starting from 1. The Value is another AssociativeArray which contains key-value pairs of contact information with the following keys: 
		* <br>	contactid
		* <br>	name
		* <br>	company
		* <br>	emailaddr
		*
		*/
		function listNames($userName,$password,$role,$langpref,$parentid,$customerId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$customerId);
			$return = $this->s->call("listNames",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Modifies the details for the specified contact. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The contact whose details are to be modified. 
		* @param string Name.
		* @param string Company.
		* @param string E-Mail.
		* @param string Address1.
		* @param string Address2.
		* @param string Address3.
		* @param string City.
		* @param string State.
		* @param string Country.
		* @param string Zip.
		* @param string Country Code.
		* @param string Tel. No.
		* @param string Country Code.
		* @param string Fax No.
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=245
		* <br>	description=DomainContact
		* <br>	actiontype=Mod
		* <br>	actiontypedesc=Modification of Contact Details in the Registry
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Contact modification completed successfully in all registry
		* <br>	status=Success
		*
		*/
		function mod($userName,$password,$role,$langpref,$parentid,$contactId,$name,$company,$emailAddr,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$faxNoCc,$faxNo)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$contactId,$name,$company,$emailAddr,$address1,$address2,$address3,$city,$state,$country,$zip,$telNoCc,$telNo,$faxNoCc,$faxNo);
			$return = $this->s->call("mod",$para);
			$this->debugfunction();
			return $return;
		}
	}

?>
