<?php

function smarty_modifier_date($string, $format="%b %e, %Y", $default_date=null)
{
  echo timestampToDate($string);
} 
?>
