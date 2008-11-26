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

    $plg = new plgn_prov_ENSIM_WINDOWS_3();
    $plg->p_all($VAR);
}


# Main Class
class plgn_prov_ENSIM_WINDOWS_3
{
    function plgn_prov_ENSIM_WINDOWS_3()
    {
        $this->name             = 'ENSIM_WINDOWS_3';
        $this->task_based       = true;
        $this->remote_based     = false;
        $this->fallback_manual  = true;
        $this->nl = ' & ';
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

        # start the command
        $cmd =  'adddomain -s <xml>';

        # ip based?
        if ($this->plugin_data['ipinfo_namebased'] == '0' && $this->ip != false)
        {
          $cmd .= '<type>IPBased<type></ip>'.$this->ip.'</ip>';
          $ssl = '1';
        }
        else
        {
            $cmd .= '<type>NameBased</type>';
            $ssl = '0';
        }

        # construct the remaining XML:
        while (list($key, $val) = each($this->plugin_data)) {
            if (empty($this->plugin_data[$key]))
                $this->plugin_data[$key] = '0';
        }

        $cmd .=
        '<domain>'              . $this->service['domain_name']. '.' . $this->service['domain_tld'] .     '</domain>'.
        '<user>'                . $this->login['username']          . '</user>'.
        '<password>'            . $this->login['password']          . '</password>'.
        '<email>'               . $this->account['email']           . '</email>'.
        '<sendEmail>0</sendEmail>'.
        '<setDns>'              . $this->server_cnfg['dns']         . '</setDns>'.
        '<diskquota>'           . $this->plugin_data['diskquota']   . '</diskquota>' .
        '<maxusers>'            . $this->plugin_data['maxusers']    . '</maxusers>' .
        '<services>'.
        '<winfiles>'            . $this->plugin_data['winfiles']    . '</winfiles>' .
        '<winanalog>'           . $this->plugin_data['winfiles']    . '</winfiles>' .
        '<odbc>'                . $this->plugin_data['odbc']        . '</odbc>' .
        '<coldfusion>'          . $this->plugin_data['coldfusion']  . '</coldfusion>' .
        '<frontpage>'           . $this->plugin_data['frontpage']   . '</frontpage>' .
        '<perl>'                . $this->plugin_data['perl']        . '</perl>' .
        '<php>'                 . $this->plugin_data['php']         . '</php>' .
        '<mysql>'               . $this->plugin_data['mysql']       . '</mysql>' .
        '<urchin>'              . $this->plugin_data['urchin']      . '</urchin>' .
        '<msftpsvc>'.
        '<accessread>1</accessread>' .
        '<accesswrite>1</accesswrite>' .
        '<connectiontimeout>900</connectiontimeout>' .
        '<allowanonymous>'          . $this->plugin_data['allowanonymous'] . '</allowanonymous>' .
        '<logtype>'                 . $this->plugin_data['logtype'] . '</logtype>' .
        '<maxconnections>'          . $this->plugin_data['ftp_maxconnections'] . '</maxconnections>' .
        '<maxconnectionsunlimited>' . $this->plugin_data['ftp_maxconnectionsunlimited'] . '</maxconnectionsunlimited>' .
        '<msdosdiroutput>1</msdosdiroutput>' .
        '<exitmessage>Goodbye</exitmessage>' .
        '<greetingmessage>Hello</greetingmessage>' .
        '<maxclientsmessage>To many users. Try again later</maxclientsmessage>' .
        '<msftpsvc>'                . $this->plugin_data['msftpsvc'] . '</msftpsvc>' .
        '</msftpsvc>'.
        '<w3svc>' .
        '<cgi>'                     . $this->plugin_data['cgi']         . '</cgi>' .
        '<ssi>'                     . $this->plugin_data['ssi']         . '</ssi>' .
        '<sslc>'                    . $ssl       . '</sslc>' .
        '<maxconnectionsunlimited>' . $this->plugin_data['w3svc_maxconnectionsunlimited'] . '</maxconnectionsunlimited>' .
        '<allowkeepalive>1</allowkeepalive>' .
        '<appisolated>2</appisolated>' .
        '<enablereversedns>1</enablereversedns>' .
        '<accessread>1</accessread>' .
        '<accesswrite>0</accesswrite>' .
        '<accesssslflags>1</accesssslflags>' .
        '<execflag>1</execflag>' .
        '<enabledirbrowsing>1</enabledirbrowsing>' .
        '<enabledefaultdoc>1</enabledefaultdoc>' .
        '<defaultdoc>Default.html</defaultdoc>' .
        '<logtype>'                 . $this->plugin_data['logtype']                 . '</logtype>' .
        '<enablebandwidthquota>'    . $this->plugin_data['enablebandwidthquota']    . '</enablebandwidthquota>' .
        '<enablecpuquota>'          . $this->plugin_data['cpuquota']                . '</enablecpuquota>' .
        '<defaultdocfooter>"footer"</defaultdocfooter>' .
        '<connectiontimeout>900</connectiontimeout>' .
        '<cpuquota>'                . $this->plugin_data['cpuquota']                . '</cpuquota>' .
        '<maxbandwidth>'            . $this->plugin_data['maxbandwidth']            . '</maxbandwidth>' .
        '<maxconnections>'          . $this->plugin_data['w3svc_maxconnections']    . '</maxconnections>' .
        '<serversize>'              . $this->plugin_data['serversize']              . '</serversize>' .
        '<enabledocfooter>0</enabledocfooter>' .
        '<footertype>0</footertype>' .
        '<footerstring>0</footerstring>' .
        '<footerfile>0</footerfile>' .
        '<w3svc>'                   . $this->plugin_data['w3svc'] . '</w3svc>' .
        '</w3svc>' .
        '<winmail>' .
        '<autoresponder>'           . $this->plugin_data['autoresponder']       . '</autoresponder>' .
        '<userforwards>'            . $this->plugin_data['userforwards']        . '</userforwards>' .
        '<cachrest>Postmaster</>'   .
        '<winmail>'                 . $this->plugin_data['winmail']             . '</winmail>' .
        '</winmail>' .
        '</services>' .
        '</xml>'. $this->nl;

        return $cmd;
    }

