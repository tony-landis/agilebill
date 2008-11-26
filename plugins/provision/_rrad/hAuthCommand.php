<?
class hAuthCommand extends hCommand
{
	var $username;
	var $password;
	var $salesperson;

	function hAuthCommand($u,$p,$s)
	{
		$this->username = $u;
		$this->password = $p;
		$this->salesperson = $s;
		$this->prefix = "L I";
	}

	function assemble()
	{
		$str =  $this->prefix.$this->delim.$this->username
			 .  $this->delim.$this->password.$this->delim.RRAD_FAMILY."-"
			 .  RRAD_F_VERSION;
		
		if (strlen($this->salesperson))
			$str .= $this->delim.$this->salesperson;
		return $str;
	}
}
?>
