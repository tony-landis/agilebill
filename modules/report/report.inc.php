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
 * Agileco Report Module 
 */
class report
{  
	/**
	 * Get the list of available modules 
	 */
	function module_menu($VAR)
	{
		global $C_translate;

		if(!empty($VAR['report_module']))
		$default = $VAR['report_module'];
		else
		$default = false;

		$return = '';

		$path = PATH_AGILE . 'reports/';
		chdir($path);
		$dir = opendir($path);
		$count = 0;
		while ($file_name = readdir($dir))
		{
			if(  $file_name != '..' && $file_name != '.' && !preg_match("/.xml/i", $file_name) && !preg_match("/.php/i", $file_name))
			{
				$name = $C_translate->translate('menu', $file_name, '');
				if(empty($name) && preg_match("/^[a-zA-Z0-9\-\_]{1,}/", $file_name)) $name = strtoupper($file_name);
				if(!empty($name))
				{
					$return .= "<option value=\"{$file_name}\"";
					if($default == $file_name) $return .= " selected";
					$return .= ">{$name}</option>\n";

					$count++;
				}
			}
		}

		if($count > 10) $count = 10;

		echo '<select id="report_module" name="report_module" size="'.$count.'" onChange="submit()" multiple>';
		if($count==0)
		echo '<option value="">No Reports Available</option>';
		echo $return;
		echo '</select>';
	}
 	
	/**
	 * Get the list of available reports 
	 */
	function report_menu($VAR)
	{
		global$C_translate;
		$C_xml = new CORE_xml;

		if(empty($VAR['report_module'])) {
			echo $C_translate->translate('no_reports','report','');
			return;
		} else {
			$module = $VAR['report_module'];
		}
		 

		if(!empty($VAR['report_template']))
		$default = $VAR['report_template'];
		else
		$default = false;

		$return = '<select id="report_template" name="report_template" width="150" size="5" onChange="submit()" multiple>';

		$path = PATH_AGILE . 'reports/'.$module.'/';
		chdir($path);
		$dir = opendir($path);
		$count = 0;
		while ($file_name = readdir($dir))
		{
			if($file_name != '..' && $file_name != '.' && preg_match("/.xml$/i", $file_name)) { 
				$template = $C_xml->xml_to_array($path.$file_name); 
				$name = $template['report']['title'];
				 

				$return .= "<option value=\"{$file_name}\"";
				if($default == $file_name) $return .= " selected";
				$return .= ">{$name}</option>\n";

				$count++;
			}
		}


		if($count==0)
		$return .= '<option value="">'. $C_translate->translate('no_reports','report','').'</option>';
		$return .= '</select>';
		echo $return;
	}


 

	/**
     * Get user criteria 
     */
	function get_user_criteria($VAR)
	{    
		# validation
		if(empty($VAR['report_module']) || empty($VAR['report_template'])) return false;
		$module = $VAR['report_module'];
		$report = $VAR['report_template'];
		
		# include reporting classess
		require_once PATH_MODULES . 'report/class.Report.php';
		require_once PATH_MODULES . 'report/class.Level.php';
		require_once PATH_MODULES . 'report/class.ReportParser.php'; 
		$f = new HTML_ReportFormatter;  
		$r = new Reporting($f, true); 
		$p = new ReportParser($r); 
		$result = $p->setInputFile(PATH_AGILE.'reports/'.$module.'/'.$report); 
		$result = $p->parse();  
 
		# pre-process the user criteria array
		$arr = $p->getUserCriteria();
		if(is_array($arr)) {
			foreach($arr as $cond) {
				if($cond['type']=='menu') {		
					//print_r($cond);		
				}
			}
		}
		 
		global $smarty;
		$smarty->assign('userCriteria', $arr);
	}

