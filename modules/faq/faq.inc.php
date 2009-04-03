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
	
class faq
{

	# Open the constructor for this mod
	function faq()
	{
		# name of this module:
		$this->module = "faq";
		$this->version = "1.0.0a";

		if(!defined('AJAX'))
		{
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
	}



	### autofill for admin 'canned messages' in ticket reply
	function autofill($VAR)
	{
		$db = &DB(); 
		if(!empty($VAR['faq_autofill']) && strlen($VAR['faq_autofill']) > 3)  { 
			$result = $db->Execute(
				$sql = sqlSelect(
					$db,
					Array( 'faq_translate', 'faq' ),		 
					Array( 'A.*', 'B.name' ),			 
					" A.faq_id = B.id AND MATCH(A.question, A.answer) AGAINST(".$db->qstr($VAR['faq_autofill'].'*')." IN BOOLEAN MODE)",  
					""
				)
			);  
		} 

		echo '<ul>';            
		# Create the alert for no records found
		if (@$result != false && $result->RecordCount() > 0)  { 
			$i=0;  
			while(!$result->EOF && $i < 10)  { 
				echo '<li><div class="name"><b>' .  stripcslashes( substr( ereg_replace("\r\n", " ",  $result->fields['question']), 0, 65 ) ). '</b></div>'.
					'<div class="email"><span class="informal">' . stripcslashes( substr( ereg_replace("\r\n", " ",  $result->fields['answer']),0, 75 ) ) . '</span></div>'.
					'<div class="index" style="display:none">'. stripcslashes( $result->fields['answer'].'</div></li>'). "\r\n"; 
				$result->MoveNext();
				$i++;
			} 
		} else {  
			include_once(PATH_CORE.'translate.inc.php');
			include_once(PATH_CORE.'xml.inc.php'); 
			$C_translate = new CORE_translate;  
			echo '<li><div class="name"><b>'. $C_translate->translate('admin_no_match','faq') .'</b></div>' .
				 '<div class="email"><span class="informal">'. $C_translate->translate('admin_no_match_help','faq') .'</span></div>'.
				 '<div class="index" style="display:none">null</div></li>' . "\r\n"; 
		}
		echo "</ul>";

	}


	### Get the faq details:
	function faq_show($VAR) 
	{
		if (!empty($VAR['id'])) {
			$db = &DB();
			$rs = $db->Execute( 
				$sql = sqlSelect( 
					$db,
					Array( 'faq_translate', 'faq', 'faq_category' ),		 
					Array( 'A.*', 'B.name', 'C.group_avail' ),			 
					" B.id = ::".$VAR['id'].":: AND B.id = A.faq_id AND A.language_id = '".SESS_LANGUAGE."' AND B.status = 1 AND C.status = 1 ",  
					""
				)
			); 	        	    	        	    	
		}

		global $C_auth;
		if( !$rs || $rs->RecordCount() == 0 || !$C_auth->auth_group_by_id( unserialize( $rs->fields['group_avail'] ))) {
			include_once(PATH_CORE.'translate.inc.php');
			include_once(PATH_CORE.'xml.inc.php'); 
			$C_translate = new CORE_translate;  
			echo $C_translate->translate('no_faqs','faq');
		} else {
			echo '<p><u>' . ereg_replace("\r\n", "<BR>", stripcslashes( htmlentities( $rs->fields['question']) ) ). '</u></p>' .
				 '<p>' 	  . ereg_replace("\r\n", "<BR>", $this->linkalize(stripcslashes( htmlentities( $rs->fields['answer']) ) ) ). '</p>';             		
		}
	}  


	### Get the faq translation:
	function faq_search($VAR) 
	{
		$db = &DB(); 
		if(!empty($VAR['search']) && strlen($VAR['search']) > 3)  { 
			$result = $db->Execute(
				$sql = sqlSelect(
					$db,
					Array( 'faq_translate', 'faq', 'faq_category'  ),		 
					Array( 'A.*', 'B.name', 'C.group_avail' ),			 
					"  A.faq_id = B.id AND B.faq_category_id = C.id AND MATCH(A.question, A.answer) AGAINST(".$db->qstr($VAR['search'].'*')." IN BOOLEAN MODE) AND B.status = 1 AND C.status=1",  
					""
				)
			);  
		} elseif (!empty($VAR['category_id'])) {
			$result = $db->Execute(  
				$sql = sqlSelect( 
					$db,
					Array( 'faq_translate', 'faq', 'faq_category' ),		 
					Array( 'A.*', 'B.name', 'C.group_avail' ),			 
					" B.faq_category_id = ::".$VAR['category_id'].":: AND B.id = A.faq_id AND B.faq_category_id = C.id AND A.language_id = '".SESS_LANGUAGE."' AND B.status = 1 AND C.status=1",  
					""
				)
			);         		
		} 

		echo '<ul>';             
		if (@$result != false && $result->RecordCount() > 0)  { 
			$i=0;  
			while(!$result->EOF)  {  
				global $C_auth;
				if( $C_auth->auth_group_by_id( unserialize($result->fields['group_avail'] ))) {
					echo '<li><div><a href="?_page=faq:faq&id='.$result->fields['faq_id'].'">' . stripcslashes( htmlentities(  ereg_replace("\r\n", " ",  $result->fields['question']) ) ). '</a></div> </li>'. "\r\n"; 
					$i++;
				} 
				$result->MoveNext();  
			} 
		} else { 
			include_once(PATH_CORE.'translate.inc.php');
			include_once(PATH_CORE.'xml.inc.php'); 
			$C_translate = new CORE_translate;  
			echo '<li><div class="name"><b>'. $C_translate->translate('admin_no_match','faq') .'</b></div> </div></li>' . "\r\n"; 
		}
		echo "</ul>";
	}



	### Get the authorized/active categories
	function faq_categories($VAR) 
	{
		$db = &DB();
			$rs = $db->Execute( $sql= 
				$sql = sqlSelect(
				$db,
				'faq_category',
				'*' ,
				" status=1 ",
				"sort_order,name,date_orig"
			)
		);

		if(!$rs || $rs->RecordCount() == 0) {
		//
		} else {
			while(!$rs->EOF) {
				// validate groups
				global $C_auth;  
				if( $C_auth->auth_group_by_id( unserialize($rs->fields['group_avail']) )) {
					$smart[] = $rs->fields;
				}
				$rs->MoveNext();
			}	
		}

		global $smarty;
		$smarty->assign('faq_category_list', @$smart);        	            		
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

		if($id && !empty($VAR['faq_question'])) 
		{
			# Insert translation
			$db = &DB();
			$idx = $db->GenID(AGILE_DB_PREFIX. 'faq_translate_id');
			$sql = "INSERT INTO	".AGILE_DB_PREFIX."faq_translate
					SET
					site_id = ".DEFAULT_SITE.",
					id = $idx,
					faq_id = $id,
					date_orig = ".time().",
					date_last = ".time().",
					language_id = '".DEFAULT_LANGUAGE."',
					answer = ".$db->qstr( @$VAR['faq_answer'] ).",
					question = ".$db->qstr( @$VAR['faq_question']);
			$db->Execute($sql);	  	 
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
		$this->associated_DELETE[] =
				Array(
					'table' => 'faq_translate',
					'field' => 'faq_id'
				);

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

	function linkalize($text) 
	{
		  $text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $text);          
		  $text = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i", "<A TARGET=\"_blank\" HREF=\"$1\">$1</A>", $text); //make all URLs links
		  $text = preg_replace("/[\w-\.]+@(\w+[\w-]+\.){0,3}\w+[\w-]+\.[a-zA-Z]{2,4}\b/i","<ahref=\"mailto:$0\">$0</a>",$text);
		  return $text;
	}     	
}
?>
