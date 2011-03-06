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
	
# Debug 
if(empty($VAR) && empty($VAR['do']) && !defined("PATH_AGILE"))
{
	include_once('../../config.inc.php');
	$test = new CORE_ssl;
	$test->test();	
} 

class CORE_ssl
{
	 function CORE_ssl ($type=false)
	 {
		$disabled_functions = ini_get('disable_functions');

		if ( defined("PATH_CURL") && is_file(PATH_CURL) )
			$this->connect_curl_binary 	   = true;

		if ( function_exists('curl_init') && $curl_version = curl_version()) {
			if ( phpversion() >= 5 ) {
				if (preg_match('/openssl/i', @$curl_version['ssl_version'] )) 
					$this->connect_curl_module = true;
			} else {
				if (preg_match('/openssl/i', curl_version()))
					$this->connect_curl_module = true;
			}
		}

		if (phpversion() >= '4.3.0') 
			if (function_exists("fsockopen") )
				if (function_exists("openssl_public_decrypt"))
					$this->connect_fsockopen = true;
	}

	# debuging...
	function test() {  
		echo '<textarea cols="50" rows="50">'; 
		if ( @$this->connect_curl_binary ) { 
			echo 'Using Binary Curl:   					';
			echo $this->connect('secure.authorize.net', '', '', true, 1);
		} elseif ( @$this->connect_curl_module ) {
			echo 'Using Curl PHP Module:				';
			echo $this->connect('www.amazon.com', '', '', true, 1);	
		} elseif ( @$this->connect_fsockopen ) {
			echo 'Using PHP fsockopen() function + openssl_public_decrypt():			';		
			echo $this->connect('www.amazon.com', '', '', true, 1);			
		} else 	{
			echo 'No SSL functionality!';						
		} 
		echo "</textarea>";
	}		

	# type: 1=post 2=get
	function connect($host, $url, $vars, $ssl, $type) { 
		if ( @$this->connect_curl_binary ) 
			return $this->connect_curl_binary ($host, $url, $vars, $ssl, $type);			
		elseif ( @$this->connect_curl_module ) 
			return $this->connect_curl_module ($host, $url, $vars, $ssl, $type);	
		elseif ( @$this->connect_fsockopen ) 	
			return $this->connect_fsockopen   ($host, $url, $vars, $ssl, $type);		
		else 				
			return false;								
	} 

	# SSL connection with Curl Binary  
	function connect_curl_binary($host, $url, $vars, $ssl, $type) 	{

		if($ssl)  $urli = 'https://'.$host .''. $url;
		else 	  $urli = 'http://'.$host .''. $url;	 

		$params = "";  
		if(is_array($vars)) {
			for($i=0; $i<count($vars); $i++) {
			   if ($i > 0) { $params .= '&'; }
			   $params .=  $vars[$i][0].'='.urlencode($vars[$i][1]);

			}
		} elseif (!empty($vars)) {
		   $params = $vars;
		} 

		$curl = PATH_CURL;  

		# connect  
		if(!empty($params)) {
			//echo $curl ."-d -k \"$params\"". $urli;
			//echo $urli.'?'.$params;
			//exit;
			return `$curl -k -d "$params" $urli`;
		} else {
			//echo "$curl -k $urli";
			return `$curl -k $urli`;
		}   
	}	


	# SSL connection with Curl Module  
	function connect_curl_module($host, $url, $vars, $ssl, $type) 	{ 

		if($ssl)  $url = 'https://'.$host .''. $url;
		else 	  $url = 'http://'.$host .''. $url; 

		$params = '';
		if(is_array($vars)) {
			for($i=0; $i<count($vars); $i++) {
			   if ($i > 0) { $params .= '&'; }
			   @$params .=  $vars[$i][0].'='.urlencode($vars[$i][1]);

			}
		} elseif (!empty($vars)) {
		   $params = $vars;
		}   

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_POST,				1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,		$params);
		curl_setopt($ch, CURLOPT_URL,				$url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  	0); 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,	1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	FALSE);   
		$result = curl_exec ($ch);
		curl_close ($ch);
		return $result;	 
	}		


	# SSL connection with fsockopen()  
	function connect_fsockopen($host, $url, $vars, $ssl, $type) 	{

		if($ssl) { 
			$host = 'ssl://'. $host;
			$port = 443;
		} else { 
			$port = 80;
		}

		$fp = @fsockopen ($host, $port, $errno, $errstr, 120);
		$ret = ""; 
		@$req = substr ($url, $p);

		if ($fp) {
			fputs ($fp, "POST $req  HTTP/1.1\n");
			fputs ($fp, "Accept: */*\n");
			fputs ($fp, "Accept-Language: en\n");
			fputs ($fp, "Connection: Keep-Alive\n");
			fputs ($fp, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)\n");
			fputs ($fp, "Content-type: application/x-www-form-urlencoded\n");
			$out = "";  
			for($i=0; $i<count($vars); $i++) {
				if($i>0 && !empty($vars[$i][0]))
					$out .= '&'; 
				$out .= rawurlencode($vars[$i][0]) .'='. $out .= rawurlencode($vars[$i][1]);
			} 
			$out = trim ($out);
			fputs ($fp, "Content-length: ".strlen($out)."\n\n");
			fputs ($fp, "$out");
			fputs ($fp, "\n");
			while(!feof($fp))  
				$ret .= fgets($fp,128); 
			fclose ($fp);
		} else {
			return false;
		}
		return $ret;  		
	}			
}
?>