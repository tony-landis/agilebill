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
	
class newsletter_subscriber
{

	# Open the constructor for this mod
	function newsletter_subscriber()
	{
		# name of this module:
		$this->module = "newsletter_subscriber";

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
	##	GRAPH STATISTICS        ##
	##############################
	function graph($start, $end, $constraint, $default)
	{
		global $C_translate;


		$db     = &DB();
		$sql    = 'SELECT date_orig FROM ' . AGILE_DB_PREFIX . 'newsletter_subscriber WHERE
					site_id     =  ' . $db->qstr(DEFAULT_SITE) . ' AND
					date_orig   >= ' . $db->qstr($start) . ' AND
					date_orig   <= ' . $db->qstr($end);

		$result = $db->Execute($sql);
		if($result->RecordCount() == 0)
		{
			$arr['title']   = $C_translate->translate('search_no_results','','');
			$arr['results'] = $default;
			return $arr;
		}
		$ii=0;
		while(!$result->EOF)
		{
			$d = $result->fields['date_orig'];
			for($i=0; $i<count($constraint); $i++)
			{
				if($d >= $constraint[$i]["start"] && $d < $constraint[$i]["end"])
				$default[$i]++;
				$ii++;
			}
			$result->MoveNext();
		}

		$C_translate->value['newsletter_subscriber']['count'] = $result->RecordCount();
		$title = $C_translate->translate('statistics','newsletter_subscriber','');
		$arr['title']   = $title;
		$arr['results'] = $default;
		return $arr;
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

		### Set the static vars:
		if($id)
		{
			global $smarty;
			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;     		
			$static_var->add($VAR, $this->module, $id);
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

		### Get the static vars:
		global $smarty;
		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var; 
		$ids = explode(',',$VAR['id']);    
		$arr = $static_var->update_form($this->module, 'update', $ids[0]);  
		if(gettype($arr) == 'array') 
		$smarty->assign('static_var', $arr); 
		else 
		$smarty->assign('static_var', false);    		
	}		

	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		if($db->update($VAR, $this, $type))
		{    		
			### Update the static vars:
			require_once(PATH_CORE   . 'static_var.inc.php');
			$static_var = new CORE_static_var;
			$static_var->update($VAR, 'newsletter_subscriber', $VAR['id']);  
		}
	}


	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$db = new CORE_database;
		 $db->mass_delete($VAR, $this, "");
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

		$db = &DB();	

		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;

		# set the search criteria array
		$arr = $VAR;

		# loop through the submitted field_names to get the WHERE statement
		$where_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(eregi($pat, $key))
					{	 				
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " WHERE ".AGILE_DB_PREFIX."newsletter_subscriber.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										$VAR['field_option']["$pat_field"]["$i_arr"];
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];
										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}


