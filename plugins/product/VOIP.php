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
 * @author Tony Landis <tony@agileco.com> and Thralling Penguin, LLC <http://www.thrallingpenguin.com>
 * @package AgileBill
 * @version 1.4.93
 */
	
require_once PATH_MODULES.'voip/base_voip_plugin.inc.php';

class voipDID
{
	var $data;
	var $plugin_id = 0;
	var $has_callwaiting = 1;
	
	function load($did)
	{
		$db =& DB();
		$rs = $db->Execute($sql=sqlSelect($db,"voip_did","*","did=::$did::"));
		#echo $sql."<BR />";
		$this->data = $rs->fields;
	}
	
	function save()
	{
		if(!is_array($this->data)) {
			echo "Invalid state to save voipDID.";
			return;
		}
		$db =& DB();
		$sql = sqlUpdate($db,"voip_did",$this->data,"id=::".$this->data['id']."::");
		#echo $sql."<BR />";
		$db->Execute($sql);
	}
	
	function insert()
	{
		if(!is_array($this->data)) {
			echo "Invalid state to insert voipDID.";
			return;
		}	
		$db =& DB();
		$sql = sqlInsert($db,"voip_did",$this->data);
		#echo $sql."<BR />";		
		if(!$db->Execute($sql)) {
			echo $db->ErrorMsg();
		}
	}
	
	function delete()
	{
		if(!is_array($this->data)) {
			echo "Invalid state to delete voipDID.";
			return;
		}	
		$db =& DB();
		$db->Execute(sqlDelete($db,"voip_did","id=::".$this->data['id']."::"));
	}
	
	function getPluginID()
	{
		if($this->plugin_id) return $this->plugin_id;
		
		require_once PATH_MODULES."voip/voip.inc.php";
		$v = new voip;
		$didtable = $v->get_did_e164($this->data['did']);
		$this->plugin_id = $didtable['voip_did_plugin_id'];
		return $this->plugin_id;
	}
	
	function getDID()
	{
		return $this->data['did'];
	}
	
	function setDID($did)
	{
		$this->data['did'] = $did;
	}
	
	function getID()
	{
		return $this->data['id'];
	}
	
	function getAccountID()
	{
		return $this->data['account_id'];
	}
	
	function setAccountID($id)
	{
		$this->data['account_id'] = $id;
	}
	
	function getActive()
	{
		return $this->data['active'];
	}
	
	function setActive($v)
	{
		$this->data['active'] = $v;
	}
	
	function setServiceID($v)
	{
		$this->data['service_id'] = $v;
	}
	
	function setServiceParentID($v)
	{
		$this->data['service_parent_id'] = $v;
	}
	
	function setCNAM($v)
	{
		$this->data['cnam'] = $v;
	}
	
	function setBlacklist($v)
	{
		$this->data['blacklist'] = $v;
	}
	
	function setANIRouting($v)
	{
		$this->data['anirouting'] = $v;
	}
	
	function setVoicemail($v)
	{
		$this->data['voicemailenabled'] = $v;
		$this->data['voicemailafter'] = 30;
	}
	
	function setFailover($v)
	{
		$this->data['failover'] = $v;
	}
	
	function setRemoteCallForwarding($v)
	{
		$this->data['remotecallforward'] = $v;
	}
	
	function setFax($v, $email)
	{
		$this->data['rxfax'] = $v;
		$this->data['faxemail'] = $email;
	}
	
	function setConference($v, $lim)
	{
		$this->data['conf'] = $v;
		$this->data['conflimit'] = $lim;
	}
	
	function setFaxDetection($v, $email)
	{
		$this->data['faxdetection'] = $v;
		$this->data['faxemail'] = $email;
	}
	
	function setBusyCallForward($v)
	{
		$this->data['busycallforwardenabled'] = $v;
	}
	
	function setCallForward($v)
	{
		$this->data['callforwardingenabled'] = $v;
	}
	
	function setChannel($v)
	{
		$this->data['channel'] = $v;
	}
	
	function setChannelArg($v)
	{
		$this->data['channelarg'] = $v;
	}
	
	function setCallWaiting($v)
	{
		$this->has_callwaiting = $v;
	}
	
