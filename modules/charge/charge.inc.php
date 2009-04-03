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
	
class charge
{
	var $xmlrpc=false;

	# Open the constructor for this mod
	function charge_construct()
	{  
		# name of this module:
		$this->module = "charge";

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

	function sweep_daily() {
		$this->sweep('0');
	}

	function sweep_weekly() {
		$this->sweep('1');
	}

	function sweep_monthly() {
		$this->sweep('2');
	}

	function sweep_quarterly() {
		$this->sweep('3');
	}

	function sweep_semi_annually() {
		$this->sweep('4');
	}

	function sweep_annually() {
		$this->sweep('5');
	}										

	##############################
	##		API   		        ##
	##############################
	function sweep($type)
	{   
		$this->charge_construct();

		include_once(PATH_MODULES.'account_billing/account_billing.inc.php');
		$account_billing = new account_billing; 

		include_once(PATH_MODULES . 'tax/tax.inc.php');
		$taxObj = new tax;		

		include_once(PATH_MODULES . 'discount/discount.inc.php');

		$db = &DB();
		$sql = "SELECT DISTINCT
					".AGILE_DB_PREFIX."charge.id,
					".AGILE_DB_PREFIX."charge.account_id,
					".AGILE_DB_PREFIX."charge.service_id,
					".AGILE_DB_PREFIX."charge.amount,
					".AGILE_DB_PREFIX."charge.taxable,
					".AGILE_DB_PREFIX."charge.attributes,
					".AGILE_DB_PREFIX."charge.quantity,
					".AGILE_DB_PREFIX."charge.product_id,
					".AGILE_DB_PREFIX."charge.description,  
					".AGILE_DB_PREFIX."account.affiliate_id,
					".AGILE_DB_PREFIX."account.reseller_id,
					".AGILE_DB_PREFIX."account.country_id,
					".AGILE_DB_PREFIX."account.currency_id, 
					".AGILE_DB_PREFIX."account.state 
				FROM 
					".AGILE_DB_PREFIX."charge  		
				LEFT JOIN 
					".AGILE_DB_PREFIX."account 			
				ON 
					".AGILE_DB_PREFIX."account.id 		 = " . AGILE_DB_PREFIX."charge.account_id 							
				WHERE  
					".AGILE_DB_PREFIX."charge.site_id 	 = " . $db->qstr(DEFAULT_SITE) . " 
				AND 
					".AGILE_DB_PREFIX."account.site_id 	 = " . $db->qstr(DEFAULT_SITE) . "			
				AND
					".AGILE_DB_PREFIX."charge.status 	 = " . $db->qstr('0') ." 
				AND
					".AGILE_DB_PREFIX."charge.sweep_type = " . $db->qstr($type) ."
				ORDER BY
					".AGILE_DB_PREFIX."charge.account_id"; 
		$rs = $db->Execute($sql);   
		if ($rs === false) {
			global $C_debug;
			$C_debug->error('charge.inc.php','charge :: sweep()', $db->ErrorMsg(). "\r\n\r\n". $sql); 
			return false;
		}				

		$account_id = false;
		$invoice_id = false; 
		$i 			= false;
		$i_total 	= false;

		$invoice_count = 0;
		$sweep_count = 0;

		while(!$rs->EOF)
		{  			
			if( $rs->fields['account_id'] != $account_id )
			{   
				$account_id = $rs->fields['account_id']; 
				$i=0;
				$i_total = $this->count_account_charges($account_id, $rs->CurrentRow(), $rs);

				$sub_total = 0;
				$taxable_amount = 0;
				$this_discount_total = 0;
				$tax_amt = 0;
				$discount_amt = 0;				    

				# Start a new transaction
				$trans = &DB();
				$trans->StartTrans();

				# Start a new invoice 
				$invoice_id = $db->GenID(AGILE_DB_PREFIX . 'invoice_id'); 	

				# check for any discounts for the parent invoice or account_id (applied at checkout and should continue to be applied if recurring type discount)
				$discountObj = new discount;

				# get parent invoice id if service specified (for discount checking)
				$parent_invoice_id = false;
				if($rs->fields['service_id']) {						
					$parentinv = $db->Execute(sqlSelect($db,"service","invoice_id","id={$rs->fields['service_id']}"));
					if($parentinv && $parentinv->RecordCount()) {
						$parent_invoice_id = $parentinv->fields['invoice_id'];
					}
				}

				# get available discounts to this account/service	
				$discountObj->available_discounts($account_id, 1, $parent_invoice_id);				  					
			}



			###########################
			##### LINE ITEM ACTIONS ###
			###########################

			if( !empty($account_id) )
			{
				### Get the line item id
				$invoice_item_id = $db->GenID(AGILE_DB_PREFIX . 'invoice_item_id');

				### Set the invoice item details:
				$product_id = $rs->fields['product_id'];
				if(!empty($product_id) && empty($this->product["$product_id"]))
				{
					$sql = "SELECT sku FROM ".AGILE_DB_PREFIX."product WHERE  
							id 	= " . $db->qstr($product_id) . " AND
							site_id = " . $db->qstr(DEFAULT_SITE); 
					$prod = $db->Execute($sql);   
					if($prod->RecordCount() > 0)
					{
						$sku = $prod->fields['sku'];
						$this->product["$product_id"] = $sku;
						$product_attr = '';
						if(!empty($rs->fields['description']))
							$product_attr = "Description=={$rs->fields['description']}\r\n";
						$product_attr .= $rs->fields['attributes'];
					}						
					else
					{
						$sku = $rs->fields['description'];
						$this->product["$product_id"] = $sku;
						$product_attr = $rs->fields['attributes'];
					}
				} elseif (!empty($this->product["$product_id"])) {
					$sku = $this->product["$product_id"];
					$product_attr = $rs->fields['attributes'];
				} else {
					$sku = $rs->fields['description'];
					$product_attr = $rs->fields['attributes'];
				}

				$quantity = $rs->fields['quantity'];
				$price_base = $rs->fields['amount']; 
				$item_total_amt = ($price_base * $quantity);

				// Calculate any recurring discounts for this account					
				$item_discount_amt = $discountObj->calc_all_discounts(1, $invoice_item_id, $rs->fields['product_id'], $item_total_amt, $account_id, $sub_total+$item_total_amt);												
				$item_total_amt -= $item_discount_amt;					
				$sub_total += $item_total_amt;
				$discount_amt += $item_discount_amt;

				# calculate any taxes for this item
				$item_tax_amt=0;  
				if($rs->fields['taxable']) {  
					$item_tax_arr = $taxObj->calculate($item_total_amt, $rs->fields['country_id'], $rs->fields['state']);				   
					if(is_array($item_tax_arr)) foreach($item_tax_arr as $tx) $item_tax_amt += $tx['rate']; 
					$tax_amt += $item_tax_amt; 
				}  

				### Add line item to new invoice
				$sql	= "INSERT INTO ".AGILE_DB_PREFIX."invoice_item SET
						id					    = ".$db->qstr( $invoice_item_id ) .",
						site_id				    = ".$db->qstr( DEFAULT_SITE ).",
						invoice_id			    = ".$db->qstr( $invoice_id ).",
						account_id				= ".$db->qstr( $account_id ).",
						date_orig			    = ".$db->qstr( time() ).",
						product_id			    = ".$db->qstr( $product_id ).",
						sku					    = ".$db->qstr( $sku ).",
						quantity			    = ".$db->qstr( $quantity ).",
						item_type			    = ".$db->qstr( '0' ).",
						product_attr		    = ".$db->qstr( $product_attr ).", 
						price_type              = ".$db->qstr( '0' ).",
						price_base			    = ".$db->qstr( $price_base ).",
						price_setup			    = ".$db->qstr( 0 ) .",
						tax_amt					= ".$db->qstr($item_tax_amt) . ",
						total_amt				= ".$db->qstr($item_total_amt) . ",
						discount_amt			= ".$db->qstr($item_discount_amt);
				$trans->Execute($sql);		

				# Insert tax records
				$taxObj->invoice_item($invoice_id, $invoice_item_id, $account_id, @$item_tax_arr);	        					 

				# Insert discount records
				$discountObj->invoice_item($invoice_id, $invoice_item_id, $account_id);

				### Update this charge status to billed
				$sql  = "UPDATE ".AGILE_DB_PREFIX."charge SET
						status   	= ".$db->qstr( '1' ) ." 
						WHERE
						site_id	    = ".$db->qstr( DEFAULT_SITE )." AND 
						id		    = ".$db->qstr( $rs->fields['id'] ) ;
				$trans->Execute($sql);	 
				$i++;		
				$sweep_count++;	
			}



			#######################
			### INVOICE ACTIONS ###
			####################### 
			if($i_total == $i || $i == $rs->RecordCount())
			{  
				if( $invoice_id )
				{  
					### Get the most recent billing id for this client:
					if(!isset($billing_id["$account_id"]))
					{
						$billing_arr = $account_billing->default_billing($account_id);							
						$billing_id["$account_id"] = $billing_arr['billing_id'];
						$checkout_plugin_id["$account_id"] = $billing_arr['checkout_plugin_id'];							
					} 

					### Affiliate & Reseller info:
					$affiliate_id = $rs->fields['affiliate_id'];
					$reseller_id  = $rs->fields['reseller_id'];
					$actual_billed_currency_id = $rs->fields['currency_id'];				 

					# calculate any taxes 
					@$total = $sub_total + $tax_amt;

					if($total <= 0) {
						$process_status = 1;
						$billing_status = 1;
					} else {
						$process_status = 0;
						$billing_status = 0;			            	
					}

					### Generate the invoice insert SQL:
					$sql = "INSERT INTO ".AGILE_DB_PREFIX."invoice SET
								id							= ".$db->qstr($invoice_id).",
								site_id						= ".$db->qstr(DEFAULT_SITE).",
								date_orig					= ".$db->qstr(time()).",
								date_last					= ".$db->qstr(time()).",
								process_status				= ".$db->qstr($process_status).",
								billing_status				= ".$db->qstr($billing_status).",
								print_status				= ".$db->qstr('0').",
								account_id					= ".$db->qstr($account_id).",
								account_billing_id			= ".$db->qstr($billing_id["$account_id"]).",
								affiliate_id				= ".$db->qstr($affiliate_id).",
								reseller_id					= ".$db->qstr($reseller_id).",
								checkout_plugin_id			= ".$db->qstr($checkout_plugin_id["$account_id"]).",  
								tax_amt						= ".$db->qstr($tax_amt).", 
								discount_amt				= ".$db->qstr($discount_amt).",
								actual_billed_currency_id	= ".$db->qstr($actual_billed_currency_id).",
								actual_billed_amt			= ".$db->qstr('0').",
								billed_currency_id			= ".$db->qstr(DEFAULT_CURRENCY).",
								billed_amt					= ".$db->qstr('0').",
								total_amt					= ".$db->qstr($total).",
								notice_count				= ".$db->qstr('0').",
								notice_max					= ".$db->qstr(MAX_BILLING_NOTICE).",
								notice_next_date			= ".$db->qstr(time()).",
								grace_period				= ".$db->qstr(GRACE_PERIOD).",
								due_date					= ".$db->qstr(time());
					$trans->Execute($sql); 					

					### Close this transaction 
					$trans->CompleteTrans(); 

					$i_total = false;
					$i = false; 
					$account_id = false;
					$invoice_id = false;  	
					$discount = false;
					$cookie = false; 						
					$invoice_count++;
				}
			}   
			$rs->MoveNext();
		}

		global $C_debug;
		$C_debug->alert("Swept $sweep_count Charge(s) into $invoice_count Invoice(s).");
		return true;
	} 



	### Get total charges for an account
	function count_account_charges($account, $start_pos, &$rs)
	{
		$rs->Move($start_pos);
		$i = 0;
		while(!$rs->EOF)
		{ 
			if($rs->fields['account_id'] != $account)
			{
				$rs->Move($start_pos);
				return $i;
			}
			$i++;
			$rs->MoveNext();
		}
		$rs->Move($start_pos);
		return $i;				
	}



	##############################
	##		API   		        ##
	##############################
	function api($VAR)
	{
		$db = &DB();

		# amount
		if(@$VAR['amount'] <= 0) {
			return $this->api_return(0,'','Invalid value supplied for the \'amount\' parameter, must be greater than 0'); 
		} else {
			$amount = $VAR['amount'];
		}

		# sweep_type
		if(@$VAR['sweep_type'] <= 6) { 
			$sweep_type = $VAR['sweep_type']; 
		} else {
			return $this->api_return(0,'','Invalid value supplied for the \'sweep_type\' parameter, must be 0-6'); 		
		}

		# account_id OR service_id
		if(empty($VAR['account_id']) && empty($VAR['service_id'])) { 
			return $this->api_return(0,'','Either the \'account_id\' or \'service_id\' parameter must be provided'); 
		} else {

			# check the account id 
			if(!empty($VAR['account_id']))
			{ 
				$sql = "SELECT * FROM ".AGILE_DB_PREFIX."account WHERE
						id = " . $db->qstr($VAR['account_id']) . " OR
						username = " . $db->qstr($VAR['account_id']) . " AND
						site_id = " . $db->qstr(DEFAULT_SITE);
				$rs = $db->Execute($sql);   
				if ($rs === false) {
					global $C_debug;
					$C_debug->error('charge.inc.php','charge :: api()', $db->ErrorMsg(). "\r\n\r\n". $sql); 						
				}	
				if($rs->RecordCount() == 1)
				{
					$account_id = $rs->fields['id'];
				} 
				else
				{
					return $this->api_return(0,'','The \'account_id\' value provided does not exist'); 			
				}
			}

			# check the service id 
			elseif(!empty($VAR['service_id']))
			{				 
				$sql = "SELECT id,account_id FROM ".AGILE_DB_PREFIX."service WHERE 
						site_id = " . $db->qstr(DEFAULT_SITE) . " AND
						id = " . $db->qstr($VAR['service_id']);
				$rs = $db->Execute($sql);   
				if ($rs === false) {
					global $C_debug;
					$C_debug->error('charge.inc.php','charge :: api()', $db->ErrorMsg(). "\r\n\r\n". $sql); 						
				}	
				if($rs->RecordCount() == 1)
				{
					$service_id = $VAR['service_id'];
					$account_id = $rs->fields['account_id'];
				} else {
					return $this->api_return(0,'','The \'service_id\' value provided does not exist'); 			
				}					
			} 
		} 		

		# taxable
		if(empty($VAR['taxable']))	
			$taxable = 0;
		else
			$taxable = $VAR['taxable'];

		# attributes
		if(!empty($VAR['attributes'])) {  
			@$attributes = ereg_replace("@@", "\r\n", $VAR['attributes']);
			@$attributes = ereg_replace("--", "==", $attributes);
		} else {
			$attributes = false;
		}

		# quantity
		if(empty($VAR['quantity']))	
			$quantity = 1;
		else
			$quantity = $VAR['quantity'];

		# product id
		if(empty($VAR['product_id'])) {
			$product_id = false;
		} else { 
			$product_id = $VAR['product_id'];	
		}

		# description
		if(empty($VAR['description'])) {
			$description = false;
		} else { 
			$description = $VAR['description'];	
		}		

		/* Start: SQL Insert Statement */ 
		$sql = "SELECT * FROM ".AGILE_DB_PREFIX."charge WHERE id = -1";
		$rs = $db->Execute($sql); 

		$id = $db->GenID(AGILE_DB_PREFIX . 'charge_id');
		$insert = Array (	'id' 			=> $id,
							'site_id' 		=> DEFAULT_SITE,
							'date_orig' 	=> time(),
							'status'	 	=> 0,
							'sweep_type' 	=> $sweep_type,
							'account_id' 	=> @$account_id,
							'service_id' 	=> @$service_id,
							'product_id' 	=> @$product_id,
							'amount' 		=> $amount,
							'quantity' 		=> $quantity,
							'taxable' 		=> $taxable,
							'attributes' 	=> $attributes,
							'description' 	=> $description ); 
		$sql = $db->GetInsertSQL($rs, $insert);
		$result = $db->Execute($sql);  
		if ($result === false) {
			global $C_debug;
			$C_debug->error('charge.inc.php','charge :: api()', $db->ErrorMsg(). "\r\n\r\n". $sql);
			return $this->api_return(0,'','The SQL insert failed!'); 
		}						 
		else
		{
			return $this->api_return(1,$id,'');
		}  
		return true;
	}


	function api_return($status=0,$id='',$error='')
	{
		if(!$this->xmlrpc) {
			echo "status=={$status}++charge_id={$id}++error=={$error}";
		} else {
			$arr =  array('status'=>$status, 'charge_id'=>$id, 'error'=> $error); 
			#print_r($arr);
			return $arr;
		}
	}



	##############################
	##		ADD   		        ##
	##############################
	function add($VAR)
	{
		$this->charge_construct();
		if(!empty($VAR['attributes']))
		{
			$attr = false;
			for($i=0; $i<count($VAR['attributes']); $i++)
				if (!empty($VAR['attributes'][$i][0]))
					$attr .= "{$VAR['attributes'][$i][0]}=={$VAR['attributes'][$i][1]}\r\n";
			$VAR['charge_attributes'] = $attr;
		}

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
		$this->charge_construct();
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
		$this->charge_construct();
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
		$this->charge_construct();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}		

	##############################
	##	     SEARCH FORM        ##
	##############################
	function search_form($VAR)
	{
		$this->charge_construct();
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
		$this->charge_construct();

		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		$db = &DB();	

		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;

		# set the search criteria array
		$arr = $VAR;

		# loop through the submitted field_names to get the WHERE statement
		$where_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(eregi($pat, $key))
					{	 				
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " WHERE ".AGILE_DB_PREFIX."charge.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										$VAR['field_option']["$pat_field"]["$i_arr"];
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];
										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}


										if($i_arr == 0)
										{
											$where_list .= " WHERE ".AGILE_DB_PREFIX."charge.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
											$i++;
										}
										else
										{
										   $where_list .= " AND ".AGILE_DB_PREFIX."charge.".$field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										   $i++;
										}
								   }
								}
							}
							else
							{	
							   $where_list .= " WHERE ".AGILE_DB_PREFIX."charge.".$field . " = " . $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
			else
			{
				if($value != '')
				{
					$pat = "^" . $this->module . "_";
					if(eregi($pat, $key))
					{
						$field = eregi_replace($pat,"",$key);
						if(eregi('%',$value))
						{
						   # do any data conversion for this field (date, encrypt, etc...)
						   if(isset($this->field["$field"]["convert"]))
						   {
								$value = $validate->convert($field, $value, $this->field["$field"]["convert"]);
						   }

						   $where_list .= " AND ".AGILE_DB_PREFIX."charge.".$field . " LIKE " . $db->qstr($value, get_magic_quotes_gpc());
						   $i++;
						}
						else
						{
							# check if array
							if(is_array($value))
							{	
								for($i_arr=0; $i_arr < count($value); $i_arr++)
								{
								   if($value["$i_arr"] != '')
								   {
										# determine any field options (=, >, <, etc...)
										$f_opt = '=';
										$pat_field = $this->module.'_'.$field;
										if(isset($VAR['field_option']["$pat_field"]["$i_arr"]))
										{
										   $f_opt = $VAR['field_option']["$pat_field"]["$i_arr"];

										   # error checking, safety precaution
										   if($f_opt != '='  && $f_opt != '>'  && $f_opt != '<' && $f_opt != '>=' && $f_opt != '<=' && $f_opt != '!=')
											   $f_opt = '=';
										}

										# do any data conversion for this field (date, encrypt, etc...)
										if(isset($this->field["$field"]["convert"]))
										{
											$value["$i_arr"] = $validate->convert($field, $value["$i_arr"], $this->field["$field"]["convert"]);
										}

										$where_list .= " AND ".AGILE_DB_PREFIX."charge.". $field . " $f_opt " . $db->qstr($value["$i_arr"], get_magic_quotes_gpc());
										$i++;
								   }
								}
							}
							else
							{		
							   $where_list .=  " AND ".AGILE_DB_PREFIX."charge.". $field . " = ". $db->qstr($value, get_magic_quotes_gpc());
							   $i++;
							}
						}
					}
				}
			}
		}

		# Code for attribute searches:
		if(!empty($VAR['item_attributes']))
		{ 
			$attr_arr = $VAR['item_attributes'];
			for($ati=0; $ati<count($attr_arr); $ati++)
			{ 
				if(!empty($attr_arr[$ati]['0']))
				{
					if($where_list == '') 
						$where_list .= ' WHERE '; 
					else 
						$where_list .= ' AND ';						
					$where_list .= AGILE_DB_PREFIX."charge.attributes LIKE " . 
								$db->qstr("%{$attr_arr[$ati]['0']}=={$attr_arr[$ati]['1']}%");
				}
			}
		}


		#### finalize the WHERE statement
		if($where_list == '') 
			$where_list .= ' WHERE '; 
		else 
			$where_list .= ' AND '; 


		# get limit type
		if(isset($VAR['limit']))
		{
			$limit = $VAR['limit'];
		}
		else
		{
			$limit = $this->limit;
		}

		# get order by
		if(isset($VAR['order_by']))
		{
			$order_by = $VAR['order_by'];
		}
		else
		{
			$order_by = $this->order_by;
		}

		$q = "SELECT ".AGILE_DB_PREFIX."charge.id FROM ".AGILE_DB_PREFIX."charge "; 
		$q .= $where_list ." ".AGILE_DB_PREFIX."charge.site_id = " . $db->qstr(DEFAULT_SITE); 

		$q_save = "SELECT DISTINCT %%fieldList%%, ".AGILE_DB_PREFIX."charge.id FROM ".AGILE_DB_PREFIX."charge ";
		$q_save .= $where_list ." %%whereList%% ";

		#echo $q;
		#exit;

		# run the database query
		$result = $db->Execute($q);

		# error reporting
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('charge.inc.php','search', $db->ErrorMsg());	  
			return false;      	                    	       
		}

		# get the result count:
		$results = $result->RecordCount();

		# get the first record id:
		if($results == 1)  $record_id = $result->fields['id'];

		# define the DB vars as a Smarty accessible block
		global $smarty; 

		# Create the definition for fast-forwarding to a single record:
		if ($results == 1 && !isset($this->fast_forward))
		{
			$smarty->assign('record_id', $record_id);
		}

		# create the search record:
		if($results > 0)
		{
			# create the search record
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$arr['module'] 	= $this->module;
			$arr['sql']		= $q_save;
			$arr['limit']  	= $limit;
			$arr['order_by']= $order_by;
			$arr['results']	= $results;
			$search->add($arr);

			# define the search id and other parameters for Smarty
			$smarty->assign('search_id', $search->id);

			# page:
			$smarty->assign('page', '1');

			# limit:
			$smarty->assign('limit', $limit);

			# order_by:
			$smarty->assign('order_by', $order_by);
		}

		# define the result count
		$smarty->assign('results', $results);         	    
	}



	##############################
	##		SEARCH SHOW	        ##
	##############################

	function search_show($VAR)
	{
		$this->charge_construct();

		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);

		# set the field list for this method:
		$arr = $this->method[$type];
		$field_list = '';
		$i=0;
		while (list ($key, $value) = each ($arr))
		{
			if($i == 0)
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= AGILE_DB_PREFIX . "charge" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			else
			{
				$field_var =  $this->table . '_' . $value;
				$field_list .= "," . AGILE_DB_PREFIX . "charge" . "." . $value;

				// determine if this record is linked to another table/field
				if($this->field[$value]["asso_table"] != "")
				{
					$this->linked[] = array('field' => $value, 'link_table' => $this->field[$value]["asso_table"], 'link_field' => $this->field[$value]["asso_field"]);
				}
			}
			$i++;
		}


		# get the search details:
		if(isset($VAR['search_id'])) {
			include_once(PATH_CORE   . 'search.inc.php');
			$search = new CORE_search;
			$search->get($VAR['search_id']);
		} else {
			# invalid search!
			echo '<BR> The search terms submitted were invalid!';       # translate... # alert

			if(isset($this->trigger["$type"])) {
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			}
		}

		# get the sort order details:
		if(isset($VAR['order_by']) && $VAR['order_by'] != "") {
			$order_by = ' ORDER BY ' . $VAR['order_by'];
			$smarty_order =  $VAR['order_by'];
		} else  {
			$order_by = ' ORDER BY ' . $this->order_by;
			$smarty_order =  $search->order_by;
		}


		# determine the sort order
		if(isset($VAR['desc'])) {
			$order_by .= ' DESC';
			$smarty_sort = 'desc=';
		} else if(isset($VAR['asc']))  {
			$order_by .= ' ASC';
			$smarty_sort = 'asc=';
		} else {
			if (!eregi('date',$smarty_order)) {
				$order_by .= ' ASC';
				$smarty_sort = 'asc=';
			} else {
				$order_by .= ' DESC';
				$smarty_sort = 'desc=';
			}
		} 

		# generate the full query 
		$db = &DB();
		$q = eregi_replace("%%fieldList%%", $field_list, $search->sql);
		$q = eregi_replace("%%tableList%%", AGILE_DB_PREFIX.$construct->table, $q);
		$q = eregi_replace("%%whereList%%", "", $q);
		$q .= " site_id = " . $db->qstr(DEFAULT_SITE);
		$q .= $order_by;

		//////////////////
		# echo "<BR> $q <BR>";

		$current_page=1;
		$offset=-1;
		if (!empty($VAR['page'])) $current_page = $VAR['page'];
		if (empty($search->limit)) $search->limit=25; 
		if($current_page>1) $offset = (($current_page * $search->limit) - $search->limit);            
		$result = $db->SelectLimit($q, $search->limit, $offset);


		# error reporting
		if ($result === false)
		{		
			global $C_debug;
			$C_debug->error('charge.inc.php','search', $db->ErrorMsg());

			if(isset($this->trigger["$type"]))
			{
				include_once(PATH_CORE   . 'trigger.inc.php');
				$trigger    = new CORE_trigger;
				$trigger->trigger($this->trigger["$type"], 0, $VAR);
			} 
			return;                    	        	
		}


		# put the results into a smarty accessable array  
		$i=0;
		$class_name = TRUE;
		while (!$result->EOF) {
			$smart[$i] = $result->fields;

			if($class_name)
			{
				$smart[$i]['_C'] = 'row1';
				$class_name = FALSE;
			} else {
				$smart[$i]['_C'] = 'row2';
				$class_name = TRUE;
			}
			$result->MoveNext();
			$i++;
		}


		# get any linked fields
		if($i > 0)
		{
			$db_join = new CORE_database;
			$this->result = $db_join->join_fields($smart, $this->linked);
		}
		else
		{
			$this->result = $smart;
		} 

		# get the result count:
		$results = $result->RecordCount();

		# define the DB vars as a Smarty accessible block
		global $smarty;

		# define the results
		$smarty->assign($this->table, $this->result);
		$smarty->assign('page',		$VAR['page']);
		$smarty->assign('order',	$smarty_order);
		$smarty->assign('sort',		$smarty_sort);
		$smarty->assign('limit',	$search->limit);
		$smarty->assign('search_id',$search->id);
		$smarty->assign('results', 	$search->results);

		# get the total pages for this search:
		if(empty($search->limit))
			$this->pages = 1;
		else
			$this->pages = intval($search->results / $search->limit);
		if ($search->results % $search->limit) $this->pages++;

		# total pages
		$smarty->assign('pages', 	$this->pages);

		# current page
		$smarty->assign('page', 	$current_page);
		$page_arr = '';
		for($i=0; $i <= $this->pages; $i++)
		{
			if ($this->page != $i) 	$page_arr[] = $i;
		}

		# page array for menu
		$smarty->assign('page_arr',	$page_arr); 
	}	

	##############################
	##	   SEARCH EXPORT        ##
	##############################    	
	function search_export($VAR)
	{
		$this->charge_construct();

	   # require the export class    	
	   require_once (PATH_CORE   . "export.inc.php");

	   # Call the correct export function for inline browser display, download, email, or web save.
	   if($VAR["format"] == "excel")
	   {
		   $type = "export_excel";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_excel($VAR, $this, $type);    	
	   }

	   else if ($VAR["format"] == "pdf")
	   {
		   $type = "export_pdf";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_pdf($VAR, $this, $type);      	
	   }

	   else if ($VAR["format"] == "xml")
	   {
		   $type = "export_xml";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_xml($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "csv")
	   {
		   $type = "export_csv";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_csv($VAR, $this, $type);
	   }

	   else if ($VAR["format"] == "tab")
	   {
		   $type = "export_tab";
		   $this->method["$type"] = explode(",", $this->method["$type"]);
		   $export = new CORE_export;
			$export->search_tab($VAR, $this, $type);
	   }                                           	
	}      	

	##############################
	##	   IMPORT		        ##
	##############################    	
	function import($VAR)
	{
		$this->charge_construct();

		include_once(PATH_CORE.'import.inc.php');
		$import = new CORE_import;

		if(!empty($VAR['confirm']))
		{
			$import->do_import($VAR, $this);
		}
		else
		{
			$import->prepare_import($VAR, $this);
		} 
	}	 
}
?>