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
	
/*
    ============================================================================
    Available Config Variables:
    ============================================================================
  
    debug (0/1 test/live) 
    mode  (0/1 test/live)
	
	user
	password
	plan_id
	 
    ============================================================================
*/	
class plg_reg_NAMES4EVER
{
	function plg_reg_NAMES4EVER ($obj)
	{
		$this->registrar 	= $obj->registrar;
		$this->domainrs		= $obj->domain;

		$this->domain_name	= $obj->domain['domain_name'].'.'.$obj->domain['domain_tld'];
		$this->domain 		= $obj->domain['domain_name'];
		$this->tld			= $obj->domain['domain_tld'];
		$this->term 		= $obj->domain['domain_term'];

		if(!empty($obj->server['ns_primary']))
		$this->ns1			= $obj->server['ns_primary'];
		else
		$this->ns1			= $obj->registrar['ns1'];

		if(!empty($obj->server['ns_secondary']))
		$this->ns2			= $obj->server['ns_secondary'];
		else
		$this->ns2			= $obj->registrar['ns2'];

		if(!empty($obj->server['ns_ip_primary']))
		$this->nsip1 		= $obj->server['ns_ip_primary'];
		else
		$this->nsip1 		= $obj->registrar['ns1ip'];

		if(!empty($obj->server['ns_ip_secondary']))
		$this->nsip2 		= $obj->server['ns_ip_secondary'];
		else
		$this->nsip2 		= $obj->registrar['ns2ip'];

		# get the account details for the registrant:
		$db = &DB();
		$q = "SELECT * FROM  ".AGILE_DB_PREFIX."account WHERE
				id			= ".$db->qstr( $this->domainrs['account_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 1) {
			$this->account = $rs->fields;
		}

		# get the country code for this account:
		$q = "SELECT three_code FROM  ".AGILE_DB_PREFIX."country WHERE
				id			= ".$db->qstr( $this->account['country_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 1) {
			$this->country = $rs->fields['three_code'];
		}
	}


	######################
	### Register domain
	######################

	function register()
	{     
		global $VAR;
		$VAR['test']=1;

		# generate the xml string 
		$xml_request = '?<?xml version="1.0" ?><request version="1.0" id="REQUEST-123456">'.
		  '<reseller id="'. 		$this->registrar['user'] .'">'.
			'<password>secret'. 	$this->registrar['password'] .'</password>'.
			'<plan-id>'. 			$this->registrar['plan_id'] .'</plan-id>'.
		  '</reseller>'.
		  '<add-domain fqdn="'. 	$this->domain_name .'">'.
			'<password>'. 			$this->account['password'] .'</password>'.
			'<organization>'. 		$this->account['company'] .'</organization>'.
			'<ns priority="1" fqdn="'.$this->ns1 .'">'.
			  '<ip>'. 				$this->nsip1 .'</ip>'.
			'</ns>'.
			'<ns priority="2" fqdn="'.$this->ns2 .'">'.
			  '<ip>'. 				$this->nsip2 .'</ip>'.
			'</ns>'.
			'<contact type="admin">'.
			  '<first-name>'. 		$this->account['first_name'] .'e</first-name>'.
			  '<last-name>'. 		$this->account['last_name'] .'</last-name>'.
			  '<organization>'. 	$this->account['company'] .'</organization>'.
			  '<address>'.
				'<street>'. 		$this->account['address1'] .'</street>'.
				'<city>'. 			$this->account['city'] .'</city>'.
				'<state>'. 		$this->account['state'] .'</state>'.
				'<postalcode>'. 	$this->account['zip'] .'</postalcode>'.
				'<country>'. 		$this->country .'</country>'.
			  '</address>'.
			  '<voice>+1 (888) 555-1212</voice>'.
			  '<email>'. 			$this->account['email'] .'</email>'.
			'</contact>'.
			'<registration-period>'.$this->term .'</registration-period>'.
		  '</add-domain>'. 
		'</request>';

		# Test mode:
		if($this->registrar['mode'] == 1)
		$this->url = '/PirinLink/Pirin.exe';
		else
		$this->url = '/PirinLink/PirinTest.exe';


		# Create the SSL connection & get response from the gateway:	
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$response = $n->connect('names4ever.com', $this->url, $xml_request, true, 1);

		# debug
		#if($this->registrar['debug'] == 1)
		#echo "<textarea>$response</textarea>";

		if(preg_match('@<status code="0">@', $response))  
		return TRUE;
		else
		return FALSE; 
	}


	### Renew domain
	function renew()
	{
		return false;
	}

	### Transfer domain
	function transfer()
	{
		return false;
	}

	### Park domain
	function park()
	{
		return false;
	}
}

?>
