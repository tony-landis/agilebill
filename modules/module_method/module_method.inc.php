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
	
class module_method
{

	# Open the constructor for this mod
	function module_method()
	{
		# name of this module:
		$this->module = "module_method";

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
	##		VIEW METHODS        ##
	##############################

	function view_methods($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$this->this_search_show($VAR, $this, $type);
	}	

	##############################
	##	VIEW METHODS p.2     #####
	##############################

	function this_search_show($VAR, $construct, $type)
	{
		global $VAR;

		# Get the list of parents for this group
		$this->group_parent_list($VAR['module_method_group_id']);


		# generate the full query
		$q = "SELECT * FROM
			  ".AGILE_DB_PREFIX."module_method
			  WHERE
			  module_id = ".$VAR['module_method_module_id'];
		$db = &DB();     			
		$result = $db->Execute($q);



		# put the results into a smarty accessable array
		$i=0;
		$class_name = TRUE;

		while (!$result->EOF) {	
			$smart[$i] = $result->fields;	
			$method_id = $smart[$i]['id'];

			## Get the auth status for this method & group
			$q1 = 'SELECT id FROM '.AGILE_DB_PREFIX.'group_method WHERE
				method_id = '.$smart[$i]['id'].' AND
				group_id  = '.$VAR['module_method_group_id'];				    	
			$db1 = &DB();
			$resulta = $db1->Execute($q1);
			#echo "<BR> $q1";				

			## authorized by current group
			if($resulta->RecordCount() >= 1)
			{
				#echo "<BR>1 - This group matches!";
				$smart[$i]['checked'] = '1';
			}
			else
			{								    		
				# get the parent group id(s) for this group
				$match = false;
				for($ii=0; $ii < count($this->group); $ii++)
				{
					if($match == false && $this->group[$ii] != $VAR['module_method_group_id'])
					{
						$q2 = 'SELECT id FROM '.AGILE_DB_PREFIX.'group_method
								WHERE method_id = '.$method_id.'
								AND group_id = '.$this->group[$ii];

						$db2 = &DB();
						$resultb = $db2->Execute($q2);              		

						#echo "<BR> $q2";                    		
						if($resultb->RecordCount() >= 1)
						  $match = true;
					}
				}


				## authorized by parent
				if($match)
				{    		
					#echo "<BR>2 - This Parent Matches!";	
					$smart[$i]['checked'] = '2';
				}
				else
				{
					## not authorized
					#echo "<BR>3 - NO matches";
					$smart[$i]['checked'] = '3';    				
				}
			}


			if($class_name)
			{
				$smart[$i]['_C'] = 'row2';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row1';
				$class_name = TRUE;
			}

			$result->MoveNext();
			$i++;

		}

		# get any linked fields
		if($i > 0)
		{
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($smart, $this->linked);
		}
		else
		{
			$this->result = $smart;
		}


		# get the result count:
		$results = $result->RecordCount();
		# define the DB vars as a Smarty accessible block
		global $smarty;
		# define the results
		$smarty->assign($construct->table, $this->result);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	1111);
		$smarty->assign('sort',		1111);
		$smarty->assign('limit',	1111);
		$smarty->assign('search_id',1111);
		$smarty->assign('results', 	$results);

		# get the total pages for this search:
		$this->pages = 1;
		if(null != $search) {
			if ($search->results % $search->limit) $this->pages++;
		}
		# total pages
		$smarty->assign('pages', 	$this->pages);
		# current page
		$smarty->assign('page', 	$current_page);
		$page_arr = '';
		for($i=0; $i <= $this->pages; $i++)
		{
			if ($this->page != $i) 	$page_arr[] = $i;
		}
		# page array for menu
		$smarty->assign('page_arr',	$page_arr);

	}


	/**
	* Get any inherited groups (parent) for a specified group
	* @return 	void
	* @since 	Version 1.0
	* @param 	int Group Id
	*/
	function group_parent_list($group)
	{
		# check if this group is a child with an active parent
		$db = &DB();
		$q = "SELECT id,parent_id FROM " . AGILE_DB_PREFIX . "group
				WHERE id		 	=  '$group'
				AND   site_id 		=  '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# error handling
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('auth.inc.php','group_parent_list', $db->ErrorMsg());
		}

		# loop through the results    	
		while (!$result->EOF)
		{
			# add this group to the list
			$this->group[] = $result->fields['id'];

			# get inherited groups and check that the associated
			# group is active before adding to the list of authorized groups.
			if($result->fields['parent_id'] != 0)
			{
				$this->group_parent_list($result->fields['parent_id']);
			}

			# move to next record
			$result->MoveNext();
		}
	}

	/**
	* Mass add methods to a specific group
	* @return 	void
	* @since 	Version 1.0
	* @param 	int Group Id
	*/

	function update_relations()
	{
		global $VAR;

		if(isset($VAR['id']) && $VAR['id'] != '')
		{
			$arr = explode(',', $VAR['id']);
		}
		else { return;}



		for($i=0; $i<count($arr); $i++)
		{
			if($i == 0)
			{
				# clear the entries first
				$q = "DELETE FROM ".AGILE_DB_PREFIX."group_method
						  WHERE
						  site_id    = '" . DEFAULT_SITE . "' AND
						  module_id  = '".$VAR['module_method_module_id']."' AND
						  group_id   = '".$VAR['module_method_group_id']."'";	
				$db = &DB();     			
				$result = $db->Execute($q);	
			}           		

			if($arr[$i] == 0) return;

			# determine the record id:
			$this->record_id = $db->GenID(AGILE_DB_PREFIX . 'group_method_id');

			# generate the full query
			$q = "INSERT INTO ".AGILE_DB_PREFIX."group_method
				  SET
				  id        = '".$this->record_id."',
				  site_id   = '" . DEFAULT_SITE . "',
				  method_id = '".$arr[$i]."',
				  module_id = '".$VAR['module_method_module_id']."',
				  group_id  = '".$VAR['module_method_group_id']."'";	    	        	    	        	
			$db = &DB();     			
			$result = $db->Execute($q);
		}
	}           	       	
}
?>