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
 * Weblog Handler Class 
 */
class CORE_weblog
{
	/**
	* Logs the sesson variables.
	* 
	* @return 	void
	* @since 	Version 1.0
	*/

	function CORE_weblog()
	{
		global $current_module, $VAR;		
		if ($current_module == '')
			$module = '';
		else
			$module = $current_module;

		if (!isset($VAR['_page']) || $VAR['_page'] == '')
		{
			$page = '';
		}
		else
		{
			@$pagearr = explode(':', $VAR['_page']);
			@$page = $pagearr["1"];
		}

		### browser type:
		$arr_browser = $this->get_browser();
		$browser = $arr_browser["type"]. ' ' . $arr_browser["ver"];

		### product:
		if (isset($VAR['prid']))
		$product = $VAR['prid'];
		else
		$product = '';

		### log the current hit to the database
		$db = &DB();
		$id = $db->GenID(AGILE_DB_PREFIX . "" . 'weblog_id');
		$sql = "INSERT INTO ".AGILE_DB_PREFIX."weblog SET
				id      	= ".$db->qstr($id).",
				date_orig   = ".$db->qstr(time()).",
				site_id		= ".$db->qstr(DEFAULT_SITE).",
				session_id	= ".$db->qstr(SESS).",
				account_id	= ".$db->qstr(SESS_ACCOUNT).",
				affiliate_id= ".$db->qstr(SESS_AFFILIATE).",
				reseller_id	= ".$db->qstr(SESS_RESELLER).",
				campaign_id	= ".$db->qstr(SESS_CAMPAIGN).",
				product_id	= ".$db->qstr($product).",
				module		= ".$db->qstr($module).",
				page		= ".$db->qstr($page).",
				referrer	= ".$db->qstr($this->get_referrer()).",
				browser		= ".$db->qstr($browser).",
				os			= ".$db->qstr($this->get_os());
		$result = $db->Execute($sql);

		# error reporting:
		if ($result === false)
		{
			global $C_debug;
			$C_debug->error('weblog.inc.php','weblog', $db->ErrorMsg());
		}

		return;
	}




	/**
	* Gets the current referrer.
	*
	* @return 	void
	* @since 	Version 1.0
	*/

	function get_referrer()
	{
		global $HTTP_REFERER, $_SERVER;

		if(@$HTTP_REFERER != "")
			$re = @$HTTP_REFERER;
		else
			$re = @$_SERVER['HTTP_REFERER'];


		if ($re != '')
		{
			if (URL != '')
			{	
				if (preg_match ('/'.URL.'/i', $re)) return;
			}
			if (SSL_URL != '')	
			{
				if (preg_match ('/'.SSL_URL.'/i', $re)) return;
			}
		}

		return $re;
	}	


	/**
	* Gets the current user's browser type.
	*
	* @return   string
	* @since 	Version 1.0
	*/

	function get_browser()
	{
		global $HTTP_USER_AGENT, $_SERVER;

		if(@$HTTP_USER_AGENT  == "") @$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

		$browser=array();
		if(@preg_match("@(opera) ([0-9]{1,2}.[0-9]{1,3}){0,1}@i",$HTTP_USER_AGENT,$match) || @preg_match("@(opera/)([0-9]{1,2}.[0- 9]{1,3}){0,1}@i",$HTTP_USER_AGENT,$match))
		{
			$browser['type']= "Opera";
			$browser['ver']=$match[2];
		} elseif(preg_match("@(konqueror)/([0-9]{1,2}.[0-9]{1,3})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "Konqueror";
			$browser['ver']=$match[2];
		} elseif(preg_match("@(lynx)/([0-9]{1,2}.[0-9]{1,2}.[0-9]{1,2})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "Lynx";
			$browser['ver']=$match[2];
		} elseif(preg_match("@(links) \(([0-9]{1,2}.[0-9]{1,3})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "Links";
			$browser['ver']=$match[2];
		} elseif(preg_match("@(msie) ([0-9]{1,2}.[0-9]{1,3})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "MSIE";
			$browser['ver']=$match[2];
		} elseif(preg_match("@(netscape6)/(6.[0-9]{1,3})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "Netscape";
			$browser['ver']=$match[2];
		} elseif(preg_match("@mozilla/5@i",$HTTP_USER_AGENT)) {
			$browser['type'] = "Mozilla";
			$browser['ver']="Unknown";
		} elseif(preg_match("@(mozilla)/([0-9]{1,2}.[0-9]{1,3})@i",$HTTP_USER_AGENT,$match)) {
			$browser['type'] = "Netscape ";
			$browser['ver']=$match[2];
		} elseif(preg_match("/w3m/i",$HTTP_USER_AGENT)) {
			$browser['type'] = "w3m";
			$browser['ver']="Unknown";
		} else {
			$browser['type'] = "Unknown";
			$browser['ver']="Unknown";
		}
		return $browser;
	}

	/**
	* Gets the current user's AGILE_OS type.
	*
	* @return 	string
	* @since 	Version 1.0
	*/
	function get_os()
	{
		global $HTTP_USER_AGENT, $_SERVER;

		if(@$HTTP_USER_AGENT  == "") @$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT'];

		if(preg_match("/linux/i",$HTTP_USER_AGENT))
		{
			$return = "Linux";
		} elseif(preg_match("/win32/i",$HTTP_USER_AGENT)) {
			$return = "Windows";
		} elseif((preg_match("@(win)([0-9]{2})@i",$HTTP_USER_AGENT,$match)) || (eregi("(windows) ([0-9]{2})",$HTTP_USER_AGENT,$match))) {
			$return = "Windows $match[2]";
		} elseif(preg_match("@(winnt)([0-9]{1,2}.[0-9]{1,2}){0,1}@i",$HTTP_USER_AGENT,$match)) {
			$return = "Windows NT $match[2]";
		} elseif(preg_match("@(windows nt)( ){0,1}([0-9]{1,2}.[0-9]{1,2}){0,1}@i",$HTTP_USER_AGENT,$match)) {
			$return = "Windows NT $match[3]";
		} elseif(preg_match("/mac/i",$HTTP_USER_AGENT)) {
			$return = "Macintosh";
		} elseif(preg_match("@(sunos) ([0-9]{1,2}.[0-9]{1,2}){0,1}@i",$HTTP_USER_AGENT,$match)) {
			$return = "SunOS $match[2]";
		} elseif(preg_match("@(beos) r([0-9]{1,2}.[0-9]{1,2}){0,1}@i",$HTTP_USER_AGENT,$match)) {
			$return = "BeOS $match[2]";
		} elseif(preg_match("/freebsd/i",$HTTP_USER_AGENT)) {
			$return = "FreeBSD";
		} elseif(preg_match("/openbsd/i",$HTTP_USER_AGENT)) {
			$return = "OpenBSD";
		} elseif(preg_match("/irix/i",$HTTP_USER_AGENT)) {
			$return = "IRIX";
		} elseif(preg_match("@os/2@i",$HTTP_USER_AGENT)) {
			$return = "AGILE_OS/2";
		} elseif(preg_match("/plan9/i",$HTTP_USER_AGENT)) {
			$return = "Plan9";
		} elseif(preg_match("/unix/i",$HTTP_USER_AGENT) || preg_match("/hp-ux/i",$HTTP_USER_AGENT)) {
			$return = "Unix";
		} elseif(preg_match("/osf/i",$HTTP_USER_AGENT)) {
			$return = "OSF";
		} else {
			$return = "Unknown";
		}
		return $return;
	}
}
?>