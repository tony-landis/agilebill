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
	
class CORE_trigger
{
   function trigger($trigger, $type, $VAR)
   {          	
	   if($type)
	   {
		   # do success trigger(s)
		   if(isset($trigger["success"]))
			   $this->run_triggers($trigger["success"]);
	   }
	   else
	   {
		   # do failure trigger(s)
		   if(isset($trigger["failure"]))
			   $this->run_triggers($trigger["failure"]);
	   }
   }	

   # run the trigger(s):
   function run_triggers($trigger)
   {
	   global $C_method;
	   $triggers = explode(',', $trigger);
	   for($i=0; $i<count($triggers); $i++)
	   {
		   if(isset($triggers[$i]))
		   {
			   $triggerss = explode(':',$triggers[$i]);
			   # added to remove php error: Undefined offset
			   if(isset($triggerss) && count($triggerss) > 1)
			   {
				  $C_method->exe($triggerss[0], $triggerss[1]);
			   }
		   }
	   }
   }
}
?>