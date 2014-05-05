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
* acct
* 
* Vars from service config:
* 
* package
* 
*/
  
  
class plgn_prov_PSA_RESELL_7_5
{
    function plgn_prov_PSA_RESELL_7_5()
    {
        $this->name             = 'PSA_RESELL_7_5';
        $this->task_based       = false;
        $this->remote_based     = true;
		
		# PSA STUFF:
		$this->psapath 			= "enterprise/control/agent.php"; 
		$this->proto 			= "1.3.1.0";		
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
			$user_len = 10;
			        	
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
				$rand = md5(md5($username).microtime());
				$username = substr($username, 0, $user_len-3);	
				$username = $username . substr($rand, 0, 3);
			}
			
			# Set the password
			$password = substr(md5(md5(time()).$domain.$username), 0, 10);			
			
			# Set the user/pass for the XML queries
			$this->login['username'] = $this->service['domain_name'].'.'.$this->service['domain_tld']; //$username;
			$this->login['password'] = $password; 
            
        } else {
        	
        	# Validate
            $this->login['username'] = $this->service['domain_name'].'.'.$this->service['domain_tld']; //$this->service['host_username'];
            $this->login['password'] = $this->service['host_password'];
        }
		
        
        # get ip address
        if ($this->plugin_data['ip_based'] == '1') {
            $this->ip = $host->useipaddress($this->service, $this->server);
        } else {
            $this->ip = $this->server['name_based_ip']; 
        }		
		   
		# Get the account id  
		$cl_id = $this->server_cfg['acct'];
						
		###################################	 
		###  ADD IP TO POOL: 			###
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
				
		# Loop through the values below and convert to true or false: 
		
		$tf_arr = Array (
			'create_domains', 'manage_phosting', 'manage_sh_access', 'manage_not_chroot_shell', 'manage_quota', 'manage_subdomains',
			'manage_log', 'manage_anonftp', 'manage_crontab', 'site_builder', 'change_limits', 'manage_dns',
			'manage_webapps', 'manage_maillists', 'manage_drweb', 'make_dumps', 
			'fp', 'fp_auth', 'fp_ssl', 'ssl', 'shell', 'php', 'ssi', 'cgi',
			'mod_perl', 'mod_python', 'asp', 'asp_dot_net', 'coldfusion', 'webstat',
			'errdocs', 'at_domains'
		);
						
		for($i=0; $i<count($tf_arr); $i++) {
			if($this->plugin_data["$tf_arr[$i]"] == 1)
				$this->plugin_data["$tf_arr[$i]"] = 'true';
			else
				$this->plugin_data["$tf_arr[$i]"] = 'false';
		}
		 				
		
		# Calculate limits	
	 	@$ftp_quota  = ceil($this->plugin_data['ftp_quota']) *1024*1024;	//MB
		@$disk_space = ceil($this->plugin_data['disk_space']) *1024*1024;	//bytes
		@$max_traffic= ceil($this->plugin_data['max_traffic']) *1024*1024;	//bytes
		@$mbox_quota = ceil($this->plugin_data['mbox_quota']) *1024;		//??
		 
		if($this->plugin_data['shell'] == 1)
			$shell = 'true';
		else
			$shell = '/bin/false';
			

		###################################
		### ADD NEW DOMAIN AND LIMITS:	###
		
		$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<packet version="{$this->proto}">
	<domain> 
		<add>
			<gen_setup>
				<name>{$this->service['domain_name']}.{$this->service['domain_tld']}</name>
				<client_id>$cl_id</client_id> 
				<ip_address>{$this->ip}</ip_address>
				<htype>vrt_host</htype>
				<status /> 
			</gen_setup>  
			<limits>
				<max_subdom>{$this->plugin_data['max_subdom']}</max_subdom>
				<disk_space>{$disk_space}</disk_space>
				<max_traffic>{$max_traffic}</max_traffic>
				<max_wu>{$this->plugin_data['max_wu']}</max_wu>
				<max_db>{$this->plugin_data['max_db']}</max_db>
				<max_box>{$this->plugin_data['max_box']}</max_box>
				<mbox_quota>{$mbox_quota}</mbox_quota>
				<max_redir>{$this->plugin_data['max_redir']}</max_redir>
				<max_mg>{$this->plugin_data['max_mg']}</max_mg>
				<max_resp>{$this->plugin_data['max_resp']}</max_resp> 
				<max_maillists>{$this->plugin_data['max_maillists']}</max_maillists>
				<max_webapps>{$this->plugin_data['max_webapps']}</max_webapps> 												
			</limits>
			<prefs>
				<www>true</www> 
			</prefs>	
			<user>
				<enabled>true</enabled>
				<pname>{$this->account['first_name']} {$this->account['last_name']}</pname>
				<cname>{$this->account['company']}</cname>
				<login>{$this->login['username']}</login>
				<password>{$this->login['password']}</password> 
				<email>{$this->account['email']}</email> 
				<pcode>00000</pcode>
				<country>US</country> 
				<status>0</status>
				<phone />
				<fax />	
				<multiply_login>true</multiply_login>
				<perms>  
			    	<manage_phosting>false</manage_phosting>
			        <manage_sh_access>{$this->plugin_data['manage_sh_access']}</manage_sh_access>
			        <manage_not_chroot_shell>{$this->plugin_data['manage_not_chroot_shell']}</manage_not_chroot_shell>
			        <manage_quota>{$this->plugin_data['manage_quota']}</manage_quota>
			        <manage_subdomains>{$this->plugin_data['manage_subdomains']}</manage_subdomains> 
			        <manage_log>{$this->plugin_data['manage_log']}</manage_log>
			        <manage_anonftp>{$this->plugin_data['manage_anonftp']}</manage_anonftp>
			        <manage_crontab>{$this->plugin_data['manage_crontab']}</manage_crontab>
			        <site_builder>{$this->plugin_data['site_builder']}</site_builder>
			        <change_limits>{$this->plugin_data['change_limits']}</change_limits>
			        <manage_dns>{$this->plugin_data['manage_dns']}</manage_dns> 
			        <manage_webapps>{$this->plugin_data['manage_webapps']}</manage_webapps>
			        <manage_maillists>{$this->plugin_data['manage_maillists']}</manage_maillists>
			        <manage_drweb>{$this->plugin_data['manage_drweb']}</manage_drweb>
			        <make_dumps>{$this->plugin_data['make_dumps']}</make_dumps> 	 			
				</perms>					
			</user> 
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
			$insert = Array ( 'host_provision_plugin_data' 	=> serialize($plugin_data),
							  'host_username' => $this->login['username'],
							  'host_password' => $this->login['password']);  
			$sql 	= $db->GetUpdateSQL($rs, $insert);
			$result = $db->Execute($sql);  
			if ($result === false) {
				global $C_debug;
			    $C_debug->error('PSA_RESELL_7_5.php','plgn_prov_PSA_RESELL_7_5 :: p_new()', $db->ErrorMsg(). "\r\n\r\n". $sql); 
			}  
			
		    # send the user the details
		    include_once(PATH_MODULES.'email_template/email_template.inc.php');
		    $email = new email_template;
		    $email->send('host_new_user', $this->account['id'], $this->service_id, '', ''); 
		}		
		 
		
		$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<packet version="{$this->proto}">
	<domain> 
		<set>
			<filter>
				<id>{$domain_id}</id> 
			</filter>
		 	<values> 
				<hosting>
					<vrt_hst> 
						<ip_address>{$this->ip}</ip_address>
						<ftp_login>{$this->login['username']}</ftp_login>
						<ftp_password>{$this->login['password']}</ftp_password>	
						<ftp_quota>{$ftp_quota}</ftp_quota>
						<fp>{$this->plugin_data['fp']}</fp>
						<fp_ssl>{$this->plugin_data['fp_ssl']}</fp_ssl> 
						<ftp_password>{$this->service['host_password']}</ftp_password>								
						<fp_auth>{$this->plugin_data['fp_auth']}</fp_auth>
						<fp_admin_login>{$this->login['username']}</fp_admin_login>
						<fp_admin_password>{$this->login['password']}</fp_admin_password>								
						<ssl>{$this->plugin_data['ssl']}</ssl>
						<shell>{$this->plugin_data['shell']}</shell>
						<php>{$this->plugin_data['php']}</php>
						<ssi>{$this->plugin_data['ssi']}</ssi>
						<cgi>{$this->plugin_data['cgi']}</cgi>
						<mod_perl>{$this->plugin_data['mod_perl']}</mod_perl>
						<mod_python>{$this->plugin_data['mod_python']}</mod_python>
						<asp>{$this->plugin_data['asp']}</asp>
						<asp_dot_net>{$this->plugin_data['asp_dot_net']}</asp_dot_net>
						<coldfusion>{$this->plugin_data['coldfusion']}</coldfusion>
						<webstat>{$this->plugin_data['webstat']}</webstat>
						<errdocs>{$this->plugin_data['errdocs']}</errdocs>
						<at_domains>{$this->plugin_data['at_domains']}</at_domains>
					</vrt_hst> 
				</hosting> 
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
				
		return false; 		
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
		### SET CLIENT PERMISSIONS & ADD IP TO POOL: ###
		
		# Loop through the values below and convert to true or false: 		
		$tf_arr = Array (
			'create_domains', 'manage_phosting', 'manage_sh_access', 'manage_not_chroot_shell', 'manage_quota', 'manage_subdomains',
			'manage_log', 'manage_anonftp', 'manage_crontab', 'site_builder', 'change_limits', 'manage_dns',
			'manage_webapps', 'manage_maillists', 'manage_drweb', 'make_dumps', 
			'fp', 'fp_ssl', 'ssl', 'shell', 'php', 'ssi', 'cgi',
			'mod_perl', 'mod_python', 'asp', 'asp_dot_net', 'coldfusion', 'webstat',
			'errdocs', 'at_domains'
		);
						
		for($i=0; $i<count($tf_arr); $i++) {
			if($this->plugin_data["$tf_arr[$i]"] == 1)
				$this->plugin_data["$tf_arr[$i]"] = 'true';
			else
				$this->plugin_data["$tf_arr[$i]"] = 'false';
		}

		# Calculate limits	
	 	@$ftp_quota  = round($this->plugin_data['ftp_quota']) *1024*1024;
		@$disk_space = round($this->plugin_data['disk_space']) *1024*1024;
		@$max_traffic= round($this->plugin_data['max_traffic']) *1024*1024;
		@$mbox_quota = round($this->plugin_data['mbox_quota']) *1024;
		
		 

			
		###################################
		### EDIT DOMAIN AND LIMITS:	    ###
		
		$data =<<<EOF
