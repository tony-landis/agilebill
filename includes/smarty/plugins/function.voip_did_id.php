<?php

/*
 * Smarty plugin
 * -------------------------------------------------------------
 * Type:     function
 * Name:     voip_did_id
 * Purpose:  get the voip_did_id associated with a service id
 * -------------------------------------------------------------
 */
function smarty_function_voip_did_id($params, &$smarty)
{
    extract($params);

    if (empty($service_id)) {
        $smarty->trigger_error("voip_did_id: attribute 'service_id' required");
        return false;
    }

    $db =& DB();
    $rs = $db->Execute(sqlSelect($db, "service", "prod_attr_cart", "id=::".$service_id."::"));
    $prod_attr_cart = unserialize($rs->fields['prod_attr_cart']);
    $did = "";
    if (!empty($prod_attr_cart['station']))
    	$did = $prod_attr_cart['station'];
    if (!empty($prod_attr_cart['ported']))
    	$did = $prod_attr_cart['ported'];
    
    if(substr($did,0,1) == "1") {
    	$did = "(did=::".$did.":: OR did=::".substr($did,1)."::)";
    } else {
    	$did = "did=::".$did."::";
    }
    $rs = $db->Execute($sql=sqlSelect($db, "voip_did", "id", $did));
    return $rs->fields[0];
}

?>
