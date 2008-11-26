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
	
require_once PATH_MODULES.'voip/base_voip_plugin.inc.php';

class plgn_prov_PREPAID extends base_voip_plugin
{
  function plgn_prov_PREPAID()
  { 
		$this->name             = 'PREPAID';
		$this->task_based       = false;
		$this->remote_based     = true;
  }
  
  function delete_cart($VAR, $cart)
  {
  	parent::delete_cart($VAR, $cart, true);
  }
  
  function validate_cart($VAR, $product) 
  {
  	// check if prepaid type is ani or pin, if so, escape: 
  	$unserial = unserialize($product->fields['prod_plugin_data']);
  	if(!empty($unserial['type']) && ( $unserial['type']=='ani' || $unserial['type']=='pin' ) ) return true;
  	 
  	// verify that attr['station'] is defined and numeric
  	@$did = $VAR['attr']['station'];
  	$ported = 0;
  	if (@$VAR['attr']['ported']) {
  		$did = $VAR['attr']['ported'];
  		$ported = 1;
  	}
  	if(empty($did) || !is_numeric($did)) 
  		return "Sorry, the DID format specified is incorrect.";
  	
  	// check if user owns did && is in did pool 
  	$db =& DB();
  	$didrs = $db->Execute(sqlSelect($db,"voip_did","id,did","did = ::{$did}:: AND account_id=".SESS_ACCOUNT)); 
  	if($didrs && $didrs->RecordCount()>0) return true;

		return parent::validate_cart($VAR, $product, $did, $ported);
	}
   
  # add new service
  function p_new()
  { 
  	# todo: check that the pin is random!
  	include_once(PATH_MODULES.'voip_prepaid/voip_prepaid.inc.php');
  	$prepaid= new voip_prepaid;		
	 
		# determine the prepaid type:
		switch($this->product_attr['type']) {
			case 'did':
				return $prepaid->provision_did_new($this);
			break;
			case 'ani':
				return $prepaid->provision_ani_new($this);
			break;
			case 'pin':
				return $prepaid->provision_pin_new($this);
			break;
		}  
		return false;
  }

  # edit service   
  function p_edit()
  {
		# determine the prepaid type:
		switch($this->product_attr['type']) { 
			case 'did':
				include_once(PATH_PLUGINS.'product/VOIP.php');
				$voip = new plgn_prov_VOIP; 
				if(!$voip->p_one($this->service_id)) return false;
			break; 
		}		
		return true;
  }

  # activate service
  function p_inactive()
  {  
		$db=&DB(); 
		$rs = $db->Execute(sqlSelect($db,"voip_did","id,did","service_id = $this->service_id"));
		$did_id = $rs->fields['id'];		
		$fields=Array('in_use'=>2);
		$db->Execute(sqlUpdate($db,"voip_prepaid",$fields,"voip_did_id = {$did_id}"));
		return true;
  }

  # deactivate service
  function p_active()
  { 
		$db=&DB(); 
		$rs = $db->Execute(sqlSelect($db,"voip_did","id,did","service_id = $this->service_id"));
		$did_id = $rs->fields['id'];		
		$fields=Array('in_use'=>0);
		$db->Execute(sqlUpdate($db,"voip_prepaid",$fields,"voip_did_id = {$did_id}"));    	
		return true;
  }

  # delete service
  function p_delete()
  {
  	$db =& DB();
		# determine the prepaid type:
		switch($this->product_attr['type']) { 
			case 'ani':
				$sql = sqlDelete($db,"voip_prepaid","pin=::{$this->prod_attr_cart['ani_new']}::");
				$db->Execute($sql);
				break;
			case 'did':
				include_once(PATH_PLUGINS.'product/VOIP.php');
				$voip = new plgn_prov_VOIP; 
				$voip->p_one($this->service_id);
				break; 
		}			    	  
		$rs = $db->Execute($sql=sqlSelect($db,"voip_did","id,did","service_id = $this->service_id"));
		$did_id = $rs->fields['id'];
		$db->Execute($sql=sqlDelete($db,"voip_prepaid","voip_did_id = {$did_id}"));
		return true;
  }

	function p_one($id)
	{
		$db =& DB();
	
		# Get the asterisk global configuration
		$sql = sqlSelect($db, "voip", "voip_vm_passwd, voip_secret_gen", "");
		$rs = $db->Execute($sql);
		$this->voip_vm_passwd = $rs->fields['voip_vm_passwd'];
		$this->voip_secret_gen = $rs->fields['voip_secret_gen'];
		
		parent::p_one($id);
	}
}
?>