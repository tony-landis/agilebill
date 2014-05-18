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
	
class cart
{ 
	var $account_id;
	var $session_id;
	
	/**
	 * How many associated product levels to check for products to grant?
	 *
	 * @var int
	 */
	var $assoc_grant_levels=5;
	var $module='cart';
 
	/**
     * Admin View Cart Contents  
     */
	function admin_view($VAR) { 
		if(!empty($VAR['account_id'])) {
			$this->account_id = $VAR['account_id'];
		} else {
			return false;
		}
		$this->admin_view = true;
		$this->view($VAR, $this);
	}
 
	/**
     * Admin Add Cart Contents 
     */
	function admin_add($VAR) { 
		if(!empty($VAR['account_id'])) {
			$this->account_id = $VAR['account_id'];
			$db = &DB(); 
			$rs = $db->Execute(sqlSelect($db,"session","id","account_id=$this->account_id"));
			if($rs && $rs->RecordCount()) {
				$this->session_id = $rs->fields['id'];
			} else {
				$this->session_id = SESS;
			}
		} else {
			return false;
		}
		$this->add($VAR, $this);
	}

	/**
     * Admin change cart qty
     */
	function admin_changeqty($VAR) { 
		if(!empty($VAR['account_id'])) {
			$this->account_id = $VAR['account_id'];
		} else {
			return false;
		}
		$this->admin = true;
		$this->changeqty($VAR, $this);
	}

	/**
     * Change the quantity of an item 
     */
	function changeqty($VAR) { 
		if(empty($this->account_id)) $this->account_id = SESS_ACCOUNT;
		if(empty($this->session_id)) $this->session_id = SESS;

		@$id = $VAR['id'];
		@$qty = $VAR['qty'];
		if ($id <= 0 ) return;

		$db = &DB(); 
		if ($qty == '0') {
			# Product Plugin Level deletion
			$cartid = & $db->Execute($sql=sqlSelect($db,"cart","*","id=::{$id}:: AND session_id = ::{$this->session_id}::"));
			if($cartid) $product = & $db->Execute($sql=sqlSelect($db,"product","*","id=::{$cartid->fields['product_id']}::"));
			if(!empty($product->fields['prod_plugin']) && !empty($product->fields['prod_plugin_data'])) {
				$prodplgfile = PATH_PLUGINS.'product/'. $product->fields['prod_plugin_file'] . '.php';
				if(is_file($prodplgfile)) {
					include_once(PATH_PLUGINS.'product/'. $product->fields['prod_plugin_file'] . '.php');
					eval('$prodplg = new plgn_prov_'. $product->fields['prod_plugin_file'] .';');
					if(is_object($prodplg)) {
						if(is_callable(array($prodplg, 'delete_cart'))) { 
							$prodplg->delete_cart($VAR, $cartid->fields);
						}
					}
				}
			}

			# delete main cart items & subitems:
			$sql='';
			if(empty($this->admin)) $sql = "AND session_id = ::{$this->session_id}::";
			$rs = & $db->Execute($sql=sqlDelete($db,"cart","(id=::{$id}:: OR cart_parent_id=::{$id}:: ) $sql"));

			global $smarty;
			$smarty->assign('js', false);
			return false;
		}
  
		# update the quantity:
		if(!preg_match("/^[0-9]{1,5}$/",$qty)) $qty = 1;
		if($qty < 1) $qty = 1;
		 
		if(!$this->admin) $sql_extra = " AND session_id=::$this->session_id::"; else $sql_extra='';
		if($VAR["type"] == 1) { 
			$fields=Array('quantity'=>$qty);
			$db->Execute($sql=sqlUpdate($db,"cart",$fields,"id=::$id:: $sql_extra")); 
		} else if ($VAR["type"] == 2) { 
			$fields=Array('recurr_schedule'=>$VAR["schedule"]);
			$db->Execute($sql=sqlUpdate($db,"cart",$fields,"id=::$id:: $sql_extra")); 
		} else if ($VAR["type"] == 3) {
			# change domain term 
			$fields=Array('domain_term'=>$VAR["term"]);
			$db->Execute($sql=sqlUpdate($db,"cart",$fields,"id=::$id:: $sql_extra"));
		} 

		# get the product id: 
		$result = $db->Execute(sqlSelect($db,"cart","*","id=$id $sql_extra","cart_type,date_orig"));

		# get the product details: 
		$product = $db->Execute(sqlSelect($db,"product","*","id={$result->fields["product_id"]}"));
 
		if($result->fields["cart_type"] == "2") {
			# domain name, get pricing
			include_once(PATH_MODULES.'host_tld/host_tld.inc.php');
			$tldObj=new host_tld;			
			$tldprice = $tldObj->price_tld_arr($result->fields["domain_tld"], $result->fields["host_type"], false, false, false, $this->account_id);			
			$qty = $result->fields["domain_term"];
			$base_price   = $tldprice[$qty];
			$setup_price  = 0;
		} else if($result->fields["cart_type"] == "3") {
			# ad-hoc, get price
			$base_price   = $result->fields["ad_hoc_amount"] * $result->fields["quantity"];
			$setup_price   = $result->fields["ad_hoc_setup"] * $result->fields["quantity"]; 
		} else {
			include_once(PATH_MODULES.'product/product.inc.php');
			$productObj=new product;

			# get pricing for this product:
			$prod_price = $productObj->price_prod($product->fields, $result->fields["recurr_schedule"], $this->account);
			$setup_price = $prod_price["setup"] * $result->fields["quantity"];
			$base_price  = $prod_price["base"]  * $result->fields["quantity"];

			# get pricing for any attributes:
			$attr_price = $productObj->price_attr($product->fields, $result->fields["product_attr"], $result->fields["recurr_schedule"], $this->account);
			$setup_price += ($attr_price["setup"]  * $result->fields["quantity"]);
			$base_price  += ($attr_price["base"]   * $result->fields["quantity"]);

			# get the qty
			$qty = $result->fields["quantity"];
		}

		# set the smarty fields:
		global $smarty;
		$smarty->assign('qty',   $qty);
		$smarty->assign('base',  $base_price);
		$smarty->assign('setup', $setup_price);
		$smarty->assign('js', 	 true);
		return;
	}
	
