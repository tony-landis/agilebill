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
	
class CORE_session
{
	var $id;

	function CORE_session()
	{
		global $C_debug, $_GET, $_POST,$_COOKIE, $HTTP_COOKIE_VARS, $VAR;

		if (isset($_GET['s']))
			$session_arr[] = $_GET['s'];
		else if (isset($_POST['s']))
			$session_arr[] = $_POST['s'];
		else if(isset($_COOKIE[COOKIE_NAME]))
			$session_arr[] = $_COOKIE[COOKIE_NAME];
		else if (isset($HTTP_COOKIE_VARS[COOKIE_NAME]))
			$session_arr[] = $HTTP_COOKIE_VARS[COOKIE_NAME];

		if(isset($session_arr)) {
			for($i=0; $i<count($session_arr); $i++)  {
				if($session_arr[$i] != '')  {	
					$validate = $this->validate($session_arr[$i]);
					if($validate != FALSE) {
						$this->id = $session_arr[$i];
						$i = count($session_arr);
					}
				}
			}
		}  

		@$this->sess_date_expire   = time() + (SESSION_EXPIRE*60);
		if(!isset($this->id))
		{ 
			empty($VAR['tid']) 	? $this->sess_theme_id 		= DEFAULT_THEME 	: $this->sess_theme_id 		= $VAR['tid'];
			empty($VAR['lid'])  ? $this->sess_language_id   = DEFAULT_LANGUAGE  : $this->sess_language_id   = $VAR['lid'];
			empty($VAR['cid'])  ? $this->sess_country_id    = DEFAULT_COUNTRY  	: $this->sess_country_id    = $VAR['cid'];
			empty($VAR['cyid']) ? $this->sess_currency_id   = DEFAULT_CURRENCY  : $this->sess_currency_id   = $this->get_currency($VAR['cyid']); 
			empty($VAR['wid'])  ? $this->sess_weight_id     = DEFAULT_WEIGHT    : $this->sess_weight_id     = $VAR['wid'];    	  
			@$this->sess_reseller_id   = $VAR['rid'];       
			@$this->sess_affiliate_id  = $this->get_affiliate(0); 
			@$this->sess_campaign_id   = $this->get_campaign(0);
			$this->sess_logged         = false;
			$this->sess_account_id     = false;          	
			$this->session();
		}
		else
		{ 
			empty($VAR['tid'])  ? $this->sess_theme_id      = $validate['theme_id']     : $this->sess_theme_id      = $VAR['tid'];
			empty($VAR['lid'])  ? $this->sess_language_id   = $validate['language_id']  : $this->sess_language_id   = $VAR['lid'];
			empty($VAR['cid'])  ? $this->sess_country_id    = $validate['country_id']   : $this->sess_country_id    = $VAR['cid'];
			empty($VAR['cyid']) ? $this->sess_currency_id   = $validate['currency_id']  : $this->sess_currency_id   = $this->get_currency($VAR['cyid']); 
			empty($VAR['wid'])  ? $this->sess_weight_id     = $validate['weight_id']    : $this->sess_weight_id     = $VAR['wid'];
			empty($VAR['rid'])  ? $this->sess_reseller_id   = $validate['reseller_id']  : $this->sess_reseller_id   = $VAR['rid'];
			empty($VAR['aid'])  ? $this->sess_affiliate_id  = $validate['affiliate_id'] : $this->sess_affiliate_id  = $this->get_affiliate($validate['affiliate_id']);
			empty($VAR['caid']) ? $this->sess_campaign_id   = $validate['campaign_id']  : $this->sess_campaign_id   = $this->get_campaign($validate['campaign_id']);

			$this->sess_account_id  = $validate['account_id'];
			$this->sess_logged      = $validate['logged'];

			$db = &DB();
			$q = "UPDATE " . AGILE_DB_PREFIX . "session SET
			date_last         = " . $db->qstr(time())                  . ",
			date_expire	      = " . $db->qstr($this->sess_date_expire) . ",
			ip                = " . $db->qstr(USER_IP)                 . ",
			theme_id          = " . $db->qstr($this->sess_theme_id)    . ",
			country_id        = " . $db->qstr($this->sess_country_id)  . ",
			language_id       = " . $db->qstr($this->sess_language_id) . ",
			currency_id       = " . $db->qstr($this->sess_currency_id) . ",
			weight_id         = " . $db->qstr($this->sess_weight_id)   . ",
			reseller_id       = " . $db->qstr($this->sess_reseller_id) . ",
			affiliate_id      = " . $db->qstr($this->sess_affiliate_id). ",
			campaign_id       = " . $db->qstr($this->sess_campaign_id) . "
			WHERE
			id                = " . $db->qstr($this->id) . "
			AND
			site_id           = " . $db->qstr(DEFAULT_SITE);

			// update the old session ONLY if info has changed or expires/no update in the past 5 minutes.
			if (!empty($VAR['tid'])	|| !empty($VAR['lid']) || !empty($VAR['cid']) || !empty($VAR['cyid']) ||
				!empty($VAR['wid']) || !empty($VAR['rid']) || !empty($VAR['aid']) || !empty($VAR['caid']) ) {  
				$result = $db->Execute($q); 	    			
			} else if ($validate['logged'] == '0' && !empty($this->sess_date_expire) && $this->sess_date_expire+60*5 < time()) { 		             
				$result = $db->Execute($q);    	        	
			} else if (!empty($validate['date_last']) && $validate['date_last']+60*5 < time()) { 
				$result = $db->Execute($q);   
			}    	        	
		}

		if(!defined("SESS")) define ('SESS', $this->id);         	
		$this->setcookies();                       
	}


