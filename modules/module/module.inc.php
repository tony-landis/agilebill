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
	
class module
{

	# Open the constructor for this mod
	function module()
	{
		# name of this module:
		$this->module = "module";

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

		# core modules for installation/deletion:
		$this->core_mods= Array(  'account',
								  'account_admin',
								  'account_group',
								  'backup',
								  'blocked_email',
								  'blocked_ip',
								  'country',
								  'currency',
								  'email_template',
								  'email_template_translate',
								  'group',
								  'group_method',
								  'login_lock',
								  'login_log',
								  'log_error',
								  'module',
								  'module_method',
								  'newsletter',
								  'newsletter_subscriber',
								  'session',
								  'session_auth_inc',
								  'setup',
								  'setup_email',
								  'staff',
								  'staff_department',
								  'static_relation',
								  'static_var',
								  'static_var_record',
								  'task',
								  'temporary_data'  );


		$this->dev_inst_excl = Array(   'module',
										'module_method',
										'group_method',
										'backup',
										'login_log',
										'session',
										'weblog',
										'temporary_data',
										'setup',
										'session_auth_cache');
	}


	### Send php/mysql/server/license details and check versions
	function remote_version_check($VAR) {     
		global $C_auth;
		if(!$C_auth->auth_method_by_name("module","upgrade")) return false;

		if(is_file(PATH_AGILE.'Version.txt'))
		$f['version']=trim(file_get_contents(PATH_AGILE.'Version.txt'));
		else
		$f['version']='SVN';

		$f['license']=LICENSE_KEY;
		$f['php']=phpversion();
		$f['mysql']=mysql_get_client_info();
		$f['os']=$_ENV['OS'];
		$f['proc']=$_ENV['PROCESSOR_ARCHITECTURE'];
		$f['arch']=$_ENV['PROCESSOR_ARCHITEW6432'];
		$f['server']=$_SERVER["SERVER_SOFTWARE"]; 
		global $smarty;
		$smarty->assign('send', $f);
	} 

	### Get remote hash file and check for inconsitancys in local files
	function remote_update($VAR)
	{
		$ver = $VAR['ver']; 				
		$mis=0;
		$md5=0;
		$i=0;
		$msg = '';

		# Get the core modules & compare  
		if(defined('DEMO_VERSION'))
			$url_core = 'http://agileco.com/downloads/trial/'.$ver.'.hash.txt';
		else 
			$url_core = 'http://agileco.com/downloads/commercial/'.$ver.'.hash.txt';

		@$data = file_get_contents($url_core);
		if(empty($data)) {
			$msg .= 'Failed to retrieve MD5 Hash file at http://agileco.com/downloads/commercial/'.$ver.'.hash.txt...<BR>';
		} else {  
			$arr = explode("|",$data); 
			foreach($arr as $arx)
			{
				$rx = explode(',',$arx);
				@$ar['name'] = $rx[1];
				@$ar['md5'] = $rx[0]; 
				if(!empty($ar['name']) && !empty($ar['md5']) && 
					!preg_match("@^install/@", $ar['name']) &&
					!preg_match("/^test.php/", $ar['name']))
				{
					if(!is_file(PATH_AGILE.$ar["name"]))
					{
						$core_mis[] = $ar["name"];
						$mis++;
					} 
					elseif(md5_file(PATH_AGILE.$ar["name"]) != $ar["md5"])
					{ 
						$core_md5[] =  $ar["name"];
						$md5++;
					}
				}
				$i++;
			}      
			$smart[] = Array ('name' => 'Core', 'md5' => @$core_md5, 'mis' => @$core_mis );
		}


		### Get each optional module && compare	
		if(!defined('DEMO_VERSION'))
		{	 
			@$modules = $VAR["module"]; 
			foreach($modules as $module)
			{
				$data = '';
				@$data = file_get_contents('http://agileco.com/downloads/commercial/'.$module.'.hash.txt');
				if(empty($data)) {
					$msg .= 'Failed to retrieve MD5 Hash file at http://agileco.com/downloads/commercial/'.$module.'.hash.txt...<BR>';
				} else {  
					$arr = explode("|",$data); 
					foreach($arr as $arx)
					{
						$rx = explode(',',$arx);
						@$ar['name'] = $rx[1];
						@$ar['md5'] = $rx[0];

						# check if file exists locally...
						if(!empty($ar['name']) && !empty($ar['md5']) ) 
						{
							if(!is_file(PATH_AGILE.$ar["name"]))
							{
								$f_mis[] = $ar["name"];
								$mis++;
							} 
							elseif(md5_file(PATH_AGILE.$ar["name"]) != $ar["md5"])
							{ 
								$f_md5[] =  $ar["name"];
								$md5++;
							}
						}
						$i++;
					}

					$smart[] = Array ('name' => $module, 'md5' => @$f_md5, 'mis' => @$f_mis );
					unset($f_mis);
					unset($f_md5);
				}		
			}
		} 

		global $smarty;
		$smarty->assign('modules', $smart);	 
		$smarty->assign('md5', $md5);
		$smarty->assign('mis', $mis);

		if(!empty($msg)) {
			global $C_debug;
			$C_debug->alert($msg);
		}
	}  



