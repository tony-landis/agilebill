<?php
/**
 * Usage example for Image_Graph.
 *
 * Main purpose:
 * Demonstrate odo & donut plots
 *
 * Other:
 * Radial gradient fillings
 *
 * $Id: plot_odo.php,v 1.1 2005/08/30 21:25:24 nosey Exp $
 *
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';
// create the graph
$driver=& Image_Canvas::factory('png',array('width'=>400,'height'=>300,'antialias' => 'native'));
$Graph = & Image_Graph::factory('graph', $driver);
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);


// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Odo Chart', 12)),
        Image_Graph::horizontal(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            80
        ),
        5
    )
);

$Legend->setPlotarea($Plotarea);
$Legend->setAlignment(IMAGE_GRAPH_ALIGN_HORIZONTAL);
/***************************Arrows************************/
$Arrows = & Image_Graph::factory('dataset');
$Arrows->addPoint('ok', 200, 'OK');
$Arrows->addPoint('std', 120, 'Std');
$Arrows->addPoint('bad', 250, 'Bad');


/**************************PARAMATERS for PLOT*******************/

// create the plot as odo chart using the dataset
$Plot =& $Plotarea->addNew('Image_Graph_Plot_Odo',$Arrows);
$Plot->setRange(100, 300);
$Plot->setAngles(135, 270);
$Plot->setRadiusWidth(75);
$Plot->setLineColor('gray');//for range and outline

$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
$Plot->setArrowMarker($Marker);

$Plotarea->hideAxis();
/***************************Axis************************/
// create a Y data value marker

$Marker->setFillColor('transparent');
$Marker->setBorderColor('transparent');
$Marker->setFontSize(7);
$Marker->setFontColor('black');

// create a pin-point marker type
$Plot->setTickLength(14);
$Plot->setAxisTicks(5);
/********************************color of arrows*************/
$FillArray = & Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.6', 'OK');
$FillArray->addColor('orange@0.6', 'Std');
$FillArray->addColor('green@0.6', 'Bad');

// create a line array
$LineArray =& Image_Graph::factory('Image_Graph_Line_Array');
$LineArray->addColor('blue', 'OK');
$LineArray->addColor('orange', 'Std');
$LineArray->addColor('green', 'Bad');
$Plot->setArrowLineStyle($LineArray);
$Plot->setArrowFillStyle($FillArray);

/***************************MARKER OR ARROW************************/
// create a Y data value marker
$Marker =& $Plot->addNew('Image_Graph_Marker_Value', IMAGE_GRAPH_VALUE_Y);
$Marker->setFillColor('black');
$Marker->setBorderColor('blue');
$Marker->setFontSize(7);
$Marker->setFontColor('white');
// create a pin-point marker type
$PointingMarker =& $Plot->addNew('Image_Graph_Marker_Pointing_Angular', array(20, &$Marker));
// and use the marker on the plot
$Plot->setMarker($PointingMarker);
/**************************RANGE*******************/
// create the dataset
/*$Range[] = & Image_Graph::factory('dataset',array(array(100,140)));
$Range[] = & Image_Graph::factory('dataset',array(array(150,260)));
$Range[] = & Image_Graph::factory('dataset',array(array(270,290)));*/
//print_r($Range);die();
$Plot->addRangeMarker(100, 140);
$Plot->addRangeMarker(150, 260);
$Plot->addRangeMarker(270, 290);
// create a fillstyle for the ranges
$FillRangeArray = & Image_Graph::factory('Image_Graph_Fill_Array');
$FillRangeArray->addColor('green@0.7');
$FillRangeArray->addColor('yellow@0.7');
$FillRangeArray->addColor('blue@0.7');
/* $FillRangeArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'green'));
$FillRangeArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'orange'));
$FillRangeArray->addNew('gradient', array(IMAGE_GRAPH_GRAD_RADIAL, 'white', 'red')); */
$Plot->setRangeMarkerFillStyle($FillRangeArray);

// output the Graph
$Graph->done();
?>