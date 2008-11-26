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
	
header("Pragma: no-cache" );
header("Cache-Control: no-cache, must-revalidate" );

if (!file_exists("/usr/bin/munge_monitor") || !file_exists("/var/log/asteriskmem")) {
	echo '<center>Sorry, the required scripts for processing memory reports are not installed.</center>';
	exit;
}
if (GD == false) {
	echo '<center>Sorry, this report requires GD support inside of PHP';
	exit;
}	

ob_start();  
require_once ('../../config.inc.php'); 
require_once (PATH_INCLUDES."jpgraph/jpgraph.php");
require_once (PATH_INCLUDES."jpgraph/jpgraph_line.php");


$keys = array();
function get_index($v) {
	global $keys;

	if (in_array($v,$keys)) {
		return array_search($v,$keys);
	}
	$keys[] = $v;
	return array_search($v,$keys);
}

function get_index_name($v) {
	global $keys;
	return $keys[$v];
}

$fp = popen("/usr/bin/munge_monitor </var/log/asteriskmem","r");
$data = "";
while(!feof($fp))
	$data .= fread($fp,65536);
fclose($fp);
$lines = explode("\n",$data); $prev = ""; $i=-1; $j=0;
foreach ($lines as $line) {
	$col = explode("|",$line);
	if($col[2]>10) {
	#echo "<pre>"; print_r($col); echo "</pre>";
	if ($prev != $col[0]) {
		$prev = $col[0];
		$i++;
		$j=0;
		for($t=0;$t<40;$t++)
		$datay[$i][$t] = 0;
	}
	$datay[get_index($col[3])][$i] = $col[2];
	$datax[$i] = date("H:j",$col[0]);
	$j++;
	}
}
#echo "<pre>"; print_r($datay); echo "</pre>"; exit;
$graph = new Graph(800,768,"auto");
$graph->SetShadow();
$graph->SetBackgroundGradient('#8e8e8e','#e1e1e1');
// Use an integer X-scale
$graph->SetScale("textlin");

// Set title and subtitle
$graph->title->Set("Memory Leaks");
$graph->subtitle->Set("Shows the number of unfreed blocks requested by each module");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Make the margin around the plot a little bit bigger
// then default
$graph->img->SetMargin(40,140,40,80);

// Slightly adjust the legend from it's default position in the
// top right corner to middle right side
$graph->legend->Pos(0.05,0.5,"right","center");

// Display every 10:th datalabel
$graph->xaxis->SetTextTickInterval(6);
$graph->xaxis->SetTextLabelInterval(6);
$graph->xaxis->SetTickLabels($datax);
$graph->xaxis->SetLabelAngle(90);

$rgb = new RGB();
$i = 0;
foreach($datay as $dy) {
	// Create a red line plot
	$p[$i] = new LinePlot($dy);
	reset($rgb->rgb_table);
	for($j=0;$j<=$i;$j += 1) {
		for($k=0;$k<=10;$k++) {
			next($rgb->rgb_table);
		}
		if( current($rgb->rgb_table) == "" ) {
			reset($rgb->rgb_table);
		}
	}
	$p[$i]->SetColor(current($rgb->rgb_table));
	$p[$i]->SetLegend(get_index_name($i));

	// The order the plots are added determines who's ontop
	$graph->Add($p[$i]);
	// $graph->Add($b1);
	#$i++;
	#echo "<pre>"; print_r($dy); echo "</pre>";
	$i++;
}

// Finally output the  image
$graph->Stroke(); 
ob_end_flush(); 
exit;
?>