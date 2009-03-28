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
	
class db_mapping
{
	var $sync_limit=100;

	# Open the constructor for this mod
	function construct()
	{       	
		# name of this module:
		$this->module = "db_mapping";

		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . $this->module . "/" . $this->module . "_construct.xml";

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


	########################################################################
	### Syncronize all accounts & groups
	########################################################################

	function sync($VAR)
	{
		$id = @$VAR['id'];
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE id = '.$db->qstr(@$VAR["id"]).' AND site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false) { 
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','sync', $db->ErrorMsg()); 
			echo $db->ErrorMsg();
			return;
		}

		$file   = $result->fields['map_file'];  		
		$group_map = $result->fields['group_map'];

		if($file != '')
		{
			include_once(PATH_PLUGINS . 'db_mapping/'. $file.'.php');
			eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
			$_MAP->sync($id, $file);
			return true;
		}
		return false;
	}





	##############################
	##  Connect to the remote db #
	##############################

	function &DB_connect($id, $map_file)
	{

		### Get the variables for this map plugin:
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE ';

		if($id)
		  $sql .= ' id      = '.$db->qstr($id).' AND ';
		else if($map_file)
		  $sql .= ' map_file      = '.$db->qstr($map_file).' AND ';

		$sql .= ' site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','DB_connect', $db->ErrorMsg()); return;
		}

		$file   = $result->fields['map_file'];
		if($file != '')
		{
			include_once(PATH_PLUGINS . 'db_mapping/'. $file.'.php');
			eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );

			### Get the DB settings
			$db_name      = $result->fields['db_name'];
			$db_host      = $result->fields['db_host'];
			$db_prefix    = $result->fields['db_prefix'];
			$db_user      = $result->fields['db_user'];
			$db_pass      = $result->fields['db_pass'];
			@$db_type     = $_MAP->map['db_type'];

			if($db_host == '*') $db_host = AGILE_DB_HOST;
			if($db_user == '*') $db_user = AGILE_DB_USERNAME;
			if($db_pass == '*') $db_pass = AGILE_DB_PASSWORD;
			if($db_type == '')  $db_type = 'mysql';

			$const = 'DB2_PREFIX' . strtoupper($file);
			if(!defined($const)) eval ( 'define ("'.$const.'", "'.$db_prefix.'");' );

			$this->db_prefix = $db_prefix;

			### Connect to the remote Db;
			$ADODB = &NewADOConnection($db_type);
			$ADODB->Connect($db_host, $db_user, $db_pass, $db_name);
			$ADODB_FETCH_MODE = 'ADODB_FETCH_ASSOC';
			return $ADODB; 
		}
		return false;
	}



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->construct();
		global $C_translate;

		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);   

		# set the field list for this method:
		$arr = $this->method["$type"];

		# define the validation class
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;		
		$this->validated = true;	


		####################################################################
		# loop through the field list to validate the required fields
		####################################################################

		while (list ($key, $value) = each ($arr))
		{
			# get the field value
			$field_var  	= $this->module . '_' . $value;
			$field_name 	= $value;
			$this->validate = true;

			####################################################################
			# perform any field validation...
			####################################################################

			# check if this value is unique
			if(isset($this->field["$value"]["unique"]) && isset($VAR["$field_var"]))
			{
				if(!$validate->validate_unique($this->table, $field_name, "record_id", $VAR["$field_var"]))
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->table . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),							# translate
												'error' 		=> $C_translate->translate('validate_unique',"", ""));	 				
				}
			}

			# check if the submitted value meets the specifed requirements
			if(isset($this->field["$value"]["validate"]))
			{
				if(isset($VAR["$field_var"]))
				{
					if($VAR["$field_var"] != '')
					{
						if(!$validate->validate($field_name, $this->field["$value"], $VAR["$field_var"], $this->field["$value"]["validate"]))
						{
							$this->validated = false;
							$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
														'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
														'error' 		=> $validate->error["$field_name"] );								
						}					
					}
					else
					{
						$this->validated = false;
						$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
													'error' 		=> $C_translate->translate('validate_any',"", "")); 	
					}
				}
				else
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
												'error' 		=> $C_translate->translate('validate_any',"", "")); 		 																		
				}
			}
		}




		####################################################################
		# If validation was failed, skip the db insert &
		# set the errors & origonal fields as Smarty objects,
		# and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;	

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $this->val_error);	

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
			   define('FORCE_PAGE', $VAR['_page_current']);
			}

			# define any triggers
			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}

			return;
		}
		else
		{                		
			# begin the new database class:
			$db = &DB();


			# loop through the field list to create the sql queries
			$field_list = '';
			$i = 0;
			reset($arr);
			while (list ($key, $value) = each ($arr))
			{
				# get the field value
				$field_var  	= $this->module . '_' . $value;
				$field_name 	= $value;

				####################################################################
				# perform any special actions
				####################################################################
				# md5, rc5, pgp, gpg, time, date, date-time

				if(isset($this->field["$value"]["convert"]) && isset($VAR["$field_var"]))
				{
					# do the conversion...
					$VAR["$field_var"] = $validate->convert($field_name, $VAR["$field_var"], $this->field["$value"]["convert"]);
				}


				if(isset($VAR["$field_var"]))
				{
					$field_list .= ", " . $value . "=" . $db->qstr($VAR["$field_var"]);
				}
			}

			# add a comma before the site_id if needed
			if($field_list != '')
			{
				$field_list .= ',';
			}

			# determine the record id:
			$this->record_id = $db->GenID(AGILE_DB_PREFIX . "" . $this->table.'_id');

			# determine the record id, if it is an ACCOUNT record
			if($this->table == 'account') 	$this->record_id = md5($this->record_id . '' . microtime());

			# define the new ID as a constant
			define(strtoupper('NEW_RECORD_'.$this->table.'_ID'), $this->record_id);

			# generate the full query
			$q = "INSERT INTO ".AGILE_DB_PREFIX."$this->table
					SET
					id = ". $db->qstr($this->record_id)."
					$field_list
					site_id = " . $db->qstr(DEFAULT_SITE);

			# execute the query
			$result = $db->Execute($q);

			# error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('database.inc.php','add', $db->ErrorMsg());

				if(isset($this->trigger["$type"]))
				{
					include_once(PATH_CORE   . 'trigger.inc.php');
					$trigger    = new CORE_trigger;
					$trigger->trigger($this->trigger["$type"], 0, $VAR);
				}
			}


			$VAR["id"] = $this->record_id;
			@$redirect_page = $VAR['_page'];
			define('REDIRECT_PAGE', '?_page=' . $redirect_page . '&id=' . $this->record_id . '&s=' . SESS);

			# RUN ANY INSTALL SCRIPT!
			$file       = $VAR['db_mapping_map_file'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );

				if ( isset($_MAP->map['install']) && $_MAP->map['install'] == true)
				{
					$_MAP->install();
				}
			}
		}
	}



	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{	
		global $smarty;
		$this->construct();

		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->view($VAR, $this, $type);

		### Define the group mapping....
		$id = @$VAR['id'];

		### Get the variables for this map plugin:
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			  id      = '.$db->qstr(@$VAR["id"]).' AND
			  site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false) { global $C_debug;
			$C_debug->error('db_mapping.inc.php','view', $db->ErrorMsg()); return;
		}

		$file   = $result->fields['map_file'];

		$group_map = $result->fields['group_map'];
		if($group_map != '')
		$group_map = unserialize ( $group_map );
		else
		$group_map = Array();

		if($file != '')
		{
			include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
			eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );

			### If this map type is 'db' groups based:   	
			if ($_MAP->map['group_type'] == 'db' || $_MAP->map['group_type'] == 'db-status')
			{
				### Connect to the DB & get the groups:
				$dbm      = new db_mapping;    		
				$db       = $dbm->DB_connect($id, 'false');
				eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($file) .';' );
				$sql      = "SELECT * FROM " . $db_prefix . "" . $_MAP->map['group_map']['table'] . "
							 ORDER BY      " . $_MAP->map['group_map']['name'];
				$db2 = $db->Execute($sql);
				if ($db2 === false)
				{
					global $C_debug;
					$C_debug->error('db_mapping.inc.php','view', $db->ErrorMsg());
					$smarty->assign('db_mapping_result', $db->ErrorMsg());
					return;
				}        		

				### get the remote groups...
				if($db2->RecordCount() > 0)
				{


					$i=0;
					while ( !$db2->EOF )
					{
					  $smart[$i]['id']      = $db2->fields[$_MAP->map['group_map']['id']];
					  $smart[$i]['name']    = $db2->fields[$_MAP->map['group_map']['name']];
					  $db2->MoveNext();
					  $i++;
					}

					### Get the local groups:
					$db = &DB();
					$sql = 'SELECT * FROM '.AGILE_DB_PREFIX.'group WHERE
							site_id = '.$db->qstr(DEFAULT_SITE).'
							ORDER BY name';
					$groups = $db->Execute($sql);
					if ($groups === false)
					{
						global $C_debug;
						$C_debug->error('db_mapping.inc.php','view', $db->ErrorMsg());
						return;
					}

					if($groups->RecordCount() > 0)
					{
						$i=0;
						while ( !$groups->EOF )
						{
						  $id = $groups->fields['id'];
						  $smartgr[$i]['id']     = $groups->fields['id'];
						  $smartgr[$i]['name']   = $groups->fields['name'];


						  for($ii=0; $ii<count($smart); $ii++)
						  {
							  $rid = $smart[$ii]['id'];
							  $name= $smart[$ii]['name'];
							  $checked = false;
							  if(isset($group_map[$id][$rid]) && $group_map[$id][$rid] != false )
							  $checked = true;
							  $smartgr[$i]['remote'][$ii]['id']   = $rid;
							  $smartgr[$i]['remote'][$ii]['name'] = $name;
							  $smartgr[$i]['remote'][$ii]['check']= $checked;            		
						  }

						  $groups->MoveNext();
						  $i++;
						}

						### Define smarty vars
						$smarty->assign('db_mapping_result', false);
						$smarty->assign('db_mapping_template', 'db_mapping:group_map_' . $_MAP->map['group_type']);
						$smarty->assign('db_mapping_groups', $smartgr);              		
					}
					else
					{
						global $C_translate;
						$message = $C_translate->translate('no_local_groups','db_mapping','');
						$smarty->assign('db_mapping_result', $message);
					}            		
				}
				else
				{
					global $C_translate;
					$message = $C_translate->translate('no_remote_groups','db_mapping','');
					$smarty->assign('db_mapping_result', $message);
				}
			}
			elseif  ( $_MAP->map['group_type'] == 'status' )
			{
				### This is at 'status' based database map

				### Get the local groups:
				$db = &DB();
				$sql = 'SELECT * FROM '.AGILE_DB_PREFIX.'group WHERE
						site_id = '.$db->qstr(DEFAULT_SITE).'
						ORDER BY name';
				$groups = $db->Execute($sql);
				### error reporting:
				if ($groups === false)
				{
					global $C_debug;
					$C_debug->error('db_mapping.inc.php','view', $db->ErrorMsg()); return;
				}            		
				if($groups->RecordCount() > 0)
				{
					$i=0;
					while ( !$groups->EOF )
					{
						$id = $groups->fields['id'];
						$smart[$i]['id']     = $groups->fields['id'];
						$smart[$i]['name']   = $groups->fields['name'];
						@$smart[$i]['value']  = $group_map[$id];              		        		
						$groups->MoveNext();
						$i++;
					}
				}


				### Assign the smarty vars:
				$smarty->assign('db_mapping_result', false);
				$smarty->assign('db_mapping_template', 'db_mapping:group_map_status');
				$smarty->assign('db_mapping_groups', $smart);              		
			}
			else
			{
				### No group mapping for this database map
				global $C_translate;
				$message = $C_translate->translate('no_group_mapping','db_mapping','');
				$smarty->assign('db_mapping_result', $message);
			}                              		
		}
		else
		{
			global $C_translate;
			$message = $C_translate->translate('file_error','db_mapping','');
			$smarty->assign('db_mapping_result', $message);
		}
	}		

	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$this->construct();
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
		$this->construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		



	##############################
	##		    SEARCH		    ##
	##############################
	function search($VAR)
	{
		$this->construct();

		### Read the contents of the /plugins/db_mapping directory:
		$count = 0;
		chdir(PATH_PLUGINS . 'db_mapping');
		$dir = opendir(PATH_PLUGINS . 'db_mapping');
		while ($file_name = readdir($dir))
		{
			if($file_name != '..' && $file_name != '.')
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
		$this->construct();

		### Read the contents of the /plugins/db_mapping directory:
		$count = 0;
		chdir(PATH_PLUGINS . 'db_mapping');
		$dir = opendir(PATH_PLUGINS . 'db_mapping');
		while ($file_name = readdir($dir))
		{
			if($file_name != '..' && $file_name != '.')
			{
				$result[$count]['name'] = preg_replace('/.php/', '', $file_name);
				$result[$count]['id']   = $count;

				### Get the status of this plugin:
				$db = &DB();
				$q  = 'SELECT status,id FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
						map_file = '. $db->qstr($result[$count]['name']) . ' AND
						site_id  = '. $db->qstr(DEFAULT_SITE);
				$dbmap = $db->Execute($q);

				### error reporting:
				if ($dbmap === false)
				{
					global $C_debug;
					$C_debug->error('db_mapping.inc.php','search_show', $db->ErrorMsg()); return;
				}

				if($dbmap->RecordCount() > 0)
				{
					$result[$count]['id'] = $dbmap->fields['id'];
					$result[$count]['status'] = 1;
					$result[$count]['active'] = $dbmap->fields['status'];
				} else {
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






################################################################################    	
# >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
################################################################################    	


	########################################################################
	###    Add a new account to the remote db
	########################################################################

	function account_add ( $account_id )
	{
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','account_add', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->plaintext_password = $this->plaintext_password;
				$_MAP->account_add ( $account_id );
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}







	########################################################################
	###    Edit an existing account in the remote db
	########################################################################

	function account_edit ( $account_id, $old_username )
	{
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','account_edit', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->plaintext_password = $this->plaintext_password;
				$_MAP->account_edit ( $account_id, $old_username );
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}









	########################################################################
	###    Delete an account from the remote db
	########################################################################

	function account_delete ( $account_id, $username )
	{
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','account_delete', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->account_delete ( $account_id, $username );
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}








	########################################################################
	###    Sync the remote groups for a specific user
	########################################################################

	function account_group_sync ( $account_id )
	{		
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql); 
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','account_group_sync', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->account_group_sync ( $account_id );
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}








	########################################################################
	###    Log the user in
	########################################################################

	function login ( $account_id )
	{

		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','login', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			$cookie = $result->fields['cookie_name'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->login ( $account_id, $cookie);
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}








	########################################################################
	###    Log the user out
	########################################################################

	function logout ( $account_id )
	{
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'db_mapping WHERE
			   status  = '.$db->qstr(1).' AND
			   site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','logout', $db->ErrorMsg());
			return;
		}

		while ( !$result->EOF )
		{
			$file   = $result->fields['map_file'];
			$id     = $result->fields['id'];
			$cookie = $result->fields['cookie_name'];
			if($file != '')
			{
				include_once (PATH_PLUGINS . 'db_mapping/'. $file.'.php');               	
				eval ( '$_MAP = new map_'. strtoupper ( $file ) . ';' );
				$_MAP->logout ( $account_id, $cookie );
				unset ($_MAP);
			}
			$result->MoveNext();
		}
	}








################################################################################
# <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<  	
################################################################################    	
















############################################################################
#>>>>> default sync module
############################################################################

function MAP_sync ($id, $file, $MAP_this)
{
	global $C_debug, $C_translate, $VAR;

	$message=''; 
	$remote_complete=false;
	$local_complete=false; 
	$count_exp=0;
	$count_upd=0;
	$count_imp=0;    

	// set limits for large databases: 
	$offset=-1;
	if(!empty($VAR['offset'])) $offset=$VAR['offset'];

	# get total number of accounts
	$db = &DB();
	$sql= 'SELECT count(*) as idx FROM '.AGILE_DB_PREFIX.'account WHERE site_id = '. DEFAULT_SITE .' AND status=1';
	$result = $db->Execute($sql);
	$total_accounts = $result->fields['idx'];
	if($total_accounts < $offset) $local_complete=true;

	# Loop through each account in the LOCAL DB to update / add the record to the REMOTE DB...
	if(!$local_complete)
	{
		$sql= 'SELECT id,username,email FROM '.AGILE_DB_PREFIX.'account WHERE site_id = '.DEFAULT_SITE.' AND status=1';
		$result = $db->SelectLimit($sql, $this->sync_limit, $offset);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_sync:1', $db->ErrorMsg());
			return;
		}

		# loop through local accounts
		while ( !$result->EOF ) {
			$account_id     = $result->fields['id'];
			$user           = $result->fields['username'];
			$email          = $result->fields['email'];

			# Check if an account with this username or e-mail exists in the REMOTE DB:
			$dbm      = new db_mapping;
			$dbx      = $dbm->DB_connect($id, 'false');
			eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($file) .';' );
			$sql      = "SELECT * FROM " . $db_prefix . $MAP_this->map['account_map_field'] . "
							 WHERE " .  $MAP_this->map['account_fields']['username']['map_field']. " = ".$dbx->qstr($user);
			$rs2 = $dbx->Execute($sql);
			if ($rs2 === false) {
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_sync:2', $dbx->ErrorMsg());
				return;
			}

			if($rs2->RecordCount() == 0) {
				# Account does not exist: ADD IT
				if(!eregi('admin', $user) && !eregi('administrator', $user)) {
					$MAP_this->account_add($account_id);
					$count_exp++;
				}
			} else {
				# Account exist: UPDATE IT - Skip if user is 'admin' or 'administrator'
				if(!eregi('admin', $user) && !eregi('administrator', $user)) {
					$MAP_this->account_edit($account_id, $user);
					$count_upd++;
				}
			}
			$result->MoveNext();
		}
	}

	# get total number of remote accounts
	$dbm = new db_mapping;
	$dbx = $dbm->DB_connect(false, $MAP_this->map['map']);
	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
	$sql    = "SELECT count(*) as idx FROM " . $db_prefix . $MAP_this->map['account_map_field'];
	$result = $dbx->Execute($sql);
	$total_accounts = $result->fields['idx'];
	if($total_accounts < $offset) $remote_complete=true;

	// loop through remote accounts
	if(!$remote_complete) {

		$sql    = "SELECT * FROM " . $db_prefix  . $MAP_this->map['account_map_field'];
		$result = $dbx->SelectLimit($sql, $this->sync_limit, $offset);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_sync:3', $dbx->ErrorMsg());
			return;
		} 
		while ( !$result->EOF )
		{
			$fld_account_id = $MAP_this->map['account_fields']['id']['map_field'];
			$fld_user       = $MAP_this->map['account_fields']['username']['map_field'];
			$fld_email      = $MAP_this->map['account_fields']['email']['map_field'];

			$account_id     = $result->fields[$fld_account_id];
			$user           = $result->fields[$fld_user];
			$email          = $result->fields[$fld_email];

			# Check if an account with this username or e-mail exists in the LOCAL DB:
			$db = &DB();
			$sql= 'SELECT id FROM '.AGILE_DB_PREFIX.'account WHERE 
				( username= '.$db->qstr($user).' OR email   = '.$db->qstr($email).' ) AND 
				site_id = '.DEFAULT_SITE .' AND status=1';
			$db2 = $db->Execute($sql);
			if ($db2 === false)  {
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_sync:4', $db->ErrorMsg());
				return;
			} 
			if($db2->RecordCount() == 0) {
				#  Account does not exist: ADD IT - Skip if user is 'admin' or 'administrator'
				if(!eregi('admin', $user) && !eregi('administrator', $user))
				$MAP_this->account_import($account_id);
				$count_imp++;
			}
			$result->MoveNext();
		}
	}

	$C_translate->value['db_mapping']['count_exp'] = $count_exp;
	$message .=  $C_translate->translate('exported','db_mapping', '');

	$C_translate->value['db_mapping']['count_upd'] = $count_upd;
	$message .= "<BR>". $C_translate->translate('updated','db_mapping', '');

	$C_translate->value['db_mapping']['count_imp'] = $count_imp;
	$message .= "<BR>". $C_translate->translate('imported','db_mapping', '');

	if($remote_complete && $local_complete) {
		$message .= "<br>Local and Remote Account Sync completed after processing $offset records.<br>";
	} else { 
		$offset+=$this->sync_limit;
		$url = "?_page=db_mapping:view&id=$id&do[]=db_mapping:sync&offset=$offset";
		$message .= "<br>Syncing Local and Remote Databases, please be patient as this screen will continue to refresh until all results have been processed. <BR> Total Records Processed: $offset<br>";
		$message .= "<a href=\"$url\">Click here if the page does not refresh</a>";
		$message .= "<script language=javascript>document.location='$url';</script>";
	}

	$C_debug->alert($message);
}




	############################################################################
	#>>>>> default Account Add module
	############################################################################

	function MAP_account_add( $account_id, $MAP_this )
	{
		### Get the local account details
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'account WHERE
				site_id = '.$db->qstr(DEFAULT_SITE).' AND
				id      = '.$db->qstr($account_id);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_add', $db->ErrorMsg());
			return;
		}

		### Create the insert statement
		$dbm    = new db_mapping;    		
		$db     = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    = "INSERT INTO " .
				$db_prefix . "" . $MAP_this->map['account_map_field'] . ' SET ';

		### Define the main fields
		$comma=false;
		reset ( $MAP_this->map['account_fields'] );
		while ( list ($key, $val) = each ( $MAP_this->map['account_fields'] ))
		{
			if ( $val['map_field'] && $key != 'id')
			{
				if($comma) $sql .= " , ";
				$sql .= $val['map_field'] . " = ". $db->qstr($result->fields[$key]);
				$comma = true;
			}
			elseif ( $val['map_field'] && $key == 'id')
			{
				if(isset($val['unique']) && $val['unique'] == '1')
				{
					$remote_account_id = $db->GenID($db_prefix . "" . $MAP_this->map['account_map_field'] . '_id');
					if($comma) $sql .= " , ";
					$sql .= $val['map_field'] . " = ". $db->qstr($remote_account_id);
					$comma = true;
				}
			}
		}

		### Define any custom fields
		for($i=0; $i<count($MAP_this->map['extra_field']); $i++)
		{
			if ( $MAP_this->map['extra_field'][$i]['add'] )
			{
				if($comma) $sql .= " , ";
				$value = $MAP_this->map['extra_field'][$i]['value'];

				# conversion
				if ($value == 'random|64')
				$value = rand(999,99999) .''. md5(microtime() . '-' . rand(999,9999));
				if ($value == 'random|32')
				$value = md5(microtime() . rand(999,99999));

				$sql .= $MAP_this->map['extra_field'][$i]['name'] . " = ".$db->qstr($value);
				$comma = true;
			}
		}
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_add', $db->ErrorMsg());
			return;
		}
		return true;
	}











	############################################################################
	#>>>>> default Account Edit method
	############################################################################

	function MAP_account_edit($account_id, $old_username, $MAP_this)
	{
		$db     = &DB();
		if( $old_username == '')
		{
			### Get the username:
			$sql    = 'SELECT username FROM ' . AGILE_DB_PREFIX . 'account WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr($account_id);
			$result = $db->Execute($sql);
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_edit(1)', $db->ErrorMsg());
				return;
			}

			if($result->RecordCount() > 0)
			{
				$old_username = $result->fields['username'];
			}
		}


		if (@$old_username == strtolower('admin')       ||
			@$old_username == strtolower('administrator'))
		return false;

		### Get the current account details from the local db
		$sql= "SELECT * FROM " . AGILE_DB_PREFIX . "account WHERE
			   id      = " . $db->qstr($account_id) . " AND
			   site_id = " . $db->qstr(DEFAULT_SITE);
		$result=$db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_edit(2)', $db->ErrorMsg());
			return;
		}

		### Create the sql update statement
		unset($db);
		$dbm    = new db_mapping;
		$db     = $dbm->DB_connect(false, $MAP_this->map['map']);  
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql    = "UPDATE " .
				  $db_prefix . "" . $MAP_this->map['account_map_field'] . ' SET ';


		### Define the main fields
		$comma=false;
		reset ( $MAP_this->map['account_fields'] );
		while ( list ($key, $val) = each ( $MAP_this->map['account_fields'] ))
		{
			if ( $val['map_field'] && $key != 'id')
			{
				if($comma) $sql .= " , ";
				$sql .= $val['map_field'] . " = ". $db->qstr($result->fields[$key]);
				$comma = true;
			}
		}

		### Define any custom fields
		for($i=0; $i<count($MAP_this->map['extra_field']); $i++)
		{
			if ( $MAP_this->map['extra_field'][$i]['edit'] )
			{
				if($comma) $sql .= " , ";
				$sql .= $MAP_this->map['extra_field'][$i]['name'] . " = ".
				$db->qstr($MAP_this->map['extra_field'][$i]['value']);
				$comma = true;
			}
		}

		### Update the account in the remote db
		$sql   .= " WHERE " . $MAP_this->map['account_fields']['username']['map_field'] .
		' = ' . $db->qstr($old_username);
		$result = $db->Execute($sql); 
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_edit(3)', $db->ErrorMsg());
			return;
		} 
	}




	############################################################################
	#>>>>> default Account Deletion method
	############################################################################

	function MAP_account_delete($account_id, $username, $MAP_this)
	{
		### Check if delete is allowed for this db map:
		if ( !isset($MAP_this->map['account_sync_field']['delete']) ||
			 !$MAP_this->map['account_sync_field']['delete'] )
			 return false;

		### Check if username is defined
		if ( $username == '')
		{
			### Get the username:
			$db     = &DB();
			$sql    = 'SELECT username FROM ' . AGILE_DB_PREFIX . 'account WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr($account_id);
			$result = $db->Execute($sql);

			### error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_delete', $db->ErrorMsg());
				return;
			}

			if($result->RecordCount > 0)
			{
				$username = $result->fields['username'];
			}
		}

		###################################################################
		### Get the remote account ID:
		$dbm    = new db_mapping;    		
		$db2    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    = "SELECT " . $MAP_this->map['account_fields']['id']['map_field'] . " FROM " .
				   $db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
				   $MAP_this->map['account_fields']['username']['map_field'] . " = " .
				   $db2->qstr($username);
		$result = $db2->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_delete', $db2->ErrorMsg());
			return;
		}

		$fld               = $MAP_this->map['account_fields']['id']['map_field'];
		$remote_account_id = $result->fields[$fld];

		####################################################################
		### Delete the remote account:       		        		
		$sql    = "DELETE FROM " .
				   $db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
				   $MAP_this->map['account_fields']['id']['map_field'] . " = " .
				   $db2->qstr($remote_account_id);
		$result = $db2->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_delete', $db->ErrorMsg());
			return;
		}

		####################################################################
		### Delete the remote groups:
		if ( $MAP_this->map['group_type'] == 'db' || $MAP_this->map['group_type'] == 'hardcode' )
		{   		        		
			$sql    = "DELETE FROM " .
					   $db_prefix . "" . $MAP_this->map['group_account_map']['table'] . ' WHERE ' .
					   $MAP_this->map['group_account_map']['account_id'] . " = " .
					   $db2->qstr($remote_account_id);
			$result =  $db2->Execute($sql);

			### error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_delete', $db->ErrorMsg());
				return;
			}

			return $remote_account_id;
		}
	}






	########################################################################
	#>>>>> default Account Import method
	########################################################################

	function MAP_account_import($remote_account_id, $MAP_this)
	{
		####################################################################
		### Get the remote account details:
		$dbm    = new db_mapping;    		
		$db2     = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    =   "SELECT * FROM " .
					$db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
					$MAP_this->map['account_fields']['id']['map_field'] . " = " .
					$db2->qstr($remote_account_id);
		$result = $db2->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_import', $db2->ErrorMsg());
			return;
		}

		if ($result->RecordCount() == 0) return false;

		####################################################################
		### get a unique account id:

		$db              = &DB();
		$account_id      = $db->GenID(AGILE_DB_PREFIX . 'account_id');

		$fld             = $MAP_this->map['account_fields']['username']['map_field'];
		@$username       = $result->fields[$fld];

		$fld             = $MAP_this->map['account_fields']['password']['map_field'];
		@$password       = $result->fields[$fld];

		if ( !$MAP_this->map['account_fields']['company']['map_field'] )
		{
			$company     = '';
		}
		else
		{
			$fld         = $MAP_this->map['account_fields']['company']['map_field'];
			@$company    = $result->fields[$fld];
		}

		$fld             = $MAP_this->map['account_fields']['email']['map_field'];
		@$email          = $result->fields[$fld];

		$fld             = $MAP_this->map['account_fields']['email_type']['map_field'];
		@$email_type     = $result->fields[$fld];

		$fld             = $MAP_this->map['account_fields']['date_last']['map_field'];
		@$date_last      = $result->fields[$fld];
		if ($date_last  <= 0) $date_last = time();

		$fld             = $MAP_this->map['account_fields']['date_orig']['map_field'];
		@$date_orig      = $result->fields[$fld];
		if ($date_orig  <= 0) $date_orig = time();


		if ( !$MAP_this->map['account_fields']['last_name']['map_field'] )
		{
			$status = '1';
		}
		else
		{
			$fld             = $MAP_this->map['account_fields']['status']['map_field'];
			@$status         = $result->fields[$fld];
			if ($status  != '0' && $status != '1') $status = '1';
		}


		$fld = $MAP_this->map['account_fields']['first_name']['map_field'];
		@$first_name     = $result->fields[$fld];
		@$name_arr       = explode(' ', $first_name);

		if ( !$MAP_this->map['account_fields']['last_name']['map_field'] )
		{
			if (count($name_arr) >= 3)
			{
				@$first_name  = $name_arr["0"];
				@$middle_name = $name_arr["1"];
				@$last_name   = $name_arr["2"];
			}
			elseif (count($name_arr) == 2)
			{
				@$first_name  = $name_arr["0"];
				$middle_name  = '';
				@$last_name   = $name_arr["1"];
			}
			else
			{
				$middle_name = '';
				$last_name   = '';
			}
		}
		else
		{
			$fld             = $MAP_this->map['account_fields']['middle_name']['map_field'];
			@$middle_name    = $result->fields[$fld];

			$fld             = $MAP_this->map['account_fields']['last_name']['map_field'];
			@$last_name      = $result->fields[$fld];
		}



		####################################################################
		### Create the sql update statement

		$sql    = "INSERT INTO ". AGILE_DB_PREFIX ."account SET
					id          = ".$db->qstr($account_id).",
					site_id     = ".$db->qstr(DEFAULT_SITE).",
					language_id = ".$db->qstr(DEFAULT_LANGUAGE).",
					affiliate_id= ".$db->qstr(DEFAULT_AFFILIATE).",
					reseller_id = ".$db->qstr(DEFAULT_RESELLER).",
					currency_id = ".$db->qstr(DEFAULT_CURRENCY).",
					theme_id    = ".$db->qstr(DEFAULT_THEME).",
					status      = ".$db->qstr($status).",
					date_orig   = ".$db->qstr($date_orig).",
					date_last   = ".$db->qstr($date_last).",
					username    = ".$db->qstr($username).",
					password    = ".$db->qstr($password).",
					first_name  = ".$db->qstr($first_name).",
					middle_name = ".$db->qstr($middle_name).",
					last_name   = ".$db->qstr($last_name).",
					company     = ".$db->qstr($company).",
					email       = ".$db->qstr($email).",
					email_type  = ".$db->qstr($email_type);
		$result = $db->Execute($sql);

		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_import', $db->ErrorMsg() . " --- ". $sql);
			return;
		}


		####################################################################
		### add the user to the default group:

		$group_id = $db->GenID(AGILE_DB_PREFIX . "" . 'account_id');
		$sql = '
			INSERT INTO ' . AGILE_DB_PREFIX . 'account_group SET
			id              = ' . $db->qstr ( $group_id ) . ',
			site_id         = ' . $db->qstr ( DEFAULT_SITE ) . ',
			date_orig       = ' . $db->qstr ( time() ) . ',
			group_id        = ' . $db->qstr ( DEFAULT_GROUP ) . ',
			account_id      = ' . $db->qstr ( $account_id ) . ',
			active          = ' . $db->qstr ('1');
		$db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_import', $db->ErrorMsg());
			return;
		}
	}


















	########################################################################
	#>>>>> default Account Import method ( FOR 'DB' based groups... )
	########################################################################

	function MAP_account_group_sync_db($account_id, $MAP_this)
	{ 
		### Get the local account details

		$db = &DB();
		$sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
				site_id = '.$db->qstr(DEFAULT_SITE).' AND
				id      = '.$db->qstr($account_id);
		$result = $db->Execute($sql); 
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db1', $db->ErrorMsg(). ' ---> ' . $sql);
			return;
		}

		$user   = $result->fields['username'];
		$email  = $result->fields['email'];

		### Get the remote account id:
		$dbm    = new db_mapping;    		
		$db2     = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    = "SELECT " .
					$MAP_this->map['account_fields']['id']['map_field']
					. " FROM " .
					$db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
					$MAP_this->map['account_fields']['username']['map_field'] . " = " .
					$db2->qstr($user);
		$result = $db2->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db2', $db2->ErrorMsg(). ' ---> ' . $sql);
			return;
		}

		$fld_remote_id     = $MAP_this->map['account_fields']['id']['map_field'];
		$remote_account_id = $result->fields[$fld_remote_id];

		### Delete all current groups for this account id: 
		if(!empty($MAP_this->map['group_account_map']['account_id'])) {
		$sql    = "DELETE FROM " .
					$db_prefix . "" . $MAP_this->map['group_account_map']['table']
					. ' WHERE ' .
					$MAP_this->map['group_account_map']['account_id'] . " = " .
					$db2->qstr($remote_account_id);
		$result = $db2->Execute($sql); 
		if ($result === false)
		{
			global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db3', $db2->ErrorMsg(). ' ---> ' . $sql);
			return;
		}
		}

		### Get the group_map array for this database map: 
		if(!isset($this->group_arr))
		{
			$db = &DB();
			$sql = "SELECT group_map FROM ".AGILE_DB_PREFIX."db_mapping WHERE
					map_file = ".$db->qstr($MAP_this->map['map'])." AND
					site_id  = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($sql);

			### error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db4', $db->ErrorMsg(). ' ---> ' . $sql);
				return;
			}
			@$MAP_this->group_arr = unserialize( $result->fields['group_map'] );
		}


		####################################################################
		### Determine the groups the selected account is authorize for:

		$db = &DB();
		$sql = "SELECT group_id,date_start,date_expire FROM ".
				AGILE_DB_PREFIX."account_group WHERE
				account_id  =  ".$db->qstr($account_id)." AND
				active      =  ".$db->qstr(1)." AND
				site_id     =  ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db4', $db->ErrorMsg(). ' ---> ' . $sql);
			return;
		}

		if($result->RecordCount() == 0) return;
		while( !$result->EOF )
		{
			$start  = $result->fields['date_start'];
			$expire = $result->fields['date_expire'];
			$group  = $result->fields['group_id'];

			### Group access started and not expired:
			if
				(($expire >= time() || $expire == '' || $expire == '0')
				&&
				($start <= time()   || $start == ''  || $start == '0'))
			{
				### Group is authorized:
				### Get the associated remote group(s) this account needs
				### to be added to:

				reset ($MAP_this->group_arr);
				while ( list ($key, $val) = each ($MAP_this->group_arr))
				{
					if ($key == $group)
					{
						### what remote group(s) is this group mapped to?
						while ( list ($remote_group, $add) = each ($val))
						{
							if ($add)
							{
								### create this group in the remote DB:
								$dbm    = new db_mapping;
								$db2    = $dbm->DB_connect(false, $MAP_this->map['map']);
								eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
								$sql = "INSERT INTO " .
									   $db_prefix . "" .
									   $MAP_this->map['group_account_map']['table'] . ' SET ' .
									   $MAP_this->map['group_account_map']['group_id'] . " = " .
									   $db2->qstr($remote_group) . ", " .
									   $MAP_this->map['group_account_map']['account_id'] . " = " .
									   $db2->qstr($remote_account_id);
								$group_result = $db2->Execute($sql); 
								if ($group_result === false)
								{
									global $C_debug;
									$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_db5', $db2->ErrorMsg(). ' ---> ' . $sql);
									return;
								}
							}
						}
					}
				}
			}
		  $result->MoveNext();
		}
		return $remote_account_id;
	}

















	########################################################################
	#>>>>> default Account Import method ( FOR 'DB' based groups... )
	########################################################################

	function MAP_account_group_sync_status($account_id, $MAP_this)
	{
		####################################################################
		### Get the local account details

		$db = &DB();
		$sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
				site_id = '.$db->qstr(DEFAULT_SITE).' AND
				id      = '.$db->qstr($account_id);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}

		$user   = $result->fields['username'];
		$email  = $result->fields['email'];



		####################################################################
		### Get the remote account id:

		$dbm    = new db_mapping;    		
		$db2     = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    = "SELECT " .
					$MAP_this->map['account_fields']['id']['map_field']
					. " FROM " .
					$db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
					$MAP_this->map['account_fields']['username']['map_field'] . " = " .
					$db2->qstr($user);
		$result = $db2->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db2->ErrorMsg());
			return;
		}

		$fld_remote_id     = $MAP_this->map['account_fields']['id']['map_field'];
		$remote_account_id = $result->fields[$fld_remote_id];


		####################################################################
		### Get the group_map array for this database map:

		if(!isset($this->group_arr))
		{
			$db = &DB();
			$sql = "SELECT group_map FROM ".AGILE_DB_PREFIX."db_mapping WHERE
					map_file = ".$db->qstr($MAP_this->map['map'])." AND
					site_id  = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($sql);

			### error reporting:
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
				return;
			}

			@$MAP_this->group_arr = unserialize( $result->fields['group_map'] );
		}


		####################################################################
		### Determine the groups the selected account is authorize for:

		$db = &DB();
		$sql = "SELECT group_id,date_start,date_expire FROM ".
				AGILE_DB_PREFIX."account_group WHERE
				account_id  =  ".$db->qstr($account_id)." AND
				active      =  ".$db->qstr(1)." AND
				site_id     =  ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}

		if($result->RecordCount() == 0) return;

		$MAP_this->status = 0;

		while( !$result->EOF )
		{
			$start  = $result->fields['date_start'];
			$expire = $result->fields['date_expire'];
			$group  = $result->fields['group_id'];

			### Group access started and not expired:
			if
				(($expire >= time() || $expire == '' || $expire == '0')
				&&
				($start <= time()   || $start == ''  || $start == '0'))
			{
				### Group is authorized:
				### Get the associated remote group(s) this account needs
				### to be added to:


				if(is_array($MAP_this->group_arr))
				{
					reset ($MAP_this->group_arr);
					while ( list ($key, $add) = each ($MAP_this->group_arr))
					{
						if ($key == $group)
						{
							if ($add != '' && gettype($add) != 'string')
							{
								if ( $MAP_this->status < $add )
								$MAP_this->status = $add;
							}
							else
							{
								$MAP_this->status = $add;
							}
						}
					}
				}
			}
		  $result->MoveNext();
		}

		### Update the remote account:
		$dbm    = new db_mapping;
		$db2     = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = "UPDATE " .
				$db_prefix . "" .
				$MAP_this->map['account_map_field'] . ' SET ' .
				$MAP_this->map['account_status_field'] . " = " .
				$db2->qstr($MAP_this->status) . " WHERE " .
				$MAP_this->map['account_fields']['id']['map_field'] . " = " .
				$db2->qstr($remote_account_id);
		$group_result = $db2->Execute($sql);

		### error reporting:
		if ($group_result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}
		return $remote_account_id;
	}









	########################################################################
	#>>>>> default Account Import method ( FOR 'DB' based groups... )
	########################################################################

	function MAP_account_group_sync_db_status($account_id, $MAP_this)
	{ 
		### Get the local account details 
		$db = &DB();
		$sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
				site_id = '.$db->qstr(DEFAULT_SITE).' AND
				id      = '.$db->qstr($account_id);
		$result = $db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}

		$user   = $result->fields['username'];
		$email  = $result->fields['email'];


		### Get the remote account id: 
		$dbm    = new db_mapping;    		
		$db2    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );        		        		
		$sql    = "SELECT " .
					$MAP_this->map['account_fields']['id']['map_field']
					. " FROM " .
					$db_prefix . "" . $MAP_this->map['account_map_field'] . ' WHERE ' .
					$MAP_this->map['account_fields']['username']['map_field'] . " = " .
					$db2->qstr($user);
		$result = $db2->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db2->ErrorMsg());
			return;
		}

		$fld_remote_id     = $MAP_this->map['account_fields']['id']['map_field'];
		$remote_account_id = $result->fields[$fld_remote_id];

		### Get the group_map array for this database map: 
		if(!isset($this->group_arr))
		{
			$db = &DB();
			$sql = "SELECT group_map,group_rank FROM ".AGILE_DB_PREFIX."db_mapping WHERE
					map_file = ".$db->qstr($MAP_this->map['map'])." AND
					site_id  = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($sql);
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
				return;
			}

			@$MAP_this->group_arr  = unserialize( $result->fields['group_map'] );
			@$MAP_this->group_rank = unserialize( $result->fields['group_rank']);	 
		} 

		### Determine the groups the selected account is authorize for: 
		$db = &DB();
		$sql = "SELECT group_id,date_start,date_expire FROM ".
				AGILE_DB_PREFIX."account_group WHERE
				account_id  =  ".$db->qstr($account_id)." AND
				active      =  ".$db->qstr(1)." AND
				site_id     =  ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}

		if($result->RecordCount() == 0) return;

		$MAP_this->status = 0;
		if($result->RecordCount() == 0) return;
		$rank = 0;
		while( !$result->EOF )
		{
			$start  = $result->fields['date_start'];
			$expire = $result->fields['date_expire'];
			$group  = $result->fields['group_id'];

			### Group access started and not expired:
			if
				(($expire >= time() || $expire == '' || $expire == '0')
				&&
				($start <= time()   || $start == ''  || $start == '0'))
			{
				### Group is authorized:
				### Get the associated remote group(s) this account needs
				### to be added to:

				if(!empty($MAP_this->group_arr) && is_array($MAP_this->group_arr))
				{
					reset ($MAP_this->group_arr); 
					foreach($MAP_this->group_arr as $key => $val) 
					{
						if ($key == $group)
						{
							### what remote group(s) is this group mapped to?
							foreach($val as $remote_group => $add)  { 
								if (!empty($add) && $MAP_this->group_rank[$key]['rank'] > $rank)
								{
									$MAP_this->status = $add;
									$rank = $MAP_this->group_rank[$key]['rank']; 
								} 
							}
						}
					}
				}
			}
		  $result->MoveNext();
		}

		### Update the remote account:
		$dbm    = new db_mapping;
		$db2    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = "UPDATE " .
				$db_prefix . "" .
				$MAP_this->map['account_map_field'] . ' SET ' .
				$MAP_this->map['account_status_field'] . " = " .
				$db2->qstr($MAP_this->status) . " WHERE " .
				$MAP_this->map['account_fields']['id']['map_field'] . " = " .
				$db2->qstr($remote_account_id);
		$group_result = $db2->Execute($sql); 
		if ($group_result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','MAP_account_group_sync_status', $db->ErrorMsg());
			return;
		}
		return $remote_account_id;
	}
}
?>