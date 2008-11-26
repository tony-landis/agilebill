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
	
class CORE_translate
{ 
	function CORE_translate() {
		if(defined("SESS_LANGUAGE")) 
			$language = SESS_LANGUAGE;
		else
			$language = DEFAULT_LANGUAGE; 		
		$language = $this->get_lang_pack('CORE', $language);
		define('LANG', $this->language_name);
	}


	function get_lang_pack($module, $language) {	
		# define the language names / ids (must match the language.name & language.id fields in the DB
		$this->lang_arr[0] = 'english';

		# get the Core language pack
		if($module=='CORE') {
			$pack_file = PATH_LANGUAGE .'core/'. $language . '_core.xml';
			$this->language_name = $language;
		} else if ($language != '') {
			$pack_file = PATH_LANGUAGE . '' . $module . '/' . $language . '_' . $module . '.xml';
		} 
		$def_pack_file = PATH_LANGUAGE . '' . $module . '/' . DEFAULT_LANGUAGE . '_' . $module . '.xml';

		# check that the defined file exists, if not, use the default language instead:
		if(file_exists($pack_file)) {
			# open the file for parsing	
			$C_xml = new CORE_xml;
			$this->lang_pack["$module"]["$language"] = $C_xml->xml_to_array($pack_file);				
		} else {
			# open the default language file for parsing	
			$C_xml = new CORE_xml;
			$this->lang_pack["$module"]["$language"] = $C_xml->xml_to_array($def_pack_file);
		}			
	}

	function translate_resource($module, $resource, $language) {	 
		 if(!empty($this->value["$module"])) $array = $this->value["$module"]; 
		 @$string = $this->lang_pack["$module"]["$language"]["translate"]["$resource"];			
		 if(!empty($array) && is_array($array) && !empty($string)) 
			 while(list ($key, $val) = each ($array))  
				$string = str_replace("%%{$key}%%", $val, $string); 
		 return $string;
	}

	function value($module, $variable, $value) {
		$this->value["$module"]["$variable"] = $value;
	}			

	function translate($resource, $module='CORE', $language=SESS_LANGUAGE) {			
		# determine the language 
		if(empty($language)) {
			if(defined("SESS_LANGUAGE")) 
				$language = SESS_LANGUAGE;
			else 
				$language = DEFAULT_LANGUAGE;				
		}

		if(empty($module)) $module = 'CORE';

		if(!empty($resource)) {				
			# checks if this is the core
			if($module == 'CORE')	
				return $this->translate_resource('CORE', $resource, $language);

			# load the language pack for the current module if needed:
			if(!isset($this->lang_pack["$module"]["$language"]))
				$this->get_lang_pack($module,$language);

			# translate/return the current resource
			return $this->translate_resource($module, $resource, $language);				
		}			
	}
}
?>