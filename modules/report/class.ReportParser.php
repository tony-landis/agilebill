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
	
set_include_path(PATH_SEPARATOR.PATH_INCLUDES."pear");
require_once 'XML/Parser.php';

class reportColumnTag
{
	var $type;
	var $sql = '';
	var $criteria = '';
	var $orderby = '';
	var $indent = 0;
	var $display = '';
	var $field = '';
	var $aggregate = false;
	var $width = '';
	var $format = '';
	var $group_children_by = false;
	var $visible = true;
	var $hide_dups = false;
	var $condition = '';
	var $sql_criteria = '';
	var $total = false;
	var $total_format = '';
	var $link = '';
	var $user_criteria_table = '';
	var $user_criteria_col_id = '';
	var $user_criteria_col_name = '';
	var $user_criteria_date_format = 'Y-m';
	var $user_criteria_type;
	var $user_criteria;
	var $user_criteria_aggregate;
	
	function reportColumnTag()
	{
		$this->type = 'reportLevelTag';
		$this->user_criteria = false;
	}
}

class reportDatasetTag
{
	var $type;
	var $sql = '';
	var $criteria = '';
	var $orderby = '';
	
	function reportDatasetTag()
	{
		$this->type = 'reportDatasetTag';
	}
}

class ReportParser extends XML_Parser
{
	// Holds a reference to the reporting root class
	var $reportClass;
	// Holds the cdata as we parse a given tag
	var $cdata;
	// Holds our processing objects in a first in - last out (FILO) manner
	var $stack;
	
	// Holds the fields the user may edit and change
	var $user_criteria;
	// Holds the fields the user has changed
	var $user_criteria_data;
	// Holds the fields that have been added
	var $user_criteria_fields;
	
	function ReportParser(&$reportClass)
	{
		static $uc;
		static $ucd;
		static $ucf;
		
		$this->user_criteria =& $uc;
		$this->user_criteria_data =& $ucd;
		$this->user_criteria_fields =& $ucf;
		
		# Create the user criteria fields as static members
		
		if(!is_a($reportClass,'Reporting')) die('Parameter 1 must be a Reporting reference.');
		# Save the Reporting reference
		$this->reportClass =& $reportClass;
		# Clear the cdata buffer
		$this->cdata = "";
		# Init the stack
		$this->stack = array();
		# Call into XML_Parser, to set us up the bomb. (See references on ALL YOUR BASE R BELONG TO US - Seriously, it's funny shit.)
		parent::XML_Parser();
		# Do NOT perform case foldering!
		$this->folding = false;
	}
	
	function getUserCriteria()
	{
		return $this->user_criteria;
	}
	
	function setUserCriteria($field, $condition, $value)
	{
		#$this->user_criteria_fields[] = $field;
		$this->user_criteria_data[] = array($field, $condition, $value);
	}

	function getUserCriteriaSQL($field,&$isAggregate)
	{
		$sql = "";
		if(!is_array($this->user_criteria_data)) return '';
		foreach($this->user_criteria as $uc) {
			if($uc['name'] == $field) {
				break;
			}
		}
		foreach($this->user_criteria_data as $ucd) {
			if($ucd[0] == $field) {
				$db =& DB();
				
				# format the field correctly
				switch($uc['type']) {
					case 'date':
					case 'date_year_month':
						$ucd[2] = date('Ym',$ucd[2]);
						break;
					case 'date_year':
						$ucd[2] = date('Y',$ucd[2]);
						break;
					default:
						break;
				}
				
				if(strpos(strtoupper($ucd[1]),'NULL') !== false) {
					$sql .= $field." ".$ucd[1];
				} else {
					if(ereg("^[0-9]+$",$ucd[2]))
						$sql .= $field." ".$ucd[1]." ".$ucd[2];
					else
						$sql .= $field." ".$ucd[1]." ".$db->qstr($ucd[2]);
				}
				$sql .= " AND ";
			}
		}
		if(strlen($sql)) 
			$sql = substr($sql,0,strlen($sql)-4);
		$isAggregate = $uc['aggregate'];
		return $sql;
	}
	
