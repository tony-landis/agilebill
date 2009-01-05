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
	
class LevelGraph extends Level_Base {
	
	var $lev_setting;
	var $lev_items;
	var $SQL_filtered;
	var $group_level;
	var $grouping_criteria;
	var $formatter;
	var $graph;
	var $plotarea;
	var $dataset;
	var $canvas;
	
	function LevelGraph (
		$title = '', 
		$width=800, 
		$height=300, 
		$type='bar', 
		$direction = 'vertical',
		$x_angle = 90)
	{
		if(!strlen($direction)) $direction = 'vertical';
		
		$this->lev_setting = array(
			"title"	=> $title,
			"width" => $width,
			"height" => $height,
			"y_label_angle" => 0,
			"x_label_angle" => intval($x_angle),
			"type" => $type,
			"direction" => $direction,
			"SQL_criteria" => ''
		);
	}
	
	function addDataset ($SQL_select, $SQL_criteria='', $SQL_order='')
	{
		$this->dataset[] = array(
			"SQL_select" => $SQL_select,
			"SQL_criteria" => $SQL_criteria,
			"SQL_order" => $SQL_order
		);
	}
	
	function display ($grouping_criteria = Null, $grouping_aggregate = Null, &$formatter) 
	{		
		$this->formatter =& $formatter;
		$this->formatter->setLevel($this);
		$this->grouping_criteria = $grouping_criteria;
		$this->grouping_aggregate = $grouping_aggregate;
		
		$this->group_level = false;
		if (count($this->lev_items) > 0) {
			$this->group_level = true;
		}

		set_include_path(get_include_path().PATH_SEPARATOR.PATH_INCLUDES."pear");
		include_once 'Image/Graph.php';
		include_once 'Image/Canvas.php';
		
		$this->canvas =& Image_Canvas::factory('png', array(
			'width' => $this->lev_setting["width"],
			'height' => $this->lev_setting["height"],
			'antialias' => 'native'
		));
		
		$this->graph =& Image_Graph::factory('graph', $this->canvas);
		$this->plotarea =& $this->graph->addNew('plotarea',array(
			'Image_Graph_Axis_Category', 'Image_Graph_Axis', $this->lev_setting['direction']
		));
		
		$this->plotarea->setFillColor('black@0.1');
		#$this->plotarea->showShadow();
		
		$AxisX =& $this->plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
		$AxisY =& $this->plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
		
		$AxisX->setFontAngle($this->lev_setting["x_label_angle"]);
		$AxisY->setFontAngle($this->lev_setting["y_label_angle"]);
		
		foreach($this->dataset as $dset) {
			$SQL_criteria = '';
			if ($dset['SQL_criteria'] != '') {
				$SQL_criteria = $this->addToFilter($SQL_criteria, 
					$dset['SQL_criteria']);
			}
			#echo "C1: ".$SQL_criteria."<BR>";
			if ($this->lev_setting['SQL_criteria'] != '') {
				$SQL_criteria = $this->addToFilter($SQL_criteria, 
					$this->lev_setting['SQL_criteria']);
			}
			#echo "C2: ".$SQL_criteria."<BR>";
			if (count($this->grouping_criteria) > 0) {
				foreach ($this->grouping_criteria as $key => $value) {				
					$SQL_criteria = $this->addToFilter($SQL_criteria, 
						"$key = $value", 
						$this->grouping_aggregate[$key]
					);
				}	
			}
			#echo "C3: ".$SQL_criteria."<BR>";
			$this->SQL_filtered = $this->shuffleSQL($dset["SQL_select"].$SQL_criteria." ".$dset["SQL_order"]);		
			if(defined('REPORT_DEBUG'))
				echo "<br>The SQL_filtered is: " . $this->SQL_filtered . "<br>";
			$db =& DB();
			$result = $db->Execute($this->SQL_filtered);
			if(!$result) {
				echo "SQL: ".$this->SQL_filtered."<br>".$db->ErrorMsg();
				exit;
			}
			$num_rows = $result->RecordCount();
			
			if ($num_rows == 0) {			
				return;
			}
		
			$Dataset =& Image_Graph::factory('dataset');
			while(!$result->EOF) {
				$Dataset->addPoint($result->fields[0],$result->fields[1]);
				$result->MoveNext();
			}
			$Plot =& $this->plotarea->addNew($this->lev_setting["type"], &$Dataset);
		}
		
		$file = tempnam($this->formatter->output_path."/", "s");
		@unlink($file);
		$file .= ".png";
		$this->graph->done(
			array('filename' => $file)
		);
		
		# add to output
		$this->formatter->insertImage(
			$file,
			$this->lev_setting["width"],
			$this->lev_setting["height"]
		);
		
		#intersperse and whatnot
		if ($this->group_level === true) {
			$this->formatter->endTable();
		}
		if ($this->group_level === true) {				
			$this->intersperse($this->grouping_criteria,$this->grouping_aggregate);
		}	
	}
}

