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
### Database Map for: SendStudio 2004 
################################################################################

class map_SendStudio_2004
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################
    
    function map_SendStudio_2004 ()
    {
        $this->map =
            Array (
                'map'           => 'SendStudio_2004',
                'db_type'       => 'mysql',
                'notes'         => 'This is for SendStudio 2004',
                'group_type'    => 'status',    // db, status, none


                ### Define the account mapping properties
                'account_map_field' => 'admins',
                'account_status_field' => 'Status',
                'account_default_status' => '0',

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
                                'map_field'      => 'AdminID'
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
                                'map_field'      => 'UserName'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'Password'
                            ),

                        'misc'          =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'first_name'    =>
                            Array
                            (
                                'map_field'      => 'AdminName'
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
                                'map_field'      => 'Email'
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
                                    'name'  => 'Manager',
                                    'value' => '0',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'Root',
                                    'value' => '0',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'LoginString',
                                    'value' => '0',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),

                                Array
                                (
                                    'name'  => 'MaxLists',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'PerHour',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0,
                                ),
                                Array
                                (
                                    'name'  => 'PerMonth',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0,
                                ),
                                Array
                                (
                                    'name'  => 'DisplaySummaries',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0,
                                ),
                                Array
                                (
                                    'name'  => 'Attachments',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0,
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
			
			### Do the custom stuff:
			$custom = new dbmapping_sendstudio_2004;
			$custom->MAP_account_add($account_id, $this);
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
            return;
        }


        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################

        function logout($account_id)
        {
            return;
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

class dbmapping_sendstudio_2004 
{   
	function MAP_account_add ($account_id, $MAP_this)
	{
		### Array of SectionIDS for the 'allow_functions' table:
		$SectionID = Array(10, 14, 12, 18, 19, 1, 2, 4, 7, 5, 8, 11, 6, 13, 17, 16);
		
		### Get the username from the account_id
		$db = &DB();
		$sql= 'SELECT username,password FROM '.AGILE_DB_PREFIX.'account WHERE
    		   id  			= '.$db->qstr($account_id).' AND
    		   site_id 		= '.$db->qstr(DEFAULT_SITE);
		$result = $db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('SendStudio_2004.php','MAP_account_add', $db->ErrorMsg());
		}
		unset($db); 
		$username = $result->fields['username'];

		### Get the remote account id from the username
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = 'SELECT AdminID FROM ' . $db_prefix . 'admins
				WHERE Username = '.$dbm->qstr($username);
		$result = $dbm->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('db_mapping.inc.php','Map_account_login_get_remote_account_id', $dbm->ErrorMsg());
			return false; 
		}
		
		$remote_account_id = $result->fields['AdminID'];

		if (!empty($remote_account_id))
        {
		
			for($i=0; $i<count($SectionID); $i++)
			{
				### Insert each SectionID
				$sql = 'INSERT INTO ' . $db_prefix . 'allow_functions 
						SET
						AdminID = '.$dbm->qstr($remote_account_id).',
						SectionID ='.$dbm->qstr($SectionID[$i]); 
				$dbm->Execute($sql);
			} 
		} 
	} 
}
?>