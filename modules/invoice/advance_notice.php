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
* Advance invoice e-mail notification class
*/
class advance_notice
{
	var $advance_days=false;
	var $advance_days_service;
	
	function task() {
		 
		$db=&DB();
		
		/* get the max invoice days from the setup_invoice table */
		if(!$this->advance_days) {
			$setup = $db->Execute(sqlSelect($db,"setup_invoice","advance_notice","advance_notice>0 and advance_notice != '' and advance_notice is not null"));
			if(!$setup->RecordCount()) return false; 
			 $this->advance_days = $setup->fields['advance_notice'];
		}

		/* pre-billing days global setting */
		if(MAX_INV_GEN_PERIOD <= 0) $max_days = $max_date=1; else $max_days = MAX_INV_GEN_PERIOD;

		/* pre-notification date for service */
		$this->advance_days_service = $this->advance_days + $max_days;
		$max_date = time()+($this->advance_days_service*86400);
		date("Y-m-d", $max_date);

		$p=AGILE_DB_PREFIX; $s=DEFAULT_SITE;
		$ids=false;
		$account=false;
		$date=false;
		$invoice=false;
		$sql = "SELECT DISTINCT service.id as serviceId, account.id as accountId, invoice.id as invoiceId, from_unixtime(service.date_next_invoice,'%Y-%m-%d') as dayGroup
					FROM {$p}service as service 
					JOIN {$p}account as account ON ( service.account_id=account.id and account.site_id={$s} )
					LEFT JOIN {$p}invoice as invoice ON ( service.invoice_id=invoice.id and invoice.site_id={$s} )
					WHERE service.site_id={$s} 
					AND service.active = 1 
					AND ( service.invoice_advance_notified IS NULL OR service.invoice_advance_notified = 0 )
					AND ( service.suspend_billing IS NULL OR service.suspend_billing = 0 )  
					AND ( service.date_next_invoice > 0 AND service.date_next_invoice IS NOT NULL )
					AND  
					((
					    ( account.invoice_advance_gen!='' OR account.invoice_advance_gen is not null ) AND service.date_next_invoice <= ((86400*(account.invoice_advance_gen+{$this->advance_days})) + (UNIX_TIMESTAMP(CURDATE())))
					 ) OR (
					    ( account.invoice_advance_gen='' OR account.invoice_advance_gen is null ) AND service.date_next_invoice <= {$max_date}
					))
					ORDER BY accountId, dayGroup, serviceId";  
		$rs = $db->Execute($sql);
		if($rs === false) {global $C_debug; $C_debug->error('advance_notice.inc.php','task()', $sql . " \r\n\r\n " . @$db->ErrorMsg()); }
		if($rs && $rs->RecordCount()) {
			while(!$rs->EOF) {
				if( $ids && ($rs->fields['accountId'] != $account ) || ($rs->fields['dayGroup'] != $date) ) {
					$this->sendEmail($ids, $account, $date);
					$ids=false;
				}

				// set the current account and date
				$account=$rs->fields['accountId'];
				$invoice=$rs->fields['invoiceId'];
				$date=$rs->fields['dayGroup'];

				// add to id list
				if($ids) $ids.=",";
				$ids.=$rs->fields['serviceId'];
				$rs->MoveNext();
			}
			if($ids) $this->sendEmail($ids, $account, $date);
		}
	}
	
	
	/* send e-mail to user with pre-billing notice */
	function sendEmail($ids, $account, $date) { 
		if (empty($account)) return;
		
		//echo "<br> $account - $ids - $date";
		 
		/* send e-mail to user */ 
		include_once(PATH_MODULES.'email_template/email_template.inc.php');
		$mail = new email_template; 
		$mail->send('invoice_pregen_notice',  $account, $ids, DEFAULT_CURRENCY, $date);		
		
		/* update service.invoice_advance_notified=1 to stop future notifications */ 
		$db=&DB();
		$db->Execute("UPDATE ".AGILE_DB_PREFIX."service SET invoice_advance_notified=1 WHERE site_id=".DEFAULT_SITE." AND id in ($ids)");		
	}
}
?>
