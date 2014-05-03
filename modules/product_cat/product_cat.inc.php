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
	
class product_cat
{

	# Open the constructor for this mod
	function product_cat()
	{
		# name of this module:
		$this->module = "product_cat";

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
	## USER CATEGORY ARR SMARTY ##
	##############################
	function user_menu($VAR)
	{
		global $smarty, $C_auth; 
		$db 	= &DB();
		$dbc	= new CORE_database; 
		$sql 	= $dbc->sql_select(	"product_cat", "*", 
									"status = 1 AND (parent_id = 0 OR parent_id IS NULL OR parent_id = id)", "position", $db);
		$result	= $db->Execute($sql); 
		if($result->RecordCount() == 0) {
			return false;
		} else  {
			while(!$result->EOF) {
				# check for group settings:
				$groups = unserialize($result->fields['group_avail']);

				$auth = false;
				for($ii=0; $ii<count($groups); $ii++) {
					if($C_auth->auth_group_by_id($groups[$ii])) $auth = true;
				}

				if($auth) {
					#  Create the array for smarty
					$smart[] = $result->fields;
					$i++;
				}
				$result->MoveNext();        			        				
			}
			$smarty->assign('product_cat', $smart);     		
		}        	
	}


	##############################
	## USER VIEW A CATEGORY	    ##
	##############################
	function user_view($VAR)
	{	
		global $smarty, $C_auth; 
		$db 	= &DB(); 

		### Get the category information:
		$sql 	= sqlSelect($db, "product_cat", "id,position,template,name,thumbnail,group_avail,parent_id,max", "status = 1 AND id = ::{$VAR['id']}::");
		$result	= $db->Execute($sql);

		if(!$result || $result->RecordCount() == 0) {
			return false;
		} else {

			# check for group settings:
			$groups = unserialize($result->fields['group_avail']);

			# max results per page:
			$max  	 = $result->fields['max'];
			if(empty($max)) $max = 25; 
			$count 	 = 0; 
			if(empty($VAR['page']) || !is_numeric($VAR['page']) || $VAR['page'] <= 1 ) {
				$page  = 1;
				$start = 0;
			} else {
				$page = $VAR['page'];
				$start = ($page-1)*$max;
			}	            	

			$auth = false;
			for($ii=0; $ii<count($groups); $ii++) {
				if($C_auth->auth_group_by_id($groups[$ii])) $auth = true;
			}         			        				

			if($auth) {
				$smart[] =  $result->fields;
				$smarty->assign('product_cat_arr', $smart);   
			} else {
				return false;  		
			}

			$parent_id = $result->fields['parent_id'];
			if(!$parent_id) $parent_id = '0';
		} 

		### Get the items in this category:
		$sql 	= sqlSelect($db, "product", "id,sku,thumbnail,avail_category_id,price_base,price_setup,group_avail", "active = 1", "position,sku");
		$result	= $db->Execute($sql); 
		if(!$result || $result->RecordCount() == 0) {
			return false;
		} else  { 
			while(!$result->EOF) {
				# check that this item can be displayed in the current category:
				$cat 	= false;
				$cats	= unserialize($result->fields['avail_category_id']);
				for($i=0; $i<count($cats); $i++)  {
					if($cats[$i] == $VAR['id']) {
						$cat = true;
						break;
					}
				}

				if($cat) {       		 
					# check for group settings:
					$groups = unserialize($result->fields['group_avail']);

					$auth = false;
					for($ii=0; $ii<count($groups); $ii++) {
						if($C_auth->auth_group_by_id($groups[$ii])) {
							$auth = true;
							break;
						}
					}  

					### Paging
					if($auth)  { 
						if($count >= $start && $count < $max*$page)   		            		
							$smart_prod[] = $result->fields;  
						$count++;
					}
				}
				$result->MoveNext();
			}  

			$results = $count;
			$pages 	 = intval($results / $max);
			if ($results % $max) $pages++;

			for($i=1;$i<=$pages;$i++) $pagearr[$i] = $i;

			$smarty->assign('product_arr', 	$smart_prod);  
			$smarty->assign('page_page', 	$page);
			$smarty->assign('page_results', $results);
			$smarty->assign('page_pages', 	$pages);
			$smarty->assign('page_arr', 	$pagearr);
		}

		### Get any sub-categories: 
		$sql = sqlSelect($db, "product_cat", "*", "status = 1 AND parent_id = ::{$VAR['id']}::","position,name");
		$result	= $db->Execute($sql); 
		if($result && $result->RecordCount()) {
			while(!$result->EOF) {
				$smart_sub_cat[] = $result->fields;
				$result->MoveNext();
			}
			$smarty->assign('product_sub_cat', $smart_sub_cat);
		}       		

		### Get any parent categores:   
		$p = AGILE_DB_PREFIX;
		$d = DEFAULT_SITE;
		$smart_parent_cat = Array();
		for($i=0; $i<=5; $i++)
		{
			if($parent_id > 0 ) 
			{	
				# Get parent id & add to array
				$sql = "SELECT 
						{$p}product_cat.id,
						{$p}product_cat.parent_id,
						{$p}product_cat.template,
						{$p}product_cat_translate.name
						FROM
						{$p}product_cat,{$p}product_cat_translate
						WHERE
						{$p}product_cat.site_id = {$d}
						AND
						{$p}product_cat_translate.site_id = {$d}
						AND
						{$p}product_cat.id = $parent_id
						AND
						{$p}product_cat_translate.product_cat_id = $parent_id
						AND
						{$p}product_cat_translate.language_id = '".SESS_LANGUAGE."'";
				$result = $db->Execute($sql);
				if($result && $result->RecordCount())
				{
					$parent_id = $result->fields['parent_id'];  
					$smart_parent_cat[] = $result->fields;
				}
			} 
		}

		$smart_parent_cat = array_reverse($smart_parent_cat);

		## Get the current category: 
		$sql = "SELECT 
				{$p}product_cat.id,
				{$p}product_cat.parent_id,
				{$p}product_cat.template,
				{$p}product_cat_translate.name
				FROM
				{$p}product_cat,{$p}product_cat_translate
				WHERE
				{$p}product_cat.site_id = {$d}
				AND
				{$p}product_cat_translate.site_id = {$d}
				AND
				{$p}product_cat.id = ".$db->qstr($VAR['id'])."
				AND
				{$p}product_cat_translate.product_cat_id = ".$db->qstr($VAR['id'])."
				AND
				{$p}product_cat_translate.language_id = '".SESS_LANGUAGE."'";
		$result = $db->Execute($sql);
		if($result && $result->RecordCount())  
			$smart_parent_cat[] = $result->fields; 

		$smarty->assign('parent_cat', $smart_parent_cat);

	}



	###############################
	## ADMIN SINGLE SELECT MENU  ##
	###############################
	function admin_menu_parent($VAR)
	{
		global $smarty, $C_auth; 
		$db 	= &DB();
		$dbc	= new CORE_database; 
		$sql 	= $dbc->sql_select(	"product_cat", "*",  "", "parent_id,position,name", $db);
		$result	= $db->Execute($sql);

		# Get current id
		if(!empty($VAR['id'])) {
			$cid = preg_replace("/,/","", $VAR['id']);
		} else {
			$current = '';
		}

		# Loop and put in array
		while(!$result->EOF) { 
			if($result->fields['parent_id'] == "" || $result->fields['parent_id'] == 0 || $result->fields['parent_id'] == $result->fields['id']) {
				$arr[0][] = $result->fields;
			} else { 
				$arr["{$result->fields['parent_id']}"][] = $result->fields;
			} 

			# get current parent_id
			if($cid > 0 && $result->fields['id'] == $cid)
				$current = $result->fields['parent_id'];

			$result->MoveNext();
		} 

		# Create menu
		$option = '';

		for($i=0; $i<count($arr[0]); $i++)
		{
			$id = $arr[0][$i]["id"];

			if($id == $current) 
				$sel = 'selected'; 
			else 
				$sel = ''; 

			$option .= '<option value="'.$id.'" '.$sel.'>'.$arr[0][$i]["name"].'</option>';

			##########################
			# get the sub-categories # (LEVEL 2)
			if(isset($arr[$id]))
			{
				for($ii=0; $ii<count($arr[$id]); $ii++)
				{
					$idx = $arr[$id][$ii]["id"];
					if($idx == $current) $sel = 'selected'; else $sel = ''; 
					$option .= '<option value="'.$idx.'" '.$sel.'>-&nbsp;&nbsp;'.$arr[$id][$ii]["name"].'</option>';        			
				}

				##########################
				# get the sub-categories # (LEVEL 3)
				if(isset($arr[$idx]))
				{
					for($iii=0; $iii<count($arr[$idx]); $iii++)
					{
						$idx2 = $arr[$idx][$iii]["id"];
						if($idx2 == $current) $sel = 'selected'; else $sel = ''; 
						$option .= '<option value="'.$idx2.'" '.$sel.'>&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx][$iii]["name"].'</option>';        			
					}

					##########################
					# get the sub-categories # (LEVEL 4)
					if(isset($arr[$idx2]))
					{
						for($iiii=0; $iiii<count($arr[$idx2]); $iiii++)
						{
							$idx3 = $arr[$idx2][$iiii]["id"];
							if($idx3 == $current) $sel = 'selected'; else $sel = ''; 
							$option .= '<option value="'.$idx3.'" '.$sel.'>&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx2][$iiii]["name"].'</option>';        			
						}

						##########################
						# get the sub-categories # (LEVEL 5)
						if(isset($arr[$idx3]))
						{
							for($iiiii=0; $iiiii<count($arr[$idx3]); $iiiii++)
							{
								$idx4 = $arr[$idx3][$iiiii]["id"];
								if($idx4 == $current) $sel = 'selected'; else $sel = ''; 
								$option .= '<option value="'.$idx4.'" '.$sel.'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx3][$iiiii]["name"].'</option>';        			
							}
						}		        			
					}	        			
				}        			
			}  
		}

		echo '<select name="product_cat_parent_id" onChange="submit()">';
		echo '<option value="0"></option>';
		echo $option;        	
		echo '</select>'; 
	}


	##############################
	## ADMIN MULTI SELECT MENU  ##
	##############################
	function admin_menu_product($VAR)
	{
		global $smarty, $C_auth; 
		$db 	= &DB();
		$dbc	= new CORE_database; 

		# Get current category id
		if(!empty($VAR['id'])) {
			$product_id = preg_replace("/,/","", $VAR['id']);
			$sql = $dbc->sql_select("product", "avail_category_id", "id = $product_id","", $db);
			$product = $db->Execute($sql);
			$current = unserialize($product->fields['avail_category_id']);
		} else {
			$current = '';
		}

		# Loop and put in array
		$sql 	= $dbc->sql_select(	"product_cat", "*",  "", "parent_id,position,name", $db);
		$result	= $db->Execute($sql); 
		while(!$result->EOF) 
		{ 
			# determine if selected
			$select = false;
			for($ix=0; $ix<count($current); $ix++) {
				if($current[$ix] == $result->fields['id']) {
					$result->fields['sel'] = 'selected';
					break;
				}
			} 

			# set array 		 
			if($result->fields['parent_id'] == "" || $result->fields['parent_id'] == 0 || $result->fields['parent_id'] == $result->fields['id']) {
				$arr[0][] = $result->fields; 
			} else {  
				$arr["{$result->fields['parent_id']}"][] = $result->fields; 
			}  

			$result->MoveNext();
		} 

		# Create menu
		$option = '';

		for($i=0; $i<count($arr[0]); $i++)
		{
			$id = $arr[0][$i]["id"];

			$option .= '<option value="'.$id.'" '.@$arr[0][$i]["sel"].'>'.$arr[0][$i]["name"].'</option>';

			##########################
			# get the sub-categories # (LEVEL 2)
			if(isset($arr[$id]))
			{
				for($ii=0; $ii<count($arr[$id]); $ii++)
				{
					$idx = $arr[$id][$ii]["id"]; 
					$option .= '<option value="'.$idx.'" '.@$arr[$id][$ii]["sel"].'>-&nbsp;&nbsp;'.$arr[$id][$ii]["name"].'</option>';        			
				}

				##########################
				# get the sub-categories # (LEVEL 3)
				if(isset($arr[$idx]))
				{
					for($iii=0; $iii<count($arr[$idx]); $iii++)
					{
						$idx2 = $arr[$idx][$iii]["id"]; 
						$option .= '<option value="'.$idx2.'" '.@$arr[$idx][$iii]["sel"].'>&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx][$iii]["name"].'</option>';        			
					}

					##########################
					# get the sub-categories # (LEVEL 4)
					if(isset($arr[$idx2]))
					{
						for($iiii=0; $iiii<count($arr[$idx2]); $iiii++)
						{
							$idx3 = $arr[$idx2][$iiii]["id"]; 
							$option .= '<option value="'.$idx3.'" '.@$arr[$idx2][$iiii]["sel"].'>&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx2][$iiii]["name"].'</option>';        			
						}

						##########################
						# get the sub-categories # (LEVEL 5)
						if(isset($arr[$idx3]))
						{
							for($iiiii=0; $iiiii<count($arr[$idx3]); $iiiii++)
							{
								$idx4 = $arr[$idx3][$iiiii]["id"];  
								$option .= '<option value="'.$idx4.'" '.@$arr[$idx3][$iiiii]["sel"].'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-&nbsp;&nbsp;'.$arr[$idx3][$iiiii]["name"].'</option>';        			
							}
						}		        			
					}	        			
				}        			
			}  
		}

		echo '<select name="product_avail_category_id[]" size="5" multiple >'; 
		echo $option;        	
		echo '</select>';         	
	}        



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		global $_FILES;

		####################################################################
		### Validate the thumbnail upoad:
		if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
		{
			$VAR['product_cat_thumbnail']   = "cat_thmb_".$_FILES['upload_file1']['name'];
		}

		### Validate the image upoad:
		if(isset($_FILES['upload_file2']) && $_FILES['upload_file2']['size'] > 0)
		{
			$VAR['product_cat_image']   = "cat_img_".$_FILES['upload_file2']['name'];
		}


		####################################################################
		## Attempt to add the record:

		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$result = $db->add($VAR, $this, $type);		


		####################################################################
		### Copy the image(s)
		if($result)
		{
			### Copy 1ST file upoad:
			if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
				copy($_FILES['upload_file1']['tmp_name'], PATH_IMAGES . "cat_thmb_" . $_FILES['upload_file1']['name']);

			### Copy the 2ND file upoad:
			if(isset($_FILES['upload_file2']) && $_FILES['upload_file2']['size'] > 0)
				copy($_FILES['upload_file2']['tmp_name'], PATH_IMAGES . "cat_img_" . $_FILES['upload_file2']['name']);
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
		global $_FILES;

		####################################################################
		### Validate the thumbnail upoad:
		if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
		{
			$VAR['product_cat_thumbnail']   = "cat_thmb_".$_FILES['upload_file1']['name'];
		} elseif ( $VAR['delthumb'] == 1 ) {
			$VAR['product_cat_thumbnail']   = "";
		}

		### Validate the image upoad:
		if(isset($_FILES['upload_file2']) && $_FILES['upload_file2']['size'] > 0)
		{
			$VAR['product_cat_image']       = "cat_img_".$_FILES['upload_file2']['name'];
		} elseif ( $VAR['delimg'] == 1 ) {
			$VAR['product_cat_image']   = "";
		}

		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$result = $db->update($VAR, $this, $type);

		####################################################################
		### Copy the image(s)
		if($result)
		{
			### Copy 1ST file upoad:
			if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
				copy($_FILES['upload_file1']['tmp_name'], PATH_IMAGES . "cat_thmb_" . $_FILES['upload_file1']['name']);

			### Copy the 2ND file upoad:
			if(isset($_FILES['upload_file2']) && $_FILES['upload_file2']['size'] > 0)
				copy($_FILES['upload_file2']['tmp_name'], PATH_IMAGES . "cat_img_" . $_FILES['upload_file2']['name']);
		}    		
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	 
		$this->associated_DELETE =
			Array ( Array ( 'table' => 'product_cat_translate', 'field' => 'product_cat_id') );

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
