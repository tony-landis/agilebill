<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate switching between canvass
 * 
 * Other: 
 * PNG and SVG canvas usage
 * 
 * $Id: driver_png_svg.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

// create a new GD canvas
$Canvas =& Image_Canvas::factory('gd',
    array(
        'filename' => './images/modify.jpg',
        'left' => 400,
        'top' => 100,
        'width' => 500,
        'height' => 500,
        'transparent' => true            
        )
    ); 
 
    // create the graph using the GD canvas
$Graph =& Image_Graph::factory('graph', $Canvas);

// create a simple graph
$Graph->add(
    Image_Graph::vertical(
        $Plotarea = Image_Graph::factory('plotarea'),
        $Legend = Image_Graph::factory('legend'),
        90
    )
);    
$Legend->setPlotarea($Plotarea);        
$Dataset =& Image_Graph::factory('random', array(10, 2, 15, true));       
$Plot =& $Plotarea->addNew('area', $Dataset);
$Plot->setLineColor('gray');
$Plot->setFillColor('blue@0.2');

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);
$Graph->addNew('title', array('Simple Area Chart Sample', 12));
    
// output the graph using the GD canvas
$Graph->done(array('filename' => './canvassample.png'));

// create a new SVG canvas
$Canvas =& Image_Canvas::factory('svg',
    array(
        'width' => 600,
        'height' => 400
    )
); 
// make the graph use this now instead
$Graph->setCanvas($Canvas);

// 're'-output the graph, but not using the SVG canvas
$Graph->done(array('filename' => './canvassample.svg'));
?>
