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
	
class CORE_login_handler
{

	function login($VAR, $md5=true)
	{
		global $C_translate, $C_debug; 

		# check that the username/password are both set
		if(($VAR['_username'] == '') || ($VAR['_password'] == ''))
		{
			$C_debug->alert($C_translate->translate('login_enter_both','',''));
			return;
		}

		# md5 the password
		if($md5)
		$pass = md5($VAR['_password']);
		else
		$pass = $VAR['_password'];

		# check the database for a match
		$db = &DB();
		$q = "SELECT id,status,username,password,date_expire FROM " . AGILE_DB_PREFIX . "account WHERE
				password = '$pass' AND
				username = '".$VAR['_username']."' AND
				site_id  = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# get the account id
		$id = $result->fields['id'];

		# check that their is no lock on this account id or IP address:
		if($this->locked ($id))
		{
			$C_debug->alert($C_translate->translate('login_locked','',''));								
			return;
		}


		# verify the username/password match.
		if($result->fields['username'] == $VAR['_username'])
		{
			if (($result->fields['password'] !== $VAR['_password']) && ($result->fields['password'] != $pass))
			{ 
				# no match
				$C_debug->alert($C_translate->translate('login_pw_failed','',''));

				# log as a failed login
				$this->lock_check($VAR,"0",$id);
				return;
			}
		}
		else
		{
			# no username match
			$C_debug->alert($C_translate->translate('login_un_pw_failed','',''));

			# reload the login page
			$VAR["_page"] = 'account:login';				

			# log as a failed login
			$this->lock_check($VAR,"0",$VAR['_username']);
			return;
		}


		if($result->fields['date_expire'] == "0" || $result->fields['date_expire'] == "")	
		$date_expire = time()+99;
		else
		$date_expire = $result->fields['date_expire'];


		# check that it is an active account
		if($result->fields['status'] != "1" || $date_expire <= time())
		{
			# inactive account
			$C_debug->alert($C_translate->translate('login_inactive','',''));			

			# log as failed login
			$this->lock_check($VAR,"0",$id);
			return;
		}
		else 
		{
			# active account - check for password sharing if login_share module is installed
			include_once(PATH_CORE.'list.inc.php');
			$C_list = new CORE_list; 
			if($C_list->is_installed('login_share'))
			{  					
				include_once(PATH_MODULES.'login_share/login_share.inc.php');
				$share = new login_share;
				if(!$share->login($id, $VAR['_username'])) 
				{
					# shared account alert
					$C_debug->alert($C_translate->translate('shared_account','login_share',''));

					# log as failed login
					$this->lock_check($VAR,"0",$id);

					return;		
				}	
			}					
		}

		# set the expiry date of the login session
		$date_expire = (time() + (SESSION_EXPIRE * 60));

		# update the DB
		$db = &DB();
		$q = "UPDATE " . AGILE_DB_PREFIX . "session
				SET
				ip= '". USER_IP ."',
				date_expire = '$date_expire',
				logged = '1',
				account_id = '$id'
				WHERE
				id = '" . SESS . "'
				AND
				site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		# delete any old sessions for this account
		$db = &DB();
		$q = "DELETE FROM " . AGILE_DB_PREFIX . "session   WHERE
				account_id = '$id' 	AND
				id != '" . SESS . "' AND
				site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		#return logged in message
		$C_debug->alert($C_translate->translate('login_success','',''));


		# Get the last successful login:
		$db = &DB();
		$q = "SELECT * FROM  " . AGILE_DB_PREFIX . "login_log   WHERE
			  account_id    = ". $db->qstr($id)." 	AND
			  status        = ". $db->qstr(1)."      AND
			  site_id       = ". $db->qstr(DEFAULT_SITE) . "
			  ORDER BY date_orig DESC LIMIT 1";
		$result = $db->Execute($q);
		if($result->RecordCount() != 0)
		{
			$ip   = $result->fields["ip"];
			$date = $result->fields["date_orig"];
			$date1 = date(UNIX_DATE_FORMAT, $date);	 		 	
			$date1.= "  ".date(DEFAULT_TIME_FORMAT, $date);

			$message = $C_translate->translate('login_log_success','','');
			$message = preg_replace('/%date%/', $date1, $message);
			$message = preg_replace('/%ip%/', $ip, $message);
			$C_debug->alert($message);
		}


		# log the successful login
		$this->lock_check($VAR,"1",$id);


		####################################################################
		### Do any db_mapping
		####################################################################
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr('db_mapping') . ' AND
					status      = ' . $db->qstr("1");
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->login ( $id );
		}
	}




	function logout ($VAR)
	{
		global $C_debug, $C_translate;
		$db = &DB();

		# get the account id (for DB mapping):
		$q = "SELECT account_id FROM ". AGILE_DB_PREFIX ."session WHERE
			 id = '" . SESS . "' AND
			 site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);
		$account_id = $result->fields['account_id'];

		# logout the current session by editing the database record
		$q = "UPDATE ". AGILE_DB_PREFIX ."session SET logged='0'
			 WHERE id = '" . SESS . "' AND
			 site_id = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);


		# delete any session caches!
		$q      = 'DELETE FROM '.AGILE_DB_PREFIX.'session_auth_cache WHERE
				session_id  = '. $db->qstr(SESS) .' AND
				site_id     = '. $db->qstr(DEFAULT_SITE);
		$db->Execute($q);	

		# logout success:
		$C_debug->alert($C_translate->translate('logout_success','',''));

		####################################################################
		### Do any db_mapping
		####################################################################
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr('db_mapping') . ' AND
					status      = ' . $db->qstr("1");
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0) {
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->logout ( $account_id );
		}
	}




	function locked ($account_id)
	{
		if($account_id != '')
			$sql = " OR account_id = '$account_id' AND ";
		else
			$sql = " AND ";

		# check by IP & USER
		$db = &DB();
		$q = "SELECT id FROM " . AGILE_DB_PREFIX . "login_lock WHERE
				ip 			= '" . USER_IP . "'";
		$q .=	$sql;
		$q .= " date_expire >= '" . time() . "' AND
				site_id 	= '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);

		$i = 0;
		while (!$result->EOF)
		{
			$i++;
			$result->MoveNext();
		}

		# return the results
		if ($i > 0)
			return true;
		else
			return false;
	}



	function lock_check ($VAR,$status,$account_id)
	{
		# if this is a success, delete all login old login records..
		/*
		if($status == 1)
		{
			# delete all login attempts for this account
			#  (to clean the slate after the account login lock expires)
			$db = &DB();
			$q = "DELETE FROM " . AGILE_DB_PREFIX . "login_log WHERE
					account_id  =  '$account_id' AND
					site_id     = '" . DEFAULT_SITE . "'";
			$result = $db->Execute($q);
		}
		*/

		# create the appropriate login attempt record.
		$db = &DB();
		$login_id = $db->GenID(AGILE_DB_PREFIX . 'login_log_id');
		$q = "INSERT INTO " . AGILE_DB_PREFIX . "login_log SET
				id          =  " . $db->qstr($login_id) . ",
				ip 			=  " . $db->qstr( USER_IP ) . ",
				account_id	=  " . $db->qstr($account_id ) . ",
				date_orig 	=  " . $db->qstr(time()) . ",
				status		=  " . $db->qstr($status ) . ",
				site_id 	=  " . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);

		# if this is a successfull login, we can now exit...
		if($status == 1)  return;

		# determine the time period to check for login attempts after:
		$date_orig = (time() - (LOGIN_ATTEMPT_TIME*60));

		# check the database for all the failed login attempts from
		# this IP withing the time period defined in the setup.
		$q = "SELECT id FROM " . AGILE_DB_PREFIX . "login_log WHERE
				ip 			=  '" . USER_IP . "' AND
				date_orig 	>= '$date_orig' 	AND
				status		=  '0' 				AND
				site_id     = '" . DEFAULT_SITE . "'";
		$result = $db->Execute($q);
		$i = 0;
		while (!$result->EOF)
		{
			$i++;
			$result->MoveNext();
		}


		# check that it does not exceed the allowed failed login attempts
		if($i >= LOGIN_ATTEMPT_TRY)
		{
			# get the time this login block will expire:
			$date_expire = (time() + (LOGIN_ATTEMPT_LOCK * 60));

			# delete all old blocks for this ip
			$q = "DELETE FROM " . AGILE_DB_PREFIX . "login_lock WHERE
					ip          =  '" . USER_IP . "' AND
					site_id     = '" . DEFAULT_SITE . "'";
			$result = $db->Execute($q);

			# create a block on this login
			$q = "INSERT INTO " . AGILE_DB_PREFIX . "login_lock SET
					ip          =  '" . USER_IP . "',
					date_orig   =  '".time()."',
					date_expire =  '$date_expire',
					site_id     = '" . DEFAULT_SITE . "'";
			$result = $db->Execute($q);

			# delete all login attempts for this account
			#  (to clean the slate after the account login lock expires)
			$q = "DELETE FROM " . AGILE_DB_PREFIX . "login_log WHERE
					ip          =  '" . USER_IP . "' AND
					status		= '0' AND
					site_id     = '" . DEFAULT_SITE . "'";
			$result = $db->Execute($q);
		}
	}
}
?>