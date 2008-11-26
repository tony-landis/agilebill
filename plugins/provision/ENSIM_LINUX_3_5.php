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

    $plg = new plgn_prov_ENSIM_LINUX_3_5();
    $plg->p_all($VAR);
}


# Main Class
class plgn_prov_ENSIM_LINUX_3_5
{
    function plgn_prov_ENSIM_LINUX_3_5()
    {
        $this->name             = 'ENSIM_LINUX_3_5';
        $this->task_based       = true;
        $this->remote_based     = false;
        $this->nl = '; ';
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
        if ($this->plugin_data['ipinfo_namebased'] == '0')
            $this->ip = $host->useipaddress($this->service, $this->server);
        else
            $this->ip = false ;


        # force on/off for specific vars
        $tf_array = Array(  'mailscanner', 'develenv', 'aspmgr', 'scriptsmgr', 'logrotate', 'analog', 'files',
                            'vhbackup', 'bind', 'imap', 'tomcat4', 'openssl', 'ssi', 'weblogs',
                            'anonftp', 'majordomo', 'vacation', 'spam_filter', 'sqmail', 'mivamerchant', 'frontpage',
                            'webalizer', 'proftpd', 'sendmail', 'cgi', 'perl', 'subdomain', 'ssh',
                            'telnet','mysql'
                            );

        while(list($key,$val) = each($this->plugin_data)) {
            for($i=0;$i<count($tf_array); $i++) {
                if ($tf_array[$i] == $key) {
                    if($val == 1)
                    $this->plugin_data[$key] = 'on';
                    else
                    $this->plugin_data[$key] = 'off';
                    $i = 100;
                }
            }
        }

        # tomcat
        if($this->plugin_data['tomcat4']  == 'on')
        $this->plugin_data['apache_jail'] = 0;


        # start the command
        $cmd =  "AddVirtDomain "        .
        " -c siteinfo".
        ",domain="          . $this->service['domain_name'] . "." . $this->service['domain_tld'] .
        ",admin_user=\""    . $this->login['username']      . "\"".
        ",passwd="          . $this->login['password']      .
        ",tpasswd="         . $this->login['password']      .
        ",email="           . $this->account['email'];

        # ip based?
        if ($this->plugin_data['ipinfo_namebased'] == '0' && $this->ip == false)
        {
        	$cmd .= " -c ipinfo,namebased=0,ipaddrs=\[" . $this->ip . "\]";
        	$cmd .= " -c openssl," . $this->plugin_data['openssl'];
        	$cmd .= " -c ssh,"     . $this->plugin_data['ssh'];
        	$cmd .= ",jail="       . $this->plugin_data['ssh_jail'];			
        } else {
        	$cmd .= " -c ipinfo,namebased=1";
        }
		 
        $cmd .=
        " -c bandwidth"     .
        ",threshold="       . $this->plugin_data['bandwidth_threshold'] .
        ",rollover="        . $this->plugin_data['bandwidth_rollover']  .
        " -c mailscanner,"  . $this->plugin_data['mailscanner']    .
        ",scan_incoming="   . $this->plugin_data['mailscanner_in']    .
        ",scan_outgoing="   . $this->plugin_data['mailscanner_out']    .
        " -c develenv,"     . $this->plugin_data['develenv']    .
        " -c aspmgr,"       . $this->plugin_data['aspmgr']    .
        " -c scriptsmgr,"   . $this->plugin_data['scriptsmgr']    .
        " -c logrotate,"    . $this->plugin_data['logrotate']    .
        " -c analog,"       . $this->plugin_data['analog'] .
        " -c files,"        . $this->plugin_data['files']  .
        " -c vhbackup,"     . $this->plugin_data['vhbackup']    .
        " -c bind,"         . $this->plugin_data['bind']    .
        " -c imap,"         . $this->plugin_data['imap']    .
        " -c tomcat4,"      . $this->plugin_data['tomcat4']    .
        " -c ssi,"          . $this->plugin_data['ssi']    .
        " -c weblogs,"      . $this->plugin_data['weblogs']    .
        " -c anonftp,"      . $this->plugin_data['anonftp']    .
        " -c majordomo,"    . $this->plugin_data['majordomo']    .
        " -c vacation,"     . $this->plugin_data['vacation']    .
        " -c spam_filter,"  . $this->plugin_data['spam_filter']    .
        " -c sqmail,"       . $this->plugin_data['sqmail']    .
        " -c mivamerchant," . $this->plugin_data['mivamerchant']    .
        " -c frontpage,"    . $this->plugin_data['frontpage']    .
        " -c webalizer,"    . $this->plugin_data['webalizer']    .
        " -c proftpd,"      . $this->plugin_data['proftpd']    .
        " -c sendmail,"     . $this->plugin_data['sendmail']    .
        " -c cgi,"          . $this->plugin_data['cgi']    .
        " -c mod_perl,"     . $this->plugin_data['perl']    .
        " -c subdomain,"    . $this->plugin_data['subdomain']    .
        ",max="             . $this->plugin_data['max_subdomain']    .
        " -c users,maxusers=". $this->plugin_data['maxusers']    .
        " -c ssh,"          . $this->plugin_data['ssh']    .
        ",jail="            . $this->plugin_data['ssh_jail']    .
        " -c telnet,"       . $this->plugin_data['telnet']    .
        ",jail="            . $this->plugin_data['telnet_jail']    .
        " -c apache,on"       .
        ",jail="            . $this->plugin_data['apache_jail']    .
        " -c diskquota,units=MB" .
        ",quota="           . $this->plugin_data['diskquota']    .
        " -c mysql,"        . $this->plugin_data['mysql']    .
        ",dbaseadmin="      . $this->login['username']    .
        ",dbasenum="        . $this->plugin_data['dbasenum'];
        $cmd .= $this->nl;
        return $cmd;
    }

