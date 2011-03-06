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
 * Webmin Virtualmin Remote Class 
 */
class WEBMIN
{ 
	var $host;  	 
	var $user;
	var $pass;
	var $debug;
	var $reseller;
	var $cookiepath; 

	/*
	* Check for Agilebill
	*/
	function WEBMIN()
	{
		if(!defined("PATH_FILES"))
		{
			echo "Dependancy Failure!";
			exit;
		} 
	}




	/*
	* Get the unique domain id
	*/		
	function sitenum()
	{
		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/search.cgi';
		$post = "&field=dom&match=0&what={$this->domain}";		 	
		$ret  = $this->connect($cgi,$post);					  		
		preg_match ("/(edit_domain.cgi\?dom=).([0-9]{1,})('>$this->domain<)/i", $ret, $arr); 			 
		if(is_array($arr) && count($arr) > 0) {
			$id = preg_replace("/('>$this->domain<)/", "", preg_replace("/(edit_domain.cgi\?dom=)/","", $arr[0]));
		} else { 
			preg_match ("/(edit_domain.cgi\?dom=).([0-9]{1,})('><i>$this->domain<\/i><)/i", $ret, $arr); 			 
			if(is_array($arr) && count($arr) > 0)
				$id = preg_replace("/('><i>$this->domain</i><)/", "", preg_replace("/(edit_domain.cgi\?dom=)/","", $arr[0]));
			else
				$id = false;				
		} 

		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";

		return $id;	
	}


	/*
	* Add a domain
	*/ 
	function add()
	{    	 
		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/domain_setup.cgi';							
		$post = "parentuser=&to=&dom={$this->domain}&owner=Created+by+AB&email_def=0&email={$this->email}".
				"&user_def=0&user={$this->username}&pass={$this->password}&mgroup_def=1&mgroup=&group_def=1&ip=".
				"&group=&prefix_def=1&mailbox=0&template=0&mailboxlimit_def=1&mailboxlimit=&domslimit_def=1&domslimit=".
				"&dir={{$this->prod['home_dir']}&unix={$this->prod['unix']}&dns={$this->prod['dns']}".
				"&web={$this->prod['website']}&webalizer={$this->prod['webalizer']}&logrotate={$this->prod['logrotate']}".
				"&mysql={$this->prod['mysql']}&webmin={$this->prod['webmin']}&virt={$this->prod['network_interface']}";

		$ret  = $this->connect($cgi,$post);		 
		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";

		if(!preg_match("/Failed/i", $ret))	 
		return true;	 
		else
		return false;
   }


   /*
   * Edit the domain
   */ 
   function edit()
   { 
		/*
		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false; 

		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/save_domain.cgi';							
		$post = "dom={$sitenum}&virt=0&ip={$this->ip}&owner=Created+by+AB&email_def=0&email={$this->email}&passwd_def=1&passwd={$this->password}". 			 
				"&dir={{$this->prod['home_dir']}&unix={$this->prod['unix']}&dns={$this->prod['dns']}".
				"&web={$this->prod['website']}&webalizer={$this->prod['webalizer']}&logrotate={$this->prod['logrotate']}".
				"&mysql={$this->prod['mysql']}&webmin={$this->prod['webmin']}&virt={$this->prod['network_interface']}";	   		

		$ret  = $this->connect($cgi,$post);	 
		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";		


		if(!eregi("Failed", $ret))	 
		return true;	 
		else
		return false;	
		*/

		return true;					
   }


   /*
   * Suspend account
   */ 
   function suspend($dologin=true)
   {    
		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false; 

		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/disable_domain.cgi';							
		$post = "confirm=Yes, Disable It&dom={$sitenum}";

		$ret  = $this->connect($cgi,$post);	 
		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";		

		if(!preg_match("/Failed/i", $ret))	 
		return true;	 
		else
		return false;
   }


   /*
   * Unsuspend account  
   */ 
   function unsuspend($dologin=true)
   {   
		# get unique sitenum
		$sitenum = $this->sitenum(); 
		if(!$sitenum) return false; 

		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/enable_domain.cgi';							
		$post = "confirm=Yes, Enable It&dom={$sitenum}";

		$ret  = $this->connect($cgi,$post);	 
		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";		

		if(!preg_match("/Failed/i", $ret))	 
		return true;	 
		else
		return false;	   	   
   }


   /*
   * Delete account 
   */ 
   function del()
   {	
		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false; 

		$url  = "{$this->user}:{$this->pass}@{$this->host}:{$this->port}";
		$cgi  = $url.'/virtual-server/delete_domain.cgi';							
		$post = "confirm=Yes, Delete It&dom={$sitenum}";

		$ret  = $this->connect($cgi,$post);	 
		if($this->debug) echo "<br><br>{$cgi}?{$post}<br><textarea cols=100 rows=20>$ret</textarea>";		

		if(!preg_match("/Failed/i", $ret))	 
		return true;	 
		else
		return false;
   }	



   /*
   * Curl connect
   * 
   */  	   
   function connect($url,$post)
   {
		if($this->ssl)
			$url = 'https://'.$url;  
		else
			$url = 'http://'.$url;

		$ch = curl_init(); 
		curl_setopt($ch, CURLOPT_URL,$url);
		curl_setopt($ch, CURLOPT_POST, 1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		#curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1 );
		curl_setopt($ch, CURLOPT_HEADER, 1);

		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  	0);  
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	FALSE); 			

		$data = curl_exec ($ch); 
		curl_close ($ch);  
		return $data; 
   }  	      
}
?>