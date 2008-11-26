<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate how to manually set axis labels
 * 
 * Other: 
 * None specific
 * 
 * $Id: manual_labels.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(500, 200));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(8);

$Graph->setFont($Font);

$Plotarea =& $Graph->addNew('plotarea');
  
$Dataset =& Image_Graph::factory('random', array(8, 1, 10));
$Plot =& $Plotarea->addNew('line', array(&$Dataset));

$LineStyle =& Image_Graph::factory('Image_Graph_Line_Dashed', array('red', 'transparent'));
//$Plot->setLineColor('red');
$Plot->setLineStyle($LineStyle);

$AxisY =& $Plotarea->getAxis('y');
$AxisY->setLabelInterval(array(2, 4, 9));

// output the Graph
$Graph->done();
?>