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

class didArea
{
	var $cc;
	var $data;
	
	function didArea($cc = 1, $data = false)
	{
		$this->cc = $cc;
		$this->data = $data;
	}
	
	function determineArea($cc, $number)
	{	
		$db =& DB();	
		switch($cc) {
			case 1:
				/* usa - return the npa,nxx */
				return substr($number,0,6);
			default:
				$sql = "SELECT locName, npa FROM ".AGILE_DB_PREFIX."voip_npa_nxx 
					WHERE country_code=".$db->qstr($cc)." ORDER BY length(npa) desc";
				$rs = $db->Execute($sql);
				if($rs && $rs->RecordCount()) {
					while(!$rs->EOF) {
						if(strncmp($rs->fields['npa'],$number,strlen($rs->fields['npa']))==0) {
							return $rs->fields['npa'];
						}
						$rs->MoveNext();
					}
					return false;
				} else {
					return false;
				}
		}
	}
	
	function getName()
	{
		return $this->data['locName'];
	}
	
	function getState()
	{
		return $this->data['locState'];
	}
	
	function getAreacode()
	{
		return $this->data['areacode'];
	}
	
	function getNpa()
	{
		return $this->data['npa'];
	}
	
	function getNxx()
	{
		return $this->data['nxx'];
	}
	
	function getStations($plugins)
	{
		// $data['areacode'] or $data['npa']+$data['nxx']
		$p = AGILE_DB_PREFIX;
		$db =& DB();
		$pre = "";
		if($this->data['country_code'] == 1) {	
			$sql = "select distinct A.country_code,A.npa,A.nxx,A.station
				FROM {$p}voip_pool AS A
				left join {$p}voip_npa_nxx AS B
				on (A.npa=B.npa and A.nxx=B.nxx AND B.country_code='1') 
				WHERE (A.account_id IS NULL OR A.account_id = 0)
				AND (A.date_reserved IS NULL OR A.date_reserved = 0)	
				AND A.npa = " . $db->qstr($this->data['npa']) . "
				AND A.nxx = " . $db->qstr($this->data['nxx']) . "
				AND A.voip_did_plugin_id in (".join(",",$plugins).")
				AND A.site_id=".DEFAULT_SITE."  
				LIMIT 0,50";
		} elseif($this->data['country_code'] == 61) {
			$sql = "select distinct A.country_code,A.npa,A.nxx,A.station
				FROM {$p}voip_pool AS A
				left join {$p}voip_npa_nxx AS B
				on (A.npa=B.npa and A.nxx=B.nxx AND B.country_code='1')
				WHERE (A.account_id IS NULL OR A.account_id = 0)
				AND (A.date_reserved IS NULL OR A.date_reserved = 0)
				AND A.npa = " . $db->qstr($this->data['npa']) . "
				AND A.nxx = " . $db->qstr($this->data['nxx']) . "
				AND A.voip_did_plugin_id in (".join(",",$plugins).")
				AND A.site_id=".DEFAULT_SITE."
				LIMIT 0,50";
		} else {
			$sql = "select distinct A.country_code,A.areacode as npa,A.nxx,A.station
				FROM {$p}voip_pool AS A
				left join {$p}voip_npa_nxx AS B
				on (A.areacode=B.npa AND B.country_code=".$db->qstr($this->data['country_code']).") 
				WHERE (A.account_id IS NULL OR A.account_id = 0)
				AND (A.date_reserved IS NULL OR A.date_reserved = 0)	
				AND A.areacode = " . $db->qstr($this->data['areacode']) . "
				AND A.voip_did_plugin_id in (".join(",",$plugins).")
				AND A.site_id=".DEFAULT_SITE."  
				LIMIT 0,50";
			$pre = "011";
		}
		#echo "document.write('".str_replace("'","\\'",str_replace("\n","",$sql))."');"; return;
		$rs = $db->Execute($sql);
		if($rs && $rs->RecordCount()) {
			$i = 0;
			while(!$rs->EOF) {
				if ($rs->fields['country_code'] == '1') {
					$dids[$i][0] = $pre.$rs->fields['country_code'].$rs->fields['npa'].$rs->fields['nxx'].$rs->fields['station'];
					$dids[$i++][1] = $rs->fields['country_code']." ".$rs->fields['npa'].$rs->fields['nxx'].$rs->fields['station'];
				} elseif($rs->fields['country_code'] == '61') {
					$dids[$i][0] = $pre.$rs->fields['country_code'].$rs->fields['npa'].$rs->fields['nxx'].$rs->fields['station'];
					$dids[$i++][1] = $rs->fields['country_code']." ".$rs->fields['npa'].$rs->fields['nxx'].$rs->fields['station'];
				} else {
					$dids[$i][0] = $pre.$rs->fields['country_code'].$rs->fields['station'];
					$dids[$i++][1] = $rs->fields['country_code']." ".$rs->fields['station'];
				}
				$rs->MoveNext();
			}
			return $dids;
		}
		syslog(LOG_INFO,$db->ErrorMsg()." -> ".$sql);
		return false;
	}
}

class didAreas
{
	var $data, $cc;
	
	function didAreas($cc, $plugins = false)
	{
		$this->cc = $cc;
		$p = AGILE_DB_PREFIX;
		$db =& DB();
		if($cc==61) {
			$sql = "select distinct A.npa,A.nxx,B.locName 
				from {$p}voip_pool AS A 
				inner join {$p}voip_npa_nxx AS B 
				on (A.npa=B.npa and A.country_code=".$db->qstr($cc)." AND 
				B.country_code=".$db->qstr($cc).") 
				WHERE (A.account_id IS NULL OR A.account_id = 0) AND 
				(A.date_reserved IS NULL OR A.date_reserved = 0) AND ";
			if(is_array($plugins))
				$sql .= "A.voip_did_plugin_id in (".join(",",$plugins).") AND ";
			$sql .= "A.site_id=".DEFAULT_SITE." ORDER BY B.locName";
		}
		elseif($cc!=1) {
			$sql = "select distinct A.areacode,B.locName 
				from {$p}voip_pool AS A 
				inner join {$p}voip_npa_nxx AS B 
				on (A.areacode=B.npa and A.country_code=".$db->qstr($cc)." AND 
				B.country_code=".$db->qstr($cc).") 
				WHERE (A.account_id IS NULL OR A.account_id = 0) AND 
				(A.date_reserved IS NULL OR A.date_reserved = 0) AND ";
			if(is_array($plugins))
				$sql .= "A.voip_did_plugin_id in (".join(",",$plugins).") AND ";
			$sql .= "A.site_id=".DEFAULT_SITE." ORDER BY B.locName";
		} else {		
			$sql = "select distinct A.npa,A.nxx,B.locName,B.locState from {$p}voip_pool AS A inner join {$p}voip_npa_nxx AS B on
				(A.npa=B.npa and A.nxx=B.nxx and B.country_code='1')
				WHERE (A.account_id IS NULL OR A.account_id = 0) AND 
				(A.date_reserved IS NULL OR A.date_reserved = 0) AND ";	 		
			if(is_array($plugins))
				$sql .= "A.voip_did_plugin_id in (".join(",",$plugins).") AND ";
		 	$sql .= "A.site_id=".DEFAULT_SITE." ORDER BY B.locName";
		}
		# echo "document.write('".str_replace("'","\\'",str_replace("\n","",$sql))."');";	
		$rs = $db->Execute($sql);
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				$this->data[] = $rs->fields;
				$rs->MoveNext();
			}
		}
	}
	
	function getArea()
	{
		if(!is_array($this->data)) return false;
		$row = each($this->data);
		if($row === false)	{ reset($this->data); return false; }
		return new didArea($this->cc, $row[1]);
	}
}

class didCountry
{
	var $data;
	
	function didCountry($row)
	{
		$this->data = $row;
	}
	
	function getAreas($plugin = false)
	{
		return new didAreas($this->data['country_code'],$plugin);
	}
	
	function getCode()
	{
		return $this->data['country_code'];
	}
	
	function getName()
	{
		return $this->data['name'];
	}
}

class didCountries
{
	var $data;
	var $did_plugin_ids;
	
	function didCountries($pluginArray)
	{	
		$this->did_plugin_ids = $pluginArray;
		$p = AGILE_DB_PREFIX;
		$db =& DB();
		$sql = "select distinct a.country_code,c.id,c.code,c.name from {$p}voip_pool as b 
				left join {$p}voip_iso_country_code_map as a on (a.country_code=b.country_code) 
				left join {$p}voip_iso_country_code as c on (a.iso_country_code=c.code) 
				where (account_id IS NULL or account_id=0)
				and (b.date_reserved IS NULL or b.date_reserved = 0 )
				and c.site_id=".DEFAULT_SITE." AND b.site_id=".DEFAULT_SITE." and a.site_id=".DEFAULT_SITE;
		$rs = $db->Execute($sql);
		if($rs && $rs->RecordCount()) {	
			while(!$rs->EOF) {	
				$this->data[] = $rs->fields;
				$rs->MoveNext();	
			}
		}
		#echo "<pre>".print_r($this->data,true)."</pre>";
		reset($this->data);
	}
	
	function getCountry()
	{
		$row = each($this->data);
		if($row === false) { reset($this->data); return false; }
		return new didCountry($row[1]);
	}
}


class DateFunc {

	function day_of_week( $m, $d, $y ) {
		// Calculate the day of the week, with sat. starting it
		$r = date('w',mktime(0,0,0,$m,$d,$y));
		// As r stands above, sun=0 sat=6
		$r = $r + 1;
		if( $r > 6 )
		$r = 0;
		// now sun=6 sat=0
		return $r;
	}

	function dow( $m, $d, $y ) {
		$a = $this->day_of_week( $m, $d, $y );
		
		switch( $a ) {
		case 0:
			return "SA";
		case 1:
			return "SU";
		case 2:
			return "MO";
		case 3:
			return "TU";
		case 4:
			return "WE";
		case 5:
			return "TH";
		case 6:
			return "FR";
		}
	}

	function week_of_year( $m, $d, $y ) {
		// Calculate the week of the year, using Saturdays...
		$day = date('z',mktime(0,0,0,$m,$d,$y) ) + 1;
		$wday= $this->day_of_week($m,$d,$y) + 1;
		$week = 0;
		
		while( $day > 0 ) {
			$wday = $wday - 1;
			$day = $day - 1;
			if( $wday < 0 )	{
				$wday = $wday + 7;
				$week = $week + 1;
			}
		}
		return $week;
	}

	function get_range_begin( $m, $d, $y ) {
		// First valid date in the week (always a Sat)
		return mktime(0,0,0,$m,($d+(6-$this->day_of_week($m,$d,$y)))-6,$y);
	}
	
	function get_range_end( $m, $d, $y ) {
		// End valid date in a week (always a Fri)
		return mktime(0,0,0,$m,($d+(6-$this->day_of_week($m,$d,$y))),$y);
	}

