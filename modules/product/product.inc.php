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
	
class product
{
	/**
	 * Holds the array of available attributes for the current product	 
	 * @var array
	 */
	var $attr;
	
	/** Admin: View details  */
	function admin_details($VAR)  { 
		$this->session_id = SESS;
		if(!empty($VAR['account_id'])) {
			$this->account_id = $VAR['account_id'];	
			$db=&DB();		
			$rs = $db->Execute(sqlSelect($db,"session","id","account_id={$this->account_id}"));
			if($rs && $rs->RecordCount()) $this->session_id = $rs->fields['id']; 
		} 
		$this->details($VAR, $this);
	}

	/** User: View details */
	function details($VAR, &$product_obj) {
		global $smarty;
		if(empty($VAR['id'])) return false;

		# able to view inactive items?
		$db = &DB();
		global $C_auth;
		if($C_auth->auth_method_by_name('invoice','add')) $active = ''; else $active = " AND active=1";
  
		$result = $db->Execute(sqlSelect($db,"product","*","id=::{$VAR['id']}:: $active"));
		if($result->RecordCount() == 0) return false;

		# check for group settings:
		$groups = unserialize($result->fields['group_avail']);
		$auth = false;
		for($ii=0; $ii<count($groups); $ii++) {
			if($C_auth->auth_group_by_id($groups[$ii])) {
				$auth = true;
				break;
			}
		}
		if(!$auth) return false;

		# define the DB vars as a Smarty accessible block
		$smarty->assign('product', $result->fields);

		# If trial, get the sku of the trial product:
		if($result->fields["price_type"] == '2') { 
			$trial = $db->Execute(sqlSelect($db,"*","product","id=::{$result->fields["price_trial_prod"]}::"));
			$smarty->assign('trial', $trial->fields);
		}

		# Get the best price for base, setup, & any attributes:
		$this->price_arr($result->fields);
		$smarty->assign('price', $this->price);

		# Get any attributes & attribute pricing:
		$this->attr_arr($VAR['id']);
		$smarty->assign('attr', $this->attr);

		return true;
	}

	/**
	 * Get Atribute values for product details page, sets $this->attr
	 *
	 * @param int $product_id The product id
	 * @return unknown
	 */
	function attr_arr($product_id)
	{
		global $C_auth; 
		# set the current account
		if(empty($this->account_id)) $this->account_id = SESS_ACCOUNT; 
		$db = &DB(); 
		$result = $db->Execute(sqlSelect($db,"product_attr","*","product_id=::$product_id::","sort_order"));
		if(!$result || $result->RecordCount() == 0) {
			$this->attr = false;
			return false;
		} 
		# loop through each attribute to get the values:
		$i=0;
		while( !$result->EOF ) {
			$this->attr[$i]["id"]           = $result->fields["id"];
			$this->attr[$i]["type"]         = $result->fields["collect_type"];
			$this->attr[$i]["default"]      = $result->fields["collect_default"];
			$this->attr[$i]["name"]         = $result->fields["name"];
			$this->attr[$i]["description"]  = $result->fields["description"];
			$this->attr[$i]["required"]     = $result->fields["required"]; 
			#get the best base & setup price
			$g_ar = unserialize($result->fields["price_group"]);
			$ret['base']  = $result->fields["price_base"];
			$ret['setup'] = $result->fields["price_setup"];
			if($ret['base'] != 0 && $ret['setup']!= 0) {
				if(count($g_ar) > 0) {
					while (list ($group, $vals) = each ($g_ar)) {
						if ($C_auth->auth_group_by_account_id($this->account_id, $group)) {
							if($vals["price_base"] != "" && $vals["price_base"] < $ret['base'])
							$ret['base'] = $vals["price_base"];
							if($vals["price_setup"] != "" && $vals["price_setup"] <  $ret['setup'])
							$ret['setup'] = $vals["price_setup"];
						}
					}
				}
			}
			# if menu, get the menu values as an array:
			if($result->fields["collect_type"] == '2') {
				$pat="\r\n";
				$tarr = false;
				$marr = explode($pat,$result->fields["collect_default"]); 
				for($ii=0;$ii<count($marr); $ii++) {
					if(empty($marr[$ii]) || $marr[$ii] == '*') {
						# blank line
						$tarr[] = Array ('name' => '', 'base' => 0, 'setup' => 0);
					} else {
						# populated line, determine base/setup price:
						if(ereg('==', $marr[$ii])) {
							# Use custom prices
							$marrp = explode("==", $marr[$ii]);
							$tarr[] = Array ('name' => @$marrp[0], 'base' =>  @$marrp[1], 'setup' =>  @$marrp[2]);
						} else {
							# Use default prices
							$tarr[] = Array ('name' => $marr[$ii], 'base' =>  $ret['base'], 'setup' =>  $ret['setup']);
						}
					}
				}
				$this->attr[$i]["default"] = $tarr;
			}

			$this->attr[$i]["price_base"]  = $ret["base"];
			$this->attr[$i]["price_setup"] = $ret["setup"];
			$result->MoveNext();
			$i++;
		}
		return true;
	}