	/**
     * Set user criteria and display report
     */
	function view($VAR)
	{ 
		# validation
		if(empty($VAR['report_module']) || empty($VAR['report_template'])) return false;
		$module = $VAR['report_module'];
		$report = $VAR['report_template'];
 		$format = $VAR['report_format'];
 		
		# include reporting classess
		require_once PATH_MODULES . 'report/class.Report.php';
		require_once PATH_MODULES . 'report/class.Level.php';
		require_once PATH_MODULES . 'report/class.ReportParser.php'; 
 
		set_time_limit(0);
		 
		if($format=='text')
			$f = new TXT_ReportFormatter;
		elseif($format=='html')
			$f = new HTML_ReportFormatter;
		elseif($format=='pdf')
			$f = new PDF_ReportFormatter;
		
		# Tell the formatter where to save the output 
		$dir = md5(tempnam(PATH_FILES, "s"));
		$path = PATH_FILES.'reports/'.$dir; 
		@unlink($path);
		mkdir($path, 0775);
		$f->setOutputDirectory($path);
		
		# set report construct file to use
		$r = new Reporting($f, true); 
		$p = new ReportParser($r); 
		$result = $p->setInputFile(PATH_AGILE.'reports/'.$module.'/'.$report); 
		
		# Get user criteria
		$arr = $p->getUserCriteria();
	  
		# Set the user criteria
		if(!empty($VAR['report']['conditions']) && is_array($VAR['report']['conditions'])) {
			foreach($VAR['report']['conditions'] as $arr) {				   
				$exp = $arr['exp'];
				$col = $arr['col'];
				$val = $arr['value'];
				$type= $arr['type'];				
				foreach($col as $i=>$name) {					
					if($type[$i] == 'date_year_month') {
						$val[$i] = array('month'=> $val['month'][$i], 'year'=> $val['year'][$i]);
					} 					
					$this->setSQLCondition($p, $col[$i], $exp[$i], $val[$i], $type[$i]);
				}
			} 
		} 
		#echo '<pre>'.print_r($p,true).'</pre>'; exit;
		$result = $p->parse();
		#echo '<pre>'.print_r($p,true).'</pre>'; exit;
		$r->display();		  
		
		if($format=='text') {	
			header('Content-type: text/txt'); 
			header('Content-Disposition: inline; filename="report.txt"'); 	 
			echo file_get_contents($f->getOutput());  
			@unlink($f->getOutput());
			
		} elseif($format=='html') {
			$f->getOutput(); 
			$url=URL.'includes/files/reports/'.$dir.'/report.html';
			echo "<script>document.location='$url';</script>";
			
		} elseif ($format=='pdf') {
			header('Content-type: application/pdf'); 
			header('Content-Disposition: inline; filename="report.pdf"'); 
			readfile($f->getOutput());
			@unlink($f->getOutput());			
		}  
	}

	/**
	 * Get actual SQL contition from user input and add to userCondtions
	 */
	function setSQLCondition(&$reportObj, $column, $condition, $value=false, $type=false) 
	{
		$o["EQ"] = '=';
		$o["NOT"] = '<>';
		
		$o["GT"] = '>';
		$o["LT"] = '<';
		$o["GTEQ"] = '>=';
		$o["LTEQ"] = '<=';	
				
		$o["LIKE"] = 'LIKE'; 
		$o["NLIKE"] = 'IS NOT LIKE';
		 
		$o["NULL"] = 'IS NULL';
		$o["NNULL"] = 'IS NOT NULL';
		 
		// actual SQL condition
		$c = $o["$condition"];
		
		// determine value
		if( $condition=="NULL" || $condition=="NNULL" ) 
			$v=false;
		elseif ($value=='') 
			return false;
		else
			$v=$value;		
			
		if($type=='date') {			
			$v=$this->convert_date_time($value);
			$reportObj->setUserCriteria($column, $c, $v);			
		} elseif ($type=='date_year') { 
			$v=mktime(0,0,0,1,1,$value);
			$reportObj->setUserCriteria($column, $c, $v); 			
		} elseif ($type=='date_year_month') {			
			if(!empty($value['year'])) {
				if(empty($value['month'])) $month=1; else $month=$value['month']; 
				$v=mktime(0,0,0,$month,1,$value['year']); 
				$reportObj->setUserCriteria($column, $c, $v);
			} 			
		} else {  	
			$reportObj->setUserCriteria($column, $c, $v);
		}
	}
  
	/**
	 * convert DEFAULT_DATE_TIME_FORMT to unix time stamp 
	 */
	function convert_date_time ($date, $constraint=false)
	{
		if($date == '0' || $date == '')
		return '';

		$Arr_format = explode(DEFAULT_DATE_DIVIDER, UNIX_DATE_FORMAT);
		$Arr_date   = explode(DEFAULT_DATE_DIVIDER, $date);

		for($i=0; $i<3; $i++)
		{
			if($Arr_format[$i] == 'd')
			$day = $Arr_date[$i];

			if($Arr_format[$i] == 'm')
			$month = $Arr_date[$i];

			if($Arr_format[$i] == 'Y')
			$year = $Arr_date[$i];
		}

		$timestamp = mktime(23, 59, 59, $month, $day, $year);

		return $timestamp;
	}
}
?>