    # edit service
    function p_edit()
    {
        # get ip address
        if(!empty($this->service['host_ip']))
            $this->ip = $this->service['host_ip'];
        else
            $this->ip = $this->server['name_based_ip'];


        # force on/off for specific vars
        $tf_array = Array(  'mailscanner', 'develenv', 'aspmgr', 'scriptsmgr', 'logrotate', 'analog', 'files',
                            'vhbackup', 'bind', 'imap', 'tomcat4', 'openssl', 'ssi', 'weblogs',
                            'anonftp', 'majordomo', 'vacation', 'spam_filter', 'sqmail', 'mivamerchant', 'frontpage',
                            'webalizer', 'proftpd', 'sendmail', 'cgi', 'perl', 'subdomain', 'ssh',
                            'telnet','mysql'
                            );

        while(list($key,$val) = each($this->plugin_data)) {
            for($i=0;$i<count($tf_array); $i++) {
                if ($tf_array[$i] == $key) {
                    if($val == 1)
                    $this->plugin_data[$key] = 'on';
                    else
                    $this->plugin_data[$key] = 'off';
                    $i = 100;
                }
            }
        }

        # tomcat
        if($this->plugin_data['tomcat4']  == 'on')
        $this->plugin_data['apache_jail'] = 0;

        # start the command
        $cmd =  "EditVirtDomain -D " . $this->service['domain_name']. "." . $this->service['domain_tld'] .
        " -c siteinfo".
        ",domain="          . $this->service['domain_name']. "." . $this->service['domain_tld'] .
        ",admin_user=\""    . $this->service['host_username']      . "\"".
        ",passwd="          . $this->service['host_password']      .
        ",tpasswd="         . $this->service['host_password']      .
        ",email="           . $this->account['email'];

        # ip based?
        if ($this->plugin_data['ipinfo_namebased'] == '0' && $this->ip == false)
        {
       		$cmd .= " -c ipinfo,namebased=0,ipaddrs=\[" . $this->ip . "\]";
        	$cmd .= " -c openssl," . $this->plugin_data['openssl'];
        	$cmd .= " -c ssh,"     . $this->plugin_data['ssh'];
        	$cmd .= ",jail="       . $this->plugin_data['ssh_jail'];
        } else {
        	$cmd .= " -c ipinfo,namebased=1";		
        }

        $cmd .=
        " -c bandwidth"     .
        ",threshold="       . $this->plugin_data['bandwidth_threshold'] .
        ",rollover="        . $this->plugin_data['bandwidth_rollover']  .
        " -c mailscanner,"  . $this->plugin_data['mailscanner']    .
        ",scan_incoming="   . $this->plugin_data['mailscanner_in']    .
        ",scan_outgoing="   . $this->plugin_data['mailscanner_out']    .
        " -c develenv,"     . $this->plugin_data['develenv']    .
        " -c aspmgr,"       . $this->plugin_data['aspmgr']    .
        " -c scriptsmgr,"   . $this->plugin_data['scriptsmgr']    .
        " -c logrotate,"    . $this->plugin_data['logrotate']    .
        " -c analog,"       . $this->plugin_data['analog'] .
        " -c files,"        . $this->plugin_data['files']  .
        " -c vhbackup,"     . $this->plugin_data['vhbackup']    .
        " -c bind,"         . $this->plugin_data['bind']    .
        " -c imap,"         . $this->plugin_data['imap']    .
        " -c tomcat4,"      . $this->plugin_data['tomcat4']    .
        " -c ssi,"          . $this->plugin_data['ssi']    .
        " -c weblogs,"      . $this->plugin_data['weblogs']    .
        " -c anonftp,"      . $this->plugin_data['anonftp']    .
        " -c majordomo,"    . $this->plugin_data['majordomo']    .
        " -c vacation,"     . $this->plugin_data['vacation']    .
        " -c spam_filter,"  . $this->plugin_data['spam_filter']    .
        " -c sqmail,"       . $this->plugin_data['sqmail']    .
        " -c mivamerchant," . $this->plugin_data['mivamerchant']    .
        " -c frontpage,"    . $this->plugin_data['frontpage']    .
        " -c webalizer,"    . $this->plugin_data['webalizer']    .
        " -c proftpd,"      . $this->plugin_data['proftpd']    .
        " -c sendmail,"     . $this->plugin_data['sendmail']    .
        " -c cgi,"          . $this->plugin_data['cgi']    .
        " -c mod_perl,"     . $this->plugin_data['perl']    .
        " -c subdomain,"    . $this->plugin_data['subdomain']    .
        ",max="             . $this->plugin_data['max_subdomain']    .
        " -c users,maxusers=". $this->plugin_data['maxusers']    .
        " -c telnet,"       . $this->plugin_data['telnet']    .
        ",jail="            . $this->plugin_data['telnet_jail']    .
        " -c apache,on"       .
        ",jail="            . $this->plugin_data['apache_jail']    .
        " -c diskquota,units=MB" .
        ",quota="           . $this->plugin_data['diskquota']    .
        " -c mysql,"        . $this->plugin_data['mysql']    .
        ",dbasenum="        . $this->plugin_data['dbasenum'];
        return $cmd;
    }

