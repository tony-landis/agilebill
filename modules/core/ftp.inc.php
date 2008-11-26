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
* FTP Connection Class
* 
* This class creates a connection to a remote host for file upload/download/deletion.
*/
class CORE_ftp
{
	var $server='';
	var $username='';
	var $password='';
	var $port=21;
	var $remote_dir='';



	### Setup the connection:
	function CORE_ftp($server, $username='anonymous', $password='anonymous@domain.com', $port=21)
	{
		$this->server=$server;
		$this->username=$username;
		$this->password=$password;
		$this->port=$port;
	}




	### Test the connection:
	function return_connection()
	{
		# SET THE ERROR REPORTING LEVEL OFF!
		error_reporting(0);

		$conn_id = @ftp_connect($this->server, $this->port);
		if (!$conn_id) return false;

		$login_result = @ftp_login($conn_id, $this->username, $this->password);
		if (!$login_result) return false;

		# SET THE ERROR REPORTING LEVEL ( E_ALL ^ E_NOTICE )
		$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
		eval($error_reporting_eval);
		return $conn_id;
	}




	### Kill the connection:
	function kill()
	{
		if(isset($this->conn))
		$this->disconnect();
		unset($this);
	}




	### Delete a file:
	function delete($filename)
	{
		# SET THE ERROR REPORTING LEVEL OFF!
		error_reporting(0);

		$conn=$this->return_connection();
		if (!$conn) return false;

		@ftp_pasv($conn, $passive);
		$this->set_remote_dir(ftp_pwd($conn));
		if(!ftp_delete($conn, $filename))
		{
			@ftp_quit($this->conn);
			return false;
		}
		else
		{
			@ftp_quit($this->conn);
			return true;
		}

		# SET THE ERROR REPORTING LEVEL ( E_ALL ^ E_NOTICE )
		$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
		eval($error_reporting_eval);
		return true;
	}




	### Send a file:
	function send($filename='', $save_as='', $passive=TRUE)
	{
		# SET THE ERROR REPORTING LEVEL OFF!
		error_reporting(0);

		$conn=$this->return_connection();
		if ( !$conn ) return false;

		@ftp_pasv($conn, $passive);
		$this->set_remote_dir(ftp_pwd($conn));
		if(!ftp_put($conn, $save_as, $filename, FTP_BINARY))
		{
			@ftp_quit($this->conn);
			return false;
		}
		else
		{
			@ftp_quit($this->conn);
			return true;
		}

		# SET THE ERROR REPORTING LEVEL ( E_ALL ^ E_NOTICE )
		$error_reporting_eval = 'error_reporting('.ERROR_REPORTING.');';
		eval($error_reporting_eval);
		return true;
	}

	### Get a file
	function get($filename='', $save_as='', $passive=TRUE)
	{
		# SET THE ERROR REPORTING LEVEL OFF!
		error_reporting(0);

		$conn=$this->return_connection();
		if (!$conn) return false;

		@ftp_pasv($conn, $passive);
		$this->set_remote_dir(ftp_pwd($conn));
		if(!ftp_get($conn, $save_as, $this->remote_dir.$filename, FTP_BINARY))
		{
			@ftp_quit($this->conn);
			return false;
		}
		else
		{
			@ftp_quit($this->conn);
			return true;
		}
	}

	### Set the current directory:
	function set_remote_dir($dir)
	{
		$x = substr($dir, (strlen($dir)-1));
		if($x != "/" && $x != "\\")
		$dir.="/";
		$this->remote_dir=$dir;
	}	
} 
?>