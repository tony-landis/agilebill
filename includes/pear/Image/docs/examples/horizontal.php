<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Displaying labels and titles on horizontal and vertical plots
 * 
 * Other: 
 * Datapreprocessor, Axis markers
 * 
 * $Id: horizontal.php,v 1.2 2005/10/05 20:56:38 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
error_reporting(E_ALL);

include_once 'Image/Graph.php';
include_once 'Image/Canvas.php';

   
$Dataset =& Image_Graph::factory('dataset',
    array(
        array(
            'A' => 10,
            'B' => 9,
            'C' => 4,
            'D' => 6,
            'E' => 5,
            'F' => 9,
            'G' => 11,
            'H' => 8
        )
    )
);

$Dataset2 =& Image_Graph::factory('dataset',
    array(
        array(
            'A' => 121,
            'B' => 134,
            'C' => 192,
            'D' => 213,
            'E' => 123,
            'F' => 167,
            'G' => 153,
            'H' => 149
        )
    )
);

$Canvas =& Image_Canvas::factory('png', array('width' => 800, 'height' => 400));      

// create the graph
$Graph =& Image_Graph::factory('graph', &$Canvas);    

$Graph->setFont(Image_Graph::factory('font', array('Courier New')));
$Graph->setFontSize(8);
  
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Vertical & Horizontal Plots', 11)),
        Image_Graph::vertical(
            Image_Graph::horizontal(
                $Plotarea = Image_Graph::factory('plotarea'),
                $Plotarea2 = Image_Graph::factory('plotarea', array('category', 'axis', 'horizontal'))
            ),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        7
    )
);
$Legend->setPlotarea($Plotarea);           

$GridY =& $Plotarea->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);

$Plot =& $Plotarea->addNew('step', &$Dataset);    
$Plot->setFillColor('blue@0.2');
$Marker =& Image_Graph::factory('value_marker', IMAGE_GRAPH_VALUE_Y);
$Marker->setFontSize(7);
$Plot->setMarker($Marker);

$Plot12 =& $Plotarea->addNew('line', &$Dataset2, IMAGE_GRAPH_AXIS_Y_SECONDARY);
$Plot12->setLineColor('red');

$Plot->setTitle('Primary axis');
$Plot12->setTitle('Secondary axis');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setTitle('X Data', array('size' => 10));

$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setTitle('Y Data', array('size' => 10));

$AxisY2 =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
$AxisY2->setTitle('Y2 Data', array('size' => 10));

$Plotarea->setFillColor('gray@0.2');

$GridY =& $Plotarea2->addNew('line_grid', null, IMAGE_GRAPH_AXIS_Y);

$Plot2 =& $Plotarea2->addNew('step', &$Dataset);    
$Plot2->setFillColor('blue@0.2');
$Marker2 =& Image_Graph::factory('value_marker', IMAGE_GRAPH_VALUE_Y);
$Marker2->setFontSize(7);
$Plot2->setMarker($Marker2);

$Plot22 =& $Plotarea2->addNew('line', &$Dataset2, IMAGE_GRAPH_AXIS_Y_SECONDARY);
$Plot22->setLineColor('red');

$Plot2->setTitle('Primary axis');
$Plot22->setTitle('Secondary axis');

$AxisX =& $Plotarea2->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setTitle('X Data', array('size' => 10));

$AxisY =& $Plotarea2->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setTitle('Y Data', array('size' => 10));

$AxisY2 =& $Plotarea2->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
$AxisY2->setTitle('Y2 Data', array('size' => 10));    
$Plotarea2->setFillColor('gray@0.2');
        
$Graph->done();
?>