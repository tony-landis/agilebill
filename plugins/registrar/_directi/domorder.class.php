<?php

	include_once("nusoap.php");

	/**
	* @access private
	*/
	$debugfunction = $DEBUG;

	/**
	* This class consist of functions related to Domain Orders. 
	*/
	class DomOrder
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
		function DomOrder($wsdlurl="wsdl/domain.wsdl")
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
		* Adds a Child NameServer on a domain name. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under which the CNS is to be added
		* @param string The name to be used for the Child NameServer (ex. host1.foundationapi.com)
		* @param Array The IP Addresses to be associated with the CNS. A maximum of 13 IP Addresses can be provided.It is an Array with no specific keys for the elements. 
		*<br><br><b>Returns:</b>
		*<br>AssociativeArray with the result of the modification. 
		*<br>	entityid=327
		*<br>	description=apitest04.org
		*<br>	actiontype=AddCns
		*<br>	actiontypedesc=Addition of Child Nameserver host1.apitest04.org with IP [203.23.53.105]
		*<br>	actionstatus=Success
		*<br>	actionstatusdesc=Addition Completed Successfully
		*<br>	status=Success
		*
		*/
		function addChildNameServer($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$ipAddress)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$ipAddress);
			$return = $this->s->call("addChildNameServer",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Returns the availability status of a domainname. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param string The domainname for which availability is to be checked
		* @param boolean If this parameter is true, then availability will be checked for all supported TLD's. If it is false, then availability will be checked only for the specified TLD
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>The AssociativeArray has the domainname as the key and a AssociativeArray as the value The inner AssociativeArray  has two keys - status and classkey {atestdomain.com={status=regthroughothers, classkey=domcno}} Possible values for the status are: available, regthroughus and regthroughothers The classkey denotes the TLD type of the domainname.
		*
		*/
		function checkAvailability($userName,$password,$role,$langpref,$parentid,$domainName,$suggestAlternative)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$domainName,$suggestAlternative);
			$return = $this->s->call("checkAvailability",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Deletes the IP Address associated with a Child NameServer 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under which the CNS to be modifed is created
		* @param string The Child NameServer to be modified
		* @param Array The IP Addresses to be removed. An Array with no specific keys for the elements 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the action. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=DelCnsIp
		* <br>	actiontypedesc=Deletion of IP Address [203.23.53.106] from Child Nameserver dns1.apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function deleteChildNameServerIp($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$ipAddress)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$ipAddress);
			$return = $this->s->call("deleteChildNameServerIp",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* DELETES the specified domain name from the Registry.
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId of the domain name to be deleted. 
		* <br><br>This command must be used with caution. Once a domain is deleted, there are two ways of getting it back: It can then either be Restored (at cost) It must be re-registered once the domain name becomes available in the Registry (between 5-35 days). 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the action. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=DelDomain
		* <br>	actiontypedesc=Deletion of apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Deletion has been completed
		* <br>	status=Success
		*
		*/
		function deleteDomain($userName,$password,$role,$langpref,$parentid,$orderId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId);
			$return = $this->s->call("deleteDomain",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Returns the complete details of a particular order. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId for which details are required
		* @param Array The various details that are required for the order.(OrderDetails,StatusDetails,ContactIds,RegistrantContactDetails,AdminContactDetails,TechContactDetails,BillingContactDetails,NsDetails,DomainStatus,PricingDetails,All)
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>AssociativeArray with name-value pairs for all the details of the order 
		* <br>	orderid=123
		* <br>	entityid=123
		* <br>	description=foundationapi.org
		* <br>	ns2=ns3.logicboxes.com
		* <br>	ns1=ns1.logicboxes.com
		* <br>	entitytypeid=7
		* <br>	eaqid=0
		* <br>	currentstatus=Active
		* <br>	customercost=10.0
		* <br>	parentkey=1
		* <br>	domainstatus=[]
		* <br>	orderstatus=[]
		* <br>	creationtime=1060855310
		* <br>	classname=com.logicboxes.foundation.sfnb.order.domorg.DomOrg
		* <br>	classkey=domorg
		* <br>	domsecret=JKz42pbB5H
		* <br>	cns={},
		* <br>	endtime=1092457910
		* <br>	domainname=foundationapi.org
		* <br>	registrantcontactid=320
		* <br>	admincontactid=320
		* <br>	techcontactid=320
		* <br>	billingcontactid=320
		* <br>If you have requested for the contact details as well, you will get keys as registrantcontact, billingcontact, admincontact and techcontact. The value for these keys will be a AssociativeArray with the following keys: 
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
		function getDetails($userName,$password,$role,$langpref,$parentid,$orderId,$option)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$option);
			$return = $this->s->call("getDetails",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Returns a list of the locks placed on a domain name. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId of the domain name 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the list of locks. The AssociativeArray will have the lock type (customerlock, resellerlock) as the key. The value will be a AssociativeArray with the following details 
		* <br> lockerid=3
		* <br> addedby=Reseller Company
		* <br> creationdt=1063959797
		* <br>lockerid is the Customer/ResellerId of the person who added the lock addedby is the Company Name of the person who added the lock creationdt is the Date the lock was added in Unix timestamp format.
		*
		*/
		function getLockList($userName,$password,$role,$langpref,$parentid,$orderId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId);
			$return = $this->s->call("getLockList",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Returns a list of Domain Names that match the specified search criteria. If you do not want to specify a particular criterion, pass null for object parameters and 0 for numeric parameters. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int[] int[] with no keysspecified of OrderIds for listing Specific Orders
		* @param int[] int[] with no keysspecified of ResellerIds for listing Orders under specific Sub-Reseller(s)
		* @param int[] int[] with no keysspecified of CustomerIds for listing Orders belonging to specific Customer(s)
		* @param boolean
		* @param string[] string[] with no keysspecified to return Orders of specific TLD's (.com	=	domcno,.net	=	domcno,.org	=	domorg,.biz	=	dombiz,.info	=	dominfo,.us	=	domus)
		* @param string[] string[] with no keysspecified for listing Orders having specific Current Status Valid values are: InActive, Active, Suspended, Deleted
		* @param string String for listing of Orders where the domain name matches description. The check is not for an exact match. If any part of the domainname matches description, it will be returned in the search result.
		* @param string UNIX TimeStamp (epoch) for listing Orders created after creationDTRangStart
		* @param string UNIX TimeStamp (epoch) for listing Orders created before creationDTRangEnd
		* @param string 
		* @param string 
		* @param int No. of Records to be returned. The maximum valoue allowed is 50
		* @param int Page Number for which records are required
		* @param string[] string[] with no keysspecified of Field names for sorting Listing of Orders. Default sorting is by orderId. 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>AssociativeArray which contains list of orders matching the search criteria The Keys for the AssociativeArray are values from 1 to n. The Value is another AssociativeArray which contains key-value pairs of domain information. The outerAssociativeArray also contains two additional parameters - 
		* <br>	recsonpage = The no of records returned in this key-value paired array
		* <br>	recsindb = The total no of records available that match the search criteria
		* <br>Keys in the inner key-value paired array per order: 
		* <br>	orders.orderid=101
		* <br>	entity.customerid=21
		* <br>	entity.entityid=101
		* <br>	orders.endtime=1088270630
		* <br>	orders.timestamp=2003-07-01 16:20:55.185655
		* <br>	entity.entitytypeid=5
		* <br>	entity.currentstatus=Active
		* <br>	entitytype.entitytypekey=domorg
		* <br>	orders.creationtime=1056650030
		* <br>	entitytype.entitytypename=.ORG Domain
		* <br>	orders.creationdt=1056445863
		* <br>	entity.description=foundationapi.com
		*
		*/

		function listOrder($userName,$password,$role,$langpref,$parentid,$orderId,$resellerId,$customerId,$showChildOrders,$domainType,$currentStatus,$description,$creationDTRangStart,$creationDTRangEnd,$endTimeRangStart,$endTimeRangEnd,$numOfRecordPerPage,$pageNum,$orderBy)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$resellerId,$customerId,$showChildOrders,$domainType,$currentStatus,$description,$creationDTRangStart,$creationDTRangEnd,$endTimeRangStart,$endTimeRangEnd,$numOfRecordPerPage,$pageNum,$orderBy);
			$return = $this->s->call("list",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Modifies the IP Address associated with a Child NameServer 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under which the CNS to be modifed is created
		* @param string The Child NameServer to be modified
		* @param string The IP Addresses to be removed
		* @param string The new IP Addresses to be added 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=ModCnsIp
		* <br>	actiontypedesc=Modification of Child Namserver IP Address from 203.23.53.105 to 203.23.53.106 for dns1.apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function modifyChildNameServerIp($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$oldIp,$newIp)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$cns,$oldIp,$newIp);
			$return = $this->s->call("modifyChildNameServerIp",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Modifies the name of the specified CNS 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under which the CNS to be modified is created
		* @param string The old name of the Child NameServer (ex. dns1.foundationapi.com)
		* @param string The new name to be used for the Child NameServer (ex. host1.foundationapi.com) 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=ModCnsName
		* <br>	actiontypedesc=Modification of Child Nameserver host1.apitest04.org to dns1.apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function modifyChildNameServerName($userName,$password,$role,$langpref,$parentid,$orderId,$oldCns,$newCns)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$oldCns,$newCns);
			$return = $this->s->call("modifyChildNameServerName",$para);
			$this->debugfunction();
			return $return;
		}
		
		/**
		* Modifies the Contacts associated with a domain name. This call is valid only for those domains that support a transfer secret. Currently, this does not include .com/.net domains. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under for which the Contacts are to be modified
		* @param int The contact to be used as the Registrant
		* @param int The contact to be used as the Admin Contact
		* @param int The contact to be used as the Tech Contact
		* @param int The contact to be used as the Billing Contact 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=ModContact
		* <br>	actiontypedesc=Modification of Contact Details of apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function modifyContact($userName,$password,$role,$langpref,$parentid,$orderId,$registrantContactId,$adminContactId,$techContactId,$billingContactId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$registrantContactId,$adminContactId,$techContactId,$billingContactId);
			$return = $this->s->call("modifyContact",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Modifies the Transfer Secret associated with a domain name. This call is valid only for those domains that support a transfer secret. Currently, this does not include .com/.net domains. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId under for which the Transfer Secret is to be modified
		* @param string The new Transfer Secret to be set for the domain name 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=ModDomainSecret
		* <br>	actiontypedesc=Modification of Domain Secret of apitest04.org
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function modifyDomainSecret($userName,$password,$role,$langpref,$parentid,$orderId,$newSecret)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$newSecret);
			$return = $this->s->call("modifyDomainSecret",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Modifies the NameServers of a domain 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId for which NS's are to be modified
		* @param AssociativeArray The required NS values. The key-value paired array should have the key as ns1...ns13 as required, with the value as the NS to be used. ex. {ns1=ns.foundationapi.com,ns2=ns2.foundationapi.com} 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the modification. 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=ModNS
		* <br>	actiontypedesc=Modification of Nameservers of apitest04.org to [ns3.foundationapi.com, ns2.foundationapi.com]
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Modification Completed Successfully.
		* <br>	status=Success
		*
		*/
		function modifyNameServer($userName,$password,$role,$langpref,$parentid,$orderId,$nsHash)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId,$nsHash);
			$return = $this->s->call("modifyNameServer",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Attempts to Register the specified domain name(s).
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param AssociativeArray This should contain the domain name(s) which are to be registered. The key-value paired array should have the domainname as the key, and the number of years to register the domain for as the value. ex. {domain1.com=1,domain2.net=2}
		* @param Array The NameServers to be used for the domains being registered. The same NS's will be used for all the domain names specified in the domain hash. It is an array with no keys specified.
		* @param int The contact to be used as the Registrant for all the specified domains.
		* @param int The contact to be used as the Admin Contact for all the specified domains.
		* @param int The contact to be used as the Tech Contact for all the specified domains.
		* @param int The contact to be used as the Billing Contact for all the specified domains.
		* @param int The customer under whom the orders should be added
		* @param string This parameter will decide how the Customer Invoices will be handled. NoInvoice - If this value is passed, then no customer invoice will be generated for the domains. PayInvoice - If this value is passed, then a customer invoice will be generated for the domains in the first step. If there is sufficient balance in the Customer's Debit Account, then the invoices will be paid and the domains will be registered. If a customer has less balance than required, then as many domains as possible will be registered with the existing funds. All other orders will remain pending in the system. KeepInvoice - If this value is passed, then a customer invoice will be generated for the domains. However, these invoices will not be paid. They will be kept pending, while the orders will be executed. 
		* <br><br>This method performs the action in two steps - 1. It adds an order in the system for the domain name. 2. It attempts to register the domainname in the Registry. Your Reseller account must have sufficient funds to register the domain names since this is a billable action. 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the domain registration. The AssociativeArray has the domainnames as the key, and a AssociativeArray as the value. The inner AssociativeArray will have key-values as follows: 
		* <br>	entityid=434
		* <br>	description=apitest04.info
		* <br>	actiontype=AddNewDomain
		* <br>	actiontypedesc=Registration of apitest04.info for 1 years
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Domain registration Completed Successfully
		* <br>	status=Success
		* <br>	eaqid=1168
		* <br>Incase you have chosen "KeepInvoice" or "PayInvoice", the return AssociativeArray will also contain the following data: 
		* <br>	customerid=8
		* <br>	invoiceid=727
		* <br>	sellingcurrencysymbol=INR
		* <br>	sellingamount=-500.000
		* <br>	unutilisedsellingamount=-500.000
		* <br>invoiceid is the Id that you will need to pass to Fund.payCustomerTransaction if you wish to pay the invoice at a later date. selllingamount is the Invoice amount in your Selling Currency unutilisedselllingamount is the Pending Invoice amount in your Selling Currency. In case of "KeepInvoice", the pending amount will always be equal to the invoice amount. In case of "PayInvoice", if the Customer does not have sufficient funds to pay the entire invoice amount, unutilisedsellingamount will reflect the balance amount that is pending. If the invoice has been completely paid, the unutilisedsellingamount will be 0.
		*
		*/
		function registerDomain($userName,$password,$role,$langpref,$parentid,$domainHash,$ns,$registrantContactId,$adminContactId,$techContactId,$billingContactId,$customerId,$invoiceOption)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$domainHash,$ns,$registrantContactId,$adminContactId,$techContactId,$billingContactId,$customerId,$invoiceOption);
			$return = $this->s->call("registerDomain",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* UnLocks the specified Domain name. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId of the domain to be locked 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the action 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=Lock
		* <br>	actiontypedesc=Removal of customerlock
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Locking removed successfully
		* <br>	status=Success
		*
		*/
		function removeCustomerLock($userName,$password,$role,$langpref,$parentid,$orderId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId);
			$return = $this->s->call("removeCustomerLock",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Attempts to Renew the specified domain name(s). 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param AssociativeArray This should contain the domain name(s) which are to be renewed.		In $domainHash one has to send Assosiative Array containing Inside one more Assosiative Array containing following info.                                                              array(<domain name> => array("entityid" => <orderId>,"noofyears" => <No ofyears>,"expirydate" => <expiry date in seconds>) and so on.....);                                                        e.g. array("directi.com" => array("entityid" => "123","noofyears" => "1","expirydate" => "2000");
		* @param string This parameter will decide how the Customer Invoices will be handled. NoInvoice If this value is passed, then no customer invoice will be generated for the domains. PayInvoice - If this value is passed, then a customer invoice will be generated for the domains in the first step. If there is sufficient balance in the Customer's Debit Account, then the invoices will be paid and the domains will be registered. If a customer has less balance than required, then as many domains as possible will be registered with the existing funds. All other orders will remain pending in the system. KeepInvoice - If this value is passed, then a customer invoice will be generated for the domains. However, these invoices will not be paid. They will be kept pending, while the orders will be executed. 
		* <br><br>Attempts to Renew the specified domain name(s). This method performs the action in two steps - 1. It adds an action for the Renewal of the Domain. 2. It attempts to renew the domainname in the Registry. Your Reseller account must have sufficient funds to register the domain names since this is a billable action.
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the Renwwal. The AssociativeArray has the domainnames as the key, and a AssociativeArray as the value. The inner AssociativeArray will have key-values as follows: 
		* <br>	entityid=435
		* <br>	description=apitest04.com
		* <br>	actiontype=RenewDomain
		* <br>	actiontypedesc=Renewal of apitest04.com for 1 years
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Domain renewed successully
		* <br>	status=Success
		* <br>	eaqid=1169
		* <br>Incase you have chosen "KeepInvoice" or "PayInvoice", the return AssociativeArray will also contain the following data: 
		* <br>	customerid=8
		* <br>	invoiceid=727
		* <br>	sellingcurrencysymbol=INR
		* <br>	sellingamount=-500.000
		* <br>	unutilisedsellingamount=-500.000
		* <br>invoiceid is the Id that you will need to pass to Fund.payCustomerTransaction if you wish to pay the invoice at a later date. selllingamount is the Invoice amount in your Selling Currency unutilisedselllingamount is the Pending Invoice amount in your Selling Currency. In case of "KeepInvoice", the pending amount will always be equal to the invoice amount. In case of "PayInvoice", if the Customer does not have sufficient funds to pay the entire invoice amount, unutilisedsellingamount will reflect the balance amount that is pending. If the invoice has been completely paid, the unutilisedsellingamount will be 0.
		*
		*/
		function renewDomain($userName,$password,$role,$langpref,$parentid,$domainHash,$invoiceOption)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$domainHash,$invoiceOption);
			$return = $this->s->call("renewDomain",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Locks the specified Domain name. When a domain name is locked, no changes can be made to ir unless it is unlocked. 
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param int The orderId of the domain to be locked 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the action 
		* <br>	entityid=327
		* <br>	description=apitest04.org
		* <br>	actiontype=Lock
		* <br>	actiontypedesc=Addition of customerlock
		* <br>	actionstatus=Success
		* <br>	actionstatusdesc=Locking completed successfully
		* <br>	status=Success
		*
		*/
		function setCustomerLock($userName,$password,$role,$langpref,$parentid,$orderId)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$orderId);
			$return = $this->s->call("setCustomerLock",$para);
			$this->debugfunction();
			return $return;
		}

		/**
		* Attempts to place a Transfer order for the specified domain name(s).
		*
		* @return AssociativeArray
		* @param string Username.
		* @param string Password.
		* @param string Role.
		* @param string Language Preference.
		* @param int Parent id.
		* @param AssociativeArray This should contain the domain name(s) which are to be registered. The AssociativeArray should have the domainname as the key, and the domain transfer secret as the value. In case of domains which do not have a transfer secret, an empty string should be passed. ex. {domain1.com=secret1,domain2.net=secret2}
		* @param int The contact to be used as the Registrant for all the specified domains.
		* @param int The contact to be used as the Admin Contact for all the specified domains.
		* @param int The contact to be used as the Tech Contact for all the specified domains.
		* @param int The contact to be used as the Billing Contact for all the specified domains.
		* @param int The customer under whom the orders should be added
		* @param string This parameter will decide how the Customer Invoices will be handled. NoInvoice - If this value is passed, then no customer invoice will be generated for the domains. PayInvoice - If this value is passed, then a customer invoice will be generated for the domains in the first step. If there is sufficient balance in the Customer's Debit Account, then the invoices will be paid and the domains will be registered. If a customer has less balance than required, then as many domains as possible will be registered with the existing funds. All other orders will remain pending in the system. KeepInvoice - If this value is passed, then a customer invoice will be generated for the domains. However, these invoices will not be paid. They will be kept pending, while the orders will be executed. 
		* <br><br>This method performs the action in two steps - 1. It adds an order in the system for the domain name. 2. It attempts to register the domainname in the Registry. Your Reseller account must have sufficient funds to register the domain names since this is a billable action. 
		* <br><br><b>Returns:</b>
		* <br>AssociativeArray.
		* <br>A AssociativeArray with the result of the Transfer order. The AssociativeArray has the domainnames as the key, and a AssociativeArray as the value. The inner AssociativeArray will have key-values as follows: 
		* <br>	entityid=435
		* <br>	description=apitest04.com
		* <br>	actiontype=AddTransferDomain
		* <br>	actiontypedesc=Transfer of apitest04.com from old Registrar alongwith 1 year Renewal
		* <br>	actionstatus=RFASent
		* <br>	actionstatusdesc=Transfer waiting for Admin Contact Approval
		* <br>	status=Success
		* <br>	eaqid=1169
		* <br>Incase you have chosen "KeepInvoice" or "PayInvoice", the return key-value paired array will also contain the following data: 
		* <br>	customerid=8
		* <br>	invoiceid=727
		* <br>	sellingcurrencysymbol=INR
		* <br>	sellingamount=-500.000
		* <br>	unutilisedsellingamount=-500.000
		* <br>invoiceid is the Id that you will need to pass to Fund.payCustomerTransaction if you wish to pay the invoice at a later date. selllingamount is the Invoice amount in your Selling Currency unutilisedselllingamount is the Pending Invoice amount in your Selling Currency. In case of "KeepInvoice", the pending amount will always be equal to the invoice amount. In case of "PayInvoice", if the Customer does not have sufficient funds to pay the entire invoice amount, unutilisedsellingamount will reflect the balance amount that is pending. If the invoice has been completely paid, the unutilisedsellingamount will be 0.
		*
		*/
		function transferDomain($userName,$password,$role,$langpref,$parentid,$domainHash,$registrantContactId,$adminContactId,$techContactId,$billingContactId,$customerId,$invoiceOption)
		{
			$para = array($userName,$password,$role,$langpref,$parentid,$domainHash,$registrantContactId,$adminContactId,$techContactId,$billingContactId,$customerId,$invoiceOption);
			$return = $this->s->call("transferDomain",$para);
			$this->debugfunction();
			return $return;
		}
	}

?>