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
	
class static_page
{

	# Open the constructor for this mod
	function static_page()
	{
		# name of this module:
		$this->module = "static_page";

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
	##	GET AUTH CATEGORIES     ##
	##############################

	function page_list($VAR)
	{	
		/* check if current session is authorized for any ticket departments..
			and return true/false...
		*/
		global $smarty;
		if(!isset($VAR['id']))
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}

		### Check if user is auth for the selected category:
		$db     = &DB();
		$sql    = 'SELECT DISTINCT id,name,group_avail FROM ' . AGILE_DB_PREFIX . 'static_page_category WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1') .' AND
					id          = ' . $db->qstr($VAR['id']);
		$result = $db->Execute($sql);


		if($result->RecordCount() == 0)
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}

		global $C_auth;
		$iii = 0;

		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_avail']);

			for($i=0; $i<count($arr); $i++)
			{
				if($C_auth->auth_group_by_id($arr[$i]))
				{
					$iii++;
					$i=count($arr);
				}
			}
			$result->MoveNext();
		}

		if($iii == 0)
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}


		$sql    =  'SELECT id,name,date_expire,date_start
					FROM ' . AGILE_DB_PREFIX . 'static_page WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					static_page_category_id = ' . $db->qstr($VAR['id']) . ' AND
					status      = ' . $db->qstr('1') .'
					ORDER BY sort_order,date_orig,name';
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			$smarty->assign('static_page_category_display', false);
			return false;
		}

		global $C_auth;
		$ii = 0;

		while(!$result->EOF)
		{
			$start = $result->fields['date_start'];
			$expire= $result->fields['date_expire'];

			### Check that it is not expired
			if (( $start == "0"  || $start <= time()+2  ) &&
			   ( $expire == "0"  || $expire >= time() )  )
			{

				### Get the translated name, for the current session language
				$sql = 'SELECT body_intro, title, language_id
						FROM ' . AGILE_DB_PREFIX . 'static_page_translate WHERE
						site_id         = ' . $db->qstr(DEFAULT_SITE) . ' AND
						static_page_id  = ' . $db->qstr($result->fields['id']) . ' AND
						language_id     = ' . $db->qstr(SESS_LANGUAGE);
				$translate = $db->Execute($sql);

				if($translate->RecordCount() > 0)
				{
					$arr_smarty[] = Array  (
							'name'          => $result->fields['name'],
							'title'         => $translate->fields['title'],
							'intro'         => $translate->fields['body_intro'],
							);
					$ii++;
				}
				else
				{
					### Get the translated name, for the default langauge
					$sql = 'SELECT body_intro, title, language_id
							FROM ' . AGILE_DB_PREFIX . 'static_page_translate WHERE
							site_id         = ' . $db->qstr(DEFAULT_SITE) . ' AND
							static_page_id  = ' . $db->qstr($result->fields['id']) . ' AND
							language_id     = ' . $db->qstr(DEFAULT_LANGUAGE);
					$translate = $db->Execute($sql);
					if($translate->RecordCount() > 0)
					{
						$arr_smarty[] =  Array  (
								'name'          => $result->fields['name'],
								'title'         => $translate->fields['title'],
								'intro'         => $translate->fields['body_intro'],
							   );
						$ii++;
					}
				}
			}
			$result->MoveNext();
		}



		if($ii == "0")
		{
			 $smarty->assign('static_page_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('static_page_display', 	true);
			$smarty->assign('static_page_results', 	$arr_smarty);
			return true;
		}
	}





	########################################################################
	### Show the page

	function page_show($VAR)
	{	
		/* check if current session is authorized for any ticket departments..
			and return true/false...
		*/
		global $smarty;
		if(!isset($VAR['id']) && !isset($VAR['name']))
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}

		### Check if user is auth for the selected category:
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_page WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1') .' AND
					name        = ' . $db->qstr(@$VAR['name']) .' OR
					id          = ' . $db->qstr(@$VAR['id']);
		$page = $db->Execute($sql);

		if($page->RecordCount() == 0)
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}


		$category_id = $page->fields['static_page_category_id'];
		$page_id     = $page->fields['id'];

		### Check if user is auth for the selected category:
		$db     = &DB();
		$sql    = 'SELECT DISTINCT id,name,group_avail FROM ' . AGILE_DB_PREFIX . 'static_page_category WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					status      = ' . $db->qstr('1') .' AND
					id          = ' . $db->qstr($category_id);
		$result = $db->Execute($sql);


		if($result->RecordCount() == 0)
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}





		global $C_auth;
		$iii = 0;
		$id = $result->fields['id'];


		while(!$result->EOF)
		{
			@$arr = unserialize($result->fields['group_avail']);

			for($i=0; $i<count($arr); $i++)
			{
				if($C_auth->auth_group_by_id($arr[$i]))
				{
					$iii++;
					$i=count($arr);
				}
			}
			$result->MoveNext();
		}

		if($iii == 0)
		{
			global $C_debug;
			$smarty->assign('static_page_display', false);
			return false;
		}



		### Check that it is not expired
		$ii = 0;
		$start = $page->fields['date_start'];
		$expire= $page->fields['date_expire'];

		### Check that it is not expired
		if (( $start == "0"  || $start <= time()+2  ) &&
		   ( $expire == "0"  || $expire >= time() )  )
		{

			### Get the translated name, for the current session language
			$sql = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'static_page_translate WHERE
						site_id         = ' . $db->qstr(DEFAULT_SITE) . ' AND
						static_page_id  = ' . $db->qstr($page->fields['id']) . ' AND
						language_id     = ' . $db->qstr(SESS_LANGUAGE);
			$translate = $db->Execute($sql);

			if($translate->RecordCount() > 0)
			{
				$arr_smarty = Array  (
						'name'          => $result->fields['name'],
						'title'         => $translate->fields['title'],
						'body'         => $translate->fields['body_full'],
						);
					$ii++;
			}
			else
			{
				### Get the translated name, for the default langauge
				$sql = 'SELECT *  FROM ' . AGILE_DB_PREFIX . 'static_page_translate WHERE
						site_id         = ' . $db->qstr(DEFAULT_SITE) . ' AND
						static_page_id  = ' . $db->qstr($page->fields['id']) . ' AND
						language_id     = ' . $db->qstr(DEFAULT_LANGUAGE);
				$translate = $db->Execute($sql);
				if($translate->RecordCount() > 0)
				{
					$arr_smarty =  Array  (
							'name'          => $result->fields['name'],
							'title'         => $translate->fields['title'],
							'body'          => $translate->fields['body_full'],
							);
					$ii++;
				}

			}
		}


		if($ii == "0")
		{
			 $smarty->assign('static_page_display', false);
			 return false;
		}
		else
		{
			$smarty->assign('static_page_display', 	true);
			$smarty->assign('static_page_results', 	$arr_smarty);
			return true;
		}
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
		$this->associated_DELETE[] =
				Array(
					'table' => 'static_page_translate',
					'field' => 'static_page_id'
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
}
?>