	function getWeekArray() {
		$cwn = $this->week_of_year(date('m'),date('d'),date('Y'));
		$ts  = mktime(0,0,0,date('m'),date('d'),date('Y'));
		$n = 0;
		for( $i=$cwn; $n<16; $i-- ) {
			$j = $i;
			if( $j > 52 )
				$j = abs(53 - $j) + 1;
			else if( $j < 1 )
				$j = 53 + $j;
			$ret[$n]['text'] = "Week #$j " . date(UNIX_DATE_FORMAT,$this->get_range_begin(date('m',$ts),date('d',$ts),date('Y',$ts))) . " through " . date(UNIX_DATE_FORMAT,$this->get_range_end(date('m',$ts),date('d',$ts),date('Y',$ts)));
			$ret[$n]['begin'] = $this->get_range_begin(date('m',$ts),date('d',$ts),date('Y',$ts));
			$ret[$n]['end'] = $this->get_range_end(date('m',$ts),date('d',$ts),date('Y',$ts));
			$ret[$n]['week'] = $j;
			
			$n++;
			$ts = $ts - (86400 * 7);
		}
		return $ret;
	}

	function getNumberOfDays($date1, $date2) {
		# Correct parameters if needed.
		if($date2<$date1) {
			$tmp = $date1;
			$date1 = $date2;
			$date2 = $tmp;
		}
		# Get the year, month, day values of the two dates
		#$d1[0] = date('Y',$date1); #substr($date1,0,4);
		#$d1[1] = date('m',$date1); #substr($date1,4,2);
		#$d1[2] = date('d',$date1); #substr($date1,6,2);
		#$d2[0] = date('Y',$date2); #substr($date2,0,4);
		#$d2[1] = date('m',$date2); #substr($date2,4,2);
		#$d2[2] = date('d',$date2); #substr($date2,6,2);

		# Construct UNIX timestamps based on the original dates
		$date1 = mktime(0,0,0, date('m',$date1), date('d',$date1), date('Y',$date1));
		$date2 = mktime(0,0,0, date('m',$date2), date('d',$date2), date('Y',$date2));
		#print "date1=".date('Ymd',$date1)."<br>date2=".date('Ymd',$date2)."<br>"; exit;
		$tmp = $date1;
		$y = date('Y',$date1); 
		$m = date('m',$date1);
		$d = date('d',$date1);
		$days = 0;

		# Increments $tmp one day at a time until it matches $date2
		while ($tmp != $date2) {
			$d++;
			$tmp = mktime(0,0,0, date($m), date($d), date($y));
			$days++;
			#echo "tmp=$tmp date2=$date2 m=$m d=$d y=$y<br>";
			#if($d>30) break;
		} # while

		# Returns the number of increments for the dates to match
		return $days;
	}
}


class voip
{ 	
	var $voip_intrastate;
	var $voip_default_prefix;
	var $perform_normalization;
	var $normalization_min_len;
	 
	/**
	* Handle the click to call feature. This is complex as apache/php runs as wwwadmin, so this may need an additional layer
	* to allow asterisk to receive the call file as owner root.
	*/
	function place_call($VAR)
	{
		global $C_debug;
		
		# gimmie temp file
		$file = tempnam("/tmp","clicktocall-").".call";
		$C_debug->alert("Placing Call, please answer your phone when it rings and the call will then be completed.");
		
		$fp = fopen($file, "w");
		fwrite($fp,"Channel: ".$VAR['voip_from']."\n");
		fwrite($fp,"MaxRetries: 0\n");
		fwrite($fp,"RetryTime: 60\n");
		fwrite($fp,"WaitTime: 20\n");
		fwrite($fp,"Callerid: \"Click to Crash\" <".$VAR['voip_callerid'].">\n");
		fwrite($fp,"Context: international\n");
		fwrite($fp,"Extension: ".$VAR['voip_to']."\n");
		fwrite($fp,"Priority: 1\n");
		fclose($fp);
		chmod($file,0777);

		system("mv -f ".escapeshellarg($file)." /var/spool/asterisk/outgoing/");
	}

	/**
	* Given a country code and possible NPA/NXX, determine where in the world we're at.
	*/
	function where_is(&$db, $countrycode, $npa, $nxx)
	{
		if ($countrycode == 1) {
			# We are in the USA, use npa/nxx lookup
			if ($npa == "800" || $npa == "866" || $npa == "877" || $npa == "888")
				return "Toll-free";
			$rs =& $db->Execute("SELECT locName, locState FROM ".AGILE_DB_PREFIX."voip_npa_nxx WHERE country_code='1' and npa=".$db->qstr($npa)." and nxx=".$db->qstr($nxx));
			if ($rs && $rs->RecordCount())
			return $rs->fields[0].", ".$rs->fields[1];
			else
			return "Unknown";
		} else {
			/*
			mysql> describe ab_voip_iso_country_code_map;
			+------------------+-------------+------+-----+---------+-------+
			| Field            | Type        | Null | Key | Default | Extra |
			+------------------+-------------+------+-----+---------+-------+
			| id               | int(11)     |      | PRI | 0       |       |
			| country_code     | varchar(16) |      | MUL |         |       |
			| iso_country_code | char(3)     |      |     |         |       |
			| iso_sub_code     | char(3)     |      |     |         |       |
			+------------------+-------------+------+-----+---------+-------+
			4 rows in set (0.00 sec)
			
			mysql> describe ab_voip_iso_country_code;
			+-------+-------------+------+-----+---------+-------+
			| Field | Type        | Null | Key | Default | Extra |
			+-------+-------------+------+-----+---------+-------+
			| id    | int(11)     |      | PRI | 0       |       |
			| code  | char(3)     |      | UNI |         |       |
			| name  | varchar(64) |      |     |         |       |
			+-------+-------------+------+-----+---------+-------+
			3 rows in set (0.00 sec)
			*/
			$sql = "SELECT b.name FROM ".AGILE_DB_PREFIX."voip_iso_country_code_map a left join ".AGILE_DB_PREFIX."voip_iso_country_code b
			on (a.iso_country_code=b.code)
			WHERE a.country_code like ".$db->qstr($countrycode."%");
			$rs =& $db->Execute($sql);
			if ($rs && $rs->RecordCount())
			return $rs->fields[0];
			else return "Unknown";
		}
		return "Unknown";	
	}
	
	/** 
	* Given a DID, returns the e.164 representation and the country code with possible npa/nxx if USA. If successful,
	* a true is returned, otherwise false is returned.
	*
	* @param $number Input telephone number
	* @param $e164 Output cleaned number in E.164 format
	* @param $countrycode Output country code designator
	* @param $npa Output NPA code if USA country
	* @param $nxx Output NXX code if USA country
	*/
	function e164($number, &$e164, &$countrycode, &$npa, &$nxx)
	{
		if(function_exists('agileco_e164')) {
			if(($r = agileco_e164($number,$this->voip_default_prefix)) === false)
				return false;
			$e164 = $r['e164'];
			$countrycode = $r['country_code'];
			$npa = $r['npa'];
			$nxx = $r['nxx'];
			#echo "<pre>".print_r($r, true)."</pre>";
			return true;
		}	 
		$e164 = ""; $countrycode = ""; $npa = ""; $nxx = "";
		
		if (preg_match("/[a-zA-Z]/",$number)) return false;
		if (!strncmp($number, "+", 1)) {
			# if the number has a leading plus, strip it.
			$number = substr($number,1);
		}
		if (strlen($number) == 7) {
			# USA local dialing, need to prefix the country code and local npa
			$e164 = "+1".$this->voip_default_prefix.$number;
		}
		if (!strncmp($number, "0111", 4)) {
			# Screwed up USA call, strip the international prefixing
			$e164 = "+".substr($number, 3);
		}
		/* UK Specific hack */
		if (!strncmp($number, "44", 2)) {
			$e164 = "+011".$number;
		}
		if (!strncmp($number, "0", 1) && strlen($number) == 11) {
			$e164 = "+01144".$number;
		}
		/* End UK Specific hack */
		if (strlen($number) == 10 && strncmp($number,"011",3)) {
			# USA Call without the country code selection
			$e164 = "+1".$number;
		}
		/* Aus specific hack */
        if (!strncmp($number, "61", 2)) {
            $e164 = "+011" . $number;
            // print $e164;
            $npa = substr($e164, 6, 2);
            $nxx = substr($e164, 8, 4);
        }
        // aus interstate call
        if (!strncmp($number, "0", 1) && strlen($number) == 10) {
            $e164 = "+01161" . $number;
        }
		// aus toll free call
        if ((strncmp($number, "1300", 4) || strncmp($number, "1800", 4)) && strlen($number) == 10) {
            $e164 = "+01161" . $number;
        }
        /* End Aus specific hack */
		if ($e164 == "") {
			$e164 = "+".$number;
		}

		# ok, cleaned the number. Now, what's the country code?
		if (!strncmp($e164, "+011", 4)) {
			# international
			$countrycode = $this->parse_country_code($e164);
		} else {
			# USA
			$countrycode = "1";
			$npa = substr($e164, 2, 3);
			$nxx = substr($e164, 5, 3);
		}
		if (strlen($countrycode) && strlen($number))
			return true;
		return false;
	}
	
	
	/**
	* Parses the E.164 number for a correct country code. NOTE: Doesn't handle the USA right. Returns the country code.
	* Example: +011xxxxxxx
	*/
	function parse_country_code($e164)
	{
		if(function_exists('agileco_parse_country_code')) {
			#echo 'calling agileco_parse_country_code!<br>';
			return agileco_parse_country_code($e164);
		}	
    $numdigs = 2;
    $d1 = substr($e164, 4, 1);
    $d2 = substr($e164, 5, 1);
		switch ($d1) {
			case '1':
			case '7':
				$numdigs = 1;
				break;
			case '2':
				if ($d2 != '0' && $d2 != '7') {
					$numdigs = 3;
				}
				break;
			case '3':
				if ($d2 == '5' || $d2 == '7' || $d2 == '8') {
					$numdigs = 3;
				}
				break;
			case '4':
				if ($d2 == '2') {
					$numdigs = 3;
				}
				break;
			case '5':
				if ($d2 == '0' || $d2 == '9') {
					$numdigs = 3;
				}
				break;
			case '6':
				if ($d2 >= 7) {
					$numdigs = 3;
				}
				break;
			case '8':
				if ($d2 == '0' || $d2 == '3' || $d2 == '5' || $d2 == '7') {
					$numdigs = 3;
				}
				break;
			case '9':
				if ($d2 == '6' || $d2 == '7' || $d2 == '9') {
					$numdigs = 3;
				}
				break;
			default:
				$numdigs = 0;
				break;
		}
	
		if ($d2<0 || $d2>9) {
			$numdigs = 0;
		} else {
			if (strlen($e164) < $numdigs) {
				$numdigs = 0;
			}
		}
		if ($numdigs) {
			return substr($e164, 4, $numdigs);
		}
		return "";		
	}
	
