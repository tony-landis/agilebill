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
	
class email_queue
{

	# Open the constructor for this mod
	function email_queue()
	{
		# name of this module:
		$this->module = "email_queue";

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
	##	SEND E-MAILS IN QUEUE   ##
	##############################
	function send($VAR)
	{ 
		$VAR_ORIG = $VAR;

		$db 	= &DB();
		$dbm 	= new CORE_database;
		$sql 	= $dbm->sql_select("email_queue", "*", "status = 0", "", $db);
		$rs 	= $db->Execute($sql);

		if($rs != false && $rs->RecordCount() > 0)
		{
			require_once ( PATH_MODULES . 'email_template/email_template.inc.php' );
			while(!$rs->EOF)
			{
				# Get values 
				global $VAR;
				$VAR  = unserialize($rs->fields['var']);

				$sql1 = $rs->fields['sql1'];
				$sql2 = $rs->fields['sql2'];
				$sql3 = $rs->fields['sql3'];

				if(!empty($sql1)) {
					if(ereg("^a:", $sql1) && is_array(unserialize($sql1)))  $sql1 = unserialize($sql1); 
				} else {
					$sql1 = false;
				}

				if(!empty($sql2)) {
					if(ereg("^a:", $sql2) && is_array(unserialize($sql2)))  $sql2 = unserialize($sql2); 
				} else {
					$sql2 = false;
				}

				if(!empty($sql3)) {
					if(ereg("^a:", $sql3) && is_array(unserialize($sql3)))  $sql3 = unserialize($sql3); 
				} else {
					$sql3 = false;
				}    				 

				# Send email
				$mail = new email_template;
				$result = $mail->send($rs->fields['email_template'], $rs->fields['account_id'], $sql1, $sql2, $sql3, false);

				# Update to sent status
				if($result)  
				{
					$sql = "UPDATE ".AGILE_DB_PREFIX."email_queue SET
							status = 1
							WHERE
							id		= {$rs->fields['id']}
							AND
							site_id	= ".DEFAULT_SITE;
					$db->Execute($sql);
				} 			
				$rs->MoveNext();
			}
		}

		$VAR = $VAR_ORIG;   		
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