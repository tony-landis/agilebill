<?php
/*

AgileVoice - Detailed PDF invoice with CDR detail
(C) 2006 Agileco LLC. All rights reserved.

$Id:$

*/
require_once PATH_INCLUDES.'pdf/invoice_base.inc.php';

class pdf_invoice_overview extends pdf_invoice_base
{
	var $billToCompany = true;
	var $invoiceDueAmt = 0; 
	var $invoiceCurrency = '$';
	var $invoiceDecimals = '5'; 
	var $itemsSummary;
	var $itemsSummaryMax=16;
	var $itemsFull;
    var $news = '';
    var $v, $i, $y;
    var $pageType = 2;
    var $show_itemized = 1;
    
    function getTemplate() {
    	if ($this->pageType == 2)
    		return PATH_INCLUDES."pdf/invoice2.pdf";
    	return PATH_INCLUDES."pdf/invoice1.pdf";
    }

	// draw the logo
	function drawCompanyLogo() {
		$x = 9;
		$y = 7;
		$width = 50;
		if(is_file(PATH_THEMES.DEFAULT_THEME.'/images/invoice_logo.png'))
		$this->Image(PATH_THEMES.DEFAULT_THEME.'/images/invoice_logo.png',$x,$y,$width);  
	}
	
	// draw the company address
	function drawCompanyAddress() {  
		global $C_translate;
		// add the company address on the payment stub (1) 
		$this->SetFont('arial','B', 13);
		$this->SetXY(18,202);		$this->Cell(0,0,$C_translate->translate('pdf_payment_coupon','setup_invoice'));
		$this->SetFont('arial','', 8);		
		$this->SetXY(18,206);		$this->Cell(0,0,$C_translate->translate('pdf_return1','setup_invoice'));
		$this->SetXY(18,209);		$this->Cell(0,0,$C_translate->translate('pdf_return2','setup_invoice').$this->companyName);
		
		$this->SetFont('arial','', 10);
		$x = 18; $y = 216;
		if ($this->pageType == 2) {		
			$y = 230;		
		}
		$this->SetXY(18,$y);		$this->Cell(0,0, $this->companyName); $y += 4;
		$this->SetXY(18,$y);		$this->Cell(0,0, $this->companyAddress);  $y += 4;
		$this->SetXY(18,$y);		$this->Cell(0,0, $this->companyCity. ", ". $this->companyState . " " . $this->companyZip);	$y += 4;
	}
	
	function drawAccountMailing() {
		// add the company address on the payment stub (2) 
		$this->SetFont('arial','B', 10);

		if($this->billToCompany && !empty($this->account['company'])) 
			$name = $this->account['company']; 
		else 
			$name = $this->account['first_name'].' '.$this->account['last_name'];
	 
		$x = 110; $y = 248;
		if ($this->pageType == 2) {
			$x = 18; $y = 268;
		}
		$this->SetXY($x,$y); 		$this->Cell(0,0, html_entity_decode($name,ENT_NOQUOTES)); $y += 4;
		$this->SetXY($x,$y);		$this->Cell(0,0, $this->account['address1'] .' '. $this->account['address2']);  $y += 4;
		$this->SetXY($x,$y);		$this->Cell(0,0, $this->account['city'] . ", ". $this->account['state'] . " " . $this->account['zip']);	$y += 4;
	}
	
	function drawAccountId() {
		global $C_translate;
		$this->SetFont('arial','',11); 
		$this->SetXY(110, 205); $this->Cell(0,0,$C_translate->translate('pdf_account_number','setup_invoice'));
		$this->SetXY(150, 205);
		$this->Cell(0,0, $this->account['id']);		// add to bottom of invoice
					
	}
	
	function drawAccountUsername() {
		global $C_translate;
		$this->SetFont('arial','B',11);
		$this->SetXY(95, 18); $this->Cell(0,0,$C_translate->translate('pdf_account_username','setup_invoice'));
		$this->SetXY(201,18); $this->Cell(0,0,$this->account['username'],0,0,'R');

		$this->SetFont('arial','',11);
		$this->SetXY(95,30);
		$contact = $C_translate->translate('pdf_contact','setup_invoice')."\n";
		$contact .= $C_translate->translate('pdf_contact_online','setup_invoice').$this->contact_us_url."\n";
		$contact .= $C_translate->translate('pdf_contact_phone','setup_invoice').$this->contact_us_phone;		
		$this->MultiCell(0,4,$contact); 
		
		$this->SetXY(9,170);
		$this->MultiCell(0, 4, str_replace('\n',"\n",$this->news)); 
	}
	
