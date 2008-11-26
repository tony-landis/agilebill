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
### Database Map for: Flyspray 0.9.5
### Last Update: 9-20-04
### 
### You must modify the structure of the flyspray_users.user_pass field to
### varchar(32) prior to installing this plugin. 
### Also, you must force all registrations, logins, and account modifications 
### to occur in AgileBill.
################################################################################
class map_FLYSPRAY_95
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_FlySpray_95 ()
    {
        $this->map =
            Array (
                'map'           => 'FlySpray_95',
                'db_type'       => 'mysql',
                'notes'         => 'FlySpray_95',
                'group_type'    => 'db-status',    // db, db-status, status, none

                ### Define the group fields in the target db
                'group_map'     =>
                    Array
                    (
                        'table'     => 'flyspray_groups',
                        'id'        => 'group_id',
                        'name'      => 'group_name'
                    ),


                ### Define the account mapping properties
                'account_map_field'         => 'flyspray_users',
                'account_status_field'      => 'group_in',
                'account_default_status'    => '0',

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
                                'map_field'      => 'user_id'
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
                                'map_field'      => 'user_name'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'user_pass'
                            ),

                        'misc'          =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'first_name'    =>
                            Array
                            (
                                'map_field'      => 'real_name',
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
                                'map_field'      => 'email_address'
                            ),

                        'email_type'    =>
                            Array
                            (
                                'map_field'      => false
                            )
                        ),

                    ### Define any extra fields for account table in the target db:
                    'extra_field' =>
                        Array
                            (
                                Array
                                (
                                    'name'  => 'account_enabled',
                                    'value' => '1',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'notify_type',
                                    'value' => '1',
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

            return true;

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
            # Get the remote account details:
            $db = &DB();
            $sql= 'SELECT username FROM '.AGILE_DB_PREFIX.'account WHERE
                    id      = '.$db->qstr($account_id).' AND
                    site_id = '.$db->qstr(DEFAULT_SITE);
            $result = $db->Execute($sql);
            if ($result === false)
            {
                global $C_debug;
                $C_debug->error('db_mapping.inc.php','login', $db->ErrorMsg());
                return;
            }

            $username = $result->fields['username'];

            ### Get the remote account id from the username
            $dbm    = new db_mapping;
            $db2    = $dbm->DB_connect(false, $this->map['map']);
            eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' );
            $sql = 'SELECT user_id,user_pass FROM flyspray_users
                    WHERE user_name = '.$db2->qstr($username);
            $result = $db2->Execute($sql);
            if ($result === false)
            {
                global $C_debug;
                echo $db2->ErrorMsg();
                $C_debug->error('db_mapping.inc.php','login', $db2->ErrorMsg());
                return;
            }

            $remote_account_id = $result->fields['user_id'];
            $remote_user_pass  = $result->fields['user_pass'];

            #session_start();
            setcookie('flyspray_userid', $remote_account_id, time()+60*60*24*30, "/");
            setcookie('flyspray_passhash', crypt("{$remote_user_pass}", "4t6dcHiefIkeYcn48B"), time()+60*60*24*30, "/");
            return true;
        }


        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################

        function logout($account_id)
        {
            setcookie('flyspray_userid', '', time()-60, '/');
            setcookie('flyspray_passhash', '', time()-60, '/');
            return true;
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
?>