	function getCallWaiting()
	{
		return $this->has_callwaiting;
	}
}

/**
 * Parent class for VoIP provisioning
 */ 
class voip_provisioning
{
	/**
	 * The default voicemail PIN code
	 */
	var $voip_vm_passwd;
	
	/**
	 * The method of password generation
	 */
	var $voip_secret_gen;
	
	/**
	 * Reference to underlying product plugin
	 */	 	
	var $prod_plugin;
	
	/**
	 * Authorized domain
	 */	 	
	var $voip_domain;
	
	function voip_provisioning(&$prod_plugin)
	{
		$this->prod_plugin =& $prod_plugin;
	}
	
	function load_config()
	{
    $db =& DB();
		$sql = sqlSelect($db, "voip", "voip_vm_passwd, voip_secret_gen, auth_domain", "");
		$rs = $db->Execute($sql);
		$this->voip_vm_passwd = $rs->fields['voip_vm_passwd'];
		$this->voip_secret_gen = $rs->fields['voip_secret_gen'];
		$this->voip_domain = $rs->fields['auth_domain'];
	}
	
	function generate_secret(&$didClass)
	{
		$this->load_config();
		switch ($this->voip_secret_gen) {
			case 0:
				$secret = substr(ereg_replace("[^0-9]","",microtime()),0,7);
				break;
			case 1:
				$secret = strrev($didClass->getDID());
				break;
			case 2:
				$secret = $didClass->getDID();
				break;
			default:
				$secret = strrev($didClass->getDID());
				break;
		}
		return $secret;
	}

	function call_did_plugin(&$didClass, $method)
	{
		$db =& DB();
		if(!is_a($didClass,'voipDID')) {
			echo 'First parameter was not a voipDID class.';
			exit;
		}
		$plugin_id = $didClass->getPluginID();
		
		$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","plugin,avail_countries","id = $plugin_id"));
		if($rs && $rs->RecordCount() > 0) {
		 	$plugin = $rs->fields['plugin']; 			 
		} else {
			return false;
		}
		
		// load the plugin and call purchase(); 
		$file = PATH_PLUGINS.'voip_did/'.$plugin.'.php';
		if(is_file($file)) {
			include_once($file);
			eval('$plg = new plgn_voip_did_'.$plugin.';'); 
			if(is_object($plg)) {
				if(is_callable(array($plg, $method))) {
					$plg->id = $plugin_id;
					$plg->did = $didClass->getDID(); 		
					$plg->did_id = $didClass->getID();
					$plg->account_id = $didClass->getAccountID(); 
					eval('$plg->'.$method.'();');
					return true;
				}
			}  
		}
		return false;
	}
	
	function add_in_network(&$didClass)
	{
		if(!is_a($didClass,'voipDID'))
			die('Parameter must be a voipDID');
		$db =& DB();
		$f = array( 
			'did'		=> $didClass->getDID(),
			'site_id'	=> DEFAULT_SITE
		);
		$sql = sqlInsert($db,"voip_in_network",$f);
		#echo $sql."<BR />";		
		$db->Execute($sql);
	}
	
	function delete_in_network(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('Parameter must be of voipDID');
		$db =& DB();
		$sql = sqlDelete($db,"voip_in_network","did=::".$didClass->getDID()."::");
		#echo $sql."<BR />";
		$db->Execute($sql);
	}
	
	function add_voicemail(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
			
		$db =& DB();
		$this->load_config();

		# get the account information
		$account = $db->Execute(sqlSelect($db,"account","first_name,last_name,email","id=::".$didClass->getAccountID()."::"));
		
		$f = array(
			'account_id'		=> $didClass->getAccountID(),
			'context'				=> 'default',
			'mailbox'				=> $didClass->getDID(),
			'password'			=> $this->voip_vm_passwd,
			'fullname'			=> $account->fields['first_name']." ".$account->fields['last_name'],
			'email'					=> $account->fields['email']
		);
		$db->Execute($sql=sqlInsert($db,"voip_vm",$f));
		#echo $sql."<BR />";
	}
	
