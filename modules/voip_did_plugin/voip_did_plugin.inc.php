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
	
class voip_did_plugin
{

	# Open the constructor for this mod
	function voip_did_plugin()
	{
		# name of this module:
		$this->module = "voip_did_plugin";

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

	function task($VAR) 
	{ 
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"voip_did_plugin","*",""));
		if($rs && $rs->RecordCount() > 0)
		{
			while(!$rs->EOF) 
			{        			 
				// load the plugin and call refresh(); 
				$plugin = $rs->fields['plugin'];
				$file = PATH_PLUGINS.'voip_did/'.$plugin.'.php';
				if(is_file($file)) {
					include_once($file);
					eval('$plg = new plgn_voip_did_'.$plugin.';'); 
					if(is_object($plg)) {
						if(is_callable(array($plg, 'release'))) {
							$plg->id = $rs->fields['id'];; 
							$plg->refresh();  
						}
					} 
				} 	 
				$rs->MoveNext();	
			}        		
		}    	
	}



	/** Reserve a DID

	mysql> describe ab_voip_pool;
	+--------------------+--------------+------+-----+---------+-------+
	| Field              | Type         | Null | Key | Default | Extra |
	+--------------------+--------------+------+-----+---------+-------+
	| id                 | mediumint(9) |      | PRI | 0       |       |
	| site_id            | mediumint(9) | YES  | MUL | NULL    |       |
	| account_id         | mediumint(9) | YES  |     | NULL    |       |
	| npa                | varchar(16)  | YES  | MUL | NULL    |       |
	| nxx                | varchar(16)  | YES  |     | NULL    |       |
	| station            | varchar(32)  | YES  |     | NULL    |       |
	| country_code       | mediumint(9) | YES  |     | NULL    |       |
	| date_reserved      | bigint(20)   | YES  |     | NULL    |       |
	| voip_did_plugin_id | mediumint(9) | YES  |     | NULL    |       |
	+--------------------+--------------+------+-----+---------+-------+
	9 rows in set (0.00 sec) 
	*/
	function reserve($voip_did_plugin_id, $did) {
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;

		$db =& DB();

		$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
		if ($v->e164($did, $e164, $cc, $npa, $nxx)) {
			if ($cc == '1') {
				$station = substr($e164, 8);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=".time().", account_id=".intval(SESS_ACCOUNT)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND (account_id IS NULL or account_id=0) AND
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} elseif($cc == '61') {
				$station = substr($e164, 12);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=".time().", account_id=".intval(SESS_ACCOUNT)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND (account_id IS NULL or account_id=0) AND
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} else {
				$station = substr($e164, 4 + strlen($cc));
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=".time().", account_id=".intval(SESS_ACCOUNT)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND (account_id IS NULL or account_id=0) AND
					country_code=".$db->qstr($cc)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			}
			$db->Execute($sql);
			syslog(LOG_INFO,$sql);
			if ($db->Affected_Rows())
				return true;
		}
		return "Could not complete request, the number has already been reserved by another user.<BR>
				Please go back and refresh the order page and make a different selection.";
	}

	/** Purchase a DID
	*/ 
	function purchase($voip_did_plugin_id, $did) {
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;

		$db =& DB();

		$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
		if ($v->e164($did, $e164, $cc, $npa, $nxx)) {
			if ($cc == '1') {
				$station = substr($e164, 8);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=".intval($this->account_id)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} elseif($cc == '61') {
				$station = substr($e164, 12);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=".intval($this->account_id)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} else {
				$station = substr($e164, 4 + strlen($cc));
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=".intval($this->account_id)."
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND
					country_code=".$db->qstr($cc)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			}
			syslog(LOG_INFO,$sql);
			$db->Execute($sql);
			if ($db->Affected_Rows())
				return true;
		}
		return "Could not complete request, the number has already been reserved by another user.<BR>
				Please go back and refresh the order page and make a different selection.";
	}

	/** Release the DID back to the pool of available numbers
	*/
	function release($voip_did_plugin_id, $did) {
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;

		$db =& DB();

		$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
		if ($v->e164($did, $e164, $cc, $npa, $nxx)) {
			if ($cc == '1') {
				$station = substr($e164, 8);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=NULL
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND 
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} elseif($cc == '61'){
				$station = substr($e164, 12);
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=NULL
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND
					country_code=".$db->qstr($cc)." AND npa=".$db->qstr($npa)." AND nxx=".$db->qstr($nxx)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			} else {
				$station = substr($e164, 4 + strlen($cc));
				$sql = "UPDATE ".AGILE_DB_PREFIX."voip_pool SET
					date_reserved=NULL, account_id=NULL
					WHERE voip_did_plugin_id=".$voip_did_plugin_id." AND
					country_code=".$db->qstr($cc)." AND station=".$db->qstr($station)." AND site_id=".DEFAULT_SITE;
			}
			$db->Execute($sql);
			#echo $sql;
			if ($db->Affected_Rows())
				return true;
		}
		return "Could not complete request, the number has already been reserved by another user.<BR>
				Please go back and refresh the order page and make a different selection.";
	}

	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
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
		$db = new CORE_database;
		 $db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
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
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_show($VAR, $this, $type);
	}
}
?>