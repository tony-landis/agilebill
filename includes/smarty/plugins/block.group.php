<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.group.php
 * Type:     block
 * Name:     group
 * Purpose:  display content to authenticated groups only
 * -------------------------------------------------------------
 */
function smarty_block_group($params, $resource, &$smarty)
{
    if(empty($resource)) return;

    @$id = $params["id"];
    $do  = false;
    $db  = &DB();
    $sql = 'SELECT status,group_avail FROM ' . AGILE_DB_PREFIX . 'htaccess WHERE
            site_id     = ' . $db->qstr(DEFAULT_SITE) . ' AND
            status      = ' . $db->qstr('1') . ' AND
            id          = ' . $db->qstr($id);
    @$result = $db->Execute($sql);
	$do = false;
    if(@$result->RecordCount() > 0) {
        global $C_auth;
        @$arr = unserialize($result->fields['group_avail']);
        for($i=0; $i<count($arr); $i++)
            if($do == false && $C_auth->auth_group_by_id($arr[$i]))
                $do = true;
    }
  
    if($do) {
	    echo $resource;
    } else  {
        echo @$params["msg"];
    }
}
?>