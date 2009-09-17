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
 * @author Tony Landis <tony@agileco.com> and Thralling Penguin, LLC <http://www.thrallingpenguin.com>
 * @package AgileBill
 * @version 1.4.93
 */
	
require_once PATH_MODULES.'product/base_product_plugin.inc.php';
 
class base_voip_plugin extends base_product_plugin
{
	function delete_cart($VAR, $cart, $checkDID = false)
	{
		if(!isset($cart['product_attr'])) return;

		$db =& DB();
 		$attr = unserialize($cart['product_attr']);  
 		if(!empty($attr['station']))
 		{ 
 			$did = $attr['station'];
 			
 			if($checkDID) {
	    	// check if user owns did && is in did pool (if so we can assume it was a topup and return)
	    	$didrs = $db->Execute(sqlSelect($db,"voip_did","id,did","did = ::{$did}:: AND account_id=".SESS_ACCOUNT)); 
	    	if($didrs && $didrs->RecordCount()>0) return;
			}
			  	   			
   		// get E164 so we can determine the country code and did npa/nxx/station and find the did and plugin
			include_once(PATH_MODULES.'voip/voip.inc.php');
			$v = new voip;
			$did_arr = $v->get_did_e164($did);
			if(!$did_arr) return; 
		
			$plugin_id = $did_arr['voip_did_plugin_id']; 			
		  
    	// Get the plugin detials 
			$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","plugin,avail_countries","id = $plugin_id"));
			if($rs && $rs->RecordCount() > 0) {
		 		$plugin = $rs->fields['plugin']; 			 
			} else {
				return;
			}
				
   		// load the plugin and call release(); 
    	$file = PATH_PLUGINS.'voip_did/'.$plugin.'.php';
    	if(is_file($file)) {
    		include_once($file);
    		eval('$plg = new plgn_voip_did_'.$plugin.';'); 
				if(is_object($plg)) {
					if(is_callable(array($plg, 'release'))) {
						$plg->id = $did_arr['voip_did_plugin_id'];;
						$plg->did = $did; 		
						$plg->did_id = $did_arr['id']; 
						$plg->release();  
					}
				}
    	}
 		}
	}
	
	function validate_cart($VAR, $product, $did, $ported)
	{
		// get E164 so we can determine the country code and did npa/nxx/station
		$db =& DB();
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;
 		$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
		if ($v->e164($did, $e164, $cc, $npa, $nxx)) 
		{
			if ($ported) return true;
				
			// verify this did is in voip_pool, and is not assigned to an account, and is not reserved 
			if ($cc == '1') {
				$station = substr($e164, 8);
				$sql = sqlSelect($db,"voip_pool","*",
					"(date_reserved IS NULL OR date_reserved=0) AND (account_id IS NULL OR account_id=0) AND country_code=$cc AND npa=$npa AND nxx=$nxx AND station=$station");
			} elseif ($cc == '61') {
				$station = substr($e164, 12);
				$sql = sqlSelect($db,"voip_pool","*",
					"(date_reserved IS NULL OR date_reserved=0) AND (account_id IS NULL OR account_id=0) AND country_code=$cc AND npa=$npa AND nxx=$nxx AND station=$station");
			} else {
				$station = substr($e164, 4 + strlen($cc));
				$sql = sqlSelect($db,"voip_pool","*",
					"(date_reserved IS NULL OR date_reserved=0) AND (account_id IS NULL OR account_id=0) AND country_code=$cc AND station=$station"); 	 
			} 
			$rs = $db->Execute($sql);
			if($rs && $rs->RecordCount() > 0) {
				$did_id = $rs->fields['id'];
				$plugin_id = $rs->fields['voip_did_plugin_id']; 
			} else { 
				return "Sorry, the selected number is not available or has been removed from our system, please go back and select another.";
			}  
		} else {
		 	return "The format for the provided number is incorrect.";
		}
		
		// get the id of the current country calling code
		$country_id = 0;
		$country = $db->Execute($sql = sqlSelect($db,"voip_iso_country_code_map","iso_country_code","country_code = $cc"));				
		if($country && $country->RecordCount() == 1) {
			$countryc = & $db->Execute($sql = sqlSelect($db,"voip_iso_country_code","id","code = ::{$country->fields['iso_country_code']}::"));		 
			if($countryc && $countryc->RecordCount() == 1) {
				$country_id = $countryc->fields['id'];	
			} else {
				return "Sorry, the selected number is not available as the country is disallowed for the current product";
			}
		}

  	// validate that the country is available for the selected plugin 
  	$country_auth = false;
		$rs = $db->Execute(sqlSelect($db,"voip_did_plugin","plugin,avail_countries","id = $plugin_id"));
		if($rs && $rs->RecordCount()) {
		 	$plugin = $rs->fields['plugin'];
			$carr = unserialize($rs->fields['avail_countries']);
			foreach($carr as $cid) {
				if($country_id == $cid) { $country_auth=true; break; }
			}			 
		}
		if(!$country_auth) return "Sorry, the selected number is not available as the country is disallowed for the current product";
	   
  	// Get the plugin details and load plugin as an object
  	$file = PATH_PLUGINS.'voip_did/'.$plugin.'.php';
  	if(is_file($file)) {
  		include_once($file); 
  		eval('$plg = new plgn_voip_did_'.$plugin.';'); 
			if(is_object($plg)) {
				if(is_callable(array($plg, 'reserve'))) {
					$plg->id = $plugin_id;
					$plg->did = $did;
					$plg->did_id = $did_id;
					$plg->country = $cc;				
					$result = $plg->reserve();  
					if($result === true) { 
						return true;
					} else {
						return $result;
					}
				}
			} else {
				return "VoIP DID object couldn't be created.";
			}
  	} else {
  		return "VoIP DID plugin not found.";
  	} 
   	// something failed...
   	return "An unknown error occurred while attempting to reserve your requested number, please try again later.";
	}

	/**
	 * Retrieve the DID assigned to a service ID
	 */	 	
	function get_parent_did($id)
	{
		$db = &DB();
		$sql    = 'SELECT prod_attr_cart FROM '.AGILE_DB_PREFIX.'service WHERE
               id      =  '.$db->qstr($id).' AND
               site_id =  '.$db->qstr(DEFAULT_SITE);
		$rs = $db->Execute($sql);
		@$a = unserialize($rs->fields['prod_attr_cart']);
		$did = "";
		if (!empty($a['station'])) {
			$did = str_replace("-", "", $a['station']);
		}
		if (!empty($a['ported'])) {
			$did = $a['ported'];
		}
		return $did;
	}
}
?>