<?php

/**
* This class is basically used to get formatted o/p.
*/

class Response
{
	/**
	* Holds an Error code.
	*
	* @access public
	* @var string
	*/
	var $errorCode;
	
	/**
	* Holds an Class which throws Error.
	*
	* @access public
	* @var string
	*/
	var $errorClass;
	
	/**
	* Holds an Error Description.
	*
	* @access public
	* @var string
	*/
	var $errorMsg;
	
	/**
	* Holds an Error Level.
	*
	* @access public
	* @var string
	*/
	var $errorLevel;
	
	/**
	* @access private
	*/
	var $seperator = "#~#";    // seperator used in Error string.

	/**
	* @access private
	*/
	var $data;	// Holds data.

	/**
	* @access private
	*/
	var $error = false;	//Holds error string.

	/**
	* The constructor which takes data to be analysed as a parameter.
	*
	* @param string data to be analysed
	*
	*/
	function Response($value)
	{
		$this->data = $value;
		if(is_array($this->data))
		{
			$this->errorAnalyse();
		}
	}

	/**
	* @access private
	*
	*/
	// This function analyse the data for error. 
	// If data consists of Error string it fills the variables $errorCode,$errorClass,$errorMsg $errorLevel and $error.

	function errorAnalyse()
	{
		foreach($this->data as $key => $value)
		{
			if ($key == "faultstring")
			{
				$error = array();
				$counter = 1;
				$start = 0;
				
				while($pos = strpos($value,$this->seperator,$start))
				{
					$error[$counter] = substr($value,$start,$pos-$start);
					$start = $pos+strlen($this->seperator);
					$counter = $counter+1;
				}
				$this->errorCode = $error[1];
				$this->errorClass = $error[2];
				$this->errorMsg = $error[3];
				$this->errorLevel = $error[4];
				$this->error = true;
			}
		}
	}
	/**
	* This function returns true/false depending upon whether data is an error string or not.
	*
	* @return boolean
	*
	*/
	function isError()
	{
		return $this->error;
	}

	/**
	* This function returns the data if no error occured . 
	*
	* @return Any
	*
	*/
	function getResult()
	{
		if(!$this->error)
		{
			return $this->data;
		}
		else
		{
			return "<b>Error Occured</b>.<br><br> Access Member Variables of the Response class for Error Description<br>";
		}
	}

	/**
	* This fuction print the Error in proper format.
	*
	* @return void
	*
	*/
	function printError()
	{
		if($this->error)
		{
			print "<b>Error Code:</b><br>" . $this->errorCode . "<br>";
			print "<b>Error Class:</b><br>" . $this->errorClass. "<br>";
			print "<b>Error Description:</b><br>" . $this->errorMsg . "<br>";
			print "<b>Error Level:</b><br>" . $this->errorLevel . "<br>";
		}
		else
		{
			print "<b>No Error:</b> Call printData(\$dataToPrint) to print Result<br><br>";
		}
	}
	
	/**
	* This fuction print the passed data in proper format.
	*
	* @return void
	* @param string Data to print.
	*
	*/
	function printData($dataToPrint)
	{
		if(!$this->error)
		{
			if(is_array($dataToPrint))
			{
				foreach($dataToPrint as $key => $value)
				{
					if(is_array($value))
					{
						$this->printData($value);
					}
					else
					{
						print "$key ---><b> $value </b><br>";
					}
				}
			}
			else
			{
				print "$dataToPrint<br>";
			}
		}
		else
		{
			print "<b>Error Occured:</b> Call printError() to print Error<br><br>";
		}
	}
}
?>