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
class plgn_prov_HELM_3_1
{
    function plgn_prov_HELM_3_1()
    {
        $this->name             = 'HELM_3_1';
        $this->task_based       = false;
        $this->remote_based     = true;
    }

    # add new service
    function p_new()
    {
        # get the common server class and set login details
        include_once(PATH_MODULES.'host_server/host_server.inc.php');
        $host = new host_server;
        if($this->service['host_username'] == '' || $this->service['host_password'] == '') 
        $this->login = $host->generate_login($this->service, $this->account, 4, 4, false);
        else 
        $this->login['username'] = $this->service['host_username']; 
         
		#include the Helm class (sorry, that file is encoded)
		include_once(PATH_CORE.'helm.inc.php');
	   	$helm = new HELM;  
	   	$helm->ssl  		= true; 
		$helm->cookie_path	= PATH_FILES.'HELM_COOKIE.dat'; 
	   	$helm->host 		= $this->server_cfg['host'];
	   	$helm->user 		= $this->server_cfg['user']; 
	   	$helm->pass 		= $this->server_cfg['pass'];
	   	$helm->debug		= $this->server['debug']; 
		$result = $helm->add( 	$this->server_cfg['reseller'],
							$this->login['username'],
							$this->service['domain_name'],
							$this->service['domain_tld'],
							$this->plugin_data['plan'],
							$this->service['sku'],
							$this->account['first_name'],
							$this->account['last_name'],
							$this->account['company'],
							$this->account['address1'],
							$this->account['city'],
							$this->account['state'],
							$this->account['zip'],
							$this->account['email']);  
							
							
	    # send the user the details
	    include_once(PATH_MODULES.'email_template/email_template.inc.php');
	    $email = new email_template;
	    $email->send('host_new_user', $this->account['id'], $this->service_id, '', '');	
	
		return $result;
	}

    # edit service  (not used)
    function p_edit()
    {
        return true;
    }


    # activate service
    function p_inactive()
    {
		#include the Helm class (sorry, that file is encoded)
		include_once(PATH_CORE.'helm.inc.php');
	   	$helm = new HELM;  
	   	$helm->ssl  		= true; 
		$helm->cookie_path	= PATH_FILES.'HELM_COOKIE.dat'; 
	   	$helm->host 		= $this->server_cfg['host'];
	   	$helm->user 		= $this->server_cfg['user']; 
	   	$helm->pass 		= $this->server_cfg['pass'];
	   	$helm->debug		= $this->server['debug']; 
		return $helm->suspend($this->service['host_username']); 
    }


    # deactivate service
    function p_active()
    {
		#include the Helm class (sorry, that file is encoded)
		include_once(PATH_CORE.'helm.inc.php');
	   	$helm = new HELM;  
	   	$helm->ssl  		= true; 
		$helm->cookie_path	= PATH_FILES.'HELM_COOKIE.dat'; 
	   	$helm->host 		= $this->server_cfg['host'];
	   	$helm->user 		= $this->server_cfg['user']; 
	   	$helm->pass 		= $this->server_cfg['pass'];
	   	$helm->debug		= $this->server['debug']; 
		return $helm->unsuspend($this->service['host_username']); 
    }


    # delete service
    function p_delete()
    {
		#include the Helm class (sorry, that file is encoded)
		include_once(PATH_CORE.'helm.inc.php');
	   	$helm = new HELM;  
	   	$helm->ssl  		= true; 
		$helm->cookie_path	= PATH_FILES.'HELM_COOKIE.dat'; 
	   	$helm->host 		= $this->server_cfg['host'];
	   	$helm->user 		= $this->server_cfg['user']; 
	   	$helm->pass 		= $this->server_cfg['pass'];
	   	$helm->debug		= $this->server['debug']; 
		return $helm->del($this->service['host_username']); 
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
                $result 	= $this->p_inactive();
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
        if(@$result != false)
        {
            # update
            $sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
                        queue        =  ' . $db->qstr( 'none' ) . ',
                        date_last    =  ' . $db->qstr( time() ) . '
                        WHERE
                        id           =  ' . $db->qstr( $rs->fields['id'] ) . ' AND
                        site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $upd = $db->Execute($sql);

        } else {
            # error log
            $C_debug->error($this->name.'php', $this->service['queue'], @$result);
        }
    }
}
?>