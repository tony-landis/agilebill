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
	
include_once('../../config.inc.php');
require_once(PATH_ADODB  . 'adodb.inc.php');
require_once(PATH_CORE   . 'database.inc.php');
require_once(PATH_CORE   . 'setup.inc.php');
require_once(PATH_CORE   . 'vars.inc.php');
include_once(PATH_INCLUDES.'xml-rpc/xml-rpc.php');
include_once(PATH_MODULES .'charge/charge.inc.php');
$C_debug 	= new CORE_debugger;
$C_vars 	= new CORE_vars;
$VAR        = $C_vars->f;
$C_db       = &DB();
$C_setup 	= new CORE_setup;

class ChargeServer extends IXR_Server {
	
	var $account_id;
	var $username;
	var $password;
	
    function ChargeServer() { 
    	$this->IXR_Server(array(
            'charge.add' => 'this:add' 
        )); 
    }
    
    function add($args) { 
        $this->username = $args[0];
        $this->password = $args[1]; 
        if(!$this->validate()) return array('status'=>false, 'charge_id'=>false, 'error'=> "Authentication failed");
        
        @$var['account_id'] = $args[2];
        @$var['service_id'] = $args[3];
        @$var['amount'] = $args[4];
        @$var['sweep_type'] = $args[5];
        @$var['taxable'] = $args[6];
        @$var['quantity'] = $args[7];
        @$var['product_id'] = $args[8];
        @$var['description'] = $args[9];
        @$var['attributes'] = $args[10];
        
        $charge = new charge;
        $charge->xmlrpc=true;
        $ret = $charge->api($var, $charge);
         
        return $ret;
         
    }
    
    function validate() {
    	if(empty($this->username) || empty($this->password)) return false; 
    	$p=AGILE_DB_PREFIX;
    	$s=DEFAULT_SITE;
    	$db=&DB();
    	$sql = "SELECT DISTINCT A.id,A.username FROM {$p}account as A, {$p}account_group as AG 
		WHERE A.username=".$db->qstr($this->username)." AND A.password=MD5(".$db->qstr($this->password).")
		AND A.status=1 AND AG.account_id = A.id AND A.site_id={$s} AND AG.site_id={$s}
		AND AG.group_id in ( SELECT DISTINCT GM.group_id FROM _group_method GM JOIN {$p}module M on (M.name='charge' and M.site_id={$s}) JOIN {$p}module_method MM on (MM.name='api' AND MM.module_id=M.id and MM.site_id={$s}))";
    	$rs=$db->Execute($sql);
    	if(!$rs || !$rs->RecordCount()) return false;
    	else return true;    	
    }
}
$server = new ChargeServer();
?>