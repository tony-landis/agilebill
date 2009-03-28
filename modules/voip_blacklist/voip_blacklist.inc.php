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
	
class voip_blacklist
{

	# Open the constructor for this mod
	function voip_blacklist()
	{
		# name of this module:
		$this->module = "voip_blacklist";

		if(!defined('AJAX')) {
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
	}

	function user_search($VAR) {
		if(SESS_LOGGED) {
			$VAR['voip_blacklist_account_id'] = SESS_ACCOUNT;	 
			$type = "search";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$db = new CORE_database;
			$db->search($VAR, $this, $type); 
		} else {
			define("FORCE_REDIRECT", "?_page=account:account");
		}        	
	}

	function user_search_show($VAR) {
		$this->search_show($VAR,$this);
	}

	function user_view($VAR) {
		if(SESS_LOGGED) {
			$this->did = @$VAR['id'];
			$db = &DB();
			$rs = & $db->Execute(sqlSelect($db,"voip_blacklist","account_id","id = ::$this->did::"));		        	
			if($rs->fields['account_id'] == SESS_ACCOUNT) {
				$this->view($VAR,$this);	
			}
			return;
		}  
		echo "Not logged in or authenticated!";       
	}

	function user_delete($VAR) {
		if(SESS_LOGGED) {
			$this->did = @$VAR['delete_id'];
			$db = &DB();
			$rs = & $db->Execute(sqlSelect($db,"voip_blacklist","account_id","id = ::$this->did::"));
			#echo $sql;exit;		        	
			if($rs->fields['account_id'] == SESS_ACCOUNT) {
				$this->delete($VAR,$this);	
			}
			return;
		}  
		echo "Not logged in or authenticated!";        	
	}

	function user_update($VAR) {
		if(SESS_LOGGED) {
			$this->did = @$VAR['id'];
			$db = &DB();
			$rs = & $db->Execute(sqlSelect($db,"voip_blacklist","account_id","id = ::$this->did::"));		        	
			if($rs->fields['account_id'] == SESS_ACCOUNT) {
				$this->update($VAR,$this);	
			}
			return;
		}  
		echo "Not logged in or authenticated!";        	
	}


	function user_add($VAR) { 
		# verify logged in:
		if(!SESS_LOGGED) {
			define("FORCE_REDIRECT", "?_page=account:account");
			return false;
		}

		# Verify did_id is owned by user
		$did=@$VAR['voip_blacklist_voip_did_id'];
		$db=&DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_did","account_id","id = ::$did::"));
		if($rs && $rs->RecordCount() > 0 && $rs->fields['account_id'] == SESS_ACCOUNT) 
		{
			// insert the record 
			// todo: validate the src no
			$VAR['voip_blacklist_account_id'] = SESS_ACCOUNT;
			$this->add($VAR,$this);
		}
		return true; 
	} 

	function ajax_add($VAR)
	{
		$db =& DB();
		$rs = $db->Execute($sql=sqlSelect($db,"voip_cdr","src, dst","id=::".$VAR['voip_cdr_id'].":: and account_id=::".SESS_ACCOUNT."::"));
		if ($rs && $rs->RecordCount()) {
			if(strlen($rs->fields['src'])) {
				$did = $rs->fields['dst'];
				$rs1 = $db->Execute(sqlSelect($db,"voip_did","id, blacklist","account_id=::".SESS_ACCOUNT.":: and (did=::$did:: or did=::1$did::)"));
				if ($rs1 && $rs1->RecordCount()) {
					if ($rs1->fields['blacklist']) {
						$rs2 = $db->Execute(sqlSelect($db,"voip_blacklist","id","account_id=::".SESS_ACCOUNT.":: and src=::".$rs->fields['src']."::"));
						if ($rs2 && $rs2->RecordCount()) {
							echo "alert('Sorry, this number is already in your blacklist.');\n";
						} else {
							$f['account_id'] = SESS_ACCOUNT;
							$f['voip_did_id'] = $rs1->fields['id'];
							$f['src'] = $rs->fields['src'];
							$f['dst'] = 'Playback tt-monkeys';
							$db->Execute(sqlInsert($db,"voip_blacklist",$f));
							echo "alert('Added entry to your blacklist.');\n";
						}
					} else {
						echo "alert('Your account does not have the blacklist feature.');\n";
					}
				} else {
					echo "alert('Sorry, can not find the DID associated with this CDR.');\n";
				}
			}
		} else {
			echo "alert('Sorry, the CDR does not belong to your account.');\n";
		}
		return true;
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