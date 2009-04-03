<?php

/** MAGRATHEA AgileVoice VoIP DID Pool Plugin 
* @example include_once(PATH_PLUGINS.'voip_did/'.$plugin_name.'.php');
* @example eval('$did_plugin = new plg_voip_did_'.$plugin_name.';');
* @example $did_plugin->id = $voip_did_plugin_id;
* @example $did_plugin->did = $did;
* @example $did_plugin->country = $country;
* @example $did_plugin->method();
*/
class plgn_voip_did_MAGRATHEA
{
	var $id;					// voip_did_plugin_id from database
	var $did;					// full E164 DID
	var $country;				// country calling code
	var $release_minutes;		// The configured release minutes for reserved DIDs
	var $plugin;				// The plugin name
	var $reserve=24;			// Number of hours reserved 
	var $name='MAGRATHEA';		// Plugin name
	var $avail_countries;		// Available countries array
	var $plugin_data;			// Plugin data array
	var $host;					// host to provision to
	var $user;					// MAGRATHEA username
	var $pass;					// MAGRATHEA password
	var $server;				// MAGRATHEA-TELECOM server
	var $poolcount;				// Number of DIDs to request from their server
	var $type;					// sip/iax
	var $country_area;			// array with available country/areas ( $country_area[0][country_id] and $country_area[0][area_code] )
	var $codes;					// array of return error messages from MAGRATHEA.org
	
	/** Get the plugin settings from the database */
	function config() {
		$db =& DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","*","id = $this->id"));
		$this->release_minutes = $rs->fields['release_minutes'];
		$this->avail_countries = $rs->fields['avail_countries'];
		$this->plugin_data = unserialize($rs->fields['plugin_data']);  
		$this->user = $this->plugin_data['user'];
		$this->pass = $this->plugin_data['pass'];
		$this->type = @$this->plugin_data['type'];
		$this->host = $this->plugin_data['host'];
		$this->server = $this->plugin_data['server'];
		$this->poolcount = $this->plugin_data['poolcount'];
		$this->country_area = $this->plugin_data['country_area'];
	}
	
	/** 
	* Once a DID has been purchased and payment has been received from the customer, this
	* function then asks the DID provider to actually provision the DID to us.
	*
	*/	
	function purchase() {
		$this->config();

    	# include the magrathea/telnet classes
    	include_once(PATH_INCLUDES."telnet/magrathea.inc.php");

    	$bOk = false;
		$t = new magrathea();
		$ret = $t->login($this->server,$this->user,$this->pass);
		if ($ret !== false) {
			if ($t->activate(substr($this->did,5)) === false) {
				$this->log_message('purchase','Error while calling activate');
			} else {
				echo "activated";
				# Set the destination of the DID
				if( $t->set(substr($this->did,5), $this->did."@".$this->host) === false) {
					$this->log_message('purchase','Error while calling set');
				} else {
					$bOk = true;
					echo "set!";
				}
			}
		} else {
			$this->log_message('purchase','Error during login: server='.$this->server." user=".$this->user);
		}
		$t->logout();

		if ($bOk == false) {
			$this->log_message('purchase', $this->did.':Magrathea-Telecom request for purchasing DID failed:');
			return false;
		}	
		mail("jbenden@agilevoice.com","magrathea plugin","purchase {$this->id} on did {$this->did}");
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		$plugin->account_id = $this->account_id;

		return $plugin->purchase($this->id, $this->did);
	}
	
	/** 
	 * Reserve a DID
	 *
	 */
    function reserve() {
    	require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		
		return $plugin->reserve($this->id, $this->did);
    }
    
    /** 
     * Release a reserved DID
     *
     */
    function release() {
		$this->config();
		
    	# include the magrathea/telnet classes
    	include_once(PATH_INCLUDES."telnet/magrathea.inc.php");

    	$bOk = false;
		$t = new magrathea();
		$ret = $t->login($this->server,$this->user,$this->pass);
		if ($ret !== false) {
			if ($t->deactivate(substr($this->did,5)) === false) {
				$this->log_message('release','Error while calling activate');
			} else {
				$bOk = true;
			}
		}
		$t->logout();

		if ($bOk == false) {
			$this->log_message('release', $this->did.':Magrathea-Telecom request for deactivating DID failed:');
			return false;
		}
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		
		return $plugin->release($this->id, $this->did);    	
    }
     
