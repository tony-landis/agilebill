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
 * Basic Product Plugin Class for AgileBill 
 * 
 */
class base_product_plugin
{
	var $name		= '';
	var $tax_based 	= false;
	var $remote_based = true;
	var $account;
	var $product;
	var $product_attr;
	 
	/**
	 * Provision new service
	 */
	function p_new()
	{  
		return true;		
	}
 
	/**
	 * Modify service
	 */
	function p_edit()
	{
		return true;
	}

	/**
	 * Suspend service
	 */
	function p_inactive()
	{  
		return true;
	}

	/**
	 * Activate service
	 */
	function p_active()
	{ 
		return true;
	}

	/**
	 * Delete service 
	 */
	function p_delete()
	{ 
		return true;
	}
 
	function p_one($id)
	{
		global $C_debug;
		
		/* Get the service details */
		$db = &DB();
		$rs = $db->Execute(sqlSelect($db,"service","*","id=::$id::"));
		if(!$rs || !$rs->RecordCount()) return false; 		
		$this->service = $rs->fields;
		@$this->plugin_data = unserialize($this->service['prod_plugin_data']);
		@$this->prod_attr_cart = unserialize($this->service['prod_attr_cart']);
		
		/* Get the account details */
		$acct = $db->Execute(sqlSelect($db,"account","*","id=::{$this->service['account_id']}::"));
		if($acct && $acct->RecordCount()) $this->account = $acct->fields;

	    /* Get the product details */
	    if(!empty($this->service['product_id'])) {
		    $product = $db->Execute(sqlSelect($db,"product","*","id = ::{$this->service['product_id']}::"));
		    $this->product = $product->fields;
		    @$this->product_attr = unserialize($product->fields['prod_plugin_data']);
	    }
		
		/* determine the correct action */
		switch ($this->service['queue'])
		{ 
			case 'new':
				$result = $this->p_new(); 
				break; 
			case 'active':
				$result = $this->p_active(); 
				break;	 
			case 'inactive':
				$result = $this->p_inactive();
				break; 
			case 'edit':  
				if ($this->service['active'] == 1)
					$this->p_active();
				else
					$this->p_inactive();
				$result = $this->p_edit();
				break; 
			case 'delete':
				$result = $this->p_delete();
				break;
		}
		if(@$result !== false) { 
			$sql = 'UPDATE '.AGILE_DB_PREFIX.'service SET queue='.$db->qstr('none') . ', date_last='.$db->qstr(time()) . ' WHERE id ='.$db->qstr($rs->fields['id']).' AND site_id='.$db->qstr(DEFAULT_SITE);
			$upd = $db->Execute($sql);
		} else { 
			$C_debug->error($this->name.'php', $this->service['queue'], @$result);
		}
	}
}
?>