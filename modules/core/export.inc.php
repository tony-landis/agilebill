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
* Export Handler Class
* 
* This class handles the various export methods for excel, pdf, csv, xml, & txt (tab frormatted).  
*/
class CORE_export
{


########################################
##	EXPORT MULTIPE PDF INVOICES    #####
########################################

function pdf_invoice($VAR, $construct, $type)
{
	global $V_validate, $C_list, $C_translate;

	# loop through the field list to create the sql queries
	$arr = $construct->method[$type];
	$field_list = '';
	$i=0;
	while (list ($key, $value) = each ($arr))
	{
		if($i == 0)
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

			// determine if this record is linked to another table/field
			if($construct->field[$value]["asso_table"] != "")
			{
				$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
			}
		}
		else
		{
			$field_var =  $construct->table . '_' . $value;
			$field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

			// determine if this record is linked to another table/field
			if($construct->field[$value]["asso_table"] != "")
			{
				$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
			}
		}
		$i++;
	}

	# get the search details:
	if(isset($VAR['search_id']))
	{
		include_once(PATH_CORE   . 'search.inc.php');
		$search = new CORE_search;
		$search->get($VAR['search_id']);
	}
	else
	{
		echo '<BR> The search terms submitted were invalid!';
	}

	# get the sort order details:
	if(isset($VAR['order_by']) && $VAR['order_by'] != "")
	{
		$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
		$smarty_order =  $VAR['order_by'];
	}
	else
	{
		$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
		$smarty_order =  $search->order_by;
	}

	# format saved search string
	$sql = explode (" WHERE ", $search->sql);

	# generate the full query
	$db = &DB();
	$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
	$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
	$q = preg_replace("/%%whereList%%/i", "", $q);
	$q .= " ".AGILE_DB_PREFIX . "invoice.site_id = '" . DEFAULT_SITE . "'";
	$q .= $order_by;

	$invoice = $db->Execute($q);

	# error reporting
	if ($invoice === false)
	{
		global $C_debug;
		$C_debug->error('core:export.inc.php','pdf_invoice', $db->ErrorMsg() . '<br><br>' .$q);
		echo "An SQL error has occured!";
		return;
	}

	include_once(PATH_MODULES.'invoice/invoice.inc.php');
	$iv=new invoice;

	$iv->pdfExport($invoice);


}	



########################################
##	ADMIN SEARCH EXCEL EXPORT      #####
########################################

