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
*
*
* Vars from service config:
*
* package
*
*/


class plgn_prov_PLESK_8
{
    function plgn_prov_PLESK_8()
    {
        $this->name             = 'PLESK_8';
        $this->task_based       = false;
        $this->remote_based     = true;

	# PSA STUFF:
	$this->psapath 			= "enterprise/control/agent.php";
	$this->proto 			= "1.4.2.0";
    }


    # add new service
    function p_new()
    {
        # get the common server class and set login details
        include_once(PATH_MODULES.'host_server/host_server.inc.php');
        $host = new host_server;
        if($this->service['host_username'] == '' || $this->service['host_password'] == '')
        {
        	# set the limits
		$pass_len = 10;
		$user_len = 12;

        	# Generate a new username/login:
		$domain = $this->service['domain_name'].$this->service['domain_tld'];

		# set the username
		$username = trim($domain);
		$username = eregi_replace("[-_\.]", "", $username);
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
		$password = substr(md5(md5(time()).$domain.$username), 0, 10);

		# Set the user/pass for the XML queries
		$this->login['username'] = $username;
		$this->login['password'] = $password;

        } else {

        	# Validate
		$this->login['username'] = $this->service['host_username'];
		$this->login['password'] = $this->service['host_password'];
        }


        # get ip address
        if ($this->plugin_data['ip_based'] == '1') {
		$this->ip = $host->useipaddress($this->service, $this->server);
        } else {
		$this->ip = $this->server['name_based_ip'];
        }


	####################################################
	### Assemble the XML for the account creation:	####

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<add>
					<gen_info>
						<pname>{$this->account['first_name']} {$this->account['last_name']} ({$this->login['username']})</pname>
						<login>{$this->login['username']}</login>
						<passwd>{$this->login['password']}</passwd>
						<email>{$this->account['email']}</email>
						<pcode>00000</pcode>
						<country>US</country>
						<status>0</status>
						<phone>18005551212</phone>
					</gen_info>
					<template-name>{$this->plugin_data['client_template_name']}</template-name>
				</add>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);

	# Get the account id
	if (!$cl_id = $this->getid($result))
		return false;


	##############################
	### ADD IP TO CLIENT POOL: ###

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<ippool_add_ip>
					<client_id>$cl_id</client_id>
					<ip_address>{$this->ip}</ip_address>
				</ippool_add_ip>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);

	#######################
	### ADD NEW DOMAIN: ###

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<domain>
				<add>
					<gen_setup>
						<name>{$this->service['domain_name']}.{$this->service['domain_tld']}</name>
						<client_id>$cl_id</client_id>
						<htype>vrt_hst</htype>
						<ip_address>{$this->ip}</ip_address>
						<status />
					</gen_setup>
					<hosting>
						<vrt_hst>
							<ftp_login>{$this->login['username']}</ftp_login>
							<ftp_password>{$this->login['password']}</ftp_password>
							<ip_address>{$this->ip}</ip_address>
						</vrt_hst>
					</hosting>
					<template-name>{$this->plugin_data['domain_template_name']}</template-name>
				</add>
			</domain>
