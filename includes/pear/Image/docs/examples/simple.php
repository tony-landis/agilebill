<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Datapreprocessing usage and idea
 * 
 * Other: 
 * Matrix layout
 * 
 * $Id: simple.php,v 1.1 2005/09/30 18:59:17 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
error_reporting(E_ALL);
include 'Image/Graph.php';
$Graph =& Image_Graph::factory('graph', array(400, 300));
$Plotarea =& $Graph->addNew('plotarea');
$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Denmark', 10);
$Dataset->addPoint('Norway', 3);
$Dataset->addPoint('Sweden', 8);
$Dataset->addPoint('Finland', 5);
$Plot =& $Plotarea->addNew('bar', &$Dataset);
$Graph->done();
?>