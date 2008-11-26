<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu
 * Purpose:  Get creates a html menu for associated records
 * -------------------------------------------------------------
 */
function smarty_function_html_menu($params, &$smarty)
{	 	
	$conditions='';
	extract($params);  
	if(empty($field)) $field = $name;	
	if(empty($id)) $id = $field;
	
	$db = &DB();
	$rs = & $db->Execute( $sql = sqlSelect($db, $assoc_table, "id,".$assoc_field, $conditions, $assoc_field));
	 	 
	#echo $sql;
	
	$return = '<select id="'.$id.'" name="'. $field .'">';
	if($default == "all" || $blank) $return .= '<option value="" selected></option>'; 
	if($rs && $rs->RecordCount() > 0) 
	{
		while(!$rs->EOF) 
		{ 
			$return .= '<option value="' . $rs->fields['id'] . '"';
			if($default == $rs->fields['id']) $return .= "selected";
			$return .= '>' . $rs->fields["$assoc_field"] . '</option>';	
			$rs->MoveNext();			
		}
	} else {
		if( $default != "all") $return .= '<option value=""></option>';
	}  
	
	$return .= '</select>';
	echo $return;
} 
?>