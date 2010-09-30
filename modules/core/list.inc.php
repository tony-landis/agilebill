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
	
class CORE_list
{
	var $id;

	function menu($input_id, $name, $table, $field, $id, $class, $all=false) {
		global $C_translate;
		if($all == true || $id == 'all') $all = true;
		if(!isset($this->id)) $this->id = 100;
		if($input_id <= 0 && $input_id != 'no') $input_id = $this->id++;
		$db = &DB();
		$sql= "SELECT id, $field FROM ".AGILE_DB_PREFIX."$table WHERE site_id = '" . DEFAULT_SITE . "' ORDER BY $field";
		$result = $db->Execute($sql);
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','menu', $db->ErrorMsg());
		} else {
			$return = '<select id="'. $field  .'_'. $input_id .'" name="'. $name .'" class="'.$class.'">';
			if($all)
				$return .= '<option value=""></option>';
			$i = 0;
			while (!$result->EOF) {
				$return .= '<option value="' . $result->fields["id"] . '"';
				if($id == $result->fields["id"])
					$return .= "selected";
				$return .= '>' . $result->fields["$field"] . '</option>
				';
				$i++;
				$result->MoveNext();
			}
			if($i==0)
				$return .= '<option value="">'. $C_translate->translate('lists_none_defined','CORE','').'</option>';
			$return .= '</select>';
			if($i > 0 && $input_id != 'no')
				 $return .= '&nbsp;<img src="themes/' . THEME_NAME . '/images/icons/zoomi_16.gif" border="0" width="16" height="16" onclick="menu_item_view(\''.$table.'\',\''.$field .'_'.$input_id.'\');">';
			echo $return;
		}
	}

	function decrypt($data) {
		include_once(PATH_CORE.'crypt.inc.php');
		return CORE_decrypt($data);
	}

	function menu_cc_admin($field, $account, $default, $class, $user=false) {
		include_once(PATH_MODULES . 'account_billing/account_billing.inc.php');
		$acct_bill = new account_billing;
		echo $acct_bill->menu_admin($field, $account, $default, $class, $user);
	} 

	function menu_multi($default, $name, $table, $field, $id, $max, $class) 	{
		include_once(PATH_CORE . 'list_menu_multi.inc.php');
		echo list_menu_multi($default, $name, $table, $field, $id, $max, $class);	
	}

	function menu_files($id, $name, $default, $path, $pre, $ext, $class) {
		include_once(PATH_CORE . 'list_menu_files.inc.php');
		echo list_menu_files($id, $name, $default, $path, $pre, $ext, $class);
	}

	function format_currency ($number, $currency_id) {
		if(empty($number)) $number = 0; 
		if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
		if(!isset($this->format_currency[$currency_id])) $this->currency($currency_id);
		if($currency_id != DEFAULT_CURRENCY)
			if(!isset($this->format_currency[DEFAULT_CURRENCY])) 
				$this->currency(DEFAULT_CURRENCY);		
		$number *= $this->format_currency[DEFAULT_CURRENCY]["convert"][$currency_id]["rate"];
		if($number > .05 || $number == 0 || $number < -1)
			return $this->format_currency[$currency_id]["symbol"]
			   . "" . number_format($number, DEFAULT_DECIMAL_PLACE) . " "
			   . $this->format_currency[$currency_id]["iso"];
		else
			return $this->format_currency[$currency_id]["symbol"]
			   . "" . number_format($number, 3) . " "
			   . $this->format_currency[$currency_id]["iso"]; 
	}

	function format_currency_num ($number, $currency_id) {
		if(empty($number)) $number = 0; 
		if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
		if(!isset($this->format_currency[$currency_id])) $this->currency($currency_id);
		if(!isset($this->format_currency[DEFAULT_CURRENCY])) $this->currency(DEFAULT_CURRENCY);		
		$number *= $this->format_currency[DEFAULT_CURRENCY]["convert"][$currency_id]["rate"];
		if($number > .05 || $number == 0 || $number < -1)
			return $this->format_currency[$currency_id]["symbol"] . number_format($number, DEFAULT_DECIMAL_PLACE);
		else
			return $this->format_currency[$currency_id]["symbol"] . number_format($number, 3);
	} 

	function format_currency_decimal ($number, $currency_id) {
		if(empty($number)) return 0;
		if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
		if(!isset($this->format_currency[$currency_id])) $this->currency($currency_id);
		if(!isset($this->format_currency[DEFAULT_CURRENCY])) $this->currency(DEFAULT_CURRENCY);		
		return round($number *= $this->format_currency[DEFAULT_CURRENCY]["convert"][$currency_id]["rate"], 2);
	} 		       

	function currency_list($ret)  {
		if(!isset($this->format_currency[$currency_id])) $this->currency(DEFAULT_CURRENCY);
		global $smarty;
		$smarty->assign("$ret", $this->format_currency[DEFAULT_CURRENCY]["convert"]); 
	}

	function currency_iso ($currency_id) {
		if(empty($currency_id)) $currency_id = DEFAULT_CURRENCY;
		if(!isset($this->format_currency[$currency_id])) $this->currency($currency_id);
		return $this->format_currency[$currency_id]["iso"];
	}

	function currency($currency_id) {
		$db = &DB();
		$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'currency WHERE
				   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
				   id          = ' . $db->qstr($currency_id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0) {
			$this->format_currency[$currency_id] = Array (
							'symbol'        => $result->fields["symbol"],
							'convert'       => unserialize($result->fields["convert_array"]),
							'iso'           => $result->fields["three_digit"]);
			return true;
		} else {
			return false;
		}
	}

	function radio($input_id, $name, $table, $field, $id, $class) {
		include_once(PATH_CORE . 'list_radio.inc.php');
		echo list_radio($input_id, $name, $table, $field, $id, $class);		
	}	

	function check($input_id, $name, $table, $field, $default, $class) {
		include_once(PATH_CORE . 'list_check.inc.php');
		echo list_check($input_id, $name, $table, $field, $default, $class);
	}	 

	function select_groups($default, $field_name, $class, $size, $own_account) {
		include_once(PATH_CORE . 'list_select_groups.inc.php');
		return list_select_groups($default, $field_name, $class, $size, $own_account);
	}

	function calender_view($field, $default, $css, $id) 	{
		if(isset($default) && $default != '' && $default != '0') 
		$default  = date(UNIX_DATE_FORMAT, $default);
		else
		$default = '';
		include_once(PATH_CORE.'list_calendar.inc.php');
		echo list_calender_add($field, $default, $css);
	}		

	function calender_add($field, $default, $css) {
		if($default == 'now') $default = date(UNIX_DATE_FORMAT, time());
		include_once(PATH_CORE.'list_calendar.inc.php');
		echo list_calender_add($field, $default, $css);                                                    
	}

	function calender_add_static_var($field, $default, $css) {
		if($default == 'now') $default = date(UNIX_DATE_FORMAT, time());
		include_once(PATH_CORE.'list_calendar.inc.php');
		echo list_calender_add_static($field, $default, $css);
	}

	function calender_search($field, $default, $css) {	
		if($default == 'now') $default = date(UNIX_DATE_FORMAT, time());
		echo '
			<select name="field_option['.$field.'][0]">
			   <option value=">">></option>
			  <option value="<="><=</option>
			  <option value=">=">>=</option>
			  <option value="!=">!=</option>
			</select>&nbsp;&nbsp;'; 
		$this->calender_view($field.'[0]', $default, $css, 1);
		echo '<BR>
			<select name="field_option['.$field.'][1]">
			  <option value="<"><</option> 
			  <option value="<="><=</option>
			  <option value=">=">>=</option>
			  <option value="!=">!=</option>
			</select>&nbsp;&nbsp;';
		$this->calender_view($field.'[1]', $default, $css, 1);			
	}

	function setup_default_date($default, $css)	{
		include_once(PATH_CORE . 'list_setup_default_date.inc.php');
		echo list_setup_default_date($default, $css);
	}

	function card_type_menu($default_selected, $checkout_id, $field='checkout_plugin_data[card_type]', $class) {
		include_once(PATH_CORE . 'list_card_type_menu.inc.php');
		echo list_card_type_menu($default_selected, $checkout_id, $field, $class);        	
	}		

	function date($date) {	
	  if($date == '') $date = time();
	  return date(UNIX_DATE_FORMAT, $date);	 	
	}		

	function date_time($date) {		
	  if($date == '') $date = time();
	  $ret = date(UNIX_DATE_FORMAT, $date);	 		 	
	  $ret .= "  ".date(DEFAULT_TIME_FORMAT, $date);
	  return $ret;
	}

	function unserial ($data, $var) {
		global $smarty;
				if(is_string($data)) $array = unserialize($data);
				if(is_array($array)) $smarty->assign($var, $array); 
		return;
	}

	function smarty_array($table, $field, $sql, $return) {
		$db = &DB();
		$sql= "SELECT id, $field FROM ".AGILE_DB_PREFIX."$table
			   WHERE site_id = '" . DEFAULT_SITE . "'" . $sql . "
			   ORDER BY $field";
		$result = $db->Execute($sql);	
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','smarty_array', $db->ErrorMsg());
			return false;
		} 	
		while (!$result->EOF)
		{				
			$smart[] = $result->fields;
			$result->MoveNext();
		} 
		global $smarty;
		$smarty->assign("$return", $smart);
		return true;
	}

	function translate($table, $field1, $field2, $id, $var) {
		global $smarty;
		$db = &DB();
		$sql= "SELECT id, $field1 FROM ".AGILE_DB_PREFIX."$table
			  WHERE site_id = " . $db->qstr(DEFAULT_SITE) . " AND
			  language_id   = " . $db->qstr(SESS_LANGUAGE).  " AND " .
			  $field2 . "   = " . $db->qstr($id);
		$result = $db->Execute($sql);	
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('list.inc.php','translate', $db->ErrorMsg());
			return false;
		} else if($result->RecordCount() > 0) {
			$smarty->assign("$var", $result->fields);
			return $result->fields;
		} else {
			if (SESS_LANGUAGE == DEFAULT_LANGUAGE) {
				return false;
			} else {
				$sql= "SELECT id, $field1 FROM ".AGILE_DB_PREFIX."$table
					  WHERE site_id = " . $db->qstr(DEFAULT_SITE) . " AND
					  language_id   = " . $db->qstr(DEFAULT_LANGUAGE).  " AND " .
					  $field2 . "   = " . $db->qstr($id);
				$result = $db->Execute($sql);	
				if ($result === false) {
					global $C_debug;
					$C_debug->error('list.inc.php','translate', $db->ErrorMsg());
					return false;
				} else if($result->RecordCount() > 0) {
					$smarty->assign("$var", $result->fields);
					return $result->fields;
				} else {
					return false;
				}
			}
		}
	}

	function bool($field, $curr_value, $extra) {
		global $C_translate;
		if($curr_value == 'all') {
			$true = '';
			$false= '';
		} else if($curr_value == "1") {
			$true = ' selected';
			$false= '';
		} else {
			$true = '';
			$false= ' selected';
		}

		$return  = '<select id="'.$field.'" name="'. $field .'" '.$extra.'>';		
		if($curr_value == 'all')
		$return .= '<option value="" selected></option>
		';
		$return .= '<option value="1"' . $true . '>'.  $C_translate->translate('true', 'CORE','') . '</option>';
		$return .= '<option value="0"' . $false . '>'. $C_translate->translate('false','CORE','') . '</option>';
		$return .= '</select>';		
		echo $return;
	}

	function bool_static_var($field, $curr_value, $class) {
		global $C_translate;
		if ($curr_value == 'all') {
			$true = '';
			$false= '';
		} else if ($curr_value == 0) {
			$true = '';
			$false= ' selected';
		} else {
			$true = ' selected';
			$false= '';
		}
		$return  = '<select id="'.$field.'" name="'. $field .'">';		
		if($curr_value == 'all')
		$return .= '<option value="" selected></option>';
		$return .= '<option value="1"' . $true . '>'.  $C_translate->translate('true', 'CORE','') . '</option>';
		$return .= '<option value="0"' . $false . '>'. $C_translate->translate('false','CORE','') . '</option>';
		$return .= '</select>';		
		return $return;
	}

	function graphview() {
		global $VAR, $C_method;
		$auth = Array('product:top', 'account_admin:top', 'affiliate:top', 'invoice:compare');
		for($i=0; $i<count($auth); $i++) {
			if($auth[$i] == $VAR['graph']) {
				$m = explode(':', $VAR['graph']);
				$C_method->exe_noauth($m[0], $m[1]);
				exit;
			}
		}
	}

	function bar_graph() {
		global $VAR;
		require_once(PATH_CORE   . 'graph.inc.php');
		$graph   = new CORE_graph;
		@$module = $VAR['graph_module'];
		@$range  = $VAR['graph_range'];
		@$start  = $VAR['graph_start'];
		@$extra  = $VAR['graph_extra'];
		$graph->BAR_graph($module, $range, $start, $extra);
	}

	function pie_graph() {
		global $VAR;
		require_once(PATH_CORE   . 'graph.inc.php');
		$graph   = new CORE_graph;
		@$module = $VAR['graph_module'];
		@$method = $VAR['graph_method'];
		@$range  = $VAR['graph_range'];
		@$start  = $VAR['graph_start'];
		@$extra  = $VAR['graph_extra'];
		$graph->PIE_graph($module, $method, $range, $start, $extra);
	}

	function is_installed($module) { 
		if(@$this->is_installed[$module] == true) return true;
		if($this->auth_method_by_name($module, 'search')) {
			$this->is_installed[$module] = true;
			return true;
		}
		$db = &DB();
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'module WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name        = ' . $db->qstr($module) . ' AND
					status      = ' . $db->qstr("1");
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0) {
			$this->is_installed[$module] = true;
			return true;
		} else {
			return false;
		}
	}

	function auth_method_by_name($module, $method) {
		global $C_auth; 
		if(!is_object($C_auth)) return false;            
		return $C_auth->auth_method_by_name($module, $method);
	}

	function generate_admin_menu() {
		global $C_auth;
		echo $C_auth->generate_admin_menu();
	} 

	function account($field) {
		if (empty($this->account) && SESS_LOGGED) {
			$db = &DB();
			$sql    = 'SELECT * FROM ' . AGILE_DB_PREFIX . 'account WHERE
						site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
						id  = ' . $db->qstr(SESS_ACCOUNT);
			$result = $db->Execute($sql);
			$this->account = $result->fields;
		}
		echo $this->account[$field];
	}        

	# Get the AgileBill version info
	function version()  {
		require_once(PATH_CORE.'version.inc.php');
	}
}
?>