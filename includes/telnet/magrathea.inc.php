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
 * @author Tony Landis <tony@agileco.com> and Thralling Penguin, LLC <http://www.thrallingpenguin.com>
 * @package AgileBill
 * @version 1.4.93
 */

/**
 * Magrathea-Telecom.co.uk Plug in
 *
 * @package   magrathea
 * @author    Joseph Benden <joe@thrallingpenguin.com>
 * @version   0.1
 * @access    public
 */
require_once(PATH_INCLUDES."telnet/telnet.inc.php");

class magrathea extends telnet {
	
	/**
	 * The TCP port number the Magrathea service listens on.
	 * @access private
	 * @var    integer
	 */
	var $port = 777;

	
	
	/**
	 * Handle connecting and logging into Magrathea. Returns 0 on success.
	 * @param $hostname The hostname to connect to
	 * @param $username Username
	 * @param $password Password
	 */
	function login($hostname, $username, $password) {
		$r = ""; $code = ""; $msg = "";
		$ret = $this->connect($hostname, $this->port, $username, $password);
		if ($ret == 0) {
			$ret = false;
			$this->getresponse($r);
			if ($this->use_usleep) usleep($this->sleeptime);
			else sleep(1);
			$this->loginprompt = $r;
			$this->docommand("AUTH $username $password",$r);
			if ($this->parse_response($r, $code, $msg)) {
				if ($code == 0) {
					# Logged in!
					$ret = true;
				}
			}
		} else {
			$ret = false;
		}
		return $ret;
	}

	/**
	 * Log out of the Magrathea system.
	 */
	function logout() {
		$r = ""; $code = ""; $msg = "";
		$this->docommand("QUIT",$r);
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				return true;
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','logout','Error: '.$msg);
			}
		}
		return false;
	}
		
	/**
	 * Attempt to allocate a single phone number. False is returned on error, else a phone number is returned.
	 * @param $areacode The desired area code to find a possible number for, using underscores for wildcard characters.
	 */
	function allocate($areacode) {
		$r = ""; $code = ""; $msg = "";
		$ret = $this->docommand("ALLO $areacode",$r);
		$ret = false;
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				# got a number
				$ret = ereg_replace("[^0-9]","",$msg);
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','allocate','Error: '.$msg);
				return false;
			}
		} else {
			global $C_debug;
			$C_debug->error('magrathea','allocate',"Parse Response Error (code: $areacode): ".$msg);
			return false;
		}
		return $ret;
	}

	/**
	 * Attempt to activate a single phone number. Returns a boolean success indicator.
	 * @param $did The desired phone number to activate.
	 */
	function activate($did) {
		$r = ""; $code = ""; $msg = "";
		$this->docommand("ACTI $did",$r);
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				# got a number
				return true;
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','activate','Error: '.$msg);
			}
		} else {
			global $C_debug;
			$C_debug->error('magrathea','activate','(ACTI '.$did.') Error, couldnot parse: '.$r);
		}
		return false;
	}

	/**
	 * Attempt to deactivate a single phone number. Returns a boolean success indicator.
	 * @param $did The desired telephone number to deactivate/disconnect.
	 */
	function deactivate($did) {
		$r = ""; $code = ""; $msg = "";
		$this->docommand("DEAC $did",$r);
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				return true;
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','deactivate','Error: '.$msg);
			}
		}
		return false;
	}

	/**
	 * Attempt to reactivate a single phone number. Returns a boolean success indicator.
	 * @param $did The desired telephone number to reactivate/reconnect.
	 */
	function reactivate($did) {
		$r = ""; $code = ""; $msg = "";
		$this->docommand("REAC $did",$r);
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				return true;
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','reactivate','Error: '.$msg);
			}
		}
		return false;
	}

	/**
	 * Set a given phone number's destination, over RFC 2833 DTMF SIP. Returns a boolean success indicator.
	 * @param $did The desired telephone number to provision.
	 * @param $sip_url The full URL to send the number to, ie: me@sip1.mydomain.com
	 */
	function set($did, $sip_url) {
		$r = ""; $code = ""; $msg = "";
		$this->docommand("SET $did 1 S:".$sip_url,$r);
		if ($this->parse_response($r, $code, $msg)) {
			if ($code == 0) {
				return true;
			} else {
				# Log an error!
				global $C_debug;
				$C_debug->error('magrathea','reactivate','Error: '.$msg);
			}
		}
		return false;
	}
	
	/**
	 * Parses the responses from Magrathea, pulling out the response code and the associated message.
	 * @param $r The response text
	 * @param $code A reference to a variable to store the parsed response code
	 * @param $msg A reference to a variable to store the parsed message
	 */
	function parse_response($r, &$code, &$msg) {
		$code = intval(substr($r,0,1));
		$msg = substr($r,2);
		#echo "Parsed code: $code \n Parsed msg: $msg \n";
		if ($code == 0)
			return true;
		return false;
	}
}

?>