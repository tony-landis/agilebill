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

class affiliate_commission
{
	# Open the constructor for this mod
	function affiliate_commission()
	{
		# name of this module:
		$this->module = "affiliate_commission";

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
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$search_limit = 50;

		global $C_debug, $C_translate; 
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;

		$this->start_date = false;
		if(!empty($VAR['affiliate_commission_start_date'])) $this->start_date = $validate->convert('', $VAR['affiliate_commission_start_date'], 'date');

		$this->end_date=false;
		if(!empty($VAR['affiliate_commission_end_date'])) $this->end_date = $validate->convert('', $VAR['affiliate_commission_end_date'], 'date');

		# determine the offset & limit
		if(!empty($VAR['page'])) {
			$current_page = $VAR['page'];
		} else {
			$current_page = '1';
		} 

		# determine the offset & limit
		$offset=-1;
		if($current_page == 1) {
			$offset = 0;
		} else {
			$offset =  (($current_page * $search_limit) - $search_limit);
		}

		$db = &DB();
		if($current_page == 1)
			$this->GenID = $db->GenID(AGILE_DB_PREFIX . 'affiliate_commission_id');
		else
			$this->GenID = @$VAR['GenID'];

		# Generate the SQL for this commission generation session:		
		$sql = "SELECT id,affiliate_id,total_amt,tax_amt,type FROM ".AGILE_DB_PREFIX."invoice WHERE site_id = ".$db->qstr(DEFAULT_SITE)." AND
				process_status = 1 AND billing_status = 1 AND
				( affiliate_id IS NOT NULL AND affiliate_id !='' ) AND 
				total_amt > 0 "; 
		if($this->start_date) $sql .=	" AND date_orig	>= ".$db->qstr($this->start_date);				
		if($this->end_date) $sql .=	" AND date_orig	<= ".$db->qstr($this->end_date);        		
		$result = $db->SelectLimit($sql, $search_limit, $offset);

		#echo $sql;
		#echo "<BR><BR>";
		#print_r($result->fields);
		#exit;

		# No more results - print link to export data:
		if($result->RecordCount() == 0 && $current_page > 1) { 
			$msg = $C_translate->translate('generated','affiliate_commission','');    
			$msg .= '&nbsp;&nbsp;&nbsp; <a href="?_page=affiliate_commission:view&id='.$this->GenID.'">'.$C_translate->translate('submit','','').'</a>';    					
			$msg .= '<SCRIPT LANGUAGE="JavaScript"> 
					refresh("1", "?_page=affiliate_commission:view&id='.$this->GenID.'");
					</SCRIPT>';

			$C_debug->alert($msg);
			return;
		}

		# Loop through the results:
		$count = 0;
		while (!$result->EOF) 
		{
			$do = true;
			$level = 1; 
			unset($affiliate_arr);    			
			$affiliate_id = $result->fields["affiliate_id"]; 

			# check if the commissions for this invoice have already been issued... 
			$sql = "SELECT id FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
					site_id			= ".$db->qstr(DEFAULT_SITE)." AND
					invoice_id		= ".$db->qstr($result->fields["id"]);
			$result2 = $db->Execute($sql);
			if ($result2->RecordCount() == 0) 
			{

				# loop through all affiliate levels to generate the commissions...
				while ( $level < 100 && @$affiliate_id)
				{ 
					for($i=0; $i<count(@$affiliate_arr); $i++) 
					{
						if ($affiliate_arr[$i] == $affiliate_id) { $i=100; $do = false; }
					}

					if($do) 
					{
						// get the total amount of the items after discounts and before taxes
						$sqli = "SELECT sum(total_amt) as invoice_amount
								 FROM ".AGILE_DB_PREFIX."invoice_item WHERE site_id = ".$db->qstr(DEFAULT_SITE)." 
								 AND invoice_id = {$result->fields["id"]}
								 GROUP BY invoice_id";
						$rsi=$db->Execute($sqli);
						if($rsi && $rsi->RecordCount()) {
							$invoice_amount = $rsi->fields["invoice_amount"];
							$arr = $this->calc_commission($affiliate_id, $invoice_amount, $result->fields["type"], $level);
							if (@$arr["amount"] > 0)
								$this->add_invoice_commission($arr['amount'], $affiliate_id, $result->fields["id"]); 
							unset($affiliate_id); 
							if (isset($arr["affiliate_id"]))
								$affiliate_id = $arr['affiliate_id'];

							$count++;
						}
					}
					$level++;
				}                       
			}

			# add this invoice to the processed list...
			$result->MoveNext();
		} 		

		# Create the affiliate_commission record:
		if($count == 0) 
		{   
			$msg = $C_translate->translate('no_results','affiliate_commission','');     		
			$C_debug->alert($msg);    			
		} 
		else 
		{ 
			$start = ($search_limit * $current_page) - $search_limit;
			$stop  = ($search_limit * $current_page);  
			$page  = $current_page + 1;  
			$C_translate->value['affiliate_commission']['start']  = $start;
			$C_translate->value['affiliate_commission']['stop']   = $stop;
			$C_translate->value['affiliate_commission']['genid']  = $this->GenID; 
			$C_translate->value['affiliate_commission']['page']   = $page; 
			$C_translate->value['affiliate_commission']['unixtime_start_date'] = $this->start_date;
			$C_translate->value['affiliate_commission']['unixtime_stop_date']  = $this->end_date;	    		

			$msg = $C_translate->translate('continue','affiliate_commission','');
			$url =  '?_page=core:blank&do[]=affiliate_commission:add&GenID='.$this->GenID.
					'&page='.$page.
					'&affiliate_commission_start_date='.@$VAR['affiliate_commission_start_date'].
					'&affiliate_commission_start_date='.@$VAR['affiliate_commission_start_date']; 
			$msg .= '&nbsp;&nbsp;&nbsp; <a href="'.$url.'">'.$C_translate->translate('submit','','').'</a>';  	  
			$msg .= '<script language="JavaScript">document.location = "'.$url.'";</script>';	 
			$C_debug->alert($msg);
		}
	}


