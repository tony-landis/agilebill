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
	
class campaign
{
	# Open the constructor for this mod
	function campaign()
	{ 
		# name of this module:
		$this->module = "campaign";

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
	##		CLICK    	        ##
	##############################
	function click($VAR)
	{ 
		### Set the forward URL
		if(!empty($VAR['caid']))
		{
			$db     = &DB();
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'campaign WHERE
					   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					   id        = ' . $db->qstr(@$VAR['caid']);
			$result = $db->Execute($sql);  
			if (strlen($result->fields['url']) >= 1) $url = $result->fields['url'];
		} 
		if(empty($url)) $url = URL;

		echo '<script language="JavaScript"> <!-- START
		document.location="'.$url.'";
		//  END -->
		</SCRIPT>';

		### LOG the click
		if(!isset($VAR['_log']) && !empty($VAR['caid']))
		{
			if(isset($VAR['file']))
			$file_no = $VAR['file'];
			else
			$file_no = '1';
			$count_field= 'clicked'.$file_no;
			$count      = $result->fields[$count_field] + 1;
			$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'campaign SET
						'.$count_field.' = ' . $db->qstr($count) . '  WHERE
						site_id     	= ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          	= ' . $db->qstr(@$VAR['caid']);
			$result = $db->Execute($sql);
		}
	}



	##############################
	##		DISPLAY  	        ##
	##############################
	function display($VAR)
	{

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'campaign WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr(@$VAR['id']);
		$result = $db->Execute($sql);
		if(isset($VAR['file']))
		$file_no = $VAR['file'];
		else
		$file_no = '1';
		$file       = PATH_FILES . 'campaign_' . $VAR['id'] . '_' . $file_no .'.dat';
		$type       = 'type'.$file_no;
		$name       = 'file'.$file_no;
		$count_field= 'served'.$file_no;
		$count      = $result->fields[$count_field] + 1;

		if($result->RecordCount() > 0)
		{
			### Open the file
			if (@$file=fopen($file, 'r'))
			{
				### Display the correct headers:
				header ("Content-type: " . $result->fields[$type]);
				header ("Content-Disposition: inline; filename=" . $result->fields[$name]);
				header ("Content-Description: PHP/INTERBASE Generated Data" );
				fpassthru($file);
				### Increment the file
				if(!isset($VAR['_log']))
				{
					$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'campaign SET
								'.$count_field.' = ' . $db->qstr($count) . '  WHERE
								site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
								id          = ' . $db->qstr(@$VAR['id']);
					$result = $db->Execute($sql);
				}
				exit;
			}
		}
		echo 'Sorry, the campaign or required file does not exist!';
	}



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
	   global $_FILES;


		# Validate the files
		for($i=1; $i<=12; $i++) {
			if(isset($_FILES['upload_file'.$i]) && $_FILES['upload_file'.$i]['size'] > 0) {
				$VAR['campaign_type'.$i]   = $_FILES['upload_file'.$i]['type'];
				$VAR['campaign_file'.$i]   = $_FILES['upload_file'.$i]['name'];            		
			}  
		}


		## Attempt to add the record: 
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$campaign_id = $db->add($VAR, $this, $type);


		### Copy the files & delete temp files
		if($campaign_id > 0) {
			for($i=1; $i<=12; $i++) {
				if(isset($_FILES['upload_file'.$i]) && $_FILES['upload_file'.$i]['size'] > 0)
				{
					if(!copy($_FILES['upload_file'.$i]['tmp_name'], PATH_FILES . 'campaign_'.$campaign_id.'_'.$i.'.dat'))
					{
						### ERROR:
					} 

					#unlink any temp files
					unlink($_FILES['upload_file'.$i]['tmp_name']);
				}
			}
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
		$smart = $db->view($VAR, $this, $type);

		#print_r($smart);
		for($i=0;$i<count($smart); $i++) 
		{
			# Get the stats for each advertisement:
			$budget = $smart[$i]['budget']; 

			# Total Impressions, Total Clicks, & Accounts referred
			$smart[$i]['impressions_total'] = 0;
			$smart[$i]['clicks_total'] = 0;
			$smart[$i]['accounts_referred'] = $this->referred_accounts($smart[$i]['id']); 

			# Invoices referred
			$inv = $this->referred_invoices($smart[$i]['id']); 
			$smart[$i]['invoices_referred'] = $inv[0]; 
			$smart[$i]['invoices_revenue'] = round( $inv[1], 2); 

			# total impressions & clicks:
			$impressions_total=0;
			$clicks_total=0;
			for($ii=1; $ii<=12; $ii++)
			{ 
				$impressions_total += $smart[$i]["served".$ii]; 
				$clicks_total += $smart[$i]["clicked".$ii];   		

				if(empty($smart[$i]["served".$ii]) && empty($smart[$i]["served".$ii]) && empty($smart[$i]["served".$ii]))
				$smart[$i]["show".$ii] = false;
				else
				$smart[$i]["show".$ii] = true;	
			}
			$smart[$i]['impressions_total'] = $impressions_total;
			$smart[$i]['clicks_total'] = $clicks_total;


			# Impr. percentage, Clicks percentage, CTR 
			$ctr_count = 0;   
			$ctr_amt = 0;			   		
			for($ii=1; $ii<=12; $ii++)
			{    
				# Impr. percentage
				if( $smart[$i]["show".$ii] ) 
				{
					$impressions_percentage = $smart[$i]["served".$ii] / $impressions_total; 
					$smart[$i]['impressions_percentage'.$ii] = "(".round($impressions_percentage*100)."%)"; 
				} else {
					$smart[$i]['impressions_percentage'.$ii] = ''; 
				}      					

				# Clicks percentage
				if( $smart[$i]["show".$ii] ) {
					@$clicks_percentage = ($smart[$i]["clicked".$ii] / $clicks_total); 
					$smart[$i]['clicks_percentage'.$ii] = "(".round($clicks_percentage*100)."%)"; 
				} else {
					$smart[$i]['clicks_percentage'.$ii] = ''; 
				}   

				# Cost
				if( $smart[$i]["show".$ii] ) {
					$impressions_percentage = $smart[$i]["served".$ii] / $impressions_total;
					$smart[$i]['cost'.$ii] = round($budget *  $impressions_percentage, 2);
				} else {
					$smart[$i]['cost'.$ii] = "0";
				}

				# CTR
				if( $smart[$i]["show".$ii] ) {
					$ctr = ( $smart[$i]["clicked".$ii] / $smart[$i]["served".$ii] ) * 100;	
					if($ctr < 0)
					$smart[$i]['ctr'.$ii] = round($ctr).'%';
					else 
					$smart[$i]['ctr'.$ii] = round($ctr,1).'%';
					$ctr_count++;
					$ctr_amt += $ctr;
				} else {
					$smart[$i]['ctr'.$ii] = "0%";
					$ctr_count++;
				}
			}

			# CTR Avg:
			if($ctr_count > 0)
				$smart[$i]['ctr_avg'] = round( $ctr_amt / $ctr_count, 2) .'%';
			else 
				$smart[$i]['ctr_avg'] = "0%";


			# CPC Avg:
			if($clicks_total > 0) {
				$cpc_avg =  $budget /  $clicks_total;
				if($cpc_avg < .1)
					$smart[$i]['cpc_avg'] = round($cpc_avg,3);
				else 
					$smart[$i]['cpc_avg'] = round($cpc_avg,2);
			} else {
				$smart[$i]['cpc_avg'] = 0;
			}

			# CPI Avg:
			if($impressions_total > 0) {
				 $cpi_avg = $budget / $impressions_total;
				 if($cpi_avg < .1)
					$smart[$i]['cpi_avg'] = round($cpi_avg,3);
				 else
					$smart[$i]['cpi_avg'] = round($cpi_avg,2);
			} else {
				$smart[$i]['cpi_avg'] = 0;
			}

			# Cost per Conversion:
			if($inv[0] > 0)
				$smart[$i]['conversion_cost'] = round($budget / $inv[0], 2);
			else
				$smart[$i]['conversion_cost'] = 0;

			# Avg sales amount:
			if($inv[0] > 0 && $budget > 0)
				$smart[$i]['invoice_avg'] = round($inv[1] / $inv[0], 2);
			else
				$smart[$i]['invoice_avg'] = 0;

			# ROI
			if($inv[0] > 0 && $budget > 0)
				$smart[$i]['roi'] = round(($inv[1] / $budget) * 100).'%';
			else
				$smart[$i]['roi'] = '---';    

			# Impression to Buy Ratio
			if($inv[0] > 0 && $impressions_total > 0) {
				$ratio = round($impressions_total / $inv[0]);  
				$percent = round( ($inv[0] / $impressions_total) * 100, 3);  			
				$smart[$i]['impr_to_buy'] = $ratio . ' / 1 &nbsp;&nbsp; ('.$percent.'%)';
			} else {
				$smart[$i]['impr_to_buy'] = '---';
			} 

			# Click-thru to Buy ratio
			if($inv[0] > 0 && $clicks_total > 0) {
				$ratio   = round($clicks_total / $inv[0]);    	
				$percent = round( ($inv[0] / $clicks_total) * 100, 2);
				$smart[$i]['click_to_buy'] = $ratio . ' / 1 &nbsp;&nbsp; ('.$percent.'%)';
			} else {
				$smart[$i]['click_to_buy'] = '---';
			}  
		}

		global $smarty;
		$smarty->assign('campaign', $smart);
	}	

	### Get the number of referred accounts:
	function referred_accounts ($id) {
		$dba = new CORE_database;
		$db = &DB();
		$sql = $dba->sql_select("account","id","campaign_id = $id", false, $db);
		$rs = $db->Execute($sql);
		return $rs->RecordCount();
	}

	### Get the number of referred invoices:
	function referred_invoices ($id) {
		$dba = new CORE_database;
		$db = &DB();
		$sql = $dba->sql_select("invoice","id,total_amt","campaign_id = $id", false, $db);
		$rs = $db->Execute($sql);
		if ($rs->RecordCount() == 0) {
			return Array(0,0);
		} else {
			$total = 0;
			while(!$rs->EOF) {
				$total += $rs->fields['total_amt'];
				$rs->MoveNext();
			}
		}
		return Array($rs->RecordCount(), $total);
	}    	

	##############################
	##	AFFILIATE VIEW  	    ##
	##############################
	function affiliate($VAR)
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
		# Validate the files
		for($i=1; $i<=12; $i++) {
			if(isset($_FILES['upload_file'.$i]) && $_FILES['upload_file'.$i]['size'] > 0) {
				$VAR['campaign_type'.$i]   = $_FILES['upload_file'.$i]['type'];
				$VAR['campaign_file'.$i]   = $_FILES['upload_file'.$i]['name'];            		
			}  
		}

		# Store the record
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$rs = $db->update($VAR, $this, $type);

		### Copy the files
		if($rs) {
			for($i=1; $i<=12; $i++) {
				if(isset($_FILES['upload_file'.$i]) && $_FILES['upload_file'.$i]['size'] > 0)
				{
					if(!copy($_FILES['upload_file'.$i]['tmp_name'], PATH_FILES . 'campaign_'.$VAR['campaign_id'].'_'.$i.'.dat'))
					{
						### ERROR:
					}
				}
			}
		} 
	}