    # activate service
    function p_inactive()
    {
        return "DisableVirtDomain " . strtoupper($this->service['domain_name']. "." . $this->service['domain_tld']);
    }

    # deactivate service
    function p_active()
    {
        return "EnableVirtDomain " . strtoupper($this->service['domain_name']. "." . $this->service['domain_tld']);
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
			
        return "DeleteVirtDomain " . strtoupper($this->service['domain_name']. "." . $this->service['domain_tld']);
    }

    # construct echo all updates
    function p_all($VAR)
    {
        global $C_debug;

        # Error checking
        if( empty($VAR['key']) ) {
            echo 'REM Server Key Missing!';
            exit;
        }

        # Get the server details
        $db     = &DB();
        $sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
                   keycode      =  ' . $db->qstr( $VAR['key'] ) . ' AND
                   site_id      =  ' . $db->qstr(DEFAULT_SITE);
        $rs = $db->Execute($sql);
        if (@$rs->RecordCount() <= 0) {
            echo 'REM Server ID does not exist';
            exit;
        } else {
            $this->server = $rs->fields;
            @$this->server_cnfg = unserialize($rs->fields['provision_plugin_data']);
        }

        # Check that this server is using this plugin
        if($this->server['provision_plugin'] != @$this->name)
        {
            echo 'REM Wrong plugin for this server ';
            exit;
        }

        # Check the auth for this post
        if (!empty($VAR['key']) && $VAR['key'] == $this->server['keycode'])
        {
            # authorized!
        } else {
            echo 'REM Unauthorized Key! ';
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
            echo 'REM No Records To Add/Update ';
            exit;
        }
        $i=0;
        while(!$rs->EOF)
        {

            # set details
            $this->service = $rs->fields;

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
                    # send the user the details
                    include_once(PATH_MODULES.'email_template/email_template.inc.php');
                    $email = new email_template;
                    $email->send('host_edit_user', $this->account['id'], $rs->fields['id'], '', '');
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
	                $sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service SET
	                           queue        =  ' . $db->qstr( 'none' ) . ',
	                           date_last    =  ' . $db->qstr( time() ) . '
	                           WHERE
	                           id           =  ' . $db->qstr( $rs->fields['id'] ) . ' AND
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