	function startHandler($xp, $name, $attr)
	{
		# Clear the cdata buffer
		$this->cdata = "";
		
		if($name == 'report' && !count($this->stack)) {
			# initial outer report tag. set our start state.
			$this->stack[] =& $this->reportClass;
			return;
		} else if($name == 'level') {
			$this->stack[] = new Level;
		} else if($name == 'graph') {
			$this->stack[] = new LevelGraph(
				$attr['title'],
				intval($attr['width']),
				intval($attr['height']),
				$attr['type'],
				@$attr['direction'],
				@$attr['x_angle']
			);
		} else if($name == 'column') {
			$this->stack[] = new reportColumnTag;
		} else if($name == 'dataset') {
			$this->stack[] = new reportDatasetTag;
		} else if($name == 'break') {
			$work =& $this->last($this->stack);
			if($work) {
				$work->addBreak();
			}
		} else if($name == 'div') {
			$item = new Report_DivAdaptor;
			$item->id = $attr['id'];
			$this->stack[] =& $item;
		} else if($name == 'user_criteria') {
			$work =& $this->last($this->stack);
			if($work && is_a($work,'reportColumnTag')) {
				# validate the type
				switch($attr['type']) {
					case 'auto_affiliate':
					case 'auto_account':
					case 'menu':
					case 'bool':
					case 'text':
					case 'date_year_month':
					case 'date_year':
					case 'date':
						break;
					default:
						echo 'Error in user_criteria type.'; exit;
				}
				$work->user_criteria = true;
				$work->user_criteria_type = $attr['type'];
				$work->user_criteria_date_format = @$attr['date_format'];
				$work->user_criteria_table = @$attr['table'];
				$work->user_criteria_col_id = @$attr['col_id'];
				$work->user_criteria_col_name = @$attr['col_name'];
				$work->user_criteria_aggregate = @$attr['aggregate'];
			}
		}
		#echo '<pre>startHandler: '.$name."\n\n".print_r($this->stack,true).'</pre>';
	}
	
