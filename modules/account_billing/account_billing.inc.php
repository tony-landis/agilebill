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
	
class account_billing
{ 
	/**
	 * Return a list of options for the drop-down menus
	 */
	function cardMenuArr(&$result, $selected=false) {
		global $C_translate;
		if($result && $result->RecordCount()) {
			while (!$result->EOF) {
				$arr["{$result->fields["id"]}"] = $C_translate->translate('card_type_'.$result->fields["card_type"],'checkout','') .
				' ...' . $result->fields["card_num4"];
				if($result->fields['card_type'] != 'eft')
				$arr["{$result->fields["id"]}"] .= ' | Exp: ' . $result->fields["card_exp_month"] . '/' . $result->fields["card_exp_year"];
				$result->MoveNext();
			}
			return $arr; 
		}
		return false;
	}
 
	/**
     * Get a list of cards of file for a specific user and checkout plugin 
     */
	function list_on_file($VAR) {
		global $C_auth;
		if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow')) $account_id=$VAR['account_id']; else $account_id=SESS_ACCOUNT;
		if(empty($VAR['option'])) return false; else $checkout_plugin_id=$VAR['option'];
		$db=&DB();
		$year  = preg_replace("/^20/", "", date("Y")); 
		$result = $db->Execute($sql=sqlSelect($db,"account_billing","id,card_type,card_num4,card_exp_month,card_exp_year", 
		"(card_exp_year>=::$year:: or card_type='eft') AND account_id=::$account_id:: AND checkout_plugin_id=::$checkout_plugin_id::"));
		$arr = $this->cardMenuArr($result);	
		if($arr) {
			global $smarty;
			$smarty->assign('onfile', $arr);	
		} 
	}
	
	/**
	 * Generate an admin menu of the list of cards on file...
	 */
	function menu_admin($field, $account, $default, $class, $user) {
		global $C_translate;
		if(empty($field)) $field = 'ccnum';
		$input_id = '1';  
		$return = '<select id="'. $field  .'_'. $input_id .'" name="'. $field .'">';		 
		$db = &DB();
		$sql= "SELECT * FROM ". AGILE_DB_PREFIX."account_billing WHERE site_id = ".$db->qstr(DEFAULT_SITE)." AND account_id = ".$db->qstr($account)." ORDER BY card_num4";
		$result = $db->Execute($sql);
		$arr = $this->cardMenuArr($result);	
		if($arr) {
			if($id == "all") $return .= '<option value="" selected></option>';		 
			foreach($arr as $id=>$val) {
				$return .= '<option value="'.$id.'"';
				if($default==$id) $return .= " selected";
				$return .= '>' . $val . '</option>';
			}
			$return .= '</select>';  
			if(!$user) { 
				$return .= '&nbsp;<img src="themes/' . THEME_NAME . '/images/icons/zoomi_16.gif" border="0" width="16" height="16" onclick="menu_item_view(\'account_billing\',\''.$field .'_'.$input_id.'\');">'; 
				$return .= '&nbsp;<img src="themes/' . THEME_NAME . '/images/icons/add_16.gif" border="0" width="16" height="16" onclick="menu_item_add(\'account_billing\',\''.$field .'_'.$input_id.'\');">';
			} else { 
				$return .= '&nbsp;<img src="themes/' . THEME_NAME . '/images/icons/zoomi_16.gif" border="0" width="16" height="16" onclick="document.location=\''.SSL_URL.'?_page=account_billing:user_view&id='.$default.',\';">';
			}			
		} else {
			$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
			$return .= '</select>'; 			
		} 
		echo $return;
	}	