	##############################
	##		TRANSLATE           ##
	##############################
	function translate($VAR)
	{
		if(!isset($VAR['translate_language']) || !isset($VAR['translate_module']))
		{
			echo "error!";
			return;
		}

		if($VAR['translate_module'] == "")
		{
			echo "error!";
			return;
		}

		# Get the default language file:
		if(!$file = fopen(PATH_LANGUAGE . '' .
					$VAR["translate_module"] .
					"/english_" . $VAR["translate_module"] .
					".xml", "r"))
		{
			echo 'Unable to open base translation!';
		}
		else
		{
			$systran_text='';
			while(!feof($file))
			{
				$systran_text .= fgetc($file);
			}
		}
		fclose($file);


		for($i=0; $i<count($VAR['translate_language']); $i++)
		{
			if(isset($VAR['translate_language'][$i]) && $VAR['translate_language'][$i] != "")
			{
			   $systran_lp      = $VAR['translate_language'][$i];
			   $language   		= $VAR['translate_lang'][$systran_lp];

			   ### Get the translation from systran:
			   $language_xml = trim($this->systran($systran_text,$systran_lp));

			   # write the language packs
			   $file = fopen(PATH_LANGUAGE . '' . $VAR["translate_module"] . "/".$language."_" . $VAR["translate_module"] . ".xml", "w+");
			   fputs($file, $language_xml);
			   fclose($file);
			}
		}
	} 


