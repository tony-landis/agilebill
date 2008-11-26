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
tr_partnerno
tr_pass
============================================================================
*/

class plg_reg_TOTALREGISTRATIONS
{
	function plg_reg_TOTALREGISTRATIONS ($obj)
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
		$q = "SELECT two_code FROM  ".AGILE_DB_PREFIX."country WHERE
				id			= ".$db->qstr( $this->account['country_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 1) {
			$this->country = $rs->fields['two_code'];
		}
	}

	### Register domain
	function register()
	{
		# compose the message:
		$msg 		= $this->emailCompose('N');
		return $this->sendNow($msg);
	}

	### Renew domain
	function renew()
	{
		# compose the message:
		$msg 		= $this->emailCompose('R');
		return $this->sendNow($msg);
	}

	### Transfer domain
	function transfer()
	{
		# compose the message:
		$msg 		= $this->emailCompose('T');
		return $this->sendNow($msg);
	}

	### Park domain
	function park()
	{
		return false;
	}


	### Send the mail to nominet:
	function sendNow($msg)
	{
		# Get the e-mail detials for this server
		$db = &DB();
		$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
					site_id     = ".$db->qstr(DEFAULT_SITE)." AND
					id          = ".$db->qstr(DEFAULT_SETUP_EMAIL);
		$setup_email        = $db->Execute($q);

		$E['html']		 =  false;
		$E['priority']   =  false;
		$E['to_email']   =  $this->registrar['tr_partnerno'] . "@totalregistrations.com";
		$E['to_name']    =  'TotalRegistrations';
		$E['bcc_list']   =  false;
		$E['cc_list']    =  false;
		$E['subject']    =  $msg;
		$E['body_text']  =  $this->registrar['tr_partnerno'] . " Domain Registration";
		$E['from_name']  =  $setup_email->fields['from_name'];
		$E['from_email'] =  $setup_email->fields['from_email'];

		if($setup_email->fields['type'] == 0) {
			$type = 0;
		} else {
			$type = 1;
			$E['server']    = $setup_email->fields['server'];
			$E['account']   = $setup_email->fields['username'];
			$E['password']  = $setup_email->fields['password'];
		}

		# load the email module
		include_once(PATH_CORE.'email.inc.php');
		$email = new CORE_email;

		# Debug:
		if($this->registrar['debug']) {
			$E['to_email']   =  'billing@dreamcost.com';
			if($type == 0) {
				$email->PHP_Mail($E);
			} else {
				$email->SMTP_Mail($E);
			}
		}

		# Send the message:
		$email = new CORE_email;
		$E['to_email']   =  $this->registrar['tr_partnerno'] . "@totalregistrations.com";
		if($type == 0) {
			if($email->PHP_Mail($E)) return true;
		} else {
			if($email->SMTP_Mail($E)) return true;
		}
	}


	### Compose the message text:
	function emailCompose($type)
	{
$msg =
"Version: 2.0

New Domain Name registration for Total Registrations

Email completed template to [partnernumber]@totalregistrations.com

Authorization
1.   Action (N/M/D/T/R/TR).........: ".$type."

2.   Complete Domain Name..........: ".strtoupper($this->domain_name)."

Organization Using Domain Name
3a.  Organization Name.............: " . $this->account['company'] . "
3b.  Street Address 1..............: " . $this->account['address1'] . "
3c.  Street Address 2..............: " . $this->account['address2'] . "
3d.  Town/City.....................: " . $this->account['city'] . "
3e.  State/County..................: " . $this->account['state'] . "
3f.  Postal/Zip Code...............: " . $this->account['zip'] . "
3g.  Country Code..................: " . $this->country . "

Administrative Contact
4a.  TR Handle (if known)..........:
4b.  Name..........................: " . $this->account['first_name'] .' ' .$this->account['last_name'] . "
4c.  Organization Name.............: " . $this->account['company'] . "
4d.  Street Address 1..............: " . $this->account['address1'] . "
4e.  Street Address 2..............: " . $this->account['address2'] . "
4f.  Town/City.....................: " . $this->account['city'] . "
4g.  State/County..................: " . $this->account['state'] . "
4h.  Postal/Zip Code...............: " . $this->account['zip'] . "
4i.  Country Code..................: " . $this->country . "
4j.  Phone Number..................: 0-000-000-000
4k.  Fax Number....................: 0-000-000-000
4l.  E-Mailbox.....................: " . $this->account['email'] . "

Technical Contact
5a.  TR Handle (if known)..........:
5b.  Name..........................: " . $this->account['first_name'] .' ' .$this->account['last_name'] . "
5c.  Organization Name.............: " . $this->account['company'] . "
5d.  Street Address 1..............: " . $this->account['address1'] . "
5e.  Street Address 2..............: " . $this->account['address2'] . "
5f.  Town/City.....................: " . $this->account['city'] . "
5g.  State/County..................: " . $this->account['state'] . "
5h.  Postal/Zip Code...............: " . $this->account['zip'] . "
5i.  Country Code..................: " . $this->country . "
5j.  Phone Number..................: 0-000-000-000
5k.  Fax Number....................: 0-000-000-000
5l.  E-Mailbox.....................: " . $this->account['email'] . "

Billing Contact
6a.  TR Handle (if known)..........:
6b.  Name..........................: " . $this->account['first_name'] .' ' .$this->account['last_name'] . "
6c.  Organization Name.............: " . $this->account['company'] . "
6d.  Street Address 1..............: " . $this->account['address1'] . "
6e.  Street Address 2..............: " . $this->account['address2'] . "
6f.  Town/City.....................: " . $this->account['city'] . "
6g.  State/County..................: " . $this->account['state'] . "
6h.  Postal/Zip Code...............: " . $this->account['zip'] . "
6i.  Country Code..................: " . $this->country . "
6j.  Phone Number..................: 0-000-000-000
6k.  Fax Number....................: 0-000-000-000
6l.  E-Mailbox.....................: " . $this->account['email'] . "

Primary Name Server
7a.  Primary Server name...........: " . $this->ns1 . "
7b.  Primary Server IP address.....: " . $this->nsip1 . "

Secondary Name Server
8a.  Secondary Server Hostname.....: " . $this->ns2 . "
8b.  Secondary Server IP address...: " . $this->nsip2 . "

Tertiary Name Server
9a.  Tertiary Server Hostname......:
9b.  Tertiary Server IP address....:

10a. Registration Period (1-10yrs).: " . $this->term . "
10b. Bulk Whois Output (Y/N).......: N

11a. Partner Number................: " . $this->registrar['tr_partnerno'] . "
11b. Partner Password..............: " . $this->registrar['tr_pass'] . "
";
		return $msg;
	}
}
?>