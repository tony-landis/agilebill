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
	
	
# Account Discount Affiliate Plugin
class plgn_aff_ACCOUNT_DISCOUNT
{
	########################################################################
	## Add new affiliate:
	########################################################################

	function add($account_id, $affiliate_id)
	{
	}



	########################################################################
	## Add an account credit for this affiliate
	########################################################################

	function commission($total, $affiliate_id, $affiliate_commission_id)
	{
		global $plgn_aff_MONEYBOOKERS;

		### Get the affiliate details:
		$db		= &DB();
		$sql    = 'SELECT account_id, plugin_data FROM ' . AGILE_DB_PREFIX . 'affiliate
					WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					id          = ' . $db->qstr($affiliate_id);
		$aff = $db->Execute($sql);  
		$account_id = $aff->fields['account_id'];

		### Check that this account has not already been credited for this commission id:
		$AFFCOM = $affiliate_id.'-'.$affiliate_commission_id;
		$sql    = 'SELECT id FROM ' . AGILE_DB_PREFIX . 'discount WHERE
					site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
					name		= ' . $db->qstr($AFFCOM);
		$check = $db->Execute($sql);  
		if($check->RecordCount() > 0) return;

		### Add the credit to this affiliates account:  
		$id = $db->GenID(AGILE_DB_PREFIX . 'invoice_id');
		$sql    = 'INSERT INTO ' . AGILE_DB_PREFIX . 'discount
					SET
					id					= '. $db->qstr($id) 	. ',
					site_id     		= '. $db->qstr(DEFAULT_SITE) . ',
					date_orig			= '. $db->qstr(time()) 	. ',
					date_start			= '. $db->qstr(time()) 	. ',
					status				= '. $db->qstr('1') 	. ',
					name				= '. $db->qstr($AFFCOM) . ',
					notes				= '. $db->qstr("Affiliate Commission ID $affiliate_commission_id, $affiliate_id") . ',
					max_usage_account 	= '. $db->qstr("1") 	. ',
					avail_account_id  	= '. $db->qstr($account_id) . ', 
					new_status			= '. $db->qstr("1") 	. ',
					new_type			= '. $db->qstr("1") 	. ',
					new_rate			= '. $db->qstr($total) 	. ',
					new_max_discount	= '. $db->qstr($total) 	. ',
					new_min_cost		= '. $db->qstr($total) 	. ',            
					recurr_status		= '. $db->qstr("1") 	. ',
					recurr_type			= '. $db->qstr("1") 	. ',
					recurr_rate			= '. $db->qstr($total) 	. ',
					recurr_max_discount	= '. $db->qstr($total) 	. ',
					recurr_min_cost		= '. $db->qstr($total);
		$check = $db->Execute($sql);  
	}


	########################################################################
	## Create the header for the affiliate export file
	########################################################################

	function header()
	{
		echo "<BR><CENTER>The discounts have been added to the affiliates accounts.</CENTER>";
	}         
}
?>