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
 * Base checkout plugin class
 *
 */
class base_checkout_plugin
{ 
	var $checkout_id; /* current checkout plugin id */
	var $name; /* plugin name */
	var $type; /* redirect, gateway, or other */
	var $recurr_only=false; /* bool */
	var $return_url; /* return url */
	var $success_url; /* decline url */
	var $support_cur; /* supported currency array */
	var $cfg;
	var $flds;
	var $eft; /* true if checkout plugin type is eft */
	var $req_all_flds=true; /* require all account fields (first/last name, address1, state/province, zip) */
	var $req_fields_arr=false; /* if req_all_fields=false, use this array to define which fields will be required */
	var $billing;  /* the billing details */
	var $account; /* the account level details */
	 
	/**
	 * Get the checkout plugin settings from the database
	 */
	function getDetails($checkout_id) {
		if(!$checkout_id) return;
		$this->checkout_id = $checkout_id;
		$db = &DB();
		$q  = "SELECT * FROM ".AGILE_DB_PREFIX."checkout WHERE site_id=".DEFAULT_SITE." AND id=".$db->qstr($checkout_id);
		$rs = $db->Execute($q);
		if($rs && $rs->RecordCount()) {
			@$this->cfg = unserialize($rs->fields["plugin_data"]);
			$this->flds = $rs->fields;
		}
	} 
	 
	/**
	 * Get country name, 
	 *
	 * @param string $field name, two_code, or three_code
	 */
	function getCountry($field, $country_id) {
		$db = &DB();
		$sql= 'SELECT '.$field.' FROM '.AGILE_DB_PREFIX.'country WHERE site_id='.DEFAULT_SITE.' AND id='.$country_id;
		$rs = $db->Execute($sql);
		if($rs == false || $rs->RecordCount() == 0)
			return "Not Defined";
		else
			return $rs->fields["$field"];
	}
			
	/**
	 * Validate the current currency is allowed
	 *
	 * @return bool
	 */
	function validate_currency($currency_iso) {
		$do = false;
		for($i=0; $i<count($this->support_cur); $i++)
		if ($currency_iso == $this->support_cur[$i])
		$do = true;
		if ( !$do ) {
			global $C_list, $C_translate;
			$C_translate->value['checkout']['currency'] = $C_list->currency_iso(DEFAULT_CURRENCY);
			$msg = $C_translate->translate('currency_not_supported','checkout','');
			$this->redirect='<script language=Javascript> alert(\''.$msg.'\');';
			if($this->type=='redirect') $this->redirect.= ' history.back();';
			$this->redirect.='</script>';
			return false;
		}
		return true;
	}
	
	/**
	 * Validate the current credit card details
	 */
	function validate_card_details(&$ret) {
		
		// validate input fields
		if($this->req_all_flds) $this->req_fields_arr = Array('first_name','last_name', 'address1', 'state', 'zip');	 
		if (is_array($this->req_fields_arr)) {
			$validate=true;
			global $VAR; 
			foreach($this->req_fields_arr as $fld) {
				if(empty($this->billing["$fld"]) && empty($this->account["$fld"]) ) {
					$VAR["{$fld}_error"]=true;
					$validate=false;
				}
			}
			if(!$validate) {  
				global $C_translate;
				$ret['status'] = 0;
				$ret['msg'] = $C_translate->translate('missing_fields','checkout','');	   		
				return false;
			}
		}
		
		// validate actual credit card details
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;
		$this->billing["cc_no"] == preg_replace('/^[0-9]/', '', $this->billing["cc_no"]);
		if (!$validate->validate_cc( $this->billing["cc_no"], false, $this->billing["card_type"], $this->cfg['card_type'] )) {
			$ret['status'] = 0;
			global $C_translate;
			$ret['msg'] = $C_translate->translate('card_invalid','checkout','');
		} elseif (!$validate->validate_cc_exp(@$this->billing["exp_month"],@$this->billing["exp_year"])) {
			$ret['status'] = 0;
			global $C_translate;
			$ret['msg'] = $C_translate->translate('card_exp_invalid','checkout','');
		} else {
			$ret['status'] = 1;
			return true;
		}
		return false;	
	}

	
	/**
	 * Validate the current eft card details
	 */
	function validate_eft_details(&$ret) { 
		// validate input fields
		if($this->req_all_flds) $this->req_fields_arr = Array('first_name','last_name', 'address1', 'city', 'state', 'zip', 'eft_check_acct_type', 'eft_trn', 'eft_check_acct', 'phone');	 
		if (is_array($this->req_fields_arr)) {
			$validate=true;
			global $VAR; 
			foreach($this->req_fields_arr as $fld) {
				if(empty($this->billing["$fld"]) && empty($this->account["$fld"]) ) {
					$VAR["{$fld}_error"]=true;
					$validate=false;
				}
			}
			if(!$validate) {   
				global $C_translate;
				$ret['status'] = 0;
				$ret['msg'] = $C_translate->translate('missing_fields','checkout','');	   
				return false;
			}
		}
		$ret['status'] = 1;
		return true;	 
	}

	
	/**
	 * Set the redirect URL and form values 
	 *
	 * @param string $url
	 * @param string $vals
	 */
	function post_vars($url,$vals) {
		$ret = '<form name="checkout_redirect" method="post" action="'.$url.'" target="_parent">';
		foreach($vals as $v)
		$ret .='<input type="hidden" name="'.$v[0].'" value="'.$v[1].'">';
		$ret .= '<script language="JavaScript">document.checkout_redirect.submit();</script>';
		$this->redirect=$ret;
	} 	
	
