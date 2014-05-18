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
	
class htaccess_dir
{

	# Open the constructor for this mod
	function htaccess_dir()
	{
		# name of this module:
		$this->module = "htaccess_dir";

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

		# add extra lines needed in the .htaccess files when added/updated:
		# Example: 
		#$this->htaccess_extra = "Options +FollowSymlinks\n"; 
		$this->htaccess_extra = '';
	}




	##############################
	##		ADD   		        ##
	##############################

	function add($VAR)
	{
		global $C_translate, $C_debug;
		$VAR['htaccess_dir_htaccess'] = '# Error!';
		$this->validated = true;

		### Change the path...
		if ( isset ( $VAR['htaccess_dir_path'] ) && $VAR['htaccess_dir_path'] != '' )
		{
			# trim whitspaces
			$VAR['htaccess_dir_path'] = trim ( $VAR['htaccess_dir_path'] );

			# replace all forward slashes with back slashes
			$VAR['htaccess_dir_path'] = preg_replace('/\\\\/', '/', $VAR['htaccess_dir_path']);

			# add the final trailing slash if missing
			if ( !preg_match('@[/]$@', $VAR['htaccess_dir_path'] ) )
			$VAR['htaccess_dir_path'] = $VAR['htaccess_dir_path'] . '/';
		}


		if( isset ( $VAR['htaccess_dir_path'] ) && $VAR['htaccess_dir_path'] != '' )
		{
			################################################################
			### VERIFY LOCAL PATH & WRITABILITY!

			@$filename  = $VAR['htaccess_dir_path']  . '.htaccess';
			@$id        = $VAR['htaccess_dir_htaccess_id'];
			$db         = &DB();
			$sql        = 'SELECT name FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($id);
			$result = $db->Execute($sql);
			@$name = $result->fields['name'];

			### Check path
			$path =  $VAR['htaccess_dir_path'];
			if ( is_dir ( $path ) )
			{
				### Check if is writable!
				if ( !is_writable ( $path ) )
				{
					## Path not writable!
					$this->validated = false;
					$this->val_error[] = array(
										'field' 		=> 'none',
										'field_trans' 	=> $C_translate->translate('error', 'core', ""),
										'error' 		=> $C_translate->translate('path_auth', 'htaccess_dir', ""));
				}
			}
			else
			{
				### Path broken!
				$this->validated = false;
				$this->val_error[] = array(
									'field' 		=> 'none',
									'field_trans' 	=> $C_translate->translate('error', 'core', ""),				
									'error' 		=> $C_translate->translate('path_broke', 'htaccess_dir', ""));
			}
		}



		####################################################################
		### If validation was failed, skip the db insert &
		### set the errors & origonal fields as Smarty objects,
		### and change the page to be loaded.
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
			return;
		}


		####################################################################
		### Create the record/verify fields

		$type 	= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 	= new CORE_database;
		$dir_id = $db->add($VAR, $this, $type);

		####################################################################
		### Create the .htaccess file

