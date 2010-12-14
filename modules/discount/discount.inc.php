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
	
class discount
{
	/**
	 * array of available discounts
	 *
	 * @var array
	 */
	var $discounts=false; 	
	
	/**
	 * the cumulative amount of the current discount
	 *
	 * @var float
	 */
	var $discount_total=0;  
	
	/**
	 * the array of discounts applied to the line_items
	 *
	 * @var array
	 */
	var $discount_arr;	
	
	/**
	 * Array that contains list of discount plugins in the /plugins/discount/ directory to load
	 *
	 * @var array
	 */
	var $plugins=false;			

	/**
	 * Setup the discount plugin array if needed
	 */
	function discount() {
		// $this->plugins = array('test');
	}
	
	/**
	 * Load a specific discount plugin and validate the results
	 *
	 * @param string $plugin The plugin name
	 * @param string $discount The discount code
	 * @return bool
	 */
	function plugin_validate($plugin, $discount) {		 
		$plugin_file = PATH_PLUGINS.'discount/'. $plugin . '.php';
		if(is_file($plugin_file)) {
			include_once($plugin_file);
			eval('$plg = new plgn_discount_'. $plugin .';');
			if(is_object($plg)) {
				if(is_callable(array($plg, 'validate'))) {
					$plg->discount = $discount;
					return $plg->validate($discount);
				}
			}
		}		 
		return false;				
	}
	
	/**
	 * Add a discount at the cart/checkout by user/admin
	 *
	 * @param array $VAR
	 * @return bool
	 */
	function add_cart_discount($VAR) {
		global $C_debug, $C_translate, $smarty;

		# Validate input
		if(empty($VAR["discount"])) {
			$C_debug->alert($C_translate->translate('invalid_discount','checkout',''));
			return false;
		}
		
		$discount_code = $VAR["discount"];

		# Check the supplied discount
		$db = &DB();
		$rs = $db->Execute(sqlSelect($db,"discount","*","( date_start IS NULL OR date_start=0 OR date_start<".time().") AND ( date_expire>=".time()." OR date_expire IS NULL OR date_expire=0 ) AND name=::$discount_code::"));
		if (!$rs || !$rs->RecordCount() || $rs->fields["status"] != '1') {
			
			// local check failed, attempt any discount plugins
			$plg=false;
			if($this->plugins && is_array($this->plugins)) {
				foreach($this->plugins as $plugin){
					if($discount_code = $this->plugin_validate($plugin, $discount_code)) {	
						$plg=true;
						break;	
					}
				}
			}
			// no plugins returned true...
			if(!$plg) {
				$C_debug->alert($C_translate->translate('invalid_discount','checkout',''));
				return false;
			}
		}

		# get existing discounts: 
		$arr = array();
		$rs = $db->Execute(sqlSelect($db,"session","discounts","id=::".SESS."::"));
		if($rs && $rs->RecordCount() && !empty($rs->fields['discounts']) && is_string($rs->fields['discounts'])) 
			$arr=unserialize($rs->fields['discounts']);
	   
		# check for duplicates
		$do = true;
		if(is_array($arr)) {
			foreach($arr as $key=>$discount) { 
				if($discount == $discount_code) {
					unset($arr[$key]);
					$do = false; 
				}
			} 
		}
		
		# update session data
		if($do) $arr[]=$discount_code; 
		$rs = $db->Execute(sqlUpdate($db,"session",array('discounts'=> serialize($arr)), "id=::".SESS."::"));
			 
		return true;				
	}
	
	
	/**
    * Commit current discounts to the database (call after creating an invoice_item record)
    */
	function invoice_item($invoice_id,$invoice_item_id,$account_id,$discount_arr=false) {
		if($discount_arr && is_array($discount_arr)) $this->discount_arr=$discount_arr;
		if(is_array($this->discount_arr)) {
			$db=&DB();
			foreach($this->discount_arr as $dsc) {
				$sql="INSERT INTO ".AGILE_DB_PREFIX."invoice_item_discount SET id=".sqlGenID($db,"invoice_item_discount").", site_id=".DEFAULT_SITE.", invoice_id=$invoice_id, account_id=$account_id, invoice_item_id=$invoice_item_id, discount=".$db->qstr($dsc["discount"]).", amount=".$db->qstr($dsc["amount"]);
				$db->Execute($sql);
			}
		}
	}