	function delete_voicemail(&$didClass)
	{
		$db =& DB();
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
		$db->Execute($sql=sqlDelete($db,"voip_vm","context=::default:: and mailbox=::".$didClass->getDID()."::"));
		#echo $sql."<BR />";
	}
}


class ser_voip_provisioning extends voip_provisioning
{
	var $ser_host = 'localhost';
	var $ser_type = 'mysql';
	var $ser_name = 'serdb';
	var $ser_user = '';
	var $ser_pass = '';
	var $tbl_subscriber = 'subscriber';
	var $tbl_alias = 'aliases';
	var $tbl_group = 'grp';
	var $tbl_acc = 'acc';
	var $ser_ip	= 'localhost';

	function & DBFactory()
	{
		static $dbc;
		
		if(!is_resource($dbc)) {
			$dbc = NewADOConnection($this->ser_type);
			$dbc->Connect($this->ser_host, $this->ser_user, $this->ser_pass, $this->ser_name);
		}
		return $dbc; 
	}
		
	function ser_voip_provisioning(&$prod_plugin)
	{
		parent::voip_provisioning($prod_plugin);
		
		# Load our needed configuration items
		$this->ser_ip = $prod_plugin->plugin_data['ser_ip'];
		$this->ser_host = $prod_plugin->plugin_data['ser_db_host'];
		$this->ser_name = $prod_plugin->plugin_data['ser_db_name'];
		$this->ser_user = $prod_plugin->plugin_data['ser_db_user'];
		$this->ser_pass = $prod_plugin->plugin_data['ser_db_pass'];
		$this->tbl_subscriber = $prod_plugin->plugin_data['ser_db_subscriber'];
		$this->tbl_alias = $prod_plugin->plugin_data['ser_db_alias'];
		$this->tbl_group = $prod_plugin->plugin_data['ser_db_grp'];
		$this->tbl_acc = $prod_plugin->plugin_data['ser_db_acc'];
	}
		
	function addCustom(&$didClass)
	{
	}
	
	function removeCustom(&$didClass)
	{
	}
	
	function addDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must of voipDID');

		$this->load_config();
		$db =& DB();
		
		$account = $db->Execute(sqlSelect($db,"account","*","id=::".$didClass->getAccountID()."::"));
		$pw = $this->generate_secret($didClass);
		
		$dbs =& $this->DBFactory();
		$f = array(
			'phplib_id'				=> md5($didClass->getDID()),
			'username'				=> $didClass->getDID(),
			'domain'					=> $this->voip_domain,
			'password'				=> $pw,
			'first_name'			=> $account->fields['first_name'],
			'last_name'				=> $account->fields['last_name'],
			'flag'						=> 'o',
			'ha1'							=> md5($didClass->getDID().":".$this->voip_domain.":".$pw),
			'ha1b'						=> md5($didClass->getDID()."@".$this->voip_domain.":".$this->voip_domain.":".$pw)
		);
		$fsql = ""; $vsql = "";
		foreach($f as $fld => $val) {
			$fsql .= $fld.",";
			$vsql .= $db->qstr($val).",";
		}
		$fsql = substr($fsql,0,strlen($fsql)-1);
		$vsql = substr($vsql,0,strlen($vsql)-1);
		$sql = "INSERT INTO ".$this->tbl_subscriber." ($fsql) VALUES ($vsql)";
		#echo $sql."<br>";
		if(!$dbs->Execute($sql)) echo $dbs->ErrorMsg();
		
