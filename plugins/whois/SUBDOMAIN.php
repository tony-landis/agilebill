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
	
class plgn_whois_SUBDOMAIN
{
    function check($domain,$tld,$tld_array)
    {   
        // check the domain validity:
        if(!preg_match('/^[a-zA-Z0-9\-]{1,}$/i', $domain))  return false;
        if(preg_match('/[-]{2,}/', $domain))               return false;
        if(preg_match('/^[-]{1,}/', $domain))              return false;
        if(preg_match('/[-]{1,}$/', $domain))              return false;
  
        $db = &DB();
        $dbm = new CORE_database;
        $sql = $dbm->sql_select('service', 'id', "domain_name = ::$domain:: AND domain_tld = ::$tld::","", $db);
        $rs = $db->Execute($sql); 
        if($rs == false || $rs->RecordCount() > 0) 
            return false;
        else
			return true;
    }
 
    function check_transfer($domain,$tld,$tld_array)
    { 
		return false;
    } 

    // not implemented
    function details()
    {
        return false;
    }
}




?>
