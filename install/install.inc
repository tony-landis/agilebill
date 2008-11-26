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
	
global $VAR, $_SERVER, $HTTP_SERVER_VARS;

# get the file path
if (is_file($_SERVER["PATH_TRANSLATED"])) {
	$path = $_SERVER["PATH_TRANSLATED"];
} elseif (is_file($HTTP_SERVER_VARS["SCRIPT_FILENAME"])) {
	$path = $HTTP_SERVER_VARS["SCRIPT_FILENAME"];
} else {
	$path = PATH_AGILE;
}
$path = eregi_replace('index.php', '', $path);
$path = eregi_replace("[\\\\]", '/', $path);

# Get the current IP & Domain 
if(isset($_SERVER)) {
	$site_domain = @$_SERVER['HTTP_HOST'];
	$site_ip	= @$_SERVER['SERVER_ADDR'];
} else {
	$server = getallheaders();
	$site_domain = $server['Host'];
	$site_ip 	= false;
}

# req writable files/paths
$_FILES         = Array (   Array('file',   'config.inc.php'),
							Array('dir',    'includes/smarty/templates/'),
							Array('dir',    'includes/files/'),
							Array('dir',    'includes/images/') );

# Modules to skip over (duplicate tables)
$_SKIP_MODULES   = Array (  'account_admin' );

# Core modules needed
$_CORE_MODULES   = Array (  'module',  'module_method', 'group_method');

# Base modules needed
$_BASE_MODULES   = Array (  'setup',
							'account',
							'account_memo',
							'account_admin',
							'account_group',
							'account_billing',
							'backup',
							'blocked_email',
							'blocked_ip',
							'cart',
							'country',
							'currency',
							'checkout',
							'discount',
							'email_template',
							'email_template_translate',
							'group',
							'group_method',
							'invoice',
							'invoice_commission',
							'invoice_item',
							'invoice_item_tax',
							'invoice_item_discount',
							'invoice_memo',
							'invoice_setup',
							'login_lock',
							'login_log',
							'log_error',
							'module',
							'module_method',
							'newsletter',
							'net_term',
							'newsletter_subscriber',
							'product',
							'product_attr',
							'product_cat',
							'product_cat_translate',
							'product_img',
							'product_translate',
							'search',
							'search_saved',
							'session',
							'session_auth_cache',
							'setup_email',
							'setup_invoice',
							'service',
							'service_memo',
							'staff',
							'staff_department',
							'static_relation',
							'static_var',
							'static_var_record',
							'tax',
							'task',
							'temporary_data'
						);

# optional modules to check for
$_CHK_MODULES    = Array (  'affiliate'  	=> Array ('affiliate', 'affiliate_commission', 'affiliate_template'),  
							'asset'  		=> Array ('asset', 'asset_pool'),   	
							'campaign'		=> Array ('campaign'),
							'charge'	 	=> Array ('charge'),
							'db_mapping' 	=> Array ('db_mapping'),
							'email_queue'	=> Array ('email_queue'),
							'file' 		 	=> Array ('file', 'file_category'),
							'faq' 		 	=> Array ('faq', 'faq_category', 'faq_translate'),
							'htaccess'   	=> Array ('htaccess', 'htaccess_dir', 'htaccess_exclude'),
							'host_server'	=> Array ('host_server', 'host_registrar_plugin', 'host_tld'),
							'import'	 	=> Array ('import'),
							'login_share'	=> Array ('login_share'),
							'report'		=> Array ('report'),
							'radius'		=> Array ('radius','radius_service'),
							'static_page'	=> Array ('static_page', 'static_page_category','static_page_translate'),
							'ticket'     	=> Array ('ticket', 'ticket_department', 'ticket_message','ticket_attachment'),
							'voip'       	=> Array ('voip', 'voip_cdr', 'voip_did', 'voip_did_plugin', 'voip_fax','voip_fax_data', 'voip_in_network','voip_local_lookup','voip_pool','voip_rate','voip_rate_prod', 'voip_sip','voip_iax','voip_vm'),
							'voip_prepaid' 	=> Array ('voip_prepaid') );