	function validate($session_id) {
		global $C_debug;
		$db = &DB();
		$q = "SELECT
			" . AGILE_DB_PREFIX . "session.*,
			" . AGILE_DB_PREFIX . "account.id AS acct_id,
			" . AGILE_DB_PREFIX . "account.status,
			" . AGILE_DB_PREFIX . "account.date_expire  AS account_date_expire,
			" . AGILE_DB_PREFIX . "session_auth_cache.date_expire AS sess_auth_date_expire,
			" . AGILE_DB_PREFIX . "session_auth_cache.group_arr,
			" . AGILE_DB_PREFIX . "session_auth_cache.module_arr
			FROM
			" . AGILE_DB_PREFIX . "session
			LEFT JOIN " . AGILE_DB_PREFIX . "account ON ".AGILE_DB_PREFIX."account.id = ".AGILE_DB_PREFIX."session.account_id 
			LEFT JOIN " . AGILE_DB_PREFIX . "session_auth_cache ON " . AGILE_DB_PREFIX . "session.id = " . AGILE_DB_PREFIX . "session_auth_cache.session_id
			WHERE
			" . AGILE_DB_PREFIX . "session.id = " . $db->qstr($session_id) . "
			AND
			" . AGILE_DB_PREFIX . "session.site_id = " . $db->qstr(DEFAULT_SITE) . "		
			AND ((
			" . AGILE_DB_PREFIX . "account.site_id = " . $db->qstr(DEFAULT_SITE) . "
			AND
			" . AGILE_DB_PREFIX . "session.account_id IS NOT NULL
			) OR (
		    " . AGILE_DB_PREFIX . "account.site_id IS NULL
			AND
			" . AGILE_DB_PREFIX . "session.account_id IS NULL
			))	        		    
			AND
			" . AGILE_DB_PREFIX . "session_auth_cache.site_id = " . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);
		if ($result === false) {
			$C_debug->error('session.inc.php','validate', $db->ErrorMsg());
			echo '<BR>Unable to start session: Database Error: ' . $db->ErrorMsg();
			return;
		} else if	($result->RecordCount() == 0) {  
			return FALSE;
		}  

		// Set the auth caching for use in the auth module to save a query there:
		$this->auth_cache['date_expire'] 	= $result->fields["sess_auth_date_expire"];
		$this->auth_cache['group_arr'] 		= $result->fields["group_arr"];
		$this->auth_cache['module_arr'] 	= $result->fields["module_arr"];

		if($result->fields['id'] == $session_id) {
			if($result->fields["logged"] == "1") { 
				if($result->fields['status'] != "1") { 
					return FALSE;
				} else if(!empty($result->fields['account_date_expire']) && $result->fields['account_date_expire'] < time())   {
					return FALSE;
				} else if(SESSION_EXPIRE != 0 && $result->fields['date_expire'] <= time()) { 
					$this->logout($session_id);
					return FALSE;
				}
			}

			if(SESSION_IP_MATCH)  {
				if($result->fields['ip'] != USER_IP) { 
					$this->delete($session_id);
					return FALSE;
				}
			}

		} else  {	 
			return FALSE;
		}

		return $result->fields;
	}