function search_excel($VAR, $construct, $type)
{
	global $C_translate;

	# set the field list for this method:
	$arr = $construct->method["$type"];
	$filename = 'Export.xls';


	# determine what action to take:
	# inline display, download, email, web save    	

	if($VAR["type"] == "display")
	{
	   header ('Content-type: application/x-msexcel');
	   header ("Content-Disposition: inline; filename=$filename" );
	   header ("Content-Description: PHP/INTERBASE Generated Data" );
	}

	else if($VAR["type"] == "download")
	{
		header ("Content-Disposition: attachment; filename=$filename" );
	}

	else if($VAR["type"] == "email")
	{
		echo "Email exports not supported yet!";
		exit;
	}

	else if($VAR["type"] == "email")
	{
		echo "Saving exports not supported yet!";
		exit;
	}           	

	# Start the Excel Stream
	xlsBOF();


		/************** BEGIN STANDARD EXPORT SEARCH CODE *********************/


		   # set the field list for this method:
		 $arr = $construct->method["$type"];

		 # loop through the field list to create the sql queries
		 $arr = $construct->method[$type];
		 $field_list = '';
		 $i=0;
		 while (list ($key, $value) = each ($arr))
		 {
			 if($i == 0)
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 else
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 $i++;
		 }

		# get the search details:
		if(isset($VAR['search_id']))
		{
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		}
		else
		{
			echo '<BR> The search terms submitted were invalid!';
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "")
		{
			$order_by = ' ORDER BY ' .   AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		}
		else
		{
			$order_by = ' ORDER BY ' .   AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
			$smarty_order =  $search->order_by;
		}

		# determine the offset & limit
		if(isset($VAR['page']))
		{
			$current_page = $VAR['page'];
		}
		else
		{
			$current_page = '0';
		}

		# determine the offset & limit
		if($current_page==0)
		{
			$offset = '0,10000000';
		}
		else
		{
			$offset =  (($current_page * $search->limit) - $search->limit). ',' . $search->limit;
		}

		# format saved search string
		$sql = explode (" WHERE ", $search->sql);

		# generate the full query
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " " . AGILE_DB_PREFIX . $construct->table.".site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;
		$db = &DB();

		# determine the offset & limit             
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('core:export.inc.php','search_xml', $db->ErrorMsg() . '<br><br>' .$q);
			echo "An SQL error has occured!";
			return;
		}

		# put the results into an array
		$i=0;
		$class_name = TRUE;
		$results = '';
		while (!$result->EOF) {
			$results[$i] = $result->fields;
			   $result->MoveNext();
			   $i++;
		}

		# get any linked fields
		if($i > 0)
		{
			$this->result = $results;
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($results, $this->linked);
		}
		else
		{
			$this->result = $results;
		}

		/************** END STANDARD EXPORT SEARCH CODE *********************/


	# WRITE THE FIRST COLUMN
	reset($arr);
	$column=0;
	while (list ($key, $value) = each ($arr))
	{
		// get the translated field name
		$field = $C_translate->translate('field_'.$value, $construct->module,'');

		if(gettype($this->result["$ii"]["$key"]) != "string" )
		{
			xlsWriteLabel($row,$column,$field);
		}
		$column++;
	}


	# LOOP THROUGH THE RESULTS & DISPLAY AS EXCEL
	$row = 1;
	for($ii = 0; $ii < count($this->result); $ii++)
	{
		# get the data for each cell:
		reset($arr);
		$column=0;
		while (list ($key, $value) = each ($arr))
		{
			if  ($construct->field["$value"][convert] == 'date'      ||
				 $construct->field["$value"][convert] == 'time'      ||
				 $construct->field["$value"][convert] == 'date-now'  ||
				 $construct->field["$value"][convert] == 'date-time' )
			{
				 $data = date(UNIX_DATE_FORMAT,$this->result["$ii"]["$value"]);
			} else {
				 $data = $this->result["$ii"]["$value"];
			}

			if(is_numeric ($data))
				xlsWriteNumber($row,$column,$data);
			else
				xlsWriteLabel($row,$column,$data);

			$column++;
		}
		$row++;
	}


	# Dispay the output
	xlsEOF();
	exit();
}	












	###################################
	##	ADMIN SEARCH PDF EXPORT   #####
	###################################

	function search_pdf($VAR, $construct, $type)
	{
		# include fpdf class:
		include_once(PATH_CORE . 'fpdf.inc.php');

		# set the field list for this method:
		$arr = $construct->method["$type"];

		# loop through the field list to create the sql queries
		$field_list = '';
		$heading_list = '';
		$width_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $construct->table . '_' . $value;
				$field_list .= $value;
				$heading_list .= strtoupper($value);		 // <- translate
				$width_list .= $construct->field[$value]['pdf_width'];
			}
			else
			{
				$field_var =  $construct->table . '_' . $value;
				$field_list .= "," . $value;
				$heading_list .= "," . strtoupper($value);		// <- translate
				$width_list .= "," . $construct->field[$value]['pdf_width'];
			}
			$i++;
		}

		# start the new PDF class...
		$pdf = new PDF_MC_Table();
		$pdf->Open();
		$pdf->AddPage();

		# Determine the number of columns and width for each one...	
		$SetWidths = explode(",",$width_list);
		$pdf->SetWidths($SetWidths);	

		# Define the table heading	
		$TableHeading =  explode(",",$heading_list);			

		# Define the Properties for the table heading cells:			
		# set the font:
		$pdf->HeadFontFamily 	= "Arial";
		$pdf->HeadFontStyle  	= "BI";
		$pdf->HeadFontSize		= 8;

		# set the font color:
		$pdf->HeadTextColor1	= 255;
		$pdf->HeadTextColor2	= 255;
		$pdf->HeadTextColor3	= 255;

		# set the bg color:
		$pdf->HeadFillColor1	= 90;
		$pdf->HeadFillColor2	= 90;
		$pdf->HeadFillColor3	= 90;

		# set the hieght
		$pdf->HeadHeight		= 6;

		# Print the Heading:
		$pdf->HeadRow($TableHeading);

		# Define the row settings for the rest of the cells...	
		# define the font settings
		$pdf->SetFontFamily 	= "Arial";
		$pdf->SetFontStyle  	= "";
		$pdf->SetFontSize		= "7";

		# set the hieght
		$pdf->RowHeight		= 4;			




		/************** BEGIN STANDARD EXPORT SEARCH CODE *********************/


		 # set the field list for this method:
		 $arr = $construct->method["$type"];

		 # loop through the field list to create the sql queries
		 $arr = $construct->method[$type];
		 $field_list = '';
		 $i=0;
		 while (list ($key, $value) = each ($arr))
		 {
			 if($i == 0)
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 else
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 $i++;
		 }

		# get the search details:
		if(isset($VAR['search_id']))
		{
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		}
		else
		{
			echo '<BR> The search terms submitted were invalid!';
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "")
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		}
		else
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
			$smarty_order =  $search->order_by;
		}

		# format saved search string
		$sql = explode (" WHERE ", $search->sql);

		# generate the full query
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . $construct->table.".site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;
		$db = &DB();

		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('core:export.inc.php','search_xml', $db->ErrorMsg() . '<br><br>' .$q);
			echo "An SQL error has occured!";
			return;
		}

		# put the results into an array
		$i=0;
		$class_name = TRUE;
		$results = '';
		while (!$result->EOF) {
			$results[$i] = $result->fields;
			   $result->MoveNext();
			   $i++;
		}

		# get any linked fields
		if($i > 0)
		{
			$this->result = $results;
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($results, $this->linked);
		}
		else
		{
			$this->result = $results;
		}

		/************** END STANDARD EXPORT SEARCH CODE *********************/





		# LOOP THROUGH THE RESULTS & DISPLAY AS PDF
		$results = $result->RecordCount();
		$BackAlt = TRUE;
		while (!$result->EOF)
		{
			# get the data for each cell:
			reset($arr);
			$CurrRow='';
			$i=0;
			while (list ($key, $value) = each ($arr))
			{
				if($i == 0)
				{
					$CurrRow .= $result->fields["$key"];
				}
				else
				{
					$CurrRow .= "::" . $result->fields["$key"];
				}
				$i++;
			}		
			$ThisRow =  explode("::",$CurrRow);			

			# set the colors & fonts
			if($BackAlt)
			{
				# alternating row 1:
				$pdf->SetTextColorVar1	= "50";
				$pdf->SetTextColorVar2	= "50";
				$pdf->SetTextColorVar3	= "50";			
				$pdf->SetFillColorVar1	= 255;
				$pdf->SetFillColorVar2	= 255;
				$pdf->SetFillColorVar3	= 255;						
				$BackAlt = FALSE;									
			}
			else
			{
				# alternating row 2:				
				$pdf->SetTextColorVar1	= "0";
				$pdf->SetTextColorVar2	= "0";
				$pdf->SetTextColorVar3	= "0";			
				$pdf->SetFillColorVar1	= 240;
				$pdf->SetFillColorVar2	= 240;
				$pdf->SetFillColorVar3	= 240;					
				$BackAlt = TRUE;			
			}

			# check for needed page break
			$nb=0;
			for($ii=0;$ii<count($dataarr);$ii++)
			   $nb=max($nb,$pdf->NbLines($pdf->widths[$ii],$dataarr[$ii]));
			$h=5*$nb;

			# Issue a page break first if needed
			if($pdf->CheckPageBreak($h))
				# print the Table Heading again
				$pdf->HeadRow($TableHeading);
				$pdf->Row($ThisRow);		
			# Next record
			$result->MoveNext();
		}

		# Dispay the output
		$pdf->Output();
		exit;
	}	