		# store off the group permissions
		$grps = explode(",",str_replace(" ","",$this->prod_plugin->plugin_data['context']));
		foreach($grps as $group) {
			$f = array(
				'username'			=>	$db->qstr($didClass->getDID()),
				'domain'				=>	$db->qstr($this->voip_domain),
				'grp'						=>	$db->qstr($group),
				'last_modified'	=> 'NOW()'
			);
			$fsql = ""; $vsql = "";
			foreach($f as $fld => $val) {
				$fsql .= $fld.",";
				$vsql .= $val.",";
			}
			$fsql = substr($fsql,0,strlen($fsql)-1);
			$vsql = substr($vsql,0,strlen($vsql)-1);
			$sql = "INSERT INTO ".$this->tbl_group." ($fsql) VALUES ($vsql)";
			#echo $sql."<br />";
			if(!$dbs->Execute($sql)) echo $dbs->ErrorMsg();
		}
	}
	
	function addVirtualDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die("Parameter is not a voipDID class.");
		
		$this->load_config();
		$db =& DB();
				
		$parent_did = $this->prod_plugin->get_parent_did($this->prod_plugin->prod_attr_cart['parent_service_id']);
		$contact = 'sip:'.$parent_did.'@'.$this->ser_ip;
		
		$dbs =& $this->DBFactory();
		$f = array(
			'username'			=>	$dbs->qstr($didClass->getDID()),
			'domain'				=>	$dbs->qstr($this->voip_domain),
			'contact'				=>	$dbs->qstr($contact),
			'expires'				=>	$dbs->qstr('2010-12-25 01:01:01'),
			'q'							=>	'1',
			'callid'				=>	$dbs->qstr('The-Answer-To-The-Ultimate-Question-Of-Life-Universe-And-Everything'),
			'cseq'					=>	'42',
			'last_modified'	=>	'NOW()',
			'replicate'			=>	'0',
			'state'					=>	'0',
			'flags'					=>	'128',
			'user_agent'		=>	$dbs->qstr('AgileVoice')
		);
		$fsql = ""; $vsql = "";
		foreach($f as $fld => $val) {
			$fsql .= $fld.",";
			$vsql .= $val.",";
		}
		$fsql = substr($fsql,0,strlen($fsql)-1);
		$vsql = substr($vsql,0,strlen($vsql)-1);
		$sql = "INSERT INTO ".$this->tbl_alias." ($fsql) VALUES ($vsql)";
		#echo $sql."<br>";
		if(!$dbs->Execute($sql)) echo $dbs->ErrorMsg();		
	}
	
	function deleteDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
		$this->load_config();
		
		$dbs =& $this->DBFactory();
		$sql = "DELETE FROM ".$this->tbl_subscriber." WHERE username=".$dbs->qstr($didClass->getDID())." AND
			domain=".$dbs->qstr($this->voip_domain);
		#echo $sql."<br>";
		$dbs->Execute($sql);
	}
	
	function deleteVirtualDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
		$this->load_config();
		
		$dbs =& $this->DBFactory();
		$sql = "DELETE FROM ".$this->tbl_alias." WHERE username=".$dbs->qstr($didClass->getDID())." AND
			domain=".$dbs->qstr($this->voip_domain);
		#echo $sql."<br>";
		$dbs->Execute($sql);
	}
}

class asterisk_voip_provisioning extends voip_provisioning
{
	function asterisk_voip_provisioning(&$prod_plugin)
	{
		parent::voip_provisioning($prod_plugin);
	}
	
	function addCustom(&$didClass)
	{
		;
	}
	
	function removeCustom(&$didClass)
	{
		;
	}
	
	function addDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must of voipDID');

		$this->load_config();
		$db =& DB();
		
		$account = $db->Execute(sqlSelect($db,"account","*","id=::".$didClass->getAccountID()."::"));
		$pw = $this->generate_secret($didClass);
		$fullname = trim($account->fields['first_name']." ".$account->fields['last_name']);
		
		$channel = 'SIP';
		$channel_table = 'voip_sip';
		$channel_key = 'sip';
		if(@$this->prod_plugin->plugin_data['provision_channel'] == 1) {
			$channel = 'IAX2';
			$channel_table = 'voip_iax';
			$channel_key = 'iax';
		}
		
		$sql = "delete from ".AGILE_DB_PREFIX.$channel_table." WHERE 
			$channel_key=".$db->qstr($didClass->getDID())." and site_id=".$db->qstr(DEFAULT_SITE);
		#echo $sql."<br>";
		$db->Execute($sql);
					
