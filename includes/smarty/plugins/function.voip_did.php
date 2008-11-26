<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     voip_did
 * Purpose:  get the DID associated with a service id
 * -------------------------------------------------------------
 */
function smarty_function_voip_did($params, &$smarty)
{
    extract($params);

    if (empty($service_id)) {
        $smarty->trigger_error("voip_did: attribute 'service_id' required");
        return false;
    }

    $db =& DB();
    $rs = $db->Execute(sqlSelect($db, "service", "prod_attr_cart", "id=::".$service_id."::"));
    $prod_attr_cart = unserialize($rs->fields['prod_attr_cart']);
    if (!empty($prod_attr_cart['station']))
    	return $prod_attr_cart['station'];
    if (!empty($prod_attr_cart['ported']))
    	return $prod_attr_cart['ported'];
}

?>