	/** Get cart contents and return adodb rs */
	function get_contents(&$db) { 
		$sql = 'SELECT DISTINCT c.* FROM ' . AGILE_DB_PREFIX . 'cart as c '; 
		if(!empty($this->account_id)) { 
			$sql .= "LEFT JOIN ".AGILE_DB_PREFIX."session AS s ON ( s.site_id = ".DEFAULT_SITE." AND s.account_id = {$this->account_id} ) 
					 WHERE ( c.account_id = {$this->account_id} OR c.session_id = s.id ) ";        	
		} else {
			$sql .= " WHERE c.session_id = ". $db->qstr(SESS);
			$this->account_id = SESS_ACCOUNT;
		} 
		$sql .=  '  AND c.site_id = ' .DEFAULT_SITE. ' AND ( c.cart_parent_id  = 0  OR c.cart_parent_id IS NULL ) ORDER BY c.cart_type, c.date_orig'; 
		$result = $db->Execute($sql);	
		return $result;	
	}
	
	/**
	 * Convert cart contents into invoice object & get smarty data
	 */
	function put_contents_invoice(&$db, &$result, &$invoice, &$smart, $taxObj=false, $discountObj=false) {
	   
		// get parent cart items
		$i=0;
		while(!$result->EOF)
		{ 
			$id=$result->fields['id'];
			$this->addInvoiceItem($id,$result,$invoice,$smart,$i, false, false, $taxObj, $discountObj);
			// ad hoc  
			if($result->fields["cart_type"] == 3 ) {  							
				$smart[$i]["price"] = $invoice->invoice_item["$id"]["total_amt"];
			// domain			
			} if($result->fields["cart_type"] == 2 ) { 							
				$smart[$i]["price"] = $invoice->invoice_item["$id"]["total_amt"];
				$smart[$i]["tld_arr"] =  $invoice->tld_arr["$id"];
				$smart[$i]["sku"] = 'DOMAIN-'. strtoupper($result->fields["host_type"]);
			// product   
			} else  {  															
				@$smart[$i]["price"] = $invoice->price_arr["$id"];
			}
			 
			// get the product attributes
			$smart[$i]["attr"]='';
			if(!empty($invoice->invoice_item[$id]['product_attr_cart'])) {
				@$attrib = explode("\r\n", $invoice->invoice_item[$id]['product_attr_cart']); 
				foreach($attrib as $attr)  {
					$attributei = explode('==', $attr);
					if(!empty($attributei[0]) && !empty($attributei[1])) $smart[$i]["attr"] .= "<U>" . $attributei[0] . "</U> : ". $attributei[1] . "<BR>";					 
				} 
			}
								
			// get all children of this item   
			$ii=0;   
			$resultassoc = $db->Execute(sqlSelect($db,"cart","*","cart_parent_id=::{$result->fields["id"]}:: AND id!=::{$result->fields["id"]}::"));
			if($resultassoc && $resultassoc->RecordCount()) {
				while(!$resultassoc->EOF) { 
					$id=$resultassoc->fields["id"];
					$this->addInvoiceItem($id, $resultassoc, $invoice, $smart, $i, $ii, true, $taxObj, $discountObj); 
			 		// domain
					if($resultassoc->fields["cart_type"] == 2 ) { 	 
						$smart[$i]["assoc"][$ii]["price"] = $invoice->invoice_item["$id"]["total_amt"];
						$smart[$i]["assoc"][$ii]["tld_arr"] = $invoice->tld_arr["$id"];
						$smart[$i]["assoc"][$ii]["sku"] = 'DOMAIN-'. strtoupper($result->fields["host_type"]);
					}
					$resultassoc->MoveNext();
				}
			}  
			$result->MoveNext();
			$i++;
		} 	 
	}

