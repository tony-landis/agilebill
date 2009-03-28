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
	
/**
 * Radius Provisioning Class for AgileBill 
 */
class radius
{ 
	var $user_regex='^([a-zA-Z0-9\-\_\.]{4,20})$'; 
	var $pass_regex='^([a-zA-Z0-9\-\_\.]{4,20})$'; 
 	
	/**
	 * Get the user's password list:
	 */
	function do_list($VAR)
	{
		global $smarty, $C_debug;
		 
		# Validate logged in:
		if(!SESS_LOGGED) {
			$C_debug->alert("You must be logged in!");
			return;
		}
		 
		# Get all accounts defined for this user:
		$db=&DB(); 			
		$result = $db->Execute(sqlSelect($db,"radius","*","account_id=::".SESS_ACCOUNT."::","username DESC")); 
		if($result && $result->RecordCount()) {
			while(!$result->EOF) {
				
				if($result->fields['auth'] == 'login')
					$old_login[] = $result->fields;
				
				if($result->fields['auth'] == 'wireless')
					$old_wireless[] = $result->fields;
				
				$result->MoveNext();
			}
		}
		$smarty->assign("old_login", $old_login);
		$smarty->assign("old_wireless", $old_wireless);
	 		
		$rs = $db->Execute($sql=sqlSelect($db,"radius_service","*",
			"account_id=::".SESS_ACCOUNT.":: AND (radius_id IS NULL OR radius_id=0 OR radius_id=::::)")); 
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {				 
				if($rs->fields['auth']=='login') {
					$new_login[] = array('id'=>$rs->fields['id']);
				} elseif($rs->fields['auth']=='wireless') {
					$new_wireless[] = array('id'=>$rs->fields['id']);
				} 
				$rs->MoveNext();
			}
		} 
		 			
