<?
class hWebService extends hService
{
	function newDomain ($domain, $password, $package, 
							$email, $linktodomain = "")
	{
		if (strlen($linktodomain)>0)
			$email .= " $linktodomain";
		$cmd = new hCommand("W A", "$password $package $email");
		$cmd->domain = $domain;
		if ($this->RRADServer->write($cmd))
		{
			// Return a domain context if create succeeded ...
			$d = &new hDomain($domain);
			return $d;
		}
		return false;
	}

	function delDomain ()
	{
		return $this->RRADServer->write(
					new hCommand("W D", "", $this->context));
	}

	function setPassword($password)
	{

		echo "\n\n<!-- Password: $password -->\n\n";	
		return $this->RRADServer->write(
					new hCommand("W C",$password,$this->context));
	}

	function setPackage($newpackage, $referencedomain="")
	{
		if (strlen($referencedomain)>1)
			$newpackage .= " $referencedomain";
		return $this->RRADServer->write(
				new hCommand("A C",$newpackage,$this->context));
	}

	function setStorage ($megabytes) 
	{
		return $this->RRADServer->write(
			new hCommand("A S",$megabytes,$this->context));
	}

	function setBandwidth ($megabytes)
	{
		return $this->RRADServer->write(
			new hCommand("A B",$megabytes,$this->context));
	}

	
	// Positive return code on success (it's the service-id.)
	// false on failure

	function addService ($product_code, $quantity = 1, 
			$discount = 0, $comment = "")
	{
		if (strlen($comment)>0)
			$discount .= " $comment"; 
		$cmd = new hCommand("S A","$product_code $quantity $discount",$this->context);
		$r_code = $this->RRADServer->write($cmd);  
		if ($r_code)
		{
			$pieces = explode ("#", $this->RRADServer->getMessage());
			$r_code = preg_replace("/[^0-9]/", "", $pieces[1] );
		}
		return $r_code; 
	} 

	function dropService ($service_name, $id="")
	{
		if (strlen($id)>0)
			$service_name .= " $id";
		$cmd = new hCommand("S D",$service_name,$this->context);
		return $this->RRADServer->write($cmd);
	}
}

?>