	##############################
	##		 DELETE	 ADDs       ##
	##############################
	function delete_add($VAR)
	{	
		$field  = 'file'.$VAR['file'];
		$db     = &DB();
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'campaign
					SET
					'.$field.'  = \'\'
					WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($VAR["campaign_id"]);
		$result = $db->Execute($sql);

		error_reporting(0);
		if(!unlink(PATH_FILES . 'campaign_'.$VAR["campaign_id"].'_' . $VAR['file'] . '.dat'))
		{
			global $C_debug;
			$C_debug->error('file.inc.php','delete', PATH_FILES . 'file_'.$id[$i].
			'.dat does not exist for deletion. File could not be deleted.');
		}
		# Restore the proper error level
		$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
		eval($error_reporting_eval);
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
					site_id = '" . DEFAULT_SITE . "'";
			# execute the query
			$result = $db->Execute($q);


			# error reporting
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('campaign.inc.php','delete', $db->ErrorMsg());                   	        	
			}
			else
			{
				for($i=0; $i<count($id); $i++)
				{
					if($id[$i] != '')
					{
						error_reporting(0);

						for($ii=1; $ii<=12; $ii++) 
							unlink(PATH_FILES . 'campaign_'.$id[$i].'_'.$ii.'.dat'); 

						# Restore the proper error level
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
		$smart = $db->search_show($VAR, $this, $type);

		for($i=0; $i<count($smart); $i++)
		{  
			# Get the stats for each advertisement:
			$budget = $smart[$i]['budget']; 


			# Invoices referred
			$inv = $this->referred_invoices($smart[$i]['id']); 
			$smart[$i]['invoices_referred'] = $inv[0]; 
			$smart[$i]['invoices_revenue'] = $inv[1];
			@$smart[$i]['invoice_avg'] = $inv[1] / $inv[0]; 

			# total impressions & clicks:
			$impressions_total=0;
			$clicks_total=0;
			for($ii=1; $ii<=12; $ii++)
			{ 
				$impressions_total += $smart[$i]["served".$ii]; 
				$clicks_total += $smart[$i]["clicked".$ii];   		

				if(empty($smart[$i]["served".$ii]) && empty($smart[$i]["served".$ii]) && empty($smart[$i]["served".$ii]))
				$smart[$i]["show".$ii] = false;
				else
				$smart[$i]["show".$ii] = true;	
			}
			$smart[$i]['impressions_total'] = $impressions_total;
			$smart[$i]['clicks_total'] = $clicks_total;


			# Impr. percentage, Clicks percentage, CTR 
			$ctr_count = 0;   
			$ctr_amt = 0;			   		
			for($ii=1; $ii<=12; $ii++)
			{      
				# CTR
				if( $smart[$i]["show".$ii] ) {
					$ctr = ( $smart[$i]["clicked".$ii] / $smart[$i]["served".$ii] ) * 100;	
					if($ctr < 0)
					$smart[$i]['ctr'.$ii] = round($ctr).'%';
					else 
					$smart[$i]['ctr'.$ii] = round($ctr,1).'%';
					$ctr_count++;
					$ctr_amt += $ctr;
				} else {
					$smart[$i]['ctr'.$ii] = "0%";
					$ctr_count++;
				}
			}

			# CTR Avg:
			if($ctr_count > 0)
				$smart[$i]['ctr_avg'] = round( $ctr_amt / $ctr_count, 2) .'%';
			else 
				$smart[$i]['ctr_avg'] = "0%";


			# CPC Avg:
			if($clicks_total > 0) {
				$cpc_avg =  $budget /  $clicks_total;
				if($cpc_avg < .1)
					$smart[$i]['cpc_avg'] = round($cpc_avg,3);
				else 
					$smart[$i]['cpc_avg'] = round($cpc_avg,2);
			} else {
				$smart[$i]['cpc_avg'] = 0;
			}

			# CPI Avg:
			if($impressions_total > 0) {
				 $cpi_avg = $budget / $impressions_total;
				 if($cpi_avg < .1)
					$smart[$i]['cpi_avg'] = round($cpi_avg,3);
				 else
					$smart[$i]['cpi_avg'] = round($cpi_avg,2);
			} else {
				$smart[$i]['cpi_avg'] = 0;
			}

			# Cost per Conversion:
			if($inv[0] > 0 && $budget > 0)
				$smart[$i]['conversion_cost'] = round($budget / $inv[0], 2);
			else
				$smart[$i]['conversion_cost'] = 0;

			# ROI
			if($inv[0] > 0 && $budget > 0)
				$smart[$i]['roi'] = round(($inv[1] / $budget) * 100).'%';
			else
				$smart[$i]['roi'] = '---';     
		}

		global $smarty;
		$smarty->assign('campaign', $smart);


	}	
	##############################
	##	   SEARCH EXPORT        ##
	##############################    	
	function search_export($VAR)
	{
	   # require the export class    	
	   require_once (PATH_CORE   . "export.inc.php");

	   # Call the correct export function for inline browser display, download, email, or web save.
	   if($VAR["format"] == "excel")
	   {
		   $type = "export_excel";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_excel($VAR, $this, $type);    	
	   }

	   else if ($VAR["format"] == "pdf")
	   {
		   $type = "export_pdf";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_pdf($VAR, $this, $type);      	
	   }

	   else if ($VAR["format"] == "xml")
	   {
		   $type = "export_xml";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_xml($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "csv")
	   {
		   $type = "export_csv";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_csv($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "tab")
	   {
		   $type = "export_tab";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_tab($VAR, $this, $type);
	   }                                           	
	}		
}
?>