	/**
	* Get the activity for the week needed
	*/
	function activity($VAR) {
		global $smarty;

		$fdids = $this->get_fax_dids(SESS_ACCOUNT);
		$dt = new DateFunc;
		if (empty($VAR['wnum']))
			$wnum = $dt->week_of_year(date('m'),date('d'),date('Y'));
		else
			$wnum = $VAR['wnum'];

		$smarty->assign('wnum',$wnum);
		$weeks = $dt->getWeekArray();
		$wtext[] = "-- Please Select --";
		foreach ($weeks as $w) {
			$wtext[$w['week']] = $w['text'];
			if ($w['week'] == $wnum) {
				$b = $w['begin'];
				$e = $w['end']; $e+=86400;
			}
		}
		
		$smarty->assign('weeks', $wtext);

		$db=&DB();
		$p=AGILE_DB_PREFIX;
		
		// get dids
		$sql_in='';
		$sql_out='';
		$dids = $this->get_all_dids(SESS_ACCOUNT);
		$dids = $this->normalize_dids($dids);
 
		if(count($dids)>0) {
			foreach($dids as $did) {
				if($sql_in!='') {
					$sql_in .= ' OR ';
					$sql_out .=  ' OR ';
				}
				$sql_in .= " dst = $did ";
				$sql_out .= " src = $did ";
				if(strncmp($did,"1",1) && strncmp($did,"0",1)) {
					$sql_in .= " OR dst = ".$db->qstr("1".$did)." ";
					$sql_out .= " OR src = ".$db->qstr("1".$did)." ";
				}
			}
		} else {
			$rs =& $db->Execute(sqlSelect($db,"account","username","id=".SESS_ACCOUNT));
			$sql_out = "accountcode=::".$rs->fields[0]."::";
		}		 			
		# gather prepaid account pins
		$pins = array();
		$rs =& $db->Execute(sqlSelect($db,"voip_prepaid","pin","account_id=".SESS_ACCOUNT));
		while ($rs && !$rs->EOF) {
			$pins[] = $rs->fields[0];
			$rs->MoveNext();
		}
		foreach ($pins as $pin) {
			if ($sql_out!='') {
				$sql_out .= ' OR ';
			}
			if ($sql_in!='') {
				$sql_in .= ' OR ';
			}
			$sql_out .= " accountcode=::cc:".$pin.":: ";
			$sql_in .= " dst = ::".$pin.":: ";
		}

		# Get currency
		$rs =& $db->Execute($sql=sqlSelect($db,"currency","symbol","id=".DEFAULT_CURRENCY));
		$smarty->assign('currency', $rs->fields[0]);
		
		# Get last 25 incoming:
		$rs =& $db->Execute(sqlSelect($db,"voip_cdr","date_orig, clid, src, dst, ceiling(duration/60) as duration, amount, lastapp"," ( $sql_in ) AND (lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') AND disposition='ANSWERED' AND account_id = ".SESS_ACCOUNT." AND date_orig>=$b AND date_orig<=$e","date_orig DESC"));
		if($rs && $rs->RecordCount() > 0) {
			$i = 0;
			while(!$rs->EOF) {
				$in[$i]=$rs->fields;
				if (strcasecmp($rs->fields['lastapp'], 'VoiceMail') == 0)
					$in[$i]['type'] = 'v';
				else if (in_array($rs->fields['dst'], $fdids))
					$in[$i]['type'] = 'f';
				$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
				if ($this->e164($rs->fields['src'], $e164, $cc, $npa, $nxx)) {
					$in[$i]['location'] = $this->where_is($db, $cc, $npa, $nxx);
				}										
				$i++;
				$rs->MoveNext();
			}
		} 
				
		# Get last 25 outgoing:
		$rs =& $db->Execute($sql = sqlSelect($db,"voip_cdr","date_orig, clid, dst, ceiling(duration/60) as duration, amount, lastapp"," ( $sql_out ) AND (lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') AND disposition='ANSWERED' AND account_id = ".SESS_ACCOUNT." AND date_orig>=$b AND date_orig<=$e","date_orig DESC"));
		if($rs && $rs->RecordCount() > 0) {
			$i = 0;
			while(!$rs->EOF) {
				$out[$i]=$rs->fields;
				if (strcasecmp($rs->fields['lastapp'], 'VoiceMail') == 0)
					$out[$i]['type'] = 'v';
				foreach ($pins as $pin) {
					if (strcmp($rs->fields['accountcode'], "cc:".$pin)==0) {
						$out[$i]['type'] = 'c';
					}
				}
				$cc = ""; $npa = ""; $nxx = ""; $e164 = "";					
				if ($this->e164($rs->fields['dst'], $e164, $cc, $npa, $nxx)) {
					$out[$i]['location'] = $this->where_is($db, $cc, $npa, $nxx);
				}										
				$i++;				
				$rs->MoveNext();
			}
		}	

		$smarty->assign('in', $in);
		$smarty->assign('out', $out); 		
	}

	function normalize_dids($dids)
	{
		foreach($dids as $did) {
			$out[] = $did;
			if(!strncmp($did,"011",3))
				$out[] = substr($did,3);
			if(!strncmp($did,"1",1))
				$out[] = substr($did,1);
		}
		return $out;
	}
	
	/**
	* Get the last 25 placed and received calls for the user
	* @todo Get some call statistics daily/weekly/monthly for the user
	*/
	function overview($VAR) {
		global $smarty;

		// validate logged in
		if(!SESS_LOGGED) return false;
		
 		$fdids = $this->get_fax_dids(SESS_ACCOUNT);
		// get dids
		$sql_in='';
		$sql_out='';
		$dids = $this->get_all_dids(SESS_ACCOUNT);
		$dids = $this->normalize_dids($dids);
		 
		$db=&DB();
		$p=AGILE_DB_PREFIX;		 
		if(count($dids)>0) {
			foreach($dids as $did) {
				if($sql_in!='') {
					$sql_in .= ' OR ';
					$sql_out .=  ' OR ';
				}
				$sql_in .= " dst = ".$db->qstr($did)." ";
				$sql_out .= " src = ".$db->qstr($did)." ";
				if(strncmp($did,"1",1) && strncmp($did,"0",1)) {
					$sql_in .= " OR dst = ".$db->qstr("1".$did)." ";
					$sql_out .= " OR src = ".$db->qstr("1".$did)." ";
				}
			}
		} else {
			$rs =& $db->Execute(sqlSelect($db,"account","username","id=".SESS_ACCOUNT));
			$sql_out = "accountcode=::".$rs->fields[0]."::";
		}		 			
		# gather prepaid account pins
		$pins = array();
		$rs =& $db->Execute(sqlSelect($db,"voip_prepaid","pin","account_id=".SESS_ACCOUNT));
		while ($rs && !$rs->EOF) {
			$pins[] = $rs->fields[0];
			$rs->MoveNext();
		}
		foreach ($pins as $pin) {
			if ($sql_out!='') {
				$sql_out .= ' OR ';
			}
			if ($sql_in!='') {
				$sql_in .= ' OR ';
			}
			$sql_out .= " accountcode=::cc:".$pin.":: ";
			$sql_in .= " dst = ::".$pin.":: ";
		}
				
		# Get last 25 incoming:
		$rs =& $db->Execute(sqlSelect($db,"voip_cdr","id, date_orig, clid, src, dst, ceiling(duration/60) as duration, lastapp"," ( $sql_in ) AND (lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') AND disposition='ANSWERED' AND account_id = ".SESS_ACCOUNT,"date_orig DESC LIMIT 25",25));
		if($rs && $rs->RecordCount() > 0) {
			$i = 0;
			while(!$rs->EOF) {
				$in[$i]=$rs->fields;
				if (strcasecmp($rs->fields['lastapp'], 'VoiceMail') == 0)
					$in[$i]['type'] = 'v';
				else if (in_array($rs->fields['dst'], $fdids))
					$in[$i]['type'] = 'f';
				$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
				if ($this->e164($rs->fields['src'], $e164, $cc, $npa, $nxx)) {
					$in[$i]['location'] = $this->where_is($db, $cc, $npa, $nxx);
				}					
				$i++;				
				$rs->MoveNext();
			}
		} 
		#echo "\n\n<!-- $sql -->\n\n";
		
		# Get last 25 outgoing:
		$rs =& $db->Execute(sqlSelect($db,"voip_cdr","id, accountcode, date_orig, clid, dst, ceiling(duration/60) as duration, lastapp"," ( $sql_out ) AND (lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') AND disposition='ANSWERED' AND account_id = ".SESS_ACCOUNT,"date_orig DESC LIMIT 25",25));
		if($rs && $rs->RecordCount() > 0) {
			$i = 0;
			while(!$rs->EOF) {
				$out[$i]=$rs->fields;
				if (strcasecmp($rs->fields['lastapp'], 'VoiceMail') == 0)
					$out[$i]['type'] = 'v';
				foreach ($pins as $pin) {
					if (strcmp($rs->fields['accountcode'], "cc:".$pin)==0) {
						$out[$i]['type'] = 'c';
					}
				}
				$cc = ""; $npa = ""; $nxx = ""; $e164 = "";					
				if ($this->e164($rs->fields['dst'], $e164, $cc, $npa, $nxx)) {
					$out[$i]['location'] = $this->where_is($db, $cc, $npa, $nxx);
				}										
				$i++;				
				$rs->MoveNext();
			}
		}	
 

		echo $sql; 
		
		$smarty->assign('in', $in);
		$smarty->assign('out', $out); 
	}
	
	 
	/**
	* Get user features for selected did
	*/
	function features($VAR) {
		
		// validate logged in
		if(!SESS_LOGGED) return false;

		global $smarty;
		 
		// get the selected did
		if(!empty($VAR['voip_did_id']) && is_numeric($VAR['voip_did_id'])) 
		{ 
			$smart=$this->get_auth_did($VAR['voip_did_id']);
			$smarty->assign('record',$smart);  
		} 
		else 
		{
			$dids = $this->get_all_dids(SESS_ACCOUNT);
			if(is_array($dids)) $dids = $dids[0];
			if(!$dids) return false;
			$smart=$this->get_auth_did($dids);
			$smarty->assign('record',$smart); 
		}	  
	}
	
	/**
	* verify that a specific did is validated for the current account
	*/
	function get_auth_did($did) {
		$db=&DB(); 
		if($did) $sql = "id = ::$did:: AND "; else $did =''; 
		$rs = & $db->Execute($sql=sqlSelect($db,"voip_did","*","$sql account_id = ".SESS_ACCOUNT));	 
		
		if($rs && $rs->RecordCount()>0) { 
			// get the voicemail email setting
			if(!empty($rs->fields['voicemailenabled'])) { 
		  		$vm = & $db->Execute($sql = sqlSelect($db,"voip_vm","email","mailbox = ::{$rs->fields['did']}:: AND account_id = ".SESS_ACCOUNT));	
		  		$rs->fields['vm_email'] = $vm->fields['email'];  
		  	}	 
		  	// is callwaiting enabled or disabled
		  	$callwaiting = 1;
		  	$cw =& $db->Execute($sql=sqlSelect($db,"voip_sip","data","keyword=::incominglimit:: and sip=::".$rs->fields['did']."::"));
		  	if(!empty($cw->fields['data'])) {
		  		if($cw->fields['data'] == 1) {
		  			$callwaiting = 0;
		  		}
		  	}
		  	$rs->fields['sip_callwaiting'] = $callwaiting;	 
		  	return $rs->fields;		 
		} else {
			return false;
		}
	}
	
