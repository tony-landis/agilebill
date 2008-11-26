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
	
class login_share
{
	function login_share() 
	{
		// Number of days to check when searching for shared passwords
		$this->range = 7;
		
		// Cutoff number for different subnets - if the amount of unique
		// IP addresses used to access an account is greater than this number, the
		// account will be suspended.
		$this->max_subnets = 5;
		
		// number of subnets to compare when search for shared passwords
		// example: 
		// 	"4" would check the full IP address, ie: 123.456.789.123
		//	"3" would check the first 3 subnets of an IP, ie: 123.456.789. (recommended)
		//	"2" would check the first 2 subnets of an IP, ie: 123.456.
		// 	"1" would check the first 1 subnets of an IP, ie: 123.
		$this->subnets = 3;	
		
		// List of exempt usernames:
		$this->exempt = Array('admin');	
	}
		
	### Detect password sharing
	function login($account_id, $username)
	{
		# Check if account is exempt:
		if(!empty($this->exempt) && is_array($this->exempt))
			for($i=0; $i<count($this->exempt); $i++)
				if($this->exempt[$i] == $username)
					return true;
		
		# Determine current subnet to match:
		@$arr = explode('.', USER_IP);
		
		# Validate values
		if(!is_array($arr) || !is_numeric($this->subnets) || $this->subnets > 4 )
			return true;
			
		# Subnet...
		$subnet='';
		for($i=0; $i<$this->subnets; $i++)  { 
			$subnet .= $arr[$i];
			if($i<3) $subnet .= '.';
		}
		
		# time limit...
		$limit = time()	- (86400 * $this->range);
		
		# Generate SQL query
		$db = &DB(); 
		$sql = "SELECT DISTINCT 
					ip 
				FROM ".
					AGILE_DB_PREFIX."login_log
				WHERE
					date_orig > $limit
				AND
					ip NOT LIKE '$subnet%'
				AND
					account_id = $account_id
				AND
					site_id	   = ".DEFAULT_SITE;
		$rs = $db->Execute($sql);
		
		if($this->max_subnets > 0 && $rs->RecordCount() >= ($this->max_subnets - 1))
		{
			$subnets["$subnet"] = true;
			
			$count = 0;
			while(!$rs->EOF)
			{
				@$arr = explode('.', $rs->fields['ip']);
				$subnet1='';
				for($i=0; $i<$this->subnets; $i++) {
					$subnet1 .= $arr[$i];
					if($i<3) $subnet1 .= '.';
				}
					
				if(empty($subnets["$subnet1"]))
					$subnets["$subnet1"] = true;
					
				if(!empty($subnets) && count($subnets) >= $this->max_subnets) 
					break;
				
				$rs->MoveNext();
			}
			
			# over limit?
			if(!empty($subnets) && count($subnets) >= $this->max_subnets) 
			{ 
				# Deactivate account: 
				$sql = "UPDATE ".
							AGILE_DB_PREFIX."account
						SET
							status 	= 0
						WHERE 
							site_id	= ".DEFAULT_SITE."
						AND
							id = $account_id";
				$db->Execute($sql);	
				
				# send e-mail alerts
			    include_once(PATH_MODULES.'email_template/email_template.inc.php');
			    
			    # send user alert
			    $email = new email_template;
			    $email->send('login_share_ban_user', $account_id, '', $this->max_subnets, count($subnets));				
			    
			    # send admin alert
			    $email = new email_template;
			    $email->send('admin->login_share_ban_admin', $account_id, '', $this->max_subnets, count($subnets));				    
						 
				return false;
			}
			else
			{ 
				return true;
			}
		} 
		else 
		{ 
			return true;
		}
	}
}
?>