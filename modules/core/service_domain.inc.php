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
	
class service_domain
{
	# Set variables
	function service_domain ( $rs ) {
		$this->domain = $rs;
	}


	########################################################
	# Do domain registration, transfer, park, or renewal  ##
	########################################################

	function s_new()
	{
		# Get the registrar plugin data
		$db = &DB();
		$q = "SELECT * FROM  ".AGILE_DB_PREFIX."host_registrar_plugin WHERE
				id			= ".$db->qstr( $this->domain['host_server_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);;
		$rs = $db->Execute($q);
		if ($rs != false && $rs->RecordCount() == 1) {
			$this->server = $rs->fields;
		}

		# Get the registrar plugin data
		$q = "SELECT * FROM  ".AGILE_DB_PREFIX."host_registrar_plugin WHERE
				id			= ".$db->qstr( $this->domain['domain_host_registrar_id'] )." AND
				site_id     = ".$db->qstr(DEFAULT_SITE);;
		$rs = $db->Execute($q);
		if ($rs === false || $rs->RecordCount() == 0) {
			return false;
		} else {
			# Load the plugin class
			$this->registrar = unserialize( $rs->fields['plugin_data'] );
			$filename = PATH_PLUGINS.'registrar/'.$rs->fields['file'].'.php';
			if(!file_exists($filename)) return false;
			include_once($filename);
			$eval = '$_PLG = new plg_reg_'.$rs->fields['file'].'($this);';
			eval($eval);
		}

		switch ( $this->domain['domain_type'] )
		{
			case 'register':
				return $_PLG->register();
			break;

			case 'transfer':
				return $_PLG->transfer();
			break;

			case 'park':
				return $_PLG->park();
			break;

			case 'renew':
				return $_PLG->renew();
			break;
		}
		return false;
	}
}
?>