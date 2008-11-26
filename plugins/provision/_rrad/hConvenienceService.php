<?
class hConvenienceService extends hService
{
	function newDomain ($domain, $password, $package, 
									$contactInfo, $linkdomain="")
							
	{
		$web_svc = $this->RRADServer->getWebService();
		$info_svc = $this->RRADServer->getInfoService();

		$newdom = $web_svc->newDomain( $domain,$password, 
				$package, $contactInfo["Email"],$linkdomain);
		if (!$newdom)
			return false;

		$info_svc->setContext($newdom);
		$contact_details = $info_svc->getContactInfo();
		if (!$contact_details)
			return false; // Domain created, but couldn't set info ...?

		foreach($contactInfo as $k => $v)
			$contact_details->set($k,$v);
		return $info_svc->setInfo($contact_details);
	}

	function delDomain ($domain)
	{
		$web_svc = $this->RRADServer->getWebService();
		$dom = $this->RRADServer->getContext($domain);
        if (!$dom)
            return false;
        $web_svc->setContext($dom);

        return $web_svc->delDomain();
	}
	
	function setPackage($domain, $package, $referencedomain="")
	{
		$web_svc = $this->RRADServer->getWebService();
		$dom = $this->RRADServer->getContext($domain);
        
        if (!$dom)
            return false;

        $web_svc->setContext($dom);
		return $web_svc->setPackage($package,$referencedomain);
	}
	
	function setPassword($domain, $password)
	{
		$web_svc = $this->RRADServer->getWebService();
		$dom = $this->RRADServer->getContext($domain);
        
		if (!$dom)
			return false;
        $web_svc->setContext($dom);
        return $web_svc->setPassword($password);
	}

	function addService ($domain, $product_code, $quantity = 1, 
			$discount = 0, $comment = "")
	{
		$web_svc = $this->RRADServer->getWebService();
		$dom = $this->RRADServer->getContext($domain);
        
        if (!$dom)
            return false;
        $web_svc->setContext($dom);

		return $web_svc->addService($product_code,$quantity, 
										$discount,$comment);
	} 
	
	function dropService($domain, $service, $id="")
	{
	 	$web_svc = $this->RRADServer->getWebService();
        $dom = $this->RRADServer->getContext($domain);

        if (!$dom)
            return false;
        $web_svc->setContext($dom);
		return $web_svc->dropService($service,$id);
	}
}
?>
