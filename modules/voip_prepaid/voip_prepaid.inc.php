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
 * @author Tony Landis <tony@agileco.com> and Thralling Penguin, LLC <http://www.thrallingpenguin.com>
 * @package AgileBill
 * @version 1.4.93
 */
	
class voip_prepaid
{

	# Open the constructor for this mod
	function voip_prepaid()
	{ 		
		$db =& DB();
		$rs = $db->Execute(sqlSelect($db,"voip","prepaid_low_balance","id=::".DEFAULT_SITE."::"));
		
		if ($rs && $rs->RecordCount() > 0)
		{
			# e-mail user's once when balance reaches this amount:
			$this->lowBalance = $rs->fields[0];
		}
		else
		{
			$this->lowBalance = false;
		}

		$this->pinLenth = 10; 		// up to 10

		# name of this module:
		$this->module = "voip_prepaid";

		# location of the construct XML file:
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";

		# open the construct file for parsing
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);

		$this->method   = $construct["construct"]["method"]; 
		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"];
	}

	/** generate a new pin */
	function genPin()
	{
		for($trys=0; $trys<=10; $trys++)
		{
			$rand = rand(1000000000,9999999999);
			$pin = substr($rand,0,$this->pinLenth);

			// check if unique
			$db=&DB();
			$rs = $db->Execute(sqlSelect($db,"voip_prepaid","id","pin = ::$pin::"));
			if($rs->RecordCount() > 0) {
				$trys++;	// pin is not unique
			} else {
				return $pin; // pin is unique
			}
		}
		return false;
	}

	/** balance notification task */
	function task($VAR)
	{
		include_once(PATH_MODULES.'email_template/email_template.inc.php');

		// do not run task if lowBalance is not set
		if ($this->lowBalance == false)
			return;

		// delete expired pins?
		// $delrs = & $db->Execute(sqlDelete($db,"voip_prepaid"," date_expire <> 0 and date_expire is not null and date_expire > ".time()));

		// get low balances and notify
		$db=&DB();
		$rs = & $db->Execute($sql = sqlSelect($db,"voip_prepaid","*", "balance <= $this->lowBalance and (bulk is null or bulk=0) and (date_email is null or date_email = 0) "));
		if($rs && $rs->RecordCount() > 0)
		{
			while(!$rs->EOF)
			{
				# send the user the details
				$email = new email_template;
				$email->send('voip_balance_prepaid', $rs->fields['account_id'], $rs->fields['id'], '', number_format($rs->fields['balance'],4));

				# update the record
				$db->Execute( sqlUpdate($db, "voip_prepaid", array('date_email'=>time()),"id={$rs->fields['id']}"));
				$rs->MoveNext();
			}
		}
	}
	
  
	/** provision pin */
	function provision_pin_new(&$obj)
	{
		$db =&DB(); 
		
		// default field values:
		if(!empty($obj->product_attr['expire']) && !empty($obj->product_attr['expire_days'])) 
		$fields['expire_days'] = $obj->product_attr['expire_days'];
		$fields['date_expire'] = 0; 
				
		// check if user passed existing pin
		if(!empty($obj->prod_attr_cart['pin']))
		{
			// if existing pin, validate that it belongs to the user and we can add a balance to it
			$pin = $obj->prod_attr_cart['pin'];
			$pinrs = & $db->Execute(sqlSelect($db,"voip_prepaid","*","pin = ::$pin:: AND account_id = {$obj->account['id']}  "));
			if($pinrs && $pinrs->RecordCount() == 1)
			{
				// update existing pin:
				$fields['balance'] = $obj->service['price'] + $pinrs->fields['balance']; 
				$rs = $db->Execute(sqlUpdate($db,"voip_prepaid",$fields,"pin = ::$pin::"));	
				return true;
			}
		}

		// the balance from the invoice line item (not including setup fee)
		$itemid = $obj->service['invoice_item_id'];
		$invoiceItem = $db->Execute(sqlSelect($db,"invoice_item","price_base","id = $itemid"));
		if($invoiceItem && $invoiceItem->RecordCount() > 0) {
			$balance = $invoiceItem->fields['price_base'];	
		} else {
			$balance = $obj->service['price'];
		}
				
		// still here? generate a new pin 
		$pin = $this->genPin();  
		if(!$pin) return false;	// could not generate unique 
		$fields = Array('account_id'  => $obj->account['id'],
						'product_id'  => $obj->service['product_id'],
						'pin'		  => $pin,
						'balance' 	  => $balance,
						'in_use' 	  => 0); 
		if(!empty($obj->product_attr['expire']) && !empty($obj->product_attr['expire_days'])) $fields['expire_days'] = $obj->product_attr['expire_days'];	 	
		$pin_id = sqlGenID($db, "voip_prepaid"); 
		$sql=sqlInsert($db,"voip_prepaid",$fields, $pin_id); 
		$rs = $db->Execute($sql); 
		if ($rs)  { 
			# send the user the details
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$email = new email_template;
			$email->send('voip_new_prepaid_pin', $obj->account['id'], $pin_id, $pin, $obj->plugin_data['number']);
		} else {
			return false;
		}	
		return true;
	}

	/** provision ani */
	function provision_ani_new($obj)
	{ 		
		$db=&DB();
  
		// default field values:
		if(!empty($obj->product_attr['expire']) && !empty($obj->product_attr['expire_days'])) 
		$fields['expire_days'] = $obj->product_attr['expire_days'];
		$fields['date_expire'] = 0; 
					
		// check if ani exists already in db
		$pin = $obj->prod_attr_cart['ani_new'];
		if(!empty($pin)) { 
			$pinexists = $db->Execute(sqlSelect($db,"voip_prepaid","*","pin = ::$pin:: AND ani=1")); 
		}
		 
		if($pinexists && $pinexists->RecordCount()>0)
		{ 
			// update existing pin: 
			$fields['balance'] 	= $obj->service['price'] + $pinexists->fields['balance'];
			$rs = $db->Execute(sqlUpdate($db,"voip_prepaid",$fields,"pin = ::$pin:: AND ani=1"));
			return true; 
		}
		elseif(!empty($obj->prod_attr_cart['ani_old'])) 
		{
			// existing ani provided by user
			$pin = $obj->prod_attr_cart['ani_old'];  
			$pinrs = $db->Execute(sqlSelect($db,"voip_prepaid","*","pin = ::$pin:: AND ani=1"));
			if($pinrs && $pinrs->RecordCount() == 1)
			{
				// update existing pin:
				$fields['balance'] 	= $obj->service['price'] + $pinexists->fields['balance'];
				$rs = $db->Execute(sqlUpdate($db,"voip_new_prepaid_did",$fields,"pin = ::$pin:: AND ani=1"));
				return true;
			}				
		}

		// the balance from the invoice line item (not including setup fee)
		$itemid = $obj->service['invoice_item_id'];
		$invoiceItem = $db->Execute(sqlSelect($db,"invoice_item","price_base","id = $itemid"));
		if($invoiceItem && $invoiceItem->RecordCount() > 0) {
			$balance = $invoiceItem->fields['price_base'];	
		} else {
			$balance = $obj->service['price'];
		}
				
		// still here? generate a new ani prepaid record   
		$pin = $obj->prod_attr_cart['ani_new'];  
		$fields = Array('account_id'  => $obj->account['id'],
						'product_id'  => $obj->service['product_id'],
						'pin'		  => trim($pin),
						'balance' 	  => $balance,
						'in_use' 	  => 0,
						'ani'		  => 1); 
		if(!empty($obj->product_attr['expire']) && !empty($obj->product_attr['expire_days']))
		$fields['expire_days'] = $obj->product_attr['expire_days'];	 	
		
		$pin_id = sqlGenID($db, "voip_prepaid"); 
		$sql=sqlInsert($db,"voip_prepaid", $fields, $pin_id); 
		$rs = $db->Execute($sql);
			
		if ($rs) { 
			# send the user the details
			include_once(PATH_MODULES.'email_template/email_template.inc.php');
			$email = new email_template;
			$email->send('voip_new_prepaid_ani', $obj->account['id'], $pin_id, $pin_id, $obj->plugin_data['number']);
		} else {
			return false;
		}
		return true;
	}	
	
	
	/** provision did */
	function provision_did_new($obj)
	{ 		
		@$a = unserialize($obj->service['prod_attr_cart']);
		$did = $a['station'];
		 
		// new or top-up?
		$db=&DB(); 
		$didrs = $db->Execute($sql=sqlSelect($db,"voip_prepaid","id,pin,balance","pin = ::{$did}:: AND voip_did_id is not null AND voip_did_id <> 0 "));
		if($didrs && $didrs->RecordCount() > 0) {
			$new = false;
		} else  {
			$new = true;
		}
		  
		// the balance from the invoice line item (not including setup fee)
		$itemid = $obj->service['invoice_item_id'];
		$invoiceItem = $db->Execute(sqlSelect($db,"invoice_item","price_base","id = $itemid"));
		if($invoiceItem && $invoiceItem->RecordCount() > 0) {
			$balance = $invoiceItem->fields['price_base'];	
		} else {
			$balance = $obj->service['price'];
		}
		/*
		echo "<BR><BR>$sql<BR><BR>";
		
		echo $new;
 
		echo "$".$balance;
		
		#print_r($obj->service);
		 */
		 
		
		if ($new) 
		{	 
			// include voip plugin and provision the did
			include_once(PATH_PLUGINS.'product/VOIP.php');
			$voip = new plgn_prov_VOIP; 
			if(!$voip->p_one($obj->service_id)) return false;
			 
			# create the prepaid record
			$didrs = $db->Execute(sqlSelect($db,"voip_did","id,did","service_id = ::{$obj->service_id}::"));
			if($didrs && $didrs->RecordCount() > 0)
			{
				$fields = Array('account_id'  => $obj->account['id'],
								'product_id'  => $obj->service['product_id'],
								'voip_did_id' => $didrs->fields['id'],
								'pin'		  => $didrs->fields['did'],
								'balance' 	  => $balance,
								'in_use' 	  => 0); 
												
				$pin_id = sqlGenID($db, "voip_prepaid"); 
				$sql=sqlInsert($db,"voip_prepaid", $fields, $pin_id); 
				$rs = $db->Execute($sql); 
				return true;
			} else {
				return false;
			}
		} 
		else 
		{
			# top-up the prepaid balance
			$fields = Array( 'balance' => $balance + $didrs->fields['balance']);
			$db->Execute($sql = sqlUpdate($db,"voip_prepaid", $fields, "id = {$didrs->fields['id']}"));  					 
			return true;
		}  
		return true;		
	}
		

	/** get users existing prepaid numbers */
	function menu_did($VAR)
	{
		global $smarty;
		if(!SESS_LOGGED) {
			$smarty->assign('ani', false);
			return;
		}
		
		if(!empty($VAR['account_id'])) 
			$account_id = $VAR['account_id'];
		else
			$account_id = SESS_ACCOUNT;
					
		$db=&DB();
		$rs = & $db->Execute($sql=sqlSelect($db,"voip_prepaid","*","voip_did_id is not null AND voip_did_id <> 0 AND (ani <> 1 or ani is  null) AND account_id = ".$account_id));
		if($rs && $rs->RecordCount() > 0) {
			$arr[0] = "-- New Number --";
			while(!$rs->EOF) {
				$arr["{$rs->fields['pin']}"] = "Number: ". $rs->fields['pin'] . ' -- Balance: '. number_format($rs->fields['balance'],6);
				$rs->MoveNext();
			}
		} else {
			$arr=false;
		}
		$smarty->assign('dids', $arr);
		return;
	}

	
	/** get users existing ani numbers */
	function menu_ani($VAR)
	{
		global $smarty;
		if(!SESS_LOGGED) {
			$smarty->assign('ani', false);
			return;
		}
		
		if(!empty($VAR['account_id'])) 
			$account_id = $VAR['account_id'];
		else
			$account_id = SESS_ACCOUNT;
					
		$db=&DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_prepaid","*","ani=1 AND account_id = ".$account_id));
		if($rs && $rs->RecordCount() > 0) {
			$arr[0] = "-- New Number --";
			while(!$rs->EOF) {
				$arr["{$rs->fields['pin']}"] = "Number: ". $rs->fields['pin'] . ' -- Balance: '. number_format($rs->fields['balance'],6);
				$rs->MoveNext();
			}
		} else {
			$arr=false;
		}
		$smarty->assign('ani', $arr);
		return;
	}


	/** get users existing pin numbers */
	function menu_pins($VAR)
	{
		global $smarty;
		if(!SESS_LOGGED) {
			$smarty->assign('pins', false);
			return;
		}
		
		if(!empty($VAR['account_id'])) 
			$account_id = $VAR['account_id'];
		else
			$account_id = SESS_ACCOUNT;
					
		$db=&DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_prepaid","*","(ani = 0 OR ani is null) AND account_id = ".$account_id));
		if($rs && $rs->RecordCount() > 0) {
			$arr[0] = "-- Generate a new Pin # for this purchase --";
			while(!$rs->EOF) {
				$arr["{$rs->fields['pin']}"] = "Pin # ". $rs->fields['pin'] . ' -- Balance: '. number_format($rs->fields['balance'],6);
				$rs->MoveNext();
			}
		} else {
			$arr=false;
		}
		$smarty->assign('pins', $arr);
		return;
	}

	/** Add new pin(s) */
	function add($VAR)
	{
		if(!empty($VAR['bulk']))
		{
			if(empty($VAR['voip_prepaid_account_id']) || empty($VAR['voip_prepaid_product_id']) ||
			empty($VAR['voip_prepaid_balance']) || empty($VAR['voip_prepaid_qty']) || empty($VAR['voip_prepaid_bulk']))
			{
				print("Failed: Please check that you have provided an account, product, balance, and quantity, and bulk reference number");
				return;
			}
			else
			{
				$db=&DB();

				for($i=0; $i<$VAR['voip_prepaid_qty']; $i++)
				{
					if($pin = $this->genPin())
					{
						// insert the record
						$fields["pin"]=$pin;
						$fields["account_id"] 	= $VAR['voip_prepaid_account_id'];
						$fields["product_id"] 	= $VAR['voip_prepaid_product_id'];
						$fields["balance"]		= $VAR['voip_prepaid_balance'];
						$fields['date_expire'] 	= $VAR['voip_prepaid_date_expire'];
						$fields["bulk"] 		= $VAR['voip_prepaid_bulk'];
						$db->Execute(sqlInsert($db,"voip_prepaid",$fields));
					}
				}
				echo "Added Batch Successfully!";
				echo "<script>document.location='?_page=core:search&module=voip_prepaid&voip_prepaid_bulk={$VAR['voip_prepaid_bulk']}';</script>";
			}

		} else {
			$type 		= "add";
			$this->method["$type"] = explode(",", $this->method["$type"]);
			$db 		= new CORE_database;
			$db->add($VAR, $this, $type);
		}
	}

	function view($VAR)
	{
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}

	function update($VAR)
	{
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}

	function delete($VAR)
	{
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}

	function search_form($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}

	function search($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	}

	function search_show($VAR)
	{
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	}

	/** Export search results */
	function search_export($VAR)
	{
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
}
?>
