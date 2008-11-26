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
	
class CORE_graph
{


	########################################################################
	### Create a Bar Graph
	########################################################################

	function PIE_graph($module, $method, $type, $start, $extra_fields)
	{
		global $C_translate, $C_auth;
		include_once(PATH_CORE . 'validate.inc.php');
		$dt = new CORE_validate;

		include (PATH_GRAPH."jpgraph.php");

		####################################################################
		### Check if 'search' is authorized for this account
		####################################################################

		# check the validation for this function
		if($C_auth->auth_method_by_name($module,'search'))
		{
			# validate this file exists, and include it.
			if (file_exists(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php'))
			{
				include_once(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php');
			}
			else
			{
				### Not exist!
				$error = $C_translate->translate('module_non_existant','','');
			}
		}
		else
		{
			### Not auth
			$error = $C_translate->translate('module_non_auth','','');
		}


		if(isset($error))
		{
			include (PATH_GRAPH."jpgraph_canvas.php");
			// Create the graph.
			$graph = new CanvasGraph(460,55,"auto");
			$t1 = new Text($error);
			$t1->Pos(0.2,0.5);
			$t1->SetOrientation("h");
			$t1->SetBox("white","black",'gray');
			$t1->SetFont(FF_FONT1,FS_NORMAL);
			$t1->SetColor("black");
			$graph->AddText($t1);
			$graph->Stroke();
			exit;
		}


		# initialize the module, if it has not already been initialized
		$eval = '$' . $module . ' = new ' . $module . '; ';
		$eval .= '$this_Obj  = $' . $module . ';';
		eval ($eval);

		# run the function
		$array = call_user_func (array($module, $method), $start_str, $end_str, $constraint_array, $default_array, $extra_fields);


		include  (PATH_GRAPH."jpgraph_pie.php");
		include  (PATH_GRAPH."jpgraph_pie3d.php");


		$data    = $array['data'];
		$legends = $array['legends'];

		// Create the Pie Graph.
		$graph = new PieGraph(500,250,"auto");
		$graph->SetScale("textlin");
		$graph->SetMarginColor('#F9F9F9');
		$graph->SetFrame(true,'#FFFFFF',0);
		$graph->SetColor('#F9F9F9');

		// Create pie plot
		$p1 = new PiePlot3d($data);
		$p1->SetTheme("water");
		$p1->SetCenter(0.4);
		$p1->SetAngle(30);
		$p1->value->SetFont(FF_FONT1,FS_NORMAL,8);
		$p1->SetLegends($legends);

		// Explode the larges slice:
		$largest = 0;
		for($i=0; $i<count($data); $i++)
		{
			if ($data[$i] > $largest)
			{
				$largest = $data[$i];
				$explode = $i;
			}
		}
		if($explode)
		$p1->ExplodeSlice($explode);

		$graph->Add($p1);
		$graph->Stroke();
	}




	########################################################################
	### Create a Bar Graph
	########################################################################

	function BAR_graph($module, $type, $start, $extra_fields)
	{
		global $C_translate, $C_auth;
		include_once(PATH_CORE . 'validate.inc.php');
		$dt = new CORE_validate;

		include (PATH_GRAPH."jpgraph.php");

		####################################################################
		### Check if 'search' is authorized for this account
		####################################################################

		# check the validation for this function
		if($C_auth->auth_method_by_name($module,'search'))
		{
			# validate this file exists, and include it.
			if (file_exists(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php'))
			{
				include_once(PATH_MODULES . '/' . $module . '/' . $module . '.inc.php');
			}
			else
			{
				### Not exist!
				$error = $C_translate->translate('module_non_existant','','');
			}
		}
		else
		{
			### Not auth
			$error = $C_translate->translate('module_non_auth','','');
		}


		if(isset($error))
		{
			include (PATH_GRAPH."jpgraph_canvas.php");
			// Create the graph.
			$graph = new CanvasGraph(460,55,"auto");
			$t1 = new Text($error);
			$t1->Pos(0.2,0.5);
			$t1->SetOrientation("h");
			$t1->SetBox("white","black",'gray');
			$t1->SetFont(FF_FONT1,FS_NORMAL);
			$t1->SetColor("black");
			$graph->AddText($t1);
			$graph->Stroke();
			exit;
		}




		####################################################################
		### BY WEEK
		####################################################################

		if ($type == 'week')
		{
			$FONT_SIZE= 7;
			$AbsWidth = 12;
			$interval = 4;

			$type = $C_translate->translate('week','','');
			if($start == "" || $start <= 12)
			{
				## Get the beginning/end of this week
				$start_str    = mktime (24,0,0,12, 31, date("Y")-1);
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (24,0,0,12, 30, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);

			}
			else
			{
				## Get the beginning/end of the specified week
				$start_str    = mktime (24,0,0,12, 31, date("$start")-1);
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (24,0,0,12, 30, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);

			}

			### Set the constraint array:
			$curr_str = $start_str;
			while ( $curr_str <= $end_str )
			{
				$new_curr_str       = mktime (0,0,0,date("m",$curr_str), date("d",$curr_str)+7, date("Y",$curr_str));
				$constraint_array[] = Array('start' => $curr_str, 'end'   => $new_curr_str);	
				$curr_str = $new_curr_str;
				$default_array[] = 0;
			}
		}


		####################################################################
		### MONTH
		####################################################################

		else if ($type == 'month')
		{
			$FONT_SIZE = 10;
			$AbsWidth = 12;
			$TickLables = $gDateLocale->GetShortMonth();
			$interval =1;
			$type = $C_translate->translate('month','','');

			if($start == "" || $start < 12)
			{
				## Get the beginning/end of this week
				$start_str    = mktime (24,0,0,12, 31, date("Y")-1);
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (24,0,0,12, 30, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);
			}
			else
			{
				## Get the beginning/end of the specified week
				## Get the beginning/end of this week
				$start_str    = mktime (24,0,0,12, 31, date("$start")-1);
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (24,0,0,12, 30, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);
			}

			### Set the constraint array:
			$curr_str = $start_str;
			while ( $curr_str <= $end_str )
			{
				$new_curr_str       = mktime (0,0,0,date("m",$curr_str)+1, date("d",$curr_str), date("Y",$curr_str));
				$constraint_array[] = Array('start' => $curr_str, 'end'   => $new_curr_str);
				$curr_str = $new_curr_str;
				$default_array[] = 0;
			}


		}


		####################################################################
		### BY YEAR
		####################################################################

		else if ($type == 'year')
		{
			$FONT_SIZE = 10;
			$interval =1;
			$AbsWidth = 13;

			$type = $C_translate->translate('year','','');


			## Get the beginning/end of this year - 10
			$start_str    = mktime (0,0,0,1,1, date("Y")-9);
			$start        = date(UNIX_DATE_FORMAT, $start_str);
			$end_str      = mktime (0,0,0,12,30, date("Y",$start_str)+9);
			$end          = date(UNIX_DATE_FORMAT, $end_str);    		         		


			### Set the constraint array:
			$curr_str = $start_str;
			while ( $curr_str <= $end_str )
			{
				$new_curr_str       = mktime (0,0,0,date("m",$curr_str), date("d",$curr_str), date("Y",$curr_str)+1);
				$constraint_array[] = Array('start' => $curr_str, 'end'   => $new_curr_str);	
				$TickLables[]       = date("Y",$curr_str);
				$curr_str = $new_curr_str;
				$default_array[] = 0;
			}    		
		}




		####################################################################
		### BY DAY
		####################################################################

		else
		{
			$FONT_SIZE= 8;
			$interval = 3;
			$AbsWidth = 4;
			$type     = $C_translate->translate('day','','');

			if($start == ""  || $start > 12 || $start < 1 )
			{
				## Get the beginning/end of this week
				$start_str    = mktime (0,0,0,date("m"), 1, date("Y"));
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (0,0,0,date("m",$start_str)+1, 1, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);
			}
			else
			{
				## Get the beginning/end of the specified week
				$start_str    = mktime (0,0,0,date("$start"), 1, date("Y"));
				$start        = date(UNIX_DATE_FORMAT, $start_str);
				$end_str      = mktime (0,0,0,date("m",$start_str)+1, 1, date("Y",$start_str));
				$end          = date(UNIX_DATE_FORMAT, $end_str);

			}

			### Set the constraint array:
			$curr_str = $start_str;
			while ( $curr_str < $end_str )
			{
				$new_curr_str       = mktime (0,0,0,date("m",$curr_str), date("d",$curr_str)+1, date("Y",$curr_str));
				$constraint_array[] = Array('start' => $curr_str, 'end'   => $new_curr_str);	
				$TickLables[]       = date("j",$curr_str);
				$curr_str           = $new_curr_str;
				$default_array[]    = 0;
			}
		}


		# initialize the module, if it has not already been initialized
		$eval = '$' . $module . ' = new ' . $module . '; ';
		$eval .= '$this_Obj  = $' . $module . ';';
		eval ($eval);

		# run the function
		$array = call_user_func (array($module, "graph"), $start_str, $end_str, $constraint_array, $default_array, $extra_fields);


		include  (PATH_GRAPH."jpgraph_bar.php");

		$datay=$array['results'];

		// Create the graph. These two calls are always required
		$graph = new Graph(550,200,"auto");
		$graph->SetScale("textlin");
		$graph->yaxis->scale->SetGrace(25);
		$graph->SetMarginColor('#F9F9F9');
		$graph->SetFrame(true,'darkgreen',0);
		$graph->SetColor('#FFFFFF');
		#$graph->SetFrame(false);


		// Adjust the margin a bit to make more room for titles
		$graph->img->SetMargin(45,10,15,25);

		// Create a bar pot
		$bplot = new BarPlot($datay);

		// Set the X
		if(isset($TickLables))
		$graph->xaxis->SetTickLabels($TickLables);
		$graph->xaxis->SetTextLabelInterval($interval);
		$graph->xaxis->SetColor("navy");
		$graph->yaxis->SetColor("navy");

		// Adjust fill color
		$bplot->SetFillColor('#506DC7');
		$bplot->value->Show();
		$bplot->value->SetFont(FF_FONT1,FS_NORMAL,$FONT_SIZE);
		$bplot->value->SetAngle(90);
		$bplot->value->SetFormat('%0.0f');
		$bplot->value->SetColor("darkblue");
		$graph->Add($bplot);

		// Setup the titles
		$title = $array['title'];
		$graph->title->Set($title . "     $start - $end");
		$graph->title->SetFont(FF_FONT1,FS_BOLD);
		$graph->title->SetColor("black");


		// Display the graph
		$graph->Stroke();
	}
}
?>