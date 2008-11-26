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
### Database Map for: SMF 1.0.2
### Last Update: 2-21-2004
################################################################################
class map_SMF_102
{
    ############################################################################
    ### Define the settings for this database map
    ############################################################################ 

    function map_SMF_102 ()

    {

        $this->map =

            Array (

                'map'           => 'SMF_102',

                'db_type'       => 'mysql',

                'notes'         => 'This is for SMF 1.0.2',

                'group_type'    => 'db-status',    // db, status, none







                ### Define the account mapping properties

                'account_map_field' 		=> 'members',

                'account_status_field' 		=> 'ID_GROUP',

                'account_default_status' 	=> '4',

                



                ### Define the group fields in the target db

                'group_map'     => 
                    Array 
                    ( 
                        'table'     => 'membergroups', 
                        'id'        => 'ID_GROUP', 
                        'name'      => 'groupName' 
                    ),

                    

                    

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

                                'map_field'      => 'ID_MEMBER'

                            ),

                        'date_orig'     =>

                            Array

                            (

                                'map_field'      => 'dateRegistered'

                            ),



                        'date_last'     =>

                            Array

                            (

                                'map_field'      => 'lastLogin'

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

                                'map_field'      => 'memberName'

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

                                'map_field'      => 'realName',

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

                                'map_field'      => 'emailAddress'

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

                                    'name'  => 'lngfile',

                                    'value' => 'english',

                                    'add'   => 1,

                                    'edit'  => 0

                                ),

                                

                                Array

                                (

                                    'name'  => 'hideEmail',

                                    'value' => '1',

                                    'add'   => 1,

                                    'edit'  => 0

                                ),
								
								
								Array

                                (

                                    'name'  => 'im_email_notify',

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
        function sync($id, $file) {
            $db_map = new db_mapping;
            $db_map->MAP_sync ($id, $file, $this);
        }

        ########################################################################
        ### Create a new account in the target DB
        ######################################################################## 
        function account_add($account_id) {  
			$db_map_misc = new dbmapping_smf;
			$db_map_misc->MAP_account_add ($account_id, $this);

            ### Sync the groups for this account: 
            $remote_account_id = $this->account_group_sync( $account_id ); 
        } 
        
        ########################################################################
        ### Edit an existing account in the target DB
        ########################################################################

        function account_edit($account_id, $old_username) {
            $db_map_misc = new dbmapping_smf;
			$db_map_misc->MAP_account_edit ($account_id, $old_username, $this);

            ### Update the groups in the remote db 
            $this->account_group_sync($account_id);
        }

        ########################################################################
        ### Delete an existing account from the target DB
        ########################################################################
        function account_delete($account_id, $username) {
            $db_map = new db_mapping;
            $db_map->MAP_account_delete ($account_id, $username, $this);
        }

        ########################################################################
        ### Export / Update all accounts / groups to the target DB
        ########################################################################
        function account_import($remote_account_id)  {
            $db_map = new db_mapping;
            $db_map->MAP_account_import ($remote_account_id, $this);
        }

        ########################################################################
        ### Create the cookie/session for login sync
        ########################################################################
        function login($account_id) {
            return;
        }

        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################
        function logout($account_id) {
            return;
        }

        ########################################################################
        ### Syncronize the groups for a specific account in the remote DB
        ######################################################################## 
        function account_group_sync($account_id) {
	        $db_map = new db_mapping;
            return  $db_map->MAP_account_group_sync_db_status ($account_id, $this);
        }

}

 
class dbmapping_smf extends db_mapping
{  
	### ADD ACCOUNT
	function MAP_account_add ($account_id, $MAP_this)
	{
		### Get the account details from AB
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'account WHERE id = '.$db->qstr($account_id).' AND site_id = '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql); 
		unset($db);  
		  
		### Insert the new account into SMF
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = "INSERT INTO {$db_prefix}members SET  
				dateRegistered 	= {$account->fields['date_orig']},
				lastLogin		= {$account->fields['date_last']},
				passwd			= '{$account->fields['password']}', 
				memberName 		= ".$dbm->qstr($account->fields['username']).",				
				realName		= ".$dbm->qstr($account->fields['username']).",	
				emailAddress	= ".$dbm->qstr($account->fields['email']).",
				lngfile			= 'english',
				ID_GROUP		= '4',
				hideEmail		= '1',
				im_email_notify = '1'";                                     				
		$result = $dbm->Execute($sql);  		
		if ($result === false) {
			global $C_debug;
			$C_debug->error('SMF_102.php','MAP_account_add', $dbm->ErrorMsg());
			return false;
		}	 	
	}
	 
	
	### EDIT ACCOUNT
	function MAP_account_edit($account_id, $old_username, $MAP_this)
	{ 
		### Get the account details from AB
		$db = &DB();
		$sql= 'SELECT * FROM '.AGILE_DB_PREFIX.'account WHERE id = '.$db->qstr($account_id).' AND site_id = '.$db->qstr(DEFAULT_SITE);
		$account = $db->Execute($sql); 
		unset($db);  
		  
		### UPDATE the account into SMF
		$dbm    = new db_mapping;
		$dbm    = $dbm->DB_connect(false, $MAP_this->map['map']);
		eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($MAP_this->map['map']) .';' );
		$sql = "UPDATE {$db_prefix}members SET   
				passwd			= ".$dbm->qstr($account->fields['password']).",
				memberName 		= ".$dbm->qstr($account->fields['username']).",				
				realName		= ".$dbm->qstr($account->fields['username']).",	
				emailAddress	= ".$dbm->qstr($account->fields['email'])."
				WHERE 
				memberName 		= ".$dbm->qstr( $old_username );                                     				
		$result = $dbm->Execute($sql);  
		if ($result === false) {
			global $C_debug;
			$C_debug->error('SMF_102.php','MAP_account_edit', $dbm->ErrorMsg());
			return false;
		} 		
	}  
}
	
?>