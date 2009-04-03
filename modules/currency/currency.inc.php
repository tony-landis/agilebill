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
	
class currency
{

	# Open the constructor for this mod
	function currency()
	{
		# name of this module:
		$this->module = "currency";

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
		# this function removed in v1.4.2
		return false;

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

	function task($VAR)
	{
		$db = &DB();

		// Fetch all active currencies
		$currencies = array();
		$rs = $db->Execute(sqlSelect($db, "currency", "*", "status=1"));
		if ($rs)
		{
		   while (!$rs->EOF)
		   {
		      $currencies[$rs->fields['id']] = $rs->fields;
		      $rs->MoveNext();
		   }

		   $rs->Close();
		}

		foreach ($currencies as $currFrom)
		{
		   $conversions = array();
		   foreach ($currencies as $currTo)
		   {
		      // Get currency conversion
		      if ($currFrom['three_digit'] != $currTo['three_digit'])
		      {
		         $ch = curl_init('http://www.xe.net/ucc/convert.cgi?Amount=1&From=' . $currFrom['three_digit'] . '&To=' . $currTo['three_digit']);
		         curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		         curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		         curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
		         curl_setopt($ch, CURLOPT_CRLF, true);
		         $resp = curl_exec ($ch);
		         curl_close ($ch);

                         $m = array();
		         preg_match('/[0-9.]+\s*' . $currFrom['three_digit'] . '\s*=\s*([0-9.]+)\s*' . $currTo['three_digit'] . '/', $resp, $m);
		      }
		      else
		      {
		         // Conversion to/from same currency is always 1.
		         $m = array(1 => '1');
		      }

		      if (sizeof($m) > 0)
		      {
		         $conversions[$currTo['id']] = array (
		             'rate' => $m[1]
		            ,'iso' => $currTo['three_digit']
		         );
		      }
		   }

		   // Update conversions array
		   $db->Execute('UPDATE ' . AGILE_DB_PREFIX . 'currency SET convert_array = ' . $db->qstr(serialize($conversions)) . ' WHERE id = ' . $db->qstr($currFrom['id']) . ' AND site_id = ' . $db->qstr($currFrom['site_id']));
		}
	}
}
?>
