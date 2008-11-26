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
 * @author Tony Landis <tony@agileco.com> and Thralling Penguin, LLC <http://www.thrallingpenguin.com>
 * @package AgileBill
 * @version 1.4.93
 */
	
class BASE_ReportFormatter {
	var $output_path;
	var $output;
	
	function setOutputDirectory($d)
	{
		$this->output_path = $d;
	}
	
	function getOutput()
	{
		return $this->output;
	}
}

class HTML_ReportFormatter extends BASE_ReportFormatter {
	var $indent = 0;
	var $coldata;
	var $level;
	var $div_count;
	
	function setLevel(&$level)
	{
		$this->level =& $level;
	}
	
	function setIndent($amount)
	{
		if($amount == "") return;
		$this->indent = $amount;
	}
	
	function addRow()
	{
		$this->coldata = array();
		echo " <tr>\n";
	}
	
	function endRow()
	{
		/* output the coldata */
		#if($this->indent) {
		#	echo "  <td width=\"{$this->indent}\">&nbsp;</td>\n";
		#}
		$w = 100 / count($this->coldata);
		foreach($this->coldata as $col) {
			$tag = "td";
			if (is_a($col[1],'ReportStyle')) {
				$s = "style=\"".$col[1]->doHTML()."\"";
				if($col[1]->is_heading) $tag = "th scope=\"col\"";
			} else {
				$s = "";
			}
			
			echo "  <{$tag} {$s} width=\"{$w}%\">\n";
			echo "  {$col[0]}\n";
			echo "  </{$tag}>\n";
		}
		echo " </tr>\n";
	}
	
	function addColumn($col, $style = '')
	{
		/* take a column and a style */
		$this->coldata[] = array($col, $style);
	}
	
	function addTable($style, $heading)
	{
		$i = $this->indent / 30; if($i<0) $i = 0;
		++$i;
		$this->div_count = 0;
		
		if($style != 'report_heading') {
			echo '<div id="level_'.$i.'">';
			$this->div_count += 1;
		}
		if($this->indent == 0) {
			if($style != 'level') {
				echo '<div id="'.$style.'">';
				$this->div_count += 1;
			}
		}
		echo $heading;
		echo '<table width="100%" border="0" cellspacing="0">';
		echo "\n";
	}
	
	function endTable()
	{
		$i = $this->indent / 30; if($i<0) $i = 0;
		++$i;
		echo "</table>\n";
		
		if($this->div_count == 1)
			echo "</div>\n";
		if($this->indent == 0) {
			if($this->div_count == 2)
				echo "</div>\n";
		}
	}
	
	function startDocument($title = '')
	{
		ob_start();
		echo "<html>\n";
		echo "<head>\n";
		echo "<title>{$title}</title>\n";
		echo "<link rel=\"stylesheet\" href=\"".URL."themes/default_admin/report_style.css\" type=\"text/css\">";	
		echo "</head>\n<body>\n";
	}
	
	function endDocument()
	{
		echo "</body>\n</html>\n";
		$content = ob_get_contents();
		ob_end_clean();
		$this->output = $this->output_path."/report.html";
		$fp = fopen($this->output,'w');
		if($fp) {
			fwrite($fp,$content);
			fclose($fp);
		} else {
			echo 'Could not write to output file.';
		}		
	}
	
	function addBreak()
	{
		echo "<br /><br /><br />";
	}

	function insertImage($file,$width,$height)
	{
		$file = basename($file);
		echo "<center><img src=\"$file\" width=\"$width\" height=\"$height\" /></center>\n";
	}	
	
	function write($s)
	{
		echo $s;
	}
}

/**
 * Outputs the report in DOS text format
 */
class TXT_ReportFormatter extends BASE_ReportFormatter {
	var $indent = 0;
	var $aindent;
	var $coldata;
	var $level;
	
	function setLevel(&$level)
	{
		$this->level =& $level;
	}
	
	function setIndent($amount)
	{
		if($amount == "") return;
		$this->indent = $amount;
	}
	
	function addRow()
	{
		$this->coldata = array();
	}
	
	function endRow()
	{
		/* output the coldata */
		if($this->indent) {
			$n = intval($this->indent) / 30;
			echo str_repeat("    ", $n);
		}
		$w = (100 / count($this->coldata))/100 * 80;
		foreach($this->coldata as $col) {
			echo str_pad($col[0], $w);
		}
		echo "\r\n";
	}
	
