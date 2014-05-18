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
	
class host_tld
{
 
	/**
	 * Get the TLD pricing array
	 *
	 * @param string $tld 
	 * @param string $type park, register, renew 
	 * @param int $product_id
	 * @param array $discount_products
	 * @param float $discount_rate
	 * @param int $account
	 * @return array
	 */
	function price_tld_arr($tld, $type, $product_id=false, $discount_products=false, $discount_rate=false, $account=SESS_ACCOUNT)
	{
		# get the plugin for this domain:
		$db     = &DB(); 
		$result = $db->Execute(sqlSelect($db,"host_tld","*","name=::$tld:: AND status=1"));
		if($result == false || $result->RecordCount() == 0) return false;

		# serialize:
		global $C_auth;
		$p_arr = unserialize($result->fields["price_group"]);

		# get the pricing for domain parking:
		if($type == "park") {
			if($p_arr[0]["show"] != "1") {
				return false;
			} else {
				$i = 0;
				$type = $register;
				while (list ($group, $vals) = each ($p_arr[$i]))
				if (gettype($group) != 'string' && $C_auth->auth_group_by_account_id($account, $group))
				if(empty($price) || $vals["register"] < $price)
				$price = $vals["register"];
				return $price;
			}
		} else {
			# get any hosting discounts for this product:
			if(!empty($discount_products)) {
				$d_arr = unserialize($discount_products);
				for($ii=0; $ii<count($d_arr); $ii++) {
					if($d_arr[$ii] == $result->fields["id"])
					$hosting_discount = $discount_rate;
				}
			}

			if(empty($hosting_discount)) $hosting_discount = false;

			# get the pricing details for registrations/transfers for this TLD:
			if(count($p_arr) > 0)
			for($i=1; $i<=10; $i++)
			if($p_arr[$i]["show"] == "1")
			while (list ($group, $vals) = each ($p_arr[$i]))
			if (gettype($group) != 'string' && $C_auth->auth_group_by_account_id($account, $group))
			if(empty($price[$i]) || $vals[$type] < $price[$i])
			if(!empty($vals[$type]))
			if($hosting_discount != false)
			$price[$i] = $vals[$type] - ($vals[$type] * $hosting_discount);
			else
			$price[$i] = $vals[$type];
			return $price;
		}
		return false;
	}
    	
    		
	/** SUGGEST RESULTS	
	 */
	function suggest($VAR)
	{
		$db = &DB();
		$dbm = new CORE_database;
		$sql = $dbm->sql_select('host_tld','name,default_term_new', "auto_search = 1 AND status = 1", "name", $db);
		$rs = $db->Execute($sql);
		while(!$rs->EOF) {
			$smart[] = $rs->fields;
			$rs->MoveNext();
		}

		$count = count($smart);
		$js = 	"var tldArr = new Array($count); var tldCount = $count; ";
		for($i=0; $i<$count; $i++)
		$js .= "tldArr[$i] = '{$smart[$i]['name']}'; ";

		global $smarty;
		$smarty->assign('tlds', $smart);
		$smarty->assign('javascript', $js);
	}