		if( isset ( $dir_id )  &&  $dir_id > 0 )
		{
			### GENERATE THE EXCLUDE LIST
			$exclude_list = $this->exclude_list();

			### GENERATE THE .HTACCESS FILE
			$nl   = "\n";
			$data = $this->htaccess_extra . 'RewriteEngine on'                          . $nl;
			if(empty($VAR['htaccess_dir_recursive']))
			$data .= 'RewriteRule   ^(.*)/.*$      -                  [L]' . $nl;
			$data .= 'RewriteRule ' . $exclude_list . '$ htaccess_index.php?_HTACCESS_ID='.$id.'&_HTACCESS_DIR_ID='.$dir_id;

			### Update the db record
			$db   = &DB();
			$sql  = "UPDATE ".AGILE_DB_PREFIX."htaccess_dir SET
				   htaccess = " . $db->qstr( $data ) . " WHERE
				   id       = " . $db->qstr( $dir_id ) . " AND
				   site_id  = " . $db->qstr( DEFAULT_SITE );
			$result = $db->Execute($sql);

			### WRITE THE LOCAL .HTACCESS FILE
			$fp = fopen($filename, "w+");
			fwrite($fp,$data);
			fclose($fp);

			### WRITE THE htaccess_index.php FILE
			$php_filename = $VAR['htaccess_dir_path'] . 'htaccess_index.php';
			$data = $this->create_php();
			$fp = fopen($php_filename, "w+");
			fwrite($fp,$data);
			fclose($fp);
		}
	}





	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		global $C_translate, $C_debug;
		$this->validated = true;

		### Change the path...
		if ( isset ( $VAR['htaccess_dir_path'] ) && $VAR['htaccess_dir_path'] != '' )
		{
			# trim whitspaces
			$VAR['htaccess_dir_path'] = trim ( $VAR['htaccess_dir_path'] );

			# replace all forward slashes with back slashes
			$VAR['htaccess_dir_path'] = preg_replace('/\\\\/', '/', $VAR['htaccess_dir_path']);

			# add the final trailing slash if missing
			if ( !preg_match('@[/]$@', $VAR['htaccess_dir_path'] ) )
			$VAR['htaccess_dir_path'] = $VAR['htaccess_dir_path'] . '/';
		}

		### Change the .htaccess data
		if( isset ( $VAR['htaccess_dir_path'] ) && $VAR['htaccess_dir_path'] != '' )
		{

			################################################################
			### VERIFY LOCAL PATH & WRITABILITY!

			@$filename  	= $VAR['htaccess_dir_path'] . '.htaccess';
			@$php_filename 	= $VAR['htaccess_dir_path'] . 'htaccess_index.php';
			@$id        	= $VAR['htaccess_dir_htaccess_id'];
			$db         	= &DB();
			$sql        	= 'SELECT name FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
							site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
							id          = ' . $db->qstr($id);
			$result = $db->Execute($sql);
			@$name = $result->fields['name'];


			### Check path
			$path =  $VAR['htaccess_dir_path'];
			if ( is_dir ( $path ) )
			{
				### Check if is writable!
				if ( is_writable ( $path ) )
				{
					### GENERATE THE EXCLUDE LIST
					$exclude_list = $this->exclude_list();
					$nl   = "\n";

					/*
					$data = $this->htaccess_extra .
							'RewriteEngine on'                          . $nl .
							'RewriteRule   ^(.*)/.*$      -                  [L]' . $nl .
							'RewriteRule '                              .
							'' . $exclude_list . '$ '           .
							'htaccess_index.php'                              .
							'?_HTACCESS_ID='.$id.'&_HTACCESS_DIR_ID='.$VAR["htaccess_dir_id"];
					*/

					$data = $this->htaccess_extra . 'RewriteEngine on'                          . $nl;
					if(empty($VAR['htaccess_dir_recursive']))
					$data .= 'RewriteRule   ^(.*)/.*$      -                  [L]' . $nl;
					$data .= 'RewriteRule ' . $exclude_list . '$ htaccess_index.php?_HTACCESS_ID='.$id.'&_HTACCESS_DIR_ID='.$VAR["htaccess_dir_id"];


					### Set the .htaccess var for the db
					$VAR['htaccess_dir_htaccess'] = $data;
				}
				else
				{
					## Path not writable!
					$this->validated = false;
					$this->val_error[] = array(
										'field' 		=> 'none',
										'field_trans' 	=> $C_translate->translate('error', 'core', ""),
										'error' 		=> $C_translate->translate('path_auth', 'htaccess_dir', ""));
				}
			}
			else
			{
				### Path broken!
				$this->validated = false;
				$this->val_error[] = array(
									'field' 		=> 'none',
									'field_trans' 	=> $C_translate->translate('error', 'core', ""),				
									'error' 		=> $C_translate->translate('path_broke', 'htaccess_dir', ""));
			}
		}

		####################################################################
		### If validation was failed, skip the db insert &
		### set the errors & origonal fields as Smarty objects,
		### and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;	

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $this->val_error);	

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
				define('FORCE_PAGE', $VAR['_page']);
			}
			return;
		}

		### Update the db record
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$dir = $db->update($VAR, $this, $type);

		if($dir != false)
		{
			### UPDATE THE LOCAL .HTACCESS FILE
			$fp = fopen($filename, "w+");
			fwrite($fp,$data);
			fclose($fp);

			### UPDATE THE LOCAL htaccess_index.php
			$data = $this->create_php();
			$fp = fopen($php_filename, "w+");
			fwrite($fp,$data);
			fclose($fp);
		}
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		### Get the array
		if(isset($VAR["delete_id"]))
		$id = explode(',', $VAR["delete_id"]);
		elseif (isset($VAR["id"]))
		$id = explode(',', $VAR["id"]);

		### Loop:
		for($i=0; $i<count($id); $i++)
		{
			### Delete the protection
			$this->delete_one($id[$i]);
		}
	}

	##############################
	##		 DELETE	ONE         ##
	##############################
	function delete_one($id)
	{	
		global $C_debug, $C_translate;

		if ($id == '') return false;

		### Get the details of this directory record
		$db  = &DB();
		$sql = "SELECT * FROM ".AGILE_DB_PREFIX."htaccess_dir WHERE
				site_id = ".$db->qstr(DEFAULT_SITE)." AND
				id      = ".$db->qstr($id);
		$result = $db->Execute($sql);
		$type = $result->fields['type'];
		$path = $result->fields['path'];

		if( $result != false )
		{    	
			### DELETE THE LOCAL .HTACCESS FILE
			$filename = $result->fields['path'] . '.htaccess';
			if ( @unlink ($filename) === false)
			{
				$C_translate->value['htaccess_dir']['dir'] = $result->fields['path'] . '.htaccess';
				$C_debug->alert($C_translate->translate('remove_fail','htaccess_dir',''));
			}

			### DELETE THE LOCAL HTACCESS_ATILE.PHP FILE
			$filename = $result->fields['path'] . 'htaccess_index.php';
			@unlink ($filename);
		}

		### Delete the Record:
		$db = &DB();
		$sql = "DELETE FROM ".AGILE_DB_PREFIX."htaccess_dir WHERE
				site_id = ".$db->qstr(DEFAULT_SITE)." AND
				id      = ".$db->qstr($id);
		$resulta = $db->Execute($sql);

		### Success message
		$C_translate->value['htaccess_dir']['dira'] = $path;
		$C_debug->alert($C_translate->translate('remove_success','htaccess_dir',''));               		

		return true;
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

	function exclude_list()
	{
		global $VAR;

		$list   = '';
		@$Arr    = $VAR['htaccess_dir_exclude'];
		if ( count($Arr) == 0) return '';

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'htaccess_exclude WHERE
				   site_id     = ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0) return '';
		while(!$result->EOF)
		{
			$id = $result->fields['id'];
			$ext= $result->fields['extension'];

			### GENERATE THE EXCLUDE LIST
			for ($i=0; $i<count($Arr); $i++)
			{
				if ($id == $Arr[$i])
				{
					if ( $list == '')
					$list = $ext;
					else
					$list.= '|'.$ext;
				}
			}        	
			$result->MoveNext();
		}

		if ($list != '') $list = '!(\.+' . $list . ')';
		return $list;    	
	}



	function create_php()
	{
		$data = '<?php
define ( "INDEX_FILE",  "index.html" );
require_once ("' . PATH_AGILE . 'htaccess_index.php"); ?>';
		return $data;
	}                	
}
?>