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
	
class voip_did
{

	# Open the constructor for this mod
	function voip_did()
	{
		# name of this module:
		$this->module = "voip_did";

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

	###########################################
	### AJAX Auto-selector
	###########################################

	function autoselect($VAR)
	{                 	
		$db = &DB();
		$p = AGILE_DB_PREFIX;

		if (empty($VAR['did_search'])) {
			$where = "id > 0";
			$type = 1;
		} else {
			$where = "did LIKE   ".$db->qstr($VAR['did_search'].'%');
			$type = 4;
		}

		$q = "SELECT id,did FROM {$p}voip_did WHERE 
				( $where )
			  AND  
				site_id = " .DEFAULT_SITE."
			  ORDER BY did";   
		$result = $db->SelectLimit($q, 10);          

		# Create the alert for no records found
		echo '<ul>';            
		# Create the alert for no records found
		if ($result->RecordCount() > 0)  { 
			$i=0;  
			while(!$result->EOF) 
			{ 
				echo '<li><div class="name"><b>' . $result->fields['did'] . '</b></div>'.
					'<div class="index" style="display:none">'.$result->fields['id']. '</div></li>'."\r\n"; 
				$result->MoveNext();
				$i++;
			} 
		}  
		echo "</ul>";
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
		global $smarty;
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$smart = $db->view($VAR, $this, $type);

		# echo "<pre>".print_r($smart,true)."</pre>";
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db, "voip_sip", "*", "sip=::".$smart[0]['did']."::"));
		while (!$rs->EOF) {
			$smarty->assign('sip_'.$rs->fields['keyword'],$rs->fields['data']);
			$rs->MoveNext();
		}
		$sip_canreinvite_options['yes'] = 'Yes';
		$sip_canreinvite_options['no'] = 'No';
		$sip_canreinvite_options['update'] = 'Update';
		$smarty->assign('sip_canreinvite_options', $sip_canreinvite_options);
		$sip_dtmfmode_options['info'] = 'Info';
		$sip_dtmfmode_options['rfc2833'] = 'RFC 2833';
		$sip_dtmfmode_options['inband'] = 'In-band Audio';
		$smarty->assign('sip_dtmfmode_options', $sip_dtmfmode_options);
		$sip_nat_options['yes'] = 'Yes';
		$sip_nat_options['no'] = 'No';
		$sip_nat_options['always'] = 'Always';
		$smarty->assign('sip_nat_options', $sip_nat_options);
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

		# update the voip_sip table fields
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db, "voip_did", "did", "id=::".$VAR['id']."::"));

		#echo "<pre>".print_r($VAR,true)."</pre>";
		$f[0]['username'] = $VAR['sip_username'];
		$f[1]['secret'] = $VAR['sip_secret'];
		$f[2]['qualify'] = $VAR['sip_qualify'];
		$f[3]['mailbox'] = $VAR['sip_mailbox'];
		$f[4]['incominglimit'] = $VAR['sip_incominglimit'];
		$f[5]['dtmfmode'] = $VAR['sip_dtmfmode'];
		$f[6]['canreinvite'] = $VAR['sip_canreinvite'];
		$f[7]['callerid'] = $VAR['sip_callerid'];
		$f[8]['nat'] = $VAR['sip_nat'];
		for ($i = 0; $i < 9; $i++) {
			#echo "<pre>".print_r($f[$i],true)."</pre>";
			$k = key($f[$i]);
			$v = $f[$i][$k]; 
			if (empty($v)) {
				$sql = "DELETE FROM ".AGILE_DB_PREFIX."voip_sip WHERE sip=".$db->qstr($rs->fields['did'])." and keyword=".$db->qstr($k)." and site_id=".DEFAULT_SITE;
			} else {
				$rs2 = $db->Execute(sqlSelect($db, "voip_sip", "id", "sip=::".$rs->fields['did'].":: AND keyword=::".$k."::"));
				if ($rs2 && $rs2->fields[0] > 0) {
					$sql = "UPDATE ".AGILE_DB_PREFIX."voip_sip SET data=".$db->qstr($v)." WHERE sip=".$db->qstr($rs->fields['did'])." and keyword=".$db->qstr($k)." and site_id=".DEFAULT_SITE;
				} else {
					$flds['data'] = $v;
					$flds['keyword'] = $k;
					$flds['sip'] = $rs->fields['did'];
					$sql = sqlInsert($db, "voip_sip", $flds);
					# $sql = "INSERT INTO ".AGILE_DB_PREFIX."voip_sip SET data=".$db->qstr($v)." WHERE sip=".$db->qstr($rs->fields['did'])." and keyword=".$db->qstr($k)." and site_id=".DEFAULT_SITE;
				}
			}
			if (!$db->Execute($sql)) {
				echo $db->ErrorMsg();
			}    			
		}
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