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
	
define('VB3_COOKIE_PREFIX', 'bb');

################################################################################
### Database Map for: vBulletin 3.x
################################################################################


class map_VBULLETIN_3
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_VBULLETIN_3 ()
    {
        $this->map =
            Array (
                'map'           => 'vBulletin_3',
                'db_type'       => 'mysql',
                'notes'         => 'This is for vBulletin 3',
                'group_type'    => 'db-status',    // db, db-status, status, none


                ### Define the group fields in the target db
                'group_map'     =>
                    Array
                    (
                        'table'     => 'usergroup',
                        'id'        => 'usergroupid',
                        'name'      => 'title'
                    ),


                ### Define the account mapping properties
                'account_map_field' 		=> 'user',
                'account_status_field' 		=> 'usergroupid',
                'account_default_status' 	=> '2',

                'account_sync_field'=>
                    Array
                    (
                        'add'       => 'username,email,password',
                        'edit'      => 'username,email,password',
                        'import'    => 'username,email,password',
                        'export'    => 'username,email,password',
                        'delete'    => '1'
                    ),

                'account_fields'    =>
                    Array
                    (
                        'id'        =>
                            Array
                            (
                                'map_field'      => 'userid'
                            ),
                        'date_orig'     =>
                            Array
                            (
                                'map_field'      => 'joindate'
                            ),

                        'date_last'     =>
                            Array
                            (
                                'map_field'      => 'lastactivity'
                            ),

                        'date_expire'   =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'language_id'   =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'country_id'    =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'username'      =>
                            Array
                            (
                                'map_field'      => 'username'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'password'
                            ),

                        'misc'          =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'first_name'    =>
                            Array
                            (
                                'map_field'      => false,
                            ),

                        'last_name'     =>
                            Array
                            (
                                'map_field'      => false,
                            ),

                        'middle_name'   =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'company'       =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'status'         =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'email'         =>
                            Array
                            (
                                'map_field'      => 'email'
                            ),

                        'email_type'    =>
                            Array
                            (
                                'map_field'      => false
                            )
                        ),

                    ### Define any extra fields for account table in the target db:
                    'extra_field' =>
                        Array ( 0 )
                    );

        }





        ########################################################################
        ### Syncronize all accounts & groups
        ########################################################################

        function sync($id, $file)
        {
            $db_map = new db_mapping;
            $db_map->MAP_sync ($id, $file, $this);
        }



        ########################################################################
        ### Create a new account in the target DB
        ########################################################################

        function account_add($account_id)
        {  
			$db_map_misc = new dbmapping_vb3;
			$db_map_misc->MAP_account_add ($account_id, $this);

            ### Sync the groups for this account: 
            $remote_account_id = $this->account_group_sync( $account_id ); 
        }



        ########################################################################
        ### Edit an existing account in the target DB
        ########################################################################

        function account_edit($account_id, $old_username)
        {
            $db_map_misc = new dbmapping_vb3;
			$db_map_misc->MAP_account_edit ($account_id, $old_username, $this);

            ### Update the groups in the remote db 
            $this->account_group_sync($account_id);
        }



        ########################################################################
        ### Delete an existing account from the target DB
        ########################################################################

        function account_delete($account_id, $username)
        {
			$db_map_misc = new dbmapping_vb3;
			$db_map_misc->MAP_account_delete ($account_id, $username, $this);
              
            /*                    	
            $db_map = new db_mapping;
            $remote_account_id = $db_map->MAP_account_delete ($account_id, $username, $this);
            
            ### Update the remote account:
            $dbm    = new db_mapping;
        	$db2     = $dbm->DB_connect(false, $this->map['map']);
        	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' );
            $sql = "DELETE FROM " .
                    $db_prefix . "userfield WHERE userid =  " .
                    $db2->qstr($remote_account_id);
            $db2->Execute($sql);
              
            $sql = "DELETE FROM " .
                    $db_prefix . "usertextfield WHERE userid =  " .
                    $db2->qstr($remote_account_id);
            $db2->Execute($sql);  
            */          
        }



        ########################################################################
        ### Export / Update all accounts / groups to the target DB
        ########################################################################

        function account_import($remote_account_id)
        {
            $msg = 'Import from VB3 not supported due to the unique password encryption used in VB3';            
            global $C_debug;
            $C_debug->alert($msg);
        }




        ########################################################################
        ### Create the cookie/session for login sync
        ########################################################################

        function login($account_id, $cookie)
        {
			$db_map_misc = new dbmapping_vb3;
			$db_map_misc->MAP_account_login ($account_id, $cookie, $this);
        }


        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################

        function logout($account_id, $cookie)
        {
			$db_map_misc = new dbmapping_vb3;
			$db_map_misc->MAP_account_logout ($account_id, $cookie, $this);
        }


        ########################################################################
        ### Syncronize the groups for a specific account in the remote DB
        ########################################################################

        function account_group_sync($account_id)
        {
            if ( $this->map['group_type'] == 'db')
            {
                $db_map = new db_mapping;
                return $db_map->MAP_account_group_sync_db ($account_id, $this);
            }
            elseif  ( $this->map['group_type'] == 'status')
            {
                $db_map = new db_mapping;
                return  $db_map->MAP_account_group_sync_status ($account_id, $this);
            }
            elseif  ( $this->map['group_type'] == 'db-status')
            {
                $db_map = new db_mapping;
                return  $db_map->MAP_account_group_sync_db_status ($account_id, $this);
            }
            else
            {
                return false;
            }
        }
}


        
        
        
        
        
        
     
class dbmapping_vb3 extends db_mapping
{ 
	
