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
	
class htaccess
{

	# Open the constructor for this mod
	function htaccess()
	{       	
		# name of this module:
		$this->module = "htaccess";

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
	## LIST AUTH HTACCESS URLS  ##
	##############################

	function list_dirs($VAR)
	{	 
		global $smarty, $C_auth;
		$ii = 0;

		### Get a list of htaccess groups:
		$db     =  &DB();
		$sql    =  'SELECT id,group_avail
					FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1');
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{

			$smarty->assign('htaccess_display', false);
			return false;
		}

		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_avail']);
			$id   = $result->fields['id'];
			$this_show = false;

			for($i=0; $i<count($arr); $i++)
			{
				if($C_auth->auth_group_by_id($arr[$i]))
				{
					$this_show = true;
					$i=count($arr);
				}
			}

			if($this_show)
			{
				### Get each directory and add it to the array:
				$db     =  &DB();
				$sql    =  'SELECT *
							FROM ' . AGILE_DB_PREFIX . 'htaccess_dir WHERE
							site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
							htaccess_id = ' . $db->qstr($id) . ' AND
							status      = ' . $db->qstr('1');
				$result_dir = $db->Execute($sql);

				while(!$result_dir->EOF)
				{

					$arr_smarty[] = Array  (
							'id'            => $result_dir->fields['id'],
							'name'          => $result_dir->fields['name'],
							'description'   => $result_dir->fields['description'],
							'url'          => $result_dir->fields['url']
							);
					$ii++;
					$result_dir->MoveNext();
				}
			}
			$result->MoveNext();
		}



		if($ii == "0")
		{
			 $smarty->assign('htaccess_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('htaccess_display', 	true);
			$smarty->assign('htaccess_results', 	$arr_smarty);
			return true;
		}
	}





	##############################
	##  Smarty Authentication   ##
	##############################
	function check_smarty($VAR)
	{
		global $smarty, $C_translate;
		if($this->check_auth($VAR['_htaccess_id']) )
		{
			if(isset($VAR['_htaccess_dir_id']))
			{
				## Get the URL for this htaccess area:
				$db     = &DB();
				$sql    = 'SELECT url FROM ' . AGILE_DB_PREFIX . 'htaccess_dir WHERE
							site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
							id          = ' . $db->qstr($VAR['_htaccess_dir_id']);
				$result = $db->Execute($sql);
				if($result->RecordCount() > 0)
					$smarty->assign('htaccess_url', $result->fields['url']);
				$smarty->assign('htaccess_auth', "1");
				return true;
			}
		}

		$smarty->assign('htaccess_auth', "0");
		return false;
	}


	##############################
	##  Check Authentication    ##
	##############################
	function check_auth($id)
	{
		### Check if user is a member of one of the authorized groups:
		$db     = &DB();
		$sql    = 'SELECT status,group_avail FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($id);
		$result = $db->Execute($sql);

		if($result->RecordCount() > 0)
		{
			if ($result->fields['status'] != '1') return false;
			@$arr = unserialize($result->fields['group_avail']);
			global $C_auth;
			for($i=0; $i<count($arr); $i++)
				if($C_auth->auth_group_by_id($arr[$i]))  return true;

		}

		return false;
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$id = $db->add($VAR, $this, $type);

		if(isset($id) && $id > 0)
		{
			# Create the php index file for the Apache mod_auth_remote module:
			/*
			$GroupArray = '';
			for($i=0; $i<count($VAR['htaccess_group_avail']); $i++) 
			{
				if($i > 0) $GroupArray .= ',';
				$GroupArray .= $VAR['htaccess_group_avail'][$i]; 
			}

			$data = '<?php
$Status = '.@$VAR['htaccess_status'].';
$GroupArray = Array('.$GroupArray.');
if($Status != "1") { header(\'WWW-Authenticate: Basic realm="Failed"\'); header("HTTP/1.0 401 Unauthorized"); exit; }
include_once("../../../config.inc.php");
require_once(PATH_ADODB  . "adodb.inc.php");
require_once(PATH_CORE   . "database.inc.php");
require_once(PATH_MODULES. "htaccess/mod_auth_remote.inc.php"); 
?>';      		

			# add dir:
			$dir = PATH_FILES . 'htaccess_'. $id .'/'; 
			if(is_dir($dir))
			mkdir($dir, '755');

			$file = $dir . 'index.php';    			
			$fp = fopen($file, "w+");  
			fputs($fp, $data);  
			fclose($fp); 
			*/ 
		}   	
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
		$result = $db->update($VAR, $this, $type); 

		if($result)
		{
			$id = $VAR['htaccess_id'];

			# Update the php index file for the Apache mod_auth_remote module: 
			$GroupArray = '';
			for($i=0; $i<count($VAR['htaccess_group_avail']); $i++) 
			{
				if($i > 0) $GroupArray .= ',';
				$GroupArray .= $VAR['htaccess_group_avail'][$i]; 
			}


			$data = '<?php
$Status = '.@$VAR['htaccess_status'].';
$GroupArray = Array('.$GroupArray.');
if($Status != "1") { header(\'WWW-Authenticate: Basic realm="Failed"\'); header("HTTP/1.0 401 Unauthorized"); exit; }
include_once("../../../config.inc.php");
require_once(PATH_ADODB  . "adodb.inc.php");
require_once(PATH_CORE   . "database.inc.php");
require_once(PATH_MODULES. "htaccess/mod_auth_remote.inc.php"); 
?>';    		

			# add dir:
			$dir = PATH_FILES . 'htaccess_'. $id; 
			if(!is_dir($dir))    			
			mkdir($dir, '755');

			$file = PATH_FILES . 'htaccess_'. $id . '/index.php';    			
			$fp = fopen($file, "w+");  
			fputs($fp, $data);  
			fclose($fp);  
		}     		
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{
		global $C_debug, $C_translate;

		### Get the array
		if(isset($VAR["delete_id"]))
		$id = explode(',', $VAR["delete_id"]);
		elseif (isset($VAR["id"]))
		$id = explode(',', $VAR["id"]);

		### Load class for deleting sub-dirs.
		include_once ( PATH_MODULES .'htaccess_dir/htaccess_dir.inc.php' );
		$htdir = new htaccess_dir;

		### Loop:
		$db = &DB();
		for($i=0; $i<count($id); $i++)
		{
			if ( $id[$i] > 0 )
			{
				### Delete the htpasswd record:
				$sql = "DELETE FROM ".AGILE_DB_PREFIX."htaccess WHERE
						site_id = ".$db->qstr(DEFAULT_SITE)." AND
						id      = ".$db->qstr($id[$i]);
				$result = $db->Execute($sql);

				if ( $result )
				{
					### Delete .htaccess file(s) from the sub-directories
					$sql = "SELECT id FROM ".AGILE_DB_PREFIX."htaccess_dir WHERE
							site_id       = ".$db->qstr(DEFAULT_SITE)." AND
							htaccess_id   = ".$db->qstr($id[$i]);
					$result = $db->Execute($sql);
					if ($result->RecordCount() > 0 )            		
					  $htdir->delete_one($result->fields['id']);
				}
			}	

			### Delete the mod_auth_remote files:
			/*
			unlink(PATH_FILES.'htaccess_'. $id[$i] . '/index.php');		
			rmdir(PATH_FILES.'htaccess_'. $id[$i] );
			*/
		}		
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