	/**
	* Update user features for selected did
	*/	
	function update_features($VAR) {
		if(!SESS_LOGGED) return false;

		// get the selected did
		if(!empty($VAR['voip_did_id']) && is_numeric($VAR['voip_did_id'])) 
		{
			if($flds = $this->get_auth_did($VAR['voip_did_id']))
			{
				$fields['voicemailafter'] 			= @$VAR['voip_voicemailafter']; 
				$fields['callforwardingenabled'] 	= @$VAR['voip_callforwardingenabled'];
				$fields['cfringfor'] 				= @$VAR['voip_cfringfor'];
				$fields['cfnumber'] 				= @$VAR['voip_cfnumber'];
				$fields['busycallforwardingenabled']= @$VAR['voip_busycallforwardingenabled'];
				$fields['bcfnumber'] 				= @$VAR['voip_bcfnumber'];
				$fields['faxemail'] 				= @$VAR['voip_faxemail'];
				$fields['failovernumber'] 			= @$VAR['voip_failovernumber'];
				$fields['remotecallforwardnumber'] 	= @$VAR['voip_remotecallforwardnumber']; 
				$db=&DB();
		  		$rs = & $db->Execute($sql=sqlUpdate($db,"voip_did",$fields,"id = ::{$flds['id']}::"));
		  	  
		  		if(!empty($VAR['voip_vm_email']) && !empty($flds['voicemailenabled'])) {
		  			$fields2['email'] = $VAR['voip_vm_email'];
		  			$rs = & $db->Execute($sql = sqlUpdate($db,"voip_vm",$fields2,"mailbox = ::{$flds['did']}:: AND account_id = ".SESS_ACCOUNT)); 
		  		} 
		  		
		  		# update the call waiting setting
		  			if(!empty($VAR['sip_callwaiting'])) {
		  				# delete the incominglimit
		  				$sql = "DELETE FROM ".AGILE_DB_PREFIX."voip_sip WHERE sip=".$flds['did']." and keyword='incominglimit' and site_id=".DEFAULT_SITE;
		  				$db->Execute($sql);
		  			} else {
		  				$f['data'] = "1";
    					$f['keyword'] = "incominglimit";
    					$f['sip'] = $flds['did'];
    					$sql = sqlInsert($db, "voip_sip", $f);
    					$db->Execute($sql);
		  			}
			}
		}		
	}

	// get available parent service ids
	function menu_parent_service($VAR) {
 
		if(!empty($VAR['account_id'])) 
			$account_id = $VAR['account_id'];
		else
			$account_id = SESS_ACCOUNT;
			
		$db=&DB();
		$rs=&$db->Execute($sql=sqlSelect($db,array("voip_did","service"),"A.id,A.service_id,A.did","A.service_id = B.id AND (A.rxfax=0 or A.rxfax is null) AND (A.conf=0 or A.conf is null) AND (A.remotecallforward=0 or A.remotecallforward is null) AND B.active=1 AND B.account_id=".$account_id,false,false,"DISTINCT"));
		if($rs && $rs->RecordCount() > 0) {
			
			$return = '<select name="attr[parent_service_id]">';
			$return .= '<option value="" selected></option>'; 
			if($rs && $rs->RecordCount() > 0) 
			{
				while(!$rs->EOF) 
				{ 
					$return .= '<option value="' . $rs->fields['service_id'] . '">' . $rs->fields["did"] . '</option>';	
					$rs->MoveNext();			
				}
			}   
			$return .= '</select>';	
			echo $return;		
		} else {
			echo "No associated accounts found";
		}
	}

	
	// function 
	function get_did_plugin_countries($id, &$plugins) {
		$countries = false;
		$db=&DB();
		$rs = & $db->Execute($sql=sqlSelect($db,"product","prod_plugin_data","id = ::$id::"));
		 
		if($rs && $rs->RecordCount() > 0) { 
			@$plugin = unserialize($rs->fields['prod_plugin_data']);
			@$plugins = $plugin['voip_did_plugins'];
	  
			if(is_array($plugins) && count($plugins > 0)) {
				$sql = '';
				foreach($plugins as $key=>$plgid) {
					if($sql!='') $sql .= " OR ";
					$sql .= " id = $plgid ";
				}
				$rs = & $db->Execute(sqlSelect($db,"voip_did_plugin","avail_countries","( $sql )"));
				if($rs && $rs->RecordCount() > 0) {
					while(!$rs->EOF) {
						$carr = unserialize($rs->fields['avail_countries']);
						foreach($carr as $cid) $countries["$cid"] = $cid;
						$rs->MoveNext();
					}
				}
			} else {
				$plugins = array();
			}
		}
		return $countries;
	}
	
	// get available countries
	function menu_countries($VAR) {
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');

		if(!$did_plugins = $this->get_did_plugin_countries($VAR['id'],$plugins)) { 
			echo '-- No Countries --';
			return;
		} 

		$countries = new didCountries($plugins);
		$js = '<select id="voip_country" name="attr[country]" onChange="voipChangeCountry(this.value)">';
		$js .= '<option value="" selected>--- Pick a Country ---</option>';	 	
		while($c = $countries->getCountry()) {
			$js .= '<option value="' . $c->getCode() . '">' . $c->getName() . '</option>';
		}
		$js .= '</select>';
		echo $js;
	}
	
	 
	// 
	function menu_states($VAR) 
	{
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');		
		$did_plugins = $this->get_did_plugin_countries($VAR['id'],$plugins);

		$areas = new didAreas(1,$plugins);
		#echo "<pre>".print_r($areas,true)."</pre>";
		$areas_seen = array();
		$js = '<select id="voip_state" name="attr[state]" onChange="voipChangeState(this.value)">';
		$js .= '<option value="" selected>--- Pick a State ---</option>';
		while($area = $areas->getArea()) {		 
			#echo print_r($area,true);			
			if(!isset($areas_seen[$area->getState()])) {			
				$js .= '<option value="' . $area->getState() . '">' . $area->getState() . '</option>';
				$areas_seen[$area->getState()] = true;
			}
		}
		$js .= '</select>';
		echo $js;
	}
  
	// return location menu
	function menu_location($VAR) 
	{	
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate'); 
		$did_plugins = $this->get_did_plugin_countries($VAR['id'],$plugins);
		
		$cc = 1; 
		if(!empty($VAR['country'])) 
			$cc = $VAR['country'];
		#echo "alert('$cc');"; exit;
		$areas = new didAreas($cc,$plugins);
		$js = 'menuClearOptions("voip_location");';
		$js .= "menuAppendOption('voip_location', '', '-- Select A Location --');";
		$count = 0; 
		while($area = $areas->getArea()) {		
			if($cc==61) {
				$js .= "menuAppendOption('voip_location', '{$cc}:".$area->getNpa()."-".$area->getNxx()."', '".$area->getName()." (".$area->getNpa()."-".$area->getNxx().")');";
			}
			elseif($cc!=1) {
				$js .= "menuAppendOption('voip_location', '{$cc}:".$area->getAreacode()."', '".$area->getName()." (".$area->getAreacode().")');";
			} else if($area->data['locState']==$VAR['state']) {
				$js .= "menuAppendOption('voip_location', '".$area->getNpa()."-".$area->getNxx()."', '".$area->getName()." (".$area->getNpa()."-".$area->getNxx().")');";
			}
			$count++;
		}
		if($count)
			echo $js;
		else
			echo 'document.write("Sorry, none available at this time. Please check again later.");';
		return;
	}
	
	
	// return location menu
	function menu_station($VAR) 
	{  
		header('Pragma: no-cache');
		header('Cache-Control: no-cache, must-revalidate');
		$did_plugins = $this->get_did_plugin_countries($VAR['id'],$plugins);
		#echo "alert(\"".str_replace("\n","\\\n",str_replace("\"","\\\"",print_r($VAR,true)))."\");";return;
				
		#if(empty($VAR['location']) && empty($VAR['country'])) return false; 
		$l = $VAR['location'];
		if (strchr($l,':')) {
			$cn = explode(':', $l);
			$data['country_code'] = $cn[0];
			if($cn[0] == '61') {
				$cn = explode('-', $cn[1]);
				$data['npa'] = $cn[0];
				$data['nxx'] = $cn[1];
			} else {
				$data['areacode'] = $cn[1];
			}
		} else {
			$cn = explode('-', $l);
			$data['country_code'] = 1;
			$data['npa'] = $cn[0];
			$data['nxx'] = $cn[1];
		}
		$area = new didArea($data['country_code'], $data);
		#echo "alert(\"".str_replace("\n","\\\n",str_replace("\"","\\\"",print_r($area,true)))."\");"; return;
		
		$js = 'menuClearOptions("voip_station"); ';
		$js .= "menuAppendOption('voip_station', '', '-- Select A Station --'); "; 
		$dids = $area->getStations($plugins);
		#echo "alert(\"".str_replace("\n","\\\n",str_replace("\"","\\\"",print_r($dids,true)))."\");"; return;
	 	foreach($dids as $did) { 
	 		#if($data['country_code'] == 1) {
	 			$js .= "menuAppendOption('voip_station', '".$did[0]."', '{$did[1]}'); ";
	 		#} else {
	 		#	;
	 		#}
	 	}
	 	echo $js;
	 	return;
	 	
		$db=&DB();
		$p = AGILE_DB_PREFIX; 				
		if(!empty($VAR['location'])) 
		{
			
			$l = $VAR['location']; 
			if(eregi(':', $l)) 			// passed country_code:region
			{									
				$cn = explode(':', $l);
				$ccode = $cn[0];
				$npa   = $cn[1];
				
				$sql = "select distinct left(A.station,4) as npa,B.locName, station from {$p}voip_pool AS A 
				inner join {$p}voip_npa_nxx AS B on (left(A.station,4)=B.npa AND 
				A.country_code='{$ccode}' AND B.country_code='{$ccode}' AND B.npa='{$npa}') 
				WHERE (A.account_id IS NULL OR A.account_id = 0) AND 
				(A.date_reserved IS NULL OR A.date_reserved = 0) AND 
				A.voip_did_plugin_id in (".join(",",$plugins).") AND
				A.site_id=".DEFAULT_SITE." ORDER BY B.locName LIMIT 0,50";  
				// loop through results
				$rs = $db->Execute($sql);
				if($rs && $rs->RecordCount() > 0)  {	 
					while(!$rs->EOF) { 
						if(!empty($rs->fields["station"]))
						$js .= "menuAppendOption('voip_station', '011". $ccode . $rs->fields["station"]."', '{$ccode} {$rs->fields["station"]}'); ";
						$rs->MoveNext();
					} 
				}  else {
					$js = 'document.write("Sorry, none available at this time. Please check again later.");';
				} 						 				
			} else {
				$np=explode('-',$l);		// passed npa-nxx
				$npa=$np[0];
				$nxx=$np[1];			 
				$sql = "select distinct A.country_code,A.npa,A.nxx,A.station
						FROM {$p}voip_pool AS A
						left join {$p}voip_npa_nxx AS B
						on (A.npa=B.npa and A.nxx=B.nxx AND B.country_code='1') 
						WHERE (A.account_id IS NULL OR A.account_id = 0)
						AND (A.date_reserved IS NULL OR A.date_reserved = 0)	
						AND A.npa = " . $db->qstr($npa) . "
						AND A.nxx = " . $db->qstr($nxx) . "
						AND A.voip_did_plugin_id in (".join(",",$plugins).")
						AND A.site_id=".DEFAULT_SITE."  
						LIMIT 0,50";  
	 
				// loop through results
				$rs = $db->Execute($sql);
				if($rs && $rs->RecordCount() > 0)  {	 
					while(!$rs->EOF) { 
						if(!empty($rs->fields["station"]))
						$js .= "menuAppendOption('voip_station', '". $rs->fields["country_code"].$rs->fields["npa"].$rs->fields["nxx"].$rs->fields['station'] ."', '{$rs->fields["npa"]}-{$rs->fields["nxx"]}-{$rs->fields["station"]}'); ";
						$rs->MoveNext();
					} 
				}  else {
					$js = 'document.write("Sorry, none available at this time. Please check again later.");';
				}  	
			}
		} 
		else 
		{
			$country = $VAR['country'];
			$sql = "SELECT DISTINCT B.country_code, B.station from {$p}voip_pool AS B 
					LEFT JOIN {$p}voip_iso_country_code_map AS A  ON (B.country_code=A.country_code) 
					WHERE ( account_id IS NULL or account_id = 0) 
					AND ( B.date_reserved IS NULL or B.date_reserved = 0 )
					AND A.iso_country_code=".$db->qstr($country)."
					AND B.voip_did_plugin_id in (".join(",",$plugins).")
					and A.site_id=".DEFAULT_SITE." AND B.site_id=".DEFAULT_SITE." 
					LIMIT 0,50";	
			 
			$rs = $db->Execute($sql);
			if($rs && $rs->RecordCount() > 0) {	 
				while(!$rs->EOF) { 
					if(!empty($rs->fields["station"]))
					$js .= " menuAppendOption('voip_station', '011". $rs->fields["country_code"] . $rs->fields["station"] ."', '{$rs->fields["country_code"]}{$rs->fields["station"]}'); ";
					$rs->MoveNext();
				} 
			} else {
				$js = 'document.write("Sorry, none available at this time. Please check again later.");';
			}				
		}
		  
		echo $js; 	
		ob_end_flush();
		return true; 
	}

