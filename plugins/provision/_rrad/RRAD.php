<? 
// Author: Dirk Bhagat, August 15, 2000.
// Client API for Rrad: in php. Version: 0.4

// $Id: librrad.php,v 1.1 2000/10/05 16:02:21 dirk Exp $

define ("RRAD_DEFAULT_SERVER_NAME", "rrad.hostopia.com");
define ("RRAD_DEFAULT_SERVER_PORT", 669);
define ("RRAD_DEFAULT_USERNAME", "testola10000.com");
define ("RRAD_DEFAULT_PASSWORD", "test123");
define ("RRAD_FAMILY", "Rasmus");
define ("RRAD_F_VERSION", "1.2");

/**
* RRADServer used to authenticate client, and provide access to other services.
*
* @access Public
*/

class RRADServer
{
    /**
    * Username to authenticate with.
    * @var  string  $username
    */
	var $username;

    /**
    * Password to authenticate with.
    * @var  string  $password
    */
	var $password;

	var $salesrep;
	var $hostname = "rrad.hostopia.com";
	var $port = 669;
	var $sock; //socket to communicate on.

    /**
    * Instantiate an RRADServer object. This must be the first step taken.
    * @param  string
    * @param  string
    * @param  string
    * @access public  
    */
	function RRADServer($user, $pass, $srep="")
	{
		$this->username = $user;
		$this->password = $pass;
		$this->salesrep = $srep;
	}

    /**
    * Auth using credentials supplied. Precedes requests for services etc.
    * @access public  
	*/
	function authenticate()
	{
		$this->sock = fsockopen ($this->hostname, $this->port,
                &$errno, &$errstr, 10);
        if (!$this->sock)
        {
            $this->message = "$errstr ($errno)\n";
			die("Can't connect ".$this->hostname." ".$this->port);
            return false;
        }
		
		$cmd = new hAuthCommand($this->username,$this->password,$this->salesrep);
        $ret = $this->write($cmd);
        if ( (!$ret) || (strlen($this->message)<1) )
           $this->message = "ERR: Invalid username or password.";
        return $ret;
	}

	/**
    * Closes the connection to the server. 
    * @access public
	*/

	function close()
	{
		$cmd = new hCloseCommand();
		$ret = $this->write($cmd);
		if ( (!$ret) || (strlen($this->message)<1) )
			$this->message = "ERR: Could not close connection.";

		/* Try closing the sock anyway */   
		fclose($this->sock);
		$this->sock = false; 
		return $ret;
	}

    /**
    * Writes a command to the server. 
	* @param  object
    * @access private
	*/
	function simple_write($cmd)
	{
		// print "<br>client says: '".$cmd->assemble()."'<br>";
		fputs($this->sock, $cmd->assemble()."\r\n");
	}

    /**
    * Set the host that Authenticate() will connect to. Defaults are provided.
	* @param  string
    * @access public
	*/
	function setHost($host)
	{
		$this->hostname = $host;
	}

    /**
    * Set the Port that Authenticate() will connect to.
	* @param  int
    * @access public
	*/
	function setPort($port)
	{
		$this->port = $port;
	}

    /**
    * Get a context object that is used to configure each service. A domain name is currentl the only accepted value.
	* @param  string
    * @access public
	*/
	function getContext($dom)
	{
		$c = new hDomain($dom);
		return $c;	
	}	

    /**
    * Get the latest message from the server.
    * @access public
	*/

	function getMessage ()
	{
		return $this->message;
	}

    /**
    * Get the next 'row' of data returned from the server.
    * @access private
	*/
	function getNextRow()
	{
        if (!feof($this->sock))
        {
			$this->message = fgets ($this->sock,4096);
			if (substr($this->message,0,1) == chr(3))
				return false;
            else if (substr($this->message,0,4) == "ERR:")
                return false;

			// Strip newline
			$this->message = chop($this->message);
			$vals =  explode(chr(31),$this->message);
			return $vals;
		}
		return false;
	}

    /**
    * Write a command to the server and retrieve the server's response.
	* @param object
    * @access private
	*/
	function write ($cmd)
	{
		if (! $this->sock )
		{
			$this->message = "ERR: Commands out of sync";
			return -1;
		}

		// print "<br>client says: '".$cmd->assemble()."'<br>";
		fputs ($this->sock, $cmd->assemble()."\r\n");
		if (!feof($this->sock))
		{
			$this->message = fgets ($this->sock,128);
			if (substr($this->message,0,2) == "OK")
			{
				return true;
			}
			else 
			{
				return false;
			}
		}
		else
		{
			$this->message = "ERR: No response from server.";
			return false;
		}
	}

    /**
    * Retrieve an hWebService object. Use this for Web methods.
    * @access public
	*/
	function getWebService()
	{
		$w = new hWebService();
		$w->RRADServer = &$this;
		return $w;
	}

    /**
    * Retrieve an hInfoService object. Use this to retrieve Info objects.
    * @access public
	*/
	function getInfoService()
	{
		$i = new hInfoService();
		$i->RRADServer = &$this;
		return $i;
	}

    /**
    * Retrieve an hConvenienceService object. Commonly used methods.
    * @access public
	*/
	
	function getConvenienceService()
	{
		$convenience = new hConvenienceService();
		$convenience->RRADServer = &$this;
		return $convenience;
	}

    /**
    * Retrieve an hAdminService object.
    * @access public
	*/

	function getAdminService()
	{
		$admin = new hAdminService();
		$admin->RRADServer = &$this;
		return $admin;
	}

    /**
    * Retrieve an hMailService object. Used to execute mail management commands.
    * @access public
        */

        function getMailService()
        {
                $mail = new hMailService();
                $mail->RRADServer =&$this;
                return $mail;
        }
}
?>
