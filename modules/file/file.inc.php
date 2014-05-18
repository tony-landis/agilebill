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
	
class file
{

	# Open the constructor for this mod
	function file()
	{       	
		# name of this module:
		$this->module = "file";

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
	##  LIST AUTH FILES         ##
	##############################

	function file_list($VAR)
	{	
		global $smarty;
		if(!isset($VAR['id']))
		{
			global $C_debug;
			$smarty->assign('file_display', false);
			return false;
		}

		### Check if user is auth for the selected category:
		$db     =  &DB();
		$sql    =  'SELECT *
					FROM ' . AGILE_DB_PREFIX . 'file WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					file_category_id = ' . $db->qstr($VAR['id']) . ' AND
					status      = ' . $db->qstr('1') .'
					ORDER BY sort_order,date_orig,name';
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{ 
			$smarty->assign('file_display', false);
			return false;
		} 

		global $C_auth;
		$ii = 0;

		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_avail']);
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

			   $start = $result->fields['date_start'];
			   $expire= $result->fields['date_expire'];

				### Check that it is not expired
				if (( $start == "0"  || $start <= time()+2  ) &&
				   ( $expire == "0"  || $expire >= time() )  )
				{
					$arr_smarty[] = Array  (
							'id'            => $result->fields['id'],
							'name'          => $result->fields['name'],
							'description'   => $result->fields['description'],
							'size'          => $result->fields['type'],
							'size'          => $result->fields['size']
							);
					$ii++;
				}
			}
			$result->MoveNext();
		}


