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
	
# MoneyBookers Affiliate Plugin
class plgn_aff_MONEYBOOKERS
{
	########################################################################
	## Add new affiliate:
	########################################################################

	function add($account_id, $affiliate_id)
	{
		$db     = &DB();
		$sql    = 'SELECT email FROM ' . AGILE_DB_PREFIX . 'account WHERE
				   site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
				   id          = ' . $db->qstr($account_id);
		$result = $db->Execute($sql);
		if($result->RecordCount() > 0)
		{
			$plugin_data["email"] = $result->fields['email'];
			$sql    = 'UPDATE ' . AGILE_DB_PREFIX . 'affiliate SET
					   plugin_data =  '. $db->qstr(serialize($plugin_data)) . '
					   WHERE
					   site_id     =  '. $db->qstr(DEFAULT_SITE) . ' AND
					   id          =  '. $db->qstr($affiliate_id);
			$result = $db->Execute($sql);
		}
	}



	########################################################################
	## Create the line in the export commission file for this affiliate
	########################################################################

	function commission($total, $affiliate_id, $affiliate_commission_id)
	{
		global $plgn_aff_MONEYBOOKERS;

		### Get the affiliate details:
		$db		= &DB();
		$sql    = 'SELECT plugin_data FROM ' . AGILE_DB_PREFIX . 'affiliate
					WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($affiliate_id);
		$aff = $db->Execute($sql);
		$plugin_data = unserialize($aff->fields['plugin_data']);

		### Generate this line for the export:
		$ret  = $plugin_data["email"] . '	';
		$ret .= 'USD	';
		$ret .= $total . '	';
		$ret .= "Affiliate Commission " . $affiliate_commission_id . " for ".$affiliate_id;
		$ret .= '
';
		### Return the generated line:
		return $ret;
	}

	########################################################################
	## Create the header for the affiliate export file
	########################################################################

	function header()
	{
	   $filename = 'MoneyBookers_Affiliate_Mass_Payment.txt';   	 
	   header ('Content-type: application/x-txt');
	   header ("Content-Disposition: inline; filename=$filename" ); 
	}         
}
?>