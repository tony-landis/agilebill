<?php
/*

AgileBill - Default PDF invoice with summary detail
(C) 2006 Agileco LLC. All rights reserved.

$Id:$

*/
require_once PATH_INCLUDES.'pdf/invoice_base.inc.php';

class pdf_invoice_overview extends pdf_invoice_base
{
	var $billToCompany = true;
	var $invoiceDueAmt = 0; 
	var $invoiceCurrency = '$';
	var $invoiceDecimals = '2'; 
	var $itemsSummary;
	var $itemsSummaryMax=6;
	var $itemsFull;
    var $news = '';
    var $v, $i, $y;
    var $pageType = 1;
    var $show_itemized = 1;
    
	// draw the logo
	function drawCompanyLogo() {
		$x = 10;
		$y = 10;
		$width = 50;
		if(is_file(PATH_THEMES.'default/images/invoice_logo.jpg'))
		$this->Image(PATH_THEMES.'default/images/invoice_logo.jpg',$x,$y,$width);  
	}
	
	// draw the company address
	function drawCompanyAddress() {  
		// add the company address on the payment stub (1) 
		$this->SetFont('times','B', 10);
		$this->SetXY(18,202);		$this->Cell(50,10, $this->companyName);				 
		$this->SetXY(18,206);		$this->Cell(50,10, $this->companyAddress);  
		$this->SetXY(18,210);		$this->Cell(50,10, $this->companyCity. ", ". $this->companyState . " " . $this->companyZip);	
	}
	
	function drawAccountMailing() {
		// add the company address on the payment stub (2) 
		$this->SetFont('times','B', 10);

		if($this->billToCompany && !empty($this->account['company'])) 
		$name = $this->account['company']; 
		else 
		$name = $this->account['first_name'].' '.$this->account['last_name'];
	 
		$this->SetXY(120,240); 		$this->Cell(10,10, html_entity_decode($name,ENT_NOQUOTES)); 		 
		$this->SetXY(120,244);		$this->Cell(50,10, $this->account['address1'] .' '. $this->account['address2']);  
		$this->SetXY(120,248);		$this->Cell(50,10, $this->account['city'] . ", ". $this->account['state'] . " " . $this->account['zip']);		
	}
	
	function drawAccountId() {
		$this->SetFont('times','B',11);
		$this->SetXY(28, 39.5);
		$this->Cell(10,10, $this->account['id']);		// add to top of invoice 
					
	}
	
	function drawAccountName() {
		$this->SetFont('times','B',11);
		$this->SetXY(36, 44); 
		if($this->billToCompany && !empty($this->account['company']))
		$this->Cell(10,10, html_entity_decode($this->account['company'],ENT_NOQUOTES));	
		else
		$this->Cell(10,10, html_entity_decode($this->account['first_name'].' '.$this->account['last_name'],ENT_NOQUOTES));	
	} 
	
	function drawAccountUsername() {
		$this->SetFont('times','B',11);
		$this->SetXY(161, 44);
		$this->Cell(10,10, $this->account['username']);	  
	}
	
	function drawAccountAddress() {
		$this->SetFont('times','B',11);
	}
	
	function drawInvoiceNo() { 		
		$this->SetFont('times','',11); 
		$this->SetXY(180, 222);
		$this->Cell(10,10, $this->invoice["id"]); // draw at the bottom of invoice 		
	}
	
	function drawInvoiceRange() {
		if($this->show_service_range) {
			global $C_translate;
			$this->SetFont('times','B',11); 
			$this->SetXY(8,85.5); 	 
			$this->Cell(50,10, $C_translate->translate('pdf_service_range_month','setup_invoice') . " {$this->dateRange}");	 	
		}
	}
	
	function drawInvoiceDueDate() {
		$this->SetFont('times','B',11);
		$this->SetXY(161, 39.5);
		$this->Cell(10,10, date(UNIX_DATE_FORMAT, $this->invoice['due_date']));			// draw at top of invoice
		
		$this->SetFont('times','',11); 
		$this->SetXY(151, 222);
		$this->Cell(10,10, date(UNIX_DATE_FORMAT, $this->invoice['due_date']),0,0,'C'); // draw at the bottom of invoice 			
	}
	
	function drawInvoiceTotalAmt() {  
		$this->SetXY(147.5,72);
		$this->Cell(10,10, $this->_currency($this->invoice['total_amt']),0,0,'C');		// draw at the top 
	}
	
	function drawInvoicePaidAmt() {
		$this->SetFont('times','',11); 
		$this->SetXY(47,72);
		$this->Cell(10,10, $this->_currency($this->invoice['billed_amt']), 0,0,'C');
	}
	
	function drawInvoiceDueAmt() {
		$this->SetFont('times','',11);
		
		$this->SetXY(114,72);
		$this->Cell(10,10, $this->_currency($this->invoiceDueAmt),0,0,'C');		// draw at the top

		$this->SetXY(181,72);
		$this->Cell(10,10, $this->_currency($this->invoiceDueAmt),0,0,'C');		// draw at the top
				
		$this->SetXY(123, 222);
		$this->Cell(10,10, $this->_currency($this->invoiceDueAmt),0,0,'C');		// draw at the bottom		
	}
	
