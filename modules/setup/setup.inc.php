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
	
class setup
{

	# Open the constructor for this mod
	function setup()
	{
		# name of this module:
		$this->module = "setup";

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
		if ($VAR['setup_currency_id'] != DEFAULT_CURRENCY)
			$curr = true;
		else
			$curr = false;        		       	

		# make sure the index.php file is not included at the end:
		if(!empty($VAR['setup_ssl_url']))
			$VAR['setup_ssl_url'] = preg_replace('/index.php/', '', $VAR['setup_ssl_url']);
		if(!empty($VAR['setup_nonssl_url']))
			$VAR['setup_nonssl_url'] = preg_replace('/index.php/', '', $VAR['setup_nonssl_url']);        		

		# Validate trailing slash is on the end of the URL:
		if(!empty($VAR['setup_ssl_url']) && !preg_match('@/$@', $VAR['setup_ssl_url']))
			$VAR['setup_ssl_url'] .= '/';		
		# Validate trailing slash is on the end of the URL:
		if(!empty($VAR['setup_nonssl_url']) && !preg_match('@/$@', $VAR['setup_nonssl_url']))
			$VAR['setup_nonssl_url'] .= '/';	        			

		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$rs = $db->update($VAR, $this, $type);

		if($rs && $curr) 
		{ 
			/* Start: Update all sessions & accounts */
			$db = &DB();
			$sql = "UPDATE ".AGILE_DB_PREFIX."session 
					SET
					currency_id = ".$db->qstr($VAR['setup_currency_id'])."
					WHERE
					site_id 	= ".$db->qstr(DEFAULT_SITE)." AND
					currency_id != ".$db->qstr($VAR['setup_currency_id']);
			$rs = $db->Execute($sql); 	

			$sql = "UPDATE ".AGILE_DB_PREFIX."account 
					SET
					currency_id = ".$db->qstr($VAR['setup_currency_id'])."
					WHERE
					site_id 	= ".$db->qstr(DEFAULT_SITE)." AND
					currency_id != ".$db->qstr($VAR['setup_currency_id']);
			$rs = $db->Execute($sql); 				 					
			/* End: SQL Insert Statement */
		}

		# Clear out the cache entry
		if (defined("AGILE_CORE_CACHE_DIR") && AGILE_CORE_CACHE_DIR != '') {
			$tfile = AGILE_CORE_CACHE_DIR."core-setup";
			if (file_exists($tfile)) {
				unlink(AGILE_CORE_CACHE_DIR."core-setup");
			}
		}
	}


	##############################
	##		PHP_INFO            ##
	##############################

	function _php_info() {
		phpinfo();
	}
}
?>
