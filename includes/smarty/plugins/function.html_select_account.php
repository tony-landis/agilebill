<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_select_account
 * Purpose:  Autoselect for accounts in AgileBill
 * -------------------------------------------------------------
 */
function smarty_function_html_select_account($params, &$smarty)
{ 	
	$id = $params['id'];
	$name = $params['name'];
	$default = $params['default'];
	$type = $params['type']; 
	if(empty($id)) $id = $name."_id";
	
	if(!empty($default)) {
		$db = &DB();
		$p = AGILE_DB_PREFIX;
		$q = "SELECT id,first_name,middle_name,last_name  FROM  {$p}account  
	          WHERE id = {$default} 
	          AND site_id = " . DEFAULT_SITE;   
		$result = $db->Execute($q);
		if($result->RecordCount() > 0)
		$val = $result->fields['first_name'].' '.$result->fields['last_name'];
		
		if(!empty($val)) {
			# Get 
			
		}
	} else {
		$val = '';
	}

	if(empty($val))  {  
		echo '   	
		<input type="hidden" id="'.$id.'_hidden" name="'.$name.'" value="'.$default.'" />
		<input type="text" autocomplete="off" id="'.$id.'" name="account_search" size="35" value="'.$val.'" /> 
		<div class="auto_complete" id="'.$id.'_auto_complete"></div>  
		<script type="text/javascript">new Ajax.Autocompleter("'.$id.'", "'.$id.'_auto_complete", "ajax.php?do[]=account_admin:autoselect", { })</script>
		';	 
	} else {
		echo "<a href=\"#\" onClick=\"window.open('?_page=account_admin:view&id={$default}', 'mainFrame', '')\"><u>{$val}</u></a>";
		echo "<input value=\"{$default}\" id=\"{$id}\" name=\"{$name}\" type=\"hidden\">";
	}
} 
?>