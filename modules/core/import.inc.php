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
	
	
/**
 * Import Handler Module.
 */
class CORE_import
{

	### Validate the import file/upload.	
	### Generate the array to display the field options.

	function prepare_import($VAR, $construct)
	{
		global $_FILES, $C_debug, $smarty;

		if($VAR['filetype'] == 'upload')
		{
			if(isset($_FILES['file']) && $_FILES['file']['size'] > 0)
			{ 
				# added str_replace for spaces, because Linux really doesn't like them much.
				$this->filename = str_replace(" ","_",'import_'.$VAR['module'].'_'.$_FILES['file']['name']);
				$this->file = PATH_FILES . $this->filename;
				copy($_FILES['file']['tmp_name'], $this->file);		 
			}	
			else
			{
				$C_debug->alert('Please go back and enter a file to upload!');
				return false;
			}			
		}
		elseif ($VAR['filetype'] == 'local')
		{
			@$this->filename = $VAR['file'];
			@$this->file = PATH_FILES.$VAR['file'];
			if(!is_file($this->file))
				$C_debug->alert('The specified file does not exist!');
				return false;
		}
		else
		{
			$C_debug->alert('Please go back and enter all required fields!');
			return false;
		}

		# open the file for parsing
		$data 	= file_get_contents($this->file);
		$rows 	= explode("\r\n", $data);


		if($VAR['type'] == 'csv')
			$sp = ',';
		elseif($VAR['type'] == 'tab')
			$sp = '	';
		else
			return false;

		# get the first row for the example data
		if(count($rows) == 0)
		{	
			return false;
		}
		else
		{  
			# set the columns and sample data smarty array:
			$columns = explode($sp, $rows[0]);
			$this->sbox = array('-- Use Constant Value --');

			for($i=0;$i<count($columns);$i++)
			{ 
				$smart[$i]['idx'] = $i;
				$smart[$i]['sample'] = $columns[$i];
				$this->sbox[] = "Field #$i: ".$columns[$i];

				if($VAR['type'] && preg_match("/^\"/",$smart[$i]['sample']) && preg_match("/\"$/",$smart[$i]['sample']))
				{
					$smart[$i]['sample'] = preg_replace("/^\"/","",$smart[$i]['sample']);
					$smart[$i]['sample'] = preg_replace("/\"$/","",$smart[$i]['sample']);
				}
			}

			# set the smarty vars:
			$smarty->assign('rows',count($rows));
			$smarty->assign('columns',$smart);
			$smarty->assign('file', $this->filename);
			$smarty->assign('type', $VAR['type']);

			# set the available fields:
			$fields = explode(',', '--NONE--,'.$construct->method['import']);
			$smarty->assign('fields', $fields);


			$smarty->assign('fields_array',$this->build_fields($construct));	
			return true;
		}		
	}

	function build_fields(&$construct)
	{
		$fields_array = explode(",",$construct->method['import']);
		$i = 0;
		foreach ($fields_array as $f) {
			$itbl[$i]['field'] = $f;
			$itbl[$i]['options'] = @$this->sbox;
			$itbl[$i]['type'] = '';
			$itbl[$i]['custom'] = 0;
			$i++;
		}
		if(isset($construct->import_custom) && count($construct->import_custom)) {
			foreach ($construct->import_custom as $f) {
				$itbl[$i]['field'] = $f['name'];
				$itbl[$i]['options'] = $f['value'];
				$itbl[$i]['type'] = $f['type'];
				$itbl[$i]['custom'] = 1;
				$i++;
			}
		}
		return $itbl;			
	}