	function setcookies() {   

		if(defined("AGILE_COOKIE") && AGILE_COOKIE != '') {
			$domain = AGILE_COOKIE;
		} else {
			global $_SERVER; 
			if(isset($_SERVER)) {
				@$domain = $_SERVER['HTTP_HOST']; 
			} else {
				$server = getallheaders();
				$domain = $server['Host']; 
			} 
			$domain = '.'.preg_replace('/^www./', '', $domain);
		}			    

		if(COOKIE_EXPIRE == 0 )
		$cookie_expire = (time() + 86400*365);
		else
		$cookie_expire = (time() + (COOKIE_EXPIRE*60));
		if(empty($domain) || preg_match('/localhost/', $domain))
		setcookie(COOKIE_NAME,$this->id,$cookie_expire,'/');  
		else
		setcookie(COOKIE_NAME,$this->id,$cookie_expire,'/', $domain);      

		# Affiliate Cookie
		if(!empty($this->sess_affiliate_id)) {
			$aid_expire = time()+86400*720;
			$aid_cookie_name = COOKIE_NAME . 'aid';
			if(empty($domain) || preg_match('/localhost/i', $domain))
			setcookie($aid_cookie_name, $this->sess_affiliate_id, $aid_expire,'/');
			else
			setcookie($aid_cookie_name, $this->sess_affiliate_id, $aid_expire,'/', $domain); 
		}

		# Campaign Cookie
		if(!empty($this->sess_campaign_id)) { 
			$cid_expire = time()+86400*720;
			$cid_cookie_name = COOKIE_NAME . 'caid';
			if(empty($domain) || preg_match('/localhost/i', $domain))
			setcookie($cid_cookie_name, $this->sess_campaign_id, $cid_expire,'/');
			else
			setcookie($cid_cookie_name, $this->sess_campaign_id, $cid_expire,'/', $domain);            
		}
	}


	function get_affiliate($old_aid) {
		global $_COOKIE, $VAR;
		$aid_cookie_name = COOKIE_NAME.'aid'; 
		if(isset($VAR['aid']))
			$aid   = $VAR['aid'];
			else if(isset($_COOKIE[$aid_cookie_name]))
		@$aid  = $_COOKIE[$aid_cookie_name];
			else if(isset($HTTP_COOKIE_VARS[$aid_cookie_name]))
		@$aid  = $HTTP_COOKIE_VARS[$aid_cookie_name]; 
		if ($aid == $old_aid) {
			return $aid;
		} else if (empty($aid)) {
			return '';
		} else {
			// validate
			$db = &DB();
			$q = "SELECT id,account_id FROM " . AGILE_DB_PREFIX . "affiliate
				  WHERE id = ".$db->qstr($aid)."   AND
				  site_id  = ".$db->qstr(DEFAULT_SITE);
			@$result = $db->Execute($q);
			if(@$result->fields['id'] == $aid)
			return $aid;
			else
			return '';
		}
	}


	function get_campaign($old_cid) {
		global $_COOKIE, $VAR;
		$cid_cookie_name = COOKIE_NAME.'caid'; 
		if(isset($VAR['caid']))
			$cid   = $VAR['caid'];
		else if(isset($_COOKIE[$cid_cookie_name]))
			@$cid  = $_COOKIE[$cid_cookie_name];
		else if(isset($HTTP_COOKIE_VARS[$cid_cookie_name]))
			@$cid  = $HTTP_COOKIE_VARS[$cid_cookie_name];
		if ($cid == $old_cid) {
			return $cid;
		} else if (empty($cid)) {
			return '';
		} else {
			// validate
			$db = &DB();
			$q = "SELECT id FROM " . AGILE_DB_PREFIX . "campaign
				  WHERE id = ".$db->qstr($cid)."   AND
				  site_id  = ".$db->qstr(DEFAULT_SITE);
			@$result = $db->Execute($q);
			if(@$result->fields['id'] == $cid)
			return $cid;
			else
			return '';
		}
	}    


