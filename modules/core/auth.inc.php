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
	
class CORE_auth
{
	var $auth_modules;
	var $auth_methods;
	var $account=false;
	var $logged=false;

	function CORE_auth($force)
	{
		global $VAR;

		if(!isset( $this->auth_methods ) )   {
		  #include (PATH_CORE   . 'auth_methods.inc');
		  #$this->auth_methods = $auth_methods;
		}

		if(defined("SESS_LOGGED")) 	{
			if(SESS_LOGGED == "1")  {
				$this->logged 	= TRUE;
				$this->account	= SESS_ACCOUNT;
			}
			else {
				$this->logged 	= FALSE;
				$this->account	= 0;
			}		   	
		} 	else 	{
			$this->logged 	= FALSE;
			$this->account	= 0;

			if(!defined('SESS_LOGGED')) define('SESS_LOGGED', false);
			if(!defined('SESS')) define('SESS', false);
		}

		if($force && defined("FORCE_SESS_ACCOUNT")) {
			$this->account = FORCE_SESS_ACCOUNT;
			$this->logged  = TRUE;
		} 
        $this->auth_update();
		if (  isset($VAR['_logout']) ||
			  isset($VAR['_login'])  ||
			  isset($VAR['lid'])     ||
			  $force == true         ||
			  CACHE_SESSIONS != "1") {
        return;
	} else {
            if($this->session_auth_cache_retrieve())
            {
                $this->module_count = count($this->module);
                return;
            }
		}
	}


	function auth_update() {
	    $this->group = array('0');
		$this->module = array('0');
	    
		if($this->account) {
			$this->group_list($this->account);
			if (!$this->group) {
				return;
			}
    		$db = &DB();
    		$p = AGILE_DB_PREFIX;
    		$sql="SELECT DISTINCT MM.module_id, GM.method_id, GM.group_id,  
    			M.name AS module_name, M.parent_id AS module_parent_id, M.menu_display AS module_display, 
    			MM.name AS method_name, MM.page AS method_page, MM.menu_display AS method_display
    			FROM {$p}group_method as GM 
    			LEFT JOIN {$p}module as M on (GM.module_id=M.id and M.site_id=".DEFAULT_SITE.")
    			LEFT JOIN {$p}module_method as MM on (GM.method_id=MM.id and MM.site_id=".DEFAULT_SITE.") "; 
    		for($i=0; $i<count($this->group); $i++)
    		if($i==0) $sql .= "WHERE (GM.group_id={$this->group[$i]} ";
    		else      $sql .= "OR GM.group_id={$this->group[$i]} "; 
    		$sql .= ") AND GM.site_id=".DEFAULT_SITE." ORDER BY M.name,MM.name";
    		$result=$db->Execute($sql);
			if($result === false)
			{
				global $C_debug;
				$C_debug->error('core:auth.inc.php','auth_update', $db->ErrorMsg() . '<br><br>' .$q);
				return;
			}
			while (!$result->EOF) {
				$module_name 	= $result->fields["module_name"];
				$method_name	= $result->fields["method_name"];

				if(empty($this->module[$module_name])) {        			
					$this->module[$module_name] = 			  array($result->fields["module_id"],
																	$result->fields["module_parent_id"], 
																	$result->fields["module_display"]);
				}

				if(empty($this->module[$module_name][$method_name])) {
					$this->module[$module_name][$method_name] = array($result->fields["method_id"],
																	  $result->fields["method_display"], 
																	  $result->fields["method_page"]);
				} 
				$result->MoveNext();
			}
		}
		$this->session_auth_cache_update();
	}


	function session_auth_cache_update() {
		$db = &DB();
		$expire = time() + 7200; // 1 hour

		if(isset($this->group) && gettype($this->group) == 'array')
		$group  = serialize($this->group);
		else
		$group  = 0;

		if(isset($this->module) && gettype($this->module) == 'array')
		$module = serialize($this->module);
		else
		$module = 0;

		$q      = 'DELETE FROM '.AGILE_DB_PREFIX.'session_auth_cache WHERE
				session_id  = '. $db->qstr(SESS) .' AND
				site_id     = '. $db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'session_auth_cache_id');
		$q      = 'INSERT INTO '.AGILE_DB_PREFIX.'session_auth_cache SET
				id          = '. $db->qstr($id) .',
				site_id     = '. $db->qstr(DEFAULT_SITE) .',
				session_id  = '. $db->qstr(SESS) .',
				date_expire = '. $db->qstr($expire) .',
				group_arr   = '. $db->qstr($group) .',
				module_arr  = '. $db->qstr($module);
		$db->Execute($q);
	}


