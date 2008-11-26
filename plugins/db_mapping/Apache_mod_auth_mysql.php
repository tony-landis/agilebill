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
### Database Map for: Apache mod_auth_mysql
################################################################################

// Define the way to store passwords in the database
// If you choose plaintext, the "Sync" feature will not work
define('MOD_AUTH_MYSQL_PW', 'md5'); // md5 or plaintext

class map_APACHE_MOD_AUTH_MYSQL
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_APACHE_MOD_AUTH_MYSQL ()
    {
        $this->map =
            Array (
                'map'           => 'Apache_mod_auth_mysql',
                'db_type'       => 'mysql',
                'notes'         => 'This is for the Apache mod_auth_mysql',
                'group_type'    => 'status',    // db, db-status, status, none

                ### Define the account mapping properties
                'account_map_field' => 'mysql_auth',
                'account_status_field' => 'groups',
                'account_default_status' => '',

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
                                'map_field'      => 'id'
                            ),
                        'date_orig'     =>
                            Array
                            (
                                'map_field'      => false
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
                                'map_field'      => 'username'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'passwd'
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
                                'map_field'      => false
                            ),

                        'email_type'    =>
                            Array
                            (
                                'map_field'      => false
                            )
                        ),

                    ### Define any extra fields for account table in the target db:
                    'extra_field' => false
                    
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
        	if(MOD_AUTH_MYSQL_PW == 'md5')
        	{
	            $db_map = new db_mapping;
	            $db_map->MAP_account_add ($account_id, $this);
        	} 
        	elseif(MOD_AUTH_MYSQL_PW == 'plaintext')
        	{
				$db_map_misc = new dbmapping_mod_auth_mysql;
				$db_map_misc->MAP_account_add ($account_id, $this);
	
	            ### Sync the groups for this account: 
	            $remote_account_id = $this->account_group_sync( $account_id );         		
        	}

            ### Sync the groups for this account: 
            $this->account_group_sync( $account_id );
        }
 

        ########################################################################
        ### Edit an existing account in the target DB
        ########################################################################

        function account_edit($account_id, $old_username)
        {
        	
        	if(MOD_AUTH_MYSQL_PW == 'md5')
        	{
	            $db_map = new db_mapping;
	            $db_map->MAP_account_edit ($account_id, $old_username, $this);
        	} 
        	elseif(MOD_AUTH_MYSQL_PW == 'plaintext')
        	{
	            $db_map_misc = new dbmapping_mod_auth_mysql;
				$db_map_misc->MAP_account_edit ($account_id, $old_username, $this);
	
	            ### Update the groups in the remote db 
	            $this->account_group_sync($account_id);       		
        	} 
        	
            ### Update the groups in the remote db 
            $this->account_group_sync($account_id);
        }

        
         
        ########################################################################
        ### Delete an existing account from the target DB
        ########################################################################

        function account_delete($account_id, $username)
        {
            $db_map = new db_mapping;
            $remote_account_id = $db_map->MAP_account_delete ($account_id, $username, $this);      
        }



        ########################################################################
        ### Export / Update all accounts / groups to the target DB
        ########################################################################

        function account_import($remote_account_id)
        {
            $msg = 'Import from not supported with this plugin...';            
            global $C_debug;
            $C_debug->alert($msg);
        }




        ########################################################################
        ### Create the cookie/session for login sync
        ########################################################################

        function login($account_id, $cookie)
        {
			return false;
        }


        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################

        function logout($account_id, $cookie)
        {
        	return false;
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


         
     
class dbmapping_mod_auth_mysql extends db_mapping
{ 
	
	### ADD ACCOUNT
	function MAP_account_add ($account_id, $MAP_this)
	{
		### Get the account details from AB
		$db = &DB();
		$sql= 'SELECT username FROM '.AGILE_DB_PREFIX.'account WHERE
    		       id  	= '.$db->qstr($account_id).' AND
    		       site_id 		= '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql);
		if ($account === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_account_username', $db->ErrorMsg());
		}
		unset($db); 
		
		$username = $account->fields['username'];  
		@$password = $MAP_this->plaintext_password;
		
		### Insert the new account into VB3 
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']); 
		$sql = 'INSERT INTO mysql_auth SET   
				username 	= '.$dbm->qstr($username) . ',
				passwd		= '.$dbm->qstr($password) ;
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
		$sql= 'SELECT username FROM '.AGILE_DB_PREFIX.'account WHERE
    		       id  	= '.$db->qstr($account_id).' AND
    		       site_id 		= '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql);
		if ($account === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_account_username', $db->ErrorMsg());
		}
		unset($db); 
		$username = $account->fields['username'];
		@$password = $MAP_this->plaintext_password;
		
		### Update the account in VB3 
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']); 
		$sql = 'UPDATE mysql_auth SET  ';  
		
		if(@$MAP_this->plaintext_password != false)
		$sql .= ' password		= '.$dbm->qstr($password) . ', ';
		
		$sql .= ' username 		= '.$dbm->qstr($username) . ' 
				WHERE
				username 		= '.$dbm->qstr($old_username) ;
		
		$result = $dbm->Execute($sql);  
		if ($result === false) {
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false;
		}			
	} 
} 
?>