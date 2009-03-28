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
	
class asset
{
	var $module = "asset";
	
	/* check availibility */
	function available($assetPoolId, $qty=1) {
		$db=&DB();
		$rs = $db->Execute($sql=sqlSelect($db,"asset","id","(status=0 or status is null) and pool_id=::$assetPoolId::")); 
		if($rs && $rs->RecordCount() >= $qty) 
			return true;
		else
			return false;
	}
	
	/* assign a asset to a specific service id from a specific asset pool */
	function assign($serviceId, $assetPoolId) {
		// check if any available
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"asset","id,asset","(status=0 or status is null) and pool_id=::$assetPoolId::","",1));
		if($rs && $rs->RecordCount()) {
			$id = $rs->fields['id'];
			$asset = $rs->fields['asset'];
			$fields=Array('service_id'=>$serviceId, 'status'=>1, 'date_last'=>time());
			$db->Execute($sql=sqlUpdate($db,"asset",$fields,"id = $id")); 
			return $asset;
		}
		return false;
	}
	
	/** assign a known asset */
	function assignKnown($serviceId, $assetId) {
		$fields=Array('service_id'=>$serviceId, 'status'=>1, 'date_last'=>time());
		$db=&DB();
		$sql=sqlUpdate($db,"asset",$fields,"id = ::$assetId::");  			
		$db->Execute($sql);
	}
	
	/* un-assign a specific asset */
	function unAssign($assetId) {
		$db=&DB(); 
		$db->Execute($sql="UPDATE ".AGILE_DB_PREFIX."asset SET status='0',date_last='".time()."',service_id='0' WHERE id='$assetId'"); 
		return true;
	}
	
	/* un-assign all assets for a specific service */
	function unAssignAll($serviceId) { 
		$db=&DB(); 	
		$db->Execute($sql="UPDATE ".AGILE_DB_PREFIX."asset SET status='0',date_last='".time()."',service_id='0' WHERE service_id='$serviceId'"); 
		return true;
	}
	
	
	/* import assets */
	function import($VAR) {
		global $C_debug;
		if(empty($VAR['asset_pool_id'])) {
			$C_debug->alert("No asset pool specified");
			return;
		}
		
		$db =& DB();
		if (is_uploaded_file($_FILES['datafile']['tmp_name'])) {
			# Got a file to import
			$fp = fopen($_FILES['datafile']['tmp_name'],"r");
			if ($fp) {
				$counter = 0; $skipped = 0;
				while (!feof($fp)) {
					$line = fgets($fp,128);
					$cols=explode(",", $line);
					if(!empty($cols[0])) {
	 					$fields = Array(
	 						'date_orig'=>time(),
	 						'date_last'=>time(),
	 						'status'=>0,
	 						'service_id'=>0,
	 						'pool_id'=> $VAR['asset_pool_id'],
	 						'asset'=>@$cols[0],
	 						'misc'=>@$cols[1]); 
						$db->Execute( sqlInsert($db, "asset", $fields) );
						$counter++; 
					} else {
						$skipped++;
					}
				}
				$C_debug->alert("Imported $counter new Asset(s) and skipped $skipped Asset(s)!");
			} else {
				$C_debug->alert('Unable to fopen the file sent.');
			}
		} else { 
			$C_debug->alert('Unable to process the uploaded file.');
		}
	}
		 
	function construct() {  
		$this->xml_construct = PATH_MODULES . $this->module . "/" . $this->module . "_construct.xml"; 
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
 
	function add($VAR) {
		$this->construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	} 
	
	function view($VAR) {
		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}
 
	function update($VAR) {
		$this->construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
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
}
?>