	/**
	 * Calculate the cost for the attributes in the cart
	 *
	 * @param array $fields The product record fields
	 * @param array $cart_attr The array of attributes in the cart
	 * @param int $recurr_schedule The recurring schedule, 0-5
	 * @param int $account The account id
	 * @param bool $prorate Apply prorating or not
	 * @return unknown
	 */
	function price_attr($fields, $cart_attr, $recurr_schedule, $account=SESS_ACCOUNT, $prorate=true)
	{
		global $C_auth;
		$ret['base'] = 0;
		$ret['setup'] = 0;
		$product_id = $fields['id'];
		
		# Get the vars:
		if(!empty($cart_attr) && !is_array($cart_attr)) $cart_attr = unserialize($cart_attr);
		if(!is_array($cart_attr)) return false;
 
		# get the attributes for this product
		$db = &DB(); 
		$result = $db->Execute(sqlSelect($db, "product_attr", "*","product_id=::$product_id::","sort_order")); 
		if(!$result || $result->RecordCount() == 0) {
			$this->attr = false; 
			return false;
		}

		# loop through each attribute to get the values & validate the input:
		$i=0;
		while( !$result->EOF ) {
			$calc = false;
			reset($cart_attr); 
			# loop through each attribute defined in the cart
			foreach($cart_attr as $id=>$val) {
				$menu_def = true;  
				# if defined in the cart:
				if(!empty($val) && is_numeric($id) && $id == $result->fields["id"]) { 
					# get the best base & setup price
					$g_ar = unserialize($result->fields["price_group"]);
					$curr['base']  = $result->fields["price_base"];
					$curr['setup'] = $result->fields["price_setup"]; 
					### if menu, get the base & setup amount from the selected item:
					if($result->fields["collect_type"] == '2') {
						$marr = explode("\r\n",$result->fields["collect_default"]); 
						# Loop through each menu option
						for($ii=0;$ii<count($marr); $ii++) {
							# Check if current menu item matches the one selected
							if(!empty($marr[$ii]) && $marr[$ii] != '*' && ereg("^$val", $marr[$ii])) {
								# populated line, determine base/setup price:
								if(ereg('==', $marr[$ii])) {
									# Use custom prices
									$marrp = explode("==", $marr[$ii]); 
									@$ret['base']  += @$marrp[1];
									@$ret['setup'] += @$marrp[2];
									$menu_def = false;
								}
							}
						}
						$this->attr[$i]["default"] = $tarr;
					} 

					### determine best group pricing
					if($menu_def) {
						if($curr['base'] > 0 || $curr['setup'] > 0) {
							if(count($g_ar) > 0) {
								$idx = 0;
								while (( (list ($group, $vals) = each ($g_ar)) && ($idx < 1) )) {
									// check if better pricing exist for current group
									if (is_numeric($group) && $C_auth->auth_group_by_account_id($account, $group)) {
										// calculate the base price
										if($vals["price_base"] != "" && $vals["price_base"] < $curr['base']) @$ret['base'] += $vals["price_base"];
										else @$ret['base'] += $curr["base"];
										// calculate the setup price
										if($vals["price_setup"] != "" && $vals["price_setup"] <  $curr['setup']) @$ret['setup'] += $vals["price_setup"];
										else @$ret['setup'] += $curr["setup"];
										$idx++;
									}
								}
							}
						}
					}
				}
			}
			$result->MoveNext();
			$i++;
		}
		 
		# check the subscription schedule and calculate actual rate for this schedule:
		$arr = Array(.23, 1, 3, 6, 12, 24, 36);
		if($fields["price_recurr_type"] == 1)
		$ret['base'] *= $arr[$recurr_schedule];
 
		# check for any prorating for the selected schedule:
		if($fields["price_recurr_type"] == 1 && $prorate==true)
		$prorate = $this->prorate($recurr_schedule, $fields["price_recurr_weekday"], $fields["price_recurr_week"]);
 
		# calculate the prorated recurring amount:
		if (@$prorate > 0 && $ret["base"] > 0)  $ret["base"] *= $prorate;

		return Array('base' => @round($ret["base"], 2), 'setup' => @$ret["setup"]);
	}
               
