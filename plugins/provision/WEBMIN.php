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
	


# Main Class
class plgn_prov_WEBMIN
{
    function plgn_prov_WEBMIN()
    {
        $this->name             = 'WEBMIN';
        $this->task_based       = false;
        $this->remote_based     = true;
    }

    # add new service
    function p_new()
    { 	
        # get the common server class and set login details
        include_once(PATH_MODULES.'host_server/host_server.inc.php');
        $host = new host_server;
        if($this->service['host_username'] == '' && $this->service['host_password'] == '') 
		{
        	$this->login = $host->generate_login($this->service, $this->account, 4, 4, false);
		}
        else 
		{
        	$this->login['username'] = $this->service['host_username']; 
			$this->login['password'] = $this->service['host_password']; 
		}
         
        # get ip address
        if ($this->plugin_data['network_interface'] == '1') {
            $this->ip = $host->useipaddress($this->service, $this->server);
        } else {
            $this->ip = $this->server['name_based_ip'] ;  
        }		 
		 
		#include the webmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'webmin.inc.php');
		$e = new WEBMIN;  
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass']; 
		$e->port 	= $this->server_cfg['port']; 
		$e->ssl 	= $this->server_cfg['ssl'];
		
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		$e->username= $this->login['username'];
		$e->password= $this->login['password']; 
		$e->email	= $this->account['email'];		
		$e->ip		= $this->ip;		
		$e->prod	= $this->plugin_data;
 		
		# add
		$result = $e->add(); 
 
	    # send the user the details
	    include_once(PATH_MODULES.'email_template/email_template.inc.php');
	    $email = new email_template;
	    $email->send('host_new_user', $this->account['id'], $this->service_id, '', '');	
	
		return $result;
	}

    # edit service  (not used)
    function p_edit()
    { 	
		#include the webmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'webmin.inc.php');
		$e = new WEBMIN;  
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass']; 
		$e->port 	= $this->server_cfg['port']; 
		$e->ssl 	= $this->server_cfg['ssl'];
		
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		$e->password= $this->service['host_password']; 
		$e->email	= $this->account['email'];
		$e->prod	= $this->plugin_data;
		
        if(!empty($this->service['host_ip']))
            $e->ip = $this->service['host_ip'];
       	else
            $e->ip = $this->server['name_based_ip'];
		 	 
 		# edit
		$result = $e->edit(); 
		
		# suspend/unsuspend
        if ( $this->service['active'] == 1  )
        	$e->unsuspend();
        else
        	$e->suspend(); 
							
		return $result; 
    }


    # activate service
    function p_inactive()
    {
		#include the webmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'webmin.inc.php');
		$e = new WEBMIN;  
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass']; 
		$e->port 	= $this->server_cfg['port']; 
		$e->ssl 	= $this->server_cfg['ssl'];
		
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		
		return $e->suspend(); 
    }


    # deactivate service
    function p_active()
    {
		#include the webmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'webmin.inc.php');
		$e = new WEBMIN;  
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass']; 
		$e->port 	= $this->server_cfg['port']; 
		$e->ssl 	= $this->server_cfg['ssl'];
		
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		
		return $e->unsuspend(); 
    }


    # delete service
    function p_delete()
    {
		# recycle the IP if ip_based: 
        if ($this->plugin_data['network_interface'] == '1') {			 
		    include_once(PATH_MODULES.'host_server/host_server.inc.php');
		    $host = new host_server;					
			$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);	
        }  		
			
		#include the webmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'webmin.inc.php');
		$e = new WEBMIN;  
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass']; 
		$e->port 	= $this->server_cfg['port']; 
		$e->ssl 	= $this->server_cfg['ssl'];
		
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
						
		return $e->del(); 
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
		$this->service = $rs->fields;
        if($rs->RecordCount() == 0) {
            return false;
        }
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
				$this->p_edit();
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
}
?>