<?xml version="1.0" encoding="UTF-8" standalone="no" ?>
<packet version="{$this->proto}">
	<domain> 
		<set>
			<filter>
				<id>{$this->plugin_data['domain_id']}</id> 
			</filter>
		 	<values> 
				<hosting>
					<vrt_hst>
						<ip_address>{$this->ip}</ip_address>
						<ftp_login>{$this->service['host_username']}</ftp_login>
						<ftp_password>{$this->service['host_password']}</ftp_password>	
						<ftp_quota>{$ftp_quota}</ftp_quota>
						<fp>{$this->plugin_data['fp']}</fp>
						<fp_ssl>{$this->plugin_data['fp_ssl']}</fp_ssl> 
						<ftp_password>{$this->service['host_password']}</ftp_password>								
						<fp_auth>{$this->plugin_data['fp_auth']}</fp_auth>
						<fp_admin_login>{$this->service['host_username']}</fp_admin_login>
						<fp_admin_password>{$this->service['host_password']}</fp_admin_password>								
						<ssl>{$this->plugin_data['ssl']}</ssl>
						<shell>{$this->plugin_data['shell']}</shell>
						<php>{$this->plugin_data['php']}</php>
						<ssi>{$this->plugin_data['ssi']}</ssi>
						<cgi>{$this->plugin_data['cgi']}</cgi>
						<mod_perl>{$this->plugin_data['mod_perl']}</mod_perl>
						<mod_python>{$this->plugin_data['mod_python']}</mod_python>
						<asp>{$this->plugin_data['asp']}</asp>
						<asp_dot_net>{$this->plugin_data['asp_dot_net']}</asp_dot_net>
						<coldfusion>{$this->plugin_data['coldfusion']}</coldfusion>
						<webstat>{$this->plugin_data['webstat']}</webstat>
						<errdocs>{$this->plugin_data['errdocs']}</errdocs>
						<at_domains>{$this->plugin_data['at_domains']}</at_domains>
					</vrt_hst> 
				</hosting>
				<limits>
					<max_subdom>{$this->plugin_data['max_subdom']}</max_subdom>
					<disk_space>{$disk_space}</disk_space>
					<max_traffic>{$max_traffic}</max_traffic>
					<max_wu>{$this->plugin_data['max_wu']}</max_wu>
					<max_db>{$this->plugin_data['max_db']}</max_db>
					<max_box>{$this->plugin_data['max_box']}</max_box>
					<mbox_quota>{$mbox_quota}</mbox_quota>
					<max_redir>{$this->plugin_data['max_redir']}</max_redir>
					<max_mg>{$this->plugin_data['max_mg']}</max_mg>
					<max_resp>{$this->plugin_data['max_resp']}</max_resp> 
					<max_maillists>{$this->plugin_data['max_maillists']}</max_maillists>
					<max_webapps>{$this->plugin_data['max_webapps']}</max_webapps> 												
				</limits> 	
				<user>
					<perms>  
			            <manage_phosting>{$this->plugin_data['manage_phosting']}</manage_phosting>
			            <manage_sh_access>{$this->plugin_data['manage_sh_access']}</manage_sh_access>
			            <manage_not_chroot_shell>{$this->plugin_data['manage_not_chroot_shell']}</manage_not_chroot_shell>
			            <manage_quota>{$this->plugin_data['manage_quota']}</manage_quota>
			            <manage_subdomains>{$this->plugin_data['manage_subdomains']}</manage_subdomains> 
			            <manage_log>{$this->plugin_data['manage_log']}</manage_log>
			            <manage_anonftp>{$this->plugin_data['manage_anonftp']}</manage_anonftp>
			            <manage_crontab>{$this->plugin_data['manage_crontab']}</manage_crontab>
			            <site_builder>{$this->plugin_data['site_builder']}</site_builder>
			            <change_limits>{$this->plugin_data['change_limits']}</change_limits>
			            <manage_dns>{$this->plugin_data['manage_dns']}</manage_dns> 
			            <manage_webapps>{$this->plugin_data['manage_webapps']}</manage_webapps>
			            <manage_maillists>{$this->plugin_data['manage_maillists']}</manage_maillists>
			            <manage_drweb>{$this->plugin_data['manage_drweb']}</manage_drweb>
			            <make_dumps>{$this->plugin_data['make_dumps']}</make_dumps>  			
					</perms>					
				</user> 
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
	<domain> 
		<del>
			<filter>
				<id>{$this->plugin_data['domain_id']}</id> 
			</filter> 
		</del>	 		  
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
	
	
	/*
	* Get the id returned
	*/
	
	function getid($result)
	{
		preg_match ("/(<id>)+([0-9]){1,99}/i", $result, $arr); 			 
		if(is_array($arr) && count($arr) > 0) {  
			$id = preg_replace("@<id>@","", $arr[0]);  	 				
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