	/**
	 * Set array for processing order with a stored billing record
	 * "getStoredArray"
	 */
	function setBillingFromDB($account_id, $account_billing_id, $checkout_plugin_id,$rebilling=false) {
		$db=&DB();
		$ccrs=$db->Execute($sql=sqlSelect($db,"account_billing","*","account_id=::$account_id:: AND id=::$account_billing_id:: AND checkout_plugin_id=::$checkout_plugin_id::"));		 
		return $this->setBillingFromDBObj($ccrs);
	}
	
	/**
	 * Get stored array by passing in database object
	 */
	function setBillingFromDBObj(&$ccrs,$rebilling=false) {
		if($ccrs && $ccrs->RecordCount())  {
			
			// account fields
			if(empty($ccrs->fields['address1'])) {
				if(!$this->setAccountFromDB($ccrs->fields['account_id'])) return false;
			} else {
				$this->account = Array(
					'first_name'=> $ccrs->fields['first_name'],
					'last_name'=> $ccrs->fields['last_name'],
					'company'=> $ccrs->fields['company'],
					'address1'=> $ccrs->fields['address1'],
					'address2'=> $ccrs->fields['address2'],
					'city'=> $ccrs->fields['city'],
					'state'=> $ccrs->fields['state'],
					'zip'=> $ccrs->fields['zip'],
					'country_id'=> $ccrs->fields['country_id'],
					'phone'=> $ccrs->fields['phone'],
					'company' => $ccrs->fields['company'],
					'email'	=> $ccrs->fields['email']				 
				);
			}
					
			// get the card or eft details & decrypt 
			include_once(PATH_CORE.'crypt.inc.php');
			$this->billing['card_type'] = $ccrs->fields['card_type'];
			$this->billing['rebilling'] = $rebilling;
			if($this->eft || $ccrs->fields['card_type']=='eft') { 
				// stored eft 	
				$this->billing['eft_check_acct_type'] = $ccrs->fields['eft_check_acct_type'];	
				$this->billing['eft_check_checkno'] = false;
				$this->billing['eft_check_acct'] = CORE_decrypt($ccrs->fields['eft_check_acct']);
				$this->billing['eft_trn'] = CORE_decrypt($ccrs->fields['eft_trn']);
				if(!empty($ccrs->fields['ssn'])) $this->billing['ssn'] = CORE_decrypt($ccrs->fields['ssn']);
				if(!empty($ccrs->fields['dob'])) $this->billing['dob'] = CORE_decrypt($ccrs->fields['dob']);
				if(!empty($ccrs->fields['dl_no'])) $this->billing['dl_no'] = CORE_decrypt($ccrs->fields['dl_no']);
			} else {
				// stored card
				$this->billing['cc_no'] = CORE_decrypt($ccrs->fields['card_num']);
				$this->billing['exp_month'] = $ccrs->fields['card_exp_month'];	
				$this->billing['exp_year'] = $ccrs->fields['card_exp_year'];	 
			} 
			
			/* write back params to global */
			$this->setBillingParams();
			 
			return true;
		} else {
			return false;
		}		
	}
	