	function drawAccountAddress() {
		$this->SetFont('times','B',11);
	}
	
	function drawInvoiceNo() { 
		global $C_translate;
		$this->SetFont('arial','B',11); 
		$this->SetXY(95, 14); $this->Cell(0,0,$C_translate->translate('pdf_invoice_number','setup_invoice'));
		$this->SetXY(201, 14);	$this->Cell(0,0, $this->invoice['id'],0,0,'R');		// add to bottom of invoice		
		
		$this->SetFont('arial','',11); 
		$this->SetXY(110, 210); $this->Cell(0,0,$C_translate->translate('pdf_invoice_number','setup_invoice'));
		$this->SetXY(150, 210);	$this->Cell(0,0, $this->invoice['id']);		// add to bottom of invoice				
	}
	
	function drawInvoiceDueDate() {
		global $C_translate;
		$this->SetFont('arial','B',11);
		$this->SetXY(95,10); $this->Cell(0,0,$C_translate->translate('pdf_billing_date','setup_invoice'));
		$this->SetXY(201,10); $this->Cell(0,0, date(UNIX_DATE_FORMAT, $this->invoice['due_date']),0,0,'R');			// draw at top of invoice
		
		$this->SetFont('arial','',11); 
		$this->SetXY(110, 200); $this->Cell(0,0,$C_translate->translate('pdf_bill_date','setup_invoice'));
		$this->SetXY(150, 200);
		$this->Cell(0,0, date(UNIX_DATE_FORMAT, $this->invoice['due_date'])); // draw at the bottom of invoice 			
	}
	
	function drawInvoiceTotalAmt() {
		global $C_translate;
		$this->SetFont('arial','B',11); 
		$this->SetXY(95, 22); $this->Cell(0,0,$C_translate->translate('pdf_current_charges','setup_invoice'));
		$this->SetXY(201, 22);	$this->Cell(0,0, $this->_currency($this->invoice['total_amt']),0,0,'R');		// draw at the top 
		
		$this->SetFont('arial','',9); 
		$this->SetXY(110, 222); $this->Cell(0,0,$C_translate->translate('pdf_current_charges','setup_invoice'));
		$this->SetXY(201, 222);	$this->Cell(0,0, $this->_currency($this->invoice['total_amt']),0,0,'R');		// draw at the top 
	}
	
	function drawInvoiceDueAmt() {
		global $C_translate;
		$this->SetFont('times','',11);
		
		$this->SetFont('arial','',9); 
		$this->SetXY(110, 226); $this->Cell(0,0,$C_translate->translate('pdf_amount_due_by','setup_invoice').date(UNIX_DATE_FORMAT, $this->invoice['due_date']));
		$this->SetXY(201, 226);	$this->Cell(0,0, $this->_currency($this->invoiceDueAmt),0,0,'R');
		
		$this->SetXY(110, 230); $this->Cell(0,0,$C_translate->translate('pdf_make_check','setup_invoice'));
		$this->SetXY(110, 234); $this->Cell(0,0,$this->companyName);
	}
	
	/**
	 * Called before begining to loop the invoice_item table. Used to set initial values.
	 */
	function drawLineItems_pre($iteration) {
		$this->iteration = $iteration;
		if($iteration>0)
			return false;
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$this->v = new voip;
		$this->i = 0;
		$this->y = 0;
		return true;	
	}
	
