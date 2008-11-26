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
osrs_user
osrs_testkey
osrs_livekey
osrs_enviroment (0/1 test/live)

============================================================================
*/	

set_magic_quotes_runtime(0);
error_reporting(0);

require_once '_opensrs-php/openSRS_base.php'; 

class openSRS extends openSRS_base 
{ 
	var $USERNAME				= OPENSRS_USERNAME;
	var $TEST_PRIVATE_KEY		= TEST_PRIVATE_KEY;
	var $LIVE_PRIVATE_KEY		= LIVE_PRIVATE_KEY;

	var $environment			= 'TEST';	# 'TEST' or 'LIVE'
	var $crypt_type				= 'DES';	# 'DES' or 'BLOWFISH';
	var $protocol				= 'XCP';	# 'XCP' for domains, 'TPP' for email and certs

	var $connect_timeout		= 20;		# seconds
	var $read_timeout			= 20;		# seconds

	var $RELATED_TLDS = array(
		array( '.ca' ),
		array( '.com', '.net', '.org' ),
		array( '.co.uk', '.org.uk' ),
		array( '.vc' ),
		array( '.cc' ),
	);
} 

class plg_reg_OPENSRS 
{
	function plg_reg_OPENSRS ($obj)
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
		if($this->registrar['osrs_user'] == 1)
		$mode = 'live';
		else
		$mode = 'test';

		define('OPENSRS_USERNAME', $this->registrar['osrs_user']);
		define('TEST_PRIVATE_KEY', $this->registrar['osrs_testkey']);
		define('LIVE_PRIVATE_KEY', $this->registrar['osrs_livekey']);

		$O = new openSRS;

		$cmd = array(
			'action'        	=> 	'sw_register',
			'object'        		=> 'DOMAIN',
			'attributes'    	=> 	array(
				'object'        		=>  'DOMAIN',
				'protocal'				=>	'XCP',
				'registrant_ip'			=>	'',
				'domain'        		=> 	$this->domain_name,
				'affiliate_id'  		=> 	'',
				'auto_renew'			=> 	'0',
				'ca_link_domain'		=> 	'0',
				'custom_nameservers'	=>	'1',	      	
				'custom_tech_contact'	=>	'0',
				'domain_description'	=>	'',
				'encoding_type'			=>	'',
				'flock_domain'			=>	'',
				'handle'				=>	'process',
				'isa_trademark'			=>	'0',
				'lang_pref'				=>	'EN',
				'legal_type'			=>	'CCT',
				'link_domains'			=>	'0',
				//'nameserver_list'		=>	'',
				'period'				=>	$this->term,
				'rant_no'				=>	'',
				'reg_domain'			=>	'',
				'reg_password'			=>	'',
				'reg_type'				=>	'new',
				'reg_username'			=>	$this->account['username'],
				'nameserver_list'		=>	array(
					'0'						=> 	array(
						'sortorder'				=>	'1',
						'name'					=>	$this->ns1
						),
					'1'						=> 	array(
						'sortorder'				=>	'2',
						'name'					=>	$this->ns2
						),        			
				),        	
				'tld_data'				=>	array(
					'forwarding_email'		=>	$this->account['email'],
					'nexus'					=>	array(
						'app_purpose'			=>	'',
						'category'				=>	'',
						'validator'				=>	'',
					),
				),       	
				'contact_set'			=> array(
					'admin'					=> array (
						'address1'				=>	$this->account['address1'],
						'address2'				=>	$this->account['address2'],
						'address3'				=>	'',
						'city'					=>	$this->account['city'],
						'country'				=>	'US',
						'email'					=>	$this->account['email'],
						'fax'					=>	'',
						'first_name'			=>	$this->account['first_name'],
						'last_name'				=>	$this->account['last_name'],
						'lang_pref'				=>	'EN',
						'org_name'				=>	$this->account['company'],
						'phone'					=>	'888-555-1212',
						'postal_code'			=>	$this->account['zip'],
						'state'					=>	$this->account['state'],
						'url'					=>	'http://' . $this->domain_name
					),

					'billing'			=> array (
						'address1'				=>	$this->account['address1'],
						'address2'				=>	$this->account['address2'],
						'address3'				=>	'',
						'city'					=>	$this->account['city'],
						'country'				=>	'US',
						'email'					=>	$this->account['email'],
						'fax'					=>	'',
						'first_name'			=>	$this->account['first_name'],
						'last_name'				=>	$this->account['last_name'],
						'lang_pref'				=>	'EN',
						'org_name'				=>	$this->account['company'],
						'phone'					=>	'888-555-1212',
						'postal_code'			=>	$this->account['zip'],
						'state'					=>	$this->account['state'],
						'url'					=>	'http://' . $this->domain_name
					),

					'owner'				=> array (
						'address1'				=>	$this->account['address1'],
						'address2'				=>	$this->account['address2'],
						'address3'				=>	'',
						'city'					=>	$this->account['city'],
						'country'				=>	'US',
						'email'					=>	$this->account['email'],
						'fax'					=>	'',
						'first_name'			=>	$this->account['first_name'],
						'last_name'				=>	$this->account['last_name'],
						'lang_pref'				=>	'EN',
						'org_name'				=>	$this->account['company'],
						'phone'					=>	'888-555-1212',
						'postal_code'			=>	$this->account['zip'],
						'state'					=>	$this->account['state'],
						'url'					=>	'http://' . $this->domain_name
					),       		        		        		
				)
			)
		);

		# get the result
		$result = $O->send_cmd($cmd);

		# debug
		if($this->registrar['debug'] == 1) 
		{		
			echo "<h1>Command</h1>\n";
			print_r($cmd);

			echo "<HR />";
			echo "<h1>Result</h1>\n";
			print_r($result);

			echo "<h1>Command</h1>\n";
			print_r($cmd);

			echo "<HR />";
			echo "<h1>Log</h1>\n";
			$O->showlog();

			echo "<HR />";
			echo "<h1>OPS XML Log</h1>\n";
			$O->_OPS->showlog('xml');

			echo "<HR />";
			echo "<h1>OPS Raw Log</h1>\n";
			$O->_OPS->showlog('raw'); 
		}

		if(@$result['is_success'] == 1)  
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