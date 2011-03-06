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
	
class CORE_validate
{	
	function convert($field, $data, $type)
	{
		switch($type)
		{
			case "date":
				return $this->convert_date ($data,$field);
				break;
			case "time":
				return $this->convert_time ($data,$field);
				break;
			case "date-time":
				return $this->convert_date_time ($data,$field);
				break;
			case "date-now":
				return $this->convert_date_now($data,$field);
				break;					
			case "md5":
				return $this->convert_md5 ($data,$field);
				break;
			case "sha":
				return $this->convert_sha ($data,$field);
				break;					
			case "rc5":
				return $this->convert_rc5 ($data,$field);
				break;
			case "crypt":
				return $this->convert_crypt ($data,$field);
				break;
			case "encrypt":
				return $this->convert_encrypt ($data,$field);
				break;					
			case "gpg":
				return $this->convert_gpg ($data,$field);
				break;																														
			case "pgp":
				return $this->convert_pgp ($data,$field);
				break;	
			case "array":
				return $this->convert_array ($data,$field);
				break;
			case "html":
				return $this->convert_html ($data,$field);
				break;
		}
	}


	# convert DEFAULT_TIME_FORMT to unix time stamp
	function convert_time ($data,$field)
	{
		return strtotime($data);
	}		

	# convert DEFAULT_DATE_FORMT to unix time stamp
	function convert_date ($date,$field)
	{
		if($date == '0' || $date == '')
		  return '';

		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);

		for($i=0; $i<3; $i++)
		{
			if($Arr_format[$i] == 'd')
				$day = $Arr_date[$i];

			if($Arr_format[$i] == 'm')
				$month = $Arr_date[$i];

			if($Arr_format[$i] == 'Y')
				$year = $Arr_date[$i];
		}

