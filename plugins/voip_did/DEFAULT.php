<?php

/** DEFAULT AgileVoice BoIP DID Pool Plugin 
* @example include_once(PATH_PLUGINS.'voip_did/'.$plugin_name.'.php');
* @example eval('$did_plugin = new plg_voip_did_'.$plugin_name.';');
* @example $did_plugin->id = $voip_did_plugin_id;
* @example $did_plugin->did = $did;
* @example $did_plugin->country = $country;
* @example $did_plugin->method();
*/
class plgn_voip_did_DEFAULT
{
	var $did;					// full E164 DID
	var $country;				// country calling code
	var $release_minutes;		// The configured release minutes for reserved DIDs
	var $plugin;				// The plugin name
	var $reserve=24;			// Number of hours reserved 
	var $name='DEFAULT';		// Plugin name
	var $avail_countries;		// Available countries array 

	/** Get the plugin settings from the database */
	function config() {
		$db =& DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","*","id = $this->id"));
		$this->release_minutes = $rs->fields['release_minutes'];
		$this->avail_countries = $rs->fields['avail_countries'];  
	}
	
	/** 
	* Once a DID has been purchased and payment has been received from the customer, this
	* function then asks the DID provider to actually provision the DID to us.
	*/	
	function purchase() {
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		$plugin->account_id = $this->account_id;

		return $plugin->purchase($this->id, $this->did);
	}
	
	/** Reserve a DID
	*/
    function reserve() {
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		
		return $plugin->reserve($this->id, $this->did);
    }
    
    /** Release a reserved DID
    */
    function release() {
    	# DIDx doesn't support an API method to cancel a DID
    	# So, I guess the number remains ours - just free it from the customer.
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		
		return $plugin->release($this->id, $this->did);    	
    }
     
    /** Task to refresh available dids cart items
    */
    function refresh() {
 	}
}
?>
