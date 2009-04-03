<?php

ini_set('memory_limit', '128M');
set_time_limit(0);

/** DIDX AgileVoice VoIP DID Pool Plugin 
* @example include_once(PATH_PLUGINS.'voip_did/'.$plugin_name.'.php');
* @example eval('$did_plugin = new plg_voip_did_'.$plugin_name.';');
* @example $did_plugin->id = $voip_did_plugin_id;
* @example $did_plugin->did = $did;
* @example $did_plugin->country = $country;
* @example $did_plugin->method();
*/
class plgn_voip_did_DIDX
{
	var $id;					// voip_did_plugin_id from database
	var $did;					// full E164 DID
	var $country;				// country calling code
	var $release_minutes;		// The configured release minutes for reserved DIDs
	var $plugin;				// The plugin name
	var $reserve=24;			// Number of hours reserved 
	var $name='DIDX';			// Plugin name
	var $avail_countries;		// Available countries array
	var $plugin_data;			// Plugin data array
	var $host;					// host to provision to
	var $user;					// didx username
	var $pass;					// didx password
	var $type;					// sip/iax
	var $country_area;			// array with available country/areas ( $country_area[0][country_id] and $country_area[0][area_code] )
	var $codes;					// array of return error messages from DIDx.org
	
	/** Get the plugin settings from the database */
	function config() {
		$db =& DB();
		$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","*","id = $this->id"));
		$this->release_minutes = $rs->fields['release_minutes'];
		$this->avail_countries = $rs->fields['avail_countries'];
		$this->plugin_data = unserialize($rs->fields['plugin_data']);  
		$this->user = $this->plugin_data['user'];
		$this->pass = $this->plugin_data['pass'];
		$this->type = $this->plugin_data['type'];
		$this->host = $this->plugin_data['host'];
		$this->country_area = $this->plugin_data['country_area'];
		
		$this->codes[-1] = 'User ID does not exist';
		$this->codes[-2] = 'Your Password is Incorrect';
		$this->codes[-3] = 'This DID Number is already Sold';
		$this->codes[-4] = 'This DID Number is already Reserved';
		$this->codes[-5] = 'DID Number doesn\'t exit';
		$this->codes[-6] = 'The Country Code does not exist';
	}
	
	/** 
	* Once a DID has been purchased and payment has been received from the customer, this
	* function then asks the DID provider to actually provision the DID to us.
	*
	* sub BuyDIDByNumber ($UserID,$Password,$DIDNumber,$SIPorIAX, $Flag)
	*/	
	function purchase() {
		$this->config();

    	# include the soap classes
    	include_once(PATH_INCLUDES."nusoap/lib/nusoap.php");

		$bOk = false;
		$client = new soapclient("http://didx.org/cgi-bin/WebBuyDIDServer.cgi", false);

		$err = $client->getError();
		if ($err) {
			global $C_debug;
			$C_debug->error('DIDX.php', 'purchase', 'Could not acquire information from DIDx.org');
		} else {
			$params = array(
				'UserID' 		=> $this->user,
				'Password' 		=> $this->pass,
				'DIDNumber' 	=> $this->did,
				'SIPorIAX' 		=> $this->did."@".$this->host,
				'Flag'			=> 1 /* SIP version */
			);
			$result = $client->call('BuyDIDByNumber', $params, 'http://didx.org/BuyDID');

			if (is_array($result)) {
				while ((list($k,$v)=each($result)) !== false) {
					if (is_array($v)) {
						if ($v[0] < 0) {
							# error occured, let's log it!
							$this->log_message('purchase', 'SOAP Response: '.$this->codes[$v[0]]);
						} else {
							$bOk = true;
						}
					}
				}
			} else {
				if ($result < 0) {
					# error occured, let's log it!
					$this->log_message('purchase', 'SOAP Response: '.$this->codes[$result]);
				} else {
					$bOk = true;
				}
			}
		}
		if ($bOk == false) {
			$this->log_message('purchase', $this->did.':SOAP Request for purchasing DID failed:'.$result);
			return false;
		}	
		require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		$plugin->account_id = $this->account_id;
	
		return $plugin->purchase($this->id, $this->did);
	}
	
