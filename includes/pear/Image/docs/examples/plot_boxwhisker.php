<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Show box & whisker chart
 * 
 * Other: 
 * None specific
 * 
 * $Id: plot_boxwhisker.php,v 1.4 2005/08/03 21:21:53 nosey Exp $
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
        Image_Graph::factory('title', array('Box & Whisker Chart', 12)),        
        Image_Graph::vertical(
            $Plotarea = Image_Graph::factory('plotarea'),
            $Legend = Image_Graph::factory('legend'),
            90
        ),
        5
    )
);   
$Legend->setPlotarea($Plotarea);
  
$Plotarea->addNew('line_grid', array(), IMAGE_GRAPH_AXIS_Y);       
  
$Dataset =& Image_Graph::factory('dataset');
$Dataset->addPoint('Security', array(10, 21, 12, 18, 12, 17, 14, 13));
$Dataset->addPoint('Internal', array(3, 6, 1, 9, 12, 4, 3, 5, 6));
$Dataset->addPoint('External', array(9, 10, 12, 15, 13, 12, 11, 17));
$Plot =& $Plotarea->addNew('Image_Graph_Plot_BoxWhisker', array(&$Dataset));

$Fill =& Image_Graph::factory('Image_Graph_Fill_Array');
$Fill->addColor('red', 'min');
$Fill->addColor('green', 'max');
$Fill->addColor('orange@0.2', 'quartile1');
$Fill->addColor('blue@0.2', 'median');
$Fill->addColor('orange@0.2', 'quartile3');
$Fill->addColor('yellow@0.1', 'box');
$Plot->setFillStyle($Fill);
$Line =& Image_Graph::factory('Image_Graph_Line_Solid', 'black@0.6');
$Line->setThickness(1);
$Plot->setLineStyle($Line);    
        
// output the Graph
$Graph->done();
?>