	### ADD ACCOUNT
	function MAP_account_add ($account_id, $MAP_this)
	{
		### Get the account details from AB
		$db = &DB();
		$sql= 'SELECT username,email,password FROM '.AGILE_DB_PREFIX.'account WHERE
    		       id  	= '.$db->qstr($account_id).' AND
    		       site_id 		= '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql);
		if ($account === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_account_username', $db->ErrorMsg());
		}
		unset($db); 
		
		# GENERATE SALT:
		$salt = '';
		for ($i = 0; $i < 3; $i++) 
			$salt .= chr(rand(32, 126)); 
					
		$username = $account->fields['username'];
		$email	  = $account->fields['email'];
		$password = md5( $account->fields['password'] . $salt );
		
		/*
		# GENERATE PASSWORD:
		if(!empty($MAP_this->plaintext_password))
			$password = md5(md5(@$MAP_this->plaintext_password) . $salt);
		else
			$password = md5( $account->fields['password'] . $salt );
		*/
		 
		### Insert the new account into VB3 
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'INSERT INTO ' . $db_prefix . 'user SET  
				displaygroupid 	= '.$dbm->qstr(0) . ',
				username 		= '.$dbm->qstr($username) . ',
				password		= '.$dbm->qstr($password) . ',
				passworddate	= '.$dbm->qstr(date("Y-m-d")) . ',
				email			= '.$dbm->qstr($email) . ',
				styleid			= '.$dbm->qstr(0) . ',
				showvbcode		= '.$dbm->qstr(2) . ',
				customtitle		= '.$dbm->qstr(0) . ',
				joindate		= '.$dbm->qstr(time()) . ',
				daysprune		= '.$dbm->qstr(0) . ',
				lastvisit		= '.$dbm->qstr(time()) . ',
				lastactivity	= '.$dbm->qstr(time()) . ',
				reputationlevelid = '.$dbm->qstr(1) . ',
				options			= '.$dbm->qstr('2135') . ',
				salt			= '.$dbm->qstr($salt) ;
		$result = $dbm->Execute($sql);  
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false;
		}	 
	}
	
	
	
	### EDIT ACCOUNT
	function MAP_account_edit($account_id, $old_username, $MAP_this)
	{
		### Get the account details from AB
		$db = &DB();
		$sql= 'SELECT username,email,password FROM '.AGILE_DB_PREFIX.'account WHERE
    		       id  	= '.$db->qstr($account_id).' AND
    		       site_id 		= '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql);
		if ($account === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_account_username', $db->ErrorMsg());
		}
		unset($db); 
		
		# GENERATE SALT:
		$salt = '';
		for ($i = 0; $i < 3; $i++) 
			$salt .= chr(rand(32, 126));
						
		$username = $account->fields['username'];
		$email	  = $account->fields['email'];
		$password = md5( $account->fields['password'] . $salt );
		
		/*
		if(!empty($MAP_this->plaintext_password))
		{  
			$password = md5(md5($MAP_this->plaintext_password) . $salt);
		} else {
			$password = md5( $account->fields['password'] . $salt );
		}
		*/
		
		### Update the account in VB3 
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'UPDATE ' . $db_prefix . 'user SET  ';  
		
		if(@$MAP_this->plaintext_password != false)
		$sql .= ' password		= '.$dbm->qstr($password) . ',				
				salt			= '.$dbm->qstr($salt) . ',';
		
		$sql .= ' passworddate	= '.$dbm->qstr(date("Y-m-d")) . ',
				username 		= '.$dbm->qstr($username) . ',
				email			= '.$dbm->qstr($email) . ' 
				WHERE
				username 		= '.$dbm->qstr($old_username) ;
		
		$result = $dbm->Execute($sql);  
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false;
		}			
	}
	
	
	function MAP_account_delete ($account_id, $username, $MAP_this)
	{
		global $C_debug;

		### Get the remote account id from the username 
		$dbm    = new db_mapping;
		$db2    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'SELECT userid FROM ' . $db_prefix . 'user
				WHERE username = '.$db2->qstr($username);
		$result = $db2->Execute($sql);
		if ($result === false) { 
			$C_debug->error('vBulletin_3.php','MAP_account_delete (1)', $db2->ErrorMsg() . '  ' . $sql);
			return false;
		}
		
		$vb_user_id = $result->fields['userid']; 

		# Suspend the user user:
        $sql = "UPDATE " . $db_prefix . "user SET usergroupid = '1' WHERE userid =  " . $db2->qstr($vb_user_id);
        $result = $db2->Execute($sql);		
		if ($result === false) { 
			$C_debug->error('vBulletin_3.php','MAP_account_delete (2)', $db2->ErrorMsg() . '  ' . $sql); 
		}  
	}
	
	
	### LOGIN
	function MAP_account_login ($account_id, $cookie, $MAP_this)
	{    
		### Get the username from the account_id
		$db = &DB();
		$sql= 'SELECT username,password FROM '.AGILE_DB_PREFIX.'account WHERE
    		       id  	= '.$db->qstr($account_id).' AND
    		       site_id 		= '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_account_username', $db->ErrorMsg());
		}
		unset($db);

		$username = $result->fields['username'];
		$password = $result->fields['username'];

		### Get the remote account id from the username
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'SELECT userid,password FROM ' . $db_prefix . 'user
				WHERE username = '.$dbm->qstr($username);
		$result = $dbm->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false;
		}
		
		$vb_user_id = $result->fields['userid'];
		$password   = $result->fields['password'];
		 
		if (!empty($vb_user_id))
        { 
			# GENERATE COOKIES FOR LOGIN  
			setcookie(VB3_COOKIE_PREFIX.'userid',   $vb_user_id, 0, '/');
			setcookie(VB3_COOKIE_PREFIX.'password', md5($password . trim($cookie)), 0, '/');
			setcookie(VB3_COOKIE_PREFIX.'lastactivity', time(), 0, '/');
		}  
	}
	 
	
	### LOGOUT 
	function MAP_account_logout ($account_id, $cookie, $MAP_this)
	{ 
		setcookie(VB3_COOKIE_PREFIX.'userid' );
		setcookie(VB3_COOKIE_PREFIX.'password' );  
		setcookie(VB3_COOKIE_PREFIX.'lastactivity' );
		setcookie(VB3_COOKIE_PREFIX.'lastvisit' );
		setcookie(VB3_COOKIE_PREFIX.'sessionhash' );
	}
}
	
?>