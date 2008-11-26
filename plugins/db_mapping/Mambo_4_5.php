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
### Database Map for: Mambo 4.5.x
### Last Update: 4-20-2005
################################################################################

class map_MAMBO_4_5
{

    ############################################################################
    ### Define the settings for this database map
    ############################################################################

    function map_MAMBO_4_5 ()
    {
        $this->map =
            Array (
                'map'           => 'Mambo_4_5',
                'db_type'       => 'mysql',
                'notes'         => 'This is for Mambo 4.5.x',
                'group_type'    => 'db-status',    // db, db-status, status, none


                ### Define the group fields in the target db
                'group_map'     =>
                    Array
                    (
                        'table'     => 'core_acl_aro_groups',
                        'id'        => 'group_id',
                        'name'      => 'name'
                    ),
 

                ## Should records be deleted?
                'account_sync_field'=>
                    Array
                    (
                        'delete'    => '1'
                    ),
                    

                # Set the user table & fields
                'account_map_field' => 'users',
                'account_status_field' => 'gid',
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
                                'map_field'      => 'name',
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
                                    'name'  => 'gid',
                                    'value' => '29',
                                    'add'   => 1,
                                    'edit'  => 0
                                ),
                                Array
                                (
                                    'name'  => 'registerDate',
                                    'value' => '2005-04-20 18:54:29',
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
            $db_map->MAP_sync ($id, $file);
        }

        ########################################################################
        ### Create a new account in the target DB
        ########################################################################
        
        function account_add($account_id)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_add ($account_id);
            
            ### Sync the groups for this account: 
            $this->account_group_sync( $account_id );
        }

        ########################################################################
        ### Edit an existing account in the target DB
        ########################################################################
        
        function account_edit($account_id, $old_username)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_edit ($account_id, $old_username);