		$f = array(
			'account'				=> $didClass->getDID(),
			'canreinvite'		=> 'no',
			'insecure'			=> 'very',
			'dtmfmode'			=> 'rfc2833',
			'host'					=> 'dynamic',
			'nat'						=> 'yes',
			'qualify'				=> '5000',
			'secret'				=> $pw,
			'type'					=> 'friend',
			'username'			=> $didClass->getDID(),
			'context'				=> 'default',
			'callerid'			=> sprintf("\"%s\" <%s>", $fullname, $didClass->getDID())
		);
		if(strlen($this->prod_plugin->plugin_data['context']))
			$f['context'] = $this->prod_plugin->plugin_data['context'];
		if($this->prod_plugin->plugin_data['voicemail_enabled'])
			$f['mailbox'] = $didClass->getDID();
		if($didClass->getCallWaiting() == 0)
			$f['incominglimit'] = '1';
			
		foreach($f as $fld => $val) {
			$flds = array(
				$channel_key	=> $didClass->getDID(),
				'keyword'			=> $fld,
				'data'				=> $val,
				'flags'				=> ''	
			);
			$sql = sqlInsert($db,$channel_table,$flds);
			#echo $sql."<br />";
			$db->Execute($sql);
		}
	}
	
	function addVirtualDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die("Parameter is not a voipDID class.");
		
		$this->load_config();
		$db =& DB();
		
		# A virtual DID is just a regular DID that has a channelarg set to the parent DIDs number
		$parent_did = $this->prod_plugin->get_parent_did($this->prod_plugin->prod_attr_cart['parent_service_id']);
		$didClass->setChannelArg($parent_did);
	}
	
	function deleteDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
	}
	
	function deleteVirtualDID(&$didClass)
	{
		if(!is_a($didClass,'voipDID')) die('parameter must be of voipDID');
	}
}

class plgn_prov_VOIP extends base_voip_plugin
{
  function plgn_prov_VOIP()
  { 
    $this->name             = 'VOIP';
    $this->task_based       = false;
    $this->remote_based     = true;
  }
    
	function delete_cart($VAR, $cart)
	{
		parent::delete_cart($VAR, $cart, false);
	}
    