</packet>
EOF;


	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	# Get the account id
	if(!$domain_id = $this->getid($result))  {
		return false;
	} else {
		$db 	= &DB();
		$id 	= $this->service_id;
		$sql 	= "SELECT * FROM ".AGILE_DB_PREFIX."service WHERE id = $id";
		$rs 	= $db->Execute($sql);
		$plugin_data = unserialize($rs->fields['host_provision_plugin_data']);
		$plugin_data['account_id'] = $cl_id;
		$plugin_data['domain_id'] = $domain_id;
		$insert = Array ('host_provision_plugin_data' 	=> serialize($plugin_data),
				 'host_username' => $this->login['username'],
				 'host_password' => $this->login['password']);
		$sql 	= $db->GetUpdateSQL($rs, $insert);
		$result = $db->Execute($sql);
		if ($result === false) {
			global $C_debug;
			$C_debug->error('PLESK_8.php','plgn_prov_PLESK_8 :: p_new()', $db->ErrorMsg(). "\r\n\r\n". $sql);
		}

		# send the user the details
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$email = new email_template;
		$email->send('host_new_user', $this->account['id'], $this->service_id, '', '');
	}
	return true;
    }


    # edit service
    function p_edit()
    {
	## Get the IP
	if(!empty($this->service['host_ip']))
		$this->ip = $this->service['host_ip'];
	else
		$this->ip = $this->server['name_based_ip'];


        ################################################
        ### SET CLIENT LOGIN

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<set>
					<filter>
						<id>{$this->plugin_data['account_id']}</id>
					</filter>
			        <values>
			          <gen_info>
			          	<login>{$this->service['host_username']}</login>
			          	<passwd>{$this->service['host_password']}</passwd>
			          </gen_info>
			        </values>
				</set>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	############################
	### SET CLIENT TEMPLATE: ###

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<set>
					<filter>
						<id>{$this->plugin_data['account_id']}</id>
					</filter>
			        <values>
			          <template-name>{$this->plugin_data['client_template_name']}</template-name>
			        </values>
				</set>
				<ippool_add_ip>
					<client_id>{$this->plugin_data['account_id']}</client_id>
					<ip_address>{$this->ip}</ip_address>
				</ippool_add_ip>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	### Edit the domain settings:

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<domain>
				<set>
					<filter>
						<id>{$this->plugin_data['domain_id']}</id>
					</filter>
			        <values>
			          <gen_setup>
			            <status>16</status>
						<name>{$this->service['domain_name']}.{$this->service['domain_tld']}</name>
			          </gen_setup>
			        </values>
				</set>
			</domain>
</packet>
EOF;


	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<domain>
				<set>
					<filter>
						<id>{$this->plugin_data['domain_id']}</id>
					</filter>
			        <values>

			        	<gen_setup>
			            	<status>0</status>
							<name>{$this->service['domain_name']}.{$this->service['domain_tld']}</name>
			          	</gen_setup>
					<hosting>
						<vrt_hst>
							<ip_address>{$this->ip}</ip_address
							<ftp_login>{$this->service['host_username']}</ftp_login>
							<ftp_password>{$this->service['host_password']}</ftp_password>
						</vrt_hst>
					</hosting>
					<template-name>{$this->plugin_data['domain_template_name']}</template-name>
					</values>
				</set>
			</domain>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);

	if(!empty($result))
		return true;
	else
		return false;
    }


    # activate service
    function p_inactive()
    {
	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<set>
					<filter>
						<id>{$this->plugin_data['account_id']}</id>
					</filter>
			        <values>
			          <gen_info>
			            <status>16</status>
			          </gen_info>
			        </values>
				</set>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<domain>
				<set>
					<filter>
						<id>{$this->plugin_data['domain_id']}</id>
					</filter>
			        <values>
			          <gen_setup>
			            <status>16</status>
			          </gen_setup>
			        </values>
				</set>
			</domain>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);
	if(!empty($result))
		return true;
	else
		return false;
    }


    # deactivate service
    function p_active()
    {
	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<set>
					<filter>
						<id>{$this->plugin_data['account_id']}</id>
					</filter>
			        <values>
			          <gen_info>
			            <status>0</status>
			          </gen_info>
			        </values>
				</set>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<domain>
				<set>
					<filter>
						<id>{$this->plugin_data['domain_id']}</id>
					</filter>
			        <values>
			          <gen_setup>
			            <status>0</status>
						<name>{$this->service['domain_name']}.{$this->service['domain_tld']}</name>
			          </gen_setup>
			        </values>
				</set>
			</domain>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);


	if(!empty($result))
		return true;
	else
		return false;
    }




    # delete service
    function p_delete()
    {
	# recycle the IP if ip_based:
	if ($this->plugin_data['ip_based'] == '1') {
		include_once(PATH_MODULES.'host_server/host_server.inc.php');
		$host = new host_server;
		$this->ip = $host->unuseipaddress($this->server, $this->service['host_ip']);
	}

	$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
		<packet version="{$this->proto}">
			<client>
				<del>
					<filter>
						<id>{$this->plugin_data['account_id']}</id>
					</filter>
				</del>
			</client>
</packet>
EOF;

	# Connect & get response:
	$result = $this->connect(
		$this->server_cfg['host'],
		$this->server_cfg['port'],
		$this->server_cfg['user'],
		$this->server_cfg['pass'],
		$data
	);

	# Debug:
	$this->debug($data, $result);

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
	    $db = &DB();
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
	* Get the id returned
	*/

	function getid($result)
	{
		preg_match ("/(<id>)+([0-9]){1,99}/i", $result, $arr);
		if(is_array($arr) && count($arr) > 0) {
			$id = ereg_replace("<id>","", $arr[0]);
			if(!is_numeric($id))
				return false;
			else
				return $id;
		} else {
			return false;
		}
		return false;
	}

	/*
	* Debug
	*/

	function debug($data,$result=false)
	{
		if($this->server['debug']) {
			echo '<B><BR>REQUEST:</B><BR>';
			echo "<pre>" . htmlspecialchars($data) . "</pre>";
			echo '<B>RESPONSE:</B><BR>';
			echo "<pre>" . htmlspecialchars($result) . "</pre>";
		}
	}

	/*
	* Curl connect functions
	*/
	function connect($HOST, $PORT, $LOGIN, $PASSWD, $DATA)
	{
		$url = "https://" . $HOST . ":" . $PORT . "/" . $this->psapath;

		$headers = array(
			"HTTP_AUTH_LOGIN: " . $LOGIN,
			"HTTP_AUTH_PASSWD: " . $PASSWD,
			"HTTP_PRETTY_PRINT: TRUE",
			"Content-Type: text/xml",
		);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $DATA);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1);
		$result = curl_exec($ch);
		$this->result = $result;
		curl_close($ch);
		return $result;
	}
}

?>