	function get_currency($id)  { 
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'currency WHERE id = ' . $db->qstr($id) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);   
		if($result->fields['status'] == 1)  return $id;  
		global $VAR; $VAR['cyid'] = DEFAULT_CURRENCY; 
		return DEFAULT_CURRENCY;  
	}    


	function session()  {
		global $C_debug;
		mt_srand ((double) microtime() * 1000000);
		$this->id = md5(uniqid(mt_rand(),1));
		$db = &DB();
		$q = "SELECT id FROM " . AGILE_DB_PREFIX . "session
			  WHERE id = ".$db->qstr($this->id)."   AND
			  site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);
		if ($result === false) {
			echo "SESSION FAILED: Unable to connect to database";
			exit;
		}  if($result->RecordCount() == 0) {
			$expires = time() + (SESSION_EXPIRE*60);
			$db = &DB();
			$q = "INSERT INTO " . AGILE_DB_PREFIX . "session SET
					id 			= ".$db->qstr($this->id).",
					date_orig	= ".$db->qstr(time()).",
					date_last	= ".$db->qstr(time()).",
					date_expire	= ".$db->qstr($expires).",
					logged		= ".$db->qstr('0').",
					ip			= ".$db->qstr(USER_IP).",
					site_id		= ".$db->qstr(DEFAULT_SITE).",
					affiliate_id= ".$db->qstr($this->sess_affiliate_id).",
					reseller_id	= ".$db->qstr($this->sess_reseller_id).",
					country_id	= ".$db->qstr($this->sess_country_id).",
					language_id	= ".$db->qstr($this->sess_language_id).",
					currency_id	= ".$db->qstr($this->sess_currency_id).",
					weight_id	= ".$db->qstr($this->sess_weight_id).",
					theme_id	= ".$db->qstr($this->sess_theme_id).",
					campaign_id = ".$db->qstr($this->sess_campaign_id);
			$result = $db->Execute($q);
			$C_debug->sql_count();
			if ($result === false) {
				$C_debug->error('session.inc.php','validate', $db->ErrorMsg());
				echo 'Unable to start session: Db error<RB><BR>' . $q . '<BR><BR>' . $db->ErrorMsg();
				exit;
			}
		}
	}


	function logout($sess) {
		$db = &DB();
		$q = "UPDATE " . AGILE_DB_PREFIX . "session SET logged = '0' WHERE
			  id = '$sess' AND
			  site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('session.inc.php','logout', $db->ErrorMsg());
		}

		$q      = 'DELETE FROM '.AGILE_DB_PREFIX.'session_auth_cache WHERE
				   session_id  = '. $db->qstr($sess) .' AND
				   site_id     = '. $db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		define('FORCE_SESS_ACCOUNT', 0);
		define('FORCE_SESS_LOGGED',  FALSE);

		if(CACHE_SESSIONS == '1') {
			$VAR['_login']  = '1';
			$force          = true;
			$C_auth  	    = new CORE_auth($force);
			global $C_auth2;
			$C_auth2 = $C_auth;			            		
		}                   	
	}


	function delete($sess) {
		global $C_debug;

		$db = &DB();
		$q = "DELETE FROM " . AGILE_DB_PREFIX . "session WHERE id = '$sess' AND site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);
		$C_debug->sql_count();
		if ($result === false) $C_debug->error('session.inc.php','delete', $db->ErrorMsg());	                	
	}


	function session_constant() {
		# Define the constants	   	
		define ('SESS_THEME',		$this->sess_theme_id);
		define ('SESS_COUNTRY',		$this->sess_country_id);
		define ('SESS_LANGUAGE',	$this->sess_language_id);
		define ('SESS_CURRENCY',	$this->sess_currency_id);
		define ('SESS_WEIGHT',		$this->sess_weight_id);
		define ('SESS_RESELLER',	$this->sess_reseller_id);
		define ('SESS_AFFILIATE',	$this->sess_affiliate_id);
		define ('SESS_CAMPAIGN',	$this->sess_campaign_id);
	}


	function session_constant_log() {
		global $VAR;
		if(isset($VAR['_login']) || isset($VAR['_logout'])) {
			$db = &DB();
			$q  = "SELECT logged,account_id FROM " . AGILE_DB_PREFIX . "session
				  WHERE id 	  = " . $db->qstr($this->id) . "
				  AND site_id = " . $db->qstr(DEFAULT_SITE);
			$result = $db->Execute($q);
			global $C_debug;
			$C_debug->sql_count();
			if ($result === false)  $C_debug->error('session.inc.php','session_constant', $db->ErrorMsg());
			if(!defined("SESS_LOGGED"))
				define ('SESS_LOGGED',  $result->fields['logged']);
			if(!defined("SESS_ACCOUNT"))
				define ('SESS_ACCOUNT',	$result->fields['account_id']);  		                  	
		} else {	            	
			if(!defined("SESS_LOGGED"))
				define ('SESS_LOGGED',	$this->sess_logged);
			if(!defined("SESS_ACCOUNT"))
				define ('SESS_ACCOUNT',	$this->sess_account_id);
		}

		if(SESS_LOGGED)
		define ('SESS_EXPIRES',		$this->sess_date_expire);
		else
		define ('SESS_EXPIRES',		0);
	}		
}
?>