	/** Returns the fields from voip_pool for a given DID entry.
	*/
	function get_did_e164($did)
	{
		$db =& DB();

 		$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
		if ($this->e164($did, $e164, $cc, $npa, $nxx)) {
			if ($cc == '1') {
				$station = substr($e164, 8);
				$where = "country_code=1 and npa=::$npa:: and nxx=::$nxx:: and station=::$station::";
			} elseif ($cc == '61') {
				$station = substr($e164, 12);
				$where = "country_code=61 and npa=::$npa:: and nxx=::$nxx:: and station=::$station::";
			} else {
				$station = substr($e164, 4 + strlen($cc));
				$where = "country_code=::$cc:: and station=::$station::";
			}
			$rs = $db->Execute(sqlSelect($db, "voip_pool", "*", $where));
			if (!$rs)	return false;
			return $rs->fields;
		}
		return false;	
	}
	
	/** Save the configuration.
	 */
	function config($VAR)
	{
		global $C_debug;
		$db = & DB();
		 
		# define the validation class
		include_once(PATH_CORE . 'validate.inc.php');
		$validate = new CORE_validate;		
		$arr['min_len'] = 4;
		$arr['max_len'] = 4;
		
		if(is_numeric($VAR['voip_vm_passwd']) && !empty($VAR['voip_intrastate']))
		{
			$fields['voip_vm_passwd'] = $VAR['voip_vm_passwd'];
			$fields['voip_intrastate'] = $VAR['voip_intrastate'];
			$fields['voip_secret_gen'] = $VAR['voip_secret_gen'];
			$fields['voip_default_prefix'] = $VAR['voip_default_prefix'];
			$fields['prepaid_low_balance'] = $VAR['prepaid_low_balance'];
			$fields['auth_domain'] = $VAR['auth_domain'];
			$fields['perform_normalization'] = $VAR['perform_normalization'];
			$fields['normalization_min_len'] = $VAR['normalization_min_len'];
			$rs = $db->Execute( sqlSelect($db, "voip", "id", "site_id=::".DEFAULT_SITE."::") );
			if ($rs && !$rs->EOF) {
				$db->Execute( sqlUpdate($db, "voip", $fields, "site_id=::".DEFAULT_SITE."::") );
			} else {
				$db->Execute( sqlInsert($db, "voip", $fields) );
			}			
			$C_debug->alert("Saved!");
		} else {
			$C_debug->alert("Problems while saving:".$db->ErrorMsg());
		}  
	}

	/** Load the configuration variables into smarty
	 */
	function config_get($VAR)
	{
		global $smarty;
		$db = & DB(); 
		$sql = sqlSelect($db, "voip", "*", ""); 
		$rs = $db->Execute($sql);
		$smarty->assign('config',$rs->fields);
	}

	/** Return all available DIDs the customer owns.
	 * @param $account_id: The account to return dids for
	 */
	function get_all_dids($account_id)
	{
		return $this->get_all_dids_internal($account_id, 0);
	}
	
	/** Return all DIDs with voice mail active.
	 * @param $account_id: The account to return dids for
	 */
	function get_voicemail_dids($account_id)
	{
		return $this->get_all_dids_internal($account_id, 1);
	}
	
	/** Return all DIDs with fax active.
	 * @param $account_id: The account to return dids for
	 */
	function get_fax_dids($account_id)
	{
		return $this->get_all_dids_internal($account_id, 2);
	}
	
	/** Internal function used to gather an accounts available DIDs.
	 *  @param $VAR The AB passed array
	 *  @param $filter A flag to specify the type of DIDs to return. 0=ALL, 1=VM, 2=FAX, 3=CONFERENCE
	 */
	function get_all_dids_internal($account_id, $filter)
	{
		$db = & DB();
		$rs = & $db->Execute($sql=sqlSelect($db,"voip_did","did,voicemailenabled,rxfax,conf","active=1 AND account_id = ::$account_id::")); 
		#echo $sql;
		$dids = array(); 
		if ($rs && $rs->RecordCount() > 0) {
			while (!$rs->EOF) { 
				$did = $rs->fields['did']; 
				switch ($filter) {
				case 0:										//ALL
					array_push($dids, $did);
					break;
				case 1:
					if ($rs->fields['voicemailenabled']) {	//VM
						array_push($dids, $did);
					}
					break;
				case 2:
					if ($rs->fields['rxfax']) {				//FAX
						array_push($dids, $did);
					}
					break;
				case 3:
					if ($rs->fields['conf']) {				//CONF
						array_push($dids, $did);
					}
					break;
				default:
		        	global $C_debug;
		        	$C_debug->error('voip.inc.php','get_all_dids_internal','Invalid filter passed: '.$filter);
				}	
				$rs->MoveNext();
			}
		}
		return $dids;
	}

	function normalize(&$db)
	{
		$count = 0;
		$sql = sqlSelect($db, "voip_cdr", "src, dst, id", "(rated is null or rated=0)");
		#echo $sql."<BR>";
		$rs = $db->Execute($sql);
		while (!$rs->EOF) {
			$src = $rs->fields['src'];
			$dst = $rs->fields['dst'];

            $e164 = ""; $cc = ""; $npa = ""; $nxx = "";
            if (strlen($src)>=$this->normalization_min_len && $this->e164($src, $e164, $cc, $npa, $nxx)) {
                if ($cc == 61) {
                    $src = substr($e164, 6);
                } else {
                    $src = substr($e164,1);
                }
            }
            $e164 = ""; $cc = ""; $npa = ""; $nxx = "";
            if (strlen($dst)>=$this->normalization_min_len && $this->e164($dst, $e164, $cc, $npa, $nxx)) {
                if ($cc == 61) {
                    $dst = substr($e164, 6);
                } else {
                    $dst = substr($e164,1);
                }
            }
			#echo "src=".$rs->fields['src']." dst=".$rs->fields['dst']."<br>";
			#echo "esrc=".$src." edst=".$dst."<br><br>";
			#$f = array('src' => $src, 'dst' => $dst, 'rated' => '2');
			#$sql = sqlUpdate($db,"voip_cdr",$f,"id=::".$rs->fields['id']);
			$sql = "UPDATE ".AGILE_DB_PREFIX."voip_cdr SET 
				src=".$db->qstr($src).", dst=".$db->qstr($dst).", rated=2
				WHERE id=".$db->qstr($rs->fields['id']);
			#echo $sql."<br>";
			$db->Execute($sql);
			$count++;
			$rs->MoveNext();
		}
		echo "Normalized $count records...\n";
		
	}
		
