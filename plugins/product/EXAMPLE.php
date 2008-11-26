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
 * Product Plugin for AgileBill
 * 
 * @copyright 2005,2006, and onward, Agileco, LLC. All rights reserverd.
 * @license All usage subject to license terms of Agileco, LLC.
 * 
 */
 
require_once PATH_MODULES.'product/base_product_plugin.inc.php';

class plgn_prov_EXAMPLE extends base_product_plugin
{ 
 
  var $name='EXAMPLE'; /* change this to your plugin name! */ 
   
  /* create new service */
  function p_new()
  { 
  	/* do some background logging of what is going on */ 
  	global $C_debug;
  	$C_debug->error('EXAMPLE.php', 'p_new ('.$this->service['queue'].')', print_r(array($this->plugin_data, $this->service),true) );
  	
  	/* some available variables */
  	$this->account['username']; /* all the fields on the account table, eg: id, username, email, first_name, last_name */
  	$this->service['id'];		/* all the fields in the service table, eg: id, host_username, host_password, etc */
  	$this->plugin_data['my_field']; /* all the fields captured by the custom product plugin configuration template */
  	
	return true;
  }
  
  /* edit existing service */
  function p_edit() {

  	/* do some background logging of what is going on */ 
  	global $C_debug;
  	$C_debug->error('EXAMPLE.php', 'p_edit ('.$this->service['queue'].')', print_r(array($this->plugin_data, $this->service),true) );
  	  	
   	return true;
  } 
  
  /* deactivate existing service */
  function p_inactive() {

  	/* do some background logging of what is going on */ 
  	global $C_debug;
  	$C_debug->error('EXAMPLE.php', 'p_inactive ('.$this->service['queue'].')', print_r(array($this->plugin_data, $this->service),true) );
  	  	
   	return true;
  } 
 
  /* reactivate existing service */
  function p_active() {
  	
  	/* do some background logging of what is going on */ 
  	global $C_debug;
  	$C_debug->error('EXAMPLE.php', 'p_active ('.$this->service['queue'].')', print_r(array($this->plugin_data, $this->service),true) );
  	  	
   	return true;
  } 
 
  /* delete existing service */
  function p_delete() {
  	
  	/* do some background logging of what is going on */ 
  	global $C_debug;
  	$C_debug->error('EXAMPLE.php', 'p_delete ('.$this->service['queue'].')', print_r(array($this->plugin_data, $this->service),true) );
  	  	
   	return true;
  } 
}
?>