	/**
	 * Set account from account db table
	 */
	function setAccountFromDB($id) {
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"account","first_name,last_name,company,address1,address2,city,state,zip,country_id","id=::$id::"));
		if($rs&&$rs->RecordCount()) {
			$this->account = $rs->fields;
			return true;
		}
		return false;
	}
	
	/**
	 * Set account and billing details from $VAR (user params)
	 */
	function setBillingFromParams($VAR) { 
		global $VAR;
		@$a = $VAR['checkout_plugin_data'];		
		
		@$this->billing = Array(
			'card_type' => $a['card_type'],
			'cc_no' => $a['cc_no'],
			'ccv' => $a['ccv'],
			'exp_month' => $a['exp_month'],
			'exp_year' => $a['exp_year'],
			'eft_check_acct_type' => $a['eft_check_acct_type'],
			'eft_check_checkno' => $a['eft_check_checkno'],
			'eft_check_acct' => $a['eft_check_acct'],
			'eft_trn' => $a['eft_trn'],
			'ssn' => $a['ssn'],
			'dob' => $a['dob'],
			'dl_no' => $a['dl_no'] 
		); 
		
		@$this->account = Array(
			'first_name' => stripslashes($a['first_name']),
			'last_name' => stripslashes($a['last_name']),
			'address1' => stripslashes($a['address1']),
			'address2' => stripslashes($a['address2']),
			'city' => stripslashes($a['city']),
			'state' => stripslashes($a['state']),
			'zip' => stripslashes($a['zip']),
			'country_id' => stripslashes($a['country_id']),
			'phone' => stripslashes($a['phone']),
			'company' => stripslashes($a['company']),
			'email' => stripslashes($a['email'])  
		); 
		
		/* write back params for global */
		$this->setBillingParams();
	}
	
	/**
	 * Write the vars back to the global VAR for availibilty on the checkout plugin templates
	 */
	function setBillingParams() {
		global $VAR;
		foreach($this->billing as $key=>$val) $VAR["$key"]=$val;
		foreach($this->account as $key=>$val) $VAR["$key"]=$val;		
	}
	
	
	/**
     * Store the billing credit card entered
     */	
	function saveCreditCardDetails($VAR)  { 
		global $C_auth;
		if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow'))
			$account_id=$VAR['account_id'];
		else
			$account_id=SESS_ACCOUNT; 

		# Check if this card is already on file:
		$last_four = substr($this->billing['cc_no'],(strlen($this->billing['cc_no'])-4),4);
		$db = &DB();
		$q = "SELECT id,card_exp_month,card_exp_year FROM ".AGILE_DB_PREFIX."account_billing WHERE
			site_id 		= ".$db->qstr(DEFAULT_SITE)			." AND 
			account_id 		= ".$db->qstr($account_id)	." AND
			card_num4 		= ".$db->qstr($last_four)			." AND
			checkout_plugin_id = ".$db->qstr($this->checkout_id)	." AND
			card_type	 	= ".$db->qstr($this->billing['card_type']); 
		$rs = $db->Execute($q);
		if($rs && $rs->RecordCount()) { 
			$fields=Array('card_exp_month'=>$this->billing['exp_month'], 'card_exp_year'=>$this->billing['exp_year']);
			$db->Execute(sqlUpdate($db,"account_billing",$fields,"id = {$rs->fields['id']}"));
			return $rs->fields['id'];
		} 

		include_once(PATH_CORE.'crypt.inc.php');
		$card_num = CORE_encrypt ($this->billing['cc_no']); 
		$id = $db->GenID(AGILE_DB_PREFIX . 'account_billing_id');
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."account_billing SET
			id 					= " . $db->qstr($id) . ",
			site_id				= " . $db->qstr(DEFAULT_SITE) . ",
			account_id			= " . $db->qstr(@$account_id) . ",
			checkout_plugin_id	= " . $db->qstr(@$this->checkout_id) . ", 
			card_type			= " . $db->qstr(@$this->billing['card_type']) . ",
			card_num			= " . $db->qstr(@$card_num) . ",
			card_num4			= " . $db->qstr(@$last_four) . ",
			card_exp_month		= " . $db->qstr(@$this->billing['exp_month']) . ",
			card_exp_year		= " . $db->qstr(@$this->billing['exp_year']) . ",
			card_start_month	= " . $db->qstr(@$this->billing['start_month']) . ",
			card_start_year		= " . $db->qstr(@$this->billing['start_year']) . ",  
			first_name			= " . $db->qstr(@$this->account['first_name']) . ",
			last_name			= " . $db->qstr(@$this->account['last_name']) . ",
			address1			= " . $db->qstr(@$this->account['address1']) . ",
			address2			= " . $db->qstr(@$this->account['address2']) . ",
			city				= " . $db->qstr(@$this->account['city']) . ",
			state				= " . $db->qstr(@$this->account['state']) . ",
			zip					= " . $db->qstr(@$this->account['zip']) . ",
			country_id			= " . $db->qstr(@$this->account['country_id']) . ",
			phone				= " . $db->qstr(@$this->account['phone']) . ",
			email				= " . $db->qstr(@$this->account['email']) . ",
			company				= " . $db->qstr(@$this->account['company']) ;
		$result = $db->Execute($sql);
		if ($result) return $id; 
		return false;
	}
	
	
	/**
	 * Store the billing EFT details entered
	 */
	function saveEFTDetails($VAR) {
		global $C_auth;
		if(!empty($VAR['account_id']) && $C_auth->auth_method_by_name('checkout','admin_checkoutnow'))
			$account_id=$VAR['account_id'];
		else
			$account_id=SESS_ACCOUNT;
  
		# Check if this card is already on file:
		$last_four = substr($this->billing['eft_check_acct'],(strlen($this->billing['eft_check_acct']) - 4),4);
		$db = &DB();
		$q = "SELECT id,card_exp_month,card_exp_year FROM ".AGILE_DB_PREFIX."account_billing WHERE
			site_id 		= ".$db->qstr(DEFAULT_SITE)				." AND 
			account_id 		= ".$db->qstr($account_id)				." AND
			card_num4 		= ".$db->qstr($last_four)				." AND
			checkout_plugin_id = ".$db->qstr($this->checkout_id)	." AND
			card_type	 	= ".$db->qstr($this->billing['card_type']); 
		$rs = $db->Execute($q); 
		if($rs && $rs->RecordCount()) { 
			return $rs->fields['id'];
		}  
		
		include_once(PATH_CORE.'crypt.inc.php'); 
		$ssn=false;
		$dob=false;
		$dl_no=false;
		if(!empty($this->billing['dob'])) $dob = CORE_encrypt ($this->billing['dob']);
		if(!empty($this->billing['ssn'])) $ssn = CORE_encrypt ($this->billing['ssn']);
		if(!empty($this->billing['dl_no'])) $dl_no = CORE_encrypt ($this->billing['dl_no']);
		$check_acct = CORE_encrypt ($this->billing['eft_check_acct']);
		$trn = CORE_encrypt ($this->billing['eft_trn']);
				
		$id = $db->GenID(AGILE_DB_PREFIX . 'account_billing_id');
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."account_billing SET
			id 					= " . $db->qstr($id) . ",
			site_id				= " . $db->qstr(DEFAULT_SITE) . ",
			account_id			= " . $db->qstr($account_id) . ",
			checkout_plugin_id	= " . $db->qstr($this->checkout_id) . ", 
			card_num4			= " . $db->qstr($last_four) . ", 
			card_type			= " . $db->qstr(@$this->billing['card_type']) . ",
			eft_check_checkno	= " . $db->qstr($this->billing['eft_check_checkno']) . ",
			eft_check_acct_type	= " . $db->qstr($this->billing['eft_check_acct_type']) . ",
			eft_trn				= " . $db->qstr($trn) . ",
			eft_check_acct		= " . $db->qstr($check_acct) . ",
			dob					= " . $db->qstr($dob) . ",
			dl_no				= " . $db->qstr($dl_no) . ",  
			ssn					= " . $db->qstr($ssn) . ", 
			first_name			= " . $db->qstr(@$this->account['first_name']) . ",
			last_name			= " . $db->qstr(@$this->account['last_name']) . ",
			address1			= " . $db->qstr(@$this->account['address1']) . ",
			address2			= " . $db->qstr(@$this->account['address2']) . ",
			city				= " . $db->qstr(@$this->account['city']) . ",
			state				= " . $db->qstr(@$this->account['state']) . ",
			zip					= " . $db->qstr(@$this->account['zip']) . ",
			country_id			= " . $db->qstr(@$this->account['country_id']) . ",
			phone				= " . $db->qstr(@$this->account['phone']) . ",
			email				= " . $db->qstr(@$this->account['email']) . ",
			company				= " . $db->qstr(@$this->account['company']);
		$result = $db->Execute($sql);
		if ($result) return $id;
		return false;		
	}
}
?>