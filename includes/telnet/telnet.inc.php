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
 * Telnet class
 *
 * @package   telnet
 * @author    Joseph Benden <joe@thrallingpenguin.com>
 * @version   0.1
 * @access    public
 */
class telnet {
	
	/**
	 * Specifies the style of communications. 0 for socket line mode, 1 for telnet compatible mode.
	 *
	 * @access private
	 * @var    boolean
	 */
	var $mode = 0;
	
	/**
	 * Whether or not to use microsleep.
	 *
	 * @access private
	 * @var    boolean
	 */
	var $use_usleep = 0;
	
	/**
	 * Amount of time to sleep in between communications.
	 *
	 * @access private
	 * @var    integer
	 */
	var $sleeptime = 125000;
	
	/**
	 * Amount of time to sleep after logging in to the remote server.
	 *
	 * @access private
	 * @var    integer
	 */
	var $loginsleeptime = 1000000;
	
	/**
	 * Internal file descriptor
	 *
	 * @access private
	 * @var    resource
	 */
	var $fp = NULL;
	
	/**
	 * Stored login prompt, used to tell if a username/password failed.
	 *
	 * @access private
	 * @var    string
	 */
	var $loginprompt;

	
	
	
	/**
	 * Connects to a remote telnet or socket line mode server.
	 * @param $server The remote servers IP address or hostname
	 * @param $port The remote servers TCP port
	 * @param $user The username
	 * @param $pass The password
	 * @return 0 for success
	 * @return 1 for an error whilst opening the network connection
	 * @return 2 for an unknown hostname resolution
	 * @return 3 for a login failure
	 * @return 4 for a PHP versioning error
	 */
	function connect($server, $port, $user, $pass) {
		$rv=0;
		$r = "";
		$vers=explode('.',PHP_VERSION);
		$needvers=array(4,3,0);
		$j=count($vers);
		$k=count($needvers);
		if ($k<$j) $j=$k;
		for ($i=0;$i<$j;$i++) {
			if (($vers[$i]+0)>$needvers[$i]) break;
			if (($vers[$i]+0)<$needvers[$i]) return 4;
		}

		# Make sure we're disconnected first
		$this->disconnect();
	
		if (strlen($server)) {
			if (preg_match('/[^0-9.]/',$server)) {
				$ip=gethostbyname($server);
				if ($ip==$server) {
					$ip='';
					$rv=2;
				}
			} else $ip=$server;
		} else $ip='127.0.0.1';
	
		if (strlen($ip)) {
			if (($this->fp=fsockopen($ip, $port))) {
				if ($this->mode == 1) {
					fputs($this->fp,chr(0xFF).chr(0xFB).chr(0x1F).chr(0xFF).chr(0xFB).
						chr(0x20).chr(0xFF).chr(0xFB).chr(0x18).chr(0xFF).chr(0xFB).
						chr(0x27).chr(0xFF).chr(0xFD).chr(0x01).chr(0xFF).chr(0xFB).
						chr(0x03).chr(0xFF).chr(0xFD).chr(0x03).chr(0xFF).chr(0xFC).
						chr(0x23).chr(0xFF).chr(0xFC).chr(0x24).chr(0xFF).chr(0xFA).
						chr(0x1F).chr(0x00).chr(0x50).chr(0x00).chr(0x18).chr(0xFF).
						chr(0xF0).chr(0xFF).chr(0xFA).chr(0x20).chr(0x00).chr(0x33).
						chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0x2C).chr(0x33).
						chr(0x38).chr(0x34).chr(0x30).chr(0x30).chr(0xFF).chr(0xF0).
						chr(0xFF).chr(0xFA).chr(0x27).chr(0x00).chr(0xFF).chr(0xF0).
						chr(0xFF).chr(0xFA).chr(0x18).chr(0x00).chr(0x58).chr(0x54).
						chr(0x45).chr(0x52).chr(0x4D).chr(0xFF).chr(0xF0));
				}
				if ($this->use_usleep) usleep($this->sleeptime);
				else sleep(1);

				if ($this->mode == 1) {
					fputs($this->fp,chr(0xFF).chr(0xFC).chr(0x01).chr(0xFF).chr(0xFC).
						chr(0x22).chr(0xFF).chr(0xFE).chr(0x05).chr(0xFF).chr(0xFC).chr(0x21));
				}
				if ($this->use_usleep) usleep($this->sleeptime);
				else sleep(1);
				
				if ($this->mode == 1) {
					# BEGIN actual LOGIN method
					$this->getresponse($r);
					$r=explode("\n",$r);
					$this->loginprompt=$r[count($r)-1];
			
					fputs($this->fp,"$user\r");
					if ($this->use_usleep) usleep($this->sleeptime);
					else sleep(1);
			
					fputs($this->fp,"$pass\r");
					if ($this->use_usleep) usleep($this->loginsleeptime);
					else sleep(1);
					$this->getresponse($r);
					$r=explode("\n",$r);
					if (($r[count($r)-1]=='')||($this->loginprompt==$r[count($r)-1])) {
						$rv=3;
						$this->disconnect();
					}
					# END LOGIN method
				}
			} else $rv=1;
		}
		return $rv;
	}

	/**
	 * Disconnect from the remote server.
	 * @param $exit If the exit command is used prior to disconnecting. Default is yes.
	 */
	function disconnect($exit=1) {
		$junk = "";
		if ($this->fp) {
			if ($exit) $this->docommand('exit',$junk);
			fclose($this->fp);
			$this->fp=NULL;
		}
	}

	/**
	 * Execute a remote command and return the reply.
	 * @param $c The command to execute
	 * @param $r A reference to a variable which will receive the returned result
	 * @return True or false, if the connection is still alive
	 */
	function docommand($c,&$r) {
		if ($this->fp) {
			fputs($this->fp,"$c\r");
			if ($this->use_usleep) usleep($this->sleeptime);
			else sleep(1);
			$this->getresponse($r);
			$r=preg_replace("/^.*?\n(.*)\n[^\n]*$/","$1",$r);
		}
		return $this->fp?1:0;
	}

	/**
	 * Performs the socket operations required to read all available data from a remote connection.
	 * @param $r A reference to a variable which will receive the read data
	 */
	function getresponse(&$r) {
		$r='';
		do { 
			$r.=fread($this->fp,1000);
			$s=socket_get_status($this->fp);
		} while ($s['unread_bytes']);
	}
	
}

?>