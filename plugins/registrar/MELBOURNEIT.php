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
melbourne_user
melbourne_pass
melbourne_pgpemail
melbourne_mode (0/1 test/live)

============================================================================
*/

class plg_reg_MELBOURNEIT
{
	function plg_reg_MELBOURNEIT ($obj)
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
				id			= ".$db->qstr( $this->registrar['melbourne_pgpemail'] )." AND
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

		# set the test mode
		if(!$this->registrar['melbourne_mode'])
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


<?
/*
	MELBOURN IT EMAIL TEMPLATE
*/

// Register a new domain now!
function melbourneit_register($account,$domain) {
require_once($path . "melbourneit.setup.php");

// GET THE DOMAIN DETAILS
$subject 	= "New Melbourneit Domain Registration ($domain_name)";

// Determine the correct tld and get the email body...
$mbit = new MELBOURNEIT_MAIL;
$tld =  strtolower(determine_domain_tld($domain_name));

// co.uk
if($tld == "co.uk") {
	$email = $mbit->mail_co_uk($account,$domain);
	if(MELBOURNIT_MODE=="TEST") {
		define('EMAIL_TO','nsiappstest@InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="LIVE") {
		define('EMAIL_TO','nsiapps@prod.InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="OTHER") {
		define('EMAIL_TO',MELBOURNIT_OTHER);
	} else {
		define('EMAIL_TO',setup("email"));
	}


} elseif ((($tld == "com") || ($tld == "net") || ($tld == "org"))) {
	$email = $mbit->mail_c_n_o($account,$domain);
	if(MELBOURNIT_MODE=="TEST") {
		define('EMAIL_TO','nsiappstest@InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="LIVE") {
		define('EMAIL_TO','nsiapps@prod.InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="OTHER") {
		define('EMAIL_TO',MELBOURNIT_OTHER);
	} else {
		define('EMAIL_TO',setup("email"));
	}


} elseif (($tld == "info") || ($tld == "biz")) {
	$email = $mbit->mail_info_biz($account,$domain);
	if(MELBOURNIT_MODE=="TEST") {
		define('EMAIL_TO','nsiappstest@InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="LIVE") {
		define('EMAIL_TO','nsiapps@prod.InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="OTHER") {
		define('EMAIL_TO',MELBOURNIT_OTHER);
	} else {
		define('EMAIL_TO',setup("email"));
	}

} elseif ($tld == "name") {
	$email = $mbit->mail_name($account,$domain);
	if(MELBOURNIT_MODE=="TEST") {
		define('EMAIL_TO','newtldapps@prod.internetnamesww.com');
	} elseif (MELBOURNIT_MODE=="LIVE") {
		define('EMAIL_TO','newtldappstest@internetnamesww.com');
	} elseif (MELBOURNIT_MODE=="OTHER") {
		define('EMAIL_TO',MELBOURNIT_OTHER);
	} else {
		define('EMAIL_TO',setup("email"));
	}

} elseif ($tld == "us") {
	$email = $mbit->mail_us($account,$domain);
	if(MELBOURNIT_MODE=="TEST") {
		define('EMAIL_TO','nsiappstest@InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="LIVE") {
		define('EMAIL_TO','nsiapps@prod.InternetNamesWW.com');
	} elseif (MELBOURNIT_MODE=="OTHER") {
		define('EMAIL_TO',MELBOURNIT_OTHER);
	} else {
		define('EMAIL_TO',setup("email"));
	}

} else {
	if($DEBUG) echo "<BR>The melbourneit registrar module does not support '$tld' type domains!<BR>";
	return FALSE;

}



// Now, send the mail..
mail(EMAIL_TO, $subject, $email, $headers);

// Display debug info
if($DEBUG) echo "<pre> $email </pre>";

return TRUE;
}








// class for construction email templates
class MELBOURNEIT_MAIL {
var $account;
var $domain;


// function for .co.uk domains
function mail_co_uk($account,$domain) {
	global $path,$NAMESEVERS;


	// GET THE DOMAIN DETAILS
	$db = new ps_DB;
	$q   = "SELECT domain_years,domain_name,cp_login FROM domains WHERE domain_id = '$domain'";
	$db->query($q);
	$db->next_record();
	$domain_name 	= $db->f("domain_name");	
	$years 			= $db->f("domain_years");	
	$username		= $db->f("cp_login");


	// GET THE ACCOUNT DETAILS
	$dba = new ps_DB;
	$q   = "SELECT * FROM account WHERE account_id = '$account'";
	$dba->query($q);
	$dba->next_record();
	$name 		= $dba->f("account_name");
	$address	=	$dba->f("account_address");
	$city		=	$dba->f("account_city");
	$state		=	$dba->f("account_state");
	$zip		=	$dba->f("account_zip");
	$country	=	$dba->f("account_country");
	$phone		=	$dba->f("account_phone");
	$fax		=	$dba->f("account_fax");
	$email		=	$dba->f("account_email");
	$company	=	$dba->f("account_company");

	$dbc = new ps_DB;
	$q	= "SELECT country_2_code FROM country WHERE country_id = '$country'";
	$dbc->query($q);
	$dbc->next_record();
	$country = $dbc->f("country_2_code");


	$name		= 	trim($name);
	$fullname 	= 	explode(" ",$name);
	$count		= 	count($fullname) - 1;
	$l_name  	=   $fullname[$count];
	$f_name	 	=	$fullname[0];
	$name		= 	$l_name . ", " . $f_name;



	 $return = 'Authorization
(W)Check         (N)ew         (M)odify         (U)pdate
0a.  Domain Transaction..........:N
0b.  Auth Scheme...........................:
0c.  Auth Info......................................:' . MELBOURNIT_USER . '/' . MELBOURNIT_PASS . '

1.  Comments..........................:Registered Via HostAdmin

2.  Complete Domain Name............:' . $domain_name . '

Organization Using Domain Name
3a.  Organization Name.............:' . 		$company . '
3b.  Street Address...............:' . 			$address . '
3c.  City...............................:' . 	$city . '
3d.  State................................:' . 	$state . '
3e.  Postal Code..........................:' . 	$zip . '
3f.  Country...............................:' . $country . '
3g.  Phone..............................:' . 	$phone . '
3h. Fax ................................:' . 	$fax . '
3i.  Email........................:' . 			$email . '
3k. OganisationType ...............:OTHER
3l. OganisationNumber ...............:0

Administrative Contact
4a.  NIC Handle (if known)..........:
4b.  (I)ndividual (R)ole.......................:I
4c.  Name (Last, First)................:' .		$name . '
4d.  Organization Name.............:' . 		$company . '
4e.  Street Address...............:' . 			$address . '
4f.  City..............................:' . 	$city . '
4g.  State................................:' . 	$state . '
4h. Postal Code...........................:' . 	$zip . '
4i.  Country..................................:' . $country . '
4j.  Phone Number..................:' . 		$phone . '
4k. Fax Number....................:' . 			$fax . '
4l.  E-Mailbox.................:' . 			$email . '

Technical Contact
5a.  NIC Handle (if known)...........:
5b.  (I)ndividual (R)ole......................:
5c.  Name (Last, First).................:
5d.  Organization Name.............:
5e.  Street Address................:
5f.  City...............................:
5g.  State................................:
5h.  Postal Code..........................:
5i.  Country..................................:
5j.  Phone Number..................:
5k.  Fax Number.....................:
5l.  E-Mailbox...................:

Billing Contact
6a.  NIC Handle (if known)...........:
6b.  (I)ndividual (R)ole......................:I
6c.  Name (Last, First).................:' . $name .  '
6d.  Organization Name.............:' . $company . '
6e.  Street Address...............:' . $address . '
6f.  City...............................:' . $city . '
6g.  State................................:' . $state . '
6h.  Postal Code..........................:' . $zip . '
6i.  Country....................................:' . $country . '
6j.  Phone Number..................:' . $phone . '
6k.  Fax Number.....................:' . $fax . '
6l.  E-Mailbox.................:' . $email . '

Primary Name Server
7a.  Primary Server Hostname........:'. 	$NAMESEVERS["0"][server] . '
7b.  Primary Server Netaddress........:'. 	$NAMESEVERS["0"][ip] . '

Secondary Name Server(s)
8a.  Secondary Server Hostname......:'. 	$NAMESEVERS["1"][server] . '
8b.  Secondary Server Netaddress.......:'. 	$NAMESEVERS["1"][ip] . '
8c.  Secondary Server Hostname......:
8d.  Secondary Server Netaddress.......:
8e.  Secondary Server Hostname......:
8f.  Secondary Server Netaddress.......:

Product/Service Options (For New Registrations Only)
9a.  Registration Period (2 yrs).............:2';

return $return;

}






















// function for .com, .net, .org domains
function mail_c_n_o($account,$domain) {
	global $path,$NAMESEVERS;


	// GET THE DOMAIN DETAILS
	$db = new ps_DB;
	$q   = "SELECT domain_years,domain_name,cp_login FROM domains WHERE domain_id = '$domain'";
	$db->query($q);
	$db->next_record();
	$domain_name 	= $db->f("domain_name");	
	$years 			= $db->f("domain_years");	
	$username		= $db->f("cp_login");


	// GET THE ACCOUNT DETAILS
	$dba = new ps_DB;
	$q   = "SELECT * FROM account WHERE account_id = '$account'";
	$dba->query($q);
	$dba->next_record();
	$name 		= 	$dba->f("account_name");
	$address	=	$dba->f("account_address");
	$city		=	$dba->f("account_city");
	$state		=	$dba->f("account_state");
	$zip		=	$dba->f("account_zip");
	$country	=	$dba->f("account_country");
	$phone		=	$dba->f("account_phone");
	$fax		=	$dba->f("account_fax");
	$email		=	$dba->f("account_email");
	$company	=	$dba->f("account_company");

	$dbc = new ps_DB;
	$q	= "SELECT country_2_code FROM country WHERE country_id = '$country'";
	$dbc->query($q);
	$dbc->next_record();
	$country = $dbc->f("country_2_code");


	$name		= 	trim($name);
	$fullname 	= 	explode(" ",$name);
	$count		= 	count($fullname) - 1;
	$l_name  	=   $fullname[$count];
	$f_name	 	=	$fullname[0];
	$name		= 	$l_name . ", " . $f_name;



	 $return = 'Authorization
0a.  (N)ew (M)odify (D)elete......:N
0b.  Auth Scheme..................:
0c.  Auth Info....................:' . MELBOURNIT_USER . '/' . MELBOURNIT_PASS . '

1.   Comments.....................:Registered Via HostAdmin

2.   Complete Domain Name.........:' . $domain_name . '

Organization Using Domain Name
3a.  Organization Name............:' . $company . '
3b.  Street Address...............:' . $address . '
3c.  City.........................:' . $city . '
3d.  State........................:' . $state . '
3e.  Postal Code..................:' . $zip . '
3f.  Country......................:' . $country . '

Administrative Contact
4a.  NIC Handle (if known)........:
4b.  (I)ndividual (R)ole..........:I
4c.  Name (Last, First)...........:' . $name . '
4d.  Organization Name............:' . $company . '
4e.  Street Address...............:' . $address . '
4f.  City.........................:' . $city . '
4g.  State........................:' . $state. '
4h.  Postal Code..................:' . $zip . '
4i.  Country......................:' . $country . '
4j.  Phone Number.................:' . $phone . '
4k.  Fax Number...................:' . $fax . '
4l.  E-Mailbox....................:' . $email . '

Technical Contact
5a.  NIC Handle (if known)........:
5b.  (I)ndividual (R)ole..........:I
5c.  Name (Last, First)...........:' . $name . '
5d.  Organization Name............:' . $company . '
5e.  Street Address...............:' . $address . '
5f.  City.........................:' . $city . '
5g.  State........................:' . $state . '
5h.  Postal Code..................:' . $zip . '
5i.  Country......................:' . $country . '
5j.  Phone Number.................:' . $phone . '
5k.  Fax Number...................:' . $fax . '
5l.  E-Mailbox....................:' . $email . '

Billing Contact
6a.  NIC Handle (if known)........:
6b.  (I)ndividual (R)ole..........:I
6c.  Name (Last, First)...........:' . $name . '
6d.  Organization Name............:' . $company . '
6e.  Street Address...............:' . $address . '
6f.  City.........................:' . $city . '
6g.  State........................:' . $state . '
6h.  Postal Code..................:' . $zip . '
6i.  Country......................:' . $country . '
6j.  Phone Number.................:' . $phone . '
6k.  Fax Number...................:' . $fax . '
6l.  E-Mailbox....................:' . $email . '

Primary Name Server
7a.  Primary Server Hostname......:' . $NAMESEVERS["0"][server] . '
7b.  Primary Server Netaddress....:' . $NAMESEVERS["0"][ip] . '

Secondary Name Server(s)
8a.  Secondary Server Hostname....:' . $NAMESEVERS["1"][server] . '
8b.  Secondary Server Netaddress..:' . $NAMESEVERS["1"][ip] . '

Product/Service Options (For New Registrations Only)
9a.  Registration Period (1-10yrs):' . $years;

return $return; 		
}



















// function for .info, .biz domains
function mail_info_biz($account,$domain) {
	global $path,$NAMESEVERS;


	// GET THE DOMAIN DETAILS
	$db = new ps_DB;
	$q   = "SELECT domain_years,domain_name,cp_login FROM domains WHERE domain_id = '$domain'";
	$db->query($q);
	$db->next_record();
	$domain_name 	= $db->f("domain_name");	
	$years 			= $db->f("domain_years");	
	$username		= $db->f("cp_login");


	// GET THE ACCOUNT DETAILS
	$dba = new ps_DB;
	$q   = "SELECT * FROM account WHERE account_id = '$account'";
	$dba->query($q);
	$dba->next_record();
	$name 		= 	$dba->f("account_name");
	$address	=	$dba->f("account_address");
	$city		=	$dba->f("account_city");
	$state		=	$dba->f("account_state");
	$zip		=	$dba->f("account_zip");
	$country	=	$dba->f("account_country");
	$phone		=	$dba->f("account_phone");
	$fax		=	$dba->f("account_fax");
	$email		=	$dba->f("account_email");
	$company	=	$dba->f("account_company");

	$dbc = new ps_DB;
	$q	= "SELECT country_2_code FROM country WHERE country_id = '$country'";
	$dbc->query($q);
	$dbc->next_record();
	$country = $dbc->f("country_2_code");

	$name		= 	trim($name);
	$fullname 	= 	explode(" ",$name);
	$count		= 	count($fullname) - 1;
	$l_name  	=   $fullname[$count];
	$f_name	 	=	$fullname[0];
	$name		= 	$l_name . ", " . $f_name;



	 $return = 'Authorization
0a.  (W)check (N)ew (M)odify (U)pdate (D)elete..:N
0b.  Auth Scheme..................:
0c.  Auth Info....................:' . MELBOURNIT_USER . '/' . MELBOURNIT_PASS . '
0d.  Module.......................:

1.   Comments.....................:Registered via HostAdmin

2.   Complete Domain Name.........:' .  $domain_name . '
2c.  Courtesy Language............:
2d.  Tracking ID..................:

Organization Using Domain Name
3a.  Organization Name............:' . $company . '
3b.  Street Address Line 1........:' . $address . '
3b2. Street Address Line 2........:
3b3. Street Address Line 3........:
3c.  City.........................:' . $city . '
3d.  State........................:' . $state . '
3e.  Postal Code..................:' . $zip . '
3f.  Country......................:' . $country . '
3g.  Phone Number.................:' . $phone . '
3h.  Fax Number...................:' . $fax . '
3i.  E-mailbox....................:' . $email . '
3j.  Institution..................:' . $company . '

Administrative Contact
4a.  NIC Handle (if known)........:
4b.  (I)ndividual (R)ole..........:I
4c.  Name (Last, First)...........:' . $name . '
4d.  Institution Name.............:' . $company . '
4e.  Street Address Line 1........:' . $address . '
4e2. Street Address Line 2........:
4e3. Street Address Line 3........:
4f.  City.........................:' . $city . '
4g.  State........................:' . $state . '
4h.  Postal Code..................:' . $zip . '
4i.  Country......................:' . $country . '
4j.  Phone Number.................:' . $phone . '
4k.  Fax Number...................:' . $fax . '
4l.  E-Mailbox....................:' . $email . '

Technical Contact
5a.  NIC Handle (if known)........:
5b.  (I)ndividual (R)ole..........:I
5c.  Name (Last, First)...........:' . $name . '
5d.  Institution Name.............:' . $company . '
5e.  Street Address Line 1........:' . $address . '
5e2. Street Address Line 2........:
5e3. Street Address Line 3........:
5f.  City.........................:' . $city . '
5g.  State........................:' . $state . '
5h.  Postal Code..................:' . $zip . '
5i.  Country......................:' . $country . '
5j.  Phone Number.................:' . $phone . '
5k.  Fax Number...................:' . $fax . '
5l.  E-Mailbox....................:' . $email . '

Billing Contact
6a.  NIC Handle (if known)........:
6b.  (I)ndividual (R)ole..........:I
6c.  Name (Last, First)...........:' . $name . '
6d.  Institution Name.............:' . $company . '
6e.  Street Address Line 1........:' . $address . '
6e2. Street Address Line 2........:
6e3. Street Address Line 3........:
6f.  City.........................:' . $city . '
6g.  State........................:' . $state . '
6h.  Postal Code..................:' . $zip . '
6i.  Country......................:' . $country . '
6j.  Phone Number.................:' . $phone . '
6k.  Fax Number...................:' . $fax . '
6l.  E-Mailbox....................:' . $email . '

Primary Name Server
7a.  Primary Server Hostname......:' .  $NAMESEVERS["0"][server] . '
7b.  Primary Server Netaddress....:' .  $NAMESEVERS["0"][ip] . '

Secondary Name Server(s)
8a.  Secondary Server Hostname....:' .  $NAMESEVERS["1"][server] . '
8b.  Secondary Server Netaddress..:' .  $NAMESEVERS["1"][ip] . '
8c.  Secondary Server Hostname....:
8d.  Secondary Server Netaddress..:
8e.  Secondary Server Hostname....:
8f.  Secondary Server Netaddress..:
8g.  Secondary Server Hostname....:
8h.  Secondary Server Netaddress..:
8i.  Secondary Server Hostname....:
8j.  Secondary Server Netaddress..:
8k.  Secondary Server Hostname....:
8l.  Secondary Server Netaddress..:
8m.  Secondary Server Hostname....:
8n.  Secondary Server Netaddress..:
8o.  Secondary Server Hostname....:
8p.  Secondary Server Netaddress..:
8q.  Secondary Server Hostname....:
8r.  Secondary Server Netaddress..:
8s.  Secondary Server Hostname....:
8t.  Secondary Server Netaddress..:
8u.  Secondary Server Hostname....:
8v.  Secondary Server Netaddress..:
8w.  Secondary Server Hostname....:
8x.  Secondary Server Netaddress..:

Product/Service Options (For New Registrations Only)
9a.  Registration Period (1-10yrs):' . $years . '
9b.  Domain Submission Number.....:

.info Trademark Information
10a. Trademark Domain (1/0).......:
10b. Trademark Name...............:
10c. Trademark reference ID.......:
10d. Trademark year...............:
10e. Trade mark country...........:';

return $return; 			
}


















// function for .name domains
function mail_name($account,$domain) {
	global $path,$NAMESEVERS;


	// GET THE DOMAIN DETAILS
	$db = new ps_DB;
	$q   = "SELECT domain_years,domain_name,cp_login FROM domains WHERE domain_id = '$domain'";
	$db->query($q);
	$db->next_record();
	$domain_name 	= $db->f("domain_name");	
	$years 			= $db->f("domain_years");	
	$username		= $db->f("cp_login");


	// GET THE ACCOUNT DETAILS
	$dba = new ps_DB;
	$q   = "SELECT * FROM account WHERE account_id = '$account'";
	$dba->query($q);
	$dba->next_record();
	$name 		= $dba->f("account_name");
	$address	=	$dba->f("account_address");
	$city		=	$dba->f("account_city");
	$state		=	$dba->f("account_state");
	$zip		=	$dba->f("account_zip");
	$country	=	$dba->f("account_country");
	$phone		=	$dba->f("account_phone");
	$fax		=	$dba->f("account_fax");
	$email		=	$dba->f("account_email");
	$company	=	$dba->f("account_company");

	$dbc = new ps_DB;
	$q	= "SELECT country_2_code FROM country WHERE country_id = '$country'";
	$dbc->query($q);
	$dbc->next_record();
	$country = $dbc->f("country_2_code");


	$name		= 	trim($name);
	$fullname 	= 	explode(" ",$name);
	$count		= 	count($fullname) - 1;
	$l_name  	=   $fullname[$count];
	$f_name	 	=	$fullname[0];
	$name		= 	$l_name . ", " . $f_name;



	 $return = 'Authorization
0a. Operation...:N
0c. Auth Info...:' . MELBOURNIT_USER . '/' . MELBOURNIT_PASS . '

2.  Complete Domain Name...:' . 			$domain_name . '
2e. Product type...:name

Organisation Using Domain Name
3a. Name of Organisation...:' . 			$company . '
3b. Address Line #1 of Organisation...:' . 	$address . '
3b2. Address Line #2 of Organisation...:
3b3. Address Line #3 of Organisation...:
3c. Suburb/City of Organisation...:' . 		$city . '
3d. State of Organisation...:' . 			$state . '
3e. Postcode/Zipcode of Organisation...:' . $zip . '
3f. Country of Organisation...:' . 			$country . '
3g. Telephone Number of Organisation...:' . $phone . '
3h. Fax Number of Organisation...:' . 		$fax . '
3i. Email Address of Organisation...:' . 	$email . '
3j. Institution of Organisation...:

Administrative Contact
4b. Position of Organisation Contact...:
4c. Name of Organisation Contact (Last, First)...:' . 	$name . '
4d. Institution of Organisation Contact (Institution)...:
4e. Address Line #1 of Organisation Contact...:' . 		$address . '
4e2. Address Line #2 of Organisation Contact...:
4e3. Address Line #3 of Organisation Contact...:
4f. Suburb/City of Organisation Contact...:' . 			$city . '
4g. State of Organisation Contact...:' . 				$state . '
4h. Postcode/Zipcode of Organisation Contact...:' . 	$zip . '
4i. Country of Organisation Contact...:' . 				$country . '
4j. Telephone Number of Organisation Contact...:' . 	$phone . '
4k. Fax Number of Organisation Contact...:' . 			$fax . '
4l. Email Address of Organisation Contact...:' . 		$email . '

Technical Contact
5b. Position of Technical Contact...:
5c. Name of Technical Contact (Last, First)...:' . 		$name . '
5d. Institution of Technical Contact...:
5e. Address Line #1 of Technical Contact...:' . 		$address . '
5e2. Address Line #2 of Technical Contact...:
5e3. Address Line #3 of Technical Contact...:
5f. Suburb/City of Technical Contact...:' . 			$city . '
5g. State of Technical Contact...:' . 					$state . '
5h. Postcode/Zipcode of Technical Contact...:' . 		$zip . '
5i. Country of Technical Contact...:' . 				$country. '
5j. Telephone Number of Technical Contact...:' . 		$phone . '
5k. Fax Number of Technical Contact...:' . 				$fax . '
5l. Email Address of Technical Contact....:' . 			$email . '

Billing Contact
6b. Position of Billing Contact...:
6c. Name of Billing Contact (Last, First)...:' . 		$name . '
6d. Institution of Billing Contact...:
6e. Address Line #1 of Billing Contact...:' . 			$address . '
6e2. Address Line #2 of Billing Contact...:
6e3. Address Line #3 of Billing Contact...:
6f. Suburb/City of Billing Contact...:' . 				$city . '
6g. State of Billing Contact...:' . 					$state . '
6h. Postcode/Zipcode of Billing Contact...:' . 			$zip . '
6i. Country of Billing Contact...:' . 					$country . '
6j. Telephone Number of Billing Contact...:' . 			$phone . '
6k. Fax Number of Billing Contact...:' .				$fax . '
6l. Email Address of Billing Contact...:' . 			$email . '

Primary Name Server
7a. Primary Name Server Hostname...:' . 				$NAMESEVERS["0"][server] . '
7b. Primary Name Server IP Address...:' . 				$NAMESEVERS["0"][ip] . '

Secondary Name Server
8a. Secondary Name Server Hostname 1...:' . 			$NAMESEVERS["1"][server] . '
8b. Secondary Name Server IP Address 1...:' . 			$NAMESEVERS["1"][ip] . '
8c. Secondary Name Server Hostname 2...:
8d. Secondary Name Server IP Address 2...:
8e. Secondary Name Server Hostname 3...:
8f. Secondary Name Server IP Address 3...:
8g. Secondary Name Server Hostname 4...:
8h. Secondary Name Server IP Address 4...:
8i. Secondary Name Server Hostname 5...:
8j. Secondary Name Server IP Address 5...:
8k. Secondary Name Server Hostname 6...:
8l. Secondary Name Server IP Address 6...:
8m. Secondary Name Server Hostname 7...:
8n. Secondary Name Server IP Address 7...:
8o. Secondary Name Server Hostname 8...:
8p. Secondary Name Server IP Address 8...:
8q. Secondary Name Server Hostname 9...:
8r. Secondary Name Server IP Address 9...:
8s. Secondary Name Server Hostname 10...:
8t. Secondary Name Server IP Address 10...:
8u. Secondary Name Server Hostname 11...:
8v. Secondary Name Server IP Address 11...:
8w. Secondary Name Server Hostname 12...:
8x. Secondary Name Server IP Address 12...:

Product/Service Options
9a. Registration Period...:' . $years . '

Email Forwarding
15a. Email Forwarding Address...:';

return $return; 		
}



















// function for .us domains
function mail_us($account,$domain) {
	global $path,$NAMESEVERS;


	// GET THE DOMAIN DETAILS
	$db = new ps_DB;
	$q   = "SELECT domain_years,domain_name,cp_login FROM domains WHERE domain_id = '$domain'";
	$db->query($q);
	$db->next_record();
	$domain_name 	= $db->f("domain_name");	
	$years 			= $db->f("domain_years");	
	$username		= $db->f("cp_login");


	// GET THE ACCOUNT DETAILS
	$dba = new ps_DB;
	$q   = "SELECT * FROM account WHERE account_id = '$account'";
	$dba->query($q);
	$dba->next_record();
	$name 		= $dba->f("account_name");
	$address	=	$dba->f("account_address");
	$city		=	$dba->f("account_city");
	$state		=	$dba->f("account_state");
	$zip		=	$dba->f("account_zip");
	$country	=	$dba->f("account_country");
	$phone		=	$dba->f("account_phone");
	$fax		=	$dba->f("account_fax");
	$email		=	$dba->f("account_email");
	$company	=	$dba->f("account_company");

	$dbc = new ps_DB;
	$q	= "SELECT country_2_code FROM country WHERE country_id = '$country'";
	$dbc->query($q);
	$dbc->next_record();
	$country = $dbc->f("country_2_code");


	$name		= 	trim($name);
	$fullname 	= 	explode(" ",$name);
	$count		= 	count($fullname) - 1;
	$l_name  	=   $fullname[$count];
	$f_name	 	=	$fullname[0];
	$name		= 	$l_name . ", " . $f_name;



	 $return = 'Authorization
0a. Operation...:N
0c. Auth Info...:' . MELBOURNIT_USER . '/' . MELBOURNIT_PASS . '

2. Complete Domain Name...:' . 			$domain_name . '

Organisation Using Domain Name
3a. Name of Organisation...:' . 			$company . '
3b. Address Line #1 of Organisation...:' . 	$address . '
3b2. Address Line #2 of Organisation...:' . $address . '
3b3. Address Line #3 of Organisation...:' . $address . '
3c. Suburb/City of Organisation...:' . 		$city . '
3d. State of Organisation...:' . 			$state . '
3e. Postcode/Zipcode of Organisation...:' . $zip . '
3f. Country of Organisation...:' . 			$country . '
3g. Telephone Number of Organisation...:' . $phone . '
3h. Fax Number of Organisation...:' . 		$fax . '
3i. Email Address of Organisation...:' . 	$email . '
3j. Institution of Organisation...:' . 		$company . '

Administrative Contact
4b. Position of Organisation Contact...:
4c. Name of Organisation Contact (Last, First)...:' . 		$name . '
4d. Institution of Organisation Contact (Institution)...:' .$company . '
4e. Address Line #1 of Organisation Contact...:' . 			$address . '
4e2. Address Line #2 of Organisation Contact...:' . 		$address . '
4e3. Address Line #3 of Organisation Contact...:' . 		$address . '
4f. Suburb/City of Organisation Contact...:' . 				$city . '
4g. State of Organisation Contact...:' . 					$state . '
4h. Postcode/Zipcode of Organisation Contact...:' . 		$zip . '
4i. Country of Organisation Contact...:' .					$country . '
4j. Telephone Number of Organisation Contact...:' . 		$phone . '
4k. Fax Number of Organisation Contact...:' . 				$fax . '
4l. Email Address of Organisation Contact...:' . 			$email . '

Technical Contact
5b. Position of Technical Contact...:
5c. Name of Technical Contact (Last, First)...:
5d. Institution of Technical Contact...:
5e. Address Line #1 of Technical Contact...:
5e2. Address Line #2 of Technical Contact...:
5e3. Address Line #3 of Technical Contact...:
5f. Suburb/City of Technical Contact...:
5g. State of Technical Contact...:
5h. Postcode/Zipcode of Technical Contact...:
5i. Country of Technical Contact...:
5j. Telephone Number of Technical Contact...:
5k. Fax Number of Technical Contact...:
5l. Email Address of Technical Contact...:

Billing Contact
6b. Position of Billing Contact...:
6c. Name of Billing Contact (Last, First)...:' . $name . '
6d. Institution of Billing Contact...:' . 		$company . '
6e. Address Line #1 of Billing Contact...:' . 	$address . '
6e2. Address Line #2 of Billing Contact...:
6e3. Address Line #3 of Billing Contact...:
6f. Suburb/City of Billing Contact...:' . 		$city . '
6g. State of Billing Contact...:' . 			$state . '
6h. Postcode/Zipcode of Billing Contact...:' . 	$zip . '
6i. Country of Billing Contact...:' . 			$country . '
6j. Telephone Number of Billing Contact...:' . 	$phone . '
6k. Fax Number of Billing Contact...:' . 		$fax . '
6l. Email Address of Billing Contact...:' . 	$email . '

Primary Name Server
7a. Primary Name Server Hostname...:' . 		$NAMESEVERS["0"][server] . '
7b. Primary Name Server IP Address...:' . 		$NAMESEVERS["0"][ip] . '

Secondary Name Server
8a. Secondary Name Server Hostname 1...:' . 	$NAMESEVERS["1"][server] . '
8b. Secondary Name Server IP Address 1...:' . 	$NAMESEVERS["1"][ip] . '
8c. Secondary Name Server Hostname 2...:
8d. Secondary Name Server IP Address 2...:
8e. Secondary Name Server Hostname 3...:
8f. Secondary Name Server IP Address 3...:
8g. Secondary Name Server Hostname 4...:
8h. Secondary Name Server IP Address 4...:
8i. Secondary Name Server Hostname 5...:
8j. Secondary Name Server IP Address 5...:
8k. Secondary Name Server Hostname 6...:
8l. Secondary Name Server IP Address 6...:
8m. Secondary Name Server Hostname 7...:
8n. Secondary Name Server IP Address 7...:
8o. Secondary Name Server Hostname 8...:
8p. Secondary Name Server IP Address 8...:
8q. Secondary Name Server Hostname 9...:
8r. Secondary Name Server IP Address 9...:
8s. Secondary Name Server Hostname 10...:
8t. Secondary Name Server IP Address 10...:
8u. Secondary Name Server Hostname 11...:
8v. Secondary Name Server IP Address 11...:
8w. Secondary Name Server Hostname 12...:
8x. Secondary Name Server IP Address 12...:

Product/Service Options
9a. Registration Period...:' . $years . '

US Nexus Information
14a. RselnexusAppPurpose...:P1
14b. RselnexusCategory...:C21
14c. RstrnexusNameServerCert...:N';

return $return; 		
}

}

?>