	/** 
	 * Reserve a DID
	 *
	 * sub ReserveDIDByNumber ($UserID,$Password,$DIDNumber)
	 */
    function reserve() {
    	return true;
    	
		$this->config();

    	# include the soap classes
    	include_once(PATH_INCLUDES."nusoap/lib/nusoap.php");

		$bOk = false;
		$client = new soapclient("http://didx.org/cgi-bin/WebReserveDIDServer.cgi", false);

		$err = $client->getError();
		if ($err) {
			global $C_debug;
			$C_debug->error('DIDX.php', 'reserve', 'Could not acquire information from DIDx.org');
		} else {
			$params = array(
				'UserID' 		=> $this->user,
				'Password' 		=> $this->pass,
				'DIDNumber' 	=> $this->did
			);
			$result = $client->call('ReserveDIDByNumber', $params, 'http://didx.org/Reserve');
			if (is_array($result)) {
				while ((list($k,$v)=each($result)) !== false) {
					if (is_array($v)) {
						if ($v[0] < 0) {
							# error occured, let's log it!
							$this->log_message('reserve', 'SOAP Response: '.$this->codes[$v[0]]);
						} else {
							$bOk = true;
						}
					}
				}
			} else {
				if ($result < 0) {
					# error occured, let's log it!
					$this->log_message('reserve', 'SOAP Response: '.$this->codes[$result]);
				} else {
					$bOk = true;
				}
			}
		}		
		if ($bOk == false) {
			$this->log_message('reserve', $this->did.':SOAP Request for reserve DID failed:'.$result);
			return false;
		}	
    	require_once(PATH_MODULES."voip_did_plugin/voip_did_plugin.inc.php");
		$plugin = new voip_did_plugin;
		
		return $plugin->reserve($this->id, $this->did);
    }
    
    /** 
     * Release a reserved DID
     *
	 * sub ReleaseDID("584884","asdf5","44554645587")
     */
    function release() {
		$this->config();

    	# include the soap classes
    	include_once(PATH_INCLUDES."nusoap/lib/nusoap.php");

		$bOk = false;
		$client = new soapclient("http://didx.org/cgi-bin/WebReleaseDIDServer.cgi", false);

		$err = $client->getError();
		if ($err) {
			global $C_debug;
			$C_debug->error('DIDX.php', 'release', 'Could not acquire information from DIDx.org');
		} else {
			$params = array(
				'UserID' 		=> $this->user,
				'Password' 		=> $this->pass,
				'DIDNumber' 	=> $this->did
			);
			$result = $client->call('ReleaseDID', $params, 'http://didx.org/Release');

			if (is_array($result)) {
				while ((list($k,$v)=each($result)) !== false) {
					if (is_array($v)) {
						if ($v[0] < 0) {
							# error occured, let's log it!
							$this->log_message('release', 'SOAP Response: '.$this->codes[$v[0]]);
						} else {
							$bOk = true;
						}
					}
				}
			} else {
				if ($result < 0) {
					# error occured, let's log it!
					$this->log_message('release', 'SOAP Response: '.$this->codes[$result]);
				} else {
					$bOk = true;
				}
			}
		}		
		if ($bOk == false) {
			$this->log_message('release', $this->did.':SOAP Request for release DID failed:'.$result);
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
    	
    	# include the soap classes
    	include_once(PATH_INCLUDES."nusoap/lib/nusoap.php");
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$voip = new voip;
    	
    	$db =& DB();
		$client = new soapclient("http://didx.org/cgi-bin/WebGetListServer.cgi", false);

		$err = $client->getError();
		if ($err) {
			global $C_debug;
			$C_debug->error('DIDX.php', 'refresh', 'Could not acquire information from DIDx.org');
		} else {
			$entries = explode("\r\n", $this->country_area);
			foreach ($entries as $entry) {
				$eparts = explode(":", $entry);
				$areas = explode(",", $eparts[1]);
				$bDelete = true;
				foreach ($areas as $area) {
					$params = array(
						'UserID' 		=> $this->user,
						'Password' 		=> $this->pass,
						'CountryCode' 	=> $eparts[0],
						'AreaCode' 		=> $area
					);
					$result = $client->call('getAvailableDIDS', $params, 'http://didx.org/GetList');

					unset($queue);
					while (is_array($result) && (list($k,$v)=each($result)) !== false) {
						if (is_array($v)) {
							if ($v[0] < 0) {
								# error occured, let's log it!
								$this->log_message('refresh', 'SOAP Response: '.$this->codes[$v[0]]);
							} else {
								if ($eparts[0] == '1') {
									;
								} else {
									$v[0] = "011".$v[0];
								}
								# got a phone number! let's insert it into the pool
								$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
								if ($voip->e164($v[0], $e164, $cc, $npa, $nxx)) {
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
							}
						}
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
							$db->Execute($q);
						}
					}
				} # end foreach area
			} # end foreach entries
		}
    }
    
    function log_message($method, $message) {
		$db =& DB();
		$id = sqlGenId($db, "log_error");
    	$q = "INSERT INTO ".AGILE_DB_PREFIX."log_error 
		SET 
		  id         = ". $db->qstr($id).", 
		  date_orig  = ". $db->qstr(time()).", 
		  account_id = ". @$db->qstr(SESS_ACCOUNT).", 
		  module     = ". $db->qstr('DIDX.php').", 
		  method     = ". $db->qstr($method).", 
		  message    = ". $db->qstr($message).", 
		  site_id    = ". @$db->qstr(DEFAULT_SITE); 
        $db->Execute($q);
    }    	
}
?>