class Level extends Level_Base {

	var $lev_setting;
	var $lev_fields;
	var $lev_items;
	var $indent_html;
	var $SQL_filtered;
	var $group_level;
	var $grouping_criteria;
	var $has_title;
	var $formatter;
	var $add_headers = 0;
	var $class;
	
	function Level ($title='', 
		$SQL_select='', $SQL_criteria='', $SQL_order='', 
		$title_table_class='title', $title_class='', $indent=0, 
		$lev_field_width = '', 
		$lev_field_class = 'row', $lev_label_class = '', $lev_table_class = 'level',
		$lev_tot_class = 'rc-lev-tot1', $lev_colspan_label_class = 'rc-lev-colspan-label1')
	{

		if($lev_field_class == 'row') {
			$lev_field_class = new ReportStyle;
			$lev_field_class->backgroundColor(230,230,230);
		}
		if($lev_label_class == '') {
			$lev_label_class = new ReportStyle;
			$lev_label_class->bold();
			$lev_label_class->fontFamily('arial');
			$lev_label_class->is_heading = true;
		}
		
		$this->lev_setting = array(
			"title" => $title,
			"SQL_select" => $SQL_select,
			"SQL_criteria" => $SQL_criteria,
			"SQL_order" => $SQL_order,
			"title_class_html" => $title_class,
			"title_table_class_html" => $title_table_class,
			"indent" => $indent,
			"lev_field_width" => $lev_field_width,
			"lev_field_class" => $lev_field_class,
			"lev_label_class" => $lev_label_class,
			"lev_table_class_html" => $lev_table_class,
			"lev_tot_class" => $lev_tot_class,
			"lev_colspan_label_class" => $lev_colspan_label_class,
			"class" => ''
			);
	}	
	
	function setTitle($t)
	{
		$this->lev_setting["title"] = $t;
	}
	
	function setSql($s)
	{
		$this->lev_setting["SQL_select"] = $s;
	}
	
	function setCriteria($c)
	{
		$this->lev_setting["SQL_criteria"] = $c;
	}
	
	function setOrderby($o)
	{
		$this->lev_setting["SQL_order"] = $o;
	}
	
	function setIndent($i)
	{
		$this->lev_setting["indent"] = $i;
		return $i;
	}
	
	function setClass($c)
	{
		$this->lev_setting["class"] = $c;
	}
	