		$smarty->assign("new_login", @$new_login);
		$smarty->assign("new_wireless", @$new_wireless);		
		
	}
	
	/**
	 * Get total accounts available for this user
	 */
	function available_accounts(&$avail_login, &$avail_wireless) {
		$db=&DB();
		$rs = $db->Execute($sql=sqlSelect($db,"radius_service","*",
			"account_id=::".SESS_ACCOUNT.":: AND (radius_id IS NULL OR radius_id=0 OR radius_id=::::)")); 
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {				 
				if($rs->fields['auth']=='login') {
					$avail_login++;
				} elseif($rs->fields['auth']=='wireless') {
					$avail_wireless++;
				} 
				$rs->MoveNext();
			}
		}		
	}	
	
	/**
	 * Add a radius entry
	 */
	function add_radius($service_id, $radius_service_id, $username, $password=false) {
          
        // determine type of auth
        if(!$password)
        	$auth='wireless';
        else 
        	$auth='login';
  
        // get the associated service
        $db=&DB();
        $rs = $db->Execute(sqlSelect($db,"service","*", "id=::$service_id::"));
        if(!$rs || !$rs->RecordCount()) return false;
        
        $f['service_id']=$service_id;
        $f['username']=$username;
        $f['password']=$password;        
        $f['account_id']=$rs->fields['account_id'];
        $f['sku']=$rs->fields['sku'];
        $f['active']=1;
        
        // insert radius record
        $arr=unserialize($rs->fields['prod_plugin_data']);
        foreach($arr as $a=>$b) {
        	if($a != 'max') $f[$a]=$b; 
        }
        $id = sqlGenID($db,"radius");
        $db->Execute($sql=sqlInsert($db,"radius",$f,$id));
        
        // update radius_service table
        $db->Execute(sqlUpdate($db, "radius_service", array('radius_id'=>$id), "id = $radius_service_id"));
       
        return true;	
	}
	
	/**
	 * validate mac id
	 */
	function validate_wireless($user) { 
		if(ereg("^([0-9A-Z]{2}) ([0-9A-Z]{2}) ([0-9A-Z]{2}) ([0-9A-Z]{2}) ([0-9A-Z]{2}) ([0-9A-Z]{2})$",$user)) return $user;
		return false;
	}
	
	/**
	 * validate username and password 
	 */
	function validate_login($user,$pass) {
	 
		if(!ereg("$this->user_regex", $pass)) return false;
		if(!ereg("$this->pass_regex", $pass)) return false;
		return true;
	}
	
	/**
	 * Validate unique user/mac id
	 */
	function validate_unique($id,$username) {
		
		$s='';
		if($id) $s="id!=::$id:: AND ";
		$db=&DB();
		$result = $db->Execute($sql=sqlSelect($db,"radius","id","$s username=::$username::")); 
		  
		if($result === false || $result->RecordCount() == 0)
			return true;
		else
			return false;
	}
	 
	/**
	 * Update password list 
	 */
	function do_update($VAR)
	{
		global $smarty, $C_debug, $C_translate;
		$db=&DB(); 
		
		$msg  = false;
			  
		# Validate logged in:
		if(!SESS_LOGGED) {
			$C_debug->alert("You must be logged in!");
			return;
		}  
  
		# Loop through the submitted passwords for update: 
		if(!empty($VAR['username']) && is_array($VAR['username'])) {
			foreach($VAR['username'] as $id=>$val) {  
				$user = $VAR['username'][$id];
				@$pass = $VAR['password'][$id];
				
				
				$result = $db->Execute(sqlSelect($db,"radius","*", "id=::$id:: AND account_id=::".SESS_ACCOUNT."::"));
				if($result && $result->RecordCount())
				{			  
					if($result->fields['auth'] == 'login') { 
						 if(!$this->validate_login($user,$pass) || !$this->validate_unique($id, $user)) { 
							$C_translate->value["radius"]["user"]=$user;
							$C_translate->value["radius"]["pass"]=$pass;
							$msg .= $C_translate->translate("err_login", "radius")."<br>";
						} else {
							// update login record
							$db->Execute(sqlUpdate($db,"radius",array('password'=>$pass, 'username'=>$user), "id=$id"));
							#$used_login++;
						} 
					} elseif ($result->fields['auth'] == 'wireless') {
						// validate mac id
						$user=strtoupper($user);
						$user=str_replace("-", " ", $user);						
						if(!$this->validate_wireless($user) || !$this->validate_unique($id, $user)) {
							$C_translate->value["radius"]["user"]=$user;
							$msg .= $C_translate->translate("err_wireless", "radius")."<br>"; 
						} else {
							$db->Execute(sqlUpdate($db,"radius",array('username'=>$user), "id=$id")); 
						}
					}  
				} 	 
			} 
		}
		
		
		# Loop through the submitted passwords for additions:
		if(!empty($VAR['new_username']) && is_array($VAR['new_username'])) {
			foreach($VAR['new_username'] as $id=>$val) {
				if(!empty($VAR['new_username'][$id])) {
					$user = $VAR['new_username'][$id];
					@$pass = $VAR['new_password'][$id];
					 
					// validation
					$rsRS = $db->Execute(sqlSelect($db,"radius_service","*", "id=::$id:: AND account_id=::".SESS_ACCOUNT."::"));
			        $service_id = $rsRS->fields['service_id'];
			        $radius_service_id = $rsRS->fields['id'];					
					if($rsRS->fields['auth'] == 'login') { 
						if(!$this->validate_login($user,$pass) || !$this->validate_unique(false, $user)) {
							$C_translate->value["radius"]["user"]=$user;
							$C_translate->value["radius"]["pass"]=$pass;
							$msg .= $C_translate->translate("err_login", "radius")."<br>";
						} else {
							// add login record
							$this->add_radius($service_id, $radius_service_id, $user, $pass); 
						}
					} elseif ($rsRS->fields['auth'] == 'wireless' ) { 
						// validate mac id
						$user=strtoupper($user);
						$user=str_replace("-", " ", $user);								
						if(!$this->validate_wireless($user) || !$this->validate_unique(false, $user)) {
							$C_translate->value["radius"]["user"]=$user;
							$msg .= $C_translate->translate("err_wireless", "radius")."<br>"; 
						} else {
							// add mac id record
							$this->add_radius($service_id, $radius_service_id, $user);  
						}
					} 	 
				}
			}
		}
				
		if(!empty($msg)) $C_debug->alert($msg);
	}

	
	# Open the constructor for this mod
	function construct()
	{
		# name of this module:
		$this->module = "radius";

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



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{
		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$this->construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{
		$this->construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	##############################
	##		    SEARCH		    ##
	##############################
	function search($VAR)
	{
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	##############################
	##		SEARCH SHOW	        ##
	############################## 
	function search_show($VAR)
	{
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}

	##############################
	##	   SEARCH EXPORT        ##
	##############################
	function search_export($VAR)
	{
		$this->construct();
		# require the export class
		require_once (PATH_CORE   . "export.inc.php");

		# Call the correct export function for inline browser display, download, email, or web save.
		if($VAR["format"] == "excel")
		{
			$type = "export_excel";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_excel($VAR, $this, $type);
		}

		else if ($VAR["format"] == "pdf")
		{
			$type = "export_pdf";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_pdf($VAR, $this, $type);
		}

		else if ($VAR["format"] == "xml")
		{
			$type = "export_xml";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_xml($VAR, $this, $type);
		}

		else if ($VAR["format"] == "csv")
		{
			$type = "export_csv";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_csv($VAR, $this, $type);
		}

		else if ($VAR["format"] == "tab")
		{
			$type = "export_tab";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$export = new CORE_export;
			$export->search_tab($VAR, $this, $type);
		}
	} 
}
?>