	/**
     * Get the start & end of set billing schedules 
	 *
	 * @param int $type
	 * @param int $weekday
	 * @param int $week
	 * @return float
	 */
	function recurrDates($type, $weekday, $week) {
		if ($type == 0) {
			$period_start = time();
			$period_end   = $period_start + (86400*7);
			return Array('start' => $period_start, 'end' => $period_end);
		} elseif ($type == 1) {
			$inc_months = 1;
		}  elseif ($type == 2) {
			$inc_months = 3;
		} elseif ($type == 3) {
			$inc_months = 6;
		} elseif ($type == 4) {
			$inc_months = 12;
		} elseif ($type == 5) {
			$inc_months = 24;
		} else {
			return false;
		} 
		$d = mktime(0, 0 ,0 ,date('m', time()), $weekday, date('y', time()));
		if($d < time())
		$period_start = $d;
		else
		$period_start = mktime(0,0,0,date('m', $d)-1, $weekday, date('y', $d));
		$period_end   = mktime(0,0,0,date('m', $period_start)+$inc_months, $weekday, date('y', $period_start));
		return Array('start' => $period_start, 'end' => $period_end);
	}
    	
	/**
	 * Determine Prorate Amount 
	 *
	 * @param int $type
	 * @param int $weekday
	 * @param int $week
	 * @return float
	 */
	function prorate($type, $weekday, $week)
	{ 
		$arr = $this->recurrDates($type, $weekday, $week);
		if(!$arr) return 0; 
		$total_time 	= $arr['end'] - $arr['start'];
		$remain_time	= $arr['end'] - time();
		$percent_remain = ($remain_time/$total_time) ;
		return round($percent_remain,2);
	}
		 	
	/**
	 * Get the lowest price for one-time or recurring product fees
	 *
	 * @param array $fields Array containing all product fields
	 */
	function price_arr($fields) {
		global $C_auth;
		if(empty($this->account_id))  $this->account_id = SESS_ACCOUNT;

		$type = $fields['price_type'];
		$g_ar = unserialize($fields["price_group"]);
		if($type != "1")
		{
			# get the best base price (trial or one-time charges):
			$ret['base']  = $fields["price_base"];
			$ret['setup'] = $fields["price_setup"];

			if(is_array($g_ar) && count($g_ar) > 0)
			{
				while (list ($group, $vals) = each ($g_ar))
				{
					if (is_numeric($group) && $C_auth->auth_group_by_account_id($this->account_id, $group))
					{
						if($this->group_pricing($group))
						{
							if($vals["price_base"] != "" || $vals["price_setup"] != "" )
							{
								if(!empty($vals["price_base"]) && $vals["price_base"] < $ret['base'])
								$ret['base']= $vals["price_base"];
								if(!empty($vals["price_setup"]) && $vals["price_setup"] <  $ret['setup'])
								$ret['setup'] = $vals["price_setup"];
							}
						}
					}
				}
			}
			$this->price = $ret;
		}
		else
		{
			## Recurring charge, return best base/setup rates for all available payment schedules
			if(is_array($g_ar) && count($g_ar) > 0)
			{
				for($i=0; $i<count($g_ar); $i++)
				{
					foreach($g_ar[$i] as $group=>$vals)
					{
						if($g_ar[$i]["show"] == "1")
						{
							if (is_numeric($group) && $C_auth->auth_group_by_account_id($this->account_id, $group))
							{
								if($this->group_pricing($group))
								{
									if($vals["price_base"] != "" || $vals["price_setup"] != "" )
									{
										if(empty($ret[$i]['base']) || $vals["price_base"] < $ret[$i]['base']) $ret["$i"]['base'] = $vals["price_base"];
										if(empty($ret[$i]['setup']) || $vals["price_setup"] < $ret[$i]['setup']) $ret["$i"]['setup'] = $vals["price_setup"];
									}
								}
							}
						}
					}
				}
			}
		}
		$this->price = $ret;
	}