	function addField ($label, $name, $isAggregate = false, $width = '', $format = '', 
		$group_children_by = false, $visible = true, $hide_dups = false,  
		$class = '', $lev_class_also = false, $label_class = '', $lev_label_class_also = false,
		$condition = '', $cond_class = '', 
		$SQL_select = '', $SQL_criteria = '',		
		$colspan = 0, $colspan_label = '', $colspan_label_class = '', $lev_colspan_class_also = false,
		$has_tot = false, $tot_label = '', $tot_format = '', $tot_class = '', $lev_tot_class_also = false,
		$link = '')
	{
		//NB every field added MUST be in the source SQL
		//if a grouping variable, then set group_children_by to True
		//if a field is an aggregate, then it must be True.  This causes it's usage to be in HAVING clause, instead of WHERE
		
		if ($link != "") {
			$is_link = True;
			$link_text_start = $link["link_text_start"];
			$link_text_end = $link["link_text_end"];
			$link_title_start = $link["link_title_start"];
			$link_title_end = $link["link_title_end"];
			$link_href_start = $link["link_href_start"];
			$link_href_end = $link["link_href_end"];
		} else {
			$is_link = False;
			$link_text_start = "";
			$link_text_end = "";
			$link_title_start = "";
			$link_title_end = "";
			$link_href_start = "";
			$link_href_end = "";
		}
		
		if($tot_class == '') {
			$tot_class = new ReportStyle;
			$tot_class->backgroundColor(200,200,200);
		}				
		$this->lev_fields[$name] = array("field_label"=>$label,
			"field_name"=>$name,
			"field_aggregate"=>$isAggregate,
			"colspan"=>$colspan, 
			"colspan_label"=>$colspan_label, 
			"colspan_label_class"=>$colspan_label_class,
			"lev_colspan_class_also"=>$lev_colspan_class_also,
			"field_SQL_select"=>$SQL_select,
			"field_SQL_criteria"=>$SQL_criteria,
			"group_children_by"=>$group_children_by,
			"visible"=>$visible,
			"hide_duplicates"=>$hide_dups,
			"field_width"=>$width,
			"field_format"=>$format,
			"field_class"=>$class,
			"lev_class_also" =>$lev_class_also,
			"field_label_class"=>$label_class,
			"lev_label_class_also"=>$lev_label_class_also,
			"field_condition"=>$condition,
			"field_cond_class"=>$cond_class,
			"has_tot"=>$has_tot,
			"tot_label"=>$tot_label,
			"field_tot_format"=>$tot_format,
			"field_tot_class"=>$tot_class,
			"lev_tot_class_also"=>$lev_tot_class_also,
			"is_link"=>$is_link,
			"link_text_start"=>$link_text_start,
			"link_text_end"=>$link_text_end,
			"link_title_start"=>$link_title_start,
			"link_title_end"=>$link_title_end,
			"link_href_start"=>$link_href_start,
			"link_href_end"=>$link_href_end
			);
	}
	
