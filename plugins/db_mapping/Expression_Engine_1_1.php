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
### Database Map for: pMachine Pro 2.1
### Last Update: 12-17-2003
################################################################################
class map_EXPRESSION_ENGINE_1_1
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_Expression_Engine_1_1 ()
    {
        $this->map =
            Array (
                'map'           => 'Expression_Engine_1_1',
                'db_type'       => 'mysql',
                'notes'         => 'Expression Engine v1.1',
                'group_type'    => 'db-status',    // db, db-status, status, none

                ### Define the group fields in the target db
                'group_map'     =>
                    Array
                    (
                        'table'     => 'member_groups',
                        'id'        => 'group_id',
                        'name'      => 'group_title'
                    ),


                ### Define the account mapping properties
                'account_map_field'         => 'members',
                'account_status_field'      => 'group_id',
                'account_default_status'    => '5',

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
                                'map_field'      => 'member_id'
                            ),
                        'date_orig'     =>
                            Array
                            (
                                'map_field'      => 'join_date'
                            ),

                        'date_last'     =>
                            Array
                            (
                                'map_field'      => 'last_visit'
                            ),

                        'date_expire'   =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'language_id'   =>
                            Array
                            (
                                'map_field'      => 'language'
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
                                'map_field'      => 'screen_name',
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
                        Array
                            (
                                Array
                                (
                                    'name'  => 'timezone',
                                    'value' => 'UTC',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'daylight_savings',
                                    'value' => 'n',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'localization_is_site_default',
                                    'value' => 'n',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'time_format',
                                    'value' => 'us',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'template_size',
                                    'value' => '28',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'notepad_size',
                                    'value' => '18',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'unique_id',
                                    'value' => 'random|32',
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
?>