<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate gradient fillings
 * 
 * Other: 
 * None specific
 * 
 * $Id: gradient_fill_area.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
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

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Gradient filled smoothed area chart', 12)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            85
        ),
        8
    )
);

$Legend->setPlotarea($Plotarea);
	
// create the 1st dataset
$Dataset =& Image_Graph::factory('random', array(10, 40, 100, true));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('smooth_area', array(&$Dataset));
// create a vertical gradient fill using red and yellow, ie bottom of graph 
// will be yellow and the 'higher' the value the more red it will be, ie a 'fire' effect
$Plot->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'green', 'lightyellow')));
$Plot->setTitle('Inside scope');

// create the 2nd dataset
$Dataset2 =& Image_Graph::factory('random', array(10, 50, 70, true));
// create the 2nd plot as smoothed area chart using the 2nd dataset
$Plot2 =& $Plotarea->addNew('smooth_area', array(&$Dataset2));
// create a vertical gradient fill using red and yellow, ie bottom of graph 
// will be yellow and the 'higher' the value the more red it will be, ie a 'fire' effect
$Plot2->setFillColor('white@0.4');
$Plot2->setTitle('Outside scope');

$Graph->setBackground(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL_MIRRORED, 'steelblue', 'lightcyan')));
$Graph->setBorderColor('black');


// create a Y data value marker
$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_MAX);
// create a pin-point marker type
$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
// and use the marker on the 1st plot
$Plot->setMarker($PointingMarker);	
// format value marker labels as percentage values
$Marker->setDataPreProcessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));
$Marker->setFontSize(7);

$AxisY =& $Plotarea->getAxis('y');
$AxisY->forceMinimum(30);

// output the Graph
$Graph->done();
?>