	function display ($grouping_criteria = Null, $grouping_aggregate = Null, &$formatter) 
	{		
		$this->formatter =& $formatter;
		$this->formatter->setLevel($this);
		$this->grouping_criteria = $grouping_criteria;
		$this->grouping_aggregate = $grouping_aggregate;
		
		#echo "Freshly received grouping criteria: " . print_r($this->grouping_criteria); //check
		
		$this->group_level = false;
		if (count($this->lev_items) > 0) {
			$this->group_level = true;
		}
		
		$SQL_criteria = '';
		if ($this->lev_setting['SQL_criteria'] != '') {
			$SQL_criteria = $this->addToFilter($SQL_criteria, 
				$this->lev_setting['SQL_criteria']);
		}
		
		if (count($this->grouping_criteria) > 0) {
			foreach ($this->grouping_criteria as $key => $value) {				
				$SQL_criteria = $this->addToFilter($SQL_criteria, 
					"$key = $value", 
					$this->grouping_aggregate[$key]
				);
			}	
		}
		
		//echo "<br>The row filter clause is: " . $SQL_criteria , "<br>"; //check
		// 2-2) put select, where, and order clauses together
		$this->SQL_filtered = $this->shuffleSQL($this->lev_setting["SQL_select"].$SQL_criteria." ".$this->lev_setting["SQL_order"]);		
		
		if(defined('REPORT_DEBUG'))
			echo "<br>The SQL_filtered is: " . $this->SQL_filtered . "<br>"; //check
		
				
		$db =& DB();
		$result = $db->Execute($this->SQL_filtered);
		if(!$result) {
			echo "SQL: {$this->SQL_filtered}<br>".$db->ErrorMsg();
			exit;
		}
		$num_rows = $result->RecordCount();
		if ($num_rows == 0) {			
			return;
		}

		$this->indent_html = $this->setIndent($this->lev_setting['indent']);
		
		// 3-3) insert level title if needed
		$this->has_title = False;
		if ($this->lev_setting["title"] != '') {
			$this->has_title = true;
		}

		$heading = '';
		if ($this->has_title === true) {
			if(is_a($this->formatter,'HTML_ReportFormatter')) {
				$this->formatter->setIndent($this->indent_html);
				$heading = '<div id="title">'.$this->lev_setting["title"].'</div>';
				
			} else {
				$this->formatter->setIndent($this->indent_html);
				$this->formatter->addTable($this->lev_setting["title_table_class_html"]);
				$this->formatter->addRow();
				$this->formatter->addColumn($this->lev_setting["title"], 
					$this->lev_setting["title_class_html"] 
				);
				$this->formatter->endRow();
				$this->formatter->endTable();
			}
		}
						
		//if a final row, use header-style labels	
		if ($this->group_level === false) {
			
			$this->formatter->setIndent($this->indent_html);
			$this->addHeaderLabels($heading);					
		}

			
		/*set these up once before rows begin (needed for hide_duplicates)*/	
		foreach ($this->lev_fields as $field) {
			$content[$field['field_name']] = ''; //seed/reset
			$last_content[$field['field_name']] = ''; //seed/reset
			//each of these is an array with an item for each field value in the row
		}				
		
		
		// 4) DISPLAY EACH ROW >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		while (!$result->EOF) {
			$row = $result->fields;
			$result->MoveNext();
			
			// 4-1) display each visible field (from supplied field argument) within row	
			
			//if level is group level (no header row) need to start table and body (one for each level)
			
			if ($this->group_level === true) {
		if(strlen($this->lev_setting['class']))
			$this->formatter->write('<div id="'.$this->lev_setting["class"].'">');				
				$this->formatter->setIndent($this->indent_html);
				$this->formatter->addTable($this->lev_setting["lev_table_class_html"],$heading);
			}
			
			$this->formatter->addRow();
			
			foreach ($this->lev_fields as $field) {
				
				// 4-1-0) only display field if visible
				if ($field['visible'] === false) {				
					continue;	//skip this field
				}
				
				// 4-1-1) set up class(es)	
				
				//only make label classes html if a group level
				if ($this->group_level === true) { 
					/*must create class html here - it is a combination of level and 
						field class settings	*/
					
					$field_label_class_html = $this->classHtml($field["field_label_class"],
						$this->lev_setting["lev_label_class"],
						$field["lev_class_also"]);	
				}
				
				/*if a conditional class, see if condition met.  If it is, use field_conditional class
				instead of standard field class*/
				
				$condition_met = false; //initialise				
				if ($field['field_condition'] != '') {									
					//get condition 
					$condition_met = $this->makeCondition($field['field_condition'], 
						$row, $this->lev_fields
					);
				}
				
				if ($condition_met === true) {					
					$field_class = $field['field_cond_class'];
				} else {					
					$field_class =$field['field_class'];
				}
						
				$field_class_html = $this->classHtml($field_class,
					$this->lev_setting['lev_field_class'], 
					$field['lev_class_also']);

				$field_width_html = $this->setWidth ($field["field_width"], $this->lev_setting["lev_field_width"]);
				
				if ($field["field_SQL_select"] != '') { //if this field has its own data source ...
				
					/* 5-1-3-1) set up WHERE/HAVING clause using both parent-derived criteria 
					(if the level above is team='CommunityCare' then this record source needs to be filtered to 
					only include data for the CommunityCare team*/
					
					$field_SQL_criteria = "";
					
					if ($field["field_SQL_criteria"] != "") {
						
						$field_SQL_criteria = $this->addToFilter($field_SQL_criteria, 
							$field["field_SQL_criteria"]);
					}
					
					/*
					if (count($this->grouping_criteria) > 0) {
						
						foreach ($this->grouping_criteria as $key => $value) {
							
							$field_SQL_criteria = $this->addToFilter($field_SQL_criteria, "$key = $value");
						}
					}
					*/
						if($field["field_name"]!="") {
							$dba = DB();
							$field_SQL_criteria = $this->addToFilter($field_SQL_criteria, $field["field_name"]." = ".$dba->qstr($row[$field["field_name"]]));
						}
					
					
					//echo "<br>The field filter clause is: " . $field_SQL_criteria , "<br>"; //check
					
					/* 4-1-3-2) put select, where (but not order - not applicable - 
						order derived from other fields) parts of SQL statement together*/
					
					$field_SQL_filtered = $this->shuffleSQL($field["field_SQL_select"] . $field_SQL_criteria);		
			
					#echo "<br>The field_SQL_filtered is: " . $field_SQL_filtered . "<br>"; //check
					
					// 4-1-3-3) get the content according to the SQL statement
						
					$db = DB();
					$rs = $db->Execute($field_SQL_filtered);
					if($rs && $rs->RecordCount()) {
						$content[$field["field_name"]] = $rs->fields[0];
					}
									
				} else { //take content from field
					
					$content[$field["field_name"]] = $row[$field["field_name"]];
				}

				/*if a final level, hide_duplicates = True, and it is a duplicate, 
					make $field_content = "" and skip to display*/
				$duplicate = false; //seed/reset
				
				if ($last_content[$field["field_name"]] == $content[$field["field_name"]]) {					
					$duplicate = true;	
				}
				
				//set last content for next comparison (if any)	
				$last_content[$field["field_name"]] = $content[$field["field_name"]];
				
				if ($this->group_level === false 
					AND $duplicate === true 
					AND $field["hide_duplicates"] === true) {
				
					$field_content = "";

					//skipping formatting and linking
						
				} else {
									
					// 4-1-5) set up formatting e.g. decimal places
					
					$style = $this->getStyle($field["field_format"]);
					$dp = $this->getDp($field["field_format"]);
					$date_format = $this->getDateFormat($field["field_format"]);

					//echo "<br>Field content before formatting is: " . $content[$field["field_name"]];				
					//echo "<br>Field array contains: " . print_r($field) . "<br>"; //check
					$field_content = $this->myFormat($content[$field["field_name"]], 
						$style, $dp, $date_format);
					
					// 4-1-6) put in link if required
					if ($field["is_link"] === true) {
						
						$field_content = "<a href=\"" . $field["link_href_start"] . rawurlencode($field_content) . $field["link_href_end"] . "\" " .
							"title=\"" . $field["link_title_start"] . $field_content . $field["link_title_end"] . "\">" . 
							$field["link_text_start"] . $field_content . $field["link_text_end"] .
							"</a>";
					}					
				}				
								
				// 4-1-7) display field		
				
				if ($this->group_level === true) {
					$this->formatter->addColumn($field["field_label"], $field_label_class_html);
					$this->formatter->addColumn($field_content, $field_class_html);
				} else {
					$this->formatter->addColumn($field_content, $field_class_html);
				}	
			}
					
			$this->formatter->endRow();
			$this->add_headers = 0;
			
			if ($this->group_level === true) { //a separate table for each group by level
				$this->formatter->endTable();
			}

				
			//5-1) add additional grouping criteria to $grouping_criteria
			
			//loop through all fields in level - add to grouping_criteria filter if new_group_by is True
			foreach ($this->lev_fields as $field) {
				
				if ($field["group_children_by"] != true) { //only process this field if adding to filter				
					continue;
				}
				$key = "`" . $field["field_name"] . "`";
				$value = $row[$field["field_name"]];
				
				if (strpos($field["field_format"],"num") === false) { 
					$value = $db->qstr($value);
				}				
				$this->grouping_criteria[$key] = $value;
				$this->grouping_aggregate[$key] = $field['field_aggregate'];
			}				
						
			//echo "<br>The grouping_criteria are now set to: " . print_r($this->grouping_criteria) . "<br>"; //check	*/
			
			if ($this->group_level === true) {				
				$this->intersperse($this->grouping_criteria,$this->grouping_aggregate);
							if(strlen($this->lev_setting["class"]))
					$this->formatter->write('</div>');				
			}

		}
		