	// Call as task - voip:task
	function task($VAR)
	{
		if(function_exists('agileco_parse_country_code')) {
			$this->c_task($VAR);
			return;
		}
		
		global $rate;
		$rate = array();
		$db = &DB(); 
		$rs = & $db->Execute( sqlSelect($db, "product", "id,prod_plugin_data", "prod_plugin_file=::VOIP:: and prod_plugin=1"));
		while (!$rs->EOF) {
			$pdata = unserialize($rs->fields['prod_plugin_data']);
			$id = $rs->fields['id'];
			if ($pdata['rate_cdr'] == 1) {
				$products[] = $id;
			}
			$rs->MoveNext();
		}
		
		// no products to rate
		if(empty($products)) return false;

		# Load configuration
		$sql = sqlSelect($db, "voip", "voip_intrastate, voip_default_prefix, perform_normalization, normalization_min_len", "");
		$rs = $db->Execute($sql);
		$this->voip_intrastate = explode(",",ereg_replace("[[:space:]]","",$rs->fields['voip_intrastate']));
		$this->voip_default_prefix = $rs->fields['voip_default_prefix'];
		$this->normalization_min_len = $rs->fields['normalization_min_len'];
		$this->perform_normalization = $rs->fields['perform_normalization'];
		
		ob_start();
		
		# normalize the CDR records
		echo "Begin normalization...\n";
		if($this->perform_normalization) {
			$this->normalize($db);
		}
//		print 'here!';
		echo "Finished normalization...\n";

		# rate prepaid cards, non-SIP prepaid
		$rs =& $db->Execute(sqlSelect($db,"voip_prepaid","pin, account_id, product_id, voip_did_id","(voip_did_id=0 or voip_did_id is null)"));
		if ($rs && $rs->RecordCount() > 0) {
			while (!$rs->EOF) {
				$dp = 0;
				unset($dids);
				$dids[$dp]['start'] = 0;
				$dids[$dp]['end'] = mktime(0,0,0,date('m')+1,1,date('Y'));
				$dids[$dp]['accountcode'] = "cc:".$rs->fields['pin'];
				echo "Rating calling card PIN: ".$rs->fields['pin']."\n";
				# Load rating table configuration
				$rate = $this->load_rating_table($db, $rs->fields['product_id']);				
				$this->rate_calls($db, $db, $dids, $rs->fields, false);

				# Mark inbound calls
				if ($rs->fields['voip_did_id'] > 0) {
					$sql = "update ".AGILE_DB_PREFIX."voip_cdr SET amount=0, rated=1, account_id=".$db->qstr($rs->fields['account_id'])." where dst=".$db->qstr($rs->fields['pin'])." and rated=0 and site_id=".DEFAULT_SITE;
					echo $sql."\n";
					$db->Execute($sql);
				}
				$rs->MoveNext();
			}
		}

		echo "Begin SIP Prepaid rating...\n";
		$sql = "select account_id, username, prod_attr_cart, prod_plugin_data, date_last_invoice, date_next_invoice, b.product_id, b.id as service_id from ".AGILE_DB_PREFIX."account as a left join ".AGILE_DB_PREFIX."service as b on (a.id=b.account_id) where a.status=1 and prod_plugin_name='PREPAID' and b.active=1 and a.site_id=".DEFAULT_SITE." and b.site_id=".DEFAULT_SITE;
		echo $sql."\n";
		$rs =& $db->Execute($sql);
		if ($rs && $rs->RecordCount() > 0) {
			while (!$rs->EOF) {
						$dp = 0;
						unset($dids);
						$cart = @unserialize($rs->fields['prod_attr_cart']);
						$plugin = unserialize($rs->fields['prod_plugin_data']);
						if (isset($cart['station']) && isset($plugin['type']) && $plugin['type'] == 'did') {
							$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
							$dids[$dp]['end'] = $rs->fields['date_next_invoice'] + 86399;
							$dids[$dp]['did'] = $cart['station'];
							# Load rating table configuration
							$rate = $this->load_rating_table($db, $rs->fields['product_id']);
							if (is_array($rate)) {
								$this->rate_calls($db, $db, $dids, $rs->fields);
							}
						}
				$rs->MoveNext();
			}
		}

		echo "Begin postpaid rating...\n";
		# rate calls
		$sql = "select account_id, username, prod_attr_cart, prod_plugin_data, date_last_invoice, date_next_invoice, b.product_id, b.id as service_id, b.sku from ".AGILE_DB_PREFIX."account as a left join ".AGILE_DB_PREFIX."service as b on (a.id=b.account_id) where a.status=1 and prod_plugin_name='VOIP' and b.active=1 and product_id IN (".join(",",$products).") and a.site_id=".DEFAULT_SITE." and b.site_id=".DEFAULT_SITE;
		echo $sql."\n";
		$rs = $db->Execute($sql);
		$dp = 0;
		while (!$rs->EOF) {
			$dp = 0; unset($dids);
			$cart = @unserialize($rs->fields['prod_attr_cart']);
			$plugin = unserialize($rs->fields['prod_plugin_data']);

			$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
			$dids[$dp]['end']   = $rs->fields['date_next_invoice'];
			
			echo "Last invoice date: {$rs->fields['date_last_invoice']} \n";
			echo "Next invoice date: {$rs->fields['date_next_invoice']} \n";

			$dids[$dp]['did'] = @$cart['station'];
			if (strlen(@$cart['ported']))
				$dids[0]['did'] = $cart['ported'];
			$cc = ""; $e164 = ""; $npa = ""; $nxx = "";
			if ((!strlen($dids[0]['did']) && $plugin['rate_accountcode'] == 0)) {
				echo "Skipping service_id = ".$rs->fields['service_id']." (sku: ".$rs->fields['sku'].")\n";
			} else {
				if ($this->e164($dids[0]['did'],$e164,$cc,$npa,$nxx)) {
					$dids[0]['did'] = substr($e164,1);
					if ($cc == '1') {
						$dp++;
						$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
						$dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
						$dids[$dp]['did']   = substr($e164,2);
						$dp++;
						$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
						$dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
						$dids[$dp]['did']   = substr($e164,1);
                    /* begin aus number hack */
                    } elseif ($cc == '61') {
                        $dp++;
                        $dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
                        $dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
                        $dids[$dp]['did']   = substr($e164,6);
						var_dump($dids[$dp]);
                    /* end aus number hack */
					} else {
						$dp++;
						$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
						$dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
						$dids[$dp]['did']   = substr($e164,4);
					}
					
				}
				if (@$cart['parent_service_id'] > 0) {
					# echo "This is a virtual number, skipping record...";
					;
				} else {		
					# load virtual numbers on this parent service
					$sql = "select * from ".AGILE_DB_PREFIX."service where account_id=".$db->qstr($rs->fields['account_id'])." and active=1 and prod_plugin_name='VOIP' and site_id=".DEFAULT_SITE;
					echo $sql."\n";
					$rs1 = $db->Execute($sql); $i = 1;
					if ($rs1) {
						while (!$rs1->EOF) {
							$carttmp = @unserialize($rs1->fields['prod_attr_cart']);
							if (@$carttmp['parent_service_id'] == $rs->fields['service_id']) {
								# is this an actual virtual line?
								$ppd = unserialize($rs1->fields['prod_plugin_data']);
								if ($ppd['parent_enabled'] && $ppd['virtual_number']) {
									$dp++;
									$dids[$dp]['start'] = $rs1->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
									$dids[$dp]['end']   = $rs1->fields['date_next_invoice'] + 86399;
									$dids[$dp]['did'] = @$carttmp['station'];
									if (strlen($carttmp['ported']))
										$dids[$dp]['did'] = $carttmp['ported'];
									$cc = ""; $e164 = ""; $npa = ""; $nxx = "";
									if ($this->e164($dids[$dp]['did'],$e164,$cc,$npa,$nxx)) {
										$dids[$dp]['did'] = substr($e164,1);
										if ($cc == '1') {
											$dp++;
											$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
											$dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
											$dids[$dp]['did'] = substr($e164,2);
											$dp++;
											$dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
											$dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
											$dids[$dp]['did'] = substr($e164,1);
                                        /* begin aus number hack */
										} elseif ($cc == '61') {
                                            $dp++;
                                            $dids[$dp]['start'] = $rs->fields['date_last_invoice'] - (MAX_INV_GEN_PERIOD*86400);
                                            $dids[$dp]['end']   = $rs->fields['date_next_invoice'] + 86399;
                                            $dids[$dp]['did'] = substr($e164,6);
										}
                                        /* end aus number hack */
									}
									echo "Found virtual number: ".$dids[$dp]['did']."\n";
								} # end test to see if truely virtual
							}
							$rs1->MoveNext();
						}
					}
	
					# Load rating table configuration
					$rate = $this->load_rating_table($db, $rs->fields['product_id']);
					if (is_array($rate)) {
						if ($plugin['rate_accountcode']) {			
							# rate accountcode based
							# echo "Rate by account code: ".$rs->fields['username']."\n";
							$dids[$dp]['accountcode'] = $rs->fields['username'];
							$this->rate_calls($db, $db, $dids, $rs->fields);
						} else {			
							# rate non-accountcode based
							$this->rate_calls($db, $db, $dids, $rs->fields);
						}	
					}
				}
			} # end did length check
			$rs->MoveNext();
		}
		$debug = ob_get_contents();
		ob_end_clean();

		// if (defined('RATING_DEBUG')) {
		// mail("sluther@bitpiston.com","Rating Debug For c2a.com.au",$debug);
		// mail("sluther@bitpiston.com","Test!","Test!");
		// ----

		// ----
		// }
		
		return true;
	}	
	
	/** Returns the call type of two DIDs. Returns: 1=INTRASTATE,2=INTERSTATE,3=TOLL-FREE
	 *  @param $src Source telephone number
	 *  @param $dst Destination telephone number
	 */
	function isIntrastateCall($src,$dst) 
	{
		# This is OHIOs NPAs. We need to know the location of us and we can load these values from
		# the beastly add-on table we have.
		#
		# Can we assume the business state is the location of this stuff?
		#
	    $ohio = $this->voip_intrastate;
	    if (strncmp("1800",$dst,4)==0 || strncmp("1877",$dst,4)==0 || strncmp("1866",$dst,4)==0 || strncmp("1888",$dst,4)==0)
	        return 3;
	    $s = 0; $d = 0;
	    foreach ($ohio as $p) {
	        if (strncmp("1".$p,$src,4)==0)
	            $s = 1;
	        if (strncmp("1".$p,$dst,4)==0)
	            $d = 1;
	    }
	    if($s == 1 && $d == 1)  return 1;
	    return 2;
	}

	/** Loads the rate table associated with a product.
	 *  @param $db A database connection handle
	 *  @param $product_id The ID of the product to load.
	 */
	function load_rating_table(&$db, $product_id)
	{
		global $rate;

		$sql = "SELECT b.id, b.connect_fee, b.increment_seconds, b.amount, b.pattern, b.type, b.direction, b.min, b.max, b.combine, b.percall as perCall, b.seconds_included, b.name FROM ".AGILE_DB_PREFIX."voip_rate_prod a inner join ".AGILE_DB_PREFIX."voip_rate b on (a.voip_rate_id=b.id and a.site_id=".DEFAULT_SITE.") WHERE product_id=".$product_id." ORDER BY type, direction DESC, length(pattern) DESC";
		echo $sql."\n";
		$rs = $db->Execute($sql); 
		unset($rate); 
		$i = 0;
		while (!$rs->EOF) {
			$rate[$i] = $rs->fields;
			$rate[$i]['pattern'] = str_replace("\r","",str_replace("\n","",ereg_replace("[^0-9;]","",$rs->fields['pattern'])));
			$rate[$i]['perCall'] = intval($rs->fields['perCall']);
			switch ($rs->fields['type']) {
			case 0:
				$rate[$i]['type'] = 'innetwork';
				break;
			case 1:
				$rate[$i]['type'] = 'local';
				break;
			case 2:
				$rate[$i]['type'] = 'regular';
				break;
			case 3:
				$rate[$i]['type'] = 'default';
				break;
			default:
				break;
			}
			switch ($rs->fields['direction']) {
			case 0:
				$rate[$i]['direction'] = 'inbound';
				break;
			case 1:
				$rate[$i]['direction'] = 'outbound';
				break;
			case 2:
				$rate[$i]['direction'] = 'both';
				break;
			default:
				break;
			}
			$i++;
			$rs->MoveNext();
		}
		if ($i == 0) {
        	#global $C_debug;
        	#$C_debug->error('voip.inc.php','load_rating_table','Rate table is empty for product_id = '.$product_id);			
			echo "Rating table is blank!\n\n";
		}
		return (isset($rate) ? $rate : false);
	}

