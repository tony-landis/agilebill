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
	
class plgn_whois_WHOIS_CO_ZA
{
    function check($domain,$tld,$tld_array)
    {  
    	
        // check the domain validity:
        if(!eregi('^[a-zA-Z0-9\-]{1,}$', $domain))  return false;
        if(eregi('[-]{2,}', $domain))               return false;
        if(eregi('^[-]{1,}', $domain))              return false;
        if(eregi('[-]{1,}$', $domain))              return false; 
        if($tld != "co.za") 						return false;
	 
		$data = $this->whois( $domain );
	 	if($data) return true;
		return false;
    }
 
    function check_transfer($domain,$tld,$tld_array)
    { 
        // check the domain validity:
        if(!eregi('^[a-zA-Z0-9\-]{1,}$', $domain))  return false;
        if(eregi('[-]{2,}', $domain))               return false;
        if(eregi('^[-]{1,}', $domain))              return false;
        if(eregi('[-]{1,}$', $domain))              return false;
		if($tld != "co.za") 						return false;
	
		$data = $this->whois( $domain );
	 	if(!$data) return true;
		return false;
    }
     
    function whois($domain)
    {  
		$lines = file('http://whois.co.za/cgi-bin/whois.sh?Domain='.$domain); 
		foreach ($lines as $line_num => $line) {
			if(eregi("Nothing matched", $line)) { 
		   		return true;
		   }
		} 
    	return false;		
    }
 
    // not implemented
    function details()
    {
        return false;
    }
} 
?>