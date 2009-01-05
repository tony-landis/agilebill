<?php
	
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */
	
class CORE_vars
{
	var $f;
	function CORE_vars()
	{
		if(isset($_GET) && count($_GET) > 0)
		{
			reset ( $_GET );
			while ( list ($key, $val) = each ( $_GET ) ) {
				$this->f["$key"] = $val;
			}
			reset ( $_GET );
		}
		if(isset($_POST) && count($_POST) > 0)
		{
			reset ( $_POST );
			while ( list ($key, $val) = each ( $_POST ) ) $this->f["$key"] = $val;
			reset ( $_POST );
		}                	

		// set the shortcuts:
		if(!isset($this->f["_page"]))
		{
			global $_PAGE_SHORTCUTS;
			for($i=0; $i<count($_PAGE_SHORTCUTS); $i++)
			{
				$shortcut = $_PAGE_SHORTCUTS[$i]["s"];
				if(isset($this->f[$shortcut])) $this->f["_page"] = $_PAGE_SHORTCUTS[$i]["p"];
			}
		}

		// mods for hardcoded vars in config/multi-site
		global $hardcode; 
		if(is_array($hardcode)) {
			foreach($hardcode as $hc) { 
				$this->f["{$hc[0]}"] = $hc[1];
			}
		}
	}

	function strip_slashes($arr) {
		global $VAR;
		if(get_magic_quotes_gpc()) {
			for($i=0; $i<count($arr); $i++) {
				$VAR[$arr[$i]] = htmlspecialchars(stripslashes($VAR[$arr[$i]]));
			}
		}
	}

	function strip_slashes_all() {
		global $VAR;
		if(get_magic_quotes_gpc()) {
			foreach($VAR as $key=>$val) {
				if(!is_array($val)) 
					$VAR["$key"] = htmlspecialchars(stripslashes($val)); 
				else
					foreach($VAR["$key"] as $keya=>$vara) $VAR[$key][$keya] = htmlspecialchars(stripslashes($vara));                    
			}
		}
	}
}
?>