	/** WHOIS LOOKUP
	 */
	function whois_mass($VAR)
	{
		global $smarty, $C_debug, $C_translate;
		$db     = &DB();

		if(!empty($VAR['domains']))
		{
			$arr = explode("\r\n", $VAR['domains']);
			$domains ='';
			$msg ='';
			// loop through each row
			for($i=0; $i<count($arr); $i++)
			{
				# check for correct structure:
				if(preg_match('/\./', $arr[$i]))
				{
					# split domain & tld
					$dt = explode('.', $arr[$i]);
					$domain = $dt[0];

					# get the current tld
					$tld = '';
					foreach($dt as $key=>$td) {
						if($key > 0) {
							if(!empty($tld)) $tld .='.';
							$tld .= $td;
						}
					}

					# check for duplicates
					$do=true;
					for($ii=0; $ii<count(@$domainarr); $ii++) {
						if($domainarr[$ii][0] == $domain && $domainarr[$ii][1] == $tld) {
							$do = false;
							break;
						}
					}
					if($do)
					{
						$C_translate->value['host_tld']['domain'] = '<b><u>'.$domain.".".$tld.'</u></b>';
						$C_translate->value['host_tld']['tld'] = '<b><u>'.$tld.'</u></b>';

						# get the plugin for this domain:
						$result = $db->Execute(sqlSelect($db,"host_tld","*","name=::$tld:: AND status=1"));
						if($result == false || $result->RecordCount() == 0)
						{
							### INVALID TLD
							$msg .= $C_translate->translate('search_mass_err_tld','host_tld','') . '<br>';
						}
						else
						{
							# get the whois plugin details for this TLD & check avail
							$file  =  $result->fields['whois_plugin'];
							$data  =  unserialize($result->fields['whois_plugin_data']);
							include_once(PATH_PLUGINS . 'whois/'. $file.'.php');
							eval ( '$_WHOIS = new plgn_whois_'. strtoupper ( $file ) . ';' );
							if($_WHOIS->check($domain, $tld, $data))
							{
								$smarty->assign("checkout", true);
								$domains .= $domain.'.'.$tld."\r\n";
								$domainarr[] = Array($domain,$tld);
							} else {
								### DOMAIN NOT AVAILABLE
								$msg .= $C_translate->translate('search_mass_err_dom','host_tld','') . '<br>';
							}
						}
					}
				}
			}
			if($msg) $C_debug->alert($msg);
			$smarty->assign('domains', @$domains);
			$smarty->assign('domainarr', @$domainarr);
		}
	}
	
	/**
	 * WHOIS RESPONSE 
	 */
	function whois_reponse($type, $VAR, $response, $park=0) {
		if(defined('AJAX')) {
			
			if($type=='register') {
				if($response)
					echo 'available('.$park.');';
				else
					echo 'unavailable();';	
								
			} elseif($type=='transfer') {
				if($response)
					echo 'unavailable();';
				else
					echo 'available();';
					
			} elseif($type=='suggest') {
				if($response)
					echo "domainUpdate('{$VAR['domain']}','{$VAR['tld']}','register','{$VAR['element']}',1);";
				else
					echo "domainUpdate('{$VAR['domain']}','{$VAR['tld']}','register','{$VAR['element']}',0);";
			}
		}
		return $response;
	}

	/**
	 * WHOIS LOOKUP
	 */
	function whois($VAR)
	{  
		if(!empty($VAR['tld']) && !empty($VAR['domain']))
		{
			$db = &DB(); 
			# check this domain & tld is not already in the service table:
			$rs =  $db->Execute(sqlSelect($db,"service","id","domain_name = ::{$VAR['domain']}:: AND domain_tld = ::{$VAR['tld']}::"));
			if($rs && $rs->RecordCount()) {
				//$smarty->assign("whois_result", "0");
				//echo 'unavailable();';				
				return $this->whois_reponse($VAR['type'], $VAR, false);
			}

			# check this domain & tld is not already in the shopping cart:
			$rs =  $db->Execute(sqlSelect($db,"cart","id","domain_name = ::{$VAR['domain']}:: AND domain_tld = ::{$VAR['tld']}::"));
			if($rs && $rs->RecordCount()) { 
				return $this->whois_reponse($VAR['type'], $VAR, false);
			}

			# get the plugin for this domain:
			$result = $db->Execute(sqlSelect($db,"host_tld","*","name=::{$VAR['tld']}:: AND status=1"));  
			if($result == false || $result->RecordCount() == 0) { 
				return $this->whois_reponse($VAR['type'], $VAR, false);
			}

			# get the whois plugin details for this TLD
			$file  =  $result->fields['whois_plugin'];
			$data  =  unserialize($result->fields['whois_plugin_data']);

			# allow parking?
			$price = unserialize ( $result->fields['price_group'] );
			$park = $price["0"]["show"]; 

			# initialize the whois plugin:
			include_once(PATH_PLUGINS . 'whois/'. $file.'.php');
			eval ( '$_WHOIS = new plgn_whois_'. strtoupper ( $file ) . ';' );

			if($_WHOIS->check($VAR['domain'], $VAR['tld'], $data)) 
				return $this->whois_reponse($VAR['type'], $VAR, true, $park); 
			else 
				return $this->whois_reponse($VAR['type'], $VAR, false, $park);
		 
		} else { 
			return $this->whois_reponse($VAR['type'], $VAR, false, $park);
		}
	}
 
