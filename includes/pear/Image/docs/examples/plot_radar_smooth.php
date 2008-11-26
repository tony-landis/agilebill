<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show smoothed radar chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_radar_smooth.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
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
        Image_Graph::factory('title', array('Smoothed Radar Chart Sample', 11)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('Image_Graph_Plotarea_Radar'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);    
$Legend->setPlotarea($Plotarea);                
    
$Plotarea->addNew('Image_Graph_Grid_Polar', IMAGE_GRAPH_AXIS_Y);

// create the dataset
$DS1 =& Image_Graph::factory('dataset');
$DS2 =& Image_Graph::factory('dataset');
for ($i = 0; $i < 360; $i += 10) {
    $DS1->addPoint($i, rand(3, 6));
    if ($i % 30 == 0) {
        $DS2->addPoint($i, rand(2, 4));
    }
}
    
$Plot1 =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Radar', $DS1);
$Plot2 =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Radar', $DS2);

// set a standard fill style
$Plot1->setLineColor('blue@0.4');    
$Plot1->setFillColor('blue@0.2');
// set a standard fill style
$Plot2->setLineColor('green@0.4');    
$Plot2->setFillColor('green@0.2');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setLabelInterval(3);
$AxisX->setLineColor('lightgrey');
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setLineColor('lightgrey');

// create a Y data value marker
$Marker =& $Plot2->addNew('Image_Graph_Marker_Circle');
$Marker->setSize(5);
$Marker->setFillColor('gray@0.2');
$Plot2->setMarker($Marker);
       

// output the Graph
$Graph->done();
?>