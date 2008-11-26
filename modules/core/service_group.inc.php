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
	
class service_group
{
	# Set variables
	function service_group ( $rs, $groups ) {
		$this->rs = $rs;
		$this->groups = $groups;
	}


	###############################################
	# Add new groups
	###############################################

	function s_new()
	{
		# Loop through each group to add:
		for($i=0; $i<count($this->groups); $i++)
		{
			# Determine the expiry date:
			$this->calcExpiry();

			# Create the new group
			$this->addGroup( $this->groups[$i] );
		}
	}


	###############################################
	# Activate groups
	###############################################

	function s_active()
	{ 
		# Loop through each group to add:
		for($i=0; $i<count($this->groups); $i++)
		{
			# Determine the expiry date:
			$this->calcExpiry();

			# Create the new group
			$this->addGroup( $this->groups[$i] );
		}
	}

	###############################################
	# Deactivate groups
	###############################################

	function s_inactive()
	{ 
		# Loop through each group to add:
		for($i=0; $i<count($this->groups); $i++)
		{
			# Create the new group
			$this->deleteGroup( $this->groups[$i] );
		}
	}

	###############################################
	# Delete Groups
	###############################################

	function s_delete()
	{
		# Loop through each group to add:
		for($i=0; $i<count($this->groups); $i++)
		{
			# Create the new group
			$this->deleteGroup( $this->groups[$i] );
		}
	}


	function s_edit() {
		return;
	}



	########################################################################################################
	#	MISC METHODS
	########################################################################################################



	# Add new group to account
	function addGroup($id)
	{
		# Delete any existing groups for this service & account
		$db = &DB();
		$q = "DELETE FROM ".AGILE_DB_PREFIX."account_group WHERE
				account_id  = ".$db->qstr($this->rs['account_id'])." AND
				group_id    = ".$db->qstr($id)." 	 				 AND
				service_id  = ".$db->qstr($this->rs['id'])." 	 	 AND
				site_id     = ".$db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		# Create the new group:
		$idx = $db->GenID(AGILE_DB_PREFIX . 'account_group_id');
		$q = "INSERT INTO ".AGILE_DB_PREFIX."account_group SET
				id          = ".$db->qstr( $idx ).",
				site_id     = ".$db->qstr( DEFAULT_SITE ).",
				date_orig   = ".$db->qstr( time() ).",
				date_expire = ".$db->qstr( $this->expire ).",
				group_id	= ".$db->qstr( $id ).",
				account_id	= ".$db->qstr( $this->rs['account_id'] ).",
				service_id	= ".$db->qstr( $this->rs['id'] ).",
				active		= ".$db->qstr( '1' );
		$rs1 = $db->Execute($q);
		if ($rs1 === false) { 
			global $C_debug;
			$C_debug->error('service_group.inc.php','addGroup', $db->ErrorMsg());
		}

		# update session cache
		if(defined('SESS_ACCOUNT') && SESS_ACCOUNT == $this->rs['account_id'] && SESS_LOGGED == 1)
		{ 
			if(CACHE_SESSIONS == '1') {
				$force = true;
				$C_auth = new CORE_auth($force);
				global $C_auth2;
				$C_auth2 = $C_auth;
			}			
		} 
		elseif (!defined('SESS_ACCOUNT') || SESS_ACCOUNT != $this->rs['account_id']) 
		{
			# delete the users_session_auth so it is reloaded on their next page view:
			$q = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
					account_id  = ".$db->qstr($this->rs['account_id'])." AND
					site_id     = ".$db->qstr(DEFAULT_SITE);
			$rss = $db->Execute($q);
			while(!$rss->EOF)
			{
				$q = "DELETE FROM ".AGILE_DB_PREFIX."session_auth_cache WHERE
						session_id	= ".$db->qstr($rss->fields['id'])." AND 
						site_id     = ".$db->qstr(DEFAULT_SITE);
				$db->Execute($q);	
				$rss->MoveNext();
			}						
		}

		# Call the dbmap:
		$this->dbmap();
	}



	# Delete/suspend groups
	function deleteGroup($id)
	{
		# Delete any existing groups for this service & account
		$db = &DB();
		$q = "DELETE FROM ".AGILE_DB_PREFIX."account_group WHERE
				account_id  =  ".$db->qstr($this->rs['account_id'])." AND
				group_id    =  ".$db->qstr($id)." 	 				 AND
				service_id  =  ".$db->qstr($this->rs['id'])." 	 AND
				site_id     =  ".$db->qstr(DEFAULT_SITE);
		$db->Execute($q);

		# update session cache
		if(SESS_ACCOUNT == $this->rs['account_id'] && SESS_LOGGED == 1)
		{ 
			if(CACHE_SESSIONS == '1') {
				$force = true;
				$C_auth = new CORE_auth($force);
				global $C_auth2;
				$C_auth2 = $C_auth;
			}			
		} 
		elseif (SESS_ACCOUNT != $this->rs['account_id']) 
		{
			# delete the users_session_auth so it is reloaded on their next page view:
			$q = "SELECT id FROM ".AGILE_DB_PREFIX."session WHERE
					account_id  = ".$db->qstr($this->rs['account_id'])." AND
					site_id     = ".$db->qstr(DEFAULT_SITE);
			$rss = $db->Execute($q);
			while(!$rss->EOF)
			{
				$q = "DELETE FROM ".AGILE_DB_PREFIX."session_auth_cache WHERE
						session_id	= ".$db->qstr($rss->fields['id'])." AND 
						site_id     = ".$db->qstr(DEFAULT_SITE);
				$db->Execute($q);	
				$rss->MoveNext();
			}						
		}

		# Call the dbmap:
		$this->dbmap();
	}


	# Calculate the expiry date for adding/activating groups:
	function calcExpiry()
	{
		if ($this->rs['group_type'] == 0) {
			$this->expire = $this->rs['date_orig'] + (86400*$this->rs['group_days']);
		} else {
			$this->expire = false;
		}
	}


	# Force new login by deleting user's session/session_auth_cache records:
	function forceLogin()
	{
		$db = &DB();
		$q = "SELECT id FROM  ".AGILE_DB_PREFIX."session WHERE
				account_id	= ".$db->qstr( $this->rs['account_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);;
		$rs = $db->Execute($q);
		if ($rs->RecordCount() > 0)
		{
			$q = "DELETE FROM ".AGILE_DB_PREFIX."session WHERE
					id  		= ".$db->qstr($rs->fields['id'])." AND
					site_id     = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);

			$q = "DELETE FROM ".AGILE_DB_PREFIX."sess_auth_cache WHERE
					session_id  = ".$db->qstr($rs->fields['id'])." AND
					site_id     = ".$db->qstr(DEFAULT_SITE);
			$db->Execute($q);
		}

		# Call the dbmap:
		$this->dbmap();
	}



	# Call the db_mapping update
	function dbmap()
	{
		global $C_list;
		if(!is_object($C_list)) {
			include_once(PATH_CORE . 'list.inc.php');
			$C_list = new CORE_list;
		}

		if($C_list->is_installed('db_mapping'))
		{ 
			# Update the db_mapping accounts
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->account_group_sync ( $this->rs['account_id'] );
		}
	}
}
?>