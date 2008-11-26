<?

ini_set("include_path", dirname(__FILE__) . "/");

include_once("RRAD.php"); 			// Always require the RRADServer Object.
include_once("hCommand.php");     	// Can't send commands without this
include_once("hAuthCommand.php"); 	// authentication commands
include_once("hCloseCommand.php"); 	// authentication commands
include_once("hService.php");		// Base class for all services	
include_once("hInfo.php");			// Base class for Info objects 
include_once("hDomain.php");		// Context required for most services.

ini_restore("include_path");
?>
