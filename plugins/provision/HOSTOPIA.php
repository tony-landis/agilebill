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
	
/* plugin vars:

	SERVER:
	- user
	- pass
	
	PRODUCT:
	- package
	- service
*/

class plgn_prov_HOSTOPIA
{
    function plgn_prov_HOSTOPIA()
    {
        $this->name             = 'HOSTOPIA';
        $this->task_based       = false;
        $this->remote_based     = true;       
    }

    # add new service
    function p_new()
    {
        # generate a password
    	$pass_len = 8; 
		$password = substr(md5(md5(time()).$this->domain_name), 0, $pass_len);		 
		$this->login['password'] = $password;  
    
    	// connect to api
		$rserver = new RRADServer($this->server_cfg['user'], $this->server_cfg['pass']); 
		if (!($rserver->authenticate())) { 
			if($this->server['debug']) print "Couldn't authenticate against server b/c: ".$rserver->getMessage();
			return false;
		}
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";				
		$con_svc = $rserver->getConvenienceService(); 
    	
		// set properties
		$properties = array("FirstName"=> $this->account['first_name'], 
							"LastName"=>  $this->account['last_name'], 
							"Email"=>	  $this->account['email'],
							"Company"=>	  $this->account['company']);
							
		// send command
		$result = $con_svc->newDomain($this->domain_name, $this->login['password'], $this->plugin_data['package'], $properties);
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";
 
		// return results
        if($result) {
        	
        	// add service
        	$con_svc->addService($this->domain_name, $this->plugin_data['service']);
 
        	// update service record
			$db 	= &DB();
			$rs = & $db->Execute( sqlSelect($db,"service", "*", "id=$this->service_id"));   
			$plugin_data = unserialize($rs->fields['host_provision_plugin_data']); 					 
			$insert = Array ( 'host_provision_plugin_data' 	=> serialize($plugin_data),
							  'host_username' => $this->domain_name,
							  'host_password' => $this->login['password']);  
			$sql 	= $db->GetUpdateSQL($rs, $insert);
			$result = $db->Execute($sql);  
	  
		    # send the user the details
		    include_once(PATH_MODULES.'email_template/email_template.inc.php');
		    $email = new email_template;
		    $email->send('host_new_user', $this->account['id'], $this->service_id, '', ''); 
		    
		    return true;
		}				
		return false; 
    }


    # edit service  (not used)
    function p_edit()
    {
    	// connect to api
		$rserver = new RRADServer($this->server_cfg['user'], $this->server_cfg['pass']); 
		if (!($rserver->authenticate())) { 
			if($this->server['debug']) print "Couldn't authenticate against server b/c: ".$rserver->getMessage();
			return false;
		}
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";				
		$con_svc = $rserver->getConvenienceService(); 
    	
		// send command
		$result = $con_svc->setPackage($this->domain_name, $this->plugin_data['plan']);
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";
 
		// return results
        if($result) return true; else return false;
    }


    # activate service
    function p_inactive()
    {
    	// connect to api
		$rserver = new RRADServer($this->server_cfg['user'], $this->server_cfg['pass']); 
		if (!($rserver->authenticate())) { 
			if($this->server['debug']) print "Couldn't authenticate against server b/c: ".$rserver->getMessage();
			return false;
		}
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";				
		$con_svc = $rserver->getConvenienceService(); 
    	
		// send command
		$result = $con_svc->dropService($this->domain_name, $this->plugin_data['plan']);
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";
 
		// return results
        if($result) return true; else return false;
    }


    # deactivate service
    function p_active()
    {
    	// connect to api
		$rserver = new RRADServer($this->server_cfg['user'], $this->server_cfg['pass']); 
		if (!($rserver->authenticate())) { 
			if($this->server['debug']) print "Couldn't authenticate against server b/c: ".$rserver->getMessage();
			return false;
		}
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";				
		$con_svc = $rserver->getConvenienceService(); 
    	
		// send command
		$result = $con_svc->addService($this->domain_name, $this->plugin_data['plan']);
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";
 
		// return results
        if($result) return true; else return false;
    }


    # delete service
    function p_delete()
    { 
    	// connect to api
		$rserver = new RRADServer($this->server_cfg['user'], $this->server_cfg['pass']); 
		if (!($rserver->authenticate())) { 
			if($this->server['debug']) print "Couldn't authenticate against server b/c: ".$rserver->getMessage();
			return false;
		}
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";				
		$con_svc = $rserver->getConvenienceService(); 
    	
		// send command
		$result = $con_svc->delDomain($this->domain_name);
		if($this->server['debug']) print "RServer Msg: ".$rserver->getMessage()."<br>";
 
		// return results
        if($result) return true; else return false;
    }


    # construct echo all updates
    function p_one($id)
    {
        global $C_debug;

        # Get the service details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
                   id               =  ' . $db->qstr( $id ) . ' AND
                   site_id          =  ' . $db->qstr(DEFAULT_SITE);
        $rs 	= $db->Execute($sql);
		$this->service_id = $id;
        if($rs->RecordCount() == 0) {
            return false;
        }
        $this->service      = $rs->fields;
        $this->domain_name  = $this->service['domain_name'].".".$this->service['domain_tld'];
        @$this->plugin_data = unserialize($this->service['host_provision_plugin_data']);

        # Get the account details
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account WHERE
                    id           =  ' . $db->qstr( $this->service['account_id'] ) . ' AND
                    site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $acct = $db->Execute($sql);
        $this->account = $acct->fields;

        # Get the server details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
                   id           =  ' . $db->qstr( $this->service['host_server_id'] ) . ' AND
                   site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if (@$rs->RecordCount() == 0) {
            return false;
        } else {
            $this->server = $rs->fields;
            @$this->server_cfg = unserialize($rs->fields['provision_plugin_data']);
        } 
        
        # Load HOSTOPIA class 
		include_once("_rrad/RRADCoreIncludes.php");
		include_once("_rrad/RRADCommonIncludes.php");
    
        # determine the correct action
        switch ($this->service['queue'])
        {
            # new
            case 'new':
                $result = $this->p_new(); 
            break;

            # active
            case 'active':
                $result = $this->p_active(); 
            break;

            # inactive
            case 'inactive':
                $result = $this->p_inactive();
            break;

            # edit
            case 'edit':
                $result = $this->p_edit();
            break;

            # delete
            case 'delete':
                $result = $this->p_delete();
            break;
        }
 
        # update service record
        if(@$result != false) {
            # update
            $sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
                        queue        =  ' . $db->qstr( 'none' ) . ',
                        date_last    =  ' . $db->qstr( time() ) . '
                        WHERE
                        id           =  ' . $db->qstr( $this->service_id ) . ' AND
                        site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $upd = $db->Execute($sql);

        } else {
            # error log
            $C_debug->error($this->name.'php', $this->service['queue'], @$result);
        }
    }
}
?>