    # edit service
    function p_edit()
    {
        # defaults
        while (list($key, $val) = each($this->plugin_data)) {
            if (empty($this->plugin_data[$key]))
                $this->plugin_data[$key] = '0';
        }

        $cmd = 'editdomain -s '.
        '<xml>'.
        '<domain>'              . $this->service['domain_name']. '.' . $this->service['domain_tld'] .  '</domain>'.
        '<email>'               . $this->account['email']           . '</email>'.
        '<diskquota>'           . $this->plugin_data['diskquota']   . '</diskquota>' .
        '<maxusers>'            . $this->plugin_data['maxusers']    . '</maxusers>' .
        '<services>'.
        '<winfiles>'            . $this->plugin_data['winfiles']    . '</winfiles>' .
        '<winanalog>'           . $this->plugin_data['winfiles']    . '</winfiles>' .
        '<odbc>'                . $this->plugin_data['odbc']        . '</odbc>' .
        '<coldfusion>'          . $this->plugin_data['coldfusion']  . '</coldfusion>' .
        '<frontpage>'           . $this->plugin_data['frontpage']   . '</frontpage>' .
        '<perl>'                . $this->plugin_data['perl']        . '</perl>' .
        '<php>'                 . $this->plugin_data['php']         . '</php>' .
        '<mysql>'               . $this->plugin_data['mysql']       . '</mysql>' .
        '<urchin>'              . $this->plugin_data['urchin']      . '</urchin>' .
        '<msftpsvc>'.
        '<accessread>1</accessread>' .
        '<accesswrite>1</accesswrite>' .
        '<connectiontimeout>900</connectiontimeout>' .
        '<allowanonymous>'          . $this->plugin_data['allowanonymous'] . '</allowanonymous>' .
        '<logtype>'                 . $this->plugin_data['logtype'] . '</logtype>' .
        '<maxconnections>'          . $this->plugin_data['ftp_maxconnections'] . '</maxconnections>' .
        '<maxconnectionsunlimited>' . $this->plugin_data['ftp_maxconnectionsunlimited'] . '</maxconnectionsunlimited>' .
        '<msdosdiroutput>1</msdosdiroutput>' .
        '<exitmessage>Goodbye</exitmessage>' .
        '<greetingmessage>Hello</greetingmessage>' .
        '<maxclientsmessage>To many users. Try again later</maxclientsmessage>' .
        '<msftpsvc>'                . $this->plugin_data['msftpsvc'] . '</msftpsvc>' .
        '</msftpsvc>'.
        '<w3svc>' .
        '<cgi>'                     . $this->plugin_data['cgi']         . '</cgi>' .
        '<ssi>'                     . $this->plugin_data['ssi']         . '</ssi>' .
        '<maxconnectionsunlimited>' . $this->plugin_data['w3svc_maxconnectionsunlimited'] . '</maxconnectionsunlimited>' .
        '<allowkeepalive>1</allowkeepalive>' .
        '<appisolated>2</appisolated>' .
        '<enablereversedns>1</enablereversedns>' .
        '<accessread>1</accessread>' .
        '<accesswrite>0</accesswrite>' .
        '<accesssslflags>1</accesssslflags>' .
        '<execflag>1</execflag>' .
        '<enabledirbrowsing>1</enabledirbrowsing>' .
        '<enabledefaultdoc>1</enabledefaultdoc>' .
        '<defaultdoc>Default.html</defaultdoc>' .
        '<logtype>'                 . $this->plugin_data['logtype']                 . '</logtype>' .
        '<enablebandwidthquota>'    . $this->plugin_data['enablebandwidthquota']    . '</enablebandwidthquota>' .
        '<enablecpuquota>'          . $this->plugin_data['cpuquota']                . '</enablecpuquota>' .
        '<defaultdocfooter>"footer"</defaultdocfooter>' .
        '<connectiontimeout>900</connectiontimeout>' .
        '<cpuquota>'                . $this->plugin_data['cpuquota']                . '</cpuquota>' .
        '<maxbandwidth>'            . $this->plugin_data['maxbandwidth']            . '</maxbandwidth>' .
        '<maxconnections>'          . $this->plugin_data['w3svc_maxconnections']    . '</maxconnections>' .
        '<serversize>'              . $this->plugin_data['serversize']              . '</serversize>' .
        '<enabledocfooter>0</enabledocfooter>' .
        '<footertype>0</footertype>' .
        '<footerstring>0</footerstring>' .
        '<footerfile>0</footerfile>' .
        '<w3svc>'                   . $this->plugin_data['w3svc'] . '</w3svc>' .
        '</w3svc>' .
        '<winmail>' .
        '<autoresponder>'           . $this->plugin_data['autoresponder']       . '</autoresponder>' .
        '<userforwards>'            . $this->plugin_data['userforwards']        . '</userforwards>' .
        '<cachrest>Postmaster</>'   .
        '<winmail>'                 . $this->plugin_data['winmail']             . '</winmail>' .
        '</winmail>' .
        '</services>' .
        '</xml>'. $this->nl;

        return $cmd;
    }

