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

    $plg = new plgn_prov_PLESK_LINUX_6();
    $plg->p_all($VAR);
}


# Main Class
class plgn_prov_PLESK_LINUX_6
{
    function plgn_prov_PLESK_LINUX_6()
    {
        $this->name             = 'PLESK_LINUX_6';
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
        if ($this->plugin_data['hst_type'] == '0') {
            $this->ip = $host->useipaddress($this->service, $this->server);
        } else {
            $this->ip = $this->server['name_based_ip'] ;
            $this->plugin_data['fp_ssl'] = 'false';
            $this->plugin_data['ssl']     = 'false';
        }

        # force true/false for specific vars
        $tf_array = Array(  'fp','fp_ssl','fpauth','ssi','php','cgi','perl','asp','python',
                            'coldfusion','ssl','webstat','err_docs','log_rotate',
                            'log_compress','mail_service','www','wuscripts','webmail');

        while(list($key,$val) = each($this->plugin_data)) {
            for($i=0;$i<count($tf_array); $i++) {
                if ($tf_array[$i] == $key) {
                    if($val == 1)
                    $this->plugin_data[$key] = 'true';
                    else
                    $this->plugin_data[$key] = 'false';
                    $i = 100;
                }
            }
        }
                    
        #  Create the account
        $cmd =  "client.pl" .
            " -c " .                $this->account['username'] .
            " -name \"".            $this->account['first_name'] . " " .$this->account['middle_name'] . " " .$this->account['last_name'] . "\"" .
            " -company \"".            $this->account['company'] . "\"" .
            " -passwd \"".          $this->login['password'] . "\"" .
            " -email \"".           $this->account['email'] . "\"" .
        $this->nl;

        # Set the account options
        $cmd .= "client_pref.pl -u ".$this->account['username'].
                " -create_domains \"false\"" .
                " -manage_phosting \"true\"" .
                " -change_limits \"false\"" .
                " -manage_dns \"false\"" .
                " -manage_log \"true\"" .
                " -manage_crontab \"true\"" .
                " -manage_anonftp \"true\"" .
                " -manage_webapps \"true\"" .
                " -manage_maillists \"true\"" .
                " -max_dom \"-1\"" .
                " -disk_space \"-1\"" .
                " -max_traffic \"-1\"" .
                " -max_box \"-1\"" .
                " -mbox_quota \"-1\"" .
                " -max_redir \"-1\"" .
                " -max_mg \"-1\"" .
                " -max_resp \"-1\"" .
                " -max_wu \"-1\"" .
                " -max_db \"-1\"" .
                " -max_maillists \"-1\"" .
                " -max_webapps \"-1\"" .
                " -ip_pool \"add:".$this->ip."\"" .
                $this->nl;



        # create the domain
        $cmd .= "domain.pl -c " .
            $this->service['domain_name']. "." . $this->service['domain_tld'] .
            " -clogin \"".          $this->account['username'] ."\"" .
            " -du_passwd \"".       $this->login['password'] ."\"" .
            " -login \"".           $this->login['username'] ."\"" .
            " -passwd \"".          $this->login['password'] ."\"" .
            " -passwd_type \"plain\"" .
            " -notify \"false\"" .
            " -status \"true\"" .
            " -dom_user \"true\"" .
            " -dns \"true\"" .
            " -www \"true\"" .
            " -hosting \"true\"" .
            " -hst_type \"phys\"" .
            " -ip \"".              $this->ip                           ."\"" .
            " -hard_quota \"".      $this->plugin_data['hard_quota']    ."\"" .
            " -fp \"".              $this->plugin_data['fp']            ."\"" .
            " -fp_ssl \"".          $this->plugin_data['fp_ssl']        ."\"" .
            " -fpauth \"".          $this->plugin_data['fpauth']        ."\"" .
            " -fplogin \"".         $this->login['username']            ."\"" .
            " -fppasswd \"".        $this->login['password']            ."\"" .
            " -ssi \"".             $this->plugin_data['ssi']           ."\"" .
            " -php \"".             $this->plugin_data['php']           ."\"" .
            " -cgi \"".             $this->plugin_data['cgi']           ."\"" .
            " -perl \"".            $this->plugin_data['perl']          ."\"" .
            " -asp \"".             $this->plugin_data['asp']           ."\"" .
            " -python \"".          $this->plugin_data['python']        ."\"" .
            " -ssl \"".             $this->plugin_data['ssl']           ."\"" .
            " -webstat \"".         $this->plugin_data['webstat']       ."\"" .
            " -err_docs \"".        $this->plugin_data['err_docs']      ."\"" .
            " -log_rotate \"".      $this->plugin_data['log_rotate']    ."\"" .
            " -log_bysize \"".      $this->plugin_data['log_bysize']    ."\"" .
            " -log_bytime \"".      $this->plugin_data['log_bytime']    ."\"" .
            " -log_max_num \"".     $this->plugin_data['log_max_num']   ."\"" .
            " -log_compress \"".    $this->plugin_data['log_compress']  ."\"" .
            $this->nl;


        # set the domain prefs
        $cmd .= "domain_pref.pl -c " .
            $this->service['domain_name']. "." . $this->service['domain_tld'] .
            " -www \"true\"" .
            " -disk_space \"".      $this->plugin_data['disk_space']    ."\"" .
            " -max_traffic \"".     $this->plugin_data['max_traffic']   ."\"" .
            " -max_box \"".         $this->plugin_data['max_box']       ."\"" .
            " -mbox_quota \"".      $this->plugin_data['mbox_quota']    ."\"" .
            " -max_redir \"".       $this->plugin_data['max_redir']     ."\"" .
            " -max_mg \"".          $this->plugin_data['max_mg']        ."\"" .
            " -max_resp \"".        $this->plugin_data['max_resp']      ."\"" .
            " -max_wu \"".          $this->plugin_data['max_wu']        ."\"" .
            " -max_db \"".          $this->plugin_data['max_db']        ."\"" .
            " -max_maillists \"".   $this->plugin_data['max_maillists'] ."\"" .
            " -max_webapps \"".     $this->plugin_data['max_webapps']   ."\"" .
            " -wuscripts \"".       $this->plugin_data['wuscripts']     ."\"" .
            " -webmail \"".         $this->plugin_data['webmail']       ."\"" .
            " -keep_traf_stat \"".  $this->plugin_data['keep_traf_stat'] . "\"";

        return $cmd;
    }