	function endHandler($xp, $name)
	{
		if(strlen($this->cdata)) {
			while(preg_match("/%%([A-Z1-2_]+)%%/", $this->cdata, $regs)>0) {
				if(defined($regs[1])) {
					$this->cdata = str_replace("%%".$regs[1]."%%", constant($regs[1]), $this->cdata);
				}
			}
		}
		
		#echo "endHandler:$name<br>";
		$work =& $this->last($this->stack); array_pop($this->stack);
		#echo "Class Type: ".get_class($work)."<br>";
		#echo '<pre>endHandler: '.$name."\n\n".print_r($this->stack,true).'</pre>';
		if($name == 'report' && count($this->stack)==1) {
			#echo 'stack is empty. returning from endHandler.<br>';
			return;
		}

		# Report
		if(is_a($work,'Reporting')) {
			if($name == 'title')
				$work->setTitle($this->cdata);
			else if($name == 'subtitle1')
				$work->setSubtitle1($this->cdata);
			else if($name == 'subtitle2')
				$work->setSubtitle2($this->cdata);
		}
		
		# Level
		if(is_a($work,'Level')) {
			if($name == 'sql')
				$work->setSql($this->cdata);
			else if($name == 'criteria')
				$work->setCriteria($this->cdata);
			else if($name == 'orderby')
				$work->setOrderby($this->cdata);
			else if($name == 'indent')
				$work->setIndent($this->cdata);
			else if($name == 'title')
				$work->setTitle($this->cdata);
			else if($name == 'htmlstyle')
				$work->setClass($this->cdata);

			else if($name == 'level') {
				$ltmp =& $work;
				$work =& $this->last($this->stack); array_pop($this->stack);
				# Do we have user criteria values on this?
				if(is_array($this->user_criteria_data)) {
					#echo '<pre>'.print_r($this->user_criteria_data,true).'</pre>';
					$tmp = $this->user_criteria_data;
					foreach($tmp as $f) {
						$a = false;
						$c = $this->getUserCriteriaSQL($f[0], $a);
						#echo "<pre>c=$c\n".print_r($f,true).'</pre>';
						if(strlen($c)) {
							$ltmp->addFieldCriteria($c, $a, $f[0]);
						}
					}
					#echo "<pre>".print_r($ltmp,true)."</pre>"; exit;
				}				
				$work->append($ltmp);
			}
		}
		if(is_a($work,'Report_DivAdaptor')) {
			$ltmp =& $work;
			$work =& $this->last($this->stack); array_pop($this->stack);
			$work->append($ltmp);
		}
		if(is_a($work,'reportColumnTag')) {
			if($name == 'display')
				$work->display = $this->cdata;
			else if($name == 'field')
				$work->field = $this->cdata;
			else if($name == 'aggregate')
				$work->aggregate = $this->cdata == 'true' ? true : false;
			else if($name == 'width')
				$work->width = $this->cdata;
			else if($name == 'format')
				$work->format = $this->cdata;
			else if($name == 'group_children_by')
				$work->group_children_by = $this->cdata == 'true' ? true : false;
			else if($name == 'visible')
				$work->visible = $this->cdata == 'true' ? true : false;
			else if($name == 'hide_dups')
				$work->hide_dups = $this->cdata == 'true' ? true : false;
			else if($name == 'sql')
				$work->sql = $this->cdata;
			else if($name == 'sql_criteria')
				$work->sql_criteria = $this->cdata;
			else if($name == 'condition')
				$work->condition = $this->cdata;
			else if($name == 'total')
				$work->total = $this->cdata == 'true' ? true : false;
			else if($name == 'total_format')
				$work->total_format = $this->cdata;
			else if($name == 'link')
				$work->link = $this->cdata;
			else if($name == 'column') {
				$f =& $work;
				$work =& $this->last($this->stack); array_pop($this->stack);
				$work->addField($f->display, $f->field, $f->aggregate, $f->width, 
					$f->format, $f->group_children_by, $f->visible, $f->hide_dups, 
					'', false, '', false, $f->condition, '', $f->sql, $f->sql_criteria, 
					0, '', '', false, $f->total, '', $f->total_format, '', false, $f->link
				);
				# If this field has user_criteria=true, then add it to the avail array
				if($f->user_criteria) {
					#echo "<pre>Trying\n".print_r($this->user_criteria_fields,true).'</pre>';
					
					if(!is_array($this->user_criteria_fields) || !in_array(str_replace('.','',$f->field),$this->user_criteria_fields)) {
						$this->user_criteria[] = array(
							'name' => $f->field,
							'display' => $f->display,
							'type' => $f->user_criteria_type,
							'date_format' => $f->user_criteria_date_format,
							'values' => '',
							'table' => $f->user_criteria_table,
							'col_id' => $f->user_criteria_col_id,
							'col_name' => $f->user_criteria_col_name,
							'aggregate' => $f->aggregate
						);
						$this->user_criteria_fields[] = str_replace('.','',$f->field);
						#echo "<pre>LEVEL\n".print_r($this->user_criteria,true).'</pre>';
					}
				}
			}
		}
		if(is_a($work,'LevelGraph')) {
			if($name == 'title')
				$work->setTitle($this->cdata);
			else if($name == 'graph') {
				# store the reference
				$ltmp =& $work;
				# pop the stack again
				$work =& $this->last($this->stack); array_pop($this->stack);
			
				# Do we have user criteria values on this?
				if(is_array($this->user_criteria_data)) {
					$tmp = $this->user_criteria_data;
					foreach($tmp as $f) {
						$a = false;
						$c = $this->getUserCriteriaSQL($f[0], $a);
						#echo "<pre>c=$c\n".print_r($f,true).'</pre>';
						if(strlen($c)) {
							$ltmp->addFieldCriteria($c, $a, $f[0]);
						}
					}
					#echo "<pre>".print_r($ltmp,true)."</pre>"; exit;
				}
				# append this graph onto the level/report/graph below
				$work->append($ltmp);
			}
		}
		if(is_a($work,'reportDatasetTag')) {
			if($name == 'criteria')
				$work->criteria = $this->cdata;
			else if($name == 'orderby')
				$work->orderby = $this->cdata;
			else if($name == 'sql')
				$work->sql = $this->cdata;
			else if($name == 'dataset') {
				# store the reference
				$d =& $work;
				# pop the stack
				$work =& $this->last($this->stack); array_pop($this->stack);
				# add the dataset to the graph
				$work->addDataset($d->sql, $d->criteria, $d->orderby);
			}
		}
		# Push the worker object back onto the stack
		$this->stack[] =& $work;		
	}
	
	function cdataHandler($xp, $cdata)
	{
		$this->cdata .= $cdata;
	}

	/**
	 * Returns the true class reference from an array, unlike any PHP function
	 */
	function &last(&$array) {
		if (!is_array($array)) 
			return null;
		if (!count($array)) 
			return false; // like end()
		return $array[count($array)-1];
	}	
}

?>