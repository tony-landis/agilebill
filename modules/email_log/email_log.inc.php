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
	
class email_log
{
	var $user_view_count = 25; /* show last X email logs for user */
	
	function user_list($VAR) {
		if(!SESS_LOGGED) return false;
		$db=&DB();
		$email = $db->GetOne("select email from ".AGILE_DB_PREFIX."account where id = ".SESS_ACCOUNT);
		$rs=$db->Execute(sqlSelect($db,"email_log","id,email,date_orig,subject,urgent,userread",
		"email=::$email:: and account_id=".SESS_ACCOUNT,'date_orig',$this->user_view_count));
		if($rs && $rs->RecordCount()) {
			$smart=array();
			while(!$rs->EOF) {
				array_push($smart, $rs->fields);
				$rs->MoveNext();		
			}
			global $smarty;
			$smarty->assign('email_log', $smart);
		}		
	}
	
	function user_view($VAR) {
		/* validate, update to read, and view() */
		if(!SESS_LOGGED || empty($VAR['id'])) return false;
		/* select id for this user */
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"email_log","*","id=::{$VAR['id']}:: and account_id=".SESS_ACCOUNT));
		if($rs && $rs->RecordCount()) {
			global $smarty;
			$smarty->assign('email_log', $rs->fields);
			if($rs->fields['userread'] != 1) {
				/* update to read */
				$fields=Array('userread'=>1);
				$db->Execute(sqlUpdate($db,"email_log",$fields,"id = {$rs->fields['id']}"));
			}
		}
	}
 
	function add($account_id, $subject, $message, $email, $html=0, $urgent=0) {
		$db=&DB();
		$fields=Array('date_orig'=>time(), 'account_id'=>$account_id, 'subject'=>$subject, 'message'=>$message, 'email'=>$email, 'html'=>$html, 'urgent'=>$urgent, 'userread'=>0);
		$id = & $db->Execute(sqlInsert($db,"email_log",$fields));
	}
 
	function view($VAR) {
		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function delete($VAR) {
		$this->construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}
 
	function search_form($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	} 
	
	function search($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}
 
	function search_show($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}
	 
	function construct() { 
		$this->module = "email_log"; 
		$this->xml_construct = PATH_MODULES . $this->module . "/" . $this->module . "_construct.xml"; 
		$C_xml = new CORE_xml;
		$construct 		= $C_xml->xml_to_array($this->xml_construct);
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
?>