	### Perform the actual import
	function do_new_import(&$VAR, &$construct)
	{
		global $C_debug;
		$this->file = PATH_FILES.$VAR['file'];
		$data 	= file_get_contents($this->file);
		$rows 	= explode("\r\n", $data);

		# get the file type:	
		if($VAR['type'] == 'csv')
			$sp = ',';
		elseif($VAR['type'] == 'tab')
			$sp = '	';
		else
			return false;

		# get the first row for the example data
		if(count($rows) == 0)
		{	
			return false;
		}
		else
		{  
			# get the available fields:
			$fields = $this->build_fields($construct);

			# start the insert statement				
			$db = &DB();
			$q = "INSERT INTO ".AGILE_DB_PREFIX."$construct->table ";
			$sql = "SELECT * FROM ".AGILE_DB_PREFIX."$construct->table WHERE id = -1";  
			$rs = $db->Execute($sql); 

			$bad = 0;
			# loop through the rows
			for($row=0; $row<count($rows); $row++)
			{
				$record = array(); 
				$columns = explode($sp, $rows[$row]);

				if (strlen($rows[$row])) {
					# echo "<pre>".print_r($columns,true)."</pre>";
					$record = array();	  
					for($i=0;$i<count($fields);$i++)
					{
						$idx = $VAR['import_select'][$fields[$i]['field']];
						if ($idx > 0) {
							$record[$fields[$i]['field']] = @$columns[--$idx];
							if($VAR['type'] && preg_match("/^\"/",$record[$fields[$i]['field']]) && preg_match("/\"$/",$record[$fields[$i]['field']])) {
								$record[$fields[$i]['field']] = preg_replace("/^\"/","",$record[$fields[$i]['field']]);
								$record[$fields[$i]['field']] = preg_replace("/\"$/","",$record[$fields[$i]['field']]);
							}							
						} else {
							$record[$fields[$i]['field']] = @$VAR['import_constant'][$fields[$i]['field']];
						}					

					}

					$record['site_id'] = DEFAULT_SITE; 
					if(empty($record['id'])) 
						$record['id'] = $db->GenID(AGILE_DB_PREFIX . $construct->table . '_id');

					$SQL = $db->GetInsertSQL($rs, $record); 	
					# echo $SQL."<br>";

					/* call import_line_process */
					if (is_callable(array($construct,'import_line_process'))) {
						$construct->import_line_process($db, $VAR, $fields, $record);
					}

					if($VAR['import_type'] == 'db')			
					{
						# execute command
						$db->Execute($SQL);
					} 
					else
					{
						# add to sql command for download
						@$SQL_DL .= $SQL . ";\r\n\r\n";
					}
				} else {
					$bad++;
				}
			}

			if(@$VAR['import_type'] == 'dl')
			{
				header ("Content-Disposition: attachment; filename=$construct->table.sql" );
				echo $SQL_DL;
				exit;
			}

			$C_debug->alert("Imported $row record(s) and $bad record(s) were skipped.");
			return true;
		}						
	}	    


	### Validate the fields.
	### Perform the actual import

	function do_import($VAR, $construct)
	{
		global $C_debug;
		$this->file = PATH_FILES.$VAR['file'];
		$data 	= file_get_contents($this->file);
		$rows 	= explode("\r\n", $data);

		# get the file type:	
		if($VAR['type'] == 'csv')
			$sp = ',';
		elseif($VAR['type'] == 'tab')
			$sp = '	';
		else
			return false;

		# get the first row for the example data
		if(count($rows) == 0)
		{	
			return false;
		}
		else
		{  
			# get the available fields:
			$fields = explode(',', ','.$construct->method['import']);

			# start the insert statement				
			$db = &DB();
			$q = "INSERT INTO ".AGILE_DB_PREFIX."$construct->table ";
			$sql = "SELECT * FROM ".AGILE_DB_PREFIX."$construct->table WHERE id = -1";  
			$rs = $db->Execute($sql); 


			# loop through the rows
			for($row=0; $row<count($rows); $row++)
			{
				$record = array(); 
				$columns = explode($sp, $rows[$row]);

				for($i=0;$i<count($columns);$i++)
				{  
					# get the field:
					$import_field 	= $VAR['import_field'];
					$import_field 	= $import_field[$i];
					$field 			= $fields[$import_field];

					# check that the field is not defined already: 
					if(!empty($record["$field"])) {
						$C_debug->alert("You have used the table <u>$field</u> more than once!");
						return false;
					}

					# get the field data:
					if(!empty($field))
					{ 
						# get the data:
						$data = $columns[$i];					
						if($VAR['type'] && preg_match("/^\"/",$data) && preg_match("/\"$/",$data))
						{
							$data = preg_replace("/^\"/","",$data);
							$data = preg_replace("/\"$/","",$data);
						} 

						# check for relational records:


						$record["$field"] = $data;
					}	 		
				}

				$record['site_id'] = DEFAULT_SITE; 
				if(empty($record['id'])) 
					$record['id'] = $db->GenID(AGILE_DB_PREFIX . $construct->table . '_id');

				$SQL = $db->GetInsertSQL($rs, $record); 	

				if($VAR['import_type'] == 'db')			
				{
					# execute command
					$db->Execute($SQL);
					if($import_rs === false)
					{
						# error message
					}
				} 
				else
				{
					# add to sql command for download
					@$SQL_DL .= $SQL . ";\r\n\r\n";
				}
			}

			if($VAR['import_type'] == 'dl')
			{
				header ("Content-Disposition: attachment; filename=$construct->table.sql" );
				echo $SQL_DL;
				exit;
			}

			$C_debug->alert("Imported $row record(s).");	 				
			return true;
		}						
	}	    
} 
?>
