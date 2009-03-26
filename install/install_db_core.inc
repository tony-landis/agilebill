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
	
##############################################
# DB installation class
##############################################
class install_db
{

	##############################################
	## insert the default data
	##############################################
	function add_data ( $arr )
	{
		$db = &DB();
		for($i=0; $i<count($arr); $i++)
		{
			$this->install_sql_data($arr[$i]);

			$sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . '' . $arr[$i] . '_id';
			$result = $db->Execute($sql);
			if($result == true && $result->fields["id"] <= "0")
			{
				$sql = 'INSERT INTO ' . AGILE_DB_PREFIX . '' . $arr[$i] . '_id SET id = 1';
				$db->Execute($sql);
			}
		}
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
								$sql = ereg_replace("site_id = '1',", "site_id = ".DEFAULT_SITE.",",$sql);
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
							$sql = ereg_replace("site_id = '1',", "site_id = ".DEFAULT_SITE.",",$sql);
							#echo '<BR>' . $sqls. ' ' . $sql;
							$result = $db->Execute($sqls. ' ' . $sql);
							if($result === false)
							@$this->error .= "<BR>". $sqls. ' ' . $sql;
						}
					}
				}
			}
		}
	}




	##############################################
	##	     INSTALL TABLES, FIELDS, & MODULES  ##
	##############################################
	function install_sql($module)
	{
		global $VAR;

		@$this->tables_created .= "<BR>Created Table `$module`";

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
			$index_flds = '';
			while (list ($key, $value) = each($arr_field))
			{
				$field = $key;
				$t_s  = $arr_field["$key"]["type"];

				if(ereg('[(]',$t_s))
				{
					$ts = preg_split('/[(]/',$t_s);
					$type = $ts[0];
					$size = ereg_replace('[)]', '', $ts[1]);
					$flds[] = Array($field, $type, $size); 
				}
				else
				{ 
					$flds[] = Array($field, $t_s); 
				}
			}


			### Check that the tables for this module have not been created already:
			$install_db = true;
			for($i=0; $i<count($this->core_modules); $i++)
			{
				if ($module == $this->core_modules[$i])
				$install_db = false;
			}
			for($i=0; $i<count($this->skip_modules); $i++)
			{
				if ($module == $this->skip_modules[$i])
				$install_db = false;
			}

			### Check that this is not a site_id creation:
			if(defined('DEFAULT_SITE') && DEFAULT_SITE > 1) $install_db = false;

			if ($install_db)
			{ 
				### Create the table & colums using the ADODB Data Dictionary functions:
				$db = &DB();
				$dict = NewDataDictionary($db);
				$table_options = array('mysql' => 'TYPE=MyISAM');
				$sqlarray = $dict->CreateTableSQL(AGILE_DB_PREFIX.''.$table, $flds, $table_options); 
				$result = $db->Execute($sqlarray[0]);
				if ($result === false)
				{
					echo "<BR><BR>" . $sqlarray[0];
					echo "<BR>Error (install_sql-1): ". $db->ErrorMsg(); 
					@$this->error .= "<BR>Error: ". $db->ErrorMsg(); 
				} 

				# create site_id index: 
				$dbres = $db->Execute("CREATE UNIQUE INDEX IDS on ".AGILE_DB_PREFIX."$table (site_id, id)"); 
				if ($dbres === false)
				{
					echo "<BR><BR>" . $sqlarray[0];
					echo "<BR>Error (install_sql-2): ". $db->ErrorMsg(); 
					@$this->error .= "<BR>Error: ". $db->ErrorMsg(); 
				}                            	 

				# Create custom indexes
				if(@$new_indexes = $construct["construct"]["index"])
				{
					while (list ($index, $fields) = each($new_indexes))
					{  
						if(!empty($index) && !empty($fields))
						{
							# create index
							$dict = NewDataDictionary($db);

							if(eregi("fulltext", $index) && AGILE_DB_TYPE == 'mysql')
								$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields, array('FULLTEXT'));
							else
								$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields);

							$dbres = $db->Execute($sqlarray[0]); 
							if ($dbres === false)
							{
								echo "<BR><BR>" . $sqlarray[0];
								echo "<BR>Error (install_sql-3): ". $db->ErrorMsg(); 
								@$this->error .= "<BR>Error: ". $db->ErrorMsg(); 
							}   
						}        		
					}
				}     

				### Create the starting auto-increment table
				$db->GenID(AGILE_DB_PREFIX .  $table . '_id');
				$db->Execute('DELETE FROM ' . AGILE_DB_PREFIX . $table . '_id');
			}
		}

		##################################################################
		### Get the module properties:

		if(@$install["install"]["module_properties"]["menu_display"] == '0')
			$menu_display   = '0';
		else if(@$install["install"]["module_properties"]["menu_display"] != '1')
			$menu_display   = '0';
		else
			$menu_display   = '1';

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
				@$this->error .= "<BR>Error: ". $db->ErrorMsg();
				#return false;
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
				name    = '     .$db->qstr($install["install"]["module_properties"]["name"]).',
				parent_id = '   .$db->qstr($parent_id).',
				notes   =   '   .$db->qstr($notes).',
				status  = '     .$db->qstr('1').',
				date_orig = '   .$db->qstr(time()).',
				date_last  = '  .$db->qstr(time()).',
				menu_display = '.$db->qstr($menu_display);
		$result = $db->Execute($q);

		###################################################################
		### Create the module_method records, and get the ID for each one

		$methods = $install["install"]["sql_inserts"]["module_method"];
		if (gettype($methods) == 'array')
		{
			while (list ($key, $value) = each($methods))
			{
				$name       = $key;
				$method_id  = $db->GenID(AGILE_DB_PREFIX . "" . 'module_method_id');

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
					@$this->error .= "<BR>Error: ". $db->ErrorMsg();
					#return false;
				}


				###############################################################
				### Create the group_method records, with the ID from each
				### of the above methods...
				### Get the groups to add to (FROM THE install.tpl form!)

				for($i=0; $i<count($VAR["module_group"]); $i++)
				{
					$group_method_id  = $db->GenID(AGILE_DB_PREFIX . 'group_method_id');
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
						@$this->error .= "<BR>Error: ". $db->ErrorMsg();
						#return false;
					}
				}
			}
		}

		# all done!
		return true;
	}






	##############################################
	##	     INSTALL TABLES & FIELDS ONLY       ##
	##############################################
	function install_sql_tbl($module)
	{
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
			while (list ($key, $value) = each($arr_field))
			{
				$field = $key;
				$t_s  = $arr_field["$key"]["type"];


				if(ereg('[(]',$t_s))
				{
					$ts = preg_split('/[(]/',$t_s);
					$type = $ts[0];
					$size = ereg_replace('[)]', '', $ts[1]);
					$flds[] = Array($field, $type, $size); 
				}
				else
				{ 
					$flds[] = Array($field, $t_s);                        
				}
			}

			# Check this is not a multi site id install
			if( DEFAULT_SITE == 1) 
			{ 
				### Create the table & colums using the ADODB Data Dictionary functions:
				$db = &DB();
				$dict = NewDataDictionary($db);
				$table_options = array('mysql' => 'TYPE=MyISAM');
				$sqlarray = $dict->CreateTableSQL(AGILE_DB_PREFIX.''.$table, $flds, $table_options);
				$result = $db->Execute($sqlarray[0]);
				if ($result === false)
				{
					echo "<BR><BR>". $sql[0];
					echo "<BR>5 Error: ". $db->ErrorMsg(); 
					@$this->error .= "<BR>Error (install_sql_tbl-1): ". $db->ErrorMsg();
					#return false;
				} 

				# Create unique index on site_id,id  (mysql specific)                       	 
				$db->Execute("CREATE UNIQUE INDEX IDS on ".AGILE_DB_PREFIX."$table (site_id, id)"); 

				# Create custom indexes
				if(@$new_indexes = $construct["construct"]["index"])
				{
					while (list ($index, $fields) = each($new_indexes))
					{
						if(!empty($index) && !empty($fields))
						{
							# create index
							$dict = NewDataDictionary($db);

							if(eregi("fulltext", $index) && AGILE_DB_TYPE == 'mysql')
							$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields, array('FULLTEXT'));
							else
							$sqlarray = $dict->CreateIndexSQL($index, AGILE_DB_PREFIX.$table, $fields);

							$dbres = $db->Execute($sqlarray[0]);
							if ($dbres === false)
							{
								echo "<BR><BR>" . $sqlarray[0];
								echo "<BR>Error (install_sql-3): ". $db->ErrorMsg();
								@$this->error .= "<BR>Error: ". $db->ErrorMsg();
							}
						}
					}
				}
			}
		}
	}
}           
?>