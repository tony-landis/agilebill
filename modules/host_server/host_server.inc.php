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
	
class host_server
{

	# Open the constructor for this mod
	function host_server()
	{		        	
		# name of this module:
		$this->module = "host_server";

		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

		# open the construct file for parsing	
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);

		$this->method   = $construct["construct"]["method"];
		$this->trigger  = $construct["construct"]["trigger"];
		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"];
	}

	# manual add
	function host_manual_new($service, $server, $account)
	{
	}

	# manual edit
	function host_manual_edit($service, $server, $account)
	{
	}

	# manual activate
	function host_manual_active($service, $server, $account)
	{
	}

	# manual deactivate
	function host_manual_inactive($service, $server, $account)
	{
	}

	# manual delete
	function host_manual_delete($service, $server, $account)
	{
	}


	# Generate a new login
	function generate_login($service,$account,$max_un_len, $max_pw_len, $shared)
	{
		# define username
		if($service['host_username'] != '')
		{
			$ret['username']    = $service['host_username'];
		} else
		{
			if ($shared == false)
			{
				# is username already in use on this server?
				$db     = &DB();
				$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'service WHERE
						   id               != ' . $db->qstr( $service['id'] ) . ' AND
						   host_server_id   =  ' . $db->qstr( $service['host_server_id'] ) . ' AND
						   host_username    =  ' . $db->qstr( $account['username'] ) . ' AND
						   site_id          =  ' . $db->qstr(DEFAULT_SITE);
				$rs     = $db->Execute($sql);
				if ($rs->RecordCount() == 0)
				{
					$ret['username'] = $account['username'];
				} else {
					$ret['username'] = $this->generate_login1($max_un_len);
				}
			} else {
				$ret['username'] = $account['username'];
			}
		}

		# define password
		if($service['host_password'] != '') {
			$ret['password']    = $service['host_password'];
		} else {
			$ret['password']   = $this->generate_login1($max_pw_len);
		}

		# save the username/password for this service
		$db     = &DB();
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service
				SET
				host_username   =   ' . $db->qstr( $ret['username'] ) . ',
				host_password   =   ' . $db->qstr( $ret['password'] ) . '
				WHERE
				id               = ' . $db->qstr( $service['id'] ) . ' AND
				site_id          =  ' . $db->qstr(DEFAULT_SITE);
		$db->Execute($sql);
		return $ret;
	}


	# random un/pw
	function generate_login1($length)
	{
		srand((double)microtime()*1000000);
		$vowels = array("a", "e", "i", "o", "u");
		$cons   = array("b", "c", "d", "g", "h", "j", "k", "l", "m", "n", "p",
				"r", "s", "t", "u", "v", "w", "tr", "cr", "br", "fr", "th",
				"dr", "ch", "ph", "wr", "st", "sp", "sw", "pr", "sl", "cl");
		$num_vowels = count($vowels);
		$num_cons = count($cons);
		for($i = 0; $i < $length; $i++){
			@$rand .= $cons[rand(0, $num_cons - 1)] . $vowels[rand(0, $num_vowels - 1)];
		}
		return $rand;
	}

	# use ip address
	function useipaddress($service, $server)
	{
		if($service['host_ip'] != '') return $service['host_ip'];

		$pat = "\r\n";
		$ips_r = '';
		@$ips = explode($pat, $server['ip_based_ip']);
		for($i=0; $i<count(@$ips); $i++) {
			if($i==0)
			{
				if($ips[0] != '')
					$ip = $ips[0];
				else
					return false;
			}
			else
			{
				if($ips[$i] != '')
					@$ips_r .= $ips[$i].$pat;
			}
		}

		# update this service
		$db     = &DB();
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'service
				SET
				host_ip          = ' . $db->qstr( $ip ) . '
				WHERE
				id               = ' . $db->qstr( $service['id'] ) . ' AND
				site_id          = ' . $db->qstr(DEFAULT_SITE);
		$db->Execute($sql);

		# update ip list for this server
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'host_server
				SET
				ip_based_ip      = ' . $db->qstr( $ips_r ) . '
				WHERE
				id               = ' . $db->qstr( $server['id'] ) . ' AND
				site_id          = ' . $db->qstr(DEFAULT_SITE);
		$db->Execute($sql);

		return $ip;
	}

	# re-use ip address
	function unuseipaddress($server, $ip)
	{
		if(empty($ip)) return false; 

		# update ip list for this server
		$ips = $ip;
		if(!empty($server['ip_based_ip']))
			$ips .= "\r\n".$server['ip_based_ip'];

		# update
		$db     = &DB();
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'host_server
				SET
				ip_based_ip      = ' . $db->qstr( $ips ) . '
				WHERE
				id               = ' . $db->qstr( $server['id'] ) . ' AND
				site_id          = ' . $db->qstr(DEFAULT_SITE);
		$db->Execute($sql); 
		return true;
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$VAR['host_server_keycode'] = md5(rand(99,999) . microtime());

		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);    		
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{
		global $smarty;

		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$dx = new CORE_database;
		$rs = $dx->view($VAR, $this, $type);

		# get the list of available servers to define as "next server"
		$db     = &DB();
		$sql= 'SELECT id,name FROM ' . AGILE_DB_PREFIX . 'host_server WHERE
				   id                   !=  ' . $db->qstr( $rs[0]['id'] ) . ' AND
				   next_host_server_id  !=  ' . $db->qstr( $rs[0]['id'] ) . ' AND
				   provision_plugin     =  ' . $db->qstr( $rs[0]['provision_plugin'] ) . ' AND
				   site_id              =  ' . $db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		if(@$rs->RecordCount() > 0)
		{
			$arr[0] = ''; 
			while(!$rs->EOF) {
				$arr[$rs->fields['id']] = $rs->fields['name'];
				$rs->MoveNext();
			}
			$smarty->assign('next_server_options', $arr);
			$smarty->assign('next_server', true);

		} else {
			$smarty->assign('next_server', false);
		}
	}		

	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->update($VAR, $this, $type);
	}

	##############################
	##		 DELETE	            ##
	##############################
	function delete($VAR)
	{	
		$db = new CORE_database;
		 $db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_form($VAR, $this, $type);
	}

	##############################
	##		    SEARCH		    ##
	##############################
	function search($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search($VAR, $this, $type);
	}

	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{	
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->search_show($VAR, $this, $type);
	}

}
?>