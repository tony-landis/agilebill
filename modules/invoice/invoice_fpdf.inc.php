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
	
require_once(PATH_INCLUDES.'pdf/fpdf.php');
define('EURO', chr(128) );
define('EURO_VAL', 6.55957 );
 
class INVOICE_FPDF extends FPDF
{
	// private variables
	var $colonnes;
	var $format;
	var $angle=0; 

	// private functions
	function RoundedRect($x, $y, $w, $h, $r, $style = '')
	{
		$k = $this->k;
		$hp = $this->h;
		if($style=='F')
			$op='f';
		elseif($style=='FD' or $style=='DF')
			$op='B';
		else
			$op='S';
		$MyArc = 4/3 * (sqrt(2) - 1);
		$this->_out(sprintf('%.2f %.2f m',($x+$r)*$k,($hp-$y)*$k ));
		$xc = $x+$w-$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l', $xc*$k,($hp-$y)*$k ));
	
		$this->_Arc($xc + $r*$MyArc, $yc - $r, $xc + $r, $yc - $r*$MyArc, $xc + $r, $yc);
		$xc = $x+$w-$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l',($x+$w)*$k,($hp-$yc)*$k));
		$this->_Arc($xc + $r, $yc + $r*$MyArc, $xc + $r*$MyArc, $yc + $r, $xc, $yc + $r);
		$xc = $x+$r ;
		$yc = $y+$h-$r;
		$this->_out(sprintf('%.2f %.2f l',$xc*$k,($hp-($y+$h))*$k));
		$this->_Arc($xc - $r*$MyArc, $yc + $r, $xc - $r, $yc + $r*$MyArc, $xc - $r, $yc);
		$xc = $x+$r ;
		$yc = $y+$r;
		$this->_out(sprintf('%.2f %.2f l',($x)*$k,($hp-$yc)*$k ));
		$this->_Arc($xc - $r, $yc - $r*$MyArc, $xc - $r*$MyArc, $yc - $r, $xc, $yc - $r);
		$this->_out($op);
	}
	
	function _Arc($x1, $y1, $x2, $y2, $x3, $y3)
	{
		$h = $this->h;
		$this->_out(sprintf('%.2f %.2f %.2f %.2f %.2f %.2f c ', $x1*$this->k, ($h-$y1)*$this->k,
							$x2*$this->k, ($h-$y2)*$this->k, $x3*$this->k, ($h-$y3)*$this->k));
	}
	
	function Rotate($angle,$x=-1,$y=-1)
	{
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0)
		{
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}
	
