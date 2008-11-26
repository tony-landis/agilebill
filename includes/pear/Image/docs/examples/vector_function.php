<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate vector function data
 * 
 * Other: 
 * Setting axis intersection
 * 
 * $Id: vector_function.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
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
        Image_Graph::factory('title', array('Vector Function Chart Sample', 12)),        
        Image_Graph::vertical(
            // create the plotarea with a normal linear axis as x-axis!
            $Plotarea = Image_Graph::factory('plotarea', 'axis'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);    
$Legend->setPlotarea($Plotarea);               

function tcost($t) { return $t*cos($t); }
function tsint($t) { return $t*sin($t); }

$GridX =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_X);
$GridY =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);
$LineStyle =& Image_Graph::factory('Image_Graph_Line_Dashed', array('lightgrey', 'transparent'));
$GridX->setLineStyle($LineStyle);
$GridY->setLineStyle($LineStyle);

$Dataset =& Image_Graph::factory('vector', array(0, 20, 'tcost', 'tsint', 200));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('line', array(&$Dataset));
$Plot->setLineColor('red');
$Plot->setTitle('f(t) = { t*cos(t), t*sin(t) }');

$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setAxisIntersection(0);

$Graph->done();
?>