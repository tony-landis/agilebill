<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     grid_column_refine  
 * Purpose:  display grid columns for refine search
 * -------------------------------------------------------------
 */
function smarty_function_grid_column_refine($params, &$smarty)
{
	extract($params);
	
	if(empty($expr)) $expr = 'LIKE';
		
	echo '<input type="hidden" name="'.$module.'[conditions]['.$column.'][exp][]" value="'.$expr.'" />';
	echo '<input type="hidden" name="'.$module.'[conditions]['.$column.'][col][]" value="'.$column.'" />';
	
	if($column == 'id')
		echo '<input type="text" name="'.$module.'[conditions]['.$column.'][val][]" onclick="this.value=\'\'" size="4" />';
	else 
		echo '<input type="text" name="'.$module.'[conditions]['.$column.'][val][]" onclick="this.value=\'\'" />';
}
 
?>