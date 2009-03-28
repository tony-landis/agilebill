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

/**
 * include_once(PATH_MODULES.'email_template/email_template.inc.php');
 * $my = new email_template;
 * $my->send('newsletter_subscribe', '4d1800b401f5d340f022688de0ac2687', 'f1714072da3c05a220ac3b60a3a57d88', '2', '3');
 */
class email_template
{
	var $debug=false;

	# Open the constructor for this mod
	function construct()
	{
		# name of this module:
		$this->module = "email_template";

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
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->construct();
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
		$this->construct();
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
		$this->construct();
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
		$this->construct();
		$this->associated_DELETE[] = Array( 'table'     => 'email_template_translate',
											'field'     => 'email_template_id');

		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->construct();
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
		$this->construct();
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
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}




	##############################
	##	 SEND EMAIL TEMPLATE    ##
	##############################

	function send($template, $acct, $sql1, $sql2, $sql3, $queue=true)
	{        	
		global $VAR, $C_debug;
		$db = &DB();

		# Send to admin only? 
		$admin_only = false;
		if(eregi('admin->', $template)) {
			$admin_only = true;
			$template = eregi_replace('admin->', '', $template);
		}  


		############################################################
		### Get the template settings

		$q = "SELECT * FROM ".AGILE_DB_PREFIX."email_template WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				name        = ".$db->qstr($template);
		$template           = $db->Execute($q);
		if($template->fields['status'] != '1')
		return;

		$E['priority']      = $template->fields["priority"];

		############################################################
		### Get the setup_email settings

		if(empty($template->fields["setup_email_id"]))
			$setup_email_id = DEFAULT_SETUP_EMAIL;
		else
			$setup_email_id = $template->fields["setup_email_id"];

		$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				id          = ".$db->qstr($setup_email_id);
		$setup_email        = $db->Execute($q);

		### E-mail queue?
		if($setup_email->fields['queue'] == 1 && $queue) 
		{     	
			# Set sql vars
			if(is_array($sql1)) $sql1 = serialize($sql1);
			if(is_array($sql2)) $sql2 = serialize($sql2);
			if(is_array($sql3)) $sql3 = serialize($sql3);
			if(is_array($VAR))  $var  = serialize($VAR);
			if(@$admin_only)
				$sql_template = "admin->".$template->fields['name'];
			else
				$sql_template = $template->fields['name'];

			# Check that this email is not already in the queue: 
			$q = "SELECT id FROM ".AGILE_DB_PREFIX."email_queue WHERE
					site_id     = ".$db->qstr(DEFAULT_SITE)." AND
					status		= 0 AND
					account_id	= '$acct' AND
					email_template= ".$db->qstr($sql_template)." AND
					sql1		= ".$db->qstr(@$sql1)." AND
					sql2		= ".$db->qstr(@$sql2)." AND
					sql3		= ".$db->qstr(@$sql3);
			$duplicates = $db->Execute($q);            	
			if($duplicates != false && $duplicates->RecordCount() > 0)
				return; 

			# queue this e-mail:
			$id = $db->GenID(AGILE_DB_PREFIX.'email_queue_id');
			$sql = "INSERT INTO ".AGILE_DB_PREFIX."email_queue SET
					id 			= $id,
					site_id 	= ".DEFAULT_SITE.",
					date_orig	= ".time().",
					date_last	= ".time().",
					status		= 0,
					account_id	= '$acct',
					email_template= ".$db->qstr($sql_template).",
					sql1		= ".$db->qstr(@$sql1).",
					sql2		= ".$db->qstr(@$sql2).",
					sql3		= ".$db->qstr(@$sql3).", 
					var			= ".$db->qstr(@$var);
			$db->Execute($sql);
			return;	
		}            	

		if($setup_email->fields['type'] == 0) {
			$type = 0;
		} else {
			$type = 1;
			$E['server']    = $setup_email->fields['server'];
			$E['account']   = $setup_email->fields['username'];
			$E['password']  = $setup_email->fields['password'];
		}

		$E['from_name']     = $setup_email->fields['from_name'];
		$E['from_email']    = $setup_email->fields['from_email'];

		if($setup_email->fields['cc_list'] != '')
			$E['cc_list']   = explode(',', $setup_email->fields['cc_list']);

		if($setup_email->fields['bcc_list'] != '')
			$E['bcc_list']  = explode(',', $setup_email->fields['bcc_list']);



		############################################################
		### Get the account settings

		$q = "SELECT * FROM ".AGILE_DB_PREFIX."account WHERE
				site_id     = ".$db->qstr(DEFAULT_SITE)." AND
				(
				email		= ".$db->qstr($acct)." OR
				id          = ".$db->qstr($acct). "
				)";
		$account            = $db->Execute($q);
		if($account == false)
		{ 
			$C_debug->error('email_template.inc.php','send1', $db->ErrorMsg() . " " . $sql);
			return false;
		}
		else 
		{             
			if($admin_only == false) 
			{
				if($account->RecordCount() > 0) { 
					$E['to_email']      = $account->fields['email'];
					$E['to_name']       = $account->fields['first_name'] . ' ' . $account->fields['last_name'];
					$this->ab_account = true; 
				} else {
					$E['to_email']      = $acct;
					$E['to_name']       = $acct; 
					$this->ab_account 	= false;           	
				}
			} else {
				$E['to_email']      = $setup_email->fields['from_email'];
				$E['to_name']       = $setup_email->fields['from_name'];   
				$this->ab_account 	= true;        	
			}
		}


		############################################################
		### Get the template translation for the specified account for text/htm

		if(@$this->ab_account && @$account->fields["language_id"] != "")
			$language_id    = $account->fields["language_id"];
		else
			$language_id    = DEFAULT_LANGUAGE;

		$q = "SELECT * FROM ".AGILE_DB_PREFIX."email_template_translate WHERE
				site_id             = ".$db->qstr(DEFAULT_SITE)." AND
				language_id         = ".$db->qstr($language_id)." AND
				email_template_id   = ".$db->qstr($template->fields["id"]);
		$setup_email        = $db->Execute($q);

		if(!$setup_email || !$setup_email->RecordCount()) {
			# get the default translation for this email:
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."email_template_translate WHERE
					site_id             = ".$db->qstr(DEFAULT_SITE)." AND
					language_id         = ".$db->qstr(DEFAULT_LANGUAGE)." AND
					email_template_id   = ".$db->qstr($template->fields["id"]);
			$setup_email        = $db->Execute($q);
		}

		if(!$setup_email || !$setup_email->RecordCount()) {
			# unable to locate translation!
			global $C_debug;
			$message = 'Unable to locate translation for Email Template "'.$template->fields['name'].'" and Language "'. $language_id .'" OR "' . DEFAULT_LANGUAGE . '"';
			$C_debug->error('email_template.inc.php','send', $message);
			return;
		}


		# set the subject:
		$E['subject']           = $setup_email->fields['subject'];

		# determine whether to send HTML or not...
		if(@$this->ab_account && $account->fields['email_type'] == 1) {
			if(!empty($setup_email->fields['message_html'])) {
				$E['body_html']      = $setup_email->fields['message_html'];
				$E['html']           = '1';
			} else {
				$E['body_html']      = false;
				$E['html']           = '0';
			}
		} else {
			$E['html']           = '0';
		}

		$E['body_text']              = $setup_email->fields['message_text'];

		### Get the date-time  
		include_once(PATH_CORE.'list.inc.php');
		$C_list = new CORE_list;
		$date = $C_list->date_time(time());

		### Url formatting...
		if($admin_only) {
			$site_url = URL.'admin.php';
			$site_ssl_url = SSL_URL.'admin.php';
		} else {
			$site_url = URL;
			$site_ssl_url = SSL_URL;            	
		}

		### Get the replace vars from the email template:	
		$replace = Array('%site_name%'  => $E['from_name'],
						 '%site_email%' => $E['from_email'],
						 '%url%'        => $site_url,
						 '%date%'       => $date,
						 '%ssl_url%'    => $site_ssl_url);

		### Get the replace vars from the $VAR variable:
		reset($VAR);
		while(list($key, $value) = each($VAR))
		{
			$re_this  = "%var_".$key."%";
			$replace[$re_this] = $value;
		}

		### Get the replace vars from the account: 
		$replace['%acct_id%'] = $acct;
		if(@$this->ab_account) {
			while(list($key, $value) = each($account->fields)) {
				$re_this  = "%acct_".$key."%";
				$replace[$re_this] = $value;
			} 
		}

		############################################################
		### Get the SQL1 Query/Arrays 
		if(!empty($template->fields["sql_1"]) && !empty($sql1) &&!is_array($sql1))
		{ 
			$sql    = eregi_replace('%DB_PREFIX%', AGILE_DB_PREFIX, $template->fields["sql_1"]);
			$sql    = eregi_replace('%SQL1%', $db->qstr($sql1), $sql);
			if(!is_array($sql2)) 
			$sql    = eregi_replace('%SQL2%', $db->qstr($sql2), $sql);
			if(!is_array($sql3)) 
			$sql    = eregi_replace('%SQL3%', $db->qstr($sql3), $sql);
			$sql   .= " AND site_id     = ".  $db->qstr(DEFAULT_SITE);
			$SQL_1  = $db->Execute($sql);

			if($SQL_1 == false)
			{
				### return the error message
				global $C_debug;
				$C_debug->error('email_template.inc.php','send', $db->ErrorMsg() . " " . $sql);
			}
			else if($SQL_1->RecordCount() > 0)
			{
				### Get the replace vars from the sql results:
				while(list($key, $value) = each($SQL_1->fields))
				{
					$re_this  = "%sql1_".$key."%";
					$replace[$re_this] = $value;
				}
			}
		} 
		elseif (is_array($sql1)) 
		{  
			 while(list($key, $value) = each($sql1[$i]))   
					$replace[$key] = $value;  
		}
		elseif (!empty($sql1))
		{
			$replace['%sql1%'] = $sql3; 
		}


		############################################################
		### Get the SQL2 Query/Arrays 
		if(!empty($template->fields["sql_2"]) && !empty($sql2) &&!is_array($sql2))
		{
			$sql = eregi_replace('%DB_PREFIX%', AGILE_DB_PREFIX, $template->fields["sql_2"]);
			$sql = eregi_replace('%SQL1%', $db->qstr($sql1), $sql);
			if(!is_array($sql2)) 
			$sql = eregi_replace('%SQL2%', $db->qstr($sql2), $sql);
			if(!is_array($sql3)) 
			$sql = eregi_replace('%SQL3%', $db->qstr($sql3), $sql);
			$sql .= " AND site_id     = ".$db->qstr(DEFAULT_SITE);
			$SQL_2     = $db->Execute($sql);
			if($SQL_2 == false)
			{
				### return the error message
				global $C_debug;
				$C_debug->error('email_template.inc.php','send', $db->ErrorMsg() . " " . $sql);
			}
			else if($SQL_2->RecordCount() > 0)
			{
				### Get the replace vars from the sql results:
				while(list($key, $value) = each($SQL_2->fields))
				{
					$re_this  = "%sql2_".$key."%";
					$replace[$re_this] = $value;
				}
			} 
		} 
		elseif (is_array($sql2)) 
		{  
			 while(list($key, $value) = each($sql2[$i]))   
					$replace[$key] = $value;  
		}
		elseif (!empty($sql2))
		{
			$replace['%sql2%'] = $sql2; 
		}


		############################################################
		### Get the SQL3 Query/Arrays 
		if(!empty($template->fields["sql_3"]) && !empty($sql3) &&!is_array($sql3))
		{
			$sql = eregi_replace('%DB_PREFIX%', AGILE_DB_PREFIX, $template->fields["sql_3"]);
			$sql = eregi_replace('%SQL1%', $db->qstr($sql1), $sql);
			if(!is_array($sql2)) 
			$sql = eregi_replace('%SQL2%', $db->qstr($sql2), $sql);
			if(!is_array($sql3)) 
			$sql = eregi_replace('%SQL3%', $db->qstr($sql3), $sql);
			$sql .= " AND site_id     = ".$db->qstr(DEFAULT_SITE);
			$SQL_3          = $db->Execute($sql);
			if($SQL_3 == false)
			{
				### return the error message
				global $C_debug;
				$C_debug->error('email_template.inc.php','send', $db->ErrorMsg() . " " . $sql);
			}
			else if($SQL_3->RecordCount() > 0)
			{
				### Get the replace vars from the sql results:
				while(list($key, $value) = each($SQL_3->fields))
				{
					$re_this  = "%sql3_".$key."%";
					$replace[$re_this] = $value;
				}
			}
		} 
		elseif (is_array($sql3)) 
		{
			 while(list($key, $value) = each($sql3))   
					$replace[$key] = $value;  
		}
		elseif (!empty($sql3))
		{
			$replace['%sql3%'] = $sql3; 
		} 

		### Replace the $replace vars in the body and subject
		while(list($key, $value) = each($replace))
		{
			$E['subject']   = eregi_replace($key, $value, $E['subject']);
			$E['body_text'] = eregi_replace($key, $value, $E['body_text']);
			if(!empty($E['body_html']))
			$E['body_html'] = eregi_replace($key, $value, $E['body_html']);
		}

		### Remove any unparsed vars from the body text and html:  
		if(!empty($E['body_html']) && ereg('%',$E['body_html']))
		@$E['body_html'] = ereg_replace("%[a-zA-Z0-9_]{1,}%", '', $E['body_html']); 
		if(!empty($E['body_text']) && ereg("%",$E['body_text']))
		@$E['body_text'] = ereg_replace("%[a-zA-Z0-9_]{1,}%", '', $E['body_text']);            

		### Set any attachments (not currently supported)
		$E['attatchments']  = '';

		/* email log? */
		global $C_list;
		if(is_object($C_list) && $C_list->is_installed('email_log')) {
			include_once(PATH_MODULES.'email_log/email_log.inc.php');
			$log = new email_log;            	
			$log->add($acct, $E['subject'], $E['body_text'], $E['to_email'], false, $E['priority']);
		}

		### Call the mail class
		require_once(PATH_CORE   . 'email.inc.php');
		$email = new CORE_email;
		$email->debug=$this->debug;
		if($type == 0)
		return $email->PHP_Mail($E);
		else
		return $email->SMTP_Mail($E);
	}
}
?>