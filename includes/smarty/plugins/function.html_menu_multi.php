<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu_multi
 * Purpose:  Get creates a html menu for associated records (multi-select)
 * -------------------------------------------------------------
 */
function smarty_function_html_menu_multi($params, &$smarty)
{	 	
	$conditions='';
	extract($params);  
	if(empty($field)) $field = $name;
	if(empty($id)) $id = $field;
	if(empty($size)) $size = '4';
  
	
	
	$db = &DB();
	$rs = & $db->Execute( $sql = sqlSelect($db, $assoc_table, "id,".$assoc_field, $conditions, $assoc_field));
	 	 
	if(empty($default)) 			$default = Array('');
	elseif (is_array($default)) 	$default = $default;
	elseif (is_numeric($default)) 	$default[] = $default;
	elseif (is_string($default)) 	$default = unserialize($default);
	else 							$default = Array('');
			    	
	
	if($default == "all") $return .= '<option value="" selected></option>'; 
	$i=0;
	if($rs && $rs->RecordCount() > 0)  {
		while(!$rs->EOF)  { 
			$return .= '<option value="' . $rs->fields['id'] . '"';
			foreach($default as $def) {	if($def == $rs->fields["id"]) $return .= " selected"; break; }
			$return .= '>' . $rs->fields["$assoc_field"] . '</option>';	
			$i++;
			$rs->MoveNext();			
		}
	} else {
		if( $default != "all") $return .= '<option value=""></option>';
	}
	
	$return .= '</select>';
	
	if($i < $size) $size = $i++;
	echo '<select id="'.$id.'" name="'. $field .'[]" size="'.$size.'" value="" multiple>' .  $return; 
	 
} 
?>