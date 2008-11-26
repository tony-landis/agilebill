<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:       function.style_hide.php
 * Type:       function
 * Name:       style_hide
 * Version:    1.0 
 * Purpose:    Hides a div/span element without disabling the view in dreamweaver
 * -------------------------------------------------------------
 */
function smarty_function_style_hide($params, &$smarty)
{  
   	return 'style="display:none"'; 
}
?>