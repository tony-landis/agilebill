<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show radar chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_radar.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('png',
    array(
        'width' => 400,
        'height' => 300,
        'antialias' => 'native'
    )
);

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Spider/Radar Chart Sample', 12)),        
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
$DS1->addPoint('Life', rand(1, 6));
$DS1->addPoint('Universe', rand(1, 6));
$DS1->addPoint('Everything', rand(1, 6));
$DS1->addPoint('Something', rand(1, 6));
$DS1->addPoint('Nothing', rand(1, 6));
$DS1->addPoint('Irrelevevant', rand(1, 6));

$DS2 =& Image_Graph::factory('dataset');
$DS2->addPoint('Life', rand(1, 6));
$DS2->addPoint('Universe', rand(1, 6));
$DS2->addPoint('Everything', rand(1, 6));
$DS2->addPoint('Something', rand(1, 6));
$DS2->addPoint('Nothing', rand(1, 6));
$DS2->addPoint('Irrelevevant', rand(1, 6));

$Plot =& $Plotarea->addNew('Image_Graph_Plot_Radar', $DS1);
$Plot2 =& $Plotarea->addNew('Image_Graph_Plot_Radar', $DS2);

   
// set a standard fill style
$Plot->setLineColor('blue@0.4');    
$Plot->setFillColor('blue@0.2');

$Plot2->setLineColor('red@0.4');    
$Plot2->setFillColor('red@0.2');
	
// output the Graph
$Graph->done();
?>