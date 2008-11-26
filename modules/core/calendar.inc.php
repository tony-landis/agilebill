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
 * Core Calendar Class
 * 
 * @example $cal = new calendar; 
 * $cal->add("<b>Paid</b> - $9,458.00", mktime(0,1,1,date('m'), date('d')+2, date('Y')), 'green', 'green');
 * $cal->add("<b>Due</b>  - $455.25", mktime(0,1,1,date('m'), date('d')+2, date('Y')), 'red', 'red', "alert('Message')");
 * echo $cal->generate();
 * 
 */
class calendar
{
	var $month;
	var $year;

	var $days;		
	var $months;
	var $leap;
	
	var $start;					// starting timestamp, based on month/year specified
	var $end;					// ending timestamp, based on month/year specified
	
	var $start_day_num=1;		// first day to show
 	var $total_days;			// max days to show
 	var $on=0;					// first day of month (0-sunday)
 	
 	var $items;					// holds array of items to display on the calenday
 	
	function calendar() {
		$this->leap	  = date("l");
		$this->months = Array('jan', 'feb', 'mar', 'apr', 'may', 'jun', 'jul', 'aug', 'sep', 'oct', 'nov', 'dec');
		$this->days   = Array(31,($this->leap == 1 ? 29 : 28),31,30,31,30,31,31,30,31,30,31); 
		if(empty($this->year)) $this->year = date("Y"); 
		if(empty($this->month)) $this->month = date("m")-1; 
		if(empty($this->total_days)) $this->total_days = $this->days["$this->month"]; 
		$this->start = mktime(0,0,0,$this->month+1, 1, $this->year); 
		$this->end   = mktime(23, 59, 59, $this->month+1, $this->total_days, $this->year); 
		$this->days	 = Array("Sun","Mon","Tue","Wed","Thu","Fri","Sat"); 
	}
	
	/**
	 * Add an item to the calendar for display
	 *
	 * @param string $data The data to display on the given date/timestamp
	 * @param date $timestamp The day of month (1-31) or the unix timestamp
	 * @param string $border The color of the border
	 * @param string $color The font color
	 */
	function add($data, $timestamp, $border='#ddd', $color='#000', $onclick=false) {
		if($timestamp > 31) {
			$day = date('d', $timestamp);
		} else {
			$day = $timestamp;
		}
		$this->items["$day"][] = Array("data" => $data, "border"=> $border, "color"=> $color, "onclick"=>$onclick);  
	}
	
	/**
	 * Generate the full calendar HTML
	 *
	 * @return string
	 */
	function generate() 
	{    
		// Today's date in number (such as 21th or whatever) 
		$today_num = $tnc = date("j");
		
		// Today's day of the week (eg: Sat)
		$today_day=date("D");
		
		// $tdc contains the index number of today in $this->days 
		$tdc=array_search(date("D"),$this->days);
		
		// This is loop is used to get the day of the week on the 1st day of this month (eg: January 1st)
		// $tdc is the day of the week in number (the index number in $this->days)
		// $tnc is decreased until it hits 1 (the first day of the month)
		// $tdc is also decreased if it goes below 0 (sunday), then it is set to 6 (saturday)
		while ($tnc > 1) { 
			$tdc--;
			if ($tdc < 0) { $tdc=6; } 
			$tnc--; 
		}
		
		// set $counter_day the first day of the month (eg: Saturday)
		$counter_day=$this->days[$tdc];
		  
		// Just the title.. displays the month and the year
		$date_display_title="<b>". date("F") ."</b> ( ".date("Y"). " )";
		
		// Creates the table with 7 columns
		$out = "<div id=\"calendar\" >
			<table border=0 cellpadding=3 cellspacing=1 width=100%>
			<tr><td colspan=7 class=title><center>{$date_display_title}</center></td></tr>
			<tr>
				<td class=days width=14.2%><b>Sun</b></td>
				<td class=days width=14.2%><b>Mon</b></td>
				<td class=days width=14.2%><b>Tue</b></td>
				<td class=days width=14.2%><b>Wed</b></td>
				<td class=days width=14.2%><b>Thu</b></td>
				<td class=days width=14.2%><b>Fri</b></td>
				<td class=days width=14.2%><b>Sat</b></td>
			</tr>
			<tr> ";
		
		// This is the loop.. starts the the first day of the month and works its way up to the last day of the month
		while ($this->start_day_num <= $this->total_days) {
			
			// create a new row if we're at the last column (which is Saturday)
			if ($this->on > 6) { 
				$this->on=0; 
				$out .= "</tr><tr>"; 
			}
			
			/* Jan 1st 2005 is on Saturday. We're on the Monday column,
				so leave this column blank, go to the next column, and start again from the loop's condition
			*/
			if ($counter_day != $this->days[$this->on]) { 
				$out .= "<td id=calendar_". $this->start_day_num .">&nbsp;</td>"; 
				$this->on++; 
				continue; 
			}
			
			/* if we are display a date thats not yet been past (which is the future), then dull down the colors
				For example, if its 16th today, and we are displaying the 17th, then set the text color to grey
			*/
			if (isset($dull)) {
				$out .= "<td class=disable id=calendar_". $this->start_day_num ." valign=top>
				<div class=date>". $this->start_day_num ."</div>";
				if(is_array($this->items["{$this->start_day_num}"])) {
					foreach($this->items["{$this->start_day_num}"] as $item) {
						if($item['onclick']) $onClick="onClick=\"{$item['onclick']}\" "; else $onClick='';
						$out .= "<div class=item style=\"border:1px solid {$item['border']}; color:{$item['color']};\" $onClick>{$item['data']}</div>"; 
					}
				} else { 
					$out .= "&nbsp;"; 
				}				
				$out .= "</td>";
			}
			
			/*
			If we are not displaying the future dates, then display it normally.
			If we are displaying today's date, set $dull as true so the dates that we dislay from here on are dull-colored
			*/
			else {
				$out .= "<td class=display id=calendar_". $this->start_day_num ." valign=top>
				<div class=date>". $this->start_day_num ."</div>";  
				if(is_array($this->items["{$this->start_day_num}"])) {
					foreach($this->items["{$this->start_day_num}"] as $item) {
						if($item['onclick']) $onClick="onClick=\"{$item['onclick']}\" "; else $onClick='';
						$out .= "<div class=item style=\"border:1px solid {$item['border']}; color:{$item['color']};\" $onClick>{$item['data']}</div>"; 
					}
				} else { 
					$out .= "&nbsp;"; 
				}
				$out .= "</td>"; 
				if ($this->start_day_num == date("j")) { $dull=true; }
			}
			
			// Move on to the next day, if all goes well
			$this->start_day_num++;
			
			// Move on to the next day of the week's index (refer to the $this->days array)
			$next_day=array_search($counter_day,$this->days) + 1;
			
			// If its over 6 (saturday), then set it to 0 (sunday) since the 7th index doesn't exist in $this->days
			$counter_day=$this->days[($next_day > 6 ? 0 : $next_day)];
			
			// go to the next column
			$this->on++;
		} 
		$out .= "</tr></table></div>";		 
		return $out;
	} 
} 
?>