	function addColumn($col, $style = '')
	{
		/* take a column and a style */
		$this->coldata[] = array($col, $style);
	}
	
	function addTable($style, $heading=false)
	{
		if($this->indent) {
			echo "\r\n";
			$this->aindent = $this->indent;
		} else {
			echo "\r\n\r\n";
		}
	}
	
	function endTable()
	{
		if($this->indent && $this->indent != $this->aindent) echo "\r\n";
	}
	
	function startDocument($title = '')
	{
		ob_start();
	}
	
	function endDocument()
	{
		$content = ob_get_contents();
		ob_end_clean();
		$this->output = $this->output_path."/report.txt";
		$fp = fopen($this->output,'w');
		if($fp) {
			fwrite($fp,$content);
			fclose($fp);
		} else {
			echo 'Could not write to output file.';
		}
	}
	
	function addBreak()
	{
		echo "\r\n\r\n";
	}
	
	function insertImage($file,$width,$height)
	{
		$file = basename($file);
		echo "\r\nSee file: $file\r\n";
	}
	
	function write($s)
	{
		
	}
}

class PDF_ReportFormatter extends BASE_ReportFormatter {
	var $indent = 0;
	var $aindent;
	var $coldata;
	var $pdf;
	var $y;
	var $level;
	
	function write($s)
	{
		
	}
	
	function setIndent($amount)
	{
		if($amount == "") return;
		$this->indent = intval($amount);
	}
	
	function addRow()
	{
		$this->coldata = array();
	}
	
	function endRow()
	{
		$x = 9;
		$n = 0;
		/* output the coldata */
		if($this->indent) {
			$n = $this->indent / 30;
			$x += ($n * 9);
		}
		$w = round((100 / count($this->coldata))/100 * (190 - ($n*9)));
		$tx = $x;
		foreach($this->coldata as $col) {
			if (is_a($col[1],'ReportStyle')) {
				/* set the bounding area */
				$col[1]->setBounds($tx,$this->y,$tx + $w,$this->y + 5);
				$col[1]->doPDF($this->pdf);
			} else {
				$tmp = new ReportStyle;
				$tmp->doPDF($this->pdf);
			}
			#echo "x={$tx} y={$this->y} data={$col[0]} style={$col[1]}<br>";
			$this->pdf->Text($tx, $this->y,$col[0]);
			$tx += $w;
		}
		$this->addY(5, true);
	}
	
	function addY($y, $repeatLabels = false)
	{
		$this->y += $y;
		if ($this->y > 275) {
			$this->pdf->AddPage();
			$this->y = 5;
			if($repeatLabels)
				$this->level->addHeaderLabels();
		}		
	}
	
	function setLevel(&$level)
	{
		$this->level =& $level;
	}
	
	function addColumn($col, $style = '')
	{
		/* take a column and a style */
		$this->coldata[] = array($col, $style);
	}
	
	function addTable($style, $heading='')
	{
		if($this->indent) {
			$this->addY(5);
			$this->aindent = $this->indent;
		} else {
			$this->addY(10);
		}
		
		# if we're close to the end, goto next page
		if($this->y > 250) $this->addY(1000);
	}
	
	function endTable()
	{
		if($this->indent && $this->indent != $this->aindent) $this->addY(5);
	}
	
	function startDocument($title = '')
	{
		require_once(PATH_INCLUDES.'pdf/fpdi.php');
		require_once(PATH_INCLUDES.'pdf/fpdf_tpl.php');
		require_once(PATH_INCLUDES.'pdf/fpdf.php');	
		
		$this->pdf = new fpdi;
		$this->pdf->addPage();
		$this->y = 5;
	}
	
	function endDocument()
	{
		$file = $this->output_path."/report.pdf";
		$this->output = $file;
		$this->pdf->Output($file,'F'); 
	}

	function addBreak()
	{
		$this->addY(10);
	}

	function insertImage($file,$width,$height)
	{
		$w = $width / 11.81102 * 2.5;
		$h = $height / 11.81102 * 2.5;
		$y = $this->y + 5;
		$x = 105 - ($w/2);
		#echo 'Place image at 0,'.$y."  $w x $h<br>";
		$this->pdf->Image($file, $x, $y, $w, $h);
		$this->y = $y + $h + 5;
	}
}

