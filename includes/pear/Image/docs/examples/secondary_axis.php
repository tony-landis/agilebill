<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate secondary y-axis usage
 * 
 * Other: 
 * None specific
 * 
 * $Id: secondary_axis.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';    
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('png', array('width' => 400, 'height' => 300, 'antialias' => 'native'));      

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

// create the plotarea layout
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Primary & Secondary Axis', 11)),
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);         

// make the legend use the plotarea (or implicitly it's plots)
$Legend->setPlotarea($Plotarea);   

// create a grid and assign it to the secondary Y axis
$GridY2 =& $Plotarea->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y_SECONDARY);  
$GridY2->setFillStyle(
    Image_Graph::factory(
        'gradient', 
        array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')
    )
);    

// create a line plot using a random dataset
$Dataset1 =& Image_Graph::factory('random', array(8, 10, 100, true)); 
$Plot1 =& $Plotarea->addNew('line', array(&$Dataset1));
$Plot1->setLineColor('red');

// create an area plot using a random dataset
$Dataset2 =& Image_Graph::factory('random', array(8, 1, 10, true)); 
$Plot2 =& $Plotarea->addNew(
    'Image_Graph_Plot_Area', 
    $Dataset2, 
    IMAGE_GRAPH_AXIS_Y_SECONDARY
);

$Plot2->setLineColor('gray');
$Plot2->setFillColor('blue@0.2');
   
    // set the titles for the plots
$Plot1->setTitle('Primary Axis');
$Plot2->setTitle('Secondary Axis');

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setTitle('Oranges');
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setTitle('Apples', 'vertical'); 
$AxisYsecondary =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y_SECONDARY);
$AxisYsecondary->setTitle('Pears', 'vertical2'); 
			 
// output the Graph
$Graph->done();
?>