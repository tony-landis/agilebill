<?
ini_set("include_path", dirname(__FILE__) . "/");

include_once("hInfoService.php");		// Info Services: usage, contact, etc 
include_once("hWebService.php");		// Web Services: add domain, delete, etc
include_once("hConvenienceService.php");	// Convenience Services: add domain, change pass, etc
include_once("hAdminService.php");		// Admin Service: enable, disable

// Different Info objects
include_once("hServiceInfo.php");		
include_once("hContactInfo.php");
include_once("hUsageInfo.php");
include_once("hAdminInfo.php");

ini_restore("include_path");
?>