class ReportStyle {
	var $font_weight;
	var $font_family;
	var $font_height;
	var $bg_color;
	var $is_heading;
	var $tx, $ty, $bx, $by;
	
	function ReportStyle()
	{
		$this->font_weight = '';
		$this->font_family = 'times';
		$this->font_height = 10;
		$this->is_heading = false;
	} 
	
	function fontFamily($f)
	{
		$this->font_family = $f;
	}
	
	function fontHeight($h)
	{
		$this->font_height = $h;
	}
	
	function bold()
	{
		$this->font_weight = 'bold';
	}
	
	function backgroundColor($r,$g,$b)
	{
		$this->bg_color = array($r,$g,$b);
	}
	
	function setBounds($x1,$y1,$x2,$y2)
	{
		$this->tx = $x1; $this->ty = $y1;
		$this->bx = $x2; $this->by = $y2;
		#echo "BOUNDS: $x1 x $y1 x $x2 x $y2<br>";
	}
	
	/**
	 * Generates the PDF style
	 */
	function doPDF(&$pdf)
	{
		if(is_array($this->bg_color)) {
			$x1 = $this->tx;
			$y1 = $this->ty - ($this->font_height * 0.352777778);
			$w = $this->bx - $this->tx;
			$h = $this->by - $this->ty;
			$pdf->SetFillColor($this->bg_color[0],$this->bg_color[1],$this->bg_color[2]);
			$pdf->Rect($x1,$y1,$w,$h,'F');
			#echo "BOX: $x1 x $y1 x $w x $h<br>";
		}
		
		$b = ($this->font_weight == 'bold' ? "B" : "");
		
		$pdf->SetFont($this->font_family,$b,$this->font_height);
	}
	
	/**
	 * Generates the HTML style
	 */
	function doHTML()
	{
		$s = "font-family: {$this->font_family}; font-size: {$this->font_height}pt;";
		if($this->font_weight == 'bold')
			$s .= " font-weight: bold;";
		if(is_array($this->bg_color)) {
			$s .= " background-color: #";
			$s .= str_pad(dechex($this->bg_color[0]), 2, '0', STR_PAD_LEFT);
			$s .= str_pad(dechex($this->bg_color[1]), 2, '0', STR_PAD_LEFT);
			$s .= str_pad(dechex($this->bg_color[2]), 2, '0', STR_PAD_LEFT);
			$s .= ";";
		}
		$s="";
		return $s;
	}
}

/**
 * This class is a adaptor pattern that serves to place a break in the formatting of the report.
 */
class Report_BreakAdaptor {
	function display($grouping, $aggregate, &$formatter)
	{
		$formatter->addBreak();
	}
}

require_once PATH_MODULES.'report/class.Level.php';
class Report_DivAdaptor extends Level_Base {
	var $id;
	var $lev_setting;
	var $lev_fields;
	var $lev_items;
	var $indent_html;
	var $SQL_filtered;
	var $group_level;
	var $grouping_criteria;
	var $has_title;
	var $formatter;
		
	function display($grouping, $aggregate, &$formatter)
	{
		$this->formatter =& $formatter;
		#echo "<div id=\"{$this->id}\">";
		if(isset($this->lev_items) && is_array($this->lev_items)) {
			foreach ($this->lev_items as $item) {
				echo "<div id=\"{$this->id}\">";
				$c = $this->formatter->indent;
				$item->display($grouping, $aggregate, $this->formatter);
				$this->formatter->indent = $c;
				echo "</div>";
			}		
		}
		#echo "</div>";
	}
}

class Reporting {	
	var $has_header;
	var $rep_title;
	var $rep_subtitle_1;
	var $rep_subtitle_2;
	var $rep_date_start;
	var $rep_date_end;
	var $rep_image_src;
	var $rep_image_width;
	var $rep_image_height;
	var $rep_image_alt;
	var $rep_desc;
	var $rep_items;
	var $rep_formatter;
	
	function Reporting (&$formatter, $has_header = false)
	{
		$this->has_header = $has_header;
		/* 
		
		TODO: Implement this crap at a later point
		
		
		$this->rep_title = $rep_title;	
		$this->rep_subtitle_1 = $rep_subtitle_1;	
		$this->rep_subtitle_2 = $rep_subtitle_2;	
		$this->rep_date_start = $rep_date_start;	
		$this->rep_date_end = $rep_date_end;	
		$this->rep_desc = $rep_desc;
		$this->rep_image_src = $rep_image_src;	
		$this->rep_image_width = $rep_image_width;	
		$this->rep_image_height = $rep_image_height;	
		$this->rep_image_alt = $rep_image_alt;	
		*/
		$this->rep_formatter =& $formatter;
	}	
	