	function drawInvoiceDiscountAmt() { 
		$this->SetXY(80.5, 72); 	
		$this->Cell(10,10, $this->_currency($this->invoice['discount_amt']), 0,0,'C');
	} 

	function drawInvoiceTaxAmt() {
		$this->SetFont('times','',11); 
		$this->SetXY(16,72);
		$this->Cell(10,10, $this->_currency($this->invoice['tax_amt']),0,0,'C');		// draw at the top		
	}
	
	/**
	 * Called before begining to loop the invoice_item table. Used to set initial values.
	 */
	function drawLineItems_pre($iteration) {
		$this->iteration = $iteration;
		if($iteration>0)
			return false;
		$this->i = 0;
		$this->y = 0;	
		return true;	
	}

	/**
	 * Called once per line item to add to the PDF invoice.
	 */
	function drawSubLineItems(&$db, $line) {
		global $C_translate;
		if ($this->i == 0 || $this->i%51 == 0) {
			$this->AddPage();

			$this->SetFont('times','B',12);
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_itemized_charges','setup_invoice'));
			$this->Cell(0,0,$C_translate->translate('pdf_page','setup_invoice').$this->PageNo(),0,0,'R');
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_invoice_number_small','setup_invoice').$this->invoice['id'],0,0,'C');
			
			# Draw table headers
			$this->SetFont('times','B',8);
			$this->SetXY(9,20);
			$this->Cell(0,0,$C_translate->translate('pdf_item_description','setup_invoice'));
			$this->SetX(145);
			$this->Cell(0,0,$C_translate->translate('pdf_item_quantity','setup_invoice'));
			$this->SetX(170);
			$this->Cell(10,0,$C_translate->translate('pdf_item_cost','setup_invoice'),0,0,'R');
			$this->SetX(145);
			$this->Cell(0,0,$C_translate->translate('pdf_item_amount','setup_invoice'),0,0,'R');
			$this->Line(9,21,200,21);
			$this->y = 24;
			$this->SetY($this->y);
		}

		$this->SetFont('times','',8);
		$this->SetX(18);	
		$this->Cell(0,0, $line);

		$this->y += 5; 
		$this->SetY($this->y);
		$this->i++;
	}
		
	/**
	 * Called once per line item to add to the PDF invoice.
	 */
	function drawLineItems(&$db, &$line) {
		global $C_translate;
		if ($this->i == 0 || $this->i%51 == 0) {
			$this->AddPage();

			$this->SetFont('times','B',12);
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_itemized_charges','setup_invoice'));
			$this->Cell(0,0,$C_translate->translate('pdf_page','setup_invoice').$this->PageNo(),0,0,'R');
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_invoice_number_small','setup_invoice').$this->invoice['id'],0,0,'C');
			
			# Draw table headers
			$this->SetFont('times','B',8);
			$this->SetXY(9,20);
			$this->Cell(0,0,$C_translate->translate('pdf_item_description','setup_invoice'));
			$this->SetX(145);
			$this->Cell(0,0,$C_translate->translate('pdf_item_quantity','setup_invoice'));
			$this->SetX(170);
			$this->Cell(10,0,$C_translate->translate('pdf_item_cost','setup_invoice'),0,0,'R');
			$this->SetX(145);
			$this->Cell(0,0,$C_translate->translate('pdf_item_amount','setup_invoice'),0,0,'R');
			$this->Line(9,21,200,21);
			$this->y = 24;
			$this->SetY($this->y);
		}

		$this->SetFont('times','',8);
		$this->SetX(9);	
		$this->Cell(0,0, $line['name']);
		$this->SetX(170);
		$this->Cell(10,0, $this->_currency($line['amount']/$line['qty']),0,0,'R');
		$this->SetX(145);
		$this->Cell(10,0, $line['qty'],0,0,'R');
		$this->SetX(145); 	
		$this->Cell(0,0, $this->_currency($line['amount']), 0,0,'R');
		$this->y += 5; 
		$this->SetY($this->y);
		$this->i++;
		
		# Draw attributes if they are present
		if (strlen($line['attr'])) {
			$atrs = preg_split("/\r\n/", str_replace('\r\n',"\r\n",$line['attr']));
			foreach ($atrs as $a) {
				$parts = preg_split("/==/", $a);
				switch ($parts[0]) {
					default:
						if(strlen($parts[0]))
							$this->drawSubLineItems($db, $parts[0].": ".$parts[1]);
						break;
				}
			}			
		}
	}
	
	function drawSummaryLineItems($items) {
		global $C_translate;
		if (!$this->show_itemized) return;
		
		$y = 105;
		$this->SetY($y);
		$this->SetFont('times','',11);

		$i=0;
		if(is_array($items)) {
			foreach($items as $line) {
				$val = $line['name'];
				$this->SetX(9);
				$this->Cell(0,0, $val);
				$this->SetX(145);
				$this->Cell(0,0, $this->_currency($line['amount']), 0,0,'R');
				$y += 5;
				$this->SetY($y);
				$i++;
				if($i > $this->itemsSummaryMax) {
					$this->SetFont('times','B',11);
					$this->SetX(9);
					$this->Cell(0,0,$C_translate->translate('pdf_summary','setup_invoice'));
					break;
				}
			}
		}	 
	}
}
?>		