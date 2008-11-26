<?php
/*
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     block.account.php
 * Type:     block
 * Name:     account
 * Purpose:  display an account field
 * -------------------------------------------------------------
 */
function smarty_block_account($params, $resource, &$smarty)
{
    $resource = trim($resource);
    if ($resource != '')
    {
        global $C_list;
        echo $C_list->account($resource);
    }
}
?>