	/** Returns boolean if the DID S is in the array of DIDs DIDS.
	 */
	function in_did_array($s, &$dids)
	{
		$ret = false;
		foreach ($dids as $d) {
			if (isset($d['did'])) {
				if ($d['did'] == $s) {
					$ret = true;
					break;
				}
			}
		}
		return $ret;
	}

	function price_call(&$dbast, $r, $dur, $callSQL, &$unit, &$quan)
	{
		$billedDur = (($dur - $r['seconds_included']) / $r['increment_seconds']);
		if ($billedDur < 0)
			$billedDur = 0;
		$billedDur = ceil($billedDur);
		$billedDur = ($billedDur * $r['increment_seconds'])/60;
		$cost = 0;
		$quan = $billedDur;
	
		# get count for min/max, honor the combine flag!
		$count = 0;
		if ($r['combine']) {
			$sql = "select sum(adjbillinterval) from ".AGILE_DB_PREFIX."voip_cdr where voip_rate_id=".$r['id']." and $callSQL";
			#echo $sql."\n";
			$rst = $dbast->Execute($sql);
			$count = intval($rst->fields[0]);
		}

		echo "count=$count, rmin=".$r['min'].", rmax=".$r['max']."\n";	
		if ($count >= $r['min'] && 
			($count <= $r['max'] || $r['max'] == -1)) {
			echo "perCall = ".$r['perCall']."\n";
			if ($r['perCall']) {
				$cost = $r['amount'];
				$quan = 1;
				$unit = $r['amount'];
			} else {
				$cost = ($r['amount'] * $billedDur);
				$quan = $billedDur;
				$unit = $r['amount'];
				echo "billedDur=".$billedDur."\n";
			}
		}
		print "cost=$cost, quan=$quan, unit=$unit\n";	
		return $cost;
	}

	/** Determines if DST is in SRC's local calling area.
	 */
	function is_local_calling_area(&$dbast, $src, $dst)
	{
		$cc1 = ""; $npa1 = ""; $nxx1 = "";
		$cc2 = ""; $npa2 = ""; $nxx2 = "";
		$e164 = "";
		if ($this->e164($src,$e164,$cc1,$npa1,$nxx1)) {
			if ($this->e164($dst,$e164,$cc2,$npa2,$nxx2)) {
				$sql = "select t1.grouping as id1, t2.grouping from ".AGILE_DB_PREFIX."voip_local_lookup as t1 join ".AGILE_DB_PREFIX."voip_local_lookup as t2 on (t1.npa='$npa1' and t1.nxx='$nxx1' and t2.npa='$npa2' and t2.nxx='$nxx2') where t1.grouping=t2.grouping";
				echo $sql."\n";
				$rs = $dbast->Execute($sql);
				if ($rs->fields[0] > 0) {
					return true;
				}
			}
		}
		return false;
	}