########################################
##	ADMIN SEARCH CSV EXPORT        #####
########################################

function search_csv($VAR, $construct, $type)
{
	global $C_translate;

	include_once(PATH_ADODB  . 'toexport.inc.php');
	$filename = 'Export.csv';


	# determine what action to take:
	# inline display, download, email, web save    	

	if($VAR["type"] == "display")
	{
	   header ('Content-type: application/x-csv');
	   header ("Content-Disposition: inline; filename=$filename" );
	}

	else if($VAR["type"] == "download")
	{
		header ("Content-Disposition: attachment; filename=$filename" );
	}



		 /************** BEGIN STANDARD EXPORT SEARCH CODE *********************/


		 # set the field list for this method:
		 $arr = $construct->method["$type"];

		 # loop through the field list to create the sql queries
		 $arr = $construct->method[$type];
		 $field_list = '';
		 $i=0;
		 while (list ($key, $value) = each ($arr))
		 {
			 if($i == 0)
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 else
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 $i++;
		 }

		# get the search details:
		if(isset($VAR['search_id']))
		{
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		}
		else
		{
			echo '<BR> The search terms submitted were invalid!';
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "")
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		}
		else
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
			$smarty_order =  $search->order_by;
		}

		# format saved search string
		$sql = explode (" WHERE ", $search->sql);

		# generate the full query
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . $construct->table.".site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;
		$db = &DB();

		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('core:export.inc.php','search_xml', $db->ErrorMsg() . '<br><br>' .$q);
			echo "An SQL error has occured!";
			return;
		}

		# put the results into an array
		$i=0;
		$class_name = TRUE;
		$results = '';
		while (!$result->EOF) {
			$results[$i] = $result->fields;
			   $result->MoveNext();
			   $i++;
		}

		# get any linked fields
		if($i > 0)
		{
			$this->result = $results;
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($results, $this->linked);
		}
		else
		{
			$this->result = $results;
		}

		/************** END STANDARD EXPORT SEARCH CODE *********************/



	# LOOP THROUGH THE RESULTS & DISPLAY AS CSV
	for($ii = 0; $ii < count($this->result); $ii++)
	{
		# print the heading:
		if($ii == 0)
		{
			reset($arr);
			$total_fields = count($arr) - 1;
			$iii = 0;
			while (list ($key, $value) = each ($arr))
			{
				 $field = $C_translate->translate('field_'.$value, $construct->module,'');
				 echo '"' . $field . '"';
				 if($total_fields > $iii) echo ',';
				 $iii++;
			}
		}	



		# new line
		echo '
';				
		# print the data for each cell:
		reset($arr);
		$total_fields = count($arr) - 1;
		$iii = 0;
		while (list ($key, $value) = each ($arr))
		{
			if  ($construct->field["$value"][convert] == 'date'      ||
				 $construct->field["$value"][convert] == 'time'      ||
				 $construct->field["$value"][convert] == 'date-now'  ||
				 $construct->field["$value"][convert] == 'date-time' )
			{
				 $data = date(UNIX_DATE_FORMAT,$this->result["$ii"]["$value"]);
			} else {
				 $data = $this->result["$ii"]["$value"];
			}

			echo '"' . $data . '"';
			if($total_fields > $iii) echo ',';
			$iii++;
		}
	}	
	exit();
}	









