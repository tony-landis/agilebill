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
	
class staff
{

	# Open the constructor for this mod
	function staff()
	{
		# name of this module:
		$this->module = "staff";

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
	##	EMAIL ONE STAFF MEMBER  ##
	##############################
	function contact($VAR)
	{
		global $C_translate, $C_debug, $C_vars;					

		## Validate the required vars (account_id, message, subject)
		if(@$VAR['mail_email'] != "" && @$VAR['mail_name'] != "" && @$VAR['mail_subject'] != "" && @$VAR['mail_message'] != "")
		{
			include_once(PATH_CORE . 'validate.inc.php');
			$validate = new CORE_validate;
			if(!$validate->validate_email($VAR['mail_email'],''))
			{
					$C_debug->alert($C_translate->translate('validate_email','',''));
					$C_vars->strip_slashes_all();
					return;
			}

			@$s  = $VAR['mail_staff_id'];
			@$d  = $VAR['mail_department_id'];


			if ($s > 0)
			{
				## Nothing to do
			}
			else if($d > 0)
			{

				## Verify the specified department && get the associated account:
				$db     = &DB();
				$sql    = 'SELECT default_staff_id FROM ' . AGILE_DB_PREFIX . 'staff_department WHERE
						   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						   id          = ' . $db->qstr($d);
				$dept = $db->Execute($sql);

				if($dept->RecordCount() == 0)
				{
					$C_debug->alert($C_translate->translate('error_dept_non_exist','staff',''));
					$C_vars->strip_slashes_all();
					return;
				}

				$s = $dept->fields['default_staff_id'];

			}
			else
			{
				## staff/dept not specified
				$C_debug->alert($C_translate->translate('error_staff_dept','staff',''));
				$C_vars->strip_slashes_all();
				return;
			}


			## Verify the specified staff account && get the associated account:
			$db     = &DB();
			$sql    = 'SELECT account_id FROM ' . AGILE_DB_PREFIX . 'staff WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr($s);
			$staff = $db->Execute($sql);

			if($staff->RecordCount() == 0)
			{
				$C_debug->alert($C_translate->translate('error_staff_non_exist','staff',''));
				$C_vars->strip_slashes_all();
				return;
			}

			$account_id = $staff->fields['account_id'];
			$sql    = 'SELECT email,first_name,last_name FROM ' . AGILE_DB_PREFIX . 'account WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id          = ' . $db->qstr($account_id);
			$account = $db->Execute($sql);

			if($account->RecordCount() == 0)
			{
				$C_debug->alert($C_translate->translate('error_staff_non_exist','staff',''));
				$C_vars->strip_slashes_all();
				return;
			}

			### Validate any static vars, if defined 
			$this->validated = true;
			if(!empty($VAR['static_relation']))
			{
				require_once(PATH_CORE   . 'static_var.inc.php');
				$static_var = new CORE_static_var;

				if(!isset($this->val_error)) 
				$this->val_error = false;

				$all_error = $static_var->validate_form('staff', $this->val_error);

				if($all_error != false && gettype($all_error) == 'array')
				{
					$this->validated = false;
				}
				else
				{
					$this->validated = true;

					# Get the fields and values and append to the message text...
					while(list($id,$value) = each($VAR['static_relation']))
					{
						if(!empty($value) && !empty($id))
						{
							# Get the name:
							$db = &DB();
							$sql = "SELECT static_var_id FROM ".AGILE_DB_PREFIX."static_relation WHERE
								 id 		= ".$db->qstr($id)." AND
								 site_id 	= ".$db->qstr(DEFAULT_SITE);
							$rs = $db->Execute($sql);
							$var_id = $rs->fields['static_var_id'];

							$sql = "SELECT name FROM ".AGILE_DB_PREFIX."static_var WHERE
								  id 		= ".$db->qstr($var_id)." AND
								  site_id 	= ".$db->qstr(DEFAULT_SITE);
							$rs = $db->Execute($sql);
							$name = $rs->fields['name'];

							$ul = eregi_replace(".", "-", $name);											
							$VAR['mail_message'] .= "\r\n\r\n";
							$VAR['mail_message'] .= "$ul";
							$VAR['mail_message'] .= "\r\n";								
							$VAR['mail_message'] .= "$name";
							$VAR['mail_message'] .= "\r\n";
							$VAR['mail_message'] .= "$ul";
							$VAR['mail_message'] .= "\r\n";
							$VAR['mail_message'] .= "$value"; 
						}
					}						
				}
			}



			if(!$this->validated)
			{
				global $smarty;	

				# set the errors as a Smarty Object
				$smarty->assign('form_validation', $all_error);	

				# set the page to be loaded
				if(!defined("FORCE_PAGE")) 
					define('FORCE_PAGE', $VAR['_page_current']); 

				global $C_vars;
				$C_vars->strip_slashes_all();

				return;
			} 

			################################################################
			## OK to send the email:

			$E['from_html']     = true;
			$E['from_name']     = $VAR['mail_name'];
			$E['from_email']    = $VAR['mail_email'];

			$db = &DB();
			$q = "SELECT * FROM ".AGILE_DB_PREFIX."setup_email WHERE
					site_id     = ".$db->qstr(DEFAULT_SITE)." AND
					id          = ".$db->qstr(DEFAULT_SETUP_EMAIL);
			$setup_email        = $db->Execute($q);

			$E['priority']      = $VAR['mail_priority'];
			$E['html']          = '0';
			$E['subject']       = $VAR['mail_subject'];
			$E['body_text']     = $VAR['mail_message'];
			$E['to_email']      = $account->fields['email'];
			$E['to_name']       = $account->fields['first_name'];

			if($setup_email->fields['type'] == 0)
			{
				$type = 0;
			}
			else
			{
				$type = 1;
				$E['server']    = $setup_email->fields['server'];
				$E['account']   = $setup_email->fields['username'];
				$E['password']  = $setup_email->fields['password'];
			} 

			if($setup_email->fields['cc_list'] != '')
				$E['cc_list']   = explode(',', $setup_email->fields['cc_list']);

			if($setup_email->fields['bcc_list'] != '')
				$E['bcc_list']  = explode(',', $setup_email->fields['bcc_list']);


			### Call the mail() or smtp() function to send
			require_once(PATH_CORE   . 'email.inc.php');
			$email = new CORE_email;

			if($type == 0)
			{
				$email->PHP_Mail($E);
			}
			else
			{
				$email->SMTP_Mail($E);
			}
		}
		else
		{
			## Error message:
			$C_debug->alert($C_translate->translate('error_req_fields','staff',''));
			$C_vars->strip_slashes_all();
			return;
		 }

		## Success message:
		$C_debug->alert($C_translate->translate('mail_sent','staff',''));

		# Stripslashes
		$C_vars->strip_slashes_all();
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

	##############################
	##		STATIC VARS         ##
	##############################

	function static_var($VAR)
	{ 
		global $smarty;
		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		$arr = $static_var->generate_form('staff', 'add', 'update');

		if(gettype($arr) == 'array')
		{ 	
			### Set everything as a smarty array, and return:
			$smarty->assign('show_static_var',		true);
			$smarty->assign('static_var',	$arr);
			return true;		 	
		}
		else
		{		 	
			### Or if no results:
			$smarty->assign('show_static_var',		false);    	
			return false;
		}
	}
}
?>