	function setTitle($t, $style = '')
	{
		if($style=='') $style = new ReportStyle;
		$style->fontHeight(16);
		$style->fontFamily('arial');
		$this->rep_title = array($t,$style);
	}
	
	function setSubtitle1($t, $style = '')
	{
		if($style=='') $style = new ReportStyle;
		$style->fontHeight(14);
		$style->fontFamily('arial');		
		$this->rep_subtitle_1 = array($t,$style);
	}
	
	function setSubtitle2($t, $style = '')
	{
		if($style=='') $style = new ReportStyle;
		$style->fontHeight(12);
		$style->fontFamily('arial');		
		$this->rep_subtitle_2 = array($t,$style);
	}
	
	function addBreak ()
	{
		$this->rep_items[] = new Report_BreakAdaptor;
	}

	function addDiv($id)
	{
		$item = new Report_DivAdaptor;
		$item->id = $id;
		$this->rep_items[] = $item;
	}
	
	function append (&$item)
	{
		$this->rep_items[] =& $item;		
	}
	
	function display ()
	{	
		$this->rep_formatter->startDocument($this->rep_title[0]);
			
		if ($this->has_header) {			
			$this->displayHeader();
		}
		if(is_array($this->rep_items)) {
			foreach ($this->rep_items as $item) {
				$cur = $this->rep_formatter->indent;
				$item->display(Null, Null, $this->rep_formatter);
				$this->rep_formatter->indent = $cur;
			}
		}
		$this->rep_formatter->endDocument();
	}
	
	function displayHeader ()
	{
		if(is_a($this->rep_formatter,'HTML_ReportFormatter')) {
			if ($this->rep_title != '') {
				echo '<h1>'.$this->rep_title[0].'</h1>';
			}
			
			if ($this->rep_subtitle_1 != '') {			
				echo '<h2>'.$this->rep_subtitle_1[0].'</h2>';
			}
		
			if ($this->rep_subtitle_2 != '') {			
				echo '<h3>'.$this->rep_subtitle_2[0].'</h3>';
			}			
				
		} else {
			$this->rep_formatter->addTable('report_heading');
						
			if ($this->rep_image_src != '') {
				
				echo "<td class='rc-rep-image-cell'>";			
				
				$width_html = '';
				$height_html = '';
				$alt_html = '';
				
				if ($this->rep_image_width != '') {				
					$width_html = " width='$this->rep_image_width'";
				}
		
				echo "<td class='rc-rep-image-cell'$width_html>";	
				
				if ($this->rep_image_height != '') {
					$height_html = " height='$this->rep_image_height'";
				}
		
				if ($this->rep_image_alt != '') {				
					$alt_html = " alt='$this->rep_image_alt'";
				}
				
				echo "<img class='rc-rep-image' src='$this->rep_image_src' $width_html $height_html $alt_html > 
				</td>";
			}
			
			$d = "";
			if ($this->rep_title != '') {
				$this->rep_formatter->addRow();
				$this->rep_formatter->addColumn($this->rep_title[0],$this->rep_title[1]);
				$this->rep_formatter->endRow();
			}
			
			if ($this->rep_subtitle_1 != '') {			
				$this->rep_formatter->addRow();
				$this->rep_formatter->addColumn($this->rep_subtitle_1[0],$this->rep_subtitle_1[1]);
				$this->rep_formatter->endRow();
			}
		
			if ($this->rep_subtitle_2 != '') {			
				$this->rep_formatter->addRow();
				$this->rep_formatter->addColumn($this->rep_subtitle_2[0],$this->rep_subtitle_2[1]);
				$this->rep_formatter->endRow();
			}
		
			# $d .= "<h3>Report generated on " . date("d/m/y") . "</h3>";
				
			if ($this->rep_desc != '') {
				$d .= "<h3>$this->rep_desc</h3>";
			}
			
			$this->rep_formatter->addRow();
			$this->rep_formatter->addColumn(' ');
			$this->rep_formatter->endRow();
			$this->rep_formatter->endTable();
		}
	}
}
?>