	function _endpage()
	{
		if($this->angle!=0)
		{
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
	
	// public functions
	function sizeOfText( $texte, $largeur )
	{
		$index    = 0;
		$nb_lines = 0;
		$loop     = TRUE;
		while ( $loop )
		{
			$pos = strpos($texte, "\n");
			if (!$pos)
			{
				$loop  = FALSE;
				$ligne = $texte;
			}
			else
			{
				$ligne  = substr( $texte, $index, $pos);
				$texte = substr( $texte, $pos+1 );
			}
			$length = floor( $this->GetStringWidth( $ligne ) );
			//$res = 1 + floor( $length / $largeur) ;
			$nb_lines += $res;
		}
		return $nb_lines;
	}
	
	// Company
	function addCompany( $nom, $adresse )
	{
		$this->Image(PATH_THEMES.'default/images/invoice_logo.jpg',11,8,50); 
		$x1 = 10;
		$y1 = 20; 
		$this->SetXY( $x1, $y1 );
		$this->SetFont('Arial','B',12);
		$length = $this->GetStringWidth( $nom );
		$this->Cell( $length, 2, $nom);
		$this->SetXY( $x1, $y1 + 4 );
		$this->SetFont('Arial','',10);
		$length = $this->GetStringWidth( $adresse ); 
		$lignes = $this->sizeOfText( $adresse, $length) ;
		$this->MultiCell($length, 4, $adresse);
	}
	  
	function addDate( $date )
	{
		global $C_translate;
		
		$r1  = $this->w - 40;
		$r2  = $r1 + 30;
		$y1  = 8;
		$y2  = $y1 ;
		$mid = $y1 + ($y2 / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
		$this->SetFont( "Helvetica", "B", 10);
		$this->Cell(10,5, $C_translate->translate('pdf_date','invoice',''), 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+4 );
		$this->SetFont( "Helvetica", "", 10);
		$this->Cell(10,5,$date, 0,0, "C");
	}
	 
	function addPageNumber( $page )
	{
		global $C_translate;
		
		$r1  = $this->w - 59;
		$r2  = $r1 + 19;
		$y1  = 8;
		$y2  = $y1;
		$mid = $y1 + ($y2 / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), $y2, 3.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1 );
		$this->SetFont( "Helvetica", "B", 10);
		$this->Cell(10,5, $C_translate->translate('pdf_page','invoice',''), 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5, $y1+4 );
		$this->SetFont( "Helvetica", "", 10);
		$this->Cell(10,5,$page, 0,0, "C");
	}
	
	// Client address
	function addClient( $adresse )
	{
		$r1     = 140;
		$r2     = $r1 + 68;
		$y1     = 35;
		$this->SetXY( $r1, $y1);
		$this->MultiCell( 60, 4, $adresse);
	}
	 
	// Invoice number
	function addInvoiceNo($id)
	{ 
		global $C_translate;
		
		$r1  = 10;
		$r2  = $r1 + 60;
		$y1  = 65;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 - 20 , $y1+1 );
		$this->SetFont( "Helvetica", "B", 10);
		$this->Cell(40, 4, $C_translate->translate('pdf_invoice_number','invoice',''), 0, 0, "C");
		$this->SetFont( "Helvetica", "", 10);
		$this->SetXY( $r1 + 9 , $y1+5 );
		$this->Cell(40, 5, $id, '', '', "C");
	}
	 
	// Payment Status
	function addStatus( $mode )
	{
		global $C_translate;
		
		$r1  = 75;
		$r2  = $r1 + 60;
		$y1  = 65;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1+1 );
		$this->SetFont( "Helvetica", "B", 10);
		$this->Cell(10,4, $C_translate->translate('pdf_payment_status','invoice',''), 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 -5 , $y1 + 5 );
		$this->SetFont( "Helvetica", "", 10);
		$this->Cell(10,5,$mode, 0,0, "C");
	}
	
