<?php
/*

AgileBill - Base class for all PDF invoice generation
(C) 2006 Agileco LLC. All rights reserved.

File orginally written by Joseph Benden on 1/9/2006

$Id:$

*/

class pdf_invoice_base extends fpdi
{
	var $iteration;
	var $show_service_range=false;
	
    function load_setup(&$rs)
    {
    	if(!$rs) {
    		$db =& DB(); 
    		$rs = $db->Execute(sqlSelect($db,"setup_invoice","*",""));
    	}
    	$this->billToCompany = $rs->fields['bill_to_company'];
    	$this->invoiceCurrency = $rs->fields['invoice_currency'];
    	$this->invoiceDecimals = $rs->fields['invoice_decimals'];
    	$this->itemsSummaryMax = $rs->fields['items_summary_max'];
    	$this->news = $rs->fields['news'];
    	$this->pageType = $rs->fields['page_type'];
    	$this->show_itemized = $rs->fields['invoice_show_itemized'];
    	$this->show_service_range = $rs->fields['invoice_show_service_dates'];
    	$this->contact_us_url = $rs->fields['contact_us_url'];
    	$this->contact_us_phone = $rs->fields['contact_us_phone'];
    	#$this->currency;
    	$this->currency = $rs->fields['invoice_currency'];
    }
    
	function drawCustom() {  
	} 

    function getTemplate() {
    	return PATH_INCLUDES."pdf/invoice.pdf";
    }

    function drawCompanyLogo() {
    }
    
    function drawCompanyAddress() {
    }
    
    function drawAccountMailing() {
    }
    
    function drawAccountId() {
    }
    
    function drawAccountName() {
    }
    
    function drawAccountUsername() {
    }
    
    function drawAccountAddress() {
    }
    
    function drawInvoiceNo() {
    }
    
	function drawInvoiceCreatedDate() {
	}
	
	function drawInvoiceRange() { 	
	}	
	
	function drawInvoiceDueDate() {
	}
	
	function drawInvoiceTotalAmt() {
	}
	
	function drawInvoicePaidAmt() {
	}

	function drawInvoiceDueAmt() {
	}
	
	function drawInvoiceDiscountAmt() { 
	} 

	function drawInvoiceDueNotice() {
	}
	
	function drawInvoicePaidNotice() {
	}
	
	function drawInvoiceTaxAmt() {
	}
	
	function drawInvoiceShippingAmt() {
	}

	/**
	 * Actual second plus pages of details. This is the constructor.
	 */
	function drawLineItems_pre($iteration) {
	}
	
	/**
	 * This is called for each line item on the second plus pages of details.
	 */
	function drawLineItems(&$db, &$line) {
	}
	
	/**
	 * Draws the summary on the first page
	 */
	function drawSummaryLineItems($items) {
	}
	
	/**
	 * Assigns the invoice fields to this object.
	 */
	function setInvoiceFields($flds) {
		$this->invoice = $flds;
	}
	
	/**
	 * Assigns the account fields to this object.
	 */
	function setAccountFields($flds) {
		$this->account = $flds;	
	}
	
	/**
	 * Assigns the item summary fields to this object.
	 */
	function setItemsSummary($items) {
		$this->itemsSummary = $items;
	}
	
	function setItemsFull($items) {
		$this->itemsFull = $items;
	}
	
	function setDateRange($periodStart, $periodEnd) {
		$this->dateRange = date(UNIX_DATE_FORMAT, $periodStart) . ' - ' . date(UNIX_DATE_FORMAT, $periodEnd);
	}
	
	function setCurrency($currency) {
		$this->invoiceCurrency = $currency;
	}
	
	function setDecimals($decimals) {
		$this->invoiceDecimals = $decimals;
	}

	function setLateFeeNotice() {
	} 	
	
	function setDueAmt($amt) {
		$this->invoiceDueAmt=$amt;
	}
	
	function setNetTerms($terms) {
		$this->netTerms=$terms;
	}	
	
	function _currency($num) {
		global $C_list;
		if($this->invoiceDecimals>3)
			return $this->invoiceCurrency . number_format($num, $this->invoiceDecimals);
		else 
			return $C_list->format_currency_num($num, $this->invoice['actual_billed_currency_id']);
	}

		
	function _putpages() {
	    $nb=$this->page;
	    if(!empty($this->AliasNbPages))
	    {
	        //Replace number of pages
	        for($n=1;$n<=$nb;$n++)
	            $this->pages[$n]=($this->compress) ? gzcompress(str_replace($this->AliasNbPages,$nb,gzuncompress($this->pages[$n]))) : str_replace($this->AliasNbPages,$nb,$this->pages[$n]) ;
	    }
	    if($this->DefOrientation=='P')
	    {
	        $wPt=$this->fwPt;
	        $hPt=$this->fhPt;
	    }
	    else
	    {
	        $wPt=$this->fhPt;
	        $hPt=$this->fwPt;
	    }
	    $filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	    for($n=1;$n<=$nb;$n++)
	    {
	        //Page
	        $this->_newobj();
	        $this->_out('<</Type /Page');
	        $this->_out('/Parent 1 0 R');
	        if(isset($this->OrientationChanges[$n]))
	            $this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$hPt,$wPt));
	        $this->_out('/Resources 2 0 R');
	        if(isset($this->PageLinks[$n]))
	        {
	            //Links
	            $annots='/Annots [';
	            foreach($this->PageLinks[$n] as $pl)
	            {
	                $rect=sprintf('%.2f %.2f %.2f %.2f',$pl[0],$pl[1],$pl[0]+$pl[2],$pl[1]-$pl[3]);
	                $annots.='<</Type /Annot /Subtype /Link /Rect ['.$rect.'] /Border [0 0 0] ';
	                if(is_string($pl[4]))
	                    $annots.='/A <</S /URI /URI '.$this->_textstring($pl[4]).'>>>>';
	                else
	                {
	                    $l=$this->links[$pl[4]];
	                    $h=isset($this->OrientationChanges[$l[0]]) ? $wPt : $hPt;
	                    $annots.=sprintf('/Dest [%d 0 R /XYZ 0 %.2f null]>>',1+2*$l[0],$h-$l[1]*$this->k);
	                }
	            }
	            $this->_out($annots.']');
	        }
	        $this->_out('/Contents '.($this->n+1).' 0 R>>');
	        $this->_out('endobj');
	        //Page content
	        $this->_newobj();
	        $this->_out('<<'.$filter.'/Length '.strlen($this->pages[$n]).'>>');
	        $this->_putstream($this->pages[$n]);
	        $this->_out('endobj');
	    }
	    //Pages root
	    $this->offsets[1]=strlen($this->buffer);
	    $this->_out('1 0 obj');
	    $this->_out('<</Type /Pages');
	    $kids='/Kids [';
	    for($i=0;$i<$nb;$i++)
	        $kids.=(3+2*$i).' 0 R ';
	    $this->_out($kids.']');
	    $this->_out('/Count '.$nb);
	    $this->_out(sprintf('/MediaBox [0 0 %.2f %.2f]',$wPt,$hPt));
	    $this->_out('>>');
	    $this->_out('endobj');
	}
	
	function _endpage()	{
	    //End of page contents
	    $this->pages[$this->page] = ($this->compress) ? gzcompress($this->pages[$this->page]) : $this->pages[$this->page];
	    $this->state=1;
	}	
	
}

?>