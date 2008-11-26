<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     db_lookup
 * Purpose:  get a field value from the database for a given id
 * -------------------------------------------------------------
 */
function smarty_function_db_lookup($params, &$smarty)
{
    extract($params);

    if (empty($id)) {
        $smarty->trigger_error("db_lookup: attribute 'id' required");
        return false;
    }
    if (empty($table)) {
        $smarty->trigger_error("db_lookup: attribute 'table' required");
        return false;
    }
    if (empty($field)) {
        $smarty->trigger_error("db_lookup: attribute 'field' required");
        return false;
    }    
    
    $db =& DB();
    $rs = $db->Execute(sqlSelect($db, $table, $field, "id=::".$id."::"));
   	return $rs->fields[0];
}

?>
