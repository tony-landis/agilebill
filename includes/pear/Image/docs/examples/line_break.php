<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate data break in line plots
 * 
 * Other: 
 * None specific
 * 
 * $Id: line_break.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
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
$Font->setSize(10);

$Graph->setFont($Font);

// setup the plotarea, legend and their layout
$Graph->add(
   Image_Graph::vertical(
      Image_Graph::factory('title', array('Data \'Break\' Sample', 12)),        
      Image_Graph::vertical(
          Image_Graph::vertical(
            $Plotarea1 = Image_Graph::factory('plotarea'),
            $Plotarea2 = Image_Graph::factory('plotarea'),
            50
        ),
        $Legend = Image_Graph::factory('legend'),
         88
      ),
      5
   )
);   

// link the legend with the plotares
$Legend->setPlotarea($Plotarea1);
$Legend->setPlotarea($Plotarea2);

// create the dataset
$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Jan', 10); 
$Dataset->addPoint('Feb', 12); 
$Dataset->addPoint('Mar', 3); 
$Dataset->addPoint('Apr', null); 
$Dataset->addPoint('May', 4); 
$Dataset->addPoint('Jun', 10); 
$Dataset->addPoint('Jul', null); 
$Dataset->addPoint('Aug', null); 
$Dataset->addPoint('Sep', 9); 
$Dataset->addPoint('Oct', 10); 
$Dataset->addPoint('Nov', 4); 
$Dataset->addPoint('Dec', 14);

// create the line plot
$Plot1 =& $Plotarea1->addNew('line', array(&$Dataset));
// set line color
$Plot1->setLineColor('red');

// create the line plot
$Plot2 =& $Plotarea2->addNew('smooth_line', array(&$Dataset));    
// set line color
$Plot2->setLineColor('blue'); 

// output the Graph
$Graph->done();
?>