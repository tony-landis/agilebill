<?
class hAdminInfo extends hInfo
{
	var $fields;
	
	function hAdminInfo(&$domaincon)
	{
		$this->fields = array ("SalesRep", "ExternalID");

		$this->RRADRetrieveCommand = &new hCommand("I RA","",$domaincon);
		$this->RRADUpdateCommand = &new hCommand("I UA","",$domaincon);

	}
	
	function getRRADUpdateCommand()
	{
		for ($i=0; $i<sizeof($this->fields); $i++)
		{
			$fn = $this->fields[$i];
			if ($i > 0)
				$suffix .= " ";
			$suffix .= str_replace(" ", chr(31), $this->properties[$fn]);
		}
		$this->RRADUpdateCommand->suffix = $suffix;
		return $this->RRADUpdateCommand;
	}
}
?>
