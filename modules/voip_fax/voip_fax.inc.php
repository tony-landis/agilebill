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
	
class voip_fax
{
	var $did;

	# Open the constructor for this mod
	function voip_fax()
	{
		# name of this module:
		$this->module = "voip_fax";

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

	function user_view($VAR) {
		if(SESS_LOGGED) {
			$this->did = @$VAR['id'];
			$db = &DB();
			$rs = & $db->Execute(sqlSelect($db,"voip_fax","account_id","id = ::$this->did::"));		        	
			if($rs->fields['account_id'] == SESS_ACCOUNT) {
				$this->view($VAR,$this);	
			}
		}
		echo "Not logged in or authenticated!";
	}

	function user_delete($VAR) {
		if(SESS_LOGGED) {
			$this->did = @$VAR['id'];
			$db = &DB();
			$rs = & $db->Execute(sqlSelect($db,"voip_fax","account_id","id = ::$this->did::"));		        	
			if($rs->fields['account_id'] == SESS_ACCOUNT) {
				$this->delete($VAR,$this);	
			}
		}         	
	}

	function user_search_show($VAR) {
		$this->search_show($VAR,$this);
	}

	function user_search($VAR) {
		if(SESS_LOGGED) {
			include_once(PATH_MODULES."voip/voip.inc.php");
			$db =& DB();
			$v = new voip;
			$fdids = $v->get_fax_dids(SESS_ACCOUNT);
			#echo "<pre>".print_r($fdids,true)."</pre>";
			if (is_array($fdids)) {
				foreach ($fdids as $did) {
					$flds['account_id'] = SESS_ACCOUNT;
					$flds['site_id'] = DEFAULT_SITE;
					$sql = sqlUpdate($db, "voip_fax", $flds, "dst = ::".$did."::");
					$db->Execute($sql);
					#echo $sql."<br>";
				}
			}
			unset($db);
			$VAR['voip_fax_account_id'] = SESS_ACCOUNT;	 
			$type = "search";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$db = new CORE_database;
			$db->search($VAR, $this, $type); 
		} else {
			define("FORCE_REDIRECT", "?_page=account:account");
		}
	}        	

	function view($VAR)  {
		global $_SERVER;			
		ob_clean();
		ob_start();			 			
		$this->did =  @$VAR['id'];
		$db = &DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_fax","mime_type","id = ::$this->did::"));					 
		if ($rs && $rs->RecordCount()==1) 
		{
			$this->mime_type = $rs->fields['mime_type']; 
			$fax = & $db->Execute(sqlSelect($db,"voip_fax_data","faxdata","fax_id = ::$this->did::")); 
			if (isset($_SERVER['HTTP_USER_AGENT']) && preg_match("/MSIE/", $_SERVER['HTTP_USER_AGENT'])) {
				ini_set('zlib.output_compression','Off');
			} 
			header("Content-Type: ". $this->mime_type ); 
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
			header("Pragma: public");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Content-Disposition: attachment; filename=\"fax.pdf\""); 
			while(!$fax->EOF) {
				echo $fax->fields['faxdata'];
				$fax->MoveNext();
			}  	
		} 
		ob_end_flush();
		exit();
	}

	function add($VAR) {
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}      

	function update($VAR) { 
		// delete assoc faxdata records
		$this->associated_DELETE[] = Array( 'table' => 'voip_fax_data', 'field' => 'fax_id');

		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR) {	
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		

	function search_form($VAR) {
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR) {	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR) {
		if(SESS_LOGGED) {
			include_once(PATH_MODULES."voip/voip.inc.php");
			$db =& DB();
			$v = new voip;
			$fdids = $v->get_fax_dids(SESS_ACCOUNT);
			#echo "<pre>".print_r($fdids,true)."</pre>";
			if (is_array($fdids)) {
				foreach ($fdids as $did) {
					$sql = "UPDATE ".AGILE_DB_PREFIX."voip_fax SET 
						account_id		= ".$db->qstr(SESS_ACCOUNT).", 
						site_id			= ".$db->qstr(DEFAULT_SITE)." 
						WHERE dst = ".$db->qstr($did);
					$db->Execute($sql);
					#echo "did=$did ".$sql."<br>";
				}
			}        		
			unset($db);
		}
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	} 
}
?>