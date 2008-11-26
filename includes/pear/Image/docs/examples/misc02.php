<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate a plot
 * 
 * Other: 
 * None specific
 * 
 * $Id: misc02.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

function XtoYear($Value)
{
    return floor($Value+2000);
}

function salaries($Value)
{
    // I wish!
    return exp($Value)+1000;
}

// create the graph as a 500 x 300 image
$Graph =& Image_Graph::factory('graph', array(600, 300));
$Graph->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'lightsteelblue', 'papayawhip')));

// create a random dataset to use for demonstrational purposes
$DataSet =& Image_Graph::factory('function', array(1, 9, 'salaries', 8));

$DataSet2 =& Image_Graph::factory('dataset');
$DataSet2->addPoint('CEO', 10);
$DataSet2->addPoint('TAP', 32); 
$DataSet2->addPoint('TBF', 13); 
$DataSet2->addPoint('ABC', 19); 
$DataSet2->addPoint('QED', 26); 

// create and set the plot font
$Font =& $Graph->addNew('font', 'Verdana');
$Font->setSize(7);
$Graph->setFont($Font);

// add a plot area in a vertical layout to display a title on top 
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Annual income', 11)),
        Image_Graph::horizontal(          
            $Plotarea = Image_Graph::factory('plotarea'),
            Image_Graph::vertical(
                $Plotarea2 = Image_Graph::factory('plotarea'),
                $Legend2 = Image_Graph::factory('legend'),
                90
            )
        ),                
        5
    ),
    5
);

$Legend2->setPlotarea($Plotarea2);

// create a bar grid and fill it with a gradient fill white->lightgray
$Grid =& $Plotarea->addNew('bar_grid', null, IMAGE_GRAPH_AXIS_Y);
$Grid->setFillColor('gray@0.2');

$Plotarea->setFillColor('gray@0.2');       

// add a line plot to the plotarea based on the function dataset
$Plot =& $Plotarea->addNew('line', array(&$DataSet));

// add coins-icon as marker
$Plot->setMarker(Image_Graph::factory('Image_Graph_Marker_Icon', './images/coins.png'));

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);

// make x-axis start at 0
$AxisX->forceMinimum(0);

// make x-axis end at 9
$AxisX->forceMaximum(9);

// show axis arrows
$AxisY->showArrow();

// make y-axis have a maximum at 9.500
$AxisY->forceMaximum(9500);

// create a datapreprocessor to map X-values to years
$AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Function', 'XtoYear'));
$AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Currency', "US$"));    

$Plot2 =& $Plotarea2->addNew('pie', array(&$DataSet2));
$Plotarea2->hideAxis();
$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'red'));
$Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'blue'));
$Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'yellow'));
$Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'green'));
$Fill->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'orange'));
$Plot2->setFillStyle($Fill);

$Marker2 =& $Graph->addNew('value_marker', IMAGE_GRAPH_VALUE_Y);
$Plot2->setMarker($Marker2);
$Marker2->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.0f%%'));

$Plot2->explode(20, 'TBF');    

// output the graph
$Graph->done();
?>