	/**
     * Allow user to delete a card on file 
     */
	function user_delete($VAR)
	{
		# Verify the current account owns this billing record
		$dbx     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account_billing WHERE
                       id           =  ' . $dbx->qstr( @$VAR['id'] ) . ' AND
                       account_id   =  ' . $dbx->qstr( SESS_ACCOUNT ) . ' AND
                       site_id      =  ' . $dbx->qstr(DEFAULT_SITE);
		$rs = $dbx->Execute($sql);
		if (@$rs->RecordCount() == 0) return false; 

		global $C_debug, $C_translate;

		# Validate this isn't the only card on file
		$rs = $dbx->Execute(sqlSelect($dbx,"account_billing","id","id=::".$VAR['id']."::"));
		if($rs && $rs->RecordCount()) {
			$msg = $C_translate->translate('card_in_use','account_billing','');
			$C_debug->alert($msg);
			return false;
		}

		# Validate the card isn't in use
		$rs = $dbx->Execute(sqlSelect($dbx,"service","id","account_billing_id=::".$VAR['id'].":: AND account_id=::".SESS_ACCOUNT.":: AND active=1"));
		if($rs && $rs->RecordCount()) {
			$msg = $C_translate->translate('card_in_use','account_billing','');
			$C_debug->alert($msg);
			return false;
		}

		# Delete the card
		$sql = sqlDelete($dbx,"account_billing","id=::".$VAR['id'].":: AND account_id=::".SESS_ACCOUNT."::");
		$dbx->Execute($sql);
		$msg = $C_translate->translate('card_removed','account_billing','');
		$C_debug->alert($msg);

		return true;
	}