	/**
     * View Cart Contents 
     */
	function view($VAR) { 
		global $smarty;
		$db = &DB(); 
		
		// get cart contents RS
		$result=$this->get_contents($db);
		if($result->RecordCount() == 0) {
			$smarty->assign('result', '0');
			return false;
		}		
		
		// init invoice object
		include_once(PATH_MODULES.'invoice/invoice.inc.php');
		$invoice = new invoice;
		$invoice->initNew(0); 
		$invoice->taxable=false;
		$invoice->discount=false;		

		$smart=''; 
		$this->put_contents_invoice($db, $result, $invoice, $smart);	
		 
		$smarty->assign('results',    count($invoice->invoice_item));
		$smarty->assign('cart', 	  $smart); 
	}
	
	/**
	 * Run a cart item through the invoice class to retrieve totals, discounts, taxes, attributes, etc.
	 *
	 * @param int $id
	 * @param array $result
	 * @param object $invoice
	 * @param array $smart
	 * @param int $i
	 * @param int $assoc
	 */
	function addInvoiceItem($id, &$result, &$invoice, &$smart, $i, $ii=false, $assoc=false, $taxObj=false, $discountObj=false) {
		    
			$sku=false;
			$price_base=false;
			$price_setup=false;
			$discount_amt=false;
			$taxable='verify';
			if($result->fields["cart_type"]==3) $taxable = $result->fields['ad_hoc_taxable'];
			if(!empty($result->fields['ad_hoc_sku'])) $sku=$result->fields['ad_hoc_sku'];
			if(!empty($result->fields['ad_hoc_amount'])) $price_base = $result->fields['ad_hoc_amount'];
			if(!empty($result->fields['ad_hoc_setup'])) $price_base = $result->fields['ad_hoc_setup'];
			if(!empty($result->fields['ad_hoc_discount'])) $discount_amt = $result->fields['ad_hoc_discount'];
			if(!empty($result->fields['product_attr']) && !is_array($result->fields['product_attr'])) $attributes=unserialize($result->fields['product_attr']);
		 
			
			$invoice->addItem(	
				$id,
				$taxObj,
				$discountObj,
				$result->fields['cart_type'], 
				$taxable,
				$result->fields['service_id'],
				false,
				$result->fields['product_id'],
				@$attributes,
				$result->fields['ad_hoc_name'],
				$sku,
				$result->fields['quantity'],
				$price_base,
				$price_setup,
				$discount_amt,
				$result->fields['recurr_schedule'],
				false,
				false,
				$result->fields['domain_name'],
				$result->fields['domain_tld'],
				$result->fields['domain_term'],
				$result->fields['host_type']
			);			
 
			if(!$assoc)
			@$smart[$i] = $result->fields; 
			else 
			@$smart[$i]["assoc"][$ii] = $result->fields; 
			
			if(!$assoc)
			@$smart[$i]["product"] = $invoice->product["$id"];
			else 
			@$smart[$i]["assoc"][$ii]["product"] = $invoice->product["$id"];
			
			if(!$assoc)
			@$smart[$i]["price_base"] = $invoice->invoice_item["$id"]["price_base"];
			else 
			@$smart[$i]["assoc"][$ii]["price_base"] = $invoice->invoice_item["$id"]["price_base"];
			
			if(!$assoc)
			@$smart[$i]["price_setup"] = $invoice->invoice_item["$id"]["price_setup"];
			else 
			@$smart[$i]["assoc"][$ii]["price_setup"] = $invoice->invoice_item["$id"]["price_setup"];
					
	}
	