    # activate service
    function p_inactive()
    {
        $cmd = '@editdomain -s "'.
        '<xml>'.
        '<domain>'              . $this->service['domain_name']. '.' . $this->service['domain_tld'] .  '</domain>'.
        '<email>'               . $this->account['email']           . '</email>'.
        '<diskquota>'           . $this->plugin_data['diskquota']   . '</diskquota>' .
        '<maxusers>'            . $this->plugin_data['maxusers']    . '</maxusers>' .
            '<services>'.
                '<msftpsvc>'.
                    '<msftpsvc>0</msftpsvc>' .
                '</msftpsvc>'.
                '<w3svc>' .
                    '<w3svc>0</w3svc>' .
                '</w3svc>' .
                '<winmail>' .
                    '<winmail>0</winmail>' .
                '</winmail>' .
            '</services>' .
        '</xml>"'. $this->nl;
        return $cmd;
    }

    # deactivate service
    function p_active()
    {
        $cmd = 'editdomain -s '.
        '<xml>'.
        '<domain>'              . $this->service['domain_name']. '.' . $this->service['domain_tld'] .  '</domain>'.
        '<email>'               . $this->account['email']           . '</email>'.
        '<diskquota>'           . $this->plugin_data['diskquota']   . '</diskquota>' .
        '<maxusers>'            . $this->plugin_data['maxusers']    . '</maxusers>' .
        '<services>'.
            '<msftpsvc>'.
                '<msftpsvc>'    . $this->plugin_data['msftpsvc']    . '</msftpsvc>' .
            '</msftpsvc>'.
            '<w3svc>' .
                '<w3svc>'       . $this->plugin_data['w3svc']       . '</w3svc>' .
            '</w3svc>' .
            '<winmail>' .
                '<winmail>'     . $this->plugin_data['winmail']     . '</winmail>' .
            '</winmail>' .
        '</services>' .
        '</xml>'. $this->nl;
        return $cmd;
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
				
        $cmd =
        'deldomain -s <xml>'.
        '<domain>'.$this->service['domain_name']. "." . $this->service['domain_tld'].'</domain>'.
        '</xml>'. $this->nl;
        return $cmd;
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