	/**
	 * Check if alternate pricing is allowed for specified group
	 *
	 * @param int $group Group ID
	 * @return bool
	 */
	function group_pricing($group) {
		$db = &DB();
		$rs = $db->Execute(sqlSelect($db,"group","pricing","id=$group"));
		if($rs && $rs->fields['pricing']==1) return true;
	}
	
	/**
	 * Best Price for Product
	 *
	 * @param array $fields
	 * @param array $recurr_schedule
	 * @param int $account
	 * @param bool $prorate
	 * @return array
	 */
	function price_prod($fields, $recurr_schedule, $account=SESS_ACCOUNT, $prorate=true) {
		global $C_auth;
		$type = $fields['price_type'];
		@$g_ar = unserialize($fields["price_group"]);
		if($type != "1") {
			# get the best base price (trial or one-time charges)
			$ret['base']  = $fields["price_base"];
			$ret['setup'] = $fields["price_setup"]; 
			if(is_array($g_ar) && count($g_ar) > 0 ) {
				while (list ($group, $vals) = each ($g_ar)) {
					if (is_numeric($group) && $C_auth->auth_group_by_account_id($account,$group)) {
						if($this->group_pricing($group)) {
							if($vals["price_base"] != "" && $vals["price_base"] < $ret['base']) $ret['base']= $vals["price_base"];
							if($vals["price_setup"] != "" && $vals["price_setup"] <  $ret['setup']) $ret['setup'] = $vals["price_setup"];
						}
					}
				}
			}
			return Array('base' => $ret["base"], 'setup' => $ret["setup"]);
		} else {
			## Recurring charge, return best base/setup rates for all available payment schedules
			if(is_array($g_ar) && count($g_ar) > 0) {
				$i = $recurr_schedule; 
				# check for any prorating for the selected schedule:
				if($fields["price_recurr_type"] == 1 && $prorate==true)
				$prorate = $this->prorate($recurr_schedule, $fields["price_recurr_weekday"], $fields["price_recurr_week"]); 
				while (list ($group, $vals) = each ($g_ar[$i])) {
					if($g_ar[$i]["show"] == "1") {
						if (is_numeric($group) && $C_auth->auth_group_by_account_id($account, $group)) {
							if($this->group_pricing($group)) {
								if($vals["price_base"] != "")
								if(empty($ret['base']) || $vals["price_base"] < $ret['base']) $ret['base'] = $vals["price_base"];
								if($vals["price_setup"] != "")
									if(empty($ret['setup']) || $vals["price_setup"] < $ret['setup'])
										$ret['setup'] = $vals["price_setup"];
							}
						}
					}
				}
			}
			if(empty($ret)) return false; 
			
			# calculate the prorated recurring amount:
			if (@$prorate > 0 && $ret["base"] > 0)  $ret["base"] *= $prorate;
		}
		return Array('base' => @round($ret["base"], 2), 'setup' => @$ret["setup"]);
	}
	
