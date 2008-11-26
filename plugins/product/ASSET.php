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
 * Asset Management Product Plugin for AgileBill  
 */
 
require_once PATH_MODULES.'product/base_product_plugin.inc.php';

class plgn_prov_ASSET extends base_product_plugin
{
  var $name='ASSET';
  var $AssetPool;
  var $AssetId=false;
  var $AssetQty;
  var $CartCheck;
  var $OnSuspend;
	
  /**
  * Validate availibility for cart 
  */
  function validate_cart($VAR, $product) 
  {
	// check if prepaid type is ani or pin, if so, escape: 
	$unserial = unserialize($product->fields['prod_plugin_data']);
	if(empty($unserial['CartCheck'])) return true;
	  	
	// check qty
	include_once(PATH_MODULES . 'asset/asset.inc.php');
	$asset=new asset;
	if(!$asset->available($unserial['AssetPool'], $unserial['AssetQty'])) {
		return "Sorry, we do not have enough of that asset available at this time. Please try again shortly";	
	}	   	
	return true;
  }
	 
  function p_new() {   
  	$this->AssetPool = $this->plugin_data['AssetPool'];
  	$this->AssetQty = $this->plugin_data['AssetQty'];
  	if(!empty($this->prod_attr_cart['AssetId']))
  		$this->AssetId = $this->prod_attr_cart['AssetId'];
  	 
	include_once(PATH_MODULES . 'asset/asset.inc.php');
	$asset=new asset;  	
	
	if(!$this->AssetId)
		for($i=0; $i<$this->AssetQty; $i++)	
			$asset->assign($this->service['id'], $this->AssetPool);
	else 
		$asset->assignKnown($this->service['id'], $this->AssetId);
		
	return true;		
  }
 
  function p_edit() { 
      return true;
  }
 
  function p_inactive() {    
  	$this->OnSuspend = $this->plugin_data['OnSuspend'];
  	if(!empty($this->OnSuspend)) $this->p_delete(); 
	return true;
  }
 
  function p_active() {  
  	$this->OnSuspend = $this->plugin_data['OnSuspend'];
  	if(!empty($this->OnSuspend)) $this->p_new();
	return true;
  }
 
  function p_delete() { 
	include_once(PATH_MODULES . 'asset/asset.inc.php');
	$asset=new asset;  	 
	$asset->unAssignAll($this->service['id']);  
	return true;
  }
}
?>