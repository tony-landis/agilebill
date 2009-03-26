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
	
class CORE_xml
{ 
	function xml_to_array($file) 
	{ 
		if (defined('AGILE_CORE_CACHE_DIR') && AGILE_CORE_CACHE_DIR != '') {
			$tfile = AGILE_CORE_CACHE_DIR."xml-".md5($file);
			if (file_exists($tfile)) {
				#clearstatcache();
				#$mt = filemtime($file);
				if (@filemtime($file) <= filemtime($tfile)) {
					#echo "cached: $file<br>";
					return unserialize(file_get_contents($tfile));
				}
			}
			#echo "cache miss: $file<br>";
		}	
		if(!is_callable('simplexml_load_file') || !is_callable('dom_import_simplexml')) {
			// call old XML to array class
			$arr = xmlFileToArray($file, $includeTopTag = true, $lowerCaseTags = true); 				
		} else {
			// use SimpleXML class  
			if(!is_file($file)) return false;
			$xml = simplexml_load_file ($file);  
			if(is_object($xml)) {
				$dom = dom_import_simplexml ($xml); 
				$arr["$dom->tagName"] = SimpleXML2Array($xml);				
			}
		}
		if (defined('AGILE_CORE_CACHE_DIR') && AGILE_CORE_CACHE_DIR != '') {
			$fp = fopen($tfile,"wb");
			if ($fp) {
				fwrite($fp, serialize($arr));
				fclose($fp);
			}
		}
		return $arr;
	}

	function array_to_xml() { 
	}

	function xml_string_to_array($string) {
	}
} 

// new XML to Array for PHP5 and SimpleXML
function SimpleXML2Array($xml) {
   if (is_object($xml) && get_class($xml) == 'SimpleXMLElement') {
	   $attributes = $xml->attributes();
	   foreach($attributes as $k=>$v) {
		   if ($v) $a[$k] = (string) $v;
	   }
	   $x = $xml;
	   $xml = get_object_vars($xml);
   }
   if (is_array($xml)) {
	   if (count($xml) == 0) return (string) $x; // for CDATA
	   foreach($xml as $key=>$value) {
		   $r[$key] = simplexml2array($value);
	   }
	   if (isset($a)) $r['@'] = $a;    // Attributes
	   return $r;
   }
   return (string) $xml;
} 


function & toString( $fileName )
{
	if ($content_array = file($fileName))
	{
		$file = implode("", $content_array); 
		return $file; 
	}
	else
	{
		// Error
		$false = false;
		return $false;
	}
}


function & xmlFileToArray($fileName, $includeTopTag = false, $lowerCaseTags = true)
{
	// Definition file not found
	if (!file_exists($fileName))
	{
		// Error
		$false = false;
		return $false;
	}
	$p = xml_parser_create();
	xml_parse_into_struct($p,toString($fileName),$vals,$index);
	xml_parser_free($p);
	$xml = array();
	$levels = array();
	$multipleData = array();
	$prevTag = "";
	$currTag = "";
	$topTag = false;
	foreach ($vals as $val)
	{
		// Open tag
		if ($val["type"] == "open")
		{
			if (!_xmlFileToArrayOpen($topTag, $includeTopTag, $val, $lowerCaseTags,
			$levels, $prevTag, $multipleData, $xml))
			{
				continue;
			}
		}
		// Close tag
		else if ($val["type"] == "close")
		{
			if (!_xmlFileToArrayClose($topTag, $includeTopTag, $val, $lowerCaseTags,
			$levels, $prevTag, $multipleData, $xml))
			{
				continue;
			}
		}
		// Data tag
		else if ($val["type"] == "complete" && isset($val["value"]))
		{
			$loc =& $xml;
			foreach ($levels as $level)
			{
				$temp =& $loc[str_replace(":arr#", "", $level)];
				$loc =& $temp;
			}
			$tag = $val["tag"];
			if ($lowerCaseTags)
			{
				$tag = strtolower($val["tag"]);
			}
			$loc[$tag] = str_replace("\\n", "\n", $val["value"]);
		}
		// Tag without data
		else if ($val["type"] == "complete")
		{
			_xmlFileToArrayOpen($topTag, $includeTopTag, $val, $lowerCaseTags,
			$levels, $prevTag, $multipleData, $xml);
			_xmlFileToArrayClose($topTag, $includeTopTag, $val, $lowerCaseTags,
			$levels, $prevTag, $multipleData, $xml);
		}
	}
	return $xml;
}



function _xmlFileToArrayOpen(& $topTag, & $includeTopTag, & $val, & $lowerCaseTags,
& $levels, & $prevTag, & $multipleData, & $xml)
{
	// don't include top tag
	if (!$topTag && !$includeTopTag)
	{
		$topTag = $val["tag"];
		return false;
	}
	$currTag = $val["tag"];
	if ($lowerCaseTags)
	{
		$currTag = strtolower($val["tag"]);
	}
	$levels[] = $currTag;

	// Multiple items w/ same name. Convert to array.
	if ($prevTag === $currTag)
	{
		if (!array_key_exists($currTag, $multipleData) ||
		!$multipleData[$currTag]["multiple"])
		{
			$loc =& $xml;
			foreach ($levels as $level)
			{
				$temp =& $loc[$level];
				$loc =& $temp;
			}
			$loc = array($loc);
			$multipleData[$currTag]["multiple"] = true;
			$multipleData[$currTag]["multiple_count"] = 0;
		}
		$multipleData[$currTag]["popped"] = false;
		$levels[] = ":arr#" . ++$multipleData[$currTag]["multiple_count"];
	}
	else
	{
		$multipleData[$currTag]["multiple"] = false;
	}

	// Add attributes array
	if (array_key_exists("attributes", $val))
	{
		$loc =& $xml;
		foreach ($levels as $level)
		{
			$temp =& $loc[str_replace(":arr#", "", $level)];
			$loc =& $temp;
		}
		$keys = array_keys($val["attributes"]);
		foreach ($keys as $key)
		{
			$tag = $key;
			if ($lowerCaseTags)
			{
				$tag = strtolower($tag);
			}
			$loc["attributes"][$tag] = & $val["attributes"][$key];
		}
	}
	return true;
}


function _xmlFileToArrayClose(& $topTag, & $includeTopTag, & $val, & $lowerCaseTags,
& $levels, & $prevTag, & $multipleData, & $xml)
{
	// don't include top tag
	if ($topTag && !$includeTopTag && $val["tag"] == $topTag)
	{
		return false;
	}
	if(isset($currTag))
	{
		if (isset($multipleData[$currTag]["multiple"]))
		{
			$tkeys = array_reverse(array_keys($multipleData));
			foreach ($tkeys as $tkey)
			{
				if ($multipleData[$tkey]["multiple"] && !$multipleData[$tkey]["popped"])
				{
					array_pop($levels);
					$multipleData[$tkey]["popped"] = true;
					break;
				}
				else if (!$multipleData[$tkey]["multiple"])
				{
					break;
				}
			}
		}
	}
	$prevTag = array_pop($levels);
	if (strpos($prevTag, "arr#"))
	{
		$prevTag = array_pop($levels);
	}
	return true;
}
?>