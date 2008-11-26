<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Somebody liked it :)
 * 
 * Other: 
 * None specific
 * 
 * $Id: misc04.php,v 1.2 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(500, 300));

$Plotarea =& $Graph->addNew('plotarea');

$Dataset =& Image_Graph::factory('Image_Graph_Dataset_Random', array(20, 10, 100, true));

$Fill =& Image_Graph::factory('Image_Graph_Fill_Image', './images/audi-tt-coupe.jpg');
$Plotarea->setFillStyle($Fill);

$Plot =& $Plotarea->addNew('Image_Graph_Plot_Smoothed_Area', $Dataset);

$Plot->setFillColor('white@0.4');
    
// output the Graph
$Graph->done();
?>
