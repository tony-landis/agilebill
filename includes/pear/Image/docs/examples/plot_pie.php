<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show pie chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_pie.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);
// create the plotareas
$Plotarea =& $Graph->addNew('plotarea');       

$Plotarea->hideAxis();

// create the dataset
$Dataset =& Image_Graph::factory('random', array(10, 10, 20, true));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Pie', $Dataset);

$Plot->Radius = 2;
	
// set a line color
$Plot->setLineColor('gray');

// set a standard fill style
$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$Plot->setFillStyle($FillArray);
$FillArray->addColor('green@0.2');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('red@0.2');
$FillArray->addColor('orange@0.2');

// add a title using the created font
$Graph->addNew('title', array('Pie Chart Sample', 11));

$Plot->explode(10);

// output the Graph
$Graph->done();
?>
