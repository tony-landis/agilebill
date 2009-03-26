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
	
class CORE_theme
{
	var $id;

	function CORE_theme()
	{
		global $VAR, $C_debug, $C_translate, $smarty;

		# Get the cuurent session theme:
		if(defined("SESS_THEME") && file_exists(PATH_THEMES . '' . SESS_THEME . '/template.tpl'))
		{
			if(SESS_THEME == 'default_admin' && SESS_LOGGED != true )
				define ('THEME_NAME', DEF_THEME_N);
			elseif (defined("ADMIN_FORCE"))
				define ('THEME_NAME', 'default_admin');
			elseif (!defined("ADMIN_FORCE") && SESS_THEME != 'default_admin')
				define ('THEME_NAME', SESS_THEME);  
			else
				define ('THEME_NAME', DEF_THEME_N);  
		}
		elseif(file_exists(PATH_THEMES.DEFAULT_THEME.'/template.tpl'))
		{
			define ('THEME_NAME', DEFAULT_THEME); 
		}
		else 
		{
			define ('THEME_NAME', DEF_THEME_N); 
		} 

		# load the block class
		$block = new CORE_block;

		# set smarty vars
		if(isset($smarty->template_dir)) unset($smarty->template_dir);
		$smarty->use_sub_dirs    = false;
		$smarty->template_dir    = PATH_THEMES . '' . THEME_NAME . '/';
		$smarty->compile_dir     = PATH_SMARTY . 'templates/';
		$smarty->config_dir      = PATH_SMARTY . 'configs/';
		$smarty->cache_dir       = PATH_SMARTY . 'cache/';
		$this->caching           = false;
		$this->compile_check     = true;
		$smarty->assign("THEME_NAME", THEME_NAME);

		# Frame Theme Escape 
		if(THEME_NAME == 'default_admin')
		{ 
			if (!empty($VAR['tid']) && empty($VAR['_escape']))
			{
				// get url string to pass to mainFrame
				$url='';
				$i=0;
				while(list($key,$val) = each($VAR)) {
					if($key != 'tid')
					{
						if($i==0) $url .= '?'; else $url .= '&';
						$url .= $key.'='.$val;
						$i++;
					}
				}
				$url = preg_replace('/tid=default_admin/', '', $url);
				$smarty->assign('mainFrameUrl', $url);
				$this_template = 'file:'.PATH_THEMES.''.THEME_NAME.'/template.tpl';
				$smarty->display($this_template);
				exit;
			} 

			if (empty($VAR['_escape']))
			$block->display('core:top_frame');

			# force or define page set?
			if(defined("FORCE_PAGE")) {
				$block->display(FORCE_PAGE);
				exit();
			} elseif (@$VAR['_page']) {
				$block->display($VAR['_page']);
				exit();
			} else {
				$block->display('core:admin');
				exit;
			}
		}

		# Standard themes
		if(isset($VAR['_escape']))
		{
			if(isset($VAR['_print']))
			{
				# load printer friendly version
				$block->display('core:top_frame');
				$block->display($VAR['_page']);
				$block->display('core:bottom_print');
				exit();
			}
			else
			{
				# check for force page:
				if(defined("FORCE_PAGE"))
				$block->display(FORCE_PAGE);
				else
				$block->display($VAR['_page']);
				exit();
			}
		}
		else
		{
			if(defined("FORCE_PAGE")) {
				define('THEME_PAGE', FORCE_PAGE);
			} else {
				if(isset($VAR['_page']))
				define('THEME_PAGE', $VAR['_page']);
				else
				define('THEME_PAGE', 'core:main');
			}

		   # load the block normally
		   $this_template           = 'file:' . PATH_THEMES . '' . THEME_NAME . '/template.tpl';
		   $smarty->display($this_template);

		}
	}
}



class CORE_block
{
	function display($block_r)
	{  
		global $smarty;                                     
		@$resource = explode(':',$block_r);
		@$module = $resource[0];
		@$block  = $resource[1]; 
		if($module == 'TEMPLATE') 
		{
			$smarty->template_dir    = PATH_THEMES . '' . THEME_NAME . '/';
			$smarty->display('file:' . $smarty->template_dir . '' . $block . '.tpl'); 
		} else {
			if(is_file(PATH_THEMES . '' . THEME_NAME . '/blocks/' . $module . '/' . $block . '.tpl')) 
			{
				$smarty->template_dir    = PATH_THEMES . '' . THEME_NAME . '/blocks/' . $module . '/';   
				$smarty->display('file:' . $smarty->template_dir . '' . $block . '.tpl');         	
			} 
			elseif (is_file(PATH_THEMES . '' . DEF_THEME_N . '/blocks/' . $module . '/' . $block . '.tpl')) 
			{
				$smarty->template_dir    = PATH_THEMES . '' . DEF_THEME_N . '/blocks/' . $module . '/';
				$smarty->display('file:' . $smarty->template_dir . '' . $block . '.tpl');  
			} 
			elseif (is_file(PATH_THEMES . 'default/blocks/' . $module . '/' . $block . '.tpl')) 
			{
				$smarty->template_dir    = PATH_THEMES . 'default/blocks/' . $module . '/';
				$smarty->display('file:' . $smarty->template_dir . '' . $block . '.tpl');  
			}             	
			else 
			{
				$smarty->display('file:'. PATH_THEMES . '' . DEF_THEME_N . '/blocks/core/invalid_page.tpl'); 
			} 
		} 
	}
}
?>