		if($ii == "0")
		{
			 $smarty->assign('file_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('file_display', 	true);
			$smarty->assign('file_results', 	$arr_smarty);
			return true;
		}
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
		$sql    = 'SELECT id,name,group_avail FROM ' . AGILE_DB_PREFIX . 'file_category WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1') .'
					ORDER BY sort_order,name';
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			$smarty->assign('file_category_display', false);
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
											'id'            => $result->fields['id']);
					$i=count($arr);
				}
			}
			$result->MoveNext();
		}

		if($ii == "0")
		{
			 $smarty->assign('file_category_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('file_category_display', 	true);
			$smarty->assign('file_category_results', 	$arr_smarty);
			return true;
		}
	}




	##############################
	##		DOWNLOAD            ##
	##############################
	function download($VAR)
	{
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'file WHERE
					site_id     = ' . DEFAULT_SITE . ' AND
					id          = ' . $db->qstr(@$VAR['id']) . ' AND
					status      = 1';
		$result = $db->Execute($sql);

		if($result->RecordCount() == 1)
		{
			$show = true;

			### Validate start date
			$s = $result->fields['date_start']; 
			if($s != '' && $s != 0)
				if($s > time())
					$show = false; 

			### Validate expire date	
			$e = $result->fields['date_expire'];
			if($e != '' && $e != 0)
				if($e < time())
					$show = false;           					

			### Validate user group: 
			if($show) {
				global $C_auth;
				@$arr = unserialize($result->fields['group_avail']);
				$show = false; 
				for($i=0; $i<count($arr); $i++) {
					if($C_auth->auth_group_by_id($arr[$i]))  {
						$show = true;		
						break;
					}
				}                
			} 

			### Get the filetype
			if($show) 
			{
				$ft = $result->fields['location_type'];
				if($ft == 0)
					$file = PATH_FILES . 'file_'.$VAR['id'].'.dat';
				elseif ($ft == 1)
					$file = $result->fields['location'];
				elseif ($ft == 2)
					$file = $result->fields['location'];

				### Open the file
				if (@$file=fopen($file, 'r'))
				{  
					### Display the correct headers:
					header ("Content-Type: " . $result->fields['type']);
					header ("Content-Size: " . $result->fields['size']);
					header ("Content-Disposition: inline; filename=" . $result->fields['name']);         	            	
					fpassthru($file);
					exit;                                     
				}
			}
		}
		echo 'Sorry, the file does not exist or you are not authorized or your access has expired!';
	}



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		global $_FILES, $smarty, $C_debug, $C_translate;

		if($VAR['file_location_type'] == '')   return false; 

		$lt = $VAR['file_location_type'];

		// UPLOADED FILE FROM LOCAL PC
		if($lt == 0) { 
			### Validate the file upoad:
			if(!isset($_FILES['upload_file']) || $_FILES['upload_file']['size'] <= 0)
			{
				global $C_debug;
				$C_debug->alert('You must go back and enter a file for upload!');
				return;
			} 
			$VAR['file_size']   = $_FILES['upload_file']['size']; 
			$VAR['file_type']   = $_FILES['upload_file']['type']; 
			$VAR['file_name']   = $_FILES['upload_file']['name'];
		}

		// ENTERED URL TO FILE
		elseif ($lt == 1) {
			### Validate the remote file can be opened and is greater than 0K
			$file = $VAR['url_file'];
			if(empty($file) || !$fp = fopen ($file, "r")) {
				# error 
				$C_debug->alert( $C_translate->translate('remote_file_err','file','') );
				return;
			} else  {  
				$VAR['file_location'] = $file; 
				$fn = explode("/", $file);  
				$count = count($fn)-1;
				$VAR['file_name'] = $fn[$count]; 
				$headers = stream_get_meta_data($fp); 
				$headers = $headers['wrapper_data']; 
				for($i=0;$i<count($headers); $i++) {
					if(preg_match('/^Content-Type:/i', $headers[$i]))
						$VAR['file_type'] = preg_replace('/Content-Type: /i', '', $headers[$i]);
					elseif(preg_match('/^Content-Length:/i', $headers[$i]))
						$VAR['file_size'] = preg_replace('/Content-Length: /i', '', $headers[$i]);
				}  
			}
		}


		// ENTERED LOCAL FILE
		elseif ($lt == 2)
		{
			@$file = $VAR['local_file'];
			if(is_file($file) && is_readable($file))
			{
				if(preg_match("@/@", $file))
					$fn = explode("/", $file); 
				else if(preg_match("@\\@", $file))
					$fn = explode("\\", $file);
				else 
					$fn[0] = $file;

				$count = count($fn)-1;
				$VAR['file_name'] = $fn[$count]; 
				$VAR['file_size'] = filesize($file);
				$VAR['file_location'] = $file; 

				include_once(PATH_CORE . 'file_extensions.inc.php');
				$ext = new file_extensions;	            	
				$VAR['file_type'] = $ext->content_type($file);
			} 
			else
			{
				$C_debug->alert( $C_translate->translate('local_file_err','file','') );
				return;
			}            		            	
		} 
		else { return false; }

		### Create the record      
		$type = "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db = new CORE_database;
		$id = $db->add($VAR, $this, $type);

		### Copy the uploaded file, or exit if fail:
		if($lt == 0) {
			if(isset($id) && $id > 0) {
				if(!copy($_FILES['upload_file']['tmp_name'], PATH_FILES . 'file_'.$id.'.dat')) {
					$C_debug->alert( $C_translate->translate('copy_file_err','file','') );
				}
			}
			unlink($_FILES['upload_file']['tmp_name']);
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
		$db->update($VAR, $this, $type);
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$db = &DB();
		$id = $this->table . '_id';

		# generate the list of ID's
		$id_list = '';
		$ii=0;

		if(isset($VAR["delete_id"]))
		{
			$id = explode(',',$VAR["delete_id"]);
		}
		elseif (isset($VAR["id"]))
		{
			$id = explode(',',$VAR["id"]);
		}

		for($i=0; $i<count($id); $i++)
		{
			if($id[$i] != '')
			{
				if($i == 0)
				{			
					$id_list .= " id = " . $db->qstr($id[$i]) . " ";
					$ii++;
				}
				else
				{
					$id_list .= " OR id = " . $db->qstr($id[$i]) . " ";
					$ii++;
				}	
			}					
		}

		if($ii>0)
		{
			# generate the full query
			$q = "DELETE FROM
					".AGILE_DB_PREFIX."$this->table
					WHERE
					$id_list
					AND
					site_id = " . DEFAULT_SITE; 
			$result = $db->Execute($q);

			# error reporting
			if ($result === false) {
				global $C_debug;
				$C_debug->error('file.inc.php','delete', $db->ErrorMsg());                   	        	
			} else { 
				for($i=0; $i<count($id); $i++) {
					if($id[$i] != '') {
						error_reporting(0);
						unlink(PATH_FILES . 'file_'.$id[$i].'.dat'); 
						$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
						eval($error_reporting_eval);                                    			                		 	
					}					
				} 

				# Alert delete message
				global $C_debug, $C_translate;
				$C_translate->value["CORE"]["module_name"] = $C_translate->translate('name',$this->module,"");
				$message = $C_translate->translate('alert_delete_ids',"CORE","");
				$C_debug->alert($message);	

			}	
		}	
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