		// 6 DISPLAY TOTALS (only if a final level and only if one required)
		
		/*any fields which have 'have_tot' equal True? Loop through them, and build array of field names.
		If the array has any values, proceed (and pass on array to provide headstart building source query*/
		
		if ($this->group_level === false) {			
			$this->addTotal($db);
			$this->formatter->endTable();
		}
	}
	
	function addHeaderLabels ($heading) 
	{
		if($this->add_headers>0)	return;
		$this->add_headers++;
		
		$this->formatter->addTable($this->lev_setting["lev_table_class_html"], $heading);
		$this->formatter->addRow();
		
		foreach ($this->lev_fields as $field) {
			// only display field if visible
			if ($field['visible'] === False) {				
				continue;	//skip this field
			}	
				
			$field_label_class_html = $this->classHtml($field["field_label_class"],
				$this->lev_setting["lev_label_class"], 
				$field["lev_label_class_also"]);	

			$field_width_html = $this->setWidth ($field["field_width"], $this->lev_setting["lev_field_width"]);
			
			// c) display
			$this->formatter->addColumn($field["field_label"], $field_label_class_html);
		}
		$this->formatter->endRow();
	}
	
	function addTotal (&$db) 
	{
		// 0 ) check to see if totals are required for any of the fields >>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>
		// (and build SQL select statement for totals row)
		
		$SQL_select_tot = '';
		foreach ($this->lev_fields as $field) {
			if ($field['has_tot'] === True) {
				if ($SQL_select_tot == '') {
					$SQL_select_tot = 
						"SELECT Sum(" . $field["field_name"] . ") AS `Tot_" . $field["field_name"] . "`" ;
				} else {
					$SQL_select_tot .= 
						", Sum(" . $field["field_name"] . ") AS `Tot_" . $field["field_name"] . "`";
				}
			}
		}
				
		if ($SQL_select_tot == '') {
			return false;	
		}	
			
		$group_by = "";
		if (count($this->grouping_criteria) > 0) {
			$group_by = "GROUP BY ";
			foreach ($this->grouping_criteria as $key=>$value) {				
				$group_by .= (($group_by === "GROUP BY ") ? $key : ", " . $key);	
			}
		}
		//echo "<br>Group by is now: " .$group_by;
		
		$SQL_tot_filtered = $this->shuffleSQL("$SQL_select_tot FROM (" . $this->SQL_filtered . 
			") AS Source "); #$group_by"; //each key in grouping criteria
		
		# echo "<br>The SQL tot filtered is: " . $SQL_tot_filtered . "<br>"; 
		$result1 = $db->Execute($SQL_tot_filtered);		
		while (!$result1->EOF) {
			$row = $result1->fields;
			$result1->MoveNext();
			
			$this->formatter->addRow();
			
			foreach ($this->lev_fields as $field) {
				if ($field["visible"] === False) {				
					continue;
				}	
								
				//must create class html here - it is a combination of field and level class settings	
				$field_tot_class_html = $this->classHtml($field["field_tot_class"],
					$this->lev_setting["lev_tot_class"], 
					$field["lev_tot_class_also"]);	

				$field_width_html = $this->setWidth ($field["field_width"], $this->lev_setting["lev_field_width"]);
											
				if ($field["has_tot"] === True) {
					
					// 6-2-1-1) set up field content (whether a value or a format)
					
					if ($field["tot_label"] != "") {
						
						// 6-2-1-1-1) set up label
						$field_content = $field["tot_label"];
						
					} else {
					
						// 6-2-1-1-2) set up formatting e.g. decimal places (only run when a cell to display)
						$field_tot = "Tot_" . $field["field_name"];						
												
						$field_content = $this->myFormat($row["$field_tot"], 
							$this->getStyle($field["field_tot_format"]), 
							$this->getDp($field["field_tot_format"]),	
							$this->getDateFormat($field["field_format"]));
					}	
						
					// 6-2-1-2) display row				
					$this->formatter->addColumn($field_content, $field_tot_class_html);
				} else {
				
					// 6-2-2) display row
					$this->formatter->addColumn(" ", $field_tot_class_html);
				}
			}					
			$this->formatter->endRow();
		}
		return true; //i.e. had totals	
	}
}


