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

class account
{
	var $parent_id;

	# Open the constructor for this mod
	function account_construct()
	{
		# name of this module:
		$this->module = "account";

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

	/** add sub account */
	function sub_account_add($VAR) {
		if(!SESS_LOGGED) return false;
		$this->parent_id=SESS_ACCOUNT;         	
		if($this->add($VAR, $this)) {        	
			// add any additional groups  
			if(!empty($VAR['groups']) && is_array($VAR['groups'])) {
				global $C_auth;
				$db=&DB();         			
				foreach($VAR['groups'] as $key => $gid) {
					if($C_auth->auth_group_by_id($gid)) { 
						$fields=Array('account_id'=>$this->account_id, 'active'=>1, 'group_id'=>$gid, 'date_orig'=>time());
						$db->Execute($sql=sqlInsert($db,"account_group",$fields)); 
					}
				}	
			}  
			define('FORCE_PAGE', 'account:account');
			global $C_debug;
			$C_debug->alert("The sub-account has been added");
		}
	}    

	/** delete sub account */
	function sub_delete($VAR) {
		// return false;
		// verify perms
		if(empty($VAR['id']) || !$this->isParentAccount($VAR['id'])) { 
			 return false;
		}  

		// ok, do deletion
		include_once(PATH_MODULES.'account_admin/account_admin.inc.php');
		$aa = new account_admin;
		$VAR['account_admin_id'] = $VAR['id'];
		$aa->delete($VAR);  	        	        	
	}

	/* check if sub account auth */
	function isParentAccount($sub_account_id) {
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"account","parent_id","id=". $db->qstr($sub_account_id)." 
				AND parent_id != 0 AND parent_id IS NOT NULL AND parent_id != '' 
				AND parent_id = ". $db->qstr(SESS_ACCOUNT)));
		if($rs && $rs->RecordCount()) {
			return true;
		}
		return false;
	}        

	/** Get authorized groups */
	function get_auth_groups($VAR) {        	        
		$groups = false;
		global $smarty, $C_auth;	
		$db=&DB();

		/* get groups for this account */
		$authgrp=array();
		if(!empty($VAR['id'])) {
			$grs = $db->Execute(sqlSelect($db,"account_group","group_id","group_id>2 and active=1 and account_id=". $db->qstr($VAR['id']))); 
			if($grs && $grs->RecordCount()) {
				while(!$grs->EOF) {
					$authgrp["{$grs->fields['group_id']}"] = true;
					$grs->MoveNext();	
				}
			}
		} 

		$ids = implode(",", $C_auth->group);
		$rs = $db->Execute($sql=sqlSelect($db,"group","id,name","id in ($ids) and id > 2"));
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) { 
				$gid = $rs->fields['id']; 
				if ( (!empty($VAR['groups']) && is_array($VAR['groups']) && !empty($VAR['groups'][$gid])) 
					|| (!empty($authgrp["$gid"])) )
					 $rs->fields['checked']=true; 
				$groups[] = $rs->fields;
				$rs->MoveNext();	
			}
		}
		$smarty->assign("groups", $groups);
	}


	/**
	* Check account limitations
	*/
	function checkLimits() { 
		if(!defined('AGILE_RST_ACCOUNT') || AGILE_RST_ACCOUNT <= 0) return true; 
		$sql="SELECT count(*) as totalacct from ".AGILE_DB_PREFIX."account WHERE site_id=".DEFAULT_SITE;
		$db=&DB();
		$rs=$db->Execute($sql);  
		if($rs && $rs->RecordCount() && $rs->fields['totalacct'] <= AGILE_RST_ACCOUNT) {
			return true;
		} else {
			global $C_debug;
			$C_debug->alert("Licensed user limit of ".AGILE_RST_ACCOUNT." exceeded, operation failed.");
			return false;
		}
		return true;
	}


	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{ 
		if(!$this->checkLimits()) return false; // check account limits

		$this->account_construct();
		global $C_list, $C_translate, $C_debug, $VAR, $smarty;
		$this->validated = true;

		### Set the hidden values:
		$VAR['account_date_orig']    = time();
		$VAR['account_date_last']    = time();

		if(defined("SESS_LANGUAGE"))
		@$VAR['account_language_id'] = SESS_LANGUAGE;
		else
		@$VAR['account_language_id'] = DEFAULT_LANGUAGE;

		if(defined("SESS_AFFILIATE"))
		@$VAR['account_affiliate_id']= SESS_AFFILIATE;
		else
		@$VAR['account_affiliate_id']= DEFAULT_AFFILIATE;

		if(defined("SESS_RESELLER"))
		@$VAR['account_reseller_id'] = SESS_RESELLER;
		else
		@$VAR['account_reseller_id'] = DEFAULT_RESELLER;

		if(defined("SESS_CURRENCY"))
		@$VAR['account_currency_id'] = SESS_CURRENCY;
		else
		@$VAR['account_currency_id'] = DEFAULT_CURRENCY;

		if(defined("SESS_THEME"))
		@$VAR['account_theme_id']    = SESS_THEME;
		else
		@$VAR['account_theme_id']    = DEFAULT_THEME;

		if(defined("SESS_CAMPAIGN"))
		@$VAR['account_campaign_id'] = SESS_CAMPAIGN;
		else
		@$VAR['account_campaign_id'] = 0;

		if(!isset($VAR['account_email_type']) && @$VAR['account_email_type'] != "1")
		@$VAR['account_email_type'] = '0';

		### Determine the proper account status:
		if(DEFAULT_ACCOUNT_STATUS != '1')
		$status = '1';
		else
		$status = '0';

		## Single field login:
		if(defined('SINGLE_FIELD_LOGIN') && SINGLE_FIELD_LOGIN==true && empty($VAR['account_password'])) {
			$VAR['account_password']='none';                  
			$VAR['confirm_password']='none';
		}

		####################################################################
		### loop through the field list to validate the required fields
		####################################################################

		$type = 'add';
		$this->method["$type"] = preg_split("/,/", $this->method["$type"]);
		$arr = $this->method["$type"];
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;		
		$this->validated = true;

		while (list ($key, $value) = each ($arr))
		{
			# get the field value
			$field_var  	= $this->module . '_' . $value;
			$field_name 	= $value;

			####################################################################
			### perform any field validation...
			####################################################################

			# check if this value is unique
			if(isset($this->field["$value"]["unique"]) && isset($VAR["$field_var"]))
			{
				if(!$validate->validate_unique($this->table, $field_name, "record_id", $VAR["$field_var"]))
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->table . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),							# translate
												'error' 		=> $C_translate->translate('validate_unique',"", ""));	 				
				}
			}

			# check if the submitted value meets the specifed requirements
			if(isset($this->field["$value"]["validate"]))
			{
				if(isset($VAR["$field_var"]))
				{
					if($VAR["$field_var"] != '')
					{
						if(!$validate->validate($field_name, $this->field["$value"], $VAR["$field_var"], $this->field["$value"]["validate"]))
						{
							$this->validated = false;
							$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
														'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
														'error' 		=> $validate->error["$field_name"] );								
						}					
					}
					else
					{
						$this->validated = false;
						$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
													'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
													'error' 		=> $C_translate->translate('validate_any',"", "")); 	
					}
				}
				else
				{
					$this->validated = false;
					$this->val_error[] =  array('field' 		=> $this->module . '_' . $field_name,
												'field_trans' 	=> $C_translate->translate('field_' . $field_name, $this->module, ""),
												'error' 		=> $C_translate->translate('validate_any',"", "")); 		 																		
				}
			}
		}



		####################################################################
		### Validate the password
		####################################################################

		if(isset($VAR['account_password']) && $VAR['account_password'] != "")
		{
			if(isset($VAR['confirm_password'])  &&  $VAR['account_password'] == $VAR['confirm_password'])
			{
				$password = $VAR['account_password'];
				$smarty->assign('confirm_account_password', $VAR["account_password"]);
			}
			else
			{
				### ERROR: The passwords provided do not match!
				$smarty->assign('confirm_account_password', '');
				$this->validated = false;
				$this->val_error[] =  array('field' => 'account_confirm_password',
									'field_trans' 	=> $C_translate->translate('field_confirm_password', $this->module, ""),							# translate
									'error' 		=> $C_translate->translate('password_change_match',"account", ""));	 				

			}
		}
		else
		{
			$smarty->assign('confirm_account_password', '');
		}


		####################################################################
		### Validate that the user's IP & E-mail are not banned!
		####################################################################

		if($this->validated)
		{
			require_once(PATH_MODULES   . 'blocked_email/blocked_email.inc.php');
			$blocked_email = new blocked_email;
			if(!$blocked_email->is_blocked($VAR['account_email']))
				$this->val_error[] =  array(
				'field'         => 'account_email',
				'field_trans' 	=> $C_translate->translate('field_email', $this->module, ""),
				'error' 		=> $C_translate->translate('validate_banned_email',"", ""));	 				


			require_once(PATH_MODULES   . 'blocked_ip/blocked_ip.inc.php');
			$blocked_ip = new blocked_ip;
			if(!$blocked_ip->is_blocked(USER_IP))
				$this->val_error[] =  array(
				'field'         => 'IP Address',
				'field_trans' 	=> $C_translate->translate('ip_address', $this->module, ""),							
				'error' 		=> $C_translate->translate('validate_banned_ip',"", ""));	
		}

		// validate the tax_id
		require_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;  
		$tax_arr = @$VAR['account_tax_id'];  
		if(is_array($tax_arr)) {
			foreach($tax_arr as $country_id => $tax_id) {
				if ($country_id == $VAR['account_country_id']) { 
					$exempt = @$VAR["account_tax_id_exempt"][$country_id];
					if(!$taxObj->TaxIdsValidate($country_id, $tax_id, $exempt)) {            
						$this->validated = false; 
						$this->val_error[] =  array(
							'field'         => 'account_tax_id',
							'field_trans' 	=> $taxObj->errField,							
							'error' 		=> $C_translate->translate('validate_general', "", "")); 					
					} 
					if($exempt) 
					$VAR['account_tax_id']=false;
					else
					$VAR['account_tax_id']=$tax_id;						
				}
			}
		} 

		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;

		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form($this->module, $this->val_error);

		if($all_error != false && gettype($all_error) == 'array')
		$this->validated = false;
		else
		$this->validated = true;



		####################################################################
		### If validation was failed, skip the db insert &
		### set the errors & origonal fields as Smarty objects,
		### and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;	

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $all_error);	

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
				define('FORCE_PAGE', $VAR['_page_current']);
			}

			# Stripslashes
			global $C_vars;
			$C_vars->strip_slashes_all();

			return;
		}

		# Get default invoice options
		$db=&DB();
		$invopt=$db->Execute(sqlSelect($db,"setup_invoice","*","")); 
		if($invopt && $invopt->RecordCount()) { 
			$invoice_delivery=$invopt->fields['invoice_delivery'];
			$invoice_format=$invopt->fields['invoice_show_itemized'];
		}			

		/* hash the password */
		if(defined('PASSWORD_ENCODING_SHA'))  
			$password_encoded = sha1($password);
		else  
			$password_encoded = md5($password);

		####################################################################
		### Insert the account record
		#################################################################### 
		$this->account_id = $db->GenID(AGILE_DB_PREFIX . 'account_id');
		$validation_str = time();

		/** get parent id */ 
		$this->account_id;
		if(empty($this->parent_id)) $this->parent_id = $this->account_id;

		$sql = '
			INSERT INTO ' . AGILE_DB_PREFIX . 'account SET
			id              = ' . $db->qstr ( $this->account_id ) . ',
			site_id         = ' . $db->qstr ( DEFAULT_SITE ) . ',
			date_orig       = ' . $db->qstr ( $validation_str ) . ',
			date_last       = ' . $db->qstr ( time()) . ',
			language_id     = ' . $db->qstr ( $VAR["account_language_id"] ) . ',
			country_id      = ' . $db->qstr ( $VAR["account_country_id"] ) . ',
			parent_id    	= ' . $db->qstr ( $this->parent_id ) . ',
			affiliate_id    = ' . $db->qstr ( @$VAR["account_affiliate_id"] ) . ',
			campaign_id    	= ' . $db->qstr ( @$VAR["account_campaign_id"] ) . ',
			reseller_id     = ' . $db->qstr ( @$VAR["account_reseller_id"] ) . ',
			currency_id     = ' . $db->qstr ( $VAR["account_currency_id"] ) . ',
			theme_id        = ' . $db->qstr ( $VAR["account_theme_id"] ) . ',
			username        = ' . $db->qstr ( $VAR["account_username"] , get_magic_quotes_gpc()) . ',
			password        = ' . $db->qstr ( $password_encoded ) . ',
			status          = ' . $db->qstr ( $status ) . ',
			first_name      = ' . $db->qstr ( $VAR["account_first_name"] , get_magic_quotes_gpc()) . ',
			middle_name     = ' . $db->qstr ( $VAR["account_middle_name"], get_magic_quotes_gpc()) . ',
			last_name       = ' . $db->qstr ( $VAR["account_last_name"] , get_magic_quotes_gpc()) . ',
			company         = ' . $db->qstr ( $VAR["account_company"] , get_magic_quotes_gpc()) . ',
			title           = ' . $db->qstr ( $VAR["account_title"] , get_magic_quotes_gpc()) . ',
			email           = ' . $db->qstr ( $VAR["account_email"] , get_magic_quotes_gpc()) . ',
			address1		= ' . $db->qstr ( $VAR["account_address1"] , get_magic_quotes_gpc()) . ',
			address2		= ' . $db->qstr ( $VAR["account_address2"] , get_magic_quotes_gpc()) . ',
			city			= ' . $db->qstr ( $VAR["account_city"] , get_magic_quotes_gpc()) . ',
			state			= ' . $db->qstr ( $VAR["account_state"] , get_magic_quotes_gpc()) . ',
			zip				= ' . $db->qstr ( $VAR["account_zip"] , get_magic_quotes_gpc()) . ',
			email_type      = ' . $db->qstr ( $VAR["account_email_type"] , get_magic_quotes_gpc()). ',
			invoice_delivery= ' . $db->qstr ( @$invoice_delivery ) . ',
			invoice_show_itemized=' . $db->qstr ( @$invoice_format) . ',
			invoice_advance_gen	= ' . $db->qstr ( MAX_INV_GEN_PERIOD ) . ',
			invoice_grace	= ' . $db->qstr ( GRACE_PERIOD ) . ',
			tax_id			= ' . $db->qstr ( @$VAR['account_tax_id'] );
		$result = $db->Execute($sql);          

		####################################################################
		### error reporting:
		####################################################################

		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('account.inc.php','add', $db->ErrorMsg());

			if(isset($this->trigger["$type"])) {
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
			return;
		}

		/* password logging class */ 
		if($C_list->is_installed('account_password_history')) {
			include_once(PATH_MODULES.'account_password_history/account_password_history.inc.php');
			$accountHistory = new account_password_history();
			$accountHistory->setNewPassword($this->account_id, $password_encoded);
		}

		####################################################################
		### Add the account to the default group:
		####################################################################

		$group_id = $db->GenID(AGILE_DB_PREFIX . 'account_group_id');
		$sql = '
			INSERT INTO ' . AGILE_DB_PREFIX . 'account_group SET
			id              = ' . $db->qstr ( $group_id ) . ',
			site_id         = ' . $db->qstr ( DEFAULT_SITE ) . ',
			date_orig       = ' . $db->qstr ( time() ) . ',
			group_id        = ' . $db->qstr ( DEFAULT_GROUP ) . ',
			account_id      = ' . $db->qstr ( $this->account_id ) . ',
			active          = ' . $db->qstr ('1');
		$db->Execute($sql);


		####################################################################    	
		### Insert the static vars:
		####################################################################

		$static_var->add($VAR, $this->module, $this->account_id);


		####################################################################    	
		### Mail the user the new_account email template
		####################################################################

		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;
		if($status == "1")
		{
			$my->send('account_registration_active', $this->account_id, $this->account_id, '', '');
		} else {
			$validation_str = strtoupper($validation_str. ':' .$this->account_id);
			$my->send('account_registration_inactive', $this->account_id, '', '', $validation_str);
		}


		####################################################################
		### Add the newsletters
		####################################################################

		if(NEWSLETTER_REGISTRATION == "1")
		{
			@$VAR['newsletter_html']        = $VAR['account_email_type'];
			$VAR['newsletter_email']        = $VAR['account_email'];
			$VAR['newsletter_first_name']   = $VAR['account_first_name'];
			$VAR['newsletter_last_name']    = $VAR['account_last_name'];
			require_once(PATH_MODULES   . '/newsletter/newsletter.inc.php');
			$newsletter = new newsletter;    		
			$newsletter->subscribe($VAR, $this);
		}


		####################################################################
		### Log in the user & display the welcome message
		####################################################################

		if($status == "1")
		{
			if($this->parent_id == $this->account_id || empty($this->parent_id)) 
			{
				$C_debug->alert($C_translate->translate("user_add_active_welcome","account",""));
				if(SESSION_EXPIRE == 0) $exp = 99999;
				else $exp = SESSION_EXPIRE;
				$date_expire = (time() + (SESSION_EXPIRE * 60));

				# update the session
				$db = &DB();
				$q = "UPDATE " . AGILE_DB_PREFIX . "session
						SET
						ip= "           . $db->qstr(USER_IP) .",
						date_expire = " . $db->qstr($date_expire) . ",
						logged = "      . $db->qstr('1').",
						account_id = "  . $db->qstr($this->account_id) . "
						WHERE
						id = "          . $db->qstr(SESS) . "
						AND
						site_id = "     . $db->qstr(DEFAULT_SITE);
				$result = $db->Execute($q);

				### constants
				define('FORCE_SESS_ACCOUNT', $this->account_id);
				define('FORCE_SESS_LOGGED',  1);

				### Reload the session auth cache
				if(CACHE_SESSIONS == '1') {
					$force = true;
					$C_auth = new CORE_auth($force);
					global $C_auth2;
					$C_auth2 = $C_auth;
				}

				if(isset($VAR['_page_next']))
				define('REDIRECT_PAGE', '?_page='.$VAR['_page_next']);
				elseif(isset($VAR['_page']))
				define('REDIRECT_PAGE', '?_page='.$VAR['_page']);
			}

			####################################################################
			### Do any db_mapping
			####################################################################
			if($C_list->is_installed('db_mapping'))
			{
				include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
				$db_map = new db_mapping;
				if(!empty($password))
					$db_map->plaintext_password  = $password;
				else
					$db_map->plaintext_password  = false;                 
				$db_map->account_add ( $this->account_id );

				$db_map = new db_mapping;
				$db_map->login ( $this->account_id );
			} 

			####################################################################
			### Affiliate Auto Creation
			####################################################################                
			if(AUTO_AFFILIATE == 1 && $C_list->is_installed("affiliate"))
			{ 
				$VAR['affiliate_account_id'] = $this->account_id;
				$VAR['affiliate_template_id'] = DEFAULT_AFFILIATE_TEMPLATE;

				include_once(PATH_MODULES . 'affiliate/affiliate.inc.php');
				$affiliate = new affiliate;
				$affiliate->add($VAR, $affiliate);
			}  
		} else {
			$C_debug->alert($C_translate->translate("user_add_inactive_welcome","account",""));
			define('FORCE_PAGE', 'core:blank');
		}
	}



	##############################
	##		VIEW			    ##
	##############################
	function view($VAR)
	{	

		### Check that user is logged in:
		if(SESS_LOGGED != '1') {
			echo "Sorry, you must be logged in!";
			return false;
		}

		$this->account_construct();

		/* check for sub account */
		if(!empty($VAR['id']) && $VAR['id'] != SESS_ACCOUNT) {
			if($this->isParentAccount($VAR['id'])) {
				$VAR['account_id'] = $VAR['id'];
				global $smarty;
				$smarty->assign('issubaccount', true);
			} else {
				return false;
			} 
		} else {
			$VAR['id'] = SESS_ACCOUNT;
			$VAR['account_id'] = SESS_ACCOUNT;            	
		}

		### Retrieve the record:
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);


		### Get the static vars:
		global $smarty;
		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		$arr = $static_var->update_form('account', 'update', SESS_ACCOUNT);  
		if(gettype($arr) == 'array') { 		
			$smarty->assign('static_var', $arr);
		} else {
			$smarty->assign('static_var', false);
		}

		/* get child accounts */
		if(empty($smarty->_tpl_vars['account'][0]['parent_id']) || $smarty->_tpl_vars['account'][0]['parent_id']==$smarty->_tpl_vars['account'][0]['id']) {
			$db=&DB();
			$rs = $db->Execute(sqlSelect($db,"account","id,first_name,last_name,email,username","parent_id=". $db->qstr(SESS_ACCOUNT)));
			if($rs && $rs->RecordCount()) {
				while(!$rs->EOF) {
					$smart[] = $rs->fields;	
					$rs->MoveNext();
				}
				$smarty->assign('subaccount', $smart);
			}
		}
	}


	##############################
	##		UPDATE		        ##
	##############################
	function update($VAR)
	{
		global $VAR;

		### Check that user is logged in:
		if(SESS_LOGGED != '1')
		echo "Sorry, you must be logged in!";


		/* check for sub account */
		$issubaccount=false;
		if(!empty($VAR['account_id']) && $VAR['account_id'] != SESS_ACCOUNT) {
			if($this->isParentAccount($VAR['account_id'])) {
				$VAR['id'] = $VAR['account_id'];
				global $smarty;
				$issubaccount=true;
			} else {
				return false;
			}
		} else {
			$VAR['id'] 			= SESS_ACCOUNT;
			$VAR['account_id'] 	= SESS_ACCOUNT;            	
		}

		$VAR['account_date_last']=time();


		// validate the tax_id
		require_once(PATH_MODULES.'tax/tax.inc.php');
		$taxObj=new tax;  
		$tax_arr = @$VAR['account_tax_id'];  
		if(is_array($tax_arr)) {
			foreach($tax_arr as $country_id => $tax_id) {
				if ($country_id == $VAR['cid']) { 
					$exempt = @$VAR["account_tax_id_exempt"][$country_id];
					if(!$txRs=$taxObj->TaxIdsValidate($country_id, $tax_id, $exempt)) {            
						$this->validated = false; 
						global $C_translate;
						$this->val_error[] =  array(
							'field'         => 'account_tax_id',
							'field_trans' 	=> $taxObj->errField,							
							'error' 		=> $C_translate->translate('validate_general', "", "")); 					
					} 
					if($exempt) 
					$VAR['account_tax_id']=false;
					else
					$VAR['account_tax_id']=$tax_id;						
				}
			}
		}            

		####################################################################
		### Get required static_Vars and validate them... return an array
		### w/ ALL errors...
		####################################################################

		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;
		if(!isset($this->val_error)) $this->val_error = false;
		$all_error = $static_var->validate_form('account', $this->val_error);

		if($all_error != false && gettype($all_error) == 'array')
		$this->validated = false;
		else
		$this->validated = true;



		####################################################################
		# If validation was failed, skip the db insert &
		# set the errors & origonal fields as Smarty objects,
		# and change the page to be loaded.
		####################################################################

		if(!$this->validated)
		{
			global $smarty;	

			# set the errors as a Smarty Object
			$smarty->assign('form_validation', $all_error);	

			# set the page to be loaded
			if(!defined("FORCE_PAGE"))
			{
				define('FORCE_PAGE', $VAR['_page_current']);
			}

			return;
		}


		### Change password
		$password_changed = false;
		if(isset($VAR['account_password']) && $VAR['account_password'] != "")
		{
			if(isset($VAR['confirm_password'])  &&  $VAR['account_password'] == $VAR['confirm_password'])
			{
				$password = $VAR['account_password'];
				unset($VAR['account_password']);
				@$VAR["account_password"] = $password;
				### Alert: the password has been changed!
				global $C_debug, $C_translate;
				$C_debug->alert($C_translate->translate('password_changed','account',''));
				$password_changed=true;

				/* check if new password is ok */
				global $C_list;
				if($C_list->is_installed('account_password_history')) {
					include_once(PATH_MODULES.'account_password_history/account_password_history.inc.php');
					$accountHistory = new account_password_history();
					if(!$accountHistory->getIsPasswordOk(SESS_ACCOUNT, $VAR['account_password'], false)) {
						$C_debug->alert("The password you have selected has been used recently and cannot be used again at this time for security purposes.");
						unset($VAR["account_password"]);
						$password_changed=false;
					}
				}

			}
			else
			{
				### ERROR: The passwords provided do not match!
				global $C_debug, $C_translate;
				$C_debug->alert($C_translate->translate('password_change_match','account',''));
				unset($VAR["account_password"]);
			}
		}
		else
		{
			unset($VAR["account_password"]);
		}


		### Change theme
		if(isset($VAR['tid'])  &&  $VAR['tid'] != "")
		@$VAR["account_theme_id"] = $VAR['tid'];


		### Change Language
		if(isset($VAR['lid'])  &&  $VAR['lid'] != "")
		@$VAR["account_language_id"] = $VAR['lid'];


		### Change country
		if(isset($VAR['cid'])  &&  $VAR['cid'] != "")
		@$VAR["account_country_id"] = $VAR['cid'];


		### Change currency
		if(isset($VAR['cyid'])  &&  $VAR['cyid'] != "")
		@$VAR["account_currency_id"] = $VAR['cyid'];

		### Get the old username ( for db mapping )
		$db     = &DB();
		$sql    = 'SELECT username FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr(SESS_ACCOUNT);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$old_username = $result->fields['username'];
		}

		### Update the record
		$this->account_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);


		/* password logging class */
		if($password_changed && is_object($accountHistory)) $accountHistory->setNewPassword(SESS_ACCOUNT, $VAR['account_password'], false);



		### Update the static vars:
		$static_var->update($VAR, 'account', SESS_ACCOUNT);

		### Do any db_mapping 
		global $C_list;
		if($C_list->is_installed('db_mapping'))
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			if(!empty($password))
				$db_map->plaintext_password  = $password;
			else
				$db_map->plaintext_password  = false;                 
			$db_map->account_edit ( SESS_ACCOUNT, $old_username );
		}

		/* update groups for subaccount */
		if($issubaccount) { 
			$db=&DB();
			$db->Execute(sqlDelete($db,"account_group","group_id>2 and 
				(service_id is null or service_id=0 or service_id='') 
				and account_id=".$db->qstr($VAR['account_id']))); 
			if(!empty($VAR['groups'])) 
			{ 
				global $C_auth; 
				foreach($VAR['groups'] as $gid=>$val) {
					if($gid==$val && $C_auth->auth_group_by_id($gid)) {
						$fields=Array('account_id'=>$VAR['account_id'], 'group_id'=>$gid, 'active'=>1, 'date_orig'=>time() );
						$db->Execute(sqlInsert($db,"account_group",$fields));
					}
				}
			}
		}
	}



	##############################
	##		PASSWORD    	    ##
	##############################

	function password($VAR)
	{	
		### Set the max time between password requests:
		$LIMIT_SECONDS  = 120;

		global $C_translate, $C_debug;

		### Is the username & email both set?
		if(!isset($VAR["account_email"]) && !isset($VAR["account_username"]) )
		{
			#### ERROR: You must enter either your username or e-mail address!
			$C_debug->alert($C_translate->translate('password_reset_req','account',''));
			return;
		}
		else if($VAR["account_email"] == ""  && $VAR["account_username"] == "")
		{
			#### ERROR: You must enter either your username or e-mail address!
			$C_debug->alert($C_translate->translate('password_reset_req','account',''));
			return;
		}

		$db = &DB();

		if(isset($VAR["account_email"]) && $VAR["account_email"] != "")
		{
			$sql = ' email = '. $db->qstr($VAR["account_email"], get_magic_quotes_gpc());
		}
		else if(isset($VAR["account_username"]) && $VAR["account_username"] != "")
		{
			$sql = ' username = '. $db->qstr($VAR["account_username"], get_magic_quotes_gpc());
		}


		$q    = 'SELECT id,email,first_name,last_name FROM ' . AGILE_DB_PREFIX . 'account
				 WHERE '.  $sql . ' AND
				 site_id     = ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);

		if($result->RecordCount() == 0)
		{
			### ERROR: No matches found!
			$C_debug->alert($C_translate->translate('password_reset_no_match','account',''));
			return;
		}
		$account = $result->fields["id"];


		###################################################################
		### Check that this email has not been requested already
		### In the last 60 seconds

		$db     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					field1      = ' . $db->qstr($account);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$limit = $result->fields['date_orig'] + $LIMIT_SECONDS;

			if($limit > time())
			{
				$error1 = $C_translate->translate("password_reset_spam_limit","account","");
				$error = preg_replace('/%limit%/', "$LIMIT_SECONDS", $error1);
				$C_debug->alert( $error );
				return;
			}
			else
			{
				### Delete the old request
				$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						field1      = ' . $db->qstr($account);
				$db->Execute($sql);
			}

		}


		###################################################################
		### Ok to continue:

		$now    = md5(microtime());
		$expire = time() + (15*60);       // expires in 15 minutes


		#####################################################
		### Create the temporary DB Record:

		$db     = &DB();
		$id     = $db->GenID(AGILE_DB_PREFIX . "" . 'temporary_data_id');
		$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'temporary_data SET
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ',
					id          = ' . $db->qstr($id) . ',
					date_orig   = ' . $db->qstr(time()) . ',
					date_expire = ' . $db->qstr($expire) . ',
					field1      = ' . $db->qstr($account) . ',
					field2      = ' . $db->qstr($now);
		$result = $db->Execute($sql);


		#####################################################
		### Send the password reset email template:

		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;
		$my->send('account_reset_password', $account, '', '', $now);

		### ALERT: we have sent an email to you....
		$C_debug->alert($C_translate->translate('password_reset_sent','account',''));
	}




	##############################
	##	   PASSWORD RESET      ##
	##############################

	function password_reset($VAR)
	{	
	  global $C_translate, $C_debug, $smarty;

		### Validate that the password is set... && confirm password is set...
		if(!isset($VAR['account_password']) || !isset($VAR['confirm_password']))
		{
			### ERROR:
			$message = $C_translate->translate('password_reset_reqq','account','');
			$C_debug->alert($message);
			return;
		}
		else if ($VAR['account_password'] == "")
		{
			### ERROR:
			$message = $C_translate->translate('password_reset_reqq','account','');
			$C_debug->alert($message);
			return;
		}
		else if ($VAR['account_password'] != $VAR['confirm_password'])
		{
			### ERROR:
			$message = $C_translate->translate('password_change_match','account','');
			$C_debug->alert($message);
			return;
		}
		else
		{
			$plaintext_password = $VAR['account_password'];

			/* hash the password */
			if(defined('PASSWORD_ENCODING_SHA'))  
				$password = sha1($VAR['account_password']);
			else  
				$password = md5($VAR['account_password']);                
		}


	   if(!isset($VAR['validate']) || $VAR['validate'] == "")
		{
			### ERROR: bad link....
			$url = '<br><a href="'. URL . '?_page=account:password">' . $C_translate->translate('submit','CORE','') . '</a>';
			$message = $C_translate->translate('password_reset_bad_url','account','');
			$C_debug->alert($message . '' . $url);
			return;
		}


		### Get the temporary record from the database
		$validate = @$VAR['validate'];
		$db     = &DB();
		$sql    = 'SELECT field1,field2 FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
					site_id     = ' .   $db->qstr(DEFAULT_SITE) . ' AND
					date_expire >= '.   $db->qstr(time()) . ' AND
					field2      = ' .   $db->qstr($validate);
		$result = $db->Execute($sql);

		if($result->RecordCount() == 0)
		{
			### ERROR: no match for submitted link, invalid or expired.
			$url = '<br><a href="'. URL . '?_page=account:password">' . $C_translate->translate('submit','CORE','') . '</a>';
			$message = $C_translate->translate('password_reset_bad_url','account','');
			$C_debug->alert($message . '' . $url);
			return;
		}            		           

		$account_id = $result->fields['field1'];

		/* check if new password is ok */ 
		global $C_list;
		if($C_list->is_installed('account_password_history')) {
			include_once(PATH_MODULES.'account_password_history/account_password_history.inc.php');
			$accountHistory = new account_password_history();
			if(!$accountHistory->getIsPasswordOk($account_id, $password)) {
				$C_debug->alert("The password you have selected has been used recently and cannot be used again at this time for security purposes.");
				return;
			}
		}


		###############################################################
		### Delete the temporary record
		$sql = 'DELETE FROM ' . AGILE_DB_PREFIX . 'temporary_data WHERE
				site_id     = ' .       $db->qstr(DEFAULT_SITE) . ' AND
				field2      = ' .       $db->qstr($validate);
		$db->Execute($sql);


		###############################################################
		### Update the password record:
		$db     = &DB();
		$sql = 'UPDATE ' . AGILE_DB_PREFIX . 'account
				SET
				date_last   = ' .  $db->qstr(time()) . ',
				password    = ' .  $db->qstr($password) . '
				WHERE
				site_id     = ' .  $db->qstr(DEFAULT_SITE) . ' AND
				id          = ' .  $db->qstr($account_id);
		$db->Execute($sql);

		/* password logging class */
		if(!empty($accountHistory) && is_object($accountHistory)) $accountHistory->setNewPassword($account_id, $password);


		####################################################################
		### Get the old username ( for db mapping )

		$db     = &DB();
		$sql    = 'SELECT username FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($account_id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$old_username = $result->fields['username'];
		}

		####################################################################
		### Do any db_mapping
		####################################################################
		global $C_list;
		if($C_list->is_installed('db_mapping'))
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping; 
			$db_map->plaintext_password  = $plaintext_password;                
			$db_map->account_edit ( $account_id, $old_username );
		}


		### Return the success message:
		$C_debug->alert($C_translate->translate('password_update_success','account',''));
		$smarty->assign('pw_changed', true);
	}	



	##############################
	##	VERIFY ACCOUNT          ##
	##############################

	function verify($VAR)
	{
		global $C_debug, $C_translate, $smarty;

		### Validate $verify is set...
		if(!isset($VAR['verify']) || $VAR['verify'] == "")
		{
			### Error: please use the form below ...
			$smarty->assign('verify_results', false);
			return;
		}

		@$verify = explode(':', $VAR['verify']);

		### Validate the $verify string....
		$db     = &DB();
		$sql    = 'SELECT id,username,status FROM ' . AGILE_DB_PREFIX . 'account WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr(@$verify[1]) . ' AND
					date_orig   = ' . $db->qstr(@$verify[0]);
		$result = $db->Execute($sql);
		if($result->RecordCount() == 0)
		{
			### Error: please use the form below ...
			$smarty->assign('verify_results', false);
			return;
		}


		### Check the status:
		$status     = $result->fields['status'];
		$username   = $result->fields['username'];
		if($status == "1")
		{
			### Account already active!
			$smarty->assign('verify_results', true);
			return;
		}

		### Update the account status
		$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'account SET
					status      = ' . $db->qstr("1") . '
					WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr(@$verify[1]);
		$result = $db->Execute($sql);


		### Account now active!
		$smarty->assign('verify_results', true);


		### Return the success message:
		$C_debug->alert($C_translate->translate('password_update_success','account',''));


		####################################################################
		### Do any db_mapping
		####################################################################
		global $C_list;
		/*
		if($C_list->is_installed('db_mapping'))
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->account_edit ( $VAR['verify'], $username );
		}
		*/ 
		if($C_list->is_installed('db_mapping') )
		{
			include_once ( PATH_MODULES . 'db_mapping/db_mapping.inc.php' );
			$db_map = new db_mapping;
			$db_map->plaintext_password  = false;                 
			$db_map->account_add ( $verify[1] );
		}             
	}




	##############################
	##	VERIFY ACCOUNT          ##
	##############################

	function verify_resend($VAR)
	{
		global $C_translate, $C_debug;

		### Is the username & email both set?
		if(!isset($VAR["account_email"]) && !isset($VAR["account_username"]) )
		{
			#### ERROR: You must enter either your username or e-mail address!
			$C_debug->alert($C_translate->translate('verify_resend_req','account',''));
			return;
		}
		else if($VAR["account_email"] == ""  && $VAR["account_username"] == "")
		{
			#### ERROR: You must enter either your username or e-mail address!
			$C_debug->alert($C_translate->translate('verify_resend_req','account',''));
			return;
		}

		$db = &DB();

		if(isset($VAR["account_email"]) && $VAR["account_email"] != "")
		{
			$sql = ' email = '. $db->qstr($VAR["account_email"], get_magic_quotes_gpc());
		}
		else if(isset($VAR["account_username"]) && $VAR["account_username"] != "")
		{
			$sql = ' username = '. $db->qstr($VAR["account_username"], get_magic_quotes_gpc());
		}


		$q    = 'SELECT id,date_orig,status,email,first_name,last_name FROM ' . AGILE_DB_PREFIX . 'account
				 WHERE '.  $sql . ' AND
				 site_id     = ' . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);

		if($result->RecordCount() == 0)
		{
			### ERROR: No matches found!
			$C_debug->alert($C_translate->translate('password_reset_no_match','account',''));
			return;
		}
		$account = $result->fields["id"];
		$status  = $result->fields["status"];
		$validation_str = strtoupper($result->fields['date_orig']. ':' . $result->fields['id']);   	

		if($status == "1")
		{
			### ERROR: This account is already active!
			$C_debug->alert($C_translate->translate('verify_resend_active','account',''));
			return;
		}

		### Resend the pending email:
		require_once(PATH_MODULES   . 'email_template/email_template.inc.php');
		$my = new email_template;
		$my->send('account_registration_inactive', $account, $account, '', $validation_str);		 		

		### Notice that the email is sent:
		$C_debug->alert($C_translate->translate("user_add_inactive_welcome","account",""));

	}


	##############################
	##		STATIC VARS         ##
	##############################

	function static_var($VAR)
	{
		global $smarty;
		require_once(PATH_CORE   . 'static_var.inc.php');
		$static_var = new CORE_static_var;


		if(preg_match('/search/', $VAR['_page']))
		$arr = $static_var->generate_form('account', 'add', 'search');
		else
		$arr = $static_var->generate_form('account', 'add', 'update');

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