# description of optional modules
$_MODULE_DESC    = Array (  'affiliate'  	=> '<u>Affiliate Module:</u> manage affiliates and commissions.',
							'asset'			=> '<u>Asset Management Module:</u> manage asset assignment status.',
							'campaign'		=> '<u>Campaign Module:</u> manage and track advertising campaigns.',
							'charge'	 	=> '<u>Charge Module:</u> manage incremental billing for accounts and services.',
							'db_mapping' 	=> '<u>DB Mapping Module:</u> sync your users to 3rd party databases, such as forums, CMS, etc.',
							'email_queue'	=> '<u>E-mail Queue:</u> Store e-mails in a queue before sending to improve the speed of the user/admin interface.',
							'file'       	=> '<u>File Module:</u> upload files that users can download based on their groups.',
							'faq'		 	=> '<u>FAQ Module:</u> manage a Frequently Asked Questions list and enables canned responses in the ticket module.',
							'htaccess'   	=> '<u>Htaccess Module:</u> protect php files and/or entire directories with htaccess based on groups and sell access.',
							'import'	 	=> '<u>Import Module:</u> manage custom imports from other programs.',
							'host_server'	=> '<u>Hosting Module:</u> sell webhosting services, domains, and automate server provisioning and domain registration.',
							'login_share'	=> '<u>Login Sharing:</u> automatically detect and ban password sharing.', 
							'report'		=> '<u>Report Module:</u> report generation for account, invoice, and other data.',
							'radius'		=> '<u>Radius Module:</u> provisioning control over Radius authentication data.',
							'static_page'	=> '<u>Static Page Module:</u> create and manage static pages on your site from your admin area.',
							'ticket'     	=> '<u>Ticket Module:</u> support ticket management.',
							'voip'		 	=> '<u>VoIP Module:</u> the core VoIP specific AgileVoice functionality.',
							'voip_prepaid'  => '<u>VoIP Prepaid Module:</u> the added VoIP Prepaid AgileVoice functionality.' );


# define the default group to add all methods to
$VAR["module_group"] = Array('1001');


# action handler
if (!empty($VAR['do']))
{
	$do = $VAR['do'];
	switch ($VAR['do'])
	{
		# check file permissions:
		case 'step1':
			$files =  test_files ($_FILES);
			$s_file = 'step1.php';
		break;

		# write the new config vars
		case 'step2':
			$files =  write_config($VAR);
			$s_file = 'step2.php';

			# get the license code
			$filename = "License.txt";
			if(is_file($filename)) {
				$handle = fopen($filename, "r");
				$license_agreement = fread($handle, filesize($filename));
				fclose($handle);
			}                

			# check for available optional modules
			$i=0;
			if ($files === true) {
				while (list($n,$v) = each($_CHK_MODULES))
				{
					if(is_dir('modules/'. $n )) {
						$modules[$i]['name'] = $n;
						$modules[$i]['desc'] = $_MODULE_DESC[$n];
						$i++;
					}
				}
			}
		break;

		# test the database connection & select the modules to install
		case 'step3':
			$database =  test_connection ();
			$s_file = 'step3.php';
			if($database === true) {
				require_once('install/install_db_core.inc');

				$dbinst = new install_db;
				$dbinst->core_modules = $_CORE_MODULES;
				$dbinst->skip_modules = $_SKIP_MODULES;

				# create core tables
				for($i=0; $i<count($_CORE_MODULES); $i++)
					$dbinst->install_sql_tbl($_CORE_MODULES[$i]);

				# create base modules
				for($i=0; $i<count($_BASE_MODULES); $i++)
					$dbinst->install_sql($_BASE_MODULES[$i]);

				# insert the data
				$dbinst->add_data($_BASE_MODULES);

				# check for user-defined modules
				if(is_array(@$VAR['modules'])) {
					for($i=0; $i<count($VAR['modules']); $i++) {
						$n = $VAR['modules'][$i];
						if(!empty($n)) {
							$arr = $_CHK_MODULES[$n];
							for($ii=0; $ii<count($arr); $ii++)
								$dbinst->install_sql($arr[$ii]);
							$dbinst->add_data($arr);
						}
					}
				}

				# insert license key and code into setup table
				$fields=Array('license_key'=> @$VAR['license_key'], 'license_code'=> @$VAR['license_code']);
				$db->Execute(sqlUpdate($db,"setup",$fields,"site_id=".DEFAULT_SITE));
			}
		break;

		# select the database connection
		case 'step4':
			$s_file = 'step4.php';
		break;
	}
}
else
{
	$do = 'step1';
	$files =  test_files ($_FILES);
	$s_file = 'step1.php';
}

# Load the template
include('install/template.inc');