	##############################
	##	SYSTRAN TRANSLATION     ##
	##############################
	function systran($text, $lang)
	{
		$systran_id      = "SystranSoft-en";
		$systran_charset = "ISO-8859-1";

		$host	=	'systranbox.com';
		$form	=	'/systran/box';
		$pass	=	array(
					 'systran_id'        =>  $systran_id,
					 'systran_charset'   =>  $systran_charset,
					 'systran_lp'        =>  $lang,
					 'systran_text'      =>  $text
					);

		// CREATE THE RECORD
		require_once(PATH_CORE  . 'post.inc.php');
		$post= new CORE_post;
		$result = $post->post_data($host, $form, $pass);
		$pat = "\n";
		$arr = explode($pat, $result);

		$ret='';
		for($i=0; $i<count($arr); $i++)
		   if($i>5) $ret.= $arr[$i];

		return $ret;
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
		# set the core modules:
		$core = $this->core_mods;

		if(isset($VAR["delete_id"]))
			$id = explode(',',$VAR["delete_id"]);
		elseif (isset($VAR["id"]))
			$id = explode(',',$VAR["id"]);

		for($i=0; $i<count($id); $i++)
		{
			if($id[$i] != '')
			{
				# get the module id
				$module_id = $id[$i];

				# is this module part of the core?
				$db = &DB();
				$q  = "SELECT name FROM ".AGILE_DB_PREFIX."module WHERE
						id      = ".$db->qstr($module_id)." AND
						site_id = ".$db->qstr(DEFAULT_SITE);
				$result = $db->Execute($q);
				$module_name = $result->fields['name'];

				# loop through the core array and see if this module is part of the core
				for($i=0; $i<count($core); $i++)
				{
					if($core[$i] == $module_name)
					{
						# alert message translated
						echo "This module is part of the core - it cannot be uninstalled!";
						return;
					}
				}

				# get each each group_method for this module & delete it
				$q  = "SELECT id FROM ".AGILE_DB_PREFIX."module_method WHERE
						module_id = ".$db->qstr($module_id)." AND
						site_id = ".$db->qstr(DEFAULT_SITE);
				$result = $db->Execute($q);

				while(!$result->EOF)
				{
					# delete the group methods...
					$q  = "DELETE FROM ".AGILE_DB_PREFIX."group_method WHERE
							module_id = ".$db->qstr($module_id)." OR
							method_id = ".$db->qstr($result->fields['id'])." AND
							site_id = ".$db->qstr(DEFAULT_SITE);
					$db->Execute($q);
					$result->MoveNext();
				}

				# delete each module_method
				$db = &DB();
				$q  = "DELETE FROM ".AGILE_DB_PREFIX."module_method WHERE
						module_id = ".$db->qstr($module_id)." AND
						site_id = ".$db->qstr(DEFAULT_SITE);
				$db->Execute($q);

				# delete the module record
				$db = &DB();
				$q  = "DELETE FROM ".AGILE_DB_PREFIX."module WHERE
					   id = ".$db->qstr($module_id)." AND
					   site_id = ".$db->qstr(DEFAULT_SITE);
				$db->Execute($q);


				# drop the associated database for this module
				### Load the construct XML file to get the table name...
				$C_xml = new CORE_xml;
				$xml_construct = PATH_MODULES . "" . $module_name . "/" . $module_name . "_construct.xml";
				$construct = $C_xml->xml_to_array($xml_construct);	


				### Check that this Module has any db installation required...
				if(isset($construct["construct"]["table"]))
				{
					### Create the module DB table
					$table = $construct["construct"]["table"];
					$db = &DB();
					$dict = NewDataDictionary($db);
					$sql = $dict->DropTableSQL(AGILE_DB_PREFIX.''.$table);
					$db->Execute($sql[0]);

					$table = $construct["construct"]["table"].'_id';
					$sql = $dict->DropTableSQL(AGILE_DB_PREFIX.''.$table);
					$db->Execute($sql[0]);
				}
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



	###################################
	##	INSTALL ERROR CHECKING: MODULE ##
	###################################
	function install_error_check($VAR)
	{
		global $smarty, $C_translate;

		###########################################
		### Check that the module name is defined:
		if(!isset($VAR["install_name"]))
		{
			 $error[] = $C_translate->translate('install_enter_name','module','');
		}
		else if ($VAR["install_name"] == '')
		{
			 $error[] = $C_translate->translate('install_enter_name','module','');
		}
		$module = trim($VAR["install_name"]);


		###########################################
		### Check that at least one group is defined:
		if(!isset($VAR["module_group"]))
			 $error[] = $C_translate->translate('install_select_group','module','');


		###########################################
		### Check if the module already exists in the Database:
		$db = &DB();
		$q  = 'SELECT name FROM '.AGILE_DB_PREFIX.'module WHERE
				name    = '.$db->qstr($module).' AND
				site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);
		if($result->RecordCount() > 0)
			$error[] = $C_translate->translate('install_module_exists','module','');

		#######################################################
		### Check if the module exists in the file structure:
		if (!is_dir(PATH_MODULES . '' . $module))
			$error[] = $C_translate->translate('install_missing_dir','module','');

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '.inc.php'))
			$error[] = $C_translate->translate('install_missing_class','module','');

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '_construct.xml'))
			$error[] = $C_translate->translate('install_missing_construct','module','');

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '_install.xml'))
			$error[] = $C_translate->translate('install_missing_install','module','');

		if(isset($error))
		{
			$error[] = $C_translate->translate('install_failed','module','');

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $error);	
			return false;
		}


		###########################################
		### Load the install XML file...
		$xml_construct = PATH_MODULES . "" . $module . "/" . $module . "_install.xml";
		$C_xml = new CORE_xml;
		$install = $C_xml->xml_to_array($xml_construct);
		$this->install = $install;

		/*
		echo "<pre>";
		print_r($install);
		echo "</pre>";
		*/

		###########################################
		### Get the module properties:
		$name           = $install["install"]["module_properties"]["name"];

		if(isset($install["install"]["module_properties"]["parent"]))
		$parent         = $install["install"]["module_properties"]["parent"];
		else
		$parent         = 0;


		############################################                                          		
		### Get dependancies.... 
		if(isset($install["install"]["module_properties"]["dependancy"]))
			$dependancy     = $install["install"]["module_properties"]["dependancy"];
		else
			$dependancy     = false;

		if($dependancy)
		{
			if(preg_match('/,/', $dependancy))
				$depend = explode(',', $dependancy);
			else
				$depend[0] = $dependancy;

			###################################################
			### Check to be sure the dependancies are installed:

			for($i=0; $i < count($depend); $i++)
			{
				$db = &DB();
				$q  = 'SELECT name FROM '.AGILE_DB_PREFIX.'module WHERE
						name    = '.$db->qstr($depend["$i"]).' AND
						status  = '.$db->qstr("1").' AND
						site_id = '.$db->qstr(DEFAULT_SITE);
				$result = $db->Execute($q);
				if($result->RecordCount() == 0)
					$error[] = $C_translate->translate('install_module_depend','module','depend='.$depend["$i"]);
			}
		}

		# check for error:
		if(isset($error))
		{
			$error[] = $C_translate->translate('install_failed','module','');

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $error);	
			return false;
		}

		return true;
	}



	#####################################
	##	INSTALL ERROR CHECKING: MODULE ##
	#####################################
	function install_error_check_sub($module)
	{	
		global $smarty;

		if ($module == '')
		{
			return true;
		}

		########################################################
		### Check if the module already exists in the Database:
		$db = &DB();
		$q  = 'SELECT name FROM '.AGILE_DB_PREFIX.'module WHERE
				name    = '.$db->qstr($module).' AND
				site_id = '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);
		if($result->RecordCount() > 0)
			$error[] = 'This module already exists in the database!';

		######################################################
		### Check if the module exists in the file structure:
		if (!is_dir(PATH_MODULES . '' . $module))
			$error[] = 'The specified module <b>directory</b> does not exist!';

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '.inc.php'))
			$error[] = 'The specified module <b>class file</b> does not exist!';

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '_construct.xml'))
			$error[] = 'The specified module <b>construct file</b> does not exist!';

		if (!file_exists(PATH_MODULES . '' . $module . '/' . $module . '_install.xml'))
			$error[] = 'The specified module <b>installation file</b> does not exist!';

		if(isset($error))
		{
			$error[] = '<B>Module Installation Failed</B>';

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $error);	
			return false;
		}

		return true;
	}


	###################################
	##	     INSTALL ERROR CHECKING  ##
	###################################
	function install_sql($module)
	{
		global $VAR, $smarty;

		###########################################
		### Load the install XML file...
		$C_xml = new CORE_xml;
		$xml_install = PATH_MODULES . "" . $module . "/" . $module . "_install.xml";			
		$install = $C_xml->xml_to_array($xml_install);

		###########################################
		### Load the construct XML file...
		$C_xml = new CORE_xml;
		$xml_construct = PATH_MODULES . "" . $module . "/" . $module . "_construct.xml";
		$construct = $C_xml->xml_to_array($xml_construct);

		### Check that this Module has any db installation required...
		if(isset($construct["construct"]["table"]))
		{
			### Create the module DB table
			$table = $construct["construct"]["table"];

			### Create the module DB fields
			$arr_field = $construct["construct"]["field"];

			### Loop through the fields to build the list:
			#$index_flds = 'id,site_id';
			$index_flds = '';
			while (list ($key, $value) = each($arr_field))
			{
				$field = $key;
				$t_s  = $arr_field["$key"]["type"];
				if(isset($arr_field["$key"]["index"]))
				{
					if(empty($index_flds))
					$index_flds .= $key;
					else
					$index_flds .= ','.$key;
				}

				if(preg_match('/[(]/',$t_s))
				{
					$ts = explode('(',$t_s);
					$type = $ts[0];
					$size = preg_replace('/[)]/', '', $ts[1]); 
					$flds[] = Array($field, $type, $size); 
				}
				else
				{ 
					$flds[] = Array($field, $t_s);                        
				}
			}

			### Multi site?
			if(DEFAULT_SITE==1) 
			{ 
				### Create the table & colums using the ADODB Data Dictionary functions:
				$db = &DB();
				$dict = NewDataDictionary($db);
				$table_options = array();
				$sqlarray = $dict->CreateTableSQL(AGILE_DB_PREFIX.''.$table, $flds, $table_options); 
				$result = $db->Execute($sqlarray[0]);
				if ($result === false)
				{
					global $C_debug;
					$C_debug->error('module.inc.php','install_db (1)', $db->ErrorMsg() . ' '. print_r($sqlarray[0]));		
					return false;
				} 

				# Create unique index on site_id,id  (mysql specific)                       	 
				$db->Execute("CREATE UNIQUE INDEX IDS on ".AGILE_DB_PREFIX."$table (site_id, id)"); 

				# Create any custom indexes
				if(@$new_indexes = $construct["construct"]["index"])
				{                	
					while (list ($index, $fields) = each($new_indexes))
					{   
						$dict = NewDataDictionary($db); 
						if(preg_match("/fulltext/i", $index) && AGILE_DB_TYPE == 'mysql')
							$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields, array('FULLTEXT'));
						else
							$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields);
						$db->Execute($sqlarray[0]); 
					}
				}
			}      
		}



		##################################################################
		### Get the module properties:

		if(isset($install["install"]["module_properties"]["menu_display"]))
			$menu_display   = $install["install"]["module_properties"]["menu_display"];
		else
			$menu_display   = '';

		if(isset($install["install"]["module_properties"]["notes"]))
			$notes          = $install["install"]["module_properties"]["notes"];
		else
			$notes          = '';

		###################################################################
		### Get the parent module...

		$db = &DB();
		$module_id = $db->GenID(AGILE_DB_PREFIX . "" . 'module_id');
		if(isset($install["install"]["module_properties"]["parent"]))
		{
			$q = 'SELECT id FROM '.AGILE_DB_PREFIX.'module WHERE
				site_id     = '.$db->qstr(DEFAULT_SITE).' AND
				name        = '.$db->qstr($install["install"]["module_properties"]["parent"]);
			$result = $db->Execute($q);



			# Error checking
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('module.inc.php','install_db', $db->ErrorMsg());			
				return false;
			}

			if($result->fields["id"] == '')
				$parent_id = $module_id;
			else
				$parent_id = $result->fields["id"];

		}
		else
		{
			$parent_id = $module_id;
		}



		##################################################################
		### Create the module record, & get the module ID
		### get the ID of the parent, and create it as child if needed...
		### else the record is a child of itself...

		$q  = 'INSERT INTO '.AGILE_DB_PREFIX.'module SET
				id      = '     .$db->qstr($module_id).',
				site_id = '     .$db->qstr(DEFAULT_SITE).',
				name    = '     .$db->qstr($module).',
				parent_id = '   .$db->qstr($parent_id).',
				notes   =   '   .$db->qstr($notes).',
				status  = '     .$db->qstr('1').',
				menu_display = '.$db->qstr($menu_display);
		$result = $db->Execute($q);

		###################################################################
		### Create the module_method records, and get the ID for each one

		@$methods = $install["install"]["sql_inserts"]["module_method"];

		if(!empty($methods) && is_array($methods))
		{
			while (list ($key, $value) = each($methods))
			{

				$name       = $key;
				$method_id  = $db->GenID(AGILE_DB_PREFIX.'module_method_id');

				if(isset($methods[$key]["notes"]))
					$notes = $methods[$key]["notes"];
				else
					$notes = '';

				if(isset($methods[$key]["page"]))
					$page       = $methods[$key]["page"];
				else
					$page       = '';

				if(isset($methods[$key]["menu_display"]))
					$menu_display = '1';
				else
					$menu_display = '0';

				$q = 'INSERT INTO '.AGILE_DB_PREFIX .'module_method SET
					  id        = '.$db->qstr($method_id).',
					  site_id   = '.$db->qstr(DEFAULT_SITE).',
					  name      = '.$db->qstr($name).',
					  module_id = '.$db->qstr($module_id).',
					  notes     = '.$db->qstr($notes).',
					  page      = '.$db->qstr($page).',
					  menu_display = '.$db->qstr($menu_display);

				$result = $db->Execute($q);

				# Error checking
				if ($result === false)
				{
					global $C_debug;
					$C_debug->error('module.inc.php','install_db :: module_method', $db->ErrorMsg());
					return false;
				}


				###############################################################
				### Create the group_method records, with the ID from each
				### of the above methods...
				### Get the groups to add to (FROM THE install.tpl form!)

				for($i=0; $i<count($VAR["module_group"]); $i++)
				{
					$group_method_id  = $db->GenID(AGILE_DB_PREFIX  . 'group_method_id');
					$q = 'INSERT INTO '.AGILE_DB_PREFIX .'group_method SET
						  id        = '.$db->qstr($group_method_id).',
						  site_id   = '.$db->qstr(DEFAULT_SITE).',
						  method_id = '.$db->qstr($method_id).',
						  module_id = '.$db->qstr($module_id).',
						  group_id  = '.$db->qstr($VAR["module_group"][$i]);

					$result = $db->Execute($q);

					# Error checking
					if ($result === false)
					{
						global $C_debug;
						$C_debug->error('module.inc.php','install_db :: group_method_id', $db->ErrorMsg());
						return false;
					}
				}
			}
		} 

		//$db->Execute ( sqlDelete(&$db, 'module', "name IS NULL or name = '' OR parent_id IS NULL or parent_id = ''")  );

		# all done!
		return true;
	}


	##############################################
	##	     INSTALL DEFAULT DATA               ##
	##############################################
	function install_sql_data($module)
	{
			# check the file:
			$f = PATH_MODULES . '' . $module . '/' . $module . '_install_data.xml';

			if(is_file($f))
			{
				# open the XML backup file:
				$C_xml = new CORE_xml;
				$backup = $C_xml->xml_to_array($f);        			
				$db = &DB();
				$arr =  $backup['install'];  			

				# loop through each table in this array
				if(is_array($arr) )
				{
					while (list ($table,$records) = each ($arr))
					{
						$runsql = false;
						$sqls = 'INSERT INTO '.AGILE_DB_PREFIX.'' . $table . ' SET ';

						if (is_array($records) )
						{        			
							# loop through each of the fields for this module
							$sql = '';
							$sqlcount = 0;
							while (list ($fld,$val) = each ($records))
							{
								if (is_array($val))
								{
									# loop through each of the fields for this module
									$sql = '';
									$sqlcount = 0;
									while (list ($fld2,$val2) = each ($val))
									{
										if ($sqlcount != 0) $sql .= ', ';
										$sql .= $fld2 .' = '.$db->qstr($val2);
										$sqlcount++;
									}
									## echo '<BR>' . $sqls. ' ' . $sql;
									$result = $db->Execute($sqls. ' ' . $sql);

								}
								else
								{
									if ($sqlcount != 0) $sql .= ', ';
									$sql .= $fld .' = '.$db->qstr($val);
									$sqlcount++;
									$runsql = true;
								}

							}
							if ($runsql)
							{
								## echo '<BR>' . $sqls. ' ' . $sql;
								$result = $db->Execute($sqls. ' ' . $sql);
								if($result === false)
								@$this->error .= "<BR>". $sqls. ' ' . $sql;
							}
						}
					}
				} 
			}
	}


	###################################
	##         MAIN INSTALLER        ##
	###################################
	function install($VAR)
	{
		global $smarty, $C_translate;

		# check this module for any errors:
		if($this->install_error_check($VAR))
		{

			### Get sub_modules of this package
			if(isset($this->install["install"]["module_properties"]["sub_modules"]))
			{
				### Check Each Sub-module:
				$arr_sub = $this->install["install"]["module_properties"]["sub_modules"];

				if(preg_match('/,/', $arr_sub))
					$arr_s = explode(',', $arr_sub);
				else
					$arr_s[] = $arr_sub;

				for($i=0; $i<count($arr_s); $i++)
				{
					if(!$this->install_error_check_sub($arr_s[$i]))
						$error[] = $C_translate->translate('install_sub_module_err','module','sub_module='.$arr_s[$i]);
				}
			}

			# check for error:
			if(isset($error))
			{
				$error[] = $C_translate->translate('install_failed','module','');

				# set the errors as a Smarty Object
				$smarty->assign('form_validation', $error);	
				return false;
			}


			### install the SQL...
			$module = trim($VAR["install_name"]);
			if($this->install_sql($module))
			{
				### Loop through the sub-modules and install each of them
				if(isset($arr_s))
				{
					for($i=0; $i<count($arr_s); $i++)
					{
						if(!$this->install_sql($arr_s[$i]))
						{
							### Errors in install_sql(), then delete any SQL changes!
							### set smarty error
							return;
						}

					}
				}
			}
			else
			{
				### Errors in install_sql(), then delete any SQL changes!
				### set smarty error
				return;
			}

			### Insert default data: 
			$this->install_sql_data($module);
		}

		# update the current user's authentication so the update group access applies
		# to them
		global $C_auth;
		$C_auth->auth_update();
	}






	###################################
	##     AUTO UPGRADER             ##
	###################################
	function upgrade($VAR)
	{
		if(!isset($VAR['module_name']) || !isset($VAR['module_group']))
		{
			echo "You must select both the module(s) to upgrade and the groups to grant access to new methods to.";
			return;
		}

		$module_count = 0;
		$method_count = 0;
		$fields_count = 0;
		$method_new_count = 0;
		$fields_new_count = 0;

		# loop through each module
		$modules = $VAR['module_name'];

		for($i=0; $i<count($modules); $i++)
		{             
			# increment module count
			$module_count++;

			# get the module details
			$db     = &DB();
			$db_module = $db->Execute(sqlSelect($db,"module","*","id=::{$modules[$i]}:: or name=::{$modules[$i]}::"));                
			$module_name = $db_module->fields['name'];
			$module_id   = $db_module->fields['id'];

			#########################################################################
			# Update the Methods from the <module>_install.xml file
			# get the install xml file
			#########################################################################

			$install_xml = PATH_MODULES.$module_name.'/'.$module_name.'_install.xml';
			if(is_file($install_xml))
			{
				$C_xml = new CORE_xml;
				@$methods = $C_xml->xml_to_array($install_xml);
				@$methods = $methods['install']['sql_inserts']['module_method'];

				# loop through the methods
				if(is_array($methods))
				{
					while (list ($key, $value) = each($methods))
					{
						# increment method count
						$method_count++;

						# see if this method exists
						$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'module_method WHERE
								   name         =  ' . $db->qstr( $key ) . ' AND
								   module_id    =  ' . $db->qstr( $module_id ) . ' AND
								   site_id      =  ' . $db->qstr(DEFAULT_SITE);
						$method_db  = $db->Execute($sql);                             
						if ($method_db === false)  {
							global $C_debug;
							$C_debug->error('module.inc.php','upgrade', $db->ErrorMsg());
						}
						if($method_db->RecordCount() == 0)
						{
							# increment method count
							$method_new_count++;

							### add this method
							@$notes          = $methods[$key]["notes"];
							@$page           = $methods[$key]["page"];
							@$menu_display   = $methods[$key]["menu_display"];

							$method_id = sqlGenID($db, 'module_method');
							$fields=Array('name'=>$key, 'module_id'=>$module_id, 'notes'=>$notes, 'page'=>$page, 'menu_display'=>$menu_display);
							$db->Execute(sqlInsert($db,"module_method",$fields, $method_id));                           
							if ($result === false)  {
								global $C_debug;
								$C_debug->error('module.inc.php','upgrade', $db->ErrorMsg());
							}

							### Create the group_method records, with the ID from each
							for($ii=0; $ii<count($VAR["module_group"]); $ii++)
							{
								$group_method_id  = $db->GenID(AGILE_DB_PREFIX . "" . 'group_method_id');
								$q = 'INSERT INTO '.AGILE_DB_PREFIX .'group_method SET
									  id        = '.$db->qstr($group_method_id).',
									  site_id   = '.$db->qstr(DEFAULT_SITE).',
									  method_id = '.$db->qstr($method_id).',
									  module_id = '.$db->qstr($module_id).',
									  group_id  = '.$db->qstr($VAR["module_group"][$ii]);
								$result = $db->Execute($q);                                     
								if ($result === false) {
									global $C_debug;
									$C_debug->error('module.inc.php','upgrade', $db->ErrorMsg());
								}
							}
						}
					}
				} 
			}


			#########################################################################
			# Update the DB Fields from the <module>_construct.xml file
			# get the install xml file
			#########################################################################

			$construct_xml = PATH_MODULES.$module_name.'/'.$module_name.'_construct.xml';
			if(is_file($construct_xml))
			{
				$C_xml = new CORE_xml;
				$construct = $C_xml->xml_to_array($construct_xml);
				@$fields = $construct['construct']['field'];

				### Check that this Module has any db installation required...
				if(!empty($construct["construct"]["table"]) && $construct["construct"]["table"] == $module_name)
				{
					### Create the module DB table
					$table = $construct["construct"]["table"];
					$db = &DB();
					$db_fields = $db->MetaColumns(AGILE_DB_PREFIX.$table, true);

					### Create the module DB fields
					$arr_field = $construct["construct"]["field"];

					### Loop through the fields to build the list: 
					while (list ($key, $value) = each($arr_field))
					{
						$field = $key;
						$FIELD = strtoupper($key);
						if(!isset($db_fields[$FIELD]))
						{
							# increment field count
							$fields_new_count++;

							$t_s  = $arr_field["$key"]["type"]; 
							if(preg_match('/[(]/',$t_s))
							{
								$ts = explode('(',$t_s);
								$type = $ts[0];
								$size = preg_replace('/[)]/', '', $ts[1]);
								$flds[] = Array($field, $type, $size); 
							}
							else
							{ 
								$flds[] = Array($field, $t_s); 
							}
						}
					}

					### Add any new columns:
					if(is_array(@$flds))
					{
						$dict = NewDataDictionary($db);
						$sqlarray = $dict->AddColumnSQL(AGILE_DB_PREFIX.$table, $flds);
						$result = $db->Execute($sqlarray[0]);
						if ($result === false) {
							global $C_debug;
							$C_debug->error('module.inc.php','install_db', $db->ErrorMsg());
							echo $db->ErrorMsg();
						}
						unset($flds);
					}


					### Remove any unused columns
					while (list ($key, $value) = each($db_fields))
					{
						$fieldname = strtolower($key);
						if(!isset($construct["construct"]["field"][$fieldname])) $flds[] = $key;

					}
					if(is_array(@$flds))
					{
						$dict = NewDataDictionary($db);
						$sqlarray = $dict->DropColumnSQL(AGILE_DB_PREFIX.$table, $flds);

						$sqlarray[0];
						$result = $db->Execute($sqlarray[0]);
						if ($result === false) {
							global $C_debug;
							$C_debug->error('module.inc.php','install_db', $db->ErrorMsg());
							echo $db->ErrorMsg();
						}
						unset($flds);
					} 


					####################################################
					### Update Indexes: 

					# Get old database indexes
					$dict = NewDataDictionary($db);
					$oldindex = $dict->MetaIndexes(AGILE_DB_PREFIX.$table);

					# check if the 'site_id' index exists:
					if(!empty($oldindex['site_id']) && $oldindex['site_id'] = 'id,site_id') { 
						$dict = NewDataDictionary($db);
						$sqlarray = $dict->DropIndexSQL('site_id', AGILE_DB_PREFIX.$table); 
						$db->Execute($sqlarray[0]);
					}

					# check that that UNIQUE index for site_id,id exists
					if(empty($oldindex['IDS']) || $oldindex['IDS']['unique'] != 1) 
					{ 	  
						$db=&DB();
						$db->Execute("alter table ".AGILE_DB_PREFIX."$table drop primary key");                         	 
						$db->Execute("CREATE UNIQUE INDEX IDS on ".AGILE_DB_PREFIX."$table (site_id, id)"); 
					}

					$dict = NewDataDictionary($db);
					$oldindex = $dict->MetaIndexes(AGILE_DB_PREFIX.$table);

					# Current construct invoices 
					if(@$new_indexes = $construct["construct"]["index"])
					{
						while (list ($index, $fields) = each($new_indexes))
						{   
							if(is_array(@$oldindex[$index]))
							{
								# already exists - compare fields: 
								$oldfields = implode(",", $oldindex[$index]['columns']);

								if($oldfields != $fields)
								{    
									# index changed - drop: 
									$dict = NewDataDictionary($db);
									$sqlarray = $dict->DropIndexSQL($index, AGILE_DB_PREFIX.$table); 

									$db->Execute($sqlarray[0]); 

									# create index
									$dict = NewDataDictionary($db); 

									if(preg_match("/fulltext/i", $index) && AGILE_DB_TYPE == 'mysql')                  
										$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields, array('FULLTEXT')); 
									else 
										$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields); 				                        
									$db->Execute($sqlarray[0]); 
								} 
							} 
							else 
							{  
								# index does not exist - create!
								$dict = NewDataDictionary($db);

								if(preg_match("/fulltext/i", $index) && AGILE_DB_TYPE == 'mysql')                  
									$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields, array('FULLTEXT')); 
								else 	                        		
									$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields); 
								$db->Execute($sqlarray[0]);
							}

							$verify_index[] = $index;
						}

						# Check for removed indexes:
						if(!empty($oldindex))
						{
							reset($oldindex);
							while (list ($index, $fields) = each($oldindex))
							{
								if(!isset($new_indexes[$index]) && $index != 'IDS')
								{
									$dict = NewDataDictionary($db);
									$sqlarray = $dict->DropIndexSQL($index, AGILE_DB_PREFIX.$table); 
									$db->Execute($sqlarray[0]);                     			
								}
							} 
						}
					}
					else
					{
						# remove all old indexes
						if(!empty($oldindex))
						{ 
							reset($oldindex);
							while (list ($index, $fields) = each($oldindex))
							{
								if($index != 'IDS')
								{
									$dict = NewDataDictionary($db);
									$sqlarray = $dict->DropIndexSQL($index, AGILE_DB_PREFIX.$table);  
									$db->Execute($sqlarray[0]);           
								}         			
							} 
						}
					}                        
				}
			}
		}

		$msg =  "Successfully checked $module_count module(s), $method_count method(s), ".
				"and $fields_count db fields. <BR>".
				"Added $method_new_count method(s) and $fields_new_count db field(s).";


		if(!empty($fields_new_count) > 0) {
			$js = '<script language="javascript">document.getElementById("module_add").submit();</script>';
			global $smarty;
			if(is_object($smarty))
				$smarty->assign('js', $js);
			else
				echo '<script language="javascript">document.refresh();</script>';
		}


		# Display the message.
		global $C_debug;
		if(is_object($C_debug)) 
			$C_debug->alert($msg);
		else
			echo $msg;

		# update the current user's authentication so the update group access applies
		# to them
		global $C_auth;
		if(is_object($C_auth)) $C_auth->auth_update();

	}


	# Create the install XML file for specified modules
	function dev_install_gen($VAR)
	{

		# loop through each module passed...
		include_once('dev.inc.php');
		$db = &DB();

		if(is_array($VAR['module'])) 
		{
			for($ix = 0; $ix<count($VAR['module']); $ix++)
			{ 
				$sql  = "SELECT * FROM ".AGILE_DB_PREFIX."module  WHERE 
						id 		= ".$db->qstr($VAR['module'][$ix])." AND
						site_id = ".$db->qstr(DEFAULT_SITE);
				$result = $db->Execute($sql);
				while(!$result->EOF)
				{ 
						# update the {module}_install.xml file data
						$xml = dev_install_xml_gen($result->fields['name'],$result->fields['id']);

						# write the file
						$file = fopen(PATH_MODULES . '' . $result->fields['name'] . '/'. $result->fields['name'] . "_install.xml", "w+");
						fputs($file, $xml);
						fclose($file);

						$do = true;
						for($i=0; $i<count($this->dev_inst_excl); $i++)
						{
							if ( $this->dev_inst_excl[$i] == $result->fields['name'])
							{
								$do = false;
							}
						}

						if ($do)
						{
							# update/create the {$module}_install_data.xml file data
							$xml = dev_install_xml_data($result->fields['name'],$result->fields['id']);
						}
						else
						{
							/*
							$xml = '<?xml version="1.0" encoding="ISO-8859-1" ?'.''.'>';
							$xml .= '
<install>
</install>';
							*/
							$xml = false;
						}

						# write the file
						if($xml != false)
						{
							$file = fopen(PATH_MODULES . '' . $result->fields['name'] . '/'. $result->fields['name'] . "_install_data.xml", "w+");
							fputs($file, $xml);
							fclose($file);
						}

						# next module
						$result->MoveNext();
				}
			}
		}
	}



	# add a new module construct
	function dev_add($VAR)
	{

		# check if the needed directories exist & attempt to create...

		if (!is_dir(PATH_MODULES . '' . $VAR["module"])) {
			echo "<BR>Path does not exist, attempting to create: ".PATH_MODULES . '' . $VAR["module"];
			if(!mkdir(PATH_MODULES . '' . $VAR["module"])) {
				echo "<BR><BR>Error: Module creation failed, please check path permissions...<BR>";
				return false;
			}
		}

		if (!is_dir(PATH_LANGUAGE . '' . $VAR["module"])) {
			echo "<BR>Path does not exist, attempting to create: ".PATH_LANGUAGE . '' . $VAR["module"];
			if(!mkdir(PATH_LANGUAGE . '' . $VAR["module"])) {
				echo "<BR><BR>Error: Module creation failed, please check path permissions...<BR>";
				return false;
			}
		}


		if (!is_dir(PATH_THEMES . 'default/blocks/' . $VAR["module"])) {
			echo "<BR>Path does not exist, attempting to create: ".PATH_THEMES . 'default/blocks/' . $VAR["module"];
			if(!mkdir(PATH_THEMES . 'default/blocks/' . $VAR["module"])) {
				echo "<BR><BR>Error: Module creation failed, please check path permissions...<BR>";
				return false;
			}
		}


		# include the dev functions:
		include('dev.inc.php');

		$construct_xml = dev_construct_xml($VAR);
			# write the consruct XML
			$file = fopen(PATH_MODULES . '' . $VAR["module"] . '/'. $VAR["module"] . "_construct.xml", "w+");
			fputs($file,$construct_xml);
			fclose($file);


		$construct_php  = dev_construct_php($VAR);
			# write the construct PHP
			$file=fopen(PATH_MODULES . '' . $VAR["module"] . '/'. $VAR["module"] . ".inc.php", "w+");
			fputs($file,$construct_php);
			fclose($file);


		$language_xml   = dev_language_xml($VAR);
			# write the language packs
			$file=fopen(PATH_LANGUAGE . '' . $VAR["module"] . "/english_" . $VAR["module"] . ".xml", "w+");
			fputs($file, $language_xml);
			fclose($file);


		$install_xml   = dev_install_xml($VAR);
			# write the language packs
			$file = fopen(PATH_MODULES . '' . $VAR["module"] . '/'. $VAR["module"] . "_install.xml", "w+");
			fputs($file, $install_xml);
			fclose($file);


		# generate the main block
		$main_tpl   = dev_block_main($VAR);
			# write the block
			$file = fopen(PATH_THEMES . 'default/blocks/' . $VAR["module"] . "/main.tpl", "w+");
			fputs($file, $main_tpl);
			fclose($file);

		# generate the add block
		$add_tpl   = dev_block_add($VAR);
			# write the block
			$file = fopen(PATH_THEMES . 'default/blocks/' . $VAR["module"] . "/add.tpl", "w+");
			fputs($file, $add_tpl);
			fclose($file);

		# generate the view block
		$view_tpl   = dev_block_view($VAR);
			# write the block
			$file = fopen(PATH_THEMES . 'default/blocks/' . $VAR["module"] . "/view.tpl", "w+");
			fputs($file, $view_tpl);
			fclose($file);

		# generate the search_form block
		$search_form_tpl   = dev_block_search_form($VAR);
			# write the block
			$file = fopen(PATH_THEMES . 'default/blocks/' . $VAR["module"] . "/search_form.tpl", "w+");
			fputs($file, $search_form_tpl);
			fclose($file);

		# generate the search_show block
		$search_show_tpl   = dev_block_search_show($VAR);
			# write the block
			$file = fopen(PATH_THEMES . 'default/blocks/' . $VAR["module"] . "/search_show.tpl", "w+");
			fputs($file, $search_show_tpl);
			fclose($file);
	}
}
?>
