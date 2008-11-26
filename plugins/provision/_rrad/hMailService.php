<?
class hMailService extends hService
{
	function setNumAccounts($numaccts)
	{
		return $this->RRADServer->write(new hCommand("A E", $numaccts, $this->context));
	}
}

?>
