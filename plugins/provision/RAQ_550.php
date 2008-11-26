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
	- quota
	- service
*/

# Remote update retrieval
if(empty($VAR))
{
    include_once('../../config.inc.php');
    require_once(PATH_ADODB  . 'adodb.inc.php');
    require_once(PATH_CORE   . 'database.inc.php');
    require_once(PATH_CORE   . 'setup.inc.php');
    require_once(PATH_CORE   . 'vars.inc.php');
    require_once(PATH_CORE   . 'xml.inc.php');

    $C_debug    = new CORE_debugger;
    $C_vars     = new CORE_vars;
    $VAR        = $C_vars->f;
    $C_db       = &DB();
    $C_setup    = new CORE_setup;

    $plg = new plgn_prov_RAQ_550();
    $plg->p_all($VAR);
}


# Main Class
class plgn_prov_RAQ_550
{
    function plgn_prov_RAQ_550()
    {
        $this->name             = 'RAQ_550';
        $this->task_based       = true;
        $this->remote_based     = false;
        $this->fallback_manual  = true;
        $this->nl 				= '; '; 
        $this->options 			= Array('enable-shell',
        								'enable-apop',
        								'enable-cgi',
        								'enable-php',
        								'enable-ssi',
        								'enable-ssl',		// remove
        								'enable-java',
        								'enable-ftp',
        								'ftp-maxconn',         								
        								'ftp-quota',
        								'maxusers',    
        								'quota'    								
        								);
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

        # Add the site
        $cmd =  "./addvsite -n www "    .
        	" -d "						. strtolower($this->service['domain_name']. "." . $this->service['domain_tld']) ;

	    
        # get ip address
        if ($this->plugin_data['ipinfo_namebased'] == '1') {
            $this->ip = $host->useipaddress($this->service, $this->server);
            $cmd .= " --i " . $this->ip;
            if(!empty($this->options['enable-ssl']))
            	$cmd .= " --enable-ssl";
        } else {
            $this->ip = $this->server['name_based_ip'];
            $cmd .= " --i " . $this->ip;
        }
        	        
        ### Get site options
        foreach( $this->options as $s => $v )  {
        	if(empty($this->plugin_data["$s"])) {
        		if($v != 0 && $v != 1)  
        		$cmd .= " --{$s} {$v}";  
        		else 
        		$cmd .= " --{$s}"; 
        	}
        }
        
        ### Add the user
        $cmd .= $this->nl;
        $cmd .=  "./adduser -n www."    . strtolower($this->service['domain_name']. "." . $this->service['domain_tld']) .
	        " -f \""          			. $this->account['first_name']." ".$this->account['last_name']. "\"".
	        " -u "						. $this->login['username'] .  
	        " -p \""          			. $this->login['password'] . "\"" .
	        " -q "						. $this->options['quota'] .
	        " -e "						. strtolower($this->account['first_name']).','.strtolower($this->account['first_name'].'.'.$this->account['last_name']);
	                        
        return $cmd; 
    }

    # edit service (MANUAL)
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

    # activate service (MANUAL)
    function p_inactive()
    {
    	# send the admin deactivate notice
    	include_once(PATH_MODULES.'email_template/email_template.inc.php');
    	$email = new email_template;
    	$email->send('admin->host_inactive_admin', $this->account['id'], $this->service['id'], '', '');
        return true; 
    }

    # deactivate service (MANUAL)
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
        if ($this->plugin_data['ipinfo_namebased'] == '0') {			 
		    include_once(PATH_MODULES.'host_server/host_server.inc.php');
		    $host = new host_server;					
			$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);	
        }   
        return "./cdelvsite --quick -n " . strtolower($this->service['domain_name']. "." . $this->service['domain_tld']); 
    }


    # construct echo all updates
    function p_all($VAR)
    {
        global $C_debug;

        # Error checking
        if( empty($VAR['key']) ) {
            echo 'ECHO Server Key Missing!';
            exit;
        }

        # Get the server details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
                   keycode      =  ' . $db->qstr( $VAR['key'] ) . ' AND
                   site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if (@$rs->RecordCount() <= 0) {
            echo 'ECHO Server ID does not exist';
            exit;
        } else {
            $this->server = $rs->fields;
            @$this->server_cnfg = unserialize($rs->fields['provision_plugin_data']);
        }

        # Check that this server is using this plugin
        if($this->server['provision_plugin'] != @$this->name)
        {
            echo 'ECHO Wrong plugin for this server ';
            exit;
        }

        # Check the auth for this post
        if (!empty($VAR['key']) && $VAR['key'] == $this->server['keycode'])
        {
            # authorized!
        } else {
            echo 'ECHO Unauthorized Key! ';
            exit;
        }

        # Get the service details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
                   type             =  ' . $db->qstr( 'host' ) . ' OR
                   type             =  ' . $db->qstr( 'host_group' ) . ' AND
                   host_server_id   =  ' . $db->qstr( $this->server['id'] ) . ' AND
                   queue           !=  ' . $db->qstr( 'none' ) . ' AND
                   site_id          =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if($rs->RecordCount() == 0) {
            echo 'ECHO No Records To Add/Update ';
            exit;
        }
        $i=0;
        while(!$rs->EOF)
        {

            # set details
            $this->service = $rs->fields;
            $this->service_id = $rs->fields['id'];

            # Get the hosting plan plugin data for this product
            $this->plugin_data = unserialize($this->service['host_provision_plugin_data']);

            # Get the account details
            $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account WHERE
                       id           =  ' . $db->qstr( $this->service['account_id'] ) . ' AND
                       site_id      =  ' . $db->qstr(DEFAULT_SITE);
            $acct = $db->Execute($sql);
            $this->account = $acct->fields;

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
                    $result = $this->p_edit();
               break;

               # delete
               case 'delete':
                    $result = $this->p_delete();
               break;
            }

            # formatting
            if (@$result != false)
            {
                if($i  > 0)
                echo $this->nl;
                echo $result;
                $i++;
            }

            # check if we need to update the status after an edit
            if ($this->service['queue'] == 'edit') {
                if ($this->service['active'] == 1  )
                    echo $this->nl . $this->p_active();
                else
                    echo $this->nl . $this->p_inactive();
            }

            # update service record
            if(@$result != false)
            {
				if($this->service['queue'] == "delete")
				{	
					# delete
	                $sql    = 'DELETE FROM ' . AGILE_DB_PREFIX . 'service WHERE
	                           id           =  ' . $db->qstr( $rs->fields['id'] ) . ' AND
	                           site_id      =  ' . $db->qstr(DEFAULT_SITE);
	                $upd = $db->Execute($sql);				
				} else { 
		            # update
					$db     	= &DB();
		            $sql   = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
		                        queue        =  ' . $db->qstr( 'none' ) . ',
		                        date_last    =  ' . $db->qstr( time() ) . '
		                        WHERE
		                        id           =  ' . $db->qstr( $this->service_id ) . ' AND
		                        site_id      =  ' . $db->qstr(DEFAULT_SITE);
		            $upd = $db->Execute($sql);
				} 
            } else {
                # error log
                $C_debug->error($this->name.'php', $this->service['queue'], @$result);
            }
            $rs->MoveNext();
        }
    }
}
?>