	/**
     * Start add to cart process 
     */
	function add($VAR) {
		# set the current account
		if(empty($this->account_id)) $this->account_id = SESS_ACCOUNT;
		if(empty($this->session_id)) $this->session_id = SESS;
 
		# Determine the type to be added to the domain: (domain or product)
		if(!empty($VAR["product_id"]))
		{
			if($this->validate_product($VAR, $VAR["product_id"], $this->account_id))  {
				if(!empty($VAR["domain_type"]))  {
					# Add hosting / domain
					$this->product_add_host($VAR);
					return true;
				} else {
					# Standard product (no hosting or domain)
					$this->product_add($VAR);
					return true;
				}
			}
		} else if (!empty($VAR["domain_name"]) && !empty($VAR["domain_tld"])) {
			# Add Domain only:
			if(is_array($VAR["domain_name"])) {
				for($i=0;$i<count($VAR["domain_name"]); $i++) {
					if($this->validate_domain($VAR["domain_name"][$i], $VAR["domain_tld"][$i])) {
						$this->domain_add($VAR["domain_name"]["$i"], $VAR["domain_tld"]["$i"], @$VAR["host_type"]["$i"], @$VAR["domain_term"]["$i"]);
					}
				}
			} else {
				if($this->validate_domain($VAR["domain_name"], $VAR["domain_tld"])) {
					$this->domain_add($VAR["domain_name"], $VAR["domain_tld"], @$VAR["host_type"], @$VAR["domain_term"]);
				}
			}
		}
	}

	/**
     * Add a domain name to cart
     *
     * @param string $domain
     * @param string $tld
     * @param int $type
     * @param int $term 
     */ 
	function domain_add($domain, $tld, $type, $term=false) { 
		if(empty($type)) return false;
		$db = &DB();
		if(!$term || empty($term) || !is_numeric($term)) {
			# get the default term for this domain: 
			$rs = $db->Execute(sqlSelect($db,"host_tld","default_term_new","name=::$tld::"));
			if($rs === false || empty($rs->fields['default_term_new'])) $term = 1; else $term = $rs->fields['default_term_new'];
		} 
		// insert into cart
		$fields=Array('date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>$this->account_id, 
					  'cart_type'=>2, 'host_type'=>$type, 'domain_term'=>$term, 'domain_name'=>$domain, 'domain_tld'=>$tld );
		$db->Execute(sqlInsert($db,"cart",$fields));	 
	}
	
	/**
     * Add an ad-hoc line item to the cart 
     */
	function ad_hoc($VAR) {
		$db = &DB();
		if(!empty($VAR['account_id'])) { 
			$rs = $db->Execute( sqlSelect($db,"session","id","account_id=::{$VAR['account_id']}::"));
			if($rs && !empty($rs->fields['id'])) {
				$this->session_id = $rs->fields['id'];
			} else {
				$this->session_id = SESS;
			}
		} else {
			return false;
		}

		if(empty($VAR["ad_hoc_sku"]) || empty($VAR["ad_hoc_name"]) || $VAR["ad_hoc_amount"] == "") {
			global $C_debug,$C_translate;
			$C_debug->alert( $C_translate->translate('ad_hoc_err', 'cart',''));
			return false;
		} 
		
		if(empty($VAR["quantity"])) $qty = 1; else $qty = $VAR["quantity"];
		if(empty($VAR["ad_hoc_taxable"])) $VAR["ad_hoc_taxable"] = 0;
 
		# Attribs: (ad_hoc_attr_var & ad_hoc_attr_val)
		for($i=0;$i<count($VAR['ad_hoc_attr_var']); $i++)
		{
			if($VAR['ad_hoc_attr_var'][$i] != '' && $VAR['ad_hoc_attr_val'][$i] != '')
			{
				$attr["{$VAR['ad_hoc_attr_var'][$i]}"] = $VAR['ad_hoc_attr_val'][$i];
			}
		}
		if(!empty($attr)) $attrib = serialize($attr); else $attrib = '';
 
		// Create the record
		$fields=Array( 'date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>$VAR['account_id'], 'product_attr'=>$attrib, 
				'cart_type'=>3, 'quantity'=>$qty, 'ad_hoc_sku'=>$VAR["ad_hoc_sku"], 'ad_hoc_name'=>$VAR["ad_hoc_name"], 
				'ad_hoc_amount'=>$VAR["ad_hoc_amount"], 'ad_hoc_taxable'=>$VAR["ad_hoc_taxable"]);
		$db->Execute(sqlInsert($db,"cart",$fields)); 
	}

