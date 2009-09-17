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
	
class voip_pool
{
	# Open the constructor for this mod
	function voip_pool()
	{
		# name of this module:
		$this->module = "voip_pool";

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

	function task_area()
	{
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$db =& DB();
		$didArea = new didArea;
		$sql = sqlSelect($db,"voip_pool","*","areacode is null or areacode=0");
		$rs = $db->Execute($sql);
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				$n = $rs->fields['npa'].$rs->fields['nxx'].$rs->fields['station'];
				if( ($area = $didArea->determineArea($rs->fields['country_code'], $n)) !== false) {
					#echo "DID=".$n." has an area of $area = ".$didArea->getName($rs->fields['country_code'],$area)."<br>";
					$f = array('areacode' => $db->qstr($area));
					$sql = sqlUpdate($db,"voip_pool",$f,"id=".$rs->fields['id']);
					#echo "plugin_id=".$rs->fields['voip_did_plugin_id']."<br>";
					#echo $sql."<br>";
					$db->Execute($sql);
				}
				$rs->MoveNext();
			}
		}
	}

	/** Imports an uploaded text file into the voip_pool table. Each line must contain a single valid phone number.

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

	 */
	function import($VAR)
	{
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;

		$db = & DB();
		if (is_uploaded_file($_FILES['datafile']['tmp_name'])) {
			# Got a file to import
			$fp = fopen($_FILES['datafile']['tmp_name'],"r");
			if ($fp) {
				$counter = 0; $skipped = 0;
				while (!feof($fp)) {
					$line = fgets($fp,128);
					$line = ereg_replace("[^0-9]", "", $line);
					$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
					if ($v->e164($line, $e164, $cc, $npa, $nxx)) {
						$fields['voip_did_plugin_id'] = $VAR['voip_did_plugin_id']; 			/* DEFAULT plugin */
						$fields['country_code'] = $cc;
						$fields['npa'] = $npa;
						$fields['nxx'] = $nxx;
						if ($cc == '1') {
							$fields['station'] = substr($e164, 8);
						} elseif($cc == "61") {
							$fields['station'] = substr($e164, 12);
						} else {
							$fields['station'] = substr($e164, 4 + strlen($cc));
						}
						$rs = $db->Execute( sqlSelect($db, "voip_pool", "id", "voip_did_plugin_id=::".$VAR['voip_did_plugin_id'].":: and country_code=::".$fields['country_code'].":: and npa=::".$fields['npa'].":: and nxx=::".$fields['nxx'].":: and station=::".$fields['station']."::") );
						if ($rs && !$rs->EOF) {
							$skipped++;
						} else {
							$db->Execute( sqlInsert($db, "voip_pool", $fields) );
							$counter++;
						}
					} else {
						$skipped++;
					}
				}
				global $C_debug;
				$C_debug->error('voip_pool.inc.php','import',"Imported $counter new DIDs and skipped $skipped DIDs!");
				$C_debug->alert("Imported $counter new DIDs and skipped $skipped DIDs!");
			} else {
				# log error message
				global $C_debug;
				$C_debug->error('voip_pool.inc.php','import','Unable to process file: '.$_FILES['datafile']['tmp_name']);
				$C_debug->alert('Unable to fopen the file sent.');
			}
		} else {
			# log error message
			global $C_debug;
			$C_debug->error('voip_pool.inc.php','import','Possible file upload attack');
			$C_debug->alert('Unable to process the uploaded file.');
		}
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

	##############################
	##	   SEARCH EXPORT        ##
	##############################    	
	function search_export($VAR)
	{
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