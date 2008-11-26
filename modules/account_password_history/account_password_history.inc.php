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

/* strict password changing enforcement */
class account_password_history
{
	var $duplicateAfter=720; /* number of days between time identical password can be used */
	var $forceChangeAfter=60; /* number of days between forced password change */
	
	/* determine if forced change is due for specified account */
	function getForceChangeDue($account_id) {
		$db=&DB();
		
		
		/* not updated in last X days? */
		$date = (time() - (86400*$this->forceChangeAfter));
		$rs = $db->Execute(sqlSelect($db,"account_password_history","id", "account_id=$account_id and date_orig <= $date and date_last = 0"));
		if($rs && $rs->RecordCount()) return true;
		
		return false;
	}
	
	/* determine if new password is permissible */
	function getIsPasswordOk($account_id, $password, $hashed=true) {
		
		if(!$hashed) $this->hashPassword($password);
		
		$db=&DB();
				
		/* currently used or used in duplicatePeriod? */
		$date = (time() - (86400*$this->duplicateAfter));
		$rs = $db->Execute(sqlSelect($db,"account_password_history","id",
			"account_id=$account_id and password=::$password:: and (date_last=0 OR (date_last > $date OR date_orig > $date))"));
		if($rs && $rs->RecordCount()) return false;
 
		return true;		
	}
	
	/* log the password change */
	function setNewPassword($account_id, $password, $hashed=true) {
		$db=&DB();
		
		if(!$hashed) $this->hashPassword($password);
		
		/* update last_date in existing passwords to indicate they are no longer in use */
		$fields['date_last'] = time(); 
		$db->Execute($sql="update ".AGILE_DB_PREFIX."account_password_history set date_last = ". time() . " where account_id = $account_id");
		
		/* insert new password */
		$fields=Array('date_orig'=>time(), 'date_last'=>0, 'account_id'=>$account_id, 'password' => $password, 'ip' => USER_IP );
		$db->Execute(sqlInsert($db, "account_password_history", $fields));
	}
	
	
	/* hash the password */
	function hashPassword(&$password) { 
		if(defined('PASSWORD_ENCODING_SHA'))  
			$password = sha1($password);
		else  
			$password = md5($password); 		
	}
	
	/* insert temp data for password reset */
	function resetPassword($account) {
            
    	/* Delete the old request */
    	$db=&DB();
    	$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE site_id  = ' . $db->qstr(DEFAULT_SITE) . ' AND field1 = ' . $db->qstr($account);
    	$db->Execute($sql);
         
    	$now    = md5(microtime());
    	$expire = time() + (20*60);        
 
    	/* Create the temporary DB Record */  
    	$id     = $db->GenID(AGILE_DB_PREFIX . 'temporary_data_id');
    	$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'temporary_data SET
                        site_id     = ' . $db->qstr(DEFAULT_SITE) . ',
                        id          = ' . $db->qstr($id) . ',
                        date_orig   = ' . $db->qstr(time()) . ',
                        date_expire = ' . $db->qstr($expire) . ',
                        field1      = ' . $db->qstr($account) . ',
                        field2      = ' . $db->qstr($now);
    	$result = $db->Execute($sql); 
    	return $now;
	}
}
?>