	/**
     * Add a product to the cart 
     */
	function product_add($VAR) {
		if(empty($VAR["quantity"])) $qty = 1; else $qty = $VAR["quantity"];
		if(!empty($VAR["attr"])) @$attr = serialize($VAR["attr"]); else $attr  = "";
		
		// Create the Main DB Record:
		$db = &DB(); 
		$fields=Array( 'date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>@$VAR['account_id'], 'product_attr'=>$attr, 
				'cart_type'=>0, 'quantity'=>$qty, 'product_id'=>$VAR["product_id"], 'recurr_schedule'=>@$VAR["recurr_schedule"], 'service_id'=>@$VAR["service_id"]);
		$db->Execute(sqlInsert($db,"cart",$fields)); 				
	}

	/**
     * Add an assoc required product
     *
     * @param int $product_id
     */
	function product_req_add($product_id) { 
		$db = &DB();
		$id = $db->GenID(AGILE_DB_PREFIX . "" . 'cart_id');
		$fields=Array( 'date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>$this->account_id, 'product_attr'=>$attr, 
				'cart_type'=>0, 'quantity'=>1, 'product_id'=>$product_id, 'recurr_schedule'=>@$VAR["recurr_schedule"]);
		$db->Execute(sqlInsert($db,"cart",$fields));    
	}

	/**
     * Add a product to the cart 
     */
	function product_add_host($VAR) { 
		if(!empty($VAR["attr"])) @$attr = serialize($VAR["attr"]); else $attr = serialize(Array(""));
		$db = &DB(); 
		$fields=Array( 'date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>@$VAR['account_id'], 'product_attr'=>$attr, 
				'cart_type'=>1, 'quantity'=>1, 'product_id'=>$VAR["product_id"], 'recurr_schedule'=>@$VAR["recurr_schedule"], 'service_id'=>@$VAR["service_id"],
				'host_type'=>$VAR["domain_type"], 'domain_name'=>$VAR["domain_name"], 'domain_tld'=>$VAR["domain_tld"] );		
		$id=sqlGenID($db,"cart");
		$db->Execute(sqlInsert($db,"cart",$fields, $id)); 

		// Get the default domain registration length:
		if ( $VAR["domain_type"] == "transfer" || $VAR["domain_type"] == "register" ) {
			$domain_term = 1; 
			$result = $db->Execute(sqlSelect($db,"host_tld","default_term_new","name=::{$VAR["domain_tld"]}::"));
			if(!empty($result->fields["default_term_new"])) $domain_term = $result->fields["default_term_new"];
		}

		// add child domain if register or transfer
		if($VAR["domain_type"] == "transfer" || $VAR['domain_type'] == "register") { 			
			$fields=Array( 'date_orig'=>time(), 'session_id'=>$this->session_id, 'account_id'=>@$VAR['account_id'], 'product_attr'=>$attr, 
					'cart_type'=>2, 'quantity'=>1, 'host_type'=>$VAR["domain_type"], 'domain_name'=>$VAR["domain_name"], 'domain_tld'=>$VAR["domain_tld"], 
					'domain_term'=>$domain_term, 'cart_parent_id'=> $id );
			$db->Execute(sqlInsert($db,"cart",$fields));  
		}  
	}

