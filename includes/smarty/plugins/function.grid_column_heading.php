<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     grid_column_heading  
 * Purpose:  display grid column heading
 * -------------------------------------------------------------
 */
function smarty_function_grid_column_heading($params, &$smarty)
{
	extract($params);
	
	if($column == '_checkbox') {
		echo '<input type="checkbox" id="'.$module.'_check_main" onclick="'.$module.'_grid.switchCheck(this.checked)" />';
		return;
	}
	
	echo '<span style="float:right;display:none;overflow:hidden" id="'.$module.'_grid_'.$column.'_asc"><img src="images/asc.gif" alt="ASC" /></span>';
	echo '<span style="float:right;display:none;overflow:hidden" id="'.$module.'_grid_'.$column.'_desc"><img src="images/desc.gif" alt="DESC" /></span>';

	global $C_translate; 
	
	if($column == 'id')
	echo '<span class="nobr"><?smarty:translate?>'.$C_translate->translate("id").'<?smarty:/translate?></span>';
	else 
	echo '<span class="nobr"><?smarty:translate module='.$module.'?>'.$C_translate->translate("field_".$column,$module).'<?smarty:/translate?></span>';
}
 
?>
