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
	
class plg_reg_MANUAL
{
	function plg_reg_MANUAL ($obj)
	{
		$this->registrar 	= $obj->registrar;
		$this->domainrs		= $obj->domain;

		$this->domain_name	= $obj->domain['domain_name'].'.'.$obj->domain['domain_tld'];
		$this->domain 		= $obj->domain['domain_name'];
		$this->tld			= $obj->domain['domain_tld'];
		$this->term 		= $obj->domain['domain_term'];

		if(!empty($obj->server['ns_primary']))
		$this->ns1			= @$obj->server['ns_primary'];
		else
		$this->ns1			= @$obj->registrar['ns1'];

		if(!empty($obj->server['ns_secondary']))
		$this->ns2			= @$obj->server['ns_secondary'];
		else
		$this->ns2			= @$obj->registrar['ns2'];

		if(!empty($obj->server['ns_ip_primary']))
		$this->nsip1 		= @$obj->server['ns_ip_primary'];
		else
		$this->nsip1 		= @$obj->registrar['ns1ip'];

		if(!empty($obj->server['ns_ip_secondary']))
		$this->nsip2 		= @$obj->server['ns_ip_secondary'];
		else
		$this->nsip2 		= @$obj->registrar['ns2ip'];
	}

	### Register domain
	function register()
	{
		# compose the message:
		$msg = $this->emailCompose('REGISTER', $this->domain_name, $this->term, $this->ns1, $this->ns2, $this->nsip1, $this->nsip2);

		# get the account id of the staff member to e-mail to:
		$db = &DB();
		$q = "SELECT account_id FROM  ".AGILE_DB_PREFIX."staff WHERE
				id			= ".$db->qstr( @$this->registrar['manual_add_email'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 0) {
			return false;
		} else {
			$account_id = $rs->fields['account_id'];
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('registrar_manual_admin', $account_id, $this->domainrs['account_id'], '', $msg);
		}
		return true;
	}

	### Transfer domain
	function transfer()
	{
		# compose the message:
		$msg = $this->emailCompose('TRANSFER', $this->domain_name, $this->term, $this->ns1, $this->ns2, $this->nsip1, $this->nsip2);

		# get the account id of the staff member to e-mail to:
		$db = &DB();
		$q = "SELECT account_id FROM  ".AGILE_DB_PREFIX."staff WHERE
				id			= ".$db->qstr( @$this->registrar['manual_transfer_email'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 0) {
			return false;
		} else {
			$account_id = $rs->fields['account_id'];
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('registrar_manual_admin',  $account_id, $this->domainrs['account_id'], '', $msg);
		}
		return true;
	}

	### Park domain
	function park()
	{
		# compose the message:
		$msg = $this->emailCompose('REGISTER', $this->domain_name, $this->term, $this->ns1, $this->ns2, $this->nsip1, $this->nsip2);

		# get the account id of the staff member to e-mail to:
		$db = &DB();
		$q = "SELECT account_id FROM  ".AGILE_DB_PREFIX."staff WHERE
				id			= ".$db->qstr( $this->registrar['manual_park_email'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 0) {
			return false;
		} else {
			$account_id = $rs->fields['account_id'];
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('registrar_manual_admin',  $account_id, $this->domainrs['account_id'], '', $msg);
		}
		return true;
	}

	### Renew domain
	function renew()
	{
		# compose the message:
		$msg = $this->emailCompose('REGISTER', $this->domain_name, $this->term, $this->ns1, $this->ns2, $this->nsip1, $this->nsip2);

		# get the account id of the staff member to e-mail to:
		$db = &DB();
		$q = "SELECT account_id FROM  ".AGILE_DB_PREFIX."staff WHERE
				id			= ".$db->qstr( $this->registrar['manual_renew_email'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($q);
		if ($rs->RecordCount() == 0) {
			return false;
		} else {
			$account_id = $rs->fields['account_id'];
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$mail = new email_template;
			$mail->send('registrar_manual_admin',  $account_id, $this->domainrs['account_id'], '', $msg);
		}
		return true;
	}

	### Compose the domain details for e-mail
	function emailCompose($type, $domain, $term, $ns1, $ns2, $ns1ip, $ns2ip)
	{
		$msg = 	"============================================================\r\n" .
				"Type				: {$type} 								 \r\n" .
				"============================================================\r\n" .
				"Domain name			: ".strtoupper($domain).   			"\r\n" .
				"Term (years)			: {$term}  							 \r\n" .
				"============================================================\r\n" .
				"Primary nameserver		: {$ns1}  							 \r\n" .
				"Secondary nameserver		: {$ns2}  						 \r\n" .
				"Primary nameserver ip		: {$ns1ip} 						 \r\n" .
				"Secondary nameserver ip		: {$ns2ip}  				 \r\n" .
				"============================================================";
		return $msg;
	}
}
?>