	/**
	 * Get the lowest (recurring) price
	 *
	 * @param array $fields
	 * @param int $account
	 * @return array Recurring Price 
	 */
	function price_recurr_arr($fields, $account) {
		global $C_auth;
		$g_ar = unserialize($fields["price_group"]);
		if(count($g_ar) > 0) {
			for($i=0; $i<count($g_ar); $i++)  {
				while (list ($group, $vals) = each ($g_ar[$i]))   {
					if($g_ar[$i]["show"] == "1") {
						if (is_numeric($group) && $C_auth->auth_group_by_account_id($account,$group)) { 
							if($vals["price_base"] != "")
								if(empty($ret[$i]['base']) || $vals["price_base"] < $ret[$i]['base']) $ret[$i]['base'] = $vals["price_base"];

							if($vals["price_setup"] != "")
								if(empty($ret[$i]['setup']) || $vals["price_setup"] < $ret[$i]['setup']) $ret[$i]['setup'] = $vals["price_setup"];
						}
					}
				}
			}
		}
		return $ret;
	}
	
	/** 
	 * Clone Existing Product 
	 */
	function cloner($VAR)
	{
		global $C_debug, $C_translate;

		$product_id = $VAR['id'];
		$sku = $VAR['product_sku'];
		$p   = AGILE_DB_PREFIX;

		if(empty($product_id) || empty($sku)) {
			$C_debug->alert( $C_translate->translate('clone_error', 'product',''));
			return false;
		}

		$db = &DB();
		$dbc= new CORE_database;

		# Get current product details
		$sql = $dbc->sql_select("product", "*", "id = $product_id", "", $db);
		$result = $db->Execute($sql);

		# Clone product
		$new_prod_id = $db->GenID(AGILE_DB_PREFIX.'product_id');
		$sql = "INSERT INTO {$p}product SET
    				id  = $new_prod_id, 
    				sku = " . $db->qstr($sku); 
		while(list($field,$value) = each($result->fields)) {
			if($field != 'sku' && $field != 'id' && !is_numeric($field) )
			$sql .= ",$field = ".$db->qstr($value);
		}
		$result = $db->Execute($sql);

		# Get current translation
		$sql = $dbc->sql_select("product_translate", "*", "product_id = $product_id", "", $db);
		$result = $db->Execute($sql);

		# Clone translation
		while(!$result->EOF)
		{
			$id = $db->GenID(AGILE_DB_PREFIX.'product_translate_id');
			$sql = "INSERT INTO {$p}product_translate SET
	    				id  = $id, 
	    				product_id = $new_prod_id"; 
			while(list($field,$value) = each($result->fields)) {
				if($field != 'product_id' && $field != 'id' && !is_numeric($field)  )
				$sql .= ",$field = ".$db->qstr($value);
			}
			$db->Execute($sql);
			$result->MoveNext();
		}

		# Get current attributes
		$sql = $dbc->sql_select("product_attr", "*", "product_id = $product_id", "", $db);
		$result = $db->Execute($sql);

		# Clone attributes
		while(!$result->EOF)
		{
			$id = $db->GenID(AGILE_DB_PREFIX.'product_attr_id');
			$sql = "INSERT INTO {$p}product_attr SET
	    				id  = $id, 
	    				product_id = $new_prod_id"; 
			while(list($field,$value) = each($result->fields)) {
				if($field != 'product_id' && $field != 'id' && !is_numeric($field) )
				$sql .= ",$field = ".$db->qstr($value);
			}
			$db->Execute($sql);
			$result->MoveNext();
		}

		$msg = $C_translate->translate('clone_success', 'product','');
		$C_debug->alert( '<a href="?_page=product:view&id='.$new_prod_id.'" target="_parent">'. $msg .'</a>');
		return $new_prod_id;
	}