	/**
	 * WHOIS TRANSFER LOOKUP 
	 */
	function whois_transfer($VAR)
	{
		global $smarty;
		if(!empty($VAR['tld']) && !empty($VAR['domain']))
		{ 
			$db = &DB();

			# check this domain & tld is not already in the service table:
			$rs =  $db->Execute(sqlSelect($db,"service","id","domain_name = ::{$VAR['domain']}:: AND domain_tld = ::{$VAR['tld']}::"));
			if($rs && $rs->RecordCount()) {
				$smarty->assign("whois_result", "0");
				return;
			}

			# check this domain & tld is not already in the shopping cart:
			$rs =  $db->Execute(sqlSelect($db,"cart","id","domain_name = ::{$VAR['domain']}:: AND domain_tld = ::{$VAR['tld']}::"));
			if($rs && $rs->RecordCount()) {
				$smarty->assign("whois_result", "0");
				return;
			}

			# get the plugin for this domain: 
			$result = $db->Execute(sqlSelect($db,"host_tld","*","name=::{$VAR['tld']}:: AND status=1"));
			if($result == false || $result->RecordCount() == 0) {
				$smarty->assign("whois_result", "0");
				return;
			}

			# get the pricing details to see if transfers are allowed for this TLD:
			$p_arr = unserialize($result->fields["price_group"]);
			$transfer = false;
			if(count($p_arr) > 0)
			for($i=1; $i<=10; $i++)
			if($p_arr[$i]["show"] == "1")
			while(list($key,$val) = each($p_arr[$i]))
			if(isset($val["transfer"]) && $val["transfer"] > 1) $transfer = true;

			if(!$transfer)
			{
				$smarty->assign("whois_result", "0");
				return;
			}

			# get the whois plugin details for this TLD
			$file  =  $result->fields['whois_plugin'];
			$data  =  unserialize($result->fields['whois_plugin_data']);

			# initialize the whois plugin:
			include_once(PATH_PLUGINS . 'whois/'. $file.'.php');
			eval ( '$_WHOIS = new plgn_whois_'. strtoupper ( $file ) . ';' );

			if($_WHOIS->check_transfer($VAR['domain'], $VAR['tld'], $data))
			{
				$smarty->assign("whois_result", "1");
				return;
			}
			else
			{
				$smarty->assign("whois_result", "0");
				return;
			} 
		}
		else
		{
			$smarty->assign("whois_result", "0");
			return;
		}
	}
 
	function constructor()
	{ 
		$this->module = "host_tld";
		$this->xml_construct = PATH_MODULES . "" . $this->module . "/" . $this->module . "_construct.xml";
		$C_xml = new CORE_xml;
		$construct = $C_xml->xml_to_array($this->xml_construct);
		$this->method   = $construct["construct"]["method"];
		$this->trigger  = $construct["construct"]["trigger"];
		$this->field    = $construct["construct"]["field"];
		$this->table 	= $construct["construct"]["table"];
		$this->module 	= $construct["construct"]["module"];
		$this->cache	= $construct["construct"]["cache"];
		$this->order_by = $construct["construct"]["order_by"];
		$this->limit	= $construct["construct"]["limit"];
	}
 
	function add($VAR)
	{
		$this->constructor();
		$type 		= "add";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db 		= new CORE_database;
		$db->add($VAR, $this, $type);
	}
 
	function view($VAR)
	{
		$this->constructor();
		$type = "view";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->view($VAR, $this, $type);
	}
 
	function update($VAR)
	{
		$this->constructor();
		$type = "update";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->update($VAR, $this, $type);
	}
 
	function delete($VAR)
	{
		$this->constructor();
		$db = new CORE_database;
		$db->mass_delete($VAR, $this, "");
	}
 
	function search_form($VAR)
	{
		$this->constructor();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_form($VAR, $this, $type);
	}
 
	function search($VAR)
	{
		$this->constructor();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search($VAR, $this, $type);
	} 
	
	function search_show($VAR)
	{
		$this->constructor();
		$type = "search";
		$this->method["$type"] = explode(",", $this->method["$type"]);
		$db = new CORE_database;
		$db->search_show($VAR, $this, $type);
	} 
}
?>