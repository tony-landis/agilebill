<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     html_select_affiliate
 * Purpose:  Autoselect for affiliate accounts in AgileBill
 * -------------------------------------------------------------
 */
function smarty_function_html_select_affiliate($params, &$smarty)
{  
	$id = $params['id'];
	$name = $params['name'];
	$default = $params['default'];
	$type = $params['type'];

	if(empty($id)) $id = $name."_id";
	
	if(!empty($default)) {
		$db = &DB();
		$p = AGILE_DB_PREFIX;
		$q = "SELECT DISTINCT
	          {$p}affiliate.id,
	          {$p}account.first_name,
	          {$p}account.last_name,
	          {$p}account.username
	          FROM 
	          {$p}account
	          LEFT JOIN
	          {$p}affiliate              	
	          ON
	          {$p}account.id = {$p}affiliate.account_id
	          WHERE  
	          {$p}affiliate.id = '{$default}' AND
	          {$p}affiliate.site_id = " . DEFAULT_SITE . " AND
	          {$p}account.site_id = " . DEFAULT_SITE;   
		$result = $db->Execute($q);
		if($result != false && $result->RecordCount() > 0) $val = $result->fields['first_name'].' '.$result->fields['last_name'];
	} else {
		$val = '';
	}
	  
	echo '   	
	<input type="hidden" id="'.$id.'_hidden" name="'.$name.'" value="'.$default.'" />
	<input type="text" autocomplete="off" id="'.$id.'" name="affiliate_search" size="35" value="'.$val.'" /> 
	<div class="auto_complete" id="'.$id.'_auto_complete"></div>  
	<script type="text/javascript">new Ajax.Autocompleter("'.$id.'", "'.$id.'_auto_complete", "ajax.php?do[]=affiliate:autoselect", { })</script>
	';
	
	if(!empty($val)) {
		// display unselect option
		echo '<a href="#" OnClick="document.getElementById(\''.$id.'\').value=\'\'; document.getElementById(\''.$id.'_hidden\').value=\'\'"> - </a>'; 
	}
} 
?>