########################################
##	ADMIN SEARCH TAB EXPORT        #####
########################################

function search_tab($VAR, $construct, $type)
{
	global $C_translate;
	include_once(PATH_ADODB  . '/toexport.inc.php');		
	$filename = 'Export.txt';


	# determine what action to take:
	# inline display, download, email, web save    	

	if($VAR["type"] == "display")
	{
	   header ('Content-type: application/x-txt');
	   header ("Content-Disposition: inline; filename=$filename" );
	}

	else if($VAR["type"] == "download")
	{
		header ("Content-Disposition: attachment; filename=$filename" );
	}

		/************** BEGIN STANDARD EXPORT SEARCH CODE *********************/


		   # set the field list for this method:
		 $arr = $construct->method["$type"];

		 # loop through the field list to create the sql queries
		 $arr = $construct->method[$type];
		 $field_list = '';
		 $i=0;
		 while (list ($key, $value) = each ($arr))
		 {
			 if($i == 0)
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 else
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 $i++;
		 }

		# get the search details:
		if(isset($VAR['search_id']))
		{
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		}
		else
		{
			echo '<BR> The search terms submitted were invalid!';
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "")
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		}
		else
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
			$smarty_order =  $search->order_by;
		}

		# format saved search string
		$sql = explode (" WHERE ", $search->sql);

		# generate the full query
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . $construct->table.".site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;
		$db = &DB();

		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('core:export.inc.php','search_xml', $db->ErrorMsg() . '<br><br>' .$q);
			echo "An SQL error has occured!";
			return;
		}

		# put the results into an array
		$i=0;
		$class_name = TRUE;
		$results = '';
		while (!$result->EOF) {
			$results[$i] = $result->fields;
			   $result->MoveNext();
			   $i++;
		}

		# get any linked fields
		if($i > 0)
		{
			$this->result = $results;
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($results, $this->linked);
		}
		else
		{
			$this->result = $results;
		}

		/************** END STANDARD EXPORT SEARCH CODE *********************/




	# LOOP THROUGH THE RESULTS & DISPLAY AS CSV
	for($ii = 0; $ii < count($this->result); $ii++)
	{
		# print the heading:
		if($ii == 0)
		{
			reset($arr);
			$total_fields = count($arr) - 1;
			$iii = 0;
			while (list ($key, $value) = each ($arr))
			{
				 $data = $C_translate->translate('field_'.$value, $construct->module,'');
				 echo $data;
				 if($total_fields > $iii) echo '    ';
				 $iii++;
			}
		}



		# new line
		echo '
';				
		# print the data for each cell:
		reset($arr);
		$total_fields = count($arr) - 1;
		$iii = 0;
		while (list ($key, $value) = each ($arr))
		{
			if  ($construct->field["$value"][convert] == 'date'      ||
				 $construct->field["$value"][convert] == 'time'      ||
				 $construct->field["$value"][convert] == 'date-now'  ||
				 $construct->field["$value"][convert] == 'date-time' )
			{
				 $data = date(UNIX_DATE_FORMAT,$this->result["$ii"]["$value"]);
			} else {
				 $data = $this->result["$ii"]["$value"];
			}

			echo addslashes($data);
			if($total_fields > $iii) echo ' ';
			$iii++;
		}
	}	
	exit();
}








	########################################
	##	ADMIN SEARCH XML EXPORT        #####
	########################################

	function search_xml($VAR, $construct, $type)
	{

		/************** BEGIN STANDARD EXPORT SEARCH CODE *********************/


		# set the field list for this method:
		$arr = $construct->method["$type"];

		# loop through the field list to create the sql queries
		 $arr = $construct->method[$type];
		 $field_list = '';
		 $i=0;
		 while (list ($key, $value) = each ($arr))
		 {
			 if($i == 0)
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= AGILE_DB_PREFIX . $construct->table . '.' . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 else
			 {
				 $field_var =  $construct->table . '_' . $value;
				 $field_list .= "," . AGILE_DB_PREFIX . $construct->table . "." . $value;

				// determine if this record is linked to another table/field
				if($construct->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $construct->field[$value]["asso_table"], 'link_field' => $construct->field[$value]["asso_field"]);
				}
			 }
			 $i++;
		 }

		# get the search details:
		if(isset($VAR['search_id']))
		{
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		}
		else
		{
			echo '<BR> The search terms submitted were invalid!';
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "")
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		}
		else
		{
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . $construct->table . '.' .  $construct->order_by;
			$smarty_order =  $search->order_by;
		}

		# format saved search string
		$sql = explode (" WHERE ", $search->sql);

		# generate the full query
		$q = preg_replace("/%%fieldList%%/i", $field_list, $search->sql);
		$q = preg_replace("/%%tableList%%/i", AGILE_DB_PREFIX.$construct->table, $q);
		$q = preg_replace("/%%whereList%%/i", "", $q);
		$q .= " ".AGILE_DB_PREFIX . $construct->table.".site_id = '" . DEFAULT_SITE . "'";
		$q .= $order_by;
		$db = &DB();

		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('core:export.inc.php','search_xml', $db->ErrorMsg() . '<br><br>' .$q);
			echo "An SQL error has occured!";
			return;
		}

		# put the results into an array
		$i=0;
		$class_name = TRUE;
		$results = '';
		while (!$result->EOF) {
			$results[$i] = $result->fields;
			$result->MoveNext();
			$i++;
		}

		# get any linked fields
		if($i > 0)
		{
			$this->result = $results;
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($results, $this->linked);
		}
		else
		{
			$this->result = $results;
		}

		/************** END STANDARD EXPORT SEARCH CODE *********************/





		# create the xml processing instruction
		# header("Content-type: text/xml");
		$filename = 'XML_Export.xml';

		# determine what action to take:
		if($VAR["type"] == "display") {
		   header ('Content-type: application/x-xml');
		   header ("Content-Disposition: inline; filename=$filename" );
		}

		else if($VAR["type"] == "download") {
			header ("Content-Disposition: attachment; filename=$filename" );
		}

		$_xml ="<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\r\n";
		$_xml .="<results>\r\n";

		# loop through the resulsts and display as PDF
		$row = 0;
		for($ii = 0; $ii < count($this->result); $ii++)
		{
			$_xml .= "	<" . $construct->table . ">\r\n";

			# get the data for each cell:
			reset($arr);
			$column=0;
			while (list ($key, $value) = each ($arr))
			{
				if  ($construct->field["$value"][convert] == 'date'      ||
					 $construct->field["$value"][convert] == 'time'      ||
					 $construct->field["$value"][convert] == 'date-now'  ||
					 $construct->field["$value"][convert] == 'date-time' )
					 {
						$date = date(UNIX_DATE_FORMAT,$this->result["$ii"]["$value"]);
						$data = htmlspecialchars($date,0,'ISO8859-1');
						//$data = test;
					 } else {
						$data = htmlspecialchars($this->result["$ii"]["$value"],0,'ISO8859-1');
					 }
				$_xml .= "		<$value>" . $data . "</$value>\r\n";
			}

			# Next record
			$_xml .= "	</" . $construct->table . ">\r\n";

		}
		$_xml .="</results>\r\n";
		echo $_xml;	
		exit();
	}
}



// ----- begin of excel export function library -----
// Excel begin of file header
function xlsBOF() {
	echo pack("ssssss", 0x809, 0x8, 0x0, 0x10, 0x0, 0x0);
	return;
}

// Excel end of file footer
function xlsEOF() {
   echo pack("ss", 0x0A, 0x00);
   return;
}


// Function to write a Number (double) into Row, Col
function xlsWriteNumber($Row, $Col, $Value) {
	echo pack("sssss", 0x203, 14, $Row, $Col, 0x0);
	echo pack("d", $Value);
	return;
}


// Function to write a label (text) into Row, Col
 function xlsWriteLabel($Row, $Col, $Value ) {
	$L = strlen($Value);
	echo pack("ssssss", 0x204, 8 + $L, $Row, $Col, 0x0, $L);
	echo $Value;
	return;
}	
?>