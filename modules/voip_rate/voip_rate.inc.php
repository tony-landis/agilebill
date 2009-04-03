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
	
class voip_rate
{

	# Open the constructor for this mod
	function voip_rate()
	{
		# name of this module:
		$this->module = "voip_rate";

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

	function import($VAR)
	{
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db,"product", "id,sku", "prod_plugin_file=::VOIP:: and prod_plugin=1"));
		$prods[0] = "-- NONE --";
		while (!$rs->EOF) {
			$prods[$rs->fields['id']] = $rs->fields['sku'];
			$rs->MoveNext();
		}
		$ic[0]['name'] = 'product_id';
		$ic[0]['type'] = 'select';
		$ic[0]['value'] = $prods;
		$this->import_custom = $ic;

		include_once(PATH_CORE.'import.inc.php');
		$import = new CORE_import;

		if(empty($VAR['confirm'])) {
			$import->prepare_import($VAR, $this);
		} else {
			$import->do_new_import($VAR, $this);
		}		
	}

	function import_line_process(&$db, &$VAR, &$fields, &$record)
	{
				if (!$VAR['import_select']['product_id'])
					return;
				$f['product_id'] = $VAR['import_select']['product_id'];
				$f['voip_rate_id'] = $record['id'];
				$db->Execute(sqlInsert($db, "voip_rate_prod", $f));
	}

	/** Output avial/assigned rate tables for configuration
	*/
	function product_rates($VAR) 
	{ 
		@$product = $VAR['product'];

		$avail=false;
		$assigned=false;

		$db=&DB();         	
		$as = $db->Execute($sql = sqlSelect($db,"voip_rate_prod","voip_rate_id","product_id = ::$product::"));           	 
		if($as && $as->RecordCount() > 0) {
			while(!$as->EOF) {  
				$av["{$as->fields['voip_rate_id']}"] = true;       			       			
				$as->MoveNext();
			}         		
		}

		$rs = $db->Execute($sql = sqlSelect($db,"voip_rate","id,name,pattern,amount",""));   
		if($rs && $rs->RecordCount() > 0)
		{
			while(!$rs->EOF) 
			{
				if(is_array($av) && array_key_exists($rs->fields['id'], $av)) {
					$assigned[] = Array('id'=> $rs->fields['id'], 'name' => $rs->fields['name'].' - '. substr($rs->fields['pattern'],0,20).' - '.$rs->fields['amount']);					   
				} else {
					$avail[] = Array('id'=> $rs->fields['id'], 'name' => $rs->fields['name'].' - '.substr($rs->fields['pattern'],0,20).' - '.$rs->fields['amount']);
				}
				$rs->MoveNext();
			}
		}

		global $smarty; 
		$smarty->assign('avail', $avail);
		$smarty->assign('assigned', $assigned);       	        	  
	}

	/** Save updated rate tables for product
	*/
	function products($VAR)
	{
		$product = $VAR['product'];
		$avail = $VAR['avail'];
		$assigned = @$VAR['assigned'];
		$db = &DB();

		// clean out any selected ids from the 'assigned' array
		if(is_array($assigned))
		foreach($assigned as $voip_rate_id)
		$db->Execute(sqlDelete($db,"voip_rate_prod"," product_id = ::$product:: AND voip_rate_id = $voip_rate_id"));

		// add any selected ids from the 'avail' array
		if(is_array($avail)) {
			foreach($avail as $voip_rate_id) {
				$fields=Array('product_id' => $product, 'voip_rate_id' => $voip_rate_id);
				$id = $db->Execute(sqlInsert($db,"voip_rate_prod",$fields));
			}       	
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
}
?>