	function rate_calls(&$db, &$dbast, $dids, $crow, $postCharges = true)
	{
		global $rate;
		$quan = 0;
		$bAccountcode = false;
		
		# probably should have the e.164 values here too.
		$sql = "(";
		foreach($dids as $d) {
			if (strlen(@$d['accountcode'])) {
				$sql .= "(accountcode=".$db->qstr($d['accountcode'])." and date_orig>='".$d['start']."' and date_orig<='".$d['end']."') OR ";
				$bAccountcode = true;
			} else {
				$sql .= "((src='".$d['did']."' or dst='".$d['did']."') and date_orig>='".$d['start']."' and date_orig<='".$d['end']."') OR ";
			}
		}

		$sql = substr($sql,0,strlen($sql)-3).")";
		$callSQL = $sql;
		$sql = "select * from ".AGILE_DB_PREFIX."voip_cdr where $sql and
			(lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') 
			AND disposition='ANSWERED' and (rated=2)";
		echo $sql."\n";
		$rs1 = $dbast->Execute($sql);
		#print_r($rate);
		while ($rs1 && !$rs1->EOF) {
			
			$calltype = 0;
			if(is_array($this->voip_intrastate))
				$calltype = $this->isIntrastateCall($rs1->fields['src'], $rs1->fields['dst']);
			$slotMatched = 0; $unit = 0; $quan = 0; $amount = 0;
			$isInbound = $this->in_did_array($rs1->fields['dst'], $dids);
			if ($isInbound)
				$calltype = 4;
				
			$slotMatched = 0; $unit = 0; $quan = 0;
			foreach ($rate as $r) {
				if ($r['type'] == 'innetwork') {
					#echo "Is the call In Network?\n";
					if ($r['direction'] == 'inbound') {
						$search = $rs1->fields['src'];
					} else if ($r['direction'] == 'outbound') {
						$search = $rs1->fields['dst'];
					} else {
						$search = $rs1->fields['src']."','".$rs1->fields['dst'];
					}
					$sql = "select count(*) from ".AGILE_DB_PREFIX."voip_in_network where did in ('".$search."')";
					echo $sql."\n";
					$rs2 = $dbast->Execute($sql);
					if ($rs2->fields[0] > 0) {
						echo "Yes, call is in-network.\n";
						$amount = 0;
						$slotMatched = $r['id'];
						#break;
					}
				} else if ($r['type'] == 'local') {
					echo "Local calling area\n";
					if ($this->is_local_calling_area($dbast, $rs1->fields['src'], $rs1->fields['dst'])) {
						$amount = 0;
						$slotMatched = $r['id'];
						#break;
					}
				} else if ($r['type'] == 'regular' || $r['type'] == 'default') {
					#echo "src=".$rs1->fields['src']."\n";
					#echo "dst=".$rs1->fields['dst']."\n";
					$pats = explode(";", $r['pattern']);
					$search = $rs1->fields['dst'];
					foreach ($pats as $pattern) {
						#echo "Matching against: $pattern\n";
						#if (ereg("^".$pattern, $search) || $r['type'] == 'default') {
						if ((strncmp($pattern, $search, strlen($pattern))==0 && strlen($pattern)) || $r['type'] == 'default') {
							echo "src=".$rs1->fields['src']."\n";
							echo "dst=".$rs1->fields['dst']."\n";
							echo "pattern=".$pattern."\n";
							echo "Billsec=".$rs1->fields['billsec']."\n";
							$amount = $this->price_call($db, $r, $rs1->fields['billsec'], $callSQL, $unit, $quan);
							$slotMatched = $r['id'];
							echo "Matched slot ".$r['id']." (".$r['name'].") with quan=$quan and unit=$unit\n";
							break;
						}
					}
				}
				# if the slot matched, and the call direction matches, then exit the rating table matching
				if ($slotMatched && (
					$r['direction'] == 'both' ||					 # rate entry for both call directions
					($r['direction'] == 'inbound' && $isInbound) ||  # inbound rate and inbound call direction
					($r['direction'] == 'outbound' && !$isInbound) ) # outbound rate and outbound call direction 
				) {
					echo "Found match: ".$slotMatched."\n";
					break;
				} else if ($slotMatched && $bAccountcode ) {
					# matched via account codes
					echo "Found match via accountcode: ".$slotMatched."\n";
					break;
				} else if ($slotMatched) {
					# reset the variables and continue trying to find a match
					$slotMatched = 0; $unit = 0; $quan = 0; $amount = 0;
					echo "Incorrect direction, continuing to match...\n";
				}
			}
	
			$sql = "update ".AGILE_DB_PREFIX."voip_cdr set account_id=".$db->qstr($crow['account_id']).", amount='".$amount."', calltype='$calltype', voip_rate_id='$slotMatched', rated=1, adjbillinterval=".$db->qstr($quan)." WHERE id=".$rs1->fields['id']." AND site_id=".DEFAULT_SITE;
			echo $sql."\n";
			if (!$db->Execute($sql)) {
				echo $db->ErrorMsg()."\n";
			}
			if ($unit && $postCharges) {
				$a = 'Source=='.$rs1->fields['src'].'\r\nDestination=='.$rs1->fields['dst'];
				$a .= '\r\nvoip_cdr_id=='.$rs1->fields['id'].'\r\ndate_orig=='.$rs1->fields['date_orig'];
				if ($r['connect_fee']) {
					$b = $a.'\r\nConnection Charge';
                    unset($fields);
                    $fields['date_orig'] = $rs1->fields['date_orig'];
                    $fields['status'] = 0;
                    $fields['service_id'] = $crow['service_id'];
                    $fields['amount'] = $r['connect_fee'];
                    $fields['sweep_type'] = 6;
                    $fields['taxable'] = 0;
                    $fields['quantity'] = 1;
                    $fields['product_id'] = $crow['product_id'];
                    $fields['attributes'] = $b;
                    $db->Execute( sqlInsert($db,"charge",$fields) );
				}
				if ($quan > 0) {
					unset($fields);
					$fields['date_orig'] = $rs1->fields['date_orig'];
					$fields['status'] = 0;
					$fields['service_id'] = $crow['service_id'];
					$fields['amount'] = $unit;
					$fields['sweep_type'] = 6;
					$fields['taxable'] = 0;
					$fields['quantity'] = $quan;
					$fields['product_id'] = $crow['product_id'];
					$fields['attributes'] = $a;
					$db->Execute( sqlInsert($db,"charge",$fields) );
				}
			}
			
			$rs1->MoveNext();
		}
	}

	function microtime_float()
	{
	   list($usec, $sec) = explode(" ", microtime());
	   return ((float)$usec + (float)$sec);
	}

/**
 * TODO: Accountcode based rating, Prepaid based rating
 */ 
	function c_task($VAR)
	{
		# set the PHP timeout and the don't abort flag
		set_time_limit(60 * 15);
		
		# normalize the CDR records
		if($this->perform_normalization) {
			echo "Begin normalization...\n";
			$this->normalize($db);
			echo "Finished normalization...\n";
		}
				
		# Add in the prepaid rating pieces

		# Begin the postpaid rating
		$bDoCallType = false;
		if(strlen($this->voip_intrastate))
			$bDoCallType = true;
		
		$db =& DB();	
		$sql = "select * from ".AGILE_DB_PREFIX."voip_cdr where 
		(lastapp='Dial' or lastapp='VoiceMail' or lastapp='MeetMe' or lastapp='Hangup') 
		AND disposition='ANSWERED' and rated=2 limit 1000";
		$rs = $db->Execute($sql); $i = 0; $total = 0.0;
		$st = $this->microtime_float();
		while(!$rs->EOF) {
			unset($match);
			# who does the number belong to?
			$account_id = 0; $service_id = 0; $product_id = 0; $callSQL = "";
			find_owner($rs->fields['src'], $account_id, $service_id, $product_id, $callSQL);
			$isInbound = 0;
			$calltype = 0;
			if($bDoCallType)
				$calltype = $this->isIntrastateCall($rs->fields['src'], $rs->fields['dst']);			
			#echo "Account: {$account_id} on src\n";
			if($account_id === false) {
				find_owner($rs->fields['dst'], $account_id, $service_id, $product_id, $callSQL);
				$isInbound = 1;
				$calltype=4;
				#echo "AccountL {$account_id} on dst\n";
			}
			
			if($account_id !== false) {
				# echo "Account=$account_id Product=$product_id Service=$service_id<br />";
				# Retrieve the correct rate table
				$rt =& $this->c_load_rating_table($db, $product_id);
				
				if(is_resource($rt)) {
					# Rate the call
					$src = $isInbound ? $rs->fields['dst'] : $rs->fields['src'];
					$dst = $isInbound ? $rs->fields['src'] : $rs->fields['dst'];
					if($match=agileco_search_rate_table($rt, strval($dst), intval($rs->fields['billsec']),
						intval($isInbound), strval($callSQL))) {
						#echo "<pre>";
						#echo "SRC=".$src."\n";
						#echo "DST=".strval($dst)."\n";
						#echo "BILLSEC=".intval($rs->fields['billsec'])."\n";
						#echo "In Bound?=".intval($isInbound)."\n";
						#echo "Call Type=".$calltype."\n";
						#echo "callSQL=".$callSQL."\n\n";
						#echo print_r($match,true);
						#echo "\n";
						#echo "</pre>";
					} else {
						#echo "SRC=".$src."\n";
						#echo "DST=".strval($dst)."\n";
						#echo "BILLSEC=".intval($rs->fields['billsec'])."\n";
						#echo "In Bound?=".intval($isInbound)."\n";
						#echo "Call Type=".$calltype."\n";
						#echo 'Returned false.'."\n\n";
						$match['amount'] = 0;
						$match['quantity'] = 0;
						$match['unit'] = 0;
						$match['voip_rate_id'] = 0;
					}
					$rated=1;
				} else {
					echo "Product $product_id does not have a rating table.\n";
				}
			} else {
				$isInbound = 0; $account_id = 0; $calltype=0; $rated = 3;
			}
			if(isset($match)) {
				$total += $match['amount'];
				$sql = "update ".AGILE_DB_PREFIX."voip_cdr SET 
					account_id=".$db->qstr($account_id).", 
					amount=".$db->qstr($match['amount']).", 
					calltype=".$db->qstr($calltype).", 
					voip_rate_id=".$db->qstr($match['voip_rate_id']).", 
					rated={$rated}, 
					adjbillinterval=".$db->qstr($match['quantity']).",
					site_id=".DEFAULT_SITE."  
					WHERE id=".$rs->fields['id']; 
				#echo $sql."\n";
				if (!$db->Execute($sql)) {
					echo $sql."\n";
					echo $db->ErrorMsg()."\n";
				}
				$a = 'Source=='.$rs->fields['src'].'\r\nDestination=='.$rs->fields['dst'];
				$a .= '\r\nvoip_cdr_id=='.$rs->fields['id'].'\r\ndate_orig=='.$rs->fields['date_orig'];
				if (isset($match['connect_fee']) && $match['connect_fee']) {
					$b = $a.'\r\nConnection Charge';
					$cid = sqlGenID($db, "charge");
					$sql = "INSERT INTO ".AGILE_DB_PREFIX."charge SET
						id=".$db->qstr($cid).",
						side_id=".DEFAULT_SITE.",
						date_orig=.".$db->qstr($rs->fields['date_orig']).",
						status=0,
						sweep_type=6,
						product_id=".$db->qstr($product_id).",
						service_id=".$db->qstr($service_id).",
						amount=".$db->qstr($match['connect_fee']).",
						quantity=1,
						taxable=0,
						attributes=".$db->qstr($b);
					if (!$db->Execute($sql)) {
						echo $sql."\n";
						echo $db->ErrorMsg()."\n";
					}
				}
				if ($match['quantity'] > 0) {
					$cid = sqlGenID($db, "charge");
					$sql = "INSERT INTO ".AGILE_DB_PREFIX."charge SET
					id=".$db->qstr($cid).",
					site_id=".DEFAULT_SITE.",
					date_orig=".$db->qstr($rs->fields['date_orig']).",
					status=0,
					sweep_type=6,
					product_id=".$db->qstr($product_id).",
					service_id=".$db->qstr($service_id).",
					amount=".$db->qstr($match['unit']).",
					quantity=".$db->qstr($match['quantity']).",
					taxable=0,
					attributes=".$db->qstr($a);
					if (!$db->Execute($sql)) {
						echo $sql."\n";
						echo $db->ErrorMsg()."\n";
					}
				}
			}
			$i++;
			$rs->MoveNext();
		}
		$et = $this->microtime_float();
		$tt = $et - $st;
		echo "Rated {$i} entries in {$tt} seconds.<br><br>";
		echo "Cough up $".number_format($total,4)."!<br>";	
	}
	
	/** Loads the rate table associated with a product.
	 *  @param $db A database connection handle
	 *  @param $product_id The ID of the product to load.
	 */
	function & c_load_rating_table(&$db, $product_id)
	{
		static $rate_cache;

		/* Is the rate already cached? */
		if(isset($rate_cache[$product_id])) {
			return $rate_cache[$product_id];
		}
		
		/* Cache Miss. Generate the entry. */
		$db =& DB();
		$rate_cache[$product_id] = agileco_new_rate_table();
		$i = 0; $st = $this->microtime_float();
		$sql = "SELECT b.id, b.connect_fee, b.increment_seconds, b.amount, b.pattern, b.type, b.direction, b.min, b.max, b.combine, b.percall, b.seconds_included FROM ".AGILE_DB_PREFIX."voip_rate_prod a left join ".AGILE_DB_PREFIX."voip_rate b on (a.voip_rate_id=b.id and a.site_id=".DEFAULT_SITE.") WHERE product_id=".$product_id." ORDER BY type ASC";
		#echo $sql."\n";
		$rs = $db->Execute($sql); 
		while (!$rs->EOF) {
			$pattern = preg_replace("/[^0-9;]/","",$rs->fields['pattern']);
			$pattern = str_replace("\r","",str_replace("\n","",$pattern));
			$pats = explode(";",$pattern);
			foreach($pats as $pat) {
				agileco_rate_table_insert(
					$rate_cache[$product_id],
					$pat,
					intval($rs->fields['id']),
					floatval($rs->fields['connect_fee']),
					intval($rs->fields['increment_seconds']),
					intval($rs->fields['seconds_included']),
					floatval($rs->fields['amount']),
					intval($rs->fields['min']),
					intval($rs->fields['max']),
					intval($rs->fields['type']),
					intval($rs->fields['direction']),
					intval($rs->fields['combine']),
					intval($rs->fields['percall'])
				);
				++$i;
			}
			$rs->MoveNext();
		}
		$et = $this->microtime_float(); $tt = $et - $st;
		echo "Loaded {$i} patterns for product {$product_id} in {$tt} seconds.<br />\n";
		return $rate_cache[$product_id];
	}
	 
}

#
# C MODULE FUNCTIONS FOLLOW BELOW
#

// NOTE: I didn't include this in the class because it is known to be faster to call
//       a function, than a method of a class.
function find_owner($did, &$account_id, &$service_id, &$product_id, &$callSQL) {
	static $owners;
	
	if(isset($owners[$did])) {
		$account_id = $owners[$did]['account_id'];
		$service_id = $owners[$did]['service_id'];
		$product_id = $owners[$did]['product_id'];
		$callSQL = $owners[$did]['callSQL'];
		return true;
	}
	$db =& DB();
	#$sql = "SELECT id FROM ab_account WHERE username=".$db->qstr($did)." and site_id=".DEFAULT_SITE;
	$sql = "select A.account_id, A.service_id, B.product_id, B.date_last_invoice, B.date_next_invoice  
		from ".AGILE_DB_PREFIX."voip_did A inner join ".AGILE_DB_PREFIX."service B on (B.id=A.service_id) 
		where did=".$db->qstr($did)." and A.site_id=".DEFAULT_SITE." and B.site_id=".DEFAULT_SITE;
	#echo $sql."<br>";
	$row = $db->GetRow($sql);
	if($row === false || !isset($row[0])) {
		$account_id = false;
		$service_id = false;
		$product_id = false;
		$callSQL = "";
		return false;
	}
	$account_id = $row[0];
	$service_id = $row[1];
	$product_id = $row[2];

	$row[3] = $row[3] - (MAX_INV_GEN_PERIOD*86400);
	$row[4] = $row[4] + 86399;
										
	$callSQL = "((src=".$db->qstr($did)." or dst=".$db->qstr($did).") and ";
	$callSQL .= "date_orig>=".$db->qstr($row[3])." and date_orig<=".$db->qstr($row[4]).")";
	
	$owners[$did]['account_id'] = $row[0];
	$owners[$did]['service_id'] = $row[1];
	$owners[$did]['product_id'] = $row[2];
	$owners[$did]['callSQL'] = $callSQL;
	return true;
}

function agileco_php_in_network($did) {
	$db =& DB();
	$sql = "select count(*) from ".AGILE_DB_PREFIX."voip_in_network where did in ('".$did."')";
	#echo $sql."\n";
	$rs2 = $db->Execute($sql);
	if ($rs2->fields[0] > 0) {
		#echo "Yes, call is local.\n";
		return 1;
	}
	return 0;
}

function agileco_php_local_call($dst) {
	global $src;
	
	$voip = new voip;
	$cc1 = ""; $npa1 = ""; $nxx1 = "";
	$cc2 = ""; $npa2 = ""; $nxx2 = "";
	$e164 = "";
	if ($voip->e164($src,$e164,$cc1,$npa1,$nxx1)) {
		if ($voip->e164($dst,$e164,$cc2,$npa2,$nxx2)) {
			$sql = "select t1.grouping as id1, t2.grouping from ".AGILE_DB_PREFIX."voip_local_lookup as t1 join ".AGILE_DB_PREFIX."voip_local_lookup as t2 on (t1.npa='$npa1' and t1.nxx='$nxx1' and t2.npa='$npa2' and t2.nxx='$nxx2') where t1.grouping=t2.grouping";
			echo $sql."\n";
			$rs = $db->Execute($sql);
			if ($rs->fields[0] > 0) {
				return 1;
			}
		}
	}
	return 0;
}

function agileco_php_minutes_used($voip_did_id, $callSQL) {
	$db =& DB();
	$sql = "select sum(adjbillinterval) from ".AGILE_DB_PREFIX."voip_cdr where voip_rate_id={$voip_did_id} and {$callSQL}";
	$rst = $db->Execute($sql);
	$num = intval($rst->fields[0]);
	echo 'Customer has '.$num.' minutes used.<br>';
	return $num;
}

?>