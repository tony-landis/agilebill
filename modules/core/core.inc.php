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
	
define('AGILE_SESS_EXPIRE_DAYS',        15);
define('AGILE_SESS_AFFIL_EXPIRE_DAYS',  180);
define('AGILE_LOG_ERR_EXPIRE_DAYS',     15);
define('AGILE_ACCOUNT_CLEANUP_DAYS',    60);
define('AGILE_INVOICE_CLEANUP_DAYS',    60);
define('AGILE_LOGIN_LOGS_EXPIRE_DAYS',  180);

class core
{
    # Main cleanup function
    function cleanup($VAR)
    {
		$alert = false;
		
        /*
          cleanup:  sessions
                    session auth cache
                    searches
                    saved searches
                    old task logs
                    old error logs
                    old login logs
                    old login locks
                    old temporary data
                    old backups
                    expired groups
                    
          */
          
          $clean = Array(
 
                    Array ( 'table' => 'log_error',
                            'field' => 'date_orig',
                            'where' => '<=',
                            'cond'  => time()-86400*AGILE_LOG_ERR_EXPIRE_DAYS
                          ),

                    Array ( 'table' => 'login_log',
                            'field' => 'date_orig',
                            'where' => '<=',
                            'cond'  => time()-86400*AGILE_LOGIN_LOGS_EXPIRE_DAYS
                          ),

                    Array ( 'table' => 'session_auth_cache',
                            'field' => 'date_expire',
                            'where' => '<=',
                            'cond'  => time()
                          ),


                    Array ( 'table' => 'search',
                            'field' => 'date_expire',
                            'where' => '<=',
                            'cond'  => time()
                          ),


                    Array ( 'table' => 'search_saved',
                            'field' => 'date_expire',
                            'where' => '<=',
                            'cond'  => time()
                          ),
                          
                    Array ( 'table' => 'login_lock',
                            'field' => 'date_expire',
                            'where' => '<=',
                            'cond'  => time()
                          ),
                          
                    Array ( 'table' => 'temporary_data',
                            'field' => 'date_expire',
                            'where' => '<=',
                            'cond'  => time()
                          )  
                        );
						 
                        
        for($i=0; $i<count($clean); $i++)
        {
            $db = &DB();
            $sql = "DELETE FROM ".AGILE_DB_PREFIX."".$clean[$i]['table']." WHERE
                    site_id             = ".$db->qstr(DEFAULT_SITE)." AND " .
                    $clean[$i]['field']."   != ".$db->qstr('')." AND " .
                    $clean[$i]['field']."   != ".$db->qstr('0')." AND " .
                    $clean[$i]['field']." ".$clean[$i]['where']." ".$db->qstr($clean[$i]['cond']);
            $result=$db->Execute($sql);
        }
		
		########################################################################
		### Remove old sessions
		########################################################################
				
        $sql = "DELETE FROM ".AGILE_DB_PREFIX."session WHERE
        		site_id     = ".$db->qstr(DEFAULT_SITE)." AND
        		date_last	< ".$db->qstr(time()-86400*AGILE_SESS_EXPIRE_DAYS)."   
                AND
                ( 
                	affiliate_id IS NULL OR
                	affiliate_id = '' OR
                	affiliate_id = 0 OR
                	campaign_id IS NULL OR
                	campaign_id = '' OR
                	campaign_id = 0 OR
                	date_last	< ".$db->qstr(time()-86400*AGILE_SESS_AFFIL_EXPIRE_DAYS)."
                )"; 
        $result = $db->Execute($sql);	      

               
		########################################################################
		### Remove expired group access
		########################################################################
				
        $sql = "DELETE FROM ".AGILE_DB_PREFIX."account_group WHERE
                site_id        = ".$db->qstr(DEFAULT_SITE)." AND
                date_expire IS NOT NULL AND
                date_expire > 0 AND
                date_expire <= ".$db->qstr(time());
        $result = $db->Execute($sql);	
			
        
        ########################################################################
        ### Remove old backups
        ########################################################################
        
        $sql = "SELECT * FROM ".AGILE_DB_PREFIX."backup WHERE
                site_id        = ".$db->qstr(DEFAULT_SITE)." AND
                date_expire   != ".$db->qstr('')." AND
                date_expire   != ".$db->qstr('0')." AND
                date_expire   != ".$db->qstr(time());
        $result = $db->Execute($sql);
        if ( $result != false && $result->RecordCount() > 0)
        {
            while (!$result->EOF)
            {
                ## Delete this one..
                $arr["delete_id"] = $result->fields['id'];
                include_once (PATH_MODULES . 'backup/backup.inc.php');
                $backup = new backup;
                $backup->delete($arr,$backup);
                $result->MoveNext();
            }
        }

        #########################################################################
        ### Repair/optimize DB Tables (MYSQL ONLY!)
        #########################################################################

        if(AGILE_DB_TYPE == 'mysql')
        {
            $db = &DB();
            $q  = "SELECT name FROM ".AGILE_DB_PREFIX."module WHERE site_id = ".$db->qstr(DEFAULT_SITE);
            $rs = $db->Execute($q);

            while ( !$rs->EOF )
            {
                $table = $rs->fields['name'];
                $sql = "CHECK TABLE ".AGILE_DB_PREFIX.$table;
                $rscheck = $db->Execute($sql);
                if ($rscheck && $rscheck->fields['Msg_type'] == 'status' && $rscheck->fields['Msg_text'] == 'OK') {
					$sql = "ANALYZE TABLE ".AGILE_DB_PREFIX.$table;
					$db->Execute($sql);
				} else {   
                    $sql = "REPAIR TABLE ".AGILE_DB_PREFIX.$table;
                    $db->Execute($sql);

                    $sql = "OPTIMIZE TABLE ".AGILE_DB_PREFIX.$table;
                    $db->Execute($sql);
                }

                $rs->MoveNext();
            }
        }
		
        #########################################################################
        ### Force the correct id keys in each table that has unique ids
        ######################################################################### 
        $sql = "SELECT name FROM ".AGILE_DB_PREFIX."module WHERE
                site_id  = ".$db->qstr(DEFAULT_SITE)." AND
                status   = ".$db->qstr('1')." 
				ORDER BY name";	
		$rs = $db->Execute($sql);
		while(!$rs->EOF)	
		{
			$module = $rs->fields['name'];
			
			# check if key table exists:
        	$sql = "SELECT id FROM ".AGILE_DB_PREFIX.$module."_id ORDER BY id DESC";
			$keytable = $db->Execute($sql);
			if ($module != 'session' && $module != 'affiliate' && $keytable != false && $keytable->RecordCount() > 0) 
			{ 
				$current_id = $keytable->fields['id'];
				
				# get the current id from the main table:
	        	$sql = "SELECT id FROM ".AGILE_DB_PREFIX.$module." ORDER BY id DESC";
				$table = $db->Execute($sql);	
				if ($table != false && $table->RecordCount() > 0) 
				{ 
					$last_id = $table->fields['id'];
					
					# does key need updated?
					if($current_id < $last_id)
					{
						$id = $last_id + 1;						
						$sql = "UPDATE ".AGILE_DB_PREFIX.$module."_id
								SET 
								id = ".$db->qstr($id)." 
								WHERE
								id = ".$db->qstr($current_id);
						 $db->Execute($sql);	
						 $alert.= "Corrected incorrect primary key on the <u>$module</u> table.<br>"; 
						
					}
				}							
			}   
			$rs->MoveNext() ;
		} 
		
        #########################################################################
        ### Run any new upgrade files in the /upgrades directory
        #########################################################################
		 		 
        @$dir = opendir(PATH_AGILE.'upgrades'); 
        while (@$file_name = readdir($dir))
        {
            $display = true; 
            if($file_name != '..' && $file_name != '.')
            {
				# check if upgrade has been run:
				$md5 = md5($file_name); 
				$sql = "SELECT data FROM ".AGILE_DB_PREFIX."temporary_data
						WHERE
						data = ".$db->qstr($md5)." AND
						field1 = ".$db->qstr('upgrade');
				$rs = $db->Execute($sql);
				if($rs->RecordCount() == 0)
				{
					# Run the upgrade: 
					if(is_file(PATH_AGILE.'upgrades/'.$file_name) && !is_dir(PATH_AGILE.'upgrades/'.$file_name))
						include_once(PATH_AGILE.'upgrades/'.$file_name); 
						
					$function = strtolower(preg_replace('/.php/', '', $file_name)); 
					
					if(preg_match('/^upgrade/', $function))
					{
						if(is_callable($function)) 
						{
							$result = call_user_func ( $function ); 
							#$result = true;
						}
						else
						{
							$result = false;
	            		}						
					}	
					    
					# If success, save so it is not run again:
					if($result)
					{  
						/* Start: SQL Insert Statement */
						$sql = "SELECT * FROM ".AGILE_DB_PREFIX."temporary_data WHERE id = -1";
						$rs = $db->Execute($sql); 
		 
						$id = $db->GenID(AGILE_DB_PREFIX . 'temporary_data_id');
						$insert = Array (	'id' 			=> $id,
											'site_id' 		=> DEFAULT_SITE,
											'data' 			=> $md5,
											'field1' 		=> 'upgrade',
											'date_orig' 	=> time(),
											'date_expire' 	=> time()+86400*365*20);
						 
						$sql = $db->GetInsertSQL($rs, $insert); 
						$result = $db->Execute($sql);  
				        if ($result === false) {
				        	global $C_debug;
				        	$C_debug->error('core.inc.php','core :: cleanup()', $db->ErrorMsg(). "\r\n\r\n". $sql); 
				        }						
						/* End: SQL Insert Statement */
						 				
						$alert .= "Upgraded to <u>$file_name</u>!<br>";
					} else {
						$alert .= "The <u>$file_name</u> upgrade failed!<br>";
					}				
				}			 
            }	
		}	
		
		
		
		#########################################################################
		## Print any alerts:
		#########################################################################
		
		if(!empty($alert))
		{
			global $C_debug;
			$C_debug->alert($alert);
		}
		
        return true;
    }

	
	/**
	 * Update invoices that are within 1 cent of full payment to paid in full
	 * so they are not suspended because of minor currency fluctuations.
	 * Change the $diff variable to the max allowable balance due on the invoice
	 * in order for the update to take place.
	 */
	function paid_fraction($VAR) {
		$diff=.02;
		$db=&DB();
		$p=AGILE_DB_PREFIX;
		$s=DEFAULT_SITE;
		$sql="UPDATE {$p}invoice SET process_status=1,billing_status=1,billed_amt=total_amt 
			WHERE ((total_amt-billed_amt)<=$diff) AND (total_amt!=billed_amt)";
		$db->Execute($sql);
		return true;
	}
	
	
	/**
	* send advance invoice notice via email
	*/
	function invoice_advance_notice($VAR) {
		include_once(PATH_MODULES . 'invoice/advance_notice.php');
		$obj = new advance_notice();
		$obj->task();	
	}
	
	
	/**
	 *  Loop though all accounts to delete inactive accounts with no 
	 * invoices, services, and no staff or affiliate accts 
	 */
    function account_cleanup($VAR)
    { 
        # Load the account admin class
        include_once(PATH_MODULES.'account_admin/account_admin.inc.php');
        $acct = new account_admin;

        # Get each account:
        $time = time()-(AGILE_ACCOUNT_CLEANUP_DAYS*86400);
        $db   = &DB();
        $db->SetFetchMode(ADODB_FETCH_ASSOC);
        $sql    = 'SELECT id,username,email,first_name,last_name,company FROM '.AGILE_DB_PREFIX.'account WHERE date_orig <= '.$time.' AND id>1 AND site_id = '.DEFAULT_SITE.' ORDER BY id';
        $rs = $db->Execute($sql);
        echo "Accounts Deleted:<pre>";
        while (!$rs->EOF)
        {
            # Get acct id
            $id = $rs->fields['id']; 
            $do = true;
             
            # Check for staff/admin account
            if ($do) {
            	$sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'account_group WHERE group_id in (4,1001) AND AND account_id='.$id.' site_id = ' . $db->qstr(DEFAULT_SITE);
            	$rs2 = $db->Execute($sql);
            	if($rs2 && $rs2->RecordCount()) $do = false; 
            }           

            # Check for invoices
            if ($do) {
	            $sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'invoice WHERE account_id =  ' . $db->qstr( $id ) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
	            $rs2 = $db->Execute($sql);
	            if($rs2 && $rs2->RecordCount()) $do = false; 
            }

            # Check for services
            if ($do) {
            	$sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'service WHERE account_id =  ' . $db->qstr( $id ) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
            	$rs2 = $db->Execute($sql);
            	if($rs2 && $rs2->RecordCount()) $do = false; 
            }

            # Check for affiliate acct
           if ($do) {
            	$sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'affiliate WHERE account_id =  ' . $db->qstr( $id ) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
            	$rs2 = $db->Execute($sql);
            	if($rs2 && $rs2->RecordCount()) $do = false; 
            }

            # Check for staff acct
            if ($do) {
            	$sql = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'staff WHERE account_id =  ' . $db->qstr( $id ) . ' AND site_id = ' . $db->qstr(DEFAULT_SITE);
            	$rs2 = $db->Execute($sql);
            	if($rs2 && $rs2->RecordCount()) $do = false; 
            }

            # Delete the account
            if ($do) { 
            	$arr['id'] = $id;
            	foreach($rs->fields as $v) echo "$v	";
            	echo "\r\n";
            	$acct->delete($arr, $acct);
            }
           
            $rs->MoveNext();
        }
        echo "</pre>";
    }

    # Delete inactive/unpaid invoices older than the allowed period:
    function invoice_cleanup($VAR)
    {
        # Load the account admin class
        include_once(PATH_MODULES.'invoice/invoice.inc.php');
        $invoice = new invoice;
        $exp = time() - 86400*AGILE_INVOICE_CLEANUP_DAYS;

        # Get each account:
        $db     = &DB();
        $sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'invoice
                    WHERE
                    process_status  =  ' . $db->qstr( 0 ) . '  AND
                    billing_status  =  ' . $db->qstr( 0 ) . '  AND
                    billed_amt      <= ' . $db->qstr( 0 ) . '  AND
                    date_last       <= ' . $db->qstr( $exp ) . '  AND
                    site_id         =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        while (!$rs->EOF)
        {
            $arr['id'] = $rs->fields['id'];
            $invoice->delete( $arr, $invoice );
            $rs->MoveNext();
        }
    }
}
?>