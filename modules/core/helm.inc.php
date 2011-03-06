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
 * Helm Remote Class  
 */
class HELM
{ 
	var $host;
	var $port;
	var $ssl;		 
	var $user;
	var $pass;
	var $home_dir;
	var $shell; 

	/*
	* Check for Agilebill
	*/
	function HELM()
	{
		if(!defined("PATH_FILES"))
		{
			echo "Dependancy Failure!";
			exit;
		}
	}

	/*
	* Add a user and define available modules 
	*/ 
	function add($reseller,$username,$domain,$tld,$plan_id,$plan_name,$fname,$lname,$company,$address,$city,$state,$zip,$email)
	{  

		# got to login first... 
		$url  = $this->host.'/default.asp';
		$post = "txtUsername=tony&txtPassword=slashdot1&RememberMe=1&btnProcess=Login&selLanguageCode=EN&selInterface=standard_XP";
		$ret = $this->connect($url,$post);	

		# logged in ok?
		if(!preg_match("/You are now logged in/i", $ret)) {
			echo "Login failed";
			return false;
		} 

		# create the user
		$url  = $this->host.'/interfaces/standard/addUser.asp'; 
		$post = "processing=1".
				"&UserAccNum=$reseller".
				"&SendWelcomeMessage=1".
				"&action=ADD".
				"&edited=TRUE". 
				"&NewAccountNumber=$username".
				"&FirstName=$fname".
				"&LastName=$lname".
				"&CompanyName=$company".
				"&Address1=$address".
				"&Town=$city".
				"&County=$state".
				"&Postcode=$zip".
				"&PrimaryEmail=$email".
				"&Country=US"; 
		$ret = $this->connect($url,$post); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		

		# Add the package
		$url  = $this->host.'/interfaces/standard/addpackage.asp'; 
		$post = "processing=0".
				"&txtStatus=0".
				"&UserAccNum=$username". 
				"&action=ADD".
				"&PlanID=$plan_id". 
				"&PackageName=$plan_name"; 
		$ret = $this->connect($url,$post); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		

		# Get the Package ID Just added			
		$USERNAME = strtoupper($username);
		preg_match ("/(UserAccNum=$USERNAME&PackageID=)+([0-9]){1,}/i", $ret, $arr); 			 
		if(is_array($arr) && count($arr) > 0) 
		{ 
			$package = preg_replace("/UserAccNum=$USERNAME&PackageID=/","", $arr[0]); 
		}	

		# Test for package id
		if(!@$package > 0) 
		{
			if($this->debug) echo  'Invalid package Id, cannot add domain.';
			return false;
		}

		# Add the Domain
		$url  = $this->host.'/interfaces/standard/AddDomain.asp'; 
		$post = "processing=0". 
				"&stage=4".
				"&UserAccNum=$username". 
				"&action=ADD". 
				"&selDomainReg=NONE".
				"&PackageID=$package".
				"&DomainName=$domain".
				"&DomainExt=$tld"; 
		$ret = $this->connect($url,$post, 5); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		 

		# if it timed out, no errors took place!
		if(empty($ret))			
		return true; 
		else
		return false;
   }


   /*
   * Suspend account in helm
   */ 
   function suspend($username)
   {
		# Add the Domain
		$url  = $this->host.'/interfaces/standard/user.asp'; 
		$post = "action=EDIT".  
				"&UserAccNum=$username". 
				"&txtStatus=1"; 
		$ret = $this->connect($url,$post, 5); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		 

		if($ret != false)
		return true;
		else
		return false;
   }


   /*
   * Unsuspend account in helm
   */ 
   function unsuspend($username)
   {
		# Add the Domain
		$url  = $this->host.'/interfaces/standard/user.asp'; 
		$post = "action=EDIT". 
				"&UserAccNum=$username". 
				"&txtStatus=0"; 
		$ret = $this->connect($url,$post, 5); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		 

		if($ret != false)
		return true;
		else
		return false;	   
   }


   /*
   * Delete account from helm
   */ 
   function del($username)
   {
		# Add the Domain
		$url  = $this->host.'/interfaces/standard/user.asp'; 
		$post = "action=DELETE". 
				"&UserAccNum=$username"; 
		$ret = $this->connect($url,$post, 5); 
		if($this->debug) echo "<br><br>$url?$post<br><textarea cols=100 rows=20>$ret</textarea>";		 

		if($ret == false)
		return true;
		else
		return false;		   
   }	     


   /*
   * Curl connect 
   */  	   
   function connect($url,$post, $timeout=false)
   {
		if($this->ssl) 
		$url = 'https://'.$url;   
		else
		$url = 'http://'.$url;

		$ch = curl_init(); 			
		if($timeout != false)			
		curl_setopt($ch, CURLOPT_TIMEOUT, 			$timeout);
		curl_setopt($ch, CURLOPT_COOKIEJAR, 		$this->cookie_path);
		curl_setopt($ch, CURLOPT_COOKIEFILE, 		$this->cookie_path);			 
		curl_setopt($ch, CURLOPT_URL, 				$url);
		curl_setopt($ch, CURLOPT_POST, 				1); 
		curl_setopt($ch, CURLOPT_POSTFIELDS, 		$post);  
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 	1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 	0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 	0); 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 	1);
		curl_setopt($ch, CURLOPT_VERBOSE, 			1 );
		curl_setopt($ch, CURLOPT_HEADER, 			1);

		$data = curl_exec ($ch); 
		curl_close ($ch);  
		return $data; 
   }    	      
}
?>