	/**
     * Validate A Domain
     *
     * @param string $domain
     * @param string $tld
     * @return bool
     */
	function validate_domain($domain, $tld) {
		# check that it is not already in the user's cart:
		$db = &DB(); 
		$result = $db->Execute(sqlSelect($db,"cart","id","(session_id=::$this->session_id:: OR account_id=::".SESS_ACCOUNT."::) AND domain_name=:: AND domain_tld=::$tld::"));
		if($result && $result->RecordCount()) {
			global $C_debug, $C_translate;
			$C_debug->alert($C_translate->translate('err_prod_already','cart',''));
			return false;
		} else {
			return true;
		}
	}

	/**
     * Validate A Product
     *
     * @param array $VAR
     * @param int $product_id
     * @param int $account_id
     * @return bool
     */
	function validate_product($VAR, $product_id, $account_id)
	{
		global $C_translate, $C_debug, $C_auth;
		$db  = &DB();

		# can user add inactive items
		if($C_auth->auth_method_by_name('invoice','add')) $active = ''; else $active = " AND active=1 ";

		# validate that product exists 
		$result = $db->Execute(sqlSelect($db,"product","*","id=::$product_id:: $active"));
		if($result->RecordCount() == 0) {
			$C_debug->alert($C_translate->translate('err_no_prod','cart',''));
			return false;
		}

		# check that product is not already in cart
		if($result->fields['cart_multiple'] != "1" && empty($VAR['service_id'])) { 
			$rs = $db->Execute(sqlSelect($db,"cart","id","product_id=::$product_id:: AND session_id=::$this->session_id::"));
			if($rs->RecordCount() > 0) {
				$C_debug->alert($C_translate->translate('err_prod_already','cart',''));
				return false;
			}
		}

		# Validate groups:
		$groups = unserialize($result->fields['group_avail']);
		$auth = false;
		for($ii=0; $ii<count($groups); $ii++) {
			if($C_auth->auth_group_by_id($groups[$ii])) {
				$auth = true;
				break;
			}
		}
		if(!$auth) return false;


		# Validate any required products:
		if(!empty($result->fields["assoc_req_prod"]))
		$reqarr     = unserialize($result->fields["assoc_req_prod"]); else $reqarr     = false;
		$reqtype    = $result->fields["assoc_req_prod_type"];
		$assoc      = true;

		if(is_array($reqarr) && !empty($reqarr[0])) {
			/*
			if($reqtype == 0 && is_array($reqarr)) {
				# ALL are required
				for($i=0; $i<count($reqarr); $i++)
				if(!$this->assoc_prod($reqarr[$i])) {
					$assoc = false;

					# Add the required product:
					$this->product_req_add($reqarr[$i]);
				}
			} elseif ($reqtype == 1 && is_array($reqarr)) {
				# ONE is required
				$assoc = false;
				for($i=0; $i<count($reqarr); $i++) {
					if($this->assoc_prod($reqarr[$i])) {
						$assoc = true;
						$i = count( $reqarr );
					} else {
						# add the required product:
						$this->product_req_add($reqarr[$i]);
					}
				}
			}*/
			
			if(!SESS_LOGGED) {
				$C_debug->alert($C_translate->translate('err_assoc_login','cart',''));
				return false;
			} 
			
			$items='<br>';	
			foreach($reqarr as $prod_id) { 
				$prodrs = $db->Execute(sqlSelect($db,"product_translate","*","product_id=$prod_id AND language_id = ::".SESS_LANGUAGE."::"));
				if($prodrs && $prodrs->RecordCount()) {
					$items .= "- <b><a href=\"?_page=product:details&id=$prod_id\">{$prodrs->fields['name']}</a></b><br>";
				}
			}	
			
			$C_translate->value("cart","items", $items);
			$msg = $C_translate->translate('err_assoc_req','cart','');
			if($reqtype == 0)
				$C_debug->alert($msg . " ". $C_translate->translate('assoc_all','cart',''));
			else 
				$C_debug->alert($msg ." ". $C_translate->translate('assoc_one','cart',''));
				
			return false;
		}

 
		# Product Plugin Level Validation
		$product = $result;
		if(!empty($product->fields['prod_plugin']) && !empty($product->fields['prod_plugin_data'])) {
			$prodplgfile = PATH_PLUGINS.'product/'. $product->fields['prod_plugin_file'] . '.php';
			if(is_file($prodplgfile)) {
				include_once(PATH_PLUGINS.'product/'. $product->fields['prod_plugin_file'] . '.php');
				eval('$prodplg = new plgn_prov_'. $product->fields['prod_plugin_file'] .';');
				if(is_object($prodplg)) {
					if(is_callable(array($prodplg, 'validate_cart'))) {
						$result = $prodplg->validate_cart($VAR, $product);
						if($result === true) { } else {
							$C_debug->alert($result);
							return false;
						}
					}
				}
			}
		} 

		# Service upgrade
		if(!empty($VAR['service_id'])) {
			# validate account logged in
			if(SESS_LOGGED == false) return false;
			$dbm = new CORE_database;

			# validate account owns service, service is modifyable, active, not canceled, and exists
			$rs = $db->Execute( $sql = $dbm->sql_select( "service", "*", "recur_modify = 1 AND active = 1 AND ( suspend_billing IS NULL OR suspend_billing = 0 ) AND account_id = ".SESS_ACCOUNT." AND id = {$VAR['service_id']}", "", $db ) );
			if($rs === false || $rs->RecordCount() == 0) return false;

			# validate selected product_id is in allowed modify array for selected service
			if(empty($rs->fields['product_id'])) return false;
			$product_id = $rs->fields['product_id'];
			$prod = $db->Execute( $dbm->sql_select( "product", "*", "id = $product_id", "", $db ) );
			if($prod === false || $prod->RecordCount() == 0) return false;
			$arr = unserialize( $prod->fields['modify_product_arr'] );
			if(!is_array($arr) || count($arr) == 0 || empty($arr[0])) return false;
			$do = false;
			foreach($arr as $pid) if( $pid == $VAR['product_id'] ) { $do = true; break; }
			if(!$do) return false;

			# make sure this service is not in the cart
			$sql    = 'DELETE FROM ' . AGILE_DB_PREFIX . 'cart WHERE site_id     = ' . $db->qstr(DEFAULT_SITE) 	. ' AND service_id  = ' . $db->qstr($VAR['service_id']) ;
			$rs = $db->Execute($sql);

			# make sure this service has no outstanding invoices:
			$p = AGILE_DB_PREFIX;
			$sql = "SELECT DISTINCT {$p}invoice.id, {$p}invoice_item.id
	            		FROM {$p}invoice,{$p}invoice_item
	            		WHERE {$p}invoice.site_id = ".DEFAULT_SITE." AND {$p}invoice_item.site_id = ".DEFAULT_SITE." 
	            		AND {$p}invoice_item.service_id = ".$db->qstr($VAR['service_id'])." 
	            		AND {$p}invoice_item.invoice_id = {$p}invoice.id  AND {$p}invoice.billing_status != 1";
			$rs = $db->Execute($sql);
			if($rs->RecordCount() > 0) {
				echo "Invoice(s) in unpaid status for selected service ID {$VAR['service_id']}, cannot upgrade";
				return false;
			}
		} 
		return true;
	}


