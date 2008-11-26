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
 * Radius Provisioning Class for AgileBill
 * 
 * @copyright 2005,2006, and onward, Agileco, LLC. All rights reserverd.
 * @license All usage subject to license terms of Agileco, LLC.
 * 
 */
 
require_once PATH_MODULES.'product/base_product_plugin.inc.php';

class plgn_prov_RADIUS extends base_product_plugin
{
	var $name='RADIUS';
	var $tax_based = false;
	var $remote_based = true;
	 
  /**
   * Provision new radius account(s)
   */
  function p_new()
  {  
  	$db=&DB();
    $this->account['id'];  
    for($i=0; $i<$this->plugin_data['max']; $i++) { 
	    $fields=Array(	'service_id'=>$this->service['id'], 
	    				'account_id'=> $this->account['id'],
	    				'auth' => $this->plugin_data['auth']);
	    			  
	    $db->Execute(sqlInsert($db,"radius_service",$fields));
  	} 
		return true;		
  }
 
  /**
   * Modify a radius account 
   */
  function p_edit()
  {
  	$f['sku']=$this->service['sku'];
      foreach($this->plugin_data as $a=>$b) {
      	if($a != 'max') $f[$a]=$b; 
      }
      $db=&DB();
      $db->Execute($sql=sqlUpdate($db,"radius",$f,"service_id={$this->service['id']}"));  	
      return true;
  }

  /**
   * Suspend a radius account
   */
  function p_inactive()
  {  
  	$db=&DB();
  	$db->Execute(sqlUpdate($db,"radius",array("active"=>0),"service_id={$this->service['id']}"));
	return true;
  }

  /**
   * Activate a radius account 
   */
  function p_active()
  { 
  	$db=&DB();
  	$db->Execute(sqlUpdate($db,"radius",array("active"=>1),"service_id={$this->service['id']}"));
	return true;
  }

  /**
   * Delete a radius account 
   */
  function p_delete()
  { 
  	$db=&DB();
  	$db->Execute(sqlDelete($db,"radius","service_id={$this->service['id']}"));
  	$db->Execute(sqlDelete($db,"radius_service","service_id={$this->service['id']}"));
	return true;
  }
}
?>