    /** Task to refresh available dids cart items
    */
    function refresh() {
    	# read configuration
    	$this->config();
	#$this->log_message('refresh','Refreshing did pool id: '.$this->id);
    	
    	# include the magrathea/telnet classes
    	include_once(PATH_INCLUDES."telnet/magrathea.inc.php");

    	$bOk = false;
		$t = new magrathea();
		$ret = $t->login($this->server,$this->user,$this->pass);

		if ($ret === false) {
			$this->log_message('refresh','Error while refreshing DID pool.');
			return false;
		}
		
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$voip = new voip;
    	
    	$db =& DB();
		$entries = explode("\r\n", $this->country_area);
		foreach ($entries as $entry) {
			$eparts = explode(":", $entry);
			$areas = explode(",", $eparts[1]);
			$bDelete = false;
			foreach ($areas as $area) {

				# the request must be padded with underscores to make a valid number
				$orig_area = $area;
				while (strlen($area) != 11) {
					$area .= "_";
				}
				
				$num_to_get = $this->poolcount;
				$sql = sqlSelect($db, "voip_pool", "count(id)","country_code=::".$eparts[0].":: AND voip_did_plugin_id=::".$this->id.":: AND station like ::".$orig_area."%:: AND (account_id is null or account_id=0)");
				$rs = $db->Execute($sql);
				if ($rs) {
					$num_to_get -= $rs->fields[0];
				}
				if ($num_to_get < 1) {
					$num_to_get = 0;
				}
				# $this->log_message('refresh',"Acquiring $num_to_get DIDs for area $area: $sql");		
				for($didnum = 0; $didnum < $num_to_get; $didnum++) {
					if (($v=$t->allocate($area)) !== false) {
					
					$v = "011".$eparts[0].$v;
					# got a phone number! let's insert it into the pool
					$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
					if ($voip->e164($v, $e164, $cc, $npa, $nxx)) {
						unset($fields);
						$fields['country_code'] = $cc;
						$fields['voip_did_plugin_id'] = $this->id;
						if ($cc == '1') {
							$fields['station'] = substr($e164, 8);
							$fields['npa'] = $npa;
							$fields['nxx'] = $nxx;
						} else {
							$fields['station'] = substr($e164, 4 + strlen($cc));
						}
						$rs = $db->Execute( sqlSelect($db,"voip_pool","id","country_code=::".$cc.":: AND voip_did_plugin_id=::".$this->id.":: AND station=::".$fields['station']."::"));
						if ($rs->RecordCount() == 0) {
							$queue[] = sqlInsert($db,"voip_pool",$fields);
						}
					} else {
						$this->log_message('refresh', 'Could not parse the phone number returned: '.$v[0]);
					}
					if (isset($queue) && is_array($queue) && count($queue)) {
						if ($bDelete) {
							# kill db entries
							$sql = "DELETE FROM ".AGILE_DB_PREFIX."voip_pool WHERE 
								voip_did_plugin_id=".$this->id." AND (account_id IS NULL or account_id=0)
								AND country_code=".$eparts[0]."
								AND (date_reserved IS NULL or date_reserved=0)";
							$db->Execute($sql);
							$bDelete = false;
						}
						foreach ($queue as $q) {
							#echo $q."\n";
							$db->Execute($q);
						}
					}
					} # end valid result check from allocate
				} # end poolcount looper
			} # end foreach entries
		}
		return $bOk;
    }
    
    function log_message($method, $message) {
		$db =& DB();
		$id = sqlGenId($db, "log_error");
    	$q = "INSERT INTO ".AGILE_DB_PREFIX."log_error 
		SET 
		  id         = ". $db->qstr($id).", 
		  date_orig  = ". $db->qstr(time()).", 
		  account_id = ". @$db->qstr(SESS_ACCOUNT).", 
		  module     = ". $db->qstr('MAGRATHEA.php').", 
		  method     = ". $db->qstr($method).", 
		  message    = ". $db->qstr($message).", 
		  site_id    = ". @$db->qstr(DEFAULT_SITE); 
        $db->Execute($q);
    }    	
}
?>