	/**
    * Get the avialable discounts for an account, session, or service
    * @param $account 
    * @param $type 0=initial order, 1=recurring charge       
    */
	function available_discounts($account, $type=0,$invoice=false) {
		// get account specific discounts
		if($type) $type = " recurr_status=1 "; else $type = " new_status=1 ";
		$db =& DB();
		$rs = $db->Execute($sql=sqlSelect($db, 'discount', '*', "avail_account_id = $account AND $type AND status=1"));
		if($rs && $rs->RecordCount()) {
			while (!$rs->EOF) {
				$this->discounts["{$rs->fields['name']}"] = $rs->fields;
				$rs->MoveNext();
			}
		}
		// get session discounts from cart
		if($type==0) {
			$rsc = $db->Execute(sqlSelect($db, 'session', 'discounts', "(account_id = $account OR id=::".SESS."::) AND discounts != '' AND discounts IS NOT NULL"));
			if($rsc && $rsc->RecordCount()) {
				$arr=unserialize($rsc->fields['discounts']);
				foreach($arr as $discount) {
					if(!empty($discount)) {
						$rs = $db->Execute(sqlSelect($db, 'discount', '*', "name=::$discount:: AND status=1"));
						if($rs && $rs->RecordCount())
						if(empty($this->discounts["{$rs->fields['name']}"])) $this->discounts["{$rs->fields['name']}"] = $rs->fields;
					}
				}
			}
		}
		// get recurring discounts
		if($type==1 && $invoice) {
			$rs = $db->Execute(sqlSelect($db,array('invoice_item_discount','discount'),'B.*', "A.invoice_id = $invoice AND A.discount = B.name AND $type AND status=1"));
			if($rs && $rs->RecordCount()) {
				while (!$rs->EOF) {
					if(empty($this->discounts["{$rs->fields['name']}"])) $this->discounts["{$rs->fields['name']}"] = $rs->fields;
					$rs->MoveNext();
				}
			}
		}
	}

	/**
    * Calculate all applicable discounts for the current line item
    * @param $type bool 0=initial product, 1=recurring product, 2=initial_domain, 3=recurring domain
    * @param $invoice_item_id int The invoice item id for the discount 
    * @param $product_id int The product ID if type=0,1 or The TLD if type = 2,3
    * @param $account_id int The account ID 
    * @param $invoice_amt float The cumulative invoice amount
    * @param $prod_amt float The product price before any discounts 
    */ 
	function calc_all_discounts($type=0,$invoice_item_id=false, $product_id, $product_amt, $account_id, $invoice_amt) {
		$total_amt=0;
		if(is_array($this->discounts)) {
			if(!$invoice_item_id) unset($this->discount_arr);
			foreach($this->discounts as $discount => $tmp)
			{
				$amt = $this->calc_item_discount($type,$discount, $product_id, $account_id, $invoice_amt, $product_amt);
				if($amt > 0)
				{
					$this->discount_arr[] = Array ('id' => $invoice_item_id, 'discount' => $discount, 'amount' => $amt );
					$total_amt += $amt;
					$this->discount_total+=$amt;
				}
			}
		}
		return $total_amt;
	}

	/**
     * Add a manual ad-hoc invoice to the array
     * 
     * @param float $amount The discount amount
     * @param string $discount Name of the discount
     * @param $invoice_item_id int The invoice item id for the discount          
     */
	function add_manual_discount($amount, $discount='MISC', $invoice_item_id=false) {
		$this->discount_arr[] = Array ('id' => $invoice_item_id, 'discount' => $discount, 'amount' => $amount );
	}


	/**
    * Calculate Recurring Discount 
    * @param $type bool 0=initial product, 1=recurring product, 2=initial_domain, 3=recurring domain
    * @param $discount string The discount array obj (must be set to $this->discount["$discounts"] containing the fields of the discount)
    * @param $product_id int The product ID if type=0,1 or The TLD if type = 2,3
    * @param $account_id int The account ID 
    * @param $invoice_amt float The cumulative invoice amount
    * @param $prod_amt float The product price before any discounts 
    */
	function calc_item_discount($type, $discount, $product_id, $account_id, $invoice_amt, $prod_amt)
	{
		$this->discount = $this->discounts["$discount"];
		if($type == 0 || $type == 2) { 						// initial
			$rate_type 	= $this->discount["new_type"];
			$rate 		= $this->discount["new_rate"];
			$min_cost 	= $this->discount["new_min_cost"];
			$max_usage_amt = $this->discount["new_max_discount"];
		} else { 											// recurr
			$rate_type 	= $this->discount["recurr_type"];
			$rate 		= $this->discount["recurr_rate"];
			$min_cost 	= $this->discount["recurr_min_cost"];
			$max_usage_amt = $this->discount["recurr_max_discount"];
		}

		if(empty($this->discounts["$discount"])) return false;
		$this->discount = $this->discounts["$discount"];

		if (!empty( $this->discount["date_start"]  ) &&  $this->discount["date_start"] > time()) return 0;

		if (!empty( $this->discount["date_expire"] ) &&  $this->discount["date_expire"] < time()) return 0;

		if(!empty( $min_cost ) && $min_cost > $invoice_amt) return 0;

		if( $this->discount["max_usage_account"] > 0 || $this->discount["max_usage_global"] > 0 )
		if(!$this->discount_check_usage($this->discount["max_usage_account"],$this->discount["max_usage_global"], $account_id, $discount))
		return 0;

		if(!empty( $this->discount["avail_account_id"]) && $this->discount["avail_account_id"] != $account_id) return 0;

		if($type==0 || $type==2) {
			if(!empty( $this->discount["avail_account_id"]) && $this->discount["avail_account_id"] != $account_id) {
				return 0;
			} else {
				if(!empty( $this->discount["avail_group_id"] )) {
					$arr=unserialize($this->discount["avail_group_id"]);
					if(is_array($arr) && count($arr) > 0 && !empty($arr[0])) {
						$do=false;
						global $C_auth;
						for($i=0; $i<count($arr); $i++) {
							if($C_auth->auth_group_by_id($arr[$i])) { $do=true; $i=count($arr); }
						}
						if(!$do) return 0;
					}
				}
			}
		}

		if($type<2 && !empty($product_id) && !empty( $this->discount["avail_product_id"] )) {
			$arr=unserialize($this->discount["avail_product_id"]);
			if(is_array($arr) && count($arr) > 0 && !empty($arr[0])) {
				$do=false;
				for($i=0; $i<count($arr); $i++) if($arr[$i] == $product_id) { $do=true; $i=count($arr); }
				if(!$do) return 0;
			}
		} elseif ($type>1) {
			if(!empty( $this->discount["avail_tld_id"] )) {
				$do=false;
				$tld = $product_id;
				$db = &DB();
				$rstld = $db->Execute(sqlSelect($db,"host_tld","id","name=::$tld::"));
				if($rstld && $rstld->RecordCount()) {
					$tld_id = $rstld->fields["id"];
					$arr=unserialize($this->discount["avail_tld_id"]);
					if(is_array($arr) && count($arr) > 0 && !empty($arr[0])) {
						for($i=0; $i<count($arr); $i++) if($arr[$i] == $tld_id) { $do=true; $i=count($arr); }
						if(!$do) return 0;
					}
				}
			}
		}

		if($rate_type=="0") {
			$discount_amt = $rate*$prod_amt;
			if(!empty($max_usage_amt) && $discount_amt > $max_usage_amt) $discount_amt = $max_usage_amt;
		} else {
			$discount_amt = $rate;
		}

		if(!empty( $max_usage_amt )) {
			if ( $discount_amt + $this->discount_total > $max_usage_amt)
			$discount_amt = $max_usage_amt - $this->discount_total;
		}

		return round($discount_amt,2);
	}


