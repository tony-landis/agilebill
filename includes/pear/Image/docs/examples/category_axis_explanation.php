<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Explaining category axis ordering
 * 
 * Other: 
 * None specifc
 * 
 * $Id: category_axis_explanation.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
require_once 'Image/Graph.php';
require_once 'Image/Canvas.php';

$Canvas =& Image_Canvas::factory('png', array('width' => 500, 'height' => 200, 'antialias' => true));      

// create the graph
$Graph =& Image_Graph::factory('graph', $Canvas);

// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(9);

$Graph->setFont($Font);

$Plotarea =& $Graph->addNew('plotarea');

$Datasets[0] =& Image_Graph::factory('dataset');
$Datasets[1] =& Image_Graph::factory('dataset');
$Datasets[2] =& Image_Graph::factory('dataset');

$Datasets[0]->addPoint('this', 1);
$Datasets[0]->addPoint('can', 3);
$Datasets[0]->addPoint('make', 2);
$Datasets[0]->addPoint('correctly', 1);

$Datasets[1]->addPoint('sentence', 1);
$Datasets[1]->addPoint('can', 1);    
$Datasets[1]->addPoint('sense', 2);
$Datasets[1]->addPoint('written', 2);
$Datasets[1]->addPoint('correctly', 2);

$Datasets[2]->addPoint('actually', 3);
$Datasets[2]->addPoint('make', 2);
$Datasets[2]->addPoint('if', 3);
$Datasets[2]->addPoint('written', 1);


// expecting the following X-axis order
// 'this sentence can actually make sense if written correctly'
// making points be placed in the following order:
//
//   |this|sentence|can|actually|make|sense|if|written|correctly|
// 1 |_1__|________|_2_|________|_3__|_____|__|_______|____4____|
// 2 |____|___1____|_2_|________|____|__3__|__|___4___|____5____|
// 3 |____|________|___|___1____|_2__|_____|3_|___4___|_________|
//
// if an append-algorithm were to be (wrongly) used it would yield
// 'this can make correctly sentence sense written actually if'
// making points be placed in the following order:
//
//   |this|can|make|correctly|sentence|sense|written|actually|if|
// 1 |_1__|_2_|_3__|____4____|________|_____|_______|________|__|
// 2 |____|_2_|____|____5____|___1____|__3__|___4___|________|__|
// 3 |____|___|_2__|_________|________|_____|___4___|___1____|3_|
 

$Plot1 =& $Plotarea->addNew('line', array(&$Datasets[0]));
$Plot2 =& $Plotarea->addNew('line', array(&$Datasets[1]));
$Plot3 =& $Plotarea->addNew('line', array(&$Datasets[2]));

$Plot1->setLineColor('red');
$Plot2->setLineColor('blue');
$Plot3->setLineColor('green');

// output the Graph
$Graph->done();
?>