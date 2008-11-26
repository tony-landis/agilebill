<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show impulse chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_impulse.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
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
        Image_Graph::factory('title', array('Impulse Chart Sample', 12)),        
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
$Dataset =& Image_Graph::factory('random', array(10, 2, 15, false));
// create the 1st plot as smoothed area chart using the 1st dataset
$Plot =& $Plotarea->addNew('Plot_Impulse', array(&$Dataset));    

// set a line color
$Plot->setLineColor('red');
    
// output the Graph
$Graph->done();
?>