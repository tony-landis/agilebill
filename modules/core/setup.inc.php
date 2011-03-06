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
	
class CORE_setup
{			
	function CORE_setup()
	{  
		if(defined('MEMCACHE_ENABLED') && MEMCACHE_ENABLED == true) {
			require_once (PATH_INCLUDES. 'cache/cache.php');	    
			$key = md5('keyname1'.__FILE__.DEFAULT_SITE);
			$sec = 60*30;      
			$timeout = get_value($key.'_exp');
			if($timeout == "" || $timeout < time() || !$fields = get_value($key) )
			{    
				$fields = $this->get_setup();
				store_value($key, $fields);
				store_value($key.'_exp', time()+$sec); 
			}  
		} else {
			$fields = $this->get_setup();
		}

		define ('DEFAULT_COUNTRY',  	$fields['country_id']);
		define ('DEFAULT_LANGUAGE',	 	$fields['language_id']);
		define ('DEFAULT_CURRENCY', 	$fields['currency_id']);
		define ('DEFAULT_WEIGHT',   	$fields['weight_id']);
		define ('DEFAULT_THEME',    	$fields['theme_id']);
		define ('DEFAULT_ADMIN_THEME',  $fields['admin_theme_id']);
		define ('DEFAULT_GROUP',    	$fields['group_id']);
		define ('DEFAULT_AFFILIATE_TEMPLATE',    $fields['affiliate_template_id']);
		define ('DEFAULT_AFFILIATE',    $fields['affiliate_id']);
		define ('DEFAULT_RESELLER',    	$fields['reseller_id']);
		define ('DEFAULT_SETUP_EMAIL', 	$fields['setup_email_id']);
		define ('DEFAULT_TIME_FORMAT', 	$fields['time_format']);
		define ('DEFAULT_ACCOUNT_STATUS',$fields['default_account_status']);
		$this->default_date_format($fields['date_format']);
		if(!defined("DEFAULT_TIME_FORMAT"))
		define ('DEFAULT_TIME_FORMAT', 	$fields['time_format']);
		define ('DEFAULT_DATE_TIME_FORMAT', $fields['date_time_format']);
		define ('DEFAULT_DECIMAL_PLACE',$fields['decimal_place']);
		define ('COOKIE_NAME',  		$fields['cookie_name']);
		define ('COOKIE_EXPIRE', 		$fields['cookie_expire']);
		define ('SESSION_IP_MATCH', 	$fields['session_ip_match']);
		define ('SESSION_EXPIRE', 		$fields['login_expire']);
		define ('NEWSLETTER_REGISTRATION',$fields['newsletter_registration']);
		define ('SEARCH_EXPIRE',		$fields['search_expire']);	        	        
		define ('ERROR_REPORTING', 		$fields['error_reporting']);
		define ('DEBUG',  				$fields['debug']);

		define ('LOGIN_ATTEMPT_TRY', 	$fields['login_attempt_try']);
		define ('LOGIN_ATTEMPT_TIME',	$fields['login_attempt_time']);
		define ('LOGIN_ATTEMPT_LOCK',	$fields['login_attempt_lock']);
		define ('DB_CACHE', 			$fields['db_cache']);
		define ('CACHE_SESSIONS',    	$fields['cache_sessions']);
		define ('WEBLOG',    	        $fields['weblog']);
		define ('LICENSE_KEY',		  	$fields['license_key']);
		define ('LICENSE_CODE',  		$fields['license_code']);

		if(!defined('SSL_URL')) 	define ('SSL_URL', 	$fields['ssl_url']);
		if(!defined('URL')) 		define ('URL', 		$fields['nonssl_url']);	        
		if(!defined('SITE_NAME')) 	define ('SITE_NAME',   $fields['site_name']);
		if(!defined('SITE_EMAIL')) 	define ('SITE_EMAIL',  $fields['site_email']);			
		if(!defined('SITE_ADDRESS'))define ('SITE_ADDRESS',$fields['site_address']);
		if(!defined('SITE_CITY')) 	define ('SITE_CITY',   $fields['site_city']);
		if(!defined('SITE_STATE')) 	define ('SITE_STATE',  $fields['site_state']);
		if(!defined('SITE_ZIP')) 	define ('SITE_ZIP',    $fields['site_zip']);
		if(!defined('SITE_PHONE')) 	define ('SITE_PHONE',  $fields['site_phone']);
		if(!defined('SITE_FAX')) 	define ('SITE_FAX',    $fields['site_fax']);

		if($fields['os'] == 1)
		define ('AGILE_OS',             'WINDOWS');
		else
		define ('AGILE_OS',             'LINUX');

		define ('PATH_CURL',            $fields['path_curl']);
		define ('SHOW_AFFILIATE_LINK',  $fields['show_affiliate_link']);
		define ('AUTO_AFFILIATE',  		@$fields['auto_affiliate']);
		define ('SHOW_TICKET_LINK',     $fields['show_ticket_link']);
		define ('SHOW_NEWSLETTER_LINK', $fields['show_newsletter_link']);
		define ('SHOW_CONTACT_LINK',    $fields['show_contact_link']);
		define ('SHOW_DOMAIN_LINK',     $fields['show_domain_link']);
		define ('SHOW_CART_LINK',       $fields['show_cart_link']);
		define ('SHOW_CHECKOUT_LINK',   $fields['show_checkout_link']);
		define ('SHOW_PRODUCT_LINK',    $fields['show_product_link']);
		define ('SHOW_CAT_BLOCK',       $fields['show_cat_block']);
		define ('SHOW_FILE_BLOCK',      $fields['show_file_block']);
		define ('SHOW_STATIC_BLOCK',    $fields['show_static_block']);
		define ('SHOW_AFFILIATE_CODE',  $fields['show_affiliate_code']);
		define ('SHOW_DISCOUNT_CODE',   $fields['show_discount_code']);
		define ('BILLING_WEEKDAY',      $fields['billing_weekday']);
		define ('GRACE_PERIOD',         $fields['grace_period']);
		define ('MAX_BILLING_NOTICE',   $fields['max_billing_notice']);
		define ('MAX_INV_GEN_PERIOD',   $fields['max_inv_gen_period']);

		$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
		eval($error_reporting_eval);	
	}

