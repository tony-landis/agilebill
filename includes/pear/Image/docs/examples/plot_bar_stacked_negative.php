<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Stacked bar charts with negative values
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_bar_stacked_negative.php,v 1.3 2005/08/03 21:21:52 nosey Exp $
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

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Negative Stacked Bar Chart', 10)),               
        $Plotarea = Image_Graph::factory('plotarea'),           
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

$Plot =& $Plotarea->addNew('bar', array(&$Dataset, 'stacked'));

$FillArray =& Image_Graph::factory('Image_Graph_Fill_Array');
$FillArray->addColor('blue@0.2');
$FillArray->addColor('yellow@0.2');
$FillArray->addColor('green@0.2');
$Plot->setFillStyle($FillArray);

$Graph->done();
?>
