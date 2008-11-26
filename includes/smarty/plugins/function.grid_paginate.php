<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     grid_paginate
 * Purpose:  display grid pagination controls
 * -------------------------------------------------------------
 */
function smarty_function_grid_paginate($params, &$smarty)
{
	extract($params); 
	global $C_translate;
	echo '<input type="button" disabled onclick="'.$module.'_grid.paginateJump(1);" value="'. $C_translate->translate("grid_first") .'" id="'.$module.'_grid_pageinate_first" />';
	echo '<input type="button" disabled onclick="'.$module.'_grid.paginateJump(\'prev\');" value="'. $C_translate->translate("grid_prev") .'" id="'.$module.'_grid_pageinate_prev" />';
	echo '<input type="text"   onchange="'.$module.'_grid.paginateJump(this.value)" onfocus="this.value=\'\'" value="1" size="4" maxlength="4" id="'.$module.'_grid_pageinate_jump" style="padding:3px; width:30px; text-align:center; background-color:#f1f1f1;" />';
	echo '<input type="button" disabled onclick="'.$module.'_grid.paginateJump(\'next\');" value="'. $C_translate->translate("grid_next") .'" id="'.$module.'_grid_pageinate_next" />';
	echo '<input type="button" disabled onclick="'.$module.'_grid.paginateJump(\'last\');" value="'. $C_translate->translate("grid_last") .'" id="'.$module.'_grid_pageinate_last" />';		
}
 
?>