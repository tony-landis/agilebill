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
	
class voip_vm
{
	var $vm=false;	// path to VM dir

	# Open the constructor for this mod
	function voip_vm()
	{
		# name of this module:
		$this->module = "voip_vm";

		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

		# open the construct file for parsing	
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);

		$this->method   = $construct["construct"]["method"];
		$this->trigger  = $construct["construct"]["trigger"];
		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"]; 		
	} 

	/**
	* Sets the voicemail path(s)
	*/
	function get_vm_path() {
		if(!SESS_LOGGED) { 
			define("FORCE_REDIRECT", "?_page=account:account");
			return false; 
		} 
		require_once(PATH_MODULES.'voip/voip.inc.php');
		$voip = new voip; 
		$dids = $voip->get_voicemail_dids(SESS_ACCOUNT);  

		if(!$dids || !is_array($dids) || count($dids) < 1) return false; 
		$db=&DB(); 
		foreach($dids as $did)
		{
			$rs = & $db->Execute($sql=sqlSelect($db,"voip_vm","context,mailbox","mailbox = ::$did:: AND account_id=".SESS_ACCOUNT));   			
			if($rs && $rs->RecordCount() > 0) 
			{  
				$path = "/var/spool/asterisk/voicemail/{$rs->fields["context"]}/{$rs->fields["mailbox"]}/INBOX/"; 
				$this->vm[] = $path;
				if (!is_dir($path)) {
					global $C_debug;
					$C_debug->error('voip_vm','get_vm_path','The voicemail directory does not have the proper permissions assigned.');
				}
			}
		}   			
	}

	/**
	* List the voicemails for the current user
	*/        
	function vm_list($VAR) {
		if(!SESS_LOGGED) { 
			define("FORCE_REDIRECT", "?_page=account:account");
		} else { 
			$ret = false; 
			$this->get_vm_path(); 
			if(is_array($this->vm) && count($this->vm) > 0)
			{ 
				$ret=array();
				foreach($this->vm as $path)
				{
					$this->get_vm_by_path($path,$ret); 
				}
				global $smarty;
				$smarty->assign('voip_fax', $ret);
				$smarty->assign('results', count($ret)); 				
			}				 		        		
		}          		         	
	} 


	/**
	* Streams the VM to the user
	*/    
	function vm_listen($VAR) 
	{ 
		if(SESS_LOGGED && @$VAR['id']!= '' && is_numeric($VAR['id']) && !empty($VAR['did']) && is_numeric($VAR['did']) )  
		{ 
			// get path for selected did && make sure it belongs to current account
			$db=&DB();
			$rs = & $db->Execute($sql=sqlSelect($db,"voip_vm","context,mailbox","mailbox = ::{$VAR['did']}:: AND account_id=".SESS_ACCOUNT)); 
			if($rs && $rs->RecordCount() > 0) 
			$path = "/var/spool/asterisk/voicemail/{$rs->fields["context"]}/{$rs->fields["mailbox"]}/INBOX/";  
			else
			return false; 

			$f = $path.'msg'.$VAR['id'].'.wav';  
			if(is_file($f) && is_readable($f)) {
				ob_start();
				header("Content-Type: audio/wav");
				echo file_get_contents($f);
				ob_end_flush();
				exit;
			}	
			else {
				echo "ERR1: Unable to retrieve specified Message";	
			}
		} else {
			echo "ERR1: No voicemail message specified or no access";
		}
	}


	/**
	* User voicemail delete function
	*/
	function user_delete($VAR) 
	{  
		if(SESS_LOGGED && @$VAR['id']!= '' && is_numeric($VAR['id']) && !empty($VAR['did']) && is_numeric($VAR['did']) )  
		{ 
			// get path for selected did && make sure it belongs to current account
			$db=&DB();
			$rs = & $db->Execute($sql=sqlSelect($db,"voip_vm","context,mailbox","mailbox = ::{$VAR['did']}:: AND account_id=".SESS_ACCOUNT)); 
			if($rs && $rs->RecordCount() > 0) 
			$path = "/var/spool/asterisk/voicemail/{$rs->fields["context"]}/{$rs->fields["mailbox"]}/INBOX/";  
			else
			return false; 

			$file = 'msg'.$VAR['id'].'.wav';  

			$wld = str_replace(".wav",".*",basename($file));
			if(strlen($wld) && strstr($wld,".*")) {
				foreach (glob($path.$wld) as $filename) { 
					unlink($filename);
				}
			}    
		}        	
	}


	/**
	* return all voicemails for a specific path
	* @return Array
	* @param $ret The array of voicemails to add any voicemails to 
	*/
	function get_vm_by_path($path,&$ret)
	{  
		if ($dh = @opendir($path)) {
			while (($file = readdir($dh)) !== false) {
				if(ereg("^msg.*\.txt",$file)) {
					$msgs[] = $file;
				}
			}
			closedir($dh);
		}
		if(is_array($msgs) && count($msgs)) {
			if(!is_array($ret)) $cnt=0;
			else $cnt = count($ret)+1;           		
			foreach($msgs as $msg) {
				$c = file_get_contents($path.$msg);
				$lines = explode("\n",$c);
				foreach($lines as $line) {
					$parts = explode("=",$line);
					$ret[$cnt][$parts[0]]=$parts[1];
					if($parts[0] == "origtime") {
						$ret[$cnt]['date'] = date('M d, Y g:i:s a',$parts[1]);
					}
				}
				$ret[$cnt]['id'] = ereg_replace("[a-zA-Z\.]","",$msg);
				$ret[$cnt]['size'] = number_format(filesize($path.$ret[$cnt]['file'])/1024,0);
				$cnt++;
			}
		} else {
			return;
		}
		return $ret;
	}        

	function add($VAR)
	{
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	function view($VAR)
	{	
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}		

	function update($VAR)
	{
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR)
	{	
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		

	function search_form($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	} 

	function search($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	} 
}
?>