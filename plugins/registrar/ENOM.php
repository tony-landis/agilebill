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

enom_user
enom_pass
enom_mode (0/1 test/live)

============================================================================
*/

class plg_reg_ENOM
{
	function plg_reg_ENOM ($obj)
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

		# set the test mode
		if($this->registrar['enom_mode'] == "1")
			$this->host = 'reseller.enom.com';
		else
			$this->host = 'resellertest.enom.com';
	}

	### Register domain
	function register()
	{
		# Compose the data to post:
		$url  = '/interface.asp';
		$vars = array(
				'Command' 					=> 'Purchase',
				'UID' 						=>  $this->registrar['enom_user'],
				'PW' 						=>	$this->registrar['enom_pass'],
				'SLD'						=>	$this->domain,
				'TLD'						=>	$this->tld,
				'NS1'						=>	$this->ns1,
				'NS2'						=>	$this->ns2,
				'RegistrantFirstName'		=>	$this->account['first_name'],
				'RegistrantLastName'		=>	$this->account['last_name'],
				'RegistrantAddress1'		=>	$this->account['address1'],
				'RegistrantCity'			=>	$this->account['city'],
				'RegistrantStateProvince'	=>	$this->account['state'],
				'RegistrantPostalCode'		=>	$this->account['zip'],
				'RegistrantEmailAddress'	=>	$this->account['email'],
				'RegistrantPhone'			=>	'0-000-000-000',
				'RegistrantFax'				=>	'0-000-000-000',
				'RegistrantNexus'			=>	'na',
				'RegistrantPurpose'			=>	'non',
				'UnLockRegistrar'			=>	'0',
				'NumYears'					=>	$this->term,
				'ResponseType'				=>	'HTML'
			);

		# For .co.uk TLDs
		$tld = strtolower($this->tld);
		if($tld == 'co.uk')
		{
			$vars['uk_legal_type'] = 'OTHER';
			$vars['uk_reg_co_no'] = 'NA';
		}

		# Do the post
		include_once(PATH_CORE.'post.inc.php');
		$post = new CORE_post;
		$result = $post->post_data($this->host, $url, $vars);

		# Debug
		if($this->registrar['debug'])
			echo "<BR><u>Result:</u>  $result	<BR>";

		# Get results
		if(preg_match("/Command completed successfully/i", $result)) {
			return TRUE;
		} else {
			return FALSE;
		}
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
