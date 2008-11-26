<?
class hInfoService extends hService
{
	function getContactInfo()
	{
		$info = new hContactInfo($this->context);
		if ($this->getInfo($info))
			return $info;
		else
			return false;
	}

	function getAdminInfo()
	{
		$info = new hAdminInfo($this->context);
		if ($this->getInfo($info))
			return $info;
		else
			return false;
	}

	function getServiceInfo()
	{
		$info = new hServiceInfo($this->context);
		if ($this->getInfo($info))
			return $info;
		else
			return $false;
	}
	
	function getUsageInfo($startMonth="", $endMonth="")
    {
		if (strlen($startMonth)> 1)
		{
			if ($startMonth < 0 || $startMonth > 12)
				return false;
		}
		if (strlen($endMonth)> 1)
        {
            if ($endMonth < 0 || $endMonth > 12)
                return false;
        }
	
		// Allow range to be inverted .. i.e. startMonth > endmonth ...
	
        $info = new hUsageInfo($startMonth,$endMonth,$this->context);
		if ($this->getInfo($info))
            return $info;
        else
			return false;
    }
	
	function setInfo($info)
	{
		$cmd = &$info->getRRADUpdateCommand();
		return $this->RRADServer->write($cmd);
	}
	
	function getInfo(&$info)
	{
		$cmd = &$info->getRRADRetrieveCommand();
		$this->RRADServer->simple_write($cmd);
		
		if (!is_array($this->RRADServer->getNextRow()))
			return false;

		while (($row = $this->RRADServer->getNextRow()))
				$info->addElement($row);
		return true;
	}
}

?>
