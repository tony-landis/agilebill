<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate multiple plots
 * 
 * Other: 
 * Title alignments, Multi-line axis labels, Markers 
 * 
 * $Id: multiple_plots.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::vertical(
            $Title = Image_Graph::factory('title', array('Multiple Plots', 11)),
            $SubTitle = Image_Graph::factory('title', array('This is a demonstration of title alignment', 7)),
            90
        ),
        $Plotarea = Image_Graph::factory('plotarea'),
        8
    )
);	
$Title->setAlignment(IMAGE_GRAPH_ALIGN_LEFT);
$SubTitle->setAlignment(IMAGE_GRAPH_ALIGN_LEFT);
   
$Grid =& $Plotarea->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y);
$Grid->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')));    
	
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Area', Image_Graph::factory('random', array(10, 20, 100, true)));
$Plot->setFillColor('red@0.2');

$Plot =& $Plotarea->addNew('line', Image_Graph::factory('random', array(10, 20, 100, true)));
$Plot->setLineColor('blue@0.2');
$CircleMarker =& Image_Graph::factory('Image_Graph_Marker_Circle');
$Plot->setMarker($CircleMarker);
$CircleMarker->setFillColor('white@0.4');
    
$Plot =& $Plotarea->addNew('bar', Image_Graph::factory('random', array(10, 2, 40, true)));
$Plot->setFillColor('green@0.2');
$Marker =& Image_Graph::factory('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
$Plot->setMarker($Marker);
$Marker->setFillColor('white');
$Marker->setBorderColor('black');
    
$AxisY = $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->showArrow();

$Array = array(
    "Jan-Feb\n2004", 
    "Mar-Apr\n2004", 
    "May-Jun\n2004", 
    "Jul-Aug\n2004", 
    "Sep-Oct\n2004", 
    "Nov-Dev\n2004", 
    "Jan-Feb\n2005", 
    "Mar-Apr\n2005", 
    "May-Jun\n2005", 
    "Jul-Aug\n2005" 
);
$AxisX = $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Array', array($Array)));
    	 
// output the Graph
$Graph->done();
?>