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
	
class task
{

	# Open the constructor for this mod
	function task()
	{
		# name of this module:
		$this->module = "task";

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



	##############################
	##	 RUN ALL DUE TASKS      ##
	##############################
	function run_all()
	{
		# ensure that tasks complete and dont hang on
		# running=1
		set_time_limit(2 * 60 * 60);

		# Loop through the tasks:
		global $VAR;

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'task WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					( running = 0 OR running IS NULL )';
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			include_once(PATH_INCLUDES . 'cron/cron.inc.php');
			$cron = new cron;
			while(!$result->EOF)
			{
				$_r  = false;
				$_s  = (int) $result->fields['date_start'];
				$_e  = (int) $result->fields['date_expire'];
				$_l  = (int) $result->fields['date_last'];
				$_c  = $result->fields['int_min'] . " ";
				$_c .= $result->fields['int_hour'] . " ";
				$_c .= $result->fields['int_month_day'] . " ";
				$_c .= $result->fields['int_month'] . " ";
				$_c .= $result->fields['int_week_day'];
				$_n = (int) time();

				if(!$_l > 0) $_l = $_n-86400*365;

				# Verify it has not expired:
				if ($_s <= $_n || $_s == "" || $_s == "0")
				{
					# Verify it is past the start date:
					if ($_e >= $_n || $_e == "" || $_e == "0")
					{
						# Verify that it is time to run:
						if($cron->due($_l, $_n, $_c))
						{
							# Run the task:
							$this->id = $result->fields['id'];
							$this->run($VAR, $this);
						}
					}
				}
				$result->MoveNext();
			}
		}
	}


	##############################
	##		RUN                 ##
	##############################
	function run($VAR)
	{  
		# check auth: (windows)
		global $VAR, $C_auth, $_ENV, $_SERVER, $_COOKIE;
		$noauth = false;
		$debug_out = false;
		if (!empty($_ENV['S']) ||
			@$_ENV['SESSIONNAME'] 		== "Console" 	||
			@$_SERVER['SESSIONNAME'] 	== "Console" 	||
			@$_SERVER['CLIENTNAME'] 	== "Console" 	||
			@empty($_COOKIE))
		{
			$debug_out = true;
			$noauth = true;
		} elseif($C_auth->auth_method_by_name('task','run')) {
			$noauth = true;
		} else {
			$noauth = false;
		}

		if (isset($this->id))
		$id = $this->id;
		elseif ( isset ($VAR['id']))
		$id = $VAR['id'];
		else return;

		###############################
		# Get task details
		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'task WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($id);
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0) return;


		$type = $result->fields['type'];
		$cmd  = $result->fields['command'];
		$log  = $result->fields['log'];

		#################################
		# Flip Run Toggle 
		$sql = 'UPDATE ' . AGILE_DB_PREFIX . 'task SET
				running = 1 WHERE
				site_id = ' . $db->qstr(DEFAULT_SITE) . ' AND
				id = ' . $db->qstr("$id");
		$db->Execute($sql);            

		###############################
		# Run task
		if (  $type == 0 )
		{
			### Internal function:
			global $C_method;
			$arr = explode(":",$cmd);

			if($noauth) {
				# run from console, no auth req
				$C_method->exe_noauth($arr[0],$arr[1]);
				#if($debug_out)
				# echo $cmd."<BR>";
			}
			else
			{
				# run from web, auth required
				$C_method->exe($arr[0],$arr[1]);
			}

			if($C_method->result)
				$result = 1;
			else
				$result = 0;
			@$message = $C_method->error;
		}
		elseif ( $type == 1)
		{
			### System command
			$message = `$cmd`;
			$result = 1;
		}


		###############################
		# Update last run date & flip run toggle		 
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'task SET
					running  = 0,
					date_last =  ' . $db->qstr(time()) . ' 
					WHERE
					site_id  =  ' . $db->qstr(DEFAULT_SITE) . ' AND
					id       = ' . $db->qstr("$id");
		$db->Execute($sql);


		###############################
		# Store task log if required
		/*
		if($log == '1')
		{ 
			$idx    = $db->GenID(AGILE_DB_PREFIX . "" . 'task_log_id');
			$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'task_log SET
						site_id  =  ' . $db->qstr(DEFAULT_SITE) . ',
						id       = ' . $db->qstr($idx) . ',
						task_id  = ' . $db->qstr($id) . ',
						result   = ' . $db->qstr(@$result) . ',
						message  = ' . $db->qstr(@$message) . ',
						date_orig= ' . $db->qstr(time());
			$result = $db->Execute($sql);
		}
		*/


		###############################
		# If admin, print success message

		if ( DEFAULT_ADMIN_THEME == SESS_THEME )
		{
			global $C_translate, $C_debug;
			#$C_debug->alert ( $C_translate->translate('true','','') );
		}
	}

	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
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
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		 $db->view($VAR, $this, $type);
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