	function get_setup()
	{ 
		$db = &DB();
		$q = "SELECT * FROM " . AGILE_DB_PREFIX . "setup WHERE site_id = ". DEFAULT_SITE;
		$result = $db->Execute($q); 
		if ($result === false || @$result->RecordCount() == 0) {
			if(is_file('install/install.inc'))
				require_once('install/install.inc');
			else
				$this->handle_failure($db);
			exit;
		} else {
			return $result->fields;
		}
	}		

	function default_date_format($default)
	{
		$default = unserialize($default);
		$format = '';
		$divider = $default[3];
		for($i=0; $i<3; $i++)
		{
			$format .= $default[$i];
			if($i != 2)
			$format .= $divider;
		}
		$arr = Array('a','A','b','B','d','j','m','u','y','Y');
		for($i=0; $i<count($arr); $i++)
		  $format = preg_replace('/'.$arr[$i].'/','%'.$arr[$i],$format);
		define ('DEFAULT_DATE_FORMAT', $format);
		$UNIX_DATE_FORMAT = preg_replace('/%/','', DEFAULT_DATE_FORMAT);
		define ('UNIX_DATE_FORMAT', $UNIX_DATE_FORMAT);
		define ('DEFAULT_DATE_DIVIDER', $divider);	
	}

	/**
	 * Handle a database connection failure gracefully
	 */
	function handle_failure(&$db) {

		// echo error page
		include_once(PATH_THEMES . 'default/blocks/core/error.tpl');

		// log the error  
		if($f=fopen(PATH_FILES.'sql_error.txt', 'a')) {
			$data = date("m-d-Y H:i:s a") . "		" . $db->_errorMsg . "\r\n";
			fputs($f,$data);
		} 

		exit;		
	}
}
?>