	function session_auth_cache_retrieve() {
		global $C_sess;
		if(!empty($C_sess->auth_cache)) {
			if ( $C_sess->auth_cache["date_expire"] > time() ) {
				$group =   $C_sess->auth_cache['group_arr'];
				$module =  $C_sess->auth_cache['module_arr'];
				if($group != '0' && $group != '')   $this->group  = unserialize($group);
				if($module != '0' && $module != '') $this->module = unserialize($module);
				return true;
			}
		}		 

		$db = &DB();
		$q      = 'SELECT * FROM '.AGILE_DB_PREFIX.'session_auth_cache WHERE
				site_id     = '. $db->qstr(DEFAULT_SITE) .' AND
				session_id  = '. $db->qstr(SESS) .' AND
				date_expire >= '. $db->qstr(time());
		$result = $db->Execute($q);
		if($result->RecordCount() > 0) {
			$group  = $result->fields['group_arr'];
			$module = $result->fields['module_arr']; 
			if($group != '0' && $group != '')   $this->group  = unserialize($group);
			if($module != '0' && $module != '') $this->module = unserialize($module);
			return true;
		}
		return false;
	}


	function group_list($account) {
		$this->group[0] = "0";
		$time = time();
		$db = &DB();
		$p = AGILE_DB_PREFIX;
		$q="SELECT DISTINCT ag.group_id AS group_id,g.parent_id AS parent_id  
			FROM {$p}account_group as ag
			INNER JOIN {$p}group as g ON (ag.group_id=g.id AND g.status=1 AND g.site_id=".DEFAULT_SITE.")
			WHERE ag.account_id = '$account'
			AND ( ag.date_start IS NULL  OR ag.date_start < $time )
			AND ( ag.date_expire IS NULL OR ag.date_expire = 0 OR ag.date_expire > $time )				
			AND ( g.date_start IS NULL   OR g.date_start <= $time ) 
			AND ( g.date_expire IS NULL  OR g.date_expire = 0 OR g.date_expire > $time ) 									
			AND ag.active=1 AND g.status=1 
			AND ag.site_id=".DEFAULT_SITE;
		$result = $db->Execute($q); 
		if ($result === false) {
			global $C_debug;
			echo $db->ErrorMsg();
			$C_debug->error('auth.inc.php','group_list', $db->ErrorMsg());
			exit;
		} elseif($result->RecordCount() == 0) {
			return;
		} else {	        
			while (!$result->EOF) {
				$arr[] = $result->fields;
				$result->MoveNext();
			}
		}

		for($i=0; $i<count($arr); $i++) {
			$do = true;
			for($ii=0; $ii<count($this->group); $ii++)
				if($this->group[$ii] == $arr[$i]["group_id"]) $do = false;

			if($do) {	  
				$this->group[] = $arr[$i]["group_id"];

				if(!empty($arr[$i]["parent_id"]) && $arr[$i]["parent_id"] != $arr[$i]["group_id"]) {
					$do = true;
					for($ii=0; $ii<count($this->group); $ii++)
						if($this->group[$ii] == $arr[$i]["parent_id"]) $do = false;
					if($do) $this->group[] = $arr[$i]["parent_id"];
				} 
			}
		}
		if($account != SESS_ACCOUNT) return $this->group;
	} 

	function auth_method_by_name($module,$method)  {			

		if(isset($this->module[$module][$method])) return TRUE; 

		if($module == 'core')
			if($method == 'cleanup') 
				return true;
			else
				return false;

		if( is_file(PATH_MODULES.$module.'/auth.inc.php')) {
			include (PATH_MODULES.$module.'/auth.inc.php');
			$this->auth_methods = $auth_methods;		 
			for($i=0; $i<count($this->auth_methods); $i++)
				if ($module == @$this->auth_methods[$i]['module'])
					if($method == false ||  $method == @$this->auth_methods[$i]['method'])
						return true;
		} 
		return FALSE;
	}

	function auth_group_by_id($id) {
		if(!is_array($id))  
			$ids[] = $id;
		else 
			$ids = $id;   
		foreach ( $ids as $group_id )  
			if(isset($this->group))
				foreach ($this->group as $this_group_id)
					if($this_group_id == $group_id) 
						return true; 
		return false;
	}

	function auth_group_by_account_id($account, $id) { 	         			 
		if(SESS_LOGGED == true && $account == SESS_ACCOUNT) 
			return $this->auth_group_by_id($id); 	        
		unset($this->group); 
		$this->group_list($account);              
		for($i=0; $i < count($this->group); $i++)
			if($this->group[$i] == $id) return true; 
		return FALSE;
	}

	function generate_admin_menu() {
		include_once(PATH_CORE.'auth_generate_admin_menu.inc.php');
		return auth_generate_admin_menu($this);
	}
}
?>