            ### Update the groups in the remote db 
            $this->account_group_sync($account_id);
        }

        ########################################################################
        ### Delete an existing account from the target DB
        ########################################################################
        
        function account_delete($account_id, $username)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_delete ($account_id, $username);               	                   
        }

        ########################################################################
        ### Export / Update all accounts / groups to the target DB
        ########################################################################

        function account_import($remote_account_id)
        {
            $db_map = new db_mapping;
            $db_map->MAP_account_import ($remote_account_id);
        }

        ########################################################################
        ### Create the cookie/session for login sync
        ########################################################################

        function login($account_id)
        {
        	global $_COOKIE;
             
            ### Get the local account details 
            $db = &DB();
            $sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
                    site_id = '.$db->qstr(DEFAULT_SITE).' AND
                    id      = '.$db->qstr($account_id);
            $result = $db->Execute($sql);  
            $user   = $result->fields['username']; 
                    	
            $dbm    = new db_mapping;    		
        	$db2    = $dbm->DB_connect(false, $this->map['map']);
        	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' );

        	### Get the remote account_id
            $sql = "SELECT * FROM " . $db_prefix . 'users WHERE username  = ' . $db2->qstr($user);
            $acct = $db2->Execute($sql);        	
        	  
            ### Determine if remote session exists:    		
            @$session_id = $_COOKIE['sessioncookie'];            
            $sql    = "SELECT * FROM " . $db_prefix . 'session WHERE session_id = ' . $db2->qstr( md5 ($session_id) );
            $result = $db2->Execute($sql);
   		    if ($result === false)
		    {
		        global $C_debug;
		        $C_debug->error('Mambo_4_5.php','account_group_sync:2', $db2->ErrorMsg());
		        return;
		    }
		     
		    if($result->RecordCount() == 0) 
		    {
		    	### New session
		    	$session_id = SESS; 
	            $sql    = "INSERT INTO " . $db_prefix . "session SET 
	            			time 		= ". time().",
	            			session_id 	= ". $db2->qstr( md5 ( $session_id ) ). ",
	            			guest 		= 0,
	            			userid    	= " . $acct->fields['id'].",
	            			gid 		= " . $acct->fields['gid'].",
	            			usertype 	= " . $db2->qstr($acct->fields['usertype']).",
	            			username  	= " . $db2->qstr($user);
	            $db2->Execute($sql);		    	
		    } else {
		    	### Update session 
	            $sql    = "UPDATE " . $db_prefix . "session SET 
	            			time 		= ". time().",
	            			session_id 	= ". $db2->qstr( md5 ( $session_id ) ). ",
	            			guest 		= 0,
	            			userid    	= " . $acct->fields['id'].",
	            			gid 		= " . $acct->fields['gid'].",
	            			usertype 	= " . $db2->qstr($acct->fields['usertype']).",
	            			username  	= " . $db2->qstr($user) . " 
	            			WHERE 
	            			session_id  = " . $db2->qstr( md5( $session_id) );
	            $db2->Execute($sql);				    	
		    } 
		    
		    // set mambo session cookie
		    if(COOKIE_EXPIRE == 0 )
            $cookie_expire = (time() + 86400*365);
	        else
	        $cookie_expire = (time() + (COOKIE_EXPIRE*60));	        
		    setcookie( "sessioncookie", $session_id, $cookie_expire, "/" ); 
            return true;
        }

        
        ########################################################################
        ### Delete the cookie/session on account logout
        ########################################################################

        function logout($account_id)
        {
            ### Get the local account details 
            $db = &DB();
            $sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
                    site_id = '.$db->qstr(DEFAULT_SITE).' AND
                    id      = '.$db->qstr($account_id);
            $result = $db->Execute($sql);  
            $user   = $result->fields['username']; 
                    	
        	@$session_id = $_COOKIE['sessioncookie']; 
        	if(!empty($session_id)) {
	            $dbm    = new db_mapping;    		
	        	$db2    = $dbm->DB_connect(false, $this->map['map']);
	        	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' ); 
	            $sql    = "UPDATE " . $db_prefix . "session SET  
	            				guest 		= 1,
	            				userid    	= '',
	            				gid 		= '',
	            				usertype 	= '',
	            				username  	= '' 	            		
	            			WHERE 
	            				session_id  = " . $db2->qstr( md5($session_id) ) . "
	            			OR
	            				username    = " . $db2->qstr($user) ;
	            $db2->Execute($sql);	
	            $acct = $db2->Execute($sql);          		
        	}
        	setcookie( "usercookie", "", time() - 1800, "/" );
            return true;
        }
        

        ########################################################################
        ### Syncronize the groups for a specific account in the remote DB
        ########################################################################

        function account_group_sync($account_id)
        { 
         	$db_map = new db_mapping;
            $db_map->MAP_account_group_sync_db_status($account_id); 
            
            
            ### Get the local account details 
            $db = &DB();
            $sql= 'SELECT username,email FROM '.AGILE_DB_PREFIX.'account WHERE
                    site_id = '.$db->qstr(DEFAULT_SITE).' AND
                    id      = '.$db->qstr($account_id);
            $result = $db->Execute($sql);
    		if ($result === false)
            {
                global $C_debug;
    		    $C_debug->error('Mambo_4_5.php','account_group_sync:1', $db->ErrorMsg());
                return;
    		}
    		
            $user   = $result->fields['username'];
            $email  = $result->fields['email'];

 
            ### Get the remote account id, username, and group ID: 
            $dbm    = new db_mapping;    		
        	$db2    = $dbm->DB_connect(false, $this->map['map']);
        	eval ( '@$db_prefix = DB2_PREFIX'. strtoupper($this->map['map']) .';' );        		        		
            $sql    = "SELECT id,gid,username FROM " .
                        $db_prefix . "" . $this->map['account_map_field'] . ' WHERE ' .
                        $this->map['account_fields']['username']['map_field'] . " = " .
                        $db2->qstr($user);
            $result = $db2->Execute($sql);
   		    if ($result === false)
		    {
		        global $C_debug;
		        $C_debug->error('Mambo_4_5.php','account_group_sync:2', $db2->ErrorMsg());
		        return;
		    }
		 
            $id = $result->fields['id']; 
            $user = $result->fields['username']; 
            $gid = $result->fields['gid'];  
            
            # Clear old values:
            $sql = "DELETE FROM  " .  $db_prefix . "core_acl_aro WHERE value = $id";
            $result = $db2->Execute($sql);              
 
            # add the core_acl_aro record
            $sql    = "INSERT INTO " .  $db_prefix . "core_acl_aro SET 
                        section_value 	= 'users',
                        value			= $id,
                        name			= ".$db2->qstr($user); 
            $result = $db2->Execute($sql);
            
                      	  
           	# Get the ID just inserted:
            $sql    = "SELECT aro_id FROM " .  $db_prefix . "core_acl_aro WHERE value = $id";
            $result = $db2->Execute($sql);
            $aro_id = $result->fields['aro_id'];
               
	        $sql = "DELETE FROM  " .  $db_prefix . "core_acl_groups_aro_map WHERE aro_id = $aro_id";
	        $result = $db2->Execute($sql);    
	                        
            if($gid > 0 && $aro_id > 0)
            {   
	            # add the core_acl_groups_aro_map record
	            $sql    = "INSERT INTO " .  $db_prefix . "core_acl_groups_aro_map SET 
	            			group_id		= $gid,
	            			aro_id			= $aro_id";          
	            $result = $db2->Execute($sql); 	 

	            # unblock
	            $sql    = "UPDATE  " . $db_prefix . $this->map['account_map_field'] . "
	                        SET block = 0
	                        WHERE id = $id";
	            $result = $db2->Execute($sql); 
            } 
            else
            {
            	/*
	            	This member gets access to nothing.
	            	
	            	Mambo doesn't have a group we can grant the users
	            	that allows them only public access articles. Lame. 
	            	
	            	After studying mambo's group system in depth, 
	            	it makes no sense how something so complicated (6 tables?) can not
	            	be used to control access to the articles?! Wit a CMS system, it is 
	            	all about the content and if you have groups, you should be able to
	            	display/hide content based on the user's group membership.
	            	
	            	However, with mambo, apparently you can set the articles so they can 
	            	be viewed by a) all users, (b) registered users, (c) Special.
	            	
	            	Since I can find no way to map the users to option (c), and obviously 
	            	non-paying members will still be registered after their subscription
	            	expires, our options are now to delete the user entirely (NO!)
	            	or set the user to blocked (lesser of two evils but will cause confusion
	            	since mambo will tell the user the login info they submitted is invalid)
	            	
            		Lets block the user and be done with it... Sigh...
            	*/
            	
	            $sql    = "UPDATE  " . $db_prefix . $this->map['account_map_field'] . "
	                        SET block = 1
	                        WHERE id = $id";
	            $result = $db2->Execute($sql);
            }  	            	            	                      
        }

}
?>        