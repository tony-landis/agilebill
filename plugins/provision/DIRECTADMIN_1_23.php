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
	
/*
* Vars available from server config:
* 
* host
* port
* user
* pass 
* reseller
* notify
* 
* 
* Vars from service config:
* 
* package
* 
*/
class plgn_prov_DIRECTADMIN_1_23
{
    function plgn_prov_DIRECTADMIN_1_23()
    {
        $this->name             = 'DIRECTADMIN_1_23';
        $this->task_based       = false;
        $this->remote_based     = true;
		$this->cookiepath 		= PATH_FILES . 'DirectAdminCookie.txt';
    }

    # add new service
    function p_new()
    {
        # get the common server class and set login details
        include_once(PATH_MODULES.'host_server/host_server.inc.php');
        $host = new host_server;
        if($this->service['host_username'] == '' || $this->service['host_password'] == '')
        {
            $this->login = $host->generate_login($this->service, $this->account, 4, 4, false);
        } else {
            $this->login['username'] = $this->service['host_username'];
            $this->login['password'] = $this->service['host_password'];
        }
		
        # get ip address
        if ($this->plugin_data['type'] == '1') {
            $this->ip = $host->useipaddress($this->service, $this->server);
        } else {
            $this->ip = $this->server['name_based_ip']; 
        }		
	  
	    # Set the post vars:
		$this->host	= 	'https://' .$this->server_cfg['host']. ':' .$this->server_cfg['port'] . 
					  	'/CMD_ACCOUNT_USER';
		$this->post =  	"action=create".
						"&add=Submit".
						"&username={$this->login['username']}".
						"&email={$this->account['email']}".
						"&passwd={$this->login['password']}".
						"&passwd2={$this->login['password']}".
						"&domain={$this->service['domain_name']}.{$this->service['domain_tld']}".
						"&package={$this->plugin_data['package']}".
						"&ip={$this->ip}".
						"&notify={$this->server_cfg['notify']}";  
				 
		# Connect & get response: 
		$result = $this->connect('25');
	  
		# Check the response & Debug
        if($this->server['debug']) echo "<pre> ". print_r($result) ." </pre>"; 
		
		if(!empty($result))
        	return true;
        else
        	return false;	
    }


    # edit service  (not used)
    function p_edit()
    { 
		return true;
    }


    # activate service
    function p_inactive()
    { 
	    # Set the post vars:
		$this->host = 	'https://' .$this->server_cfg['host']. ':' .$this->server_cfg['port'] . 
					  	'/CMD_SELECT_USERS?'.
		 				"location=CMD_SELECT_USERS&suspend=suspend".
						"&select0={$this->service['host_username']}";  
				 
		# Connect & get response: 
		$result = $this->connect('15');
	  
		# Check the response & Debug
        if($this->server['debug']) echo "<pre> ". print_r($result) ." </pre>"; 
		
		if(!empty($result))
        	return true;
        else
        	return false;		
    }


    # deactivate service
    function p_active()
    { 
	    # Set the post vars:
		$this->host = 	'https://' .$this->server_cfg['host']. ':' .$this->server_cfg['port'] . 
				  		'/CMD_SELECT_USERS?'.
						"location=CMD_SELECT_USERS&suspend=unsuspend".
						"&select0={$this->service['host_username']}";
				 
		# Connect & get response: 
		$result = $this->connect('10');
	  
		# Check the response & Debug
        if($this->server['debug']) echo "<pre> ". print_r($result) ." </pre>"; 
		
		if(!empty($result))
        	return true;
        else
        	return false;		
    }


    # delete service
    function p_delete()
    { 
		# recycle the IP if ip_based: 
        if ($this->plugin_data['type'] == '1') {			 
		    include_once(PATH_MODULES.'host_server/host_server.inc.php');
		    $host = new host_server;					
			$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);	
        } 
			
	    # Set the post vars:
		$this->host = 	'https://' .$this->server_cfg['host']. ':' .$this->server_cfg['port'] . 
				  		'/CMD_SELECT_USERS?'.
						"confirmed=Confirm&delete=yes".
						"&select0={$this->service['host_username']}";
				 
		# Connect & get response: 
		$result = $this->connect('10');
				   
		# Check the response & Debug
        if($this->server['debug']) echo "<pre> ". print_r($result) ." </pre>"; 
		
		if(!empty($result))
        	return true;
        else
        	return false;		
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
        $rs = $db->Execute($sql);
        if($rs->RecordCount() == 0) {
            return false;
        }
        $this->service      = $rs->fields;
		$this->service_id   = $rs->fields['id'];
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

       

        # determine the correct action
        switch ($this->service['queue'])
        {
            # new
            case 'new':
                $result = $this->p_new();
                # send the user the details
                include_once(PATH_MODULES.'email_template/email_template.inc.php');
                $email = new email_template;
                $email->send('host_new_user', $this->account['id'], $rs->fields['id'], '', '');
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
                if ($this->service['active'] == 1  )
                    $result = $this->p_active();
                else
                    $result = $this->p_inactive();
            break;

            # delete
            case 'delete':
                $result = $this->p_delete();
            break;
        }


        # update service record
        if(@$result)
        {
            # update
			$db     	= &DB();
            $sql   = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
                        queue        =  ' . $db->qstr( 'none' ) . ',
                        date_last    =  ' . $db->qstr( time() ) . '
                        WHERE
                        id           =  ' . $db->qstr( $this->service_id ) . ' AND
                        site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $upd = $db->Execute($sql);

        } else {
            # error log
            $C_debug->error($this->name.'.php', $this->service['queue'], $this->service['queue'] . ' For service id '. $this->service_id . ' Failed!');
        }
    }
	
	
	
	/*
	* Curl connect 
	*/  	   
	function connect($timeout=false)
	{     
		$ch = curl_init($this->host); 
		$header = Array (
			'Authorization: Basic '.base64_encode("{$this->server_cfg['user']}:{$this->server_cfg['pass']}"), 
			'Content-type: application/x-www-form-urlencoded'			
		);
		curl_setopt($ch, CURLOPT_HTTPHEADER, 		$header);  
		if($timeout != false)			
		curl_setopt($ch, CURLOPT_TIMEOUT, 			$timeout);  
		#curl_setopt($ch, CURLOPT_URL, 				$this->host );   
		
		if(!empty($this->post)) {
			curl_setopt($ch, CURLOPT_POST, 				1); 
	   		curl_setopt($ch, CURLOPT_POSTFIELDS, 		$this->post);  
		}
		 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 	0); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	1);  
		$data = curl_exec ($ch); 
		curl_close ($ch);   
		return $data; 
	} 	
}
?>