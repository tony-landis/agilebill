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
class plgn_prov_WHM
{
    function plgn_prov_WHM()
    {
        $this->name             = 'WHM';
        $this->task_based       = false;
        $this->remote_based     = true;
    }

    # add new service
    function p_new()
    {
        # get the common server class and set login details
        include_once(PATH_MODULES.'host_server/host_server.inc.php');
        $host = new host_server;
        if(empty($this->service['host_username'] )) 
        {
        	# set the limits
			$pass_len = 8;
			$user_len = 8;
			        	
        	# Generate a new username/login:
			$domain = $this->service['domain_name'].$this->service['domain_tld'];
 			
			# set the username
			$username = trim($domain);
			$username = preg_replace("/[-_\.]/", "", $username);
			if(strlen($username) < $user_len)
			{
				$rand = md5(md5($username).time());
				$diff = $user_len - strlen($username);
				$username = $username . substr($rand, 0, $diff);
			} 
			else 
			{ 
				$rand = md5(microtime().md5($username).microtime());
				$username = substr($username, 0, $user_len-5);	
				$username = $username . substr($rand, 0, 5);
			}
			
			# Set the password
			$password = substr(md5(md5(time()).$domain.$username), 0, $pass_len);			
			
			# Set the user/pass  
			$this->login['username'] = strtolower($username);
			$this->login['password'] = $password; 
            
        } else {
        	
        	# Validate
            $this->login['username'] = strtolower($this->service['host_username']);
            $this->login['password'] = $this->service['host_password'];
        }
 
        $result = createacct (  $this->server_cfg['host'],
                                $this->server_cfg['account'],
                                $this->server_cfg['accesshash'],
                                $this->usessl,
                                $this->service['domain_name']. "." . $this->service['domain_tld'],
                                $this->login['username'],
                                $this->login['password'],
                                $this->plugin_data['plan']);	  
        if($this->server['debug']) echo "<pre> $result </pre>";
        
        if(!preg_match("/Account Creation Complete/i",@$result)) {
			return false;  
		} else {					
			$db 	= &DB();
			$id 	= $this->service_id;
			$sql 	= "SELECT * FROM ".AGILE_DB_PREFIX."service WHERE id = $id";
			$rs 	= $db->Execute($sql); 
			$plugin_data = unserialize($rs->fields['host_provision_plugin_data']); 					 
			$insert = Array ( 'host_provision_plugin_data' 	=> serialize($plugin_data),
							  'host_username' => $this->login['username'],
							  'host_password' => $this->login['password']);  
			$sql 	= $db->GetUpdateSQL($rs, $insert);
			$result = $db->Execute($sql);  
			if ($result === false) {
				global $C_debug;
			    $C_debug->error('WHM.php','p_new()', $db->ErrorMsg(). "\r\n\r\n". $sql); 
			}  
			
		    # send the user the details
		    include_once(PATH_MODULES.'email_template/email_template.inc.php');
		    $email = new email_template;
		    $email->send('host_new_user', $this->account['id'], $this->service_id, '', ''); 
		}		
		return true; 
    }


    # edit service  (not used)
    function p_edit()
    {
        return true;
    }


    # activate service
    function p_inactive()
    {
        $result = suspend (     $this->server_cfg['host'],
                                $this->server_cfg['account'],
                                $this->server_cfg['accesshash'],
                                $this->usessl,
                                $this->service['host_username'] );

        if($this->server['debug']) echo "<pre> $result </pre>";
        if(preg_match("/account has been suspended/i",@$result))
        return true;
        else
        return false;
    }


    # deactivate service
    function p_active()
    {
        $result = unsuspend (   $this->server_cfg['host'],
                                $this->server_cfg['account'],
                                $this->server_cfg['accesshash'],
                                $this->usessl,
                                $this->service['host_username'] );

        if($this->server['debug']) echo "<pre> $result </pre>";
        if(preg_match("/account is now active/i",@$result))
        return true;
        else
        return false;
    }


    # delete service
    function p_delete()
    {
        $result = killacct  (   $this->server_cfg['host'],
                                $this->server_cfg['account'],
                                $this->server_cfg['accesshash'],
                                $this->usessl,
                                $this->service['host_username'] );
                                                                
        if($this->server['debug']) echo "<pre> $result </pre>";
        if(preg_match("/done/i",@$result))
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
        $rs 	= $db->Execute($sql);
		$this->service_id = $id;
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

        # set vars & load WHM class
        $this->usessl = $this->server_cfg['mode'];
        if ($this->server_cfg['path'] == "")
        $this->path = '/usr/local/cpanel/Cpanel/Accounting.php.inc';
        else
        $this->path = $this->server_cfg['path'];
        include_once($this->path);

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
