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

ob_start();

include_once('../../config.inc.php');
require_once(PATH_ADODB  . 'adodb.inc.php');
require_once(PATH_CORE   . 'database.inc.php');
require_once(PATH_CORE   . 'setup.inc.php');
require_once(PATH_CORE   . 'vars.inc.php');
require_once(PATH_CORE	 . 'translate.inc.php');
require_once(PATH_CORE	 . 'xml.inc.php');
include_once(PATH_INCLUDES . "xml-rpc/xml-rpc.php");
$C_debug 	= new CORE_debugger;
$C_setup 	= new CORE_setup;
$C_translate= new CORE_translate;
    
class AccountServer extends IXR_Server {
	
	var $account_id;
	var $account_parent_id=false;
	var $username;
	var $password;
	var $login_error;
	
    function AccountServer() { 
    	$this->IXR_Server(array(
            'account.details' => 'this:getAccountDetails' 
        )); 
    }
    
    function getAccountDetails($args) { 
        $this->username = $args[0];
        $this->password = $args[1];  
        if(!$this->getAccountAuth()) return array('auth'=> false, 'reason'=>$this->login_error); 
        $return = array( 
        	'auth' => true,
        	'acct' => $this->account_id,
        	'info' => $this->getAccountInfo(),
        	'skus' => $this->getAccountSKUs(),
        	'grps' => $this->getAccountGroups()
        );         
        return $return;
    }
        
    function getAccountAuth() {       
        // select from account where username = $this_username and password = $this->password    	
      	include_once(PATH_CORE.'login.inc.php'); 
      	$login = new CORE_login_handler();
      	if(!$login->login(array("_username"=> $this->username, "_password"=> $this->password), true)) { 
      		$this->login_error = $login->error;
        	return false;
        } else {
	    	$db=&DB();
	    	$rs = $db->Execute(sqlSelect($db,"account","id","username = ::$this->username::")); 
	    	$this->account_id = $rs->fields['id'];        	
        	return true;      	     	
        }
    }
    
    function getAccountInfo() {
    	$db=&DB();
    	$rs = $db->Execute(sqlSelect($db,"account","*","id = ::$this->account_id::"));  
    	if(!empty($rs->fields['parent_id'])) $this->account_parent_id = $rs->fields['parent_id'];
    	$ret = Array(
    		'company' => $rs->fields['company'],
    		'first_name' => $rs->fields['first_name'],
    		'last_name' => $rs->fields['last_name'],
    		'address1' => $rs->fields['address1'],
    		'address2' => $rs->fields['address2'],
    		'city' => $rs->fields['city'],
    		'state' => $rs->fields['state'],
    		'zip' => $rs->fields['zip'],
    		'email' => $rs->fields['email'],
    		'acct_parent_id' => $rs->fields['parent_id']
    	); 
    	$rs = $db->Execute(sqlSelect($db,"module","id","name=::account::"));
    	$account_module=$rs->fields['id'];
    	/* get custom fields*/
    	$sql = "SELECT DISTINCT A.value, B.name FROM ".AGILE_DB_PREFIX."static_var as B 
				LEFT JOIN ".AGILE_DB_PREFIX."static_var_record as A on (B.id=A.static_var_id AND A.record_id = ".$db->qstr($this->account_id).") 
				WHERE A.module_id=$account_module ";
    	$rs=$db->Execute($sql);
    	if($rs &&$rs->RecordCount()) {
    		while(!$rs->EOF) {
    			$fld = substr(strtolower(preg_replace("/ /",'_', $rs->fields['name'])),0,32);
    			@$ret["$fld"]=$rs->fields['value'];
    			$rs->MoveNext();
    		}
    	}
    	return $ret;
    }    
    
    function getAccountSKUs() { 
    	// select from invoice_item where id = $this->account_id 
    	$db=&DB();
    	$p=AGILE_DB_PREFIX;
    	$s=DEFAULT_SITE;
    	$arr=false;
    	$q = "SELECT DISTINCT A.sku FROM {$p}invoice_item A
    		JOIN {$p}invoice B on ((B.account_id={$this->account_id} ";
    	if($this->account_parent_id)
    	$q.= " OR B.account_id = {$this->account_parent_id} ";
    	$q.= ") AND B.id=A.invoice_id AND B.site_id={$s} AND billing_status=1 AND process_status=1)
    		WHERE A.site_id = {$s}";
    	$rs = $db->Execute($q);
    	if($rs && $rs->RecordCount()) {
    		while(!$rs->EOF) {
    			$arr[] = $rs->fields['sku'];
    			$rs->MoveNext();
    		}
    	} 
    	return $arr; 
    }
    
    function getAccountGroups() {  
    	// select from account_group where account_id = $this->account_id    	
    	$db=&DB();
    	$p=AGILE_DB_PREFIX;
    	$s=DEFAULT_SITE;
    	$arr=false;
    	$q = "SELECT DISTINCT group_id FROM {$p}account_group WHERE account_id={$this->account_id} AND site_id={$s} AND (active=1 OR active!='' or active!=0 or active is not null)";
    	$rs = $db->Execute($q);
    	if($rs && $rs->RecordCount()) {
    		while(!$rs->EOF) {
    			$arr[] = $rs->fields['group_id'];
    			$rs->MoveNext();
    		}
    	} 
    	return $arr;
    }
    
}
$server = new AccountServer();

ob_end_flush();

?> 