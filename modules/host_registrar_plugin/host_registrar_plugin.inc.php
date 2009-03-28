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
	
class host_registrar_plugin
{

	# Open the constructor for this mod
	function host_registrar_plugin()
	{		        	
		# name of this module:
		$this->module = "host_registrar_plugin";

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

		### Read the contents of the /plugins/affiliate directory:
		$count = 0;
		chdir(PATH_PLUGINS . 'registrar');
		$dir = opendir(PATH_PLUGINS . 'registrar');
		while ($file_name = readdir($dir))
		{
			if($file_name != '..' && $file_name != '.' && !eregi("^_", $file_name))
			{ 
				$count++;
			}
		}

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# create the search record:
		if($count > 0)
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module'] 	= $this->module;	
			$arr['sql']		= '';
			$arr['limit']  	= '999';
			$arr['order_by']= 'name';
			$arr['results']	= $count;
			$search->add($arr);

			# define the search id and other parameters for Smarty
			$smarty->assign('search_id', $search->id);

			# page:
			$smarty->assign('page', '1');

			# limit:
			$smarty->assign('limit', '999');

			# order_by:
			$smarty->assign('order_by', 'name');

			# define the result count
			$smarty->assign('results', $count);				
		}          		
	}


	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{	

		### Read the contents of the /plugins/db_mapping directory:
		$count = 0;
		chdir(PATH_PLUGINS . 'registrar');
		$dir = opendir(PATH_PLUGINS . 'registrar');
		while ($file_name = readdir($dir))
		{
			if($file_name != '..' && $file_name != '.' && !eregi("^_", $file_name) )
			{
				$result[$count]['name'] = eregi_replace('.php', '', $file_name);
				$result[$count]['id']   = $count;

				### Get the status of this plugin:
				$db = &DB();
				$q  = 'SELECT status,id FROM '.AGILE_DB_PREFIX.'host_registrar_plugin WHERE
						file = '. $db->qstr($result[$count]['name']) . ' AND
						site_id  = '. $db->qstr(DEFAULT_SITE);
				$dbmap = $db->Execute($q);

				### error reporting:
				if ($dbmap === false)
				{
					global $C_debug;
					$C_debug->error('affiliate_plugin.inc.php','search_show', $db->ErrorMsg()); return;
				}

				if($dbmap->RecordCount() > 0)
				{
					$result[$count]['id'] = $dbmap->fields['id'];
					$result[$count]['status'] = 1;
					$result[$count]['active'] = $dbmap->fields['status'];
				}
				else
				{
					$result[$count]['status'] = 0;
				}

				$count++;
			}
		}


		$class_name = TRUE;
		for ($i=0; $i<count($result); $i++)
		{
			$smart[$i] = $result[$i]; 				
			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}    			
		}



		# define the DB vars as a Smarty accessible block
		global $smarty;

		# define the results
		$smarty->assign($this->table, $smart);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	$smarty_order);
		$smarty->assign('sort',		$smarty_sort);
		$smarty->assign('limit',	$search->limit);
		$smarty->assign('search_id',$search->id);
		$smarty->assign('results', 	$count);

		# total pages
		$smarty->assign('pages', 	1);

		# current page
		$smarty->assign('page', 	1);
		$page_arr = '';

		# page array for menu
		$smarty->assign('page_arr',	$page_arr);	
	}
}
?>