<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show map chart
 * (for copyright reasons the map is unfortunately not available in package)
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_map.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));

// create the plotareas
$Plotarea =& $Graph->addNew('Image_Graph_Plotarea_Map', 'europe');

// create the dataset
$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Denmark', 10);
$Dataset->addPoint('Sweden', 5);
$Dataset->addPoint('Iceland', 7);
$Dataset->addPoint('Portugal', 2);
$Dataset->addPoint('Sicily', 5);

$Dataset2 =& Image_Graph::factory('dataset');
$Dataset2->addPoint('Finland', 0);
$Dataset2->addPoint('Ukraine', 0);
$Dataset2->addPoint('Cyprus', 0);

$Plot =& $Plotarea->addNew('Image_Graph_Plot_Dot', array(&$Dataset));
$Marker =& $Plot->setMarker(Image_Graph::factory('Image_Graph_Marker_Bubble'));

// set a line color
$Plot->setLineColor('gray');

// set a standard fill style
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$Marker->setFillStyle($FillArray);
$FillArray->addColor('green@0.2');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('red@0.2');
$FillArray->addColor('orange@0.2');

$Plot2 =& $Plotarea->addNew('line', array(&$Dataset2));
$Plot2->setLineColor('red');
$Marker2 =& $Plot2->setMarker(Image_Graph::factory('Image_Graph_Marker_Circle'));
$Marker2->setLineColor('blue');
$Marker2->setFillColor('white');
$Marker2->Size = 5;
$ValueMarker =& Image_Graph::factory('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_X);
$Marker2->setSecondaryMarker(Image_Graph::factory('Image_Graph_Marker_Pointing_Angular', array(40, &$ValueMarker)));

$ValueMarker->setFillColor('white');

$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(8);
$Graph->setFont($Font);
$Graph->addNew('title', array('Map Chart Sample', 12));

$Graph->setBorderColor('black');
$Graph->showShadow();

// output the Graph
$Graph->done();
?>