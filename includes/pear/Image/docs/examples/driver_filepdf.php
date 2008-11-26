<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * PDF canvas
 * 
 * Other: 
 * Datapreprocessor, Axis markers
 * 
 * $Id: driver_filepdf.php,v 1.1 2005/10/05 20:51:18 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
error_reporting(E_ALL);

require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('File_PDF', array('page' => 'A4'));

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas); 

// // setup the plotarea, legend and their layout
$Graph->add(
   Image_Graph::vertical(
      Image_Graph::factory('title', array('Simple Line Chart Sample', 12)),        
      Image_Graph::vertical(
         $Plotarea = Image_Graph::factory('plotarea'),
         $Legend = Image_Graph::factory('legend'),
         88
      ),
      5
   )
);   

// link the legend with the plotares
$Legend->setPlotarea($Plotarea);

// create a random dataset for sake of simplicity
$Dataset =& Image_Graph::factory('random', array(10, 2, 15, true));
// create the plot as line chart using the dataset
$Plot =& $Plotarea->addNew('line', array(&$Dataset));

// set a line color
$Plot->setLineColor('red');                  
     
// output the Graph
$Graph->done();
?>