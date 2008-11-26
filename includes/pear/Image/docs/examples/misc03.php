<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate radial gradient fillings
 * 
 * Other: 
 * None specific
 * 
 * $Id: misc03.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(400, 300));

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 7 pixels
$Font->setSize(7);

$Graph->setFont($Font);
	
// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Meat Export', 12)),
        Image_Graph::horizontal(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            70
        ),
        5            
    )
);

$Legend->setPlotarea($Plotarea);
		
// create the 1st dataset
$Dataset1 =& Image_Graph::factory('dataset');
$Dataset1->addPoint('Beef', rand(1, 10));
$Dataset1->addPoint('Pork', rand(1, 10));
$Dataset1->addPoint('Poultry', rand(1, 10));
$Dataset1->addPoint('Camels', rand(1, 10));
$Dataset1->addPoint('Other', rand(1, 10));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('pie', array(&$Dataset1));
$Plotarea->hideAxis();

// create a Y data value marker
$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_PCT_Y_TOTAL);
// create a pin-point marker type
$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
// and use the marker on the 1st plot
$Plot->setMarker($PointingMarker);	
// format value marker labels as percentage values
$Marker->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '%0.1f%%'));

$Plot->Radius = 2;

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$Plot->setFillStyle($FillArray);
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'green'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'blue'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'yellow'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'red'));
$FillArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'orange'));

$Plot->explode(5);
	   
// output the Graph
$Graph->done();
?>