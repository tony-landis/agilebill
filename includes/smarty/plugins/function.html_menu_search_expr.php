<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu_search_expr
 * Purpose:  display search form expression menu
 * -------------------------------------------------------------
 */
function smarty_function_html_menu_search_expr($params, &$smarty)
{
	extract($params); 
	 
	// Types: exact, date, dateex, number, fulltext, text (default)
	 	
	switch($type) {
		case 'exact':
			$options = Array('EQ', 'NULL', 'NNULL');
		break;
		
		case 'date':
			$options = Array('GTEQ', 'LTEQ', 'GT', 'LT');
		break;
		
		case 'dateex':
			$options = Array('EQ', 'GTEQ', 'LTEQ', 'GT', 'LT');
		break;
		
		case 'number':
			$options = Array('EQ', 'GTEQ', 'LTEQ', 'GT', 'LT', 'NULL', 'NNULL');
		break;
				
		case 'fulltext':
			$options = Array('FT');
		break;
		
		case 'text':
			$options = Array('LIKE','NLIKE', 'EQ', 'NOT', 'NULL', 'NNULL');
		break;
	}
	
	if(empty($options)) $options = Array('LIKE','NLIKE', 'EQ', 'NOT', 'NULL', 'NNULL');
	
	$optionsTxt["EQ"] = 'IS EXACT';
	$optionsTxt["LIKE"] = 'IS LIKE';
	$optionsTxt["NOT"] = 'IS NOT';
	$optionsTxt["NLIKE"] = 'IS NOT LIKE';
	$optionsTxt["GT"] = 'IS &gt;';
	$optionsTxt["LT"] = 'IS &lt;';
	$optionsTxt["GTEQ"] = 'IS &gt;=';
	$optionsTxt["LTEQ"] = 'IS &lt;=';
	$optionsTxt["NULL"] = 'IS NULL';
	$optionsTxt["NNULL"] = 'IS NOT NULL';
	$optionsTxt["FT"] = 'FULL TEXT';
	 
    echo '<select name="'.$module.'[conditions]['.$field.'][exp][]">';
    foreach($options as $opt) echo '<option value="'.$opt.'">'.$optionsTxt["$opt"].'</option>';  
    echo '</select>';
    
    echo '<input type="hidden" name="'.$module.'[conditions]['.$field.'][col][]" value="'.$field.'" />';
 }
?>