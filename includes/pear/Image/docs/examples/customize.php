<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Customizing axis and legends
 * 
 * Other: 
 * None specific
 * 
 * $Id: customize.php,v 1.4 2005/09/08 19:02:17 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(450, 300));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Stacked Bar Chart with defined axis properties', 12)),
        $Plotarea = Image_Graph::factory('plotarea'),
        5
    )
);

$MarkerX =& $Plotarea->addNew('Image_Graph_Axis_Marker_Area', null, IMAGE_GRAPH_AXIS_X);
$MarkerX->setFillColor('blue@0.3');
$MarkerX->setLineColor('blue@0.3');
$MarkerX->setLowerBound(7);
$MarkerX->setUpperBound(8);

$MarkerY =& $Plotarea->addNew('Image_Graph_Axis_Marker_Area', null, IMAGE_GRAPH_AXIS_Y);
$MarkerY->setFillColor('green@0.3');
$MarkerY->setLineColor('green@0.3');
$MarkerY->setLowerBound(5.2);
$MarkerY->setUpperBound(9.3);

$MarkerY =& $Plotarea->addNew('Image_Graph_Axis_Marker_Line', null, IMAGE_GRAPH_AXIS_Y);
$MarkerY->setLineColor('red');
$MarkerY->setValue(14.4);

// create the 1st plot as smoothed area chart using the 1st dataset
$Plot1 =& $Plotarea->add(
    Image_Graph::factory('bar',
        array(
            $Dataset = array(
                Image_Graph::factory('random', array(8, 1, 10, false)),
                Image_Graph::factory('random', array(8, 1, 10, false)),
                Image_Graph::factory('random', array(8, 1, 10, false))
            ),
            'stacked'
        )
    )
);

$Dataset[0]->setName('Dataset one');
$Dataset[1]->setName('Numero duo');
$Dataset[2]->setName('En-to-tre');

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.1', 0);
$FillArray->addColor('red@0.1', 1);
$FillArray->addColor('yellow@0.1', 2);
$Plot1->setFillStyle($FillArray);

// Show arrow heads on the axis
$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);

$AxisY->addMark(5);
$AxisY->addMark(7);

$AxisY->setFontSize(7);    

$AxisY->addMark(10.8, 17.5);
$AxisY->setFillColor('red@0.7');
$AxisY->setLabelInterval(array(1, 5, 9, 12, 13, 14, 19, 21));
$AxisY->setTickOptions(-3, 2);
$AxisY->setLabelInterval('auto', 2);
$AxisY->setTickOptions(-1, 1, 2);

$AxisY->setLabelOptions(
    array(
        'showtext' => true,
        'font' => array(
            'size' => 3,
            'color' => 'red'
        )
    ), 2
);  
$AxisY->setLabelOption('showoffset', true, 1);

$AxisX->showArrow();  

$Legend =& $Plotarea->addNew('legend');
$Legend->setFillColor('white@0.7');
$Legend->setFontSize(8);
$Legend->showShadow();

$Plot1->setLineColor('black@0.1');


// output the Graph
$Graph->done();
?>
