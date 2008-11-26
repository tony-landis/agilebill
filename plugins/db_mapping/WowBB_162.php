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
### Database Map for: WowBB 162
### Last Update: 4-5-2003
################################################################################

class map_WOWBB_162
{
	
	############################################################################
	### Define the settings for this database map
	############################################################################
	
	function map_WOWBB_162 ()
	{

		$this->map =
		Array (
		'map'           => 'WowBB_162',
		'db_type'       => 'mysql',
		'notes'         => 'This is for WowBB 162',
		'group_type'    => 'db-status',    // db, db-status, status, none

		### Define the account mapping properties
		'account_map_field' => 'users',
		'account_status_field' => 'user_group_id',
		'account_default_status' => '1',
		
		### Define the group fields in the target db
		'group_map'     =>
			Array
			(
			'table'     => 'user_groups',
			'id'        => 'user_group_id',
			'name'      => 'user_group_name'
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
			'map_field'      => 'user_id',
			'unique'         => true
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
			'map_field'      => 'user_password'
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
			'map_field'      => 'user_email'
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
				'name'  => 'user_topic_notification',
				'value' => 1,
				'add'   => 1,
				'edit'  => 0
				),
				Array
				(
				'name'  => 'user_pm_notification',
				'value' => 1,
				'add'   => 1,
				'edit'  => 0
				),				
				Array
				(
				'name'  => 'user_enable_pm',
				'value' => 1,
				'add'   => 1,
				'edit'  => 0
				),
				Array
				(
				'name'  => 'user_invisible',
				'value' => 0,
				'add'   => 1,
				'edit'  => 0
				),
				Array
				(
				'name'  => 'user_unread_pm',
				'value' => 0,
				'add'   => 1,
				'edit'  => 0
				), 
				Array
				(
				'name'  => 'user_admin_emails',
				'value' => 1,
				'add'   => 1,
				'edit'  => 0
				),
				Array
				(
				'name'  => 'user_forum_digest',
				'value' => 1,
				'add'   => 1,
				'edit'  => 0
				),
				Array
				(
				'name'  => 'user_joined',
				'value' => strftime('%Y-%m-%d %H:%M:%S'),
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