	##############################
	##	CALCULATE COMMISSIONS   ##
	##############################
	function calc_commission( $affiliate_id, $amount, $type, $level )
	{ 
		$db = &DB();
		$sql = "SELECT * FROM ".AGILE_DB_PREFIX."affiliate WHERE
				site_id			= ".$db->qstr(DEFAULT_SITE)." AND
				id				= ".$db->qstr($affiliate_id);
		$result = $db->Execute($sql);
		if ($result->RecordCount() == 0) return false;

		# can affiliate recieve commissions from this level?
		if($result->fields['max_tiers'] >= $level) 
		{ 
			## Order Type: 0 = new, 1 = recurring charge
			if($type == 0) { 
				$rate = unserialize($result->fields['new_commission_rate']);
				$calc = $result->fields['new_commission_type'];   				    					 
			} else {
				$rate = unserialize($result->fields['recurr_commission_rate']);
				$calc = $result->fields['recurr_commission_type'];    					
			} 
			$i = $level - 1; 
			$amount2 = $rate[$i] * $amount;	   				
			$ret['amount'] = $amount2; 
		} 
		else 
		{
			$ret['amount'] = false;
		}

		# get the parent affiliate id, if any:
		if ( $result->fields['parent_affiliate_id'] != $affiliate_id)
			$ret['affiliate_id'] = $result->fields['parent_affiliate_id'];

		return $ret;	        	        	
	}