										if($i_arr == 0)
										{
											$where_list .= " WHERE ".AGILE_DB_PREFIX."newsletter_subscriber.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
										   $where_list .= " AND ".AGILE_DB_PREFIX."newsletter_subscriber.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										   $i++;
										}
								   }
								}
							}
							else
							{	
							   $where_list .= " WHERE ".AGILE_DB_PREFIX."newsletter_subscriber.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
			else
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(eregi($pat, $key))
					{
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " AND ".AGILE_DB_PREFIX."newsletter_subscriber.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];

										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}

										$where_list .= " AND ".AGILE_DB_PREFIX."newsletter_subscriber.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
								   }
								}
							}
							else
							{		
							   $where_list .=  " AND ".AGILE_DB_PREFIX."newsletter_subscriber.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
		}

		#### finalize the WHERE statement
		if($where_list == '')
		{
			$where_list .= ' WHERE ';	 		
		}
		else
		{
			$where_list .= ' AND ';
		}


		# get limit type
		if(isset($VAR['limit']))
		{
			$limit = $VAR['limit'];
		}
		else
		{
			$limit = $this->limit;
		}

		# get order by
		if(isset($VAR['order_by']))
		{
			$order_by = $VAR['order_by'];
		}
		else
		{
			$order_by = $this->order_by;
		}

		$pre = AGILE_DB_PREFIX;	


		$q = "SELECT DISTINCT ".AGILE_DB_PREFIX."newsletter_subscriber.id FROM ".AGILE_DB_PREFIX."newsletter_subscriber ";
		$q_save = "SELECT DISTINCT %%fieldList%% FROM ".AGILE_DB_PREFIX."newsletter_subscriber ";


		######## GET ANY STATIC VARS TO SEARCH ##########
		$join_list = ''; 
		if(!empty($VAR["static_relation"]) && count( $VAR["static_relation"] > 0 )) {  
			while(list($idx, $value) = each ($VAR["static_relation"])) {
				if($value != "") {

					$join_list .= " INNER JOIN {$pre}static_var_record AS s{$idx} ON 
						( 
							s{$idx}.record_id = {$pre}{$this->table}.id
							AND
							s{$idx}.static_var_relation_id = '{$idx}'
							AND
							s{$idx}.site_id = ".$db->qstr(DEFAULT_SITE)."		        				
							AND";
					if(ereg("%", $value))
						$join_list .= " s{$idx}.value LIKE ".$db->qstr($VAR["static_relation"]["$idx"]);
					else
						$join_list .= " s{$idx}.value = ".$db->qstr($VAR["static_relation"]["$idx"]);
					$join_list .= " ) "; 
				}
			}  
		}  
		######## END STATIC VAR SEARCH ##################


		# standard where list
		$q .= $join_list . $where_list ." ".AGILE_DB_PREFIX."newsletter_subscriber.site_id = " . $db->qstr(DEFAULT_SITE);
		$q_save .= $join_list . $where_list ." %%whereList%% ";


		################## DEBUG ##################
		#echo "<pre>" . $q;
		#echo "<BR><BR>" . $q_save;
		#exit;

		# run the database query
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('newsletter_subscriber.inc.php','search', $db->ErrorMsg());	  
			return false;      	                    	       
		}

		# get the result count:
		$results = $result->RecordCount();

		# get the first record id:
		if($results == 1)  $record_id = $result->fields['id'];

		# define the DB vars as a Smarty accessible block
		global $smarty; 

		# Create the definition for fast-forwarding to a single record:
		if ($results == 1 && !isset($this->fast_forward))
		{
			$smarty->assign('record_id', $record_id);
		}

		# create the search record:
		if($results > 0)
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module'] 	= $this->module;
			$arr['sql']		= $q_save;
			$arr['limit']  	= $limit;
			$arr['order_by']= $order_by;
			$arr['results']	= $results;
			$search->add($arr);

			# define the search id and other parameters for Smarty
			$smarty->assign('search_id', $search->id);

			# page:
			$smarty->assign('page', '1');

			# limit:
			$smarty->assign('limit', $limit);

			# order_by:
			$smarty->assign('order_by', $order_by);
		}

		# define the result count
		$smarty->assign('results', $results);    
	}



	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{	         	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		# set the field list for this method:
		$arr = $this->method[$type];

		$field_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= AGILE_DB_PREFIX . "newsletter_subscriber" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "newsletter_subscriber" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			$i++;
		}  


		# get the search details:
		if(isset($VAR['search_id'])) {
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		} else {
			# invalid search!
			echo '<BR> The search terms submitted were invalid!';       # translate... # alert

			if(isset($this->trigger["$type"])) {
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "") {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'newsletter_subscriber.'.$VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . AGILE_DB_PREFIX . 'newsletter_subscriber.'.$this->order_by;
			$smarty_order =  $search->order_by;
		}


		# determine the sort order
		if(isset($VAR['desc'])) {
			$order_by .= ' DESC';
			$smarty_sort = 'desc=';
		} else if(isset($VAR['asc']))  {
			$order_by .= ' ASC';
			$smarty_sort = 'asc=';
		} else {
			if (!eregi('date',$smarty_order)) {
				$order_by .= ' ASC';
				$smarty_sort = 'asc=';
			} else {
				$order_by .= ' DESC';
				$smarty_sort = 'desc=';
			}
		}

		# determine the offset & limit
		$current_page=1;
		$offset=-1;
		if (!empty($VAR['page'])) $current_page = $VAR['page'];
		if (empty($search->limit)) $search->limit=25; 
		if($current_page>1) $offset = (($current_page * $search->limit) - $search->limit);

		# generate the full query 
		$db = &DB();
		$q = eregi_replace("%%fieldList%%", $field_list, $search->sql);
		$q = eregi_replace("%%tableList%%", AGILE_DB_PREFIX.$construct->table, $q);
		$q = eregi_replace("%%whereList%%", "", $q);
		$q .= " ".AGILE_DB_PREFIX . "newsletter_subscriber."."site_id = " . $db->qstr(DEFAULT_SITE);
		$q .= $order_by;

		//////////////////
		#echo "<BR><pre> $q </pre><BR>";

		$result = $db->SelectLimit($q, $search->limit);

		# error reporting
		if ($result === false)
		{		
			global $C_debug;
			$C_debug->error('newsletter_subscriber.inc.php','search_show', $db->ErrorMsg());

			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			} 
			return;                    	        	
		}


		# put the results into a smarty accessable array  
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF) {
			$smart[$i] = $result->fields;

			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}
			$result->MoveNext();
			$i++;
		}


		# get any linked fields
		if($i > 0)
		{
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($smart, $this->linked);
		}
		else
		{
			$this->result = $smart;
		} 

		# get the result count:
		$results = $result->RecordCount();

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# define the results
		$smarty->assign($this->table, $this->result);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	$smarty_order);
		$smarty->assign('sort',		$smarty_sort);
		$smarty->assign('limit',	$search->limit);
		$smarty->assign('search_id',$search->id);
		$smarty->assign('results', 	$search->results);

		# get the total pages for this search:
		if(empty($search->limit))
			$this->pages = 1;
		else
			$this->pages = intval($search->results / $search->limit);
		if ($search->results % $search->limit) $this->pages++;

		# total pages
		$smarty->assign('pages', 	$this->pages);

		# current page
		$smarty->assign('page', 	$current_page);
		$page_arr = '';
		for($i=0; $i <= $this->pages; $i++)
		{
			if ($this->page != $i) 	$page_arr[] = $i;
		}

		# page array for menu
		$smarty->assign('page_arr',	$page_arr);            	
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


	##############################
	##		STATIC VARS         ##
	##############################

	function static_var($VAR)
	{	
		global $smarty;

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;

		if(ereg('search', $VAR['_page']))
		$arr = $static_var->generate_form($this->module, 'add', 'search');
		else
		$arr = $static_var->generate_form($this->module, 'add', 'update'); 

		if(gettype($arr) == 'array')
		{ 	
			### Set everything as a smarty array, and return:
			$smarty->assign('show_static_var',		true);
			$smarty->assign('static_var',	$arr);
			return true;		 	
		}
		else
		{		 	
			### Or if no results:
			$smarty->assign('show_static_var',		false);
			return false;	           	 			 	
		}
	}    			
}
?>