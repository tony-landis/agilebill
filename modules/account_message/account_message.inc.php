<?php
	
/**
 * AgileBill - Open Billing Software
 *
 * This body of work is free software; you can redistribute it and/or
 * modify it under the terms of the Open AgileBill License
 * License as published at http://www.agileco.com/agilebill/license1-4.txt
 * 
 * For questions, help, comments, discussion, etc., please join the
 * Agileco community forums at http://forum.agileco.com/ 
 *
 * @link http://www.agileco.com/
 * @copyright 2004-2008 Agileco, LLC.
 * @license http://www.agileco.com/agilebill/license1-4.txt
 * @author Tony Landis <tony@agileco.com> 
 * @package AgileBill
 * @version 1.4.93
 */

/**

Sample use in tpl:

{if $list->is_installed('account_message')}
{$method->exe_noauth('account_message','view')}
<h2>System message on {$date}</h2>
<p>{$message|nl2br}</p>
{/if}

*/
class account_message
{
	/** update system message */
	function add($VAR) {
		@$m=$VAR['message']; 
		$db=&DB();
		$fields=Array('date_orig'=>time(), 'message'=>$m);
		$db->Execute(sqlUpdate($db,"account_message",$fields,"id = 1"));
	}
	
	/** view system message */
	function view($VAR) {
		if(!SESS_LOGGED) return false; 
		$db=&DB();
		$rs = $db->Execute(sqlSelect($db,"account_message","date_orig,message","id=1"));
		if($rs && $rs->RecordCount()) {
			global $smarty;	
			$smarty->assign('message', $rs->fields['message']);
			$smarty->assign('date', date("m-d-Y", $rs->fields['date_orig']));
		}
	}
}
?>