	##############################
	##	INCREMENT COMMISSION    ##
	##############################
	function add_invoice_commission( $amount, $affiliate_id, $invoice_id )
	{         	
		$db = &DB();
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_commission_id');
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."invoice_commission SET
				id						= ".$db->qstr($id).",
				site_id					= ".$db->qstr(DEFAULT_SITE).",
				date_orig				= ".$db->qstr(time()).",
				date_last				= ".$db->qstr(time()).",
				affiliate_commission_id	= ".$db->qstr($this->GenID).",
				invoice_id				= ".$db->qstr($invoice_id).",
				affiliate_id			= ".$db->qstr($affiliate_id).",
				commission				= ".$db->qstr($amount).",
				status					= ".$db->qstr("0");
		$result = $db->Execute($sql);

		### Increment the affiliate_commission totals: 
		$sql = "SELECT commissions FROM ".AGILE_DB_PREFIX."affiliate_commission WHERE
				id						= ".$db->qstr($this->GenID)." AND
				site_id					= ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql); 
		if($result->RecordCount() > 0)
		{ 
			### Update the affiliate_commission total:
			if( $result->fields['commissions'] > 0 )
				$current = $result->fields['commissions'] + $amount;
			else
				$current = $amount; 

			$sql = "UPDATE ".AGILE_DB_PREFIX."affiliate_commission  
					SET
					commissions				= ".$db->qstr($current)." 
					WHERE
					id						= ".$db->qstr($this->GenID)." AND
					site_id					= ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($sql); 
		}
		else
		{
			### Create new affiliate_commission record
			$sql = "INSERT INTO ".AGILE_DB_PREFIX."affiliate_commission SET
					id						= ".$db->qstr($this->GenID).",
					site_id					= ".$db->qstr(DEFAULT_SITE).",
					date_orig				= ".$db->qstr(time()).",
					date_begin				= ".$db->qstr($this->start_date).",
					date_end				= ".$db->qstr($this->end_date).",
					commissions				= ".$db->qstr($amount); 					 
			$result = $db->Execute($sql);   					
		}   				 			
	}




	##############################
	##	GET ARRAY OF AFFILIATES ##
	##############################
	function plugin_affiliate_list($plugin_name, $affiliate_commission_id)
	{
		# Get each affiliate in this commission:
		$db = &DB();
		$sql = "SELECT DISTINCT affiliate_id FROM ".AGILE_DB_PREFIX."invoice_commission WHERE
				affiliate_commission_id	= ".$db->qstr($affiliate_commission_id)." AND
				site_id					= ".$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);     			     			
		if ($result->RecordCount() == 0) return false; 
		while(!$result->EOF) {
			# check if the affiliate is using plugin_name specified:
			$sql = "SELECT id FROM ".AGILE_DB_PREFIX."affiliate WHERE
					id						= ".$db->qstr($result->fields['affiliate_id'])." AND
					affiliate_plugin		= ".$db->qstr($plugin_name)." AND
					site_id					= ".$db->qstr(DEFAULT_SITE);
			$result2 = $db->Execute($sql);      	
			if ($result2->RecordCount() >= 1) {
				$arr[] = $result->fields['affiliate_id'];
			}		
			$result->MoveNext();		
		}      					
		if(count(@$arr) > 0)
			return $arr;
		else
			return false;
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


		#################################################
		# Get the data for each affiliate plugin:
		global $C_translate, $smarty; 
		$id = @$VAR['id']; 

		$path = PATH_PLUGINS . '/affiliate/';
		$pre = '';
		$ext = '.php';

		$count = 0;			
		chdir($path);
		$dir = opendir($path);
		while ($file_name = readdir($dir))
		{
			$display = true;
			if($file_name != '..' && $file_name != '.')
			{
				if(!empty($ext))
				{
					$cute = eregi_replace($ext.'$', "", $file_name);
					if(!eregi($ext.'$', $file_name)) $display = false;
				}
				if(!empty($pre))
				{
					$cute = eregi_replace('^'.$pre, "", $cute);
					if(!eregi('^'.$pre, $file_name))  $display = false;
				}
				if($display)
				{  
					$name = $cute; 
					if($arr_count = $this->plugin_affiliate_list($cute, $id))
						$count = count($arr_count);
					else 
						$count = 0;  
					$cute = eregi_replace("_"," ",$cute);
					$cute = eregi_replace("-"," ",$cute);  

					$smart[] = Array ( 'name' 		=> $cute,
										'plugin' 	=> $name,
										'count' 	=> $count); 
					$count++;
				}
			}
		}

		$smarty->assign('plugindata', $smart);					
	}		


	##############################
	## RUN AFFILIATE PLUGIN     ##
	##############################
	function export($VAR)
	{
		# make sure the required vars are set: id, plugin,
		if(!isset($VAR['id']) || !isset($VAR['plugin']))
			echo "Invalid parameters passed..";

		# Load the plugin
		$file   = $VAR['plugin'];
		include_once(PATH_PLUGINS . 'affiliate/'. $file.'.php');
		eval ( '$_PLUGIN = new plgn_aff_'. strtoupper ( $file ) . ';' ); 

		# Get each affiliate to be paid a commisson for this affiliate_commission ID
		$db = &DB();
		$export='';
		if($arr_count = $this->plugin_affiliate_list($VAR['plugin'], $VAR['id'])) {
			for($i=0; $i<count($arr_count); $i++) 
			{
				$sql = "SELECT sum(commission) as commission from ".AGILE_DB_PREFIX."invoice_commission 
						WHERE
						affiliate_commission_id	= ".$db->qstr($VAR['id'])." AND
						affiliate_id			= ".$db->qstr($arr_count[$i])." AND
						site_id					= ".$db->qstr(DEFAULT_SITE)."  
						group by affiliate_id"; 
				/* SELECT affiliate_id,sum(commission) as commission from ab_invoice_commission  group by affiliate_id */
				$result = $db->Execute($sql);     			     			

				# Run PLUGIN->commission to add the data to the return text	
				if($result && $result->RecordCount() && $result->fields['commission'] > 0)	        				   		
				$export .= $_PLUGIN->commission(round($result->fields['commission'],2), $arr_count[$i], $VAR['id']);			   					   		
			}	   

			# Run the PLUGIN->header method to set any req. headers.
			$_PLUGIN->header();

			# include detailed commission details?
			$detail = '';
			if(is_callable($_PLUGIN->commission_detailed)) 
			{ 
				$sql = "SELECT comm.affiliate_id, comm.invoice_id, comm.commission, invoice.date_orig, invoice.total_amt, account.first_name, account.last_name, affiliate.plugin_data from ".AGILE_DB_PREFIX."invoice_commission as comm
						join ".AGILE_DB_PREFIX."invoice as invoice on (invoice.id=comm.invoice_id)
						join ".AGILE_DB_PREFIX."account as account on (account.id=invoice.account_id)
						join ".AGILE_DB_PREFIX."affiliate as affiliate on (affiliate.id=comm.affiliate_id)
						WHERE
						comm.affiliate_commission_id	= ".$db->qstr($VAR['id'])." AND 
						comm.site_id					= ".$db->qstr(DEFAULT_SITE)."  
						order by comm.affiliate_id"; 
				/* SELECT affiliate_id,sum(commission) as commission from ab_invoice_commission  group by affiliate_id */
				$result = $db->Execute($sql);     			     			

				# Run PLUGIN->commission to add the data to the return text	
				if($result && $result->RecordCount()) $detail = $_PLUGIN->commission_detailed($result);		        	
			}

			# Print the data 
			echo $export . "\r\n\r\n". $detail;		        			       

		} else {
			echo 'An error occurred: no affiliates are associated with this plugin';
		} 
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
		$this->associated_DELETE[] = Array ('table' => 'invoice_commission', 'field' => 'affiliate_commission_id');           
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");

		# Delete all associated discounts:
		if(isset($VAR['id']))
		{
			$id = $VAR['id'];
			for($i=0; $i<count($id); $i++)
			{
				if($id[$i] != '')
				{
					$q = '%Affiliate Commission ID '. $id[$i].'%';
					$db = &DB();
					$sql = "DELETE FROM ".AGILE_DB_PREFIX."discount WHERE
						   notes     LIKE ".$db->qstr($q)." AND
						   site_id    = ".$db->qstr(DEFAULT_SITE);
					$result = $db->Execute($sql);
				}
			}
		}
		else
		{
			$id = $VAR['delete_id'];
			$q = '%Affiliate Commission ID '. $id.'%';
			$db = &DB();
			$sql = "DELETE FROM ".AGILE_DB_PREFIX."discount WHERE
				notes     LIKE ".$db->qstr($q)." AND
				site_id    = ".$db->qstr(DEFAULT_SITE);
			$result = $db->Execute($sql);
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