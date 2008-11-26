<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_menu_product_subscription
 * Purpose:  Autoselect for all active non-hosting subscriptions
 * -------------------------------------------------------------
 */
function smarty_function_html_menu_product_subscription($params, &$smarty)
{	 	 	
	$id = $params['id'];
	$name = $params['name'];
	$default = unserialize($params['default']);
	$size = $params['size']; 
	$exclude = $params['exclude']; 
	if(empty($id)) $id = $name;
	 	  
	$db = &DB();
	$p = AGILE_DB_PREFIX;
	$q = "SELECT id,sku FROM {$p}product
	          WHERE 
	          ( host = 0 OR host IS NULL )
	          AND active = 1  
	          AND price_type = 1
	          AND id != $exclude
	          AND site_id = " . DEFAULT_SITE;   
	$result = $db->Execute($q);
	if($result && $result->RecordCount() > 0)
	{
		echo "<select id=\"$id\" name=\"{$name}[]\" size=\"$size\"  multiple>";
		while(!$result->EOF) {
			$sel='';
			foreach($default as $cur) if ($cur == $result->fields['id']) $sel = "selected";
			echo "<option value=\"{$result->fields['id']}\"$sel>{$result->fields['sku']}</option>";
			$result->MoveNext();
		}
		echo "</select>";
	}			 
} 
?>