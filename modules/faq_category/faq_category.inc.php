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
	
class faq_category
{

	# Open the constructor for this mod
	function faq_category_construct()
	{
		# name of this module:
		$this->module = "faq_category";
		$this->version = "1.0.0a";

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
	##	GET AUTH CATEGORIES     ##
	##############################

	function category_list($VAR)
	{	

		/* check if current session is authorized for any ticket departments..
			and return true/false...
		*/
		global $smarty;
		$db     = &DB();
		$sql    = 'SELECT DISTINCT fc.id,fc.name,fc.group_avail,count(f.id) children, fc.site_id, fc.status 
					FROM 
					' . AGILE_DB_PREFIX . 'faq_category fc
					INNER JOIN
					' . AGILE_DB_PREFIX . 'faq f ON f.faq_category_id = fc.id 
					GROUP BY (fc.id)
					HAVING
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1') .'
					ORDER BY fc.sort_order,fc.name';

		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			$smarty->assign('faq_category_list_display', false);
			return false;
		}

		global $C_auth;
		$ii = 0;

		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_avail']);

			for($i=0; $i<count($arr); $i++)
			{
				if($C_auth->auth_group_by_id($arr[$i]))
				{
					### Add to the array
					$ii++;
					$arr_smarty[] = Array(  'name'          => $result->fields['name'],
											'id'            => $result->fields['id'],
											'children'	=> $result->fields['children']);

					$i=count($arr);
				}
			}
			$result->MoveNext();
		}

		if($ii == "0")
		{
			 $smarty->assign('faq_category_list_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('faq_category_list_display', 	true);
			$smarty->assign('faq_category_list_results', 	$arr_smarty);
			return true;
		}
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->faq_category_construct();

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
		$this->faq_category_construct();

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
		$this->faq_category_construct();

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
		$this->faq_category_construct();

		$db = new CORE_database;
		 $db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->faq_category_construct();

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
		$this->faq_category_construct();

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
		$this->faq_category_construct();

		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_show($VAR, $this, $type);
	}	

}
?>