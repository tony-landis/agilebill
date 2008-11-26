<?

class hAdminService extends hService
{
        function enable()
        {
                $cmd = new hCommand("A AE", "", $this->context);
                $r_code = $this->RRADServer->write($cmd);
                return($r_code);
        }

        function disable()
        {
                $cmd = new hCommand("A AD", "", $this->context);
                $r_code = $this->RRADServer->write($cmd);
                return($r_code);
        }

	function suspend()
	{
		$cmd = new hCommand("A AS", "", $this->context);
                $r_code = $this->RRADServer->write($cmd);
                return($r_code);
	}

	function unsuspend()
	{
		$cmd = new hCommand("A AU", "", $this->context);
                $r_code = $this->RRADServer->write($cmd);
                return($r_code);
	}
}

?>