	// Due date
	function addDueDate( $date )
	{
		global $C_translate;
		
		$r1  = 140;
		$r2  = $r1 + 60;
		$y1  = 65;
		$y2  = $y1+10;
		$mid = $y1 + (($y2-$y1) / 2);
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1, $mid, $r2, $mid);
		$this->SetXY( $r1 + ($r2 - $r1)/2 - 5 , $y1+1 );
		$this->SetFont( "Helvetica", "B", 10);
		$this->Cell(10,4, $C_translate->translate('pdf_date_due','invoice',''), 0, 0, "C");
		$this->SetXY( $r1 + ($r2-$r1)/2 - 5 , $y1 + 5 );
		$this->SetFont( "Helvetica", "", 10);
		$this->Cell(10,5,$date, 0,0, "C");
	}
	 
	function addCols( $tab )
	{
		global $colonnes;
		
		$r1  = 10;
		$r2  = $this->w - ($r1 * 2) ;
		$y1  = 80;
		$y2  = $this->h - 50 - $y1;
		$this->SetXY( $r1, $y1 );
		$this->Rect( $r1, $y1, $r2, $y2, "D");
		$this->Line( $r1, $y1+6, $r1+$r2, $y1+6);
		$colX = $r1;
		$colonnes = $tab;
		while ( list( $lib, $pos ) = each ($tab) )
		{
			$this->SetXY( $colX, $y1+2 );
			$this->Cell( $pos, 1, $lib, 0, 0, "C");
			$colX += $pos;
			$this->Line( $colX, $y1, $colX, $y1+$y2);
		}
	}
	
	function addLineFormat( $tab )
	{
		global $format, $colonnes;
		
		while ( list( $lib, $pos ) = each ($colonnes) )
		{
			if ( isset( $tab["$lib"] ) )
				$format[ $lib ] = $tab["$lib"];
		}
	}
	
	function lineVert( $tab )
	{
		global $colonnes;
	
		reset( $colonnes );
		$maxSize=0;
		while ( list( $lib, $pos ) = each ($colonnes) )
		{
			$texte = $tab[ $lib ];
			$longCell  = $pos -2;
			$size = $this->sizeOfText( $texte, $longCell );
			if ($size > $maxSize)
				$maxSize = $size;
		}
		return $maxSize;
	}
	 
	function addLine( $ligne, $tab )
	{
		global $colonnes, $format;
	
		$ordonnee     = 10;
		$maxSize      = $ligne;
	
		reset( $colonnes );
		while ( list( $lib, $pos ) = each ($colonnes) )
		{
			$longCell  = $pos -2;
			$texte     = $tab[ $lib ];
			$length    = $this->GetStringWidth( $texte );
			$tailleTexte = $this->sizeOfText( $texte, $length );
			$formText  = $format[ $lib ];
			$this->SetXY( $ordonnee, $ligne-1);
			$this->MultiCell( $longCell, 4 , $texte, 0, $formText);
			if ( $maxSize < ($this->GetY()  ) )
				$maxSize = $this->GetY() ;
			$ordonnee += $pos;
		}
		return ( $maxSize - $ligne );
	} 
	 
	function addTotals($a, $b, $a_total, $a_paid, $a_due, $b_total, $b_paid, $b_due, $a_disc, $b_disc)
	{
		global $C_translate;
		
		$r1  = $this->w - 102;
		$r2  = $r1 + 92;
		$y1  = $this->h - 45;
		$y2  = $y1+25;
		$this->RoundedRect($r1, $y1, ($r2 - $r1), ($y2-$y1), 2.5, 'D');
		$this->Line( $r1+20,  $y1, $r1+20, $y2); // avant EUROS
		$this->Line( $r1+20, $y1+4, $r2, $y1+4); // Sous Euros & Francs
		$this->Line( $r1+55,  $y1, $r1+55, $y2); // Entre Euros & Francs
		$this->SetFont( "Arial", "B", 8); 
		$this->SetXY( $r1+30, $y1 );
		$this->Cell(15,4, $a, 0, 0, "C"); 
		$this->SetFont( "Arial", "", 8);
		$this->SetXY( $r1+65, $y1 ); 
		$this->Cell(15,4, $b, 0, 0, "C");
		$this->SetFont( "Arial", "B", 6);
		
		$this->SetXY( $r1, $y1+5 );
		$this->Cell(20,4, $C_translate->translate('pdf_discounts','invoice',''), 0, 0, "C"); 
		$this->SetXY( $r1, $y1+10 );
		$this->Cell(20,4, $C_translate->translate('pdf_total','invoice',''), 0, 0, "C");
		$this->SetXY( $r1, $y1+15 );
		$this->Cell(20,4, $C_translate->translate('pdf_paid','invoice',''), 0, 0, "C");
		$this->SetXY( $r1, $y1+20 );
		$this->Cell(20,4, $C_translate->translate('pdf_due','invoice',''), 0, 0, "C");
		
		$re  = $this->w - 65;
		$rf  = $this->w - 28;
		$y1  = $this->h - 40;
		
		// A total
		$this->SetFont( "Arial", "", 8);
		$this->SetXY( $re, $y1+0 );
		$this->Cell( 17,4, $a_disc, '', '', 'R'); 		
		$this->SetXY( $re, $y1+5 );
		$this->Cell( 17,4, $a_total, '', '', 'R'); 
		$this->SetXY( $re, $y1+10 );
		$this->Cell( 17,4, $a_paid, '', '', 'R');
		$this->SetXY( $re, $y1+15 );
		$this->Cell( 17,4, $a_due, '', '', 'R');
		$this->SetXY( $rf, $y1+5 );
		
		// B Total
		if(!empty($b)) {
			$this->Cell( 17,4, $b_total, '', '', 'R');
			$this->SetXY( $rf, $y1+10 );
			$this->Cell( 17,4, $b_paid, '', '', 'R');
			$this->SetXY( $rf, $y1+14.8 );
			$this->Cell( 17,4, $b_due, '', '', 'R');
		} 	
	}
	   
	// watermark
	function addWatermark( $text )
	{
		$this->SetFont('Arial','B',50);
		$this->SetTextColor(203,203,203);
		$this->Rotate(0);
		$this->Text(10,50,$text);
		$this->Rotate(0);
		$this->SetTextColor(0,0,0);
	} 
}
?>