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
	
class plgn_prov_MANUAL
{
    function plgn_prov_MANUAL()
    {
        $this->name             = 'MANUAL';
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
        {
            $this->login = $host->generate_login($this->service, $this->account, 4, 4, false);
        } else {
            $this->login['username'] = $this->service['host_username'];
            $this->login['password'] = $this->service['host_password'];
        }

        # get ip address
        if ($this->plugin_data['ipinfo_namebased'] == '1')
        $host->useipaddress($this->service, $this->server);

        # send the admin the creation email
        include_once(PATH_MODULES.'email_template/email_template.inc.php');
        $email = new email_template;
        $email->send('admin->host_new_admin', $this->account['id'], $this->service['id'], '', '');

        # send the user the alert email
        include_once(PATH_MODULES.'email_template/email_template.inc.php');
        $email = new email_template;
        $email->send('host_new_user', $this->account['id'], $this->service['id'], '', '');
        return true;
    }


    # edit service  (not used)
    function p_edit()
    {
        # send the admin alert email
        include_once(PATH_MODULES.'email_template/email_template.inc.php');
        $email = new email_template;
        $email->send('admin->host_edit_admin', $this->account['id'], $this->service['id'], '', '');

        # send the user the alert email
        include_once(PATH_MODULES.'email_template/email_template.inc.php');
        $email = new email_template;
        $email->send('host_edit_user', $this->account['id'], $this->service['id'], '', '');
        return true;
    }


    # deactivate service
    function p_inactive()
    {  
    	# send the admin deactivate notice
    	include_once(PATH_MODULES.'email_template/email_template.inc.php');
    	$email = new email_template;
    	$email->send('admin->host_inactive_admin', $this->account['id'], $this->service['id'], '', '');
        return true;
    }


    # deactivate service
    function p_active()
    {      
    	# send the admin deactivate notice
    	include_once(PATH_MODULES.'email_template/email_template.inc.php');
    	$email = new email_template;
    	$email->send('admin->host_active_admin', $this->account['id'], $this->service['id'], '', '');        
        return true;
    }


    # delete service
    function p_delete()
    {
		# recycle the IP if ip_based: 
        if ($this->plugin_data['ipinfo_namebased'] == '1') {			 
		    include_once(PATH_MODULES.'host_server/host_server.inc.php');
		    $host = new host_server;					
			$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);	
        } 
				
        # send the admin delete notice
        include_once(PATH_MODULES.'email_template/email_template.inc.php');
        $email = new email_template;
        $email->send('admin->host_delete_admin', $this->account['id'], $this->service['id'], '', '');
        return true;
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
                $result = $this->p_edit();
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
            $sql = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
                    queue        =  ' . $db->qstr( 'none' ) . ',
                    date_last    =  ' . $db->qstr( time() ) . '
                    WHERE
                    id           =  ' . $db->qstr( $this->service['id'] ) . ' AND
                    site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $upd = $db->Execute($sql);

        } else {
            # error log
            $C_debug->error($this->name.'php', $this->service['queue'], @$result);
        }
    }
}
?>
