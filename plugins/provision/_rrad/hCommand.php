<?
class hCommand
{
 	var $prefix; 	
 	var $suffix; 	
	var $domain;
	var $delim = " ";

	function assemble()
	{
	 	$cmd = $this->prefix.$this->delim.$this->domain;
	 	if (strlen($this->suffix)>0)
	 		$cmd .= $this->delim.$this->suffix;
		return $cmd;	
	}

	function hCommand($prefix="",$suffix="", $domaincon=false)
	{
		$this->prefix = $prefix;
		$this->suffix = $suffix;
		if (is_object($domaincon))
			$this->domain = $domaincon->getName();
	}
}
?>