    # edit service
    function p_edit()
    {
        $cmd ='';
        # force true/false for specific vars
        $tf_array = Array(  'fp','fp_ssl','fpauth','ssi','php','cgi','perl','asp','python',
                            'coldfusion','ssl','webstat','err_docs','log_rotate',
                            'log_compress','mail_service','www','wuscripts','webmail');

        while(list($key,$val) = each($this->plugin_data)) {
            for($i=0;$i<count($tf_array); $i++) {
                if ($tf_array[$i] == $key) {
                    if($val == 1)
                    $this->plugin_data[$key] = 'true';
                    else
                    $this->plugin_data[$key] = 'false';
                    $i = 100;
                }
            }
        }

        # Set the account options
        if(!empty($this->service['host_ip'])) {
            $cmd = "client_pref.pl -u ".$this->service['host_username'].
                    " -ip_pool \"add:".$this->service['host_ip']."\"" .
                    $this->nl;
        }

        # force options
        if ($this->plugin_data['hst_type'] == '1')
        {
            $this->plugin_data['fp_ssl'] = 'false';
            $this->plugin_data['ssl'] = 'false';
        }

        # edit the domain
        $cmd = "domain.pl -u " .
            $this->service['domain_name']. "." . $this->service['domain_tld'] .
            " -status \"true\"" .
            " -dns \"true\"" .
            " -www \"true\"" .
            " -hosting \"true\"" .
            " -hst_type \"phys\"" .
            " -login \"".           $this->service['host_username']     ."\"" .
            " -passwd \"".          $this->service['host_password']     ."\"" .
            " -shell \"".           $this->plugin_data['shell']         ."\"" .
            " -hard_quota \"".      $this->plugin_data['hard_quota']    ."\"" .
            " -fp \"".              $this->plugin_data['fp']            ."\"" .
            " -fp_ssl \"".          $this->plugin_data['fp_ssl']        ."\"" .
            " -fpauth \"".          $this->plugin_data['fpauth']        ."\"" .
            " -fplogin \"".         $this->service['host_username']     ."\"" .
            " -fppasswd \"".        $this->service['host_password']     ."\"" .
            " -ssi \"".             $this->plugin_data['ssi']           ."\"" .
            " -php \"".             $this->plugin_data['php']           ."\"" .
            " -cgi \"".             $this->plugin_data['cgi']           ."\"" .
            " -perl \"".            $this->plugin_data['perl']          ."\"" .
            " -asp \"".             $this->plugin_data['asp']           ."\"" .
            " -python \"".          $this->plugin_data['python']        ."\"" .
            " -ssl \"".             $this->plugin_data['ssl']           ."\"" .
            " -webstat \"".         $this->plugin_data['webstat']       ."\"" .
            " -err_docs \"".        $this->plugin_data['err_docs']      ."\"" .
            " -log_rotate \"".      $this->plugin_data['log_rotate']    ."\"" .
            " -log_bysize \"".      $this->plugin_data['log_bysize']    ."\"" .
            " -log_bytime \"".      $this->plugin_data['log_bytime']    ."\"" .
            " -log_max_num \"".     $this->plugin_data['log_max_num']   ."\"" .
            " -log_compress \"".    $this->plugin_data['log_compress']  ."\"";
            if(!empty($this->service['host_ip']))
            $cmd .=  " -ip \"".              $this->service['host_ip']           ."\"";
            else
            $cmd .=  " -ip \"".              $this->server['name_based_ip']           ."\"";
            $cmd .= $this->nl;

        # set the domain prefs
        $cmd .= "domain_pref.pl -u " .
            $this->service['domain_name']. "." . $this->service['domain_tld'] .
            " -www \"true\"" .
            " -disk_space \"".      $this->plugin_data['disk_space']    ."\"" .
            " -max_traffic \"".     $this->plugin_data['max_traffic']   ."\"" .
            " -max_box \"".         $this->plugin_data['max_box']       ."\"" .
            " -mbox_quota \"".      $this->plugin_data['mbox_quota']    ."\"" .
            " -max_redir \"".       $this->plugin_data['max_redir']     ."\"" .
            " -max_mg \"".          $this->plugin_data['max_mg']        ."\"" .
            " -max_resp \"".        $this->plugin_data['max_resp']      ."\"" .
            " -max_wu \"".          $this->plugin_data['max_wu']        ."\"" .
            " -max_db \"".          $this->plugin_data['max_db']        ."\"" .
            " -max_maillists \"".   $this->plugin_data['max_maillists'] ."\"" .
            " -max_webapps \"".     $this->plugin_data['max_webapps']   ."\"" .
            " -wuscripts \"".       $this->plugin_data['wuscripts']     ."\"" .
            " -webmail \"".         $this->plugin_data['webmail']       ."\"" .
            " -keep_traf_stat \"".  $this->plugin_data['keep_traf_stat']."\"";

        return $cmd;
    }

    # activate service
    function p_inactive()
    {
        $cmd = "domain.pl -u -status \"false\" " .
        $this->service['domain_name']. "." . $this->service['domain_tld'];
        return $cmd;
    }

    # deactivate service
    function p_active()
    {
        $cmd = "domain.pl -u -status \"true\" " .
        $this->service['domain_name']. "." . $this->service['domain_tld'];
        return $cmd;
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
				
        $cmd = "domain.pl -u -status \"false\" " .
        $this->service['domain_name']. "." . $this->service['domain_tld'];
        return $cmd;
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