	/**
    * Validate Associated Prod Req.
    *
    * @param int $product_id
    * @return bool
    */
	function assoc_prod($product_id)
	{
		# check if required assoc product is in cart
		$db = &DB();
		$p 	= AGILE_DB_PREFIX; 
		$rs = $db->Execute(sqlSelect($db,"cart","*","product_id=::$product_id:: AND session_id=::".SESS."::"));
		if($rs->RecordCount() > 0) {
			return true;
		} else if (SESS_LOGGED) {
			# check if required assoc product has been purchased
			$sql ="SELECT {$p}invoice.account_id, {$p}invoice_item.product_id
                FROM {$p}invoice_item LEFT JOIN {$p}invoice ON {$p}invoice_item.invoice_id = {$p}invoice.id
                WHERE   {$p}invoice.account_id       = " . $db->qstr(SESS_ACCOUNT) . "
                AND     {$p}invoice_item.product_id  = " . $db->qstr( $product_id ) . "
                AND     {$p}invoice.process_status   = " . $db->qstr( "1" ) . "
                AND     {$p}invoice.site_id          = " . $db->qstr(DEFAULT_SITE) . "
                AND     {$p}invoice_item.site_id     = " . $db->qstr(DEFAULT_SITE);
			$rs = $db->Execute($sql);
			$rs->RecordCount();
			if($rs->RecordCount() > 0)  return true;
		}
		return false;
	}
}
?>