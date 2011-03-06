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
 * easyAdmin Remote Class 
 */
class EASYADMIN
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
	function EASYADMIN()
	{
		if(!defined("PATH_FILES"))
		{
			echo "Dependancy Failure!";
			exit;
		} 	

	}

	/*
	* Login the user
	*/		
	function login()
	{ 		 
		# clear the cookies
		if(is_file($this->cookiepath))
			unlink($this->cookiepath);			

		$post = "";
		$ret = $this->connect($post);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";				

		$post = "name=$this->user&pass=$this->pass&submit.x=0&submit.y=0";
		$ret = $this->connect($post);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";
		if(empty($ret) || preg_match('/Username or password incorrect/i', $ret) || preg_match('/You have cookies disabled/i', $ret))
			return false;
		else
			return true;
	}


	/*
	* Get the unique domain id
	*/		
	function sitenum()
	{
		$post = "function=viewsites&sortkey=domain&filtertype=2&hostdomain=$this->domain&filtersubmit=Display";
		$ret = $this->connect($post);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";
		if(!empty($ret)) 
		{ 
			# Get the Package ID Just added			
			$domain = strtolower($this->domain);
			preg_match ("/(function=edit&sitenum=)+([0-9]){1,}+(\">www.)+($domain)/i", $ret, $arr); 			 
			if(is_array($arr) && count($arr) > 0) 
			{ 
				$sitenum = preg_replace("/function=edit&sitenum=/","", $arr[0]); 					
				$sitenum = preg_replace("/\">www.$domain/","", $sitenum); 
				$this->sitenumber = $sitenum;
				return $sitenum;
			}				
		}
		return false;		
	}


	/*
	* Add a website
	*/ 
	function add()
	{   
		# login:
		if(!$this->login())	 return false;

		# add  
		$post = "function=confirmsite". 
				"&hostname=www"	.
				"&webaliases="	.
				"&emailaliases=".
				"&directives="	.
				"&extrainfo="	.
				"&ipx="			. $this->ip			.
				"&reseller="	. $this->reseller	. 
				"&domain="		. strtolower($this->domain)	.
				"&notify="		. $this->email		.
				"&wpasswd="		. $this->passwd		.
				"&vwpasswd="	. $this->passwd		.
				"&users="		. $this->prod['users'] 		.
				"&quota="		. $this->prod['quota'] 		.
				"&enfp="		. $this->prod['enfp'] 		.
				"&enphp="		. $this->prod['enphp'] 		.
				"&enshell="		. $this->prod['enshell'] 	.
				"&enssi="		. $this->prod['enssi'] 		. 
				"&encgi="		. $this->prod['encgi'] 		.
				"&ensuexec="	. $this->prod['ensuexec'] 	.
				"&enthrottle="	. $this->prod['enthrottle'] 	.
				"&enraw="		. $this->prod['enraw'] 		.
				"&enmiva="		. $this->prod['enmiva'] 		.
				"&enssl="		. $this->prod['enssl'] 		.
				"&enfilter="	. $this->prod['enfilter']; 
		$ret = $this->connect($post,4);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";

		# set bandwith
		if($this->prod['enthrottle'] == "1") 
			$this->throttle(false); 

		# add user
		$this->add_user();						

		return true;	 
   }

   /*
   * Add the main user
   */ 
   function add_user()
   {	
		# get unique sitenum
		if(empty($this->sitenumber))
		{
			$sitenum = $this->sitenum();
			if(!$sitenum) return false;	   
		} else {
			$sitenum = $this->sitenumber;
		}

		# throttle  
		$post = "function=goadduser".
				"&subweb="			.
				"&sitenum=$sitenum"	. 
				"&remoteaccess=FTP"	.
				"&useradmin=1"		.
				"&fileadmin=1"		.
				"&quota="			. $this->prod['quota'] 		.
				"&fullname="		. $this->username 	.
				"&username="		. $this->username 	.
				"&passwd="			. $this->passwd 	.
				"&vpasswd="			. $this->passwd; 					 												
		$ret = $this->connect($post,3);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";
		return true;						
   }


   /*
   * Edit the bandwith
   */ 
   function throttle($sitenum)
   {	
		# get unique sitenum
		if(!$sitenum)
		{
			$sitenum = $this->sitenum();
			if(!$sitenum) return false;	   
		}

		# throttle  
		$post = "function=editthrottle". 
				"&sitenum=$sitenum"	. 
				"&limit="			. $this->prod['limit'] 		.
				"&bwunit="			. $this->prod['bwunit'] 	.
				"&duration="		. $this->prod['duration'] 		.
				"&durationunit="	. $this->prod['durationunit']; 					 												
		$ret = $this->connect($post,2);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";
		return true;						
   }


   /*
   * Edit the account
   */ 
   function edit()
   {
		# login:
		if(!$this->login())	 return false;	   

		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false;

		# edit  
		$post = "function=updatesite&sitenum=$sitenum". 
				"&hostname=www"	. 
				"&ipx="			. $this->ip			.
				"&reseller="	. $this->reseller	. 
				"&domain="		. strtolower($this->domain)	.
				"&notify="		. $this->email		.
				"&wpasswd="		. $this->passwd		.
				"&vwpasswd="	. $this->passwd		.
				"&users="		. $this->prod['users'] 		.
				"&quota="		. $this->prod['quota'] 		.
				"&enfp="		. $this->prod['enfp'] 		.
				"&enphp="		. $this->prod['enphp'] 		.
				"&enshell="		. $this->prod['enshell'] 	.
				"&enssi="		. $this->prod['enssi'] 		. 
				"&encgi="		. $this->prod['encgi'] 		.
				"&ensuexec="	. $this->prod['ensuexec'] 	.
				"&enthrottle="	. $this->prod['enthrottle'] 	.
				"&enraw="		. $this->prod['enraw'] 		.
				"&enmiva="		. $this->prod['enmiva'] 		.
				"&enssl="		. $this->prod['enssl'] 		.
				"&enfilter="	. $this->prod['enfilter']; 
		$ret = $this->connect($post,2);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>"; 

		# set bandwith 
		if($this->prod['enthrottle'] == "1") 
			$this->throttle($sitenum);

		# add user
		$this->add_user();

		return true; 					
   }


   /*
   * Suspend account
   */ 
   function suspend($dologin=true)
   {
		# login:
		if($dologin)
			if(!$this->login())	
				return false;	   

		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false;

		# suspend
		$post = "function=suspendsite&sitenum=$sitenum&action=suspend&confirm=1";
		$ret = $this->connect($post,2);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>"; 

		return true; 
   }


   /*
   * Unsuspend account  
   */ 
   function unsuspend($dologin=true)
   {
		# login:
		if($dologin)
			if(!$this->login())	
				return false;	   

		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false;

		# unsuspend
		$post = "function=suspendsite&sitenum=$sitenum&action=activate";
		$ret = $this->connect($post,2);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>"; 

		return true;    
   }


   /*
   * Delete account 
   */ 
   function del()
   {	   
		# login:
		if(!$this->login())	 return false;	   

		# get unique sitenum
		$sitenum = $this->sitenum();
		if(!$sitenum) return false;

		# delete
		$post = "function=delete&sitenum=$sitenum&value=Yes, delete this site";
		$ret = $this->connect($post,2);	
		if($this->debug) echo "<br><br>$this->host?$post<br><textarea cols=100 rows=20>$ret</textarea>";

		return true;   
   }	     


   /*
   * Curl connect 
   */  	   
   function connect($post, $timeout=false)
   {    
		$ch = curl_init(); 			
		if($timeout != false)			
		curl_setopt($ch, CURLOPT_TIMEOUT, 			$timeout); 
		curl_setopt($ch, CURLOPT_COOKIEJAR, 		$this->cookiepath);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 		$this->cookiepath);	 
		curl_setopt($ch, CURLOPT_URL, 				$this->host );  
		if(!empty($post))
		{
			curl_setopt($ch, CURLOPT_POST, 			1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, 	$post);  
		}
		else
		{
			curl_setopt($ch, CURLOPT_HTTPGET, 		true);  
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 	0); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	1); 
		curl_setopt($ch, CURLOPT_HEADER, 			1);

		$data = curl_exec ($ch); 
		curl_close ($ch);  

		return $data; 
   }    	      
}
?>