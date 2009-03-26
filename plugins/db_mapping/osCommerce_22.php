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
### Database Map for: osCommerce 2.2ms2
### Last Update: 12-16-2003
################################################################################


class map_OSCOMMERCE_22
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_OSCOMMERCE_22 ()
    {
        $this->map =
            Array (
                'map'           => 'osCommerce_22',
                'db_type'       => 'mysql',
                'notes'         => 'This is for osCommerce 2.2ms2',
                'group_type'    => 'none',
                'install'       => true,


                ### Define the account mapping properties
                'account_map_field' => 'customers',


                'account_fields'    =>
                    Array
                    (
                        'id'        =>
                            Array
                            (
                                'map_field'      => 'customers_id',
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
                                'map_field'      => 'customers_username'
                            ),

                        'password'      =>
                            Array
                            (
                                'map_field'      => 'customers_password'
                            ),

                        'misc'          =>
                            Array
                            (
                                'map_field'      => false
                            ),

                        'first_name'    =>
                            Array
                            (
                                'map_field'      => 'customers_firstname',
                            ),

                        'last_name'     =>
                            Array
                            (
                                'map_field'      => 'customers_lastname',
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
                                'map_field'      => 'customers_email_address'
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
        ### Install syncronization
        ########################################################################

        function install()
        {

            ### Add the 'customers_username' field:
            $dbm    = new db_mapping;
        	$db     = $dbm->DB_connect(false, $this->map['map']);
        	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' );
            $sql    = "ALTER TABLE customers ADD customers_username VARCHAR( 128 ) NOT NULL";
            $result = $db->Execute($sql);
            
            ### move the customer's email to the username field...
            $sql    = "SELECT customers_id, customers_email_address FROM customers";
            $result = $db->Execute($sql);
            while ( !$result->EOF )
            {
                $sql = 'UPDATE customers SET
                        customers_username = ' . $db->qstr($result->fields['customers_email_address']) . '
                        WHERE customers_id = ' . $db->qstr($result->fields['customers_id']);
                $update = $db->Execute($sql);
                $result->MoveNext();
            }
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
            $db_map = new db_mapping;
            $db_map->MAP_account_add ($account_id, $this);

            ### todo: add the customers_info record
            ### todo: add the address_book_id record
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




?>