/**
 * This is the base Level class, any and all types of rendered chunks MUST inherit from this.
 */ 
class Level_Base {

	/**
	 * Add a user criteria to the WHERE clause
	 */
	function addFieldCriteria($sql, $bIsAggregate = false, $field = '')
	{
		$this->lev_setting['SQL_criteria'] = 
			$this->addToFilter($this->lev_setting['SQL_criteria'], 
			$sql, $bIsAggregate);
			
		if(strlen($this->lev_setting['SQL_order'])>0) {
			$sql = $this->lev_setting['SQL_order'];
			if( ($p=stripos($sql,'GROUP BY')) !== false) {
				if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
					$groupby = substr($sql,$p,$t[1]-$p);
					if(stripos($groupby,$field)===false)
					$sql = str_replace($groupby,$groupby.", {$field}",$sql);
				}
			}
			$this->lev_setting['SQL_order'] = $sql;
		} else {
			$this->lev_setting['SQL_order'] = "GROUP BY ".$field;
		}
		
		if(isset($this->dataset)) {
			for($i=0;$i<count($this->dataset);$i++) {
				if(strlen($this->dataset[$i]['SQL_order'])>0) {
					$sql = $this->dataset[$i]['SQL_order'];
					if( ($p=stripos($sql,'GROUP BY')) !== false) {
						if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
							$groupby = substr($sql,$p,$t[1]-$p);
							if(stripos($groupby,$field)===false)
							$sql = str_replace($groupby,$groupby.", {$field}",$sql);
						}
					}
					$this->dataset[$i]['SQL_order'] = $sql;
				} else {
					$this->dataset[$i]['SQL_order'] = "GROUP BY ".$field;
				}
			}
		}
	}
	
	/**
	 * Add a sub-object to render beneath the current object
	 */
	function append (&$item)
	{
		$this->lev_items[] =& $item; 
	}

	function addBreak ()
	{
		$this->lev_items[] = new Report_BreakAdaptor;
	}

	function addDiv($id)
	{
		$item = new Report_BreakAdaptor;
		$item->id = $id;
		$this->lev_items[] =& $item;
	}
		
	/**
	 * This calls all sub-objects of the current object to tell them to build their output
	 */
	function intersperse ($grouping_criteria, $grouping_aggregate)
	{
		if(isset($this->lev_items) && is_array($this->lev_items)) {
			foreach ($this->lev_items as $item) {
		#if(strlen($this->lev_setting['class']))
		#	$this->formatter->write('<div id="'.$this->lev_setting["class"].'">');				
				$c = $this->formatter->indent;
				$item->display($grouping_criteria, $grouping_aggregate, $this->formatter);
				$this->formatter->indent = $c;
				#if(strlen($this->lev_setting["class"]))
				#	$this->formatter->write('</div>');				
			}		
		}
	}	
	
	/**
	 * Build onto the SQL WHERE/HAVING clause
	 */
	function addToFilter ($full_criteria, $new_criterion, $aggregate = false)
	{
		/*
		echo "<BR>";
		echo "full_criteria = ".$full_criteria."<br>";
		echo "new_criterion = ".$new_criterion."<br>";
		echo "aggregate     = ".$aggregate."<br>";
		*/
		$new_criterion = str_replace("  "," ",$new_criterion);
		$new_criterion = str_replace("WHERE HAVING","HAVING",$new_criterion);
		$t = "WHERE";
		if($aggregate) {
			$t = "HAVING";
		}
		if ($full_criteria == "") {
			if(strpos($new_criterion,$t)!==false)
			return $new_criterion;
			else
			return " $t " . $new_criterion;
			break;
		}
		#echo "t:".$t."<br>fc:".$full_criteria."<br>nc:".$new_criterion."<BR>";
		if (strpos($full_criteria, $t) === False && strpos($new_criterion, $t)===false) {
			if($t == "WHERE") {
				$r = " WHERE ".$full_criteria;
				if(strlen($new_criterion))
					$r .= " AND ".$new_criterion;
				return $r;
			} else {
				return $full_criteria." $t " . $new_criterion;
			}
		} else if(strpos($full_criteria, $t)!==false && strpos($new_criterion, $t) !== false) {
			$sql = $full_criteria . " AND " . substr($new_criterion, strpos($new_criterion,$t)+strlen($t));
			$sql = str_replace("AND HAVING","HAVING",$sql);
			#echo 'OUTPUT:'.$sql."<br>";
			return $sql;
		} else {
			#echo "fc1:".$full_criteria."<br>nc:".$new_criterion."<BR>";
			if(strncasecmp(trim($new_criterion),"having",6)==0)
			return $full_criteria . ' '. $new_criterion;
			else
			return $full_criteria . " AND " . $new_criterion;
		}
	}
	
	function find_next_sql_keyword($sql,$offset=0)
	{
		$ret = false;
		if(is_string($sql) && is_numeric($offset)) {
			$arr = array(
				'WHERE',
				'GROUP BY',
				'ORDER BY',
				'LIMIT',
				'HAVING'
			);
			$bFound = false;
			foreach($arr as $k) {
				if( ($t=stripos($sql,$k,$offset)) !== false) {
					if($ret === false) {
						$ret = array($k,$t);
						$bFound = true;
					}
				}
			}
			if($bFound == false) {
				$ret = array('',strlen($sql));
			}
		}
		return $ret;
	}
	
	function shuffleSQL($sql, $groupBy='')
	{
		if( ($p=stripos($sql,'HAVING')) !== false) {
			# found a having clause
			$leading = '';
			$having = '';
			$groupby = '';
			$orderby = '';
			$limit = '';
					
			if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
				$having = substr($sql,$p,$t[1]-$p);
				$sql = str_replace($having,'',$sql);
			}
			if( ($p=stripos($sql,'GROUP BY')) !== false) {
				if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
					$groupby = substr($sql,$p,$t[1]-$p).$groupBy;
					$sql = str_replace($groupby,'',$sql);
				}
			}
			if( ($p=stripos($sql,'ORDER BY')) !== false) {
				if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
					$orderby = substr($sql,$p,$t[1]-$p);
					$sql = str_replace($orderby,'',$sql);
				}
			}
			if( ($p=stripos($sql,'LIMIT')) !== false) {
				if( ($t=$this->find_next_sql_keyword($sql,$p+1)) !== false) {
					$limit = substr($sql,$p,$t[1]-$p);
					$sql = str_replace($limit,'',$sql);
				}
			}			
			#echo "having=$having<br>groupby=$groupby<br>orderby=$orderby<br>limit=$limit<br>";
			$sql .= $groupby.' '.$having.' '.$orderby.' '.$limit;
		}
		return $sql;
	}

	
	function makeCondition ($condition, $row, $fields)
	{	
		//replace bracketed text with data from field in row with same name
		//numeric data does not need to be unquoted as long as == is used instead of ===
		//date data must be wrapped in strtotime on both sides of comparison to work (NB Unix time 1970 onwards only)
		//replace brackets with PHP code then use eval to run generated string as PHP code
		
		$condition = str_replace('[','$row[\'',$condition);
		$condition = str_replace(']','\']',$condition);
		$condition = "if($condition){\$condition_met=True;}else{\$condition_met=False;}";
		//echo "<br>Test expression as generated: $condition";//check
		eval($condition);
		
		return $condition_met;
	}
	
	function setIndent ($indent)
	{
		return $this->formatter->setIndent($indent);
	}
	
	function myFormat ($value, $style, $dp, $date_format)
	{
		switch ($style) {
			
			case "upper":
			
				return strtoupper($value);
				break;
			
			case "perc":
				
				return sprintf("%01." . $dp . "f", $value) . "%";
				break;
			
			case "dol":
			
				return "$" . number_format($value, $dp);			
				break;
			
			case "num":
			
				return number_format($value, $dp);;
				break;	

			case "date":
				if ($value != "") {
					/*echo "Date format: " . $date_format . "; 
						Value: " . $value . "Date: " . date($date_format, strtotime($value)); //check*/
					return date($date_format, $value);
				
				} else {
					
					return $value;
				}
				
				break;	
				
			default:
				
				return $value;
				break;	
		}		
	}
	
	function getStyle ($format)
	{
		$field_format = explode(",", $format);
		return $field_format[0];
	}

	function getDp ($format)
	{
		$field_format = explode(",", $format);
		return (count($field_format) > 1 ? $field_format[1] : 0);
	}

	function getDateFormat ($format)
	{
		$field_format = explode(",", $format);
		return (count($field_format) > 2 ? $field_format[2] : "m/d/Y");
	}
	
	function setWidth ($field_width, $level_field_width)
	{
		$width = (($field_width != "") ? $field_width : $level_field_width); 	
		return (($width != "") ? " width='" . $width . "'" : "" );
	}
	
	/**
	 * This is worthless junk from the original author, should be refactored
	 */
	function classHtml ($field_class, $lev_class, $both = False)
	{
		if(is_a($field_class,'ReportStyle'))
			return $field_class;
		if(is_a($lev_class,'ReportStyle'))
			return $lev_class;	
		if ($field_class == "" AND $lev_class == "") { //nothing to set
					
			return "";
		}
		
		switch ($both) {
		
			case true: //use both classes
				
				return "$field_class $lev_class";	
				break;
			
			case false:	//only use on class (field takes precedence over level)
				
				if ($field_class != '') {	
					return $field_class;	
				} else {
					return $lev_class;
				}				
				break;
		}
	}
}
?>
