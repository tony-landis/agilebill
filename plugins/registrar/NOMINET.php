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

debug (bool)
nominet_tag
pgp_emailto

============================================================================

PGP key instructions:
http://www.nic.uk/TagHolders/UsingTheAutomaton/RegisteringThePgpKey/#email
*/

class plg_reg_NOMINET
{
	function plg_reg_NOMINET ($obj)
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

		# get the account id of the staff member to e-mail to:
		$db = &DB();
		$q = "SELECT account_id FROM  ".AGILE_DB_PREFIX."staff WHERE
				id			= ".$db->qstr( $this->registrar['pgp_emailto'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 1) {
			$this->staff_account_id = $rs->fields['account_id'];
		}

		# get the account details for the registrant:
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
		# compose the message:
		$msg = $this->emailCompose();

		# send the e-mail
		if($this->staff_account_id) {
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('registrar_nominet_admin',  $this->staff_account_id, '', strtoupper($this->registrar['nominet_tag']), $msg);
			return true;
		}
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



	### Compose the message text:
	function emailCompose()
	{

		$msg =
"-------- start of template --------
key:" . $this->domain_name . "
for:" . $this->account['company'] . "
nserver:" . $this->ns1. "
nserver:" . $this->ns2 . "
reg-contact:" . $this->account['first_name'].' '.$this->account['last_name'] . "
reg-trad-name:
reg-type:OTHER
reg-co-no:OTHER
reg-opt-out:
reg-addr:" . $this->account['address1'] . "
reg-addr:
reg-addr:
reg-addr:
reg-city:" .$this->account['city'] . "
reg-county:" . $this->account['state'] . "
reg-country:" . $this->country . "
reg-postcode:" . $this->account['zip'] . "
reg-phone:0-000-000-000
reg-fax:0-000-000-000
reg-email:" . $this->account['email'] . "
admin-c:" . $this->account['first_name'].' '.$this->account['last_name'] . "
phone:0-000-000-000
fax-no:0-000-000-000
e-mail:" . $this->account['email']. "
address:" . $this->account['address1'] . "
address:" . $this->account['city']. "
address:" . $this->account['state']. "
address:" . $this->account['zip']. "
address:" . $this->country . "
tech-c:" . $this->account['first_name'].' '.$this->account['last_name'] . "
phone:0-000-000-000
fax-no:0-000-000-000
e-mail:" . $this->account['email'] . "
address:" . $this->account['address1'] . "
address:" . $this->account['city'] . "
address:" . $this->account['state'] . "
address:" . $this->account['zip'] . "
address:" . $this->country . "
billing-c:" . $this->account['first_name'].' '.$this->account['last_name'] . "
phone:0-000-000-000
fax-no:0-000-000-000
e-mail:" . $this->account['email'] . "
address:" . $this->account['address1'] . "
address:" . $this->account['city'] . "
address:" . $this->account['state'] . "
address:" . $this->account['zip'] . "
address:" . $this->country . "
first-bill:th
recur-bill:bc
notes:
--------- end of template ---------";

		return $msg;
	}
}
?>