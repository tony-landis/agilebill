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
	
class import
{

	# Open the constructor for this mod
	function import()
	{
		# name of this module:
		$this->module = "import";


		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

		# open the construct file for parsing	
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);


		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"];
	}

	### Store import id
	function import_transaction($plugin, $action, $ab_table, $ab_id, $remote_table, $remote_id, &$db)
	{
		# Check that this record has not already been imported:
		$sql = "SELECT id FROM ".AGILE_DB_PREFIX."import WHERE 
				plugin 		= ".$db->qstr($plugin)." AND
				action 		= ".$db->qstr($action)." AND
				ab_table 	= ".$db->qstr($ab_table)." AND
				ab_id		= ".$db->qstr($ab_id)." AND
				remote_table= ".$db->qstr($remote_table). " AND
				remote_id	= ".$db->qstr($remote_id)." AND
				site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($sql);         	

		# check results
		if($rs === false || $rs->RecordCount() > 0) {
			$db->FailTrans();
			return false;
		}

		# Insert the record
		$id = $db->GenID(AGILE_DB_PREFIX.'import_id');
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."import SET
				id 			= $id,
				date_orig 	= ".time().",
				plugin 		= ".$db->qstr($plugin).",
				action 		= ".$db->qstr($action).",
				ab_table 	= ".$db->qstr($ab_table).",
				ab_id		= ".$db->qstr($ab_id).",
				remote_table= ".$db->qstr($remote_table). ",
				remote_id	= ".$db->qstr($remote_id).",
				site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($sql);          	   
		return true;
	}     	


	### Do an action for a specific plugin: 
	function do_action ($VAR)
	{ 
		# Load the plugin
		if(!is_file($file = PATH_PLUGINS . 'import/'.@$VAR['plugin'].'.php'))
		return false;


		# New instance
		include_once($file);
		$import_plugin = new import_plugin;        	


		# Call the required method       	 
		call_user_func (array($import_plugin, @$VAR['action']), $VAR, $import_plugin);        	
	}


	### Do an action for a specific plugin: 
	function undo_action ($VAR)
	{
		$db = &DB();

		# Make sure this action is done...
		$sql = "SELECT * FROM ".AGILE_DB_PREFIX."import WHERE
						plugin = ".$db->qstr($VAR['plugin'])." AND
						action = ".$db->qstr($VAR['action'])." AND
						site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($sql);
		if($rs->RecordCount() == 0) {
			echo 'There is nothing to undo!';
			return;
		}      

		while(!$rs->EOF)
		{
				$table = $rs->fields['ab_table'];
				$id    = $rs->fields['ab_id'];

				$q = "DELETE FROM ".AGILE_DB_PREFIX."$table WHERE
						id = $id AND
						site_id = ".DEFAULT_SITE;
				$db->Execute($q);

				$rs->MoveNext();
		}

		# delete the selected action:
		$sql = "DELETE FROM ".AGILE_DB_PREFIX."import WHERE
				plugin = ".$db->qstr($VAR['plugin'])." AND
				action = ".$db->qstr($VAR['action'])." AND
				site_id = ".DEFAULT_SITE;
		$rs = $db->Execute($sql);  	        	
	}


	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{	         
		$db = &DB();

		if(!is_file($file = PATH_PLUGINS . 'import/'.@$VAR['plugin'].'.php'))
		return false;

		include_once($file);
		$import_plugin = new import_plugin;

		# Loop through each action to determine its availibility status 
		$actions = $import_plugin->actions;
		$done = false; 
		for($i=0; $i<count($actions); $i++) {	 
			# are the dependencies met? 
			$depn = $actions[$i]['depn']; 
			if(is_array($depn)) {  
				for($ii=0; $ii<count($depn); $ii++) {    
					if(empty($done["{$depn[$ii]}"])) {
						$actions[$i]['status'] = 'pending'; 
						break;
					}
				}
				if($actions[$i]['status'] != 'pending')
					$actions[$i]['status'] = 'ready';
			} else {
				$actions[$i]['status'] = 'ready';
			} 

			# passed dependencies, check if it has been run already or not: 
			if($actions[$i]['status'] == 'ready') {  
				$sql = "SELECT id FROM ".AGILE_DB_PREFIX."import WHERE
						plugin = ".$db->qstr($import_plugin->name)." AND
						action = ".$db->qstr($actions[$i]['name'])." AND
						site_id = ".DEFAULT_SITE;
				$rs = $db->Execute($sql);
				if($rs->RecordCount() > 0) {
					$actions[$i]['status'] = 'done';
					$actions[$i]['records'] = $rs->RecordCount();						
					$done["{$actions[$i]['name']}"] = true;
				} 
			} 
		}  		

		global $smarty;
		$smarty->assign('name', $import_plugin->name);
		$smarty->assign('instructions', $import_plugin->instructions);
		$smarty->assign('import', $actions); 	
	}	




	##############################
	##		    SEARCH		    ##
	##############################
	function search($VAR)
	{	
		### Read the contents of the /plugins/affiliate directory:
		$count = 0;
		chdir(PATH_PLUGINS . 'import');
		$dir = opendir(PATH_PLUGINS . 'import');
		while ($file_name = readdir($dir)) {
			if($file_name != '..' && $file_name != '.' && !preg_match("/^_/", $file_name) && preg_match("/.php$/i", $file_name)) { 
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
		chdir(PATH_PLUGINS . 'import');
		$dir = opendir(PATH_PLUGINS . 'import');
		while ($file_name = readdir($dir)) {
			if($file_name != '..' && $file_name != '.' && !preg_match("/^_/", $file_name) && preg_match("/.php$/i", $file_name) ) {
				$result[$count]['name'] = preg_replace('/.php/i', '', $file_name);
				$result[$count]['id']   = $count; 
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