#########################################################################################
# Write the new config file
#########################################################################################
function write_config ($VAR)
{
	# get the config template
	if ($file = fopen('install/config.inc', 'r'))
	{
		$data = '';
		while (!feof($file))
			$data .= fgetc($file);
		fclose($file);

		# replace the user vars
		$data = ereg_replace('%%PATH_AGILE%%',         @$VAR['path_agile'],    $data);
		$data = ereg_replace('%%AGILE_DB_TYPE%%',      @$VAR['db_type'],       $data);
		$data = ereg_replace('%%AGILE_DB_HOST%%',      @$VAR['db_host'],       $data);
		$data = ereg_replace('%%AGILE_DB_DATABASE%%',  @$VAR['db_database'],   $data);
		$data = ereg_replace('%%AGILE_DB_USERNAME%%',  @$VAR['db_username'],   $data);
		$data = ereg_replace('%%AGILE_DB_PASSWORD%%',  @$VAR['db_password'],   $data);
		$data = ereg_replace('%%AGILE_DB_PREFIX%%',    @$VAR['db_prefix'],     $data);

		if(!defined("DEFAULT_SITE")) define("DEFAULT_SITE", 1);
		$data = str_replace('%%AGILE_DEFAULT_SITE%%', DEFAULT_SITE, $data); 

		# write the new config file
		if ($file = fopen('config.inc.php', 'w')) {
			fputs($file, $data);
			return true;
		} else {
			return false;
		}
	} else {
		return false;
	}
}




#########################################################################################
# Check file/directory permissions
#########################################################################################
function test_files ($_arr_perm)
{
	# check each file/dir
	$_perm_err = false;
	for($i=0; $i<count($_arr_perm); $i++)
	{
		if($_arr_perm[$i][0] == 'file')
		{
			if (!file_exists($_arr_perm[$i][1]) || !is_writable($_arr_perm[$i][1]))  {
				$_perm_err .=  "<br><br><b>File Permission Problem Detected:</b><br>".
								"The file `<u>". $_arr_perm[$i][1] . "</u>` must be set set to writable. [ERROR-1]";
			}
		}
		else
		{
			if (!is_dir($_arr_perm[$i][1]) || !is_writable($_arr_perm[$i][1]))  {
				$_perm_err .=  "<br><br><b>Directory Permission Problem Detected:</b><br>".
								"The directory `<u>". $_arr_perm[$i][1] . "</u>` must be set set to writable. [ERROR-2]";
			}
		}
	}
	if(empty($_perm_err))
	return true;
	else
	return $_perm_err;
}


#########################################################################################
# Check database connection
#########################################################################################
function test_connection()
{
	if(AGILE_DB_PREFIX=="") {
		return  "<br><br><b>Database Configration Problem Detected:</b><br>".
		"You must return to Step 1 of the installation process and enter a database prefix.";
	}

	# Attempt to connect to the database
	$db         = &DB();
	$err        = @$db->ErrorMsg();
	if(!empty($err) )
	{
		if (eregi('No Database Selected', $err) || eregi('Unknown database', $err))
		{
			# attempt to create the database
			$result = $db->Execute('CREATE DATABASE '.AGILE_DB_DATABASE);
			if($result === false)
			return  "<br><br><b>Database Problem Detected:</b><br>".
					"The database named `".AGILE_DB_DATABASE."` does not exist on `".AGILE_DB_HOST."`, ".
					"and the user/pass provided does not have permission to create it. [ERROR-3]";
			else
				$result = true;
		}
		elseif (eregi('Access denied', $err))
		{
			return  "<br><br><b>Database Problem Detected:</b><br>".
					"The database `".AGILE_DB_DATABASE."` cannot be accessed with the user user/pass provided. [ERROR-4]";
		}
		else
		{
			return  "<br><br><b>Database Problem Detected:</b><br>".
					$err. " [ERROR-5]";
		}
	}

	# drop table
	# $db->Execute('DROP TABLE '. AGILE_DB_DATABASE);

	if(defined('DEFAULT_SITE') && DEFAULT_SITE == 1)
	{
		# attempt table creation:
		$db         = &DB();
		$dict       = NewDataDictionary($db);
		$flds       = "col1 C(32), col2 C(32)";
		$sqlarray   = $dict->CreateTableSQL('1st_delete_this', $flds);
		$result     = $db->Execute($sqlarray[0]);
		if ($result === false) {
			echo $db->ErrorMsg();
			   return   "<br><br><b>Database Problem Detected:</b><br>".
						"You do not have the correct access to the database; unable to create tables. [ERROR-6]";
		}

		# attempt index creation:
		$db         = &DB();
		$idxflds    = 'co11, col2';
		$sqlarray = $dict->CreateIndexSQL('id', '1st_delete_this', $idxflds);
		$dict->ExecuteSQLArray($sqlarray);
		if ($result === false) {
			   return   "<br><br><b>Database Problem Detected:</b><br>".
						"You do not have the correct access to the database; unable to create indexes. [ERROR-7]";
		}  

		# database tests passed:
		$db->Execute('DROP TABLE 1st_delete_this');
	}


	return true;
} 
?>