	/**
     * Task to detect credit cards that are expiring soon and e-mail the user a notice to update the card
     */
	function task($VAR) {
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$sql ='';

		for($i=1; $i<3; $i++) {
			$exp = mktime(0,0,0,date('m')+$i,date('d'),date('Y'));
			$month = date("m",$exp);
			$year  = preg_replace("/^20/", "", date("Y",$exp));
			if(!empty($sql)) $sql .= " OR ";
			$sql .= " ( card_exp_month = '$month' AND card_exp_year = '$year' ) ";
		}

		$db = &DB();
		$rs = $db->Execute($qq=sqlSelect($db,"account_billing", "id,account_id,notify_count", " ( $sql ) AND notify_count < 4"));
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF)  {
				$email = new email_template;
				$email->send('account_billing_exp_soon', $rs->fields['account_id'], $rs->fields['id'],'','');
				$fields=Array('notify_count'=> $rs->fields["notify_count"]+1);
				$db->Execute(sqlUpdate($db,"account_billing", $fields, "id = {$rs->fields['id']}"));
				$rs->MoveNext();
			}
		}
	}


	/** Get most recent account billing id */
	function default_billing($account_id)
	{
		$db = &DB();
		$sql = "SELECT id,checkout_plugin_id FROM ".AGILE_DB_PREFIX."account_billing WHERE
			        account_id 	= " . $db->qstr($account_id) . " AND
			        site_id 	= " . $db->qstr(DEFAULT_SITE); 
		$billing = $db->Execute($sql);
		if($billing->RecordCount() > 0)
		{
			$ret['billing_id'] = $billing->fields['id'];
			$ret['checkout_plugin_id'] = $billing->fields['checkout_plugin_id'];
		} else {
			$ret['billing_id'] = false;
			$ret['checkout_plugin_id'] = false;
		}
		return $ret;
	}
 
 
	function add($VAR)
	{
		global $C_debug, $C_translate;

		# Get the last 4 digits of the cc:
		@$VAR['account_billing_card_num4'] = substr($VAR['account_billing_card_num'],(strlen($VAR['account_billing_card_num']) - 4),4);

		# Validate the req fields
		if(empty($VAR['account_billing_card_exp_month']) || empty($VAR['account_billing_card_exp_year']) || empty($VAR['account_billing_card_num']))
		{
			$msg = $C_translate->translate('val_missing','account_billing','');
			$C_debug->alert($msg);
			return false;
		}

		# Validate the exp date
		if(mktime(0,0,0,$VAR['account_billing_card_exp_month'], date('d'), $VAR['account_billing_card_exp_year']) <= time())
		{
			$msg = $C_translate->translate('val_exp','account_billing','');
			$C_debug->alert($msg);
			return false;
		}

		# Validate the card against the card type
		include_once(PATH_CORE.'validate.inc.php');
		$validate = new CORE_validate;
		if(!$validate->validate_cc( $VAR['account_billing_card_num'], 'card_num', $VAR['account_billing_card_type'], false ) )
		{
			$msg = $C_translate->translate('val_cc','account_billing','');
			$C_debug->alert($msg);
			return false;
		}

		$this->construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}
 
	function view($VAR)
	{
		global $C_debug, $C_translate;
		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}
 
	function update($VAR)
	{ 
		global $C_translate,$C_debug;
		
		/* load database object */
		$db = new CORE_database;		
		$this->construct();		
		$type = "update";
		  
		/* conditional fields for cc/eft */		
		$dbx=&DB();
		$rs = $dbx->Execute(sqlSelect($dbx,"account_billing","card_type,id,checkout_plugin_id","id=::{$VAR['id']}::"));
		if(!$rs || !$rs->RecordCount()) return false;  
		
		$billing_id = $rs->fields['id'];
		$checkout_plugin_id = $rs->fields['checkout_plugin_id'];
					
		if($rs->fields['card_type'] == 'eft') {
			/* EFT   */ 
			$this->method["$type"] = $db->ignore_fields(
				array('card_exp_month', 'card_exp_year', 'card_num' ), 
				$this->method["$type"]
			);
			
			/* last four */
			@$VAR['account_billing_card_num4'] = substr($VAR['account_billing_eft_check_acct'],(strlen($VAR['account_billing_eft_check_acct']) - 4),4);			
				 
		} else {
			/* CC   */
			
			# Validate the exp date
			if(mktime(0,0,0,$VAR['account_billing_card_exp_month'], date('d'), $VAR['account_billing_card_exp_year']) <= time())
			{
				$msg = $C_translate->translate('val_exp','account_billing','');
				$C_debug->alert($msg);
				return false;
			}
			
			# Validate the card against the card type
			include_once(PATH_CORE.'validate.inc.php');
			$validate = new CORE_validate;
			if(!$validate->validate_cc( @$VAR['account_billing_card_num'], 'card_num', @$VAR['account_billing_card_type'], false ) )
			{
				$msg = $C_translate->translate('val_cc','account_billing','');
				$C_debug->alert($msg);
				return false;
			}		
			
			$this->method["$type"] = $db->ignore_fields(
				array('eft_trn', 'eft_check_acct'), 
				$this->method["$type"]
			);
			
			/* last four */
			@$VAR['account_billing_card_num4'] = substr($VAR['account_billing_card_num'],(strlen($VAR['account_billing_card_num']) - 4),4);								
		} 
	  
		if( $db->update($VAR, $this, $type) )
		{ 
			# Update any invoices using this billing record
			$dba = &DB();
			$sql = "UPDATE ".AGILE_DB_PREFIX."invoice SET
						checkout_plugin_id 	= " . $dba->qstr($checkout_plugin_id) . "
						WHERE site_id				= " . $dba->qstr(DEFAULT_SITE) . "
						AND account_billing_id	= " . $dba->qstr($billing_id);
			$result = $dba->Execute($sql);     			
			return true;
		}
		return false;
	}
 
	function delete($VAR) {
		$this->construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}
 
	function search_form($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}
  
	function search($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}
 
	function user_search($VAR) {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED) return false; 

		# Lock the account_id
		$VAR['account_billing_account_id'] = SESS_ACCOUNT;

		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}
 
	function search_show($VAR) {
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}
 
	function user_search_show($VAR) {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED) return false; 
		$this->construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}
 
	function user_view($VAR) 	{
		# Check that the correct account owns this billing record
		$dbx     = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account_billing WHERE
                       id           =  ' . $dbx->qstr( @$VAR['id'] ) . ' AND
                       account_id   =  ' . $dbx->qstr( SESS_ACCOUNT ) . ' AND
                       site_id      =  ' . $dbx->qstr(DEFAULT_SITE);
		$rs = $dbx->Execute($sql);
		if (@$rs->RecordCount() == 0) return false; 

		$this->construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}
 
	function user_update($VAR) { 
		if(!SESS_LOGGED) return false;
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"account_billing","card_type","id=::{$VAR['id']}:: and account_id=".SESS_ACCOUNT));
		if(!$rs || !$rs->RecordCount()) return false;  
		$result=$this->update($VAR, $this);
		global $VAR; $VAR['_page'] = 'account_billing:user_view';
		if($result) {
			global $C_debug;
			$C_debug->alert("Your billing details have been updated.");
		}
	}

	
	# Open the constructor for this mod
	function construct()
	{
		# name of this module:
		$this->module = "account_billing";

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
}
?>