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
	
/*
 * include_once(PATH_CORE.'post.inc.php');
 * $post = new CORE_post;
 * $result = $post->post_data($host, $url, $data);
 */
class CORE_post
{ 
    // post data and return reply
    function post_data ($host, $url, $data, $port=80)
    { 
    	if (strlen($host)<1) return false; 
    	if (strlen($url)<1)  return false; 
    	if ((!is_array($data)) || sizeof($data)<1) return false;

    	$fp = @fsockopen ($host, $port, $errno, $errstr, 120);
    	$ret = ""; 
    	@$req = substr ($url, $p);
    	
    	if ($fp) {
    		fputs ($fp, "POST $req HTTP/1.0\n");
    		$this->post_send_headers ($fp);
    		fputs ($fp, "Content-type: application/x-www-form-urlencoded\n");
    		$out = "";
    		while (list ($k, $v) = each ($data)) {
    			if(strlen($out) != 0) $out .= "&";
    			$out .= rawurlencode($k). "=" .rawurlencode($v);
    		}
    		$out = trim ($out);
    		fputs ($fp, "Content-length: ".strlen($out)."\n\n");
    		fputs ($fp, "$out");
    		fputs ($fp, "\n");
    		while(!feof($fp))  
    			$ret .= fgets($fp,128); 
    		fclose ($fp);
    	} 
    	return $ret; 
    }	

    // send out "browser" headers
    function post_send_headers ($fp) {
    	fputs ($fp, "Accept: */*\n");
    	fputs ($fp, "Accept-Language: en\n");
    	fputs ($fp, "Connection: Keep-Alive\n");
    	fputs ($fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)\n");
    }
}
?>