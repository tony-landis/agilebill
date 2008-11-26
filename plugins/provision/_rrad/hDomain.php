<?
class hDomain 
{
	var $domainname;
	var $RRADServer;

	function getName()
	{
		return $this->domainname;
	}

	function hDomain ($domain)
	{
		$this->domainname = $domain;
	}
}
?>
