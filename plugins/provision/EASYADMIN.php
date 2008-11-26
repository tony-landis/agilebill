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
class plgn_prov_EASYADMIN
{
    function plgn_prov_EASYADMIN()
    {
        $this->name             = 'EASYADMIN';
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
        if ($this->plugin_data['hst_type'] == '0') {
            $this->ip = $host->useipaddress($this->service, $this->server);
        } else {
            $this->ip = $this->server['name_based_ip'] ; 
            $this->plugin_data['enssl']     = 0;
        }		 
		 
		#include the easyAdmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'easyadmin.inc.php');
		$e = new EASYADMIN; 
		$e->cookiepath = PATH_FILES . 'easyCookie.txt';
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass'];
		$e->reseller= $this->server_cfg['reseller']; 
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		$e->username= $this->login['username'];
		$e->passwd  = $this->login['password']; 
		$e->email	= $this->account['email'];		
		$e->ip		= $this->ip;		
		$e->prod	= Array('users' 	=> $this->plugin_data['users'],
							'quota' 	=> $this->plugin_data['quota'], 
							'enfp'		=> $this->plugin_data['enfp'],
							'enphp'		=> $this->plugin_data['enphp'],
							'enshell'	=> $this->plugin_data['enshell'],
							'enssi'		=> $this->plugin_data['enssi'],
							'encgi'		=> $this->plugin_data['encgi'],
							'ensuexec'	=> $this->plugin_data['ensuexec'],
							'enthrottle'=> $this->plugin_data['enthrottle'],
							'enraw'		=> $this->plugin_data['enraw'],
							'enmiva'	=> $this->plugin_data['enmiva'],
							'enssl'		=> $this->plugin_data['enssl'],
							'enfilter'	=> $this->plugin_data['enfilter'],
							'limit'		=> $this->plugin_data['limit'],
							'bwunit'	=> $this->plugin_data['bwunit'],
							'duration'	=> $this->plugin_data['duration'],
							'durationunit'=>$this->plugin_data['durationunit'] );
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
		#include the easyAdmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'easyadmin.inc.php');
		$e = new EASYADMIN; 
		$e->cookiepath = PATH_FILES . 'easyCookie.txt'; 		
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass'];
		$e->reseller= $this->server_cfg['reseller']; 
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		$e->passwd  = $this->login['password']; 
		$e->email	= $this->account['email'];
		
        if(!empty($this->service['host_ip']))
            $e->ip = $this->service['host_ip'];
       	else
            $e->ip = $this->server['name_based_ip'];
		 		
		$e->prod	= Array('users' 	=> $this->plugin_data['users'],
							'quota' 	=> $this->plugin_data['quota'], 
							'enfp'		=> $this->plugin_data['enfp'],
							'enphp'		=> $this->plugin_data['enphp'],
							'enshell'	=> $this->plugin_data['enshell'],
							'enssi'		=> $this->plugin_data['enssi'],
							'encgi'		=> $this->plugin_data['encgi'],
							'ensuexec'	=> $this->plugin_data['ensuexec'],
							'enthrottle'=> $this->plugin_data['enthrottle'],
							'enraw'		=> $this->plugin_data['enraw'],
							'enmiva'	=> $this->plugin_data['enmiva'],
							'enssl'		=> $this->plugin_data['enssl'],
							'enfilter'	=> $this->plugin_data['enfilter'],
							'limit'		=> $this->plugin_data['limit'],
							'bwunit'	=> $this->plugin_data['bwunit'],
							'duration'	=> $this->plugin_data['duration'],
							'durationunit'=>$this->plugin_data['durationunit'] );
 		# add
		$result = $e->edit(); 
		
		# suspend/unsuspend
        if ( $this->service['active'] == 1  )
        	$e->unsuspend(false);
        else
        	$e->suspend(false); 
							
		return $result; 
    }


    # activate service
    function p_inactive()
    {
		#include the easyAdmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'easyadmin.inc.php');
		$e = new EASYADMIN; 
		$e->cookiepath = PATH_FILES . 'easyCookie.txt'; 
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass'];
		$e->reseller= $this->server_cfg['reseller']; 
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		
		return $e->suspend(); 
    }


    # deactivate service
    function p_active()
    {
		#include the easyAdmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'easyadmin.inc.php');
		$e = new EASYADMIN; 
		$e->cookiepath = PATH_FILES . 'easyCookie.txt'; 
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass'];
		$e->reseller= $this->server_cfg['reseller']; 
		$e->domain	= $this->service['domain_name'].'.'.$this->service['domain_tld']; 
		
		return $e->unsuspend(); 
    }


    # delete service
    function p_delete()
    {
		# recycle the IP if ip_based: 
        if ($this->plugin_data['hst_type'] == '0') {			 
		    include_once(PATH_MODULES.'host_server/host_server.inc.php');
		    $host = new host_server;					
			$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);	
        }  		
			
		#include the easyAdmin class (sorry, that file is encoded)
		include_once(PATH_CORE.'easyadmin.inc.php');
		$e = new EASYADMIN; 
		$e->cookiepath = PATH_FILES . 'easyCookie.txt'; 
		$e->debug   = $this->server['debug'];
		$e->host 	= $this->server_cfg['host'];
		$e->user 	= $this->server_cfg['user'];
		$e->pass 	= $this->server_cfg['pass'];
		$e->reseller= $this->server_cfg['reseller']; 
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