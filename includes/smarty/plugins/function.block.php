<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     block
 * Purpose:  display an agilebill block
 * Usage:  	 {block module=? block=?}
 * -------------------------------------------------------------
 */
function smarty_function_block($params, &$this)
{
    extract($params);
                
    if($module == 'TEMPLATE') { 
    	$this->display('file:' . PATH_THEMES . '' . THEME_NAME . '/' . $smarty->template_dir . '' . $block . '.tpl');
    } else {    	
    	if(is_file(PATH_THEMES . '' . THEME_NAME . '/blocks/' . $module . '/' . $block . '.tpl')) 
    		$this->display('file:'. PATH_THEMES . '' . THEME_NAME . '/blocks/' . $module . '/' . $block . '.tpl' );    	 
    	elseif (is_file(PATH_THEMES . '' . DEF_THEME_N . '/blocks/' . $module . '/' . $block . '.tpl')) 
    		$this->display('file:' . PATH_THEMES . '' . DEF_THEME_N . '/blocks/' . $module . '/' . $block . '.tpl');    	 
    	elseif (is_file(PATH_THEMES . 'default/blocks/' . $module . '/' . $block . '.tpl')) 
    		$this->display('file:' . PATH_THEMES . 'default/blocks/' . $module . '/' . $block . '.tpl');    	 
    	else 
    		$this->display('file:'. PATH_THEMES . '' . DEF_THEME_N . '/blocks/core/invalid_page.tpl');    	  
    } 
} 
?>