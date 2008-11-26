<?php

function smarty_modifier_bool($string, $format="", $default_date=null)
{
	if($string)
  	echo translate("true");
  	else
  	echo translate("false");
} 
?>
