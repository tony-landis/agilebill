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
 * This module creates a printable pdf reciept on manual invoice reconciliation suitable for retail printing.
 * To enable, simply change "var $active=false;" to "var $active=true" below.
 * When an invoice is reconciled from the admin menu, a PDF will be displayed inline, and you can use the print
 * feature in the inline acrobat reader to print.
 */

require_once(PATH_INCLUDES.'pdf/fpdf.php');
 
class receipt_print extends FPDF
{
	var $active=false;
	var $save_path = '';
	
	function add(&$invoiceRs, $payment_amt, $paid_amt) {
		
		if(!$this->active) return false; 
		
		ob_start();
		
		$this->AddPage();		
		$this->SetFont('Arial','B',10);
		
		$this->drawCompanyLogo();
		$this->drawCompanyAddress();
		 
		$this->SetXY(0,50);
		$this->Cell(50,10,"*** INVOICE# {$invoiceRs->fields['id']} ***");
	 
		$this->SetXY(0,60);
		$this->Cell(50,10,"DATE ".date("d/m/Y D H:i"));
		
		$this->SetXY(0,70); $this->Cell(50,5,"INVOICE TOTAL: ");
		$this->SetXY(45,70); $this->Cell(50,5, number_format($invoiceRs->fields['total_amt'],2));
		
		$this->SetXY(0,75);  $this->Cell(50,5,"PREV AMT DUE: " );	 
		$this->SetXY(45,75); $this->Cell(50,5, number_format($invoiceRs->fields['total_amt']-$invoiceRs->fields['billed_amt'],2));	 
		
		$this->SetXY(0,80);  $this->Cell(50,5,"CURRENT PAYMENT: " ); 
		$this->SetXY(45,80); $this->Cell(50,5, $payment_amt); 
		
		$this->SetXY(0,85);  $this->Cell(50,5,"PAID TO DATE: " );	
		$this->SetXY(45,85); $this->Cell(50,5, number_format($paid_amt,2));	
				
		$this->SetXY(0,90);  $this->Cell(50,5,"CURRENY AMT DUE: " );
		$this->SetXY(45,90); $this->Cell(50,5, number_format($invoiceRs->fields['total_amt']-$paid_amt,2));
					
		$this->SetXY(0,100);  $this->Cell(50,5,"Thank You!" );
		
		$this->Output();
		
		ob_flush();
	}
	
	
	// draw the logo
	function drawCompanyLogo() { 
		$width = 50;
		if(is_file(PATH_THEMES.'default/images/invoice_logo.jpg'))
		$this->Image(PATH_THEMES.'default/images/invoice_logo.jpg',0,0,$width);  
	}
	
	// draw the company address
	function drawCompanyAddress() {  
		// add the company address on the payment stub (1) 
		$this->SetFont('times','B', 10);
		$this->SetXY(0,30);		$this->Cell(50,10, SITE_NAME);				 
		$this->SetXY(0,35);		$this->Cell(50,10, SITE_ADDRESS);  
		$this->SetXY(0,40);		$this->Cell(50,10, SITE_CITY. ", ". SITE_STATE . " " . SITE_ZIP);	
	} 	
} 
?>