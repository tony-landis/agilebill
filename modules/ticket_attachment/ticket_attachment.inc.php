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
	
class ticket_attachment
{
	function download($VAR) {
		if(empty($VAR['id'])) return false;
		
		$id=$VAR['id'];
		
		// get ticket id
		$db=&DB();
		$rs=$db->Execute(sqlSelect($db, Array("ticket_attachment","ticket"), "A.ticket_id,B.department_id,B.account_id","A.id=::$id:: AND A.ticket_id=B.id"));
		if(!$rs || $rs->RecordCount()==0) return false;
		
		// is this an admin?
		global $C_auth;
		if($C_auth->auth_method_by_name("ticket","view")) {
			
			// get the data & type
			$rs=$db->Execute(sqlSelect($db, "ticket_attachment", "*", "id=::$id::"));
			
			// set the header
			require_once(PATH_CORE.'file_extensions.inc.php');
			$ft = new file_extensions;
			$type = $ft->set_headers_ext($rs->fields['type'], $rs->fields['name']); 
			  
			if (empty($type)) {
				echo imap_qprint($rs->fields['content']);
			} elseif(eregi("^text", $type)) { 
				echo imap_base64($rs->fields['content']);
			} else {
				echo imap_base64($rs->fields['content']);
			}			
			exit;
		}
	}
}
?>