	/**
	 * Called once per line item to add to the PDF invoice.
	 */
	function drawLineItems(&$db, &$line) {
		global $C_translate;
		if ($this->i == 0 || $this->i%51 == 0) {
			$this->AddPage();

			$this->SetFont('arial','B',12);
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_itemized_calls','setup_invoice'));
			$this->Cell(0,0,$C_translate->translate('pdf_page','setup_invoice').$this->PageNo(),0,0,'R');
			$this->SetXY(3,10); $this->Cell(0,0,$C_translate->translate('pdf_invoice_number_small','setup_invoice').$this->invoice['id'],0,0,'C');
			
			# Draw table headers
			$this->SetFont('arial','B',8);
			$this->SetXY(9,20);
			$this->Cell(0,0,$C_translate->translate('pdf_item_from','setup_invoice'));
			$this->SetX(75);
			$this->Cell(0,0,$C_translate->translate('pdf_item_to','setup_invoice'));
			$this->SetX(160);
			$this->Cell(10,0,$C_translate->translate('pdf_item_min','setup_invoice'),0,0,'R');
			$this->SetX(145);
			$this->Cell(0,0,$C_translate->translate('pdf_item_amount','setup_invoice'),0,0,'R');
			$this->Line(9,21,200,21);
			$this->y = 24;
			$this->SetY($this->y);
		}

		if ($line['price_type'] != 0) {
			$this->SetFont('arial','I',8);
		} else {
			$this->SetFont('arial','',8);
		}			

		$val = $line['name'];
		if (strlen($line['attr'])) {
			$val = "";
			$atrs = preg_split("/\r\n/", str_replace('\r\n',"\r\n",$line['attr']));
			foreach ($atrs as $a) {
				$parts = preg_split("/==/", $a);
				switch ($parts[0]) {
					case "Destination":
						$this->SetX(75);
						$this->Cell(0,0,$parts[1]);
						$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
						if ($this->v->e164($parts[1], $e164, $cc, $npa, $nxx)) {
							$this->SetX(115);
							$this->Cell(0,0,substr($this->v->where_is($db, $cc, $npa, $nxx), 0, 20));
						}													
						break;
					case "Source":
						$this->SetX(9);
						$this->Cell(0,0,$parts[1]);
						$cc = ""; $npa = ""; $nxx = ""; $e164 = "";
						if ($this->v->e164($parts[1], $e164, $cc, $npa, $nxx)) {
							$this->SetX(39);
							$this->Cell(0,0,substr($this->v->where_is($db, $cc, $npa, $nxx), 0, 20));
						}	
						break;
					case "parent_service_id":
						$sql = sqlSelect($db,"service","prod_attr","id=::".$parts[1]."::");
						$rstmp = $db->Execute($sql);
						$atrs2 = preg_split("/\r\n/", $rstmp->fields['prod_attr']);
						foreach ($atrs2 as $a2) {
							$parts2 = preg_split("/==/", $a2);
							switch ($parts2[0]) {
								case "station":
								case "ported":
									$val = $line['name']." for ".$parts2[1];
									break;
								default:
									break;
							}
						}
						break;
					case "station":
					case "ported":
						$val = $line['name']." for ".$parts[1];
						break;
					default:
						break;
				}
			}
		}

		$this->SetX(9);	
		$this->Cell(0,0, $val);
		if ($line['price_type'] == 0) {
			$this->SetX(160);
			$this->Cell(10,0, $line['qty']." M",0,0,'R');
		} else {
			$q = $line['qty'];
			if(empty($q)) $q = 1;
			$this->SetX(160);
			$this->Cell(10,0, $line['qty'],0,0,'R');
		}
		$this->SetX(145); 	
		$this->Cell(0,0, $this->_currency($line['total_amt']), 0,0,'R');
		$this->y += 5; 
		$this->SetY($this->y);
		$this->i++;
	}
	
	function drawSummaryLineItems($items) {
		global $C_translate;
		if (!$this->show_itemized) return;
		
		# Include the voip class
		include_once(PATH_MODULES.'voip/voip.inc.php');
		$v = new voip;
		$db = &DB();
		
		$this->SetFont('arial','B',11);
		$y = 70;
		$this->SetY($y);	 
		
		$this->SetX(9); $this->Cell(0,0,$C_translate->translate('pdf_cur_charge_summary','setup_invoice').$this->dateRange);
		$y += 5;
		$this->SetY($y);
		
		$this->SetFont('arial','',9);

		$i=0;
		if(is_array($items)) {
			foreach($items as $line) {
				$val = $line['name'];
				$this->SetX(9);	
				if (@$line['item_type'] == 5) {
					$val = $line['quantity'].$C_translate->translate('pdf_combine_minutes','setup_invoice');
				}
				$q = $line['quantity'];
				if(empty($q)) $q = 1;
				$this->Cell(0,0, $q);
				$this->SetX(18);
				$this->Cell(0,0, $val);
				$this->SetX(145); 	
				$this->Cell(0,0, $this->_currency($line['amount']), 0,0,'R');
				$y += 5; 
				$this->SetY($y);
				$i++;
				if($i > $this->itemsSummaryMax) {
					$this->SetFont('arial','B',11);
					$this->SetX(9);
					$this->Cell(0,0,$C_translate->translate('pdf_summary','setup_invoice'));	
					break;
				}
			}
		}
	}
}

?>