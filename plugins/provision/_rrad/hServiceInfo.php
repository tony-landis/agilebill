<?
class hServiceInfo extends hInfo
{
	function hServiceInfo($domaincon)
	{
		$this->fields = array ("ServiceName", "Status", "ServiceID");
		$this->RRADRetrieveCommand = &new hCommand("I RS","",$domaincon);
	}
}
?>
