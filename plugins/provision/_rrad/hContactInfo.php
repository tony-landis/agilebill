<?
class hContactInfo extends hInfo
{
	var $fields;
	
	function hContactInfo(&$domaincon)
	{
		$this->fields = array (
				"FirstName", "LastName", "Company", "Address1",
				"Address2", "City", "State", "PostalCode", "Country",
				"Email", "Phone", "Fax");

		$this->RRADRetrieveCommand = &new hCommand("I RC","",$domaincon);
		$this->RRADUpdateCommand = &new hCommand("I UC","",$domaincon);

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
