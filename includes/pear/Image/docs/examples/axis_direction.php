<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Axis direction
 * 
 * Other: 
 * None specific
 * 
 * $Id: axis_direction.php,v 1.1 2005/09/30 18:59:17 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 300));

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(10);

$Graph->setFont($Font);

// setup the plotarea, legend and their layout
$Graph->add(
   Image_Graph::vertical(
      Image_Graph::factory('title', array('Changing Axis Direction', 12)),        
      Image_Graph::horizontal(
         $Plotarea1 = Image_Graph::factory('plotarea'),
         $Plotarea2 = Image_Graph::factory('plotarea'),
         50
      ),
      5
   )
);   

$Dataset =& Image_Graph::factory('random', array(10, 2, 15, true));
$Plot1 =& $Plotarea1->addNew('line', array(&$Dataset));
$Plot1->setLineColor('red');                  

$Plot2 =& $Plotarea2->addNew('line', array(&$Dataset));
$Plot2->setLineColor('red');

$AxisY =& $Plotarea2->getAxis('y');
$AxisY->setInverted(true);                  
     
// output the Graph
$Graph->done();
?>