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

    username
    password
    reseller              

    ============================================================================
*/

class plg_reg_PLANETDOMAIN
{
	function plg_reg_PLANETDOMAIN ($obj)
	{
		$this->registrar 	= $obj->registrar;
		$this->domainrs		= $obj->domain;

		$this->domain_name	= strtolower($obj->domain['domain_name'].'.'.$obj->domain['domain_tld']);
		$this->domain 		= strtolower($obj->domain['domain_name']);
		$this->tld			= strtolower($obj->domain['domain_tld']);
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

		# Get the country code (2digit): 
		$sql = 'SELECT two_code FROM ' . AGILE_DB_PREFIX . 'country WHERE
				  site_id   = ' . $db->qstr(DEFAULT_SITE) . ' AND
				  id        = ' . $db->qstr($this->account["country_id"]);
		$rs = $db->Execute($sql);
		if($rs == false || $rs->RecordCount() == 0) 
			$this->country = "US";
		else 
			$this->country = $rs->fields["two_code"];


		# set the test/live mode host & url
		if(!$this->registrar['mode'])
		$this->host = 'www.planetdomain.com';
		else
		$this->host = 'www.planetdomain.com'; 
		$this->url = '/servlet/TLDServlet';
	}



	### Register domain
	function register()
	{ 	 			
		# Set the user/pass: 
		$pass_len = 8;
		$user_len = 14;

		# Generate a new username/login:
		$domain = $this->domain_name;

		# set the username
		$username = trim($domain);
		$username = preg_replace("/[-_\.]/", "", $username);
		if(strlen($username) < $user_len)
		{
			$rand = md5(md5($username).time());
			$diff = $user_len - strlen($username);
			$username = $username . substr($rand, 0, $diff);
		} 
		else 
		{ 
			$rand = md5(microtime().md5($username).microtime());
			$username = substr($username, 0, $user_len-5);	
			$username = $username . substr($rand, 0, 5);
		}

		# Set the password
		$password = substr(md5(md5(time()).$domain.$username), 0, 10);			

		# Set the user/pass for the XML queries
		$this->username = strtolower($username);
		$this->password = strtolower($password);

		### create an account
		$vars = array(
				Array ('operation', 		'user.add'),
				Array ('admin.username', 	$this->registrar['username']),
				Array ('admin.password', 	$this->registrar['password']),
				Array ('reseller.id',		$this->registrar['reseller']),
				Array ('response.format',	'KEY_VALUE'), 
				Array ('user.lastname', 	$this->account['last_name']), 
				Array ('user.firstname', 	$this->account['first_name']), 
				Array ('user.address1', 	$this->account['address1']), 
				Array ('user.suburb', 		$this->account['city']), 
				Array ('user.postcode', 	$this->account['zip']), 
				Array ('user.state', 		$this->account['state']), 
				Array ('user.country', 		$this->country), 
				Array ('user.phone', 		'+1.8885551212'), 
				Array ('user.email', 		$this->account['email']), 
				Array ('user.username', 	$this->username), 
				Array ('user.password', 	$this->password) 			
		); 


		# Create the SSL connection & get response from the gateway:	
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$result = $n->connect($this->host, $this->url, $vars, true, 1);

		# Debugging
		$this->debug($vars,$result);

		preg_match ("/(user.id=)+([a-zA-Z0-9]){1,30}/i", $result, $arr); 			 
		if(is_array($arr) && count($arr) > 0) {  
			$id = preg_replace("/user.id=/","", $arr[0]);  	 				
			if(!is_string($id)) 
				return false; 
			else 
				$user_id = $id; 
		} else { 
			return false;
		}


		# register the domain;
		unset($vars);
		$vars = array(
				Array ('operation', 		'domain.register'),
				Array ('admin.username', 	$this->registrar['username']),
				Array ('admin.password', 	$this->registrar['password']),
				Array ('reseller.id',		$this->registrar['reseller']),
				Array ('response.format',	'KEY_VALUE'), 
				Array ('domain.name', 		$this->domain_name),
				Array ('owner.id', 			$user_id),
				Array ('tech.id', 			$this->registrar['reseller']),
				Array ('admin.id', 			$this->registrar['reseller']),
				Array ('billing.id', 		$this->registrar['reseller']),
				Array ('register.period', 	$this->term),
				Array ('ns.name.0', 		$this->ns1),
				Array ('ns.ip.0', 			$this->nsip1),
				Array ('ns.name.1', 		$this->ns2),
				Array ('ns.ip.1', 			$this->nsip2)
		); 

		### .us parameters
		if ($this->tld == 'us')
		{
			$vars[] = Array('us.intended_use', '');
			$vars[] = Array('us.nexus_category', '');
		}

		### .au parameters
		elseif (preg_match('/au/', $this->tld))
		{
			$vars[] = Array('au.registrant.name', $this->account['first_name'].' '.$this->account['last_name']); 

			if($this->tld == 'asn.au' || $this->tld == 'com.au' || $this->tld == 'net.au' || $this->tld == 'org.au')
			{
				$vars[] = Array('au.org.type', 'OTHER'); 
			} 
			elseif($this->tld == 'id.au')
			{
				$vars[] = Array('au.org.type', 'CITIZEN'); 
				$vars[] = Array('au.registrant.address', $this->account['address1'].', '.$this->account['city'].', '.$this->account['state'].' '.$this->account['zip']);
			} 
		}


		# Create the SSL connection & get response from the gateway:	
		include_once (PATH_CORE . 'ssl.inc.php');
		$n = new CORE_ssl;
		$result = $n->connect($this->host, $this->url, $vars, true, 1);

		# Debug
		$this->debug($vars,$result);

		# Result
		if(preg_match('/success=TRUE/i', $result))
			return true;
		else
			return false; 	 
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


	function debug($data,$result=false)
	{
		if($this->registrar['debug']) {
			echo '<B><BR><BR>REQUEST:</B><BR><pre>';
			print_r($data);
			echo "</pre>";
			echo '<B>RESPONSE:</B><BR><pre>';
			echo "$result";
			echo  htmlspecialchars($result);
			echo "</pre>";
		}
	}		
}
?>
