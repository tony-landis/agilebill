<?

class hUsageInfo extends hInfo
{
	var $startMonth;
	var $endMonth;

	function hUsageInfo($startMonth, $endMonth, $domaincon)
	{
		
		$this->startMonth = $startMonth;
		$this->endMonth = $endMonth;
		$this->fields = array ("Month", "Bandwidth", "Diskusage", 
										"MailboxesUsed", "Mailboxes");
		$this->RRADRetrieveCommand = &new hCommand("I RU","",$domaincon);
	}

	function getRRADRetrieveCommand()
	{
		if (strlen($this->startMonth)>0 )
			$this->RRADRetrieveCommand->suffix .= $this->startMonth;
		
		if (strlen($this->endMonth)>0 )
			$this->RRADRetrieveCommand->suffix .= " ".$this->endMonth;
		return $this->RRADRetrieveCommand;
	}
}

?>
