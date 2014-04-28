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
	
class plgn_whois_DEFAULT
{
    function check($domain,$tld,$tld_array)
    {
        //return true;
        // check the domain validity:
        if(!preg_match('/^[a-zA-Z0-9\-]{1,}$/i', $domain))  return false;
        if(preg_match('/[-]{2,}/', $domain))               return false;
        if(preg_match('/^[-]{1,}/', $domain))              return false;
        if(preg_match('/[-]{1,}$/', $domain))              return false;

	
		$data = $this->whois($tld_array["whois_server"],$domain . '.' . $tld);
		if(!$data) return false;
		if(preg_match('/'.$tld_array["avail_response"].'/i', $data))
            return true;
        else
			return false;
		return false;
    }


    function check_transfer($domain,$tld,$tld_array)
    {
        //return true;
        // check the domain validity:
        if(!preg_match('/^[a-zA-Z0-9\-]{1,}$/i', $domain))  return false;
        if(preg_match('/[-]{2,}/', $domain))               return false;
        if(preg_match('/^[-]{1,}/', $domain))              return false;
        if(preg_match('/[-]{1,}$/', $domain))              return false;

	
		$data = $this->whois($tld_array["whois_server"],$domain . '.' . $tld);
		if(!$data) return false;
		if(preg_match('/'.$tld_array["avail_response"].'/i', $data))
            return false;
        else
			return true;
		return false;
    }
    
    
    function whois($server,$domain)
    {
    	$data = " ";
    	$fp = fsockopen($server, 43);
    	if($fp) {
    		fputs($fp, $domain."\r\n");
    		while(!feof($fp))  $data .= fread($fp, 1000);
    		fclose($fp);
    	} else {
    		return false;
    	}
    	return $data;
    }


    // not implemented
    function details()
    {
        return false;
    }
}




?>
