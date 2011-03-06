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
	
function CORE_database_join_fields($result, $linked)
{  
	# get any linked fields
	for($i=0; $i < count($linked); $i++)
	{
		$field      = $linked["$i"]["field"];
		$link_field = $linked["$i"]["link_field"];
		$link_table = $linked["$i"]["link_table"];		

		# get an array of the values to select from the database
		$arr = '';
		$ids = '';
		for ($ii=0; $ii < count($result); $ii++ ) {
			$curr = $result["$ii"]["$field"];
			if(!isset($arr["$curr"]))
			{
				if($ids == '')
				{
					$arr["$curr"] = true;
					$ids .= " id = '" . $curr . "'";	
				}
				else
				{
					$arr["$curr"] = true;	
					$ids .= " OR id = '" . $curr . "'";						
				}
			}
		}

		if($ids != '')
		{
			# generate the SQL query
			$sql= "
			SELECT id,$link_field FROM " . AGILE_DB_PREFIX . "$link_table
			WHERE
			( $ids )
			AND site_id = '" . DEFAULT_SITE . "'";				
			$db = &DB();
			$rss = $db->Execute($sql);


		# error reporting
		if ($rss === false)
		{
			global $C_debug;
			$C_debug->error('database.inc.php','join_fields', $db->ErrorMsg());
		}			
		else
		{		 
			# set the results as an array
			while (!$rss->EOF) {					
				for ($ii=0; $ii < count($result); $ii++ ) {
					if($result[$ii][$field] == $rss->fields[id]) {
						if(preg_match('/,/',$link_field)) {
							$fields = explode(',',$link_field);
							for($iii=0; $iii<count($fields); $iii++) {
								$fieldname = $fields[$iii];
								$rss->fields[$fieldname];
								$result[$ii][$fieldname] = $rss->fields[$fieldname];
							} 
						} else  {								
							# change the field from the id to the name...
							$result[$ii][$field] = $rss->fields[$link_field];
						}
					}
				}	
				$rss->MoveNext();					
			}					
		}				
	}
	}
	return $result;
}		
?>