	/**
    * Check discount usage for account/global restrictions
    */
	function discount_check_usage($max_acct, $max_global, $account_id, $discount)
	{
		$db = &DB();
		$rs = $db->Execute(sqlSelect($db,"invoice_item_discount","invoice_id,account_id","","","","DISTINCT"));
		if($rs && $rs->RecordCount()) {

			# Check global usage
			if(!empty($max_global) && $max_global > 0) if($rs->RecordCount() >= $max_global) return false;

			# Check usage by this account
			if(!empty($max_acct) && $max_acct > 0) {
				$i=0;
				while(!$rs->EOF) {
					if($rs->fields['account_id'] == $account_id) $i++;
					$rs->MoveNext();
				}
				if($i>=$max_acct) return false;
			}
		}
		return true;
	}


	function discount_construct() {
		$this->module = "discount";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
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

	function user_search($VAR) {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED)  {
			return false;
		} 
		# Lock the account_id
		$VAR['discount_avail_account_id'] = SESS_ACCOUNT; 
		$this->discount_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function user_search_show($VAR) {
		# Lock the user only for his billing_records:
		if(!SESS_LOGGED)  {
			return false;
		} 
		$this->discount_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}

	function add($VAR) {
		$this->discount_construct();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}

	function view($VAR) {
		$this->discount_construct();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function update($VAR) {
		$this->discount_construct();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR) {
		$this->discount_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	function search_form($VAR) {
		$this->discount_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR) {
		$this->discount_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR) {
		$this->discount_construct();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$dbc = new CORE_database;
		$smart = $dbc->search_show($VAR, $this, $type); 
		$db = &DB(); 
		for($i=0; $i<count($smart); $i++)
		{
		 	$smart[$i]['savings'] 	= 0;
			$smart[$i]['orders'] 	= 0; 			
			$smart[$i]['revenue'] 	= 0;
			$rs = $db->Execute($sql=sqlSelect($db,Array("invoice","invoice_item_discount"),"SUM(A.total_amt) as sum","B.invoice_id=A.id AND A.billing_status=1 AND B.discount=::{$smart[$i]['name']}::","","","DISTINCT"));
			if($rs && $rs->RecordCount()) $smart[$i]['revenue'] = $rs->fields['sum'];
			$rs = $db->Execute(sqlSelect($db, 'invoice_item_discount', 'invoice_id,amount', "discount=::{$smart[$i]['name']}::"));
			if($rs && $rs->RecordCount() > 0) {
				while(!$rs->EOF)  {   
					$smart[$i]['savings'] += $rs->fields['amount']; 
					if(empty($invoices[$rs->fields['invoice_id']])) {
				 		$smart[$i]['orders']++;
				 		$invoices[$rs->fields['invoice_id']]=true;
					}
					$rs->MoveNext();
				}
			}
		}
		global $smarty;
		$smarty->clear_assign('discount');
		$smarty->assign('discount', $smart);
	}	
}
?>