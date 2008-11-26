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
	
 
################################################################################
### Database Map for: Invision Power Board 2
### Last Update: 10-28-2004
################################################################################

class map_INVISION_POWER_BOARD_2
{
	
	############################################################################
	### Define the settings for this database map
	############################################################################
	
	function map_INVISION_POWER_BOARD_2 ()
	{

		$this->map =
		Array (
			'map'           => 'Invision_Power_Board_2',
			'db_type'       => 'mysql',
			'notes'         => 'This is for Invision Power Board 2.x',
			'group_type'    => 'db-status',    // db, status, none

                ### Define the group fields in the target db
                'group_map'     =>
                    Array
                    (
                        'table'     => 'groups',
                        'id'        => 'g_id',
                        'name'      => 'g_title'
                    ),

                                                          
                ### Define the account mapping properties
                'account_map_field' 		=> 'members',
                'account_status_field' 		=> 'mgroup',
                'account_default_status' 	=> '1',
                
                'account_sync_field'=>
                    Array
                    (
                        'delete'    => '1'
                    ),
                    
                'account_fields'    =>
                    Array
                    (
                        'id'        =>
                            Array
                            (
                                'map_field'      => 'id',
                                'unique'         => true
                            ),
                        'date_orig'     =>
                            Array
                            (
                                'map_field'      => 'joined'
                            ),

                        'date_last'     =>
                            Array
                            (
                                'map_field'      => false
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
                                'map_field'      => 'name'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'legacy_password'
                            ),

                        'misc'          =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'first_name'    =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'last_name'     =>
                            Array
                            (
                                'map_field'      => false
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
		Array (
			
		Array
                                (
                                    'name'  => 'ip_address',
                                    'value' => USER_IP,
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
		
		Array
                                (
                                    'name'  => 'view_sigs',
                                    'value' => 1,
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
		Array
                                (
                                    'name'  => 'view_avs',
                                    'value' => 1,
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
		Array
                                (
                                    'name'  => 'view_pop',
                                    'value' => 1,
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
		Array
                                (
                                    'name'  => 'view_img',
                                    'value' => 1,
                                    'add'   => 1,
                                    'edit'  => 0
                                ), 
		Array
                                (
                                    'name'  => 'last_visit',
                                    'value' => mktime(),
                                    'add'   => 1,
                                    'edit'  => 0
                                )
						)

		);
	}        


        

        ########################################################################
        ### Syncronize all accounts & groups
        ########################################################################

        function sync($id, $file)
        {
            $db_map = new db_mapping;
            $this   = $db_map->MAP_sync ($id, $file, $this);
        }



        ########################################################################
        ### Create a new account in the target DB
        ########################################################################

        function account_add($account_id)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_add ($account_id, $this);

            ### Sync the groups for this account:
            if( $this->map['group_type'] != 'none' &&
                $this->map['group_type'] != 'add_remove' )
            $this->account_group_sync( $account_id );
        }



        ########################################################################
        ### Edit an existing account in the target DB
        ########################################################################

        function account_edit($account_id, $old_username)
        { 
            $db_map = new db_mapping;
            $db_map->MAP_account_edit ($account_id, $old_username, $this);
			
            ### Update the groups in the remote db
            if( $this->map['group_type'] != 'none' &&
                $this->map['group_type'] != 'add_remove' )
            $this->account_group_sync($account_id);
			return true;
        }



        ########################################################################
        ### Delete an existing account from the target DB
        ########################################################################

        function account_delete($account_id, $username)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_delete ($account_id, $username, $this);
        }



        ########################################################################
        ### Export / Update all accounts / groups to the target DB
        ########################################################################

        function account_import($remote_account_id)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_import ($remote_account_id, $this);
        }




		########################################################################
		### Create the cookie/session for login sync
		########################################################################
		
		function login($account_id)
		{
			$db_map_misc = new dbmapping_misc;
			$db_map_misc->MAP_account_login ($account_id, $this);
		}
	
	
		########################################################################
		### Delete the cookie/session on account logout
		########################################################################
		
		function logout($account_id)
		{
			$db_map_misc = new dbmapping_misc;
			$db_map_misc->MAP_account_logout ($account_id, $this);
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

 


###########################################################################################################
### Misc IBF class extends db_mapping################################################################
###########################################################################################################

class dbmapping_misc extends db_mapping
{ 
	########################################################################
	#>>>>> IBF login
	########################################################################
	
	function MAP_account_login ($account_id, $MAP_this)
	{
		
		$user_agent = addslashes($_SERVER['HTTP_USER_AGENT']);
		$today = time();
		
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

		### Get the remote account id from the username
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'SELECT id FROM ' . $db_prefix . 'members
				WHERE name = '.$dbm->qstr($username);
		$result = $dbm->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false;
		}
		
		$remote_account_id = $result->fields['id'];

		if (!empty($remote_account_id))
        {
			### Select required info for the session table  
			$sql = 'SELECT name,mgroup,legacy_password FROM ' . $db_prefix . 'members
	                    WHERE id='.$dbm->qstr($remote_account_id);
			$result = $dbm->Execute($sql);
			if ($result === false)
			{
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
				return false;
			} 
			
			$account_member_name = $result->fields['name'];
			$account_mgroup      = $result->fields['mgroup'];
			$account_password    = $result->fields['legacy_password'];

            ### cookies
            setcookie("session_id",SESS,0,'/');
            setcookie("member_id",$remote_account_id,0,'/');
            setcookie("pass_hash",$account_password,0,'/');
 
			### Delete any old sessions 
			$sql = 'DELETE FROM ' . $db_prefix . 'sessions WHERE member_id='.$dbm->qstr($remote_account_id).' OR id='.$dbm->qstr(SESS);
			$result = $dbm->Execute($sql);
			if ($result === false)  {
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','Map_account_login_delete_sessions', $dbm->ErrorMsg()); 
			}
			
			### Insert new session into remote db 
			$sql = 'INSERT INTO ' . $db_prefix . 'sessions SET
            		id 				= '.$dbm->qstr(SESS).',
            		member_name		= '.$dbm->qstr($account_member_name).',
            		member_id 		= '.$dbm->qstr($remote_account_id).',
            		ip_address 		= '.$dbm->qstr(USER_IP).',
            		browser			= '.$dbm->qstr($user_agent).',
            		running_time	= '.$dbm->qstr($today).',
            		login_type		= '.$dbm->qstr('-1').',
            		location		= '.$dbm->qstr('idx,,').',
            		member_group	= '.$dbm->qstr($account_mgroup).',
            		in_forum		= '.$dbm->qstr(0).',
            		in_topic		= '.$dbm->qstr(0);
			$result = $dbm->Execute($sql);
			if ($result === false)   {
				global $C_debug;
				$C_debug->error('db_mapping.inc.php','Map_account_login_insert_session', $dbm->ErrorMsg()); 
			}
		}
	}
	
	########################################################################
	#>>>>> IBF logout
	########################################################################
	
	function MAP_account_logout ($account_id, $MAP_this)
	{
		
		### Clear the session info in IBF
		$dbm    = new db_mapping;
		$db    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'UPDATE ' . $db_prefix . 'sessions SET
        			member_name		='.$db->qstr('NULL').',
        			member_id 		='.$db->qstr(0).',
        			login_type 		='.$db->qstr(0).',
        			member_group	='.$db->qstr(2).' 
        			WHERE id 		='.$db->qstr(SESS);
		$result = $db->Execute($sql);

		### error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_logout_delete_account_session', $db->ErrorMsg());
			$smarty->assign('db_mapping_result', $db->ErrorMsg());
			return;
		}
		 
		### Clear the IBF cookies
		setcookie("session_id",0,0,'/');
		setcookie("member_id",0,0,'/');
		setcookie("pass_hash",0,0,'/');
		return; 
	} 
}
	
?>