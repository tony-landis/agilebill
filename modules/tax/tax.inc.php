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
	
class tax
{ 
	/**
    * Calculate all applicable taxes for a given product
    * @return Array
    */
	function calculate($taxable_amount, $country_id, $zone)
	{
		$arr=false;
		$db     = &DB();
		$result = $db->Execute(sqlSelect($db,"tax","id,description,rate",
		"(zone = '' OR zone IS NULL OR zone = ::*:: OR zone = ::$zone::) AND
            	 (country_id = ::$country_id:: OR country_id = '' OR country_id IS NULL)","zone")); 
		if(!$result || $result->RecordCount() == 0) {
			return false;
		} else  {
			while(!$result->EOF) {

				$arr[] = Array(	'rate' => round($result->fields["rate"] * $taxable_amount,2),
				'name' => $result->fields["description"],
				'id'   => $result->fields["id"]);

				$result->MoveNext();
			}
		}
		return $arr;
	}

	/**
    * Insert invoice_item_tax Records
    */       
	function invoice_item($invoice_id, $invoice_item_id, $account_id, $tax_arr) {
		$db =& DB();
		if(!is_array($tax_arr)) return false;
		foreach($tax_arr as $tax) {
			$sql="INSERT INTO ".AGILE_DB_PREFIX."invoice_item_tax SET id=".sqlGenID($db,"invoice_item_tax").", site_id=".DEFAULT_SITE.", date_orig=".time().", invoice_id={$invoice_id}, invoice_item_id={$invoice_item_id}, account_id={$account_id}, tax_id={$tax["id"]}, amount=".$db->qstr($tax["rate"]);
			$db->Execute($sql);
		}
	}
	
	/**
	 * Generate the HTML for tax id collection on account creation/update form 
	 */
	function get_tax_ids() { 		
		$db=&DB();
		$rs=$db->Execute($sql=sqlSelect($db,"tax","*","tax_id_collect=1 AND zone=::*::")); 
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				$arr[$rs->fields["tax_id_name"]] = $rs->fields;
				$rs->MoveNext();
			}
		} else {
			return false;
		}
		foreach($arr as $val) $ret[] = $val; 
		  
		global $smarty;
		$smarty->assign('tax_ids', $ret);
	}
	
	
	/**
	 * Validate inputted tax id on account addition/update
	 */
	function TaxIdsValidate($country_id, $tax_id, $exempt=false) {		
		$db=&DB();
		$rs=$db->Execute(sqlSelect($db,"tax","*","country_id=$country_id AND zone=::*:: AND tax_id_collect=1 AND tax_id_req=1"));				
		if($rs && $rs->RecordCount()) {
			$this->errField = $rs->fields['tax_id_name'];
			
			if(empty($tax_id)) 
				if($rs->fields['tax_id_exempt'] && $exempt)
					return true;
				else
					return false;
					 					
			if(!empty($rs->fields['tax_id_regex'])) {
				$regex=$rs->fields['tax_id_regex'];
				if(!preg_match(",$regex,", trim($tax_id))) return false;
			}
		}
		return true;
	}
	
	function tax_construct() {
		$this->module = "tax";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
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
		$this->tax_construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	function view($VAR) {
		$this->tax_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function update($VAR) {
		$this->tax_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR) {
		$this->tax_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	function search_form($VAR) {
		$this->tax_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR) {
		$this->tax_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR) {
		$this->tax_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}
}
?>