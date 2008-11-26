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
mode (0/1 test/live)

============================================================================
*/

class plg_reg_ONLINENIC
{
	function plg_reg_ONLINENIC ($obj)
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
		if($this->registrar['mode'] == "1")
			$this->host = 'www.onlinenic.com';
		else
			$this->host = '218.5.81.149';
		$this->port = 20001;

		$this->customer_id = $obj->registrar['user'];
		$this->password = $obj->registrar['pass'];
	}

	### Register domain
	function register()
	{
		### CONNECT
		$this->transaction_id = $this->domainrs['id'].'_login_'.time(); 
		$fp = false; 
		if($this->connectRegServer($fp))
		{  
			### LOGIN
			if($this->onlinenicLogin($fp)) { 					

				# TLD handling
				if($this->tld == 'us') {
					$unspec = "AppPurpose=P1 NexusCategory=C11";
					$domantype = 806;
				} elseif ($this->tld == 'info') {
					$unspec = false;
					$domantype = 805;
				} elseif ($this->tld == 'biz') {
					$unspec = false;
					$domantype = 800;
				} elseif ($this->tld == 'com' || $this->tld == 'net' || $this->tld == 'org') {
					$unspec = false; 
					$domantype = 0;
				}															

				### Get country 
				$db = &DB();
				$sql    = 'SELECT two_code FROM ' . AGILE_DB_PREFIX . 'country WHERE
								site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
								id        = ' . $db->qstr($this->account["country_id"]);
				$rs = $db->Execute($sql);
				if($rs == false || $rs->RecordCount() == 0) 
					$country = "US";
				else 
					$country = $rs->fields["two_code"];

				# ORG
				if(empty($this->account['company']))
					$org = 'None';
				else
					$org = $this->account['company'];			

				### CREATE CONTACT
				$password = $this->generate_password();
				$this->transaction_id = $this->domainrs['id'].'_contact_'.time();
				if ( $contactid = $this->onlinenicRegisterContact( $fp, $domantype, $this->account['first_name'].' '.$this->account['last_name'], $org, $this->account['address1'], $this->account['address2'], '', $this->account['city'], $this->account['state'], $country, $this->account['zip'], '+1.8885551212', '+1.8885551212', $this->account['email'], $password, $this->transaction_id, $unspec))
				{ 
					### REGISTER DOMAIN
					$this->transaction_id = $this->domainrs['id'].'_register_'.time(); 
					if($this->onlinenicRegisterDomain($fp, $domantype, $this->domain.'.'.$this->tld, $this->term, $this->ns1, $this->ns2, $contactid, $contactid, $contactid, $contactid, $password)) 
					{ 
						return true;	
					}	  
				}  
			} 
		}			
		fclose($fp); 
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

	function connectRegServer(&$fp)
	{
		if(!($fp = fsockopen($this->host, $this->port, $errno, $errstr, 90)))
		{ 
			return false;
		}
		//socket_set_blocking($fp, TRUE);
		$i = 0;
		while(!feof($fp))
		{	
			$i ++;
			$line = fgets($fp, 2);
			@$result .= $line;
			if(ereg("</epp>$", $result))
			{
				break;
			}
			if ($i > 5000) break;
		} 
		if(ereg("</greeting></epp>$", $result))
		{
			return true;
		}else {
			return false;
		}
	}


	function sendCommand($fp, $command)
	{
		fputs($fp, $command);
		$i = 0;
		while(!feof($fp))
		{
			$i ++;
			$line = fgets($fp, 2);
			@$result .= $line;
			if(ereg("</epp>$", $result))
			{
				break;
			}
			if ($i > 5000) break;
		} 
		$this->debug($command, $result);
		return $result;
	}


	function onlinenicLogin($fp)
	{
		$clTrid		= $this->transaction_id;
		$checksum	= md5($this->customer_id . md5($this->password) . $clTrid . "login");		 
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
<epp>
<command>
		<creds>
			<clID>{$this->customer_id}</clID>
			<options>
				<version>1.0</version>
				<lang>en</lang>
			</options>
	</creds>
	<clTRID>". $clTrid . "</clTRID>
	<login>
		<chksum>" .$checksum . "</chksum>
	</login>
</command>
</epp>";
		$result = $this->sendCommand($fp, $xml);		
		if(!strstr($result, "<result code=\"1000\">"))
		{
			return false;
		}else {
			return true;
		}
	}


	function onlinenicRegisterDomain($fp, $domain_type, $domain, $year, $dns1, $dns2, $registrant, $admin, $tech, $billing, $password)
	{
		$clTrid		= $this->transaction_id;
		$checksum	= md5($this->customer_id . md5($this->password) . $this->transaction_id . "crtdomain" . $domain_type . $domain . $year . $dns1 . $dns2 . $registrant . $admin . $tech . $billing . $password);
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
<epp>
<command>
	<create>
		<domain:create>
			<domain:type>" . $domain_type . "</domain:type>
			<domain:name>" . $domain . "</domain:name>
			<domain:period>" . $year . "</domain:period>
			<domain:ns1>" . $dns1 . "</domain:ns1>
			<domain:ns2>" . $dns2 . "</domain:ns2>
			<domain:registrant>" . $registrant . "</domain:registrant>
			<domain:contact type=\"admin\">" . $admin . "</domain:contact>
			<domain:contact type=\"tech\">" . $tech . "</domain:contact>
			<domain:contact type=\"billing\">" . $billing . "</domain:contact>
			<domain:authInfo type=\"pw\">" . $password . "</domain:authInfo>
		</domain:create>
	</create>
	<clTRID>" . $clTrid . "</clTRID>
	<chksum>" . $checksum . "</chksum>
</command>
</epp>";
		$result = $this->sendCommand($fp, $xml);			
		if(!strstr($result, "<result code=\"1000\">"))
		{
			return false;
		} 
		return true;
	}



	function onlinenicRegisterContact($fp, $domain_type, $name, $org, $address1, $address2, $address3, $city, $province, $country, $postalcode, $telephone, $fax, $email, $password, $contact_id, $unspec)
	{
		$clTrid		= $this->transaction_id;
		$checksum	= md5($this->customer_id . md5($this->password) . $this->transaction_id . "crtcontact" . $name . $org . $email);	
		$xml = "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
<epp>
<command>
	<create>
		<contact:create>
			<contact:domaintype>" . $domain_type . "</contact:domaintype>
			<contact:ascii>
				<contact:name>" . $name . "</contact:name>
				<contact:org>" . $org . "</contact:org>
				<contact:addr>
					<contact:street1>" . $address1 . "</contact:street1>\n";
		if($address2 != "")
		{
			$xml .= "<contact:street2>" . $address2 . "</contact:street2>\n";
			if($address3 != "")
			{
				$xml .= "<contact:street3>" . $address2 . "</contact:street3>\n";
			}
		}
					$xml .= "<contact:city>" . $city . "</contact:city>
					<contact:sp>" . $province . "</contact:sp>
					<contact:pc>" . $postalcode . "</contact:pc>
					<contact:cc>" . $country . "</contact:cc>
				</contact:addr>
			</contact:ascii>
			<contact:voice>" . $telephone . "</contact:voice>
			<contact:fax>" . $fax . "</contact:fax>
			<contact:email>" . $email . "</contact:email>
			<contact:pw>" . $password . "</contact:pw>
		</contact:create>
	</create>\n";
		if($unspec != "")
		{
			$xml .= "		<unspec>" . $unspec . "</unspec>\n";
		}
		$xml .= "		<clTRID>" . $clTrid . "</clTRID>
	<chksum>" . $checksum . "</chksum>
</command>
</epp>";
		$result = $this->sendCommand($fp, $xml);
		if(!strstr($result, "<result code=\"1000\">"))
		{
			return false;
		}
		return $contact_id = $this->onlinenicGetValue($result, "<contact:id>", "</contact:id>"); 
	}  

	function onlinenicGetValue($msg, $str1, $str2)
	{
		$start_pos = strpos($msg, $str1);
		$stop_post = strpos($msg, $str2);
		$start_pos += strlen($str1);
		return substr($msg, $start_pos, $stop_post - $start_pos);
	}

	function getResultCode($result)
	{
		$start_pos = strpos($result, "<result code=\"");
		return substr($result, $start_pos + 14, 4);
	}

	function generate_password()
	{
		$fillers = "1234567890!@#$%&*-_=+^";
		$fillers .= date('h-i-s, j-m-y, it is w Day z ');
		$fillers .= "123!@#$%&*-_4567!@#$%&*-_890=+^";
		$temp = md5($fillers);
		$temp = substr($temp, 5, 10); 
		return $temp;
	}		

	function debug($data,$result=false)
	{
		if($this->registrar['debug']) {
			echo '<B><BR>REQUEST:</B><BR>';
			echo "<pre>" . htmlspecialchars($data) . "</pre>";
			echo '<B>RESPONSE:</B><BR>';
			echo "<pre>" . htmlspecialchars($result) . "</pre>";
		}
	}		
}
?>