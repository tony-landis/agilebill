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

    br_user
    br_pass
    br_pgpemail
    br_mode (0/1 test/live)

    ============================================================================
*/

	class plg_reg_BULKREGISTER
	{
		function plg_reg_BULKREGISTER ($obj)
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
			        id			= ".$db->qstr( $this->registrar['br_pgpemail'] )." AND
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
			$q = "SELECT two_code FROM  ".AGILE_DB_PREFIX."country WHERE
			        id			= ".$db->qstr( $this->account['country_id'] )." AND
			        site_id     = ".$db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($q);
			if ($rs->RecordCount() == 1) {
				$this->country = $rs->fields['two_code'];
			}

			# set the test mode
			if(!$this->registrar['br_mode'])
				$this->test = 'T';
			else
				$this->test = '';
		}

        ### Register domain
		function register()
		{
			# compose the message:
			$msg = $this->emailCompose('N');

			# send the e-mail
            if($this->staff_account_id) {
				include_once(PATH_MODULES.'email_template/email_template.inc.php');
				$mail = new email_template;
				$mail->send('registrar_bulkregister_admin',  $this->staff_account_id, '', '', $msg);
				return true;
			}
			return false;
		}

		### Renew domain
		function renew()
		{
			# compose the message:
			$msg = $this->emailCompose('R');

			# send the e-mail
            if($this->staff_account_id) {
				include_once(PATH_MODULES.'email_template/email_template.inc.php');
				$mail = new email_template;
				$mail->send('registrar_bulkregister_admin',  $this->staff_account_id, '', '', $msg);
				return true;
			}
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
 		function emailCompose($type)
 		{

$msg = 'Authorization
0a. (N)ew/W(Renew).............: '.$type.'
0b. Auth Scheme(login handle)..: '.$this->registrar['br_user'].'
0c. Auth Info(login password)..: '.$this->registrar['br_pass'].'
0d. (T)est Mode (OPTIONAL).....: '.$this->test.'


Domains Applied For
2. Complete Domain Name.......: '.strtoupper($this->domain_name).'


Registrant
3a. Organization Name..........: '. $this->account['company'] .'
3b. Street Address.............: '. $this->account['address1'] .'
3c. City.......................: '. $this->account['city'] .'
3d. State......................: '. $this->account['state'] .'
3e. Postal Code................: '. $this->account['zip'] .'
3f. Country....................: '. $this->country .'


Administrative Contact
4a. BR Handle..................:

OR

4c. Name (Last, First).........: '. $this->account['first_name'] .', ' . $this->account['last_name'] . '
4d. Organization Name..........: '. $this->account['company'] .'
4e. Street Address.............: '. $this->account['address1'] .'
4f. City.......................: '. $this->account['city'] .'
4g. State......................: '. $this->account['state'] .'
4h. Postal Code................: '. $this->account['zip'] .'
4i. Country....................: '. $this->country .'
4j. Phone Number...............: 0-000-000-000
4k. Fax Number.................: 0-000-000-000
4l. Email......................: '. $this->account['email'] .'


Technical Contact
5a. BR Handle..................:

OR

5c. Name (Last, First).........: '. $this->account['first_name'] .', ' . $this->account['last_name'] . '
5d. Organization Name..........: '. $this->account['company'] .'
5e. Street Address.............: '. $this->account['address1'] .'
5f. City.......................: '. $this->account['city'] .'
5g. State......................: '. $this->account['state'] .'
5h. Postal Code................: '. $this->account['zip'] .'
5i. Country....................: '. $this->country .'
5j. Phone Number...............: 0-000-000-000
5k. Fax Number.................: 0-000-000-000
5l. Email......................: '. $this->account['email'] .'


Billing Contact
6a. BR Handle..................:

OR

6c. Name (Last, First).........: '. $this->account['first_name'] .', ' . $this->account['last_name'] . '
6d. Organization Name..........: '. $this->account['company'] .'
6e. Street Address.............: '. $this->account['address1'] .'
6f. City.......................: '. $this->account['city'] .'
6g. State......................: '. $this->account['state'] .'
6h. Postal Code................: '. $this->account['zip'] .'
6i. Country....................: '. $this->country .'
6j. Phone Number...............: 0-000-000-000
6k. Fax Number.................: 0-000-000-000
6l. Email......................: '. $this->account['email'] .'


Primary Name Server (OPTIONAL)
7a. Primary Server Hostname....: '.$this->ns1.'
7b. Primary Server IP Address..: '.$this->nsip1.'


Secondary Name Server (OPTIONAL)
8a. Secondary Name Server Hostname..: '.$this->ns2.'
8b. Secondary Name Server IP Address: '.$this->nsip2.'
8c. Additional Server Hostname......:
8d. Additional Server IP Address....:
8e. Additional Server Hostname......:
8f. Additional Server IP Address....:

Registration Period - 1-10 years (OPTIONAL)
9a. Registration Period........: '.$this->term;
			return $msg;
		}
	}
?>