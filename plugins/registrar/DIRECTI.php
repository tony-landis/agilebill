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

user
pass
parent_id

mode (0/1 test/live)
debug

============================================================================
*/


class plg_reg_DIRECTI
{
	function plg_reg_DIRECTI($obj)
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

	### Register domain
	function register()
	{ 		 
		if($this->registrar['mode'] == 1)
			$SERVICE_URL = "http://www.myorderbox.com/anacreon/servlet/rpcrouter"; // HTTP LIVE SERVICE URL
			// $SERVICE_URL = "https://www.myorderbox.com/anacreon/servlet/rpcrouter"; // HTTPS LIVE SERVICE URL				
		else
			$SERVICE_URL = "http://demo.myorderbox.com:9090/anacreon/servlet/rpcrouter"; // HTTP DEMO SERVICE URL
			// $SERVICE_URL = "https://demo.myorderbox.com:9443/anacreon/servlet/rpcrouter"; // HTTPS DEMO SERVICE URL

		if($this->registrar['debug'] == 1)
			$DEBUG = true;  
		else
			$DEBUG = false;

		$data = Array (	'DIRECTI_USERNAME' 	=> $this->registrar['user'],
						'DIRECTI_PASSWORD' 	=> $this->registrar['pass'],
						'DIRECTI_PARENTID' 	=> $this->registrar['parent_id'],
						'DIRECTI_URL' 		=> $SERVICE_URL,
						'DIRECTI_DEBUG' 	=> $DEBUG,
						'DOMAIN_NAME' 		=> $this->domain_name,
						'TERM' 				=> $this->term,
						'NS1' 				=> $this->ns1,
						'NS2' 				=> $this->ns2,
						'ACCT_USER' 		=> $this->account['email'],
						'ACCT_PASS' 		=> $this->account['password'],
						'ACCT_NAME' 		=> $this->account['first_name'] .' '. $this->account['last_name'],
						'ACCT_ADDR' 		=> $this->account['address1'],
						'ACCT_CITY' 		=> $this->account['city'],
						'ACCT_STATE' 		=> $this->account['state'],
						'ACCT_ZIP' 			=> $this->account['zip'],
						'ACCT_COUNTRY' 		=> $this->country );

		include_once(PATH_CORE.'post.inc.php');
		$post = new CORE_post;
		$result = $post->post_data('192.168.2.6', '/agilebill/plugins/registrar/_directi/register.php', $data);

		#Debug
		if($DEBUG) echo $result;

		if(ereg("REGISTER SUCCESS!", $result)) 
		return true;
		else
		return false;
	}

	function transfer()
	{
		return false;
	}

	function park()
	{
		return false;
	}

	function renew()
	{
		return false;
	}
}
?>