  function validate_cart($VAR,$product) 
  { 
		// verify that attr['station'] is defined and numeric
		@$did = $VAR['attr']['station'];
		$ported = 0;
		if (@$VAR['attr']['ported']) {
			$did = $VAR['attr']['ported'];
			$ported = 1;
		}
		
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db,"product","prod_plugin_data","id=::".$VAR['product_id']));
		$prod_plugin_data = unserialize($rs->fields['prod_plugin_data']);
		if(!empty($VAR['attr']['parent_service_id']) || $prod_plugin_data['rate_accountcode'] == 1)
			return true;
		if((empty($did) || !is_numeric($did))) 
			return "Sorry, the DID format specified is incorrect.";

		return parent::validate_cart($VAR, $product, $did, $ported);
	}	

	function getDID(&$ported)
	{
		$did = str_replace("-","",@$this->prod_attr_cart['station']);
		if (strlen(@$this->prod_attr_cart['ported'])) {
			$did = $this->prod_attr_cart['ported'];
			$ported = true;
		}
		return $did;	
	}
	
  # add new service
  function p_new()
  {
  	#echo 'p_new<br />';
		$db = &DB();
		if (empty($this->prod_attr_cart['parent_service_id'])) {
			$this->prod_attr_cart['parent_service_id'] = 0;
		}
		#echo 'Retrieving did.<br />';
		$ported = false;
		$did = $this->getDID($ported);
		#echo 'DID is: '.$did.'<br />';
				
		switch(@$this->plugin_data['voip_platform']) {
			case 'ser':
				$vp = new ser_voip_provisioning($this);
				#echo 'Created ser<br />';
				break;
			case 'asterisk':
			default:
				$vp = new asterisk_voip_provisioning($this);
				#echo 'Created asterisk<br />';
		}

		if($this->plugin_data['rate_accountcode'])
			return true;

		# figure out which object to load
		#echo 'Creating voipDID.<br />';
		$didClass = new voipDID;
		if($this->prod_attr_cart['parent_service_id'] && !$this->plugin_data['virtual_number']) {
			$didClass->load($this->get_parent_did($this->prod_attr_cart['parent_service_id']));
    	} else {
			# new did needs create
			$didClass->setDID($did);
			$didClass->setAccountID($this->account['id']);
			$didClass->setServiceID($this->service['id']);
			$didClass->setServiceParentID($this->prod_attr_cart['parent_service_id']);
			$didClass->setActive(1);
			$didClass->insert();
			#echo 'Loading didClass.<br />';
			$didClass->load($did);
			
    		# go ahead and call the did plugin's purchase method,
    		# if the number isn't ported
    		if($ported==false && $vp->call_did_plugin($didClass,'purchase') == false) {
    			# purchase method failed. Post an error message and bomb out
    			$didClass->delete();
    			return false;
    		}
		
			if($this->plugin_data['virtual_number']) {	
				# provision a virtual number
				$vp->addVirtualDID($didClass);	
			} else {
				# provision a regular number
				$vp->addDID($didClass);
			}
		}
		#echo 'Set channel and features.<br />';
		
		# set the channel type
		$didClass->setChannel('SIP');
		if(@$this->plugin_data['provision_channel'] == 1)
			$didClass->setChannel('IAX2');
			
		# set all of the features entailed with this account
		if(@$this->plugin_data['cnam_enabled'])
			$didClass->setCNAM(1);
		if(@$this->plugin_data['blacklist_enabled'])
			$didClass->setBlacklist(1);
		if(@$this->plugin_data['anirouting_enabled'])
			$didClass->setANIRouting(1);
		if(@$this->plugin_data['can_failover'])
			$didClass->setFailover(1);
		if(@$this->plugin_data['remote_call_forwarding'])
			$didClass->setRemoteCallForwarding(1);
		if(@$this->plugin_data['fax_account'])
			$didClass->setFax(1,$this->account['email']);
		if(@$this->plugin_data['meetme_account'])
			$didClass->setConference(1, $this->plugin_data['meetme_min_limit']);
		if(@$this->plugin_data['faxdetection_enabled'])
			$didClass->setFaxDetection(1,$this->account['email']);
		if(@$this->plugin_data['callwaiting_enabled'])
			$didClass->setCallWaiting(1);
				
		# Callforward and Busycallforward are missing!
				
		if(@$this->plugin_data['voicemail_enabled']) {
			# provision voicemail to this account
			$didClass->setVoicemail(1);
			$vp->add_voicemail($didClass);
		}

		# Call custom handler
		# SER -> add entries to 'group' table
		$vp->addCustom($didClass);
		
		$didClass->save();
		#echo 'Called save voipDID.<br />';
		
		if($this->plugin_data['innetwork_enabled']) {
			# set this number to innetwork
			$vp->add_in_network($didClass);
		}
		
		# send the user the details
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$email = new email_template;
		$email->send('voip_new_service', $this->account['id'], $this->service_id, $did, $did);			
		
		# send the admin the provisioning details
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$email = new email_template;
		$email->send('admin->voip_manual', $this->account['id'], $this->service_id, $did, 'Provision VoIP Adaptor');	

		return true;		
	}

	function p_inactive()
	{
		$db = &DB();
		if (empty($this->prod_attr_cart['parent_service_id'])) {
			$this->prod_attr_cart['parent_service_id'] = 0;
		}
		$ported = false;
		$did = $this->getDID($ported);
		
		switch(@$this->plugin_data['voip_platform']) {
			case 'ser':
				$vp = new ser_voip_provisioning($this);
				break;
			case 'asterisk':
			default:
				$vp = new asterisk_voip_provisioning($this);
		}

		# figure out which didVOIP object to load
		$didClass = new voipDID;
		$didClass->load($this->get_parent_did($this->prod_attr_cart['parent_service_id']));
		$didClass->setActive(0);
		$didClass->save();
		return true;
	}

	function p_active()
	{
		$db = &DB();
		if (empty($this->prod_attr_cart['parent_service_id'])) {
			$this->prod_attr_cart['parent_service_id'] = 0;
		}
		$ported = false;
		$did = $this->getDID($ported);
		
		switch(@$this->plugin_data['voip_platform']) {
			case 'ser':
				$vp = new ser_voip_provisioning($this);
				break;
			case 'asterisk':
			default:
				$vp = new asterisk_voip_provisioning($this);
		}

		# figure out which didVOIP object to load
		$didClass = new voipDID;
		$didClass->load($this->get_parent_did($this->prod_attr_cart['parent_service_id']));
		$didClass->setActive(1);
		$didClass->save();
		return true;
	}

	function p_delete()
	{
		$db = &DB();
		if (empty($this->prod_attr_cart['parent_service_id'])) {
			$this->prod_attr_cart['parent_service_id'] = 0;
		}
		$debug = "";
		$ported = false;
		$did = $this->getDID($ported);
		
		switch(@$this->plugin_data['voip_platform']) {
			case 'ser':
				$vp = new ser_voip_provisioning($this);
				break;
			case 'asterisk':
			default:
				$vp = new asterisk_voip_provisioning($this);
		}

		# figure out which object to load
		$didClass = new voipDID;
		if($this->prod_attr_cart['parent_service_id'] && $this->plugin_data['virtual_number'] != 1) {
			$didClass->load($this->get_parent_did($this->prod_attr_cart['parent_service_id']));
		} else {
			$didClass->load($did);
		}

		# set all of the features entailed with this account
		if(@$this->plugin_data['cnam_enabled'])
			$didClass->setCNAM(0);
		if(@$this->plugin_data['blacklist_enabled'])
			$didClass->setBlacklist(0);
		if(@$this->plugin_data['anirouting_enabled'])
			$didClass->setANIRouting(0);
		if(@$this->plugin_data['can_failover'])
			$didClass->setFailover(0);
		if(@$this->plugin_data['remote_call_forwarding'])
			$didClass->setRemoteCallForwarding(0);
		if(@$this->plugin_data['fax_account'])
			$didClass->setFax(0,$this->account['email']);
		if(@$this->plugin_data['meetme_account'])
			$didClass->setConference(0, $this->plugin_data['meetme_min_limit']);
		if(@$this->plugin_data['faxdetection_enabled'])
			$didClass->setFaxDetection(0, $this->account['email']);
			
		# Callforward and Busycallforward are missing!
				
		if(@$this->plugin_data['voicemail_enabled']) {
			# provision voicemail to this account
			$vp->delete_voicemail($didClass);
		}
				
		if(@$this->plugin_data['provision_enabled']) {
			$didClass->setActive(0);
			if($this->plugin_data['virtual_number']) {	
				# have ser provision a virtual number
				$vp->deleteVirtualDID($didClass);	
			} else {
				# have ser provision a regular number
				$vp->deleteDID($didClass);
			}
			if($this->plugin_data['innetwork_enabled']) {
				$vp->delete_in_network($didClass);
			}
			# go ahead and call the did plugin's release method,
			# if the number wasn't ported
			if($ported == false && $vp->call_did_plugin($didClass,'release') == false) {
				# Post an error message
				;
			}
			$didClass->delete();
		} else {
			$didClass->save();
		}
		return true;
	}

	function p_one($id)
	{
		$db =& DB();
	
		# Get the asterisk global configuration
		$sql = sqlSelect($db, "voip", "voip_vm_passwd, voip_secret_gen", "");
		$rs = $db->Execute($sql);
		$this->voip_vm_passwd = $rs->fields['voip_vm_passwd'];
		$this->voip_secret_gen = $rs->fields['voip_secret_gen'];
		
		# pass onto the parent class
		parent::p_one($id);
	}

	# send a debugging email
	function sendDebug($defined_vars, $d = 'Nothing')
	{
		$ignorelist=array("HTTP_POST_VARS","HTTP_GET_VARS",
			"HTTP_COOKIE_VARS","HTTP_SERVER_VARS","_SERVER",
			"HTTP_ENV_VARS","HTTP_SESSION_VARS",
			"_ENV","PHPSESSID","SESS_DBUSER",
			"SESS_DBPASS","HTTP_COOKIE");

		$timestamp=date("m/d/Y h:j:s");
		$message="Debug report created $timestamp\n";
		$message .= "\n\nLocally generated messages:\n\n";
		$message .= $d;
		if (defined('PROVISIONING_DEBUG')) {
			mail('me@me.com','VOIP Plugin Debug for '.URL,$message);
		}
	}
}

?>