	function add($VAR)  {
		# defaults for 'recurring' product
		if($VAR["product_price_type"] == "1")
		{
			$VAR['product_price_recurr_default'] = "1";
			$VAR['product_price_recurr_type'] = "0";
			$VAR['product_price_recurr_week'] = "1";
			$VAR['product_price_recurr_weekday'] = "1";

			# Set default recurring prices: (monthly only)
			$db     = &DB();
			$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'group WHERE
                            site_id             = ' . $db->qstr(DEFAULT_SITE) . ' AND
                            pricing		        = ' . $db->qstr('1');
			$rs = $db->Execute($sql);
			while(!$rs->EOF) {
				$i = $rs->fields['id'];
				$recur_price[0][$i]['price_base']  = '';
				$recur_price[0][$i]['price_setup'] = '';
				@$recur_price[1][$i]['price_base'] = $VAR['product_price_base'];
				@$recur_price[1][$i]['price_setup'] = $VAR['product_price_setup'];
				$recur_price[2][$i]['price_base']  = '';
				$recur_price[2][$i]['price_setup'] = '';
				$recur_price[3][$i]['price_base']  = '';
				$recur_price[3][$i]['price_setup'] = '';
				$recur_price[4][$i]['price_base']  = '';
				$recur_price[4][$i]['price_setup'] = '';
				$recur_price[5][$i]['price_base']  = '';
				$recur_price[5][$i]['price_setup'] = '';
				$rs->MoveNext();
			}
			$recur_price[0]['show'] = "0";
			$recur_price[1]['show'] = "1";
			$recur_price[2]['show'] = "0";
			$recur_price[3]['show'] = "0";
			$recur_price[4]['show'] = "0";
			$recur_price[5]['show'] = "0";
			@$VAR['product_price_group'] = $recur_price;
		}

		# Defaults for product groups:
		$VAR['product_group_avail'] = Array('0');

		$this->product_construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$result 	= $db->add($VAR, $this, $type);

		# Create a translate record for this product:
		if($result) {
			$db     = &DB();
			$id     = $db->GenID(AGILE_DB_PREFIX . 'product_translate_id');
			$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'product_translate SET
                            site_id             = ' . $db->qstr(DEFAULT_SITE) . ',
                            id                  = ' . $db->qstr($id) . ',
                            product_id          = ' . $db->qstr($result) . ',
                            language_id         = ' . $db->qstr(DEFAULT_LANGUAGE) . ',
                            name                = ' . $db->qstr(@$VAR["translate_name"]) . ',
                            description_short   = ' . $db->qstr(@$VAR["translate_description_short"]) . ',
                            description_full    = ' . $db->qstr(@$VAR["translate_description_full"]);
			$db->Execute($sql);
		}
	}

	function view($VAR) {
		$this->product_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function update($VAR) {
		global $_FILES;
		$imgarr = Array('jpeg','jpg','gif','bmp','tif','tiff','png');
		if(isset($_FILES['upload_file1']) && $_FILES['upload_file1']['size'] > 0)
		{
			for($i=0; $i<count($imgarr); $i++)
			{
				if(eregi($imgarr[$i].'$', $_FILES['upload_file1']['name']))
				{
					$filename = eregi_replace(',', '', 'prod_thmb_' . @$VAR["id"] . "." . $imgarr[$i]);
					$i = 10;
				}
			}
		}
		elseif (@$VAR['delimg'] == 1)
		{
			$filename = "";
		}

		### Validate the thumbnail upoad:
		if(isset($filename))
		$VAR['product_thumbnail']   = $filename;

		$this->product_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$result = $db->update($VAR, $this, $type);

		### Copy the thumbnail
		if($result && isset($filename))
		{
			### Copy 1ST file upoad:
			copy($_FILES['upload_file1']['tmp_name'], PATH_IMAGES . "" . $filename);
		}
	}

	function delete($VAR) {
		$this->associated_DELETE =
		Array (
		Array ( 'table' => 'product_translate', 'field' => 'product_id'),
		Array ( 'table' => 'product_attr', 		'field' => 'product_id'),
		Array ( 'table' => 'product_img', 		'field' => 'product_id')
		);
		$this->product_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	function search_form($VAR) {
		$this->product_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR)
	{
		$this->product_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR) {
		$this->product_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}
	
	function product_construct() {
		$this->module = "product";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
		$C_xml 			= new CORE_xml;
		$construct 		= $C_xml->xml_to_array($this->xml_construct);
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