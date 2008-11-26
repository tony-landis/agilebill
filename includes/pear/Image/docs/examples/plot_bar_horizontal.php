<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Vertical and Horizontal Plots
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_bar_horizontal.php,v 1.1 2005/09/08 19:02:17 nosey Exp $
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
$Font->setSize(8);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Vertical and Horizontal Bar Chart', 12)),
        Image_Graph::horizontal(               
            $Plotarea1 = Image_Graph::factory('plotarea'),           
            $Plotarea2 = Image_Graph::factory('plotarea', array('category', 'axis', 'horizontal')),
            50
        ),           
        5            
    )
);
    
$Dataset[0] =& Image_Graph::factory('dataset');
$Dataset[1] =& Image_Graph::factory('dataset');
$Dataset[2] =& Image_Graph::factory('dataset');

$Dataset[0]->addPoint('A', 1);
$Dataset[0]->addPoint('B', 4);
$Dataset[0]->addPoint('C', -1);
$Dataset[0]->addPoint('D', 2);
$Dataset[0]->addPoint('E', 1);
$Dataset[0]->addPoint('F', 2);
$Dataset[0]->addPoint('G', 3);

$Dataset[1]->addPoint('A', 2);
$Dataset[1]->addPoint('B', -3);
$Dataset[1]->addPoint('C', -2);
$Dataset[1]->addPoint('D', 3);
$Dataset[1]->addPoint('E', 3);
$Dataset[1]->addPoint('F', 2);
$Dataset[1]->addPoint('G', -1);    

$Dataset[2]->addPoint('A', -1);
$Dataset[2]->addPoint('B', 2);
$Dataset[2]->addPoint('C', 1);
$Dataset[2]->addPoint('D', 3);
$Dataset[2]->addPoint('E', -1);
$Dataset[2]->addPoint('F', 2);
$Dataset[2]->addPoint('G', 3);    

$Plot1 =& $Plotarea1->addNew('bar', array(&$Dataset, 'stacked'));
$Plot2 =& $Plotarea2->addNew('bar', array(&$Dataset, 'stacked'));

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');
$Plot1->setFillStyle($FillArray);
$Plot2->setFillStyle($FillArray);

$Graph->done();
?>
