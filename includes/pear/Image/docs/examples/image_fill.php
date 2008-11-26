<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate image filling
 * 
 * Other: 
 * Datapreprocessor, Marker
 * 
 * $Id: image_fill.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */


require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300));

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 8 pixels
$Font->setSize(8);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Image Filling / Data Preprocessing', 11)),
        $Plotarea = Image_Graph::factory('plotarea'),
        8
    )
);

// create a Y grid
$GridY =& $Plotarea->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y);
// that is light gray in color
$GridY->setFillColor('lightgrey');

// create the 1st dataset
$Dataset1 =& Image_Graph::factory('random', array(8, 70, 100, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot1 =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Area', array(&$Dataset1));
// create a vertical gradient fill using red and yellow, ie bottom of graph
// will be yellow and the "higher" the value the more red it will be, ie a "fire" effect
$Plot1->setFillStyle(Image_Graph::factory('Image_Graph_Fill_Image', './images/mountain.jpg'));

// create a Y data value marker
$Marker =& $Plot1->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_MAX);
// fill it with white
$Marker->setFillColor('white');
// and use black border
$Marker->setBorderColor('black');
// create a pin-point marker type
$PointingMarker =& $Plot1->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
// and use the marker on the 1st plot
$Plot1->setMarker($PointingMarker);
// format value marker labels as percentage values
$Marker->setDataPreProcessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));

// create the 2nd dataset
$Dataset2 =& Image_Graph::factory('random', array(8, 30, 80, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot2 =& $Plotarea->addNew('bar', array(&$Dataset2));
// create a vertical gradient fill using red and yellow, ie bottom of graph
// will be yellow and the 'higher' the value the more red it will be, ie a 'fire' effect
$Plot2->setFillColor('orange@0.6');

// Show arrow heads on the axis
$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisX->showArrow();
$AxisY->showArrow();

$AxisX->setDataPreProcessor(Image_Graph::factory('Image_Graph_DataPreprocessor_RomanNumerals'));
$AxisY->setDataPreProcessor(Image_Graph::factory('Image_Graph_DataPreprocessor_NumberText'));
$AxisY->forceMinimum(10);

// output the Graph
$Graph->done();
?>