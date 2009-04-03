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
	
class group
{
	# Open the constructor for this mod
	function group()
	{
		# name of this module:
		$this->module = "group";

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
		$group_id   = $db->add($VAR, $this, $type);

		# add the new group to the account_group table:
		$db = &DB();
		$record_id = $db->GenID(AGILE_DB_PREFIX . 'account_group_id');
		$sql= "INSERT INTO ". AGILE_DB_PREFIX ."account_group SET
				id			= ".$db->qstr($record_id).",
				site_id 	= ".$db->qstr(DEFAULT_SITE).", 
				date_orig	= ".$db->qstr(time()).",
				date_expire = ".$db->qstr('0').",
				group_id	= ".$db->qstr($group_id).",
				account_id	= ".$db->qstr(SESS_ACCOUNT).",
				active		= ".$db->qstr(1);
		$result = $db->Execute($sql); 
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','select_groups', $db->ErrorMsg());
			return;
		}

		# update the current user's authentication so the newly added group appears
		# as available to them
		global $C_auth;
		$C_auth->auth_update();

		return;
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
		#remove any group ids <= 1001 from the VAR array:	
		global $C_debug, $C_auth;	
		$id_list = '';		
		if(isset($VAR["delete_id"])) 
			$id = explode(',',$VAR["delete_id"]); 
		elseif (isset($VAR["id"])) 
			$id = explode(',',$VAR["id"]);  

		for($i=0; $i<count($id); $i++) 
		{
			if(!empty($id[$i])  && $id[$i] > 1001 ) 
			{
				if($i == 0) 
					$id_list .= $id[$i];  
				else 
					$id_list .= ','.$id[$i];  

				# Check if group allowed:
				if(!$C_auth->auth_group_by_id($id[$i])) {
					$C_debug->alert('The selected group cannot be modified as your account is not authorized for it.');
					return;
				}    									
			} else {
				$C_debug->alert('The selected group is part of the CORE and cannot be edited.');
				return;
			}
		} 

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
		global $C_auth;

		$this->associated_DELETE[] = Array ('table' => 'account_group', 'field' => 'group_id');
		$this->associated_DELETE[] = Array ('table' => 'group_method',  'field' => 'group_id');

		#remove any group ids <= 1001 from the mass delete array:	
		global $C_debug;	
		$id_list = '';		
		if(isset($VAR["delete_id"])) 
			$id = explode(',',$VAR["delete_id"]); 
		elseif (isset($VAR["id"])) 
			$id = explode(',',$VAR["id"]);  

		for($i=0; $i<count($id); $i++) 
		{
			if(!empty($id[$i]) && $id[$i] > 1001 ) 
			{
				if($i == 0) 
					$id_list .= $id[$i];  
				else 
					$id_list .= ','.$id[$i]; 

				# Check if group allowed:
				if(!$C_auth->auth_group_by_id(1001) && !$C_auth->auth_group_by_id($id[$i])) {
					$C_debug->alert('The selected group cannot be modified as your account is not authorized for it.');
					return;
				}

			} else {
				$C_debug->alert('One or more of the groups selected to be deleted are part of the core and cannot be removed.');
			}
		}

		$VAR['id'] = $id_list;
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
	##	   VISUAL LAYOUT        ##
	##############################
	function visual_layout()
	{

		$class = 'form_field';

		# get the default group
		if(!isset($default)) $default = unserialize(DEFAULT_GROUP);
		for($i=0; $i<count($default); $i++) $checked[$default[$i]] = true;	                	

		# get the currect selected value & display
		$db = &DB();
		$sql= "SELECT id,name,parent_id FROM ".AGILE_DB_PREFIX."group WHERE id != '0' AND site_id = '" . DEFAULT_SITE . "' ORDER BY parent_id,name";
		$result = $db->Execute($sql);

		# error handling
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','select_groups', $db->ErrorMsg());
		}		

		# number of results
		if($result->RecordCount() > 0)
		{
			# set the results to an array:
			$arr = $result->GetArray();	

			# start the list
			$ret = '';

			#----------------------
			# start the parent loop
			#----------------------
			$group = 0;
			$arr_count = count($arr); 		
			for($i=0; $i < $arr_count; $i++)
			{
				$level = 0;
				if($arr[$i]['parent_id'] == $group)
				{

					$ret .= '&nbsp;&nbsp;'. $arr[$i]['name'];
					$ret .= '&nbsp;&nbsp;&nbsp; <a href="?_page=group:view&id='.$arr[$i]['id'].'">edit</a><br>';

					#----------------------
					# start the child loop
					#----------------------
					$level++;
					$ii_group = $arr[$i]['id'];
					$ii_print = 1;

					# count the available childs for this group
					$count_child[$ii_group]=0;
					for($c_child=0; $c_child < $arr_count; $c_child++)
						if($arr[$c_child]['parent_id'] == $ii_group) $count_child[$ii_group]++;

					for($ii=0; $ii < $arr_count; $ii++)
					{
						if($arr[$ii]['parent_id'] == $ii_group)
						{
							$ret .= '&nbsp;&nbsp;|__';
							$ret .= '&nbsp;&nbsp;'. $arr[$ii]['name'];
							$ret .= '&nbsp;&nbsp;&nbsp; <a href="?_page=group:view&id='.$arr[$ii]['id'].'">edit</a><br>';

							$ii_print++;

							#--------------------------
							# start the sub-child loop
							#--------------------------
							$level++;
							$iii_group = $arr[$ii]['id'];
							$iii_print = 0;
							for($iii=0; $iii < $arr_count; $iii++)
							{
								if($arr[$iii]['parent_id'] == $iii_group)
								{

									if($count_child[$ii_group] < $ii_print)
									{
										$ret .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__ ';
									}
									else
									{
										$ret .= '&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|__ ';
									}
									$ret .= '&nbsp;&nbsp;'. $arr[$iii]['name'];
									$ret .= '&nbsp;&nbsp;&nbsp; <a href="?_page=group:view&id='.$arr[$iii]['id'].'">edit</a><br>';
									$iii_print++;
								}	
							}
							$level--;	
							#-----------------------
							# end of sub-child loop
							#-----------------------

						}	
					}
					$level--;	
					#-------------------
					# end of child loop
					#-------------------
				}
			}    		    		
		}	
		else
		{
			return 'No groups available!'; // translate!
		}
		echo $ret;     	 			
	}


}
?>