		$timestamp = mktime(0, 0, 0, $month, $day, $year);
		return $timestamp;	
	}

	# convert DEFAULT_DATE_TIME_FORMT to unix time stamp
	function convert_date_time ($date,$field)
	{
		if($date == '0' || $date == '')
		  return '';

		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);

		for($i=0; $i<3; $i++) {
			if($Arr_format[$i] == 'd') if(!empty($Arr_date[$i])) $day = $Arr_date[$i];
			if($Arr_format[$i] == 'm') if(!empty($Arr_date[$i])) $month = $Arr_date[$i];                    
			if($Arr_format[$i] == 'Y') if(!empty($Arr_date[$i])) $year = $Arr_date[$i]; 
		}

		if(empty($day)) $day = date('d');
		if(empty($month)) $month = date('m');
		if(empty($year)) $year = date('Y');       

		@$timestamp = mktime( date("H"), date("i"), date("s"), $month, $day, $year);
		return $timestamp;	
	}

	function convert_date_now ($data,$field)
	{
		return time();
	}		

	function convert_md5 ($data,$field)
	{
		if($data != "")
			return md5($data);
		else
			return '';
	}	 

	function convert_rc5 ($data,$field)
	{
		if($data != "")
			return rc5($data);
		else
			return '';
	}						

	function convert_sha ($data, $field)
	{
		if($data != "")
			return sha1($data); 
		else
			return '';
	}

	function convert_crypt ($data,$field)
	{
		if($data != "")
			return crypt($data);
		else
			return '';
	}	

	function convert_encrypt ($data,$field)
	{
		if($data != "") {
			include_once(PATH_CORE.'crypt.inc.php');
			return CORE_encrypt ($data);
		}
		else
		{
			return '';
		}
	}		

	function convert_gpg ($data,$field)
	{
		if($data != "")
			return gpg($data);
		else
			return '';
	}	

	function convert_pgp ($data,$field)
	{
		if($data != "")
			return pgp($data);
		else
			return '';
	}	


	function convert_array ($data,$field)
	{
		if($data != "")
			return serialize($data);
		else
			return serialize(Array(""));
	}

	function convert_html ($data,$field)
	{
		if($data == "")
			return "";
		else
			return htmlspecialchars($data);;
	}


	function validate($field, $arr, $data, $type)
	{
		if(isset($arr["min_len"]))
		{
			 if($arr["min_len"] > 1)
			 {
				global $C_translate;
				if (strlen($data) < $arr["min_len"]) {
					$C_translate->value["CORE"]["min_length"] = $arr["min_len"];
					$this->error[$field] = $C_translate->translate('validate_min_length','CORE','');
					return FALSE;
				}
			}
		}
		if(isset($arr["max_len"]))
		{	
			if($arr["max_len"] > 1)
			{
				global $C_translate;
				if (strlen($data) > $arr["max_len"]) {
					$C_translate->value["CORE"]["max_length"] = $arr["max_len"];
					$this->error[$field] = $C_translate->translate('validate_max_length','CORE','');					
					return FALSE; 	 				
				}
			}
		}

		switch($type)
		{
			case "email":
				return $this->validate_email ($data,$field);
				break;
			case "date":
				return $this->validate_date ($data,$field);
				break;
			case "time":
				return $this->validate_time ($data,$field);
				break;
			case "date-time":
				return $this->validate_date_time ($data,$field);
				break;
			case "address":
				return $this->validate_address ($data,$field);
				break;
			case "zip":
				return $this->validate_zip ($data,$field);
				break;
			case "phone":
				return $this->validate_phone ($data,$field);
				break;
			case "cc":
				return $this->validate_cc ($data,$field, false, false);
				break;
			case "check":
				return $this->validate_check ($data,$field);
				break;
			case "numeric":
				return $this->validate_numeric ($data,$field);
				break;
			case "alphanumeric":
				return $this->validate_alphanumeric ($data,$field);
				break;
			case "non_numeric":
				return $this->validate_non_numeric ($data,$field);
				break;
			case "float":
				return $this->validate_float ($data,$field);
				break;
			case "any":
				return $this->validate_any ($data,$field);
				break;		
			case "domain":
				return $this->validate_domain ($data,$field);
				break;
			case "ip":
				return $this->validate_ip ($data,$field);
				break;													
			case "password":
				return $this->validate_password ($data,$field);
				break;						
		}
	}


	function validate_email($data,$field)
	{
		if(preg_match("/^[a-z0-9\._-]+@+[a-z0-9\._-]+\.+[a-z]{2,4}$/i", $data)) 
		{
			return TRUE; 
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_email','CORE','');						
			return FALSE; 
		}
	}


	function validate_ip($data,$field)
	{
		$ip = $data;
		$valid = TRUE; 

		if(preg_match("/^((127)|(192)|(10).*)$/", "$ip")) {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_ip','CORE','');
			return FALSE;
		}

		$ip = explode(".", $ip);
		if(count($ip)!=4) {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_ip','CORE','');
			return FALSE;
		}

		foreach($ip as $block)  
			if(!is_numeric($block) || $block>255 || $block<1)  
				$valid = FALSE; 

		if($valid == FALSE) {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_ip','CORE','');
			return FALSE;			
		} else {
			return TRUE;
		} 	
	}


	function validate_domain($data,$field)
	{ 
		if (!preg_match('#^[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $data)) { 
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_domain','CORE','');
			return FALSE; 
		} else { 
			return true; 
		}
	} 


	function validate_date($data,$field)
	{	

		if($data == '0' || $data == '')
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_date','CORE','');								
			return false;
		}

		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $data);

		if(!gettype($Arr_date) == 'array'  ||  count($Arr_date) != 3)
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_date','CORE','');								
			return false;
		}

		for($i=0; $i<3; $i++)
		{
			if($Arr_format[$i] == 'd')
				$day = $Arr_date[$i];

			if($Arr_format[$i] == 'm')
				$month = $Arr_date[$i];

			if($Arr_format[$i] == 'Y')
				$year = $Arr_date[$i];
		}

		@$timestamp = mktime(0, 0, 0, $month, $day, $year);

		$check_ts = mktime(0,0,0,"1","1","1979");

		if($timestamp >= $check_ts)
		{
			return true;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_date','CORE','');								
			return false;
		}            	
	}

	function validate_time($data,$field)
	{
		return TRUE;
	}

	function validate_date_time($data,$field)
	{
		return TRUE;
	}					


	function validate_address($data,$field)
	{
		if(@strlen($data) >= 2 && preg_match('/[0-9]{1,}/i', $data) && preg_match('/[a-z]{1,}/i', $data)) {
			return TRUE;
		} else {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_address','CORE','');					
			return FALSE;
		}
	}	


	function validate_zip($data,$field)
	{
		if(@strlen($data) >= 4   &&   preg_match('/[0-9a-zA-Z-]{4,}/i', $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_zip','CORE','');	
			return FALSE;
		}
	}	


	function validate_phone($data,$field)
	{
		if(@strlen($data) > 9  &&   preg_match('/[0-9()-]{10,}/i', $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_phone','CORE','');	
			return FALSE;
		}
	}		

	function validate_fax($data,$field)
	{	
		if(@strlen($data) > 9  &&   preg_match('/[0-9()-]{10,}/i', $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_fax','CORE','');	
			return FALSE;
		}
	}			

	function validate_check($data,$field)
	{		
		return TRUE;
	}	


	function validate_numeric($data,$field)
	{
		if(preg_match("/^[0-9]{1,}$/i", $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_numeric','CORE','');	
			return FALSE;
		}			
	}	

	function validate_alphanumeric($data,$field)
	{
		if(preg_match("/^[0-9a-zA-Z-]{1,}$/i", $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_alphanumeric','CORE','');	
			return FALSE;
		}
	}				

	function validate_non_numeric($data,$field)
	{
		if(!preg_match("/[0-9]{1,}/i", $data))
		{				
			return TRUE;				
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_non_numeric','CORE','');				
			return FALSE;
		}							
	}	

	function validate_float($data,$field)
	{
		if(preg_match("/^[0-9\.]{1,}$/i", $data))
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_float','CORE','');	
			return FALSE;
		}			
	}

	function validate_any ($data,$field)
	{
		if($data != "")
		{
			return TRUE;
		}
		else
		{
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_any','CORE','');	
			return FALSE;
		}	
	}			


	function validate_unique ($table, $field, $id, $value)	
	{		
		if($value == '') return TRUE;
		$db = &DB();
		$value = $db->qstr($value);
		$q = "SELECT $field FROM ".AGILE_DB_PREFIX."$table WHERE $field = ".$value." ";			
		if($id != '' && $id != 'record_id') 
			$q .= "AND id != " . $db->qstr($id);		 
		$q .= "AND site_id = " . $db->qstr(DEFAULT_SITE);
		$result = $db->Execute($q);	  
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('validate.inc.php','validate_unique', $db->ErrorMsg());
			return FALSE;
		}
		else
		{								
			if($result->RecordCount() == 0)
			return TRUE;
			else
			return FALSE;							
		}
	}

	function validate_cc_exp($month, $year)
	{
		if (preg_replace("/^0/i", "", $year) > preg_replace("/^0/i","", date("y")))
		return true;
		elseif ( preg_replace("/^0/i","", $year) == preg_replace("/^0/i","", date("y")) &&
				 preg_replace("/^0/i","", $month) >= preg_replace("/^0/i","", date("m")))
		return true;
		else
		return false;
	}


	function validate_cc( $ccNum, $field, $card_type, $card_type_accepted_arr )
	{
		$v_ccNum = false;


		if ($card_type == "visa" || !$card_type) {
			// VISA
			if ( preg_match("/^4[0-9]{12}([0-9]{3})?$/", $ccNum) ) {
				$v_ccNum = true;
				$c_type  = 'visa';
			}
		}
		else if ($card_type == "mc" || !$card_type) {
			// MC
			if ( preg_match("/^5[1-5][0-9]{14}$/", $ccNum) )  {
				$v_ccNum = true;
				$c_type  = 'mc';
			}
		}
		else if ($card_type == "amex" || !$card_type) {
			// AMEX
			if ( preg_match("/^3[47][0-9]{13}$/", $ccNum) )  {
				$v_ccNum = true;
				$c_type  = 'amex';
			}
		}
		else if ($card_type == "discover" || !$card_type) {
			// DISCOVER
			if ( preg_match("/^6011[0-9]{12}$/", $ccNum) )  {
				$v_ccNum = true;
				$c_type  = 'discover';
			}
		}
		else if ($card_type == "delta" || !$card_type) {
			// DELTA ?
			if ( preg_match ( "/^4(1373[3-7]|462[0-9]{2}|5397[8-9]|".
			"54313|5443[2-5]|54742|567(2[5-9]|3[0-9]|4[0-5])|".
			"658[3-7][0-9]|659(0[1-9]|[1-4][0-9]|50)|844(09|10)|".
			"909[6-7][0-9]|9218[1-2]|98824)[0-9]{10}$/i" ) ) {
				$v_ccNum = true;
				$c_type  = 'delta';
			}
		}
		else if ($card_type == "solo" || !$card_type) {
			// SOLO  ?
			if ( preg_match("/^6(3(34[5-9][0-9])|767[0-9]{2})[0-9]{10}([0-9]{2,3})?$/") ) {
				$v_ccNum = true;
				$c_type  = 'solo';
			}
		}
		else if ($card_type == "switch" || !$card_type) {
			// SWITCH  ?
			if ( preg_match('/^49(03(0[2-9]|3[5-9])|11(0[1-2]|7[4-9]|8[1-2])|36[0-9]{2})[0-9]{10}([0-9]{2,3})?$/', $ccNum) ||
				 preg_match('/^564182[0-9]{10}([0-9]{2,3})?$/', $ccNum) ||
				 preg_match('/^6(3(33[0-4][0-9])|759[0-9]{2})[0-9]{10}([0-9]{2,3})?$/', $ccNum) )  {
					 $v_ccNum = true;
					 $c_type  = 'switch';
			}
		}
		else if ($card_type == "jcb" || !$card_type) {
			// JCB
			if ( preg_match("/^(3[0-9]{4}|2131|1800)[0-9]{11}$/", $ccNum) )  {
				$v_ccNum = true;
				$c_type  = 'jcb';
			}
		}
		else if ($card_type == "diners" || !$card_type) {
			// DINERS
			if ( preg_match("/^3(0[0-5]|[68][0-9])[0-9]{11}$/", $ccNum) ) {
				$v_ccNum = true;
				$c_type  = 'diners';
			}
		}
		else if ($card_type == "carteblanche" || !$card_type) {
			// CARTEBLANCHE
			if ( preg_match("/^3(0[0-5]|[68][0-9])[0-9]{11}$/", $ccNum) ) {
				$v_ccNum = true;
				$c_type  = 'carteblanche';
			}
		}
		else if ($card_type == "enroute" || !$card_type) {
			// ENROUTE
			if (( (substr($ccNum, 0, 4) == "2014" || substr($ccNum, 0, 4) == "2149") && (strlen($ccNum) == 15) )) {
				$v_ccNum = true;
				$c_type  = 'enroute';
			}
		}

		// validate accepted card type
		if ($card_type_accepted_arr != false & $v_ccNum) {
			$v_ccNum = false;
			for($i=0; $i<count($card_type_accepted_arr); $i++)
				if($card_type_accepted_arr[$i] == $c_type) $v_ccNum = true;
		}

		if ( $v_ccNum )
		{
			return TRUE;
		} else {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_cc','CORE','');
			return FALSE;
		}
	} 

	/**
	 * Strong password validation
	 */ 
	function validate_password($data, $field) {        	

		// force numbers and letters
		if(!preg_match("/[0-9]{1,}/i", $data) || !preg_match("/[a-z]{1,}/i", $data)) {
			global $C_translate;
			$this->error[$field] = $C_translate->translate('validate_password','CORE',''); 
			return false;
		}

		global $VAR; 
		$exclude = array();

		// not in email eq to email
		if(!empty($VAR['account_email'])) {
			@$e=explode("@",$VAR['account_email']);
			@$exclude[] = $e[0];
			@$exclude[] = $VAR['account_email'];
		} elseif(!empty($VAR['account_admin_email'])) {
			@$e=explode("@",$VAR['account_admin_email']);
			@$exclude[] = $e[0];
			@$exclude[] = $VAR['account_admin_email'];
		}

		// not eq to name (first or last) 
		@$exclude[] = $VAR['account_username']; 

		@$exclude[] = $VAR['account_admin_username'];        	
		@$exclude[] = $VAR['account_admin_first_name'];
		@$exclude[] = $VAR['account_admin_last_name'];

		// not eq to initials
		if(!empty($VAR['account_first_name']) && !empty($VAR['account_last_name'])) {
			@$exclude[] = $VAR['account_first_name'];
			@$exclude[] = $VAR['account_last_name'];         		
			@$exclude[] = substr($VAR['account_first_name'], 0, 1) . substr($VAR['account_middle_name'], 0, 1). substr($VAR['account_last_name'], 0, 1);
		} else if(!empty($VAR['account_admin_first_name']) && !empty($VAR['account_admin_last_name'])) {
			@$exclude[] = $VAR['account_admin_first_name'];
			@$exclude[] = $VAR['account_admin_last_name'];         		
			@$exclude[] = substr($VAR['account_admin_first_name'], 0, 1) . substr($VAR['account_admin_middle_name'], 0, 1).  substr($VAR['account_admin_last_name'], 0, 1);
		}

		// check against data
		foreach($exclude as $bad_data) {        	
			if(!empty($bad_data) && preg_match('/'.$bad_data.'/i',$data)) {  		
				global $C_translate;
				$this->error[$field] = $C_translate->translate('validate_password','CORE','');     
				return false;   			
			}
		}
		return true;
	}

	function DateToEpoch($format,$date)
	{
		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);
		for($i=0; $i<3; $i++)
		{
			if($Arr_format[$i] == 'd') $day = $Arr_date[$i];
			if($Arr_format[$i] == 'm') $month = $Arr_date[$i];
			if($Arr_format[$i] == 'Y') $year = $Arr_date[$i];
		}
		$timestamp = mktime(0, 0, 0, $month, $day, $year);
		return $timestamp;			
	}				

	function EpochToDate($epoch)
	{
		return date(UNIX_DATE_FORMAT,$epoch);
	}
} 	
?>