<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show band chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_band.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
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

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Band Chart Sample', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);   

$Legend->setPlotarea($Plotarea);        
    
// create the dataset
$Dataset =& Image_Graph::factory('dataset');
for ($i = 0; $i < 40; $i++) {
    $v1 = rand(5, 15);
    $v2 = $v1 + rand(10, 20);
    $Dataset->addPoint($i, array('low' => $v1, 'high' => $v2));
} 

// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Band', $Dataset);

// set a line color
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');

$Axis =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$Axis->setLabelInterval(5);

// output the Graph
$Graph->done();
?>
