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
	 			
require_once(PATH_FPDF . '/fpdf.php');

/**
* FPDF Handler Class
* 
* This class handles the conversion to PDF format for printing, etc. 
*/ 
class CORE_fpdf
{

	/**
	* Opens the FPDF class and gets ready for data conversion...
	* 
	* @return 	void
	* @since 	Version 1.0
	*/

	function CORE_fpdf()
	{
	}



	/**
	* FPDF Table Class
	* 
	* This class handles the of tables to PDF, for searches, lists, reports, etc.
	*  
	* @version 1.0 Beta, 2003/06/07
	* @package Core
	*/
}

class PDF_MC_Table extends FPDF
{
	var $widths;

	function SetWidths($w)
	{
		//Set the array of column widths
		$this->widths=$w;
	}

	function Header()
	{
		//Select Arial bold 15
		$this->SetFont('Arial','I',12);
		//Move to the right
		$this->Cell(190,10,"Search Results (translate)",'0','0','C');
		//Line break
		$this->Ln(10);
	}	

	function Footer()
	{
		//Go to 1.5 cm from bottom right
		$this->SetXY(-20,-15);
		//Select Arial italic 8
		$this->SetFont('Arial','I',8);
		//Print page number
		$this->Cell(0,10,'Page '.$this->PageNo(),0,0,'C');
		//Go to 1.5 cm from bottom left
		$this->SetXY(10,-15);		
		//Select Arial italic 8
		$this->SetFont('Arial','I',8);		
		//Print date
		$this->Cell(0,10,date("m-d-Y"),0,0,'L');
	}


	function HeadRow($data)
	{
		// Set the fill color
		#$this->SetDrawColor(0,0,0);	
		$this->SetFillColor($this->HeadFillColor1,$this->HeadFillColor2,$this->HeadFillColor3);
		$this->SetTextColor($this->HeadTextColor1,$this->HeadTextColor2,$this->HeadTextColor3);
		$this->SetFont($this->HeadFontFamily,$this->HeadFontStyle,$this->HeadFontSize);

		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=$this->HeadHeight*$nb;

		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h);
			//Print the text
			$this->MultiCell($w,$this->HeadHeight,$data[$i],1,'L',1);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}



	function Row($data)
	{
		// Set the fill color
		#$this->SetDrawColor(0,0,0);	
		$this->SetFillColor($this->SetFillColorVar1,$this->SetFillColorVar2,$this->SetFillColorVar3);
		$this->SetTextColor($this->SetTextColorVar1,$this->SetTextColorVar2,$this->SetTextColorVar3);
		$this->SetFont($this->SetFontFamily,$this->SetFontStyle,$this->SetFontSize);

		//Calculate the height of the row
		$nb=0;
		for($i=0;$i<count($data);$i++)
			$nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
		$h=$this->RowHeight*$nb;

		//Draw the cells of the row
		for($i=0;$i<count($data);$i++)
		{
			$w=$this->widths[$i];
			//Save the current position
			$x=$this->GetX();
			$y=$this->GetY();
			//Draw the border
			$this->Rect($x,$y,$w,$h);
			//Print the text
			$this->MultiCell($w,$this->RowHeight,$data[$i],1,'L',1);
			//Put the position to the right of the cell
			$this->SetXY($x+$w,$y);
		}
		//Go to the next line
		$this->Ln($h);
	}

	function CheckPageBreak($h)
	{
		//If the height h would cause an overflow, add a new page immediately
		if($this->GetY()+$h>$this->PageBreakTrigger)
		{
			$this->AddPage($this->CurOrientation);
			return TRUE;
		}

	}

	function NbLines($w,$txt)
	{
		//Computes the number of lines a MultiCell of width w will take
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 and $s[$nb-1]=="\n")
			$nb--;
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$nl=1;
		while($i<$nb)
		{
			$c=$s[$i];
			if($c=="\n")
			{
				$i++;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
				continue;
			}
			if($c==' ')
				$sep=$i;
			$l+=$cw[$c];
			if($l>$wmax)
			{
				if($sep==-1)
				{
					if($i==$j)
						$i++;
				}
				else
					$i=$sep+1;
				$sep=-1;
				$j=$i;
				$l=0;
				$nl++;
			}
			else
				$i++;
		}
		return $nl;
	}
}
?>