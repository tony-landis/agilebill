<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu_sub
 * Purpose:  Creates a html menu for associated records w/sub-record support
 * -------------------------------------------------------------
 */
function smarty_function_html_menu_sub($params, &$smarty)
{	 	
	$conditions='';
	$actions='';
	extract($params);  
	if(empty($field)) $field = $name;	
	if(empty($id)) $select_id = $field; else $select_id = $id;
	if(empty($parent_id)) $parent_id = 'parent_id';
	if(!empty($onchange)) $actions .= " onchange=\"$onchange\" ";
	 	 
	$db	= &DB(); 
	$result	= $db->Execute( sqlSelect($db,$assoc_table,"id,$parent_id,".$assoc_field, $conditions, "$parent_id,$assoc_field"));
 
	# Get current id
	if(!empty($VAR['id'])) $cid = ereg_replace(",","", $VAR['id']); else $current = '';
	  
	# Loop and put in array
	while(!$result->EOF) {
		if($result->fields["$parent_id"] == "" || $result->fields["$parent_id"] == 0 || $result->fields["$parent_id"] == $result->fields['id']) {
			$arr[0][] = $result->fields;
		} else {
			$arr["{$result->fields["$parent_id"]}"][] = $result->fields;
		}

		# get current parent_id
		if($cid > 0 && $result->fields['id'] == $cid)
		$current = $result->fields["$parent_id"]; 
		$result->MoveNext();
	}
 
	$option = '';
	$dirpre = ' \\ ';

	for($i=0; $i<count($arr[0]); $i++) {
		$id = $arr[0][$i]["id"]; 
		if($id == $current) $sel = 'selected'; else $sel = ''; 
		$dir = $dirpre.$dir.$arr[0][$i]["$assoc_field"]; 
		$option .= '<option value="'.$id.'" '.$sel.'>'.$dir.'</option>';
 
		# get the sub-records # (LEVEL 2)
		if(isset($arr[$id])) {
			for($ii=0; $ii<count($arr[$id]); $ii++) {
				$idx = $arr[$id][$ii]["id"];
				if($idx == $current) $sel = 'selected'; else $sel = '';
				$dir .= $dirpre.$arr[$id][$ii]["$assoc_field"];
				$option .= '<option value="'.$idx.'" '.$sel.'>'.$dir.'</option>';
			}
 
			# get the sub-records # (LEVEL 3)
			if(isset($arr[$idx])) {
				for($iii=0; $iii<count($arr[$idx]); $iii++) {
					$idx2 = $arr[$idx][$iii]["id"];
					if($idx2 == $current) $sel = 'selected'; else $sel = '';
					$dir .= $dirpre.$arr[$idx][$iii]["$assoc_field"];
					$option .= '<option value="'.$idx2.'" '.$sel.'>'.$dir.'</option>';
				}
 
				# get the sub-records # (LEVEL 4)
				if(isset($arr[$idx2])) {
					for($iiii=0; $iiii<count($arr[$idx2]); $iiii++) {
						$idx3 = $arr[$idx2][$iiii]["id"];
						if($idx3 == $current) $sel = 'selected'; else $sel = '';
						$dir .= $dirpre.$arr[$idx2][$iiii]["$assoc_field"];
						$option .= '<option value="'.$idx3.'" '.$sel.'>'.$dir.'</option>';
					}
 
					# get the sub-records # (LEVEL 5)
					if(isset($arr[$idx3])) {
						for($iiiii=0; $iiiii<count($arr[$idx3]); $iiiii++) {
							$idx4 = $arr[$idx3][$iiiii]["id"];
							if($idx4 == $current) $sel = 'selected'; else $sel = '';
							$dir .= $dirpre.$arr[$idx3][$iiiii]["$assoc_field"];
							$option .= '<option value="'.$idx4.'" '.$sel.'>'.$dir.'</option>';
						}
					}
				}
			}
		}
	}

	echo "<select id=\"$select_id\" name=\"$field\"$actions\>";
	echo "<option value=\"0\">\\</option>";
	echo $option;
	echo '</select>';   		
} 
?>