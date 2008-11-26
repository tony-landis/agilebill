<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show dot/scatter chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_scatter_linefit.php,v 1.1 2005/09/14 20:27:25 nosey Exp $
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
        Image_Graph::factory('title', array('Dot Chart Sample', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);
$Legend->setPlotarea($Plotarea);        

// create the dataset
$Dataset1 =& Image_Graph::factory('random', array(10, 10, 50, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot1 =& $Plotarea->addNew('Image_Graph_Plot_Dot', array(&$Dataset1));
$Marker1 =& Image_Graph::factory('Image_Graph_Marker_Cross');
$Marker1->setFillColor('blue');
$Marker1->setLineColor('black');
// set a line color
$Plot1->setMarker($Marker1);
$Plot1->setTitle('Introvert');

// create the 1st plot as smoothed area chart using the 1st dataset
$Plot2 =& $Plotarea->addNew('Image_Graph_Plot_Fit_Line', array(&$Dataset1));
// set a line color
$Plot2->setLineColor('blue');
$Plot2->setTitle('Extrovert');

// output the Graph
$Graph->done();
?>