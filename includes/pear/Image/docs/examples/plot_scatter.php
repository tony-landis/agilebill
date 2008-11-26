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
 * $Id: plot_scatter.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
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
$Dataset1 =& Image_Graph::factory('random', array(10, 2, 9, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot1 =& $Plotarea->addNew('Image_Graph_Plot_Dot', array(&$Dataset1));
$Marker1 =& Image_Graph::factory('Image_Graph_Marker_Cross');
$Marker1->setFillColor('blue');
$Marker1->setLineColor('black');
// set a line color
$Plot1->setMarker($Marker1);
$Plot1->setTitle('Introvert');

// create the dataset
$Dataset2 =& Image_Graph::factory('random', array(10, 10, 15, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot2 =& $Plotarea->addNew('Image_Graph_Plot_Dot', array(&$Dataset2));
$Marker2 =& Image_Graph::factory('Image_Graph_Marker_Plus');
$Marker2->setFillColor('green');
$Marker2->setLineColor('black');
// set a line color
$Plot2->setMarker($Marker2);
$Plot2->setTitle('Extrovert');

// output the Graph
$Graph->done();
?>