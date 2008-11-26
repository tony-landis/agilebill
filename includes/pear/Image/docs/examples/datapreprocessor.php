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
 * $Id: datapreprocessor.php,v 1.4 2005/08/03 21:21:52 nosey Exp $
 * 
 * @package Image_Graph
 * @author Jesper Veggerby <pear.nosey@veggerby.dk>
 */
 
error_reporting(E_ALL);
include('Image/Graph.php');

function foo($value) {
    return '-' . chr($value+63) . '-';
}

// create the graph
$Graph =& Image_Graph::factory('Image_Graph', array(600, 400));
// add a TrueType font
$Font =& $Graph->addNew('font', 'Verdana');
// set the font size to 11 pixels
$Font->setSize(7);
    
$Graph->setFont($Font);
   
// create the plotarea
$Graph->add(
    Image_Graph::vertical(
	    Image_Graph::factory('Image_Graph_Title', array('DataPreprocessor Example', 11)),
        $Matrix = Image_Graph::factory('Image_Graph_Layout_Matrix', array(2, 2)),
        5
    )
);

$Charts = array('bar', 'line', 'Image_Graph_Plot_Smoothed_Line', 'Image_Graph_Plot_Area');

for ($i = 0; $i < 2; $i++) {
    for ($j = 0; $j < 2; $j++) {
        $Plotarea =& $Matrix->getEntry($i, $j);

        $Chart = $Charts[($i*2+$j)];

        $GridY =& $Plotarea->addNew('bar_grid', IMAGE_GRAPH_AXIS_Y);
        $GridY->setFillStyle(Image_Graph::factory('gradient', array(IMAGE_GRAPH_GRAD_VERTICAL, 'white', 'lightgrey')));

        $Var = "Plot$i$j";
        $Dataset =& Image_Graph::factory('Image_Graph_Dataset_Random', array(8, 10, 100, $Chart == 'Image_Graph_Plot_Area'));
        $$Var =& $Plotarea->addNew($Chart, array(&$Dataset));
    }
}
$Plotarea =& $Matrix->getEntry(0, 0);

$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_RomanNumerals'));
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_NumberText'));

$Plotarea =& $Matrix->getEntry(0, 1);
$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setDataPreprocessor(
    Image_Graph::factory('Image_Graph_DataPreprocessor_Array',
        array(
            array(
                1 => '30 Jul',
                2 => '31 Jul',
                3 => '1 Aug',
                4 => '2 Aug',
                5 => '3 Aug',
                6 => '4 Aug',
                7 => '5 Aug',
                8 => '6 Aug'
            )
        )
    )
);
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Formatted', '+ %0.1f%%'));

$Plotarea =& $Matrix->getEntry(1, 0);
$AxisX =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_X);
$AxisX->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Function', 'foo'));
$AxisY =& $Plotarea->getAxis(IMAGE_GRAPH_AXIS_Y);
$AxisY->setDataPreprocessor(Image_Graph::factory('Image_Graph_DataPreprocessor_Currency', 'US$'));

// just for looks
$Plot00->setFillColor('red@0.2');

$Plot11->setFillColor('blue@0.2');

// output the Graph
$Graph->done();
?>