<?php
/**
 * Usage example for Image_Graph.
 * 
 * Main purpose: 
 * Demonstrate Matrix layout
 * 
 * Other: 
 * None specific
 * 
 * $Id: layout_matrix.php,v 1.3 2005/08/03 21:21:53 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */

require_once 'Image/Graph.php';    

// create the graph
$Graph =& Image_Graph::factory('graph', array(600, 400));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);

$Graph->setFont($Font);

// create the plotarea
$Graph->add(
    Image_Graph::vertical(
        Image_Graph::factory('title', array('Matrix Layout', 10)),               
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(3, 3)),           
        5            
    )
);
    
for ($i = 0; $i < 9; $i++) {
    $Dataset =& Image_Graph::factory('random', array(5, 2, 15, false));
    $Plotarea =& $Matrix->getEntry($i % 3, floor($i / 3));
    $Plotarea->addNew('line_grid', false, IMAGE_GRAPH_AXIS_X);
    $Plotarea->addNew('line_grid', false, IMAGE_GRAPH_AXIS_Y);

    $Plot =& $Plotarea->addNew('line', $Dataset);
    $Plot->setLineColor('red');
    $Plot->setTitle("x^2");
}
    
$Graph->done();
?>
